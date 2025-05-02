<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\RegencyModel;
use Illuminate\Http\Request;

class RegencyController extends Controller
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
        $country_id = $request->get('country_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $where = [
            'ms_country_id' => $country_id
        ];

        if($request->get('province_id')) {
            $where['province_id']  = $request->get('province_id');
        }
        
        $data = RegencyModel::select('regency_name', 'regency_id')
        ->when($q, function ($query, $q) {
            return $query->where('regency_name', 'ilike', '%'.$q.'%');
        })
        ->where($where)->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
