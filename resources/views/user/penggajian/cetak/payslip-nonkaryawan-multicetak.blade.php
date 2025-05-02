<?php
// header('Access-Control-Allow-Origin: *');
// $karyawan_ids = [];
// $karyawans = [];
  $payroll_period = $payrolls[0]->payroll_period;
// foreach($payrolls as $payroll) {
//   if(!in_array($payroll->ms_karyawan_id, $karyawan_ids)) {
//     $payroll_period = $payroll->payroll_period;
//     $payroll_paid_at = $payroll->payroll_paid_at;
//     $wajibpajak_name = $payroll->karyawan->wajibpajak->wajibpajak_name;
//     $wajibpajak_address = $payroll->karyawan->wajibpajak->wajibpajak_address;
//     $karyawanjabatan_name = ($payroll->karyawan->jabatan) ? $payroll->karyawan->jabatan->karyawanjabatan_name : '-';
//     $karyawan_nik = $payroll->payroll_karyawan_nik;
//     $karyawan_name = $payroll->payroll_karyawan_name;
//     $karyawan_npwp = $payroll->payroll_karyawan_npwp;

//     $payroll_period = Carbon\Carbon::parse($payroll_period)->translatedFormat('F - Y');
//     $paid_date = Carbon\Carbon::parse($payroll_paid_at)->translatedFormat('d F Y');

//     $payroll_prorate_salary = 0;
//     $payroll_allowance_pph21 = 0;
//     $payroll_deduction_pph21 = 0;
//     $payroll_total_income = 0;
//     $payroll_total_netto = 0;
//     $payroll_total_outcome = 0;
    
//     $payroll_prorate_salary += $payroll->payroll_prorate_salary;
//     $payroll_allowance_pph21 += $payroll->payroll_allowance_pph21;
//     $payroll_deduction_pph21 += $payroll->payroll_deduction_pph21;
//     $payroll_total_income += $payroll->payroll_total_income;
//     $payroll_total_netto += $payroll->payroll_total_netto;
//     $payroll_total_outcome += $payroll->payroll_total_outcome;
    
//     $payroll_prorate_salary = $payroll_prorate_salary;
//     $payroll_allowance_pph21 = $payroll_allowance_pph21;
//     $payroll_deduction_pph21 = $payroll_deduction_pph21;
//     $payroll_total_income = $payroll_total_income;
//     $payroll_total_netto = $payroll_total_netto;
//     $payroll_total_outcome = $payroll_total_outcome;

//     array_push($karyawan_ids, $payroll->ms_karyawan_id);
//     $karyawans[$payroll->ms_karyawan_id] = [
//       'payroll_period' => $payroll_period,
//       'payroll_paid_at' => $payroll_paid_at,
//       'wajibpajak_name' => $wajibpajak_name,
//       'wajibpajak_address' => $wajibpajak_address,
//       'karyawanjabatan_name' => $karyawanjabatan_name,
//       'karyawan_nik' => $karyawan_nik,
//       'karyawan_name' => $karyawan_name,
//       'karyawan_npwp' => $karyawan_npwp,
//       'payroll_period' => $payroll_period,
//       'paid_date' => $paid_date,
//       'payroll_prorate_salary' => $payroll_prorate_salary,
//       'payroll_allowance_pph21' => $payroll_allowance_pph21,
//       'payroll_deduction_pph21' => $payroll_deduction_pph21,
//       'payroll_total_income' => $payroll_total_income,
//       'payroll_total_netto' => $payroll_total_netto,
//       'payroll_total_outcome' => $payroll_total_outcome,
//     ];
//   } else {
//     $karyawans[$payroll->ms_karyawan_id]['payroll_prorate_salary'] += $payroll->payroll_prorate_salary;
//     $karyawans[$payroll->ms_karyawan_id]['payroll_allowance_pph21'] += $payroll->payroll_allowance_pph21;
//     $karyawans[$payroll->ms_karyawan_id]['payroll_deduction_pph21'] += $payroll->payroll_deduction_pph21;
//     $karyawans[$payroll->ms_karyawan_id]['payroll_total_income'] += $payroll->payroll_total_income;
//     $karyawans[$payroll->ms_karyawan_id]['payroll_total_netto'] += $payroll->payroll_total_netto;
//     $karyawans[$payroll->ms_karyawan_id]['payroll_total_outcome'] += $payroll->payroll_total_outcome;
//   }
// }
// $karyawans = array_values($karyawans);
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
  <?php 
  $i = 0;
  foreach($payrolls as $payroll) :
  ?>
  <div class="container pdf" id="pdf{{$i}}">
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
          <span class="text-bold">{{$payroll->wajibpajak->wajibpajak_name}}</span>
          <br>
          {{$payroll->wajibpajak->wajibpajak_address}}
        </p>
      </div>
      <div class="col-sm-6 text-right">
        <h5 class="text-bold">SLIP GAJI<br>{{Carbon\Carbon::parse($payroll->payroll_period)->translatedFormat('F - Y')}}</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <label class="col-sm-3 nodot-label">NIK</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->payroll_karyawan_nik}}
          </div>
          <label class="col-sm-3 nodot-label">JABATAN</label>
          <div class="col-sm-3 text-bold">
            
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 nodot-label">Nama</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->payroll_karyawan_name}}
          </div>
          <label class="col-sm-3 nodot-label">NPWP</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->payroll_karyawan_npwp}}
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
            Rp. {{number_format($payroll->payroll_prorate_salary, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_pph21, 0, ',', '.')}}
          </div>
        </div>
        
      </div>
      <div class="col-sm-6">
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_pph21, 0, ',', '.')}}
          </div>
        </div>
      </div>
      <div class="col-sm-12 mb-3 border border-dark">
        <div class="row">
          <div class="col-sm-6">
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Total Pendapatan</label>
              <div class="col-sm-6 text-right text-bold">
                Rp. {{number_format($payroll->payroll_total_income, 0, ',', '.')}}
              </div>
            </div>
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Gaji Bersih</label>
              <div class="col-sm-6 text-right text-bold bg-label-dark">
                Rp. {{number_format($payroll->payroll_total_netto, 0, ',', '.')}}
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="row">
              <label class="col-sm-6 nodot-label text-bold">Total Pemotongan</label>
              <div class="col-sm-6 text-right text-bold">
                Rp. {{number_format($payroll->payroll_total_outcome, 0, ',', '.')}}
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
        <p>{{($stpenggajian && $stpenggajian->stpenggajiankaryawan_location) ? $stpenggajian->stpenggajiankaryawan_location : '-'}}, {{Carbon\Carbon::parse($payroll->payroll_paid_at)->translatedFormat('d F Y')}}</p>
      </div>
      <div id="logo-poweredby">
        <img src="{{asset('assets/img/logo/powered_by_rekkaa.png')}}" alt="" class="img-fluid" width="150" />
      </div>
    </div>
  </div>
  <?php
    $i++;
  endforeach;
  ?>
  <div id="blank" style="position: fixed; background-color: #fff; top: 0; left: 0; right: 0; bottom: 0;">
    <div class="loader-table">
      <div class="loader-table-cell">
        <div class="loader"></div>
      </div>
    </div>
  </div>
  <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/html2canvas/html2canvas.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jsPDF-master/dist/jspdf.umd.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jszip-v3.10.1-1/dist/jszip.js')}}"></script>
  <script src="{{asset('assets/vendor/js/FileSaver.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/daterangepicker-master/moment.min.js')}}"></script>

  <script>
    window.jsPDF = window.jspdf.jsPDF;
    let payrolls = JSON.parse('<?php echo addslashes(json_encode($payrolls)) ?>');
    let maxData = payrolls.length;
    console.log('payrolls', payrolls);
    let zip = new JSZip();
    const docs = [];
    const createPDF = (index) => {
      let elementHTML = document.querySelector("#pdf" + index);
      if (elementHTML) {
        docs.push(new jsPDF({
          orientation: 'landscape'
        }));
        docs[index].html(elementHTML, {
          callback: function(pdf) {
            let payroll = payrolls[index];
            // pdf.save(index + ".pdf");
            let title = payroll.payroll_karyawan_name + '- Payslip ' + moment(payroll.payroll_trx_at).format('YYYY-MM-DD');
            zip.file(title + ".pdf", pdf.output("blob"));

            console.log('index', index)
            if (index < maxData) {

              console.log('indexafter', index)
              createPDF(index + 1);
            }
            let nextidx = index + 1;
            let nextElementHTML = document.querySelector("#pdf" + nextidx);
            if (nextElementHTML == undefined) {
              zip.generateAsync({
                  type: "blob"
                })
                .then(function(content) {
                  // see FileSaver.js
                  saveAs(content, "payslip_" + payroll.payroll_period + ".zip");
                  // close window
                  setTimeout(function () {
                      window.close();
                  }, 1000)
                });
            }
          },
          margin: [5, 5, 5, 5],
          autoPaging: 'text',
          x: 0,
          y: 0,
          width: 140, //target width in the PDF document
          windowWidth: 575 //window width in CSS pixels
        });
      }
    }
    createPDF(0);
  </script>
</body>

</html>