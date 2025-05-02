<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\XenditCallback\XenditCallbackController;
use App\Http\Controllers\Controller;
use App\Model\Master\PermissionModel;
use App\Model\Master\RegencyModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\UserOrderModel;
use App\User;
use Error;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'password' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['required', 'string', 'min:10', 'max:16'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    function index(Request $request)
    {
        if(env('APP_ENV') == 'production') {
            return redirect('/login');
        }
        $subscription_id = intval($request->input('subscription_id'));
        $data = [
            'title' => 'Rekkaa - Halaman Registrasi'
        ];
        // dd($subscription_id);
        $subscription = SubscriptionModel::with(['subscriptionpermission'])->where(['subscription_id' => $subscription_id, 'subscription_active' => '1'])->first();
        if(!$subscription) {
            return abort(404);
        }
        if(count($subscription->subscriptionpermission) < 1) {
            return abort(404);
        }
        // filter the addon according the subscription
        $subscriptionpermission = json_decode($subscription->subscriptionpermission[0]->subscriptionpermission_permissions);
        $menu_ids = [];
        $permits = [];
        foreach($subscriptionpermission as $subpermit) {
            array_push($menu_ids, $subpermit->menu_id);

            $permits[$subpermit->menu_id] = (array) $subpermit;
        }
        // dd($permits);

        $temppermission = PermissionModel::where(['permission_visibility' => 'PRICING_PAGE', 'permission_type' => 'PLATFORM', 'permission_active' => 1])
        ->whereIn('permission_entity_type', [$subscription->subscription_entity_type, 'SEMUA'])
        ->whereIn('ms_menu_id', $menu_ids)
        ->whereIn('permission_id', [5, 31])
        ->orderBy('permission_id', 'ASC')
        ->get();

        $permission = [];
        foreach($temppermission as $temppermit) {
            if(isset($permits[$temppermit->ms_menu_id])) {
                if(isset($permits[$temppermit->ms_menu_id][$temppermit->permission_code])) {
                    array_push($permission, $temppermit);
                }
            }
        }

        $data['permission'] = $permission;
        $data['subscription'] = $subscription;

        $user_id_continue = session()->get('user_id_continue');
        if($user_id_continue) {
            $data['user_continue'] = User::with(['wajibpajak'])->where(['user_id' => $user_id_continue, 'user_active' => 0])->first(); 
        }
        // dd($subscription);
        return view('auth/register', $data);        
    }

    // for internal
    function indexsinglepage(Request $request)
    {
        $code = $request->get('code');
        // if($code != env('APP_INTERNALCODE')) {
        //     return redirect('/login');
        // }
        $data = [
            'title' => 'Rekkaa - Halaman Registrasi',
            'content' => 'auth.register.register-form-calculator',
        ];
        // dd($subscription);
        return view('index', $data);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function register(Request $request)
    {
        if(env('APP_ENV') == 'production') {
            return redirect('/login');
        }
        
        $subscription_id = $request->input('subscription_id');
        $user_email = $request->input('user_email');
        $npwp = $request->input('npwp');
        $nik = '0000000000000000';
        $country_id = $request->input('country_id');
        $city_id = $request->input('city_id');
        $validation = [
            'user_email' => 'required|email',
            'name' => 'required',
            'password' => 'required|min:6',
            'phone' => 'required|min:9|max:12',
            'subscription_id' => 'required',
            'country_id' => 'required',
            'city_id' => 'required',
        ];
        $validation_message = [
            'user_email.required' => 'Email wajib diisi!',
            'user_email.email' => 'Format email tidak sesuai!',
            'user_email.unique' => 'Email sudah digunakan!',
            'npwp.required' => 'NPWP wajib diisi!',
            'npwp.unique' => 'NPWP sudah digunakan',
            'nik.required' => 'NIK wajib diisi!',
            'nik.unique' => 'NIK sudah digunakan',
            'name.required' => 'Nama wajib diisi!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 6 karakter!',
            'phone.required' => 'No. Hp wajib diisi!',
            'subscription_id.required' => 'Subscription wajib diisi!',
            'country_id.required' => 'Negara wajib diisi!',
            'city_id.required' => 'Kotas wajib diisi!',
            // 'nik.required' => 'NIK wajib diisi!',
            // 'nik.unique' => 'NIK sudah digunakan!',
            'npwp.min' =>'NPWP harus terdiri dari 15 angka!',
            'npwp.max' =>'NPWP harus terdiri dari 15 angka!',
            'nik.min' => 'NIK harus berisi 16 angka!',
            'nik.max' => 'NIK harus berisi 16 angka!',
        ];
        
        // check if country not indonesia
        $intercity = null;
        if($country_id != 100) {
            $intercity = $city_id;
            $city_id = 0;
        } else {
            $city = RegencyModel::where(['regency_id' => $city_id, 'ms_country_id' => $country_id])->first();
            if(!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kota tidak ditemukan! Silahkan pilih kota yang disediakan.'
                ]);
            }
            $city_id = $city->regency_id;
        }

        // check subscription exist
        $subscription = SubscriptionModel::select('ms_subscription.*')
        ->where([
            'subscription_id' => $subscription_id, // GRATIS
            'subscription_active' => 1
        ])->first();
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan'
            ]);
        }

        $npwp_type = $subscription->subscription_entity_type;

        // check user exist
        $user = User::select('user_id', 'user_email', 'user_active')->where([
            'user_email' => $user_email,
        ])->first();
            
        $where_wajibpajak = [];
        if($npwp_type === 'BADAN') { // BADAN
            $validation['npwp'] = 'required|min:20|max:20';

            $where_wajibpajak['wajibpajak_npwp'] = $npwp;
        } else { // INDIVIDU
            $validation['nik'] = 'required|min:16|max:16';

            $nik = $request->input('nik');
            $where_wajibpajak['wajibpajak_nik'] = $nik;
        }

        // dd($where_wajibpajak);
        $wajibpajak = WajibPajakModel::select('wajibpajak_id', 'ms_user_id', 'wajibpajak_npwp', 'wajibpajak_nik')
        ->with(['user'])->where($where_wajibpajak)->first();
        // dd($wajibpajak);
        
        if(!$user) { // if user not exist
            $validation['user_email'] .= '|unique:ms_user';
            if($npwp_type === 'BADAN') { // BADAN
                $validation['npwp'] = 'required|min:20|max:20|unique:ms_wajib_pajak,wajibpajak_npwp';
            } else { // INDIVIDU
                $validation['nik'] = 'required|min:16|max:16|unique:ms_wajib_pajak,wajibpajak_nik';
            }
        } else { // if the user already registered
            if($user->user_active == '1') { // if the user already active
                $validation['user_email'] = '|unique:ms_user';
                // return response()->json([
                //     'success' => false,
                //     'message' => 'Email sudah terdaftar! Silahkan login ke akun anda.'
                // ]);
            }
        }
        if($wajibpajak) { // if entity already used. Then show the email of the owner.
            if($user) {
                if($wajibpajak->ms_user_id != $user->user_id) { // if entity not belong to user.
                    // set unique validation
                    if($npwp_type === 'BADAN') { // BADAN
                        $validation['npwp'] = 'required|min:20|max:20|unique:ms_wajib_pajak,wajibpajak_npwp';
                    } else { // INDIVIDU
                        $validation['nik'] = 'required|min:16|max:16|unique:ms_wajib_pajak,wajibpajak_nik';
                    }
                }
                
                $validation_message['npwp.unique'] .= ' oleh '.mask_email($wajibpajak->user->user_email);
                $validation_message['nik.unique'] .= ' oleh '.mask_email($wajibpajak->user->user_email);
            }
        }

        if($npwp_type !== 'BADAN') { // if not BADAN
            if($request->input('register_kepemilikan_npwp') == 't') { // set the default npwp if there is no npwp
                $npwp = '00.000.000.0-000.000';
            }
            $nik = $request->input('nik');
        }
        
        $this->validate($request, $validation, $validation_message);
        
        // // check entity exist
        // $where_entity = [];
        
        
        // dd($subscription->subscriptionpermission);

        DB::beginTransaction();
        try {
            if(!$user) { // create 
                $generate_token = Hash::make('VRA'.$request->input('user_email').date('Y-m-dhms'));
                $user = User::create([
                    'user_email' => $request->input('user_email'),
                    'user_password' => Hash::make($request->input('password')),
                    'user_verified_token' => $generate_token,
                    'user_active' => '0', // registration incomplete
                ]);
            } else { // update current user
                // dd($user);
                $user->update([
                    'user_password' => Hash::make($request->input('password')),
                ]);
            }

            // dd($wajibpajak);
            if($wajibpajak) { // update existing
                $wajibpajak->update([
                    'wajibpajak_npwp' => $npwp,
                    'wajibpajak_nik' => $nik,
                    'wajibpajak_name' => $request->input('name'),
                    'wajibpajak_type' => $npwp_type,
                    'wajibpajak_address' => $request->input('address'),
                    'wajibpajak_phone' => $request->input('phone'),
                    'ms_country_id' => $country_id,
                    'ms_regency_id' => $city_id,
                    'wajibpajak_intercity' => $intercity,
                ]);
                // update mr user wajib pajak
                MrUserWajibPajakModel::where([
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                ])->update([
                    // 'userwajibpajak_name' => $wajibpajak->wajibpajak_name,
                    'userwajibpajak_phone' => $wajibpajak->wajibpajak_phone,
                ]);
            } else {
                $wajibpajak = WajibPajakModel::create([
                    'wajibpajak_npwp' => $npwp,
                    'wajibpajak_nik' => $nik,
                    'wajibpajak_name' => $request->input('name'),
                    'wajibpajak_type' => $npwp_type,
                    'wajibpajak_address' => $request->input('address'),
                    'wajibpajak_email' => $user->user_email,
                    'wajibpajak_phone' => $request->input('phone'),
                    'ms_country_id' => $country_id,
                    'ms_regency_id' => $city_id,
                    'wajibpajak_intercity' => $intercity,
                    'ms_user_id' => $user->user_id,
                ]);

                // insert mr user wajib pajak
                MrUserWajibPajakModel::create([
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                    'userwajibpajak_name' => 'Admin',
                    'userwajibpajak_owner' => '1',
                    // 'userwajibpajak_name' => $wajibpajak->wajibpajak_name,
                    'userwajibpajak_phone' => $wajibpajak->wajibpajak_phone,
                ]);
            }
            
            $order = UserOrderModel::where([
                'ms_user_id' => $user->user_id,
                'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                'ms_subscription_id' => $subscription->subscription_id,
                'userorder_status' => 'PENDING',
                'userorder_active' => 1,
            ])->orderBy('userorder_id', 'DESC')->first();

            // create order
            $subscription_period = 1;
            $order_total = $subscription->subscription_price * $subscription_period;
            $description = 'Rekkaa - Order Subscription '. $subscription->subscription_title;
            $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('XENDIT_EXPIRED_INVOICE').'  day'));
            // dd($user->user_id.' '.$wajibpajak->wajibpajak_id);
            if(!$order) { // if order not exist, then create new
                $order = UserOrderModel::create([
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                    'ms_subscription_id' => $subscription->subscription_id,
                    'userorder_qty' => $subscription_period,
                    'userorder_price' => $subscription->subscription_price,
                    'userorder_discount' => 0,
                    'userorder_total' => $order_total,
                    'userorder_subscriptiontype' => $subscription->subscription_type,
                    'userorder_paymentperiode' => $subscription_period,
                    'userorder_expired_at' => $userorder_expired_at,
                    'userorder_description' => $description,
                    'userorder_xenditurl' => null,
                    'userorder_xenditdata' => null,
                    'userorder_subscriptiondata' => '',
                    'userorder_status' => 'PENDING',
                ]);
            } else { // if exist then update.
                $order->update([
                    'ms_subscription_id' => $subscription->subscription_id,
                    'userorder_qty' => $subscription_period,
                    'userorder_price' => $subscription->subscription_price,
                    'userorder_discount' => 0,
                    'userorder_total' => $order_total,
                    'userorder_subscriptiontype' => $subscription->subscription_type,
                    'userorder_paymentperiode' => $subscription_period,
                    'userorder_expired_at' => $userorder_expired_at,
                    'userorder_description' => $description,
                    'userorder_xenditurl' => null,
                    'userorder_xenditdata' => null,
                    'userorder_subscriptiondata' => '',
                    'userorder_status' => 'PENDING',
                ]);
            }

            session()->forget('user_id_continue');

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil.',
                'data' => [
                    // 'user_id' => $user->user_id
                ]
            ]);

        } catch(Error $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function registeraddon(Request $request)
    {
        $subscription_id = $request->input('subscription_id');
        $user_email = $request->input('user_email');
        $discount = $request->input('discount');
        $period = ($discount) ? $discount['period'] : 1;
        $listaddon = $request->input('listaddon');
        $validation = [
            'user_email' => 'required|email',
            'subscription_id' => 'required',
            'subscription_periode' => 'required',
        ];
        $validation_message = [
            'user_email.required' => 'Email wajib diisi!',
            'user_email.email' => 'Format email tidak sesuai!',
            'subscription_id.required' => 'Subscription wajib diisi!',
            'subscription_periode.required' => 'Periode Langganan wajib diisi!',
        ];
        
        $this->validate($request, $validation, $validation_message);

        // check subscription exist
        $subscription = SubscriptionModel::select('ms_subscription.*')
        ->where([
            'subscription_id' => $subscription_id,
            'subscription_active' => 1
        ])->first();
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan'
            ]);
        }
        // check user exist
        $user = User::select('user_id', 'user_email', 'user_active')->where(['user_email' => $user_email])->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        // check entity exist
        // dd($where_entity);
        $wajibpajak = WajibPajakModel::select('ms_user_id','wajibpajak_id', 'wajibpajak_npwp', 'wajibpajak_nik', 'wajibpajak_active')->where([
            'ms_user_id' => $user->user_id
        ])->first();
        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);
        }

        // dd($user->user_id.' '.$wajibpajak->wajibpajak_id);
        $order = UserOrderModel::where([
            'ms_user_id' => $user->user_id,
            'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'ms_subscription_id' => $subscription->subscription_id,
            'userorder_active' => 1,
        ])->first();
        if(!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ]);
        }
        // dd($order);
        // find addon price
        $reallistaddon = [];
        $addon_total = 0;
        if($subscription->subscription_type != 'FREESSS') {
            if($listaddon) {
                $addon_ids = [];
                foreach($listaddon as $addon) {
                    array_push($addon_ids, $addon['id']);
                }
                // dd($addon_ids);
                if($addon_ids) { // get real price add on
                    $permissions = PermissionModel::select('permission_id', 'permission_price', 'permission_code_name')
                    ->whereIn('permission_id', $addon_ids)
                    ->where(['permission_visibility' => 'PRICING_PAGE', 'permission_active' => 1])->get();
                    // dd($permissions);
                    if($permissions) {
                        foreach($permissions as $pm) {
                            $addon_qty = 1;
                            foreach($listaddon as $addon) {
                                if($addon['id'] == $pm->permission_id) {
                                    $addon_qty = $addon['qty'];
                                    break;
                                }
                            }

                            array_push($reallistaddon, [
                                'id' => $pm->permission_id,
                                'qty' => $addon_qty,
                                'price' => $pm->permission_price,
                                'text' => $pm->permission_code_name,
                            ]);

                            $addon_total += $pm->permission_price * $addon_qty;
                        }
                    }
                }
            }
        }
        // var_dump($addon_total);
        // dd($reallistaddon);

        $subscription_discount = ($subscription->subscription_discount) ? json_decode($subscription->subscription_discount) : null;
        // dd($subscription_discount);
        $discount_total = 0;
        if($subscription_discount) {
            foreach($subscription_discount as $sdiscount) {
                if($sdiscount->period == $period) {
                    if($sdiscount->value_type == 'PERSEN') {
                        $discount_total = $subscription->subscription_price * $sdiscount->value / 100;
                    } else {
                        $discount_total = $sdiscount->value;
                    }
                    break;
                }
            }
        }
        // dd($discount_total);
        // $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 1 month'));
        // if($period == 6) {
        //     $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 6 month'));
        // } else if($period == 12) {
        //     $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' 12 month'));
        // }
        $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('XENDIT_EXPIRED_INVOICE').'  day'));
        $order_total = (($subscription->subscription_price  - $discount_total) * $period) + $addon_total;
        
        DB::beginTransaction();
        try {
            $save_data_order = [
                'userorder_qty' => $period,
                'userorder_price' => $subscription->subscription_price,
                'userorder_discount' => $discount_total,
                'userorder_addon' => $addon_total,
                'userorder_total' => $order_total,
                'userorder_subscriptiontype' => $subscription->subscription_type,
                'userorder_paymentperiode' => $period,
                'userorder_expired_at' => $userorder_expired_at,
                'userorder_subscriptiondata' => json_encode([
                    'listaddon' => $reallistaddon
                ])
            ];
            $order->update($save_data_order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tambah Add on berhasil.',
                'data' => [
                    'order' => $save_data_order
                ]
            ]);

        } catch(Error $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function registerconfirmation(Request $request)
    {
        $subscription_id = $request->input('subscription_id');
        $user_email = $request->input('user_email');
        $discount = $request->input('discount');
        // $period = ($discount) ? $discount['period'] : 1;
        // $listaddon = $request->input('listaddon');
        $validation = [
            'user_email' => 'required|email',
            'subscription_id' => 'required',
        ];
        $validation_message = [
            'user_email.required' => 'Email wajib diisi!',
            'user_email.email' => 'Format email tidak sesuai!',
            'subscription_id.required' => 'Subscription wajib diisi!',
        ];
        
        $this->validate($request, $validation, $validation_message);

        // check subscription exist
        $subscription = SubscriptionModel::select('ms_subscription.*')
        ->where([
            'subscription_id' => $subscription_id,
            'subscription_active' => 1
        ])->first();
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan'
            ]);
        }
        // check user exist
        $user = User::select('user_id', 'user_email', 'user_active')->with(['userwajibpajak'])->where(['user_email' => $user_email])->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        // check entity exist
        // dd($where_entity);
        $wajibpajak = WajibPajakModel::
        // select('wajibpajak_id', 'wajibpajak_npwp', 'wajibpajak_nik', 'wajibpajak_name', 'wajibpajak_phone')
        with(['country', 'regency'])
        ->where([
            'ms_user_id' => $user->user_id
        ])->first();
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);
        }

        // dd($wajibpajak->country);
        $order = UserOrderModel::where([
            'ms_user_id' => $user->user_id,
            'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'userorder_active' => 1,
            'ms_subscription_id' => $subscription->subscription_id,
        ])->first();
        if(!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ]);
        }
        // dd($order);
        // $subscription->subscriptionpermission = ($subscription->spermission_json) ? json_decode($subscription->spermission_json) : [];
       // dd($subscription);
       $grouppermissions = [];
       $grouppermissions_manajer = [];
       $grouppermissions_karyawan = [];
    //    $menu_ids = [];
    //    foreach($subscription->subscriptionpermission as $sub) {
    //        $decodepermission = json_decode($sub->subscriptionpermission_permissions);
    //        foreach($decodepermission as $pm) {
    //            array_push($menu_ids, $pm->menu_id);
    //        }
    //    }
    //    if($menu_ids) {
        //    $permissions = PermissionModel::select('permission_id', 'permission_code', 'permission_default_group')->whereIn('ms_menu_id', $menu_ids)->get();
        //    if($permissions) {
        //        foreach($permissions as $permission) {
        //         //    $exp_default_group = ($permission->permission_default_group) ? explode(',', $permission->permission_default_group) : null;
        //            array_push($grouppermissions, $permission->permission_id);

        //         //    if($exp_default_group) {
        //         //        if(in_array('MANAJER', $exp_default_group)) {
        //         //            array_push($grouppermissions_manajer, $permission->permission_id);
        //         //        }
        //         //        if(in_array('KARYAWAN', $exp_default_group)) {
        //         //            array_push($grouppermissions_karyawan, $permission->permission_id);
        //         //        }
        //         //    }
        //        }
        //    }
    //    }
       // dd($grouppermissions_manajer);
       // dd(implode(',',$grouppermissions));
        DB::beginTransaction();
        try {
            $periode_pembayaran_label = $order->userorder_paymentperiode.' Bulan Sekali';
            
            $description = 'Rekkaa - Order Subscription '. $subscription->subscription_title. ' for '.$wajibpajak->wajibpajak_name;

            $phone = $wajibpajak->wajibpajak_phone;

            $phone = "+62".substr($phone, -11, -7) . "" . substr($phone, -7, -4) . "" . substr($phone, -4);
            $xendit_url = '';
            $xendit_data = null;
            $status = 'PENDING';
            if($order->userorder_total > 0) {
                $params = [
                    'external_id' => $order->userorder_no,
                    'amount' => $order->userorder_total,
                    'description' => $description,
                    'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
                    'name' => $wajibpajak->wajibpajak_name,
                    'surname' => $wajibpajak->wajibpajak_name,
                    'email' => $user->user_email,
                    'phone' => $wajibpajak->wajibpajak_phone,
                    'city' => ($wajibpajak->ms_country_id == 100) ? $wajibpajak->regency->regency_name : $wajibpajak->wajibpajak_intercity,
                    'country' => $wajibpajak->country->country_name,
                    'postal_code' => '',
                    'address' => $wajibpajak->wajibpajak_address,
                    'items' => [
                        [
                            'name' => $description .' - '.$periode_pembayaran_label,
                            'quantity' => $order->userorder_qty,
                            'price' => $order->userorder_price,
                            'category' => $periode_pembayaran_label,
                            'url' => '#'
                        ]
                    ],
                    'fees' => [
                        [
                            'type' => 'Add on',
                            'value' => $order->userorder_addon,
                        ],
                        [
                            'type' => 'Diskon',
                            'value' => ($order->userorder_discount > 0) ? '-'.intval($order->userorder_discount * $order->userorder_qty) : 0
                        ]
                    ]
                ];
                // echo json_encode($params);
                // die;
                $createInvoice = xendit_generate_invoice($params);
                $xendit_url = $createInvoice['invoice_url'];
                $xendit_data = json_encode($createInvoice);
            }

            // update user order
            $order->update([
                'userorder_status' => $status,
                'userorder_xenditurl' => $xendit_url,
                'userorder_xenditdata' => $xendit_data,
            ]);

            if($order->userorder_total == 0) {
                $request->request->add([
                    'external_id' => $order->userorder_no,
                    'status' => 'PAID',
                    'paid_amount' => 0,
                    'payment_method' => 'REKKAA_DIRECT',
                    'payment_channel' => 'REKKAA_REGISTER',
                ]);
                $xendictcallback = new XenditCallbackController();
                // dd($request->all());
                $checkcallback = $xendictcallback->callback($request);
                // $decodecallback = ($checkcallback) ? json_decode($checkcallback) : null;
                // dd($checkcallback->getData()->success);
                if(!$checkcallback->getData()->success) {
                    return response()->json([
                        'success' => false,
                        'message' => $checkcallback->getData()->message
                    ]);
                }
                // user
                // $user->update(['user_active' => 1])
                // // Wajib Pajak Subscription
                // $wajibpajaksubs = WajibPajakSubscriptionModel::create([
                //     'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                //     'ms_user_id' => $order->ms_user_id,
                //     'ms_subscription_id' => $subscription->subscription_id,
                //     'wajibpajaksubscription_permission' => $subscription->subscription_permission,
                //     'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
                //     'wajibpajaksubscription_expired_at' => date('Y-m-d H:i:s'),
                //     'tr_userorder_id' => $order->userorder_id
                // ]);

                // // Admin Group
                // $usergroup = UserGroupModel::create([
                //     'usergroup_name' => 'Admin',
                //     'usergroup_code' => 'REKKAA',
                //     'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                // ]);

                // $usergroupmember = UserGroupMemberModel::create([
                //     'ms_usergroup_id' => $usergroup->usergroup_id,
                //     'usergroupmember_members' => $user->user_id,
                // ]);

                // $userggroupaccess = UserGroupAccessModel::create([
                //     'ms_usergroup_id' => $usergroup->usergroup_id,
                //     'usergroupaccess_permissions' => implode(',',$grouppermissions)
                // ]);
                
                // // notif if free
                // NotificationModel::insert([
                //     [
                //         'notification_title' => 'Selamat Datang Di REKKAA!',
                //         'notification_type' => 'EMAIL',
                //         'notification_from' => env("MAIL_FROM_ADDRESS"),
                //         'notification_to' => $user_email,//session()->get('user_data')['user_email'],
                //         'ms_user_id' => $user->user_id,
                //         'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                //         'notification_view' => 'email.email-berlangganan-free',
                //         'notification_data' => json_encode([
                //             'subscription' => [
                //                 'title' => $subscription->subscription_title,
                //                 'qty' => $order->userorder_qty,
                //                 'price' => $order->userorder_price,
                //                 'discount' => $order->userorder_discount,
                //                 'total' => $order->userorder_total,
                //                 'periode' => $order->userorder_paymentperiode,
                //                 'activated_at' => $order->userorder_created_at,
                //                 'expired_at' => $order->userorder_expired_at,
                //                 'renewed_at' => null,
                //             ],
                //         ])
                //     ]
                // ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi Registrasi berhasil',
                'data' => [
                    'invoice_url' => $xendit_url
                ]
            ]);

        } catch(Error $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function registercalculator(Request $request)
    {
        $validation = [
            'user_email' => 'required|email|unique:ms_user',
            'name' => 'required',
            'password' => 'required|min:6',
            'phone' => 'required|min:9|max:12',
            'npwp_type' => 'required|in:BADAN,INDIVIDU',
            'country_id' => 'required',
            'city_id' => 'required',
        ];
        
        $validation_message = [
            'user_email.required' => 'Email wajib diisi!',
            'user_email.email' => 'Format email tidak sesuai!',
            'user_email.unique' => 'Email sudah digunakan!',
            'name.required' => 'Nama wajib diisi!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 6 karakter!',
            'phone.required' => 'No. Hp wajib diisi!',
            'npwp_type.required' => 'Tipe NPWP wajib diisi!',
            'country_id.required' => 'Negara wajib diisi!',
            'city_id.required' => 'Kotas wajib diisi!',
        ];

        $country_id = $request->input('country_id');
        $city_id = $request->input('city_id');
        $npwp_type = $request->input('npwp_type');
        $npwp = $request->input('npwp');
        $nik = '0000000000000000';
        if($npwp_type === 'BADAN' || $request->input('register_kepemilikan_npwp') != 't') {
            $validation['npwp'] = 'required|unique:ms_wajib_pajak,wajibpajak_npwp|min:20|max:20';

            $validation_message['npwp.required'] = 'NPWP wajib diisi!';
            $validation_message['npwp.unique'] = 'NPWP sudah digunakan!';
            $validation_message['npwp.min'] = 'NPWP harus terdiri dari 15 angka!';
            $validation_message['npwp.max'] = 'NPWP harus terdiri dari 15 angka!';
        } 
        
        if($npwp_type === 'INDIVIDU') { // INDIVIDU
            if($request->input('register_kepemilikan_npwp') == 't') {
                $npwp = '00.000.000.0-000.000';
            }

            $validation['nik'] = 'required|unique:ms_wajib_pajak,wajibpajak_nik|min:16|max:16';
            $validation_message['nik.required'] = 'NIK wajib diisi!';
            $validation_message['nik.unique'] = 'NIK sudah digunakan!';
            $validation_message['nik.min'] = 'NIK harus berisi 16 angka!';
            $validation_message['nik.max'] = 'NIK harus berisi 16 angka!';

            $nik = $request->input('nik');
        }

        $this->validate($request, $validation, $validation_message);

        $subscription = SubscriptionModel::select('ms_subscription.*')
        ->where([
            'subscription_id' => ($npwp_type == 'BADAN') ? 1 : 6, // INDIVIDU GRATIS
            'subscription_active' => 1
        ])->first();
        // dd($subscription->subscriptionpermission);
        if(!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription tidak ditemukan'
            ]);
        }

        DB::beginTransaction();
        try {
            $generate_token = Hash::make('VRA'.$request->input('user_email').date('Y-m-dhms'));

            $user = User::create([
                'user_email' => $request->input('user_email'),
                'user_password' => Hash::make($request->input('password')),
                'user_verified_token' => $generate_token,
            ]);
            $wajibpajak_save_data = [
                'wajibpajak_npwp' => $npwp,
                'wajibpajak_nik' => $nik,
                'wajibpajak_name' =>$request->input('name'),
                'wajibpajak_type' => $npwp_type,
                'wajibpajak_address' => $request->input('address'),
                'wajibpajak_email' => $request->input('user_email'),
                'wajibpajak_phone' => $request->input('phone'),
                'ms_country_id' => $country_id,
                'ms_regency_id' => $city_id,
                'wajibpajak_intercity' => null,
                'ms_user_id' => $user->user_id,
            ];
            // dd($wajibpajak_save_data);
            $wajibpajak = WajibPajakModel::create($wajibpajak_save_data);

            MrUserWajibPajakModel::create([
                'ms_user_id' => $user->user_id,
                'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                'userwajibpajak_name' => 'Admin',
                'userwajibpajak_owner' => '1',
                // 'userwajibpajak_name' => $wajibpajak->wajibpajak_name,
                'userwajibpajak_phone' => $wajibpajak->wajibpajak_phone,
            ]);

            // dd($insertwajibpajak);
            $order = UserOrderModel::create([
                'ms_user_id' => $user->user_id,
                'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                'ms_subscription_id' => $subscription->subscription_id, // FREE BADAN
                'userorder_qty' => 12,
                'userorder_price' => 0,
                'userorder_discount' => 0,
                'userorder_total' => 0,
                'userorder_subscriptiontype' => $subscription->subscription_type,
                'userorder_paymentperiode' => 12,
                'userorder_expired_at' => date('Y-m-d H:i:s'),
                'userorder_description' => 'Rekkaa - Order Subscription '.$subscription->subscription_type,
                'userorder_xenditurl' => null,
                'userorder_xenditdata' => null,
                'userorder_subscriptiondata' => '',
                'userorder_status' => 'PENDING',
            ]);
            // dd($insertorder);
            $order = UserOrderModel::where(['userorder_id' => $order->userorder_id])->first();
            // dd($order);
            
            $request->request->add([
                'external_id' => $order->userorder_no,
                'status' => 'PAID',
                'paid_amount' => 0,
                'payment_method' => 'REKKAA_DIRECT',
                'payment_channel' => 'REKKAA_REGISTER',
                // 'source_page' => 'FREE_PAGE',
            ]);
            $xendictcallback = new XenditCallbackController();
            // dd($request->all());
            $checkcallback = $xendictcallback->callback($request);
            
            // $decodecallback = ($checkcallback) ? json_decode($checkcallback) : null;
            // dd($checkcallback->getData());
            if(!$checkcallback->getData()->success) {
                return response()->json([
                    'success' => false,
                    'message' => $checkcallback->getData()->message
                ]);
            }

            // user session
            $session_data = [
                'user_id' => $user->user_id,
                'user_email' => $user->user_email,
                'user_name' => $wajibpajak->wajibpajak_name,
            ];
            session()->put('user_data', $session_data);

            // wajib pajak session
            session()->put('wajibpajak_current', [
                'wajibpajak_id' => $wajibpajak->wajibpajak_id,
                'wajibpajak_name' => $wajibpajak->wajibpajak_name,
                'wajibpajak_npwp' => $wajibpajak->wajibpajak_npwp,
                'wajibpajak_type' => $wajibpajak->wajibpajak_type,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => $session_data
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
