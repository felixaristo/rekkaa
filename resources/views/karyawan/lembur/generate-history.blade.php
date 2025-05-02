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
      <th align="center">Kode Cuti</th>
      <th align="center">Tanggal Pengajuan</th>
      <th align="center">Tanggal Mulai Cuti</th>
      <th align="center">Tanggal Berakhir Cuti</th>
      <th align="center">Alasan Cuti</th>
      <th align="center">Status</th>
      <th align="center">Tanggal Persetujuan</th>
      <th align="center">Alasan Tidak Diterima</th>
      <th align="center">Status</th>
      <th align="center">Remark</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($listleave as $att)
        <tr>
          <td align="center">{{$att['karyawan_id']}}</td>
          <td align="center">{{$att['leave_id']}}</td>
          <td align="center">{{$att['leavekaryawan_request_date']}}</td>
          <td align="center">{{$att['leavekaryawan_start_date']}}</td>
          <td align="center">{{$att['leavekaryawan_end_date']}}</td>
          <td align="center">{{$att['leavekaryawan_request_note']}}</td>
          <td align="center">{{$att['leavekaryawan_status']}}</td>
          <td align="center">{{$att['leavekaryawan_approval_date']}}</td>
          <td align="center">{{$att['leavekaryawan_approval_note']}}</td>
          <td align="center">{{$att['status']}}</td>
          <td align="center">{{$att['remark']}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
