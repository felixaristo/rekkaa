<?php
namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Model\Transaction\UserOrderModel;
use Illuminate\Http\Request;

class OrderController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Riwayat Order',
            'content' => 'user.subscription.order.index',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

        $where = [
            'userorder_active' => 1,
            'tr_user_order.ms_user_id' => session()->get('user_data')['user_id'],
        ];
        $list = UserOrderModel::select('tr_user_order.*')
        // ->join('ms_wajib_pajak', 'wajibpajak_id', '=', 'ms_wajibpajak_id')
        ->with('wajibpajak')
        ->where($where)
        ->take($limit)->skip($offset)
        ->orderBy('userorder_created_at', 'DESC')->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = UserOrderModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }
}