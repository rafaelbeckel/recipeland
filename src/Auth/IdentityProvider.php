<?php declare(strict_types=1);

namespace App\Security\User;

use Recipeland\Data\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class IdentityProvider implements UserProviderInterface
{
    public function loadUserByUsername($username): UserInterface
    {
        try() {
            $user = User::where('username', $username)->firstOrFail();
        } catch($e) {
            throw new UsernameNotFoundException(
                'User "'.$username.'" not found.'
            );
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                'Instances of "'.get_class($user).'" are not supported.'
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
