<?php

namespace Tests\Api;

use GuzzleHttp\Client;
use Tests\TestSuite;

class ApiTest extends TestSuite
{
    protected $client;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->client = new Client();
        $this->url = getenv('URL');
        
        // TODO create Test DB
    }
    
    public function test_list_recipes()
    {
        echo "API test: GET /recipes and get a list of recipes";
        $response = $this->client->request('GET', $this->url.'/recipes');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_create_recipe()
    {
        echo "API test: POST /recipes and create a new recipe";
        $response = $this->client->request('POST', $this->url.'/recipes');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_create_recipes()
    {
        echo "API test: POST /recipes and create multiple recipes";
        $response = $this->client->request('POST', $this->url.'/recipes');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_get_recipe()
    {
        echo "API test: GET /recipes/{id} and get the given recipe";
        $response = $this->client->request('GET', $this->url.'/recipes/1');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_edit_recipe()
    {
        echo "API test: PUT /recipes/{id} and edit the given recipe";
        $response = $this->client->request('PUT', $this->url.'/recipes/1');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_edit_recipe_part()
    {
        echo "API test: PATCH /recipes/{id} and edit part of the given recipe";
        $response = $this->client->request('PUT', $this->url.'/recipes/1');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_delete_recipe()
    {
        echo "API test: DELETE /recipes/{id} and remove the given recipe";
        $response = $this->client->request('DELETE', $this->url.'/recipes/1');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
    
    public function test_create_rating()
    {
        echo "API test: PATCH /recipes/{id}/rating and evaluate the given recipe";
        $response = $this->client->request('POST', $this->url.'/recipes/1/rating');
        $this->assertContains('Hi', (string) $response->getBody());
        
        $this->markTestIncomplete('JSON API not implemented yet.');
    }
}