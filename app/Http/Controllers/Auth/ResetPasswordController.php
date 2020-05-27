<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);

        $user->setRememberToken(Str::random(60));

        $user->save();
        if ($user->status == config('site.blacklistedUser') && $user->role_id == 3) {
            return redirect('/login');
        }
        $str = 'Your password has been changed.';
        session()->flash('message', $str);
        session()->flash('success', $str);
        session()->flash('class', 'success');
    }
}
