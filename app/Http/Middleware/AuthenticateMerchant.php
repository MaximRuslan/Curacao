<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateMerchant
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
        if (auth()->guard('merchant')->check()) {
            return $next($request);
        } else {
            return redirect()->route('merchant.login.index');
        }
    }
}
