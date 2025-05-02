<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="{{url('assets/img/logo/logo.png')}}" />
  <link href="{{url('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <title>{{$data['title']}}</title>
  <style>
    /* body{ 
      margin: 0;
      overflow: hidden;
      width: 100%;
    } */
    /* body{ 
      margin: 0;
      overflow: hidden;
      width: 100%;
    }

  .footer {
    margin-bottom: 30px;
  }
  .box-title {
    width: 100%;
    height: 100%;
    text-align: center;
  }
  .title {
    font-size: 21px!important;
  }
  .subtitle {
    font-size: 13px!important;
  }
  td {
    vertical-align: top;
  } */
  /* td {
    vertical-align: top;
    padding: 5px auto;
  } */
  </style>
</head>
<body>

<div class="container">
  <table class="table table-header">
    <thead>
      <tr>
        <td class="table-logo" width="150">
        
        <img
            src="{{url('assets/img/logo/logo.png')}}"
            alt=""
            class="img-fluid"
            width="150"
          />
        </td>
        <td class="text-center">
          <h3>PT Ruhika Fusta Nusantara</h3>
          <p class="subtitle">{{$data['address']->setting_value}}</p>
        </td>
      </tr>
    </thead>
  </table>
  <!-- <hr style="border-top: 1px solid #eee!important"> -->
  <table class="table">
    <thead>
      <tr>
        <th class="text-center" colspan="3"><u>{{$data['title']}}</u></th>
      </tr>
    </thead>
  </table>
  <table class="table">
    <tbody>
      <tr>
        <td width="200">
          Kepemilikan NPWP
        </td>
        <td width="10">:</td>
        <td>{{$data['kepemilikannpwp']->kepemilikannpwp_name}}</td>
      </tr>
      <tr>
        <td>
          Kategori
        </td>
        <td>:</td>
        <td>{{$data['jenistransaksi']->objekpajak_category}}</td>
      </tr>
      <tr>
        <td>
          Jenis Transaksi
        </td>
        <td>:</td>
        <td><span class="text-bold">[{{$data['jenistransaksi']->objekpajak_code}}]</span> {{$data['jenistransaksi']->objekpajak_description}}</td>
      </tr>
      <tr>
        <td>
          Tarif
        </td>
        <td>:</td>
        <td>{{$data['jenistransaksi']->objekpajak_rate}} %</td>
      </tr>
      <tr>
        <td>
          Dasar Pengenaan Pajak
        </td>
        <td>:</td>
        <td>Rp. {{number_format($data['dasar_pajak'], 2, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          PPh Pasal 4 ayat 2
        </td>
        <td>:</td>
        <td>
          @php
          $total_pph = $data['kepemilikannpwp']->kepemilikannpwp_value * $data['jenistransaksi']->objekpajak_rate / 100 * $data['dasar_pajak']
          @endphp
          <span class="text-bold">Rp. {{number_format($total_pph, 2, ',','.')}}</span>
        </td>
      </tr>
    </tbody>
  </table>
  <htmlpagefooter name="myFooter">
    <table class="table table-footer">
      <thead>
        <tr>
          <td class="text-right">
            <p>Copyright&copy; Rekkaa. All Right Reserved.</p>
          </td>
        </tr>
      </thead>
    </table>
    </htmlpagefooter>
</div>
</body>
</html>