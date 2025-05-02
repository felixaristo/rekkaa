<?php

namespace App\Http\Middleware;

use App\Model\Master\KaryawanModel;
use Closure;

class AksesKaryawan
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
        $karyawan_data = session()->get('karyawan_data');
        if(!$karyawan_data) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'session_timeout' => true,
                ]);
            } else {
                return redirect('/karyawan/login');
            }
        }

        $karyawan = KaryawanModel::with(['wajibpajak', 'bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance'])->where([
            'karyawan_id' => $karyawan_data['karyawan_id'],
            'karyawan_isuser' => 1,
            'karyawan_active' => 1
        ])->first();
        
        if(!$karyawan) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'session_timeout' => true,
                ]);
            } else {
                return redirect('/karyawan/login');
            }
        }
        
        $request->attributes->set('karyawan', $karyawan);
        return $next($request);
    }
}
