<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\ReferralHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{
    public function index()
    {
        $data = [];
        $user = auth()->user();
        $referred_by_name = '';
        if ($user->referred_by != null) {
            $referred_by = User::where('referral_code', '=', $user->referred_by)->first();
            $referred_by_name = $referred_by->firstname . ' ' . $referred_by->lastname;
        }
        $data['referral_infos'] = [
            [
                'title' => __('keywords.code'),
                'value' => $user->referral_code
            ],
            [
                'title' => __('keywords.total_referrals'),
                'value' => Helper::numberShowing($user->getStatusReferrals())
            ],
            [
                'title' => __('keywords.referred_by'),
                'value' => $referred_by_name
            ],
        ];


        return view('client1.pages.referrals.index', $data);
    }

    public function indexDatatable()
    {
        $history = ReferralHistory::select('referral_histories.*', DB::raw('concat(users.firstname," ",users.lastname,"-",users.id_number) as name'),
            DB::raw('concat(ref.firstname," ",ref.lastname,"-",ref.id_number) as ref_name'))
            ->leftJoin('users', 'users.id', '=', 'referral_histories.client_id')
            ->leftJoin('users as ref', 'ref.id', '=', 'referral_histories.referred_client')
            ->where('referral_histories.client_id', '=', auth()->user()->id);

        return DataTables::of($history)
            ->addColumn('bonus_payout', function ($row) {
                return Helper::decimalShowing($row->bonus_payout, auth()->user()->country);
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return __('keywords.Start');
                } else if ($row->status == 2) {
                    return __('keywords.PIF');
                }
            })
            ->addColumn('date', function ($row) {
                return Helper::date_to_current_timezone($row->date);
            })
            ->make(true);

    }
}
