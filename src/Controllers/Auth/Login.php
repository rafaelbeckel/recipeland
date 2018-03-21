<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Lumi\Data\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Recipeland\Http\Requests\LoginRequest;
use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Recipes extends Controller
{
    /**
     * @description
     * Authenticate a user
     *
     * @params ServerRequestInterface
     **/
    public function login(LoginRequest $request)
    {
        $username = $this->getQueryParam('username');
        $password = $this->getQueryParam('password');
        
        $user = User::where('username', $username)
                    ->orWhere('email', $username)
                    ->get();
        
        if (!$user) {
            return $this->error('unauthorized');
        }
        
        $user->generateToken();
        
        $signer = new Sha512();
        $keychain = new Keychain();
        
        $token = (new Builder())->setIssuer(getenv('JWT_ISSUER'))
                                ->setAudience(getenv('JWT_AUDIENCE'))
                                ->setId($user->token)
                                ->setIssuedAt(time())
                                ->setNotBefore(time())
                                ->setExpiration(time() + getenv('JWT_EXPIRATION_TIME_IN_SECONDS'))
                                ->set('user_id', $user->id)
                                ->set('permissions', $user->roles()->)
                                ->getToken();
        
        $this->setHeader('Authorization', 'Bearer '.$token);
        $this->setJsonResponse(['status' => 'Authorized']);
    }

    /**
     * @description
     * Create a new user
     *
     * @params ServerRequestInterface
     **/
    public function register(NewUserRequest $request)
    {
        // Create a new user here
    }
}
