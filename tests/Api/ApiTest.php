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
    protected $container;
    protected $url;
    
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

        $response = $this->request('/recipes');
        $this->assertHeaders($response);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected, $responseArray);
    }

    public function test_create_recipe()
    {
        echo 'API test: POST /recipes and create a new recipe';

        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_create_recipes()
    {
        echo 'API test: POST /recipes and create multiple recipes';

        $this->markTestIncomplete('Auth not implemented yet.');
    }

    public function test_get_recipe_1()
    {
        echo 'API test: GET /recipes/1 and get the given recipe';

        $response = $this->request('/recipes/1');
        $this->assertHeaders($response);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected['data'][0], $responseArray);
    }
    
    public function test_get_recipe_2()
    {
        echo 'API test: GET /recipes/2 and get the given recipe';

        $response = $this->request('/recipes/2');
        $this->assertHeaders($response);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals($this->expected['data'][1], $responseArray);
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

        $response = $this->request('/recipes/non/existent/route');
        $this->assertHeaders($response, 404);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals(['error' => 'Not Found'], $responseArray);
    }

    public function test_405()
    {
        echo 'API test: GET /recipes/{id}/rating and receive 405 - Method Not Allowed';

        $response = $this->request('/recipes/1/rating');
        $this->assertHeaders($response, 405);
        $responseArray = $this->jsonToArray($response);
        $this->assertEquals(['error' => 'Method Not Allowed'], $responseArray);
    }
    
    
    
    /***********************************************************************
     *                       API HELPER METHODS                            *
     ***********************************************************************/
    public function request(string $path, string $method='GET', string $scheme='https'): ResponseInterface
    {
        $request = (new Request($method, $this->url.$path))->withHeader('x-forwarded-proto', $scheme);
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
        if(getenv('ENVIRONMENT') == 'testing' && 
           getenv('DB_CONNECTION') == 'pgtest') {
            $phinx = new PhinxApplication();
            $wrapper = new TextWrapper($phinx);
            $wrapper->setOption('configuration', BASE_DIR.'/phinx.php');
            $wrapper->setOption('environment', 'testing');
            $wrapper->setOption('parser', 'PHP');
            
            switch ($command)
            {
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
