<?php 
use Carbon\Carbon;
$joindate = Carbon::parse($user->user_created_at);
$joindatey = $joindate->format('Y');
?>
@if(!$stpenggajian)
<div class="alert alert-danger alert-dismissible" role="alert">
    <h3 class="text-danger">Peringatan!</h3>
    <p>Silahkan lakukan pengaturan penggajian terlebih dahulu <a href="{{route('user.page.pengaturan.penggajian.index')}}?menu_id=10">disini</a> untuk menggunakan fitur ini.</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif

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
          <div class="col-md-3">
            <label for="filterListEmployee">Karyawan</label>
            <select multiple style="width: 100%;" name="filterListEmployee[]" id="filterListEmployee" class="form-control" data-placeholder=" Pilih Karyawan" autocomplete="off"></select>
          </div>
          <div class="col-md-3">
            <label for="filterStatus">Status</label>
            <select id="filterStatus" name="filterStatus" class="form-control" autocomplete="off">
              <option value="-1" selected>Semua</option> <!-- Added "ALL" option -->
              <option value="0">Menunggu Perhitungan</option>
              <option value="1">Menunggu Konfirmasi</option>
              <option value="2">Menunggu Pembayaran</option>
              <option value="3">Dibayarkan</option>
            </select>
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
          @if(in_array('CALCULATE_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-outline-warning disabled" id="btn-kalkulasi" href="#">
              <i class='bx bx-refresh'></i>
              Kalkulasi Ulang
            </a>
            <a class="btn btn-sm btn-outline-info disabled" id="btn-finalisasi" href="#">
              <i class='bx bx-check'></i>
              Finalisasi Perhitungan
            </a>
          @endif
          @if(in_array('CONFIRM_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-danger disabled" id="btn-konfirmasi" href="#">
              <i class='bx bx-check'></i>
              Konfirmasi
            </a>
          @endif
          @if(in_array('PAY_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-success disabled" id="btn-bayar" href="#">
              <i class='bx bx-money'></i>
              Bayar
            </a>
          @endif
          @if(in_array('SEND_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-warning disabled" id="btn-kirim-slip" href="#">
              <i class='bx bx-upload'></i>
              Kirim Slip
            </a>
          @endif
          @if(in_array('DOWNLOAD_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-info disabled" id="btn-download-slip" href="#">
              <i class='bx bx-download'></i>
              Download Slip
            </a>
          @endif
          </div>
        </div>
        @if(in_array('IMPORT_CUSTOM_ALL_DED', request()->get('permission_codes')))
        <div class="row mt-2">
          <div class="col-sm-12 text-right">
          <a class="btn btn-sm btn-outline-info" id="btn-import-additional" href="#">
              <i class='bx bxs-cloud-upload'></i>
              Impor Tambahan Tunjangan & Potongan
            </a>  
          </div>
        </div>
        @endif
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-penggajian">
            <thead class="table-light">
              <tr>
                <th>
                  <div class="form-check form-check-inline">
                    <input name="karyawan_ischeck" class="form-check-input" type="checkbox" value="1" id="karyawan_ischeck">
                  </div>
                </th>
                <th>Nama Karyawan</th>
                <th>Periode</th>
                <th>Gaji Bersih</th>
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

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true" data-bs-focus="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">Unduh Templat & Impor Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sebelum mengimpor Tambahan Tunjangan & Potongan Karyawan, anda harus mengunduh Templat Impor Tambahan Tunjangan & Potongan Karyawan
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Tambahan Tunjangan & Potongan Karyawan yang akan di impor dengan Templat Impor Tambahan Tunjangan & Potongan sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import-file" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Data</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Impor -->
<div class="modal fade" id="modalImporAdditional" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Impor Tambahan Tunjangan & Potongan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
          id="closeImpor"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form class="form-horizontal form-lbl-dot">
            <div class="form-group row mb-3">
              <label for="file_import" class="col-sm-4 lbl-req">Unggah Data</label>
              <div class="col-sm-8">
                <input type="file" required name="file_import" id="file_import" class="form-control">
              </div>
            </div>
            <div class="form-group row mb-3">
              <div class="col-sm-12 text-right">
                <button type="button" id="importButton" class="btn btn-sm btn-outline-info"><i class='bx bxs-cloud-upload'></i> Impor</button>
              </div>
            </div>
          </form>

          <div class="progress-container" style="display: none;">
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 0;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailImportModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailImportModalLabel">Rincian Impor Tambahan Tunjangan & Potongan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeDetail"></button>
      </div>
      <div class="modal-body">
        <table id="table-detail" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
          <!-- Your DataTable content goes here -->
          <thead>
            <tr>
              <th>Baris</th>
              <th>Id Karyawan</th>
              <th>Tipe Tambahan</th>
              <th>Status</th>
              <th>Catatan</th>
              <!-- Add more columns as needed -->
            </tr>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <div class="col-sm-6 text-right">
          <a class="btn btn-sm btn-outline-warning" id="btn-unduh-rincian" href="#">
            <i class='bx bxs-file-export'></i> Unduh Rincian
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="historyModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Histori Impor Tambahan Tunjangan & Potongan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeHistory"></button>
      </div>
      <div class="modal-body">
        <div class="row align-items-center mb-3">
          <div class="col-md-3">
              <label for="start-date-history" class="small">Tanggal Awal</label>
              <input type="text" id="start-date-history" name="start-date-history" class="form-control form-control-sm" autocomplete="off">
          </div>
          <div class="col-md-3">
              <label for="end-date-history" class="small">Tanggal Akhir</label>
              <input type="text" id="end-date-history" name="end-date-history" class="form-control form-control-sm" autocomplete="off">
          </div>
          <div class="col-md-6 mt-md-4 mt-sm-3 text-md-start text-sm-end">
              <button id="searchButtonHistory" class="btn btn-search btn-outline-warning me-2 btn-sm">
                  <i class="bx bx-search-alt"></i> Cari
              </button>
              <button id="resetButtonHistory" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
                  <i class="bx bx-reset"></i> Reset
              </button>
          </div>
        </div>
        <!-- DataTable container -->
        <table id="table-history" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
          <!-- Your DataTable content goes here -->
          <thead>
            <tr>
              <th>Tanggal Impor</th>
              <th>File Impor</th>
              <th>Pengunggah</th>
              <th>Status Impor</th>
              <th>Histori File</th>
              <!-- Add more columns as needed -->
            </tr>
          </thead>
        </table>
      </div>
      <!-- <div class="modal-footer"> -->
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
      <!-- </div> -->
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
    function generateHistory(id) {
    $(".spinner-box").css({ display: "table" });
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const requestData = {
        id : id
    };

    $.ajax({
        url: "{{ route('user.penggajian.generate-history') }}?menu_id={{request()->get('menu_id')}}", // Concatenate the 'id' to the URL
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function(response) {
            setTimeout(function() {
                window.open(response, '_blank');
                window.close();
                $(".spinner-box").fadeOut();
            }, 1500);
        },
        error: function(error) {
            // Handle errors, if any
            console.error(error);
        },
    });
  }

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let filterPeriode = moment().format('MM-YYYY');
    $("#filterPeriode").datepicker({
      language: "id-ID",
      format: "MM-yyyy",
      startView: "months", 
      minViewMode: "months",
      startDate: '01-<?php echo $joindatey ?>',
      endDate: '12-'+moment().format('Y'),
    }).datepicker( "setDate", filterPeriode)
    .on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#filterPeriode').datepicker("getDate");
        filterPeriode = moment(dt).format('MM-YYYY');
    });
    
    $("#filterStatus").select2();
    $("#filterListEmployee").select2({
		  // dropdownParent: $("#formAddCuti"),
      // tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
				data: function (params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.karyawan_id;
						item.text = item.karyawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
      },
		})
    $("#searchButton").click(function(e) {
      e.preventDefault();
      deselectData();
      tablePenggajian.draw();
    });

    $("#resetButton").click(function(e) {
      e.preventDefault();
      $("filterPeriode").val(null).trigger('change');
      $("filterListEmployee").val(null).trigger('change');
      $("filterStatus").val(null).trigger('change');
      deselectData();
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
        "url": "{{route('user.page.penggajian.datatable', ['menu_id' => request()->get('menu_id')])}}",
        "type": "GET",
        "data": function(data) {
          //     console.log(data); // send data to server
          data.periode = filterPeriode;
          data.karyawan_ids = $("#filterListEmployee").val();
          data.status = $("#filterStatus").val();
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
      "columns": [{
          "data": "payroll_id",
          "render": function(data, type, row) {
            return `<div class="form-check form-check-inline">
                <input class="form-check-input karyawan_checked" type="checkbox"  value="1">
              </div>`;
          }
        },
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
            if (data) {
              // let payroll_status = data.payroll_status; 
              let btn = `<a class="dropdown-item rekkaa-page-link" href="{{route('user.page.penggajian.detail', '')}}/${row.payroll_id}?menu_id=${currentMenuId}"><i class="bx bx-list-ul me-1 text-info"></i> Detail</a>`;
              if (data == 0) {
                <?php if(in_array('CALCULATE_PAYSLIP', request()->get('permission_codes'))): ?>
                btn += ` <a href="#" class="dropdown-item btn-kalkulasi"><i class="bx bx-refresh text-warning"></i> Kalkulasi Ulang</a>`
                btn += ` <a href="#" class="dropdown-item btn-finalisasi"><i class="bx bx-check text-info"></i> Finalisasi Perhitungan</a>`
                <?php endif; ?>
              } else if (data == 1) {
                <?php if(in_array('CALCULATE_PAYSLIP', request()->get('permission_codes'))): ?>
                  btn += ` <a href="#" class="dropdown-item btn-kalkulasi"><i class="bx bx-refresh text-warning"></i> Kalkulasi Ulang</a>`
                <?php endif; ?>
                <?php if(in_array('CONFIRM_PAYSLIP', request()->get('permission_codes'))): ?>
                btn += ` <a href="#" class="dropdown-item btn-konfirmasi"><i class="bx bx-check text-danger"></i> Konfirmasi</a>`
                <?php endif; ?>
              } else if (data == 2) {
                <?php if(in_array('PAY_PAYSLIP', request()->get('permission_codes'))): ?>
                btn += ` <a href="#" class="dropdown-item btn-bayar"><i class="bx bx-money text-success"></i> Bayar</a>`;
                <?php endif; ?>
              } else if (data == 3) {
                <?php if(in_array('SEND_PAYSLIP', request()->get('permission_codes'))): ?>
                btn += ` <a href="#" class="dropdown-item btn-kirimslip"><i class="bx bx-upload text-warning"></i> Kirim Slip</a>`;
                <?php endif; ?>
                <?php if(in_array('DOWNLOAD_PAYSLIP', request()->get('permission_codes'))): ?>
                btn += ` <a href="#" class="dropdown-item btn-cetakslip"><i class="bx bxs-file-pdf text-info"></i> Lihat Slip</a>`;
                <?php endif; ?>
              }
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
            } else {
              return '';
            }
          }
        },
      ],
    });

    $("#table-penggajian").on("click", ".btn-kalkulasi", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      kalkulasiData = [data.payroll_uuid];
      msg = `Apakah anda ingin melakukan perhitungan ulang penggajian karyawan?`;
      processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}`, msg);
    })

    $("#table-penggajian").on("click", ".btn-finalisasi", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      kalkulasiData = [data.payroll_uuid];
      msg = `Apakah anda ingin melakukan finalisasi perhitungan penggajian karyawan?`;
      processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}&final=1`, msg);
    })

    $("#table-penggajian").on("click", ".btn-konfirmasi", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      kalkulasiData = [data.payroll_uuid];
      msg = `Apakah anda ingin mengkonfirmasi perhitungan penggajian karyawan?
      <br><span class="text-danger">*Perhitungan akan di <b>lock</b> dan tidak bisa di kalkulasi ulang.</span>`;
      processPayslip(`{{route('user.page.penggajian.confirmation')}}?menu_id=${currentMenuId}`, msg);
    })
    
    $("#table-penggajian").on("click", ".btn-bayar", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      bayarData = [data.payroll_uuid];
      msg = `Apakah anda ingin melakukan pembayaran karyawan?`;
      processPayslip(`{{route('user.page.penggajian.paid')}}?menu_id=${currentMenuId}`, msg);
    })

    $("#table-penggajian").on("click", ".btn-kirimslip", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      console.log('data.karyawan', data.karyawan);
      if (data.karyawan.karyawan_isuser != 1) {
        Swal.fire({
          html: 'Kirim informasi slip gaji gagal. Karyawan tersebut bukan merupakan user!',
          showCancelButton: false,
          confirmButtonText: "Ok",
          icon: 'error'
        })
        return false;
      }
      kirimData = [data.payroll_uuid];
      let payroll_period = moment(data.payroll_period).format('MMMM YYYY');
      msg = `Apakah anda ingin mengirim info slip periode <b>${payroll_period}</b> kepada karyawan ini?`;
      processPayslip(`{{route('user.page.penggajian.kirim')}}?menu_id=${currentMenuId}`, msg);
    })

    $("#table-penggajian").on("click", ".btn-cetakslip", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tablePenggajian.row(row).data();
      // console.log('data', data)
      let payroll_uuid = data.payroll_uuid;
      window.open(`{{route('user.page.penggajian.cetak', '')}}/${payroll_uuid}?menu_id=${currentMenuId}`, '_blank');
    })

    // kalkulasi ulang
    $("#btn-kalkulasi").click(function(e) {
      e.preventDefault();

      msg = `Apakah anda ingin melakukan perhitungan ulang penggajian karyawan?`;
      processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}`, msg);
    })
    // finalisasi
    $("#btn-finalisasi").click(function(e) {
      e.preventDefault();

      msg = `Apakah anda ingin melakukan finalisasi perhitungan penggajian karyawan?`;
      processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}&final=1`, msg);
    })
    // konfirmasi
    $("#btn-konfirmasi").click(function(e) {
      e.preventDefault();

      msg = `Apakah anda ingin mengkonfirmasi perhitungan penggajian karyawan?
      <br><span class="text-danger">*Perhitungan akan di <b>lock</b> dan tidak bisa di kalkulasi ulang.</span>`;
      processPayslip(`{{route('user.page.penggajian.confirmation')}}?menu_id=${currentMenuId}`, msg);
    })
    // bayar
    $("#btn-bayar").click(function(e) {
      e.preventDefault();

      msg = `Apakah anda ingin melakukan pembayaran karyawan?`;
      processPayslip(`{{route('user.page.penggajian.paid')}}?menu_id=${currentMenuId}`, msg);
    })
    // kirim
    $("#btn-kirim-slip").click(function(e) {
      e.preventDefault();

      msg = `Apakah anda ingin mengirim info slip kepada karyawan?`;
      processPayslip(`{{route('user.page.penggajian.kirim')}}?menu_id=${currentMenuId}`, msg);
    })
    // download
    $("#btn-download-slip").click(function(e) {
      e.preventDefault();
      let payroll_uuids = kalkulasiData.concat(bayarData, kirimData);
      let params = '';
      if (payroll_uuids.length > 0) {
        for (let i = 0; i < payroll_uuids.length; i++) {
          if (i == 0)
            params += 'payroll_uuids[]=' + payroll_uuids[i];
          else
            params += '&payroll_uuids[]=' + payroll_uuids[i];
        }
      }
      window.open(`{{route('user.page.penggajian.multicetak', '')}}?menu_id=${currentMenuId}&${params}`, '_blank');
    })

    //import tunjangan & potongan
    $("#btn-import-additional").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("show");
      // msg = `Apakah anda ingin melakukan perhitungan ulang penggajian karyawan?`;
      // processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}`, msg);
    })

    let kalkulasiData = [];
    let bayarData = [];
    let kirimData = [];
    let checkAll = 0;
    $("#table-penggajian").on("click", ".karyawan_checked", function(e) {
      let ischecked = $(this).is(':checked');
      let row = $(this).closest('tr');

      
      if (ischecked == false) {
        deselectData(ischecked, row);
        checkAll = 0;
      } else {
        selectData(row);
      //   console.log('kalkulasiData', kalkulasiData);
      // console.log('bayarData', bayarData);
      // console.log('kirimData', kirimData);
        // console.log("zz", tablePenggajian.rows().data().length);
        let totalData = tablePenggajian.rows().data().length;
        if(kalkulasiData.length >= totalData) {
          checkAll = 1;
        }
        if(bayarData.length >= totalData) {
          checkAll = 1;
        }
        if(kirimData.length >= totalData) {
          checkAll = 1;
        }
      }
    });
    $('#karyawan_ischeck').on('click', function() {
      if ($('#karyawan_ischeck').is(':checked')) {
        
        $(".karyawan_checked").prop("checked", true).trigger("change");
        selectData();
        checkAll = 1;
      } else {
        deselectData();
        $(".karyawan_checked").prop("checked", false).trigger("change");
        checkAll = 0;
      }
    });

    function enableBtn() {
      $("#btn-kalkulasi, #btn-finalisasi, #btn-konfirmasi, #btn-bayar, #btn-kirim-slip, #btn-download-slip").addClass('disabled');
      if (kalkulasiData.length > 0 && kirimData.length <= 0 && bayarData.length <= 0) {
        
        // console.log('isnotfinal', isnotfinal)
        if(isfinal > 0) {
          $("#btn-kalkulasi").removeClass('disabled');
          $("#btn-konfirmasi").removeClass('disabled');
        } else {
          $("#btn-kalkulasi").removeClass('disabled');
          $("#btn-finalisasi").removeClass('disabled');
        }
      } 

      if (bayarData.length > 0 && kalkulasiData.length <= 0 && kirimData.length <= 0)
        $("#btn-bayar").removeClass('disabled');

      if (kirimData.length > 0 && kalkulasiData.length <= 0 && bayarData.length <= 0)
        $("#btn-kirim-slip, #btn-download-slip").removeClass('disabled');

      // console.log(kalkulasiData,kirimData,bayarData);
    }

    let isnotfinal = 0;
    let isfinal = 0;
    function selectData(row = null) {
      // console.log('row', row);
      if (row) {
        tablePenggajian.row(row).select();

      } else {
        tablePenggajian.rows().select();
      }

      let selecteddata = tablePenggajian.rows('.selected').data();
      // console.log('selecteddata', selecteddata);
      for (let i = 0; i < selecteddata.length; i++) {
        let data = selecteddata[i];
        let selectedKalkulasiIdx = kalkulasiData.indexOf(data.payroll_uuid);
        let selectedBayarIdx = bayarData.indexOf(data.payroll_uuid);
        let selectedKirimIdx = kirimData.indexOf(data.payroll_uuid);
        if(data.payroll_status == 0) {
          isnotfinal += 1;
        }
        if(data.payroll_status == 1) {
          isfinal += 1;
        }
        if ((data.payroll_status == 1 || data.payroll_status == 0) && data.payroll_lock == 0) {
          if (selectedKalkulasiIdx < 0)
            kalkulasiData.push(data.payroll_uuid);
        }
        if (data.payroll_status == 2 && data.payroll_lock == 1)
          if (selectedBayarIdx < 0)
            bayarData.push(data.payroll_uuid);
        if (data.payroll_status == 3 && data.payroll_lock == 1)
          if (selectedKirimIdx < 0)
            kirimData.push(data.payroll_uuid);
      }
      console.log(kalkulasiData, kirimData, bayarData);
      enableBtn();
    }

    function deselectData(ischecked = false, row = null) {
      if (ischecked == false && row) {
        tablePenggajian.row(row).deselect();
        let singledata = tablePenggajian.row(row).data();
        let kalkulasiidx = kalkulasiData.indexOf(singledata.payroll_uuid);
        let bayaridx = bayarData.indexOf(singledata.payroll_uuid);
        let kirimidx = kirimData.indexOf(singledata.payroll_uuid);
        console.log('kirimidx', kirimidx);
        if (kalkulasiidx > -1)
          kalkulasiData.splice(kalkulasiidx, 1);
        if (bayaridx > -1)
          bayarData.splice(bayaridx, 1);
        if (kirimidx > -1) {
          console.log('kirimDatabefore', kirimData)
          kirimData.splice(kirimidx, 1);
          console.log('kirimDataafter', kirimData)
        }
        
        if(singledata.payroll_status == 0) {
          isnotfinal -= 1;
        }
        if(singledata.payroll_status == 1) {
          isfinal -= 1;
        }
      } else {
        $('#karyawan_ischeck').prop("checked", false).trigger("change");
        tablePenggajian.rows().deselect();
        kalkulasiData = [];
        bayarData = [];
        kirimData = [];
        isnotfinal = 0;
        isfinal = 0;
      }
      console.log(kalkulasiData, kirimData, bayarData);
      enableBtn();
    }

    function processPayslip(url = '#', msg = '') {
      Swal.fire({
        html: msg,
        icon: 'question',
        preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanPotongan.row(row).remove();
          // return true;
          return fetch(`${url}`, {
              method: 'POST',
              body: new URLSearchParams($.param({
                _token: $("meta[name=csrf-token]").attr('content'),
                check_all: checkAll,
                payroll_period: filterPeriode,
                payroll_uuids: kalkulasiData.concat(bayarData, kirimData)
              }))
            })
            .then(response => {
              if (!response.ok) {
                return response.text().then(res => {
                  throw new Error(res);
                })
              }
              return response.json()
            })
            .catch(error => {
              Swal.showValidationMessage(`Request failed: ${error}`);
            })
        },
        allowOutsideClick: () => false
      }).then((result) => {
        console.log('result', result)
        result = result.value;
        if (result == undefined) {
          return false;
        }

        if (!result.success) {
          let data = result.data;
          let infokaryawan = "<br><br>";
          if (data && data.karyawan != undefined) {
            let karyawan = data.karyawan;
            karyawan.forEach(kr => {
              infokaryawan += `<li class="text-bold">${kr.name} (${kr.enid})</li>`;
            });
          }
          Swal.fire({
            html: result.message + infokaryawan,
            showCancelButton: false,
            confirmButtonText: "Ok",
            icon: 'error'
          })
          return false;
        }

        toastr.success(result.message);
        deselectData();
        tablePenggajian.draw();
        checkAll = 0;
      });
    }

    $("#btn-template").on("click", function() {
      $(".spinner-box").css({
        display: "table"
      });
      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      // Make the POST request to your API endpoint
      $.ajax({
        url: `{{ route('user.penggajian.import-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        contentType: "application/json",
        success: function(response) {
          setTimeout(function() {
            window.open(response, '_blank');
            // window.close();
            $(".spinner-box").fadeOut();
          }, 1500);

          return
        },
        error: function(error) {
          // Handle errors, if any
          console.error(error);
        },
      });
    })

    $("#btn-import-file").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("hide");
      $("#modalImporAdditional").modal("show");
    })

    $("#closeImpor").click(function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
      $("#modalImporAdditional").modal("hide");
    })

    let tblDetailImport = $("#table-detail").DataTable({
      "searching": false,
      "language": {
        // "searchPlaceholder": "Cari Nama Karyawan",
        // "emptyTable": "Tidak ada data"
      },
      "lengthChange": true,
      "processing": true,
      "serverSide": false,
      "paging": false, // Enable pagination
      "lengthMenu": [100], // Set number of records to display per page
      "info": false,
      "ordering": false,
      "fnInitComplete": function() {
          this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [0,1,2,3,4],
        className: 'text-center'
      }],
    })

    $('#importButton').click(function() {
      // Construct the FormData object here (replace this with your actual code)
      let input = document.getElementById('file_import');
      $(this).prop('disabled', true);
      $(this).css('background-color', 'gray');
      $(this).css('color', 'white');

      let formData = new FormData();
      // return false;
      if(input.files.length < 1) {
        toastr.error('Mohon masukan file dengan format xls');
        $('#importButton').prop('disabled', false);
        $('#importButton').css('background-color', '');
        $('#importButton').css('color', '');
        return false;
      }
       // Show the progress bar container
      $('.progress-container').show();
      setTimeout(function () {
        $(".progress-bar").css("width", "30%");
      }, 300);

      formData.append('_token', $("meta[name=csrf-token]").attr('content'));
      formData.append('file', input.files[0]);

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      setTimeout(function () {
        $(".progress-bar").css("width", "95%");
      }, 1200);

      // Simulate a 1.8-second delay to set the progress bar to 30%
      setTimeout(function () {
          $.ajax({
          type: 'POST', // or 'GET' depending on your controller action
          url: `{{ route("user.page.penggajian.import") }}?menu_id=${currentMenuId}`,
          data: formData,
          headers: {
            "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
          },
          processData: false,
          contentType: false,
          success: function (response) {
            $('#importButton').prop('disabled', false);
            $('#importButton').css('background-color', '');
            $('#importButton').css('color', '');
            

            // Show SweetAlert success message
            setTimeout(function () {
              if (response.success === false) {
                // Show SweetAlert for failure
                Swal.fire({
                  icon: 'warning',
                  title: 'Gagal Impor',
                  html: (response.message) ? response.message : 'Cek file anda atau hubungi Customer Service',
                  showCancelButton: false,
                  confirmButtonText: 'Oke'
                });
              } else {
                $('#modalImporAdditional').modal('hide');
                input.value = "";
                $('.progress-container').hide();

                tblDetailImport.clear().draw();;

                $.each(response.result, function (index, item) {
                  // console.log(item, 'item')
                  tblDetailImport.row.add([
                    item.row,  // Row number
                    item.karyawan_enid,  // Id Karyawan
                    item.additional_type,  // Nama Karyawan
                    item.status,  // Status
                    item.remark,  // Remark
                    // Add more columns as needed
                  ]).draw(false); // 'false' means don't redraw the table until all rows are added
                });

                var id = response.history;

                // Find the button element by its ID
                var button = document.getElementById("btn-unduh-rincian");

                // Set the onclick attribute with the id
                button.onclick = function(e) {
                  e.preventDefault(); // Prevent the default behavior
                  generateHistory(id); // Call your function with the id
                };
                
                $('#detailImportModalLabel').text(`Rincian Impor : ${response.totalsuccess} dari ${response.totaldata} data berhasil di impor`);

                $('#detailImportModal').on('shown.bs.modal', function () {
                  // Clear the DataTable
                  tblDetailImport.columns.adjust().draw();
                });

                $('#detailImportModal').modal('show');

                tablePenggajian.draw()
              }
            }, 100)

            
            $(".progress-bar").css("width", "0%");
          },
          error: function (error) {
            $('#importButton').prop('disabled', false);
            $('#importButton').css('background-color', '');
            $('#importButton').css('color', '');
            Swal.fire({
              icon: 'warning',
              title: 'Gagal Impor',
              html: 'Cek file anda atau hubungi Customer Service',
              showCancelButton: false,
              confirmButtonText: 'Oke'
            });

            $(".progress-bar").css("width", "0%");
          }
        });
      }, 1800);
    })

    const currentDate = new Date();

    // Calculate the first day of the current month
    const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Calculate the last day of the current month
    const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
    const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;

    $(`#start-date-history`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedFirstDay, 
    });

    $(`#end-date-history`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedLastDay, // Set the calculated first day as the start date
      endDate: formattedLastDay, 
    });

    let tblHistory = $("#table-history").DataTable({
      "searching": false,
      "language": {
        // "searchPlaceholder": "Cari Nama Karyawan",
        // "emptyTable": "Tidak ada data"
      },
      "lengthChange": false,
      "processing": true,
      "serverSide": true,
      // "paging": true, // Enable pagination
      "lengthMenu": [10], // Set number of records to display per page
      "info": false,
      "ordering": false,
      "ajax": {
        "url": `{{ route('user.page.penggajian.importhistory') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          // Add any additional data you want to pass to the server here
          data.start_date = $('#start-date-history').val() ? moment($('#start-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
          data.end_date = $('#end-date-history').val() ? moment($('#end-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
          data.page = data.start / data.length + 1; // Calculate the current page based on start and length
          data.per_page = data.length; // Set the number of records per page
        },
      },
      "fnInitComplete": function() {
          this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [
        {
          "data": "historyimport_date",
          "title": "Tanggal Impor",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === "display" || type === "filter")) {
              return data.substring(0, 19); // Display only the date part (YYYY-MM-DD)
            }
            return data; // For sorting and other purposes, return the original data as it is
          }
        },
        {
          "data": "historyimport_file_name",
          "title": "File Impor",
          "className": "text-center",
        },
        {
          "data": "userwajibpajak_name",
          "title": "Pengunggah",
          "className": "text-center",
        },
        {
          "data": "historyimport_detail_import",
          "title": "Status Impor",
          "className": "text-center",
        },
        {
          "data": "historyimport_id",
          "title": "Histori File",
          "className": "text-center",
          "render": function(data, type, row) {
            return '<a href="javascript:void(0);" onclick="generateHistory(' + data + ');">Unduh</a>';
          }
        },
      ]
    })

    $("#btn-import-history").on("click", function(e) {
      $(".spinner-box").css({ display: "table" });
      tblHistory.draw();
      e.preventDefault();
      // $("#importModal").modal("hide");
      // $("#historyModal").modal("show");
      $("#importModal").modal("hide");
      $("#historyModal").modal("show");
      $(".spinner-box").fadeOut();
    });

    $("#searchButtonHistory").on("click", function () {
      tblHistory.draw();
    });

    $("#resetButtonHistory").on("click", function () {
        $(`#start-date-history`).val(formattedFirstDay);
        $(`#end-date-history`).val(formattedLastDay);
        tblHistory.draw();
    });

    $("#closeHistory").click(function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
      $("#historyModal").modal("hide");
      $(`#start-date-history`).val(formattedFirstDay);
      $(`#end-date-history`).val(formattedLastDay);
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>