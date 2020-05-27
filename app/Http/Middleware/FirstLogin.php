<?php

namespace App\Http\Middleware;

use Closure;

class FirstLogin
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
        if (auth()->check()) {
            if (auth()->user()->terms_accepted == null) {
                return redirect('terms-conditions');
            }
        }
        return $next($request);
    }
}
