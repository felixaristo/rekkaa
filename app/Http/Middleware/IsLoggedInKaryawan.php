<?php

namespace App\Http\Middleware;

use Closure;

class IsLoggedInKaryawan
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
        $session_karyawan = session()->get('karyawan_data');
        if($session_karyawan) {
            return redirect('/karyawan/beranda');
        }
        return $next($request);
    }
}
