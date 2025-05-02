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
    </tr>
    </thead>
    <tbody>
      @foreach ($kehadiran as $att)
        <tr>
          <td align="center">{{$att['karyawan_id']}}</td>
          <td align="center">{{$att['karyawan_name']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
