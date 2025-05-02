<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Access-Control-Allow-Origin" content="*" />
  <link rel="icon" type="image/x-icon" href="assets/img/logo/logo.png" />
  <link href="{{asset('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <style>
    body {
      color: #000;
      letter-spacing: 0.01px;
    }
    
    .flex {
      display: flex;
      justify-content: space-between;
    }
    .black-box {
      background-color: #000;
      width: 20px;
      height: 10px;
      display: block;
    }
    .white-box-inline {
      width: 15px;
      height: 10px;
      display: inline-block;
      border: 1px solid;
    }
    .black-box-inline {
      background-color: #000;
      width: 15px;
      height: 10px;
      display: inline-block;
      border: 1px solid;
    }
    hr {
      margin-top: 0;
      margin-bottom: 0;
      border-top: 1px solid #000;
    }
    .fs-11 {
      font-size: 11px;
    }
    .fs-12 {
      font-size: 12px;
    }
    .fs-14 {
      font-size: 14px;
    }
    .fs-16 {
      font-size: 16px;
    }
    .fs-18 {
      font-size: 18px;
    }
    .mt-30 {
      margin-top: 30px;
    } 
    .mb-30 {
      margin-bottom: 30px;
    }
    .mb-60 {
      margin-bottom: 60px;
    }
    .pb-10 {
      padding-bottom: 10px;
    }
    h3 {
      margin-top: 10px;
    }
    .header {
      border-bottom: 3px solid;
    }
    .boxlogo {
      padding-top: 5px;
    }
    .boxlogo, .boxtoptitle {
      border-right: 3px solid #000;
    }
    .boxnomor {
      padding: 13.8px 15px;
      border-top: 3px solid #000;
      /* border-bottom: 3px solid #000; */
      border-right: 3px solid #000;
    }
    .boxnomor span, .boxmasa span, .boxspan span {
      padding: 1px 10px;
    }
    .pemotong {
      border-bottom: 3px solid #000;
      border-right: 3px solid #000;
      border-left: 3px solid #000;
      padding: 10px 15px;
    }
    .boxidentitas {
      border: 3px solid #000;
      padding: 10px 15px;
    }
    .table {
      margin-bottom: 0px;
    }
    .table > caption + thead > tr:first-child > td, .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > td, .table > thead:first-child > tr:first-child > th {
      border-top: 1px solid #000;
    }
    .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td {
      border: 1px solid #000;
    }
    .blur {
      background-color: #ddd;
    }
  </style>
  <?php

  use Carbon\Carbon;

  $i = 0;
  $periode = (request()->get('periode')) ? request()->get('periode') : date('m-Y');
  $filtermonth = Carbon::createFromFormat('!m-Y', $periode)->format('m');
  $filteryear = Carbon::createFromFormat('!m-Y', $periode)->format('Y');
  $beginmonth = '01';
  $karyawans = [];
  $nobuktipotong = '';
  
  // $ji=0;
  foreach($data as $dt) {
    $karyawans[$dt->ms_karyawan_id][] = $dt;
    // if($ji==0) {
      // if($dt->pph21->pph21_pajakpph21_data) {
      //   $stpajakpph21 = json_decode($dt->pph21->pph21_pajakpph21_data);
      // }
    // }

    // $ji++;
  }
  // if(count($data) > 0) {
    // $lastdata = $data[count($data)-1];
    // $stpajakpph21 = ($lastdata->pph21->pph21_pajakpph21_data) ? json_decode($lastdata->pph21->pph21_pajakpph21_data) : null;
    // $stpajakpph21 = json_decode($lastdata->pph21->pph21_pajakpph21_data);
  // }
  // dd($stpajakpph21);
  // stpajakpph21
  // $stpajakpph21_companyname = ($stpajakpph21 && isset($stpajakpph21->stpajakpph21_companyname)) ? $stpajakpph21->stpajakpph21_companyname : '';
  // $stpajakpph21_name = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_name : '';
  // $stpajakpph21_npwp = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_npwp : [];
  // $stpajakpph21_npwpseparate = ($stpajakpph21_npwp) ? explode('-', $stpajakpph21_npwp) : [];
  // $stpajakpph21_npwpseparatelast = (count($stpajakpph21_npwpseparate) > 0) ? explode('.', $stpajakpph21_npwpseparate[1]) : [];
  // $stpajakpph21_npwp1 = (count($stpajakpph21_npwpseparate) > 1) ? $stpajakpph21_npwpseparate[0] : '';
  // $stpajakpph21_npwp2 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[0] : '';
  // $stpajakpph21_npwp3 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[1] : '';

  $wajibpajak_name = $data[0]->wajibpajak->wajibpajak_name;
  $wajibpajak_npwpseparate = explode('-', $data[0]->wajibpajak->wajibpajak_npwp);
  $wajibpajak_npwpseparatelast = explode('.', $wajibpajak_npwpseparate[1]);
  $wajibpajak_npwp1 = $wajibpajak_npwpseparate[0];
  $wajibpajak_npwp2 = $wajibpajak_npwpseparatelast[0];
  $wajibpajak_npwp3 = (count($wajibpajak_npwpseparatelast) > 1) ? $wajibpajak_npwpseparatelast[1] : '';
  
  
  // dd($karyawans);
  foreach($karyawans as $dt) :
    // dd($dt[0]->karyawan->country->country_name);
    // dd($dt[0]->pph21);
    $lastdtidx = count($dt) - 1;

    $stpajakpph21 = json_decode($dt[$lastdtidx]->pph21->pph21_pajakpph21_data);
    $stpajakpph21_companyname = ($stpajakpph21 && isset($stpajakpph21->stpajakpph21_companyname)) ? $stpajakpph21->stpajakpph21_companyname : '';
    $stpajakpph21_name = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_name : '';
    $stpajakpph21_npwp = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_npwp : [];
    $stpajakpph21_npwpseparate = ($stpajakpph21_npwp) ? explode('-', $stpajakpph21_npwp) : [];
    $stpajakpph21_npwpseparatelast = (count($stpajakpph21_npwpseparate) > 0) ? explode('.', $stpajakpph21_npwpseparate[1]) : [];
    $stpajakpph21_npwp1 = (count($stpajakpph21_npwpseparate) > 1) ? $stpajakpph21_npwpseparate[0] : '';
    $stpajakpph21_npwp2 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[0] : '';
    $stpajakpph21_npwp3 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[1] : '';

    $beginmonth = Carbon::parse($dt[0]->pph21->pph21_period)->format('m');
    $filtermonth = Carbon::parse($dt[$lastdtidx]->pph21->pph21_period)->format('m');
    $nobuktipotong = $dt[$lastdtidx]->pph21->pph21_nobuktipotong;
    $nik = $dt[$lastdtidx]->payroll_karyawan_nik;
    $karyawan_name = $dt[$lastdtidx]->payroll_karyawan_name;
    $karyawan_address = $dt[$lastdtidx]->payroll_karyawan_address;
    $split_address = [];
    if( strlen( $karyawan_address) > 50) {
      $split_address = explode( "\n", wordwrap( $karyawan_address, 60));
    } else {
      $split_address[] = $karyawan_address;
    }

    $karyawan_gender = $dt[$lastdtidx]->payroll_karyawan_gender;
    $karyawanjabatan_name = $dt[$lastdtidx]->payroll_karyawanjabatan_name;
    $karyawan_citizenship = $dt[$lastdtidx]->karyawan->karyawan_citizenship;
    $country_name = ($dt[$lastdtidx]->karyawan->country) ? $dt[$lastdtidx]->karyawan->country->country_name : '&nbsp;';
    $payroll_method = $dt[$lastdtidx]->payroll_method;
    $ptkp_description = $dt[$lastdtidx]->pph21->pph21_ptkp_description;
    // $name = "[hi] helloz [hello] (hi) {jhihi}";
    $ptkp_code = preg_replace('/[\(].*?[\)]/' , '', $ptkp_description);
    // $ptkp_code = $ptkp_description;
    // dd($ptkp_description, $ptkp_code);
    $npwp1 = '';
    $npwp2 = '';
    $npwp3 = '';
    if($dt[0]->payroll_karyawan_npwp) {
      $npwpseparate = explode('-', $dt[$lastdtidx]->payroll_karyawan_npwp);
      $npwpseparatelast = explode('.', $npwpseparate[1]);
      $npwp1 = $npwpseparate[0];
      $npwp2 = $npwpseparatelast[0];
      $npwp3 = (count($npwpseparatelast) > 1) ? $npwpseparatelast[1] : '';
    }

    $total_prorate_salary = 0;
    $total_allowance_pph21 = 0;
    $total_tunjangan = 0;
    $total_tunjangan_tahunan = 0;
    $total_allowance_bpjstkkes = 0;
    $total_biaya_jabatan = 0;
    $total_biaya_bpjstkjht = 0;
    $total_biaya = 0;
    $total_netto = 0;
    $total_netto_year = 0;
    $total_ptkp = 0;
    $total_pkp = 0;
    $total_pph21_year = 0;
    $total_pph21 = 0;
    $prev_cp_netto = 0;
    $prev_cp_pph21_total = 0;
    foreach($dt as $tdt) {
      $total_prorate_salary += $tdt->pph21->pph21_prorate_salary;
      $total_allowance_pph21 += $tdt->payroll_allowance_pph21;
      $total_allowance_bpjstkkes += $tdt->payroll_allowance_bpjstk + $tdt->payroll_allowance_bpjskes;
      $total_biaya_jabatan += $tdt->pph21->pph21_deduction_position;
      $total_biaya_bpjstkjht += $tdt->pph21->pph21_deduction_jht_payslip + $tdt->pph21->pph21_deduction_jp;

      $total_netto += $tdt->pph21->pph21_netto_month;
      $total_netto_year += $tdt->pph21->pph21_netto_year;
      $total_ptkp += $tdt->pph21->pph21_ptkp;
      if($tdt->pph21->pph21_prevcp_netto > 0) {
        $total_pkp += $tdt->pph21->pph21_prevcp_netto + $tdt->pph21->pph21_netto_year - $tdt->pph21->pph21_ptkp;
        $prev_cp_netto = $tdt->pph21->pph21_prevcp_netto;
        $prev_cp_pph21_total = $tdt->pph21->pph21_prevcp_pph21_total;
      } else {
        $total_pkp += $tdt->pph21->pph21_pkp;
      }
      $total_biaya += $total_biaya_jabatan + $total_biaya_bpjstkjht;
      $total_pph21 += $tdt->pph21->pph21_total_month;
      $total_pph21_year += $tdt->pph21->pph21_total_month * 12;

      $tunjangan = json_decode($tdt->payroll_allowance_setting);
      if($tunjangan) {
        foreach($tunjangan as $tj) {
          if($tj->sttunjangankaryawan_period == 'TAHUN') {
              $total_tunjangan_tahunan += $tj->sttunjangankaryawan_accumulate_value;
          } else {
              $total_tunjangan += $tj->sttunjangankaryawan_accumulate_value;
          }
        }
      }
      $customtunjangan = json_decode($tdt->payroll_allowance_addition);
      if($customtunjangan) {
        foreach($customtunjangan as $ctj) {
          $total_tunjangan += $ctj->nominal;
        }
      }
    }
    
    $total_bruto = $total_prorate_salary + $total_allowance_pph21 + $total_tunjangan + $total_allowance_bpjstkkes;
    // $total_biaya = $total_biaya_jabatan + $total_biaya_bpjstkjht;
    // $total_netto = $total_bruto - $total_biaya;
  ?>
    <div class="container pdf" id="pdf{{$i}}" data-kyname="{{$karyawan_name}}">
      <p style="font-size: 13px; color: rgb(166, 166, 166); letter-spacing: 2px; margin-bottom: 0px">areastaples</p>
      <hr>
      <div class="flex">
        <span class="black-box"></span>
        <span class="black-box"></span>
      </div>
      <div class="header">
        <div class="row">
          <div class="col-sm-3 text-center boxlogo pb-10">
            <img src="{{asset('assets/img/logo/kemenkeu-logo.png')}}" alt="" class="img-fluid" width="120" />
            <h3 class="fs-12 text-bold">
              KEMENTERIAN KEUANGAN RI
              <br>
              DIREKTORAT JENDERAL PAJAK
            </h3>
          </div>
          <div class="col-sm-6 text-center">
            <div class="row">
              <div class="col-sm-11 boxtoptitle">
                <h3 class="fs-18 text-bold mb-30">
                BUKTI PEMOTONGAN PAJAK PENGHASILAN<br>
                PASAL 21 BAGI PEGAWAI TETAP ATAU<br>
                PENERIMA PENSIUN ATAU TUNJANGAN HARI<br>
                TUA/JAMINAN HARI TUA BERKALA
              </h3>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 text-left boxnomor">
              NOMOR : <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">H.01</span> <span>1</span><span>.</span><span>1</span><span>-</span><span class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$filtermonth}}</span><span>.</span><span class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$filteryear}}</span><span>-</span><span class="text-center" style="width: 140px; border-bottom: 2px solid; display: inline-block;">{{$nobuktipotong}}</span>
              </div>
            </div>
          </div>
          <div class="col-sm-3 text-right">
            <div class="row">
              <div class="col-sm-12">
                <div>
                  <span class="white-box-inline"></span> <span class="black-box-inline"></span> <span class="white-box-inline"></span> <span class="black-box-inline"></span>
                </div>
                <h3 class="fs-12 text-bold">
                  FORMULIR 1721 - A1
                </h3>
                <div class="text-left" style="margin-left: -50px">
                  <p class="fs-11">
                    Lembar ke-1 : untuk Penerima Penghasilan
                    <br>
                    Lembar ke-2 : untuk Pemotong
                  </p>
                </div>
              </div>
              <div class="col-sm-12 text-left boxmasa" style="padding: 10px 15px;">
                <h3 class="fs-12 text-center" style="margin-top: 0px; margin-bottom: 5px">
                  MASA PEROLEHAN<br>PENGHASILAN (mm - mm)
                </h3>
                <p style="margin-bottom: 0px;">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">H.02</span> 
                  <span class="text-center" class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$beginmonth}}</span>
                  <span>-</span>
                  <span class="text-center" class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$filtermonth}}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- pemotong -->
      <div class="pemotong">
        <div class="row">
          <div class="col-sm-2">
            NPWP
            <br>
            PEMOTONG 
            <span class="pull-right">:</span>
          </div>
          <div class="col-sm-10 boxspan">
            <br>
            <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">H.03</span> 
            <span style="width: 150px; border-bottom: 2px solid; display: inline-block;">{{$wajibpajak_npwp1}}</span>
            <span>-</span>
            <span style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$wajibpajak_npwp2}}</span>
            <span>.</span>
            <span style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{$wajibpajak_npwp3}}</span>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2">
            NAMA
            <br>
            PEMOTONG 
            <span class="pull-right">:</span>
          </div>
          <div class="col-sm-10 boxspan">
            <br>
            <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">H.04</span> 
            <span style="width: 90%; border-bottom: 2px solid; display: inline-block;">{{$stpajakpph21_companyname}}</span>
          </div>
        </div>
      </div>
      <!-- identitas -->
      <div class="identitas">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="fs-16">A. IDENTITAS PENERIMA PENGHASILAN YANG DIPOTONG</h3>
          </div>
        </div>
        <div class="boxidentitas">
          <div class="row ">
            <div class="col-sm-7">
              <div class="row">
                <div class="col-sm-3">
                  1. NPWP
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.01</span> 
                  <span style="width: 150px; border-bottom: 2px solid; display: inline-block;">{!!($npwp1) ? $npwp1 : '&nbsp;' !!}</span>
                  <span>-</span>
                  <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{!!($npwp2) ? $npwp2 : '&nbsp;' !!}</span>
                  <span>.</span>
                  <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{!!($npwp3) ? $npwp3 : '&nbsp;' !!}</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-3">
                  2.
                    NIK/NO
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;PASSPOR
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <br>
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.02</span> 
                  <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">{{$nik}}</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-3">
                  3. NAMA
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.03</span> 
                  <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">{{$karyawan_name}}</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-3">
                  4. ALAMAT
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.04</span> 
                  <?php 
                    if(count($split_address) > 0) :
                      $j = 0;
                      foreach($split_address as $address) : ?>
                      @if($j == 0)
                      <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">{{$address}}</span>
                      @else
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;visibility:hidden">A.04</span>
                      <span style="width: 85%; border-bottom: 2px solid; display: inline-block; margin-top:20px;">{{$address}}</span>
                      @endif
                      <?php $j++;
                        endforeach;
                    else: ?>
                    <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">&nbsp;</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  5. JENIS KELAMIN
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-8 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.05</span> 
                  <span class="text-center" style="width: 35px; height: 25px; border: 2px solid; display: inline-block;">{!! $karyawan_gender == 'L' ? 'X' : '&nbsp;' !!}</span> &nbsp;LAKI-LAKI
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.06</span> 
                  <span class="text-center" style="width: 35px; height: 25px; border: 2px solid; display: inline-block;">{!! $karyawan_gender == 'P' ? 'X' : '&nbsp;' !!}</span> &nbsp; PEREMPUAN
                </div>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="row mb-3">
                <div class="col-sm-12">
                  6. STATUS/JUMLAH TANGGUNGAN KELUARGA UNTUK PTKP
                </div>
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-4">
                      K /
                      <br>
                      &nbsp;&nbsp;&nbsp;
                      <span class="text-center" style="width: 60%; border-bottom: 2px solid; display: inline-block;">{!!strpos($ptkp_code, 'TK/') > -1 ? '&nbsp;' : str_replace('K/', '', $ptkp_code) !!}</span>
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.07</span>
                    </div>
                    <div class="col-sm-4">
                      TK /
                      <br>
                      &nbsp;&nbsp;&nbsp;
                      <span class="text-center" style="width: 60%; border-bottom: 2px solid; display: inline-block;">{!!strpos($ptkp_code, 'TK/') > -1 ? str_replace('TK/', '', $ptkp_code) : '&nbsp;' !!}</span>
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.08</span>
                    </div>
                    <div class="col-sm-4">
                      HB /
                      <br>
                      &nbsp;&nbsp;&nbsp;
                      <span style="width: 60%; border-bottom: 2px solid; display: inline-block;"></span>
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.09</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-5">
                  7. NAMA JABATAN :
                </div>
                <div class="col-sm-7 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.10</span> 
                  <span style="width: 75%; border-bottom: 2px solid; display: inline-block;">{!!($karyawanjabatan_name) ? $karyawanjabatan_name : '&nbsp;' !!}</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-5">
                  8. KARYAWAN ASING :
                </div>
                <div class="col-sm-7 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.11</span> 
                  <span style="width: 35px; height: 25px; border: 2px solid; display: inline-block;">{!! ($karyawan_citizenship == 'WNA') ? 'X' : '&nbsp;' !!}</span> &nbsp; YA
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  9. KODE NEGARA DOMISILI :
                </div>
                <div class="col-sm-6 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.12</span> 
                  <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{!! ($karyawan_citizenship == 'WNA') ? $country_name : '&nbsp;' !!}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- rincian -->
      <div class="rincian">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="fs-16">B. RINCIAN PENGHASILAN DAN PENGHITUNGAN PPh PASAL 21</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center" colspan="2">URAIAN</th>
                  <th class="text-center" style="width: 250px;">JUMLAH (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th colspan="2">
                    KODE OBJEK PAJAK &nbsp;&nbsp;&nbsp;: <span class="text-center" style="width: 25px; height: 20px; border: 2px solid; display: inline-block;">X</span> &nbsp; 21-100-01
                    &nbsp; &nbsp; &nbsp;
                    <span style="width: 25px; height: 20px; border: 2px solid; display: inline-block;">&nbsp;</span> &nbsp; 21-100-02
                  </th>
                  <td class="blur"></td>
                </tr>
                <tr>
                  <th colspan="2">
                    PENGHASILAN BRUTO &nbsp;&nbsp;&nbsp;: 
                  </th>
                  <td class="blur"></td>
                </tr>
                <tr>
                  <td>1.</td>
                  <td>GAJI/PENSIUN ATAU THT/JHT</td>
                  <td class="text-right">Rp. {{number_format($total_prorate_salary, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>2.</td>
                  <td>TUNJANGAN PPh</td>
                  <td class="text-right">Rp. {{number_format($total_allowance_pph21, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>3.</td>
                  <td>TUNJANGAN LAINNYA, UANG LEMBUR DAN SEBAGAINYA</td>
                  <td class="text-right">Rp. {{number_format($total_tunjangan, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>4.</td>
                  <td>HONORARIUM DAN IMBALAN LAIN SEJENISNYA</td>
                  <td class="text-right">Rp. 0</td>
                </tr>
                <tr>
                  <td>5.</td>
                  <td>PREMI ASURANSI YANG DIBAYAR PEMBERI KERJA</td>
                  <td class="text-right">Rp. {{number_format($total_allowance_bpjstkkes, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>6.</td>
                  <td>PENERIMAAN DALAM BENTUK NATURA DAN KENIKMATAN LAINNYA YANG DIKENAKAN PEMOTONGAN PPh PASAL 21</td>
                  <td class="text-right">Rp. 0</td>
                </tr>
                <tr>
                  <td>7.</td>
                  <td>TANTIEM, BONUS, GRATIFIKASI, JASA PRODUKSI DAN THR</td>
                  <td class="text-right">Rp. {{number_format($total_tunjangan_tahunan, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>8.</td>
                  <td>JUMLAH PENGHASILAN BRUTO (1 S.D.7)</td>
                  <td class="text-right">Rp. {{number_format($total_bruto, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <th colspan="2">
                    PENGURANGAN &nbsp;&nbsp;&nbsp;: 
                  </th>
                  <td class="blur"></td>
                </tr>
                <tr>
                  <td>9.</td>
                  <td>BIAYA JABATAN/ BIAYA PENSIUN</td>
                  <td class="text-right">Rp. {{number_format($total_biaya_jabatan, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>10.</td>
                  <td>IURAN PENSIUN ATAU IURAN THT/JHT</td>
                  <td class="text-right">Rp. {{number_format($total_biaya_bpjstkjht, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>11.</td>
                  <td>JUMLAH PENGURANGAN (9 S.D 10)</td>
                  <td class="text-right">Rp. {{number_format($total_biaya, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <th colspan="2">
                    PENGHITUNGAN PPh PASAL 21 : &nbsp;&nbsp;&nbsp;: 
                  </th>
                  <td class="blur"></td>
                </tr>
                <tr>
                  <td>12.</td>
                  <td>JUMLAH PENGHASILAN NETO (8-11)</td>
                  <td class="text-right">Rp. {{number_format($total_netto, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>13.</td>
                  <td>PENGHASILAN NETO MASA SEBELUMNYA</td>
                  <td class="text-right">Rp. {{number_format($prev_cp_netto, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>14.</td>
                  <td>JUMLAH PENGHASILAN NETO UNTUK PENGHITUNGAN PPh PASAL 21 (SETAHUN/DISETAHUNKAN)</td>
                  <td class="text-right">Rp. {{number_format($total_netto_year, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>15.</td>
                  <td>PENGHASILAN TIDAK KENA PAJAK (PTKP)</td>
                  <td class="text-right">Rp. {{number_format($total_ptkp, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>16.</td>
                  <td>PENGHASILAN KENA PAJAK SETAHUN/DISETAHUNKAN (14 - 15)</td>
                  <td class="text-right">Rp. {{number_format($total_pkp, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>17.</td>
                  <td>PPh PASAL 21 ATAS PENGHASILAN KENA PAJAK SETAHUN/DISETAHUNKAN</td>
                  <td class="text-right">Rp. {{number_format($total_pph21_year, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>18.</td>
                  <td>PPh PASAL 21 YANG TELAH DIPOTONG MASA SEBELUMNYA</td>
                  <td class="text-right">Rp. {{number_format($prev_cp_pph21_total, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>19.</td>
                  <td>PPh PASAL 21 TERUTANG</td>
                  <td class="text-right">Rp. {{number_format($total_pph21, 0, ',', '.')}}</td>
                </tr>
                <tr>
                  <td>20.</td>
                  <td>PPh PASAL 21 DAN PPh PASAL 26 YANG TELAH DIPOTONG DAN DILUNAS</td>
                  <td class="text-right">Rp. {{number_format($total_pph21, 0, ',', '.')}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- identitas pemotong -->
      <div class="identitaspemotong">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="fs-16">C. IDENTITAS PEMOTONG</h3>
          </div>
        </div>
        <div class="boxidentitaspemotong">
          <div class="row">
            <div class="col-sm-6">
              <div class="row mb-3">
                <div class="col-sm-3">
                  1. NPWP
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">C.01</span> 
                  <span style="width: 150px; border-bottom: 2px solid; display: inline-block;">{{$stpajakpph21_npwp1}}</span>
                  <span>-</span>
                  <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{{$stpajakpph21_npwp2}}</span>
                  <span>.</span>
                  <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{{$stpajakpph21_npwp3}}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-3">
                  2. NAMA
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-9 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">C.02</span> 
                  <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">{{$stpajakpph21_name}}</span>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="row mb-3">
                <div class="col-sm-12">
                  3. TANGGAL & TANDA TANGAN
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;margin-left:-36px;">C.03</span> 
                  <span style="width: 36px; border-bottom: 2px solid; display: inline-block;">{{date('d', strtotime($periodepotong))}}</span>
                  <span>-</span>
                  <span style="width: 36px; border-bottom: 2px solid; display: inline-block;">{{date('m', strtotime($periodepotong))}}</span>
                  <span>-</span>
                  <span style="width: 100px; border-bottom: 2px solid; display: inline-block;">{{date('Y', strtotime($periodepotong))}}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  [dd - mm - yyyy]
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="row">
                <div class="col-sm-12">
                  <span style="width: 100%; height: 90px; border: 2px solid; display: table;">
                    <span style="display: table-cell; vertical-align: middle; text-align: center;">
                      @if($stpajakpph21 && $stpajakpph21->stpajakpph21_ttd)
                      <img src="data:image/png;base64,{{$stpajakpph21->stpajakpph21_ttd_base64}}" id="ttd" alt="" class="img-fluid" width="150" />
                      @endif
                    </span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php $i++;
  endforeach; ?>
  <div id="blank" style="position: fixed; background-color: #fff; top: 0; left: 0; right: 0; bottom: 0;">
    <div class="loader-table">
      <div class="loader-table-cell">
        <div class="loader"></div>
      </div>
    </div>
  </div>
  <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/html2canvas/html2canvas.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jsPDF-master/dist/jspdf.umd.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jszip-v3.10.1-1/dist/jszip.js')}}"></script>
  <script src="{{asset('assets/vendor/js/FileSaver.js')}}"></script>

  <script>
    window.jsPDF = window.jspdf.jsPDF;
    
    let maxData = "<?php echo count($karyawans) ?>";

    let zip = new JSZip();
    const docs = [];
    const createPDF = (index) => {
      let elementHTML = document.querySelector("#pdf" + index);
      let kyname = $("#pdf" + index).attr('data-kyname');
      
      if (elementHTML) {
        docs.push(new jsPDF({
          orientation: 'p',
          unit: 'px',
          format: 'a5',
          putOnlyUsedFonts:true
        }));
        docs[index].html(elementHTML, {
          callback: function(pdf) {
            // let payroll = payrolls[index];
            // console.log('payroll', payroll);
            // return false;
            // pdf.save(index + ".pdf");
            let title = kyname + '- Bukti Potong ' + '<?php echo $filteryear ?>';
            // console.log('title', title)
            zip.file(title + ".pdf", pdf.output("blob"));

            // console.log('index', index)
            // console.log('maxData', maxData)
            if (index < maxData) {

              // console.log('indexafter', index)
              createPDF(index + 1);
            }
            let nextidx = index + 1;
            let nextElementHTML = document.querySelector("#pdf" + nextidx);
            if (nextElementHTML == undefined) {
              zip.generateAsync({
                  type: "blob"
                })
                .then(function(content) {
                  // see FileSaver.js
                  saveAs(content, "buktipotong_karyawan_" + '<?php echo $filteryear ?>' + ".zip");
                  // close window
                  setTimeout(function () {
                      window.close();
                  }, 1000)
                });
            }
          },
          margin: [5, 5, 5, 5],
          autoPaging: 'text',
          x: 0,
          y: 0,
          width: 140, //target width in the PDF document
          windowWidth: 575 //window width in CSS pixels
        });
      }
    }
    createPDF(0);
  </script>
</body>

</html>