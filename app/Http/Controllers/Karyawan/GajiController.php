<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Libraries\AppDurationLibrary;
use App\Libraries\AppPayslipLibrary;
use App\Libraries\AppPPh21Library;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Mview\VwKaryawanPayrollModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\LemburKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Error;
use Exception;
use Faker\Generator;
use Generator as GlobalGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Spatie\Activitylog\Models\Activity;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;

class GajiController extends Controller
{
    var $maksemaildata = 10;
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Gaji Karyawan',
            'content' => 'karyawan.gaji.gaji-karyawan',
        ];

        $request->request->add(['payroll_period' => date('m-Y')]);

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

     /**
     * Display a detail of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($payrollId, Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $payroll = PayrollModel::with(['karyawan'])->where([
            'payroll_id' => $payrollId,
            'ms_karyawan_id' => $karyawan_id,
            'payroll_status' => '3',
            'payroll_active' => '1'
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$payroll) {
            abort(404);
        }

        // dd($lembur);
        $data = [
            'title' => 'Penggajian',
            'payroll' => $payroll,
            'content' => 'karyawan.gaji.detail-karyawan',
        ];
        // dd($data);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $limit = ($request->get('length')) ? intval($request->get('length')) : 10;
        $offset = ($request->get('start')) ? intval($request->get('start')) : 0;
        $order_columns = ['ms_karyawan_id','payroll_karyawan_name','ms_karyawan_id','payroll_total_netto','payroll_status'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        // dd($periode);
        $request->request->add(['payroll_period' => $periode]);
        $where = [
            // 'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            'payroll_status' => 3,
            'ms_karyawan_id' => $karyawan_id,
        ];
        $list = PayrollModel::with(['karyawan', 'pph21'])
        ->where($where)
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = PayrollModel::where($where)->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        ->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

     /**
     * Display a cetak of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cetak($payrollUuid, Request $request)
    { 
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $payroll = PayrollModel::with(['wajibpajak','karyawan','karyawan.penggajian'])->where([
            'payroll_uuid' => $payrollUuid,
            'ms_karyawan_id' => $karyawan_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$payroll) {
            abort(404);
        }

        $data = [
            'title' => 'Cetak Penggajian',
            'payroll' => $payroll,
            'content' => 'karyawan.gaji.cetak.karyawan-cetak',
        ];
        return view('karyawan.gaji.cetak.karyawan', $data);
    }

}