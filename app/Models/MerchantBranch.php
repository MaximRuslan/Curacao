<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantBranch extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'name',
    ];

    public static function pluckListing($merchant_id)
    {
        $branches = MerchantBranch::where('merchant_id', '=', $merchant_id)->pluck('name', 'id');
        return $branches;
    }
}
