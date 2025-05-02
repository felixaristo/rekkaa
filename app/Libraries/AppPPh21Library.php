<?php
namespace App\Libraries;

use App\Model\Master\BpjsRateModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Transaction\PPh21Model;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppPPh21Library {
    var $tarif21;
    var $karyawan;
    var $method;
    var $salary;
    var $pendapatankotor;
    var $pendapatanbersih;
    var $periode_month;
    var $periode_my;
    var $ratenonnpwp;
    var $bpjsrate;
    var $bpjs_accumulate_total;
    var $tunjangan = [];
    var $jkkrate = 0; 
    var $total_biaya = 0;
    var $total_tunjangan = 0;
    var $isbpjs = [1,1];
    var $pph21_prevcp_netto = 0;
    var $pph21_prevcp_pph21_total = 0;

    public function bpjsRate() {
        $where = ['bpjsrate_active' => 1, 'bpjsrate_taxable' => 1];
        $bpjsrate = BpjsRateModel::where($where)->get();
        return $bpjsrate;
    }

    public function getBpjsRate($bpjsrate, $code = null, $ispayslip = false) {
        $rate = null;
        foreach($bpjsrate as $rt) {
            if($ispayslip) {
                if ($rt->bpjsrate_code == $code) {
                    $rate = $rt;
                    break;
                }
            } else {
                if($rt->bpjsrate_taxable == '1') {
                    if ($rt->bpjsrate_code == $code) {
                        $rate = $rt;
                        break;
                    }
                }
            }
        }
        
        return $rate;
    }

    function getTotalBpjsRate($bpjsrate, $code = null, $total_gajipokok, $jkkrate = null, $ispayslip = false) {
        $rate = null;
        if($jkkrate) {
            $bpjsrate_rate = $jkkrate;
        } else {
            foreach($bpjsrate as $rt) {
                if($ispayslip) {
                    if ($rt->bpjsrate_code == $code) {
                        $rate = $rt;
                        $bpjsrate_rate = $rate->bpjsrate_rate;
                        break;
                    }
                } else {
                    if($rt->bpjsrate_taxable == '1') {
                        if ($rt->bpjsrate_code == $code) {
                            $rate = $rt;
                            $bpjsrate_rate = $rate->bpjsrate_rate;
                            break;
                        }
                    }
                }
            }
            if(!$rate) {
                return 0;
            }
        }
        $total_rate = $total_gajipokok * $bpjsrate_rate / 100;
        return $total_rate;
    }

    public function tunjanganJabatan() {
        $where = ['tunjanganjabatan_active' => 1];
        $tunjangan_jabatan = TunjanganJabatanModel::where($where)->first();
        return $tunjangan_jabatan;
    }

    public function getTunjanganJabatan($tunjangan_jabatan, $bruto_perbulan = 0)
    {
        $total_tunjangan = $bruto_perbulan * $tunjangan_jabatan->tunjanganjabatan_rate / 100;
        if($total_tunjangan > $tunjangan_jabatan->tunjanganjabatan_maximum_allowance) {
            $total_tunjangan = $tunjangan_jabatan->tunjanganjabatan_maximum_allowance;
        }
        return $total_tunjangan;
    }

    

    public function getTarifPPH21($total_pkp=0)
    {
        $tarif21 = Tarif21Model::where(['tarif21_active' => 1])->get();
        $t21 = [];
        foreach($tarif21 as $trf) {
            if ($trf->tarif21_startincome <= $total_pkp) {
                array_push($t21, $trf);
            }
        }
        return $t21;
    }

    public function getPtkpDetail($total_pkp=0)
    {
        $ptkp_detail = $this->karyawan->ptkp->ptkp_detail;
        $ptkpdet = null;
        // dd($ptkp_detail);
        foreach($ptkp_detail as $det) {
            if($total_pkp <= $det['ptkpdet_rate_month']) {
                $ptkpdet = $det;
                break;
            }
        }
        return $ptkpdet;
    }

    public function perhitunganPtkp($ptkp, $total_neto_setahun) {
        $total_ptkp = 0;
        if($ptkp) {
            $total_ptkp = $ptkp->ptkp_rate;
            // if($ptkp->ptkp_rate > $total_neto_setahun) {
            //     $total_ptkp = $total_neto_setahun;
            // }
        }
        return $total_ptkp;
    }

    public function perhitunganBruto($tahun=false, $data)
    {
        $total_bruto = 0;
        foreach($data as $d) {
            $total_bruto += $d;
        }
        $br = ($tahun) ? 12 * $total_bruto : $total_bruto;
        return round($br);
    }

    public function perhitunganNeto($tahun=false, $total_bruto, $biaya)
    {
        $total_biaya = 0;
        foreach($biaya as $d) {
            $total_biaya += $d;
        }
        return ($tahun) ? 12 * ($total_bruto - $total_biaya) : $total_bruto - $total_biaya;
    }

    /**
    * DONT DELETE
    * FOR PPH 21 before 2024
    */
    // public function calculate($metode='GROSS', $kepemilikan_npwp = 'NPWP', $total_pkp = 0, $calculation_year = 2024)
    // {
    //     $totalPPH_terutangsetahun = 0;
    //     // if($calculation_year == 2024) {
    //         // $tarif21 = $this->getTarifPPH21($total_pkp);
    //     // } else {
    //         $tarif21 = $this->getTarifPPH21($total_pkp);
    //         // dd($tarif21);
    //         $rate_nonnpwp = 100;
    //         if($kepemilikan_npwp == 'NO-NPWP') {
    //             $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
    //             $rate_nonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
    //         }
            
    //         if($metode == 'GROSS' || $metode == 'NETT') {
    //             $i = 0;
    //             foreach($tarif21 as $rate) {
    //                 $selisih = $rate->tarif21_endincome;
    //                 if($i > 0) {
    //                     $prevI = $i - 1;
    //                     $selisih = $rate->tarif21_endincome - $tarif21[$prevI]->tarif21_endincome;
    //                 }
                    
    //                 if($selisih > $total_pkp) {
    //                     $totalPPH_terutangsetahun += $total_pkp * $rate->tarif21_rate / 100 * ($rate_nonnpwp / 100);
    //                 } else {
    //                     $selisih = $rate->tarif21_endincome - $rate->tarif21_startincome;
    //                     $totalPPH_terutangsetahun += $selisih * $rate->tarif21_rate / 100 * ($rate_nonnpwp / 100);
    //                     $total_pkp -= $selisih;
    //                 }

    //                 $i++;
    //             };
    //             // dd($totalPPH_terutangsetahun);
    //         } else { // GROSS UP
    //             $i=0;
    //             // $r = [];
    //             foreach($tarif21 as $rate) {
    //                 $range_rate = 0;
    //                 $new_rate = 0;
    //                 if($i>0) {
    //                     $prev_rate = $tarif21[$i-1];
    //                     $range_rate = ($prev_rate->tarif21_endincome - $prev_rate->tarif21_startincome) * $prev_rate->tarif21_rate / 100;
    //                     $new_rate = $prev_rate->tarif21_endincome - $range_rate;
    //                 }
    //                 // array_push($r, $range_rate);
                    
    //                 if($i == count($tarif21) - 1) {
    //                     $totalPPH_terutangsetahun += (
    //                         ($total_pkp - $new_rate)  
    //                         * $rate->tarif21_rate / 
    //                         (100 - $rate->tarif21_rate) + $range_rate
    //                     ) * ($rate_nonnpwp / 100);
    //                 }
    //                 $i++;
    //             };
    //         }
    //     // }
    //     return $totalPPH_terutangsetahun;
    // }

    /**
     * PPH 21 2024 (TER)
     */
    public function calculate()
    {
        $karyawan = $this->karyawan;
        $method = $this->method;
        $pendapatankotor = $this->pendapatankotor;
        $pendapatanbersih = $this->pendapatanbersih;
        $periode_month = $this->periode_month;
        $periode_my = $this->periode_my;
        $pph21_prevcp_netto = $this->pph21_prevcp_netto;
        $pph21_prevcp_pph21_total = $this->pph21_prevcp_pph21_total;
        // dd($periode_my);
        $periode_year = Carbon::parse('01-'.$periode_my)->format('Y');
        $periode_contract_endmy = ($karyawan->karyawan_contract_end) ? Carbon::parse($karyawan->karyawan_contract_end)->format('m-Y') : null;
        // dd($periode_year);
        $ratenonnpwp = $this->ratenonnpwp;
        $total_pph_terutang_perbulan_desember = 0;
        $total_pph_terutang_pertahun_desember = 0;

        $tarif21 = null;
        // dd($periode_month, $periode_contract_endmy, $periode_my, $karyawan->karyawan_contract_end);
        if($periode_month == 12 || $periode_contract_endmy == $periode_my) { // desember NOT FIXED
            $prevperiode_month = $periode_month - 1;

            $total_bruto_jan_nov = 0;
            $total_pph21_jan_nov = 0;
            $total_month = $periode_month;
            // dd($periode_month, $prevperiode_month);
            if($periode_month > 1) {
                $pph21_jan_nov = PPh21Model::
                where(['tr_pph21.ms_karyawan_id' => $karyawan->karyawan_id])
                ->whereRaw("TO_CHAR(pph21_period, 'MM-YYYY') >= ?", ['01-'.$periode_year])
                ->whereRaw("TO_CHAR(pph21_period, 'MM-YYYY') <= ?", [$prevperiode_month.'-'.$periode_year])->get();
                $total_month = count($pph21_jan_nov) + 1; // + des
                
                foreach($pph21_jan_nov as $pph21jn) {
                    $total_bruto_jan_nov += $pph21jn->pph21_bruto_month;
                    $total_pph21_jan_nov += $pph21jn->pph21_total_month;
                }
            }
            $total_biaya_pertahun = $this->total_biaya * $total_month;
            $total_bruto_pertahun = $total_bruto_jan_nov + $pendapatankotor;
            $netto_pertahun = $total_bruto_pertahun - $total_biaya_pertahun;
            $total_pkp = $netto_pertahun - $karyawan->ptkp->ptkp_rate;
            $tarif21 = $this->getTarifPPH21($total_pkp);
            $preveRate = 0;
            $totalTarif21 = 0;
            $i=0;
            forEach($tarif21 as $rate) {
                if($i == 0) {
                    $preveRate = 0;
                }
                $maxIncome = $rate->tarif21_endincome;
                if(!isset($tarif21[$i+1])) {
                    $maxIncome = $total_pkp - $preveRate;
                } else {
                    $maxIncome -= $preveRate;
                }
                $preveRate += $maxIncome;
                $totalTarif21 += (($maxIncome * $rate->tarif21_rate) / 100);
                $i++;
                
            };
            // dd($totalTarif21, $total_pkp, $tarif21);
            if($totalTarif21 > 0) {
                $total_pph_terutang_perbulan_desember = $totalTarif21 - $total_pph21_jan_nov;
            } else {
                $total_pph_terutang_perbulan_desember = 0;
            }
            // $total_pph_terutang_perbulan_desember = ($total_pph_terutang_perbulan_desember > 0) ? $total_pph_terutang_perbulan_desember : 0;
            //         perhitunganTotalPPHTerutangPerbulan.set(totalPPhTerutangDesember);
            // dd($total_pph_terutang_perbulan_desember, $totalTarif21);
            $total_pph_terutang_setahun = 0;
            // if($total_pph_terutang_perbulan_desember > 0) {
                $total_pph_terutang_setahun = $total_pph_terutang_perbulan_desember + $total_pph21_jan_nov;
            // }
            
            $total_pph_terutang_perbulan = $total_pph_terutang_perbulan_desember;
            $total_pph_terutang_pertahun = $total_pph_terutang_setahun;
            $netto_perbulan = $pendapatankotor;
            $total_bruto_pertahun = $pendapatankotor + $total_bruto_jan_nov;
            $total_ptkp = $karyawan->ptkp->ptkp_rate;
            $total_pkp_pertahun = $total_pkp;//floor($netto_pertahun - $total_ptkp);
            $total_pkp_perbulan = $total_pkp / 12; //$total_pkp_pertahun / 12;
            $total_netto_pertahun_fix = $netto_pertahun;
            $ptkp_detail = $this->getPtkpDetail($pendapatankotor);
            $ptkpdetratepercentage = ($ptkp_detail) ? $ptkp_detail['ptkpdet_rate_percentage'] : 34;

            // for middle of period and from other company
            if($pph21_prevcp_netto > 0) {
                $total_netto_pertahun_fix += $pph21_prevcp_netto;
                $total_pkp = $total_netto_pertahun_fix - $karyawan->ptkp->ptkp_rate;
                $tarif21 = $this->getTarifPPH21($total_pkp);
                $preveRate = 0;
                $totalTarif21 = 0;
                $i=0;
                forEach($tarif21 as $rate) {
                    if($i == 0) {
                        $preveRate = 0;
                    }
                    $maxIncome = $rate->tarif21_endincome;
                    if(!isset($tarif21[$i+1])) {
                        $maxIncome = $total_pkp - $preveRate;
                    } else {
                        $maxIncome -= $preveRate;
                    }
                    $preveRate += $maxIncome;
                    $totalTarif21 += (($maxIncome * $rate->tarif21_rate) / 100);
                    $i++;
                    
                };
                if($totalTarif21 > 0) {
                    $total_pph_terutang_perbulan_desember = $totalTarif21 - $total_pph21_jan_nov - $pph21_prevcp_pph21_total;
                } else {
                    $total_pph_terutang_perbulan_desember = 0;
                }
                // dd($totalTarif21, $total_pkp, $total_pph_terutang_perbulan_desember, $total_netto_pertahun_fix, $netto_pertahun, $pph21_prevcp_netto, $pph21_prevcp_pph21_total);
                $total_pph_terutang_perbulan = $total_pph_terutang_perbulan_desember;
            }
            // dd($total_pkp, $total_bruto_pertahun, $netto_perbulan, $pendapatankotor, $total_bruto_jan_nov, $total_pkp_pertahun, $total_pkp_perbulan);
        } else {
            // get rate from category
            $karyawan->ptkp->ptkp_detail = (object) PtkpDetailModel::where(['ptkpdet_category' => $karyawan->ptkp->ptkp_category, 'ptkpdet_active' => '1'])->orderBy('ptkpdet_rate_month', 'ASC')->get()->toArray();
            $ptkp_detail = $this->getPtkpDetail($pendapatankotor);
            
            // get percentage from ptkp category
            $ptkpdetratepercentage = ($ptkp_detail) ? $ptkp_detail['ptkpdet_rate_percentage'] : 34;
            // $total_pph_terutang_perbulan = intval($pendapatankotor * $ptkpdetratepercentage / 100 * $ratenonnpwp / 100);
            if($method == 'GROSS') {
                $total_pph_terutang_perbulan = intval($pendapatankotor * $ptkpdetratepercentage / 100);
            } else {
                $total_pph_terutang_perbulangrossup = intval($pendapatankotor * $ptkpdetratepercentage / (100 - $ptkpdetratepercentage));
                $pendapatankotorgrossup = $pendapatankotor + $total_pph_terutang_perbulangrossup;
                // get rate from category
                $ptkp_detail = $this->getPtkpDetail($pendapatankotorgrossup);
                // get percentage from ptkp category
                $ptkpdetratepercentagenew = ($ptkp_detail) ? $ptkp_detail['ptkpdet_rate_percentage'] : 34;
                if($ptkpdetratepercentagenew != $ptkpdetratepercentage) {
                    // $total_pph_terutang_perbulangrossup1 = intval($pendapatankotor * $ptkpdetratepercentagenew / (100 - $ptkpdetratepercentagenew));
                    $ptkpdetratepercentage = $ptkpdetratepercentagenew;
                }
                $total_pph_terutang_perbulan = intval($pendapatankotor * $ptkpdetratepercentage / (100 - $ptkpdetratepercentage));
                
            }
            // dd($total_pph_terutang_perbulan, $pendapatankotor);
            // if($total_pph_terutang_perbulan < 0) {
            //     $total_pph_terutang_perbulan = 0;
            // }
            
            $netto_perbulan = $pendapatankotor - $this->total_biaya; // dikurangi biaya jabatan
            // dd($total_pph_terutang_perbulan, $netto_perbulan, $pendapatankotor, $total_biaya_jabatan);
            $total_bruto_pertahun = $pendapatankotor * 12;
            $netto_pertahun = $netto_perbulan * 12;
            $total_ptkp = $karyawan->ptkp->ptkp_rate;
            // dd($karyawan->ptkp);
            $total_pkp_pertahun = floor($netto_pertahun - $total_ptkp);
            $total_pkp_perbulan = $total_pkp_pertahun / 12;
            $total_pph_terutang_pertahun = $total_pph_terutang_perbulan * 12;
        }
        // dd($total_pph_terutang_perbulan);
        unset($this->karyawan->ptkp->ptkp_detail);
        return [
            'karyawan_id' => $karyawan->karyawan_id,
            'method' => $method,
            'salary' => $this->salary,
            'total_bruto_perbulan' => $pendapatankotor, 
            'total_bruto_pertahun' => $total_bruto_pertahun, 
            'ptkpdetratepercentage' => $ptkpdetratepercentage, 
            'total_netto_perbulan' => $netto_perbulan, 
            'total_netto_pertahun' => $netto_pertahun, 
            // 'total_biaya_jabatan' => $total_biaya_jabatan,
            'pph21_prevcp_netto' => $pph21_prevcp_netto,
            'pph21_prevcp_pph21_total' => $pph21_prevcp_pph21_total,
            'total_ptkp' => $total_ptkp, 
            'total_pkp_perbulan' => $total_pkp_perbulan, 
            'total_pkp_pertahun' => $total_pkp_pertahun, 
            'total_pph_terutang_perbulan' => $total_pph_terutang_perbulan, 
            'total_pph_terutang_pertahun' => $total_pph_terutang_pertahun,
            'total_pph_terutang_perbulan_desember' => $total_pph_terutang_perbulan_desember,
            'total_pph_terutang_pertahun_desember' => $total_pph_terutang_pertahun_desember,
            'tarif21' => $tarif21,
            'ptkp' => $this->karyawan->ptkp,
            'ptkp_detail' => $ptkp_detail,
        ];
    }

    // public function calculateTotal($bpjsrate, $bpjs_accumulate_total, $tunjangan = [], $jkkrate = 0, $isbpjs = [1,1])
    public function calculateTotal()
    {
        $method = $this->method;
        $salary_jamkes = $this->bpjs_accumulate_total[0];
        $salary_jk = $this->bpjs_accumulate_total[1];
        $salary_jk_jp = $this->bpjs_accumulate_total[2];

        if($this->isbpjs[0] == 1) { // bpjs kesehatan
            $penghasilan_jamkes = $this->getTotalBpjsRate($this->bpjsrate,'JamKes',$salary_jamkes);
            $biaya_jamkes = $this->getTotalBpjsRate($this->bpjsrate,'JamKesMin',$salary_jamkes);
        } else {
            $penghasilan_jamkes = 0;
            $biaya_jamkes = 0;
        }
        // dd($penghasilan_jamkes);

        // dd($salary_jk_jp);
        if($this->isbpjs[1] == 1) { // bpjs tk
            $penghasilan_jkk = $this->getTotalBpjsRate($this->bpjsrate,'JKK',$salary_jk, $this->jkkrate);
            $penghasilan_jkm = $this->getTotalBpjsRate($this->bpjsrate,'JKM',$salary_jk);
            $penghasilan_jht = $this->getTotalBpjsRate($this->bpjsrate,'JHT',$salary_jk);
            // dd($salary_jk_jp);
            $penghasilan_jp = $this->getTotalBpjsRate($this->bpjsrate,'JP',$salary_jk_jp);
            $biaya_jht = $this->getTotalBpjsRate($this->bpjsrate,'JHTMin',$salary_jk);
            $biaya_jp = $this->getTotalBpjsRate($this->bpjsrate,'JPMin',$salary_jk_jp);
            // dd($biaya_jp);
        } else {
            $penghasilan_jkk = 0;
            $penghasilan_jkm = 0;
            $penghasilan_jht = 0;
            $penghasilan_jp = 0;
            $biaya_jht = 0;
            $biaya_jp = 0;
        }
        // rate
        $penghasilan_jamkes_rt = $this->getBpjsRate($this->bpjsrate,'JamKes');
        $penghasilan_jamkes_rate = ($penghasilan_jamkes_rt) ? $penghasilan_jamkes_rt->bpjsrate_rate : 0; 
        $penghasilan_jkk_rate = $this->jkkrate;
        $penghasilan_jkm_rt = $this->getBpjsRate($this->bpjsrate,'JKM');
        $penghasilan_jkm_rate = ($penghasilan_jkm_rt) ? $penghasilan_jkm_rt->bpjsrate_rate : 0;
        $penghasilan_jht_rt = $this->getBpjsRate($this->bpjsrate,'JHT');
        $penghasilan_jht_rate = ($penghasilan_jht_rt)? $penghasilan_jht_rt->bpjsrate_rate : 0;
        $penghasilan_jp_rt = $this->getBpjsRate($this->bpjsrate,'JP');
        $penghasilan_jp_rate = ($penghasilan_jp_rt)? $penghasilan_jp_rt->bpjsrate_rate : 0;
        // rate
        $biaya_jamkes_rt = $this->getBpjsRate($this->bpjsrate,'JamKesMin');
        $biaya_jamkes_rate = ($biaya_jamkes_rt)? $biaya_jamkes_rt->bpjsrate_rate : 0;
        $biaya_jht_rt = $this->getBpjsRate($this->bpjsrate,'JHTMin');
        $biaya_jht_rate = ($biaya_jht_rt) ? $biaya_jht_rt->bpjsrate_rate : 0;
        $biaya_jp_rt = $this->getBpjsRate($this->bpjsrate,'JPMin');
        $biaya_jp_rate = ($biaya_jp_rt) ? $biaya_jp_rt->bpjsrate_rate : 0;
        // dd($penghasilan_jkk, $penghasilan_jkk, $penghasilan_jkm, $penghasilan_jht, $penghasilan_jp);

        $total_bruto_perbulan = floor($this->perhitunganBruto(false, [
            $this->salary,
            $this->tunjangan['tunjangan_nominal'],
            $penghasilan_jamkes,
            $penghasilan_jkk,
            $penghasilan_jkm,
            $penghasilan_jht,
            $penghasilan_jp,
        ]));
        $total_biaya_jabatan = floor($this->getTunjanganJabatan($this->tunjangan['tunjangan_jabatan'], $total_bruto_perbulan));
        $this->total_tunjangan = $this->tunjangan['tunjangan_nominal'] + $penghasilan_jamkes + $penghasilan_jkk + $penghasilan_jkm + $penghasilan_jht + $penghasilan_jp;
        $this->total_biaya = $total_biaya_jabatan + $this->tunjangan['potongan_nominal'] + $biaya_jamkes + $biaya_jht + $biaya_jp;
        // $total_neto_perbulan = floor($this->perhitunganNeto(false, $total_bruto_perbulan, [
        //     $total_biaya_jabatan,
        //     $this->tunjangan['potongan_nominal'],
        //     $biaya_jamkes,
        //     $biaya_jht,
        //     $biaya_jp,
        // ]));
        // // dd($total_neto_perbulan);

        // $total_neto_pertahun = floor($this->perhitunganNeto(true, $total_bruto_perbulan, [
        //     $total_biaya_jabatan,
        //     $this->tunjangan['potongan_nominal'],
        //     $biaya_jamkes,
        //     $biaya_jht,
        //     $biaya_jp,
        // ]));
        // // }
        // $total_bruto_pertahun = $total_bruto_perbulan * 12;

        // dd($metode);
        // $total_ptkp = $this->perhitunganPtkp($ptkp, $total_neto_pertahun);
        // $total_pkp = floor($total_neto_pertahun - $total_ptkp);

        // $this->karyawan;
        // $karyawan->karyawan_calculation_method;
        $this->pendapatankotor = $total_bruto_perbulan;
        // $this->pendapatanbersih = $total_neto_perbulan;
        // dd($total_neto_perbulan, $total_bruto_perbulan);
        // $periode_month = $this->periode_month;
        // $ratenonnpwp = $this->ratenonnpwp;
        // $total_pph_terutang_setahun = 0;//floor($this->calculate());
        // $total_pph_terutang_perbulan = floor($total_pph_terutang_setahun / 12);
        $calculate_pph21 = $this->calculate();
        // dd($calculate_pph21);
        return [
            'karyawan_id' => $this->karyawan->karyawan_id,
            'method' => $method,
            'gaji' => $this->salary,
            'biaya_jamkes' => floor($biaya_jamkes),
            'biaya_jht' => floor($biaya_jht),
            'biaya_jp' => floor($biaya_jp),
            'biaya_jamkes_rate' => $biaya_jamkes_rate,
            'biaya_jht_rate' => $biaya_jht_rate,
            'biaya_jp_rate' => $biaya_jp_rate,
            'total_biaya_jabatan' => floor($total_biaya_jabatan),
            'penghasilan_jamkes' => floor($penghasilan_jamkes),
            'penghasilan_jkk' => floor($penghasilan_jkk),
            'penghasilan_jkm' => floor($penghasilan_jkm),
            'penghasilan_jht' => floor($penghasilan_jht),
            'penghasilan_jp' => floor($penghasilan_jp),
            'biaya_jamkes_rate' => $biaya_jamkes_rate,
            'biaya_jht_rate' => $biaya_jht_rate,
            'biaya_jp_rate' => $biaya_jp_rate,
            'penghasilan_jamkes_rate' => $penghasilan_jamkes_rate,
            'penghasilan_jkk_rate' => $penghasilan_jkk_rate,
            'penghasilan_jkm_rate' => $penghasilan_jkm_rate,
            'penghasilan_jht_rate' => $penghasilan_jht_rate,
            'penghasilan_jp_rate' => $penghasilan_jp_rate,
            'nominal_tunjangan_lain' => floor($this->tunjangan['tunjangan_nominal']),
            'nominal_potongan_lain' => floor($this->tunjangan['potongan_nominal']),
            'total_bruto_perbulan' => floor($calculate_pph21['total_bruto_perbulan']),
            'total_bruto_pertahun' => floor($calculate_pph21['total_bruto_pertahun']),
            'total_neto_perbulan' => floor($calculate_pph21['total_netto_perbulan']),
            'total_neto_pertahun' => floor($calculate_pph21['total_netto_pertahun']),
            'total_ptkp' => floor($calculate_pph21['total_ptkp']),
            'total_pkp_perbulan' => floor($calculate_pph21['total_pkp_perbulan']),
            'total_pkp_pertahun' => floor($calculate_pph21['total_pkp_pertahun']),
            'total_pph_terutang_perbulan' => floor($calculate_pph21['total_pph_terutang_perbulan']),
            'total_pph_terutang_pertahun' => floor($calculate_pph21['total_pph_terutang_pertahun']),
            'total_pph_terutang_perbulan_desember' => floor($calculate_pph21['total_pph_terutang_perbulan_desember']),
            'total_pph_terutang_pertahun_desember' => floor($calculate_pph21['total_pph_terutang_pertahun_desember']),
            'ptkpdetratepercentage' => $calculate_pph21['ptkpdetratepercentage'],
            'pph21_prevcp_netto' => $calculate_pph21['pph21_prevcp_netto'],
            'pph21_prevcp_pph21_total' => $calculate_pph21['pph21_prevcp_pph21_total'],
            'tarif21' => $calculate_pph21['tarif21'],
            'ptkp' => $calculate_pph21['ptkp']->toArray(),
            'ptkp_detail' => $calculate_pph21['ptkp_detail'],
        ];
    }
}