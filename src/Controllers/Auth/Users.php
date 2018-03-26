<?php

declare(strict_types=1);

namespace Recipeland\Controllers\Auth;

use Recipeland\Data\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Recipeland\Http\Request\LoginRequest;
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
            $roles[$role->name] = 1;
            foreach ($role->permissions as $permission) {
                $permissions[$permission->name] = 1;
            }
        }
        
        $user->updated_at = date("Y-m-d H:i:s");
        
        $signer = new Sha512();
        $keychain = new Keychain();
        $token = (new Builder())
            ->setIssuer(getenv('JWT_ISSUER'))
            ->setAudience(getenv('JWT_AUDIENCE'))
            ->setId($user->username.' '.$user->updated_at)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 2) //match user updated_at precision
            ->setExpiration(time()+getenv('JWT_EXPIRATION_TIME'))
            ->set('user_id', $user->id)
            ->set('permissions', $permissions)
            ->set('roles', $roles)
            ->sign(
                $signer,
                $keychain->getPrivateKey(
                    'file://'.BASE_DIR.getenv('PRIVATE_KEY')
                )
            )
            ->getToken();
        
        $user->timestamps = false; // Disable autosaving timestamps
        $user->save();
        
        $this->setHeader('Authorization', 'Bearer '.$token);
        $this->setJsonResponse(['status' => 'Authorized']);
    }
}
