<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$title}}</title>
</head>
<body>
  <table>
    <thead>
    <tr>
      <th align="center">Masa Pajak</th>
      <th align="center">Tahun Pajak</th>
      <th align="center">Pembetulan</th>
      <th align="center">NPWP</th>
      <th align="center">Nama</th>
      <th align="center">Kode Pajak</th>
      <th align="center">Jumlah Bruto</th>
      <th align="center">Jumlah Pph</th>
      <th align="center">Kode Negara</th>
    </tr>
    </thead>
    <tbody>
      <?php 
      $karyawan_ids = [];
      $karyawans = [];
      foreach($data as $payroll) {
        $pph21 = $payroll->pph21;
        // dd($payroll->karyawan->objekpajak);
        if(!in_array($payroll->ms_karyawan_id, $karyawan_ids)) {
          $payroll_period = $payroll->payroll_period;
          $karyawan_name = $payroll->payroll_karyawan_name;
          $karyawan_code_objekpajak = $payroll->karyawan->karyawan_code_objekpajak;
          $karyawan_npwp = $payroll->payroll_karyawan_npwp;
      
          // $payroll_period = Carbon\Carbon::parse($payroll_period)->translatedFormat('F - Y');
      
          $pph21_bruto_month = 0;
          $pph21_total_month = 0;
          
          $pph21_bruto_month += $pph21->pph21_bruto_month;
          $pph21_total_month += $pph21->pph21_total_month;
      
          array_push($karyawan_ids, $payroll->ms_karyawan_id);
          $karyawans[$payroll->ms_karyawan_id] = [
            'karyawan_name' => $karyawan_name,
            'karyawan_npwp' => $karyawan_npwp,
            'objekpajak_code' => $karyawan_code_objekpajak,
            'payroll_period' => $payroll_period,
            'pph21_bruto_month' => $pph21_bruto_month,
            'pph21_total_month' => $pph21_total_month,
          ];
        } else {
          $karyawans[$payroll->ms_karyawan_id]['pph21_bruto_month'] += $pph21->pph21_bruto_month;
          $karyawans[$payroll->ms_karyawan_id]['pph21_total_month'] += $pph21->pph21_total_month;
        }
      }

      // dd($karyawans);
      foreach($karyawans as $dt): 
        $kode_pajak = $dt['objekpajak_code'];
        $month = date('m', strtotime($dt['payroll_period']));
        $year = date('Y', strtotime($dt['payroll_period']));
        $pembetulan = request()->get('pembetulan');
      ?>
        <tr>
          <td align="center">{{$month}}</td>
          <td align="center">{{$year}}</td>
          <td align="center">{{$pembetulan}}</td>
          <td align="center">{{$dt['karyawan_npwp']}}</td>
          <td>{{$dt['karyawan_name']}}</td>
          <td align="center">{{$kode_pajak}}</td>
          <td align="right">{{$dt['pph21_bruto_month']}}</td>
          <td align="right">{{$dt['pph21_total_month']}}</td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>