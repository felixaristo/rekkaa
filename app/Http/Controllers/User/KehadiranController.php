<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\LeaveKaryawanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use DateTime;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class KehadiranController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $data = [
            'title' => 'Daftar Absensi Karyawan',
            'content' => 'user.kehadiran.attendance-admin',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    // public function attendanceAdmin(Request $request)
    // {
    //     // Get the WajibPajakModel data based on the wajibpajak_id
    //     $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
    //     $page = ($request->input('page')) ? intval($request->input('page')) : 1;
    //     $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
    //     if (!$wajibPajak) {
    //         return response()->json(['message' => 'Data not found'], 404);
    //     }

    //     // Retrieve all related AttendanceKaryawanModel data for the given WajibPajakModel with pagination
    //     $query = $wajibPajak->karyawans()->with(['attendanceKaryawans', 'leaveKaryawans.st_leave']);

    //     if ($request->has('karyawan')) {
    //         $query->whereIn('karyawan_id', $request->input('karyawan'));
    //     }

    //     $attendanceKaryawans = $query->get();
    //     $result = [];
    //     $data = json_decode($attendanceKaryawans, true);

    //     foreach ($data as $item) {
    //         // Extract required information
    //         $karyawan_id = $item['karyawan_id'];
    //         $karyawan_name = $item['karyawan_name'];
            
    //         // Check if the karyawan has attendance data
    //         if (!empty($item['attendance_karyawans'])) {
    //             // Loop through the attendance_karyawans array and extract required attendance information
                
    //             foreach ($item['attendance_karyawans'] as $attendanceKaryawan) {
    //                 if ($startDate && $endDate) {
    //                     $endDate = $request->input('end_date');

    //                     $endDateObj = new DateTime($endDate);

    //                     // Add one day to the $endDateObj
    //                     $endDateObj->modify('+1 day');

    //                     // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
    //                     $endDate = $endDateObj->format('Y-m-d');
    //                     $formattedStartDate = date('Y-m-d', strtotime($startDate));


    //                     $checkInDate = $attendanceKaryawan['attendancekaryawan_check_in'];
    //                     // Add the condition to check if the check_in date is within the specified range
    //                     if ($checkInDate >= $formattedStartDate && $checkInDate <= $endDate) {
    //                         $getManager = KaryawanModel::where('karyawan_id', $attendanceKaryawan['attendancekaryawan_manager_id'])->first();
    //                         // If $startDate and $endDate are not provided, include all data without any filtering
    //                         $result[] = [
    //                             'karyawan_id' => $karyawan_id,
    //                             'karyawan_name' => $karyawan_name,
    //                             'manager_name' => $getManager ? $getManager['karyawan_name'] : null,
    //                             'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
    //                             'leavekaryawan_id' => null,
    //                             'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
    //                             'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
    //                             'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
    //                             'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
    //                             'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
    //                             'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
    //                             'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
    //                             'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
    //                             'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
    //                             'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
    //                             'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
    //                             'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
    //                             'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
    //                             'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
    //                             'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
    //                             'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
    //                             'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
    //                             'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
    //                             'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
    //                             'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
    //                             'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
    //                             'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
    //                         ];
    //                     }
    //                 } else {
    //                     $getManager = KaryawanModel::where('karyawan_id', $attendanceKaryawan['attendancekaryawan_manager_id'])->first();
    //                     // If $startDate and $endDate are not provided, include all data without any filtering
    //                     $result[] = [
    //                         'karyawan_id' => $karyawan_id,
    //                         'karyawan_name' => $karyawan_name,
    //                         'manager_name' => $getManager ? $getManager['karyawan_name'] : '',
    //                         'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
    //                         'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
    //                         'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
    //                         'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
    //                         'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
    //                         'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
    //                         'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
    //                         'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
    //                         'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
    //                         'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
    //                         'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
    //                         'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
    //                         'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
    //                         'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
    //                         'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
    //                         'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
    //                         'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
    //                         'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
    //                         'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
    //                         'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
    //                         'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
    //                         'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
    //                         'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
    //                     ];
    //                 }
    //             }
    //         }

    //         // Process leaveKaryawans
    //         if (!empty($item['leave_karyawans'])) {
    //             foreach ($item['leave_karyawans'] as $leaveKaryawan) {
    //                 if($leaveKaryawan['leavekaryawan_status'] === 'APPROVED') {
    //                     // Extract leave data
    //                     $leaveStart = $leaveKaryawan['leavekaryawan_start_date'];
    //                     $leaveEnd = $leaveKaryawan['leavekaryawan_end_date'];
    //                     $leaveDescription = $leaveKaryawan['st_leave']['leave_description'];

    //                     // Filter range
    //                     $filterStart = $request->input('start_date');
    //                     $filterEnd = $request->input('end_date');

    //                     $filterStart = date('Y-m-d', strtotime($filterStart));
    //                     $filterEnd = date('Y-m-d', strtotime($filterEnd));

    //                     // Calculate overlapping date range
    //                     $overlapStart = max($leaveStart, $filterStart);
    //                     $overlapEnd = min($leaveEnd, $filterEnd);
    //                     $data3[] = [
    //                         'overlapStart' => $overlapStart,
    //                         'overlapEnd' => $overlapEnd
    //                     ];
    //                     // Check if there is an overlap
    //                     if ($overlapStart <= $overlapEnd) {
    //                         // Break down the leave record into multiple rows for each day
    //                         $currentDate = new DateTime($overlapStart);
    //                         $endDate = new DateTime($overlapEnd);

    //                         while ($currentDate <= $endDate) {
    //                             $getManager = KaryawanModel::where('karyawan_id', $leaveKaryawan['leavekaryawan_manager_id'])->first();
    //                             // If $startDate and $endDate are not provided, include all data without any filtering
    //                             $result[] = [
    //                                 'karyawan_id' => $karyawan_id,
    //                                 'karyawan_name' => $karyawan_name,
    //                                 'manager_name' => $getManager ? $getManager['karyawan_name'] : '',
    //                                 'attendancekaryawan_id' => null,
    //                                 'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
    //                                 'attendancekaryawan_check_in' => $currentDate->format('Y-m-d'),
    //                                 // Add more leave data fields as needed
    //                                 'attendancekaryawan_break_start' => '',
    //                                 'attendancekaryawan_break_end' => '',
    //                                 'attendancekaryawan_check_out' => '',
    //                                 'attendancekaryawan_check_in_note' => $leaveDescription,
    //                                 'attendancekaryawan_check_in_late' => '',
    //                                 'attendancekaryawan_check_in_location' => '',
    //                                 'attendancekaryawan_check_in_photo' => '',
    //                                 'attendancekaryawan_break_start_photo' => '',
    //                                 'attendancekaryawan_break_end_photo' => '',
    //                                 'attendancekaryawan_break_end_location' => '',
    //                                 'attendancekaryawan_break_end_late' => '',
    //                                 'attendancekaryawan_check_out_photo' => '',
    //                                 'attendancekaryawan_break_start_note' => '',
    //                                 'attendancekaryawan_break_start_early' => '',
    //                                 'attendancekaryawan_break_start_late' => '',
    //                                 'attendancekaryawan_break_end_note' => '',
    //                                 'attendancekaryawan_break_start_location' => '',
    //                                 'attendancekaryawan_check_out_note' => '',
    //                                 'attendancekaryawan_check_out_location' => '',
    //                                 'attendancekaryawan_check_out_early' => '',
    //                                 'attendancekaryawan_check_in_late_note' => '',
    //                             ];

    //                             $currentDate->modify('+1 day');
    //                         }
    //                     }
    //                 }
                  
    //             }
    //         }
    //     }

    //     usort($result, function($a, $b) {
    //         return strtotime($a['attendancekaryawan_check_in']) - strtotime($b['attendancekaryawan_check_in']);
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
    //         'message' => 'Berhasil memuat data Kehadiran.',
    //         'data' => $paginatedData->items(),
    //         'draw' => $request->input('draw'),
    //         'recordsFiltered' => $paginatedData->total(),
    //         'recordsTotal' => $paginatedData->total(),
    //     ], 200);
    // }

    public function attendanceAdmin(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_ids = $request->input('karyawan');
        $startDate = ($request->input('start_date')) ? $request->input('start_date') : date('Y-m-d');
        $endDate = ($request->input('end_date')) ? $request->input('end_date') : date('Y-m-d');
        $escapeStartDate = DB::connection()->getPdo()->quote($startDate);
        $escapeEndDate = DB::connection()->getPdo()->quote($endDate);

        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $order_columns = ['karyawan_name','attendancekaryawan_check_in','attendancekaryawan_check_in','attendancekaryawan_break_start','attendancekaryawan_break_end','attendancekaryawan_check_out','attendancekaryawan_check_in_note','manager_name'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [];
        $list = AttendanceKaryawanModel::select('tr_attendance_karyawan.*', 'ms_karyawan.karyawan_id', 'ms_karyawan.karyawan_name', 'ms_karyawan.st_leave_id as leavekaryawan_id', 'manager.karyawan_name as manager_name')
        ->join('ms_karyawan', 'ms_karyawan.karyawan_id', 'tr_attendance_karyawan.ms_karyawan_id')
        ->leftJoin('ms_karyawan as manager', 'manager.karyawan_id', 'ms_karyawan.karyawan_manager_id')
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            $q->whereIn('ms_karyawan.karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereRaw(DB::raw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))
        ->whereRaw(DB::raw("ms_karyawan_id IN (SELECT karyawan_id FROM ms_karyawan WHERE karyawan_active='1' AND ms_wajibpajak_id = {$wajibpajak_id})"))
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = AttendanceKaryawanModel::
        join('ms_karyawan', 'ms_karyawan.karyawan_id', 'tr_attendance_karyawan.ms_karyawan_id')
        ->leftJoin('ms_karyawan as manager', 'manager.karyawan_id', 'ms_karyawan.karyawan_manager_id')
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            $q->whereIn('ms_karyawan.karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereRaw(DB::raw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') BETWEEN {$escapeStartDate} AND {$escapeEndDate}"))
        ->whereRaw(DB::raw("ms_karyawan_id IN (SELECT karyawan_id FROM ms_karyawan WHERE karyawan_active='1' AND ms_wajibpajak_id = {$wajibpajak_id})"))
        ->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function editAttendance(Request $request)
    {
        try {
            DB::beginTransaction();
            $message = '';

            $getAttendance = AttendanceKaryawanModel::find($request->input('attendancekaryawan_id'));

            $checkAttendance = SettingAttendanceModel::find($getAttendance['st_attendance_id']);

            $date = null;

            if($request->input('attendancekaryawan_type') === 'CHECKIN') {
                if($getAttendance['attendancekaryawan_check_in'] !== null) {
                    $date = date('Y-m-d', strtotime($getAttendance['attendancekaryawan_check_in']));
                }
                
                $getAttendance->update([
                    'attendancekaryawan_check_in' => $request->input('attendancekaryawan_check_in') === '-' ? null : $date . ' ' . $request->input('attendancekaryawan_check_in'),
                    'attendancekaryawan_check_in_note' => $request->input('attendancekaryawan_check_in_note') === '-' ? null : $request->input('attendancekaryawan_check_in_note'),
                    'attendancekaryawan_check_in_location' => $request->input('attendancekaryawan_check_in_location') === '-' ? null : $request->input('attendancekaryawan_check_in_location'),
                ]);

                if($request->input('attendancekaryawan_check_in') !== '-') {
                    $statusAttendance = '';
                    if ($checkAttendance['attendance_check_in'] < $request->input('attendancekaryawan_check_in')) {
                        if (!is_null($checkAttendance['attendance_check_in_tolerance'])) {
                            $addTolerance = strtotime($checkAttendance['attendance_check_in']) + strtotime($checkAttendance['attendance_check_in_tolerance']) - strtotime('00:00:00');
                            $tolerance = date("H:i:s", $addTolerance);
                            if($tolerance < $request->input('attendancekaryawan_check_in')) {
                                $timeA = DateTime::createFromFormat('H:i:s', $checkAttendance['attendance_check_in']);
                                $timeB = DateTime::createFromFormat('H:i', $request->input('attendancekaryawan_check_in'));
                                
                                $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
        
                                // Calculate the difference between the two times
                                $interval = $timeB->diff($timeA);
                                
                
                                // Get the difference in minutes and format the result
                                $statusAttendance = $statusAttendance . $interval->format('%H:%I');
                            } else {
                                $statusAttendance = null;
                            }
                        } else {
                            $timeA = DateTime::createFromFormat('H:i:s', $checkAttendance['attendance_check_in']);
                            $timeB = DateTime::createFromFormat('H:i', $request->input('attendancekaryawan_check_in'));
                            
                            $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                            // Calculate the difference between the two times
                            $interval = $timeB->diff($timeA);
                            
            
                            // Get the difference in minutes and format the result
                            $statusAttendance = $statusAttendance . $interval->format('%H:%I');
                        }
                    } else {
                        $statusAttendance = null;
                    }
                    
                    $getAttendance->update([
                        'attendancekaryawan_check_in_late' => $statusAttendance !== '' ? $statusAttendance : null
                    ]);
                }

                $message = 'Berhasil melakukan update Absen Masuk Karyawan';
            } else if($request->input('attendancekaryawan_type') === 'STARTBREAK') {
                if($getAttendance['attendancekaryawan_break_start'] !== null) {
                    $date = date('Y-m-d', strtotime($getAttendance['attendancekaryawan_break_start']));
                }

                $getAttendance->update([
                    'attendancekaryawan_break_start' => $request->input('attendancekaryawan_break_start') === '-' ? null : $date . ' ' . $request->input('attendancekaryawan_break_start'),
                    'attendancekaryawan_break_start_note' => $request->input('attendancekaryawan_break_start_note') === '-' ? null : $request->input('attendancekaryawan_break_start_note'),
                    'attendancekaryawan_break_start_location' => $request->input('attendancekaryawan_break_start_location') === '-' ? null : $request->input('attendancekaryawan_break_start_location'),
                ]);

                if($request->input('attendancekaryawan_break_start') !== '-') {
                    $x = Carbon::createFromTimeString($checkAttendance['attendance_start_break']);
                    $y = Carbon::createFromTimeString($request->input('attendancekaryawan_break_start'));
    
                    $timeA = DateTime::createFromFormat('H:i:s', $checkAttendance['attendance_start_break']);
                    $timeB = DateTime::createFromFormat('H:i', $request->input('attendancekaryawan_break_start'));
    
                    $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                    // Calculate the difference between the two times
                    $interval = $timeB->diff($timeA);
    
                    // Get the difference in minutes and format the result
                    $differenceTime = $interval->format('%H:%I');
    
                    if($y->greaterThan($x)) {
                        $getAttendance->update([
                            'attendancekaryawan_break_start_late' => $differenceTime
                        ]);
                    } else {
                        $getAttendance->update([
                            'attendancekaryawan_break_start_early' => $differenceTime
                        ]);
                    }
                }
                
                $message = 'Berhasil melakukan update Absen Mulai Istirahat Karyawan';
            } else if($request->input('attendancekaryawan_type') === 'ENDBREAK') {
                if($getAttendance['attendancekaryawan_break_end'] !== null) {
                    $date = date('Y-m-d', strtotime($getAttendance['attendancekaryawan_break_end']));
                }

                $getAttendance->update([
                    'attendancekaryawan_break_end' => $request->input('attendancekaryawan_break_end') === '-' ? null : $date . ' ' . $request->input('attendancekaryawan_break_end'),
                    'attendancekaryawan_break_end_note' => $request->input('attendancekaryawan_break_end_note') === '-' ? null : $request->input('attendancekaryawan_break_end_note'),
                    'attendancekaryawan_break_end_location' => $request->input('attendancekaryawan_break_end_location') === '-' ? null : $request->input('attendancekaryawan_break_end_location'),
                ]);

                if($request->input('attendancekaryawan_break_end') !== '-') {
                    if($checkAttendance['attendance_break_type'] === 'SPECIFIC') {
                        $x = Carbon::createFromTimeString($checkAttendance['attendance_end_break']);
                        $y = Carbon::createFromTimeString($request->input('attendancekaryawan_break_end'));
        
                        $timeA = DateTime::createFromFormat('H:i:s', $checkAttendance['attendance_end_break']);
                        $timeB = DateTime::createFromFormat('H:i', $request->input('attendancekaryawan_break_end'));
        
                        $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
        
                        // Calculate the difference between the two times
                        $interval = $timeB->diff($timeA);
        
                        // Get the difference in minutes and format the result
                        $differenceTime = $interval->format('%H:%I');
        
                        if ($x->lessThan($y)) {
                            $getAttendance->update([
                                'attendancekaryawan_break_end_late' => $differenceTime
                            ]);
                        }
                    } else if($checkAttendance['attendance_break_type'] === 'DURATION') {
                        // Function to convert time in H:i format to minutes
                        function timeToMinutes($time) {
                            $timeParts = explode(':', $time);
                            return intval($timeParts[0]) * 60 + intval($timeParts[1]);
                        }
    
                        // Function to subtract two time strings in H:i format and return the difference in minutes
                        function subtractTime($time1, $time2) {
                            return timeToMinutes($time1) - timeToMinutes($time2);
                        }
    
                        $duration = subtractTime($checkAttendance['attendance_break_end'], $checkAttendance['attendance_break_start']);
                        $currentDuration = subtractTime($request->input('attendancekaryawan_break_end'), $getAttendance['attendancekaryawan_break_start']);
    
                        if ($currentDuration > $duration) {
                            $lateDuration = $currentDuration - $duration;
                        
                            $getAttendance->update([
                                'attendancekaryawan_break_end_late' => $lateDuration
                            ]);
                        }
                    }
                }
                
                $message = 'Berhasil melakukan update Absen Selesai Istirahat Karyawan';
            } else if($request->input('attendancekaryawan_type') === 'CHECKOUT') {
                if($getAttendance['attendancekaryawan_check_out'] !== null) {
                    $date = date('Y-m-d', strtotime($getAttendance['attendancekaryawan_check_out']));
                }

                $getAttendance->update([
                    'attendancekaryawan_check_out' => $request->input('attendancekaryawan_check_out') === '-' ? null : $date . ' ' . $request->input('attendancekaryawan_check_out'),
                    'attendancekaryawan_check_out_note' => $request->input('attendancekaryawan_check_out_note') === '-' ? null : $request->input('attendancekaryawan_check_out_note'),
                    'attendancekaryawan_check_out_location' => $request->input('attendancekaryawan_check_out_location') === '-' ? null : $request->input('attendancekaryawan_check_out_location'),
                ]);

                if($request->input('attendancekaryawan_check_out') !== '-') {
                    
                    // Convert testA to seconds since midnight
                    list($hoursA, $minutesA) = explode(":", $request->input('attendancekaryawan_check_out'));
                    $secondsA = ($hoursA * 3600) + ($minutesA * 60);

                    // Convert testB to seconds since midnight
                    list($hoursB, $minutesB, $secondsB) = explode(":", $checkAttendance['attendance_check_out']);
                    $secondsB = ($hoursB * 3600) + ($minutesB * 60) + $secondsB;

                    // Compare the values
                    if ($secondsA < $secondsB) {
                        $timeA = DateTime::createFromFormat('H:i:s', $checkAttendance['attendance_check_out']);
                        $timeB = DateTime::createFromFormat('H:i', $request->input('attendancekaryawan_check_out'));
    
                        $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                        // Calculate the difference between the two times
                        $interval = $timeB->diff($timeA);
    
                        // Get the difference in minutes and format the result
                        $differenceTime = $interval->format('%H:%I');

                        $getAttendance->update([
                            'attendancekaryawan_check_out_early' => $differenceTime
                        ]);
                    }

                    
                }
                
                $message = 'Berhasil melakukan update Absen Keluar Karyawan';
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $getAttendance
            ], 200);      
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function export(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable',
                'end_date' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Get the WajibPajakModel data based on the wajibpajak_id
            $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
            if (!$wajibPajak) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            // Retrieve all related AttendanceKaryawanModel data for the given WajibPajakModel with pagination
            $query = $wajibPajak->karyawans()->with(['attendanceKaryawans', 'leaveKaryawans.st_leave']);

            if ($request->has('karyawan')) {
                $query->whereIn('karyawan_id', $request->input('karyawan'));
            }

            $listKaryawan = '';

            $attendanceKaryawans = $query->get();
            $kehadiran = [];
            $data = json_decode($attendanceKaryawans, true);

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_id'];
                $karyawan_name = $item['karyawan_name'];

                if($listKaryawan === '') {
                    $listKaryawan = $listKaryawan . $karyawan_name;
                } else {
                    $listKaryawan = $listKaryawan . ', ' . $karyawan_name;
                }
                
                // Check if the karyawan has attendance data
                if (!empty($item['attendance_karyawans'])) {
                    

                    // Loop through the attendance_karyawans array and extract required attendance information
                    foreach ($item['attendance_karyawans'] as $attendanceKaryawan) {
                       

                        if ($startDate && $endDate) {
                            $endDate = $request->input('end_date');

                            $endDateObj = new DateTime($endDate);

                            // Add one day to the $endDateObj
                            $endDateObj->modify('+1 day');

                            // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
                            $endDate = $endDateObj->format('Y-m-d');
                            $formattedStartDate = date('Y-m-d', strtotime($startDate));

                            $checkInDate = $attendanceKaryawan['attendancekaryawan_check_in'];
                            // Add the condition to check if the check_in date is within the specified range
                            if ($checkInDate >= $formattedStartDate && $checkInDate <= $endDate) {
                                $kehadiran[] = [
                                'karyawan_id' => $karyawan_id,
                                'karyawan_name' => $karyawan_name,
                                'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                                'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                                'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                                'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                                'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                                'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                                'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                                'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                                'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                                'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                                'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                                'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                                'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                                'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                                ];
                            }
                        } else {
                            // If $startDate and $endDate are not provided, include all data without any filtering
                            $kehadiran[] = [
                                'karyawan_id' => $karyawan_id,
                                'karyawan_name' => $karyawan_name,
                                'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                                'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                                'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                                'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                                'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                                'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                                'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                                'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                                'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                                'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                                'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                                'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                                'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                                'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                            ];
                        }
                    }
                }

                if (!empty($item['leave_karyawans'])) {
                    foreach ($item['leave_karyawans'] as $leaveKaryawan) {
                        if($leaveKaryawan['leavekaryawan_status'] === 'APPROVED') {
                            // Extract leave data
                            $leaveStart = $leaveKaryawan['leavekaryawan_start_date'];
                            $leaveEnd = $leaveKaryawan['leavekaryawan_end_date'];
                            $leaveDescription = $leaveKaryawan['st_leave']['leave_description'];
    
                            // Filter range
                            $filterStart = $request->input('start_date');
                            $filterEnd = $request->input('end_date');
    
                            $filterStart = date('Y-m-d', strtotime($filterStart));
                            $filterEnd = date('Y-m-d', strtotime($filterEnd));
    
                            // Calculate overlapping date range
                            $overlapStart = max($leaveStart, $filterStart);
                            $overlapEnd = min($leaveEnd, $filterEnd);
               
                            // Check if there is an overlap
                            if ($overlapStart <= $overlapEnd) {
                                // Break down the leave record into multiple rows for each day
                                $currentDate = new DateTime($overlapStart);
                                $endDate = new DateTime($overlapEnd);
    
                                while ($currentDate <= $endDate) {
                                    $kehadiran[] = [
                                        'karyawan_id' => $karyawan_id,
                                        'karyawan_name' => $karyawan_name,
                                        'attendancekaryawan_check_in' => $currentDate->format('Y-m-d'),
                                        'attendancekaryawan_break_start' => '',
                                        'attendancekaryawan_break_end' => '',
                                        'attendancekaryawan_check_out' => '',
                                        'attendancekaryawan_check_in_note' => $leaveDescription,
                                        'attendancekaryawan_check_in_late' => '',
                                        'attendancekaryawan_break_end_late' => '',
                                        'attendancekaryawan_break_start_early' => '',
                                        'attendancekaryawan_break_start_late' => '',
                                        'attendancekaryawan_check_out_early' => '',
                                        'attendancekaryawan_check_in_late_note' => '',
                                        'attendancekaryawan_break_start_note' => '',
                                        'attendancekaryawan_break_end_note' => '',
                                        'attendancekaryawan_check_out_note' => '',
                                    ];
    
                                    $currentDate->modify('+1 day');
                                }
                            }
                        }
                      
                    }
                }
            }

            usort($kehadiran, function($a, $b) {
                return strtotime($a['attendancekaryawan_check_in']) - strtotime($b['attendancekaryawan_check_in']);
            });

            $unixtime = time();
            $title = 'Daftar_Kehadiran_'.$unixtime;


            $startDate = new DateTime($request->input('start_date'));
            $endDate = new DateTime($request->input('end_date'));


            // Set the locale to Indonesian
            setlocale(LC_TIME, 'id_ID.utf8');

            // Format the DateTime objects into the desired format with Indonesian month names
            $startDateFormatted = strftime('%d %B %Y', $startDate->getTimestamp());
            $endDateFormatted = strftime('%d %B %Y', $endDate->getTimestamp());

            // Create the $periode string
            $periode = $startDateFormatted . ' - ' . $endDateFormatted;

            if($listKaryawan === '') {
                $listKaryawan = 'Semua';
            }

            $htmlString = view('user.kehadiran.export', ['title' => $title,'kehadiran' => $kehadiran, 'periode' => $periode, 'listKaryawan' => $listKaryawan])->render();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString);

            foreach (range('A', $spreadsheet->getActiveSheet()->getHighestColumn()) as $column) {
                $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
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

    public function importTemplate(Request $request)
    {
        try {
            $karyawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', '1')
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->get();

            // $karyawan = $query->get();
            $data = json_decode($karyawan, true);
            $kehadiran = [];

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_enid'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }

            $unixtime = time();
            $title = 'Templat_Impor_Absensi_' . $unixtime;

            $excelFilePath = public_path('assets/import/Template_Import_Absensi.xlsx'); // Update the file name and path as needed
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

            $htmlString = view('user.kehadiran.import-karyawan', ['title' => 'Referensi Data Karyawan', 'kehadiran' => $kehadiran])->render();
            $dom = new DOMDocument();
            $dom->loadHTML($htmlString);
            $table = $dom->getElementsByTagName('table')->item(0);

            $rowIndex = 1;
            foreach ($table->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetEmployee->getStyleByColumnAndRow($cellIndex, $rowIndex)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex === 1) {
                    $rowIndex++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex++;
            }

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Absensi');
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Tanggal*');
            $sheetTemplate->setCellValue('C1', 'Jam Masuk*');
            $sheetTemplate->setCellValue('D1', 'Jam Mulai Istirahat');
            $sheetTemplate->setCellValue('E1', 'Jam Selesai Istirahat');
            $sheetTemplate->setCellValue('F1', 'Jam Keluar*');
            $sheetTemplate->setCellValue('G1', 'Catatan Telat Masuk');
            $sheetTemplate->setCellValue('H1', 'Catatan Keluar Lebih Dulu');
            $sheetTemplate->setCellValue('I1', 'Catatan Jam Mulai Istirahat Lebih / Kurang');
            $sheetTemplate->setCellValue('J1', 'Catatan Jam Selesai Istirahat Lebih Lama');
            $sheetTemplate->setCellValue('K1', 'Catatan Tidak Dikantor');

            for ($col = 'A'; $col <= 'K'; $col++) {
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

    public function generateHistory(Request $request) 
    {
        try {
            $id = $request->input('id');
            // Get the WajibPajakModel data based on the wajibpajak_id
            $karyawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->where('karyawan_active', '1')
            ->where('karyawan_status', '!=', 'NONKARYAWAN')
            ->orderBy('karyawan_enid', 'ASC')
            ->get();
            
            $data = json_decode($karyawan, true);
            $kehadiran = [];

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_enid'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }
            
            $excelFilePath = public_path('assets/import/Template_Import_Absensi.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Absensi_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $sheetEmployee = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Karyawan');
            $spreadsheet->addSheet($sheetEmployee);

            $htmlString = view('user.kehadiran.import-karyawan', ['title' => 'Referensi Data Karyawan', 'kehadiran' => $kehadiran])->render();
            $dom = new DOMDocument();
            $dom->loadHTML($htmlString);
            $table = $dom->getElementsByTagName('table')->item(0);

            $rowIndex = 1;
            foreach ($table->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetEmployee->getStyleByColumnAndRow($cellIndex, $rowIndex)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex === 1) {
                    $rowIndex++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetEmployee->setCellValueByColumnAndRow($cellIndex, $rowIndex, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex++;
            }

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

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Absensi');
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Tanggal*');
            $sheetTemplate->setCellValue('C1', 'Jam Masuk*');
            $sheetTemplate->setCellValue('D1', 'Jam Mulai Istirahat');
            $sheetTemplate->setCellValue('E1', 'Jam Selesai Istirahat');
            $sheetTemplate->setCellValue('F1', 'Jam Keluar*');
            $sheetTemplate->setCellValue('G1', 'Catatan Telat Masuk');
            $sheetTemplate->setCellValue('H1', 'Catatan Keluar Lebih Dulu');
            $sheetTemplate->setCellValue('I1', 'Catatan Jam Mulai Istirahat Lebih / Kurang');
            $sheetTemplate->setCellValue('J1', 'Catatan Jam Selesai Istirahat Lebih Lama');
            $sheetTemplate->setCellValue('K1', 'Catatan Tidak Dikantor');
            $sheetTemplate->setCellValue('L1', 'Status');
            $sheetTemplate->setCellValue('M1', 'Catatan');

            for ($col = 'A'; $col <= 'M'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);
           
            function getProperty($item, $property, $format = null) {
                if (isset($item->{$property})) {
                    $value = $item->{$property};
            
                    // Apply formatting if provided
                    if ($format && is_string($value) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                        $dateTime = new DateTime($value);
                        return $dateTime->format($format);
                    }
            
                    return $value;
                } else {
                    return '';
                }
            }

            $rowIndex = 2;  

            foreach($content as $item) {
                $listabsen = [
                    'A' => isset($item->karyawan_id) ? $item->karyawan_id : '',
                    'B' => isset($item->attendancekaryawan_date) ? $item->attendancekaryawan_date : '',
                    'C' => getProperty($item, 'attendancekaryawan_check_in', 'H:i'),
                    'D' => getProperty($item, 'attendancekaryawan_break_start', 'H:i'),
                    'E' => getProperty($item, 'attendancekaryawan_break_end', 'H:i'),
                    'F' => getProperty($item, 'attendancekaryawan_check_out', 'H:i'),
                    'G' => is_object($item) && property_exists($item, 'attendancekaryawan_check_in_late_note') ? $item->attendancekaryawan_check_in_late_note : '',
                    'H' => is_object($item) && property_exists($item, 'attendancekaryawan_check_out_note') ? $item->attendancekaryawan_check_out_note : '',
                    'I' => is_object($item) && property_exists($item, 'attendancekaryawan_break_start_note') ? $item->attendancekaryawan_break_start_note : '',
                    'J' => is_object($item) && property_exists($item, 'attendancekaryawan_break_end_note') ? $item->attendancekaryawan_break_end_note : '',
                    'K' => is_object($item) && property_exists($item, 'attendancekaryawan_check_in_note') ? $item->attendancekaryawan_check_in_note : '',
                    'L' => isset($item->status) ? $item->status : 'Sukses',
                    'M' => isset($item->remark) ? $item->remark : '-',
                ];

                foreach ($listabsen as $column => $value) {
                    $sheetTemplate->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
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
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'ATTENDANCE')
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

    public function importAbsen(Request $request)
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
        $sheet = $spreadsheet->getSheetByName('Impor Absensi');
        $maxRows = $sheet->getHighestRow();

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 1; // mulai baris ke 3;

        $result = [];
        
        function decimalToTime($decimalTime) {
            if (is_float($decimalTime) || is_double($decimalTime)) {
                $seconds = $decimalTime * 86400; // 86400 seconds in a day
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;
            
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            } else {
                return $decimalTime . ":00";
            }
        }

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
                $columnA = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $cellValue = $sheet->getCell("B" . $nbi)->getValue();

                if($columnA === null) {
                    if($cellValue === null) {
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

                // $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                // // Format the timestamp as "DD MMMM YYYY"
                // $dateString = date("d F Y", $timestamp); // "F" gives you the full month name


                $columnB = $cellValue;
                $columnC = $sheet->getCell("C" . $nbi)->getValue() !== null ? decimalToTime($sheet->getCell("C" . $nbi)->getValue()) : null; // Use $sheet instead of $worksheetData
                $columnD = $sheet->getCell("D" . $nbi)->getValue() !== null ? decimalToTime($sheet->getCell("D" . $nbi)->getValue()) : null; // Use $sheet instead of $worksheetData
                $columnE = $sheet->getCell("E" . $nbi)->getValue() !== null ? decimalToTime($sheet->getCell("E" . $nbi)->getValue()) : null; // Use $sheet instead of $worksheetData
                $columnF = $sheet->getCell("F" . $nbi)->getValue() !== null ? decimalToTime($sheet->getCell("F" . $nbi)->getValue()) : null; // Use $sheet instead of $worksheetData
                $columnG = $sheet->getCell("G" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnH = $sheet->getCell("H" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnI = $sheet->getCell("I" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnJ = $sheet->getCell("J" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnK = $sheet->getCell("K" . $nbi)->getValue(); // Use $sheet instead of $worksheetData

                $result[] = [
                    "karyawan_id" => $columnA,
                    "attendancekaryawan_date" => $columnB,
                    "attendancekaryawan_check_in" => $columnC,
                    "attendancekaryawan_break_start" => $columnD,
                    "attendancekaryawan_break_end" => $columnE,
                    "attendancekaryawan_check_out" => $columnF,
                    "attendancekaryawan_check_in_late_note" => $columnG,
                    "attendancekaryawan_check_out_note" => $columnH,
                    "attendancekaryawan_break_start_note" => $columnI,
                    "attendancekaryawan_break_end_note" => $columnJ,
                    "attendancekaryawan_check_in_note" => $columnK,
                    'row' => $nb + 1
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

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil mengambil data Setting Kehadiran.',
        //     'data' => $result
        // ], 200);

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

        DB::beginTransaction();
        try {
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

                
                if($checkKaryawan['st_attendance_id'] === null) {
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Karyawan belum didaftarkan di Pengaturan Absensi';
                    continue;
                }

                if(formatDate($result[$x]['attendancekaryawan_date']) === false) {
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan format Tanggal sesuai format sistem REKKAA';
                    continue;
                }

                // Format the date as YYYY-MM-DD
                $date = convertDateStringToYYYYMMDD($result[$x]['attendancekaryawan_date']);

                $getAttendance = SettingAttendanceModel::find($checkKaryawan['st_attendance_id']);

                $settingHolidays = SettingHolidayModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                    ->whereDate('holiday_start_date', '<=', $date) // Start date is less than or equal to the current date
                    ->whereDate('holiday_end_date', '>=', $date)   // End date is greater than or equal to the current date
                    ->where('holiday_status_active', true)
                    ->get();

                $dayOfWeek = date('l', strtotime($date));

                $selectedAttendanceDays = json_decode($getAttendance['attendance_working_day']);

                if (!in_array($dayOfWeek, $selectedAttendanceDays) || count($settingHolidays) > 0) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Tanggal input tidak sesuai hari kerja';
                    continue;
                } else if ($result[$x]['attendancekaryawan_check_in'] === null) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Jam Masuk tidak boleh kosong';
                    continue;
                } else if ($result[$x]['attendancekaryawan_check_out'] === null) {
                    
                    $totalFail = $totalFail + 1;
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Jam Keluar tidak boleh kosong';
                    continue;
                }

                $statusCheckIn = '';
                if ($getAttendance['attendance_check_in'] < $result[$x]['attendancekaryawan_check_in']) {
                    if (!is_null($getAttendance['attendance_check_in_tolerance'])) {
                        $addTolerance = strtotime($getAttendance['attendance_check_in']) + strtotime($getAttendance['attendance_check_in_tolerance']) - strtotime('00:00:00');
                        $tolerance = date("H:i:s", $addTolerance);
                        if($tolerance < $result[$x]['attendancekaryawan_check_in']) {
                            $timeA = DateTime::createFromFormat('H:i:s', $tolerance);
                            $timeB = DateTime::createFromFormat('H:i:s', $result[$x]['attendancekaryawan_check_in']);
                            
                            $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                            // Calculate the difference between the two times
                            $interval = $timeB->diff($timeA);
                            
            
                            // Get the difference in minutes and format the result
                            $result[$x]['attendancekaryawan_check_in_late'] = $statusCheckIn . $interval->format('%H:%I');
                        } else {
                            $result[$x]['attendancekaryawan_check_in_late'] = null;
                        }
                    } else {
                        $timeA = DateTime::createFromFormat('H:i:s', $getAttendance['attendance_check_in']);
                        $timeB = DateTime::createFromFormat('H:i:s', $result[$x]['attendancekaryawan_check_in']);
                        
                        $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                        // Calculate the difference between the two times
                        $interval = $timeB->diff($timeA);
                        
        
                        // Get the difference in minutes and format the result
                        $result[$x]['attendancekaryawan_check_in_late'] = $statusCheckIn . $interval->format('%H:%I');
                    }
                } else {
                    $result[$x]['attendancekaryawan_check_in_late'] = null;
                }

                $statusCheckOut = '';

                list($hoursA, $minutesA) = explode(":", $result[$x]['attendancekaryawan_check_out']);
                $secondsA = ($hoursA * 3600) + ($minutesA * 60);

                // Convert testB to seconds since midnight
                list($hoursB, $minutesB, $secondsB) = explode(":", $getAttendance['attendance_check_out']);
                $secondsB = ($hoursB * 3600) + ($minutesB * 60) + $secondsB;

                // Compare the values
                if ($secondsA < $secondsB) {
                    $timeA = DateTime::createFromFormat('H:i:s', $getAttendance['attendance_check_out']);
                    $timeB = DateTime::createFromFormat('H:i:s', $result[$x]['attendancekaryawan_check_out']);

                    $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                    $interval = $timeB->diff($timeA);

                    $result[$x]['attendancekaryawan_check_out_early'] = $statusCheckOut . $interval->format('%H:%I');
                } else {
                    $result[$x]['attendancekaryawan_check_out_early'] = null;
                }

                if($getAttendance['attendance_break_status'] === true) {
                    if($getAttendance['attendance_break_type'] === 'SPECIFIC') {
                        $y = Carbon::createFromTimeString($result[$x]['attendancekaryawan_break_end']);
                        $v = Carbon::createFromTimeString($getAttendance['attendance_end_break']);
        
                        $timeA = DateTime::createFromFormat('H:i:s', $getAttendance['attendance_end_break']);
                        $timeB = DateTime::createFromFormat('H:i:s', $result[$x]['attendancekaryawan_break_end']);
        
                        $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
        
                        // Calculate the difference between the two times
                        $interval = $timeB->diff($timeA);
        
                        // Get the difference in minutes and format the result
                        $differenceTime = $interval->format('%H:%I');
        
                        if ($v->lessThan($y)) {
                            $result[$x]['attendancekaryawan_break_end_late'] = $differenceTime;
                        } else {
                            $result[$x]['attendancekaryawan_break_end_late'] = null;
                        }
                        
                    } else if($getAttendance['attendance_break_type'] === 'DURATION') {
                        // Function to convert time in H:i format to minutes
                        function timeToMinutes($time) {
                            $timeParts = explode(':', $time);
                            return intval($timeParts[0]) * 60 + intval($timeParts[1]);
                        }
    
                        // Function to subtract two time strings in H:i format and return the difference in minutes
                        function subtractTime($time1, $time2) {
                            return timeToMinutes($time1) - timeToMinutes($time2);
                        }
    
                        $duration = subtractTime($getAttendance['attendance_break_end'], $getAttendance['attendance_break_start']);
                        $currentDuration = subtractTime($result[$x]['attendancekaryawan_break_end'], $result[$x]['attendancekaryawan_break_start']);
    
                        if ($currentDuration > $duration) {
                            $lateDuration = $currentDuration - $duration;
                        
                            $result[$x]['attendancekaryawan_break_end_late'] = $lateDuration;
                        } else {
                            $result[$x]['attendancekaryawan_break_end_late'] = null;
                        }
                    }
                    

                    $v = Carbon::createFromTimeString($getAttendance['attendance_start_break']);
                    $y = Carbon::createFromTimeString($result[$x]['attendancekaryawan_break_start']);
    
                    $timeA = DateTime::createFromFormat('H:i:s', $getAttendance['attendance_start_break']);
                    $timeB = DateTime::createFromFormat('H:i:s', $result[$x]['attendancekaryawan_break_start']);
    
                    $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                    // Calculate the difference between the two times
                    $interval = $timeB->diff($timeA);
    
                    // Get the difference in minutes and format the result
                    $differenceTime = $interval->format('%H:%I');
    
                    if($y->greaterThan($v)) {
                        $result[$x]['attendancekaryawan_break_start_late'] = $differenceTime;
                        $result[$x]['attendancekaryawan_break_start_early'] = null;
                    } else {
                        $result[$x]['attendancekaryawan_break_start_early'] = $differenceTime;
                        $result[$x]['attendancekaryawan_break_start_late'] = null;
                    }
                } else {
                    $result[$x]['attendancekaryawan_break_start'] = null;
                    $result[$x]['attendancekaryawan_break_end'] = null;
                    $result[$x]['attendancekaryawan_break_start_early'] = null;
                    $result[$x]['attendancekaryawan_break_start_late'] = null;
                    $result[$x]['attendancekaryawan_break_end_late'] = null;
                }

                

                $checkAttendanceKaryawanModel = AttendanceKaryawanModel::where('ms_karyawan_id',  $checkKaryawan['karyawan_id'])->whereDate('attendancekaryawan_check_in', '=', $date)->first();

                if(!$checkAttendanceKaryawanModel) {
                    $createAttendance = AttendanceKaryawanModel::create([
                        'attendancekaryawan_manager_id' => $checkKaryawan['karyawan_manager_id'],
                        'ms_karyawan_id' => $checkKaryawan['karyawan_id'],
                        'st_attendance_id' => $getAttendance['attendance_id'],
                        'attendancekaryawan_check_in' => $date . ' ' . $result[$x]['attendancekaryawan_check_in'],
                        'attendancekaryawan_check_out' => $date . ' ' . $result[$x]['attendancekaryawan_check_out'],
                        'attendancekaryawan_break_start' => $result[$x]['attendancekaryawan_break_start'] !== null ? $date . ' ' . $result[$x]['attendancekaryawan_break_start'] : null,
                        'attendancekaryawan_break_end' => $result[$x]['attendancekaryawan_break_end'] !== null ? $date . ' ' . $result[$x]['attendancekaryawan_break_end'] : null,
                        'attendancekaryawan_break_start_early' => $result[$x]['attendancekaryawan_break_start_early'],
                        'attendancekaryawan_break_start_late' => $result[$x]['attendancekaryawan_break_start_late'],
                        'attendancekaryawan_break_end_late' => $result[$x]['attendancekaryawan_break_end_late'],
                        'attendancekaryawan_check_in_late' => $result[$x]['attendancekaryawan_check_in_late'],
                        'attendancekaryawan_check_out_early' => $result[$x]['attendancekaryawan_check_out_early'],
                        'attendancekaryawan_check_out_note' => $result[$x]['attendancekaryawan_check_out_note'],
                        'attendancekaryawan_check_in_note' => $result[$x]['attendancekaryawan_check_in_note'],
                        'attendancekaryawan_check_in_late_note' => $result[$x]['attendancekaryawan_check_in_late_note'],
                        'attendancekaryawan_break_start_note' => $result[$x]['attendancekaryawan_break_start_note'],
                        'attendancekaryawan_break_end_note' => $result[$x]['attendancekaryawan_break_end_note'],
                    ]);
                } else {
                    $checkAttendanceKaryawanModel->update([
                        'ms_karyawan_id' => $checkKaryawan['karyawan_id'],
                        'st_attendance_id' => $getAttendance['attendance_id'],
                        'attendancekaryawan_check_in' => $date . ' ' . $result[$x]['attendancekaryawan_check_in'],
                        'attendancekaryawan_check_out' => $date . ' ' . $result[$x]['attendancekaryawan_check_out'],
                        'attendancekaryawan_break_start' => $result[$x]['attendancekaryawan_break_start'] !== null ? $date . ' ' . $result[$x]['attendancekaryawan_break_start'] : null,
                        'attendancekaryawan_break_end' => $result[$x]['attendancekaryawan_break_end'] !== null ? $date . ' ' . $result[$x]['attendancekaryawan_break_end'] : null,
                        'attendancekaryawan_break_start_early' => $result[$x]['attendancekaryawan_break_start_early'],
                        'attendancekaryawan_break_start_late' => $result[$x]['attendancekaryawan_break_start_late'],
                        'attendancekaryawan_break_end_late' => $result[$x]['attendancekaryawan_break_end_late'],
                        'attendancekaryawan_check_in_late' => $result[$x]['attendancekaryawan_check_in_late'],
                        'attendancekaryawan_check_out_early' => $result[$x]['attendancekaryawan_check_out_early'],
                        'attendancekaryawan_check_out_note' => $result[$x]['attendancekaryawan_check_out_note'],
                        'attendancekaryawan_check_in_note' => $result[$x]['attendancekaryawan_check_in_note'],
                        'attendancekaryawan_break_start_note' => $result[$x]['attendancekaryawan_break_start_note'],
                        'attendancekaryawan_break_end_note' => $result[$x]['attendancekaryawan_break_end_note'],
                        'attendancekaryawan_check_in_late_note' => $result[$x]['attendancekaryawan_check_in_late_note'],
                    ]);
                }

                $totalComplete = $totalComplete + 1;
                $result[$x]['status'] = 'Sukses';
                $result[$x]['remark'] = '-';
            }

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Berhasil import data absensi.',
            //     'result' => $result
            // ], 200);

            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_user_id' => session()->get('user_data')['user_id'],
                'historyimport_type' => "ATTENDANCE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
                'historyimport_file_name' => $originalFileName
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data absensi.',
                'result' => $result,
                'totalsuccess' => $totalComplete,
                'totalfail'=> $totalFail,
                'totaldata' => $totalComplete + $totalFail,
                'history' => $createHistory->historyimport_id
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function importHandler(Request $request)
    {
        $checkEmployee = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->where('karyawan_active', '1')
            ->where('karyawan_status', '!=', 'NONKARYAWAN')
            ->limit(3)
            ->get();

        $checkAttendance = SettingAttendanceModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->where('attendance_status_active', true)
            ->limit(3)
            ->get();

        if(count($checkAttendance) === 0 || count($checkEmployee) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Berhasil memuat data Kehadiran.'
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat data Kehadiran.'
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