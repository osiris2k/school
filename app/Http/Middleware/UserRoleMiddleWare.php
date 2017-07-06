<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserRoleMiddleWare
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->role_id == 1 || $user->role_id == 4) {
            session()->put('USER_ACCESS_SITE_DATA', 'ALL');
        } else {
            session()->put('USER_ACCESS_SITE_DATA', $user->sites->lists('id'));
        }

        return $next($request);
    }

}
