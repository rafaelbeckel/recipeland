<?php declare(strict_types=1);

namespace Recipeland\Data;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Database\Capsule\Manager as DB;

class Role extends EntrustRole
{
    protected $fillable = ['name', 'display_name', 'description'];
    
    public function permissions()
    {
        return $this->perms();
    }
    
    public function attachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }
        
        
        if (! $this->permissions()->where('permission_id', $permission)->count()) {
            $this->permissions()->attach($permission);
        }
    }
    
    public function attachUser($user)
    {
        if (is_object($user)) {
            $user = $user->getKey();
        }

        if (is_array($user)) {
            $user = $user['id'];
        }
        
        if (! $this->users()->where('user_id', $user)->count()) {
            $this->users()->attach($user);
        }
    }
}
