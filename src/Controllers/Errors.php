<?php declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Errors extends Controller
{
    public function continue(Request $request)
    {
        $this->setStatus(100);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function switching_protocols(Request $request)
    {
        $this->setStatus(101);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function processing(Request $request)
    {
        $this->setStatus(102);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function ok(Request $request)
    {
        $this->setStatus(200);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function created(Request $request)
    {
        $this->setStatus(201);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function accepted(Request $request)
    {
        $this->setStatus(202);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function non_authoritative_information(Request $request)
    {
        $this->setStatus(203);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function no_content(Request $request)
    {
        $this->setStatus(204);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function reset_content(Request $request)
    {
        $this->setStatus(205);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function partial_content(Request $request)
    {
        $this->setStatus(206);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function multi_status(Request $request)
    {
        $this->setStatus(207);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function already_reported(Request $request)
    {
        $this->setStatus(208);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function multiple_choices(Request $request)
    {
        $this->setStatus(300);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function moved_permanently(Request $request)
    {
        $this->setStatus(301);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function found(Request $request)
    {
        $this->setStatus(302);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function see_other(Request $request)
    {
        $this->setStatus(303);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_modified(Request $request)
    {
        $this->setStatus(304);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function use_proxy(Request $request)
    {
        $this->setStatus(305);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function switch_proxy(Request $request)
    {
        $this->setStatus(306);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function temporary_redirect(Request $request)
    {
        $this->setStatus(307);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function bad_request(Request $request)
    {
        $this->setStatus(400);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unauthorized(Request $request)
    {
        $this->setStatus(401);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function payment_required(Request $request)
    {
        $this->setStatus(402);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function forbidden(Request $request)
    {
        $this->setStatus(403);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_found(Request $request)
    {
        $this->setStatus(404);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function method_not_allowed(Request $request)
    {
        $this->setStatus(405);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_acceptable(Request $request)
    {
        $this->setStatus(406);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function proxy_authentication_required(Request $request)
    {
        $this->setStatus(407);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_time_out(Request $request)
    {
        $this->setStatus(408);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function conflict(Request $request)
    {
        $this->setStatus(409);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function gone(Request $request)
    {
        $this->setStatus(410);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function length_required(Request $request)
    {
        $this->setStatus(411);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function precondition_failed(Request $request)
    {
        $this->setStatus(412);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_entity_too_large(Request $request)
    {
        $this->setStatus(413);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_uri_too_large(Request $request)
    {
        $this->setStatus(414);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unsupported_media_type(Request $request)
    {
        $this->setStatus(415);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function requested_range_not_satisfiable(Request $request)
    {
        $this->setStatus(416);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function expectation_failed(Request $request)
    {
        $this->setStatus(417);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function im_a_teapot(Request $request)
    {
        $this->setStatus(418);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unprocessable_entity(Request $request)
    {
        $this->setStatus(422);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function locked(Request $request)
    {
        $this->setStatus(423);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function failed_dependency(Request $request)
    {
        $this->setStatus(424);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unordered_collection(Request $request)
    {
        $this->setStatus(425);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function upgrade_required(Request $request)
    {
        $this->setStatus(426);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function precondition_required(Request $request)
    {
        $this->setStatus(428);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function too_many_requests(Request $request)
    {
        $this->setStatus(429);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function request_header_fields_too_large(Request $request)
    {
        $this->setStatus(431);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function unavailable_for_legal_reasons(Request $request)
    {
        $this->setStatus(451);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function internal_server_error(Request $request)
    {
        $this->setStatus(500);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function not_implemented(Request $request)
    {
        $this->setStatus(501);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function bad_gateway(Request $request)
    {
        $this->setStatus(502);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function service_unavailable(Request $request)
    {
        $this->setStatus(503);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function gateway_time_out(Request $request)
    {
        $this->setStatus(504);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function http_version_not_supported(Request $request)
    {
        $this->setStatus(505);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function variant_also_negotiates(Request $request)
    {
        $this->setStatus(506);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function insufficient_storage(Request $request)
    {
        $this->setStatus(507);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function loop_detected(Request $request)
    {
        $this->setStatus(508);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
    
    public function network_authentication_required(Request $request)
    {
        $this->setStatus(511);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()]);
    }
}
