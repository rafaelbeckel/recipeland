<?php declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Errors extends Controller
{
    public function continue(Request $request, string $message = null)
    {
        $this->setStatus(100);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function switching_protocols(Request $request, string $message = null)
    {
        $this->setStatus(101);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function processing(Request $request, string $message = null)
    {
        $this->setStatus(102);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function ok(Request $request, string $message = null)
    {
        $this->setStatus(200);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function created(Request $request, string $message = null)
    {
        $this->setStatus(201);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function accepted(Request $request, string $message = null)
    {
        $this->setStatus(202);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function non_authoritative_information(Request $request, string $message = null)
    {
        $this->setStatus(203);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function no_content(Request $request, string $message = null)
    {
        $this->setStatus(204);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function reset_content(Request $request, string $message = null)
    {
        $this->setStatus(205);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function partial_content(Request $request, string $message = null)
    {
        $this->setStatus(206);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function multi_status(Request $request, string $message = null)
    {
        $this->setStatus(207);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function already_reported(Request $request, string $message = null)
    {
        $this->setStatus(208);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function multiple_choices(Request $request, string $message = null)
    {
        $this->setStatus(300);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function moved_permanently(Request $request, string $message = null)
    {
        $this->setStatus(301);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function found(Request $request, string $message = null)
    {
        $this->setStatus(302);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function see_other(Request $request, string $message = null)
    {
        $this->setStatus(303);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function not_modified(Request $request, string $message = null)
    {
        $this->setStatus(304);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function use_proxy(Request $request, string $message = null)
    {
        $this->setStatus(305);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function switch_proxy(Request $request, string $message = null)
    {
        $this->setStatus(306);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function temporary_redirect(Request $request, string $message = null)
    {
        $this->setStatus(307);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function bad_request(Request $request, string $message = null)
    {
        $this->setStatus(400);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function unauthorized(Request $request, string $message = null)
    {
        $this->setStatus(401);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function payment_required(Request $request, string $message = null)
    {
        $this->setStatus(402);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function forbidden(Request $request, string $message = null)
    {
        $this->setStatus(403);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function not_found(Request $request, string $message = null)
    {
        $this->setStatus(404);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function method_not_allowed(Request $request, string $message = null)
    {
        $this->setStatus(405);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function not_acceptable(Request $request, string $message = null)
    {
        $this->setStatus(406);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function proxy_authentication_required(Request $request, string $message = null)
    {
        $this->setStatus(407);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function request_time_out(Request $request, string $message = null)
    {
        $this->setStatus(408);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function conflict(Request $request, string $message = null)
    {
        $this->setStatus(409);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function gone(Request $request, string $message = null)
    {
        $this->setStatus(410);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function length_required(Request $request, string $message = null)
    {
        $this->setStatus(411);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function precondition_failed(Request $request, string $message = null)
    {
        $this->setStatus(412);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function request_entity_too_large(Request $request, string $message = null)
    {
        $this->setStatus(413);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function request_uri_too_large(Request $request, string $message = null)
    {
        $this->setStatus(414);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function unsupported_media_type(Request $request, string $message = null)
    {
        $this->setStatus(415);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function requested_range_not_satisfiable(Request $request, string $message = null)
    {
        $this->setStatus(416);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function expectation_failed(Request $request, string $message = null)
    {
        $this->setStatus(417);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function im_a_teapot(Request $request, string $message = null)
    {
        $this->setStatus(418);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function unprocessable_entity(Request $request, string $message = null)
    {
        $this->setStatus(422);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function locked(Request $request, string $message = null)
    {
        $this->setStatus(423);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function failed_dependency(Request $request, string $message = null)
    {
        $this->setStatus(424);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function unordered_collection(Request $request, string $message = null)
    {
        $this->setStatus(425);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function upgrade_required(Request $request, string $message = null)
    {
        $this->setStatus(426);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function precondition_required(Request $request, string $message = null)
    {
        $this->setStatus(428);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function too_many_requests(Request $request, string $message = null)
    {
        $this->setStatus(429);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function request_header_fields_too_large(Request $request, string $message = null)
    {
        $this->setStatus(431);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function unavailable_for_legal_reasons(Request $request, string $message = null)
    {
        $this->setStatus(451);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function internal_server_error(Request $request, string $message = null)
    {
        $this->setStatus(500);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function not_implemented(Request $request, string $message = null)
    {
        $this->setStatus(501);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function bad_gateway(Request $request, string $message = null)
    {
        $this->setStatus(502);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function service_unavailable(Request $request, string $message = null)
    {
        $this->setStatus(503);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function gateway_time_out(Request $request, string $message = null)
    {
        $this->setStatus(504);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function http_version_not_supported(Request $request, string $message = null)
    {
        $this->setStatus(505);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function variant_also_negotiates(Request $request, string $message = null)
    {
        $this->setStatus(506);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function insufficient_storage(Request $request, string $message = null)
    {
        $this->setStatus(507);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function loop_detected(Request $request, string $message = null)
    {
        $this->setStatus(508);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
    
    public function network_authentication_required(Request $request, string $message = null)
    {
        $this->setStatus(511);
        $this->setJsonResponse(['error' => $this->response->getReasonPhrase()], $message);
    }
}
