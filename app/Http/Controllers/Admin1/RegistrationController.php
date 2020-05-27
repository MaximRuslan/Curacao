<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmEmail;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    public function index()
    {
        if (request('countryCode') && request('languageCode')) {
            $country = Country::where('mail_smtp', '=', request('countryCode'))->first();
            if ($country != null) {
                if (in_array(request('languageCode'), config('site.lang'))) {
                    session([
                        'locale' => request('languageCode')
                    ]);
                    App::setLocale(request('languageCode'));
                    $data = [];
                    $data['country'] = $country;
                    $data['options'] = [
                        '1' => Lang::get('keywords.yes'),
                        '2' => Lang::get('keywords.no')
                    ];
                    return view('admin1.pages.registration.index', $data);
                } else {
                    abort(403, 'The lang is invalid.');
                }
            } else {
                abort(403, 'The country is invalid.');
            }
        } else {
            if (!request('countryCode')) {
                abort(403, 'The country is required.');
            }
            if (!request('languageCode')) {
                abort(403, 'The lang is required.');
            }
        }
    }

    public function store()
    {
        $country = Country::where('mail_smtp', '=', request('countryCode'))->first();
        $this->validate(request(), User::registerValidationRules($country->phone_length), User::registerValidationMessages());

        $inputs = request()->except('telephone');

        if (request()->hasFile('payslip1')) {
            $payslip1 = time() . '_' . request()->file('payslip1')->getClientOriginalName();
            $path = request()->payslip1->move(public_path('uploads'), $payslip1);
            $inputs['payslip1'] = $payslip1;
        }

        $inputs['password'] = '';
        $inputs['is_verified'] = 0;
        $inputs['role_id'] = 3;
        $inputs['complete_profile'] = 1;
        $inputs['lang'] = $inputs['languageCode'];
        $inputs['country'] = $country->id;

        if (isset($inputs['referred_by']) && $inputs['referred_by'] != null && $inputs['referred_by'] != '') {
            $user = User::where('referral_code', '=', $inputs['referred_by'])->first();
            if ($user == null || $user->role_id != 3 || $user->country != $inputs['country']) {
                return back()->withInput()->withErrors([
                    'referred_by' => [
                        Lang::get('validation.valid_input', ['name' => Lang::get('keywords.referral_code')])
                    ]
                ]);
            }
        }

        unset($inputs['countryCode']);
        unset($inputs['languageCode']);

        $user = User::create($inputs);

        $password = $user->createPassword();

        $user->update([
            'email'         => $user->email,
            'password'      => bcrypt($password),
            'referral_code' => $user->getReferralCode()
        ]);
        UserInfo::create([
            'user_id' => $user->id,
            'value'   => request('telephone'),
            'type'    => 1,
        ]);
        $info = UserInfo::create([
            'user_id' => $user->id,
            'value'   => $user->email,
            'type'    => 3,
            'primary' => 1
        ]);
        Log::info('mail sent to ' . $info->value);
        try {
            Log::info($password);
            Mail::to($info->value)->send(new ConfirmEmail($user, 'web', $info->id, $info->value, $password));
        } catch (\Exception $e) {
            Log::info($e);
        }
        Log::info('verification mail sent to ' . $info->value . '.');
        $info->update([
            'sent_mail' => '1'
        ]);

        $role = Role::find($user->role_id);
        $user->syncRoles([$role->name]);
        session()->flash('success', Lang::get('keywords.registered_successfully'));
        return back();
    }
}
