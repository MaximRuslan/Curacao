<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profileInfo()
    {
        $data = [];
        $user = auth()->user();
        $data['user'] = [
            'firstname'   => ['type' => 'text', 'value' => $user->firstname],
            'lastname'    => ['type' => 'text', 'value' => $user->lastname],
            'email'       => ['type' => 'email', 'value' => $user->email],
            'sex'         => ['type' => 'radio', 'value' => $user->sex],
            'lang'        => ['type' => 'select2', 'value' => $user->lang],
            'profile_pic' => ['type' => 'image', 'value' => $user->profile_pic],
        ];
        return $data;
    }

    public function profileStore()
    {
        $data = [];
        $this->validate(request(), [
            'lang'        => 'required|in:eng,esp,pap',
            'sex'         => 'required|in:1,2',
            'profile_pic' => 'nullable|image'
        ]);

        $user = auth()->user();

        $inputs = request()->only('lang', 'sex');

        if (request()->hasFile('profile_pic')) {
            if ($user->profile_pic != '') {
                Storage::delete(public_path('uploads/' . $user->profile_pic));
            }
            $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
            request()->profile_pic->move(public_path('uploads'), $profile);
            $inputs['profile_pic'] = $profile;
        }

        $user->update($inputs);

        App::setLocale($inputs['lang']);

        session([
            'locale' => $inputs['lang']
        ]);

        $data['status'] = true;
        $data['message'] = Lang::get('keywords.profile_updated', [], App::getLocale());

        return $data;
    }

    public function profilePicDelete()
    {
        $data = [];
        $user = auth()->user();
        $user->update([
            'profile_pic' => null
        ]);
        $data['status'] = true;
        $data['message'] = Lang::get('keywords.profile_pic_deleted', [], App::getLocale());

        return $data;
    }

    /*public function changeEmail()
    {
        $this->validate(request(), ['email' => 'required|email|unique:users,email,' . auth()->user()->id]);
        $user = auth()->user();
        $user->update([
            'new_email'       => request('email'),
            'is_verified' => 0
        ]);
        try {
            EmailHelper::emailConfigChanges('user');
            $email=request('email');
            Mail::send('emails.confirm-email', ['user' => $user,'email'=>$email], function ($message) use ($user,$email) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($email);
                $message->bcc(config('site.bcc_users'));
                $message->subject(config('mail.from.name') . ': Verify your online account.');
            });
        } catch (\Exception $e) {
            Log::info($e);
        }
        Auth::logout();
        $data = [];
        $data['status'] = true;
        return $data;
    }*/

    public function changeLanguage($lang)
    {
        $data = [];
        if (!in_array($lang, ['eng', 'esp', 'pap'])) {
            $data['status'] = false;
            $data['message'] = Lang::get('keywords.something_went_wrong', [], App::getLocale());
        } else {

            $user = auth()->user();

            $user->update([
                'lang' => $lang
            ]);

            App::setLocale($lang);

            session([
                'locale' => $lang
            ]);

            $data['status'] = true;
            $data['message'] = Lang::get('keywords.language_changed', [], App::getLocale());
        }

        return $data;
    }

    public function changePassword()
    {
        $lang = App::getLocale();
        $this->validate(request(), [
            'old_password'     => 'required',
            'new_password'     => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.regex' => Lang::get('keywords.password_with_special_char', [], $lang),
            'new_password.min'   => Lang::get('keywords.password_with_special_char', [], $lang)
        ]);
        $user = auth()->user();
        if (!Hash::check(request('old_password'), $user->password)) {
            return response()->json(['old_password' => Lang::get('keywords.password_does_not_match'), [], $lang], 422);
        } else {
            $user->update(['password' => bcrypt(request('new_password'))]);
        }
        $data = [];
        return $data;
    }
}
