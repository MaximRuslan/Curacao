<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
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
        if (!auth()->guard('merchant')->check()) {
            if (auth()->check()) {
                return $next($request);
            } else {
                return redirect()->route('login');
            }
        } else {
            return redirect()->route('merchant.home.index');
        }
    }
}
