<?php

declare(strict_types=1);

namespace Tests\Api;

use Recipeland\App;
use Tests\TestSuite;
use Tests\Api\SeedsData;
use GuzzleHttp\Psr7\Uri;
use Recipeland\Data\Step;
use Recipeland\Data\User;
use Recipeland\Data\Recipe;
use Recipeland\Data\Rating;
use Phinx\Seed\AbstractSeed;
use Recipeland\Data\Ingredient;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * API test will simulate a HTTP request, without making
 * an actual request. It tests only the application, not the
 * reverse proxy configuration, but it's atill an integration
 * test, because we are also testing the DB and the Cache layers.
 *
 * @group slow
 */
class ApiTest extends TestSuite
{
    use SeedsData;
    
    const JWT_PATTERN = '|^(Bearer\s)([A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.?[A-Za-z0-9-_.+\/=]*[^\.]+)$|';
    
    protected $url;
    protected $container;
    
    /**
     * Destroy and recreate the database for every test
     **/
    public function setUp()
    {
        parent::setUp();
        
        $this->url = getenv('TEST_URL');

        $this->container = require(BASE_DIR.'/bootstrap/Config.php');
        $this->container->get('db');
        $this->container->get('facades');
        
        $this->database('rollback');
        $this->database('migrate');
        $this->database('seed', 'users');
        $this->database('seed', 'recipes');
        
        $this->expected['data'] = [
            Recipe::with('ingredients', 'steps', 'author')->find(1)->toArray(),
            Recipe::with('ingredients', 'steps', 'author')->find(2)->toArray(),
            Recipe::with('ingredients', 'steps', 'author')->find(3)->toArray(),
            Recipe::with('ingredients', 'steps', 'author')->find(4)->toArray(),
        ];
    }
    
    public function test_block_insecure_requests()
    {
        echo 'API test: block insecure http requests - returns 403';
        
        $response = $this->request('GET', '/recipes', null, [], 'http');
        $this->assertHeaders($response, 403);
    }
    
    public function test_home()
    {
        echo 'API test: GET / returns OK';

        $response = $this->request('GET', '/');
        $this->assertHeaders($response);
        $this->assertEquals('{"Recipeland":"OK!"}', (string) $response->getBody());
    }
    
    public function test_list_recipes()
    {
        echo 'API test: GET /recipes returns a list of recipes';

        $response = $this->request('GET', '/recipes');
        $this->assertHeaders($response);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected, $responseArray);
    }
    
    public function test_login()
    {
        echo 'API test: POST /login - returns a JWT';
        
        $response = $this->login();
        $this->assertEquals('{"status":"Authorized"}', (string) $response->getBody());
        $this->assertRegExp(self::JWT_PATTERN, $response->getHeader('authorization')[0]);
        $this->assertHeaders($response);
    }
    
    public function test_login_wrong_password()
    {
        echo 'API test: POST /login with wrong password - returns 401';
        
        $response = $this->login('luigi', 'Wrong-Password-123');
        $this->assertHeaders($response, 401);
    }

    public function test_create_recipe()
    {
        echo 'API test: POST /recipes and create a new recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2); // Wait for token validity
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response);
        
        $input = json_decode($this->newRecipe, true);
        $recipe = Recipe::with('ingredients', 'steps')->find(5)->toArray(); //new Recipe will have ID 5
        
        // Adapt values for deep comparison
        $input['recipe']['created_by'] = 2;
        $input['recipe']['vegetarian'] = true;
        foreach ($recipe['ingredients'] as $key => $value) {
            $recipe['ingredients'][$key]['quantity'] = $value['details']['quantity'];
            $recipe['ingredients'][$key]['unit'] = $value['details']['unit'];
        }
        $this->recursive_unset($recipe, 'id');
        $this->recursive_unset($recipe, 'details');
        $this->recursive_unset($recipe, 'created_at');
        $this->recursive_unset($recipe, 'updated_at');
        $this->recursive_unset($recipe, 'deleted_at');
        
        $this->assertEquals($input['recipe'], $recipe);
    }
    
    public function test_create_recipe_with_bad_header()
    {
        echo 'API test: POST /recipes with malformed header - returns 400';
        
        $header = [
            'authorization' => 'Thief abc'
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 400);
    }
    
    public function test_create_recipe_with_bad_token()
    {
        echo 'API test: POST /recipes with invalid token - returns 400';
        
        $header = [
            'authorization' => 'Bearer abc.def.ghi'
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 400);
    }
    
    public function test_create_recipe_with_invalid_token_signature()
    {
        echo 'API test: POST /recipes with invalid token signature - returns 401';
        
        $token_parts = explode('.', $this->get_valid_token());
        $token = $token_parts[0].'.'.$token_parts[1].'.Invalid_Signature';
        
        $header = [
            'authorization' => [$token],
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 401);
    }
    
    public function test_create_recipe_with_invalid_claims()
    {
        echo 'API test: POST /recipes with invalid claims - returns 401';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        // Do not wait - get invalid claim
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 401);
    }
    
    public function test_create_recipe_with_old_token()
    {
        echo 'API test: POST /recipes with old_token - returns 401';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        // User changed but still owns a valid token
        $user = User::find(2);
        $user->name = 'Roberto';
        $user->save();
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 401);
    }
    
    public function test_create_recipe_with_unprivileged_user()
    {
        echo 'API test: POST /recipes with unprivileged user - returns 403';
        
        $header = [
            'authorization' => [$this->get_valid_token('homer','Marge1234!')],
        ];
        
        sleep(2);
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response, 403);
    }

    public function test_get_recipe_1()
    {
        echo 'API test: GET /recipes/1 and get the given recipe';

        $response = $this->request('GET', '/recipes/1');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected['data'][0], $responseArray);
        $this->assertHeaders($response);
    }
    
    public function test_get_inexistent_recipe()
    {
        echo 'API test: GET /recipes/inexistent - returns 404';

        $response = $this->request('GET', '/recipes/99');
        $responseArray = $this->jsonToArray($response);
        $this->assertHeaders($response, 404);
    }
    
    public function test_edit_recipe()
    {
        echo 'API test: PUT /recipes/{id} and edit the given recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('PUT', '/recipes/1', $this->newRecipe, $header);
        
        $this->assertHeaders($response);
        
        $input = json_decode($this->newRecipe, true);
        $recipe = Recipe::with('ingredients', 'steps')->find(1)->toArray();
        
        // Adapt values for deep comparison
        $input['recipe']['created_by'] = 2;
        $input['recipe']['vegetarian'] = true;
        foreach ($recipe['ingredients'] as $key => $value) {
            $recipe['ingredients'][$key]['quantity'] = $value['details']['quantity'];
            $recipe['ingredients'][$key]['unit'] = $value['details']['unit'];
        }
        $this->recursive_unset($recipe, 'id');
        $this->recursive_unset($recipe, 'details');
        $this->recursive_unset($recipe, 'created_at');
        $this->recursive_unset($recipe, 'updated_at');
        $this->recursive_unset($recipe, 'deleted_at');
        
        $this->assertEquals($input['recipe'], $recipe);
    }
    
    public function test_edit_inexistent_recipe()
    {
        echo 'API test: PUT /recipes/inexistent - returns 404';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('PUT', '/recipes/99', $this->newRecipe, $header);
        $this->assertHeaders($response, 404);
    }
    
    public function test_edit_not_my_recipe()
    {
        echo 'API test: PUT /recipes/{id} not owned by user - returns 403';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('PUT', '/recipes/2', $this->newRecipe, $header);
        $this->assertHeaders($response, 403);
    }

    public function test_edit_recipe_part()
    {
        echo 'API test: PATCH /recipes/{id} and edit part of the given recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $patch = [
            'recipe' => [
                'name' => 'A beautiful name! Only the name.'
            ]
        ];
        
        sleep(2);
        
        $response = $this->request('PATCH', '/recipes/1', json_encode($patch), $header);
        
        $this->assertHeaders($response);
        
        $recipe = Recipe::find(1);
        
        // We changed only the name
        $this->assertEquals('A beautiful name! Only the name.', $recipe->name);
        
        // The rest should be the default
        $this->assertEquals($recipe->created_by, 2);
        $this->assertEquals($recipe->subtitle, 'Test Recipe by Luigi Risotto');
        $this->assertEquals($recipe->description, 'TESTING');
        $this->assertEquals($recipe->prep_time, 10);
        $this->assertEquals($recipe->total_time, 20);
        $this->assertEquals($recipe->vegetarian, 1);
        $this->assertEquals($recipe->difficulty, 1);
        $this->assertEquals($recipe->picture, 'https://example.com/example.jpg');
    }

    public function test_delete_recipe()
    {
        echo 'API test: DELETE /recipes/{id} and remove the given recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('DELETE', '/recipes/1', '', $header);
        
        $this->assertEquals(
            '{"message":"Recipe 1: Test Recipe Fooo has been deleted!"}',
            (string) $response->getBody()
        );
        $this->assertHeaders($response);
        
        $recipe = Recipe::find(1);
        $this->assertNull($recipe);
        
        $recipe = Recipe::withTrashed()->where('id', 1)->first();
        $this->assertEquals('Test Recipe Fooo', $recipe->name);
    }
    
    public function test_delete_inexistent_recipe()
    {
        echo 'API test: DELETE /recipes/inexistent - returns 404';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('DELETE', '/recipes/99', '', $header);
        $this->assertHeaders($response, 404);
    }
    
    public function test_delete_not_my_recipe()
    {
        echo 'API test: DELETE /recipes/{id} not owned by user - returns 403';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        sleep(2);
        
        $response = $this->request('DELETE', '/recipes/2', '', $header);
        $this->assertHeaders($response, 403);
    }

    public function test_create_rating()
    {
        echo 'API test: POST /recipes/{id}/rating and evaluate the given recipe once';

        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $rating = [
            'rating' => 5
        ];
        
        sleep(2);
        
        $response = $this->request('POST', '/recipes/2/rating', json_encode($rating), $header);
        $this->assertHeaders($response);
        
        $rating = Rating::where('recipe_id', 2)->first();
        $this->assertEquals('5', $rating->rating);
        
        $this->reset_container();
        
        // Assert we cannot rate twice
        $response = $this->request('POST', '/recipes/2/rating', json_encode($rating), $header);
        $this->assertHeaders($response, 403);
    }
    
    public function test_create_rating_own_recipe()
    {
        echo 'API test: POST /recipes/{id}/rating to own recipe - returns 403';

        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $rating = [
            'rating' => 5
        ];
        
        sleep(2);
        
        $response = $this->request('POST', '/recipes/1/rating', json_encode($rating), $header);
        $this->assertHeaders($response, 403);
    }
    
    public function test_create_rating_inexistent_recipe()
    {
        echo 'API test: POST /recipes/inexistent/rating - returns 404';

        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $rating = [
            'rating' => 5
        ];
        
        sleep(2);
        
        $response = $this->request('POST', '/recipes/99/rating', json_encode($rating), $header);
        $this->assertHeaders($response, 404);
    }
    
    public function test_search()
    {
        echo 'API test: GET /recipes/search?query=... search by keyword';
        
        $response = $this->request('GET', '/recipes/search?query=Foo');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertArraySubset(['name' => 'Test Recipe Fooo'], $responseArray['data'][0]);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_author()
    {
        echo 'API test: GET /recipes/search?author=... search by author';
        
        $response = $this->request('GET', '/recipes/search?author=Luigi');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertArraySubset(['created_by' => 2], $responseArray['data'][0]);
        $this->assertHeaders($response);
    }
    
    public function test_search_difficulty_and_vegetarian()
    {
        echo 'API test: GET /recipes/search?difficulty={n}&?vegetarian={n} filter properties';
        
        $response = $this->request('GET', '/recipes/search?difficulty=1&vegetarian=1');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(2, $responseArray['data']);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_prep_time_and_total_time()
    {
        echo 'API test: GET /recipes/search?prep_time={n}&?total_time={n} search by cooking time';
        
        $response = $this->request('GET', '/recipes/search?prep_time=10&total_time=20');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(1, $responseArray['data']);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_rating()
    {
        echo 'API test: GET /recipes/search?rating={n} search by rating';
        
        $recipe = Recipe::find(1);
        $recipe->rating = 5.0;
        $recipe->save();
        
        $response = $this->request('GET', '/recipes/search?rating=5');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(1, $responseArray['data']);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_subqueries_gt_lt()
    {
        echo 'API test: GET /recipes/search?prep_time={"gt":{n},"lt":{n}} search by subqueries gt lt';
        
        $response = $this->request('GET', '/recipes/search?prep_time={"gt":9,"lt":20}');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(2, $responseArray['data']);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_subqueries_gte_lte()
    {
        echo 'API test: GET /recipes/search?prep_time={"gte":{n},"lte":{n}} search by subqueries gte lte';
        
        $response = $this->request('GET', '/recipes/search?total_time={"gte":35,"lte":35}');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(1, $responseArray['data']);
        $this->assertHeaders($response);
    }

    public function test_404()
    {
        echo 'API test: GET /recipes/non/existent/route - returns 404 - Not Found';

        $response = $this->request('GET', '/recipes/non/existent/route');
        $responseArray = $this->jsonToArray($response);
        $this->assertHeaders($response, 404);
    }

    public function test_405()
    {
        echo 'API test: DELETE /recipes/{id}/rating - returns 405 - Method Not Allowed';

        $response = $this->request('DELETE', '/recipes/1/rating');
        $responseArray = $this->jsonToArray($response);
        $this->assertHeaders($response, 405);
    }
    
    
    
    /***********************************************************************
     *                       API HELPER METHODS                            *
     ***********************************************************************/
    private function request(
        string $method,
        string $path,
        string $body = null,
        array $headers = [],
        string $scheme = 'https'
    ): ResponseInterface {
        $uri = new Uri($this->url.$path);
        parse_str($uri->getQuery(), $query);
        $request = (new Request($method, $uri, $headers, $body))->withHeader('x-forwarded-proto', $scheme)
                                                                ->withQueryParams($query);
        $app = $this->container->get(App::class);
        $response = $app->go($request);
        
        return $response;
    }
    
    private function jsonToArray(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true);
    }
    
    private function assertHeaders(ResponseInterface $response, int $status=200): void
    {
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json;charset=utf-8', $response->getHeader('content-type')[0]);
    }
    
    private function login(string $username = 'luigi', string $password = 'Pasta1234!')
    {
        $body = '{
            "username" : "'.$username.'",
            "password" : "'.$password.'"
        }';
        $response = $this->request('POST', '/auth/login', $body);
        
        return $response;
    }
    
    private function reset_container()
    {
        unset($this->container);
        $this->container = require(BASE_DIR.'/bootstrap/Config.php');
    }
    
    private function get_valid_token(string $username = 'luigi', string $password = 'Pasta1234!')
    {
        $response = $this->login($username, $password);
        $token = $response->getHeader('authorization')[0];
        $this->reset_container();
        
        return $token;
    }
    
    private function recursive_unset(&$array, $unwanted_key)
    {
        unset($array[$unwanted_key]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->recursive_unset($value, $unwanted_key);
            }
        }
    }
}
