<?php

declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    protected $fillable = ['name', 'username', 'password', 'email'];

    protected $hidden = ['password', 'token', 'deleted_at', 'email', 'username'];
    
    use EntrustUserTrait, SoftDeletes {
        SoftDeletes::restore as sfRestore;
        EntrustUserTrait::restore as euRestore;
    }
    
    public function ratings()
    {
        return $this->hasMany('Recipeland\Data\Rating');
    }
    
    /**
     * @codeCoverageIgnore
     */
    public function restore(): void
    {
        $this->sfRestore();
        Cache::tags($this->table)->flush();
    }

    public function createPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->save();
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    public function recipes()
    {
        return $this->hasMany('Recipeland\Data\Recipe', 'created_by');
    }

    public function firstName()
    {
        $firstName = head(explode(' ', $this->name));

        return str_replace('_', ' ', $firstName);
    }

    public function lastName()
    {
        $lastName = last(explode(' ', $this->name));

        return str_replace('_', ' ', $lastName);
    }

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
