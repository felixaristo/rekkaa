<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$title}}</title>
</head>
<body>
<?php

use Carbon\Carbon;

  $pph21 = $data[0]->pph21;  
  $allowance_jamkesrate = $pph21->pph21_allowance_jamkes_rate;
  $allowance_jkkrate = $pph21->pph21_allowance_jkk_rate;
  $allowance_jkmrate = $pph21->pph21_allowance_jkm_rate;
  $deduction_jhtrate = $pph21->pph21_deduction_jht_rate;
  $deduction_jprate = $pph21->pph21_deduction_jp_rate;
?>

  <table>
    <thead>
      <tr>
        <th colspan="23" align="left"><b>Periode : <?php echo $periodelbl ?></b></th>
      </tr>
      <tr>
        <th colspan="5" align="center">Profil Karyawan</th>
        <th colspan="7" align="center">Penghasilan</th>
        <th colspan="5" align="center">Pengurang</th>
        <th colspan="6" align="center">Perhitungan Pajak PPh 21</th>
      </tr>
    <tr>
      <th align="center">Nama</th>
      <th align="center">NPWP</th>
      <th align="center">Posisi</th>
      <th align="center">PTKP</th>
      <th align="center">Metode PPh 21</th>
      <th align="center">Gaji Pokok</th>
      <th align="center">Tunjangan PPh 21</th>
      <th align="center">Jamkes {{$allowance_jamkesrate}}%</th>
      <th align="center">JKK {{$allowance_jkkrate}}%</th>
      <th align="center">JKM {{$allowance_jkmrate}}%</th>
      <th align="center">Tunjangan Lainnya</th>
      <th align="center">Total Pendapatan Kotor</th>
      <th align="center">Biaya Jabatan</th>
      <th align="center">JHT {{$deduction_jhtrate}}%</th>
      <th align="center">JP {{$deduction_jprate}}%</th>
      <th align="center">Pengurang Lainnya</th>
      <th align="center">Total Pengurang</th>
      <th align="center">Penghasilan Netto</th>
      <th align="center">Penghasilan Netto Setahun</th>
      <th align="center">PTKP</th>
      <th align="center">Penghasilan Kena Pajak Setahun</th>
      <th align="center">PPh Terutang Setahun</th>
      <th align="center">PPh 21</th>
    </tr>
    </thead>
    <tbody>
      <?php foreach($data as $dt): 
        $pph21 = $dt->pph21;
        $payroll_periodm = Carbon::parse($dt->payroll_period)->format('m');
        $karyawan_contract_end = $dt->karyawan->karyawan_contract_end ? Carbon::parse($dt->karyawan->karyawan_contract_end)->format('m') : null;
        $total_pengurang = $pph21->pph21_deduction_position + $pph21->pph21_deduction_jht + $pph21->pph21_deduction_jp + $pph21->pph21_deduction_other;
        $ptkp = json_decode($pph21->pph21_ptkp_data);
        $isendperiod = false;
        if($karyawan_contract_end && $karyawan_contract_end == $payroll_periodm) {
          $isendperiod = true;
        }
        if($payroll_periodm == 12 ) {
          $isendperiod = true;
        }
      ?>
        <tr>
          <td align="left">{{$dt->payroll_karyawan_name}}</td>
          <td align="center">{{$dt->payroll_karyawan_npwp}}</td>
          <td align="center">{{$dt->payroll_karyawanjabatan_name}}</td>
          <td align="center">{{$ptkp->ptkp_description}}</td>
          <td align="center">{{str_replace('_', ' ', $dt->payroll_method)}}</td>
          <td align="right">{{$pph21->pph21_prorate_salary}}</td>
          <td align="right">{{$dt->payroll_allowance_pph21}}</td>
          <td align="right">{{$pph21->pph21_allowance_jamkes}}</td>
          <td align="right">{{$pph21->pph21_allowance_jk}}</td>
          <td align="right">{{$pph21->pph21_allowance_jkm}}</td>
          <td align="right">{{$pph21->pph21_allowance_other}}</td>
          <td align="right">{{$pph21->pph21_bruto_month}}</td>
          <td align="right">{{$pph21->pph21_deduction_position}}</td>
          <td align="right">{{$pph21->pph21_deduction_jht}}</td>
          <td align="right">{{$pph21->pph21_deduction_jp}}</td>
          <td align="right">{{$pph21->pph21_deduction_other}}</td>
          <td align="right">{{$total_pengurang}}</td>
          <td align="right">{{$pph21->pph21_netto_month}}</td>
          <td align="right">{{$pph21->pph21_netto_year}}</td>
          <td align="right">{{$pph21->pph21_ptkp}}</td>
          <td align="right">{{$pph21->pph21_pkp}}</td>
          <td align="right">{{$pph21->pph21_total_year}}</td>
          <td align="right">{{$pph21->pph21_total_month}}</td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>