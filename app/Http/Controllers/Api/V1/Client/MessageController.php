<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\FirebaseNotification;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/client/messages",
     *   summary="Notifications listing",
     *     tags={"messages"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="page",in="query",description="page id to pass",type="integer"),
     *     @SWG\Response(response=200, description="{""data"":{{""notifications"": ""notifications objects."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function index()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $data['data']['notifications'] = FirebaseNotification::select('id', 'title', 'body', 'type', 'created_at')
            ->where('user_id', '=', $user->id)
            ->orderBy('id', 'desc')
            ->simplePaginate(10);
        $data['data']['message'] = '';

        return Api::ApiResponse($data, $status_code);
    }
}
