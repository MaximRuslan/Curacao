<?php

namespace App\Http\Middleware;

use Closure;

class StatusBasedFilter
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
        if (auth()->user()->status == config('site.blacklistedUser')) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin1.permission-denied');
            } else {
                return redirect('/permission-denied');
            }
        }
        return $next($request);
    }
}
