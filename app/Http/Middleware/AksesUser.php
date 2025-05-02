<?php

namespace App\Http\Middleware;

use Closure;

class AksesUser
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
        $user_data = session()->get('user_data');
        if(!$user_data) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'session_timeout' => true,
                ]);
            } else {
                return redirect('/login');
            }
        }
        return $next($request);
    }
}
