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
    .mb-50 {
      margin-bottom: 50px;
    }
    .mb-60 {
      margin-bottom: 60px;
    }
    .pb-14 {
      padding-bottom: 14px;
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
      width: 98%;
      /* border-bottom: 3px solid #000; */
      /* border-right: 3px solid #000; */
    }
    .boxnomor span, .boxmasa span, .boxspan span {
      padding: 1px 5px;
    }
    .pemotong {
      border-bottom: 3px solid #000;
      border-right: 3px solid #000;
      border-left: 3px solid #000;
      padding: 10px 15px;
    }
    .boxidentitas, .boxidentitaspemotong {
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
    .noborder-right {
      border-right: none!important;
    }
    .noborder-left {
      border-left: none!important;
    }
    .noborder-top {
      border-top: none!important;
    }
    .noborder-bottom {
      border-bottom: none!important;
    }
  </style>
  <?php 
  use Carbon\Carbon;

  $i = 0;
  $periode = (request()->get('periode')) ? request()->get('periode') : date('m-Y');
  $filtermonth = Carbon::createFromFormat('!m-Y', $periode)->format('m-Y');

  foreach($data as $dt) : 
    // stpajakpph21
    $stpajakpph21 = json_decode($dt->pph21->pph21_pajakpph21_data);
    $stpajakpph21_name = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_name : '&nbsp;';
    $stpajakpph21_npwp = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_npwp : [];
    $stpajakpph21_npwpseparate = ($stpajakpph21_npwp) ? explode('-', $stpajakpph21_npwp) : [];
    $stpajakpph21_npwpseparatelast = (count($stpajakpph21_npwpseparate) > 0) ? explode('.', $stpajakpph21_npwpseparate[1]) : [];
    $stpajakpph21_npwp1 = (count($stpajakpph21_npwpseparate) > 1) ? $stpajakpph21_npwpseparate[0] : '';
    $stpajakpph21_npwp2 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[0] : '';
    $stpajakpph21_npwp3 = (count($stpajakpph21_npwpseparatelast) > 1) ? $stpajakpph21_npwpseparatelast[1] : '';
    
    $country_numcode = ($dt->karyawan->country) ? $dt->karyawan->country->country_numcode : '&nbsp;';
    $npwp1 = '';
    $npwp2 = '';
    $npwp3 = '';
    if($dt->payroll_karyawan_npwp) {
      $npwpseparate = explode('-', $dt->payroll_karyawan_npwp);
      $npwpseparatelast = explode('.', $npwpseparate[1]);
      $npwp1 = $npwpseparate[0];
      $npwp2 = $npwpseparatelast[0];
      $npwp3 = (count($npwpseparatelast) > 1) ? $npwpseparatelast[1] : '';
    }

    $karyawan_address = $dt->payroll_karyawan_address;
    $split_address = [];
    if( strlen( $karyawan_address) > 50) {
      $split_address = explode( "\n", wordwrap( $karyawan_address, 120));
    } else {
      $split_address[] = $karyawan_address;
    }
    // dd($dt->pph21);
  ?>
    <div class="container pdf" id="pdf{{$i}}" data-kyname="{{$dt->payroll_karyawan_name.'_'.$dt->payroll_uuid}}">
      <p style="font-size: 13px; color: rgb(166, 166, 166); letter-spacing: 2px; margin-bottom: 0px">areastaples</p>
      <hr>
      <div class="flex">
        <span class="black-box"></span>
        <span class="black-box"></span>
      </div>
      <div class="header">
        <div class="row">
          <div class="col-sm-3 text-center boxlogo pb-14">
            <img src="{{asset('assets/img/logo/kemenkeu-logo.png')}}" alt="" class="img-fluid" width="120" />
            <h3 class="fs-12 text-bold">
              KEMENTERIAN KEUANGAN RI
              <br>
              DIREKTORAT JENDERAL PAJAK
            </h3>
          </div>
          <div class="col-sm-9 text-center">
            <div class="row">
              <div class="col-sm-8 boxtoptitle">
                <h3 class="fs-18 text-bold mb-50">
                  BUKTI PEMOTONGAN PAJAK<br>
                  PENGHASILAN PASAL 21 (TIDAK FINAL)<br>
                  ATAU PASAL 26
                </h3>
              </div>
              <div class="col-sm-4 text-right">
                <div>
                  <span class="white-box-inline"></span> <span class="black-box-inline"></span> <span class="white-box-inline"></span> <span class="black-box-inline"></span>
                </div>
                <h3 class="fs-12 text-bold">
                  FORMULIR 1721 - VI
                </h3>
                <div class="text-left">
                  <p class="fs-11">
                    Lembar ke-1 : untuk Penerima Penghasilan
                    <br>
                    Lembar ke-2 : untuk Pemotong
                  </p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 text-left boxnomor">
              NOMOR : <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">H.01</span> <span>1</span><span>.</span><span>1</span><span>-</span><span class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{\Carbon\Carbon::parse($dt->payroll_period)->format('m')}}</span><span>.</span><span class="text-center" style="width: 50px; border-bottom: 2px solid; display: inline-block;">{{\Carbon\Carbon::parse($dt->payroll_period)->format('Y')}}</span><span>-</span><span class="text-center" style="width: 140px; border-bottom: 2px solid; display: inline-block;">{{$dt->pph21->pph21_nobuktipotong}}</span>
              </div>
            </div>
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
            <div class="col-sm-12">
              <div class="row">
                <div class="col-sm-7">
                  <div class="row">
                    <div class="col-sm-3">
                      1. NPWP
                      <span class="pull-right">:</span>
                    </div>
                    <div class="col-sm-9 boxspan">
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.01</span> 
                      <span style="width: 150px; border-bottom: 2px solid; display: inline-block;">{{$npwp1}}</span>
                      <span>-</span>
                      <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{{$npwp2}}</span>
                      <span>.</span>
                      <span style="width: 66px; border-bottom: 2px solid; display: inline-block;">{{$npwp3}}</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-5">
                  <div class="row mb-3">
                    <div class="col-sm-5">
                      2.
                        NIK/NO PASSPOR
                      <span class="pull-right">:</span>
                    </div>
                    <div class="col-sm-7 boxspan">
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.02</span> 
                      <span style="width: 150px; border-bottom: 2px solid; display: inline-block;">{{$dt->payroll_karyawan_nik}}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-sm-2" style="width: 14.5%;">
                  3. NAMA
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-10 boxspan">
                  <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.03</span> 
                  <span style="width: 92%; border-bottom: 2px solid; display: inline-block;">{{$dt->payroll_karyawan_name}}</span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-2" style="width: 14.5%;">
                  4. ALAMAT
                  <span class="pull-right">:</span>
                </div>
                <div class="col-sm-10 boxspan">
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
                <div class="col-sm-7">
                  <div class="row">
                    <div class="col-sm-5">
                      5. WAJIB PAJAK LUAR NEGERI
                      <span class="pull-right">:</span>
                    </div>
                    <div class="col-sm-7 boxspan">
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.05</span> 
                      <span style="width: 35px; height: 25px; border: 2px solid; display: inline-block;">{!! ($dt->karyawan->karyawan_citizenship == 'WNA') ? 'X' : '&nbsp;' !!}</span> &nbsp;YA
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  <div class="row">
                    <div class="col-sm-6">
                      6. KODE NEGARA DOMISILI
                      <span class="pull-right">:</span>
                    </div>
                    <div class="col-sm-6 boxspan">
                      <span style="font-size: 10px;color: rgb(166, 166, 166);letter-spacing: 2px;">A.06</span> 
                      <span style="width: 110px; border-bottom: 2px solid; display: inline-block;">{!! ($dt->karyawan->karyawan_citizenship == 'WNA') ? $country_numcode : '&nbsp;' !!}</span>
                    </div>
                  </div>
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
            <h3 class="fs-16">B. PPh PASAL 21 DAN/ATAU PASAL 26 YANG DIPOTONG</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center" style="width: 250px; vertical-align: middle;">KODE OBJEK PAJAK</th>
                  <th class="text-center" style="width: 200px; vertical-align: middle;">JUMLAH PENGHASILAN BRUTO<br>(Rp)</th>
                  <th class="text-center" style="width: 250px; vertical-align: middle;">DASAR PENGENAAN PAJAK<br>(Rp)</th>
                  <th class="text-center" style="width: 150px; vertical-align: middle;">TARIF LEBIH TINGGI 20%<br>(TIDAK BER-NPWP)</th>
                  <th class="text-center" style="width: 100px; vertical-align: middle;">TARIF<br>(%)</th>
                  <th class="text-center" style="width: 200px; vertical-align: middle;">PPh DIPOTONG<br>(Rp)</th>
                </tr>
                <tr>
                  <th class="text-center blur">(1)</th>
                  <th class="text-center blur">(2)</th>
                  <th class="text-center blur">(3)</th>
                  <th class="text-center blur">(4)</th>
                  <th class="text-center blur">(5)</th>
                  <th class="text-center blur">(6)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center">{{$dt->pph21->pph21_objekpajak_code}}</td>
                  <td class="text-right">Rp. {{number_format($dt->payroll_total_income, 0, ',', '.')}}</td>
                  <td class="text-right">Rp. {{number_format($dt->pph21->pph21_dpp, 0, ',', '.')}}</td>
                  <td class="text-center">{{-- !$dt->payroll_karyawan_npwp || $dt->payroll_karyawan_npwp == '00.000.000.0-000.000' ? 'X' : '' --}}</td>
                  <td class="text-center">{{$dt->pph21->pph21_ptkpdet_rate_percentage}}</td>
                  <td class="text-right">Rp. {{number_format($dt->pph21->pph21_total_month, 0, ',', '.')}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- identitas pemotong -->
      <div style="margin-bottom: 30rem;">
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
                  <span style="width: 85%; border-bottom: 2px solid; display: inline-block;">{!!$stpajakpph21_name!!}</span>
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
                  <span style="width: 36px; border-bottom: 2px solid; display: inline-block;">{{date('d', strtotime($dt->payroll_trx_at))}}</span>
                  <span>-</span>
                  <span style="width: 36px; border-bottom: 2px solid; display: inline-block;">{{date('m', strtotime($dt->payroll_trx_at))}}</span>
                  <span>-</span>
                  <span style="width: 100px; border-bottom: 2px solid; display: inline-block;">{{date('Y', strtotime($dt->payroll_trx_at))}}</span>
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
        <div class="boxinfo" style="margin-top: 30px;">
          <div class="row">
            <div class="col-sm-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center blur" colspan="3">KODE OBJEK PAJAK PENGHASILAN PASAL 21 (TIDAK FINAL) ATAU PASAL 26</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th class="noborder-right noborder-bottom"></th>
                    <th class="noborder-left noborder-bottom" colspan="2">PPh PASAL 21 TIDAK FINAL</th>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">1.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-03</td>
                    <td class="noborder-left noborder-bottom noborder-top">Upah Pegawai Tidak Tetap atau Tenaga Kerja Lepas</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">2.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-04</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Distributor Multi Level Marketing (MLM)</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">3.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-05</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Petugas Dinas Luar Asuransi</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">4.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-06</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Penjaja Barang Dagangan</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">5.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-07</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Tenaga Ahli</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">6.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-08</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Bukan Pegawai yang Menerima Penghasilan yang Bersifat Berkesinambungan</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">7.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-09</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Bukan Pegawai yang Menerima Pengasilan yang Tidak Bersifat Berkesinambungan</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">8.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-10</td>
                    <td class="noborder-left noborder-bottom noborder-top">Honorarium atau Imbalan Kepada Anggota Dewan Komisaris atau Dewan Pengawas yang tidak Merangkap sebagai Pegawai Tetap</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">9.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-11</td>
                    <td class="noborder-left noborder-bottom noborder-top">Jasa Produksi, Tantiem, Bonus atau imbalan Kepada Mantan Pegawai</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">10.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-12</td>
                    <td class="noborder-left noborder-bottom noborder-top">Penarikan Dana Pensiun oleh Pegawai</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">11.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-13</td>
                    <td class="noborder-left noborder-bottom noborder-top">Imbalan Kepada Peserta Kegiatan</td>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-bottom noborder-top">12.</td>
                    <td class="noborder-right noborder-left noborder-bottom noborder-top">21-100-99</td>
                    <td class="noborder-left noborder-bottom noborder-top">Objek PPh Pasal 21 Tidak Final Lainnya</td>
                  </tr>
                  <tr>
                    <th class="noborder-right noborder-bottom noborder-top"></th>
                    <th class="noborder-left noborder-bottom noborder-top" colspan="2">PPh PASAL 26</th>
                  </tr>
                  <tr>
                    <td class="noborder-right noborder-top">1.</td>
                    <td class="noborder-right noborder-left noborder-top">27-100-99</td>
                    <td class="noborder-left noborder-top">Imbalan Sehubungan dengan jasa, pekerjaan dan kegiatan, hadiah dan penghargaan, pensiun dan pembayaran berkala lainnya yang dipotong PPh Pasal 26</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="flex">
        <span class="black-box"></span>
        <span class="black-box"></span>
      </div>
    </div>
  <?php
  $i++; endforeach;
  ?>
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
    let maxData = "<?php echo count($data) ?>";

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
            // pdf.save(index + ".pdf");
            let title = kyname + '- Bukti Potong ' + '<?php echo $filtermonth ?>';
            zip.file(title + ".pdf", pdf.output("blob"));

            // console.log('index', index)
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
                  saveAs(content, "buktipotong_nonkaryawan_" + '<?php echo $filtermonth ?>' + ".zip");
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