<?php

namespace App\Http\Middleware;

use Closure;

class IsLoggedIn
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
        $session_user_type = session()->get('user_data');
        if($session_user_type) {
            if($request->segment(2) != 'register-confirmation')
                return redirect('/user/beranda');
        }
        return $next($request);
    }
}
