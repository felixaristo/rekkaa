<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\PtkpModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PtkpController extends Controller
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
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = PtkpModel::select('ptkp_id','ptkp_marriage_status', 'ptkp_description', 'ptkp_rate', 'ptkp_category')
        ->when($q, function ($query, $q) {
            return $query->where('ptkp_description', 'ilike', '%'.$q.'%');
        })
        ->where(['ptkp_active' => 1])->limit($limit)->get();

        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
