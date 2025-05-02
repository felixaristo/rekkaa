<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\SubscriptionModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SubscriptionController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Subscription',
            'content' => 'admin.master.subscription.index',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('admin.index', $data);
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
            'subscription_active' => 1,
        ];
        $list = SubscriptionModel::select('ms_subscription.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SubscriptionModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'subscription_title' => 'required',
            'subscription_type' => 'required',
            'subscription_order' => 'required|numeric',
            'subscription_default' => 'required|numeric',
            'subscription_price' => 'required|numeric',
            'subscription_priceold' => 'required|numeric',
        ], [
            'subscription_title.required' => 'Nama wajib diisi!',
            'subscription_type.required' => 'Tipe wajib diisi!',
            'subscription_order.numeric' => 'Rate harus angka!',
            'subscription_default.numeric' => 'Rate harus angka!',
            'subscription_price.numeric' => 'Rate harus angka!',
            'subscription_priceold.numeric' => 'Rate harus angka!',
        ]);

        $save_data = [
            'subscription_title' => $request->input('subscription_title'),
            'subscription_order' => $request->input('subscription_order'),
            'subscription_default' => $request->input('subscription_default'),
            'subscription_type' => $request->input('subscription_type'),
            'subscription_price' => $request->input('subscription_price'),
            'subscription_priceold' => $request->input('subscription_priceold'),
            'subscription_description' => $request->input('subscription_description'),
            // 'subscription_paymentperiode' => 'MONTHLY',
        ];

        DB::beginTransaction();
        try {
            $subscription = SubscriptionModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Subscription berhasil disimpan',
                'data' => $subscription
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($subscriptionId, Request $request)
    {
        $this->validate($request, [
            'subscription_title' => 'required',
            'subscription_type' => 'required',
            'subscription_order' => 'required|numeric',
            'subscription_default' => 'required|numeric',
            'subscription_price' => 'required|numeric',
            'subscription_priceold' => 'required|numeric',
        ], [
            'subscription_title.required' => 'Nama wajib diisi!',
            'subscription_type.required' => 'Tipe wajib diisi!',
            'subscription_order.numeric' => 'Rate harus angka!',
            'subscription_default.numeric' => 'Rate harus angka!',
            'subscription_price.numeric' => 'Rate harus angka!',
            'subscription_priceold.numeric' => 'Rate harus angka!',
        ]);

        $subscription = SubscriptionModel::where([
            'subscription_id' => $subscriptionId, 
            'subscription_active' => 1,
        ])->first();
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan!'
            ]);
        }

        $save_data = [
            'subscription_title' => $request->input('subscription_title'),
            'subscription_order' => $request->input('subscription_order'),
            'subscription_default' => $request->input('subscription_default'),
            'subscription_type' => $request->input('subscription_type'),
            'subscription_price' => $request->input('subscription_price'),
            'subscription_priceold' => $request->input('subscription_priceold'),
            'subscription_description' => $request->input('subscription_description'),
            // 'subscription_paymentperiode' => 'MONTHLY',
        ];

        DB::beginTransaction();
        try {
            
            $subscription->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Subscription berhasil disimpan',
                'data' => $subscription
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($subscriptionId, Request $request)
    {
        $subscription = SubscriptionModel::where([
            'subscription_id' => $subscriptionId, 
            'subscription_active' => 1,
        ])->first();
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $subscription->update(['subscription_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Subscription berhasil dihapus',
                'data' => $subscription
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}