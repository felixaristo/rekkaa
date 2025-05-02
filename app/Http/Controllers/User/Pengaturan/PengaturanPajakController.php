<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Libraries\AppUploadLibrary;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanPajakController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stpajakpph21 = SettingPajakPph21Model::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();

        $data = [
            'title' => 'Pengaturan Pajak',
            'content' => 'user.pengaturan.pajak.index',
            'stpajakpph21' => $stpajakpph21,
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }
    
}