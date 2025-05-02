<?php

namespace App\Http\Middleware;

use Closure;

class AksesAdmin
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
        $user_data = session()->get('admin_data');

        $exclude_url = ['login', 'dologin'];
        if(in_array($request->segment(2), $exclude_url)) {
            return $next($request);   
        }
        
        if(!$user_data) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'session_timeout' => true,
                ]);
            } else {
                return redirect('/admin/login');
            }
        }
        return $next($request);
    }
}
