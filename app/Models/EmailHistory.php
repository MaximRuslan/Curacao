<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'email_class',
        'model_id',
        'data'
    ];
}
