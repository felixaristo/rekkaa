<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\BankModel;
use Illuminate\Http\Request;

class BankController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
        $limit = $request->get('limit') ? $request->get('limit') : 10;

        $data = BankModel::select('bank_id', 'bank_code', 'bank_name')
        ->when($q, function ($query, $q) {
            return $query->where('bank_name', 'ilike', '%'.$q.'%');
        })
        ->where(['bank_active' => 1])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
