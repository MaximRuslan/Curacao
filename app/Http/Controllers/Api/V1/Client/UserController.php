<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\Country;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/client/user-info",
     *   summary="user information api",
     *     tags={"users"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""user"": ""User information object."",""language"":""[language array]"",""message"":""""}}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function userInfo()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);
        unset($user['lang']);
        $country = Country::find($user->country);
        if ($country != null && $country->timezone != null && $country->timezone != '') {
            $user->timezone = $country->timezone;
        } else {
            $user->timezone = config('site.super_admin_timezone');
        }
        $user->web = '';
        if ($country != null && $country->web != null && $country->web != '') {
            $user->web = $country->web;
        }
        $user->has_active_loan = LoanApplication::hasActiveLoan($user);
        $data['data']['user'] = $user;
        $data['data']['language'] = config('site.language');
        $data['data']['message'] = "";
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Post(
     *   path="/client/accept-terms",
     *   summary="accept terms",
     *     tags={"users"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""You have accepted terms.""}}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function acceptTerms()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);
        $user->update([
            'terms_accepted' => 1
        ]);
        $data['data']['message'] = __('api.accepted_terms', [], request('lang'));
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Post(
     *   path="/client/change-password",
     *   summary="change password api",
     *     tags={"users"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Parameter(name="password",in="query",description="password",type="string"),
     *     @SWG\Parameter(name="password_confirmation",in="query",description="confirm password",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Your password changed successfully.""}}}"),
     *
     *     @SWG\Response(response=422, description="{""data"":{{""message"":""validation errors occurred."", ""errors"": { ""password"": [ ""The password field is required."" ] } }}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function changePassword()
    {
        $data = [];
        $status_code = 200;
        $validator = Validator::make(request()->all(), ['password' => 'required|confirmed']);
        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            $user = JWTAuth::toUser(request()->header('token'));
            $user = User::find($user->id);
            $user->update([
                'password' => bcrypt(request('password'))
            ]);
            $data['data']['message'] = __('api.changed_successfully', ['name' => __('api.password', [], request('lang'))], request('lang'));
        }
        return Api::ApiResponse($data, $status_code);
    }


    /**
     * @SWG\Post(
     *   path="/client/update-profile",
     *   summary="update profile api",
     *     tags={"users"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Parameter(name="firstname",in="query",description="First name",type="string"),
     *     @SWG\Parameter(name="lastname",in="query",description="Last Name",type="string"),
     *     @SWG\Parameter(name="email",in="query",description="Email",type="string"),
     *     @SWG\Parameter(name="language",in="query",description="Language items=""eng,esp,pap""",type="string"),
     *     @SWG\Parameter(name="gender",in="query",description="Gender 1=>male,2=>female",type="integer"),
     *     @SWG\Parameter(name="profile_pic",in="formData",description="Profile pic",type="file"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Your profile updates successfully."",""user"":""User Information object.""}}}"),
     *
     *     @SWG\Response(response=422,description="{""data"":{{""message"":""validation errors occurred."", ""errors"": { ""firstname"": [ ""The firstname field is required."" ] } }}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function update()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $validator = Validator::make(request()->all(), [
            'firstname'   => 'nullable',
            'lastname'    => 'nullable',
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'language'    => 'nullable|in:eng,esp,pap',
            'gender'      => 'nullable|in:1,2',
            'profile_pic' => 'nullable|image'
        ]);
        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            $user = User::find($user->id);
            $inputs = request()->only([
                'firstname',
                'lastname',
                'email',
                'language',
                'gender',
            ]);
            $inputs['sex'] = $inputs['gender'];
            foreach ($inputs as $key => $value) {
                if ($value == null || $value == '') {
                    unset($inputs[$key]);
                }
            }
            if (request()->hasFile('profile_pic')) {
                if ($user->profile_pic != '') {
                    Storage::delete(public_path('uploads/' . $user->profile_pic));
                }
                $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
                request()->file('profile_pic')->move(public_path('uploads'), $profile);
                $inputs['profile_pic'] = $profile;
            }

            $user->update($inputs);
            $country = Country::find($user->country);
            $user->web = '';
            if ($country != null && $country->timezone != null && $country->timezone != '') {
                $user->timezone = $country->timezone;
            } else {
                $user->timezone = config('site.super_admin_timezone');
            }
            if ($country != null && $country->web != null && $country->web != '') {
                $user->web = $country->web;
            }
            unset($user['lang']);
            $data['data']['message'] = __('api.updated_successfully', ['name' => __('keywords.profile', [], request('lang'))], request('lang'));
            $user->has_active_loan = LoanApplication::hasActiveLoan($user);
            $data['data']['user'] = $user;
        }
        return Api::ApiResponse($data, $status_code);
    }
}
