<?php

namespace App\Http\Middleware;

use Closure;

class InactiveRedirect
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
            if (date('Y-m-d H:i:s', strtotime(auth()->user()->last_activity)) < date('Y-m-d H:i:s', strtotime('-' . config('site.inactivity_logout') . ' min'))) {
                $url = 'login';
                if (auth()->user()->role_id == 3) {
                    $url = 'client/login';
                }
                auth()->logout();
                session()->flash('message', 'Your session expired please login again.');
                session()->flash('class', 'danger');
                if ($request->ajax()) {
                    return response()->json([
                        'login_again' => true
                    ], 401);
                } else {
                    return redirect($url);
                }
            } else {
                auth()->user()->update([
                    'last_activity' => date('Y-m-d H:i:s')
                ]);
            }
        }
        return $next($request);
    }
}
