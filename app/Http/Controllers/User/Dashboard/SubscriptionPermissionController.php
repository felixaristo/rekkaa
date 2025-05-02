<?php
namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrSubscriptionPermissionModel;
use App\Model\Mview\VwMenuModel;
use App\Model\Transaction\LeaveKaryawanModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use App\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionPermissionController extends Controller
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
            'title' => 'Subscription',
            'content' => 'user.dashboard.subscription-permission',
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
            'subscription_active' => '1',
            'subscription_entity_type' => $type
        ];
        $list = SubscriptionModel::select('*')
        ->with(['subscriptionpermission'])
        ->where($where)
        ->orderBy('subscription_order', 'ASC')
        ->take($limit)->skip($offset)->get();
        // dd($list[6]->sttunjangandetail[0]->karyawan);

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SubscriptionModel::where($where)->count();
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
    public function update($subscriptionId, Request $request)
    {
        // dd($request->all());
        $menus = $request->input('menu');

        $rules = [
            'menu' => 'required',
        ];
        $this->validate($request, $rules, [
            'menu.required' => 'Menu wajib diisi!',
        ]);

        $where = [
            'subscription_id' => $subscriptionId,
            'subscription_active' => '1',
        ];
        $subscription = SubscriptionModel::select('*')
        ->with(['subscriptionpermission'])
        ->where($where)->first();
        if(!$subscription) {
            abort(404);
        }
        // dd($subscription);
        $subpermissions = json_decode($subscription->subscriptionpermission[0]->subscriptionpermission_permissions);
        // dd($subpermissions, $menus);
        // foreach($subpermissions as $subpermit) {
        //     if()
        // }

        $menu_ids = array_keys($menus);
        $active_menu_ids = [];
        // get menus
        if($menu_ids) {
            $getmenus = VwMenuModel::whereIn('menu_id', $menu_ids)->where(['menu_active' => '1'])->get();

            // dd($getmenus);
            foreach($getmenus as $mn) {
                array_push($active_menu_ids, $mn->menu_id);
            }
        }
        // dd($active_menu_ids);
        // dd($menus);
        $permissions = [];
        if(count($menus) > 0) {
            $isoldqtygreater = false;
            $isoldqtygreaterfield = [];
            foreach($menus as $mnid => $mn) {
                $temp = [
                    'menu_id' => strval($mnid),
                    'is_active' => (in_array($mnid, $active_menu_ids)) ? 1 : 0,
                ];
                // $exist = false;
                foreach($mn as $key => $val) {
                    if($val) {
                        $temp[$key] = ($val) ? $val : 0;
                    }
                }
                if(count($temp) == 2) {
                    continue;
                }
                // if(!$exist) {
                //     continue;
                // }
                    
                // check for old qty is greater than new one
                foreach($subpermissions as $sub) {
                    $subarr = (array) $sub;
                    
                    if($subarr['menu_id'] == $mnid) {
                        // if($mnid == 27)
                        //     dd($subarr['Q'], $temp['Q']);
                        // // $sub['']
                        if(isset($subarr['Q']) && isset($temp['Q'])) {
                            if($subarr['Q'] > $temp['Q']) {
                                $isoldqtygreater = true;
                                $isoldqtygreaterfield = [
                                    'menu_id' => $mnid,
                                    // 'menu_name' => $subarr['menu_name'],
                                    'code' => 'Q',
                                ];
                                break;
                            }
                        }

                        if(isset($subarr['Q_SEND_EMAIL']) && isset($temp['Q_SEND_EMAIL'])) {
                            if($subarr['Q_SEND_EMAIL'] > $temp['Q_SEND_EMAIL']) {
                                $isoldqtygreater = true;
                                $isoldqtygreaterfield = [
                                    'menu_id' => $mnid,
                                    // 'menu_name' => $subarr['menu_name'],
                                    'code' => 'Q_SEND_EMAIL',
                                ];
                                break;
                            }
                        }

                        if(isset($subarr['Q_SEND_WA']) && isset($temp['Q_SEND_WA'])) {
                            if($subarr['Q_SEND_WA'] > $temp['Q_SEND_WA']) {
                                $isoldqtygreater = true;
                                $isoldqtygreaterfield = [
                                    'menu_id' => $mnid,
                                    // 'menu_name' => $subarr['menu_name'],
                                    'code' => 'Q_SEND_WA',
                                ];
                                break;
                            }
                        }

                        if(isset($subarr['Q_COPY_LINK']) && isset($temp['Q_COPY_LINK'])) {
                            if($subarr['Q_COPY_LINK'] > $temp['Q_COPY_LINK']) {
                                $isoldqtygreater = true;
                                $isoldqtygreaterfield = [
                                    'menu_id' => $mnid,
                                    // 'menu_name' => $subarr['menu_name'],
                                    'code' => 'Q_COPY_LINK',
                                ];
                                break;
                            }
                        }

                        if(isset($subarr['Q_DOWNLOAD_PDF']) && isset($temp['Q_DOWNLOAD_PDF'])) {
                            if($subarr['Q_DOWNLOAD_PDF'] > $temp['Q_DOWNLOAD_PDF']) {
                                $isoldqtygreater = true;
                                $isoldqtygreaterfield = [
                                    'menu_id' => $mnid,
                                    // 'menu_name' => $subarr['menu_name'],
                                    'code' => 'Q_DOWNLOAD_PDF',
                                ];
                                break;
                            }
                        }
                    }
                }
                if($isoldqtygreater) {
                    break;
                }

                array_push($permissions, $temp);
            }
        }
        if($isoldqtygreater) {
            return response()->json([
                'success' => false,
                'message' => 'Qty baru tidak boleh lebih kecil dari sebelumnya',
                'data' => $isoldqtygreaterfield
            ]);
        }
        // dd($isoldqtygreater,$permissions);


        $wajibpajaksubscription = WajibPajakSubscriptionModel::select('*')->where([
            'wajibpajaksubscription_active' => '1',
            'ms_subscription_id' => $subscriptionId,
        ])->get()->toArray();

        // dd($permissions);
        if(count($wajibpajaksubscription) > 0) {
            foreach($wajibpajaksubscription as &$wpsub) {
                if($wpsub['wajibpajaksubscription_permission']) {
                    $wppermission = json_decode($wpsub['wajibpajaksubscription_permission']);
                    // dd($wppermission, 'oioi');
                    $newpermission = [];
                    foreach($permissions as $npermission) {
                        // dd($npermission);
                        $newpermit = $npermission;
                        foreach($wppermission as $permit) {
                            $permitarray = (array) $permit;
                            if($permitarray['menu_id'] = $npermission['menu_id']) {
                                // dd($npermission);
                                foreach($npermission as $key => $val) {
                                    if($key == 'Q') {
                                        if(isset($permitarray[$key]) && $val > 0) { // if qty exist.
                                            if($permitarray[$key] > $val) { // check if old qty is greater than new one.
                                                $newpermit[$key] = $permitarray[$key];
                                            }
                                        }
                                    } else if($key == 'Q_SEND_EMAIL') {
                                        if(isset($permitarray[$key])) { // if qty exist.
                                            if($permitarray[$key] > $val) { // check if old qty is greater than new one.
                                                $newpermit[$key] = $permitarray[$key];
                                            }
                                        }
                                    } else if($key == 'Q_SEND_WA') {
                                        if(isset($permitarray[$key])) { // if qty exist.
                                            if($permitarray[$key] > $val) { // check if old qty is greater than new one.
                                                $newpermit[$key] = $permitarray[$key];
                                            }
                                        }
                                    } else if($key == 'Q_COPY_LINK') {
                                        if(isset($permitarray[$key])) { // if qty exist.
                                            if($permitarray[$key] > $val) { // check if old qty is greater than new one.
                                                $newpermit[$key] = $permitarray[$key];
                                            }
                                        }
                                    } else if($key == 'Q_DOWNLOAD_PDF') {
                                        if(isset($permitarray[$key])) { // if qty exist.
                                            if($permitarray[$key] > $val) { // check if old qty is greater than new one.
                                                $newpermit[$key] = $permitarray[$key];
                                            }
                                        }
                                    }
                                }
                                break;
                            }   
                        }
                        array_push($newpermission, $newpermit); 
                    }
                    $wpsub['wajibpajaksubscription_permission'] = json_encode($newpermission);
                } else { // if null
                    $wpsub['wajibpajaksubscription_permission'] = json_encode($permissions);
                }
            }
        }
        // dd('ooo',$wajibpajaksubscription);
        DB::beginTransaction();
        try {
            MrSubscriptionPermissionModel::where([
                'subscriptionpermission_id' => $subscription->subscriptionpermission[0]->subscriptionpermission_id,
                'ms_subscription_id' => $subscription->subscription_id,
            ])
            ->update([
                'subscriptionpermission_permissions' => json_encode($permissions)
            ]);

            WajibPajakSubscriptionModel::upsert($wajibpajaksubscription, 'wajibpajaksubscription_id');


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hak Akses subscription berhasil diperbarui',
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