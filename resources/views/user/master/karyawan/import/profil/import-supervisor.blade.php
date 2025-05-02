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
      <th align="center">Id Karyawan</th>
      <th align="center">Nama Karyawan</th>
      <th align="center">Jabatan</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($supervisor as $att)
        <tr>
          <td style="width:auto;" align="center">{{$att['karyawan_enid']}}</td>
          <td style="width:auto;" align="center">{{$att['karyawan_name']}}</td>
          <td style="width:auto;" align="center">{{$att['jabatan'] ? $att['jabatan']['karyawanjabatan_name'] : null}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
