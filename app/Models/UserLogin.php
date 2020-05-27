<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLogin extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_type',
        'jwt_token',
        'firebase_token',
        'login_at',
        'logout_at',
    ];

}
