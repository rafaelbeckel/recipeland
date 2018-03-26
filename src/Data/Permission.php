<?php declare(strict_types=1);

namespace Recipeland\Data;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $fillable = ['name', 'display_name', 'description'];
    
    protected $touches = ['roles'];
    
    public function attachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }
        
        if (!$this->roles()->where('role_id', $role)->count()) {
            $this->roles()->attach($role);
        }
    }
}
