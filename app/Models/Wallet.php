<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Wallet extends BaseModel
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'notes',
        'transaction_payment_date',
        'used',
        'history_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function validationRules()
    {
        return [
            'notes'                    => 'max:1000',
            'transaction_payment_date' => 'required|date_format:d/m/Y',
        ];
    }

    public static function getUserWalletAmount($userId)
    {
        $wallet = Wallet::select(DB::raw('sum(amount) as amount'))
            ->where('user_id', $userId)
            ->first();
        if ($wallet['amount'] != null) {
            return round($wallet['amount'], 2);
        } else {
            return '0.00';
        }
    }

}
