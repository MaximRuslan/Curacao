<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantCommission extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'min_amount',
        'max_amount',
        'commission'
    ];
}
