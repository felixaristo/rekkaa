<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
</head>
<body>
  <table>
    <thead>
    <tr>
      <th align="center">Nama Akun</th>
      <th align="center">Email Akun</th>
      <th align="center">No. HP Akun</th>
      <th align="center">Nama Entitas</th>
      <th align="center">Email Entitas</th>
      <th align="center">No. Telepon / HP</th>
      <th align="center">NPWP</th>
      <th align="center">NIK</th>
      <th align="center">Tipe</th>
      <th align="center">Tgl. Kadaluarsa</th>
      <th align="center">Status</th>
    </tr>
    </thead>
    <tbody>
      <?php foreach($notificationdata as $dt): 
        $subdata = ($dt->notification_data) ? json_decode($dt->notification_data) : null;
        $exp_date = ($subdata) ? $subdata->subscription->activated_at .' - '. $subdata->subscription->expired_at : '';
      ?>
        <tr>
          <td align="center">{{($dt->user->userwajibpajak) ? $dt->user->userwajibpajak->userwajibpajak_name : '-'}}</td>
          <td align="center">{{$dt->user->user_email}}</td>
          <td align="center">{{($dt->user->userwajibpajak) ? $dt->user->userwajibpajak->userwajibpajak_phone : '-'}}</td>
          <td align="center">{{$dt->wajibpajak->wajibpajak_name}}</td>
          <td align="center">{{$dt->wajibpajak->wajibpajak_email}}</td>
          <td align="center">{{$dt->wajibpajak->wajibpajak_phone}}</td>
          <td align="center">{{($dt->wajibpajak->wajibpajak_type == 'BADAN') ? $dt->wajibpajak->wajibpajak_npwp : ''}}</td>
          <td align="center">{{($dt->wajibpajak->wajibpajak_type == 'BADAN') ? '' : $dt->wajibpajak->wajibpajak_nik}}</td>
          <td align="center">{{$dt->wajibpajak->wajibpajak_type}}</td>
          <td align="center">{{$exp_date}}</td>
          <td align="center">{{$dt->notification_title}}</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>