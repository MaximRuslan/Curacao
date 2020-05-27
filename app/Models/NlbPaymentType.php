<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NlbPaymentType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nlb_id',
        'payment_type',
        'amount'
    ];
}
