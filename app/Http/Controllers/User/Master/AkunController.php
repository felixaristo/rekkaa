<?php

namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\AkunModel;
use App\Model\Master\PtkpModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('index', [
        //     'title' => 'Kalkulator PPh ayat 4 pasal 2',
        //     'content' => 'visitor.calculator.pph4a2',
        //     'kepemilikan_npwp' => KepemilikanNpwpModel::select('kepemilikannpwp_kode', 'kepemilikannpwp_nama', 'kepemilikannpwp_nilai')->get()
        // ]);
    }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $q = $request->get('q');
        $akun_level = $request->get('akun_level');
        $akun_kode = $request->get('akun_kode');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = AkunModel::select('akun_id','akun_name', 'akun_kode', 'akun_level', 'akun_saldo_normal')
        ->when($q, function ($query, $q) {
            return $query->where('akun_name', 'ilike', '%'.$q.'%');
        })->when($akun_level, function ($query, $akun_level) {
            return $query->where('akun_level', '=', $akun_level);
        })->when($akun_kode, function ($query, $akun_kode) {
            return $query->where('akun_kode', 'ilike', $akun_kode.'%');
        })->where(['akun_active' => 1])->limit($limit)->get();

        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
