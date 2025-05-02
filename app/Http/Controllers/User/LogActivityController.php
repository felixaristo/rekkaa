<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\LogActivityModel;
use App\Model\Transaction\PayrollModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogActivityController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Log Aktifitas',
            'content' => 'user.log-aktifitas.index',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function datatable(Request $request)
    {
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $listUser = MrUserWajibPajakModel::where('ms_wajibpajak_id', $wajibpajak_id)->pluck('ms_user_id')->toArray();
        // $listKaryawan = KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)->pluck('karyawan_id')->toArray();

        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

        $order_columns = ['created_at','description'];
        $order_col = $order_columns[0];
        $order_type = 'desc';

        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $startDate = ($request->input('start_date')) ? date('Y-m-d', strtotime($request->input('start_date'))) : date('Y-m-d');
        $endDate = ($request->input('end_date')) ? date('Y-m-d', strtotime($request->input('end_date'))) : date('Y-m-d');
        
        $escapeStartDate = DB::connection()->getPdo()->quote($startDate);
        $escapeEndDate = DB::connection()->getPdo()->quote($endDate);

        $logName = ['karyawan', 'attendance', 'leave', 'holiday', 'bpjs_karyawan', 'tunjangan_karyawan', 'potongan_karyawan', 'penggajian_karyawan', 'setting_pajak_pph21', 'user_group', 'history_import', 'payroll'];

        $list = LogActivityModel::where(function ($query) use ($listUser, $wajibpajak_id) {
            $query->where('ms_wajibpajak_id', $wajibpajak_id)
                ->orWhere('causer_type', 'App\Model\MasterRelation\MrUserWajibPajakModel')
                ->whereIn('causer_id', $listUser);
                // ->orWhere('causer_type', 'App\Model\Master\KaryawanModel')
                // ->whereIn('causer_id', $listKaryawan)
        })
        ->whereIn('log_name', $logName)
        ->whereRaw(DB::raw("created_at BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))
        ->orderBy($order_col, $order_type)
        ->take($limit)
        ->skip($offset)
        ->get();

        $list = $list->map(function ($item) {
            // $user = User::find($item['causer_id']);
            // $email = $user ? $user->email : "Sistem Otomatis";
        
            if ($item['log_name'] == 'karyawan') {
                $checkKaryawan = KaryawanModel::find($item['subject_id']);
                $karyawan_enid = $checkKaryawan->karyawan_enid;
        
                if ($checkKaryawan->karyawan_status == 'NONKARYAWAN') {
                    $item['description'] = str_replace("karyawan", "non karyawan", $item['description']);
                }
        
                if (strpos($item['description'], 'melakukan perubahan data karyawan')) {
                    $properties = json_decode($item['properties'], true);
        
                    if (isset($properties['old']['karyawan_active']) && $properties['old']['karyawan_active'] == "1" &&
                        isset($properties['attributes']['karyawan_active']) && $properties['attributes']['karyawan_active'] == "0") {
        
                        $newDescription = str_replace("melakukan perubahan data", "menonaktifkan", $item['description']);
        
                        $item['description'] = $newDescription;
                    }
                }
        
                $item['description'] = $item['description'] . $karyawan_enid;
            } else if ($item['log_name'] == 'history_import') {
                $checkImport = HistoryImportModel::find($item['subject_id']);

                if($checkImport->historyimport_type == 'ATTENDANCE') {
                    $additional = 'absensi dengan nama file ' . $checkImport->historyimport_file_name;
                } else if($checkImport->historyimport_type == 'LEAVE') {
                    $additional = 'cuti dengan nama file ' . $checkImport->historyimport_file_name;
                } else if($checkImport->historyimport_type == 'EMPLOYEE') {
                    $additional = 'karyawan dengan nama file ' . $checkImport->historyimport_file_name;
                } else if($checkImport->historyimport_type == 'NONEMPLOYEE') {
                    $additional = 'non karyawan dengan nama file ' . $checkImport->historyimport_file_name;
                } else if($checkImport->historyimport_type == 'PAYROLL_NONEMPLOYEE') {
                    $additional = 'penggajian non karyawan dengan nama file ' . $checkImport->historyimport_file_name;
                }

                $item['description'] = $item['description'] . $additional;
            } else if ($item['log_name'] == 'payroll') {
                $checkPayroll = PayrollModel::find($item['subject_id']);

                if($checkPayroll) {
                    $dateTime = new DateTime($checkPayroll->payroll_period);

                    setlocale(LC_TIME, 'id_ID');

                    // Format the date with the month name in Indonesian
                    $formattedDate = strftime('%B %Y', $dateTime->getTimestamp());

                    if (strpos($item['description'], 'melakukan perubahan data penggajian')) {
                        $properties = json_decode($item['properties'], true);

                        if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "1" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "2") {
                                $description = 'melakukan konfirmasi untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                        else if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "5" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "2") {
                                $description = 'melakukan konfirmasi untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                        else if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "2" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "3") {
                                $description = 'melakukan pembayaran untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                        else if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "0" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "1") {
                                $description = 'melakukan kalkulasi untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                        else if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "0" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "5") {
                                $description = 'melakukan kalkulasi ulang untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                        else if (isset($properties['old']['payroll_status']) && $properties['old']['payroll_status'] == "1" &&
                            isset($properties['attributes']['payroll_status']) && $properties['attributes']['payroll_status'] == "5") {
                                $description = 'melakukan kalkulasi ulang untuk penggajian periode ' . $formattedDate;
                                $newDescription = str_replace("melakukan perubahan data penggajian", $description, $item['description']);
            
                                $item['description'] = $newDescription;
                        }
                    } else {
                        $item['description'] = $item['description'] . 'periode ' . $formattedDate;
                    }
                }
            } else {
                $item['description'] = $item['description'] . $item['subject_id'];
            }
        
            return $item;
        });

        $totalData = LogActivityModel::where(function ($query) use ($listUser, $wajibpajak_id) {
            $query->where('ms_wajibpajak_id', $wajibpajak_id)
                ->orWhere('causer_type', 'App\Model\MasterRelation\MrUserWajibPajakModel')
                ->whereIn('causer_id', $listUser);
                // ->orWhere('causer_type', 'App\Model\Master\KaryawanModel')
                // ->whereIn('causer_id', $listKaryawan)
        })
        ->whereIn('log_name', $logName)
        ->whereRaw(DB::raw("created_at BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))->count();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat log activity.',
            'data' => $list,
            'draw' => $request->input('draw'),
            'recordsFiltered' => $totalData,
            'recordsTotal' => $totalData
        ], 200);
    }
}