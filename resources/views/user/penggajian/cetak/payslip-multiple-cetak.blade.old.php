<?php
$tunjangan = json_decode($payroll->payroll_allowance_setting);
$potongan = json_decode($payroll->payroll_deduction_setting);
$bpjssetting = json_decode($payroll->payroll_bpjssetting);
$customtunjangan = json_decode($payroll->payroll_allowance_addition);
$custompengurangan = json_decode($payroll->payroll_deduction_addition);
// dd($custompengurangan);
$payroll_period = Carbon\Carbon::parse($payroll->payroll_period)->format('F - Y');
$current_date = Carbon\Carbon::parse(date('Y-m-d'))->format('d F Y');
// {{$payroll_period}}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="assets/img/logo/logo.png" />
  <link href="{{asset('assets/css/pdf.css')}}" rel="stylesheet" type="text/css" />
  <title>{{$title}} - {{$payroll_period}}</title>
  <style>
    label {
      font-weight: normal;
    }
  </style>
</head>

<body>
  @for($i=0; $i<=2; $i++)
  <div class="container" id="pdf{{$i}}">
    <div class="row">
      <div class="col-sm-6">
        <img src="{{asset('assets/img/logo/logo.png')}}" alt="" class="img-fluid" width="200" />
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
        <h5 class="text-bold">PAYROLL SLIP<br>{{$payroll_period}}</h5>
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
            {{$payroll->karyawan->karyawan_name}} {{$i}}
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
          <label class="col-sm-6 nodot-label">Gaji Pokok <a href="#" class="btn-info-gp"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_salary, 0, ',', '.')}}
          </div>
        </div>
        @if($tunjangan)
        @foreach($tunjangan as $tj)
        <div class="row">
          <label class="col-sm-6 nodot-label">{{$tj->stgrouptunjangankaryawan_name}} <a href="#" data-info="{{base64_encode(json_encode($tj))}}" class="btn-info-tunjangan-karyawan"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($tj->sttunjangankaryawan_accumulate_value, 0, ',', '.')}}
          </div>
        </div>
        @endforeach
        @endif
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan BPJS TK <a href="#" class="btn-info-tunjangan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan BPJS Kesehatan <a href="#" class="btn-info-tunjangan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Tunjangan PPh 21 <a href="#" class="btn-info-pph21" data-type="tunjangan"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_allowance_pph21, 0, ',', '.')}}
          </div>
        </div>
        <div class="row mt-3">
          <?php if ($customtunjangan) :
            foreach ($customtunjangan as $ctunjangan) :
              // if ($ctunjangan->taxable == '1') {
              //   $taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
              // } else {
              //   $taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
              // }
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
      </div>
      <div class="col-sm-6">
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan PPh21 <a href="#" class="btn-info-pph21" data-type="potongan"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_pph21, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan BPJS TK <a href="#" class="btn-info-potongan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_bpjstk, 0, ',', '.')}}
          </div>
        </div>
        <div class="row">
          <label class="col-sm-6 nodot-label">Pengurangan BPJS Kesehatan <a href="#" class="btn-info-potongan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($payroll->payroll_deduction_bpjskes, 0, ',', '.')}}
          </div>
        </div>
        @if($potongan)
        @foreach($potongan as $tp)
        <div class="row">
          <label class="col-sm-6 nodot-label">{{$tp->stgrouppotongankaryawan_name}} <a href="#" data-info="{{base64_encode(json_encode($tp))}}" class="btn-info-potongan-karyawan"><i class="bx bx-info-circle"></i></a></label>
          <div class="col-sm-6 text-right">
            Rp. {{number_format($tp->stpotongankaryawan_accumulate_value, 0, ',', '.')}}
          </div>
        </div>
        @endforeach
        @endif
        <div class="row">
          <label class="col-sm-12 nodot-label mb-3 text-bold">Dibayarkan Perusahaan</label>
          <div class="col-sm-12">
            <div class="row">
              <label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS TK <a href="#" data-dibayarperusahaan="{{$payroll->payroll_bpjstk_paidbycompany}}" class="btn-info-tunjangan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
              <div class="col-sm-6 text-right">
                Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
              </div>
            </div>
            <div class="row">
              <label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS Kesehatan <a href="#" data-dibayarperusahaan="{{$payroll->payroll_bpjskes_paidbycompany}}" class="btn-info-tunjangan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
              <div class="col-sm-6 text-right">
                Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <?php if ($custompengurangan) :
            // $idx = 0;
            foreach ($custompengurangan as $cpengurangan) :
              // if ($cpengurangan->taxable == '1') {
              //   $taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
              // } else {
              //   $taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
              // }
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
              <div class="col-sm-6 text-right text-bold">
                <span style="background-color: #dedede; padding: 5px 10px;">Rp. {{number_format($payroll->payroll_total_netto, 0, ',', '.')}}</span>
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
      <div class="col-sm-6 offset-6">
        <p>Jakarta, {{$current_date}}</p>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-sm-6">
        <p>Approved by</p>
        <h5 class="text-bold">{{$payroll->wajibpajak->wajibpajak_name}}</h5>
      </div>
      <div class="col-sm-6">
        <p>Created by</p>
        <h5 class="text-bold">PT. RUHIKA FUSTA NUSANTARA</h5>
        <p><img src="{{asset('assets/img/logo/logo.png')}}" alt="" class="img-fluid" width="150" /></p>
      </div>
    </div>
  </div>
  @endfor
  <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/html2canvas/html2canvas.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jsPDF-master/dist/jspdf.umd.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/libs/jszip-v3.10.1-1/dist/jszip.js')}}"></script>
  <script src="{{asset('assets/vendor/js/FileSaver.js')}}"></script>

  <script>
    let payroll = JSON.parse('<?php echo addslashes(json_encode($payroll)) ?>');
    window.jsPDF = window.jspdf.jsPDF;
        
    let zip = new JSZip();
    const docs = [];
    const createPDF = (index) => {
      let elementHTML = document.querySelector("#pdf"+index);
      if(elementHTML) {
        docs.push(new jsPDF({
          orientation: 'landscape'
        }));
        docs[index].html(elementHTML, {
          callback: function(pdf) {
            // pdf.save(index + ".pdf");
            let title = payroll.karyawan.karyawan_name + ' ' + index + '- Payslip ' + payroll.payroll_period;
            zip.file(title+".pdf", pdf.output("blob"));
            
            console.log('index', index)
            if (index < 10) {
              
            console.log('indexafter', index)
              createPDF(index + 1);
            }
            let nextidx = index + 1;
            let nextElementHTML = document.querySelector("#pdf"+nextidx);
            if(nextElementHTML == undefined) {
          zip.generateAsync({type:"blob"})
            .then(function(content) {
                // see FileSaver.js
                saveAs(content, "payslip_"+payroll.payroll_period+".zip");

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