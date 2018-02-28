<?php

namespace Recipeland\Controllers;

use Recipeland\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class Errors extends Controller
{
    
    public function _continue() {
        $this->setStatus(100);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function switching_protocols() {
        $this->setStatus(101);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function processing() {
        $this->setStatus(102);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function ok() {
        $this->setStatus(200);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function created() {
        $this->setStatus(201);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function accepted() {
        $this->setStatus(202);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function non_authoritative_information() {
        $this->setStatus(203);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function no_content() {
        $this->setStatus(204);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function reset_content() {
        $this->setStatus(205);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function partial_content() {
        $this->setStatus(206);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function multi_status() {
        $this->setStatus(207);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function already_reported() {
        $this->setStatus(208);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function multiple_choices() {
        $this->setStatus(300);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function moved_permanently() {
        $this->setStatus(301);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function found() {
        $this->setStatus(302);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function see_other() {
        $this->setStatus(303);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_modified() {
        $this->setStatus(304);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function use_proxy() {
        $this->setStatus(305);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function switch_proxy() {
        $this->setStatus(306);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function temporary_redirect() {
        $this->setStatus(307);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function bad_request() {
        $this->setStatus(400);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unauthorized() {
        $this->setStatus(401);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function payment_required() {
        $this->setStatus(402);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function forbidden() {
        $this->setStatus(403);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_found() {
        $this->setStatus(404);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function method_not_allowed() {
        $this->setStatus(405);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_acceptable() {
        $this->setStatus(406);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function proxy_authentication_required() {
        $this->setStatus(407);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_time_out() {
        $this->setStatus(408);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function conflict() {
        $this->setStatus(409);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function gone() {
        $this->setStatus(410);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function length_required() {
        $this->setStatus(411);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function precondition_failed() {
        $this->setStatus(412);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_entity_too_large() {
        $this->setStatus(413);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_uri_too_large() {
        $this->setStatus(414);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unsupported_media_type() {
        $this->setStatus(415);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function requested_range_not_satisfiable() {
        $this->setStatus(416);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function expectation_failed() {
        $this->setStatus(417);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function im_a_teapot() {
        $this->setStatus(418);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unprocessable_entity() {
        $this->setStatus(422);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function locked() {
        $this->setStatus(423);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function failed_dependency() {
        $this->setStatus(424);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unordered_collection() {
        $this->setStatus(425);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function upgrade_required() {
        $this->setStatus(426);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function precondition_required() {
        $this->setStatus(428);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function too_many_requests() {
        $this->setStatus(429);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_header_fields_too_large() {
        $this->setStatus(431);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unavailable_for_legal_reasons() {
        $this->setStatus(451);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function internal_server_error() {
        $this->setStatus(500);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_implemented() {
        $this->setStatus(501);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function bad_gateway() {
        $this->setStatus(502);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function service_unavailable() {
        $this->setStatus(503);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function gateway_time_out() {
        $this->setStatus(504);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function http_version_not_supported() {
        $this->setStatus(505);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function variant_also_negotiates() {
        $this->setStatus(506);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function insufficient_storage() {
        $this->setStatus(507);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function loop_detected() {
        $this->setStatus(508);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function network_authentication_required() {
        $this->setStatus(511);
        $this->setJson(['error' => $this->response->getReasonPhrase()]);
    }
    
}