<?php

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use EntrustUserTrait, SoftDeletes;
    
    public function createPassword(string $password): void
    {
        $this->password = password_hash($password , PASSWORD_BCRYPT);
        $this->save();
    }
    
    public function verifyPassword(string $password): bool
    {
        return verify_password($password, $this->password);
    }
}