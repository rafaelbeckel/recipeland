<?php

declare(strict_types=1);

namespace Recipeland\Controllers\Auth;

use Recipeland\Data\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Recipeland\Http\Request\LoginRequest;
use Lcobucci\JWT\Signer\Rsa\Sha512 as RsaSha512;
use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Users extends Controller
{
    /**
     * @description
     * Authenticate a user
     *
     * @params ServerRequestInterface
     **/
    public function login(LoginRequest $request)
    {
        $username = $request->getParam('username');
        $password = $request->getParam('password');
        
        $user = User::where('username', $username)
                    ->orWhere('email', $username)
                    ->with('roles.permissions')
                    ->first();
        
        if (!$user || !$user->verifyPassword($password)) {
            return $this->error('unauthorized');
        }
        
        $roles = [];
        $permissions = [];
        foreach ($user->roles as $role) {
            $roles[$role->name] = $role->display_name;
            foreach ($role->permissions as $permission) {
                $permissions[$permission->name] = $permission->display_name;
            }
        }
        
        $user->updated_at = date("Y-m-d H:i:s");
        
        $signer = new RsaSha512();
        $keychain = new Keychain();
        $user->token = (new Builder())->setIssuer(getenv('JWT_ISSUER'))
                                      ->setAudience(getenv('JWT_AUDIENCE'))
                                      ->setId($user->username.' '.$user->updated_at)
                                      ->setIssuedAt(time())
                                      ->setNotBefore(time())
                                      ->setExpiration(time()+getenv('JWT_EXPIRATION_TIME'))
                                      ->set('user_id', $user->id)
                                      ->set('username', $user->username)
                                      ->set('permissions', $permissions)
                                      ->set('roles', $roles)
                                      ->sign(
                                          $signer, 
                                          $keychain->getPrivateKey(
                                              'file://'.BASE_DIR.getenv('PRIVATE_KEY')
                                          ))
                                      ->getToken();
         
        // Disable autosaving timestamps
        $user->timestamps = false;
        $user->save();
        $user->timestamps = true;
        
        $this->setHeader('Authorization', 'Bearer '.$user->token);
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
