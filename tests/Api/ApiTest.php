<?php

declare(strict_types=1);

namespace Tests\Api;

use Recipeland\App;
use Tests\TestSuite;
use GuzzleHttp\Psr7\Uri;
use Phinx\Config\Config;
use Recipeland\Data\Step;
use Recipeland\Data\User;
use Recipeland\Data\Recipe;
use Recipeland\Data\Rating;
use Phinx\Seed\AbstractSeed;
use Phinx\Wrapper\TextWrapper;
use Recipeland\Data\Ingredient;
use Recipeland\Data\Permission;
use Phinx\Console\PhinxApplication;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * API test will simulate a HTTP request, without making
 * an actual request. It tests only the application, not the
 * reverse proxy configuration, but it's atill an integration
 * test, because we are also testing the DB and the Cache layers.
 */
class ApiTest extends TestSuite
{
    const JWT_PATTERN = '|^(Bearer\s)([A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.?[A-Za-z0-9-_.+\/=]*[^\.]+)$|';
    
    protected $url;
    protected $container;
    protected $reset = true;
    
    protected $expected = [
        'current_page' => 1,
        'data' => [],
        'first_page_url' => '/?page=1',
        'from' => 1,
        'last_page' => 1,
        'last_page_url' => '/?page=1',
        'next_page_url' => null,
        'path' => '/',
        'per_page' => 10,
        'prev_page_url' => null,
        'to' => 4,
        'total' => 4,
    ];
    
    protected $newRecipe = '{
        "recipe" : {
            "name" : "My Most delicious Recipe!",
            "subtitle" : "My subtitle",
            "description" : "My description",
            "prep_time" : 10,
            "total_time" : 20,
            "vegetarian" : 1,
            "difficulty" : 3,
            "picture" : "https://example.com/picture.jpg",
            "ingredients" : [
                {
                    "slug" : "my_ingredient",
                    "quantity" : "12",
                    "unit" : "mg",
                    "name" : "My Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "milk"
                },
                {
                    "slug" : "my_second_ingredient",
                    "quantity" : "10",
                    "unit" : "ml",
                    "name" : "My Second Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "none"
                }
            ],
            "steps" : [
                {
                    "description" : "Put ingredient somewhere",
                    "picture" : "https://example.com/picture.jpg"
                },
                {
                    "description" : "Eat Ingredient",
                    "picture" : "https://example.com/picture.jpg"
                }
            ]
        }
    }';
    
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
        $this->assertEquals('{"error":"Unauthorized"}', (string) $response->getBody());
        $this->assertHeaders($response, 401);
    }

    public function test_create_recipe()
    {
        echo 'API test: POST /recipes and create a new recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertHeaders($response);
        
        $input = json_decode($this->newRecipe, true);
        $recipe = Recipe::with('ingredients', 'steps')->find(5)->toArray(); //new Recipe will have ID 5
        
        // Adapt values for deep comparison
        $input['recipe']['created_by'] = 2;
        $input['recipe']['vegetarian'] = true;
        $input['recipe']['rating'] = 'not rated yet';
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
    
    public function test_create_recipe_with_bad_token()
    {
        echo 'API test: POST /recipes with invalid token - returns 400';
        
        $header = [
            'authorization' => 'Bearer abc.def.ghi'
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $this->assertArraySubset(['error' => 'Bad Request'], json_decode((string) $response->getBody(), true));
        $this->assertHeaders($response, 400);
    }

    public function test_get_recipe_1()
    {
        echo 'API test: GET /recipes/1 and get the given recipe';

        $response = $this->request('GET', '/recipes/1');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected['data'][0], $responseArray);
        $this->assertHeaders($response);
    }
    
    public function test_get_recipe_2()
    {
        echo 'API test: GET /recipes/2 and get the given recipe';

        $response = $this->request('GET', '/recipes/2');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected['data'][1], $responseArray);
        $this->assertHeaders($response);
    }
    
    public function test_edit_recipe()
    {
        echo 'API test: PUT /recipes/{id} and edit the given recipe';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
        $response = $this->request('PUT', '/recipes/1', $this->newRecipe, $header);
        
        $this->assertHeaders($response);
        
        $input = json_decode($this->newRecipe, true);
        $recipe = Recipe::with('ingredients', 'steps')->find(1)->toArray();
        
        // Adapt values for deep comparison
        $input['recipe']['created_by'] = 2;
        $input['recipe']['vegetarian'] = true;
        $input['recipe']['rating'] = 'not rated yet';
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
    
    public function test_edit_not_my_recipe()
    {
        echo 'API test: PUT /recipes/{id} not owned by user - returns 403';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
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
    
    public function test_delete_not_my_recipe()
    {
        echo 'API test: DELETE /recipes/{id} not owned by user - returns 403';
        
        $header = [
            'authorization' => [$this->get_valid_token()],
        ];
        
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
        
        $response = $this->request('POST', '/recipes/1/rating', json_encode($rating), $header);
        $this->assertHeaders($response, 403);
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
        
        $response = $this->request('GET', '/recipes/search?difficulty=1&?vegetarian=1');
        $responseArray = $this->jsonToArray($response);
        
        $this->assertCount(2, $responseArray['data']);
        $this->assertHeaders($response);
    }
    
    public function test_search_by_prep_time_and_total_time()
    {
        echo 'API test: GET /recipes/search?prep_time={n}&?total_time={n} search by time';
        
        $response = $this->request('GET', '/recipes/search?prep_time=10&total_time=20');
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
        $this->assertEquals(['error' => 'Not Found'], $responseArray);
        $this->assertHeaders($response, 404);
    }

    public function test_405()
    {
        echo 'API test: DELETE /recipes/{id}/rating - returns 405 - Method Not Allowed';

        $response = $this->request('DELETE', '/recipes/1/rating');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals(['error' => 'Method Not Allowed'], $responseArray);
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
    
    
    
    /***********************************************************************
     *                       DATABASE SEEDING METHODS                      *
     ***********************************************************************/
    private function database($command, $what=null)
    {
        if (getenv('ENVIRONMENT') == 'testing' &&
           getenv('DB_CONNECTION') == 'pgtest') {
            $phinx = new PhinxApplication();
            $wrapper = new TextWrapper($phinx);
            $wrapper->setOption('configuration', BASE_DIR.'/phinx.php');
            $wrapper->setOption('environment', 'testing');
            $wrapper->setOption('parser', 'PHP');
            
            switch ($command) {
                case 'migrate':
                    $wrapper->getMigrate();
                    break;
                    
                case 'seed':
                    if ($what == 'users') {
                        $wrapper->getSeed(null, null, 'AclSeeder');
                    } elseif ($what == 'recipes') {
                        $this->createRecipes();
                    }
                    break;
                    
                case 'rollback':
                    $wrapper->getRollback(null, 0);
                    break;
            }
        }
    }
    
    private function createRecipes()
    {
        $authors = [
            User::where('username', 'luigi')->first(),
            User::where('username', 'burns')->first(),
        ];

        $recipes = [
            $this->createRecipe($authors[0], 'Fooo', 10, 20, 1, 1),
            $this->createRecipe($authors[1], 'Baar', 15, 25, 1, 1),
            $this->createRecipe($authors[0], 'Baaz', 20, 30, 0, 2),
            $this->createRecipe($authors[1], 'Biim', 25, 35, 1, 3),
        ];

        $ingredients = [
            $this->createIngredient('Lola'),
            $this->createIngredient('Lula'),
        ];

        $steps = [
            $this->createStep('Do something'),
            $this->createStep('Go home'),
        ];

        foreach ($recipes as $recipe) {
            $recipe->attachIngredient($ingredients[0], '123', 'mg');
            $recipe->attachIngredient($ingredients[1], '456', 'kg');
            $recipe->attachStep($steps[0], 1);
            $recipe->attachStep($steps[1], 2);
        }
    }

    private function createRecipe(
        User $author,
        string $name,
        int $prep_time,
        int $total_time,
        int $vegetarian,
        int $difficulty
    ) {
        return Recipe::firstOrCreate(
            ['name' => 'Test Recipe '.$name],
            [
                'created_by' => $author->id,
                'subtitle' => 'Test Recipe by '.$author->name,
                'description' => 'TESTING',
                'prep_time' => $prep_time,
                'total_time' => $total_time,
                'vegetarian' => $vegetarian,
                'difficulty' => $difficulty,
                'picture' => 'https://example.com/example.jpg',
            ]
        );
    }

    private function createIngredient($name)
    {
        return Ingredient::firstOrCreate(
            ['slug' => str_replace(' ', '_', strtolower($name))],
            [
                'name' => $name,
                'picture' => 'https://example.com/example.jpg',
                'allergens' => 'none',
            ]
        );
    }

    private function createStep($description)
    {
        return Step::firstOrCreate(
            ['description' => $description],
            [
                'picture' => 'https://example.com/example.jpg',
            ]
        );
    }
}
