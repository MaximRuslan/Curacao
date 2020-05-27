<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('client1.pages.login.index');
    }

    public function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request) + ['role_id' => 3, 'is_verified' => 1]
        );
    }

    public function authenticated(Request $request, $user)
    {
        $user->update([
            'last_activity' => date('Y-m-d H:i:s')
        ]);
        $country = Country::find($user->country);
        session(['timezone' => $country->timezone]);
        if ($user->status == 5) {
            $this->guard()->logout();
            return redirect()->back()->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['email' => Lang::get('keywords.account_deactivated', [], $user->lang)]);
        }
        if ($user->status == 3) {
            $this->guard()->logout();
            return redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['email' => Lang::get('keywords.account_deceased', [], $user->lang)]);
        } else {
            if ($user->lang != NULL) {
                App::setLocale($user->lang);
            }
            return redirect()->route('client1.home');
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
