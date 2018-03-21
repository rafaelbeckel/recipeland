<?php declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Errors extends Controller
{
    public function continue(Request $request, string $message = null)
    {
        $this->setStatus(100);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function switching_protocols(Request $request, string $message = null)
    {
        $this->setStatus(101);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function processing(Request $request, string $message = null)
    {
        $this->setStatus(102);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function ok(Request $request, string $message = null)
    {
        $this->setStatus(200);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function created(Request $request, string $message = null)
    {
        $this->setStatus(201);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function accepted(Request $request, string $message = null)
    {
        $this->setStatus(202);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function non_authoritative_information(Request $request, string $message = null)
    {
        $this->setStatus(203);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function no_content(Request $request, string $message = null)
    {
        $this->setStatus(204);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function reset_content(Request $request, string $message = null)
    {
        $this->setStatus(205);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function partial_content(Request $request, string $message = null)
    {
        $this->setStatus(206);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function multi_status(Request $request, string $message = null)
    {
        $this->setStatus(207);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function already_reported(Request $request, string $message = null)
    {
        $this->setStatus(208);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function multiple_choices(Request $request, string $message = null)
    {
        $this->setStatus(300);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function moved_permanently(Request $request, string $message = null)
    {
        $this->setStatus(301);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function found(Request $request, string $message = null)
    {
        $this->setStatus(302);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function see_other(Request $request, string $message = null)
    {
        $this->setStatus(303);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function not_modified(Request $request, string $message = null)
    {
        $this->setStatus(304);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function use_proxy(Request $request, string $message = null)
    {
        $this->setStatus(305);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function switch_proxy(Request $request, string $message = null)
    {
        $this->setStatus(306);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function temporary_redirect(Request $request, string $message = null)
    {
        $this->setStatus(307);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function bad_request(Request $request, string $message = null)
    {
        $this->setStatus(400);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function unauthorized(Request $request, string $message = null)
    {
        $this->setStatus(401);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function payment_required(Request $request, string $message = null)
    {
        $this->setStatus(402);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function forbidden(Request $request, string $message = null)
    {
        $this->setStatus(403);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function not_found(Request $request, string $message = null)
    {
        $this->setStatus(404);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function method_not_allowed(Request $request, string $message = null)
    {
        $this->setStatus(405);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function not_acceptable(Request $request, string $message = null)
    {
        $this->setStatus(406);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function proxy_authentication_required(Request $request, string $message = null)
    {
        $this->setStatus(407);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function request_time_out(Request $request, string $message = null)
    {
        $this->setStatus(408);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function conflict(Request $request, string $message = null)
    {
        $this->setStatus(409);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function gone(Request $request, string $message = null)
    {
        $this->setStatus(410);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function length_required(Request $request, string $message = null)
    {
        $this->setStatus(411);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function precondition_failed(Request $request, string $message = null)
    {
        $this->setStatus(412);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function request_entity_too_large(Request $request, string $message = null)
    {
        $this->setStatus(413);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function request_uri_too_large(Request $request, string $message = null)
    {
        $this->setStatus(414);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function unsupported_media_type(Request $request, string $message = null)
    {
        $this->setStatus(415);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function requested_range_not_satisfiable(Request $request, string $message = null)
    {
        $this->setStatus(416);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function expectation_failed(Request $request, string $message = null)
    {
        $this->setStatus(417);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function im_a_teapot(Request $request, string $message = null)
    {
        $this->setStatus(418);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function unprocessable_entity(Request $request, string $message = null)
    {
        $this->setStatus(422);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function locked(Request $request, string $message = null)
    {
        $this->setStatus(423);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function failed_dependency(Request $request, string $message = null)
    {
        $this->setStatus(424);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function unordered_collection(Request $request, string $message = null)
    {
        $this->setStatus(425);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function upgrade_required(Request $request, string $message = null)
    {
        $this->setStatus(426);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function precondition_required(Request $request, string $message = null)
    {
        $this->setStatus(428);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function too_many_requests(Request $request, string $message = null)
    {
        $this->setStatus(429);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function request_header_fields_too_large(Request $request, string $message = null)
    {
        $this->setStatus(431);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function unavailable_for_legal_reasons(Request $request, string $message = null)
    {
        $this->setStatus(451);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function internal_server_error(Request $request, string $message = null)
    {
        $this->setStatus(500);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function not_implemented(Request $request, string $message = null)
    {
        $this->setStatus(501);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function bad_gateway(Request $request, string $message = null)
    {
        $this->setStatus(502);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function service_unavailable(Request $request, string $message = null)
    {
        $this->setStatus(503);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function gateway_time_out(Request $request, string $message = null)
    {
        $this->setStatus(504);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function http_version_not_supported(Request $request, string $message = null)
    {
        $this->setStatus(505);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function variant_also_negotiates(Request $request, string $message = null)
    {
        $this->setStatus(506);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function insufficient_storage(Request $request, string $message = null)
    {
        $this->setStatus(507);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function loop_detected(Request $request, string $message = null)
    {
        $this->setStatus(508);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
    
    public function network_authentication_required(Request $request, string $message = null)
    {
        $this->setStatus(511);
        $json = ['error' => $this->response->getReasonPhrase()];
        if ($message) {
            $json['message'] = $message;
        }
        $this->setJsonResponse($json);
    }
}
