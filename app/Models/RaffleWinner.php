<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaffleWinner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'user_id',
        'country_id'
    ];
}
