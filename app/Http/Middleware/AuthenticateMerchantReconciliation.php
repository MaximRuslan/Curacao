<?php

namespace App\Http\Middleware;

use App\Library\Helper;
use Closure;

class AuthenticateMerchantReconciliation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Helper::authMerchant()) {
            $merchant = Helper::authMerchantUser();
            if ($merchant->type == 1 || ($merchant->type == 2 && $merchant->reconciliation == 1)) {
                return $next($request);
            } else {
                return redirect()->route('merchant.home.index');
            }
        } else {
            return redirect()->route('merchant.login.index');
        }
    }
}
