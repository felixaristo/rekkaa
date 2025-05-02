<?php

namespace App\Jobs;

use App\Http\Controllers\User\Master\KaryawanController;
use App\Http\Controllers\User\Master\NonKaryawanController;
use App\Http\Controllers\User\PajakPenghasilan\PPh21KaryawanController;
use App\Http\Controllers\User\Pengaturan\PengaturanLiburController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = false;

    // protected $request;
    protected $historyid;
    protected $modulename;
    protected $otherdata;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($historyid, $modulename, $otherdata=[])
    {
        //
        // $this->request = $request;
        $this->historyid = $historyid;
        $this->modulename = $modulename;
        $this->otherdata = $otherdata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        
        // Artisan::call('rekkaa:auto-sendemail');
        if($this->modulename == 'SETTING_HOLIDAY') {
            $stliburctrl = new PengaturanLiburController();
            $stliburctrl->importLibur($this->historyid);
        } else if($this->modulename == 'NONEMPLOYEE') {
            $nonkaryawanctrl = new NonKaryawanController();
            $nonkaryawanctrl->importKaryawan($this->historyid, $this->otherdata);
        } else if($this->modulename == 'EMPLOYEE') {
            $karyawanctrl = new KaryawanController();
            $karyawanctrl->importKaryawan($this->historyid, $this->otherdata);
        } else if($this->modulename == 'TAXREVISE') {
            // $pph21ctrl = new PPh21KaryawanController();
            // $pph21ctrl->importPPh21($this->historyid, $this->otherdata);
        }
        // $karyawanctrl = new KaryawanController();
        // $karyawanctrl->testimport($this->filename);
    }
}
