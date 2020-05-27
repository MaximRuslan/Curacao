<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ReferralHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'bonus_payout',
        'client_id',
        'loan_id',
        'status',
        'referred_client',
    ];

    public static function storeHistory($loan, $type)
    {
        $client = User::where('id', '=', $loan->client_id)->first();
        if ($client != null && $client->referred_by != null) {
            $referral_client = User::where('role_id', '=', '3')->where('referral_code', '=', $client->referred_by)->first();
            if ($referral_client != null) {
                $referral_history = ReferralHistory::where('loan_id', '=', $loan->id)
                    ->where('client_id', '=', $referral_client->id)
                    ->where('status', '=', $type)
                    ->first();
                if ($referral_history == null) {
                    $amount = self::getReferralAmount($client, $type);
                    DB::transaction(function () use ($amount, $referral_client, $loan, $type, $client) {
                        ReferralHistory::create([
                            'date'            => date('Y-m-d H:i:s'),
                            'bonus_payout'    => $amount,
                            'client_id'       => $referral_client->id,
                            'loan_id'         => $loan->id,
                            'status'          => $type,
                            'referred_client' => $client->id,
                        ]);

                        Wallet::create([
                            'user_id'                  => $referral_client->id,
                            'amount'                   => $amount,
                            'notes'                    => 'Referral Amount',
                            'transaction_payment_date' => date('Y-m-d')
                        ]);
                    });
                }
            }
        }
    }

    public static function getReferralAmount($client, $type)
    {
        $amount = 0;

        $referral_client = User::where('referral_code', '=', $client->referred_by)->first();

        $no_of_referral = User::withTrashed()->where('referred_by', '=', $client->referred_by)
            ->where('id', '<=', $client->id)
            ->count();

        $referral_category = ReferralCategory::where('min_referrals', '<=', $no_of_referral)
            ->where('max_referrals', '>=', $no_of_referral)
            ->where('country_id', '=', $referral_client->country)
            ->whereNotNull('max_referrals')
            ->orderBy('min_referrals', 'asc')
            ->first();

        if ($referral_category == null) {
            $referral_category = ReferralCategory::where('min_referrals', '<=', $no_of_referral)
                ->where('country_id', '=', $referral_client->country)
                ->whereNull('max_referrals')
                ->orderBy('min_referrals', 'asc')
                ->first();
        }

        if ($referral_category != null) {
            if ($type == 1) {
                $amount = $referral_category->loan_start;
            } elseif ($type == 2) {
                $amount = $referral_category->loan_pif;
            }
        }

        return $amount;
    }
}
