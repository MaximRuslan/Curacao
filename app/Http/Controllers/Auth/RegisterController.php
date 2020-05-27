<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'role_id'   => '3',
            'status'    => '0',
            'territory' => $data['territory'],
            'password'  => bcrypt($data['password']),
        ]);
        $user->assignRole('client');
        try {
            EmailHelper::emailConfigChanges('user');
            Mail::send('emails.confirm-email', ['user' => $user, 'password' => false], function ($message) use ($user) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($user->email);
                $message->bcc(config('site.bcc_users'));
                $message->subject(config('mail.from.name') . ': Verify your online account.');
            });
        } catch (\Exception $e) {
            Log::info($e);
        }
        return $user;
    }
}
