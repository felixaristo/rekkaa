<?php

namespace App\Console\Commands;

use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Transaction\UserOrderModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use Error;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoDowngradePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-downgradeplan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto downgrade plan for expired user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $get_stdata = getSetting('MAX_GET_DATA');
        $decode_value = ($get_stdata) ? json_decode($get_stdata->setting_value) : null;
        $max_data = ($decode_value) ? $decode_value->value : 50;
        $qty = 1;

        $wpsubscription = WajibPajakSubscriptionModel::select('tr_wajib_pajak_subscription.*', 'ms_user.*', 'ms_subscription.subscription_type')
        ->with(['subscription'])
        ->join("ms_user", 'ms_user.user_id', 'tr_wajib_pajak_subscription.ms_user_id')
        ->join("ms_subscription", 'ms_subscription.subscription_id', 'tr_wajib_pajak_subscription.ms_subscription_id')
        ->where(['wajibpajaksubscription_active' => '1'])
        ->whereNotIn('ms_subscription.subscription_type', ['FREE'])
        ->whereRaw("(TO_CHAR(wajibpajaksubscription_expired_at, 'YYYY-MM-DD')::date - (NOW()::date + 1)) = 0")
        ->limit($max_data)
        ->get();

        // dd($wpsubscription);
        if(count($wpsubscription) > 0) {
            $wpsubscription_ids = [];
            $wpsubs_tempdata = [];
            $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' '.$qty.' month'));

            // dd($wpsubscription_ids);
            DB::beginTransaction();
            try {
                $wpids = [];
                $usergroupmemberdatas = [];
                $userggroupaccessdatas = [];
                $wpsubscriptiondatas = [];
                foreach($wpsubscription as $wpsub) {
                    array_push($wpsubscription_ids, $wpsub->wajibpajaksubscription_id);

                    $subscription = SubscriptionModel::where([
                        'subscription_type' => 'FREE', 
                        'subscription_entity_type' => $wpsub->wajibpajak->wajibpajak_type, 
                        'subscription_active' => '1'
                    ])->first();
                    $noorder = strtoupper('ORDER-FREE');
                    $expired_at = date('Y-m-d H:i:s');
                    $description = 'Rekkaa - Downgrade Subscription '. $subscription->subscription_title;

                    $addonsavedata = [];
                    $grouppermissions = [];
                    $menu_ids = [];
                    $decodepermission = [];
                    foreach($subscription->subscriptionpermission as $sub) {
                        $decodepermission = json_decode($sub->subscriptionpermission_permissions);
                        foreach($decodepermission as $pm) {
                            array_push($menu_ids, $pm->menu_id);
                        }
                        
                        $addonsavedata = array_merge($addonsavedata, $decodepermission);
                    }
                    
                    $newaddonsavedata = [];
                    $temp = [];
                    
                    foreach($addonsavedata as $dp) {
                        $dparr = (array) $dp;
                        foreach($dparr as $key => $val) {
                            if($key == 'menu_id') {
                                if(isset($temp[$val]) && $temp[$val]) {
                                    $dparr['duplicate'] = $temp[$val];
                                    $temp[$val] = $dparr;
                                } else {
                                    $temp[$val] = $dparr;
                                }
                                break;
                            }
                        }
                    }
                    // $newtemp = [];
                    if($temp) {
                        foreach($temp as &$tmp) {
                            // var_dump($tmp);
                            if(isset($tmp['duplicate']) && $tmp['duplicate']) {
                                $duplicate = $tmp['duplicate'];
                                foreach($duplicate as $dpkey => $dpval) {
                                    if($dpkey != 'menu_id') {
                                        if(isset($tmp[$dpkey])) {
                                            $tmp[$dpkey] = intval($dpval + $tmp[$dpkey]);
                                        } else {
                                            $tmp[$dpkey] = intval($dpval);
                                        }
                                    }
                                }
                                unset($tmp['duplicate']);

                            }
                            array_push($newaddonsavedata, $tmp);
                        }
                    }
                    
                    if($menu_ids) {
                        $permissions = PermissionModel::select('permission_id', 'permission_code', 'permission_default_group')->whereIn('ms_menu_id', $menu_ids)->get();
                        if($permissions) {
                            foreach($permissions as $permission) {
                                array_push($grouppermissions, $permission->permission_id);
                            }
                        }
                    }

                    array_push($wpids, $wpsub->ms_wajibpajak_id);
                    // insert user order
                    $order = UserOrderModel::create([
                        'ms_user_id' => $wpsub->ms_user_id,
                        'ms_wajibpajak_id' => $wpsub->ms_wajibpajak_id,
                        'ms_subscription_id' => $subscription->subscription_id,
                        'userorder_no' => $noorder,
                        'userorder_qty' => $qty,
                        'userorder_price' => 0,
                        'userorder_discount' => 0,
                        'userorder_total' => 0,
                        'userorder_subscriptiontype' => 'FREE',
                        'userorder_paymentperiode' => $qty,
                        'userorder_expired_at' => $expired_at,
                        'userorder_description' => $description,
                        'userorder_xenditurl' => null,
                        'userorder_xenditdata' => null,
                        'userorder_subscriptiondata' => '',
                        'userorder_status' => 'PAID'
                    ]);
                    
                    // Admin Group
                    $usergroup = UserGroupModel::create([
                        'usergroup_name' => 'Admin',
                        'usergroup_code' => 'REKKAA',
                        'ms_wajibpajak_id' => $wpsub->ms_wajibpajak_id,
                    ]);

                    array_push($usergroupmemberdatas, [
                        'ms_usergroup_id' => $usergroup->usergroup_id,
                        'usergroupmember_members' => $wpsub->ms_user_id,
                    ]);

                    array_push($userggroupaccessdatas, [
                        'ms_usergroup_id' => $usergroup->usergroup_id,
                        'usergroupaccess_permissions' => implode(',',$grouppermissions)
                    ]);

                    array_push($wpsubscriptiondatas, [
                        'ms_wajibpajak_id' => $wpsub->ms_wajibpajak_id,
                        'ms_user_id' => $wpsub->ms_user_id,
                        'ms_subscription_id' => $subscription->subscription_id,
                        'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
                        'wajibpajaksubscription_expired_at' => $subscription_expired_at,
                        'wajibpajaksubscription_permission' => ($newaddonsavedata) ? json_encode($newaddonsavedata) : null,
                        'tr_userorder_id' => $order->userorder_id
                    ]);
                }

                if(count($wpids) > 0) {
                    WajibPajakSubscriptionModel::whereIn('ms_wajibpajak_id', [$wpsub->ms_wajibpajak_id])->update([
                        'wajibpajaksubscription_active' => 0,
                    ]);
                    UserGroupMemberModel::insert($usergroupmemberdatas);
                    UserGroupAccessModel::insert($userggroupaccessdatas);
                    WajibPajakSubscriptionModel::insert($wpsubscriptiondatas);
                }
                
                DB::commit();
            } catch(Error $e) {
                DB::rollBack();
                throw new Error('Downgrade User Plan Error: '.$e->getMessage());
            }
        }
    }
}
