<?php

declare(strict_types=1);

namespace Tests\Api;

use Recipeland\App;
use Tests\TestSuite;
use Phinx\Config\Config;
use Recipeland\Data\Step;
use Recipeland\Data\User;
use Recipeland\Data\Recipe;
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
    
    protected $container;
    protected $url;
    
    protected $homer_token;
    protected $luigi_token;
    protected $burns_token;
    
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
        'to' => 2,
        'total' => 2,
    ];
    
    protected $newRecipe = '{
        "recipe" : {
            "author" : "luigi",
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
                    "units" : "mg",
                    "name" : "My Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "milk"
                },
                {
                    "slug" : "my_second_ingredient",
                    "quantity" : "10",
                    "units" : "ml",
                    "name" : "My Second Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "none"
                }
            ],
            "steps" : [
                {
                    "description" : "Put ingredient somewhere",
                    "picture" : "https://example.com/picture.jpg",
                    "order" : 1
                },
                {
                    "description" : "Eat Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "order" : 2
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
        ];
    }
    
    public function tearDown()
    {
        parent::tearDown();

        $this->database('rollback');
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
        echo 'API test: POST /login and receive a JWT';
        
        $body = '{
            "username" : "luigi",
            "password" : "Pasta1234!"
        }';
        
        $response = $this->request('POST', '/auth/login', $body);
        $this->assertEquals('{"status":"Authorized"}', (string) $response->getBody());
        $this->assertRegExp(self::JWT_PATTERN, $response->getHeader('authorization')[0]);
        $this->assertHeaders($response);
    }
    
    public function test_login_wrong_password()
    {
        echo 'API test: POST /login with wrong password and receive 401';
        
        $body = '{
            "username" : "luigi",
            "password" : "Pasta123!"
        }';
        
        $response = $this->request('POST', '/auth/login', $body);
        $this->assertEquals('{"error":"Unauthorized"}', (string) $response->getBody());
        $this->assertHeaders($response, 401);
    }

    public function test_create_recipe()
    {
        echo 'API test: POST /recipes and create a new recipe';
        
        $header = [
            'authorization' => 'Bearer abc.def.ghi'
        ];
        
        $response = $this->request('POST', '/recipes', $this->newRecipe, $header);
        $responseArray = $this->jsonToArray($response);
        $this->assertHeaders($response);
        
        $input = json_decode($this->newRecipe);
        $recipe = Recipe::find(3); //new Recipe will have ID 3
        
        $this->assertEquals($input->recipe->name, $recipe->name);
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

        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_edit_recipe_part()
    {
        echo 'API test: PATCH /recipes/{id} and edit part of the given recipe';

        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_delete_recipe()
    {
        echo 'API test: DELETE /recipes/{id} and remove the given recipe';
        
        
        
        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_create_rating()
    {
        echo 'API test: POST /recipes/{id}/rating and evaluate the given recipe once';

        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_404()
    {
        echo 'API test: GET /recipes/non/existent/route and receive 404 - Not Found';

        $response = $this->request('GET', '/recipes/non/existent/route');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals(['error' => 'Not Found'], $responseArray);
        $this->assertHeaders($response, 404);
    }

    public function test_405()
    {
        echo 'API test: GET /recipes/{id}/rating and receive 405 - Method Not Allowed';

        $response = $this->request('GET', '/recipes/1/rating');
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals(['error' => 'Method Not Allowed'], $responseArray);
        $this->assertHeaders($response, 405);
    }
    
    
    
    /***********************************************************************
     *                       API HELPER METHODS                            *
     ***********************************************************************/
    public function request(string $method, string $path, string $body = null, array $headers = []): ResponseInterface
    {
        $request = (new Request($method, $this->url.$path, $headers, $body))->withHeader('x-forwarded-proto', 'https');
        $app = $this->container->get(App::class);
        
        return $app->go($request);
    }
    
    public function jsonToArray(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true);
    }
    
    public function assertHeaders(ResponseInterface $response, int $status=200): void
    {
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json;charset=utf-8', $response->getHeader('content-type')[0]);
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
            $this->createRecipe($authors[0], 'Foo'),
            $this->createRecipe($authors[1], 'Bar'),
        ];

        $ingredients = [
            $this->createIngredient('Lol'),
            $this->createIngredient('Lul'),
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

    private function createRecipe(User $author, string $name)
    {
        return Recipe::firstOrCreate(
            ['name' => 'Test Recipe '.$name],
            [
                'created_by' => $author->id,
                'subtitle' => 'Test Recipe by '.$author->name,
                'description' => 'TESTING',
                'prep_time' => 10,
                'total_time' => 20,
                'vegetarian' => 1,
                'difficulty' => 1,
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
