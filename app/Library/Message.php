<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Message
{
    public static function sendSMS($mobile, $message, $sender = null)
    {
        if ($sender == null) {
            $sender = config('site.sender_number');
        }
        if (config('site.sms_send')) {
            $client = new Client(config('site.auth_id'), config('site.auth_token'));
            try {
                $response = $client->messages->create(
                    $mobile,
                    [
                        'from'           => $sender,
                        'body'           => $message,
                        'statusCallback' => url('message/callback')
                    ]
                );
                return $response->toArray();
                print_r($response);
            } catch (TwilioException $ex) {
                Log::info($ex);
                return [];
            }
//        Log::info(json_encode($message_created));
        } else {
            Log::info($mobile);
            Log::info($message);
            return [];
        }
    }
}