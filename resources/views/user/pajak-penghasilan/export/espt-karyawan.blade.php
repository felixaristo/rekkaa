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
      <?php foreach($data as $dt): 
        $kode_pajak = '21-100-01';
        $month = date('m', strtotime($dt->payroll_period));
        $year = date('Y', strtotime($dt->payroll_period));
        $pembetulan = request()->get('pembetulan');
        $pph21 = $dt->pph21;
      ?>
        <tr>
          <td align="center">{{$month}}</td>
          <td align="center">{{$year}}</td>
          <td align="center">{{$pembetulan}}</td>
          <td align="center">{{$dt->payroll_karyawan_npwp}}</td>
          <td>{{$dt->payroll_karyawan_name}}</td>
          <td align="center">{{$kode_pajak}}</td>
          <td align="right">{{$pph21->pph21_bruto_month}}</td>
          <td align="right">{{$pph21->pph21_total_month}}</td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>