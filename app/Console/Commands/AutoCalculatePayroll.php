<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\App\Operational\CalculatePayslipController;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class AutoCalculatePayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-calculatepayroll {--periodtype=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto calculate the payroll according to the st_penggajian_karyawan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $periodtype = $this->option('periodtype');
        $limit = $this->option('limit') ? $this->option('limit') : 5;
        if(!$periodtype) {
            return $this->output->write('Please fill the period.', false);
        }

        $date = '2024-02-01';
        $payroll_period = date('!Y-m', strtotime("-1 month", strtotime($date)));
        
        $payroll_date_period = date('Y-m-d', strtotime("-1 month", strtotime($date)));
        $payroll_period = date('Y-m', strtotime($payroll_date_period));
        $payroll_date = date('d', strtotime("-1 day", strtotime($payroll_date_period)));
        // dd($payroll_date, $payroll_period);
        $where = [
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_period' => $periodtype,
        ];
        $stpenggajian_karyawan = SettingPenggajianKaryawanModel::select('ms_wajibpajak_id', 'stpenggajiankaryawan_id', 'stpenggajiankaryawan_enddate', 'stpenggajiankaryawan_calculationtime')
        ->where($where)
        ->where(function($query) use ($periodtype, $payroll_date, $payroll_period) {
            if($periodtype == 'TANGGAL') {
                $query->whereRaw("(stpenggajiankaryawan_enddate = ?)", [$payroll_date]);    
            }
            $query->whereRaw("(stpenggajiankaryawan_calculationtime IS NULL OR TO_CHAR(stpenggajiankaryawan_calculationtime, 'YYYY-MM') < ?)", [$payroll_period]);
        })
        ->limit($limit)->orderBy('stpenggajiankaryawan_id', 'asc')->get();
        // dd('test',count($stpenggajian_karyawan[5]->karyawan));
        // dd( date('Y-m-d', strtotime("+1 month", strtotime($periode))));
        // dd($stpenggajian_karyawan);
        foreach($stpenggajian_karyawan as $stpenggajian) {
            $request->request->add([
                'wajibpajak_id' => $stpenggajian->ms_wajibpajak_id,
                'stpenggajian_id' => $stpenggajian->stpenggajiankaryawan_id,
                'payroll_date_period' => $payroll_date_period,
                'periodtype' => $periodtype,
            ]);
            $calculatepayslip = new CalculatePayslipController();
            $calculatepayslip->calculate($request);
        }
    }
}
