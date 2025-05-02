<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="{{url('assets/img/logo/logo.png')}}" />
  <link href="{{url('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <title>{{$data['title']}}</title>
  <style>
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
        <td width="350" class="text-right">{{$data['kepemilikannpwp']->kepemilikannpwp_name}}</td>
      </tr>
      <tr>
        <td>
          Status Kawin
        </td>
        <td>:</td>
        <td class="text-right">{{$data['ptkp']->ptkp_description}}</td>
      </tr>
      <tr>
        <td>
          Sumber Penghasilan
        </td>
        <td>:</td>
        <td class="text-right">{{$data['sumberpenghasilan'] == 2 ? 'Beberapa Pemberi Kerja' : 'Satu Pemberi Kerja'}}</td>
      </tr>
      <tr>
        <td>
          Metode Perhitungan
        </td>
        <td>:</td>
        <td class="text-right">{{($data['metode'] == 'GROSS') ? $data['metode'] : 'Gross Up / Nett'}}</td>
      </tr>
      <tr>
        <td>
          Penghasilan
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($data['penghasilan_bruto'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          DPP 50% x Penghasilan
        </td>
        <td>:</td>
        <td class="text-right">Rp. {{number_format($data['penghasilan_dpp'], 0, ',', '.')}}</td>
      </tr>
      <tr>
        <td>
          Gunakan Skema Tarif Efektif Rata-rata (TER)
        </td>
        <td>:</td>
        <td class="text-right">{{$data['menggunakan_ter'] == 1 ? 'Ya' : 'Tidak' }}</td>
      </tr>
      @if($data['menggunakan_ter'] == 1)
      <tr>
        <td>
          Kategori TER
        </td>
        <td>:</td>
        <td class="text-right">{{$data['ptkp']->ptkp_category}}</td>
      </tr>
      <tr>
        <td>
          Tarif TER
        </td>
        <td>:</td>
        <td class="text-right">{{$data['ptkpdetratepercentage']}} %</td>
      </tr>
      @endif
      <tr>
        <td>
          PPH 21 Terutang
        </td>
        <td>:</td>
        <td class="text-right text-bold">Rp. {{number_format($data['total_pph'], 0, ',', '.')}}</td>
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