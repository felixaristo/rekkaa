<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = (session()->get('wajibpajak_current')) ? session()->get('wajibpajak_current')['wajibpajak_id'] : null;
        $user = User::with(['wajibpajak', 'wajibpajak.wajibpajaksubscription', 'wajibpajak.wajibpajaksubscription.subscription','userwajibpajak' => function($q) use ($wajibpajak_id) {
            // $where = ['userwajibpajak_owner' => 1];
            $where = ['userwajibpajak_active' => '1'];
            if($wajibpajak_id) {
                $where = ['ms_wajibpajak_id' => $wajibpajak_id];
            }
            $q->where($where);
        }])->where(['user_id' => $user_id])->first();
        // $wajibpajak = $user->wajibpajak;
        $wajibpajak = $user->userwajibpajak->wajibpajak;
        // dd($user->userwajibpajak->wajibpajak);
        if(!$wajibpajak) {
            abort(404);
        }
        // dd($user->userwajibpajak);
        // dd($wajibpajak->wajibpajaksubscription->subscription->subscription_order);
        $isowner = $user->userwajibpajak->userwajibpajak_owner;
        $subscription_order = $wajibpajak->wajibpajaksubscription->subscription->subscription_order;
        $subscriptions = SubscriptionModel::where(['subscription_active' => 1, 'subscription_entity_type' => $wajibpajak->wajibpajak_type])
        ->where('subscription_order', '>', $subscription_order)
        ->orderBy('subscription_order', 'ASC')->get();
        // dd($wajibpajak);
        $data = [
            'title' => 'Profil',
            // 'content' => ($wajibpajak) ? 'user.profil.index' : 'user.profil.index-nonowner',
            'content' => 'user.profil.index',
            'user' => $user,
            'isowner' => $isowner,
            'wajibpajak' => $wajibpajak,
            'subscriptions' => $subscriptions,
        ];
        // dd($wajibpajak->klu);
        // if(!$wajibpajak) {
        //     abort(404);
        // }

        // dd($user->userwajibpajak);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
        // dd('ooioi');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'profil_name' => 'required',
            'profil_hp' => 'required',
        ], [
            'profil_name.required' => 'Nama wajib diisi!',
            'profil_hp.required' => 'No. HP wajib diisi!',
        ]);

        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $name = $request->input('profil_name');
        $phone = $request->input('profil_hp');

        DB::beginTransaction();
        try {
            // update user wajib pajak
            MrUserWajibPajakModel::where([
                'ms_user_id' => $user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                // 'userwajibpajak_owner' => 1,
            ])->update([
                'userwajibpajak_name' => $name,
                'userwajibpajak_phone' => $phone,
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Update profil berhasil',
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