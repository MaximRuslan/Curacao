<?php

namespace App\Models;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantCommissionCalculation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'branch_id',
        'month',
        'year',
        'collected_amount',
        'commission',
    ];
}
