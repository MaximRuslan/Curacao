<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\ReferralHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReferralController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/client/referrals",
     *   summary="Referrals statistics api",
     *     tags={"referrals"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="page",in="query",description="page id to pass",type="integer"),
     *     @SWG\Response(response=200, description="{""data"":{{""credits"": ""credits objects.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function index()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);

        if (request()->header('Language') == 'es') {
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $lang = 'pap';
        } else {
            $lang = 'eng';
        }

        $referred_by_name = '';
        if ($user->referred_by != null) {
            $referred_by = User::where('referral_code', '=', $user->referred_by)->first();
            $referred_by_name = $referred_by->firstname . ' ' . $referred_by->lastname;
        }

        $data['data']['statistics'] = [
            [
                __('keywords.total_current_referrals', [], $lang) => $user->getStatusReferrals(4)
            ],
            [
                __('keywords.total_indefault_referrals', [], $lang) => $user->getStatusReferrals(5)
            ],
            [
                __('keywords.total_debt_collector_referrals', [], $lang) => $user->getStatusReferrals(6)
            ],
            [
                __('keywords.total_referrals', [], $lang) => $user->getStatusReferrals()
            ],
        ];

        $data['data']['message'] = "";

        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Get(
     *   path="/client/referral-histories",
     *   summary="Referrals History api",
     *     tags={"referrals"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="page",in="query",description="page id to pass",type="integer"),
     *     @SWG\Response(response=200, description="{""data"":{{""credits"": ""credits objects.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function history()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);

        if (request()->header('Language') == 'es') {
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $lang = 'pap';
        } else {
            $lang = 'eng';
        }

        $data['data']['history'] = ReferralHistory::select('referral_histories.*', DB::raw('concat(ref.firstname," ",ref.lastname,"-",ref.id_number) as ref_name'))
            ->leftJoin('users', 'users.id', '=', 'referral_histories.client_id')
            ->leftJoin('users as ref', 'ref.id', '=', 'referral_histories.referred_client')
            ->where('referral_histories.client_id', '=', $user->id)
            ->orderBy('id', 'desc');

        if (request('page') == 0) {
            $data['data']['history'] = $data['data']['history']->get();
        } else {
            $data['data']['history'] = $data['data']['history']->simplePaginate(50);
        }

        foreach ($data['data']['history'] as $key => $value) {
            if ($value->status == 1) {
                $data['data']['history'][$key]['status_text'] = __('keywords.Start', [], $lang);
            } else if ($value->status == 2) {
                $data['data']['history'][$key]['status_text'] = __('keywords.PIF', [], $lang);
            }
        }

        $referred_by_name = '';
        if ($user->referred_by != null) {
            $referred_by = User::where('referral_code', '=', $user->referred_by)->first();
            $referred_by_name = $referred_by->firstname . ' ' . $referred_by->lastname;
        }

        $data['data']['statistics'] = [
            [
                'name'  => __('keywords.total_current_referrals', [], $lang),
                'value' => $user->getStatusReferrals(4)
            ],
            [
                'name'  => __('keywords.total_indefault_referrals', [], $lang),
                'value' => $user->getStatusReferrals(5)
            ],
            [
                'name'  => __('keywords.total_debt_collector_referrals', [], $lang),
                'value' => $user->getStatusReferrals(6)
            ],
            [
                'name'  => __('keywords.total_referrals', [], $lang),
                'value' => $user->getStatusReferrals()
            ],
        ];

        $data['data']['message'] = "";

        return Api::ApiResponse($data, $status_code);
    }
}
