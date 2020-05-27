<?php

namespace App\Library;


use App\Models\FirebaseNotification;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Log;

class FirebaseHelper
{
    public static function firebaseNotification($id, $title, $body, $type, $data)
    {
        Log::info('firebase started');

        $url = 'https://fcm.googleapis.com/fcm/send';

        $user_tokens = UserLogin::where('user_id', '=', $id)
            ->whereNull('logout_at')
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')->toArray();

        $data += [
            "body"  => $body,
            "title" => $title,
            'type'  => $type,
        ];

        FirebaseNotification::create([
            'user_id'   => $id,
            'title'     => $title,
            'body'      => $body,
            'type'      => $type,
            'body_json' => json_encode($data)
        ]);

        $fields = [
            'registration_ids' => $user_tokens,
            'notification'     => [
                "body"  => $body,
                "title" => $title,
                'type'  => $type,
            ],
            'data'             => $data
        ];
        $fields = json_encode($fields);
        Log::info($fields);

        $headers = [];

        $headers = [
            'Authorization: key=' . config('site.firebase_server_key'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        Log::info('firebase ended');

        Log::info(json_decode($result, true));

        return json_decode($result);
    }
}