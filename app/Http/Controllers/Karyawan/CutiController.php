<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingLeaveDetailModel;
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
    public function indexEmployee(Request $request)
    {
        $data = [
            'title' => 'Pengajuan Cuti',
            'content' => 'karyawan.cuti.cuti-karyawan',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['karyawan_id' => session()->get('karyawan_data')['karyawan_id']])->count()
        ];
        // This method can return a view or perform any other action related to Absensi.
        // For this example, let's return the view for the camera pop-up.
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function indexManager(Request $request)
    {
        $data = [
            'title' => 'Daftar Cuti',
            'content' => 'karyawan.cuti.cuti-manager',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function leaveEmployee(Request $request)
    {
        $query = LeaveKaryawanModel::query();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;

        if ($startDate && $endDate) {
            // Apply date filtering to the query
            $endDate = $request->input('end_date');

            // Convert the $endDate to a DateTime object
            $endDateObj = new DateTime($endDate);

            // Add one day to the $endDateObj
            $endDateObj->modify('+1 day');

            // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
            $endDate = $endDateObj->format('Y-m-d');
            $formattedStartDate = date('Y-m-d', strtotime($startDate));

            $query->whereBetween('leavekaryawan_request_date', [$formattedStartDate, $endDate]);
        }

        if($status !== '' && $status !== null) {
            $query->where('leavekaryawan_status', $status);
        }

        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

        if(!isset($getKaryawan)) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data Cuti.',
                'data' => [],
                'draw' => $request->input('draw'),
                'recordsFiltered' => 0,
                'recordsTotal' => 0,
            ], 200);
        }

        $karyawan_id = $getKaryawan->karyawan_id;

        // Implement pagination and limit per page (assuming you have 'date' column for date in your leave table)
        $leaveData = $query
            ->where('ms_karyawan_id', $karyawan_id)
            ->where('leavekaryawan_is_hide', false)
            ->with(['st_leave' => function ($query) {
                $query->select('leave_id', 'leave_description'); // Select only the desired columns
            }])
            ->orderBy('leavekaryawan_request_date', 'ASC')
            ->paginate($perPage, ['*'], 'page', $page);

        foreach ($leaveData as $leaveItem) {
            $data = $leaveItem->leavekaryawan_approval_by;

            if($data !== null) {
                // Split the data by comma
                $dataParts = explode(",", $data);

                // Check the category and get the number
                $category = $dataParts[0];
                $number = $dataParts[1];

                if($category === 'KARYAWAN') {
                    $getUser = KaryawanModel::where('karyawan_id', $number)->first();
                    $leaveItem->approval_name = $getUser->karyawan_name;
                } else {
                    $getUser = MrUserWajibPajakModel::where('ms_user_id', $number)->first();
                    $leaveItem->approval_name = $getUser->userwajibpajak_name;
                }
            } else {
                $leaveItem->approval_name = '';
            }
            
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Cuti.',
            'data' => $leaveData->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $leaveData->total(),
            'recordsTotal' => $leaveData->total(),
        ], 200);
    }

    public function checkWeekend(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

        if(!isset($getKaryawan)) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data Cuti.',
                'data' => 0
            ], 200);
        }

        $checkHoliday = SettingHolidayModel::where('ms_wajibpajak_id', session()->get('karyawan_data')['wajibpajak_id'])->where('holiday_status_active', 1)->get();


        if($getKaryawan->st_attendance_id !== null) {
            $checkAttendance = SettingAttendanceModel::where('attendance_id', $getKaryawan->st_attendance_id)->first();

            $workingDays = json_decode($checkAttendance->attendance_working_day);
        } else {
            $workingDays = [];
        }

        // Initialize an array to store the leave days
        $leaveDays = [];

        // Iterate through the date range
        $currentDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        
        function isHoliday($date, $holidays) {
            foreach ($holidays as $holiday) {
                if ($date >= $holiday->holiday_start_date && $date <= $holiday->holiday_end_date) {
                    return true;
                }
            }
            return false;
        }

        while ($currentDate <= $endDate) {
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

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Cuti.',
            'data' => $totalLeaveDays,
        ], 200);
    }

    public function leaveEmployeeLeft(Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];

        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

        $leaveId = SettingLeaveDetailModel::where('ms_karyawan_id', session()->get('karyawan_data')['karyawan_id'])
            ->where('leavedetail_active', true)
            ->pluck('st_leave_id')
            ->toArray();

        // dd($leaveId);

        if (empty($leaveId)) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data Cuti.',
                'data' => []
            ], 200);
        }

        $result = [];

        // Use the extracted values to query the SettingAttendanceModel model
        $getLeaveSetting = SettingLeaveModel::whereIn('leave_id', $leaveId)->where('leave_status_active', true)
        ->where(function ($query) {
            $query->where('leave_active_end_date', '>=', now())
                ->orWhere('leave_grace_period', '>=', now());
        })
        ->get();
        $data = json_decode($getLeaveSetting, true);

        foreach ($data as $item) {
            $quota = $item['leave_quota'];

            if ($item['leave_probation_status'] === false && $getKaryawan->karyawan_status === 'PERCOBAAN') {
                continue;
            } else if ($item['leave_base_month'] === true) {
                $startDateObj = new DateTime($getKaryawan->karyawan_contract_end ? $getKaryawan->karyawan_contract_end : $getKaryawan->karyawan_probation_end);
                $endDateObj = new DateTime($item['leave_active_end_date']);

                if ($startDateObj->format('Y') == $endDateObj->format('Y')) {
                    // Calculate the difference in months if the years are the same
                    $quota = $quota / 12;
                    $interval = $startDateObj->diff($endDateObj);
                    $months = $interval->format('%m');  

                    $quota = ($quota * $months) + 2;
                }
            }

            $count = LeaveKaryawanModel::where('st_leave_id', $item['leave_id'])
                ->where('ms_karyawan_id', $karyawan_id)
                ->whereIn('leavekaryawan_status', ['APPROVED'])
                ->sum('leavekaryawan_quota_use');

            $description = $item['leave_description'];
            
            $quota = $quota - $count;

            
            $result[] = [
                'leave_id' => $item['leave_id'],
                'leave_active_start_date' => $item['leave_active_start_date'],
                'leave_active_end_date' => $item['leave_active_end_date'],
                'leave_grace_period' => $item['leave_grace_period'],
                'leave_description' => $description . " (" . ($quota) . ")",
                'base_description' => $description
            ];
        }

        $combinedResult = [];

        // Initialize an array to keep track of "base_description" data
        $baseDescriptionData = [];

        foreach ($result as $item) {
            $baseDescription = $item['base_description'];

            // Check if this "base_description" has been encountered before
            if (isset($baseDescriptionData[$baseDescription])) {
                // Accumulate the numbers inside parentheses in "leave_description"
                preg_match('/\((\d+)\)/', $item['leave_description'], $matches);
                if (!empty($matches)) {
                    $numberInParentheses = (int)$matches[1];
                    $baseDescriptionData[$baseDescription]['leave_description_accumulated'] += $numberInParentheses;
                }

                // Update "leave_active_start_date" and "leave_active_end_date" if needed
                if ($item['leave_active_start_date'] < $baseDescriptionData[$baseDescription]['leave_active_start_date']) {
                    $baseDescriptionData[$baseDescription]['leave_active_start_date'] = $item['leave_active_start_date'];
                }

                if ($item['leave_active_end_date'] > $baseDescriptionData[$baseDescription]['leave_active_end_date']) {
                    $baseDescriptionData[$baseDescription]['leave_active_end_date'] = $item['leave_active_end_date'];
                }

                $baseDescriptionData[$baseDescription]['leave_id'] =  $baseDescriptionData[$baseDescription]['leave_id'] . "-" . $item['leave_id'];
            } else {
                // Initialize data for this "base_description"
                $baseDescriptionData[$baseDescription] = [
                    'leave_id' => $item['leave_id'],
                    'leave_active_start_date' => $item['leave_active_start_date'],
                    'leave_active_end_date' => $item['leave_active_end_date'],
                    'leave_grace_period' => $item['leave_grace_period'],
                    'leave_description_accumulated' => 0, // Accumulated number in "leave_description"
                    'base_description' => $item['base_description'],
                ];

                // Accumulate the numbers inside parentheses in "leave_description"
                preg_match('/\((\d+)\)/', $item['leave_description'], $matches);
                if (!empty($matches)) {
                    $numberInParentheses = (int)$matches[1];
                    $baseDescriptionData[$baseDescription]['leave_description_accumulated'] += $numberInParentheses;
                }
            }
        }

        // Convert the accumulated data back to the desired format
        foreach ($baseDescriptionData as $baseDescriptionItem) {
            $combinedResult[] = [
                'leave_id' => strval($baseDescriptionItem['leave_id']),
                'leave_active_start_date' => $baseDescriptionItem['leave_active_start_date'],
                'leave_active_end_date' => $baseDescriptionItem['leave_active_end_date'],
                'leave_grace_period' => $baseDescriptionItem['leave_grace_period'],
                'leave_description' => $baseDescriptionItem['base_description'] . " (" . $baseDescriptionItem['leave_description_accumulated'] . ")",
                'base_description' => $baseDescriptionItem['base_description'],
                'quota' => intval($baseDescriptionItem['leave_description_accumulated'])
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Cuti.',
            'data' => $combinedResult
        ], 200);
    }

    public function selectEmployee(Request $request)
    {
        $q = $request->get('q');
        $karyawan_id = $request->get('karyawan_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('karyawan_data')['wajibpajak_id'],
            'karyawan_manager_id' => session()->get('karyawan_data')['karyawan_id'],
        ];
        // if($is_manager == '1') {
        //     $where['karyawan_ismanager'] = 1;
        //     $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        //     ->when($q, function ($query, $q) {
        //         return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        //     })
        //     ->where($where)
            
        //     ->limit($limit)->get();
        // } else {
        $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        ->when($q, function ($query, $q) {
            return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        })
        ->when($karyawan_id, function($query, $karyawan_id) {
            return $query->whereNotIn('karyawan_id', [$karyawan_id]);
        })
        ->where($where)
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function leaveManager(Request $request)
    {
        $query = LeaveKaryawanModel::query();
        $startDate = $request->input('start_date');
        $status = $request->input('status');
        $endDate = $request->input('end_date');
        $karyawan = $request->input('karyawan');
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;

        if ($startDate && $endDate) {
            // Apply date filtering to the query
            $endDate = $request->input('end_date');
            $endDateObj = new DateTime($endDate);
            $endDateObj->modify('+1 day');
            $endDate = $endDateObj->format('Y-m-d');
            $formattedStartDate = date('Y-m-d', strtotime($startDate));

            $query->whereBetween('leavekaryawan_request_date', [$formattedStartDate, $endDate]);
        }

        if($status && $status !== '' && $status !== null) {
            $query->where('leavekaryawan_status', $status);
        }

        if($karyawan && count($karyawan) > 0 && $karyawan !== null) {
            $query->whereIn('ms_karyawan_id', $karyawan);
        }

        $query->select(
            'ms_karyawan.karyawan_id',
            'ms_karyawan.karyawan_name',
            'tr_leave_karyawan.leavekaryawan_approval_date',
            'tr_leave_karyawan.ms_karyawan_id',
            'tr_leave_karyawan.leavekaryawan_approval_note',
            'tr_leave_karyawan.leavekaryawan_end_date',
            'tr_leave_karyawan.leavekaryawan_half_leave_status',
            'tr_leave_karyawan.leavekaryawan_id',
            'tr_leave_karyawan.leavekaryawan_manager_id',
            'tr_leave_karyawan.leavekaryawan_quota_use',
            'tr_leave_karyawan.leavekaryawan_request_date',
            'tr_leave_karyawan.leavekaryawan_request_note',
            'tr_leave_karyawan.leavekaryawan_start_date',
            'tr_leave_karyawan.leavekaryawan_status',
            'tr_leave_karyawan.st_leave_id',
            'tr_leave_karyawan.leavekaryawan_cancel_note',
            'tr_leave_karyawan.leavekaryawan_approval_by'
        );

        // Join with ms_karyawan to retrieve karyawan_name
        $query->join('ms_karyawan', 'tr_leave_karyawan.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id');

        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

        if(!isset($getKaryawan)) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data Cuti.',
                'data' => [],
                'draw' => $request->input('draw'),
                'recordsFiltered' => 0,
                'recordsTotal' => 0,
            ], 200);
        }

        $karyawan_id = $getKaryawan->karyawan_id;

        $leaveData = $query
            ->where('tr_leave_karyawan.leavekaryawan_manager_id', $karyawan_id)
            ->where('leavekaryawan_is_hide', false)
            ->with(['st_leave' => function ($query) {
                $query->select('leave_id', 'leave_description'); // Select only the desired columns
            }])
            ->orderBy('leavekaryawan_request_date', 'ASC')
            ->paginate($perPage, ['*'], 'page', $page);

        foreach ($leaveData as $leaveItem) {
            $data = $leaveItem->leavekaryawan_approval_by;

            // Split the data by comma
            if($data !== null) {
                $dataParts = explode(",", $data);

                // Check the category and get the number
                $category = $dataParts[0];
                $number = $dataParts[1];
    
                if($category === 'KARYAWAN') {
                    $getUser = KaryawanModel::where('karyawan_id', $number)->first();
                    $leaveItem->approval_name = $getUser->karyawan_name;
                } else {
                    $getUser = MrUserWajibPajakModel::where('ms_user_id', $number)->first();
                    $leaveItem->approval_name = $getUser->userwajibpajak_name;
                }
            } else {
                $leaveItem->approval_name = '';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Cuti.',
            'data' => $leaveData->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $leaveData->lastPage(),
            'recordsTotal' => $leaveData->total()
        ], 200);
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

            if($request->input('isEdit') === false) {
                DB::beginTransaction();
                try {
                    $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

                    $karyawan_id = $getKaryawan->karyawan_id;
                    $manager_id = $getKaryawan->karyawan_manager_id;
                    
                    if($manager_id == 0) {
                        $manager_id = null;
                    }

                    if($manager_id == 0) {
                        $manager_id = null;
                    }

                    if($manager_id == 0) {
                        $manager_id = null;
                    }

                    if(is_string($request->input('st_leave_id')) && strpos($request->input('st_leave_id'), "-")) {
                        $numbers = explode("-", $request->input('st_leave_id')); // Convert the string to an array

                        // Remove any leading/trailing whitespaces and convert each element to an integer
                        $numbers = array_map('intval', $numbers);

                        $a = min($numbers);

                        $count = LeaveKaryawanModel::where('st_leave_id', $a)
                            ->where('ms_karyawan_id', $karyawan_id)
                            ->whereIn('leavekaryawan_status', ['APPROVED'])
                            ->sum('leavekaryawan_quota_use');

                        $getLeaveSetting = SettingLeaveModel::where('leave_id', $a)->where('leave_status_active', true)->first();
                        
                        $aQuota = $getLeaveSetting->leave_quota - $count;
                        
                        if($aQuota >= $request->input('leavekaryawan_quota_use')) {
                            $createCuti = LeaveKaryawanModel::create([
                                'ms_karyawan_id' => $karyawan_id,
                                'st_leave_id' => $a,
                                'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                'leavekaryawan_request_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_request_date'))),
                                'leavekaryawan_manager_id' => $manager_id,
                                'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                'leavekaryawan_quota_use' => $aQuota,
                                'leavekaryawan_another_st_id' => null,
                                'leavekaryawan_another_quota_use' => null,
                                'leavekaryawan_is_hide' => false
                            ]);
                        } else {
                            $b = max($numbers);
                            $bQuota = $request->input('leavekaryawan_quota_use') - $aQuota;
                            
                            $createCuti = LeaveKaryawanModel::create([
                                'ms_karyawan_id' => $karyawan_id,
                                'st_leave_id' => $a,
                                'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                'leavekaryawan_request_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_request_date'))),
                                'leavekaryawan_manager_id' => $manager_id,
                                'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                'leavekaryawan_quota_use' => $aQuota,
                                'leavekaryawan_another_st_id' => $b,
                                'leavekaryawan_another_quota_use' => $bQuota,
                                'leavekaryawan_is_hide' => false
                            ]);
    
                            $createCutiB = LeaveKaryawanModel::create([
                                'ms_karyawan_id' => $karyawan_id,
                                'st_leave_id' => $b,
                                'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                'leavekaryawan_request_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_request_date'))),
                                'leavekaryawan_manager_id' => $manager_id,
                                'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                'leavekaryawan_quota_use' => $bQuota,
                                'leavekaryawan_another_st_id' => $a,
                                'leavekaryawan_another_quota_use' => $aQuota,
                                'leavekaryawan_another_id' => $createCuti->leavekaryawan_id,
                                'leavekaryawan_is_hide' => true
                            ]);
    
                            $createCuti->update([
                                'leavekaryawan_another_id' => $createCutiB->leavekaryawan_id, // Set the new value for 'leavekaryawan_another_id'
                            ]);
                        }
                    } else {
                        $createCuti = LeaveKaryawanModel::create([
                            'ms_karyawan_id' => $karyawan_id,
                            'st_leave_id' => $request->input('st_leave_id'),
                            'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                            'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                            'leavekaryawan_request_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_request_date'))),
                            'leavekaryawan_manager_id' => $manager_id,
                            'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                            'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                            'leavekaryawan_quota_use' => $request->input('leavekaryawan_quota_use'),
                            'leavekaryawan_another_quota_use' => 0,
                            'leavekaryawan_is_hide' => false
                        ]);
                    }
                    

                    //temporary hard code, it will check auto by group karyawan

                    if($manager_id !== null) {
                        $getManager = KaryawanModel::where('karyawan_id', $manager_id)->first();

                        $currentHour = date('G'); // Get the current hour in 24-hour format

                        if ($currentHour >= 0 && $currentHour < 12) {
                            $timeOfDay = 'Pagi';
                        } elseif ($currentHour >= 12 && $currentHour < 18) {
                            $timeOfDay = 'Siang';
                        } else {
                            $timeOfDay = 'Malam';
                        }

                        // Format the start_date from YYYY-MM-DD to DD Month YYYY
                        $startDate = date('d F Y', strtotime($createCuti->leavekaryawan_start_date));
                        
                        NotificationModel::create([
                            'notification_title' => 'Rekkaa - Pengajuan Cuti',
                            'notification_type' => 'EMAIL',
                            // 'notification_from' => env("MAIL_FROM_ADDRESS"),
                            'notification_from' => env("MAIL_FROM_ADDRESS"),
                            'notification_to' => $getManager->karyawan_email,
                            'ms_user_id' => $getManager->ms_user_id,
                            'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
                            'notification_view' => 'email.email-request-leave',
                            'notification_data' => json_encode([
                                'content' => [
                                    'time' => $timeOfDay,
                                    'manager_name' => $getManager->karyawan_name,
                                    'staff_name' => $getKaryawan->karyawan_name,
                                    'start_date' => $startDate,
                                    'quota_use' => $createCuti->leavekaryawan_another_id !== null ? $createCuti->leavekaryawan_quota_use + $createCuti->leavekaryawan_another_quota_use : $createCuti->leavekaryawan_quota_use,
                                    'reason' => $createCuti->leavekaryawan_request_note
                                ],
                                'st_leave_id' => $request->input('st_leave_id'),
                                'leavekaryawan_id' => $createCuti->leavekaryawan_id 
                            ])
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil melakukan Pengajuan Cuti.',
                        'data' => $createCuti
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }  
            } else {
                $checkLeave = LeaveKaryawanModel::where('leavekaryawan_id', $request->input('leavekaryawan_id'))->first();
                $getKaryawan = KaryawanModel::where('karyawan_id', $checkLeave->ms_karyawan_id)->first();
                $karyawan_id = $checkLeave->ms_karyawan_id;

                $manager_id = session()->get('karyawan_data')['karyawan_id'];

                $approval_id = 'KARYAWAN,' . $manager_id;

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
                        } else {
                            $leaveStartDate = Carbon::parse($checkLeave->leavekaryawan_start_date);

                            // Get the current date as a Carbon instance
                            $today = Carbon::today();
    
                            // Check if today is greater than or equal to leavekaryawan_start_date
                            if ($today->gte($leaveStartDate) && $checkLeave->leavekaryawan_status === 'APPROVED') {
                                $checkLeave->update([
                                    'leavekaryawan_status' => 'WAITINGCANCEL',
                                    'leavekaryawan_cancel_note' => $request->input('leavekaryawan_cancel_note'),
                                ]);
        
                                if($isDouble) {
                                    $checkLeaveDouble->update([
                                        'leavekaryawan_status' => 'WAITINGCANCEL',
                                        'leavekaryawan_cancel_note' => $request->input('leavekaryawan_cancel_note'),
                                    ]);
                                }

                                $isRequestCancel = true;
                            } else {
                                $checkLeave->update([
                                    'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                    'leavekaryawan_cancel_note' => $request->input('leavekaryawan_cancel_note'),
                                ]);
        
                                if($isDouble) {
                                    $checkLeaveDouble->update([
                                        'leavekaryawan_status' => $request->input('leavekaryawan_status'),
                                        'leavekaryawan_cancel_note' => $request->input('leavekaryawan_cancel_note'),
                                    ]);
                                }
                            }
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
                    } else {
                        $oldQuota =  intval($checkLeave->leavekaryawan_another_quota_use !== null ? $checkLeave->leavekaryawan_another_quota_use + $checkLeave->leavekaryawan_quota_use : $checkLeave->leavekaryawan_quota_use);
                        $oldStartDate = $checkLeave->leavekaryawan_start_date;
                        if(is_string($request->input('st_leave_id')) && strpos($request->input('st_leave_id'), "-")) {
                            $numbers = explode("-", $request->input('st_leave_id')); // Convert the string to an array

                            // Remove any leading/trailing whitespaces and convert each element to an integer
                            $numbers = array_map('intval', $numbers);
    
                            $a = min($numbers);
                            $b = max($numbers);

                            $count = LeaveKaryawanModel::where('st_leave_id', $a)
                                ->where('ms_karyawan_id', $karyawan_id)
                                ->whereIn('leavekaryawan_status', ['APPROVED'])
                                ->sum('leavekaryawan_quota_use');

                            $getLeaveSetting = SettingLeaveModel::where('leave_id', $a)->where('leave_status_active', true)->first();

                            $aQuota = $getLeaveSetting->leave_quota - $count;
                            $bQuota = $request->input('leavekaryawan_quota_use') - $aQuota;
                            
                            if(isset($checkLeaveDouble)) {
                                $checkLeaveDouble->update([
                                    'st_leave_id' => $b,
                                    'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                    'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                    'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                    'leavekaryawan_quota_use' => $bQuota,
                                    'leavekaryawan_another_st_id' => $a,
                                    'leavekaryawan_another_quota_use' => $aQuota,
                                    'leavekaryawan_another_id' => $checkLeave->leavekaryawan_id,
                                    'leavekaryawan_is_hide' => true
                                ]);
                            } else {
                                if($aQuota < $request->input('leavekaryawan_quota_use')) {
                                    $checkLeaveDouble = LeaveKaryawanModel::create([
                                        'ms_karyawan_id' => $karyawan_id,
                                        'st_leave_id' => $b,
                                        'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                        'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                        'leavekaryawan_request_date' => $checkLeave->leavekaryawan_request_date,
                                        'leavekaryawan_manager_id' => $manager_id,
                                        'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                        'leavekaryawan_status' => $checkLeave->leavekaryawan_status,
                                        'leavekaryawan_quota_use' => $bQuota,
                                        'leavekaryawan_another_st_id' => $a,
                                        'leavekaryawan_another_quota_use' => $aQuota,
                                        'leavekaryawan_another_id' => $checkLeave->leavekaryawan_id,
                                        'leavekaryawan_is_hide' => true
                                    ]);
                                } else {
                                    $aQuota = $request->input('leavekaryawan_quota_use');
                                }
                            }

                            $checkLeave->update([
                                'st_leave_id' => $a,
                                'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                'leavekaryawan_quota_use' => $aQuota,
                                'leavekaryawan_another_st_id' => isset($checkLeaveDouble) ? $b : null,
                                'leavekaryawan_another_quota_use' => isset($checkLeaveDouble) ? $bQuota : null,
                                'leavekaryawan_is_hide' => false,
                                'leavekaryawan_another_id' => isset($checkLeaveDouble) ? $checkLeaveDouble->leavekaryawan_id : null
                            ]);
                        } else {
                            if($isDouble && isset($checkLeaveDouble)) {
                                $checkLeaveDouble->delete();
                                $checkLeave->update([
                                    'st_leave_id' => $request->input('st_leave_id'),
                                    'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                    'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                    'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                    'leavekaryawan_quota_use' => $request->input('leavekaryawan_quota_use'),
                                    'leavekaryawan_another_st_id' => null,
                                    'leavekaryawan_another_quota_use' => null,
                                    'leavekaryawan_another_id' => null,
                                    'leavekaryawan_is_hide' => false
                                ]);
                            } else {
                                $checkLeave->update([
                                    'st_leave_id' => $request->input('st_leave_id'),
                                    'leavekaryawan_start_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_start_date'))),
                                    'leavekaryawan_end_date' => date('Y-m-d', strtotime($request->input('leavekaryawan_end_date'))),
                                    'leavekaryawan_request_note' => $request->input('leavekaryawan_request_note'),
                                    'leavekaryawan_quota_use' => $request->input('leavekaryawan_quota_use')
                                ]);
                            }
                        }    
                        
                        $currentStartDate = $checkLeave->leavekaryawan_start_date;
                        $currentQuota = intval($checkLeave->leavekaryawan_another_quota_use !== null ? $checkLeave->leavekaryawan_another_quota_use + $checkLeave->leavekaryawan_quota_use : $checkLeave->leavekaryawan_quota_use);
                    }

                    if($request->input('leavekaryawan_status') === 'APPROVED' || $request->input('leavekaryawan_status') === 'REJECTED' || $request->input('leavekaryawan_status') === 'CANCEL') {
                        $status = $request->input('leavekaryawan_status') === 'APPROVED' ? 'Disetujui' : 'Tidak Disetujui';

                        $getManager = KaryawanModel::where('karyawan_id', $checkLeave->leavekaryawan_manager_id)->first();
                        
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
                                    'ms_user_id' => $getKaryawan->ms_user_id,
                                    'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                    'notification_view' => 'email.email-approval-cancel-leave',
                                    'notification_data' => json_encode([
                                        'content' => [
                                            'time' => $timeOfDay,
                                            'manager_name' => $getManager->karyawan_name,
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
                                if($getManager && $getManager->karyawan_email !== null) {
                                    NotificationModel::create([
                                        'notification_title' => 'Rekkaa - Pembatalan Cuti',
                                        'notification_type' => 'EMAIL',
                                        'notification_from' => env("MAIL_FROM_ADDRESS"),
                                        'notification_to' => $getManager->karyawan_email,
                                        'ms_user_id' => $getManager->ms_user_id,
                                        'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
                                        'notification_view' => 'email.email-cancel-leave',
                                        'notification_data' => json_encode([
                                            'content' => [
                                                'time' => $timeOfDay,
                                                'manager_name' => $getManager->karyawan_name,
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
                        } else if($isRequestCancel === true && $request->input('leavekaryawan_status') === 'CANCEL') {
                            $status = 'Dibatalkan';
                            if($getManager && $getManager->karyawan_email !== null) {
                                NotificationModel::create([
                                    'notification_title' => 'Rekkaa - Pengajuan Pembatalan Cuti',
                                    'notification_type' => 'EMAIL',
                                    'notification_from' => env("MAIL_FROM_ADDRESS"),
                                    'notification_to' => $getManager->karyawan_email,
                                    'ms_user_id' => $getManager->ms_user_id,
                                    'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
                                    'notification_view' => 'email.email-request-cancel-leave',
                                    'notification_data' => json_encode([
                                        'content' => [
                                            'time' => $timeOfDay,
                                            'manager_name' => $getManager->karyawan_name,
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
                                    'ms_user_id' => $getKaryawan->ms_user_id,
                                    'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                    'notification_view' => 'email.email-reject-cancel-leave',
                                    'notification_data' => json_encode([
                                        'content' => [
                                            'time' => $timeOfDay,
                                            'manager_name' => $getManager->karyawan_name,
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
                                    'ms_user_id' => $getKaryawan->ms_user_id,
                                    'ms_wajibpajak_id' => $getKaryawan->ms_wajibpajak_id,
                                    'notification_view' => 'email.email-approval-leave',
                                    'notification_data' => json_encode([
                                        'content' => [
                                            'time' => $timeOfDay,
                                            'manager_name' => $getManager->karyawan_name,
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
                    } else if($oldQuota !== $currentQuota || $oldStartDate !== $currentStartDate) {
                        if($getKaryawan->karyawan_manager_id !== null) {
                            $getManager = KaryawanModel::where('karyawan_id', $manager_id)->first();

                            $currentHour = date('G'); // Get the current hour in 24-hour format
        
                            if ($currentHour >= 0 && $currentHour < 12) {
                                $timeOfDay = 'Pagi';
                            } elseif ($currentHour >= 12 && $currentHour < 18) {
                                $timeOfDay = 'Siang';
                            } else {
                                $timeOfDay = 'Malam';
                            }
        
                            // Format the start_date from YYYY-MM-DD to DD Month YYYY
                            $startDate = date('d F Y', strtotime($checkLeave->leavekaryawan_start_date));
                            
                            // insert tr_notification
                            NotificationModel::create([
                                'notification_title' => 'Rekkaa - Perubahan Pengajuan Cuti',
                                'notification_type' => 'EMAIL',
                                'notification_from' => env("MAIL_FROM_ADDRESS"),
                                'notification_to' => $getManager->karyawan_email,
                                'ms_user_id' => $getManager->ms_user_id,
                                'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
                                'notification_view' => 'email.email-update-leave',
                                'notification_data' => json_encode([
                                    'content' => [
                                        'time' => $timeOfDay,
                                        'manager_name' => $getManager->karyawan_name,
                                        'staff_name' => $getKaryawan->karyawan_name,
                                        'start_date' => $startDate,
                                        'quota_use' => $checkLeave->leavekaryawan_another_id !== null ? $checkLeave->leavekaryawan_quota_use + $checkLeave->leavekaryawan_another_quota_use : $checkLeave->leavekaryawan_quota_use,
                                    ],
                                    'st_leave_id' => $request->input('st_leave_id'),
                                    'leavekaryawan_id' => $checkLeave->leavekaryawan_id 
                                ])
                            ]);  
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
            }            
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportManager(Request $request)
    {
        try {
            $query = LeaveKaryawanModel::query();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            // Apply date filtering to the query
            $endDate = $request->input('end_date');
            $endDateObj = new DateTime($endDate);
            $endDateObj->modify('+1 day');
            $endDate = $endDateObj->format('Y-m-d');
            $formattedStartDate = date('Y-m-d', strtotime($startDate));

            $query->whereBetween('leavekaryawan_request_date', [$formattedStartDate, $endDate]);
        }

        $query->select(
            'ms_karyawan.karyawan_id',
            'ms_karyawan.karyawan_name',
            'tr_leave_karyawan.leavekaryawan_approval_date',
            'tr_leave_karyawan.leavekaryawan_approval_note',
            'tr_leave_karyawan.leavekaryawan_end_date',
            'tr_leave_karyawan.leavekaryawan_half_leave_status',
            'tr_leave_karyawan.leavekaryawan_id',
            'tr_leave_karyawan.leavekaryawan_manager_id',
            'tr_leave_karyawan.leavekaryawan_quota_use',
            'tr_leave_karyawan.leavekaryawan_request_date',
            'tr_leave_karyawan.leavekaryawan_request_note',
            'tr_leave_karyawan.leavekaryawan_start_date',
            'tr_leave_karyawan.leavekaryawan_status',
            'tr_leave_karyawan.st_leave_id',
            'tr_leave_karyawan.leavekaryawan_cancel_note',
            'tr_leave_karyawan.leavekaryawan_is_hide',
        );

        // Join with ms_karyawan to retrieve karyawan_name
        $query->join('ms_karyawan', 'tr_leave_karyawan.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id');

        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();
        $karyawan_id = $getKaryawan->karyawan_id;

        $leaveData = $query
            ->where('tr_leave_karyawan.leavekaryawan_manager_id', $karyawan_id)
            ->where('tr_leave_karyawan.leavekaryawan_is_hide', false)
            ->with(['st_leave' => function ($query) {
                $query->select('leave_id', 'leave_description'); // Select only the desired columns
            }])
            ->orderBy('leavekaryawan_request_date', 'desc')->get();


            $unixtime = time();
            $title = 'Daftar_Cuti_'.$unixtime;

            $htmlString = view('karyawan.cuti.export-manager', ['title' => $title,'cuti' => $leaveData])->render();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString);

            $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
            $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);

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
