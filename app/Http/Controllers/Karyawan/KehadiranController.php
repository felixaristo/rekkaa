<?php

namespace App\Http\Controllers\Karyawan;

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
    public function showCamera(Request $request)
    {
        $data = [
            'title' => 'Absensi',
            'content' => 'karyawan.kehadiran.absensi',
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

    public function indexEmployee(Request $request)
    {
        $data = [
            'title' => 'Riwayat Absensi',
            'content' => 'karyawan.kehadiran.attendance-karyawan',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function indexManager(Request $request)
    {
        $data = [
            'title' => 'Daftar Absensi',
            'content' => 'karyawan.kehadiran.attendance-manager',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function listJadwal(Request $request)
    {
        try {
            $page = ($request->input('page')) ? intval($request->input('page')) : 1;
            $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
           
            $getAttendance = SettingAttendanceModel::where('ms_wajibpajak_id', session()->get('karyawan_data')['wajibpajak_id'])
                ->orderBy('attendance_check_out')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Setting Kehadiran.',
                'data' => $getAttendance->items(),
                'draw' => $request->input('draw'),
                'recordsFiltered' => $getAttendance->total(),
                'recordsTotal' => $getAttendance->total(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list jadwal ' . $e->getMessage());

            return response()->json(['error' => 'Error get jadwal. Please try again later.', 'message' => $e],  500);
        }
    }

    public function attendanceEmployee(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;


        $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->with(['attendanceKaryawans', 'leaveKaryawans.st_leave'])->get();
        

        // // Implement pagination and limit per page (assuming you have 'date' column for date in your attendance table)
        // $attendanceData = $query->where('ms_karyawan_id', $karyawan_id)->orderBy('attendancekaryawan_check_in', 'desc')->paginate($perPage, ['*'], 'page', $page);
        $result = [];
        $data = json_decode($getKaryawan, true);

        foreach ($data as $item) {
            // Extract required information
            $karyawan_id = $item['karyawan_id'];
            $karyawan_name = $item['karyawan_name'];
            
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
                            $result[] = [
                                'karyawan_id' => $karyawan_id,
                                'karyawan_name' => $karyawan_name,
                                'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
                                'leavekaryawan_id' => null,
                                'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                                'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                                'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                                'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                                'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                                'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                                'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
                                'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
                                'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
                                'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
                                'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
                                'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                                'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
                                'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                                'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                                'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                                'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                                'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
                                'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                                'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
                                'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                                'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                            ];
                        }
                    } else {
                        // If $startDate and $endDate are not provided, include all data without any filtering
                        $result[] = [
                            'karyawan_id' => $karyawan_id,
                            'karyawan_name' => $karyawan_name,
                            'leavekaryawan_id' => null,
                            'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
                            'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                            'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                            'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                            'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                            'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                            'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                            'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
                            'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
                            'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
                            'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
                            'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
                            'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                            'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
                            'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                            'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                            'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                            'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                            'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
                            'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                            'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
                            'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                            'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                        ];
                    }
                }
            }

            // Process leaveKaryawans
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
                                $result[] = [
                                    'karyawan_id' => $karyawan_id,
                                    'karyawan_name' => $karyawan_name,
                                    'attendancekaryawan_id' => null,
                                    'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
                                    'attendancekaryawan_check_in' => $currentDate->format('Y-m-d'),
                                    // Add more leave data fields as needed
                                    'attendancekaryawan_break_start' => '',
                                    'attendancekaryawan_break_end' => '',
                                    'attendancekaryawan_check_out' => '',
                                    'attendancekaryawan_check_in_note' => $leaveDescription,
                                    'attendancekaryawan_check_in_late' => '',
                                    'attendancekaryawan_check_in_location' => '',
                                    'attendancekaryawan_check_in_photo' => '',
                                    'attendancekaryawan_break_start_photo' => '',
                                    'attendancekaryawan_break_end_photo' => '',
                                    'attendancekaryawan_break_end_location' => '',
                                    'attendancekaryawan_break_end_late' => '',
                                    'attendancekaryawan_check_out_photo' => '',
                                    'attendancekaryawan_break_start_note' => '',
                                    'attendancekaryawan_break_start_early' => '',
                                    'attendancekaryawan_break_start_late' => '',
                                    'attendancekaryawan_break_end_note' => '',
                                    'attendancekaryawan_break_start_location' => '',
                                    'attendancekaryawan_check_out_note' => '',
                                    'attendancekaryawan_check_out_location' => '',
                                    'attendancekaryawan_check_out_early' => '',
                                    'attendancekaryawan_check_in_late_note' => '',
                                ];

                                $currentDate->modify('+1 day');
                            }
                        }
                    }
                }
            }
        }

        usort($result, function($a, $b) {
            return strtotime($a['attendancekaryawan_check_in']) - strtotime($b['attendancekaryawan_check_in']);
        });

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($result, ($page - 1) * $perPage, $perPage),
            count($result),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Kehadiran.',
            'data' => $paginatedData->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $paginatedData->total(),
            'recordsTotal' => $paginatedData->total(),
        ], 200);
    }

    public function attendanceManager(Request $request)
    {
        $query = AttendanceKaryawanModel::query();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $karyawan = $request->input('karyawan');
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;

        if ($startDate && $endDate) {
            // Apply date filtering to the query
            $endDate = $request->input('end_date');

            $endDateObj = new DateTime($endDate);

            // Add one day to the $endDateObj
            $endDateObj->modify('+1 day');

            // Get the updated date in the format 'Y-m-d' and assign it back to $endDate
            $endDate = $endDateObj->format('Y-m-d');
            $formattedStartDate = date('Y-m-d', strtotime($startDate));

            $query->whereBetween('attendancekaryawan_check_in', [$formattedStartDate, $endDate]);
        }

        if($karyawan && count($karyawan) > 0 && $karyawan !== null) {
            $query->whereIn('ms_karyawan_id', $karyawan);
        }

        $query->select(
            'ms_karyawan.karyawan_id',
            'ms_karyawan.karyawan_name',
            'tr_attendance_karyawan.attendancekaryawan_id',
            'tr_attendance_karyawan.attendancekaryawan_check_in',
            'tr_attendance_karyawan.attendancekaryawan_break_start',
            'tr_attendance_karyawan.attendancekaryawan_break_end',
            'tr_attendance_karyawan.attendancekaryawan_check_out',
            'tr_attendance_karyawan.attendancekaryawan_check_in_note',
            'tr_attendance_karyawan.attendancekaryawan_check_in_late',
            'tr_attendance_karyawan.attendancekaryawan_manager_id',
            'tr_attendance_karyawan.attendancekaryawan_check_in_location',
            'tr_attendance_karyawan.attendancekaryawan_check_in_photo',
            'tr_attendance_karyawan.attendancekaryawan_break_start_photo',
            'tr_attendance_karyawan.attendancekaryawan_break_end_photo',
            'tr_attendance_karyawan.attendancekaryawan_break_end_location',
            'tr_attendance_karyawan.attendancekaryawan_break_end_late',
            'tr_attendance_karyawan.attendancekaryawan_check_out_photo',
            'tr_attendance_karyawan.attendancekaryawan_break_start_note',
            'tr_attendance_karyawan.attendancekaryawan_break_start_early',
            'tr_attendance_karyawan.attendancekaryawan_break_start_late',
            'tr_attendance_karyawan.attendancekaryawan_break_end_note',
            'tr_attendance_karyawan.attendancekaryawan_break_start_location',
            'tr_attendance_karyawan.attendancekaryawan_check_out_note',
            'tr_attendance_karyawan.attendancekaryawan_check_out_location',
            'tr_attendance_karyawan.attendancekaryawan_check_out_early',
            'tr_attendance_karyawan.attendancekaryawan_check_in_late_note'
        );

        if ($request->has('karyawan_name')) {
            $karyawanName = $request->input('karyawan_name');
            $query->where('karyawan_name', 'iLIKE', "%$karyawanName%");
        }

        // Join with ms_karyawan to retrieve karyawan_name
        $query->join('ms_karyawan', 'tr_attendance_karyawan.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id');

        $karyawan_id = null;

        if (!isset(session()->get('karyawan_data')['karyawan_id'])) {
            $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

            if(!isset($getKaryawan)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil memuat data Kehadiran.',
                    'data' => [],
                    'draw' => $request->input('draw'),
                    'recordsFiltered' => 0,
                    'recordsTotal' => 0,
                ], 200);
            }
            
            $karyawan_id = $getKaryawan->karyawan_id;
        } else {
            $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        }

        $getAttendance = $query
            ->where('tr_attendance_karyawan.attendancekaryawan_manager_id', $karyawan_id)
            ->orderBy('attendancekaryawan_check_in', 'desc')->get();

        $result = [];
        $dataAttendance = json_decode($getAttendance, true);

        foreach ($dataAttendance as $item) {
            $karyawan_name = $item['karyawan_name'];
            $result[] = [
                'karyawan_id' => $item['karyawan_id'],
                'karyawan_name' => $karyawan_name,
                'leavekaryawan_id' => null,
                'attendancekaryawan_id' => $item['attendancekaryawan_id'],
                'attendancekaryawan_check_in' => $item['attendancekaryawan_check_in'],
                'attendancekaryawan_break_start' => $item['attendancekaryawan_break_start'],
                'attendancekaryawan_break_end' => $item['attendancekaryawan_break_end'],
                'attendancekaryawan_check_out' => $item['attendancekaryawan_check_out'],
                'attendancekaryawan_check_in_note' => $item['attendancekaryawan_check_in_note'],
                'attendancekaryawan_check_in_late' => $item['attendancekaryawan_check_in_late'],
                'attendancekaryawan_check_in_location' => $item['attendancekaryawan_check_in_location'],
                'attendancekaryawan_check_in_photo' => $item['attendancekaryawan_check_in_photo'],
                'attendancekaryawan_break_start_photo' => $item['attendancekaryawan_break_start_photo'],
                'attendancekaryawan_break_end_photo' => $item['attendancekaryawan_break_end_photo'],
                'attendancekaryawan_break_end_location' => $item['attendancekaryawan_break_end_location'],
                'attendancekaryawan_break_end_late' => $item['attendancekaryawan_break_end_late'],
                'attendancekaryawan_check_out_photo' => $item['attendancekaryawan_check_out_photo'],
                'attendancekaryawan_break_start_note' => $item['attendancekaryawan_break_start_note'],
                'attendancekaryawan_break_start_early' => $item['attendancekaryawan_break_start_early'],
                'attendancekaryawan_break_start_late' => $item['attendancekaryawan_break_start_late'],
                'attendancekaryawan_break_end_note' => $item['attendancekaryawan_break_end_note'],
                'attendancekaryawan_break_start_location' => $item['attendancekaryawan_break_start_location'],
                'attendancekaryawan_check_out_note' => $item['attendancekaryawan_check_out_note'],
                'attendancekaryawan_check_out_location' => $item['attendancekaryawan_check_out_location'],
                'attendancekaryawan_check_out_early' => $item['attendancekaryawan_check_out_early'],
                'attendancekaryawan_check_in_late_note' => $item['attendancekaryawan_check_in_late_note'],
            ];
        }

        $dataLeave = LeaveKaryawanModel::query();
        $dataLeave->where('leavekaryawan_status', 'APPROVED');
        $dataLeave->select(
            'ms_karyawan.karyawan_id',
            'ms_karyawan.karyawan_name',
            'tr_leave_karyawan.ms_karyawan_id',
            'tr_leave_karyawan.leavekaryawan_end_date',
            'tr_leave_karyawan.leavekaryawan_id',
            'tr_leave_karyawan.leavekaryawan_manager_id',
            'tr_leave_karyawan.leavekaryawan_start_date',
            'tr_leave_karyawan.leavekaryawan_status',
            'tr_leave_karyawan.st_leave_id',
        );

        $dataLeave->join('ms_karyawan', 'tr_leave_karyawan.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id');

        if ($request->has('karyawan_name')) {
            $karyawanName = $request->input('karyawan_name');
            $dataLeave->where('karyawan_name', 'iLIKE', "%$karyawanName%");
        }

        $leaveData = $dataLeave
            ->where('tr_leave_karyawan.leavekaryawan_manager_id', $karyawan_id)
            ->with(['st_leave' => function ($dataLeave) {
                $dataLeave->select('leave_id', 'leave_description'); // Select only the desired columns
            }])
            ->orderBy('leavekaryawan_request_date', 'desc')->get();
        
        $leaveItem = json_decode($leaveData, true);

        foreach($leaveItem as $itemLeave) {
            $leaveStart = $itemLeave['leavekaryawan_start_date'];
            $leaveEnd = $itemLeave['leavekaryawan_end_date'];
            $leaveDescription = $itemLeave['st_leave']['leave_description'];

            // Filter range
            $filterStart = $request->input('start_date');
            $filterEnd = $request->input('end_date');

            $filterStart = date('Y-m-d', strtotime($filterStart));
            $filterEnd = date('Y-m-d', strtotime($filterEnd));

            // Calculate overlapping date range
            $overlapStart = max($leaveStart, $filterStart);
            $overlapEnd = min($leaveEnd, $filterEnd);

            if ($overlapStart <= $overlapEnd) {
                // Break down the leave record into multiple rows for each day
                $currentDate = new DateTime($overlapStart);
                $endDate = new DateTime($overlapEnd);

                while ($currentDate <= $endDate) {
                    $result[] = [
                        'karyawan_id' => $karyawan_id,
                        'karyawan_name' => $karyawan_name,
                        'attendancekaryawan_id' => null,
                        'leavekaryawan_id' => $itemLeave['leavekaryawan_id'],
                        'attendancekaryawan_check_in' => $currentDate->format('Y-m-d'),
                        // Add more leave data fields as needed
                        'attendancekaryawan_break_start' => '',
                        'attendancekaryawan_break_end' => '',
                        'attendancekaryawan_check_out' => '',
                        'attendancekaryawan_check_in_note' => $leaveDescription,
                        'attendancekaryawan_check_in_late' => '',
                        'attendancekaryawan_check_in_location' => '',
                        'attendancekaryawan_check_in_photo' => '',
                        'attendancekaryawan_break_start_photo' => '',
                        'attendancekaryawan_break_end_photo' => '',
                        'attendancekaryawan_break_end_location' => '',
                        'attendancekaryawan_break_end_late' => '',
                        'attendancekaryawan_check_out_photo' => '',
                        'attendancekaryawan_break_start_note' => '',
                        'attendancekaryawan_break_start_early' => '',
                        'attendancekaryawan_break_start_late' => '',
                        'attendancekaryawan_break_end_note' => '',
                        'attendancekaryawan_break_start_location' => '',
                        'attendancekaryawan_check_out_note' => '',
                        'attendancekaryawan_check_out_location' => '',
                        'attendancekaryawan_check_out_early' => '',
                        'attendancekaryawan_check_in_late_note' => '',
                    ];

                    $currentDate->modify('+1 day');
                }
            }
        }
        
        usort($result, function($a, $b) {
            return strtotime($a['attendancekaryawan_check_in']) - strtotime($b['attendancekaryawan_check_in']);
        });

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($result, ($page - 1) * $perPage, $perPage),
            count($result),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Kehadiran.',
            'data' => $paginatedData->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $paginatedData->total(),
            'recordsTotal' => $paginatedData->total(),
        ], 200);
    }

    public function attendanceAdmin(Request $request)
    {
        // Get the WajibPajakModel data based on the wajibpajak_id
        $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
        if (!$wajibPajak) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Retrieve all related AttendanceKaryawanModel data for the given WajibPajakModel with pagination
        $query = $wajibPajak->karyawans()->with(['attendanceKaryawans', 'leaveKaryawans.st_leave']);

        if ($request->has('karyawan_name')) {
            $karyawanName = $request->input('karyawan_name');
            $query->where('karyawan_name', 'iLIKE', "%$karyawanName%");
        }

        $attendanceKaryawans = $query->get();
        $result = [];
        $data = json_decode($attendanceKaryawans, true);

        foreach ($data as $item) {
            // Extract required information
            $karyawan_id = $item['karyawan_id'];
            $karyawan_name = $item['karyawan_name'];
            
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
                            $getManager = KaryawanModel::where('karyawan_id', $attendanceKaryawan['attendancekaryawan_manager_id'])->first();
                            // If $startDate and $endDate are not provided, include all data without any filtering
                            $result[] = [
                                'karyawan_id' => $karyawan_id,
                                'karyawan_name' => $karyawan_name,
                                'manager_name' => $getManager['karyawan_name'],
                                'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
                                'leavekaryawan_id' => null,
                                'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                                'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                                'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                                'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                                'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                                'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                                'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                                'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
                                'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
                                'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
                                'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
                                'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
                                'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                                'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
                                'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                                'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                                'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                                'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                                'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
                                'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                                'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
                                'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                            ];
                        }
                    } else {
                        $getManager = KaryawanModel::where('karyawan_id', $attendanceKaryawan['attendancekaryawan_manager_id'])->first();
                        // If $startDate and $endDate are not provided, include all data without any filtering
                        $result[] = [
                            'karyawan_id' => $karyawan_id,
                            'karyawan_name' => $karyawan_name,
                            'manager_name' => $getManager['karyawan_name'],
                            'attendancekaryawan_id' => $attendanceKaryawan['attendancekaryawan_id'],
                            'attendancekaryawan_check_in' => $attendanceKaryawan['attendancekaryawan_check_in'],
                            'attendancekaryawan_break_start' => $attendanceKaryawan['attendancekaryawan_break_start'],
                            'attendancekaryawan_break_end' => $attendanceKaryawan['attendancekaryawan_break_end'],
                            'attendancekaryawan_check_out' => $attendanceKaryawan['attendancekaryawan_check_out'],
                            'attendancekaryawan_check_in_note' => $attendanceKaryawan['attendancekaryawan_check_in_note'],
                            'attendancekaryawan_check_in_late' => $attendanceKaryawan['attendancekaryawan_check_in_late'],
                            'attendancekaryawan_check_in_late_note' => $attendanceKaryawan['attendancekaryawan_check_in_late_note'],
                            'attendancekaryawan_check_in_location' => $attendanceKaryawan['attendancekaryawan_check_in_location'],
                            'attendancekaryawan_check_in_photo' => $attendanceKaryawan['attendancekaryawan_check_in_photo'],
                            'attendancekaryawan_break_start_photo' => $attendanceKaryawan['attendancekaryawan_break_start_photo'],
                            'attendancekaryawan_break_end_photo' => $attendanceKaryawan['attendancekaryawan_break_end_photo'],
                            'attendancekaryawan_break_end_location' => $attendanceKaryawan['attendancekaryawan_break_end_location'],
                            'attendancekaryawan_break_end_late' => $attendanceKaryawan['attendancekaryawan_break_end_late'],
                            'attendancekaryawan_check_out_photo' => $attendanceKaryawan['attendancekaryawan_check_out_photo'],
                            'attendancekaryawan_break_start_note' => $attendanceKaryawan['attendancekaryawan_break_start_note'],
                            'attendancekaryawan_break_start_early' => $attendanceKaryawan['attendancekaryawan_break_start_early'],
                            'attendancekaryawan_break_start_late' => $attendanceKaryawan['attendancekaryawan_break_start_late'],
                            'attendancekaryawan_break_end_note' => $attendanceKaryawan['attendancekaryawan_break_end_note'],
                            'attendancekaryawan_break_start_location' => $attendanceKaryawan['attendancekaryawan_break_start_location'],
                            'attendancekaryawan_check_out_note' => $attendanceKaryawan['attendancekaryawan_check_out_note'],
                            'attendancekaryawan_check_out_location' => $attendanceKaryawan['attendancekaryawan_check_out_location'],
                            'attendancekaryawan_check_out_early' => $attendanceKaryawan['attendancekaryawan_check_out_early'],
                        ];
                    }
                }
            }

            // Process leaveKaryawans
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
                        $data3[] = [
                            'overlapStart' => $overlapStart,
                            'overlapEnd' => $overlapEnd
                        ];
                        // Check if there is an overlap
                        if ($overlapStart <= $overlapEnd) {
                            // Break down the leave record into multiple rows for each day
                            $currentDate = new DateTime($overlapStart);
                            $endDate = new DateTime($overlapEnd);

                            while ($currentDate <= $endDate) {
                                $getManager = KaryawanModel::where('karyawan_id', $leaveKaryawan['leavekaryawan_manager_id'])->first();
                                // If $startDate and $endDate are not provided, include all data without any filtering
                                $result[] = [
                                    'karyawan_id' => $karyawan_id,
                                    'karyawan_name' => $karyawan_name,
                                    'manager_name' => $getManager['karyawan_name'],
                                    'attendancekaryawan_id' => null,
                                    'leavekaryawan_id' => $leaveKaryawan['leavekaryawan_id'],
                                    'attendancekaryawan_check_in' => $currentDate->format('Y-m-d'),
                                    // Add more leave data fields as needed
                                    'attendancekaryawan_break_start' => '',
                                    'attendancekaryawan_break_end' => '',
                                    'attendancekaryawan_check_out' => '',
                                    'attendancekaryawan_check_in_note' => $leaveDescription,
                                    'attendancekaryawan_check_in_late' => '',
                                    'attendancekaryawan_check_in_location' => '',
                                    'attendancekaryawan_check_in_photo' => '',
                                    'attendancekaryawan_break_start_photo' => '',
                                    'attendancekaryawan_break_end_photo' => '',
                                    'attendancekaryawan_break_end_location' => '',
                                    'attendancekaryawan_break_end_late' => '',
                                    'attendancekaryawan_check_out_photo' => '',
                                    'attendancekaryawan_break_start_note' => '',
                                    'attendancekaryawan_break_start_early' => '',
                                    'attendancekaryawan_break_start_late' => '',
                                    'attendancekaryawan_break_end_note' => '',
                                    'attendancekaryawan_break_start_location' => '',
                                    'attendancekaryawan_check_out_note' => '',
                                    'attendancekaryawan_check_out_location' => '',
                                    'attendancekaryawan_check_out_early' => '',
                                    'attendancekaryawan_check_in_late_note' => '',
                                ];

                                $currentDate->modify('+1 day');
                            }
                        }
                    }
                  
                }
            }
        }
        
        usort($result, function($a, $b) {
            return strtotime($a['attendancekaryawan_check_in']) - strtotime($b['attendancekaryawan_check_in']);
        });

       // Create a paginator manually for the array of objects
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($result, ($page - 1) * $perPage, $perPage),
            count($result),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat data Kehadiran.',
            'data' => $paginatedData->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $paginatedData->total(),
            'recordsTotal' => $paginatedData->total(),
        ], 200);
    }

    public function capturePhoto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attendancekaryawan_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'attendancekaryawan_type' => 'required|in:CHECKIN,CHECKOUT,STARTBREAK,ENDBREAK',
                'attendancekaryawan_time' => 'required|date_format:Y-m-d H:i:s',
                'attendancekaryawan_location_address' => 'required',
                'st_attendance_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $inputDate = date('Y-m-d', strtotime($request->input('attendancekaryawan_time')));
            $inputTime = date('H:i', strtotime($request->input('attendancekaryawan_time')));

            $karyawan_id = null;

            if(!isset(session()->get('karyawan_data')['karyawan_id'])) {
                $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

                $karyawan_id = $getKaryawan->karyawan_id;
            } else {
                $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
            }



            $checkAttendance = $this->checkKaryawan($inputDate, $karyawan_id);
            $checkSetting = $this->checkSetting($request->input('st_attendance_id'));

            if($checkSetting->count() < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mohon hubungi HR Department untuk mengatur Absensi terdahulu.'
                ], 400); 
            }

            if ($request->input('attendancekaryawan_type') === 'CHECKIN') {
                return $this->performCheckIn($request, $inputTime, $karyawan_id, $checkSetting, $checkAttendance);
            } elseif ($request->input('attendancekaryawan_type') === 'CHECKOUT') {
                return $this->performCheckOut($request, $checkAttendance, $checkSetting, $inputTime);
            } elseif ($request->input('attendancekaryawan_type') === 'STARTBREAK') {
                return $this->performStartBreak($request, $checkAttendance, $checkSetting, $inputTime);
            } elseif ($request->input('attendancekaryawan_type') === 'ENDBREAK') {
                return $this->performEndBreak($request, $checkAttendance, $checkSetting, $inputTime);
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
    
                        $duration = subtractTime($checkAttendance['attendance_end_break'], $checkAttendance['attendance_start_break']);
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
                'karyawan_name' => 'nullable',
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

            if ($request->has('karyawan_name')) {
                $karyawanName = $request->input('karyawan_name');
                $query->where('karyawan_name', 'iLIKE', "%$karyawanName%");
            }

            $attendanceKaryawans = $query->get();
            $kehadiran = [];
            $data = json_decode($attendanceKaryawans, true);

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_id'];
                $karyawan_name = $item['karyawan_name'];
                
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

            $htmlString = view('karyawan.kehadiran.export', ['title' => $title,'kehadiran' => $kehadiran])->render();

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
            // Get the WajibPajakModel data based on the wajibpajak_id
            $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

            // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
            if (!$wajibPajak) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            // Retrieve all related AttendanceKaryawanModel data for the given WajibPajakModel with pagination
            $query = $wajibPajak->karyawans();


            $karyawan = $query->get();
            $data = json_decode($karyawan, true);
            $kehadiran = [];

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_id'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => 'RK-KY-ID-' . $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }

            $unixtime = time();
            $title = 'Templat_Impor_Absensi_' . $unixtime;

            $spreadsheet = new Spreadsheet();

            // Create the first sheet
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Catatan Pengguna');
            $sheet1->setCellValue('A1', 'Note :');
            $sheet1->setCellValue('A2', '- Untuk penginputan Absen Karyawan harus menggunakan sheet "Import Absensi"');
            $sheet1->setCellValue('A3', '- TIDAK BOLEH merubah kolom table pada sheet "Import Absensi"');
            $sheet1->setCellValue('A4', '- WAJIB memasukan Format Tanggal dengan HARI BULAN TAHUN (01 Desember 2023)');
            $sheet1->setCellValue('A5', '- WAJIB memasukan Format Jam dengan JAM:MENIT (23:59)');
            $sheet1->setCellValue('A6', '- Untuk Column dengan format Jam jika tidak di isi hanya kosongkan, untuk Column "Catatan" jika tidak di isi gunakan "-"');
            $sheet1->setCellValue('A7', '- Untuk Id Karyawan disesuaikan dengan data yang dimiliki oleh Team REKKAA dan ada pada sheet "Data Karyawan"');
            $sheet1->setCellValue('A8', '- Jika ada pertanyaan lebih lanjut, dapat menghubungi Customer Service REKKAA');
            $sheet1->setCellValue('A10', 'Contoh Data Karyawan');
            $sheet1->setCellValue('A11', 'Id Karyawan');
            $sheet1->setCellValue('B11', 'Nama Karyawan');
            $boldFontStyle = [
                'font' => ['bold' => true],
            ];
            $sheet1->setCellValue('A12', 'RK-KY-ID-1');
            $sheet1->setCellValue('B12', 'Jenaka');
            $sheet1->setCellValue('A13', 'RK-KY-ID-3');
            $sheet1->setCellValue('B13', 'Randy Utomo');
            $sheet1->setCellValue('A14', 'RK-KY-ID-4');
            $sheet1->setCellValue('B14', 'Dwitomo Dwi');

            $sheet1->getStyle('A11:B11')->applyFromArray($boldFontStyle);
            $sheet1->setCellValue('A17', 'Contoh Import Data Absensi');
            $sheet1->setCellValue('A18', 'Id Karyawan');
            $sheet1->setCellValue('B18', 'Tanggal');
            $sheet1->setCellValue('C18', 'Jam Masuk');
            $sheet1->setCellValue('D18', 'Jam Mulai Istirahat');
            $sheet1->setCellValue('E18', 'Jam Selesai Istirahat');
            $sheet1->setCellValue('F18', 'Jam Keluar');
            $sheet1->setCellValue('G18', 'Catatan Telat Masuk');
            $sheet1->setCellValue('H18', 'Catatan Keluar Lebih Dulu');
            $sheet1->setCellValue('I18', 'Catatan Jam Mulai Istirahat Lebih / Kurang');
            $sheet1->setCellValue('J18', 'Catatan Jam Selesai Istirahat Lebih Lama');
            $sheet1->setCellValue('K18', 'Catatan Tidak Dikantor');
            $sheet1->getStyle('A18:K18')->applyFromArray($boldFontStyle);
            $sheet1->setCellValue('A19', 'RK-KY-ID-1');
            $sheet1->setCellValue('B19', '30-12-2022');
            $sheet1->setCellValue('C19', '08:30');
            $sheet1->setCellValue('D19', '11:51');
            $sheet1->setCellValue('E19', '13:58');
            $sheet1->setCellValue('F19', '15:55');
            $sheet1->setCellValue('G19', 'Tambal Ban');
            $sheet1->setCellValue('H19', 'Tidak Enak Badan');
            $sheet1->setCellValue('I19', '-');
            $sheet1->setCellValue('J19', '-');
            $sheet1->setCellValue('K19', 'Request WFH kemarin');
            $sheet1->setCellValue('A20', 'RK-KY-ID-1');
            $sheet1->setCellValue('B20', '31-12-2022');
            $sheet1->setCellValue('C20', '08:01');
            $sheet1->setCellValue('D20', '12:01');
            $sheet1->setCellValue('E20', '13:11');
            $sheet1->setCellValue('F20', '17:00');
            $sheet1->setCellValue('G20', '-');
            $sheet1->setCellValue('H20', '-');
            $sheet1->setCellValue('I20', '-');
            $sheet1->setCellValue('J20', '-');
            $sheet1->setCellValue('K20', '-');
            $sheet1->setCellValue('A21', 'RK-KY-ID-3');
            $sheet1->setCellValue('B21', '01-01-2022');
            $sheet1->setCellValue('C21', '07:59');
            $sheet1->setCellValue('D21', '12:00');
            $sheet1->setCellValue('E21', '13:01');
            $sheet1->setCellValue('F21', '17:05');
            $sheet1->setCellValue('G21', '-');
            $sheet1->setCellValue('H21', '-');
            $sheet1->setCellValue('I21', '-');
            $sheet1->setCellValue('J21', '-');
            $sheet1->setCellValue('K21', '-');
            $sheet1->setCellValue('A22', 'RK-KY-ID-4');
            $sheet1->setCellValue('B22', '30-12-2022');
            $sheet1->setCellValue('C22', '08:30');
            $sheet1->setCellValue('D22', '11:51');
            $sheet1->setCellValue('E22', '13:58');
            $sheet1->setCellValue('F22', '15:55');
            $sheet1->setCellValue('G22', 'Antar Anak');
            $sheet1->setCellValue('H22', 'Jemput Istri');
            $sheet1->setCellValue('I22', '-');
            $sheet1->setCellValue('J22', '-');
            $sheet1->setCellValue('K22', '-');

            $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Karyawan');
            $spreadsheet->addSheet($sheet2);

            $htmlString = view('karyawan.kehadiran.import-karyawan', ['title' => 'Data Karyawan', 'kehadiran' => $kehadiran])->render();
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
                    $sheet2->setCellValueByColumnAndRow($cellIndex, $rowIndex, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheet2->getStyleByColumnAndRow($cellIndex, $rowIndex)->getFont()->setBold(true);
                    
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
                    $sheet2->setCellValueByColumnAndRow($cellIndex, $rowIndex, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex++;
            }

            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Import Absensi');
            $sheet3->setCellValue('A1', 'Id Karyawan');
            $sheet3->setCellValue('B1', 'Tanggal');
            $sheet3->setCellValue('C1', 'Jam Masuk');
            $sheet3->setCellValue('D1', 'Jam Mulai Istirahat');
            $sheet3->setCellValue('E1', 'Jam Selesai Istirahat');
            $sheet3->setCellValue('F1', 'Jam Keluar');
            $sheet3->setCellValue('G1', 'Catatan Telat Masuk');
            $sheet3->setCellValue('H1', 'Catatan Keluar Lebih Dulu');
            $sheet3->setCellValue('I1', 'Catatan Jam Mulai Istirahat Lebih / Kurang');
            $sheet3->setCellValue('J1', 'Catatan Jam Selesai Istirahat Lebih Lama');
            $sheet3->setCellValue('K1', 'Catatan Tidak Dikantor');
            $sheet3->getStyle('A1:K1')->applyFromArray($boldFontStyle);

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                foreach (range('B', $sheet->getHighestDataColumn()) as $column) {
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

    public function generateHistory(Request $request) 
    {
        try {
            $id = $request->input('id');
            // Get the WajibPajakModel data based on the wajibpajak_id
            $wajibPajak = WajibPajakModel::where('wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

            // If the WajibPajakModel data is not found, return a response indicating that the data was not found.
            if (!$wajibPajak) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            // Retrieve all related AttendanceKaryawanModel data for the given WajibPajakModel with pagination
            $query = $wajibPajak->karyawans();


            $karyawan = $query->get();
            $data = json_decode($karyawan, true);
            $kehadiran = [];

            foreach ($data as $item) {
                // Extract required information
                $karyawan_id = $item['karyawan_id'];
                $karyawan_name = $item['karyawan_name'];
                
                $kehadiran[] = [
                    "karyawan_id" => 'RK-KY-ID-' . $karyawan_id,
                    "karyawan_name" => $karyawan_name
                ];
            }

            $unixtime = time();
            $title = 'History_Import_Absensi_'.$unixtime;

            $spreadsheet = new Spreadsheet();

            // Create the first sheet
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Catatan Pengguna');
            $sheet1->setCellValue('A1', 'Note :');
            $sheet1->setCellValue('A2', '- Untuk penginputan Absen Karyawan harus menggunakan sheet "Import Absensi"');
            $sheet1->setCellValue('A3', '- TIDAK BOLEH merubah kolom table pada sheet "Import Absensi"');
            $sheet1->setCellValue('A4', '- WAJIB memasukan Format Tanggal dengan HARI BULAN TAHUN (01 Desember 2023)');
            $sheet1->setCellValue('A5', '- WAJIB memasukan Format Jam dengan JAM:MENIT (23:59)');
            $sheet1->setCellValue('A6', '- Untuk tidak di isi hanya kosongkan kolom.');
            $sheet1->setCellValue('A7', '- Untuk Id Karyawan disesuaikan dengan data yang telah disediakan oleh sistem pada sheet "Data Karyawan"');
            $sheet1->setCellValue('A8', '- Jika ada pertanyaan lebih lanjut, dapat menghubungi Customer Service REKKAA');
            $sheet1->setCellValue('A10', 'Contoh Data Karyawan');
            $sheet1->setCellValue('A11', 'Id Karyawan');
            $sheet1->setCellValue('B11', 'Nama Karyawan');
            $boldFontStyle = [
                'font' => ['bold' => true],
            ];
            $sheet1->setCellValue('A12', 'RK-KY-ID-1');
            $sheet1->setCellValue('B12', 'Jenaka');
            $sheet1->setCellValue('A13', 'RK-KY-ID-3');
            $sheet1->setCellValue('B13', 'Randy Utomo');
            $sheet1->setCellValue('A14', 'RK-KY-ID-4');
            $sheet1->setCellValue('B14', 'Dwitomo Dwi');

            $sheet1->getStyle('A11:B11')->applyFromArray($boldFontStyle);
            $sheet1->setCellValue('A17', 'Contoh Import Data Absensi');
            $sheet1->setCellValue('A18', 'Id Karyawan');
            $sheet1->setCellValue('B18', 'Tanggal');
            $sheet1->setCellValue('C18', 'Jam Masuk');
            $sheet1->setCellValue('D18', 'Jam Mulai Istirahat');
            $sheet1->setCellValue('E18', 'Jam Selesai Istirahat');
            $sheet1->setCellValue('F18', 'Jam Keluar');
            $sheet1->setCellValue('G18', 'Catatan Telat Masuk');
            $sheet1->setCellValue('H18', 'Catatan Keluar Lebih Dulu');
            $sheet1->setCellValue('I18', 'Catatan Jam Mulai Istirahat Lebih / Kurang');
            $sheet1->setCellValue('J18', 'Catatan Jam Selesai Istirahat Lebih Lama');
            $sheet1->setCellValue('K18', 'Catatan Tidak Dikantor');
            $sheet1->getStyle('A18:K18')->applyFromArray($boldFontStyle);
            $sheet1->setCellValue('A19', 'RK-KY-ID-1');
            $sheet1->setCellValue('B19', '30 Desember 2022');
            $sheet1->setCellValue('C19', '08:30');
            $sheet1->setCellValue('D19', '11:51');
            $sheet1->setCellValue('E19', '13:58');
            $sheet1->setCellValue('F19', '15:55');
            $sheet1->setCellValue('G19', 'Tambal Ban');
            $sheet1->setCellValue('H19', 'Tidak Enak Badan');
            $sheet1->setCellValue('I19', '');
            $sheet1->setCellValue('J19', '');
            $sheet1->setCellValue('K19', 'Request WFH kemarin');
            $sheet1->setCellValue('A20', 'RK-KY-ID-1');
            $sheet1->setCellValue('B20', '31 Desember 2022');
            $sheet1->setCellValue('C20', '08:01');
            $sheet1->setCellValue('D20', '12:01');
            $sheet1->setCellValue('E20', '13:11');
            $sheet1->setCellValue('F20', '17:00');
            $sheet1->setCellValue('G20', '');
            $sheet1->setCellValue('H20', '');
            $sheet1->setCellValue('I20', '');
            $sheet1->setCellValue('J20', '');
            $sheet1->setCellValue('K20', '');
            $sheet1->setCellValue('A21', 'RK-KY-ID-3');
            $sheet1->setCellValue('B21', '01 Januari 2022');
            $sheet1->setCellValue('C21', '07:59');
            $sheet1->setCellValue('D21', '12:00');
            $sheet1->setCellValue('E21', '13:01');
            $sheet1->setCellValue('F21', '17:05');
            $sheet1->setCellValue('G21', '');
            $sheet1->setCellValue('H21', '');
            $sheet1->setCellValue('I21', '');
            $sheet1->setCellValue('J21', '');
            $sheet1->setCellValue('K21', '');
            $sheet1->setCellValue('A22', 'RK-KY-ID-4');
            $sheet1->setCellValue('B22', '30 Desember 2022');
            $sheet1->setCellValue('C22', '08:30');
            $sheet1->setCellValue('D22', '11:51');
            $sheet1->setCellValue('E22', '13:58');
            $sheet1->setCellValue('F22', '15:55');
            $sheet1->setCellValue('G22', 'Antar Anak');
            $sheet1->setCellValue('H22', 'Jemput Istri');
            $sheet1->setCellValue('I22', '');
            $sheet1->setCellValue('J22', '');
            $sheet1->setCellValue('K22', '');

            $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Karyawan');
            $spreadsheet->addSheet($sheet2);

            $htmlString = view('karyawan.kehadiran.import-karyawan', ['title' => 'Data Karyawan', 'kehadiran' => $kehadiran])->render();
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
                    $sheet2->setCellValueByColumnAndRow($cellIndex, $rowIndex, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheet2->getStyleByColumnAndRow($cellIndex, $rowIndex)->getFont()->setBold(true);
                    
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
                    $sheet2->setCellValueByColumnAndRow($cellIndex, $rowIndex, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex++;
            }

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);
            $listabsen = [];

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

            foreach($content as $item) {
                $listabsen[] = [
                    'karyawan_id' => isset($item->karyawan_id) ? 'RK-KY-ID-' . $item->karyawan_id : '',
                    'attendancekaryawan_date' => isset($item->attendancekaryawan_date) ? $item->attendancekaryawan_date : '',
                    'attendancekaryawan_check_in' => getProperty($item, 'attendancekaryawan_check_in', 'H:i'),
                    'attendancekaryawan_break_start' => getProperty($item, 'attendancekaryawan_break_start', 'H:i'),
                    'attendancekaryawan_break_end' => getProperty($item, 'attendancekaryawan_break_end', 'H:i'),
                    'attendancekaryawan_check_out' => getProperty($item, 'attendancekaryawan_check_out', 'H:i'),
                    'attendancekaryawan_check_in_late_note' => is_object($item) && property_exists($item, 'attendancekaryawan_check_in_late_note') ? $item->attendancekaryawan_check_in_late_note : '',
                    'attendancekaryawan_check_out_note' => is_object($item) && property_exists($item, 'attendancekaryawan_check_out_note') ? $item->attendancekaryawan_check_out_note : '',
                    'attendancekaryawan_break_start_note' => is_object($item) && property_exists($item, 'attendancekaryawan_break_start_note') ? $item->attendancekaryawan_break_start_note : '',
                    'attendancekaryawan_break_end_note' => is_object($item) && property_exists($item, 'attendancekaryawan_break_end_note') ? $item->attendancekaryawan_break_end_note : '',
                    'attendancekaryawan_check_in_note' => is_object($item) && property_exists($item, 'attendancekaryawan_check_in_note') ? $item->attendancekaryawan_check_in_note : '',
                    'status' => isset($item->status) ? $item->status : 'Sukses',
                    'remark' => isset($item->remark) ? $item->remark : '-',
                ];
            }

            $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Import Absensi');
            $spreadsheet->addSheet($sheet3);

            $htmlString = view('karyawan.kehadiran.generate-history', ['title' => 'Import Absensi', 'listabsen' => $listabsen])->render();
            $dom2 = new DOMDocument();
            $dom2->loadHTML($htmlString);
            $table = $dom2->getElementsByTagName('table')->item(0);

            $rowIndex2 = 1;
            foreach ($table->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheet3->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheet3->getStyleByColumnAndRow($cellIndex, $rowIndex2)->getFont()->setBold(true);
                    
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
                    $sheet3->setCellValueByColumnAndRow($cellIndex, $rowIndex2, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex2++;
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                foreach (range('B', $sheet->getHighestDataColumn()) as $column) {
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
            ->orderBy('historyimport_date')
            ->join('ms_karyawan', 'tr_history_import.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_karyawan_id', 'tr_history_import.historyimport_status', 'tr_history_import.historyimport_id', 'ms_karyawan.karyawan_name');
        
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
            'recordsFiltered' => $dataHistory->lastPage(),
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
    
        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        $path = public_path('uploads/'.$filename);

        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Import Absensi');
        $maxRows = $sheet->getHighestRow();

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 1; // mulai baris ke 3;

        $result = [];
        
        function decimalToTime($decimalTime) {
            $seconds = $decimalTime * 86400; // 86400 seconds in a day
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;
        
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
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
                    
                } else {
                    $columnA = explode('-', $columnA);
                    $columnA = end($columnA);
                }

                if (is_string($cellValue) && preg_match('/^\d{1,2}\s\w+\s\d{4}$/', $cellValue)) {
                    // Date is in the format "DD Month YYYY"
                    // Keep the date as is
                    $dateString = $cellValue;
                } elseif (is_numeric($cellValue)) {
                    // Date is in decimal format (Excel date serial number)
                    $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                    $dateString = date("d F Y", $timestamp); // "F" gives you the full month name
                } else {
                    // Handle cases where the date format is not as expected
                    $dateString = ''; // Set to an empty string or handle the error as needed
                }

                // $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                // // Format the timestamp as "DD MMMM YYYY"
                // $dateString = date("d F Y", $timestamp); // "F" gives you the full month name


                $columnB = $dateString;
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
                    "attendancekaryawan_check_in_note" => $columnK
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
            for($x = 0; $x < count($result); $x++) {
                $checkKaryawan = KaryawanModel::find($result[$x]['karyawan_id']);
                
                $getAttendance = SettingAttendanceModel::find($checkKaryawan['st_attendance_id']);

                if($checkKaryawan['ms_wajibpajak_id'] !== session()->get('wajibpajak_current')['wajibpajak_id']) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Id Karyawan tidak terdaftar';
                    continue;
                } else if ($result[$x]['attendancekaryawan_check_in'] === null) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan jam masuk';
                    continue;
                } else if ($result[$x]['attendancekaryawan_check_out'] === null) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan jam keluar';
                    continue;
                } else if ($result[$x]['attendancekaryawan_break_start'] === null && $getAttendance['attendance_break_status'] === true) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan jam mulai istirahat';
                    continue;
                } else if ($result[$x]['attendancekaryawan_break_end'] === null && $getAttendance['attendance_break_status'] === true) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Mohon masukan jam selesai istirahat';
                    continue;
                } else if(($result[$x]['attendancekaryawan_check_in_late_note'] === null || $result[$x]['attendancekaryawan_check_in_late_note'] === '') || ($result[$x]['attendancekaryawan_check_out_note'] === null || $result[$x]['attendancekaryawan_check_out_note'] === '') || ($result[$x]['attendancekaryawan_break_start_note'] === null || $result[$x]['attendancekaryawan_break_start_note'] === '') || ($result[$x]['attendancekaryawan_break_end_note'] === null || $result[$x]['attendancekaryawan_break_end_note'] === '') || ($result[$x]['attendancekaryawan_check_in_note'] === null || $result[$x]['attendancekaryawan_check_in_note'] === '')) {
                    
                    $result[$x]['status'] = 'Gagal';
                    $result[$x]['remark'] = 'Gunakan "-" jika ingin mengkosongkan Catatan';
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
    
                        $duration = subtractTime($getAttendance['attendance_end_break'], $getAttendance['attendance_start_break']);
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

                }

                $parts = explode(' ', $result[$x]['attendancekaryawan_date']);
                $monthName = $parts[1];
                $monthNumber = date('m', strtotime($monthName));
                $dateString = $parts[2] . '-' . $monthNumber . '-' . $parts[0];
                $date = date("Y-m-d", strtotime($dateString));

                $checkAttendanceKaryawanModel = AttendanceKaryawanModel::where('ms_karyawan_id',  $result[$x]['karyawan_id'])->whereDate('attendancekaryawan_check_in', '=', $result[$x]['attendancekaryawan_date'])->first();

               

                if(!$checkAttendanceKaryawanModel) {
                    $createAttendance = AttendanceKaryawanModel::create([
                        'attendancekaryawan_manager_id' => $result[$x]['karyawan_id'],
                        'ms_karyawan_id' => $result[$x]['karyawan_id'],
                        'st_attendance_id' => $getAttendance['attendance_id'],
                        'attendancekaryawan_check_in' => $date . ' ' . $result[$x]['attendancekaryawan_check_in'],
                        'attendancekaryawan_check_out' => $date . ' ' . $result[$x]['attendancekaryawan_check_out'],
                        'attendancekaryawan_break_start' => $date . ' ' . $result[$x]['attendancekaryawan_break_start'],
                        'attendancekaryawan_break_end' => $date . ' ' . $result[$x]['attendancekaryawan_break_end'],
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
                        'ms_karyawan_id' => $result[$x]['karyawan_id'],
                        'st_attendance_id' => $getAttendance['attendance_id'],
                        'attendancekaryawan_check_in' => $date . ' ' . $result[$x]['attendancekaryawan_check_in'],
                        'attendancekaryawan_check_out' => $date . ' ' . $result[$x]['attendancekaryawan_check_out'],
                        'attendancekaryawan_break_start' => $date . ' ' . $result[$x]['attendancekaryawan_break_start'],
                        'attendancekaryawan_break_end' => $date . ' ' . $result[$x]['attendancekaryawan_break_end'],
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
            }

            $karyawanImport = null;

            if(!isset(session()->get('karyawan_data')['karyawan_id'])) {
                $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();

                $karyawanImport = $getKaryawan->karyawan_id;
            } else {
                $karyawanImport = session()->get('karyawan_data')['karyawan_id'];
            }

            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_karyawan_id' => $karyawanImport,
                'historyimport_type' => "ATTENDANCE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                
                'historyimport_content' => json_encode($result)
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data absensi.',

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

    

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $latDelta = $lat2Rad - $lat1Rad;
        $lonDelta = $lon2Rad - $lon1Rad;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    private function uploadPhotoToS3($photo)
    {
        // Generate a unique filename for the photo
        $filename = uniqid() . '.' . $photo->getClientOriginalExtension();

        // Upload the photo to Amazon S3
        $foldername = 'images/attendance';
        $foldername = env('APP_ENV').'/'.$foldername;
        $filePath = $photo->storeAs($foldername, $filename, ['disk' => 's3']);

        // Return the S3 URL of the saved image
        return Storage::disk('s3')->url($filePath);
    }

    public function checkKaryawan($inputDate, $karyawanId) {
        return AttendanceKaryawanModel::whereDate('attendancekaryawan_check_in', $inputDate)
                ->where('ms_karyawan_id', $karyawanId)
                ->first();
    }

    public function checkIncludeDate ()
    {
        $currentDate = Carbon::now(); // Get the current date

        $settingHolidays = SettingHolidayModel::where('ms_wajibpajak_id', session()->get('karyawan_data')['wajibpajak_id'])
            ->whereDate('holiday_start_date', '<=', $currentDate) // Start date is less than or equal to the current date
            ->whereDate('holiday_end_date', '>=', $currentDate)   // End date is greater than or equal to the current date
            ->where('holiday_status_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil check data Liburan.',
            'data' => $settingHolidays
        ], 200);
    }

    public function getKaryawan($userId) {
        $data = null;

        if($userId) {
            $data = KaryawanModel::where('karyawan_id', $userId)->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil check data karyawan.',
            'data' => $data
        ], 200);
    }

    private function checkSetting($attendanceId) {
        return SettingAttendanceModel::where('attendance_id', $attendanceId)->first();
    }

    private function performCheckIn($request, $inputTime, $karyawan_id, $checkSetting, $checkAttendance)
    {
        function dmsToDecimal($degrees, $minutes, $seconds, $direction)
        {
            $dd = $degrees + $minutes / 60 + $seconds / 3600;
            return ($direction === "S" || $direction === "W") ? -$dd : $dd;
        }

        function isDMSFormat($value) {
            // Define a regular expression pattern to match DMS format
            $pattern = '/^-?\d{1,3}°\s*\d{1,2}\'\s*\d{1,2}"\s*[NSEW]$/i';
        
            // Use preg_match to check if the value matches the pattern
            return preg_match($pattern, $value) === 1;
        }

        if ($checkAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah melakukan Check In Absensi hari ini.'
            ], 400);
        }

        $photoResult = null;

        if (!$request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
            return response()->json([
                'success' => false,
                'message' => 'Harap masukan photo diri sebagai bukti absensi.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
                $photo = $request->file('image');
                $photoResult = $this->uploadPhotoToS3($photo);
            }
    
            $statusAttendance = '';
    
            if ($checkSetting->attendance_check_in < $inputTime) {
                if (!is_null($checkSetting->attendance_check_in_tolerance)) {
                    $addTolerance = strtotime($checkSetting->attendance_check_in) + strtotime($checkSetting->attendance_check_in_tolerance) - strtotime('00:00:00');
                    $tolerance = date("H:i:s", $addTolerance);
                    if($tolerance < $inputTime) {
                        $timeA = DateTime::createFromFormat('H:i:s', $tolerance);
                        $timeB = DateTime::createFromFormat('H:i', $inputTime);
                        
                        $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                        // Calculate the difference between the two times
                        $interval = $timeB->diff($timeA);
                        
        
                        // Get the difference in minutes and format the result
                        $statusAttendance = $statusAttendance . $interval->format('%H:%I');
                    } 
                } else {
                    $timeA = DateTime::createFromFormat('H:i:s', $checkSetting->attendance_check_in);
                    $timeB = DateTime::createFromFormat('H:i', $inputTime);
                    
                    $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                    // Calculate the difference between the two times
                    $interval = $timeB->diff($timeA);
                    
    
                    // Get the difference in minutes and format the result
                    $statusAttendance = $statusAttendance . $interval->format('%H:%I');
                }
            }
    
            $createCheckIn = AttendanceKaryawanModel::create([
                'ms_karyawan_id' => $karyawan_id,
                'attendancekaryawan_manager_id' => $karyawan_id,
                'st_attendance_id' => $checkSetting->attendance_id,
                'attendancekaryawan_check_in' => $request->input('attendancekaryawan_time'),
                'attendancekaryawan_check_in_photo' => $photoResult,
                'attendancekaryawan_check_in_location' => $request->input('attendancekaryawan_location_address'),
                'attendancekaryawan_check_in_note' => $request->input('attendancekaryawan_check_in_note'),
                'attendancekaryawan_check_in_late_note' => $request->input('attendancekaryawan_check_in_late_note'),
                'attendancekaryawan_check_in_late' => $statusAttendance !== '' ? $statusAttendance : null
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Absen Masuk hari ini.',
                'data' => $createCheckIn
            ], 200);
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }  
    }

    private function performCheckOut($request, $checkAttendance, $checkSetting, $inputTime)
    {
        if(is_null($checkAttendance)) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu harus melakukan Absen Masuk sebelum Check Out.'
            ], 400); 
        }

        if($checkAttendance->attendancekaryawan_check_out !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak bisa set Check Out lagi di hari yang sama.'
            ], 400); 
        }

        if(!$request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
            return response()->json([
                'success' => false,
                'message' => 'Harap masukan photo diri sebagai bukti absensi.'
            ], 400);
        }

        $photoResult = null;

        DB::beginTransaction();
        try {
            if($request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
                $photo = $request->file('image');
                $photoResult = $this->uploadPhotoToS3($photo);
            }
    
            $checkAttendance->update([
                'attendancekaryawan_check_out' => $request->input('attendancekaryawan_time'),
                'attendancekaryawan_check_out_photo' => $photoResult,
                'attendancekaryawan_check_out_location' => $request->input('attendancekaryawan_location_address'),
                'attendancekaryawan_check_out_note' => $request->input('attendancekaryawan_check_out_note'),
            ]);
    
            if($request->input('attendancekaryawan_check_out_note')) {
                $timeA = DateTime::createFromFormat('H:i:s', $checkSetting->attendance_check_out);
                $timeB = DateTime::createFromFormat('H:i', $inputTime);

                $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                // Calculate the difference between the two times
                $interval = $timeB->diff($timeA);

                // Get the difference in minutes and format the result
                $differenceTime = $interval->format('%H:%I');

                $checkAttendance->update([
                    'attendancekaryawan_check_out_early' => $differenceTime
                ]);
            }
            
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Absen Keluar hari ini.',
                'data' => $checkAttendance
            ], 200); 
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        
    }

    private function performStartBreak($request, $checkAttendance, $checkSetting, $inputTime)
    {
        if(is_null($checkAttendance)) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu harus melakukan Absens Masuk sebelum Mulai Istirahat.'
            ], 400); 
        }

        if($checkAttendance->attendancekaryawan_check_out !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak bisa Mulai Istirahat karena sudah melakukan Absen Keluar.'
            ], 400); 
        }

        $photoResult = null;

        DB::beginTransaction();
        try {
            if($request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
                $photo = $request->file('image');
                $photoResult = $this->uploadPhotoToS3($photo);
            }
    
            $checkAttendance->update([
                'attendancekaryawan_break_start' => $request->input('attendancekaryawan_time'),
                'attendancekaryawan_break_start_location' => $request->input('attendancekaryawan_location_address'),
                'attendancekaryawan_break_start_photo' => $photoResult,
                'attendancekaryawan_break_start_note' => $request->input('attendancekaryawan_break_start_note')
            ]);

            if($request->input('attendancekaryawan_break_start_note')) {
                $x = Carbon::createFromTimeString($checkSetting->attendance_start_break);
                $y = Carbon::createFromTimeString($inputTime);

                $timeA = DateTime::createFromFormat('H:i:s', $checkSetting->attendance_start_break);
                $timeB = DateTime::createFromFormat('H:i', $inputTime);

                $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);

                // Calculate the difference between the two times
                $interval = $timeB->diff($timeA);

                // Get the difference in minutes and format the result
                $differenceTime = $interval->format('%H:%I');

                if($y->greaterThan($x)) {
                    $checkAttendance->update([
                        'attendancekaryawan_break_start_late' => $differenceTime
                    ]);
                } else {
                    $checkAttendance->update([
                        'attendancekaryawan_break_start_early' => $differenceTime
                    ]);
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memulai istirahat untuk hari ini.',
                'data' => $checkAttendance
            ], 200); 
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }

    private function performEndBreak($request, $checkAttendance, $checkSetting, $inputTime)
    {
        if(is_null($checkAttendance)) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu harus melakukan Absen Masuk & Istirahat terdahulu.'
            ], 400); 
        }

        if($checkAttendance->attendancekaryawan_check_out !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak bisa set dan mengakhiri Istirahat karena sudah melakukan Absen Keluar.'
            ], 400); 
        }

        if($checkAttendance->attendancekaryawan_break_start === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum Mulai Istirahat.'
            ], 400); 
        }

        $photoResult = null;

        DB::beginTransaction();
        try {
            if($request->hasfile('image') && $checkSetting->attendance_check_in_status_photo === true) {
                $photo = $request->file('image');
                $photoResult = $this->uploadPhotoToS3($photo);
            }
            
            $checkAttendance->update([
                'attendancekaryawan_break_end_location' => $request->input('attendancekaryawan_location_address'),
                'attendancekaryawan_break_end_note' => $request->input('attendancekaryawan_break_end_note'),
                'attendancekaryawan_break_end' => $request->input('attendancekaryawan_time'),
                'attendancekaryawan_break_end_photo' => $photoResult
            ]);
            
            if($request->input('attendancekaryawan_break_end_note')) {
                if($checkSetting->attendance_break_type === 'SPECIFIC') {
                    $x = Carbon::createFromTimeString($checkSetting->attendance_end_break);
                    $y = Carbon::createFromTimeString($inputTime);
    
                    $timeA = DateTime::createFromFormat('H:i:s', $checkSetting->attendance_end_break);
                    $timeB = DateTime::createFromFormat('H:i', $inputTime);
    
                    $timeB->setTime($timeB->format('H'), $timeB->format('i'), 0);
    
                    // Calculate the difference between the two times
                    $interval = $timeB->diff($timeA);
    
                    // Get the difference in minutes and format the result
                    $differenceTime = $interval->format('%H:%I');
    
                    if ($x->lessThan($y)) {
                        $checkAttendance->update([
                            'attendancekaryawan_break_end_late' => $differenceTime
                        ]);
                    }
                } else if($checkSetting->attendance_break_type === 'DURATION') {
                    // Function to convert time in H:i format to minutes
                    function timeToMinutes($time) {
                        $timeParts = explode(':', $time);
                        return intval($timeParts[0]) * 60 + intval($timeParts[1]);
                    }

                    // Function to subtract two time strings in H:i format and return the difference in minutes
                    function subtractTime($time1, $time2) {
                        return abs(timeToMinutes($time1) - timeToMinutes($time2)); // Use abs() to ensure a positive result
                    }

                    $duration = subtractTime($checkSetting->attendance_end_break, $checkSetting->attendance_start_break);

                    $currentDuration = subtractTime($inputTime, substr($checkAttendance->attendancekaryawan_break_start, 11, 5));

                    if ($currentDuration > $duration) {
                        $lateDuration = $currentDuration - $duration;

                        // Convert minutes to H:i format
                        $hours = floor($lateDuration / 60);
                        $minutes = $lateDuration % 60;
                        $lateDurationFormatted = sprintf('%02d:%02d', $hours, $minutes);

                        $checkAttendance->update([
                            'attendancekaryawan_break_end_late' => $lateDurationFormatted
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil selesai istirahat untuk hari ini.',
                'data' => $checkAttendance
            ], 200); 
        } catch (Error $e) {
            DB::rollBack();
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