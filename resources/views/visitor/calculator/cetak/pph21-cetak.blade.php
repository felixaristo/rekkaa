<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="{{url('assets/img/logo/logo.png')}}" />
  <link href="{{url('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <title>{{$data['title']}}</title>
</head>
<body>
<?php

  $perhitungan = perhitunganTotalPPH21($data['bpjsrate'], $data['ptkp'], $data['penghasilan_gaji_pokok'], $data['penghasilan_gaji_pokok'], 
		[
			'tunjangan_jabatan' => $data['tunjangan_jabatan'],
			'tunjangan_nominal' => $data['penghasilan_tunjangan_lainnya'],
		], $data['metode'], $data['kepemilikannpwp']->kepemilikannpwp_code, $data['jkkrate'], $data['isbpjs'], $data['ptkpdetratepercentage'], $data['menggunakan_ter']
	);
  // dd($perhitungan);
  // $rate_nonnpwp = 100;
  // if($data['kepemilikannpwp']->kepemilikannpwp_code == 'NO-NPWP') {
  //   $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
  //   $rate_nonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
  // }
  $pph21perbulan11 = $perhitungan['total_pph_terutang_perbulan'];
  $bruto_ter11 = $perhitungan['total_bruto_perbulan'];
  if($data['metode'] != 'GROSS') {
    $bruto_ter11 += $pph21perbulan11;
  }
  $totalPPhTerutangDesember = ($data['menggunakan_ter']) ? $perhitungan['total_pph_terutang_perbulan_desember'] : $perhitungan['total_pph_terutang_perbulan'];
  // dd($pph21perbulan11);
  ?>
<div class="container">
  <table class="table table-header">
    <thead>
      <tr>
        <td class="table-logo" width="150">
          <img
            src="{{url('assets/img/logo/logo.png')}}"
            alt=""
            class="img-fluid"
            width="150"
          />
        </td>
        <td class="text-center">
          <h3>PT Ruhika Fusta Nusantara</h3>
          <p class="subtitle">{{$data['address']->setting_value}}</p>
        </td>
      </tr>
    </thead>
  </table>
  <!-- <hr style="border-top: 1px solid #eee!important"> -->
  <table class="table">
    <thead>
      <tr>
        <th class="text-center" colspan="3"><u>{{$data['title']}}</u></th>
      </tr>
    </thead>
  </table>
  <table class="table">
    <tbody>
      <tr>
        <td width="200">
          Kepemilikan NPWP
        </td>
        <td width="10">:</td>
        <td>{{$data['kepemilikannpwp']->kepemilikannpwp_name}}</td>
      </tr>
      <tr>
        <td>
          Metode Perhitungan
        </td>
        <td>:</td>
        <td>{{($data['metode'] == 'GROSS_UP') ? 'GROSS UP' : $data['metode']}}</td>
      </tr>
      <tr>
        <td>
          menggunakan BPJS
        </td>
        <td>:</td>
        <td>{{($data['isbpjs'] == '1') ? 'Ya' : 'Tidak'}}</td>
      </tr>
      <tr>
        <td>
          Status Kawin
        </td>
        <td>:</td>
        <td>{{$data['ptkp']->ptkp_description}}</td>
      </tr>
    </tbody>
  </table>
  <table class="table">
    <tbody>
      <tr>
        <th colspan="3" class="border-bottom">
          Penghasilan
        </th>
      </tr>
      <tr>
        <td width="350">
          Gaji Pokok
        </td>
        <td width="10">:</td>
        <td class="text-right">Rp. {{number_format($data['penghasilan_gaji_pokok'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Tunjangan Lainnya, Uang Lembur, dan sebagainya
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($data['penghasilan_tunjangan_lainnya'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Jaminan Kesehatan ({{$perhitungan['penghasilan_jamkes_rate']}} %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['penghasilan_jamkes'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Jaminan Kecelakaan Kerja ({{$perhitungan['penghasilan_jkk_rate']}} %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['penghasilan_jkk'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Jaminan Kematian ({{$perhitungan['penghasilan_jkm_rate']}} %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['penghasilan_jkm'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Jaminan Hari Tua (3.7 %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['penghasilan_jht'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Jaminan Pensiun (2 %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['penghasilan_jp'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Penghasilan Bruto per Bulan
        </td>
        <td>:</td>
        <td class="text-right">
        Rp. {{number_format(
          $perhitungan['total_bruto_perbulan']
        , 0, ',', '.')}}</td>
      </tr>
    </tbody>
  </table>
  <table class="table">
    <tbody>
    <tr>
        <th colspan="3" class="border-bottom">
          Pengurang
        </th>
      </tr>
      <tr>
        <td width="350">
        Biaya Jabatan
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['total_biaya_jabatan'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Jaminan Kesehatan (1 %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['biaya_jamkes'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Jaminan Hari Tua ({{$perhitungan['biaya_jht_rate']}} %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['biaya_jht'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Jaminan Pensiun ({{$perhitungan['biaya_jp_rate']}} %)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['biaya_jp'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Penghasilan Neto per Bulan
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['total_neto_perbulan'], 0, ',', '.')}}</td>
      </tr>
    </tbody>
  </table>
  <table class="table">
    <tbody>
    <tr>
        <th colspan="3" class="border-bottom">
          Perhitungan PPH 21
        </th>
      </tr>
      @if($data['menggunakan_ter'])
      <tr>
        <th colspan="3" style="padding-top:15px">
          PPh Terutang Tarif Efektif Rata-rata (TER) Masa Januari - November
        </th>
      </tr>
      <tr>
        <td width="350">
        Bruto Perbulan
        </td>
        <td>:</td>
        <td class="text-right">{{number_format($bruto_ter11, 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td width="350">
        Kategori TER
        </td>
        <td>:</td>
        <td class="text-right">{{$data['ptkp']->ptkp_category}}</td>
      </tr>
      <tr>
        <td width="350">
        Tarif TER
        </td>
        <td>:</td>
        <td class="text-right">{{$data['ptkpdetratepercentage']}} %</td>
      </tr>
      <tr>
        <td width="350">
        PPh 21 Terutang
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($pph21perbulan11, 0, ',', '.')}}</td>
      </tr>
      <tr>
        <th colspan="3" style="padding-top:15px">
          <b>PPh Terutang Masa Pajak Desember</b>
        </td>
      </tr>
      @endif
      <tr>
        <td width="350">
        Penghasilan Neto per Tahun
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['total_neto_pertahun'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Penghasilan Tidak Kena Pajak (PTKP)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['total_ptkp'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
        Penghasilan Kena Pajak (PKP)
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($perhitungan['total_pkp'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td class="text-bold">
        PPh Terutang Setahun
        </td>
        <td>:</td>
        <td class="text-right text-bold">Rp. {{number_format($perhitungan['total_pph_terutang_setahun'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td class="text-bold">
        PPh Terutang {{($data['menggunakan_ter']) ? '' : 'Perbulan'}}
        </td>
        <td>:</td>
        <td class="text-right text-bold">Rp. {{number_format($totalPPhTerutangDesember, 0, ',', '.')}}</td>
      </tr>
    </tbody>
  </table>
  <htmlpagefooter name="myFooter">
    <!-- <table class="table table-footer">
      <thead>
        <tr>
          <td class="text-right">
            <p>Copyright&copy; Rekkaa. All Right Reserved.</p>
          </td>
        </tr>
      </thead>
    </table> -->
    </htmlpagefooter>
</div>
</body>
</html>