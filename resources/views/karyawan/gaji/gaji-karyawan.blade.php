<div class="accordion mb-4" id="accordionExample">
  <div class="card accordion-item active">
    <h2 class="accordion-header" id="headingFilter">
      <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse" role="tabpanel">
        Filter Lanjutan
      </button>
    </h2>

    <div id="filterCollapse" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <!-- Start Date and End Date inputs here -->
        <div class="row align-items-center">
          <div class="col-md-3">
            <label for="filterPeriode">Periode</label>
            <input type="text" id="filterPeriode" name="filterPeriode" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3 mt-md-4 mt-sm-3 text-md-start text-sm-end">
            <button id="searchButton" class="btn btn-search btn-outline-warning me-2 btn-sm">
              <i class="bx bx-search-alt"></i> Cari
            </button>
            <button id="resetButton" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
              <i class="bx bx-reset"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-2">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <div class="col-sm-10 text-right">
          
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-penggajian">
            <thead class="table-light">
              <tr>
                <th>Nama Karyawan</th>
                <th>Periode</th>
                <th>Gaji Bersih</th>
                <th>PPh 21</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let filterPeriode = moment().format('MM-YYYY');
    
    $("#filterPeriode").datepicker({
      language: "id-ID",
      format: "MM-yyyy",
      startView: "months", 
      minViewMode: "months"
    }).datepicker( "setDate", filterPeriode)
    .on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#filterPeriode').datepicker("getDate");
        filterPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
    });

    $("#searchButton").click(function(e) {
      e.preventDefault();
      tablePenggajian.draw();
    });

    $("#resetButton").click(function(e) {
      e.preventDefault();
      $("filterPeriode").val(null).trigger('change');
      tablePenggajian.draw();
    });

    // Begin Table Wajib Pajak
    let tablePenggajian = $("#table-penggajian").DataTable({
      // "filtering": true,
      "ordering": true,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "{{route('karyawan.gaji.view.karyawan.datatable')}}",
        "type": "GET",
        "data": function(data) {
          //     console.log(data); // send data to server
          data.periode = filterPeriode;
        }
      },
      // "fnInitComplete": function() {
      //     this.fnAdjustColumnSizing(true);
      // },
      "autoWidth": true,
      "columnDefs": [{
          target: [0,5],
          width: 30,
          orderable: false,
        }, {
          target: [2],
          orderable: false,
        }, {
          target: [0, 1, 2, 3, 4, 5],
          className: 'text-center'
        },
        //  {
        //   target: [4],
        //   "visible": false,
        //   "searchable": false,
        // }
      ],
      "columns": [
        {
          "data": "payroll_karyawan_name",
          // "className": "text-left",
          // "render": function(data, type, row) {
          //   return data;
          // }
        },
        {
          "data": "payroll_period",
          "render": function(data, type, row) {
            return (data) ? moment(data).format('MMMM - YYYY') : moment().date(0).format('MMMM - YYYY');
          }
        },
        {
          "data": "payroll_total_netto",
          "className": "text-right",
          "render": function(data, type, row) {
            let netto = 0;
            if (data) {
              netto = data;
            }
            return 'Rp. ' + formatCurrency(netto);
          }
        },
        {
          "data": "pph21",
          "className": "text-right",
          "render": function(data, type, row) {
            let pph21 = 0;
            if (data.pph21_total_month) {
              pph21 = data.pph21_total_month;
            }
            return 'Rp. ' + formatCurrency(pph21);
          }
        },
        {
          "data": "payroll_status",
          "render": function(data, type, row) {
            let status = `<span class="badge bg-warning">Menunggu Perhitungan</span>`;
            if (data) {
              // let payroll_status = data.payroll_status; 
              if (data == 1) {
                status = `<span class="badge bg-info">Menunggu Konfirmasi</span>`;
              } else if (data == 2) {
                status = `<span class="badge bg-danger">Menunggu Pembayaran</span>`;
              } else if (data == 3) {
                status = `<span class="badge bg-success">Dibayarkan</span>`;
              }
            }
            return status;
          }
        },
        {
          "data": "payroll_status",
          "render": function(data, type, row) {
            // let payroll_status = data.payroll_status; 
            let btn = `<a class="dropdown-item rekkaa-page-link" href="{{route('karyawan.gaji.view.karyawan.detail', '')}}/${row.payroll_id}"><i class="bx bx-list-ul me-1 text-info"></i> Detail</a>`;
            btn += ` <a href="#" class="dropdown-item btn-cetakslip"><i class="bx bxs-file-pdf text-info"></i> Lihat Slip</a>`;
            return `
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu">
                ${btn}
              </div>
            </div>
              `
          }
        },
      ],
    });

    $("#table-penggajian").on("click", ".btn-cetakslip", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      // console.log('data', data)
      let payroll_uuid = data.payroll_uuid;
      window.open(`{{route('karyawan.gaji.view.karyawan.cetak', '')}}/${payroll_uuid}`, '_blank');
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>