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
      <th align="center">Nama Karyawan</th>
      <th align="center">Jenis Cuti</th>
      <th align="center">Tanggal Pengajuan</th>
      <th align="center">Tanggal Mulai Cuti</th>
      <th align="center">Tanggal Berakhir Cuti</th>
      <th align="center">Status</th>
      <th align="center">Tanggal Persetujuan</th>
    </tr>
    </thead>
    <tbody>
    dd($cuti)
      @foreach ($cuti as $att)
        <tr>
          <td align="center">{{$att['karyawan_name']}}</td>
          <td align="center">{{$att['st_leave']['leave_description']}}</td>
          <td align="center">{{$att['leavekaryawan_request_date']}}</td>
          <td align="center">{{$att['leavekaryawan_start_date']}}</td>
          <td align="center">{{$att['leavekaryawan_end_date']}}</td>
          <td align="center">
            <?php
            $status = $att['leavekaryawan_status'];
            if ($status === 'APPROVED') {
                echo 'Disetujui';
            } elseif ($status === 'REJECTED') {
                echo 'Tidak Disetujui';
            } elseif ($status === 'CANCELED' || $status === 'CANCEL') {
                echo 'Dibatalkan';
            } elseif ($status === 'WAITINGCANCEL') {
                echo 'Menunggu Pembatalan';
            }else {
                echo 'Menunggu';
            }
            ?>
          </td>
          <td align="center">{{$att['leavekaryawan_approval_date']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
