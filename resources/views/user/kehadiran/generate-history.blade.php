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
      <th align="center">Tanggal</th>
      <th align="center">Jam Masuk</th>
      <th align="center">Jam Mulai Istirahat</th>
      <th align="center">Jam Selesai Istirahat</th>
      <th align="center">Jam Keluar</th>
      <th align="center">Catatan Telat Masuk</th>
      <th align="center">Catatan Keluar Lebih Dulu</th>
      <th align="center">Catatan Jam Mulai Istirahat Lebih / Kurang</th>
      <th align="center">Catatan Jam Selesai Istirahat Lebih Lama</th>
      <th align="center">Catatan Tidak Dikantor</th>
      <th align="center">Status</th>
      <th align="center">Remark</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($listabsen as $att)
        <tr>
          <td align="center">{{$att['karyawan_id']}}</td>
          <td align="center">{{$att['attendancekaryawan_date']}}</td>
          <td align="center">{{$att['attendancekaryawan_check_in']}}</td>
          <td align="center">{{$att['attendancekaryawan_break_start']}}</td>
          <td align="center">{{$att['attendancekaryawan_break_end']}}</td>
          <td align="center">{{$att['attendancekaryawan_check_out']}}</td>
          <td align="center">{{$att['attendancekaryawan_check_in_late_note']}}</td>
          <td align="center">{{$att['attendancekaryawan_check_out_note']}}</td>
          <td align="center">{{$att['attendancekaryawan_break_start_note']}}</td>
          <td align="center">{{$att['attendancekaryawan_break_end_note']}}</td>
          <td align="center">{{$att['attendancekaryawan_check_in_note']}}</td>
          <td align="center">{{$att['status']}}</td>
          <td align="center">{{$att['remark']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
