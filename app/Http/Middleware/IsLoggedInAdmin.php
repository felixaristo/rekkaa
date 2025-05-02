<?php

namespace App\Http\Middleware;

use Closure;

class IsLoggedInAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $session_admin_type = session()->get('admin_data');
        if($session_admin_type) {
            return redirect('/admin/beranda');
        }
        return $next($request);
    }
}
