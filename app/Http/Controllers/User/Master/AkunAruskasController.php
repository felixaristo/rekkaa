<?php

namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\AkunAruskasModel;
use App\Model\Master\AkunModel;
use App\Model\Master\PtkpModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunAruskasController extends Controller
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

        $data = AkunAruskasModel::select('akunaruskas_id','akunaruskas_name', 'akunaruskas_faktor')
        ->when($q, function ($query, $q) {
            return $query->where('akunaruskas_name', 'ilike', '%'.$q.'%');
        })->where(['akunaruskas_active' => 1])->limit($limit)->get();

        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
