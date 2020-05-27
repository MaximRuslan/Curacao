<?php

namespace App\Http\Middleware;

use App\Library\Api;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetUserFromToken
{

    public function handle($request, Closure $next)
    {
        $lang = 'eng';
        if (request()->header('Language') == 'es') {
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $lang = 'pap';
        }

        request()->request->add(['lang' => $lang]);

        $data = [];
        $status_code = 401;
        if (!$token = str_ireplace('Bearer ', '', request()->header('Authorization'))) {
            $data['data']['message'] = __('api.token_not_provided', [], $lang);
            return Api::ApiResponse($data, $status_code);
        }

        try {
            $user = JWTAuth::toUser($token);
        } catch (TokenExpiredException $e) {
            $data['data']['message'] = __('api.token_expired', [], $lang);
            return Api::ApiResponse($data, $status_code);
        } catch (JWTException $e) {
            $data['data']['message'] = __('api.token_invalid', [], $lang);
            return Api::ApiResponse($data, $status_code);
        }

        if (!$user) {
            $data['data']['message'] = __('api.user_not_found', [], $lang);
            return Api::ApiResponse($data, $status_code);
        }

        return $next($request);
    }
}
