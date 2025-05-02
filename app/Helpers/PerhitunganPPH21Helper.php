<?php

use App\Model\Master\BpjsRateModel;
use App\Model\Master\KepemilikanNpwpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Setting\SettingModel;

function bpjsRate() {
    $where = ['bpjsrate_active' => 1, 'bpjsrate_taxable' => 1];
    $bpjsrate = BpjsRateModel::where($where)->get();
    return $bpjsrate;
}

function getBpjsRate($bpjsrate, $code = null) {
    $rate = null;
    foreach($bpjsrate as $rt) {
        if ($rt->bpjsrate_code == $code) {
            $rate = $rt;
            break;
        }
    }
    
    return $rate;
}

function getTotalBpjsRate($bpjsrate, $code = null, $total_gajipokok, $jkkrate = 0) {
    $rate = null;
    if($jkkrate) {
        $total_rate = $total_gajipokok * $jkkrate / 100;
    } else {
        
        foreach($bpjsrate as $rt) {
            if ($rt->bpjsrate_code == $code) {
                $rate = $rt;
                break;
            }
        }
        if(!$rate) {
            return 0;
        }
        
        $total_rate = $total_gajipokok * $rate->bpjsrate_rate / 100;
    }
    return $total_rate;
}

function getTunjanganJabatan($tunjangan_jabatan, $bruto_perbulan = 0)
{
    // $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => 1])->first();
    $total_tunjangan = $bruto_perbulan * $tunjangan_jabatan->tunjanganjabatan_rate / 100;
    if($total_tunjangan > $tunjangan_jabatan->tunjanganjabatan_maximum_allowance) {
        $total_tunjangan = $tunjangan_jabatan->tunjanganjabatan_maximum_allowance;
    }
    return $total_tunjangan;
}

function getTarifPPH21($total_pkp=0)
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

function perhitunganPtkp($ptkp, $total_neto_setahun) {
    $total_ptkp = $ptkp->ptkp_rate;
    // if($ptkp->ptkp_rate > $total_neto_setahun) {
    //     $total_ptkp = $total_neto_setahun;
    // }
    return $total_ptkp;
}

function perhitunganTarifTER($total_pkp) {
    $tarif21Rates = getTarifPPH21($total_pkp);
    
    $preveRate = 0;
    $totalTarif21 = 0;
    $i=0;
    forEach($tarif21Rates as $rate) {
        if($i == 0) {
            $preveRate = 0;
        }
        $maxIncome = $rate->tarif21_endincome;
        if(!isset($tarif21Rates[$i+1])) {
            $maxIncome = $total_pkp - $preveRate;
        } else {
            $maxIncome -= $preveRate;
        }
        $preveRate += $maxIncome;
        $totalTarif21 += (($maxIncome * $rate->tarif21_rate) / 100);
        $i++;
        
    };
    return floor($totalTarif21);
}

function perhitunganBruto($tahun=false, $data)
{
    // dd($data);
    $total_bruto = 0;
    foreach($data as $d) {
        $total_bruto += $d;
    }
    $br = ($tahun) ? 12 * $total_bruto : $total_bruto;
    return round($br);
}
function perhitunganNeto($tahun=false, $total_bruto, $biaya)
{
    $total_biaya = 0;
    foreach($biaya as $d) {
        $total_biaya += $d;
    }
    return ($tahun) ? 12 * ($total_bruto - $total_biaya) : $total_bruto - $total_biaya;
}
function perhitunganPPH21Non($total_bruto = 0, $pendapatan_kotor_sebelumnya = 0, $metode = 'GROSS', $sumberpenghasilan, $ptkp, $menggunakan_ter=1)
{
    $totalPPH_terutang = 0;
    
        if($metode == 'GROSS') {
            if($pendapatan_kotor_sebelumnya > 0) {
                $total_pkp_akumulasi = $total_bruto + $pendapatan_kotor_sebelumnya;
                $total_pkp = $total_bruto * 50 / 100;
                $total_pkp = ($sumberpenghasilan == 1) ? $total_pkp - ($ptkp->ptkp_rate / 12) : $total_pkp;
                $tarif21 = getTarifPPH21($total_pkp_akumulasi);
            } else {
                $total_pkp = $total_bruto * 50 / 100;
                $total_pkp = ($sumberpenghasilan == 1) ? $total_pkp - ($ptkp->ptkp_rate / 12) : $total_pkp;
                $tarif21 = getTarifPPH21($total_pkp);
            }
            foreach($tarif21 as $rate) {
                if($rate->tarif21_endincome > $total_pkp) {
                    $totalPPH_terutang += $total_pkp * $rate->tarif21_rate / 100;
                } else {
                    $totalPPH_terutang += $rate->tarif21_endincome * $rate->tarif21_rate / 100;
                    $total_pkp -= $rate->tarif21_endincome;
                }
            }
        } else {
            $tarif21 = getTarifPPH21($total_bruto);
            $total_pkp = $total_bruto;
            $total_pkp = ($sumberpenghasilan == 1) ? $total_pkp - ($ptkp->ptkp_rate / 12) : $total_pkp;
            $rategrossnet = 0;
            foreach($tarif21 as $rate) {
                if($rate->tarif21_endincome >= $total_pkp) {
                    $rategrossnet = 100 - (50 * $rate->tarif21_rate / 100);
                } else {
                    $totalPPH_terutang += $rate->tarif21_endincome * $rate->tarif21_rate / 100;
                    $total_pkp -= $rate->tarif21_endincome;
                }
            }
            $total_pkp = $total_bruto * 100 / $rategrossnet; // pkp

            // hitung ulang pkp setelah ditambah pajak
            foreach($tarif21 as $rate) {
                if($rate->tarif21_endincome >= $total_pkp) {
                    $rategrossnet = 100 - (50 * $rate->tarif21_rate / 100);
                } else {
                    $totalPPH_terutang += $rate->tarif21_endincome * $rate->tarif21_rate / 100;
                    $total_pkp -= $rate->tarif21_endincome;
                }
            }
            $total_pkp = $total_bruto * 100 / $rategrossnet; // pkp
            $totalPPH_terutang = $total_pkp - $total_bruto;
        }
    if($menggunakan_ter == 1) {
        if($metode == 'GROSS') {
            $total_bruto = $total_bruto;
        } else {
            $total_bruto = $total_pkp;
        }
        $ptkpcategoryrate = 0;
        $ptkpcategoryratemonth = 0;
        // dd($ptkp);
        if($ptkp && $ptkp->ptkp_detail) {
            $ptkpdet = $ptkp->ptkp_detail;
            // dd($ptkpdet);
            for($i=0; $i<count($ptkpdet); $i++) {
                if($total_bruto <= $ptkpdet[$i]['ptkpdet_rate_month']) {
                    $ptkpcategoryrate = $ptkpdet[$i]['ptkpdet_rate_percentage'];
                    $ptkpcategoryratemonth = $ptkpdet[$i]['ptkpdet_rate_month'];
                    break;
                }
            }
        }
            // console.log('ptkpCategoryRate', ptkpCategoryRate);
            // $("#perhitungan_tarif_ter").val(ptkpCategoryRate);
            if($metode == 'GROSS') {
                $total_pkp = $total_bruto;
            }
            // console.log('totalPKP', totalPKP)
            $totalPPH_terutang = $total_pkp * 50/100 * $ptkpcategoryrate / 100;
    }
    return ['total_pph' => $totalPPH_terutang, 'total_pkp' => $total_pkp];
    
    
}
function perhitunganPPH21($metode='GROSS', $kepemilikan_npwp = 'NPWP', $total_pkp = 0)
{
    $tarif21 = getTarifPPH21($total_pkp);
    $rate_nonnpwp = 100;
    if($kepemilikan_npwp == 'NO-NPWP') {
        $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
        $rate_nonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
    }
    
    $totalPPH_terutangsetahun = 0;
    // dd($metode);
    if($metode == 'GROSS' || $metode == 'NETT') {
        foreach($tarif21 as $rate) {
            if($rate->tarif21_endincome > $total_pkp) {
                $totalPPH_terutangsetahun += $total_pkp * $rate->tarif21_rate / 100 * ($rate_nonnpwp / 100);
            } else {
                $totalPPH_terutangsetahun += $rate->tarif21_endincome * $rate->tarif21_rate / 100 * ($rate_nonnpwp / 100);
                $total_pkp -= $rate->tarif21_endincome;
            }
        };
        // dd($totalPPH_terutangsetahun);
    } else { // GROSS UP
        $i=0;
        $r = [];
        foreach($tarif21 as $rate) {
            $range_rate = 0;
            $new_rate = 0;
            if($i>0) {
                $prev_rate = $tarif21[$i-1];
                $range_rate = ($prev_rate->tarif21_endincome - $prev_rate->tarif21_startincome) * $prev_rate->tarif21_rate / 100;
                $new_rate = $prev_rate->tarif21_endincome - $range_rate;
            }
            // array_push($r, $range_rate);
            
            if($i == count($tarif21) - 1) {
                $totalPPH_terutangsetahun += (
                    ($total_pkp - $new_rate)  
                    * $rate->tarif21_rate / 
                    (100 - $rate->tarif21_rate) + $range_rate
                ) * ($rate_nonnpwp / 100);
            }
            $i++;
        };
        // dd($r);
    }
    return $totalPPH_terutangsetahun;
}

function perhitunganTotalPPH21($bpjsrate, $ptkp, $karyawan_bpjs, $salary, $tunjangan = [], $metode = 'GROSS', $npwp = 'NPWP', $jkkrate = 0, $isbpjs = [1,1], $ptkpdetratepercentage = 0, $menggunakan_ter=1, $period = null)
{
    $period = ($period) ? $period : date('m-Y');
    if(is_array($karyawan_bpjs) && $karyawan_bpjs) {
        $salary_jamkes = $karyawan_bpjs['penghasilan_kes_nominal'];
        $salary_jk = $karyawan_bpjs['penghasilan_tk_nominal'];
    } else {
        $salary_jamkes = $salary;
        $salary_jk = $salary;
    }

    $setting_bpjs = SettingModel::where('setting_active', 1)->where('setting_key', 'ilike', 'BPJS_%')->get();
    $max_bpjs_kes = 0;
    $max_bpjs_tk = 0;
    foreach($setting_bpjs as $stbpjs) {
        if ($stbpjs->setting_key === 'BPJS_KES_MAX_AMOUNT') {
            $max_bpjs_kes = intval($stbpjs->setting_value);
        }
        if ($stbpjs->setting_key === 'BPJS_TK_MAX_AMOUNT') {
            $bpjs_tk_setting = json_decode($stbpjs->setting_value);

            usort($bpjs_tk_setting, function($a, $b) {
                return $b->period <=> $a->period;
            });
            $max_bpjs_tk = intval($bpjs_tk_setting[0]->value);
        }
    }

    if($isbpjs[0] == 1) { // kesehatan

        if($max_bpjs_kes < $salary_jamkes) {
            $penghasilan_jamkes = getTotalBpjsRate($bpjsrate,'JamKes',$max_bpjs_kes);
        } else {
            $penghasilan_jamkes = getTotalBpjsRate($bpjsrate,'JamKes',$salary_jamkes);
        }
    } else {
        $penghasilan_jamkes = 0;
    }
    
    if($isbpjs[1] == 1) { // tk
        $penghasilan_jkk = getTotalBpjsRate($bpjsrate,'JKK',$salary_jk, $jkkrate);
        // dd($penghasilan_jkk)
        $penghasilan_jkm = getTotalBpjsRate($bpjsrate,'JKM',$salary_jk);
        $penghasilan_jht = getTotalBpjsRate($bpjsrate,'JHT',$salary_jk);
        $penghasilan_jp = getTotalBpjsRate($bpjsrate,'JP',$salary_jk);
    } else {
        $penghasilan_jkk = 0;
        // dd($penghasilan_jkk)
        $penghasilan_jkm = 0;
        $penghasilan_jht = 0;
        $penghasilan_jp = 0;
    }
    // rate
    $penghasilan_jamkes_rate = getBpjsRate($bpjsrate,'JamKes')->bpjsrate_rate;
    $penghasilan_jkk_rate = $jkkrate;
    $penghasilan_jkm_rate = getBpjsRate($bpjsrate,'JKM')->bpjsrate_rate;
    $penghasilan_jht_rate = (getBpjsRate($bpjsrate,'JHT')) ? getBpjsRate($bpjsrate,'JHT')->bpjsrate_rate : 0;
    $penghasilan_jp_rate = (getBpjsRate($bpjsrate,'JP')) ? getBpjsRate($bpjsrate,'JP')->bpjsrate_rate : 0;

    if($isbpjs[0] == 1) {
        $biaya_jamkes = getTotalBpjsRate($bpjsrate,'JamKesMin',$salary_jamkes);
    } else {
        $biaya_jamkes = 0;
    }
    if($isbpjs[1] == 1) {
        $biaya_jht = getTotalBpjsRate($bpjsrate,'JHTMin',$salary_jk);

        if($max_bpjs_tk < $salary_jk) {
            $biaya_jp = getTotalBpjsRate($bpjsrate,'JPMin',$max_bpjs_tk);
        } else {
            $biaya_jp = getTotalBpjsRate($bpjsrate,'JPMin',$salary_jk);
        }
    } else {
        $biaya_jht = 0;
        $biaya_jp = 0;
    }
	// rate
    $biaya_jamkes_rate = (getBpjsRate($bpjsrate,'JamKesMin')) ? getBpjsRate($bpjsrate,'JamKesMin')->bpjsrate_rate : 0;
    $biaya_jht_rate = getBpjsRate($bpjsrate,'JHTMin')->bpjsrate_rate;
    $biaya_jp_rate = getBpjsRate($bpjsrate,'JPMin')->bpjsrate_rate;

	if($metode == 'NETT') {
        // dd($salary);
		$total_bruto_perbulan = perhitunganBruto(false, [
			$salary,
		]);

		$total_biaya_jabatan = getTunjanganJabatan($tunjangan['tunjangan_jabatan'], $total_bruto_perbulan);
		$total_neto_perbulan = perhitunganNeto(false, $total_bruto_perbulan, [
			$total_biaya_jabatan,
		]);

		$total_neto_pertahun = perhitunganNeto(true, $total_bruto_perbulan, [
			$total_biaya_jabatan,
		]);
	} else {
		
		$total_bruto_perbulan = perhitunganBruto(false, [
			$salary,
			$tunjangan['tunjangan_nominal'],
			$penghasilan_jamkes,
			$penghasilan_jkk,
			$penghasilan_jkm,
			$penghasilan_jht,
			$penghasilan_jp,
		]);
		$total_biaya_jabatan = getTunjanganJabatan($tunjangan['tunjangan_jabatan'], $total_bruto_perbulan);

		$total_neto_perbulan = perhitunganNeto(false, $total_bruto_perbulan, [
			$total_biaya_jabatan,
			$biaya_jamkes,
			$biaya_jht,
			$biaya_jp,
		]);

		$total_neto_pertahun = perhitunganNeto(true, $total_bruto_perbulan, [
			$total_biaya_jabatan,
			$biaya_jamkes,
			$biaya_jht,
			$biaya_jp,
		]);
	}
    $total_bruto_pertahun = $total_bruto_perbulan * 12;

    // dd($metode);
	$total_ptkp = perhitunganPtkp($ptkp, $total_neto_pertahun);
	$total_pkp = $total_neto_pertahun - $total_ptkp;
	$total_pph_terutang_setahun = perhitunganPPH21($metode, $npwp, $total_pkp);
    $total_pph_terutang_perbulan_desember = 0;
    // if($tahunperhitungan == 2024) {
        // dd($ptkp->ptkp_detail);
    if($menggunakan_ter == 1) {
        $rate_nonnpwp = 100;
        // if($npwp == 'NO-NPWP') {
        //     $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
        //     $rate_nonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
        // }
        // dd($tahunperhitungan);
        if($metode == 'GROSS') {
            $total_pph_terutang_perbulan = intval($total_bruto_perbulan * $ptkpdetratepercentage / 100);
        } else {
            $ptkpcategoryrate = 0;
            $ptkpcategoryratemonth = 0;
            if($ptkp && $ptkp->ptkp_detail) {
                $ptkpdet = $ptkp->ptkp_detail;
                for($i=0; $i<count($ptkpdet); $i++) {
                    if($total_bruto_perbulan < $ptkpdet[$i]['ptkpdet_rate_month']) {
                        $ptkpcategoryrate = $ptkpdet[$i]['ptkpdet_rate_percentage'];
                        $ptkpcategoryratemonth = $ptkpdet[$i]['ptkpdet_rate_month'];
                        break;
                    }
                }
            }
            // dd($ptkpcategoryrate);

            $total_pph_terutang_perbulan_gross_up = intval($total_bruto_perbulan * $ptkpdetratepercentage / (100 - $ptkpdetratepercentage));
            $total_bruto_perbulan_gross_up = intval($total_bruto_perbulan + $total_pph_terutang_perbulan_gross_up);
            $ptkpcategoryrategrossup = 0;
            $ptkpcategoryratemonthgrossup = 0;
            // $total_ptkp = perhitunganPtkpTER($ptkp, $total_neto_pertahun);
            // pph21Perbulan11GrossUp = Math.floor(brutoPerbulan11 * ptkpCategoryRate / (100 - ptkpCategoryRate));
                    // brutoPerbulan11GrossUp = brutoPerbulan11+pph21Perbulan11GrossUp;
                    // let ptkpCategoryRateGrossUp = 0;
                    // let ptkpCategoryRateMonthGrossUp = 0;
                    if($ptkp && $ptkp->ptkp_detail) {
                        $ptkpdet = $ptkp->ptkp_detail;
                        for($i=0; $i<count($ptkpdet); $i++) {
                            if($total_bruto_perbulan_gross_up < $ptkpdet[$i]['ptkpdet_rate_month']) {
                                $ptkpcategoryrategrossup = $ptkpdet[$i]['ptkpdet_rate_percentage'];
                                $ptkpcategoryratemonthgrossup = $ptkpdet[$i]['ptkpdet_rate_month'];
                                break;
                            }
                        }
                    }

                    if($ptkpcategoryrate != $ptkpcategoryrategrossup) {
                        $ptkpcategoryrate = $ptkpcategoryrategrossup;
                        $ptkpcategoryratemonth = $ptkpcategoryratemonthgrossup;
                    }
                    $total_pph_terutang_perbulan = floor($total_bruto_perbulan * ($ptkpcategoryrate / (100 - $ptkpcategoryrate)));
                    // dd($total_pph_terutang_perbulan, $total_bruto_perbulan);
        }
        // $total_pph_terutang_perbulan = $total_pph_terutang_perbulan * $rate_nonnpwp / 100;
        
        // if($metode != 'GROSS') {
        //     $total_bruto_perbulan += $total_pph_terutang_perbulan; 
        // }
        $total_pph_terutang_perbulan_desember = intval($total_pph_terutang_setahun - ($total_pph_terutang_perbulan * 11));
        $pengurangPertahun = ($total_biaya_jabatan * 12) + ($biaya_jamkes * 12) + ($biaya_jht * 12) + ($biaya_jp * 12);
        $netoSetahun = $total_bruto_pertahun - $pengurangPertahun;
        $pkpTotal = $netoSetahun - $total_ptkp;
        $totalTarif21 = perhitunganTarifTER($pkpTotal);
        // dd($totalTarif21);
                
        $total_pph_terutang_perbulan_desember = $totalTarif21 - ($total_pph_terutang_perbulan*11);
        $total_pph_terutang_setahun = $total_pph_terutang_perbulan_desember + ($total_pph_terutang_perbulan * 11);
        $total_pph_terutang_perbulan_desember = ($total_pph_terutang_perbulan_desember > 0) ? $total_pph_terutang_perbulan_desember : 0;
        //         perhitunganTotalPPHTerutangPerbulan.set(totalPPhTerutangDesember);
        
    } else {
	    $total_pph_terutang_perbulan = $total_pph_terutang_setahun / 12;
    }

    return [
        'gaji' => $salary,
        'biaya_jamkes' => $biaya_jamkes,
        'biaya_jht' => $biaya_jht,
        'biaya_jp' => $biaya_jp,
        'biaya_jamkes_rate' => $biaya_jamkes_rate,
        'biaya_jht_rate' => $biaya_jht_rate,
        'biaya_jp_rate' => $biaya_jp_rate,
        'total_biaya_jabatan' => $total_biaya_jabatan,
        'penghasilan_jamkes' => $penghasilan_jamkes,
        'penghasilan_jkk' => $penghasilan_jkk,
        'penghasilan_jkm' => $penghasilan_jkm,
        'penghasilan_jht' => $penghasilan_jht,
        'penghasilan_jp' => $penghasilan_jp,
        'biaya_jamkes_rate' => $biaya_jamkes_rate,
        'biaya_jht_rate' => $biaya_jht_rate,
        'biaya_jp_rate' => $biaya_jp_rate,
        'penghasilan_jamkes_rate' => $penghasilan_jamkes_rate,
        'penghasilan_jkk_rate' => $penghasilan_jkk_rate,
        'penghasilan_jkm_rate' => $penghasilan_jkm_rate,
        'penghasilan_jht_rate' => $penghasilan_jht_rate,
        'penghasilan_jp_rate' => $penghasilan_jp_rate,
        'penghasilan_tk_nominal' => (isset($karyawan_bpjs['penghasilan_kes_nominal']) && $karyawan_bpjs['penghasilan_tk_nominal']) ? $karyawan_bpjs['penghasilan_tk_nominal'] : 0,
        'penghasilan_kes_nominal' => (isset($karyawan_bpjs['penghasilan_kes_nominal']) && $karyawan_bpjs['penghasilan_kes_nominal']) ? $karyawan_bpjs['penghasilan_kes_nominal'] : 0,
        'nominal_tunjangan_lain' => $tunjangan['tunjangan_nominal'],
        'total_bruto_perbulan' => $total_bruto_perbulan,
        'total_bruto_pertahun' => $total_bruto_pertahun,
        'total_neto_perbulan' => $total_neto_perbulan,
        'total_neto_pertahun' => $total_neto_pertahun,
        'total_ptkp' => $total_ptkp,
        'total_pkp' => $total_pkp,
        'total_pph_terutang_setahun' => intval($total_pph_terutang_setahun),
        'total_pph_terutang_perbulan' => ($total_pph_terutang_perbulan > 0) ? intval($total_pph_terutang_perbulan) : 0,
        'total_pph_terutang_perbulan_desember' => ($total_pph_terutang_perbulan_desember > 0) ? intval($total_pph_terutang_perbulan_desember) : 0,
    ];
}