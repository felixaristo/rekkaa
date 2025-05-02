<?php
namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\MasterRelation\MrSubscriptionPermissionModel;
use App\Model\Mview\VwMenuModel;
use App\Model\Transaction\UserOrderModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionPermissionWajibpajakController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $code = $request->get('code');
        if($code != env('APP_INTERNALCODE')) {
            return redirect('/');
        }
        
        $data = [
            'title' => 'Subscription Wajib Pajak',
            'content' => 'user.dashboard.subscription-permission-wajibpajak',
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
        $type = $request->input('type');
        $where = [
            'wajibpajak_active' => '1',
            // 'subscription_entity_type' => $type
        ];
        $list = WajibPajakModel::select('*')
        ->with(['wajibpajaksubscription', 'wajibpajaksubscription.subscription', 'wajibpajaksubscription.userorder'])
        ->where($where)
        ->orderBy('wajibpajak_id', 'DESC')
        ->take($limit)->skip($offset)->get();
        // dd($list[6]->sttunjangandetail[0]->karyawan);

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = WajibPajakModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getmenu(Request $request)
    {
        $where = [
            'menu_active' => '1',
        ];
        $list = getMenu();
        $data = [
            'data' => $list
        ];
        return response()->json($data);
    }

    /**
     * Update the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($wajibpajakId, Request $request)
    {
        // dd($request->all());
        $menus = $request->input('menu');
        // dd($menus);

        $rules = [
            'menu' => 'required',
        ];
        $this->validate($request, $rules, [
            'menu.required' => 'Menu wajib diisi!',
        ]);

        $where = [
            'wajibpajak_id' => $wajibpajakId,
            'wajibpajak_active' => '1',
        ];
        $wajibpajak = WajibPajakModel::select('*')
        ->with(['wajibpajaksubscription'])
        ->where($where)->first();
        if(!$wajibpajak) {
            abort(404);
        }
        // dd($wajibpajak);
        $wajibpajaksubscription_permission = json_decode($wajibpajak->wajibpajaksubscription->wajibpajaksubscription_permission);
        // dd(json_decode($wajibpajak->wajibpajaksubscription->wajibpajaksubscription_permission));
        // dd($subpermissions, $menus);
        // foreach($subpermissions as $subpermit) {
        //     if()
        // }
        // dd($wajibpajaksubscription);

        $menu_ids = array_keys($menus);
        $active_menu_ids = [];
        $active_permissions = [];
        // get menus
        if($menu_ids) {
            $getmenus = VwMenuModel::whereIn('menu_id', $menu_ids)->where(['menu_active' => '1'])->get();

            // dd($getmenus[0]->permissions);
            foreach($getmenus as $mn) {
                // dd($mn->permissions->toArray());
                array_push($active_menu_ids, $mn->menu_id);
                $active_permissions = array_merge($active_permissions, $mn->permissions->toArray());
                // $active_permissions = $mn->permissions->toArray();
            }
        }
        // dd($active_menu_ids);
        $permissions = [];
        if(count($menus) > 0) {
            foreach($menus as $mnid => $mn) {
                $temp = [
                    'menu_id' => strval($mnid),
                    'is_active' => (in_array($mnid, $active_menu_ids)) ? 1 : 0,
                ];
                // $exist = false;
                foreach($mn as $key => $val) {
                    if($key != 'menu_id' && $key != 'is_active') {
                        if($val) {
                            $temp[$key] = ($val) ? $val : 0;
                        }
                    }
                }
                if(count($temp) == 2) {
                    continue;
                }
                if(isset($temp))
                    array_push($permissions, $temp);
            }
        }
        $newpermission_ids = [];
        // foreach($wajibpajaksubscription_permission as $subcriptionpermit) {
        //     $subcriptionpermitarray = (array) $subcriptionpermit;
            // dd($subcriptionpermitarray['menu_id']);
            foreach($active_permissions as $permit) {
                // echo $subcriptionpermitarray['menu_id'] .'=='. $permit['ms_menu_id'];
                // if($subcriptionpermitarray['menu_id'] == $permit['ms_menu_id']) {
                    // if(isset($subcriptionpermitarray[$permit['permission_code']])) {
                        array_push($newpermission_ids, $permit['permission_id']);
                    // }
                // }
            }
        // }
        
        $usergroup = UserGroupModel::where([
            'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'usergroup_active' => '1',
            'usergroup_name' => 'Admin',
        ])->first();
        // dd($newpermission_ids, 'oooi');
        // dd($newpermission_ids);
        DB::beginTransaction();
        try {
            
            // update ke user group access
            $usergroupaccess = UserGroupAccessModel::where([
                'ms_usergroup_id' => $usergroup->usergroup_id,
            ])->update([
                'usergroupaccess_permissions' => implode(',', $newpermission_ids)
            ]);

            WajibPajakSubscriptionModel::where(['wajibpajaksubscription_id' => $wajibpajak->wajibpajaksubscription->wajibpajaksubscription_id])
            ->update(['wajibpajaksubscription_permission' => json_encode($permissions)]);


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hak Akses wajib pajak berhasil diperbarui',
            ]);
        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // /**
    //  * Update the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function orderupdate($wajibpajakId, Request $request)
    // {
    //     // dd($request->all());
    //     $tipe = $request->input('tipe');
    //     $harga = $request->input('harga');
    //     $periode = $request->input('periode');

    //     $rules = [
    //         'tipe' => 'required',
    //         'harga' => 'required',
    //         'periode' => 'required',
    //     ];
    //     $this->validate($request, $rules, [
    //         'tipe.required' => 'Tipe wajib diisi!',
    //         'harga.required' => 'Harga wajib diisi!',
    //         'periode.required' => 'Periode wajib diisi!',
    //     ]);

    //     $where = [
    //         'wajibpajak_id' => $wajibpajakId,
    //         'wajibpajak_active' => '1',
    //     ];
    //     $wajibpajak = WajibPajakModel::select('*')
    //     ->with(['wajibpajaksubscription', 'wajibpajaksubscription.userorder'])
    //     ->where($where)->first();
    //     if(!$wajibpajak) {
    //         abort(404);
    //     }
    //     // dd($wajibpajak);
    //     $userorder = $wajibpajak->wajibpajaksubscription->userorder;
    //     $wajibpajaksubscription = $wajibpajak->wajibpajaksubscription;
    //     // dd($newpermission_ids, 'oooi');
    //     // dd($newpermission_ids);
    //     // dd($userorder->userorder_subscriptiontype);
    //     $subscription_expired_at = date('Y-m-d H:i:s', strtotime($userorder->userorder_created_at . " +{$periode} month"));
    //     $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('XENDIT_EXPIRED_INVOICE').'  day'));
    //     DB::beginTransaction();
    //     try {
    //         if($userorder->userorder_subscriptiontype == $tipe) {
    //             $userorder->update([
    //                 'userorder_qty' => $periode,
    //                 'userorder_total' => $harga,
    //                 'userorder_subscriptiontype' => $tipe,
    //                 'userorder_paymentperiode' => $periode,
    //             ]);

    //             $wajibpajaksubscription->update([
    //                 'wajibpajaksubscription_expired_at' => $subscription_expired_at,
    //             ]);
    //         } else {
    //             $wajibpajaksubscription->update([
    //                 'wajibpajaksubscription_active' => 0,
    //             ]);
    //             $newuserorder = UserOrderModel::create([
    //                 'ms_user_id' => $userorder->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //                 // 'ms_subscription_id' => $userorder->subscription_id,
    //                 'ms_subscription_id' => 2, // BRONZE
    //                 'userorder_qty' => $periode,
    //                 'userorder_price' => $harga,
    //                 'userorder_discount' => 0,
    //                 'userorder_total' => $harga,
    //                 'userorder_subscriptiontype' => $tipe,
    //                 'userorder_paymentperiode' => $periode,
    //                 'userorder_expired_at' => $userorder_expired_at,
    //                 'userorder_description' => $userorder->userorder_description,
    //                 'userorder_xenditurl' => null,
    //                 'userorder_xenditdata' => null,
    //                 'userorder_subscriptiondata' => '',
    //                 'userorder_status' => 'PAID',
    //                 'userorder_qty' => $periode,
    //                 'userorder_total' => $harga,
    //                 'userorder_subscriptiontype' => $tipe,
    //                 'userorder_paymentperiode' => $periode,
    //             ]);

    //             WajibPajakSubscriptionModel::create([
    //                 'ms_wajibpajak_id' => $newuserorder->ms_wajibpajak_id,
    //                 'ms_user_id' => $newuserorder->ms_user_id,
    //                 'ms_subscription_id' => $newuserorder->ms_subscription_id,
    //                 'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
    //                 'wajibpajaksubscription_expired_at' => $subscription_expired_at,
    //                 'wajibpajaksubscription_permission' => null,
    //                 'tr_userorder_id' => $newuserorder->userorder_id
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Subscription berhasil diperbarui',
    //         ]);
    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ]);
    //     } catch(Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    /**
     * Update the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderextend($wajibpajakId, Request $request)
    {
        // dd($request->all());
        // $tipe = $request->input('tipe');
        $tipe = 'BRONZE';
        $harga = $request->input('harga');
        $periode = $request->input('periode');
        $notes = $request->input('notes');
        $qty = 1;
        $rules = [
            // 'tipe' => 'required',
            'harga' => 'required',
            'periode' => 'required',
        ];
        $this->validate($request, $rules, [
            // 'tipe.required' => 'Tipe wajib diisi!',
            'harga.required' => 'Harga wajib diisi!',
            'periode.required' => 'Periode wajib diisi!',
        ]);

        $where = [
            'wajibpajak_id' => $wajibpajakId,
            'wajibpajak_active' => '1',
        ];
        $wajibpajak = WajibPajakModel::select('*')
        ->with(['wajibpajaksubscription', 'wajibpajaksubscription.userorder'])
        ->where($where)->first();
        if(!$wajibpajak) {
            abort(404);
        }
        // dd($periode);
        $userorder = $wajibpajak->wajibpajaksubscription->userorder;
        $wajibpajaksubscription = $wajibpajak->wajibpajaksubscription;
        // dd($newpermission_ids, 'oooi');
        // dd($newpermission_ids);
        // dd($userorder->userorder_subscriptiontype);
        $subscription_expired_at = date('Y-m-d H:i:s', strtotime($periode.' '.date('H:i:s')));
        // dd($subscription_expired_at);
        $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('XENDIT_EXPIRED_INVOICE').'  day'));
        DB::beginTransaction();
        try {
            $wajibpajaksubscription->update([
                'wajibpajaksubscription_active' => 0,
            ]);
            // dd($qty);
            $newuserorder = UserOrderModel::create([
                'ms_user_id' => $userorder->ms_user_id,
                'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                // 'ms_subscription_id' => $userorder->subscription_id,
                'ms_subscription_id' => 2, // BRONZE
                'userorder_qty' => $qty,
                'userorder_price' => $harga,
                'userorder_discount' => 0,
                'userorder_total' => $harga,
                'userorder_subscriptiontype' => $tipe,
                'userorder_paymentperiode' => $qty,
                'userorder_expired_at' => $userorder_expired_at,
                // 'userorder_description' => $userorder->userorder_description,
                'userorder_description' => $notes,
                'userorder_xenditurl' => null,
                'userorder_xenditdata' => null,
                'userorder_subscriptiondata' => '',
                'userorder_status' => 'PAID',
                'userorder_qty' => $qty,
                'userorder_total' => $harga,
                'userorder_subscriptiontype' => $tipe,
            ]);

            WajibPajakSubscriptionModel::create([
                'ms_wajibpajak_id' => $newuserorder->ms_wajibpajak_id,
                'ms_user_id' => $newuserorder->ms_user_id,
                'ms_subscription_id' => $newuserorder->ms_subscription_id,
                'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
                'wajibpajaksubscription_expired_at' => $subscription_expired_at,
                'wajibpajaksubscription_permission' => $wajibpajaksubscription->wajibpajaksubscription_permission,
                'tr_userorder_id' => $newuserorder->userorder_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription berhasil diperbarui',
            ]);
        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}