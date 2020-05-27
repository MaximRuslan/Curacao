<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantDetail extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'type',
        'value',
        'is_verified',
        'primary'
    ];
}
