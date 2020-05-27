<?php

namespace App\Http\Middleware;

use App\Library\Helper;
use Closure;

class RedirectIfAuthenticatedMerchant
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Helper::authMerchant()) {
            return redirect('merchant');
        }
        return $next($request);
    }
}
