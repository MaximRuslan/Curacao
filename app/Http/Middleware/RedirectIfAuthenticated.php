<?php

namespace App\Http\Middleware;

use App\Library\Helper;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Helper::authMerchant()) {
            return redirect('merchant');
        }
        if (Auth::guard($guard)->check()) {
            if (auth()->user()->hasRole('client')) {
                return redirect('client');
            } else {
                return redirect('admin');
            }
        }

        return $next($request);
    }
}
