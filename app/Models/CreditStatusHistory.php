<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditStatusHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'status_id',
        'credit_id',
        'notes'
    ];
}
