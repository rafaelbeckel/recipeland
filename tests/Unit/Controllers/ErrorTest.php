<?php

namespace Tests\Unit\Controllers;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Recipeland\Controllers\AbstractController as Controller;
use Recipeland\Interfaces\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface as Logger;
use Tests\Unit\Http\Request\IWantThis;
use GuzzleHttp\Psr7\ServerRequest;
use Recipeland\Controllers\Errors;
use Recipeland\Helpers\Factory;
use Tests\TestSuite;

class ErrorTest extends TestSuite
{
    protected $handler;
    protected $logger;
    
    public function setUp()
    {
        parent::setUp();
        $this->request = new ServerRequest('GET', '/foo');
        $this->handler = $this->createMock(HandlerInterface::class);
    }
    
    protected function buildController($action, $arguments): Controller
    {
        $logger = $this->createMock(Logger::class);
        
        $class = new Errors($action, $arguments, $logger);
        
        return $class;
    }
    
    public function test_continue()
    {
        echo 'Error - test 100 continue';
        
        $controller = $this->buildController('continue', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(100, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_switching_protocols()
    {
        echo 'Error - test 101 switching_protocols';
        
        $controller = $this->buildController('switching_protocols', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(101, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_processing()
    {
        echo 'Error - test 102 processing';
        
        $controller = $this->buildController('processing', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(102, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_ok()
    {
        echo 'Error - test 200 ok';
        
        $controller = $this->buildController('ok', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_created()
    {
        echo 'Error - test 201 created';
        
        $controller = $this->buildController('created', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_accepted()
    {
        echo 'Error - test 202 accepted';
        
        $controller = $this->buildController('accepted', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_non_authoritative_information()
    {
        echo 'Error - test 203 non_authoritative_information';
        
        $controller = $this->buildController('non_authoritative_information', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(203, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_no_content()
    {
        echo 'Error - test 204 no_content';
        
        $controller = $this->buildController('no_content', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_reset_content()
    {
        echo 'Error - test 205 reset_content';
        
        $controller = $this->buildController('reset_content', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(205, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_partial_content()
    {
        echo 'Error - test 206 partial_content';
        
        $controller = $this->buildController('partial_content', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(206, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_multi_status()
    {
        echo 'Error - test 207 multi_status';
        
        $controller = $this->buildController('multi_status', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(207, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_already_reported()
    {
        echo 'Error - test 208 already_reported';
        
        $controller = $this->buildController('already_reported', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(208, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_multiple_choices()
    {
        echo 'Error - test 300 multiple_choices';
        
        $controller = $this->buildController('multiple_choices', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(300, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_moved_permanently()
    {
        echo 'Error - test 301 moved_permanently';
        
        $controller = $this->buildController('moved_permanently', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_found()
    {
        echo 'Error - test 302 found';
        
        $controller = $this->buildController('found', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_see_other()
    {
        echo 'Error - test 303 see_other';
        
        $controller = $this->buildController('see_other', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_not_modified()
    {
        echo 'Error - test 304 not_modified';
        
        $controller = $this->buildController('not_modified', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(304, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_use_proxy()
    {
        echo 'Error - test 305 use_proxy';
        
        $controller = $this->buildController('use_proxy', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(305, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_switch_proxy()
    {
        echo 'Error - test 306 switch_proxy';
        
        $controller = $this->buildController('switch_proxy', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_temporary_redirect()
    {
        echo 'Error - test 307 temporary_redirect';
        
        $controller = $this->buildController('temporary_redirect', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_bad_request()
    {
        echo 'Error - test 400 bad_request';
        
        $controller = $this->buildController('bad_request', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_unauthorized()
    {
        echo 'Error - test 401 unauthorized';
        
        $controller = $this->buildController('unauthorized', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_payment_required()
    {
        echo 'Error - test 402 payment_required';
        
        $controller = $this->buildController('payment_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_forbidden()
    {
        echo 'Error - test 403 forbidden';
        
        $controller = $this->buildController('forbidden', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_not_found()
    {
        echo 'Error - test 404 not_found';
        
        $controller = $this->buildController('not_found', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_method_not_allowed()
    {
        echo 'Error - test 405 method_not_allowed';
        
        $controller = $this->buildController('method_not_allowed', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_not_acceptable()
    {
        echo 'Error - test 406 not_acceptable';
        
        $controller = $this->buildController('not_acceptable', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(406, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_proxy_authentication_required()
    {
        echo 'Error - test 407 proxy_authentication_required';
        
        $controller = $this->buildController('proxy_authentication_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(407, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_request_time_out()
    {
        echo 'Error - test 408 request_time_out';
        
        $controller = $this->buildController('request_time_out', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(408, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_conflict()
    {
        echo 'Error - test 409 conflict';
        
        $controller = $this->buildController('conflict', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_gone()
    {
        echo 'Error - test 410 gone';
        
        $controller = $this->buildController('gone', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(410, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_length_required()
    {
        echo 'Error - test 411 length_required';
        
        $controller = $this->buildController('length_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(411, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_precondition_failed()
    {
        echo 'Error - test 412 precondition_failed';
        
        $controller = $this->buildController('precondition_failed', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(412, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_request_entity_too_large()
    {
        echo 'Error - test 413 request_entity_too_large';
        
        $controller = $this->buildController('request_entity_too_large', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(413, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_request_uri_too_large()
    {
        echo 'Error - test 414 request_uri_too_large';
        
        $controller = $this->buildController('request_uri_too_large', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(414, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_unsupported_media_type()
    {
        echo 'Error - test 415 unsupported_media_type';
        
        $controller = $this->buildController('unsupported_media_type', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(415, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_requested_range_not_satisfiable()
    {
        echo 'Error - test 416 requested_range_not_satisfiable';
        
        $controller = $this->buildController('requested_range_not_satisfiable', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(416, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_expectation_failed()
    {
        echo 'Error - test 417 expectation_failed';
        
        $controller = $this->buildController('expectation_failed', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(417, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_im_a_teapot()
    {
        echo 'Error - test 418 im_a_teapot';
        
        $controller = $this->buildController('im_a_teapot', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(418, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_unprocessable_entity()
    {
        echo 'Error - test 422 unprocessable_entity';
        
        $controller = $this->buildController('unprocessable_entity', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_locked()
    {
        echo 'Error - test 423 locked';
        
        $controller = $this->buildController('locked', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(423, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_failed_dependency()
    {
        echo 'Error - test 424 failed_dependency';
        
        $controller = $this->buildController('failed_dependency', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(424, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_unordered_collection()
    {
        echo 'Error - test 425 unordered_collection';
        
        $controller = $this->buildController('unordered_collection', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(425, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_upgrade_required()
    {
        echo 'Error - test 426 upgrade_required';
        
        $controller = $this->buildController('upgrade_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(426, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_precondition_required()
    {
        echo 'Error - test 428 precondition_required';
        
        $controller = $this->buildController('precondition_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(428, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_too_many_requests()
    {
        echo 'Error - test 429 too_many_requests';
        
        $controller = $this->buildController('too_many_requests', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_request_header_fields_too_large()
    {
        echo 'Error - test 431 request_header_fields_too_large';
        
        $controller = $this->buildController('request_header_fields_too_large', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(431, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_unavailable_for_legal_reasons()
    {
        echo 'Error - test 451 unavailable_for_legal_reasons';
        
        $controller = $this->buildController('unavailable_for_legal_reasons', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(451, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_internal_server_error()
    {
        echo 'Error - test 500 internal_server_error';
        
        $controller = $this->buildController('internal_server_error', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_not_implemented()
    {
        echo 'Error - test 501 not_implemented';
        
        $controller = $this->buildController('not_implemented', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(501, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_bad_gateway()
    {
        echo 'Error - test 502 bad_gateway';
        
        $controller = $this->buildController('bad_gateway', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(502, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_service_unavailable()
    {
        echo 'Error - test 503 service_unavailable';
        
        $controller = $this->buildController('service_unavailable', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(503, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_gateway_time_out()
    {
        echo 'Error - test 504 gateway_time_out';
        
        $controller = $this->buildController('gateway_time_out', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(504, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_http_version_not_supported()
    {
        echo 'Error - test 505 http_version_not_supported';
        
        $controller = $this->buildController('http_version_not_supported', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(505, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_variant_also_negotiates()
    {
        echo 'Error - test 506 variant_also_negotiates';
        
        $controller = $this->buildController('variant_also_negotiates', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(506, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_insufficient_storage()
    {
        echo 'Error - test 507 insufficient_storage';
        
        $controller = $this->buildController('insufficient_storage', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(507, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_loop_detected()
    {
        echo 'Error - test 508 loop_detected';
        
        $controller = $this->buildController('loop_detected', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(508, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
    
    public function test_network_authentication_required()
    {
        echo 'Error - test 511 network_authentication_required';
        
        $controller = $this->buildController('network_authentication_required', ['message'=>'foo']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(511, $response->getStatusCode());
        $this->assertEquals('foo', json_decode((string) $response->getBody())->message);
    }
}
