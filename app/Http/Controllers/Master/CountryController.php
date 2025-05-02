<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\CountryModel;
use App\Model\Master\RegencyModel;
use Illuminate\Http\Request;

class CountryController extends Controller
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

        $data = CountryModel::select('country_name', 'country_id')
        ->when($q, function ($query, $q) {
            return $query->where('country_name', 'ilike', '%'.$q.'%');
        })->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
