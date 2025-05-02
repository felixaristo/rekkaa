<?php
header('Access-Control-Allow-Origin: *');
// $stpenggajian = $payroll->karyawan->penggajian;
// dd($custompengurangan);
// dd($payrolls);
if($type == 'periode') {
  $payroll = $payrolls[0];
  $payroll_period = $payrolls[0]->payroll_period;
  $payroll_paid_at = $payrolls[0]->payroll_paid_at;
  $wajibpajak_name = $payrolls[0]->karyawan->wajibpajak->wajibpajak_name;
  $wajibpajak_address = $payrolls[0]->karyawan->wajibpajak->wajibpajak_address;
  $karyawanjabatan_name = ($payrolls[0]->karyawan->jabatan) ? $payrolls[0]->karyawan->jabatan->karyawanjabatan_name : '-';
  $karyawan_nik = $payrolls[0]->payroll_karyawan_nik;
  $karyawan_name = $payrolls[0]->payroll_karyawan_name;
  $karyawan_npwp = $payrolls[0]->payroll_karyawan_npwp;

  $payroll_period = Carbon\Carbon::parse($payroll_period)->translatedFormat('F - Y');
  $paid_date = Carbon\Carbon::parse($payroll_paid_at)->translatedFormat('d F Y');

  $payroll_prorate_salary = 0;
  $payroll_allowance_pph21 = 0;
  $payroll_deduction_pph21 = 0;
  $payroll_total_income = 0;
  $payroll_total_netto = 0;
  $payroll_total_outcome = 0;
  foreach($payrolls as $py) {
    $payroll_prorate_salary += $py->payroll_prorate_salary;
    $payroll_allowance_pph21 += $py->payroll_allowance_pph21;
    $payroll_deduction_pph21 += $py->payroll_deduction_pph21;
    $payroll_total_income += $py->payroll_total_income;
    $payroll_total_netto += $py->payroll_total_netto;
    $payroll_total_outcome += $py->payroll_total_outcome;
  }
  
  $payroll_prorate_salary = $payroll_prorate_salary;
  $payroll_allowance_pph21 = $payroll_allowance_pph21;
  $payroll_deduction_pph21 = $payroll_deduction_pph21;
  $payroll_total_income = $payroll_total_income;
  $payroll_total_netto = $payroll_total_netto;
  $payroll_total_outcome = $payroll_total_outcome;

} else {
  $payroll_period = Carbon\Carbon::parse($payroll->payroll_period)->translatedFormat('F - Y');
  $paid_date = Carbon\Carbon::parse($payroll->payroll_paid_at)->translatedFormat('d F Y');
  $wajibpajak_name = $payroll->wajibpajak->wajibpajak_name;
  $wajibpajak_address = $payroll->wajibpajak->wajibpajak_address;
  $karyawanjabatan_name = ($payroll->karyawan->jabatan) ? $payroll->karyawan->jabatan->karyawanjabatan_name : '-';
  $karyawan_nik = $payroll->payroll_karyawan_nik;
  $karyawan_name = $payroll->payroll_karyawan_name;
  $karyawan_npwp = $payroll->payroll_karyawan_npwp;
  $payroll_prorate_salary = $payroll->payroll_prorate_salary;
  $payroll_allowance_pph21 = $payroll->payroll_allowance_pph21;
  $payroll_deduction_pph21 = $payroll->payroll_deduction_pph21;
  $payroll_total_income = $payroll->payroll_total_income;
  $payroll_total_netto = $payroll->payroll_total_netto;
  $payroll_total_outcome = $payroll->payroll_total_outcome;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Access-Control-Allow-Origin" content="*" />
  <link rel="icon" type="image/x-icon" href="assets/img/logo/logo.png" />
  <link href="{{asset('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="{{asset('assets/vendor/fonts/boxicons.css')}}" />
  <title>{{$title}} - {{$payroll_period}}</title>
  <style>
    label {
      font-weight: normal;
    }
    .pdf {
      position: relative;
    }
    #logo-poweredby {
      position: absolute;
      bottom: 0;
      right: 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row mb-3 mt-3">
      <div class="col-sm-12 text-right">
        <button type="button" class="btn btn-sm btn-warning" id="btn-download"><i class="bx bx-download"></i> Download</button>
      </div>
    </div>
  </div>
  <div class="container pdf" id="pdf">
    <div class="row">
      <div class="col-sm-6">
        @if($stpenggajian && $stpenggajian->stpenggajiankaryawan_logo)
        <img src="data:image/png;base64,{{$stpenggajian->stpenggajiankaryawan_logo_base64}}" id="clientlogo" alt="" class="img-fluid" width="200" />
        @endif
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <p>
          <span class="text-bold">{{$wajibpajak_name}}</span>
          <br>
          {{$wajibpajak_address}}
        </p>
      </div>
      <div class="col-sm-6 text-right">
        <h5 class="text-bold">SLIP GAJI<br>{{$payroll_period}}</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <label class="col-sm-3 nodot-label">NIK</label>
          <div class="col-sm-3 text-bold">
            {{$karyawan_nik}}
          </div>
          <label class="col-sm-3 nodot-label">JABATAN</label>
          <div class="col-sm-3 text-bold">
            {{$karyawanjabatan_name}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 nodot-label">Nama</label>
          <div class="col-sm-3 text-bold">
            {{$karyawan_name}}
          </div>
          <label class="col-sm-3 nodot-label">NPWP</label>
          <div class="col-sm-3 text-bold">
            {{$karyawan_npwp}}
          </div>
        </div>
      </div>
    </div>
    <div class="row border border-dark mb-3">
      <div class="col-sm-6">
        <h5 class="text-bold">PENDAPATAN</h5>
      </div>
      <div class="col-sm-6">
        <h5 class="text-bold">PEMOTONGAN</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6" style="min-height: 175px;">
        <div class="row">
          <label class="col-sm-6 nodot-label">Gaji Pokok</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll_prorate_salary, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll_allowance_pph21, 0, ',', '.')}}
          </div>
        </div>
        
      </div>
      <div class="col-sm-6">
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll_deduction_pph21, 0, ',', '.')}}
          </div>
        </div>
      </div>
      <div class="col-sm-12 mb-3 border border-dark">
        <div class="row">
          <div class="col-sm-6">
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Total Pendapatan</label>
              <div class="col-sm-6 text-right text-bold">
                Rp. {{number_format($payroll_total_income, 0, ',', '.')}}
              </div>
            </div>
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Gaji Bersih</label>
              <div class="col-sm-6 text-right text-bold bg-label-dark">
                Rp. {{number_format($payroll_total_netto, 0, ',', '.')}}
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Total Pemotongan</label>
              <div class="col-sm-6 text-right text-bold">
                Rp. {{number_format($payroll_total_outcome, 0, ',', '.')}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-sm-6">
        <p>Disetujui oleh</p>
        <h5 class="text-bold">{{($stpenggajian && $stpenggajian->stpenggajiankaryawan_pic) ? $stpenggajian->stpenggajiankaryawan_pic : '-'}}</h5>
      </div>
      <div class="col-sm-6">
        <p>{{($stpenggajian && $stpenggajian->stpenggajiankaryawan_location) ? $stpenggajian->stpenggajiankaryawan_location : '-'}}, {{$paid_date}}</p>
      </div>
      <div id="logo-poweredby">
        <img src="{{asset('assets/img/logo/powered_by_rekkaa.png')}}" alt="" class="img-fluid" width="150" />
      </div>
    </div>
  </div>
  <div id="blob"></div>
  <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/html2canvas/html2canvas.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jsPDF-master/dist/jspdf.umd.js')}}"></script>
  <script>
    window.jsPDF = window.jspdf.jsPDF;
    $(document).ready(function() {
      $("#btn-download").click(function(e) {
        e.preventDefault();

        var printDoc = new jsPDF({
        orientation: 'landscape'
      });
      // let src = document.getElementById("pdf");
      // Source HTMLElement or a string containing HTML.
      var elementHTML = document.querySelector("#pdf");

      printDoc.html(elementHTML, {
        allowTaint: false, useCORS: true,
        // logging: true,
        // proxy: $("#clientlogo").attr("src"),
        callback: function(printDoc) {
          // Save the PDF
          let title = '{{$karyawan_name}}' + '- Payslip ' + '{{$payroll_period}}';
          printDoc.setProperties({
            title: title,
            // subject: 'Info about PDF',
            // author: 'PDFAuthor',
            // keywords: 'generated, javascript, web 2.0, ajax',
            creator: 'Rekkaa'
          });
          printDoc.save(title + ".pdf");
        },
        margin: [5, 5, 5, 5],
        autoPaging: 'text',
        x: 0,
        y: 0,
        width: 140, //target width in the PDF document
        windowWidth: 575 //window width in CSS pixels
      });
      })
    })
  </script>
</body>

</html>