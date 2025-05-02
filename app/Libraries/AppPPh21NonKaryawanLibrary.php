<?php
namespace App\Libraries;

use App\Model\Master\BpjsRateModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use Error;
use Illuminate\Support\Facades\Storage;

class AppPPh21NonKaryawanLibrary {
    var $tarif21;
    var $karyawan;
    var $pendapatankotor;
    var $dpp;
    var $ratenonnpwp;
    
    public function getTarifPPH21($total_pkp=0)
    {
        // $tarif21 = Tarif21Model::where(['tarif21_active' => 1])->get();
        $tarif21 = $this->tarif21;
        $t21 = [];
        foreach($tarif21 as $trf) {
            if ($trf['tarif21_startincome'] <= $total_pkp) {
                array_push($t21, $trf);
            }
        }
        return $t21;
    }

    public function getPtkpDetail($total_pkp=0)
    {
        $ptkp_detail = $this->karyawan->ptkp->ptkp_detail;
        $ptkpdet = null;
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

    public function perhitunganNeto($tahun=false, $total_bruto)
    {
        return ($tahun) ? 12 * $total_bruto : $total_bruto;
    }

    /**
    * DONT DELETE
    * FOR PPH 21 before 2024
    */
    // public function calculate($pendapatankotor = 0, $total_dpp_kumulatif = 0, $metode = 'GROSS', $ptkp, $multiplework = 0)
    // {
    //     // dd($ptkp);
    //     $totalPPH_terutang = 0;
    //     $total_ptkp_bulan = 0;
    //     $dpp = $pendapatankotor * 50 / 100;
    //     // dd($dpp);
    //     if($multiplework == 0) {
    //         $total_ptkp_bulan = $ptkp->ptkp_rate / 12;
    //     }
    //     // dd($dpp);
    //     // dd($total_dpp_kumulatif);
    //     $total_dpp = $dpp - $total_ptkp_bulan;
    //     $total_dpp_sebelumnya_kumulatif = $total_dpp_kumulatif;
    //     $total_dpp_kumulatif += $total_dpp;

        
    //     $diffgrossnet_sebelumnya = 0;
    //     $tarif21_sebelumnya = null;
    //     $dpp_sebelumnya_rate = 0;
    //     $dpp = $total_dpp;
    //     $ratelabel = '';

    //     $tarif21 = $this->getTarifPPH21($total_dpp_kumulatif);
    //     $dpp_rate = (count($tarif21) > 0) ? $tarif21[count($tarif21)-1]->tarif21_rate : 0;
    //     $diffgrossnet = 100 - (50 * $dpp_rate / 100);
    //     if($metode == 'GROSS' || $metode == 'NETT') {
    //         if($total_dpp > 0) {
    //             foreach($tarif21 as $rate) {
    //                 // if($rate->tarif21_startincome > )
    //                 if($rate->tarif21_endincome > $total_dpp_sebelumnya_kumulatif) {
    //                     $dpp_sebelumnya_rate = $rate->tarif21_rate;
    //                     $tarif21_sebelumnya = $rate;
    //                     break;
    //                 }
    //             }
                
    //             if($dpp_rate != $dpp_sebelumnya_rate) {
    //                 // dd($dpp_rate, $dpp_sebelumnya_rate, $total_dpp_sebelumnya_kumulatif);
    //                 $dpp_sb = $tarif21_sebelumnya->tarif21_endincome - $total_dpp_sebelumnya_kumulatif;
    //                 $totalPPH_terutang += $dpp_sb * $dpp_sebelumnya_rate / 100;
    //                 $totalPPH_terutang += ($total_dpp - $dpp_sb) * $dpp_rate / 100;

    //                 $ratelabel = $dpp_sebelumnya_rate.' %,'.$dpp_rate.' %';
    //             } else {
    //                 $totalPPH_terutang += $dpp * $dpp_rate / 100;
    //                 $ratelabel = $dpp_rate.' %';
    //             }
    //         }
    //     } else {
    //         if($total_dpp > 0) {
    //             foreach($tarif21 as $rate) {
    //                 // if($rate->tarif21_startincome > )
    //                 if($rate->tarif21_endincome > $total_dpp_sebelumnya_kumulatif) {
    //                     $dpp_sebelumnya_rate = $rate->tarif21_rate;
    //                     $diffgrossnet_sebelumnya = 100 - (50 * $rate->tarif21_rate / 100);
    //                     // $rategrossnet_sebelumnya = $rate->tarif21_rate;
    //                     $tarif21_sebelumnya = $rate;
    //                     break;
    //                 }
    //             }
    //             // dd($diffgrossnet);
    //             if($dpp_rate != $dpp_sebelumnya_rate) {
    //                 $dpp_sb = $tarif21_sebelumnya->tarif21_endincome - $total_dpp_sebelumnya_kumulatif;
    //                 $total_dppreal = $total_dpp * 100 / $diffgrossnet; // dpp
    //                 // dd($total_dppreal);
    //                 // dd('dpp_sb='.$dpp_sb, 'total_dpp='.$total_dpp,'diffgrossnet_sebelumnya='.$diffgrossnet_sebelumnya);
    //                 $totalPPH_terutang += $dpp_sb * $dpp_sebelumnya_rate / 100;
    //                 $dpp_sb1 = $total_dppreal - $dpp_sb;
    //                 // dd($total_dppreal, $dpp_sb1, $dpp_sb, $dpp_sb1 * $dpp_rate / 100);
    //                 // dd($total_dpprealsebelumnya,$dpp_sb * $dpp_sebelumnya_rate / 100, $dpp_sb, $dpp_sb1, $tarif21_sebelumnya->tarif21_endincome, $total_dpp_sebelumnya_kumulatif);
    //                 $totalPPH_terutang += $dpp_sb1 * $dpp_rate / 100;
    //                 $total_dpp = $total_dppreal;
    //                 // dd($total_dpp);
    //                 // dd('dpp_sb='.$dpp_sb, 'total_dpp='.$total_dpp,'diffgrossnet_sebelumnya='.$diffgrossnet_sebelumnya);
    //                 $ratelabel = $dpp_sebelumnya_rate.' %,'.$dpp_rate.' %';
    //             } else {
    //                 $total_dppreal = $total_dpp * 100 / $diffgrossnet; // dpp
    //                 $totalPPH_terutang = $total_dppreal * $dpp_rate / 100;

    //                 $total_dpp = $total_dppreal;

    //                 $ratelabel = $dpp_rate.' %';
    //             }
    //             $total_dpp_kumulatif = $total_dpp_sebelumnya_kumulatif + $total_dpp;
    //         }
    //     }
        
    //     return [
    //         'total_ptkp_bulan' => $total_ptkp_bulan,
    //         'total_dpp' => $total_dpp,
    //         'total_dpp_kumulatif' => ($total_dpp_kumulatif > 0) ? $total_dpp_kumulatif : 0,
    //         'total_pph_terutang' => $totalPPH_terutang,
    //         'tarif21' => $tarif21,
    //         'dpp_rate' => $dpp_rate,
    //         'ratelabel' => $ratelabel,
    //     ];
    // }

    /**
     * PPH 21 2024 (TER)
     */
    public function calculate()
    {
        $method = $this->karyawan->karyawan_calculation_method;
        // $method = 'GROSS_UP';
        // $ptkp_category = $this->karyawan->ptkp->ptkp_category;
        $total_pkp = 0;
        $ptkpcategoryrate = 0;
        $tarif21data = null;
        $total_dppgrossup = 0;
        if($method == 'GROSS' || $method == 'NET') {
           $total_pkp = $this->pendapatankotor; // pkp
        } else {
            $tarif21 = $this->getTarifPPH21($this->dpp);
            $tarif21data = (count($tarif21) > 0) ? $tarif21[count($tarif21)-1] : [];
            $dpp_rate = (count($tarif21data) > 0) ? $tarif21data['tarif21_rate'] : 0;
            $diffgrossup = 100 - (50 * $dpp_rate / 100);
            $total_dppgrossup = $this->dpp * 100 / $diffgrossup; // dpp grossup
            $nonkaryawan_pendapatankotor = $total_dppgrossup * 100 / 50; // get pendapatan kotor
            $total_pkp = $nonkaryawan_pendapatankotor; // pkp
        }
        // get rate from category
        $ptkp_detail = $this->getPtkpDetail($total_pkp);
        // dd($ptkp_detail);
        $ptkpcategoryrate = ($ptkp_detail) ? $ptkp_detail['ptkpdet_rate_percentage'] : 34;
        // get pph 21
        $totalPPH_terutang = intval(($this->ratenonnpwp / 100) * $total_pkp * 50/100 * $ptkpcategoryrate / 100);
        // dd($rate_nonnpwp, $total_pkp, $totalPPH_terutang, $ptkpcategoryrate);
        
        unset($this->karyawan->ptkp->ptkp_detail);
        return [
            'total_bruto' => $this->pendapatankotor,
            'total_pkp' => $total_pkp,
            'total_dpp' => $this->dpp,
            'total_dpp_grossup' => $total_dppgrossup,
            'rate_nonnpwp' => $this->ratenonnpwp,
            'metode' => $method,
            'total_pph_terutang' => $totalPPH_terutang,
            'tarif21' => $tarif21data,
            'ptkp' => $this->karyawan->ptkp->toArray(),
            'ptkp_detail' => $ptkp_detail,
        ];
    }

}