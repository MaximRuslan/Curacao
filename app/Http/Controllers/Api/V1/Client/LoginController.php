<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\Country;
use App\Models\LoanApplication;
use App\Models\UserLogin;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    /**
     * @SWG\Post(
     *   path="/client/login",
     *   summary="Login Api for clients",
     *     tags={"login"},
     *     @SWG\Parameter(name="email",in="query",description="client email",type="string"),
     *     @SWG\Parameter(name="password",in="query",description="client password",type="string"),
     *     @SWG\Parameter(name="device_id",in="query",description="device_id",type="string"),
     *     @SWG\Parameter(name="device_type",in="query",description="device_type in 'android','ios'",type="string"),
     *     @SWG\Parameter(name="device_token",in="query",description="device firebas token",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""token"": ""token"",""user"":""User info object"",""terms"":""Terms Date"",""message"":""""}}}"),
     *
     *     @SWG\Response(response=401, description="{""data"":{{""message"": ""invalid_credentials""}}},{""data"":{{ ""message"": ""could_not_create_token""}}}"),
     *
     *     @SWG\Response(response=422, description="{""data"":{{""message"":""validation errors occurred."", ""errors"": { ""email"": [ ""The email field is required."" ], ""password"": [ ""The password field is required."" ] } }}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function store()
    {
        $data = [];
        $status_code = 200;
        $validator = Validator::make(request()->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            try {
                if (!$token = JWTAuth::attempt(request()->only('email', 'password') + ['role_id' => '3'])) {
                    $data['data']['message'] = __('api.invalid_credentials', [], request('lang'));
                    $status_code = 401;
                } else {
                    $data['data']['token'] = $token;
                    $user = JWTAuth::toUser($token);
                    if ($user->terms_accepted == null) {
                        $user->terms_accepted = 0;
                    }
                    UserLogin::updateOrCreate([
                        'user_id'   => $user->id,
                        'device_id' => request('device_id'),
                    ], [
                        'device_type'    => request('device_type'),
                        'jwt_token'      => $token,
                        'firebase_token' => request('device_token'),
                        'login_at'       => date('Y-m-d H:i:s'),
                        'logout_at'      => null
                    ]);
                    unset($user['lang']);
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
                    $user->has_active_loan = LoanApplication::hasActiveLoan($user);
                    $data['data']['user'] = $user;
                    $data['data']['terms'] = '';
                    if ($user->terms_accepted != 1) {
                        $country = Country::find($user->country);
                        if (request()->header('Language') == 'es') {
                            $data['data']['terms'] = $country->terms_esp;
                        } else if (request()->header('Language') == 'nl') {
                            $data['data']['terms'] = $country->terms_pap;
                        } else {
                            $data['data']['terms'] = $country->terms_eng;
                        }
                    }
                    $data['data']['message'] = "";
                }
            } catch (JWTException $e) {
                $data['data']['message'] = __('api.could_not_create_token', [], request('lang'));
                $status_code = 401;
            }
        }
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Post(
     *   path="/client/forgot-password",
     *   summary="forgot password api for sending forgot password email",
     *     tags={"login"},
     *     @SWG\Parameter(name="email",in="query",description="client email",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Password reset link is sent to your inbox please check it.""}}}"),
     *
     *     @SWG\Response(response=401, description="{""message"": ""User not found."",""status"": false}"),
     *
     *     @SWG\Response(response=422, description="{""message"":""validation errors occurred."", ""errors"": { ""email"": [ ""The email field is required."" ]} }"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function forgotPassword()
    {
        $data = [];
        $status_code = 200;
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
            $status_code = 422;
        } else {
            $email = request('email');
            $credentials = ['email' => $email];
            $response = Password::sendResetLink($credentials, function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    $data['data']['message'] = __('api.password_reset_link_sent', [], request('lang'));
                    break;
                case Password::INVALID_USER:
                    $data['data']['message'] = __('api.user_not_found', [], request('lang'));
                    $status_code = 401;
                    break;
            }
        }
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Delete(
     *   path="/client/logout",
     *   summary="client logout api which whip out device login related data",
     *     tags={"login"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Account has been successfully logout.""}}}"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function destroy()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $token = str_ireplace('Bearer ', '', request()->header('Authorization'));
        UserLogin::where('user_id', '=', $user->id)
            ->where('jwt_token', '=', $token)
            ->update([
                'logout_at' => date('Y-m-d H:i:s')
            ]);
        $data['data']['message'] = __('api.successfully_logout', [], request('lang'));
        return Api::ApiResponse($data, $status_code);
    }


    /**
     * @SWG\Post(
     *   path="/client/device-token",
     *   summary="device firbase token change",
     *     tags={"login"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Parameter(name="device_id",in="query",description="device_id",type="string"),
     *     @SWG\Parameter(name="device_type",in="query",description="device_type in 'android','ios'",type="string"),
     *     @SWG\Parameter(name="device_token",in="query",description="device firebas token",type="string"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Your device token has been saved.""}}}"),
     *
     *     @SWG\Response(response=401, description="{""message"": ""User not found."",""status"": false}"),
     *
     *     @SWG\Response(response=422, description="{""message"":""validation errors occurred."", ""errors"": { ""email"": [ ""The email field is required."" ]} }"),
     *
     *     @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function tokenChange()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $token = str_ireplace('Bearer ', '', request()->header('Authorization'));
        $inputs = [];
        if (request('device_id') && request('device_id') != null && request('device_id') != '') {
            $inputs['device_id'] = request('device_id');
        }
        if (request('device_type') && request('device_type') != null && request('device_type') != '') {
            $inputs['device_type'] = request('device_type');
        }
        if (request('device_token') && request('device_token') != null && request('device_token') != '') {
            $inputs['firebase_token'] = request('device_token');
        }
        UserLogin::where('user_id', '=', $user->id)
            ->where('jwt_token', '=', $token)
            ->update($inputs);
        $data['data']['message'] = __('api.token_successfully_saved', [], request('lang'));
        return Api::ApiResponse($data, $status_code);
    }
}
