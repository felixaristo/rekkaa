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
      <th align="center">Kode Cuti</th>
      <th align="center">Deskripsi Cuti</th>
      <th align="center">Tanggal Mulai Berlaku</th>
      <th align="center">Tanggal Berakhir Berlaku</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($setting as $att)
        <tr>
          <td align="center">{{$att['leave_id']}}</td>
          <td align="center">{{$att['leave_description']}}</td>
          <td align="center">{{$att['leave_active_start_date']}}</td>
          <td align="center">{{$att['leave_active_end_date']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
