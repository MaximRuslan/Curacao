<?php

namespace App\Http\Controllers;

use App\Library\EmailHelper;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('resendVerificationMail','languageChange');
    }

    public function languageChange($lang)
    {
        session([
            'locale' => $lang
        ]);
        return [];
    }

    public function dashboard()
    {
        if (auth()->user()->hasRole('client') || auth()->user()->hasRole('super admin|admin|debt collector|loan approval|auditor|credit and processing') || session()->has('branch_id')) {
            if (auth()->user()->hasRole('super admin|admin|processor|debt collector|auditor|loan approval|credit and processing')) {
                return redirect(route('admin.dashboard'));
            } else if (auth()->user()->hasRole('manager')) {
                return redirect(route('manager.dashboard'));
            } else if (auth()->user()->hasRole('client')) {
                return redirect(route('client1.home'));
            }
        } else {
            return redirect('admin/branch/select');
        }
    }

    public function userProfile()
    {
        $user = Auth::user();
        $data['user'] = $user;
        return view('profile', $data);
    }

    public function setCountry()
    {
        $data['status'] = true;
        if (request('country') == 1) {
            session()->forget('country');
            session()->forget('timezone');
        } else {
            session()->put('country', request('country'));
            $country = Country::find(request('country'));
            if ($country != null) {
                session(['timezone' => $country->timezone]);
            } else {
                session()->forget('timezone');
            }
        }
        return $data;
    }

    public function postUserProfile(Request $request)
    {
        $user = Auth::user();
        if ($request->type == 'normal') {
            if (auth()->user()->hasRole('client')) {
                $rules = [
                    'lang' => 'required'
                ];
            } else {
                $rules = [
                    'firstname' => 'required',
                    'lastname'  => 'required',
                    'lang'      => 'required'
                ];
            }
            $this->validate($request, $rules);

            $inputs = $request->all();

            if ($request->hasFile('profile_pic')) {
                if ($user->profile_pic != '') {
                    \Storage::delete(public_path('uploads/' . $user->profile_pic));
                }
                $profile = time() . '_' . $request->file('profile_pic')->getClientOriginalName();
                $path = $request->profile_pic->move(public_path('uploads'), $profile);
                $inputs['profile_pic'] = $profile;
            } else {
                if ($request->removeImage != 'true') {
                    $inputs['profile_pic'] = $user->profile_pic;
                } else {
                    if ($user->profile_pic != '') {
                        \Storage::delete(public_path('uploads/' . $user->profile_pic));
                        $inputs['profile_pic'] = '';
                    }
                }
            }
            $user->update($inputs);
            //dd(config('laravellocalization.supportedLocales.'.$inputs['lang']));
            //  App::setLocale($inputs['lang']);
            if ($user->role_id == 3) {
                \LaravelLocalization::setLocale($inputs['lang']);
            }
        } else {
            $this->validate($request, [
                'old_password'     => 'required',
                'new_password'     => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'
                ],
                'confirm_password' => 'required|same:new_password',
            ],
                [
                    'new_password.regex' => 'Passwords should not be less than 8 characters including uppercase, lowercase, at least one number and special character.',
                    'new_password.min'   => 'Passwords should not be less than 8 characters including uppercase, lowercase, at least one number and special character.'
                ]);
            if (!\Hash::check($request->old_password, $user->password)) {

                return response()->json(['old_password' => 'The specified password does not match the database password'], 422);

                return back()->withErrors();
            } else {
                $user->update(['password' => bcrypt($request->new_password)]);
            }
        }

        return $user;
    }

    public function resendVerificationMail(User $user)
    {
        $password = str_random(6);
        $inputs['password'] = bcrypt($password);
        $inputs['is_verified'] = 0;
        $user->update($inputs);
        try {
            $country = Country::find($user->country);
            EmailHelper::emailConfigChanges('user');
            $email = '';
            if ($user->new_email != null) {
                $email = $user->new_email;
            } else {
                $email = $user->email;
            }
            $user->update([
                'new_email' => $email
            ]);
            Mail::send('emails.confirm-email', [
                'user'     => $user,
                'password' => $password,
                'email'    => $email
            ], function ($message) use ($user, $email) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($email);
                $message->bcc(config('site.bcc_users'));
                $message->subject(config('mail.from.name') . ': Verify your online account.');
            });
            session()->flash('message', 'Verification mail is sent to your email id.');
            session()->flash('type', 'success');
        } catch (\Exception $e) {
            Log::info($e);
            session()->flash('message', 'Something went wrong please try again later.');
            session()->flash('type', 'danger');
        }
        Log::info('resent');
        return back();
    }
}
