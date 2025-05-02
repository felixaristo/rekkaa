<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\LeaveKaryawanModel;
use App\Model\Transaction\NotificationModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use DateTime;
use DOMDocument;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class CutiController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $data = [
            'title' => 'Daftar Cuti Karyawan',
            'content' => 'user.cuti.cuti-admin',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    // public function leaveAdmin(Request $request)
    // {
    //     // Get the WajibPajakModel data based on the wajibpajak_id
    //     $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
    //     $page = ($request->input('page')) ? intval($request->input('page')) : 1;
    //     $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');
    //     $status = $request->input('status');

    //     // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
    //     if (!$wajibPajak) {
    //         return response()->json(['message' => 'Data not found'], 404);
    //     }

    //     // Retrieve all related LeaveKaryawanModel data for the given WajibPajakModel with pagination
    //     $query = $wajibPajak->karyawans()->with(['leaveKaryawans' => function ($query) use ($status) {
    //         $query->where('leavekaryawan_is_hide', false);
            
    //         if ($status && $status !== '' && $status !== null) {
    //             $query->where('leavekaryawan_status', $status);
    //         }
    //     }]);

    //     if ($request->has('karyawan')) {
    //         $query->whereIn('karyawan_id', $request->input('karyawan'));
    //     }

    //     $leaveKaryawans = $query->get();
    //     $result = [];
    //     $data = json_decode($leaveKaryawans, true);

    //     foreach ($data as $item) {
    //         // Extract required information
    //         $karyawan_id = $item['karyawan_id'];
    //         $karyawan_name = $item['karyawan_name'];
            
    //         // Check if the karyawan has leave data
    //         if (!empty($item['leave_karyawans'])) {
    //             // Loop through the leave_karyawans array and extract required leave information
                
    //             foreach ($item['leave_karyawans'] as $leaveKaryawan) {
    //                 $managerKaryawan = KaryawanModel::find($leaveKaryawan['leavekaryawan_manager_id']);
    //                 $settingLeave = SettingLeaveModel::find($leaveKaryawan['st_leave_id']);

    //                 if($leaveKaryawan['leavekaryawan_approval_by'] !== null) {
    //                     $data = $leaveKaryawan['leavekaryawan_approval_by'];
    //                     // Split the data by comma
    //                     $dataParts = explode(",", $data);
    
    //                     // Check the category and get the number
    //                     $category = $dataParts[0];
    //                     $number = $dataParts[1];
    
    //                     if($category === 'KARYAWAN') {
    //                         $getUser = KaryawanModel::where('karyawan_id', $number)->first();
    //                         $approvalBy = $getUser->karyawan_name;
    //                     } else {
    //                         $getUser = MrUserWajibPajakModel::where('ms_user_id', $number)->first();
    //                         $approvalBy = $getUser->userwajibpajak_name;
    //                     }
    //                 } else {
    //                     $approvalBy = '';
    //                 }
                    
    //                 if ($startDate && $endDate) {
    //                     $endDate = $request->input('end_date');

    //                     $endDateObj = new DateTime($endDate);

    //                     // Add one day to the $endDateObj
    //                     // $endDateObj->modify('+1 day');

    //                     // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
    //                     $endDate = $endDateObj->format('Y-m-d');
    //                     $formattedStartDate = date('Y-m-d', strtotime($startDate));


    //                     $checkInDate = $leaveKaryawan['leavekaryawan_request_date'];
    //                     // Add the condition to check if the check_in date is within the specified range
    //                     if ($checkInDate >= $formattedStartDate && $checkInDate <= $endDate) {
    //                         $result[] = [
    //                             'karyawan_id' => $karyawan_id,
    //                             'karyawan_name' => $karyawan_name,
    //                             'leavekaryawan_approval_date' => $leaveKaryawan['leavekaryawan_approval_date'],
    //                             'leavekaryawan_approval_note' => $leaveKaryawan['leavekaryawan_approval_note'],
    //                             'leavekaryawan_end_date' => $leaveKaryawan['leavekaryawan_end_date'],
    //                             'leavekaryawan_start_date' => $leaveKaryawan['leavekaryawan_start_date'],
    //                             'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
    //                             'leavekaryawan_manager_id' => $leaveKaryawan['leavekaryawan_manager_id'],
    //                             'leavekaryawan_quota_use' => $leaveKaryawan['leavekaryawan_quota_use'],
    //                             'leavekaryawan_request_date' => $leaveKaryawan['leavekaryawan_request_date'],
    //                             'leavekaryawan_request_note' => $leaveKaryawan['leavekaryawan_request_note'],
    //                             'leavekaryawan_status' => $leaveKaryawan['leavekaryawan_status'],
    //                             'st_leave_id' => $leaveKaryawan['st_leave_id'],
    //                             'leave_description' => $settingLeave['leave_description'],
    //                             'leavekaryawan_cancel_note' => $leaveKaryawan['leavekaryawan_cancel_note'],
    //                             'manager_name' => $managerKaryawan ? $managerKaryawan['karyawan_name'] : null,
    //                             'approval_name' => $approvalBy
    //                         ];
    //                     }
    //                 } else {
    //                     $result[] = [
    //                         'karyawan_id' => $karyawan_id,
    //                         'karyawan_name' => $karyawan_name,
    //                         'leavekaryawan_approval_date' => $leaveKaryawan['leavekaryawan_approval_date'],
    //                         'leavekaryawan_approval_note' => $leaveKaryawan['leavekaryawan_approval_note'],
    //                         'leavekaryawan_end_date' => $leaveKaryawan['leavekaryawan_end_date'],
    //                         'leavekaryawan_start_date' => $leaveKaryawan['leavekaryawan_start_date'],
    //                         'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
    //                         'leavekaryawan_manager_id' => $leaveKaryawan['leavekaryawan_manager_id'],
    //                         'leavekaryawan_quota_use' => $leaveKaryawan['leavekaryawan_quota_use'],
    //                         'leavekaryawan_request_date' => $leaveKaryawan['leavekaryawan_request_date'],
    //                         'leavekaryawan_request_note' => $leaveKaryawan['leavekaryawan_request_note'],
    //                         'leavekaryawan_status' => $leaveKaryawan['leavekaryawan_status'],
    //                         'st_leave_id' => $leaveKaryawan['st_leave_id'],
    //                         'leave_description' => $settingLeave['leave_description'],
    //                         'leavekaryawan_cancel_note' => $leaveKaryawan['leavekaryawan_cancel_note'],
    //                         'manager_name' => $managerKaryawan ? $managerKaryawan['karyawan_name'] : null,
    //                         'approval_name' => $approvalBy
    //                     ];
    //                 }
    //             }
    //         }
    //     }

    //     usort($result, function($a, $b) {
    //         return strtotime($a['leavekaryawan_request_date']) - strtotime($b['leavekaryawan_request_date']);
    //     });

    //    // Create a paginator manually for the array of objects
    //     $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
    //         array_slice($result, ($page - 1) * $perPage, $perPage),
    //         count($result),
    //         $perPage,
    //         $page,
    //         ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    //     );
        
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Berhasil memuat data Cuti.',
    //         'data' => $paginatedData->items(),
    //         'draw' => $request->input('draw'),
    //         'recordsFiltered' => $paginatedData->total(),
    //         'recordsTotal' => $paginatedData->total()
    //     ], 200);
    // }

    public function leaveAdmin(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_ids = $request->input('karyawan');
        $status = $request->input('status');
        $startDate = ($request->input('start_date')) ? Carbon::createFromFormat('d-m-Y',$request->input('start_date'))->format('Y-m-d') : date('Y-m-d');
        $endDate = ($request->input('end_date')) ? Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->format('Y-m-d') : date('Y-m-d');
        $escapeStartDate = DB::connection()->getPdo()->quote($startDate);
        $escapeEndDate = DB::connection()->getPdo()->quote($endDate);

        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $order_columns = ['ms_karyawan.karyawan_name','leave_description','leavekaryawan_request_date','leavekaryawan_start_date','leavekaryawan_end_date','leavekaryawan_status','leavekaryawan_approval_date','approval_name'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [];
        $list = LeaveKaryawanModel::select('tr_leave_karyawan.*', 'ms_karyawan.karyawan_id', 'ms_karyawan.karyawan_name', 'ms_karyawan.st_leave_id as st_leavekaryawan_id', 'manager.karyawan_name as manager_name', 'leave_description'
        , DB::raw("(SELECT CASE WHEN SPLIT_PART(tr_leave_karyawan.leavekaryawan_approval_by, ',', 1) = 'USER' 
        THEN (SELECT approvrel.userwajibpajak_name as approval_name FROM ms_user as approv
        JOIN mr_user_wajib_pajak as approvrel ON approvrel.ms_user_id = approv.user_id 
        WHERE approv.user_id = SPLIT_PART(tr_leave_karyawan.leavekaryawan_approval_by, ',', 2)::int4)
        ELSE 
        (SELECT approv.karyawan_name as approval_name FROM ms_karyawan as approv WHERE approv.karyawan_id = SPLIT_PART(tr_leave_karyawan.leavekaryawan_approval_by, ',', 2)::int4)
        END)"))
        ->join('st_leave', 'st_leave.leave_id', 'tr_leave_karyawan.st_leave_id')
        ->join('ms_karyawan', 'ms_karyawan.karyawan_id', 'tr_leave_karyawan.ms_karyawan_id')
        ->leftJoin('ms_karyawan as manager', 'manager.karyawan_id', 'tr_leave_karyawan.leavekaryawan_manager_id')
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            $q->whereIn('ms_karyawan.karyawan_id', $karyawan_ids);
        })
        ->when($status, function($q, $status) {
            $q->where('leavekaryawan_status', $status);
        })
        ->where($where)
        ->whereRaw(DB::raw("leavekaryawan_start_date BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))
        ->whereRaw(DB::raw("ms_karyawan_id IN (SELECT karyawan_id FROM ms_karyawan WHERE karyawan_active='1' AND ms_wajibpajak_id = {$wajibpajak_id})"))
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = LeaveKaryawanModel::
        join('st_leave', 'st_leave.leave_id', 'tr_leave_karyawan.st_leave_id')
        ->join('ms_karyawan', 'ms_karyawan.karyawan_id', 'tr_leave_karyawan.ms_karyawan_id')
        ->leftJoin('ms_karyawan as manager', 'manager.karyawan_id', 'tr_leave_karyawan.leavekaryawan_manager_id')
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            $q->whereIn('ms_karyawan.karyawan_id', $karyawan_ids);
        })
        ->when($status, function($q, $status) {
            $q->where('leavekaryawan_status', $status);
        })
        ->where($where)
        ->whereRaw(DB::raw("leavekaryawan_start_date BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))
        ->whereRaw(DB::raw("ms_karyawan_id IN (SELECT karyawan_id FROM ms_karyawan WHERE karyawan_active='1' AND ms_wajibpajak_id = {$wajibpajak_id})"))
        ->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function importTemplate(Request $request)
    {
        try {
            // Get the WajibPajakModel data based on the wajibpajak_id
            $karyawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', '1')
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->get();
            
            $dataKaryawan = json_decode($karyawan, true);
            $kehadiran = [];
            $setting = [];

            $settingLeave = SettingLeaveModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->orderBy('leave_id', 'ASC')
                ->get();

            $dataLeave = json_decode($settingLeave, true);

            foreach ($dataKaryawan as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_enid'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }

            function convertYYYYMMDDToDate($dateString) {
                $timestamp = strtotime($dateString);
                $formattedDate = date("d-m-Y", $timestamp);
                return $formattedDate;
            }

            foreach($dataLeave as $leave) {
                $setting[] = [
                    "leave_id" => 'CU' . $leave['leave_id'],
                    "leave_description" => $leave['leave_description'],
                    "leave_active_start_date" => convertYYYYMMDDToDate($leave['leave_active_start_date']),
                    "leave_active_end_date" => convertYYYYMMDDToDate($leave['leave_active_end_date']),
                ];
            }

            $unixtime = time();
            $title = 'Templat_Impor_Cuti_' . $unixtime;

            // $htmlString = view('user.kehadiran.export', ['title' => $title,'kehadiran' => $kehadiran])->render();

            // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            // $spreadsheet = $reader->loadFromString($htmlString);

            $excelFilePath = public_path('assets/import/Template_Import_Cuti.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            
            $sheetEmployee = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Karyawan');
            $spreadsheet->addSheet($sheetEmployee);

            $htmlString = view('user.cuti.import-karyawan', ['title' => 'Referensi Data Karyawan', 'kehadiran' => $kehadiran])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetEmployee->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $sheetSetting = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Cuti');
            $spreadsheet->addSheet($sheetSetting);

            $htmlString = view('user.cuti.import-setting', ['title' => 'Referensi Data Cuti', 'setting' => $setting])->render();
            $dom2 = new DOMDocument();
            $dom2->loadHTML($htmlString);
            $table2 = $dom2->getElementsByTagName('table')->item(0);

            $rowIndex2 = 1;
            foreach ($table2->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetSetting->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetSetting->getStyleByColumnAndRow($cellIndex, $rowIndex2)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex2 === 1) {
                    $rowIndex2++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetSetting->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex2++;
            }


            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Cuti Karyawan');
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Kode Cuti*');
            $sheetTemplate->setCellValue('C1', 'Tanggal Pengajuan*');
            $sheetTemplate->setCellValue('D1', 'Tanggal Mulai Cuti*');
            $sheetTemplate->setCellValue('E1', 'Tanggal Berakhir Cuti*');
            $sheetTemplate->setCellValue('F1', 'Alasan Cuti*');
            $sheetTemplate->setCellValue('G1', 'Tanggal Persetujuan*');

            for ($col = 'A'; $col <= 'G'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                if($sheetName !== 'Panduan Pengguna') {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    // Set all columns in the sheet to auto width
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'LEAVE')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status', 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Setting Kehadiran.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
    }

    public function generateHistory(Request $request) 
    {
        try {
            // Get the WajibPajakModel data based on the wajibpajak_id
            $karyawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', '1')
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->get();
            
            $dataKaryawan = json_decode($karyawan, true);
            $kehadiran = [];
            $setting = [];

            $settingLeave = SettingLeaveModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->orderBy('leave_id', 'ASC')
                ->get();

            $dataLeave = json_decode($settingLeave, true);

            foreach ($dataKaryawan as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_enid'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }

            function convertDate($dateString) {
                $timestamp = strtotime($dateString);
                $formattedDate = date("d F Y", $timestamp); // "F" gives you the full month name
                return $formattedDate;
            }

            foreach($dataLeave as $leave) {
                $setting[] = [
                    "leave_id" => 'CU' . $leave['leave_id'],
                    "leave_description" => $leave['leave_description'],
                    "leave_active_start_date" => convertDate($leave['leave_active_start_date']),
                    "leave_active_end_date" => convertDate($leave['leave_active_end_date']),
                ];
            }

            $unixtime = time();
            $title = 'Riwayat_Impor_Cuti_'.$unixtime;

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $excelFilePath = public_path('assets/import/Template_Import_Cuti.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

            $sheetEmployee = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Referensi Karyawan');
            $spreadsheet->addSheet($sheetEmployee);

            $htmlString = view('user.cuti.import-karyawan', ['title' => 'Data Referensi Karyawan', 'kehadiran' => $kehadiran])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetEmployee->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $sheetSetting = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Referensi Cuti');
            $spreadsheet->addSheet($sheetSetting);

            $htmlString = view('user.cuti.import-setting', ['title' => 'Data Referensi Cuti', 'setting' => $setting])->render();
            $dom2 = new DOMDocument();
            $dom2->loadHTML($htmlString);
            $table2 = $dom2->getElementsByTagName('table')->item(0);

            $rowIndex2 = 1;
            foreach ($table2->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetSetting->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetSetting->getStyleByColumnAndRow($cellIndex, $rowIndex2)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex2 === 1) {
                    $rowIndex2++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetSetting->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex2++;
            }

            $id = $request->input('id');

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Cuti Karyawan');
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Kode Cuti*');
            $sheetTemplate->setCellValue('C1', 'Tanggal Pengajuan*');
            $sheetTemplate->setCellValue('D1', 'Tanggal Mulai Cuti*');
            $sheetTemplate->setCellValue('E1', 'Tanggal Berakhir Cuti*');
            $sheetTemplate->setCellValue('F1', 'Alasan Cuti*');
            $sheetTemplate->setCellValue('G1', 'Tanggal Persetujuan*');
            $sheetTemplate->setCellValue('H1', 'Status');
            $sheetTemplate->setCellValue('I1', 'Catatan');
            $sheetTemplate->getStyle('A1:I1')->applyFromArray($boldFontStyle);

            $rowIndex = 2;  

            foreach($content as $item) {
                $listleave = [
                    'A' => isset($item->karyawan_id) ? $item->karyawan_id : '',
                    'B' => isset($item->leave_id) ? 'CU' . $item->leave_id : '',
                    'C' => isset($item->leavekaryawan_request_date) ? $item->leavekaryawan_request_date : '',
                    'D' => isset($item->leavekaryawan_start_date) ? $item->leavekaryawan_start_date : '',
                    'E' => isset($item->leavekaryawan_end_date) ? $item->leavekaryawan_end_date : '',
                    'F' => isset($item->leavekaryawan_request_note) ? $item->leavekaryawan_request_note : '',
                    // 'leavekaryawan_status' => isset($item->leavekaryawan_status) ? mapLeaveStatus($item->leavekaryawan_status) : '',
                    'G' => isset($item->leavekaryawan_approval_date) ? $item->leavekaryawan_approval_date : '',
                    // 'leavekaryawan_approval_note' => isset($item->leavekaryawan_approval_note) ? $item->leavekaryawan_approval_note : '',
                    'H' => isset($item->status) ? $item->status : 'Sukses',
                    'I' => isset($item->remark) ? $item->remark : '-',
                ];

                foreach ($listleave as $column => $value) {
                    $sheetTemplate->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }

            for ($col = 'A'; $col <= 'G'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                if($sheetName !== 'Panduan Pengguna') {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    // Set all columns in the sheet to auto width
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importCuti(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }
    
        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }
        
        $originalFileName = $file->getClientOriginalName();
    
        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        $path = public_path('uploads/'.$filename);

        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Cuti Karyawan');
        $maxRows = $sheet->getHighestRow();

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 1; // mulai baris ke 3;

        $result = [];

        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $columnA = $sheet->getCell("A" . $nbi)->getValue();
                $columnB = $sheet->getCell("B" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                
                if($columnA === null) {
                    if($columnB === null) {
                        break;
                    } else {
                        $spreadsheet_chunk->__destruct();
                        $spreadsheet_chunk = null;
                        unset($spreadsheet_chunk);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "Mohon lengkapi ID Karyawan yang masih kosong"
                        ]);
                    }
                    
                }

                if($columnB !== null) {
                    $columnB = explode('-', $columnB);
                    $columnB = end($columnB);
                }

                // $columns = ['C', 'D', 'E', 'G']; // List of column letters

                // foreach ($columns as $columnLetter) {
                //     $cellValue = $sheet->getCell($columnLetter . $nbi)->getValue();

                //     $datePattern = '/^\d{1,2}\s\w+\s\d{4}$/';

                //     if (is_string($cellValue) && preg_match('/^\d{1,2}\s\w+\s\d{4}$/', $cellValue)) {
                //         // Date is in the format "DD Month YYYY"
                //         // Keep the date as is
                //         ${'column' . $columnLetter} = $cellValue;
                //     } elseif (is_numeric($cellValue)) {
                //         // Date is in decimal format (Excel date serial number)
                //         $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                //         $dateString = date("d F Y", $timestamp); // "F" gives you the full month name
                //         ${'column' . $columnLetter} = $dateString;
                //     } else {
                //         // Handle cases where the date format is not as expected
                //         ${'column' . $columnLetter} = ''; // Set to an empty string or handle the error as needed
                //     }
                // }
               
                $columnF = $sheet->getCell("F" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnG = $sheet->getCell("G" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnC = $sheet->getCell("C" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnD = $sheet->getCell("D" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnE = $sheet->getCell("E" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
             
                $result[] = [
                    "karyawan_id" => $columnA,
                    "leave_id" => $columnB,
                    "leavekaryawan_request_date" => $columnC !== "" ? $columnC : null,
                    "leavekaryawan_start_date" => $columnD !== "" ? $columnD : null,
                    "leavekaryawan_end_date" => $columnE !== "" ? $columnE : null,
                    "leavekaryawan_request_note" => $columnF,
                    "leavekaryawan_status" => 'APPROVED',
                    "leavekaryawan_approval_date" => $columnG !== "" ? $columnG : null,
                    "row" => $nb+1
                ];
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }

        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);

        DB::beginTransaction();
        try {
            function convertDateStringToYYYYMMDD($dateString) {
                if ($dateString === null || $dateString === '') {
                    return null; // Return null for empty input
                } else {
                    // Split the date string into day, month, and year
                    list($day, $month, $year) = explode('-', $dateString);
            
                    // Create a new date in the "YYYY-MM-DD" format
                    $newDateString = "{$year}-{$month}-{$day}";
            
                    return $newDateString;
                }
            }
    
            function formatDate($data) {
                if (is_string($data) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $data)) {
                    $dateString = true;
                }  else {
                    // Handle cases where the date format is not as expected
                    $dateString = false; // Set to an empty string or handle the error as needed
                }
            
                return $dateString;
            }
                    
            function isHoliday($date, $holidays) {
                foreach ($holidays as $holiday) {
                    if ($date >= $holiday->holiday_start_date && $date <= $holiday->holiday_end_date) {
                        return true;
                    }
                }
                return false;
            }

            $checkHoliday = SettingHolidayModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->where('holiday_status_active', 1)->get();

            $totalComplete = 0;
            $totalFail = 0;

            for($x = 0; $x < count($result); $x++) {
                $checkKaryawan = KaryawanModel::where('karyawan_enid', $result[$x]['karyawan_id'])->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
        
                if(!$checkKaryawan) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Id Karyawan tidak terdaftar';
                    continue;
                } else {
                    $result[$x]['karyawan_name'] = $checkKaryawan->karyawan_name;
                }

                $getLeave = SettingLeaveModel::find($result[$x]['leave_id']);

                if (!isset($getLeave)) {

                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Kode Cuti tidak terdaftar';
                    continue;
                } else {
                    $result[$x]['leave_description'] = $getLeave->leave_description;
                }
                
                if (formatDate($result[$x]['leavekaryawan_request_date']) === false) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan Tanggal Pengajuan sesuai format sistem REKKAA';
                    continue;
                } else if (formatDate($result[$x]['leavekaryawan_start_date']) === false) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan Tanggal Mulai Cuti sesuai format sistem REKKAA';
                    continue;
                } else if (formatDate($result[$x]['leavekaryawan_end_date']) === false) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan Tanggal Berakhir Cuti sesuai format sistem REKKAA';
                    continue;
                } else if (formatDate($result[$x]['leavekaryawan_approval_date']) === false) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan Tanggal Persetujuan sesuai format sistem REKKAA';
                    continue;
                } else if ($result[$x]['leavekaryawan_request_note'] === null || $result[$x]['leavekaryawan_request_note'] === '') {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan Alasan Cuti';
                    continue;
                }

                
                $startDate = convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_start_date']);
                $endDate = convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_end_date']);

                $checkLeaveKaryawanModel = LeaveKaryawanModel::where('ms_karyawan_id',  $checkKaryawan['karyawan_id'])->whereDate('leavekaryawan_start_date', '=', $startDate)->whereDate('leavekaryawan_end_date', '=', $endDate)->whereDate('leavekaryawan_request_date', '=', convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_request_date']))->first();
                
                if($checkKaryawan['st_attendance_id'] !== null) {
                    $checkAttendance = SettingAttendanceModel::where('attendance_id', $checkKaryawan['st_attendance_id'])->first();
        
                    $workingDays = json_decode($checkAttendance['attendance_working_day']);
                } else {
                    $workingDays = [];
                }

                
                // Initialize an array to store the leave days
                $leaveDays = [];


                $currentDate = new DateTime($startDate);
                $tempEndDate = new DateTime($endDate);

                while ($currentDate <= $tempEndDate) {
                    $currentDayOfWeek = $currentDate->format('l');
                    $currentDateStr = $currentDate->format('Y-m-d');

                    // Check if the current date is not a holiday and is a working day
                    if(count($workingDays) > 0) {
                        if (!isHoliday($currentDateStr, $checkHoliday) && in_array($currentDayOfWeek, $workingDays)) {
                            $leaveDays[] = $currentDateStr;
                        }
                    } else {
                        $leaveDays[] = $currentDateStr;
                    }

                    $currentDate->modify('+1 day');
                }

                // $leaveDays now contains the leave days that meet the criteria
                $totalLeaveDays = count($leaveDays);

                $karyawan_manager_id = null;

                if($checkKaryawan['karyawan_manager_id'] !== null && $checkKaryawan['karyawan_manager_id'] !== 0 && $checkKaryawan['karyawan_manager_id'] !== '0') {
                    $karyawan_manager_id = $checkKaryawan['karyawan_manager_id'];
                }

                if(!$checkLeaveKaryawanModel) {
                    $createLeave = LeaveKaryawanModel::create([
                        'ms_karyawan_id' => $checkKaryawan['karyawan_id'],
                        'st_leave_id' => $result[$x]['leave_id'],
                        'leavekaryawan_request_date' => convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_request_date']),
                        'leavekaryawan_start_date' => $startDate,
                        'leavekaryawan_end_date' => $endDate,
                        'leavekaryawan_request_note' => $result[$x]['leavekaryawan_request_note'],
                        'leavekaryawan_status' => $result[$x]['leavekaryawan_status'],
                        'leavekaryawan_approval_date' => convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_approval_date']),
                        // 'leavekaryawan_approval_note' => $result[$x]['leavekaryawan_approval_note'],
                        'leavekaryawan_manager_id' => $karyawan_manager_id,
                        'leavekaryawan_quota_use' => $totalLeaveDays,
                        'leavekaryawan_approval_by' => 'USER,' . session()->get('user_data')['user_id']
                    ]);
                } else {
                    $checkLeaveKaryawanModel->update([
                        'ms_karyawan_id' => $checkKaryawan['karyawan_id'],
                        'st_leave_id' => $result[$x]['leave_id'],
                        'leavekaryawan_request_date' => convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_request_date']),
                        'leavekaryawan_start_date' => $startDate,
                        'leavekaryawan_end_date' => $endDate,
                        'leavekaryawan_request_note' => $result[$x]['leavekaryawan_request_note'],
                        'leavekaryawan_status' => $result[$x]['leavekaryawan_status'],
                        'leavekaryawan_approval_date' => convertDateStringToYYYYMMDD($result[$x]['leavekaryawan_approval_date']),
                        // 'leavekaryawan_approval_note' => $result[$x]['leavekaryawan_approval_note'],
                        'leavekaryawan_manager_id' => $karyawan_manager_id,
                        'leavekaryawan_quota_use' => $totalLeaveDays,
                        'leavekaryawan_approval_by' => 'USER,' . session()->get('user_data')['user_id']
                    ]);
                }

                $totalComplete = $totalComplete + 1;
                $result[$x]['status'] = 'Sukses';
                $result[$x]['remark'] = '-';
            }

            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_user_id' => session()->get('user_data')['user_id'],
                'historyimport_type' => "LEAVE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_content' => json_encode($result),
                'historyimport_file_name' => $originalFileName,
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data cuti.',
                'result' => $result,
                'totalsuccess' => $totalComplete,
                'totalfail'=> $totalFail,
                'totaldata' => $totalComplete + $totalFail,
                'history' => $createHistory->historyimport_id
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function exportAdmin(Request $request)
    {
        try {
            $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('status');
    
            // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
            if (!$wajibPajak) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            // Retrieve all related LeaveKaryawanModel data for the given WajibPajakModel with pagination
            $query = $wajibPajak->karyawans()->with(['leaveKaryawans' => function ($query) use($status) {
                $query->where('leavekaryawan_is_hide', false);

                if ($status && $status !== '' && $status !== null) {
                    $query->where('leavekaryawan_status', $status);
                }
            }]);

            
    
            if ($request->has('karyawan')) {
                $query->whereIn('karyawan_id', $request->input('karyawan'));
            }

    
            $leaveKaryawans = $query->get();
            $result = [];
            $data = json_decode($leaveKaryawans, true);

           
    
            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_id'];
                $karyawan_name = $item['karyawan_name'];
                
                // Check if the karyawan has leave data
                if (!empty($item['leave_karyawans'])) {
                    // Loop through the leave_karyawans array and extract required leave information
                    
                    foreach ($item['leave_karyawans'] as $leaveKaryawan) {
                        $managerKaryawan = KaryawanModel::find($leaveKaryawan['leavekaryawan_manager_id']);
                        $settingLeave = SettingLeaveModel::find($leaveKaryawan['st_leave_id']);

                        if($leaveKaryawan['leavekaryawan_approval_by'] !== null) {
                            $data = $leaveKaryawan['leavekaryawan_approval_by'];
                            // Split the data by comma
                            $dataParts = explode(",", $data);
        
                            // Check the category and get the number
                            $category = $dataParts[0];
                            $number = $dataParts[1];
        
                            if($category === 'KARYAWAN') {
                                $getUser = KaryawanModel::where('karyawan_id', $number)->first();
                                $approvalBy = $getUser->karyawan_name;
                            } else {
                                $getUser = MrUserWajibPajakModel::where('ms_user_id', $number)->first();
                                $approvalBy = $getUser->userwajibpajak_name;
                            }
                        } else {
                            $approvalBy = '';
                        }
    
                        if ($startDate && $endDate) {
                            $endDate = $request->input('end_date');
    
                            $endDateObj = new DateTime($endDate);
    
                            // Add one day to the $endDateObj
                            // $endDateObj->modify('+1 day');
    
                            // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
                            $endDate = $endDateObj->format('Y-m-d');
                            $formattedStartDate = date('Y-m-d', strtotime($startDate));
    
    
                            $checkInDate = $leaveKaryawan['leavekaryawan_request_date'];
                            // Add the condition to check if the check_in date is within the specified range
                            if ($checkInDate >= $formattedStartDate && $checkInDate <= $endDate) {
                                $result[] = [
                                    'karyawan_id' => $karyawan_id,
                                    'karyawan_name' => $karyawan_name,
                                    'leavekaryawan_approval_date' => $leaveKaryawan['leavekaryawan_approval_date'],
                                    'leavekaryawan_approval_note' => $leaveKaryawan['leavekaryawan_approval_note'],
                                    'leavekaryawan_end_date' => $leaveKaryawan['leavekaryawan_end_date'],
                                    'leavekaryawan_start_date' => $leaveKaryawan['leavekaryawan_start_date'],
                                    'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
                                    'leavekaryawan_manager_id' => $leaveKaryawan['leavekaryawan_manager_id'],
                                    'leavekaryawan_quota_use' => $leaveKaryawan['leavekaryawan_quota_use'],
                                    'leavekaryawan_request_date' => $leaveKaryawan['leavekaryawan_request_date'],
                                    'leavekaryawan_request_note' => $leaveKaryawan['leavekaryawan_request_note'],
                                    'leavekaryawan_status' => $leaveKaryawan['leavekaryawan_status'],
                                    'st_leave_id' => $leaveKaryawan['st_leave_id'],
                                    'leave_description' => $settingLeave['leave_description'],
                                    'leavekaryawan_cancel_note' => $leaveKaryawan['leavekaryawan_cancel_note'],
                                    'manager_name' => $managerKaryawan ? $managerKaryawan['karyawan_name'] : null,
                                    'approval_name' => $approvalBy
                                ];
                            }
                        } else {
                            $result[] = [
                                'karyawan_id' => $karyawan_id,
                                'karyawan_name' => $karyawan_name,
                                'leavekaryawan_approval_date' => $leaveKaryawan['leavekaryawan_approval_date'],
                                'leavekaryawan_approval_note' => $leaveKaryawan['leavekaryawan_approval_note'],
                                'leavekaryawan_end_date' => $leaveKaryawan['leavekaryawan_end_date'],
                                'leavekaryawan_start_date' => $leaveKaryawan['leavekaryawan_start_date'],
                                'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
                                'leavekaryawan_manager_id' => $leaveKaryawan['leavekaryawan_manager_id'],
                                'leavekaryawan_quota_use' => $leaveKaryawan['leavekaryawan_quota_use'],
                                'leavekaryawan_request_date' => $leaveKaryawan['leavekaryawan_request_date'],
                                'leavekaryawan_request_note' => $leaveKaryawan['leavekaryawan_request_note'],
                                'leavekaryawan_status' => $leaveKaryawan['leavekaryawan_status'],
                                'st_leave_id' => $leaveKaryawan['st_leave_id'],
                                'leave_description' => $settingLeave['leave_description'],
                                'manager_name' => $managerKaryawan ? $managerKaryawan['karyawan_name'] : null,
                                'approval_name' => $approvalBy
                            ];
                        }
                    }
                }
            }

            usort($result, function($a, $b) {
                return strtotime($a['leavekaryawan_request_date']) - strtotime($b['leavekaryawan_request_date']);
            });

            $unixtime = time();
            $title = 'Daftar_Cuti_Karyawan_'.$unixtime;

            $htmlString = view('user.cuti.export-admin', ['title' => $title,'cuti' => $result])->render();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString);

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                // Set all columns in the sheet to auto width
                foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }

            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'isEdit' => 'required|boolean', // New validation rule for isEdit field
                'st_leave_id' => $request->input('isEdit') === false ? 'required' : '',
                'leavekaryawan_type' => 'required|in:REQUEST,APPROVAL',
                'leavekaryawan_request_date' => $request->input('isEdit') === false ? 'required' : '', // Required only if isEdit is false
                'leavekaryawan_start_date' => $request->input('isEdit') === false ? 'required' : '',
                'leavekaryawan_end_date' => $request->input('isEdit') === false ? 'required' : '',
                'leavekaryawan_request_note' => $request->input('isEdit') === false ? 'required' : '',
                'leavekaryawan_status' => $request->input('isEdit') === false ? 'required|in:APPROVED,REJECTED,WAITING,CANCEL' : '', // Required if isEdit is false
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            
            $checkLeave = LeaveKaryawanModel::where('leavekaryawan_id', $request->input('leavekaryawan_id'))->first();
            
            $getKaryawan = KaryawanModel::where('karyawan_id', $checkLeave->ms_karyawan_id)->first();

            $approval_id = 'USER,' . session()->get('user_data')['user_id'];

            if(!$checkLeave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data cuti tidak ditemukan.'
                ], 404);
            }
            
            DB::beginTransaction();
            try {
                if($checkLeave->leavekaryawan_another_id !== null) {
                    $isDouble = true;

                    $checkLeaveDouble = LeaveKaryawanModel::where('leavekaryawan_id', $checkLeave->leavekaryawan_another_id)->first();
                } else {
                    $isDouble = false;
                }

                $isApprovalCancel = false;
                $isRequestCancel = false;
                $isRejectedCancel = false;

                if($request->input('leavekaryawan_status') === 'CANCEL') {
                    if($checkLeave->leavekaryawan_status === 'WAITINGCANCEL') {
                        $checkLeave->update([
                            'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                        ]);

                        if($isDouble) {
                            $checkLeaveDouble->update([
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                            ]);
                        }

                        $isApprovalCancel = true;
                    }
                } else if($request->input('leavekaryawan_status') === 'APPROVED') {
                    if($checkLeave->leavekaryawan_status === 'WAITINGCANCEL') {
                        $checkLeave->update([
                            'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                        ]);

                        if($isDouble) {
                            $checkLeaveDouble->update([
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                            ]);
                        }

                        $isRejectedCancel = true;
                    } else {
                        
                        $checkLeave->update([
                            'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                            'leavekaryawan_approval_date' => $request->input('leavekaryawan_approval_date'),
                            'leavekaryawan_approval_by' => $approval_id
                        ]);

                        if($isDouble) {
                            $checkLeaveDouble->update([
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                'leavekaryawan_approval_date' => $request->input('leavekaryawan_approval_date'),
                                'leavekaryawan_approval_by' => $approval_id
                            ]);
                        }
                    }
                } else if($request->input('leavekaryawan_status') === 'REJECTED') {
                    $checkLeave->update([
                        'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                        'leavekaryawan_approval_date' => $request->input('leavekaryawan_approval_date'),
                        'leavekaryawan_approval_note' => $request->input('leavekaryawan_approval_note'),
                        'leavekaryawan_approval_by' => $approval_id
                    ]);

                    if($isDouble) {
                        $checkLeaveDouble->update([
                            'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                            'leavekaryawan_approval_date' => $request->input('leavekaryawan_approval_date'),
                            'leavekaryawan_approval_note' => $request->input('leavekaryawan_approval_note'),
                            'leavekaryawan_approval_by' => $approval_id
                        ]);
                    }
                }

                if($request->input('leavekaryawan_status') === 'APPROVED' || $request->input('leavekaryawan_status') === 'REJECTED' || $request->input('leavekaryawan_status') === 'CANCEL') {
                    $status = $request->input('leavekaryawan_status') === 'APPROVED' ? 'Disetujui' : 'Tidak Disetujui';

                    $currentHour = date('G'); // Get the current hour in 24-hour format

                    if ($currentHour >= 0 && $currentHour < 12) {
                        $timeOfDay = 'Pagi';
                    } elseif ($currentHour >= 12 && $currentHour < 18) {
                        $timeOfDay = 'Siang';
                    } else {
                        $timeOfDay = 'Malam';
                    }

                    $startDate = date('d F Y', strtotime($checkLeave->leavekaryawan_start_date));

                    if($isRequestCancel === false && $request->input('leavekaryawan_status') === 'CANCEL') {
                        $status = 'Dibatalkan';
                        if($isApprovalCancel) {
                            $status = 'Disetujui';
                            NotificationModel::create([
                                'notification_title' => 'Rekkaa - Pengajuan Pembatalan Cuti',
                                'notification_type' => 'EMAIL',
                                'notification_from' => env("MAIL_FROM_ADDRESS"),
                                'notification_to' => $getKaryawan->karyawan_email,
                                'ms_user_id' => $getKaryawan->wajibpajak->ms_user_id,
                                'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                'notification_view' => 'email.email-approval-cancel-leave',
                                'notification_data' => json_encode([
                                    'content' => [
                                        'time' => $timeOfDay,
                                        'manager_name' => isset(session()->get('user_data')['user_name']),
                                        'status' => $status,
                                        'staff_name' => $getKaryawan->karyawan_name,
                                        'start_date' => $startDate,
                                        'quota_use' => $checkLeave->leavekaryawan_another_id !== null ? $checkLeave->leavekaryawan_quota_use + $checkLeave->leavekaryawan_another_quota_use : $checkLeave->leavekaryawan_quota_use,
                                    ],
                                    'st_leave_id' => $checkLeave->st_leave_id,
                                    'leavekaryawan_id' => $checkLeave->leavekaryawan_id 
                                ])
                            ]);
                        }
                    } else {
                        if($isRejectedCancel === true) {
                            $status = 'Tidak Disetujui';
                            NotificationModel::create([
                                'notification_title' => 'Rekkaa - Pengajuan Pembatalan Cuti',
                                'notification_type' => 'EMAIL',
                                'notification_from' => env("MAIL_FROM_ADDRESS"),
                                'notification_to' => $getKaryawan->karyawan_email,
                                'ms_user_id' => $getKaryawan->wajibpajak->ms_user_id,
                                'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                'notification_view' => 'email.email-reject-cancel-leave',
                                'notification_data' => json_encode([
                                    'content' => [
                                        'time' => $timeOfDay,
                                        'manager_name' => isset(session()->get('user_data')['user_name']),
                                        'status' => $status,
                                        'staff_name' => $getKaryawan->karyawan_name,
                                        'start_date' => $startDate,
                                        'quota_use' => $checkLeave->leavekaryawan_another_id !== null ? $checkLeave->leavekaryawan_quota_use + $checkLeave->leavekaryawan_another_quota_use : $checkLeave->leavekaryawan_quota_use,
                                    ],
                                    'st_leave_id' => $checkLeave->st_leave_id,
                                    'leavekaryawan_id' => $checkLeave->leavekaryawan_id 
                                ])
                            ]);
                        } else {
                            NotificationModel::create([
                                'notification_title' => 'Rekkaa - Pengajuan Cuti',
                                'notification_type' => 'EMAIL',
                                'notification_from' => env("MAIL_FROM_ADDRESS"),
                                'notification_to' => $getKaryawan->karyawan_email,
                                'ms_user_id' => $getKaryawan->wajibpajak->ms_user_id,
                                'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                'notification_view' => 'email.email-approval-leave',
                                'notification_data' => json_encode([
                                    'content' => [
                                        'time' => $timeOfDay,
                                        'manager_name' => isset(session()->get('user_data')['user_name']),
                                        'status' => $status,
                                        'staff_name' => $getKaryawan->karyawan_name,
                                        'start_date' => $startDate,
                                        'quota_use' => $checkLeave->leavekaryawan_another_id !== null ? $checkLeave->leavekaryawan_quota_use + $checkLeave->leavekaryawan_another_quota_use : $checkLeave->leavekaryawan_quota_use,
                                    ],
                                    'st_leave_id' => $checkLeave->st_leave_id,
                                    'leavekaryawan_id' => $checkLeave->leavekaryawan_id 
                                ])
                            ]);
                        }
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil melakukan update data Cuti.',
                    'data' => $checkLeave
                ], 200);                    
            } catch (Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
                    
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHandler(Request $request)
    {
        $checkEmployee = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->where('karyawan_active', '1')
            ->where('karyawan_status', '!=', 'NONKARYAWAN')
            ->limit(3)
            ->get();

        $checkLeave = SettingLeaveModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->where('leave_status_active', true)
            ->limit(3)
            ->get();

        if(count($checkLeave) === 0 || count($checkEmployee) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Berhasil memuat data cuti.'
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data cuti.'
            ], 200);
        }
    }
}

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in            $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row <   $this->endRow)) {
            return true;
        }

        return false;
    }
}