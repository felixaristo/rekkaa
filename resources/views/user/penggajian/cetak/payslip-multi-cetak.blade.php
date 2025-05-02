<?php
$current_date = Carbon\Carbon::parse(date('Y-m-d'))->format('d F Y');
// {{$payroll_period}}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="assets/img/logo/logo.png" />
  <link href="{{asset('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <title>Payroll - {{$current_date}}</title>
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
  foreach ($payrolls as $payroll) :
    $tunjangan = json_decode($payroll->payroll_allowance_setting);
    $potongan = json_decode($payroll->payroll_deduction_setting);
    $bpjssetting = json_decode($payroll->payroll_bpjssetting);
    $customtunjangan = json_decode($payroll->payroll_allowance_addition);
    $custompengurangan = json_decode($payroll->payroll_deduction_addition);
    $penggajiansetting = json_decode($payroll->payroll_penggajiansetting);

    $payroll_prorate = json_decode($payroll->payroll_prorate_data);

    $total_leave = $payroll_prorate->absence_days ?? '-';
    $working_days = $payroll_prorate->working_days ?? '-';
    $total_days = $payroll_prorate->total_days ?? '-';

    $stpenggajian = $payroll->karyawan->penggajian;
    // dd($penggajiansetting);
    $payroll_period = Carbon\Carbon::parse($payroll->payroll_period)->translatedFormat('F - Y');
    // dd($payroll->payroll_paid_at);
    $paid_date = Carbon\Carbon::parse($payroll->payroll_paid_at)->translatedFormat('d F Y');
    $bpjskes = 0;
    $bpjstk = 0;
    if($bpjssetting) {
      foreach($bpjssetting as $bset) {
        if($bset->type == 'KESEHATAN') {
          $bpjskes = 1;
        }
        if($bset->type == 'TENAGA_KERJA') {
          $bpjstk = 1;
        }
      }
    }
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
        <h5 class="text-bold">SLIP GAJI<br>{{$payroll_period}}</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <label class="col-sm-3 nodot-label">NIK</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->karyawan->karyawan_nik}}
          </div>
          <label class="col-sm-3 nodot-label">JABATAN</label>
          <div class="col-sm-3 text-bold">
            {{($payroll->karyawan->jabatan) ? $payroll->karyawan->jabatan->karyawanjabatan_name : '-'}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 nodot-label">Nama</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->karyawan->karyawan_name}}
          </div>
          <label class="col-sm-3 nodot-label">NPWP</label>
          <div class="col-sm-3 text-bold">
            {{$payroll->karyawan->karyawan_npwp}}
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
        @if($tunjangan)
        @foreach($tunjangan as $tj)
        <div class="row">
          <label class="col-sm-6 nodot-label">{{$tj->stgrouptunjangankaryawan_name}}</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($tj->sttunjangankaryawan_accumulate_value, 0, ',', '.')}}
          </div>
        </div>
        @endforeach
        @endif
        <div class="row">
          <?php if ($customtunjangan) :
            foreach ($customtunjangan as $ctunjangan) :
          ?>
              <div class="col-sm-12 pendapatan-item">
                <div class="row">
                  <label class="col-sm-7 nodot-label pendapatan-label">{{$ctunjangan->name}}</label>
                  <div class="col-sm-5 text-right pendapatan-currency">
                    Rp. {{number_format($ctunjangan->nominal, 0, ',', '.')}}
                  </div>
                </div>
              </div>
          <?php
            endforeach;
          endif ?>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_pph21, 0, ',', '.')}}
          </div>
        </div>
        @if($bpjskes)
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan BPJS Kesehatan</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
          </div>
        </div>
        @endif
        @if($bpjstk)
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan BPJS TK</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
          </div>
        </div>
        @endif
        
      </div>
      <div class="col-sm-6">
        @if($potongan)
        @foreach($potongan as $tp)
        <div class="row">
          <label class="col-sm-6 nodot-label">{{$tp->stgrouppotongankaryawan_name}}</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($tp->stpotongankaryawan_accumulate_value, 0, ',', '.')}}
          </div>
        </div>
        @endforeach
        @endif
        <div class="row">
          <?php if ($custompengurangan) :
            foreach ($custompengurangan as $cpengurangan) :
          ?>
              <div class="col-sm-12 pengurangan-item">
                <div class="row">
                  <label class="col-sm-7 nodot-label pengurangan-label">{{$cpengurangan->name}}</label>
                  <div class="col-sm-5 text-right pengurangan-currency">
                    Rp. {{number_format($cpengurangan->nominal, 0, ',', '.')}}
                  </div>
                </div>
              </div>
          <?php
            endforeach;
          endif ?>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan PPh 21</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_pph21, 0, ',', '.')}}
          </div>
        </div>
        @if($bpjskes)
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan BPJS Kesehatan</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_bpjskes, 0, ',', '.')}}
          </div>
        </div>
        @endif
        @if($bpjstk)
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan BPJS TK</label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_bpjstk, 0, ',', '.')}}
          </div>
        </div>
        @endif
        
        <div class="row mb-3 mt-3">
          <label class="col-sm-12 nodot-label text-bold">Dibayarkan Perusahaan</label>
          <div class="col-sm-12">
          @if($bpjskes)
            <div class="row">
              <label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS Kesehatan</label>
              <div class="col-sm-6 text-right">
                Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
              </div>
            </div>
          @endif
          @if($bpjstk)
            <div class="row">
              <label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS TK</label>
              <div class="col-sm-6 text-right">
                Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
              </div>
            </div>
          @endif
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
    @if($total_days > $working_days)
    <div class="row mb-5">
      <div class="col-sm-6">
        <p style="margin: 0;" class="text-bold">Note</p class="text-bold">
        <div class="row">
          <p class="col-sm-3" style="margin: 0;">On Leave</p>
          <span>: {{$total_leave}} Days</span>
        </div>
        <div class="row">
          <p class="col-sm-3" style="margin: 0;">Working Days</p>
          <span>: {{$total_days}} Days</span>
        </div>
        <div class="row">
          <p class="col-sm-3" style="margin: 0;">Total Working</p>
          <span>: {{$working_days}} Days</span>
        </div>
      </div>
    </div>
    @endif
    <div class="row mb-3">
      <div class="col-sm-6">
        <p>Disetujui oleh</p>
        <h5 class="text-bold">{{($penggajiansetting && $penggajiansetting->stpenggajiankaryawan_pic) ? $penggajiansetting->stpenggajiankaryawan_pic : '-'}}</h5>
      </div>
      <div class="col-sm-6">
        <p>{{($penggajiansetting && $penggajiansetting->stpenggajiankaryawan_location) ? $penggajiansetting->stpenggajiankaryawan_location : '-'}}, {{$paid_date}}</p>
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

  <script>
    window.jsPDF = window.jspdf.jsPDF;
    let payrolls = JSON.parse('<?php echo addslashes(json_encode($payrolls)) ?>');
    let maxData = payrolls.length;

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
            let title = payroll.karyawan.karyawan_name + '- Payslip ' + payroll.payroll_period;
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