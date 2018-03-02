<?php

namespace Tests\Http;

use Recipeland\Http\Response\Sender;
use GuzzleHttp\Psr7\Response;
use Tests\TestSuite;

use \Exception;

class SenderTest extends TestSuite
{
    public function test_send_method()
    {
        echo "Sender: Testing the response renderer output";
        
        $response = new Response(200, [], "lorem ipsum");
        $sender = new Sender($response);
        
        ob_start();
        $sender->send();
        $output = ob_get_contents();
        
        $this->assertEquals('lorem ipsum', $output);
    }
    
    public function tearDown()
    {
        ob_clean();
        parent::tearDown();
        ob_end_flush();
    }
    
}