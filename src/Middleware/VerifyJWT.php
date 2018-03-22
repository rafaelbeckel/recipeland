<?php declare(strict_types=1);

namespace Recipeland\Middleware;

use RuntimeException;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Recipeland\Helpers\Rules\IsJwt;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Traits\ReturnsErrorResponse;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class VerifyJWT implements MiddlewareInterface
{
    use ReturnsErrorResponse;
    
    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        $this->db = $request->getAttribute('db');
        $header = $request->getHeader('authorization')[0] ?? null;
        
        if (!empty($header)) {
            if (!$this->validate($header)) {
                return $this->errorResponse('bad_request', $request, $next, 'Malformed Authorization Header.');
            }
            
            try {
                $jwt = $this->parse($header);
            } catch (RuntimeException $e) {
                return $this->errorResponse('bad_request', $request, $next, 'Malformed JWT string.');
            }
            
            if (!$this->verifySignature($jwt)) {
                return $this->errorResponse('unauthorized', $request, $next, 'Invalid JWT signature.');
            };
            
            if (!$this->verifyTokenClaims($jwt)) {
                return $this->errorResponse('unauthorized', $request, $next, 'Expired JWT or invalid claims.');
            };
            
            if (!$this->verifyOwnerUpdates($jwt)) {
                return $this->errorResponse('unauthorized', $request, $next, 'Updated user needs a new token.');
            };
            
            $request = $request->withAttribute('jwt', $jwt);
        }
        
        $response = $next->handle($request);
            
        return $response;
    }
    
    private function validate(string $header): bool
    {
        $rule = new IsJwt($header);
        return $rule->apply();
    }
    
    private function parse(string $header): Token
    {
        [$type, $token] = explode(' ', $header);
        $parser = new Parser();
        $jwt = $parser->parse((string) $token);
        
        return $jwt;
    }
    
    private function verifySignature(Token $jwt): bool
    {
        $signer = new Sha512();
        $keychain = new Keychain();
        $key = $keychain->getPublicKey('file://'.BASE_DIR.getenv('PUBLIC_KEY'));
       
        return $jwt->verify($signer, $key);
    }
    
    private function verifyTokenClaims(Token $jwt): bool
    {
        $expected = new ValidationData(); // It auto-validates iat, nbf and exp
        $expected->setIssuer(getenv('JWT_ISSUER'));
        $expected->setAudience(getenv('JWT_AUDIENCE'));
        
        return (bool) $jwt->validate($expected);
    }
    
    private function verifyOwnerUpdates(Token $jwt): bool
    {
        $jti = $jwt->getClaim('jti');
        $uid = $jwt->getClaim('user_id');
            
        $user = $this->db->table('users')
                         ->select('username', 'updated_at')
                         ->where('id', $uid)
                         ->first();
        
        return (bool) $user && $jti == $this->expectedJtiFor($user);
    }
    
    private function expectedJtiFor($user): string
    {
        return $user->username.' '.$user->updated_at;
    }
}
