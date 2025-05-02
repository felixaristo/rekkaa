<?php

namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Jobs\ImportJob;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Transaction\HistoryImportModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Error;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PengaturanLiburController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan Libur',
            'content' => 'user.pengaturan.libur.index',
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
        try {
            // $page = ($request->input('page')) ? intval($request->input('page')) : 1;
            // $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
            $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
            $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

            $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
            $order_columns = ['holiday_id','holiday_description','holiday_status_active','holiday_start_date','holiday_end_date','holiday_is_cut_leave'];
            $order_col = $order_columns[0];
            $order_type = 'asc';
            if(isset($request->input('order')[0])) {
                $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
                $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
                $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
            }

            $getHoliday = SettingHolidayModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(holiday_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                        ->orWhere(DB::raw('LOWER(holiday_description)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'], 'holiday_status_active' => TRUE])
                ->orderBy($order_col, $order_type)
                ->take($limit)->skip($offset)->get();

            
            $totalData = SettingHolidayModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(holiday_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                        ->orWhere(DB::raw('LOWER(holiday_description)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->count();

            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan Liburan.',
                'data' => $getHoliday,
                'recordsFiltered' => $totalData,
                'recordsTotal' => $totalData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list holiday ' . $e->getMessage());

            return response()->json(['error' => 'Error get holiday. Please try again later.', 'message' => $e],  500);
        }
    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'holiday_description' => 'required|string',
                'holiday_start_date' => 'required',
                'holiday_end_date' => 'required',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            if($request->input('isEdit') === false) {
                $checkHolidayDesc = SettingHolidayModel::where('holiday_description', $request->input('holiday_description'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

                if($checkHolidayDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Liburan sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                DB::beginTransaction();
                try {
                    $createHolidaySetting = SettingHolidayModel::create([
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        'holiday_description' => $request->input('holiday_description'),
                        'holiday_start_date' => date('Y-m-d', strtotime($request->input('holiday_start_date'))),
                        'holiday_end_date' => date('Y-m-d', strtotime($request->input('holiday_end_date'))),
                        'holiday_remark' => $request->input('holiday_remark'),
                        // 'holiday_is_cut_leave' => $request->input('holiday_is_cut_leave'),
                        'holiday_status_active' => $request->input('holiday_status_active')
                    ]);
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil membuat daftar Liburan.',
                        'data' => $createHolidaySetting
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
                
            } else {
                $checkHoliday = SettingHolidayModel::where('holiday_id', $request->input('holiday_id'))->first();

                if(!$checkHoliday) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Liburan tidak ditemukan.'
                    ], 404);
                }

                $checkHolidayDesc = SettingHolidayModel::where('holiday_description', $request->input('holiday_description'))
                    ->where('holiday_id', '!=', $request->input('holiday_id'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                    ->first();

                if($checkHolidayDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Liburan sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                DB::beginTransaction();
                try {
                    $checkHoliday->update([
                        'holiday_description' => $request->input('holiday_description'),
                        'holiday_start_date' => date('Y-m-d', strtotime($request->input('holiday_start_date'))),
                        'holiday_end_date' => date('Y-m-d', strtotime($request->input('holiday_end_date'))),
                        'holiday_remark' => $request->input('holiday_remark'),
                        // 'holiday_is_cut_leave' => $request->input('holiday_is_cut_leave'),
                        'holiday_status_active' => $request->input('holiday_status_active'),
                    ]);
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil melakukan update pada data Liburan.',
                        'data' => $checkHoliday
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

    // public function checkIncludeDate ()
    // {
    //     $currentDate = Carbon::now(); // Get the current date

    //     $settingHolidays = SettingHolidayModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
    //         ->whereDate('holiday_start_date', '<=', $currentDate) // Start date is less than or equal to the current date
    //         ->whereDate('holiday_end_date', '>=', $currentDate)   // End date is greater than or equal to the current date
    //         ->where('holiday_status_active', true)
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Berhasil check data Liburan.',
    //         'data' => $settingHolidays
    //     ], 200);
    // }

    public function show($id)
    {
        try {
            $getHoliday = SettingHolidayModel::find($id);

            if(is_null($getHoliday)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan libur tidak ditemukan.',
                ], 404);
            }

            if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getHoliday->ms_wajibpajak_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak memiliki akses ke Pengaturan libur yang diminta.',
                ], 403);
            }
                
            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan libur.',
                'data' => $getHoliday
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list holiday ' . $e->getMessage());

            return response()->json(['error' => 'Error get holiday. Please try again later.', 'message' => $e],  500);
        }
    }

    public function destroy($id)
    {
        $getHoliday = SettingHolidayModel::find($id);

        if(is_null($getHoliday)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan libur tidak ditemukan.',
            ], 404);
        }

        if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getHoliday->ms_wajibpajak_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses ke Pengaturan libur yang diminta.',
            ], 403);
        }

        $success = $getHoliday->update(['holiday_status_active' => FALSE]);
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus data Pengaturan libur.'
        ], 200);
    }

    public function generateTemplate(Request $request) 
    {
        try {
            
            $excelFilePath = public_path('assets/import/Template_Import_Libur.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Templat_Impor_Libur_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);
         
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            ob_end_clean();
            header("Content-Disposition: attachment; filename=".$filename);
            exit($writer->save('php://output'));
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importLibur($historyid)
    {
        $getHistory = HistoryImportModel::where(['historyimport_id' => $historyid])->first();

        $wajibpajak_id = $getHistory->ms_wajibpajak_id;
        $filename = $getHistory->historyimport_file_name;

        $path = public_path('uploads/'.$filename);
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        // print_r($spreadsheet);die;
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Pengaturan Libur');
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
                if($nbi <= $maxRows) {
                    // dd($nbi, $sheet->getCell("A" . $nbi)->getValue());
                    $columnA = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                    $columnB = $sheet->getCell("B" . $nbi)->getValue();
                    $columnC = $sheet->getCell("C" . $nbi)->getValue();
                    $columnD = $sheet->getCell("D" . $nbi)->getValue();

                // dd($columnA, $maxRows);
                    if($columnA === null || $columnB === null || $columnC === null) {
                        // dd($columnA, $columnB, $columnC, $columnD);
                        $spreadsheet_chunk->__destruct();
                        $spreadsheet_chunk = null;
                        unset($spreadsheet_chunk);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "Mohon lengkapi data yang masih kosong"
                        ]);
                    }

                    $columnB = convertExcelDate($sheet->getCell("B" . $nbi)); // Use $sheet instead of $worksheetData
                    $columnC = convertExcelDate($sheet->getCell("C" . $nbi)); // Use $sheet instead of $worksheetData
                
                    $result[] = [
                        "holiday_description" => $columnA,
                        "holiday_start_date" => $columnB,
                        "holiday_end_date" => $columnC,
                        "holiday_remark" => $columnD,
                        "row" => $nb + 1
                    ];
                }
            }
            // dd($result);

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

        // print_r($result);die;
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;

            $savedata = [];
            if(count($result) > 0) {
                for($x = 0; $x < count($result); $x++) {
                    if (strtotime($result[$x]['holiday_start_date']) > strtotime($result[$x]['holiday_end_date'])) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Tgl. Mulai tidak boleh lebih besar dari Tgl. Berakhir';
                        continue;
                    }
                    
                    $savedata[] = [
                        "holiday_description" => $result[$x]['holiday_description'],
                        "holiday_start_date" => $result[$x]['holiday_start_date'],
                        "holiday_end_date" => $result[$x]['holiday_end_date'],
                        "holiday_remark" => $result[$x]['holiday_remark'],
                        "holiday_status_active" => true,
                        "ms_wajibpajak_id" => $wajibpajak_id,
                    ];
    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }
            }

            // print_r($savedata);die;
            if($savedata) {
                SettingHolidayModel::insert($savedata);
            }

            HistoryImportModel::where(['historyimport_id' => $getHistory->historyimport_id])
            ->update([
                'historyimport_status' => 'COMPLETED', 
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
            ]);
            DB::commit();

            unlink(public_path('uploads/'). $filename); // remove file
        } catch (\Exception $e) {
            // unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
        }
    }

    public function import(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }

        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];

        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'ms_user_id' => $user_id,
                'historyimport_type' => "SETTING_HOLIDAY",
                'historyimport_date' => date('Y-m-d H:i:s'),
                // 'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                // 'historyimport_content' => json_encode($result),
                'historyimport_file_name' => $filename,
                'historyimport_originfile_name' => $file->getClientOriginalName(),
                'historyimport_status' => 'PROCESSED'
            ]);

            dispatch(new ImportJob($createHistory->historyimport_id, $createHistory->historyimport_type));
            // ->delay(Carbon::now()->addSeconds(60));
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data pengaturan libur.',
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

    public function generateHistory(Request $request) 
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Libur.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Libur_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);
            $spreadsheet->removeSheetByIndex(0);

            $sheet = $spreadsheet->getSheetByName('Impor Pengaturan Libur');

            $id = $request->input('id');

            $getHistory = HistoryImportModel::where(['historyimport_id' => $id, 'ms_wajibpajak_id' => $wajibpajak_id])
            ->whereNotIn('historyimport_status', ['PROCESSED'])->first();
            if(!$getHistory) {
                abort(404, 'Riwayat tidak ditemukan');
            }
            $content = json_decode($getHistory['historyimport_content']);

            // dd($content);
            $rowIndex = 2;  

            $sheet->setCellValue('E1', 'Status Impor');
            $sheet->setCellValue('F1', 'Remark');
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('E1:F1')
                ->getBorders()
                ->getOutline()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                // ->setColor(new Color('FFFF0000'));
            foreach($content as $item) {
                $listImpor = [
                    'A' => isset($item->holiday_description) ? $item->holiday_description : '',
                    'B' => isset($item->holiday_start_date) ? Carbon::parse($item->holiday_start_date)->format('d/m/Y') : '',
                    'C' => isset($item->holiday_end_date) ? Carbon::parse($item->holiday_end_date)->format('d/m/Y') : '',
                    'D' => isset($item->holiday_remark) ? $item->holiday_remark : '',
                    'E' => isset($item->status) ? $item->status : 'Sukses',
                    'F' => isset($item->remark) ? $item->remark : '',
                ];

                foreach ($listImpor as $column => $value) {
                    $sheet->setCellValue($column . $rowIndex, $value);
                }
                // Increment the row index for the next row of data
                $rowIndex++;
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            ob_end_clean();

            header("Content-Disposition: attachment; filename=".$filename);
            exit($writer->save('php://output'));
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
        // dd($start_date);
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'SETTING_HOLIDAY')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status'
            , 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import'
            , 'tr_history_import.historyimport_originfile_name', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            // $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
            $dataHistory->whereRaw(DB::raw("TO_CHAR(tr_history_import.historyimport_date, 'YYYY-MM-DD') >= ?"), [$start_date]);
            $dataHistory->whereRaw(DB::raw("TO_CHAR(tr_history_import.historyimport_date, 'YYYY-MM-DD') <= ?"), [$end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Setting Libur.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
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
