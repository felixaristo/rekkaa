<?php
namespace App\Http\Controllers\User\Import;

use App\Http\Controllers\Controller;
use App\Model\Master\CountryModel;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
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
use stdClass;

class KaryawanImportProfilController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data = [
            'title' => 'Import Profil Karyawan',
            'content' => 'user.master.karyawan.import.profil.index',
        ];
        // dd($data['karyawan']);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function import(Request $request)
    {
        $ptkps = PtkpModel::where(['ptkp_active' => 1])->get();
        $countries = CountryModel::get();
        $bpjsData = SettingBpjsKaryawanModel::where([
            'stbpjskaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->get();
        $tunjanganData = SettingTunjanganKaryawanModel::where([
            'sttunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->get();

        $bpjsTK = new stdClass();
        $bpjsKes = new stdClass();
        foreach($bpjsData as $bpdt) {
            $key = str_replace(' ', '_', $bpdt->stbpjskaryawan_name);
            if($bpdt->stbpjskaryawan_type == 'TENAGA_KERJA') {
                $bpjsTK->$key = $bpdt->stbpjskaryawan_value;
            }
            if($bpdt->stbpjskaryawan_type == 'KESEHATAN') {
                $bpjsKes->$key = $bpdt->stbpjskaryawan_value;
            }
        }
        $bpjs = [
            'TENAGA_KERJA' => ($bpjsTK) ? $bpjsTK : (object)[],
            'KESEHATAN' => ($bpjsKes) ? $bpjsKes : (object)[],
        ];

        $tunjangan = new stdClass();
        foreach($tunjanganData as $tdt) {
            $key = str_replace(' ', '_', $tdt->sttunjangankaryawan_code);
            $tunjangan->$key = $tdt->sttunjangankaryawan_value;
        }

        ini_set('memory_limit', '512M'); 
        set_time_limit(300);
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
        // dd($worksheetData[$sheetIndex]);
        // $reader->setLoadSheetsOnly($sheetIndex);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        // $woksheetData = $spreadsheet->getSheet(2);
        // $woksheetData = $spreadsheet->getSheetByName('Sheet4');
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
        // for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
        //     $nb = $startRow;
        //     $max_chunk = $startRow + $chunkSize;
        //     // dd($max_chunk);
        //     // break;
        //     for($nb; $nb<=$max_chunk; $nb++) {
        //         $nbi = $nb + 1;
        //         // dd($nbi);
        //         // check if empty field;
        //         for($alph = "A"; $alph <= $max_alphabet_column; $alph++) {
        //             // dd($woksheetData->getCell($alph.$nbi)->getValue());
        //             if(!$woksheetData->getCell($alph.$nbi)->getValue()) {
        //                 $isEmpty = true;
        //                 break;
        //             }
        //         }
        //         if($isEmpty) {
        //             break;
        //         }
                
        //     }
        //     if($isEmpty) {
        //         break;
        //     }
        // }
        // dd($isEmpty);
        // if($isEmpty) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Isi semua data!',
        //     ]);
        // }

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
                
                $nbi = $nb + 1;
                $nik = $woksheetData->getCell("A".$nbi)->getValue();
                // dd($nik);
                if($nik) {
                    $birthdate = $woksheetData->getCell("D".$nbi)->getValue();
                    $birthdateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthdate);
                    // dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthdate));
                    $dt = \Carbon\Carbon::parse($birthdateObj);

                    $contractdate = $woksheetData->getCell("N".$nbi)->getValue();
                    $explode_contractdate = explode(' - ', $contractdate);
                    $dt_cbegin = \Carbon\Carbon::parse($explode_contractdate[0]);
                    $dt_cend = (strtoupper($explode_contractdate[1]) == 'SEKARANG') 
                    ? null 
                    : \Carbon\Carbon::parse($explode_contractdate[1]);

                    $ptkp_id = 0;
                    foreach($ptkps as $ptkp) {
                        if($ptkp->ptkp_description == $woksheetData->getCell("H".$nbi)->getValue()) {
                            $ptkp_id = $ptkp->ptkp_id;
                            break;
                        }
                    }
                    $country_id = null;
                    foreach($countries as $country) {
                        if($country->country_name == strtoupper($woksheetData->getCell("I".$nbi)->getValue())) {
                            $country_id = $country->country_id;
                            break;
                        }
                    }
                    array_push($temp_array, [
                        'karyawan_nik' => $woksheetData->getCell("A".$nbi)->getValue(),
                        'karyawan_npwp' => $woksheetData->getCell("B".$nbi)->getValue(),
                        'karyawan_name' => $woksheetData->getCell("C".$nbi)->getValue(),
                        'karyawan_birthdate' => $dt->translatedFormat('Y-m-d'),
                        'karyawan_phone' => $woksheetData->getCell("E".$nbi)->getValue(),
                        'karyawan_email' => $woksheetData->getCell("F".$nbi)->getValue(),
                        'karyawan_citizenship' => $woksheetData->getCell("G".$nbi)->getValue(),
                        'ms_country_id' => $country_id,
                        'karyawan_gender' => ($woksheetData->getCell("J".$nbi)->getValue() == 'Perempuan') ? 'P' : 'L',
                        'karyawan_address' => $woksheetData->getCell("K".$nbi)->getValue(),
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                    ]);

                    array_push($temp_array_masakerja, [
                        'karyawanmasakerja_nik' => $woksheetData->getCell("A".$nbi)->getValue(),
                        'karyawanmasakerja_npwp' => $woksheetData->getCell("B".$nbi)->getValue(),
                        'ms_ptkp_id' => $ptkp_id,
                        'karyawanmasakerja_status' => strtoupper($woksheetData->getCell("L".$nbi)->getValue()),
                        'ms_objekpajak_code' => $woksheetData->getCell("M".$nbi)->getValue(),
                        'karyawanmasakerja_calculation_method' => strtoupper(str_replace(' ', '_', $woksheetData->getCell("O".$nbi)->getValue())),
                        'karyawanmasakerja_salary' => $woksheetData->getCell("P".$nbi)->getValue(),
                        'karyawanmasakerja_position' => $woksheetData->getCell("Q".$nbi)->getValue(),
                        'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                        'karyawanmasakerja_bpjs' => $bpjs ? json_encode($bpjs) : null,
                        'karyawanmasakerja_tunjangan' => $tunjangan ? json_encode($tunjangan) : null,
                    ]);
                }
            }
            // then release the memory
            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }
        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        // // $reader->__destruct();
        unset($reader);
        // dd($temp_array);
        if($temp_array) {
            DB::beginTransaction();
            try {
                KaryawanModel::insert($temp_array);

                $karyawan = KaryawanModel::where(['karyawan_active' => 1, 'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id']])->get();
                foreach($temp_array_masakerja as &$masakerja) {
                    for($ik=0; $ik<count($karyawan); $ik++) {
                        if($masakerja['karyawanmasakerja_nik'] == $karyawan[$ik]->karyawan_nik && $masakerja['karyawanmasakerja_npwp'] == $karyawan[$ik]->karyawan_npwp) {
                            $masakerja['ms_karyawan_id'] = $karyawan[$ik]->karyawan_id;
                            break;
                        }
                    }
                }

                KaryawanMasakerjaModel::insert($temp_array_masakerja);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Import Profil berhasil.',
                ]);
            } catch (\Exception $e) {
                unlink(public_path('uploads/'). $filename); // remove file
                DB::rollback();
                // something went wrong
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            unlink(public_path('uploads/'). $filename); // remove file
            return response()->json([
                'success' => false,
                'message' => 'Import Profil gagal. Data kosong!',
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