<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use TypeError;
use ReflectionMethod;
use BadMethodCallException;
use Psr\Log\LoggerInterface;
use Recipeland\Http\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Traits\ReturnsErrorResponse;
use Recipeland\Interfaces\ControllerInterface;
use Recipeland\Interfaces\SpecializedRequestInterface;
use Recipeland\Interfaces\RequestInterface as RecipelandRequest;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

/**
 * A Controller in Recipeland is a special kind of middleware,
 * intended to be the last one called in the Middleware Stack.
 *
 * Instead of doing the traditional __call black magic hackery,
 * it implements the default handler interface from PSR-7 and
 * additionally provides a set of useful specific methods.
 */
abstract class AbstractController implements ControllerInterface, MiddlewareInterface
{
    use ReturnsErrorResponse;

    protected $error;
    protected $logger;
    protected $action;
    protected $response;
    protected $arguments = [];

    final public function __construct(
        string $action,
        array $arguments = [],
        LoggerInterface $logger = null
    ) {
        $this->logger    = $logger ?: null;
        $this->action    = $action;
        $this->arguments = $arguments;
        $this->response  = new Response();
    }

    public function __call($method, $parameters)
    {
        throw new BadMethodCallException('Method ['.$method.'] does not exist.');
    }

    public function setStatus(int $code): void
    {
        $this->response = $this->response->withStatus($code);
    }

    public function setHeader(string $key, string $value): void
    {
        $this->response = $this->response->withHeader($key, $value);
    }

    public function setResponseBody(string $body): void
    {
        $stream = $this->response->getBody();
        $stream->write($body);

        $this->response = $this->response->withBody($stream);
    }

    public function setJsonResponse(array $json): void
    {
        $this->response = $this->response->withHeader(
                                               'Content-type',
                                               'application/json;charset=utf-8'
                                           );

        $this->setResponseBody(json_encode($json));
    }
    
    protected function error(string $type): void
    {
        $this->error = $type;
    }

    final public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        $actionName = $this->action;
        
        try {
            $request = $this->upgradeRequest($actionName, $request);
            $this->$actionName($request, ...array_values($this->arguments));
            if ($this->error) {
                return $this->errorResponse($this->error, $request, $next);
            }
        } catch (TypeError $e) {
            if ($this->logger) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
            return $this->errorResponse(
                'unauthorized',
                $request,
                $next,
                $request->getAttribute('message')
            );
        } catch (BadMethodCallException $e) {
            if ($this->logger) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
            
            $message = 'Resource not found';
            if (getenv('ENVIRONMENT') == 'development') {
                $message = $e->getMessage().' '.$e->getTraceAsString();
            }

            return $this->errorResponse('not_found', $request, $next, $message);
        }

        return $this->response; // Back to upper middleware layers.
    }

    /**
     * This method will upgrade the PSR-7 Request to a Recipeland Request.
     *
     * A Recipeland Request includes a the convenience method getParam()
     * to the standard Request object. It can have two types: the regular
     * Recipeland Request, or a Specialized Request that includes methods
     * for input validation.
     *
     * A Specialized Request will attach rules to conditionally upgrade
     * the incoming PSR-7 Request. If the input parameters are validated,
     * the Request will be upgraded to a Specialized Request, otherwise it
     * becomes a regular Recipeland Request.
     *
     * All recipeland Controller actions MUST require a Recipeland Request
     * as its first parameter. If the action needs input validation, it can
     * type-hint a Specialized Request, so only validated objects will pass.
     *
     * @param string $actionName
     * @param Psr\Http\Message\ServerRequestInterface $request
     *
     * @returns Recipeland\Interfaces\RequestInterface
     */
    private function upgradeRequest(string $actionName, RequestInterface $request): RecipelandRequest
    {
        $requiredClass = $this->getRequiredRequestClass($actionName);

        $request = Request::upgrade($request);

        // Upgrade to a specialized Request if it's valid
        if ($this->isSpecializedRequest($requiredClass)) {
            $request = $requiredClass::upgradeIfValid($request);
        }

        return $request;
    }

    private function getRequiredRequestClass($actionName)
    {
        $action     = new ReflectionMethod($this, $actionName);
        $firstparam = $action->getParameters()[0] ?? null;

        // Action methods MUST require a request interface as first parameter
        if ($firstparam &&
            $firstparam->getClass() &&
            $this->isRequest($firstparam->getClass()->getName())
        ) {
            return $firstparam->getClass()->getName();
        } else {
            throw new BadMethodCallException(
                'Method ['.$actionName.'] must require a valid Request interface.'
            );
        }
    }

    private function isRequest(string $class): bool
    {
        $interfaces = [$class => $class] + class_implements($class);

        return array_key_exists(RequestInterface::class, $interfaces);
    }

    private function isSpecializedRequest(string $class): bool
    {
        $interfaces = [$class => $class] + class_implements($class);

        return array_key_exists(SpecializedRequestInterface::class, $interfaces);
    }
}
