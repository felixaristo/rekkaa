<?php
namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\MenuModel;
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

class SubscriptionMenuController extends Controller
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
            'title' => 'Menu',
            'content' => 'user.dashboard.menu',
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
            'ms_menu.menu_active' => '1',
        ];
        $list = MenuModel::select('ms_menu.*', 'parent.menu_id as parent_id', 'parent.menu_title as parent_title')
        ->join('ms_menu as parent', 'parent.menu_id', 'ms_menu.menu_parent')
        ->where($where)
        ->orderBy('ms_menu.menu_parent', 'ASC')
        ->orderBy('ms_menu.menu_position', 'ASC')
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = MenuModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
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
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = MenuModel::select('menu_id', 'menu_title')
        ->when($q, function ($query, $q) {
            return $query->where('menu_title', 'ilike', '%'.$q.'%');
        })
        ->where([
            'menu_active' => 1,
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $menu_title = $request->input('menu_title');
        $menu_parent = $request->input('menu_parent');
        $menu_position = $request->input('menu_position');
        $menu_link = $request->input('menu_link');
        $menu_icon = $request->input('menu_icon');
        $menu_description = $request->input('menu_description');
        $menu_active = $request->input('menu_active');
        $menu_visible = $request->input('menu_visible');
        $permissions = $request->input('permissions');

        // dd($groups);
        $rules = [
            'menu_title' => 'required',
            'menu_parent' => 'required|integer',
            'menu_position' => 'required',
            'menu_active' => 'required|in:0,1',
        ];

        $this->validate($request, $rules, [
            'menu_title.required' => 'Deskripsi Penggajian wajib diisi!',
            'menu_parent.required' => 'PIC Penggajian wajib diisi!',
            'menu_position.required' => 'Lokasi Penggajian wajib diisi!',
            'menu_active.required' => 'Status wajib diisi!',
            'menu_parent.integer' => 'Parent Menu berupa angka!',
            'menu_position.integer' => 'Position Menu berupa angka!',
        ]);

        $menuparent = MenuModel::where(['menu_id' => $menu_parent])->first();
        if(!$menuparent) {
            return response()->json([
                'success' => false,
                'message' => 'Menu parent tidak ditemukan!'
            ]);
        }

        $save_data = [
            'menu_code' => '-',
            'menu_group' => $menuparent->menu_group,
            'menu_title' => $menu_title,
            'menu_parent' => $menu_parent,
            'menu_position' => $menu_position,
            'menu_link' => $menu_link,
            'menu_icon' => $menu_icon,
            'menu_description' => $menu_description,
            'menu_visible' => 1,
            'menu_active' => $menu_active,
        ];

        DB::beginTransaction();
        try {
            $menu = MenuModel::create($save_data);

            // activate permission if not exist
            if($permissions) {
                $tempdata_permission = [];
                foreach($permissions as $npermit) {
                    array_push($tempdata_permission, [
                        'permission_group' => 'xxxx',
                        'permission_group_name' => 'xxxx',
                        'permission_code' => $npermit,
                        'permission_code_name' => $npermit,
                        'permission_value' => 1,
                        'ms_menu_id' => $menu->menu_id,
                    ]);
                }
                PermissionModel::insert($tempdata_permission);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil disimpan',
                'data' => $menu
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
     * Update the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($subscriptionId, Request $request)
    {
        $menu_id = $request->input('menu_id');
        $menu_title = $request->input('menu_title');
        $menu_parent = $request->input('menu_parent');
        $menu_position = $request->input('menu_position');
        $menu_link = $request->input('menu_link');
        $menu_icon = $request->input('menu_icon');
        $menu_description = $request->input('menu_description');
        $menu_active = $request->input('menu_active');
        $menu_visible = $request->input('menu_visible');
        $permissions = $request->input('permissions');

        // dd($permissions);
        $rules = [
            'menu_title' => 'required',
            'menu_parent' => 'required|integer',
            'menu_position' => 'required',
            'menu_active' => 'required|in:0,1',
        ];

        $this->validate($request, $rules, [
            'menu_title.required' => 'Deskripsi Penggajian wajib diisi!',
            'menu_parent.required' => 'PIC Penggajian wajib diisi!',
            'menu_position.required' => 'Lokasi Penggajian wajib diisi!',
            'menu_active.required' => 'Status wajib diisi!',
            'menu_parent.integer' => 'Parent Menu berupa angka!',
            'menu_position.integer' => 'Position Menu berupa angka!',
        ]);

        $menuparent = MenuModel::where(['menu_id' => $menu_parent])->first();
        if(!$menuparent) {
            return response()->json([
                'success' => false,
                'message' => 'Menu parent tidak ditemukan!'
            ]);
        }

        $menu = MenuModel::where(['menu_id' => $menu_id])->with(['permissions'])->first();
        if(!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan!'
            ]);
        }

        // dd($permissions, $menu->permissions);
        $inactive_permission = [];
        $exist_permission = [];
        $nonexist_permission = [];
        if(count($menu->permissions) > 0) {
            foreach($menu->permissions as $permit) {
                if(!in_array($permit->permission_code, $permissions)) {
                    array_push($inactive_permission, $permit->permission_code);
                } else {
                    array_push($exist_permission, $permit->permission_code);
                }
            }
        }
        $nonexist_permission = array_diff($permissions, $exist_permission);
        // dd($nonexist_permission);

        $save_data = [
            'menu_code' => '-',
            'menu_group' => $menuparent->menu_group,
            'menu_title' => $menu_title,
            'menu_parent' => $menu_parent,
            'menu_position' => $menu_position,
            'menu_link' => $menu_link,
            'menu_icon' => $menu_icon,
            'menu_description' => $menu_description,
            'menu_active' => $menu_active,
            // 'menu_visible' => 1,
        ];

        DB::beginTransaction();
        try {
            $menu->update($save_data);

            // deactivate permission if not exist
            if(count($inactive_permission) > 0) {
                PermissionModel::where(['ms_menu_id' => $menu_id])
                ->whereIn('permission_code', $inactive_permission)->update(['permission_active' => '0']);
            }
            // activate permission if exist
            if($exist_permission) {
                PermissionModel::where(['ms_menu_id' => $menu_id])
                    ->whereIn('permission_code', $exist_permission)->update(['permission_active' => '1']);
            }
            // activate permission if not exist
            if($nonexist_permission) {
                $tempdata_permission = [];
                foreach($nonexist_permission as $npermit) {
                    array_push($tempdata_permission, [
                        'permission_group' => 'xxxx',
                        'permission_group_name' => 'xxxx',
                        'permission_code' => $npermit,
                        'permission_code_name' => $npermit,
                        'permission_value' => 1,
                        'ms_menu_id' => $menu_id,
                    ]);
                }
                PermissionModel::insert($tempdata_permission);
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dirubah',
                'data' => $menu
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($menuId, Request $request)
    {
        $menu = MenuModel::where([
            'menu_id' => $menuId, 
            'menu_active' => 1,
        ])->first();
        if(!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan!'
            ]); 
        }
        
        DB::beginTransaction();
        try {
            $menu->update(['menu_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus',
                'data' => $menu
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