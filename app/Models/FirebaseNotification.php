<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'body_json',
    ];
}
