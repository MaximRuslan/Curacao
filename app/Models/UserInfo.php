<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInfo extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'value',
        'is_verified',
        'sent_mail',
        'primary',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
