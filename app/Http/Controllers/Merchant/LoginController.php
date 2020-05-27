<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmMerchantEmail;
use App\Models\Country;
use App\Models\Merchant;
use App\Models\MerchantDetail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/employee/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('merchant.pages.login.index');
    }

    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard('merchant');
    }

    public function login(\Illuminate\Http\Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // This section is the only change
        if ($this->guard()->validate($this->credentials($request))) {
            $merchant = $this->guard()->getLastAttempted();
            $merchant->update([
                'last_activity' => date('Y-m-d H:i:s')
            ]);
            $country = Country::find($merchant->country_id);
            session(['timezone' => $country->timezone]);
            if ($merchant->status == 5) {
                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['email' => 'Your account is deactivated. Please contact administrator.']);
            }

            // Make sure the user is active
            if ($merchant->is_verified && $this->attemptLogin($request)) {
                // Send the normal successful login response
                if ($merchant->lang != NULL) {
                    \App::setLocale($merchant->lang);
                }
                return redirect()->route('merchant.home.index');
            } else {
                // Increment the failed login attempts and redirect back to the
                // login form with an error message.
                $this->incrementLoginAttempts($request);
                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['email' => __('keywords.not_verified', ['resend_link' => url('merchant/resend/' . $merchant->id . '/verification')])]);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('merchant.login.index');
    }

    public function resendVerificationMail(Merchant $merchant)
    {
        $password = str_random(6);
        $inputs['password'] = bcrypt($password);
        $inputs['is_verified'] = 0;
        $merchant->update($inputs);
        $id = MerchantDetail::where('merchant_id', '=', $merchant->id)->where('type', '=', 1)->where('value', '=', $merchant->email)->first();
        try {
            Mail::to($id->value)->send(new ConfirmMerchantEmail($merchant, $id->id, $id->value, $password));
            session()->flash('message', __('keywords.verification_mail_sent'));
            session()->flash('class', 'success');
        } catch (\Exception $e) {
            Log::info($e);
            session()->flash('message', __('keywords.something_went_wrong'));
            session()->flash('class', 'danger');
        }
        Log::info('resent');
        return back();
    }
}
