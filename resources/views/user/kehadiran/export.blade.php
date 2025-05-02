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
        <th align="left" colspan="16">Periode: {{$periode}}</th>
    </tr>
    <tr>
        <th align="left" colspan="16">Karyawan: {{$listKaryawan}}</th>
    </tr>
    <tr>
        <th align="left" colspan="16"></th>
    </tr>
    <tr>
      <th align="center">Nama Karyawan</th>
      <th align="center">Tanggal</th>
      <th align="center">Alasan Tidak Dikantor</th>
      <th align="center">Waktu Masuk</th>
      <th align="center">Keterlambatan</th>
      <th align="center">Alasan Keterlambatan</th>
      <th align="center">Waktu Mulai Istirahat</th>
      <th align="center">Durasi Istirahat Dini</th>
      <th align="center">Durasi Keterlambatan Istirahat</th>
      <th align="center">Alasan Perubahan Mulai Istirahat</th>
      <th align="center">Waktu Selesai Istirahat</th>
      <th align="center">Durasi Istirahat Tambahan</th>
      <th align="center">Alasan Istirahat Tambahan</th>
      <th align="center">Waktu Keluar</th>
      <th align="center">Durasi Keluar Dini</th>
      <th align="center">Alasan Keluar Dini</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($kehadiran as $att)
        <tr>
          <td align="center">{{$att['karyawan_name']}}</td>
          <td align="center">{{substr($att['attendancekaryawan_check_in'], 0, 10)}}</td>
          <td align="center">{{$att['attendancekaryawan_check_in_note']}}</td>
          <td align="center">
            <?php
            $splitTime = explode(' ', $att['attendancekaryawan_check_in']);

            if(count($splitTime) > 1) {
              $formattedCheckIn = date('H:i', strtotime(explode(' ', $att['attendancekaryawan_check_in'])[1]));
              
              echo $formattedCheckIn;
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">
            <?php
            if($att['attendancekaryawan_check_in_late'] !== null) {
              echo $att['attendancekaryawan_check_in_late'];
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">{{$att['attendancekaryawan_check_in_late_note']}}</td>
          <td align="center">
            <?php
            $splitTime = explode(' ', $att['attendancekaryawan_break_start']);

            if(count($splitTime) > 1) {
              $formattedBreakStart = date('H:i', strtotime(explode(' ', $att['attendancekaryawan_break_start'])[1]));
              
              echo $formattedBreakStart;
            } else {
              echo '';
            }
            ?>
          </td>
          <td align="center">
            <?php
            if($att['attendancekaryawan_break_start_early'] !== null) {
              echo $att['attendancekaryawan_break_start_early'];
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">
            <?php
            if($att['attendancekaryawan_break_start_late'] !== null) {
              echo $att['attendancekaryawan_break_start_late'];
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">{{$att['attendancekaryawan_break_start_note']}}</td>
          <td align="center">
            <?php
            $splitTime = explode(' ', $att['attendancekaryawan_break_end']);

            if(count($splitTime) > 1) {
              $formattedBreakEnd = date('H:i', strtotime(explode(' ', $att['attendancekaryawan_break_end'])[1]));
              
              echo $formattedBreakEnd;
            } else {
              echo '';
            }
            ?>
          </td>
          <td align="center">
            <?php
            if($att['attendancekaryawan_break_end_late'] !== null) {
              echo $att['attendancekaryawan_break_end_late'];
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">{{$att['attendancekaryawan_break_end_note']}}</td>
          <td align="center">
            <?php
            $splitTime = explode(' ', $att['attendancekaryawan_check_out']);

            if(count($splitTime) > 1) {
              $formattedCheckOut = date('H:i', strtotime(explode(' ', $att['attendancekaryawan_check_out'])[1]));
              
              echo $formattedCheckOut;
            } else {
              echo '';
            }
            ?>
          </td>
          <td align="center">
            <?php
            if($att['attendancekaryawan_check_out_early'] !== null) {
              echo $att['attendancekaryawan_check_out_early'];
            } else {
              echo '';
            };
            ?>
          </td>
          <td align="center">{{$att['attendancekaryawan_check_out_note']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
