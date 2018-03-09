<?php declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User extends Model implements UserInterface, EquatableInterface
{
    protected $fillable = ['name', 'username', 'password', 'email'];
    
    protected $hidden = ['password', 'token', 'deleted_at', 'email', 'username'];
    
    use EntrustUserTrait, SoftDeletes {
        SoftDeletes::restore as sfRestore;
        EntrustUserTrait::restore as euRestore;
    }
    
    public function restore(): void 
    {
        $this->sfRestore();
        Cache::tags($this->table)->flush();
    }
    
    public function createPassword(string $password): void
    {
        $this->password = password_hash($password , PASSWORD_BCRYPT);
        $this->save();
    }
    
    public function verifyPassword(string $password): bool
    {
        return verify_password($password, $this->password);
    }
    
    public function recipes()
    {
        return $this->hasMany('Recipeland\Data\Recipe', 'created_by');
    }
    
    public function getRoles(): array
    {
        $symfony_roles = ['ROLE_USER'];
        $recipeland_roles = $this->roles()->get();
        
        foreach($recipeland_roles as $role) {
            $symfony_roles[] = 'ROLE_' . strtoupper($role);
        }
        
        return $symfony_roles;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function getSalt(): void
    {
        // Salt not needed in PHP's bcrypt implementation
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function eraseCredentials() 
    {
        // Do nothing
    }
    
    public function isEqualTo(UserInterface $user): bool
    {
        if ($this->username !== $user->getUsername()) {
            return false;
        }
        
        if ($this->password !== $user->getPassword()) {
            return false;
        }
        
        if (!$user instanceof Model) {
            return false;
        }
        
        return true;
    }
    
    public function firstName()
    {
        $firstName = head( explode(' ', $this->name) );
        return str_replace('_', ' ', $firstName);
    }

    public function lastName()
    {
        $lastName = last( explode(' ', $this->name) );
        return str_replace('_', ' ', $lastName);
    }
    
    public function attachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }
        
        if (! $this->roles()->where('role_id',$role)->count()) {
            $this->roles()->attach($role);
        }
    }
}