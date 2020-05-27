<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Library\TemplateHelper;
use App\Models\Country;
use App\Models\LoanType;
use App\Models\Merchant;
use App\Models\MerchantDetail;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Lang;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];
        $data['country'] = Country::find(auth()->user()->country);
        return view('client1.pages.dashboard.index', $data);
    }

    public function verifyMerchant($id, $info_value, $email)
    {
        $info = MerchantDetail::where('merchant_id', '=', decrypt($id))->where('type', '=', '1')->where(['value' => $email])->first();
        if ($info) {
            $client = Merchant::find(decrypt($id));
            $info = MerchantDetail::find(decrypt($info_value));
            if ($info) {
                MerchantDetail::where('merchant_id', '=', decrypt($id))->where('type', '=', '1')
                    ->update([
                        'primary' => 0,
                    ]);
                MerchantDetail::where('merchant_id', '=', decrypt($id))->where('id', '=', decrypt($info_value))->where('type', '=', '1')
                    ->update([
                        'is_verified' => 1,
                        'primary'     => 1,
                    ]);
                $info = MerchantDetail::find(decrypt($info_value));
                \Log::info($info);
                $client->update([
                    'is_verified' => 1,
                    'email'       => $info->value,
                ]);
                if ($client) {
                    return redirect(route('merchant.login.index'))->with('success', Lang::get('keywords.account_verified', [], $client->lang));
                } else {
                    return redirect(route('merchant.login.index'))->with('success', Lang::get('keywords.account_verified', [], $client->lang));
                }
            } else {
                return redirect(route('merchant.login.index'))->with('success', Lang::get('keywords.account_verified', [], $info->lang));
            }
        } else {
            return redirect(route('merchant.login.index'))->with('success', "Unable to verify given account!");
        }
    }

    public function verifyClient($id, $info_value, $email)
    {
        $info = UserInfo::where('user_id', '=', decrypt($id))->where('type', '=', '3')->where(['value' => $email])->first();
        if ($info) {
            $client = User::find(decrypt($id));
            $info = UserInfo::find(decrypt($info_value));
            if ($info) {
                UserInfo::where('user_id', '=', decrypt($id))->where('type', '=', '3')->update([
                    'primary' => 0,
                ]);
                UserInfo::where('user_id', '=', decrypt($id))->where('id', '=', decrypt($info_value))->where('type', '=', '3')
                    ->update([
                        'is_verified' => 1,
                        'primary'     => 1,
                    ]);
                $info = UserInfo::find(decrypt($info_value));
                \Log::info($info);
                $client->update([
                    'is_verified' => 1,
                    'email'       => $info->value,
                ]);
                if ($client) {
                    if ($client->hasRole('client')) {
                        return redirect('/')->with('success', Lang::get('keywords.account_verified', [], $client->lang));
                    } else {
                        return redirect(route('login'))->with('success', Lang::get('keywords.account_verified', [], $client->lang));
                    }
                } else {
                    return redirect(route('login'))->with('success', Lang::get('keywords.account_verified', [], $client->lang));
                }
            } else {
                return redirect(route('login'))->with('success', Lang::get('keywords.account_verified', [], $info->lang));
            }
        } else {
            return redirect(route('login'))->with('success', "Unable to verify given account!");
        }
    }

    public function loanContract(LoanType $type)
    {
        $data = [];

        $lang = '';

        if (request('Language') == 'en') {
            $data['cms'] = $type->loan_agreement_eng;
            $lang = 'eng';
        } else if (request('Language') == 'es') {
            $data['cms'] = $type->loan_agreement_esp;
            $lang = 'esp';
        } else if (request('Language') == 'nl') {
            $data['cms'] = $type->loan_agreement_pap;
            $lang = 'pap';
        } else {
            if (auth()->user()->lang == 'esp') {
                $data['cms'] = $type->loan_agreement_esp;
                $lang = 'esp';
            } else if (auth()->user()->lang == 'pap') {
                $data['cms'] = $type->loan_agreement_pap;
                $lang = 'pap';
            } else {
                $data['cms'] = $type->loan_agreement_eng;
                $lang = 'eng';
            }
        }

        $user = User::find(request('user_id'));

        $inputs = [
            'first_name'     => '',
            'last_name'      => '',
            'address'        => '',
            'loan_amount'    => '',
            'number_of_days' => '',
            'civil_status'   => '',
            'id_number'      => '',
            'today_date'     => '',
        ];

        if ($user != null) {
            $inputs['first_name'] = $user->firstname;
            $inputs['last_name'] = $user->lastname;
            $inputs['address'] = $user->address;
            $inputs['civil_status'] = Lang::get('keywords.' . config('site.civil_statues.' . $user->civil_status), [], $lang);
            $inputs['id_number'] = $user->id_number;
        }

        $inputs['today_date'] = Helper::datebaseToFrontDate(date('Y-m-d'));
        $inputs['number_of_days'] = $type->number_of_days * 7;

        if (request('loan_amount') != null) {
            $inputs['loan_amount'] = request('loan_amount');
        }

        $data['cms'] = TemplateHelper::replaceNotificationTemplateTag($data['cms'], $inputs);
        return view('loan_contract', $data);
    }
}
