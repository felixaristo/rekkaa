<?php
namespace App\Libraries;

use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\LeaveKaryawanModel;
use App\Model\Transaction\LemburKaryawanModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Error;
use Exception;

class AppPayslipLibrary {
    var $karyawan = null;
    var $payroll = null;
    var $stpenggajian_karyawan = null;
    var $attendance_days = null;
    var $tjkaryawan = null;
    var $absence_days = 0;
    var $holiday_karyawan = null;
    var $leave_karyawan = null;
    var $data = null;
    var $payroll_period = null;
    var $prorate_salary = null;
    var $bpjs_tk_gruptunjangan = null;
    var $bpjs_kes_gruptunjangan = null;
    var $bpjs_kes_ditanggung = false;
    var $bpjs_tk_ditanggung = false;
    var $bpjs_kes_gp = false;
    var $bpjs_tk_gp = false;
    var $bpjs_kes_lainnya = null;
    var $bpjs_tk_lainnya = null;
    var $setting = null;
    var $bpjsrate = null;
    var $jkkrate = null;
    var $isbpjs = null;
    var $tunjangan_jabatan = 0;
    var $ratenonnpwp = 100;
    var $custom_allowance_data = [];
    var $custom_deduction_data = [];
    var $pph21_prevcp_netto = 0;
    var $pph21_prevcp_pph21_total = 0;
    

    // for prorate salary
    public function calculate_prorate_salary()
    {
        $karyawan = $this->karyawan;
        $contract_begin = $karyawan->karyawan_contract_begin;
        $payroll = $this->payroll;
        $payroll_period = $this->payroll_period;
        $stpenggajian_karyawan = $this->stpenggajian_karyawan;
        $stpenggajian_method = $stpenggajian_karyawan->stpenggajiankaryawan_method;
        $stpenggajian_angka_tetap = $stpenggajian_karyawan->stpenggajiankaryawan_day;
        $attendance_days = $this->attendance_days;
        // dd($attendance_days);
        $holiday_karyawan = $this->holiday_karyawan;
        $holy_date = [];
        $paid_leave_date = [];
        // check if the payroll setting used specific date
        $total_days = 0;
        $absence_days = 0;
        $paid_leave_days = 0;
        $working_days = 0;
        $holy_days = 0;
        $late_times = [];
        $total_lates = [];

        // if(count($attendance_days) > 0) {
            if($holiday_karyawan) {
                foreach($holiday_karyawan as $hkr) {
                    $start_hdate = $hkr->holiday_start_date;
                    $end_hdate = $hkr->holiday_end_date;
                    while(strtotime($start_hdate) <= strtotime($end_hdate)) {
                        array_push($holy_date, $start_hdate);
                        $start_hdate = date("Y-m-d", strtotime("+1 day", strtotime($start_hdate)));
                    }
                    
                }
            }
            // dd($karyawan);
            // $tunjangan_karyawan_data = [];
            
            
            // if($karyawan->karyawan_id == 122)
                // dd($contract_begin);
            // if($karyawan->st_attendance_id) {
                // dd($stpenggajian_karyawan);

                if($stpenggajian_karyawan->stpenggajiankaryawan_period == 'KALENDER') {
                    $start_date = date('Y-m', strtotime($payroll_period)).'-01';
                    $end_date = date('Y-m', strtotime($payroll_period)).'-'.date('t', strtotime($start_date));
                    // dd($start_date, $end_date);
                    $escape_prev_start_date = DB::connection()->getPdo()->quote($start_date);
                    $escape_current_end_date = DB::connection()->getPdo()->quote($end_date);
                    $trattendances = AttendanceKaryawanModel::where(['st_attendance_id' => $karyawan->st_attendance_id, 'ms_karyawan_id' => $karyawan->karyawan_id])
                        ->whereRaw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') >= {$escape_prev_start_date}")
                        ->whereRaw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') <= {$escape_current_end_date}")
                        ->get();
                    $trleaves = LeaveKaryawanModel::
                    join('st_leave', 'st_leave_id', 'leave_id')
                    ->where(
                        ['ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id, 
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'leavekaryawan_status' => 'APPROVED', 
                        'leave_type' => 'UNPAID',
                    ])
                    ->whereRaw("TO_CHAR(leave_active_start_date, 'YYYY-MM-DD') >= {$escape_prev_start_date}")
                    ->whereRaw("TO_CHAR(leave_active_end_date, 'YYYY-MM-DD') <= {$escape_current_end_date}")
                    ->get();

                    if($trleaves) {
                        foreach($trleaves as $lkr) {
                            $start_ldate = $lkr->leave_active_start_date;
                            $end_ldate = $lkr->leave_active_end_date;
                            while(strtotime($start_ldate) <= strtotime($end_ldate)) {
                                array_push($paid_leave_date, $start_ldate);
                                $start_ldate = date("Y-m-d", strtotime("+1 day", strtotime($start_ldate)));
                            }
                            
                        }
                    }
                    
                    $absence_dates = [];
                    $late_dates = [];
                    if(count($trattendances) > 0) {
                        foreach($trattendances as $att) {
                            $dt = date('Y-m-d', strtotime($att->attendancekaryawan_check_in));
                            array_push($absence_dates, $dt);

                            $late_dates[$dt] = $att->attendancekaryawan_check_in_late;
                        }
                    }
                    while(strtotime($start_date) <= strtotime($end_date)) {
                        // dd($start_date, $end_date, $attendance_days, $holy_date, $absence_dates, $trattendances, $contract_begin);
                        
                        // check if match
                        $day = ucwords(date('l', strtotime($start_date)));
                        // echo $day;
                        
                        if(count($attendance_days) > 0) {
                            if(in_array($day, $attendance_days)) {
                                $total_days += 1;
                                
                                if(strtotime($start_date) >= strtotime($contract_begin)) {
                                    $working_days += 1;
                                    //     // dd($start_date, $contract_begin);
                                    // }
                                    
                                    if(in_array($start_date, $paid_leave_date)) {
                                        $paid_leave_days += 1;
                                    }
                                    if(in_array($start_date, $holy_date)) {
                                        $holy_days += 1;
                                    }
                                    if(!in_array($start_date, $absence_dates)) {
                                        if(!in_array($start_date, $holy_date)) {
                                            $absence_days += 1;
                                        }
                                    }

                                    if(isset($late_dates[$start_date])) {
                                        if($late_dates[$start_date] != null || $late_dates[$start_date] != '00:00') {
                                            $late_times[$start_date] = $late_dates[$start_date];
                                        }
                                    }
                                }
                            }
                        } else {
                            $total_days += 1;
                            $working_days += 1;
                        }
                        // dd($total_days, $working_days);
                        $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                    }
                    // dd($working_days, 'working_days');
                    
                    // $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $total_days;
                    // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                    // array_push($tunjangan_karyawan_data, $tjkaryawan);
                } else if($stpenggajian_karyawan->stpenggajiankaryawan_period == 'TANGGAL') { // tgl spesifik
                    // check if end date is lower then start date
                    if($stpenggajian_karyawan->stpenggajiankaryawan_enddate <= $stpenggajian_karyawan->stpenggajiankaryawan_startdate) {
                        // get days from previous month
                        $prev_month = date('Y-m', strtotime(date('Y-m', strtotime($payroll_period))." -1 month"));
                        $prev_date = str_pad($stpenggajian_karyawan->stpenggajiankaryawan_startdate, 2, "0", STR_PAD_LEFT);
                        $prev_start_date = $prev_month.'-'.$prev_date;
                        $prev_end_date = $prev_month.'-'.date('t', strtotime($prev_start_date));
                        
                        $current_month = date('Y-m', strtotime($payroll_period));
                        $current_date = str_pad($stpenggajian_karyawan->stpenggajiankaryawan_enddate, 2, "0", STR_PAD_LEFT);
                        $current_start_date = $current_month.'-01';
                        $current_end_date = $current_month.'-'.$current_date;

                        
                            // dd($karyawan->st_attendance_id);
                        $escape_prev_start_date = DB::connection()->getPdo()->quote($prev_start_date);
                        $escape_current_end_date = DB::connection()->getPdo()->quote($current_end_date);
                        $trattendances = AttendanceKaryawanModel::where(['st_attendance_id' => $karyawan->st_attendance_id, 'ms_karyawan_id' => $karyawan->karyawan_id])
                        ->whereRaw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') >= {$escape_prev_start_date}")
                        ->whereRaw("TO_CHAR(attendancekaryawan_check_in, 'YYYY-MM-DD') <= {$escape_current_end_date}")
                        ->get();
                        $trleaves = LeaveKaryawanModel::
                        join('st_leave', 'st_leave_id', 'leave_id')
                        ->where(
                            ['ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id, 
                            'ms_karyawan_id' => $karyawan->karyawan_id,
                            'leavekaryawan_status' => 'APPROVED', 
                            'leave_type' => 'PAID',
                        ])
                        ->whereRaw("TO_CHAR(leave_active_start_date, 'YYYY-MM-DD') >= {$escape_prev_start_date}")
                        ->whereRaw("TO_CHAR(leave_active_end_date, 'YYYY-MM-DD') <= {$escape_current_end_date}")
                        ->get();
                        // dd($trattendances);
                        // dd($prev_start_date, $current_end_date);

                        if($trleaves) {
                            foreach($trleaves as $lkr) {
                                $start_ldate = $lkr->leave_active_start_date;
                                $end_ldate = $lkr->leave_active_end_date;
                                while(strtotime($start_ldate) <= strtotime($end_ldate)) {
                                    array_push($leave_date, $start_ldate);
                                    $start_ldate = date("Y-m-d", strtotime("+1 day", strtotime($start_ldate)));
                                }
                                
                            }
                        }

                        $absence_dates = [];
                        $late_dates = [];
                        if(count($trattendances) > 0) {
                            foreach($trattendances as $att) {
                                $dt = date('Y-m-d', strtotime($att->attendancekaryawan_check_in));
                                array_push($absence_dates, $dt);

                                $late_dates[$dt] = $att->attendancekaryawan_check_in_late;
                            }
                        }
                        // dd($trattendances);
                        // dd($absence_dates);
                        // dd($prev_start_date, $prev_end_date, $attendance_days);
                        while(strtotime($prev_start_date) <= strtotime($prev_end_date)) {
                            
                            // check if match
                            $day = ucwords(date('l', strtotime($prev_start_date)));
                            // echo $day;
                            if(count($attendance_days) > 0) {
                                if(in_array($day, $attendance_days)) {
                                    $total_days += 1;
                                    if(strtotime($prev_start_date) >= strtotime($contract_begin)) {
                                        $working_days += 1;
                                        // }
                                        // if(!in_array($prev_start_date, $absence_dates)) {
                                        //     if(!in_array($prev_start_date, $holy_date)) {
                                        //         $absence_days += 1;
                                        //     }
                                        // }
                                        if(in_array($prev_start_date, $paid_leave_date)) {
                                            $paid_leave_days += 1;
                                        }
                                        if(in_array($prev_start_date, $holy_date)) {
                                            $holy_days += 1;
                                        }
                                        if(!in_array($prev_start_date, $absence_dates)) {
                                            if(!in_array($prev_start_date, $holy_date)) {
                                                $absence_days += 1;
                                            }
                                        }

                                        if(isset($late_dates[$current_start_date])) {
                                            if($late_dates[$current_start_date] != null || $late_dates[$current_start_date] != '00:00') {
                                                $late_times[$current_start_date] = $late_dates[$current_start_date];
                                            }
                                        }
                                    }
                                }
                            } else {
                                $total_days += 1;
                                $working_days += 1;
                            }

                            $prev_start_date = date("Y-m-d", strtotime("+1 day", strtotime($prev_start_date)));
                        }
                        
                        while(strtotime($current_start_date) <= strtotime($current_end_date)) {
                            
                            // check if match
                            $day = ucwords(date('l', strtotime($current_start_date)));
                            // echo $day;
                            if(count($attendance_days) > 0) {
                                if(in_array($day, $attendance_days)) {
                                    $total_days += 1;
                                    if(strtotime($current_start_date) >= strtotime($contract_begin)) {
                                        $working_days += 1;
                                        // echo($current_start_date);
                                        // echo "<pre>";
                                        // print_r($absence_dates);
                                        // if(!in_array($current_start_date, $absence_dates)) {
                                        //     if(!in_array($current_start_date, $holy_date)) {
                                        //         $absence_days += 1;
                                        //     }
                                        // }
                                        if(in_array($prev_start_date, $paid_leave_date)) {
                                            $paid_leave_days += 1;
                                        }
                                        if(in_array($current_start_date, $holy_date)) {
                                            $holy_days += 1;
                                        }
                                        if(!in_array($current_start_date, $absence_dates)) {
                                            if(!in_array($current_start_date, $holy_date)) {
                                                $absence_days += 1;
                                            }
                                        }

                                        if(isset($late_dates[$current_start_date])) {
                                            if($late_dates[$current_start_date] != null || $late_dates[$current_start_date] != '00:00') {
                                                // array_push($late_times, $late_dates[$current_start_date]);
                                                $late_times[$current_start_date] = $late_dates[$current_start_date];
                                            }
                                        }
                                    }
                                }
                            } else {
                                $total_days += 1;
                                $working_days += 1;
                            }

                            $current_start_date = date("Y-m-d", strtotime("+1 day", strtotime($current_start_date)));
                        }
                    } else {
                        // if(strtotime(date('Y-m-d')) >= strtotime($contract_begin)) {
                        //     $dtcontract = date('d', strtotime($contract_begin));
                        //     if($stpenggajian_karyawan->stpenggajiankaryawan_enddate > $dtcontract) {
                        //         $total_days = $stpenggajian_karyawan->stpenggajiankaryawan_enddate - $dtcontract;
                        //     } else if($stpenggajian_karyawan->stpenggajiankaryawan_startdate < $dtcontract) {
                        //         $total_days = $dtcontract - $stpenggajian_karyawan->stpenggajiankaryawan_startdate;
                        //     } 
                            // else {
                                $total_days = $stpenggajian_karyawan->stpenggajiankaryawan_enddate - $stpenggajian_karyawan->stpenggajiankaryawan_startdate;
                            // }
                        // }
                        
                    }
                    // dd($absence_days);
                    // $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $total_days;
                    // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                }
            // }
            // dd($absence_days);
            // $working_days = $total_days - $absence_days;
            // dd($working_days);
            // $working_days = $total_days;
            // dd($stpenggajian_angka_tetap);
            // dd($working_days);
            
            if(count($late_times) > 0) {
                $firstlate = null;
                $i=0;
                $temp_time = null;
                foreach($late_times as $val) {
                    if($i > 0) {
                        $secondlate = $val.':00';
                        $temp_time = AppDurationLibrary::fromString($firstlate);
                        $temp_time->add(AppDurationLibrary::fromString($secondlate));
                        $firstlate = $temp_time->getHours().':'.$temp_time->getMinutes().':00';
                    } else {
                        $firstlate = $val.':00';
                        if(count($late_times) == 1) {
                            $secondlate = '00:00:00';
                            $temp_time = AppDurationLibrary::fromString($firstlate);
                            $temp_time->add(AppDurationLibrary::fromString($secondlate));
                        }
                    }
                    $i++;
                }
                $total_lates = [$temp_time->getHours(), $temp_time->getMinutes(), $temp_time->getSeconds()];
            }

            // $working_days = $total_days - $holy_days - $absence_days + $paid_leave_days;
            // $working_days = $working_days - $absence_days;
        // } else {
        //     $working_days = $total_days;
        // }

        if($stpenggajian_method == 'TETAP' && $working_days < $total_days) {
            $total_days = $stpenggajian_angka_tetap;
        }
        // dd($total_days, $working_days);
        // if($total_days > 0) {
        //     $prorate_salary = $working_days / $total_days * $karyawan->karyawan_salary;
        // } else {
        //     $prorate_salary = $karyawan->karyawan_salary;
        // }
        // dd($working_days, $karyawan->attendance);
        if($karyawan->attendance && $karyawan->attendance->attendance_is_used == 1) {
            if(count($trattendances) <= 0) {
                $working_days = 0;
            }
        }
        // dd($working_days, $karyawan->attendance);
        if($working_days > 0) {
            $prorate_salary = $working_days / $total_days * $karyawan->karyawan_salary;
        } else {
            if($karyawan->attendance && $karyawan->attendance->attendance_is_used == 1) {
                $prorate_salary = $working_days / $total_days * $karyawan->karyawan_salary;
            } else {
                $prorate_salary = $karyawan->karyawan_salary;
            }
        }
        // dd($working_days, $prorate_salary, $contract_begin, $start_date, $end_date);

        return [
            'total_days' => $total_days,
            'absence_days' => $absence_days,
            'working_days' => $working_days,
            'attendance_days' => $attendance_days,
            'paid_leave_days' => $paid_leave_days,
            'paid_leave_date' => $paid_leave_date,
            'holy_days' => $holy_days,
            'holy_date' => $holy_date,
            'late_times' => $late_times,
            'total_lates' => $total_lates,
            'basic_salary' => $karyawan->karyawan_salary,
            'prorate_salary' => intval($prorate_salary),
        ];
    }

    // for accumulate the allowance
    public function accumulate_tunjangan()
    {
        $stpenggajian_karyawan = $this->stpenggajian_karyawan;
        $tjkaryawan = $this->tjkaryawan;
        $attendance_days = $this->attendance_days;
        $prorate_salary = $this->prorate_salary;
        $absence_days = $this->absence_days;
        $working_days = $prorate_salary['working_days'];
        $total_days = $prorate_salary['total_days'];
        // $karyawan = $this->karyawan;
        // $payroll = $this->payroll;
        // dd($prorate_salary);
        $payroll_period = $this->payroll_period;
        // check if the payroll setting used specific date
        // $total_days = 0;
        $tunjangan_nominal = 0;
        // $tunjangan_nominal_pph21 = 0;
        // $tunjangan_karyawan_data = [];
        // dd($stpenggajian_karyawan);
        // dd($payroll_period);
        if($stpenggajian_karyawan->stpenggajiankaryawan_period == 'KALENDER') {
            // if($absence_days > 0) {
            //     $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $absence_days;
            // } else {
                $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $total_days;
                // dd($total_days, $tjkaryawan->sttunjangankaryawan_value);
            // }  
            $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
            // dd($tunjangan_nominal);
            // array_push($tunjangan_karyawan_data, $tjkaryawan);
        } else if($stpenggajian_karyawan->stpenggajiankaryawan_period == 'TANGGAL') { // tgl spesifik
            // if($absence_days > 0) {
            //     $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $absence_days;
            // } else {
                $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value * $total_days;
            // }
            $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
        }

        return [
            'total_days' => $total_days,
            'tunjangan_nominal' => $tunjangan_nominal,
            'tunjangan_karyawan' => $tjkaryawan,
        ];
    }

    // calculate the allowances
    public function calculate_tunjangan()
    {
        $data = $this->data;
        // $payroll = $data['payroll'];
        $karyawan = $data['karyawan'];
        $sttunjangan = $data['sttunjangan'];
        // if($karyawan->karyawan_id == 122)
        //     dd($karyawan->sttunjangantbl);
        $current_salary = $data['current_salary'];
        $stpenggajian_karyawan = $data['stpenggajian_karyawan'];
        $wajibpajak_id = $data['wajibpajak_id'];
        $attendance_days = $data['attendance_days'];
        $current_month = $data['current_month'];
        $tjkaryawan_ids = $data['tjkaryawan_ids'];
        $grouptunjangan_ids = (isset($data['grouptunjangan_ids'])) ? $data['grouptunjangan_ids'] : [];
        $absence_days = (isset($data['absence_days'])) ? $data['absence_days'] : 0;
        
        $tunjangan_nominal = 0;
        $tunjangan_nominal_pph21 = 0;
        $tunjangan_karyawan_data = [];
        $sttunjangan_karyawan = ($sttunjangan) ? json_decode($sttunjangan) : [];
        $appPayslipLib = new AppPayslipLibrary();
        // $appPayslipLib->payroll = $payroll;
        $appPayslipLib->karyawan = $karyawan;
        $appPayslipLib->stpenggajian_karyawan = $stpenggajian_karyawan;
        $appPayslipLib->attendance_days = $attendance_days;
        $appPayslipLib->prorate_salary = $data['prorate_salary'];
        $appPayslipLib->absence_days = $absence_days;
        // dd($attendance_days);
        
        $payroll_period = Carbon::parse($this->payroll_period)->floorMonth();
        $payroll_periodm = Carbon::parse($this->payroll_period)->format('m');
        $contract_begin = Carbon::parse($karyawan->karyawan_contract_begin)->floorMonth();
        $contract_beginm = Carbon::parse($karyawan->karyawan_contract_begin)->format('m');
        // dd($payroll_periodm, $contract_beginm);
        $diffmonths = $contract_begin->diffInMonths($payroll_period);
        // dd($payroll_period, $contract_begin, $diffmonths);
        foreach($sttunjangan_karyawan as $tjkaryawan) {
            // $tjkaryawan->stgrouptunjangan = ;
            if($grouptunjangan_ids) {
                if(in_array($tjkaryawan->st_grouptunjangankaryawan_id, $grouptunjangan_ids)) {
                        // echo $tunjangan_nominal.' tunjangan_nominal ';
                        if($tjkaryawan->sttunjangankaryawan_period == 'BULAN') {
                            if($tjkaryawan->sttunjangankaryawan_calculation == 'JUMLAH_TETAP') {
                                $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value;
                                $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                    $tunjangan_nominal_pph21 += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                }
                            } 
                            // else if($tjkaryawan->sttunjangankaryawan_calculation == 'FORMULA') {
                            //     // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_value;
                            //     $stformula = ($tjkaryawan->sttunjangankaryawan_formula) ? json_decode($tjkaryawan->sttunjangankaryawan_formula) : [];
                                
                            //     $formula = '';
                            //     $operator_exist = false;
                            //     foreach($stformula as $fm) {
                            //         if($fm->value == 'GP') {
                            //             $formula .= $current_salary;
                            //         } else if(is_numeric($fm->value) && is_numeric($fm->text) == false) {
                            //             $formula_tunjangan_total = 0;
                            //             if($tjkaryawan->tunjangan_json) {
                            //                 $decode_tunjangan = ($tjkaryawan->tunjangan_json) ? $tjkaryawan->tunjangan_json : [];
                            //                 if($decode_tunjangan) {
                            //                     foreach($decode_tunjangan as $dtj) {
                            //                         if($dtj->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                            //                             $appPayslipLib->tjkaryawan = $dtj;
                            //                             $tjdata = $appPayslipLib->accumulate_tunjangan();
                            //                             $formula_tunjangan_total += $tjdata['tunjangan_nominal'];
                            //                         } else if($dtj->sttunjangankaryawan_period == 'BULAN') {
                            //                             $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                            //                             $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                            //                         } else if($dtj->sttunjangankaryawan_period == 'TAHUN') {
                            //                             if($dtj->sttunjangankaryawan_paymentperiod == $current_month) {
                            //                                 $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                            //                                 $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                            //                             }
                            //                         }
                            //                     }
                            //                 }
                            //             }
                            //             $formula .= $formula_tunjangan_total;
                            //         } else {
                            //             $operator = array('(',')','+','-','*','/','%');
                            //             if(in_array($fm->value, $operator)){
                            //                 $operator_exist = true;
                            //             }
                            //             $formula .= (is_numeric($fm->value)) ? intval($fm->value) : $fm->value;
                            //         }
                            //     }
                            //     // dd($formula);
                            //     // dd($operator_exist);
                            //     $formula_total = 0;
                            //     if($operator_exist){
                            //         try {
                            //             $formula_total = eval('return '.$formula. ';');
                            //             $tunjangan_nominal += $formula_total;
                            //             if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                            //                 $tunjangan_nominal_pph21 += $formula_total;
                            //             }
                            //             // echo $formula.' formula';
                            //         } catch (Error $e) {
                            //             throw new Exception('Formula di pengaturan Tunjangan tidak sesuai. '. $e->getMessage());
                            //         }
                            //     } else {
                            //         // dd($formula);
                            //         $formula_total = $formula;
                            //         $tunjangan_nominal += $formula_total;
                            //         if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                            //             $tunjangan_nominal_pph21 += $formula_total;
                            //         }
                            //     }
                            //     $tjkaryawan->sttunjangankaryawan_accumulate_value = $formula_total;
    
                            //     // dd($tunjangan_nominal,'oioi');
                            // }
    
                            array_push($tunjangan_karyawan_data, $tjkaryawan);
                        }
                        if($tjkaryawan->sttunjangankaryawan_period == 'TAHUN') {
                            if($tjkaryawan->sttunjangankaryawan_paymentperiod == $current_month) {
                                if($tjkaryawan->sttunjangankaryawan_calculation == 'JUMLAH_TETAP') {
                                    $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value;
                                    $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                    if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                        $tunjangan_nominal_pph21 += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                    }
                                } else if($tjkaryawan->sttunjangankaryawan_calculation == 'FORMULA') {
                                    // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_value;
                                    $stformula = ($tjkaryawan->sttunjangankaryawan_formula) ? json_decode($tjkaryawan->sttunjangankaryawan_formula) : [];
                                    
                                    $formula = '';
                                    $operator_exist = false;
                                    $wday = (count($attendance_days) > 0) ? count($attendance_days) : 7;
                                    if($this->prorate_salary['working_days'] >= $wday) {
                                        $diffmonths += 1;
                                    }

                                    $proratemonths = $diffmonths / 12;
                                    if($proratemonths >= 1) {
                                        $proratemonths = 1;
                                    } else {
                                        $proratemonths = round($proratemonths, 2);
                                        $isproratemonths = true;
                                    }

                                    foreach($stformula as $fm) {
                                        if($fm->value == 'GP') {
                                            // if($diffmonths < 12 && $tjkaryawan->sttunjangankaryawan_type == 'THR') {
                                                
                                            // }
                                            // if($diffmonths < 12 && $tjkaryawan->sttunjangankaryawan_type == 'THR') {
                                            //     $proratemonths = $diffmonths / 12;
                                            //     $formula .= '('.$current_salary .' * '.$proratemonths.')';   
                                            // } else {
                                                $formula .= $current_salary;
                                            // }
                                        } else if(is_numeric($fm->value) && is_numeric($fm->text) == false) {
                                            $formula_tunjangan_total = 0;
                                            if($tjkaryawan->tunjangan_json) {
                                                $decode_tunjangan = $tjkaryawan->tunjangan_json;
                                                if($decode_tunjangan) {
                                                    foreach($decode_tunjangan as $dtj) {
                                                        if($dtj->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                                                            // $tjdata = $this->accumulate_tunjangan($stpenggajian_karyawan, $dtj, $attendance_days, $absence_days);
                                                            $appPayslipLib->tjkaryawan = $dtj;
                                                            $tjdata = $appPayslipLib->accumulate_tunjangan();
                                                            $formula_tunjangan_total += $tjdata['tunjangan_nominal'];
                                                        } else if($dtj->sttunjangankaryawan_period == 'BULAN') {
                                                            $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                                                            $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                                                        } else if($dtj->sttunjangankaryawan_period == 'TAHUN') {
                                                            if($dtj->sttunjangankaryawan_paymentperiod == $current_month) {
                                                                $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                                                                $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            $formula .= $formula_tunjangan_total;
                                        } else {
                                            $operator = array('(',')','+','-','*','/','%');
                                            if(in_array($fm->value, $operator)){
                                                $operator_exist = true;
                                            }
                                            $formula .= (is_numeric($fm->value)) ? intval($fm->value) : $fm->value;
                                        }
                                    }
                                    if($isproratemonths) {
                                        $formula .= ' * ' . $proratemonths;
                                    }
                                    $formula_total = 0;
                                    if($operator_exist){
                                        try {
                                            $formula_total = eval('return '.$formula. ';');
                                            $tunjangan_nominal += $formula_total;
                                            if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                                $tunjangan_nominal_pph21 += $formula_total;
                                            }
                                        } catch (Error $e) {
                                            throw new Exception('Formula di pengaturan Tunjangan tidak sesuai. '. $e->getMessage());
                                        }
                                    } else {
                                        if($isproratemonths) {
                                            $formula_total = eval('return '.$formula. ';');
                                        } else {
                                            $formula_total = $formula;
                                        }
                                        $tunjangan_nominal += $formula_total;
                                        if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                            $tunjangan_nominal_pph21 += $formula_total;
                                        }
                                    }
                                    // $tjkaryawan->sttunjangankaryawan_value = $formula_total;
                                    $tjkaryawan->sttunjangankaryawan_accumulate_value = $formula_total;
                                }
    
                                array_push($tunjangan_karyawan_data, $tjkaryawan);
                            }
                        }
                        if($tjkaryawan->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                            if($stpenggajian_karyawan) {
                                // $tjdata = $this->accumulate_tunjangan($stpenggajian_karyawan, $tjkaryawan, $attendance_days, $absence_days);
                                $appPayslipLib->tjkaryawan = $tjkaryawan;
                                $tjdata = $appPayslipLib->accumulate_tunjangan();
                                // dd($tjdata);
                                $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjdata['tunjangan_nominal'];
                                $tunjangan_nominal += $tjdata['tunjangan_nominal'];
                                if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                    $tunjangan_nominal_pph21 += $tjdata['tunjangan_nominal'];
                                }
                                array_push($tunjangan_karyawan_data, $tjdata['tunjangan_karyawan']);
                            }
                        }
                    // }
                }
            } else {
                
                // if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                    // echo $tunjangan_nominal.' tunjangan_nominal ';
                    if($tjkaryawan->sttunjangankaryawan_period == 'BULAN') {
                        if($tjkaryawan->sttunjangankaryawan_calculation == 'JUMLAH_TETAP') {
                            $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value;
                            $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                            if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                $tunjangan_nominal_pph21 += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                            }
                        } 
                        // else if($tjkaryawan->sttunjangankaryawan_calculation == 'FORMULA') {
                        //     // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_value;
                        //     $stformula = ($tjkaryawan->sttunjangankaryawan_formula) ? json_decode($tjkaryawan->sttunjangankaryawan_formula) : [];
                            
                        //     $formula = '';
                        //     $operator_exist = false;
                        //     foreach($stformula as $fm) {
                        //         if($fm->value == 'GP') {
                        //             $formula .= $current_salary;
                        //         } else if(is_numeric($fm->value) && is_numeric($fm->text) == false) {
                        //             $formula_tunjangan_total = 0;
                        //             if($tjkaryawan->tunjangan_json) {
                        //                 $decode_tunjangan = $tjkaryawan->tunjangan_json; //$tjkaryawan->tunjangan_json;
                        //                 if($decode_tunjangan) {
                        //                     foreach($decode_tunjangan as $dtj) {
                        //                         if($dtj->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                        //                             // $tjdata = $this->accumulate_tunjangan($stpenggajian_karyawan, $dtj, $attendance_days, $absence_days);
                        //                             $appPayslipLib->tjkaryawan = $dtj;
                        //                             $tjdata = $appPayslipLib->accumulate_tunjangan();

                        //                             $formula_tunjangan_total += $tjdata['tunjangan_nominal'];
                        //                         } else if($dtj->sttunjangankaryawan_period == 'BULAN') {
                        //                             $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                        //                             $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                        //                         } else if($dtj->sttunjangankaryawan_period == 'TAHUN') {
                        //                             if($dtj->sttunjangankaryawan_paymentperiod == $current_month) {
                        //                                 $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                        //                                 $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                        //                             }
                        //                         }
                        //                     }
                        //                 }
                        //             }
                        //             $formula .= $formula_tunjangan_total;
                        //         } else {
                        //             $operator = array('(',')','+','-','*','/','%');
                        //             if(in_array($fm->value, $operator)){
                        //                 $operator_exist = true;
                        //             }
                        //             $formula .= (is_numeric($fm->value)) ? intval($fm->value) : $fm->value;
                        //         }
                        //     }
                        //     // dd($formula);
                        //     // dd($operator_exist);
                        //     $formula_total = 0;
                        //     if($operator_exist){
                        //         try {
                        //             $formula_total = eval('return '.$formula. ';');
                        //             $tunjangan_nominal += $formula_total;
                        //             if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                        //                 $tunjangan_nominal_pph21 += $formula_total;
                        //             }
                        //             // echo $formula.' formula';
                        //         } catch (Error $e) {
                        //             throw new Exception('Formula di pengaturan Tunjangan tidak sesuai. '. $e->getMessage());
                        //         }
                        //     } else {
                        //         // dd($formula);
                        //         $formula_total = $formula;
                        //         $tunjangan_nominal += $formula_total;
                        //         if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                        //             $tunjangan_nominal_pph21 += $formula_total;
                        //         }
                        //     }
                        //     $tjkaryawan->sttunjangankaryawan_accumulate_value = $formula_total;

                        //     // dd($tunjangan_nominal,'oioi');
                        // }

                        array_push($tunjangan_karyawan_data, $tjkaryawan);
                    }
                    if($tjkaryawan->sttunjangankaryawan_period == 'TAHUN') {
                        if($tjkaryawan->sttunjangankaryawan_paymentperiod == $current_month) {
                            if($tjkaryawan->sttunjangankaryawan_calculation == 'JUMLAH_TETAP') {
                                $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjkaryawan->sttunjangankaryawan_value;
                                $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                    $tunjangan_nominal_pph21 += $tjkaryawan->sttunjangankaryawan_accumulate_value;
                                }
                            } else if($tjkaryawan->sttunjangankaryawan_calculation == 'FORMULA') {
                                // $tunjangan_nominal += $tjkaryawan->sttunjangankaryawan_value;
                                $stformula = ($tjkaryawan->sttunjangankaryawan_formula) ? json_decode($tjkaryawan->sttunjangankaryawan_formula) : [];
                                
                                $formula = '';
                                $operator_exist = false;
                                
                                $wday = (count($attendance_days) > 0) ? count($attendance_days) : 7;
                                if($this->prorate_salary['working_days'] >= $wday) {
                                    $diffmonths += 1;
                                }
                                $proratemonths = $diffmonths / 12;
                                if($proratemonths >= 1) {
                                    $proratemonths = 1;
                                } else {
                                    $proratemonths = round($proratemonths, 2);
                                    $isproratemonths = true;
                                }
                                foreach($stformula as $fm) {
                                    // if($diffmonths < 12 && $tjkaryawan->sttunjangankaryawan_type == 'THR') {
                                    //     $isproratemonths = true;
                                    //     $proratemonths = $diffmonths / 12;
                                    // }
                                    if($fm->value == 'GP') {
                                        //     $formula .= '('.$current_salary .' * '.$proratemonths.')';   
                                        // } else {
                                            $formula .= $current_salary;
                                        // }
                                        // dd($formula, $diffmonths);
                                    } else if(is_numeric($fm->value) && is_numeric($fm->text) == false) {
                                        // dd($fm->value);
                                        // dd($tjkaryawan->tunjangan_json);
                                        $formula_tunjangan_total = 0;
                                        if($tjkaryawan->tunjangan_json) {
                                            $decode_tunjangan = $tjkaryawan->tunjangan_json;
                                            if($decode_tunjangan) {
                                                foreach($decode_tunjangan as $dtj) {
                                                    if($dtj->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                                                        // $tjdata = $this->accumulate_tunjangan($stpenggajian_karyawan, $dtj, $attendance_days, $absence_days);
                                                        $appPayslipLib->tjkaryawan = $dtj;
                                                        $tjdata = $appPayslipLib->accumulate_tunjangan();

                                                        $formula_tunjangan_total += $tjdata['tunjangan_nominal'];
                                                    } else if($dtj->sttunjangankaryawan_period == 'BULAN') {
                                                        $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                                                        $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                                                    } else if($dtj->sttunjangankaryawan_period == 'TAHUN') {
                                                        if($dtj->sttunjangankaryawan_paymentperiod == $current_month) {
                                                            $dtj->sttunjangankaryawan_accumulate_value = $dtj->sttunjangankaryawan_value;
                                                            $formula_tunjangan_total += $dtj->sttunjangankaryawan_accumulate_value;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // dd($formula_tunjangan_total);
                                        
                                        $formula .= $formula_tunjangan_total;
                                    } else {
                                        $operator = array('(',')','+','-','*','/','%');
                                        if(in_array($fm->value, $operator)){
                                            $operator_exist = true;
                                        }
                                        $formula .= (is_numeric($fm->value)) ? intval($fm->value) : $fm->value;
                                        
                                    }
                                }
                                
                                if($isproratemonths) {
                                    $formula .= ' * ' . $proratemonths;
                                }
                                // dd($this->prorate_salary);
                                // dd($isproratemonths, $proratemonths, $diffmonths, $formula);
                                // dd($formula, $stformula, $proratemonths, $diffmonths);
                                
                                $formula_total = 0;
                                if($operator_exist){
                                    try {
                                        $formula_total = eval('return '.$formula. ';');
                                        // dd($formula);
                                        $tunjangan_nominal += $formula_total;
                                        if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                            $tunjangan_nominal_pph21 += $formula_total;
                                        }
                                    } catch (Error $e) {
                                        throw new Exception('Formula di pengaturan Tunjangan tidak sesuai. '. $e->getMessage());
                                    }
                                } else {
                                    if($isproratemonths) {
                                        $formula_total = eval('return '.$formula. ';');
                                    } else {
                                         $formula_total = $formula;
                                    }
                                    // dd($formula_total);
                                    $tunjangan_nominal += $formula_total;
                                    if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                        $tunjangan_nominal_pph21 += $formula_total;
                                    }
                                }
                                // $tjkaryawan->sttunjangankaryawan_value = $formula_total;
                                $tjkaryawan->sttunjangankaryawan_accumulate_value = $formula_total;
                            }

                            array_push($tunjangan_karyawan_data, $tjkaryawan);
                        }
                    }
                    if($tjkaryawan->sttunjangankaryawan_period == 'HARI') { // if Daily Allowance
                        // dd($stpenggajian_karyawan);
                        if($stpenggajian_karyawan) {
                            // $tjdata = $this->accumulate_tunjangan($stpenggajian_karyawan, $tjkaryawan, $attendance_days, $absence_days);
                            $appPayslipLib->tjkaryawan = $tjkaryawan;
                            $tjdata = $appPayslipLib->accumulate_tunjangan();
                            // dd($tjdata);
                            $tjkaryawan->sttunjangankaryawan_accumulate_value = $tjdata['tunjangan_nominal'];
                            $tunjangan_nominal += $tjdata['tunjangan_nominal'];
                            if($tjkaryawan->sttunjangankaryawan_taxable == 1) {
                                $tunjangan_nominal_pph21 += $tjdata['tunjangan_nominal'];
                            }
                            array_push($tunjangan_karyawan_data, $tjdata['tunjangan_karyawan']);
                        }
                    }
                // }
            }
        }

        return [
            'tunjangan_nominal' => $tunjangan_nominal,
            'tunjangan_nominal_pph21' => $tunjangan_nominal_pph21,
            'tunjangan_karyawan_data' => $tunjangan_karyawan_data,
        ];
    }

    public function calculate()
    {
        $karyawan = $this->karyawan;
        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $payroll_method = $karyawan->karyawan_calculation_method;
        $stpenggajian_karyawan = $karyawan->penggajian;
        $prorate_salary = $this->prorate_salary;
        // dd($prorate_salary);
        $attendance_days = $this->attendance_days;
        $periode_month = date('m', strtotime($this->payroll_period));
        $current_month = date('n', strtotime($this->payroll_period)); // no leading zero
        $payroll_periodmy = Carbon::parse($this->payroll_period)->format('m-Y');
        $bpjs_tk_gruptunjangan = $this->bpjs_tk_gruptunjangan;
        $bpjs_kes_gruptunjangan = $this->bpjs_kes_gruptunjangan;
        $bpjs_tk_gp = $this->bpjs_tk_gp;
        $bpjs_kes_gp = $this->bpjs_kes_gp;
        $bpjs_kes_lainnya = $this->bpjs_kes_lainnya;
        $bpjs_tk_lainnya = $this->bpjs_tk_lainnya;
        $setting = $this->setting;
        $bpjsrate = $this->bpjsrate;
        $jkkrate = $this->jkkrate;
        $isbpjs = $this->isbpjs;
        $tunjangan_jabatan = $this->tunjangan_jabatan;
        $bpjs_kes_ditanggung = $this->bpjs_kes_ditanggung;
        $bpjs_tk_ditanggung = $this->bpjs_tk_ditanggung;
        $ratenonnpwp = $this->ratenonnpwp;
        $custom_allowance_data = $this->custom_allowance_data;
        $custom_deduction_data = $this->custom_deduction_data;
        // dd($current_month);
        // dd($attendance_days);

        $stattendance_karyawan_data = $karyawan->attendance;
        $potongan_nominal = 0;
        $potongan_nominal_pph21 = 0;
        $potongan_karyawan_data = [];
        $tunjangan_nominal = 0;
        $tunjangan_nominal_pph21 = 0;
        $tunjangan_karyawan_data = [];
        $custom_tunjangan_karyawan_data = [];
        $custom_pengurangan_karyawan_data = [];
        $current_salary = $prorate_salary['prorate_salary'];
        $absence_days = $prorate_salary['absence_days'];
        // $working_days = $prorate_salary['working_days'];
        $late_times = $prorate_salary['late_times'];
        $total_lates = $prorate_salary['total_lates'];
        // dd($prorate_salary);
        $tjkaryawan = $karyawan->tunjangandetail;
        $tjkaryawan_ids = [];
        foreach($tjkaryawan as $tjk) {
            array_push($tjkaryawan_ids, $tjk->st_tunjangankaryawan_id);
        }

        // Potongan
        $stpotongan_karyawan = $karyawan->potongandetail;
        // dd($stpotongan_karyawan);
        if(count($stpotongan_karyawan) > 0) {
            // dd($stpotongan_karyawan);
            foreach($stpotongan_karyawan as $ptkaryawandet) {
                // dd($ptkaryawan);
                $ptkaryawan = $ptkaryawandet->stpotongan;
                $ptkaryawan->stgrouppotongankaryawan_id = $ptkaryawan->stgrouppotongan->stgrouppotongankaryawan_id;
                $ptkaryawan->stgrouppotongankaryawan_name = $ptkaryawan->stgrouppotongan->stgrouppotongankaryawan_name;
                if($ptkaryawan->stpotongankaryawan_type == 'TETAP') {
                    if($ptkaryawan->stpotongankaryawan_method == 'BULAN') {
                        $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value;
                        $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                            $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        }

                        array_push($potongan_karyawan_data, $ptkaryawan);
                    }

                    else if($ptkaryawan->stpotongankaryawan_method == 'HARI') {
                        $ptkaryawan->stpotongankaryawan_accumulate_value = $prorate_salary['total_days'] * $ptkaryawan->stpotongankaryawan_value;
                        $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                            $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);
                    }
                } 
                else if($ptkaryawan->stpotongankaryawan_type == 'ABSEN') {
                    // dd($ptkaryawan);
                    if($ptkaryawan->stpotongankaryawan_method == 'TETAP') {
                        $ptkaryawan->stpotongankaryawan_accumulate_value = $prorate_salary['absence_days'] * $ptkaryawan->stpotongankaryawan_value;
                        $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                            $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);
                    }

                    else if($ptkaryawan->stpotongankaryawan_method == 'PRORATA') {
                        $formulapotongan_arr = ($ptkaryawan->stpotongankaryawan_formula) ? explode(',', $ptkaryawan->stpotongankaryawan_formula) : [];

                        $this->data = [
                            'payroll' => null,
                            'karyawan' => $karyawan,
                            'sttunjangan' => $karyawan->sttunjangantbl,
                            'current_salary' => $current_salary,
                            'stpenggajian_karyawan' => $stpenggajian_karyawan,
                            'wajibpajak_id' => $wajibpajak_id,
                            'attendance_days' => $attendance_days,
                            'current_month' => $periode_month,
                            'tjkaryawan_ids' => $tjkaryawan_ids,
                            'grouptunjangan_ids' => $formulapotongan_arr,
                            'prorate_salary' => $prorate_salary,
                            'absence_days' => $absence_days
                        ];
                        $calculate_tunjangan = $this->calculate_tunjangan();
                        // dd($calculate_tunjangan);

                        // dd($prorate_salary, $calculate_tunjangan);
                        if(count($calculate_tunjangan['tunjangan_karyawan_data']) > 0) {
                            $stpotongankaryawan_accumulate_value = 0;
                            $formulatjname = [];
                            // dd($potongan_nominal,'aa');
                            // dd($calculate_tunjangan['tunjangan_karyawan_data']);
                            foreach($calculate_tunjangan['tunjangan_karyawan_data'] as $tdt) {
                                // dd($tdt);
                                if($tdt->sttunjangankaryawan_period != 'TAHUN') {
                                    $ptkaryawan->stpotongankaryawan_value = $tdt->sttunjangankaryawan_value;
                                    array_push($formulatjname, [
                                        'name' => $tdt->sttunjangankaryawan_name,
                                        'value' => $tdt->sttunjangankaryawan_value,
                                        'absence_days' => $absence_days,
                                        'total_days' => $prorate_salary['total_days'],
                                    ]); 
                                    if($tdt->sttunjangankaryawan_period == 'HARI') {
                                        $stpotongankaryawan_accumulate_value += ($ptkaryawan->stpotongankaryawan_value * $prorate_salary['total_days']) * $absence_days / $prorate_salary['total_days'];
                                    } else if($tdt->sttunjangankaryawan_period == 'BULAN') {
                                        $stpotongankaryawan_accumulate_value += $ptkaryawan->stpotongankaryawan_value * $prorate_salary['total_days'] / $prorate_salary['total_days'];
                                    }
                                    // else if($tdt->sttunjangankaryawan_period == 'TAHUN' && $tdt->sttunjangankaryawan_paymentperiod == $current_month) {
                                    //     $stpotongankaryawan_accumulate_value += $ptkaryawan->stpotongankaryawan_value;
                                    // }
                                }
                            }
                            $potongan_nominal += $stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $stpotongankaryawan_accumulate_value;
                            }
                            $ptkaryawan->stpotongankaryawan_formula_text = json_encode($formulatjname);
                            $ptkaryawan->stpotongankaryawan_accumulate_value = $stpotongankaryawan_accumulate_value;
                            array_push($potongan_karyawan_data, $ptkaryawan);
                        }
                    }
                }
                else if($ptkaryawan->stpotongankaryawan_type == 'TELAT') {
                    // dd($ptkaryawan);
                    if($ptkaryawan->stpotongankaryawan_method == 'HARI') {
                        $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value * count($late_times);
                        $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                            $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);

                    } else if($ptkaryawan->stpotongankaryawan_method == 'BULAN') {
                        $ptkaryawan->stpotongankaryawan_accumulate_value = count($late_times) > 0 ? $ptkaryawan->stpotongankaryawan_value : 0;
                        $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                            $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);
                    } else if($ptkaryawan->stpotongankaryawan_method == 'KELIPATAN_HARI') {
                        if(count($late_times) > 0) {
                            $stpotongankaryawan_accumulate_value = 0;
                            foreach($late_times as $key => $val) {
                                $lttimes = AppDurationLibrary::fromString($val.':00');
                                $ltminutes = $lttimes->toMinutes();

                                if($ltminutes >= $ptkaryawan->stpotongankaryawan_accumulationtime) {
                                    $diffminutes = floor($ltminutes / $ptkaryawan->stpotongankaryawan_accumulationtime);
                                    $stpotongankaryawan_accumulate_value += $ptkaryawan->stpotongankaryawan_value * $diffminutes;
                                }
                            }

                            $ptkaryawan->stpotongankaryawan_accumulate_value = $stpotongankaryawan_accumulate_value;
    
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }
                            
                        } else {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = 0;
                            $ptkaryawan->stpotongankaryawan_value = 0;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);
                        
                    } else if($ptkaryawan->stpotongankaryawan_method == 'KELIPATAN_BULAN') {
                        if(count($total_lates) > 0) {
                            $lttimes = AppDurationLibrary::fromString($total_lates[0].':'.$total_lates[1].':'.$total_lates[2]);
                            $ltminutes = $lttimes->toMinutes();
                            if($ltminutes >= $ptkaryawan->stpotongankaryawan_accumulationtime) {
                                $diffminutes = floor($ltminutes / $ptkaryawan->stpotongankaryawan_accumulationtime);
                                $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value * $diffminutes;

                                $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                    $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                }
                                array_push($potongan_karyawan_data, $ptkaryawan);
                            }
                        } else {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = 0;
                            $ptkaryawan->stpotongankaryawan_value = 0;
                        }
                        array_push($potongan_karyawan_data, $ptkaryawan);
                    }
                }
            }
        }
        // if($py->ms_karyawan_id == 122)
        //     dd($py->karyawan);
        // Tunjangan
        if(count($tjkaryawan_ids) > 0) {
            $this->data = [
                'payroll' => null,
                'karyawan' => $karyawan,
                'sttunjangan' => $karyawan->sttunjangantbl,
                'current_salary' => $current_salary,
                'stpenggajian_karyawan' => $stpenggajian_karyawan,
                'wajibpajak_id' => $wajibpajak_id,
                'attendance_days' => $attendance_days,
                'current_month' => $periode_month,
                'tjkaryawan_ids' => $tjkaryawan_ids,
                'prorate_salary' => $prorate_salary,
            ];
            $calculate_tunjangan = $this->calculate_tunjangan();
            $tunjangan_nominal += $calculate_tunjangan['tunjangan_nominal'];
            $tunjangan_nominal_pph21 += $calculate_tunjangan['tunjangan_nominal_pph21'];
            $tunjangan_karyawan_data = array_merge($tunjangan_karyawan_data, $calculate_tunjangan['tunjangan_karyawan_data']);
            
        }
        if($custom_allowance_data) {
            foreach($custom_allowance_data as $cadata) {
                $tunjangan_nominal += (isset($cadata['nominal'])) ? intval($cadata['nominal']) : 0;
                $tunjangan_nominal_pph21 += (isset($cadata['taxable']) && $cadata['taxable'] == 1) ? intval($cadata['nominal']) : 0;
                array_push($custom_tunjangan_karyawan_data, $cadata);
            }
        }

        // dd($custom_deduction_data, $potongan_nominal);
        if($custom_deduction_data) {
            // dd($custom_deduction_data);
            foreach($custom_deduction_data as $cddata) {
                $potongan_nominal += (isset($cddata['nominal'])) ? intval($cddata['nominal']) : 0;
                $potongan_nominal_pph21 += (isset($cddata['taxable']) && $cddata['taxable'] == 1) ? intval($cddata['nominal']) : 0;
                array_push($custom_pengurangan_karyawan_data, $cddata);
            }
        }
        $sttunjanganbpjskes = [];
        $sttunjanganbpjskespph21 = [];
        $sttunjanganbpjstk = [];
        $sttunjanganbpjstkpph21 = [];
        
        if(count($tunjangan_karyawan_data) > 0) {
            foreach($tunjangan_karyawan_data as $tjdatakaryawan) {
                if(in_array($tjdatakaryawan->st_grouptunjangankaryawan_id, $bpjs_tk_gruptunjangan)) {
                    array_push($sttunjanganbpjstk, $tjdatakaryawan);
                    if($tjdatakaryawan->sttunjangankaryawan_taxable == 1) {
                        array_push($sttunjanganbpjstkpph21, $tjdatakaryawan);
                    }
                }
                if(in_array($tjdatakaryawan->st_grouptunjangankaryawan_id, $bpjs_kes_gruptunjangan)) {
                    array_push($sttunjanganbpjskes, $tjdatakaryawan);
                    if($tjdatakaryawan->sttunjangankaryawan_taxable == 1) {
                        array_push($sttunjanganbpjskespph21, $tjdatakaryawan);
                    }
                }
            }
        }
        // if($py->ms_karyawan_id == 122)
            // dd($tunjangan_karyawan_data);
        //     dd($sttunjanganbpjstkpph21,$sttunjanganbpjskespph21);
        $bpjskes_accumulate_total = ($bpjs_kes_gp) ? $current_salary : 0;
        if(($bpjs_kes_lainnya == null)) {
            if(count($sttunjanganbpjskes) > 0) {
                $bpjskes_accumulate_total = $current_salary;
                    
                if($tjkaryawan_ids) {
                    foreach($sttunjanganbpjskes as $tjkes) {
                        if(in_array($tjkes->sttunjangankaryawan_id, $tjkaryawan_ids)) {
                            $bpjskes_accumulate_total += $tjkes->sttunjangankaryawan_accumulate_value;
                        }
                    }
                }
            }
        } else {
            $bpjskes_accumulate_total = $bpjs_kes_lainnya;
        }
        // dd($bpjs_tk_gp, count($sttunjanganbpjstk) > 0);
        $bpjstk_accumulate_total = ($bpjs_tk_gp) ? $current_salary : 0;
        
        // dd($tjkaryawan_ids);
        // dd($bpjs_tk_lainnya);
        if($bpjs_tk_lainnya == null) {
            if(count($sttunjanganbpjstk) > 0) {
                $bpjstk_accumulate_total = $current_salary;
                if($tjkaryawan_ids) {
                    foreach($sttunjanganbpjstk as $tjtk) {
                        if(in_array($tjtk->sttunjangankaryawan_id, $tjkaryawan_ids)) {
                            $bpjstk_accumulate_total += $tjtk->sttunjangankaryawan_accumulate_value;
                        }
                    }
                }
            }
        } else {
            $bpjstk_accumulate_total = $bpjs_tk_lainnya;
        }
        
        $bpjstk_jp_accumulate_total = $bpjstk_accumulate_total;
        if(count($setting) > 0) {
            foreach($setting as $st) {
                if($st->setting_key == 'BPJS_TK_MAX_AMOUNT') {
                    $bpjstkval = 0;
                    // $bpjstkval = intval($st->setting_value);
                    $bpjs_tk_setting = json_decode($st->setting_value);

                    usort($bpjs_tk_setting, function($a, $b) {
                        return $b->period <=> $a->period;
                    });
                    // dd($this->payroll_period);
                    // dd($bpjs_tk_setting, $payroll_periodmy);
                    foreach($bpjs_tk_setting as $sttk) {
                        if(strtotime('01-'.$payroll_periodmy) >= strtotime('01-'.$sttk->period)) {
                            $bpjstkval = $sttk->value;

                            break;
                        }
                    }
                    if($bpjstk_jp_accumulate_total > $bpjstkval) {
                        $bpjstk_jp_accumulate_total = $bpjstkval;
                    }
                }
                if($st->setting_key == 'BPJS_KES_MAX_AMOUNT') {
                    $bpjskesval = intval($st->setting_value);
                    if($bpjskes_accumulate_total > $bpjskesval) {
                        $bpjskes_accumulate_total = $bpjskesval;
                    }
                }
            }
        }

        // find lembur
        // dd('$this->payroll_period', $this->payroll_period);
        $payroll_periodmy = Carbon::parse($this->payroll_period)->format('m-Y');
        $payroll_periodm = Carbon::parse($this->payroll_period)->format('m');
        // dd('$payroll_periodmy', $payroll_periodmy);
        $lembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan->karyawan_id, 'lemburkaryawan_status' => 'APPROVED'])
        ->whereRaw("(TO_CHAR(lemburkaryawan_start_time, 'MM-YYYY') = ?)", [$payroll_periodmy])->get();
            // dd($lembur);
        $total_lembur = 0;
        $lemburids = [];
        $lembur_taxable = 0;
        foreach($lembur as $lb) {
            $total_lembur += $lb->lemburkaryawan_total_overtime_amount;
            array_push($lemburids, $lb->lemburkaryawan_id);
            $stlembur = $lb->lemburkaryawan_stlemburkaryawan_data ? json_decode($lb->lemburkaryawan_stlemburkaryawan_data) : null;
            // dd($stlembur);
            if($stlembur) {
                $lembur_taxable = (isset($stlembur->taxable)) ? $stlembur->taxable : 0;
            }
        }
        
        if($lembur_taxable) {
            $tunjangan_nominal_pph21 += $total_lembur;
        }
        
        $appPph21KaryawanLib = new AppPPh21Library();
        $appPph21KaryawanLib->karyawan = $karyawan;
        $appPph21KaryawanLib->salary = $current_salary; // need check from prorate
        $appPph21KaryawanLib->method = $payroll_method;
        $appPph21KaryawanLib->bpjsrate = $bpjsrate;
        $appPph21KaryawanLib->bpjs_accumulate_total = [$bpjskes_accumulate_total, $bpjstk_accumulate_total, $bpjstk_jp_accumulate_total];
        $appPph21KaryawanLib->tunjangan = ['tunjangan_nominal' => $tunjangan_nominal_pph21, 'potongan_nominal' => $potongan_nominal_pph21, 'tunjangan_jabatan' => $tunjangan_jabatan];
        $appPph21KaryawanLib->jkkrate = $jkkrate;
        $appPph21KaryawanLib->isbpjs = $isbpjs;
        $appPph21KaryawanLib->ratenonnpwp = $ratenonnpwp;
        $appPph21KaryawanLib->periode_month = $payroll_periodm;
        $appPph21KaryawanLib->periode_my = $payroll_periodmy;
        $appPph21KaryawanLib->pph21_prevcp_netto = $this->pph21_prevcp_netto;
        $appPph21KaryawanLib->pph21_prevcp_pph21_total = $this->pph21_prevcp_pph21_total;

        $calculate_pph21 = $appPph21KaryawanLib->calculateTotal();
        
        $biaya_jamkes_payslip = 0;
        $biaya_jht = $calculate_pph21['biaya_jht'];
        $biaya_jp = $calculate_pph21['biaya_jp'];
        $biaya_jamkes_payslip_rate = $appPph21KaryawanLib->getBpjsRate($bpjsrate,'JamKesMin', true);
        $calculate_pph21['biaya_jamkes_payslip_rate'] = ($biaya_jamkes_payslip_rate) ? $biaya_jamkes_payslip_rate->bpjsrate_rate : 0;
        // dd($isbpjs);
        if($isbpjs[0] == 1) { // bpjs kesehatan
            $biaya_jamkes_payslip = $appPph21KaryawanLib->getTotalBpjsRate($bpjsrate,'JamKesMin', $bpjskes_accumulate_total, null, true);
            $calculate_pph21['biaya_jamkes_payslip'] = floor($biaya_jamkes_payslip);
            
        } else {
            $calculate_pph21['biaya_jamkes_payslip'] = 0;
        }

        $penghasilan_jht_payslip = 0;
        $penghasilan_jp_payslip = 0;
        $penghasilan_jkk = $calculate_pph21['penghasilan_jkk'];
        $penghasilan_jamkes = $calculate_pph21['penghasilan_jamkes'];
        $penghasilan_jkm = $calculate_pph21['penghasilan_jkm'];

        $penghasilan_jht_payslip_rate = $appPph21KaryawanLib->getBpjsRate($bpjsrate,'JHT', true);
        $penghasilan_jp_payslip_rate = $appPph21KaryawanLib->getBpjsRate($bpjsrate,'JP', true);
        $calculate_pph21['penghasilan_jht_payslip_rate'] = ($penghasilan_jht_payslip_rate) ? $penghasilan_jht_payslip_rate->bpjsrate_rate : 0;
        $calculate_pph21['penghasilan_jp_payslip_rate'] = ($penghasilan_jp_payslip_rate) ? $penghasilan_jp_payslip_rate->bpjsrate_rate : 0;

        if($isbpjs[1] == 1) { // bpjs tk
            $penghasilan_jht_payslip = $appPph21KaryawanLib->getTotalBpjsRate($bpjsrate,'JHT', $bpjstk_accumulate_total, null, true);
            $penghasilan_jp_payslip = $appPph21KaryawanLib->getTotalBpjsRate($bpjsrate,'JP', $bpjstk_jp_accumulate_total, null, true);

            $calculate_pph21['penghasilan_jht_payslip'] = floor($penghasilan_jht_payslip);
            $calculate_pph21['penghasilan_jp_payslip'] = floor($penghasilan_jp_payslip);
        } else {
            $calculate_pph21['penghasilan_jht_payslip'] = 0;
            $calculate_pph21['penghasilan_jp_payslip'] = 0;
        }
        
        $allowance_pph21 = ($payroll_method == 'GROSS') ? 0 : $calculate_pph21['total_pph_terutang_perbulan'];
        // if($py->ms_karyawan_id == 122)
        //     dd($penghasilan_jamkes);
        
        if($bpjs_kes_ditanggung) {
            $allowance_bpjskes = $penghasilan_jamkes + $biaya_jamkes_payslip;
            $biaya_jamkes_payslip = 0;
            // dd($allowance_bpjskes);
        } else {
            // dd($penghasilan_jamkes);
            $allowance_bpjskes = $penghasilan_jamkes;
            // $allowance_bpjskes = 0;
        }
        
        if($bpjs_tk_ditanggung) {
            $allowance_bpjstk = $penghasilan_jkk + $penghasilan_jkm + $penghasilan_jht_payslip + $penghasilan_jp_payslip + $biaya_jht + $biaya_jp;
            $penghasilan_jht_payslip = 0;
            $penghasilan_jp_payslip = 0;
            $biaya_jht = 0;
            $biaya_jp = 0;
        } else {
            $allowance_bpjstk = $penghasilan_jkk + $penghasilan_jkm + $penghasilan_jht_payslip + $penghasilan_jp_payslip;
            // $allowance_bpjstk = 0;
        }
        // dd($allowance_bpjstk);
        
        $income = $current_salary + $allowance_pph21 + $allowance_bpjskes + $allowance_bpjstk + $tunjangan_nominal + $total_lembur;
        // dd($current_salary + $allowance_pph21,$allowance_bpjskes,$allowance_bpjstk,$tunjangan_nominal);
        $deduction_pph21 = $calculate_pph21['total_pph_terutang_perbulan'];
        $deduction_bpjskes = $biaya_jamkes_payslip;
        $deduction_bpjstk = $biaya_jht + $biaya_jp;
        // $allowance_bpjskes_ditanggung = 0;
        // $allowance_bpjstk_ditanggung = 0;
        // if($bpjs_kes_ditanggung) {
        //     $allowance_bpjskes_ditanggung = $allowance_bpjskes;
        // }
        // if($bpjs_tk_ditanggung) {
        //     $allowance_bpjstk_ditanggung = $allowance_bpjstk;
        // }
        
        $outcome = $deduction_pph21 + $deduction_bpjskes + $deduction_bpjstk + $allowance_bpjskes + $allowance_bpjstk + $potongan_nominal;
        // dd($deduction_pph21,$deduction_bpjskes, $deduction_bpjstk, $allowance_bpjskes, $allowance_bpjstk, $potongan_nominal);

        // dd($income, $outcome);
        // dd($stattendance_karyawan_data);
        $payroll = [
            'lemburids' => $lemburids,
            'pph21' => $calculate_pph21,
            'payroll' => [
                'gaji' => floor($current_salary),
                'income' => floor($income),
                'outcome' => floor($outcome),
                'netto' => floor($income - (($outcome) > 0 ? $outcome : 0)),
                'allowance_pph21' => floor($allowance_pph21),
                'allowance_overtime' => floor($total_lembur),
                'allowance_bpjskes' => floor($allowance_bpjskes),
                'allowance_bpjstk' => floor($allowance_bpjstk),
                'deduction_pph21' => floor($deduction_pph21),
                'deduction_bpjskes' => floor($deduction_bpjskes),
                'deduction_bpjstk' => floor($deduction_bpjstk),
                'tunjangan_karyawan_data' => $tunjangan_karyawan_data,
                'custom_tunjangan_karyawan_data' => $custom_tunjangan_karyawan_data,
                'potongan_karyawan_data' => $potongan_karyawan_data,
                'custom_pengurangan_karyawan_data' => $custom_pengurangan_karyawan_data,
                'stattendance_karyawan_data' => $stattendance_karyawan_data,
            ]
        ];
        return $payroll;
    }
}