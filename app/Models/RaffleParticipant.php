<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleParticipant extends Model
{
    protected $fillable = [
        'raffle_id',
        'user_id'
    ];
}
