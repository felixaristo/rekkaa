<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\UserGroupMemberModel;

class WajibPajakController extends Controller
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

    public function list()
    {
        $data = UserGroupMemberModel::select('ms_wajib_pajak.*')
        // ->with(['wajibpajaksubscription.subscriptionmenu', function($q) {
        //     $q->select('subscriptionmenu_id');
        // }])
        // ->join('tr_wajib_pajak_subscription', function($join) {
        //     $join->on('tr_wajib_pajak_subscription.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id');
        // })
        ->where([
            'tr_user_group_member.usergroupmember_members' => session()->get('user_data')['user_id'],
            // 'wajibpajak_active' => 1,
        ])
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function select(Request $request)
    // {
    //     $q = $request->get('q');
    //     // dd($q);
    //     $limit = $request->get('limit') ? $request->get('limit') : 20;

    //     $data = RegencyModel::select('regency_name', 'regency_id')
    //     ->when($q, function ($query, $q) {
    //         return $query->where('regency_name', 'ilike', '%'.$q.'%');
    //     })->limit($limit)->get();
        
    //     return response()->json([
    //         'success' => 200,
    //         'data' => $data
    //     ]);
    // }
}
