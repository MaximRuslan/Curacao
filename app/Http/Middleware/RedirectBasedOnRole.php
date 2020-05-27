<?php

namespace App\Http\Middleware;

use Closure;

class RedirectBasedOnRole
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
        if (auth()->user()->role_id != 3) {
            return redirect('/admin');
        }
        return $next($request);
    }
}
