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

?>

  <table>
    <thead>
      <tr>
        <th colspan="23" align="left"><b>Periode : <?php echo $periodelbl ?></b></th>
      </tr>
      <tr>
        <th colspan="7" align="center">Profil Karyawan</th>
        <th colspan="4" align="center">Perhitungan Pajak PPh 21</th>
      </tr>
    <tr>
      <th align="center">Nama</th>
      <th align="center">NPWP</th>
      <th align="center">NIK</th>
      <th align="center">Alamat</th>
      <th align="center">Sumber Penghasilan</th>
      <th align="center">PTKP</th>
      <th align="center">Methode PPh 21</th>
      <th align="center">Pendapatan Kotor</th>
      <th align="center">DPP</th>
      <!-- <th align="center">DPP Kumulatif</th> -->
      <th align="center">Tarif Pajak</th>
      <th align="center">PPh 21</th>
    </tr>
    </thead>
    <tbody>
      <?php 
        $karyawan_ids = [];
        $karyawans = [];
        $pph21_tarif21_rate = '';
        $i=0;
        // dd($data);
        foreach($data as $payroll) {
          $pph21 = $payroll->pph21;
          // dd($payroll->karyawan->objekpajak);
          // if(!in_array($payroll->ms_karyawan_id, $karyawan_ids)) {
            $payroll_period = $payroll->payroll_period;
            $karyawan_name = $payroll->payroll_karyawan_name;
            $karyawan_npwp = $payroll->payroll_karyawan_npwp;
            $karyawan_nik = $payroll->payroll_karyawan_nik;
            $karyawan_address = $payroll->karyawan->karyawan_address;
            $karyawan_code_objekpajak = $payroll->karyawan->karyawan_code_objekpajak;
            $karyawan_ismultiple = $payroll->karyawan->karyawan_ismultiple;
            $pph21_ptkp_description = $pph21->pph21_ptkp_description;
            $pph21_tarif21_rate = $pph21->pph21_tarif21_rate.' %';
            $pph21_ptkpdet_rate_percentage = $pph21->pph21_ptkpdet_rate_percentage.' %';
        
            $payroll_period = Carbon\Carbon::parse($payroll_period)->translatedFormat('F - Y');
        
            $pph21_bruto_month = 0;
            $pph21_total_month = 0;
            $pph21_dpp = 0;
            $pph21_dpp_kumulatif = 0;
            
            $pph21_bruto_month += $pph21->pph21_bruto_month;
            $pph21_total_month += $pph21->pph21_total_month;
            $pph21_dpp += $pph21->pph21_dpp;
        
            // array_push($karyawan_ids, $payroll->ms_karyawan_id);
            $karyawans[] = [
              'karyawan_name' => $karyawan_name,
              'karyawan_npwp' => $karyawan_npwp,
              'karyawan_nik' => $karyawan_nik,
              'karyawan_address' => $karyawan_address,
              'objekpajak_code' => $karyawan_code_objekpajak,
              'payroll_period' => $payroll_period,
              'payroll_method' => $payroll->payroll_method,
              'pph21_ptkp_description' => $pph21_ptkp_description,
              'pph21_bruto_month' => $pph21_bruto_month,
              'pph21_total_month' => $pph21_total_month,
              'pph21_dpp_kumulatif' => $pph21_dpp_kumulatif,
              'pph21_dpp' => $pph21_dpp,
              'pph21_ptkpdet_rate_percentage' => $pph21_ptkpdet_rate_percentage,
              'karyawan_ismultiple' => ($karyawan_ismultiple == 0) ? 'Satu Pemberi Kerja' : 'Beberapa Pemberi Kerja',
            ];
        //   } else {
        //     $karyawans[$payroll->ms_karyawan_id]['pph21_bruto_month'] += $pph21->pph21_bruto_month;
        //     $karyawans[$payroll->ms_karyawan_id]['pph21_total_month'] += $pph21->pph21_total_month;
        //     $karyawans[$payroll->ms_karyawan_id]['pph21_dpp'] += $pph21->pph21_dpp;
        //     $karyawans[$payroll->ms_karyawan_id]['pph21_dpp_kumulatif'] += $pph21->pph21_dpp_kumulatif;

        //     $decode_tarif21 = json_decode($pph21->pph21_tarif21_data);
        //     // dd($pph21);
        //     $tarif21lbl = '';
        //     $idx = 0;
        //     if($decode_tarif21) {
        //       foreach($decode_tarif21 as $tf) {
        //           $tarif21lbl .= $tf->tarif21_rate.' %';
        //           if($idx < count($decode_tarif21) - 1) {
        //             $tarif21lbl .= ', ';
        //           }
        //         $idx++;
        //       }
        //     }
        //     $karyawans[$payroll->ms_karyawan_id]['pph21_tarif21_rate'] = $tarif21lbl;
        //   }
        //   $i++;
        }
        foreach($karyawans as $dt): 
        // $pph21 = $dt->pph21;
        // $total_pengurang = $pph21->pph21_deduction_position + $pph21->pph21_deduction_jht + $pph21->pph21_deduction_jp + $pph21->pph21_deduction_other;
        // $ptkp = json_decode($pph21->pph21_ptkp_data);
      ?>
        <tr>
          <td align="left" width="30">{{$dt['karyawan_name']}}</td>
          <td align="center" width="20">{{$dt['karyawan_npwp']}}</td>
          <td align="center" width="20">{{$dt['karyawan_nik']}}</td>
          <td align="center">{{$dt['karyawan_address']}}</td>
          <td align="center">{{$dt['karyawan_ismultiple']}}</td>
          <td align="center">{{$dt['pph21_ptkp_description']}}</td>
          <td align="center">{{str_replace('_', ' ', $dt['payroll_method'])}}</td>
          <td align="right">{{$dt['pph21_bruto_month']}}</td>
          <td align="right">{{$dt['pph21_dpp']}}</td>
          <!-- <td align="right">{{$dt['pph21_dpp_kumulatif']}}</td> -->
          <td align="right">{{$dt['pph21_ptkpdet_rate_percentage']}}</td>
          <td align="right">{{$dt['pph21_total_month']}}</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>