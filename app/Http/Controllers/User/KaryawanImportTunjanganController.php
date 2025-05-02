<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use SebastianBergmann\Diff\Chunk;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use SebastianBergmann\Diff\Chunk;

class KaryawanImportTunjanganController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $karyawan = KaryawanModel::where(['karyawan_id' => $karyawanId, 'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],])->first();
        // if(!$karyawan) {
        //     abort(404);
        // }
        $data = [
            'title' => 'Import Tunjangan Karyawan',
            'content' => 'user.master.karyawan.import.tunjangan.index',
            // 'karyawan' => $karyawan
        ];
        // dd($data['karyawan']);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function download_template(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $title = 'Tunjangan_Karyawan_'.$wajibpajak_id;
        $masakerja = KaryawanMasakerjaModel::select('tr_karyawan_masakerja.*', 'karyawan_npwp','karyawan_name', 'karyawan_nik'
        , DB::raw("(SELECT (SELECT json_agg(ptable) 
            FROM (
                SELECT st_tunjangan_karyawan.*
                FROM st_tunjangan_karyawan
                WHERE ms_wajibpajak_id = {$wajibpajak_id}
                AND sttunjangankaryawan_active = '1'
                ORDER BY sttunjangankaryawan_id ASC
            )
            as ptable)) as setting_tunjangankaryawan_json")
        )
        ->join("ms_karyawan", 'karyawan_id', '=', 'ms_karyawan_id')
        ->where([
            'karyawanmasakerja_active' => 1,
            'ms_karyawan.ms_wajibpajak_id' => $wajibpajak_id,
        ])->get();

        // dd($masakerja[0]->karyawanmasakerja_tunjangan);
        // return view('user.master.karyawan.import.tunjangan.template.index', [
        //     'title' => $title,
        //     'masakerja' => $masakerja,
        //     'address' => getSetting('ADDRESS'),
        // ]);

        $htmlString = view('user.master.karyawan.import.tunjangan.template.index', [
            'title' => $title,
            'masakerja' => $masakerja,
            'address' => getSetting('ADDRESS'),
        ])->render();
            // echo $htmlString;
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($htmlString);
        // $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('@');
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
        // ob_start();
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
    }

    public function import(Request $request)
    {
        // return response()->json([
        //     'success' => false,
        //     'message' => 'Masih dalam pengembangan!',
        // ]);
        ini_set('memory_limit', '512M'); 
        set_time_limit(300);

        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $sttunjangan = SettingTunjanganKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'sttunjangankaryawan_active' => 1,
        ])->get();
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }
        $file = $request->file('file');
        // dd($file->extension());
        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }
        $filename = rand().'.xlsx';
        // Storage::disk('public_uploads')->put($filename, file_get_contents($file));
        $file->move(public_path('uploads/'), $filename);
        
        $path = public_path('uploads/'.$filename);
        /**  Identify the type of $inputFileName  **/
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);
        // dd($worksheetData);
        
        $sheetIndex = 0; // rubah sesuai sheet
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $woksheetData = $spreadsheet->getSheet($sheetIndex);
        $temp_array = [];
        $temp_array_masakerja = [];
        $chunkSize = 100; // read as chunk
        $startRow = 3; // mulai baris ke 3;
        $startColumn = 0; // mulai kolom ke 0;
        // $maxRows = $worksheetData[$sheetIndex]['totalRows'] - $startRow;
        $maxRows = $worksheetData[$sheetIndex]['totalRows'];
        $maxColums = $worksheetData[$sheetIndex]['totalColumns'];
        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);
        
        // $karyawan = KaryawanModel::get();
        // $kriteria_namas = [];
        // foreach($kriterias as $k) {
        //     array_push($kriteria_namas, strtolower($k->kriteria_nama));
        // }
        $column_kriteria = [];
        $max_alphabet_column = "Q";
        $new_max_alphabet_column = $max_alphabet_column;
        $isEmpty = false;
        // dd($startRow.' - '.$maxRows.' - '.$worksheetData[$sheetIndex]['totalRows']);
        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;
            // dd($max_chunk);
            // break;
            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb;
                // dd($nbi);
                // check if empty field;
                for($alph = "A"; $alph <= $max_alphabet_column; $alph++) {
                    // dd($woksheetData->getCell($alph.$nbi)->getValue());
                    if(!$woksheetData->getCell($alph.$nbi)->getValue()) {
                        $isEmpty = true;
                        break;
                    }
                }
                if($isEmpty) {
                    break;
                }
                
            }
            if($isEmpty) {
                break;
            }
        }
        // dd($isEmpty);
        // if($isEmpty) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Isi semua data!',
        //     ]);
        // }

        DB::beginTransaction();
        try {
            for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
                // Create a new Instance of our Read Filter
                $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
                // // Tell the Read Filter, the limits on which rows we want to read this iteration
                $chunkFilter->setRows($startRow, $chunkSize);
                // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
                $spreadsheet_chunk = $reader->load($path);
                $nb = $startRow;
                $max_chunk = $startRow + $chunkSize;

                for($nb; $nb<=$max_chunk; $nb++) {
                    // .....
                    // process the file
                    // .....
                    
                    $nbi = $nb;
                    $npwp = $woksheetData->getCell("A".$nbi)->getValue();
                    $nik = str_replace("'", "", $woksheetData->getCell("B".$nbi)->getValue());
                    $karyawan_name = $woksheetData->getCell("C".$nbi)->getValue();
                    // dd($nik);
                    if($npwp && $nik && $karyawan_name) {
                        $prevnbi = 2; // kolom tunjangan
                        $tunjangan = [];
                        for($tcol="D"; $tcol<=$new_max_alphabet_column; $tcol++) {
                            $column_tunjangan_name = $woksheetData->getCell($tcol.$prevnbi)->getValue();
                            if($column_tunjangan_name) {
                                foreach($sttunjangan as $sttj) {
                                    if($sttj->sttunjangankaryawan_name == $column_tunjangan_name) {
                                        $tunjangan[$sttj->sttunjangankaryawan_code] = strval($woksheetData->getCell($tcol.$nbi)->getValue());
                                        break;
                                    }
                                }
                            }
                        }
                        // array_push($temp_array, [
                        //     'karyawanmasakerja_tunjangan' => json_encode($tunjangan),
                        //     'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        // ]);

                        $obj_tunjangan = (object) $tunjangan;
                        KaryawanMasakerjaModel::where([
                            'karyawanmasakerja_active' => 1, 
                            'karyawanmasakerja_nik' => $nik, 
                            'karyawanmasakerja_npwp' => $npwp, 
                            'ms_wajibpajak_id' => $wajibpajak_id,
                        ])->update([
                            'karyawanmasakerja_tunjangan' => json_encode($obj_tunjangan),
                        ]);
                    }
                }
                // then release the memory
                $spreadsheet_chunk->__destruct();
                $spreadsheet_chunk = null;
                unset($spreadsheet_chunk);
            }
            DB::commit();

            // then release the memory
            $spreadsheet->__destruct();
            $spreadsheet = null;
            unset($spreadsheet);
            $reader = null;
            // // $reader->__destruct();
            unset($reader);

            return response()->json([
                'success' => true,
                'message' => 'Import Tunjangan berhasil.',
            ]);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            DB::rollback();

            // then release the memory
            // dd($spreadsheet);
            // $spreadsheet->__destruct();
            // $spreadsheet = null;
            // unset($spreadsheet);
            // $reader = null;
            // // // $reader->__destruct();
            // unset($reader);
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                // 'filename' => $filename
            ]);
        }
        
    }
}

/**  Define a Read Filter class implementing IReadFilter  */
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