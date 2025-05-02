<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\UserOrderModel;
use App\Model\Transaction\UserSubscriptionPermissionModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use App\User;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NpwpController extends Controller
{
    //  /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index(Request $request)
    // {
    //     $data = [
    //         'title' => 'NPWP',
    //         'content' => 'user.npwp.index',
    //         'create_npwp' => url('/npwp/create'),
    //         'permission' => PermissionModel::where(['permission_active' => 1])->orderBy('permission_parent', 'ASC')
    //         ->orderBy('permission_child', 'ASC')->orderBy('permission_order', 'ASC')->get(),
    //         'subscription' => SubscriptionModel::with(['subscriptionpermission'])->where(['subscription_active' => 1])->whereNotIn('subscription_type', ['FREES'])->orderBy('subscription_order', 'ASC')->get()
    //     ];
    //     if($request->ajax()) {
    //         return view($data['content'], $data)->render();
    //     } else {
    //         return view('user.index', $data);
    //     }
    // }

    // public function activate(Request $request)
    // {
    //     $wajibpajak_id = $request->get('wp_id');
    //     if(!$wajibpajak_id) {
    //         return redirect()->back();
    //     }
    //     $wajibpajak = WajibPajakModel::select('ms_wajib_pajak.*')
    //     // ->join('ms_wajib_pajak_user', 'ms_wajibpajak_id', '=', 'wajibpajak_id')
    //     ->where([
    //         'wajibpajak_id' => $wajibpajak_id,
    //         'wajibpajak_active' => 1,
    //         'ms_wajib_pajak.ms_user_id' => session()->get('user_data')['user_id'],
    //     ])
    //     ->first();
    //     if(!$wajibpajak) {
    //         return redirect()->back();
    //     };

    //     session()->put('wajibpajak_current', [
    //         'wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //         'wajibpajak_name' => $wajibpajak->wajibpajak_name,
    //         'wajibpajak_npwp' => $wajibpajak->wajibpajak_npwp,
    //         'wajibpajak_type' => $wajibpajak->wajibpajak_type,
    //     ]);
       
    //     return redirect('/user/beranda');
    // }

    // public function datatable(Request $request)
    // {
    //     $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
    //     $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

    //     $where = [
    //         'wajibpajak_active' => 1,
    //         'ms_wajib_pajak.ms_user_id' => session()->get('user_data')['user_id'],
    //     ];
    //     $list = WajibPajakModel::select('ms_wajib_pajak.*', 'klu_code', 'klu_description', 'regency_name')
    //     ->with(['userorder'])
    //     ->where($where)
    //     // ->join('ms_wajib_pajak_user', 'ms_wajibpajak_id', '=', 'wajibpajak_id')
    //     ->leftJoin('ms_klu', 'ms_klu_id', '=', 'klu_id')
    //     ->leftJoin('ms_regencies', 'ms_regency_id', '=', 'regency_id')
    //     ->take($limit)->skip($offset)
    //     ->orderBy('wajibpajak_created_at', 'DESC')->get();
    //     $data['draw'] = $request->input('draw');
	// 	$data['recordsTotal'] = WajibPajakModel::where($where)->count();
	// 	$data['recordsFiltered'] = $data['recordsTotal'];
    //     $data['data'] = $list;

    //     return response()->json($data);
    // }

    // public function store(Request $request)
    // {
    //     $validation = [
    //         'wajibpajak_name' => 'required',
    //         'wajibpajak_email' => 'required|email',
    //         'wajibpajak_type' => 'required|in:BADAN,INDIVIDU',
    //         'wajibpajak_phone' => 'required',
    //         'wajibpajak_city' => 'required|integer',
    //         'wajibpajak_address' => 'required',
    //         'ms_klu_id' => 'required|integer',
    //         'wajibpajak_postal_code' => 'required',
    //     ];
    //     $validation_message = [
    //         'wajibpajak_name.required' => 'Nama wajib diisi!',
    //         'wajibpajak_type.required' => 'Tipe wajib diisi!',
    //         'ms_klu_id.required' => 'KLU wajib diisi!',
    //         'wajibpajak_address.required' => 'Alamat wajib diisi!',
    //         'wajibpajak_postal_code.required' => 'Alamat wajib diisi!',
    //         'wajibpajak_email.required' => 'Email wajib diisi!',
    //         'wajibpajak_email.email' => 'Format email tidak sesuai!',
    //         'wajibpajak_phone.required' => 'No. Telepon wajib diisi!',
    //         'wajibpajak_city.required' => 'Kota wajib diisi!',
    //         'wajibpajak_city.integer' => 'ID Kota harus angka!',
    //         // 'wajibpajak_periodepembayaran.required' => 'Periode Pembayaran wajib diisi!',
    //         // 'subscription_id.required' => 'Subcription wajib diisi!',
    //     ];

    //     $wajibpajak_type = $request->input('wajibpajak_type');
    //     $nik = '0000000000000000';
    //     $npwp = $request->input('wajibpajak_npwp');
    //     if($wajibpajak_type === 'BADAN' || $request->input('register_kepemilikan_npwp') != 't') {
    //         $validation['wajibpajak_npwp'] = 'required|min:20|max:20|unique:ms_wajib_pajak,wajibpajak_npwp';

    //         $validation_message['wajibpajak_npwp.required'] = 'NPWP wajib diisi!';
    //         $validation_message['wajibpajak_npwp.unique'] = 'NPWP sudah digunakan!';
    //     } 
        
    //     if($wajibpajak_type === 'INDIVIDU') { // INDIVIDU
    //         if($request->input('register_kepemilikan_npwp') == 't') {
    //             $npwp = '00.000.000.0-000.000';
    //         }

    //         $validation['wajibpajak_nik'] = 'required|min:16|max:16|unique:ms_wajib_pajak,wajibpajak_nik';
    //         $validation_message['wajibpajak_nik.required'] = 'NIK wajib diisi!';
    //         $validation_message['wajibpajak_nik.unique'] = 'NIK sudah digunakan!';

    //         $nik = $request->input('wajibpajak_nik');
    //     }

    //     $this->validate($request, $validation, $validation_message);
        
    //     // $subscription_id = 9; // FREE
    //     // $periode_pembayaran = 'MONTHLY'; // MONTHLY
    //     $subscription_id = ($request->input('subscription_id')) ? $request->input('subscription_id') : 9; // PLAN
    //     $periode_pembayaran = ($request->input('wajibpajak_periodepembayaran')) ? $request->input('wajibpajak_periodepembayaran') : 'MONTHLY';
        
    //     $subscription = SubscriptionModel::select('ms_subscription.*', DB::raw("(SELECT regency_name
    //     FROM ms_regencies WHERE regency_id = ".intval($request->input('wajibpajak_city')).") as regency_name"))
    //     ->where([
    //         'subscription_id' => $subscription_id,
    //         'subscription_active' => 1
    //     ])
    //     ->with(['subscriptionpermission'])->first();
    //     if(!$subscription) {
    //         abort(404);
    //     }
    //     $grouppermissions = [];
    //     $menu_ids = [];
    //     foreach($subscription->subscriptionpermission as $sub) {
    //         $decodepermission = json_decode($sub->subscriptionpermission_permissions);
    //         foreach($decodepermission as $pm) {
    //             array_push($menu_ids, $pm->menu_id);
    //         }
    //     }
    //     if($menu_ids) {
    //         $permissions = PermissionModel::select('permission_id', 'permission_code')->whereIn('ms_menu_id', $menu_ids)->get();
    //         if($permissions) {
    //             foreach($permissions as $permission) {
    //                 array_push($grouppermissions, $permission->permission_id);
    //             }
    //         }
    //     }

    //     $user = User::with(['userwajibpajak' => function($q) {
    //         $q->where('userwajibpajak_owner', '=', 1);
    //     }])->where(['user_id' => session()->get('user_data')['user_id']])->first();
    //     // dd($subscription->subscriptionpermission);

    //     $diskon = 0;
    //     $diskon_price = 0;
    //     $price = $subscription->subscription_price;
    //     $total = 0;

    //     $periode_pembayaran_label = '';
    //     $subscription_expired_at = null;
    //     if($periode_pembayaran == 'MONTHLY') {
    //         $diskon = $subscription->subscription_monthlydiscount;
    //         $periode_pembayaran_label = '1 Bulan Sekali';
    //         $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 1 month'));
    //     } else if($periode_pembayaran == 'QUARTELY') {
    //         $diskon = $subscription->subscription_quarterlydiscount;
    //         $periode_pembayaran_label = '4 Bulan Sekali';
    //         $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 4 month'));
    //     } else if($periode_pembayaran == 'SEMI_ANNUAL') {
    //         $diskon = $subscription->subscription_semiannualdiscount;
    //         $periode_pembayaran_label = '6 Bulan Sekali';
    //         $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 6 month'));
    //     } else if($periode_pembayaran == 'YEARLY') {
    //         $diskon = $subscription->subscription_yearlydiscount;
    //         $periode_pembayaran_label = '12 Bulan Sekali';
    //         $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 12 month'));
    //     }
    //     $diskon_price = $price * $diskon / 100;
    //     $total = $price - intval($diskon_price);

    //     $noorder = 'ORDER-'.date('YmdHis').rand();
    //     // $description = 'Rekkaa - Order Subscription '. $subscription->subscription_title;
    //     $description = 'Rekkaa - Order Subscription '. $subscription->subscription_title. ' for '.$request->input('wajibpajak_name');

    //     $phone = $request->input('wajibpajak_phone');

    //     $phone = "+62".substr($phone, -11, -7) . "" . substr($phone, -7, -4) . "" . substr($phone, -4);
    //     $expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +".env('XENDIT_EXPIRED_INVOICE')." day"));
    //     // dd($phone);

    //     // dd($noinvoice);
    //     // dd($user->userwajibpajak);
    //     $xendit_url = '';
    //     $xendit_data = null;
    //     if($subscription->subscription_type != 'FREESSS') {
    //         $params = [
    //             'external_id' => $noorder,
    //             'amount' => $total,
    //             'description' => $description,
    //             'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
    //             'name' => $user->userwajibpajak->userwajibpajak_name,
    //             'surname' => $user->userwajibpajak->userwajibpajak_name,
    //             'email' => $user->user_email,
    //             'phone' => $user->userwajibpajak->userwajibpajak_phone,
    //             'city' => $subscription->regency_name,
    //             'country' => 'Indonesia',
    //             'postal_code' => $request->input('wajibpajak_postal_code'),
    //             'city' => $subscription->regency_name,
    //             'address' => $request->input('wajibpajak_address'),
    //             'items' => [
    //                 [
    //                     'name' => $description .' - '.$periode_pembayaran_label,
    //                     'quantity' => 1,
    //                     'price' => $price,
    //                     'category' => $periode_pembayaran_label,
    //                     'url' => '#'
    //                 ]
    //             ],
    //             'fees' => [
    //                 [
    //                     'type' => 'DISKON',
    //                     'value' => ($diskon_price > 0) ? intval('-'.$diskon_price) : $diskon_price
    //                 ]
    //             ]
    //         ];
    //         // echo json_encode($params);
    //         // die;
    //         $createInvoice = xendit_generate_invoice($params);
    //         $xendit_url = $createInvoice['invoice_url'];
    //         $xendit_data = json_encode($createInvoice);
                
    //         // $expdate = new DateTime($expired_at);
    //         // // paperid
    //         // $params = [
    //         //     'invoice_date' => date('d-m-Y'),
    //         //     'due_date' => $expdate->format('d-m-Y'),
    //         //     'number' => $noorder,
    //         //     'amount' => $total,
    //         //     'description' => $description,
    //         //     'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
    //         //     'customer_id' => strval($user->user_id),
    //         //     'customer_name' => $user->user_name,
    //         //     'customer_email' => $user->user_email,
    //         //     'customer_phone' => $user->user_phone,
    //         //     'items' => [
    //         //         [
    //         //             'name' => $description .' - '.$periode_pembayaran_label,
    //         //             'description' => $description,
    //         //             'category' => $periode_pembayaran_label,
    //         //             'url' => '#',
    //         //             'quantity' => 1,
    //         //             'price' => $price,
    //         //             'tax' => 0,
    //         //             'discount' => ($diskon_price > 0) ? intval('-'.$diskon_price) : $diskon_price,
    //         //             'additional_info' => null
    //         //         ]
    //         //     ],
    //         //     'fees' => [
    //         //         [
    //         //             'type' => 'DISKON',
    //         //             'value' => ($diskon_price > 0) ? intval('-'.$diskon_price) : $diskon_price
    //         //         ]
    //         //     ],
    //         //     'additional_info' => null
    //         // ];
    //         // $createInvoice = paper_generate_invoice($params);
    //         // print_r($createInvoice['invoice_url']);die;
    //         // if($createInvoice['success'] == false) {
    //         //     return response()->json([
    //         //         'success' => false,
    //         //         'message' => $createInvoice['message'],
    //         //         'createInvoice' => $createInvoice,
    //         //     ]);
    //         // }
    //     }
    //     // var_dump($createInvoice);
    //     // var_dump($createInvoice->getBody()->getContents());
    //     // exit;

    //     // dd(session()->get('user_data')['user_id']);
    //     DB::beginTransaction();
    //     try {
    //         $user_id = session()->get('user_data')['user_id'];
    //         $wajibpajak = WajibPajakModel::create([
    //             'wajibpajak_npwp' => $npwp,
    //             'wajibpajak_nik' => $nik,
    //             'wajibpajak_name' => $request->input('wajibpajak_name'),
    //             'wajibpajak_type' => $request->input('wajibpajak_type'),
    //             'ms_klu_id' => $request->input('ms_klu_id'),
    //             'wajibpajak_address' => $request->input('wajibpajak_address'),
    //             'wajibpajak_postal_code' => $request->input('wajibpajak_postal_code'),
    //             'wajibpajak_email' => $request->input('wajibpajak_email'),
    //             'wajibpajak_phone' => $phone,
    //             'wajibpajak_active' => ($subscription->subscription_type !== 'FREE') ? 0 : 1,
    //             'ms_regency_id' => $request->input('wajibpajak_city'),
    //             'ms_user_id' => $user_id,
    //         ]);

    //         $wajibpajak_anggota_tim = $request->input('wajibpajak_anggota_tim') ? $request->input('wajibpajak_anggota_tim') : [];
    //         if(!$wajibpajak_anggota_tim) {
    //             // $wajibpajak_anggota_tim
    //             // set current user
    //             array_push($wajibpajak_anggota_tim, $user_id);
    //         }

    //         $temp_tim_data = [];
    //         foreach($wajibpajak_anggota_tim as $tim) {
    //             array_push(
    //                 $temp_tim_data,
    //                 [
    //                     'ms_user_id' => $tim,
    //                     'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id
    //                 ]
    //             );
    //         }
    //         // WajibPajakUserModel::insert($temp_tim_data);

    //         // // insert pengaturan bpjs
    //         // $save_data_pengaturan_bpjs = [
    //         //     [
    //         //         'st_sttunjangankaryawan_id' => 0,
    //         //         'stbpjskaryawan_name' => 'GAJI_POKOK',
    //         //         'stbpjskaryawan_value' => 0,
    //         //         'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //         //         'stbpjskaryawan_type' => 'KESEHATAN',
    //         //         'stbpjskaryawan_active' => 0,
    //         //     ],
    //         //     [
    //         //         'st_sttunjangankaryawan_id' => 0,
    //         //         'stbpjskaryawan_name' => 'GAJI_POKOK',
    //         //         'stbpjskaryawan_value' => 0,
    //         //         'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //         //         'stbpjskaryawan_type' => 'TENAGA_KERJA',
    //         //         'stbpjskaryawan_active' => 0,
    //         //     ],
    //         //     [
    //         //         'st_sttunjangankaryawan_id' => 0,
    //         //         'stbpjskaryawan_name' => 'LAINNYA',
    //         //         'stbpjskaryawan_value' => 0,
    //         //         'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //         //         'stbpjskaryawan_type' => 'KESEHATAN',
    //         //         'stbpjskaryawan_active' => 0,
    //         //     ],
    //         //     [
    //         //         'st_sttunjangankaryawan_id' => 0,
    //         //         'stbpjskaryawan_name' => 'LAINNYA',
    //         //         'stbpjskaryawan_value' => 0,
    //         //         'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //         //         'stbpjskaryawan_type' => 'TENAGA_KERJA',
    //         //         'stbpjskaryawan_active' => 0,
    //         //     ]
    //         // ];

    //         // // insert setting bpjs
    //         // $stbpjskaryawan = new SettingBpjsKaryawanModel();
    //         // $stbpjskaryawan->insert($save_data_pengaturan_bpjs);

    //         // $xendit_url = null;
    //         // $xendit_data = null;
    //         $paper_url =  null;
    //         $paper_data =  null;
    //         $status = 'PAID';
    //         if($subscription->subscription_type !== 'FREE') {
    //             // $xendit_url = $createInvoice['invoice_url'];
    //             // $xendit_data = json_encode($createInvoice);
    //             // $paper_url = 'https://'.$createInvoice['data']->data->payper_url;
    //             // $paper_data = json_encode($createInvoice['data']);
    //             $status = 'PENDING';
    //         }
    //         // insert user order
    //         $order = UserOrderModel::create([
    //             'ms_user_id' => $user_id,
    //             'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //             'ms_subscription_id' => $subscription->subscription_id,
    //             'userorder_no' => $noorder,
    //             'userorder_qty' => 1,
    //             'userorder_price' => $price,
    //             'userorder_discount' => $diskon_price,
    //             'userorder_total' => $total,
    //             'userorder_subscriptiontype' => $subscription->subscription_type,
    //             'userorder_paymentperiode' => $periode_pembayaran,
    //             'userorder_expired_at' => $expired_at,
    //             'userorder_description' => $description,
    //             'userorder_xenditurl' => $xendit_url,
    //             'userorder_xenditdata' => $xendit_data,
    //             // 'userorder_paperurl' => $paper_url,
    //             // 'userorder_paperdata' => $paper_data,
    //             'userorder_subscriptiondata' => json_encode($subscription),
    //             'userorder_status' => $status
    //         ]);

    //         if($subscription->subscription_type === 'FREE') {
    //             // Wajib Pajak Subscription
    //             $wajibpajaksubs = WajibPajakSubscriptionModel::create([
    //                 'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //                 'ms_user_id' => $order->ms_user_id,
    //                 'ms_subscription_id' => $subscription->subscription_id,
    //                 'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
    //                 'wajibpajaksubscription_expired_at' => date('Y-m-d H:i:s'),
    //                 'tr_userorder_id' => $order->userorder_id
    //             ]);

    //             $usergroup = UserGroupModel::create([
    //                 'usergroup_name' => 'Admin',
    //                 'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //             ]);

    //             $usergroupmember = UserGroupMemberModel::create([
    //                 'ms_usergroup_id' => $usergroup->usergroup_id,
    //                 'usergroupmember_members' => $user->user_id,
    //             ]);

    //             $userggroupaccess = UserGroupAccessModel::create([
    //                 'ms_usergroup_id' => $usergroup->usergroup_id,
    //                 'usergroupaccess_permissions' => implode(',',$grouppermissions)
    //             ]);

    //             // insert mr user wajib pajak
    //             MrUserWajibPajakModel::create([
    //                 'ms_user_id' => $user->user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //                 'userwajibpajak_name' => $wajibpajak->wajibpajak_name,
    //                 'userwajibpajak_phone' => $wajibpajak->wajibpajak_phone,
    //             ]);

    //             // Subscription Permission
    //             // $subscriptionpermission = [];
    //             // foreach($subscription->subscriptionpermission as $sp) {
    //             //     array_push($subscriptionpermission, [
    //             //         'tr_usersubscription_id' => $usersubscription->usersubscription_id,
    //             //         'ms_permission_code' => $sp->ms_permission_code,
    //             //         'usersubscriptionpermission_value' => $sp->subscriptionpermission_value,
    //             //     ]);
    //             // }
    //             // UserSubscriptionPermissionModel::insert($subscriptionpermission);
    //         // }

    //         // // notif if free
    //         // if($subscription->subscription_type == 'FREE') {
    //             // insert tr_notification
    //             NotificationModel::insert([
    //                 [
    //                     'notification_title' => 'Selamat Datang Di REKKAA!',
    //                     'notification_type' => 'EMAIL',
    //                     'notification_from' => env("MAIL_FROM_ADDRESS"),
    //                     'notification_to' => session()->get('user_data')['user_email'],
    //                     'ms_user_id' => $user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //                     'notification_view' => 'email.email-berlangganan-free',
    //                     'notification_data' => json_encode([
    //                         'subscription' => [
    //                             'title' => $subscription->subscription_title,
    //                             'qty' => $order->userorder_qty,
    //                             'price' => $order->userorder_price,
    //                             'discount' => $order->userorder_discount,
    //                             'total' => $order->userorder_total,
    //                             'periode' => $order->userorder_paymentperiode,
    //                             'activated_at' => $order->userorder_created_at,
    //                             'expired_at' => $order->userorder_expired_at,
    //                             'renewed_at' => null,
    //                         ],
    //                     ])
    //                 ]
    //             ]);
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Wajib pajak berhasil disimpan',
    //             'data' => [
    //                 'invoice_url' => $xendit_url
    //             ]
    //         ]);


    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // public function update(Request $request)
    // {
    //     $this->validate($request, [
    //         'wajibpajak_id' => 'required',
    //         'wajibpajak_name' => 'required',
    //         'wajibpajak_email' => 'required|email',
    //         'wajibpajak_phone' => 'required',
    //         'wajibpajak_city' => 'required|integer',
    //         'wajibpajak_address' => 'required',
    //         'ms_klu_id' => 'required|integer',
    //         'wajibpajak_postal_code' => 'required',
    //     ], [
    //         'wajibpajak_id.required' => 'ID Wajib Pajak wajib diisi!',
    //         'wajibpajak_name.required' => 'Nama wajib diisi!',
    //         'ms_klu_id.required' => 'KLU wajib diisi!',
    //         'wajibpajak_address.required' => 'Alamat wajib diisi!',
    //         'wajibpajak_postal_code.required' => 'Alamat wajib diisi!',
    //         'wajibpajak_phone.required' => 'No. Telepon wajib sesuai!',
    //         'wajibpajak_city.required' => 'Kota wajib sesuai!',
    //         'wajibpajak_email.required' => 'Email wajib diisi!',
    //         'wajibpajak_email.email' => 'Format email tidak sesuai!',
    //     ]);

    //     // find wajib pajak
    //     $wajibpajak = WajibPajakModel::where([
    //         'ms_wajib_pajak.ms_user_id' => session()->get('user_data')['user_id'],
    //         'wajibpajak_id' => $request->input('wajibpajak_id')
    //     ]);
    //     if(!$wajibpajak) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Wajib pajak tidak ditemukan!'
    //         ]);
    //     }
    //     DB::beginTransaction();
    //     try {
    //         $wajibpajak->update([
    //             'wajibpajak_email' => $request->input('wajibpajak_email'),
    //             'wajibpajak_name' => $request->input('wajibpajak_name'),
    //             'ms_klu_id' => $request->input('ms_klu_id'),
    //             'wajibpajak_address' => $request->input('wajibpajak_address'),
    //             'wajibpajak_postal_code' => $request->input('wajibpajak_postal_code'),
    //             'wajibpajak_phone' => $request->input('wajibpajak_phone'),
    //             'ms_regency_id' => $request->input('wajibpajak_city'),
    //         ]);

    //         // $wajibpajak_anggota_tim = $request->input('wajibpajak_anggota_tim') ? $request->input('wajibpajak_anggota_tim') : [];
    //         // if(!$wajibpajak_anggota_tim) {
    //         //     // $wajibpajak_anggota_tim
    //         //     // set current user
    //         //     array_push($wajibpajak_anggota_tim, session()->get('user_data')['user_id']);
    //         // }

    //         // $temp_tim_data = [];
    //         // foreach($wajibpajak_anggota_tim as $tim) {
    //         //     array_push(
    //         //         $temp_tim_data,
    //         //         [
    //         //             'ms_user_id' => $tim,
    //         //             'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id
    //         //         ]
    //         //     );
    //         // }
    //         // WajibPajakUserModel::insert($temp_tim_data);

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Wajib pajak berhasil dirubah',
    //         ]);


    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // function bayartagihan($wajibpajakId, Request $request)
    // {
    //     // dd($request->all());
    //     $user_id = session()->get('user_data')['user_id'];
    //     $order = UserOrderModel::with('wajibpajak')
    //     ->where([
    //         'ms_wajibpajak_id' => $wajibpajakId,
    //         'ms_user_id' => $user_id,
    //         // 'userorder_status' => 'PENDING',
    //     ])
    //     // ->where('userorder_expired_at', '>', date('Y-m-d H:i:s'))
    //     ->orderBy('userorder_created_at', 'DESC')
    //     ->first();

    //     if(!$order) {
    //         abort(404);
    //     }

    //     $xendit_url = $order->userorder_xenditurl;
    //     // dd($xendit_url);
    //     if($order->userorder_status == 'EXPIRED') {
    //         DB::beginTransaction();
    //         try {
    //             $noorder = 'ORDER-'.date('YmdHis').rand();
    //             $expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +".env('XENDIT_EXPIRED_INVOICE')." day"));

    //             if($order->userorder_paymentperiode == 'MONTHLY') {
    //                 $periode_pembayaran_label = '1 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 1 month'));
    //             } else if($order->userorder_paymentperiode == 'QUARTELY') {
    //                 $periode_pembayaran_label = '4 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 4 month'));
    //             } else if($order->userorder_paymentperiode == 'SEMI_ANNUAL') {
    //                 $periode_pembayaran_label = '6 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 6 month'));
    //             } else if($order->userorder_paymentperiode == 'YEARLY') {
    //                 $periode_pembayaran_label = '12 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 12 month'));
    //             }
    //             $subscription = json_decode($order->userorder_subscriptiondata);
    //             $params = [
    //                 'external_id' => $noorder,
    //                 'amount' => $order->userorder_total,
    //                 'description' => $order->userorder_description,
    //                 'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
    //                 'name' => $order->wajibpajak->wajibpajak_name,
    //                 'surname' => $order->wajibpajak->wajibpajak_name,
    //                 'email' => $order->wajibpajak->wajibpajak_email,
    //                 'phone' => $order->wajibpajak->wajibpajak_phone,
    //                 'city' => $subscription->regency_name,
    //                 'country' => 'Indonesia',
    //                 'postal_code' => $order->wajibpajak->wajibpajak_postal_code,
    //                 'city' => $subscription->regency_name,
    //                 'address' => $order->wajibpajak->wajibpajak_address,
    //                 'items' => [
    //                     [
    //                         'name' => $order->userorder_description,
    //                         'quantity' => 1,
    //                         'price' => $order->userorder_price,
    //                         'category' => $periode_pembayaran_label,
    //                         'url' => '#'
    //                     ]
    //                 ],
    //                 'fees' => [
    //                     [
    //                         'type' => 'DISKON',
    //                         'value' => intval($order->userorder_discount)
    //                     ]
    //                 ]
    //             ];
    //             // dd($params);
    //             $createInvoice = xendit_generate_invoice($params);
    //             $xendit_url = $createInvoice['invoice_url'];
    //             $xendit_data = json_encode($createInvoice);
    //             // create new
    //             $order = UserOrderModel::create([
    //                 'ms_user_id' => $user_id,
    //                 'ms_wajibpajak_id' => $order->wajibpajak->wajibpajak_id,
    //                 'ms_subscription_id' => $order->subscription->subscription_id,
    //                 'userorder_no' => $noorder,
    //                 'userorder_qty' => 1,
    //                 'userorder_price' => $order->userorder_price,
    //                 'userorder_discount' => $order->userorder_discount,
    //                 'userorder_total' => $order->userorder_total,
    //                 'userorder_subscriptiontype' => $order->userorder_subscriptiontype,
    //                 'userorder_paymentperiode' => $order->userorder_paymentperiode,
    //                 'userorder_expired_at' => $expired_at,
    //                 'userorder_description' => $order->userorder_description,
    //                 'userorder_xenditurl' => $xendit_url,
    //                 'userorder_xenditdata' => $xendit_data,
    //                 'userorder_subscriptiondata' => $order->userorder_subscriptiondata,
    //                 'userorder_status' => 'PENDING'
    //             ]);
    //             DB::commit();
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Bayar tagihan',
    //                 'data' => [
    //                     'invoice_url' => $xendit_url
    //                 ]
    //             ]);
    //         } catch(Error $e) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $e->getMessage()
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Bayar tagihan',
    //             'data' => [
    //                 'invoice_url' => $xendit_url
    //             ]
    //         ]);
    //     }
    // }

    // function lanjutlangganan($wajibpajakId, Request $request)
    // {
    //     // dd($request->all());
    //     // $expired_notif_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('SUBSCRIPTION_NOTIF_BEFORE_EXPIRED').' day'));
    //     // dd($expired_at);
    //     $user_id = session()->get('user_data')['user_id'];
    //     
    //     // dd($usersubscription);

    //     // $usersubscription = $akunnpwp->usersubscription;

    //     // return response()->json([
    //     //     'success' => false,
    //     //     'message' => 'Dalam tahap pengembangan!',
    //     // ]);
    //     // dd($usersubscription->userorder);
    //     if(!$usersubscription) {
    //         abort(404);
    //     }
    //     if(!$usersubscription->userorder) {
    //         abort(404);
    //     }
    //     // dd($usersubscription);

    //     DB::beginTransaction();
    //     try {
    //         $xendit_url = null;
    //         // dd($usersubscription->usersubscription_subscriptiontype);
    //         if($usersubscription->usersubscription_subscriptiontype == 'FREE') {
    //             $subscription = SubscriptionModel::select('ms_subscription.*')
    //                 ->where([
    //                     'subscription_id' => $usersubscription->ms_subcription_id, // GRATIS
    //                     'subscription_active' => 1
    //                 ])
    //                 ->with(['subscriptionpermission'])->first();
    //             // User Subscription
    //             $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 60 month'));
    //             $usersubscription->update([
    //                 'usersubscription_expired_at' => $subscription_expired_at,
    //             ]);

    //             // // insert tr_notification
    //             // NotificationModel::insert([
    //             //     [
    //             //         'notification_title' => 'Selamat Datang Di REKKAA!',
    //             //         'notification_type' => 'EMAIL',
    //             //         'notification_from' => env("MAIL_FROM_ADDRESS"),
    //             //         'notification_to' => $request->input('user_email'),
    //             //         'ms_user_id' => $user_id,
    //             //         'notification_view' => 'email.email-berlangganan-free',
    //             //         'notification_data' => json_encode([
    //             //             'subscription' => [
    //             //                 'title' => $subscription->subscription_title,
    //             //                 'wajibpajak_name' => $usersubscription->wajibpajak->wajibpajak_name,
    //             //                 'qty' => $usersubscription->usersubscription_qty,
    //             //                 'price' => $usersubscription->usersubscription_price,
    //             //                 'discount' => $usersubscription->usersubscription_discount,
    //             //                 'total' => $usersubscription->usersubscription_total,
    //             //                 'periode' => $usersubscription->usersubscription_paymentperiode,
    //             //                 'activated_at' => $usersubscription->usersubscription_activated_at,
    //             //                 'expired_at' => $usersubscription->usersubscription_expired_at,
    //             //                 'renewed_at' => $usersubscription->usersubscription_renewed_at,
    //             //             ],
    //             //         ])
    //             //     ]
    //             // ]);
    //         } else {
    //             $noorder = 'ORDER-'.date('YmdHis').rand();
    //             $expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +".env('XENDIT_EXPIRED_INVOICE')." day"));

    //             if($usersubscription->userorder->userorder_paymentperiode == 'MONTHLY') {
    //                 $periode_pembayaran_label = '1 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 1 month'));
    //             } else if($usersubscription->userorder->userorder_paymentperiode == 'QUARTELY') {
    //                 $periode_pembayaran_label = '4 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 4 month'));
    //             } else if($usersubscription->userorder->userorder_paymentperiode == 'SEMI_ANNUAL') {
    //                 $periode_pembayaran_label = '6 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 6 month'));
    //             } else if($usersubscription->userorder->userorder_paymentperiode == 'YEARLY') {
    //                 $periode_pembayaran_label = '12 Bulan Sekali';
    //                 // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 12 month'));
    //             }
    //             $subscription = json_decode($usersubscription->userorder->userorder_subscriptiondata);
    //             $params = [
    //                 'external_id' => $noorder,
    //                 'amount' => $usersubscription->userorder->userorder_total,
    //                 'description' => $usersubscription->userorder->userorder_description.' (Perpanjang)',
    //                 'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
    //                 'name' => $usersubscription->user_name,
    //                 'surname' => $usersubscription->user_name,
    //                 'email' => $usersubscription->user_email,
    //                 'phone' => $usersubscription->user_phone,
    //                 'city' => $subscription->regency_name,
    //                 'country' => 'Indonesia',
    //                 'postal_code' => $usersubscription->userorder->wajibpajak->wajibpajak_postal_code,
    //                 'city' => $subscription->regency_name,
    //                 'address' => $usersubscription->userorder->wajibpajak->wajibpajak_address,
    //                 'items' => [
    //                     [
    //                         'name' => $usersubscription->userorder->userorder_description.' (Perpanjang)',
    //                         'quantity' => 1,
    //                         'price' => $usersubscription->userorder->userorder_price,
    //                         'category' => $periode_pembayaran_label,
    //                         'url' => '#'
    //                     ]
    //                 ],
    //                 'fees' => [
    //                     [
    //                         'type' => 'DISKON',
    //                         'value' => intval($usersubscription->userorder->userorder_discount)
    //                     ]
    //                 ]
    //             ];
    //             // dd($params);
    //             $createInvoice = xendit_generate_invoice($params);
    //             $xendit_url = $createInvoice['invoice_url'];
    //             $xendit_data = json_encode($createInvoice);
    //             // create new
    //             $order = UserOrderModel::create([
    //                 'ms_user_id' => $user_id,
    //                 'ms_wajibpajak_id' => $usersubscription->userorder->wajibpajak->wajibpajak_id,
    //                 'ms_subscription_id' => $usersubscription->userorder->subscription->subscription_id,
    //                 'userorder_no' => $noorder,
    //                 'userorder_qty' => 1,
    //                 'userorder_price' => $usersubscription->userorder->userorder_price,
    //                 'userorder_discount' => $usersubscription->userorder->userorder_discount,
    //                 'userorder_total' => $usersubscription->userorder->userorder_total,
    //                 'userorder_subscriptiontype' => $usersubscription->userorder->userorder_subscriptiontype,
    //                 'userorder_paymentperiode' => $usersubscription->userorder->userorder_paymentperiode,
    //                 'userorder_expired_at' => $expired_at,
    //                 'userorder_description' => $usersubscription->userorder->userorder_description,
    //                 'userorder_xenditurl' => $xendit_url,
    //                 'userorder_xenditdata' => $xendit_data,
    //                 'userorder_subscriptiondata' => $usersubscription->userorder->userorder_subscriptiondata,
    //                 'userorder_status' => 'PENDING',
    //                 'userorder_kind' => 'CONTINUE'
    //             ]);
    //         }
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Bayar tagihan',
    //             'data' => [
    //                 'subscription_type' => $usersubscription->usersubscription_subscriptiontype,
    //                 'invoice_url' => $xendit_url
    //             ]
    //         ]);
    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // function upgrade(Request $request)
    // {
    //     // dd($request->all());
    //     $this->validate($request, [
    //         'subscription_id' => 'required',
    //         'wajibpajak_periodepembayaran' => 'required',
    //     ], [
    //         'subscription_id.required' => 'Paket wajib diisi!',
    //         'wajibpajak_periodepembayaran.required' => 'Periode wajib diise!',
    //     ]);
    //     $wajibpajakId = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $wajibpajak_periodepembayaran = $request->input('wajibpajak_periodepembayaran');
    //     $user_id = session()->get('user_data')['user_id'];
    //     $subscription = SubscriptionModel::select('ms_subscription.*', DB::raw("(SELECT regency_name
    //     FROM ms_regencies WHERE regency_id = ".intval($request->input('wajibpajak_city')).") as regency_name")
    //     , DB::raw("
    //     (SELECT row_to_json(ptable)
    //     FROM (
    //         SELECT ms_wajib_pajak.*, user_name, user_email, user_phone
    //         FROM ms_wajib_pajak 
    //         JOIN ms_user ON ms_user.user_id = ms_wajib_pajak.ms_user_id
    //         WHERE wajibpajak_id = ".intval($wajibpajakId)."
    //     )
    //     as ptable) as wajibpajak"))
    //     ->where([
    //         'subscription_id' => $request->input('subscription_id'),
    //         'subscription_active' => 1,
    //     ])
    //     ->whereNotIn('subscription_type', ['FREE'])->first();
    //     if(!$subscription) {
    //         // abort(404);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Subscription tidak ditemukan!'
    //         ]);
    //     }
    //     $wajibpajak = json_decode($subscription->wajibpajak);
    //     // dd($subscription);

    //     DB::beginTransaction();
    //     try {

    //         $diskon = 0;
    //         $diskon_price = 0;
    //         $price = $subscription->subscription_price;
    //         $total = 0;
    //         // $periode_pembayaran = $request->input('wajibpajak_periodepembayaran');
    //         $periode_pembayaran = $wajibpajak_periodepembayaran;
    //         $periode_pembayaran_label = '';
    //         if($periode_pembayaran == 'MONTHLY') {
    //             $diskon = $subscription->subscription_monthlydiscount;
    //             $periode_pembayaran_label = '1 Bulan Sekali';
    //         } else if($periode_pembayaran == 'QUARTELY') {
    //             $diskon = $subscription->subscription_quarterlydiscount;
    //             $periode_pembayaran_label = '4 Bulan Sekali';
    //         } else if($periode_pembayaran == 'SEMI_ANNUAL') {
    //             $diskon = $subscription->subscription_semiannualdiscount;
    //             $periode_pembayaran_label = '6 Bulan Sekali';
    //         } else if($periode_pembayaran == 'YEARLY') {
    //             $diskon = $subscription->subscription_yearlydiscount;
    //             $periode_pembayaran_label = '12 Bulan Sekali';
    //         }
    //         $diskon_price = $price * $diskon / 100;
    //         $total = $price - intval($diskon_price);

    //         $noorder = 'ORDER-'.date('YmdHis').rand();
    //         $description = 'Rekkaa - Order Subscription '. $subscription->subscription_title. ' for '.$wajibpajak->wajibpajak_name;

    //         $phone = $wajibpajak->user_phone;

    //         // $phone = "+62".substr($phone, -11, -7) . "" . substr($phone, -7, -4) . "" . substr($phone, -4);
    //         $expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +".env('XENDIT_EXPIRED_INVOICE')." day"));

    //         $params = [
    //             'external_id' => $noorder,
    //             'amount' => $total,
    //             'description' => $description.' (Upgrade)',
    //             'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
    //             'name' => $wajibpajak->user_name,
    //             'surname' => $wajibpajak->user_name,
    //             'email' => $wajibpajak->user_email,
    //             'phone' => $phone,
    //             'city' => $subscription->regency_name,
    //             'country' => 'Indonesia',
    //             'postal_code' => $wajibpajak->wajibpajak_postal_code,
    //             'city' => $subscription->regency_name,
    //             'address' => $wajibpajak->wajibpajak_address,
    //             'items' => [
    //                 [
    //                     'name' => $description .' - '.$periode_pembayaran_label.' (Upgrade)',
    //                     'quantity' => 1,
    //                     'price' => $price,
    //                     'category' => $periode_pembayaran_label,
    //                     'url' => '#'
    //                 ]
    //             ],
    //             'fees' => [
    //                 [
    //                     'type' => 'DISKON',
    //                     'value' => ($diskon_price > 0) ? intval('-'.$diskon_price) : $diskon_price
    //                 ]
    //             ]
    //         ];
    //         // dd($params);
    //         $createInvoice = xendit_generate_invoice($params);

    //         $xendit_url = $createInvoice['invoice_url'];
    //         $xendit_data = json_encode($createInvoice);
    //         $status = 'PENDING';

    //         // insert user order
    //         $order = UserOrderModel::create([
    //             'ms_user_id' => $user_id,
    //             'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
    //             'ms_subscription_id' => $subscription->subscription_id,
    //             'userorder_no' => $noorder,
    //             'userorder_qty' => 1,
    //             'userorder_price' => $price,
    //             'userorder_discount' => $diskon_price,
    //             'userorder_total' => $total,
    //             'userorder_subscriptiontype' => $subscription->subscription_type,
    //             'userorder_paymentperiode' => $wajibpajak_periodepembayaran,
    //             'userorder_expired_at' => $expired_at,
    //             'userorder_description' => $description,
    //             'userorder_xenditurl' => $xendit_url,
    //             'userorder_xenditdata' => $xendit_data,
    //             'userorder_subscriptiondata' => json_encode($subscription),
    //             'userorder_status' => $status,
    //             'userorder_kind' => 'UPGRADE',
    //         ]);

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Bayar biaya paket.',
    //             'data' => [
    //                 'invoice_url' => $xendit_url
    //             ]
    //         ]);
    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}
