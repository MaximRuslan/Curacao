<?php

namespace App\Http\Middleware;

use Closure;

class RequiredBranch
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
        if (auth()->user()->hasRole('super admin|admin|debt collector|loan approval|auditor|credit and processing') || session()->has('branch_id')) {
            return $next($request);
        } else {
            return redirect('admin/branch/select');
        }
    }
}
