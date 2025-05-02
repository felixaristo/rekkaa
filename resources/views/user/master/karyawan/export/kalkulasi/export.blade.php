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
      <?php foreach($kalkulasi as $kal): 
        $kode_pajak = '21-100-01';
        if($kal->karyawankalkulasi_status == 'NONKARYAWAN') {
          $kode_pajak = $kal->objekpajak_code;
        }
      ?>
        <tr>
          <td align="center">{{$kal->karyawankalkulasi_month}}</td>
          <td align="center">{{$kal->karyawankalkulasi_year}}</td>
          <td align="center">{{$pembetulan}}</td>
          <td align="center">{{$kal->karyawan_npwp}}</td>
          <td>{{$kal->karyawan_name}}</td>
          <td align="center">{{$kode_pajak}}</td>
          <td align="right">{{$kal->karyawankalkulasi_bruto}}</td>
          <td align="right">{{$kal->karyawankalkulasi_pph21}}</td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <script>
    // window.close();
  </script>
</body>
</html>