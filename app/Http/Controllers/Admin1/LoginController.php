<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('admin1.pages.login.index');
    }

    public function authenticated(Request $request, $user)
    {
        $user->update([
            'last_activity' => date('Y-m-d H:i:s')
        ]);
        if (!$user->hasRole('super admin')) {
            $country = Country::find($user->country);
            session(['timezone' => $country->timezone]);
        } else {
            session(['timezone' => config('site.super_admin_timezone')]);
        }
        if ($user->status == 5) {
            $this->guard()->logout();
            return redirect()->back()->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['email' => 'Your account is deactivated. Please contact administrator.']);
        }
        if ($user->status == 3 && $user->role_id == 3) {
            $this->guard()->logout();
            return redirect()->back()->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['email' => 'Sorry! Your account is deceased']);
        } else {
            if ($user->is_verified) {
                if ($user->role_id == 3 && $user->lang != NULL) {
                    App::setLocale($user->lang);
                    return redirect()->route('client1.home');
                }
                return redirect()->route('admin1.home');
            } else {
                $this->guard()->logout();
                return redirect()->back()->withInput($request->only($this->username()))
                    ->withErrors(['email' => 'You must be verified to login. Please check your mail inbox for verification mail. If you don\'t found verification mail, please <a href="' . url('resend/' . $user->id . '/verification') . '">click here</a> to resend it.']);
            }
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $redirect = '/';
        if (!auth()->user()->hasRole('client')) {
            $redirect = 'login';
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect($redirect);
    }
}
