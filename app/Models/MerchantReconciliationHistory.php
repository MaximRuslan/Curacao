<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantReconciliationHistory extends Model
{
    protected $fillable = [
        'merchant_reconciliation_id',
        'status',
        'type',
        'user_id',
    ];

    public static function addStatusHistory($reconciliation_id, $status, $type, $user_id)
    {
        self::create([
            'merchant_reconciliation_id' => $reconciliation_id,
            'status'                     => $status,
            'type'                      => $type,
            'user_id'                    => $user_id,
        ]);
    }
}
