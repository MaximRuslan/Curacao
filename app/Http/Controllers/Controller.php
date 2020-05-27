<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Class ApiController
     *
     * @package App\Http\Controllers
     *
     * @SWG\Swagger(
     *     basePath="/api/v1",
     *     host="hylawallet.com",
     *     schemes={"https"},
     *     @SWG\Info(
     *         version="1.0",
     *         title="Hyla Wallet Apis",
     *     ),
     *     @SWG\Definition(
     *         definition="Error",
     *         required={"code", "message"},
     *         @SWG\Property(
     *             property="code",
     *             type="integer",
     *             format="int32"
     *         ),
     *         @SWG\Property(
     *             property="message",
     *             type="string"
     *         )
     *     ),
     *      @SWG\Tag(
     *        name="login",
     *        description="Login related apis"
     *      ),
     *      @SWG\Tag(
     *        name="users",
     *        description="Users related apis"
     *      ),
     *     @SWG\Tag(
     *        name="loans",
     *        description="Loans related apis"
     *      ),
     *     @SWG\Tag(
     *        name="credits",
     *        description="credits related apis"
     *      ),
     *     @SWG\Tag(
     *        name="messages",
     *        description="notifications related apis"
     *      ),
     *     @SWG\Tag(
     *        name="referrals",
     *        description="referrals related apis"
     *      ),
     * )
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
