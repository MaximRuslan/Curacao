<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantReconciliation extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'merchant_id',
        'branch_id',
        'amount',
        'status',
        'date',
        'otp',
    ];

    public static function validationRules($inputs)
    {
        $max = LoanTransaction::findRemainingAmount($inputs['merchant_id'], $inputs['branch_id'], [], [], [1, 2]);

        return [
            'merchant_id' => 'required',
            'branch_id'   => 'required',
            'amount'      => 'required|min:0|max:' . $max
        ];
    }

    public static function validationMessages()
    {
        return [];
    }

    public function createTransactionId()
    {
        $merchant = Merchant::find($this->merchant_id);
        $branch = MerchantBranch::find($this->branch_id);
        $this->update([
            'transaction_id' => $merchant->name[0] . $merchant->name[1] . $branch->name[0] . $branch->name[1] . '-' . sprintf('%04d', $this->id)
        ]);
    }

    public function createOtp()
    {
        $merchant = Merchant::find($this->merchant_id);
        $branch = MerchantBranch::find($this->branch_id);
        $this->update([
            'otp' => $merchant->name[0] . $branch->name[0] . sprintf('%04d', rand(1, 9999)),
        ]);
    }

}
