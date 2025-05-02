<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$title}}</title>
</head>
<body>
  <?php 
    $tunjangan = ($masakerja) ? json_decode($masakerja[0]->setting_tunjangankaryawan_json) : [];
  ?>
  <table>
    <thead>
      <tr>
        <td class="table-logo">
          <img
            src="assets/img/logo/logo.png"
            alt=""
            class="img-fluid"
            width="150"
          />
        </td>
        <td align="center" colspan="{{ count($tunjangan) + 2}}">
          <h3>PT Ruhika Fusta Nusantara</h3>
          <p class="subtitle">{{$address->setting_value}}</p>
        </td>
      </tr>
      <tr>
        <th align="center" width="20">NPWP</th>
        <th align="center" width="20">NIK</th>
        <th align="center" width="20">Nama Karyawan</th>
        @foreach($tunjangan as $tj)
        <th align="center" width="20">{{$tj->sttunjangankaryawan_name}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      <?php 
      foreach($masakerja as $mk) : 
        $mk_tunjangan = ($mk->karyawanmasakerja_tunjangan) ? $mk->karyawanmasakerja_tunjangan : null;
      ?>
        <tr>
          <td align="center">{{$mk->karyawan_npwp}}</td>
          <td align="center">'{{($mk->karyawan_nik)}}</td>
          <td>{{$mk->karyawan_name}}</td>
          <?php foreach($tunjangan as $tj) : 
            $val = 0;
            foreach($mk_tunjangan as $mktjkey => $mktjval) :
              if($tj->sttunjangankaryawan_code === $mktjkey) {
                $val = $mktjval;
                break;
              } endforeach; 
            ?>
          <td align="right">{{$val}}</td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</body>
</html>