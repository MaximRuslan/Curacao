<?php

namespace App\Library;

use Tymon\JWTAuth\Facades\JWTAuth;

class Api
{
    public static function ApiResponse($data, $statusCode = '200', $specialMsg = [])
    {
        if (count($data['data']) > 0) {
            if (isset($data['header'])) {
                foreach ($data['header'] as $key => $value) {
                    $headers[$key] = $value;
                }
                unset($data['header']);
            }
            $returnArray['status'] = $statusCode;
            $returnArray['data'] = $data['data'];
        } else {
            if (isset($data['errorMessage'])) {
                $returnArray['errorMessage'] = $data['errorMessage'];
            }
            $returnArray['status'] = '200';
        }

        if (!empty($specialMsg)) {
            $returnArray['specialMsg'] = $specialMsg;
        }

        $headers['Content-Type'] = 'application/json';

        return response()->make(json_encode($returnArray, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK), $returnArray['status'], $headers);
    }

    public static function getAuthenticatedUser()
    {
        $data = [];
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                $data['error'] = 'User Not Found';
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $data['error'] = 'Token Expired';
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $data['error'] = 'Token Invalid';
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            $data['error'] = 'Token Absent';
        }

        $data['status'] = true;
        if (isset($data['error'])) {
            $data['status'] = false;
        } else {
            $data['user'] = $user;
        }
        return $data;
    }
}