<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\ObjekPajakModel;
use Illuminate\Http\Request;

class ObjekPajakController extends Controller
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
        $jenis = $request->get('jenis');
        $kategori = $request->get('kategori');
        $bukanpegawai = $request->get('bukanpegawai');
        $notin = ['21-100-01','21-100-02'];
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = ObjekPajakModel::select('objekpajak_code', 'objekpajak_description', 'objekpajak_rate')
        ->when($q, function ($query, $q) {
            return $query->where('objekpajak_code', 'ilike', '%'.$q.'%')
                ->orWhere('objekpajak_description', 'ilike', '%'.$q.'%');
        })->when($kategori, function ($query, $kategori) {
            return $query->where('objekpajak_category', $kategori);
        })->when($bukanpegawai, function ($query, $bukanpegawai) {
            return $query->where('objekpajak_nonemployee', $bukanpegawai);
        })->when($notin, function ($query, $notin) {
            return $query->whereNotIn('objekpajak_code', $notin);
        })
        ->where(['objekpajak_active' => 1, 'objekpajak_type' => $jenis])
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectCategory(Request $request)
    {
        $q = $request->get('q');
        $jenis = $request->get('jenis');
        $calculator = ($request->get('calculator')) ? $request->get('calculator') : 0;
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = ObjekPajakModel::select('objekpajak_category')
        ->when($q, function ($query, $q) {
            return $query->where('objekpajak_code', 'ilike', '%'.$q.'%')
                ->orWhere('objekpajak_description', 'ilike', '%'.$q.'%');
        })
        ->where(['objekpajak_active' => 1, 'objekpajak_calculator' => $calculator, 'objekpajak_type' => $jenis])
        ->groupBy('objekpajak_category')->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
