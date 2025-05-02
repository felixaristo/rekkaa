<head>
  <style>
    #start-date,
    #end-date {
      width: 100%;
      /* Adjust the width as needed */
    }

    #detailModal label em {
      font-style: italic;
    }

    #detailModal p.font-weight-bold {
      font-weight: bold;
    }

    #table-history th,
    #table-history td,
    #table-history .dataTables_paginate {
      font-size: 12px;
      /* Adjust the font size as desired */
    }
  </style>
</head>
<?php 
use Carbon\Carbon;
$joindate = Carbon::parse($user->user_created_at);
$joindatey = $joindate->format('Y');
?>
<div class="accordion" id="accordionExample">
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
            <label for="start-date">Tanggal Pengajuan</label>
            <input type="text" id="start-date" name="start-date" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3">
            <label for="status">Status</label>
            <select id="statusfilter" name="status" class="form-control" autocomplete="off">
              <option value="" selected>Semua</option> <!-- Added "ALL" option -->
              <option value="WAITING">Menunggu</option>
              <option value="CANCEL">Dibatalkan</option>
              <option value="APPROVED">Disetujui</option>
              <option value="REJECTED">Tidak Disetujui</option>
              <option value="WAITINGCANCEL">Menunggu Pembatalan</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="listEmployee">Karyawan</label>
            <select multiple style="width: 100%; height: auto; padding: 8px; margin-top: 5px;" name="listEmployee[]" id="listEmployee" class="form-control" data-placeholder=" Pilih Karyawan" autocomplete="off"></select>
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
  </div>
</div>

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5>Daftar Lembur Karyawan</h5>
          </div>
          <div class="col-sm-6 d-flex justify-content-end">
            <div class="me-2">
              <button type="button" class="btn btn-sm btn-warning" id="btnPengajuan">+ Ajukan Lembur</button>
            </div>
            <!-- "Ekspor" button -->
            @if(in_array('IMPORT', request()->get('permission_codes')))
            <div class="import-button-container me-2">
              <button type="button" class="btn btn-sm btn-outline-info"><i class='bx bxs-cloud-upload'></i> Impor</button>
            </div>
            @endif
            @if(in_array('EKSPORT', request()->get('permission_codes')))
            <div class="export-button-container">
              <button type="button" class="btn btn-sm btn-outline-warning"><i class='bx bxs-file-export'></i> Ekspor</button>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-lembur-karyawan">
          <thead>
            <tr>
              <th>Nama Karyawan</th>
              <th>Tanggal Pengajuan</th>
              <th>Tanggal Mulai Lembur</th>
              <th>Tanggal Berakhir Lembur</th>
              <th>Jam Lembur (Jam)</th>
              <th>Nominal Lembur</th>
              <th>Total Nominal Lembur</th>
              <th>Status</th>
              <th>Tanggal Persetujuan</th>
              <th>Penanggung Jawab</th>
              <th>Catatan</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Lembur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <input type="hidden" id="lemburkaryawan_id" name="lemburkaryawan_id" value="">
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="nama-karyawan"><em>Nama Karyawan</em></label>
            <p id="nama-karyawan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-pengajuan"><em>Tanggal Pengajuan</em></label>
            <p id="tanggal-pengajuan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-mulai-lembur"><em>Tanggal Mulai Lembur</em></label>
            <p id="tanggal-mulai-lembur" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-berakhir-lembur"><em>Tanggal Berakhir Lembur</em></label>
            <p id="tanggal-berakhir-lembur" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="alasan-pengajuan"><em>Alasan Lembur</em></label>
            <p id="alasan-pengajuan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="status"><em>Status</em></label>
            <p id="status" class="font-weight-bold"></p>
          </div>
          <!-- <div class="form-group">
            <label for="keputusan-penerimaan"><em>Tanggal Persetujuan</em></label>
            <p id="keputusan-penerimaan" class="font-weight-bold"></p>
          </div> -->
          <div class="form-group dibatalkan-oleh-field" style="display: none;">
            <label for="dibatalkan-oleh"><em>Tidak Disetujui Oleh</em></label>
            <p id="dibatalkan-oleh" class="font-weight-bold"></p>
          </div>
          <div class="form-group disetujui-oleh-field" style="display: none;">
            <label for="disetujui-oleh"><em>Disetujui Oleh</em></label>
            <p id="disetujui-oleh" class="font-weight-bold"></p>
          </div>
          <div class="form-group alasan-dibatalkan-field" style="display: none;">
            <label for="alasan-dibatalkan"><em>Alasan Dibatalkan</em></label>
            <p id="alasan-dibatalkan" class="font-weight-bold"></p>
          </div>
          <div class="form-group alasan-ditolak-field" style="display: none;">
            <label for="alasan-ditolak"><em>Alasan Tidak Diizinkan</em></label>
            <p id="alasan-ditolak" class="font-weight-bold"></p>
          </div>
          <div class="d-flex justify-content-end">
            <!-- <div class="import-button-container me-2"> -->
            <button class="btn-approve btn btn-warning btn-sm me-2">Setujui</button>
            <!-- </div> -->
            <!-- <div class="import-button-container me"> -->
            <button class="btn-reject btn btn-outline-danger btn-sm">Tidak Setujui</button>
            <!-- </div> -->
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pengajuanModal" tabindex="-1" aria-labelledby="pengajuanModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pengajuanModalLabel">Ajukan Lembur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formPengajuan" action="{{route('user.lembur.pengajuan.create')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="lemburkaryawan_id" name="lemburkaryawan_id" value="">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="karyawan_ids" class="form-label title-case-jadwal lbl-req">Karyawan</label>
            </div>
            <div class="col">
              <div class="input-container">
                <select style="width: 100%; height: auto; padding: 8px; margin-top: 5px;" name="karyawan_ids" id="karyawan_ids" class="form-control" data-placeholder=" Pilih Karyawan" autocomplete="off"></select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="lemburkaryawan_start_time" class="form-label title-case-jadwal lbl-req">Tanggal Mulai Lembur</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="lemburkaryawan_start_time" name="lemburkaryawan_start_time" required placeholder="Tanggal Mulai Lembur">
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="lemburkaryawan_end_time" class="form-label title-case-jadwal lbl-req">Tanggal Berakhir Lembur</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="lemburkaryawan_end_time" name="lemburkaryawan_end_time" placeholder="Tanggal Berakhir Lembur">
              </div>
            </div>    
          </div>
          <div class="form-group textbox-address mb-3">
            <label for="lemburkaryawan_request_note" class="col-sm-12 col-form-label title-case-jadwal lbl-req">Alasan Lembur</label>
            <textarea name="lemburkaryawan_request_note" required id="lemburkaryawan_request_note" class="form-control"></textarea>
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                <!-- *)Tanggal Mulai dan Tanggal Berakhir harus sama untuk Lembur 1 Hari. -->
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add your JavaScript code here -->
<script src="{{asset('assets/js/reload.js')}}"></script>
<script>

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";

    let selectedEmployeeId = [];
    $("#statusfilter").select2();

    $(`#lemburkaryawan_start_time, #lemburkaryawan_end_time`).daterangepicker({
      timePicker: true,
      timePicker24Hour: true,
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY HH:mm:ss',
      },
      minYear: moment().year(),
      maxYear: moment().year() + 1,
    });

    $("#karyawan_ids").select2({
      dropdownParent: $("#formPengajuan"),
      ajax: {
        url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
        data: function(params) {
          var query = {
            q: params.term,
            type: 'public'
          }

          // Query parameters will be ?search=[term]&type=public
          return query;
        },
        processResults: function(data) {
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

    $("#listEmployee").select2({
      // dropdownParent: $("#formAddLembur"),
      // tags: true,
      ajax: {
        url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
        data: function(params) {
          var query = {
            q: params.term,
            type: 'public'
          }

          // Query parameters will be ?search=[term]&type=public
          return query;
        },
        processResults: function(data) {
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

    let filterPeriode = moment().format('MM-YYYY');
    $("#start-date").datepicker({
      language: "id-ID",
      format: "MM-yyyy",
      startView: "months", 
      minViewMode: "months",
      startDate: '01-<?php echo $joindatey ?>',
      endDate: '12-'+moment().format('Y'),
    }).datepicker( "setDate", filterPeriode)
    .on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#start-date').datepicker("getDate");
        filterPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
    });

    $("#searchButton").on("click", function() {
      tblLembur.draw();
    });

    $("#resetButton").on("click", function () {
      let startDate = moment().format('MM-YYYY');
      filterPeriode = startDate;
      $('#start-date').datepicker( "setDate", startDate);
      $("#status").val("").trigger("change");
      $("#listEmployee").val(null).trigger("change");
      tblLembur.draw();
    });

    let tblLembur = $("#table-lembur-karyawan").DataTable({
      "searching": false,
      "searchDelay": 1050,
      "language": {
        "searchPlaceholder": "Cari Nama Karyawan",
        // "emptyTable": "Tidak ada data"
      },
      "processing": true,
      "serverSide": true,
      "lengthMenu": [10, 25, 50], // Set number of records to display per page
      "info": false,
      "ordering": true,
      "ajax": {
        "url": `{{ route('user.lembur.list.admin') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          // Add any additional data you want to pass to the server here
          data.status = $('#status').val()
          data.start_date = filterPeriode
          data.karyawan_ids = $("#listEmployee").val()
        },
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "karyawan",
          "title": "Nama Karyawan",
          "className": "text-center",
          "render": function(data, type, row) {
            return data.karyawan_name
          }
        },
        {
          "data": "lemburkaryawan_created_at",
          "title": "Tanggal Pengajuan",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-';
          }
        },
        {
          "data": "lemburkaryawan_start_time",
          "title": "Tanggal Mulai Lembur",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-';
          }
        },
        {
          "data": "lemburkaryawan_end_time",
          "title": "Tanggal Berakhir Lembur",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-';
          }
        },
        {
          "data": "lemburkaryawan_total_time",
          "title": "Jam Lembur (Jam)",
          "className": "text-center",
          "render": function(data, type, row) {
            return Math.floor(data / 60);
          }
        },
        {
          "data": "lemburkaryawan_overtime_amount",
          "title": "Nominal Lembur",
          "className": "text-center",
          "render": function(data, type, row) {
            return "Rp."+formatCurrency(data)
          }
        },
        {
          "data": "lemburkaryawan_total_overtime_amount",
          "title": "Total Nominal Lembur",
          "className": "text-center",
          "render": function(data, type, row) {
            return "Rp."+formatCurrency(data)
          }
        },
        {
          "data": "lemburkaryawan_status",
          "title": "Status",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data === 'APPROVED') {
              return '<span class="badge bg-success">Disetujui</span>'
            } else if (data === 'REJECTED') {
              return '<span class="badge bg-danger">Tidak Disetujui</span>';
            } else if (data === 'CANCELED' || data === 'CANCEL') {
              return '<span class="badge bg-warning">Dibatalkan</span>';
            } else if (data === 'WAITINGCANCEL') {
              return '<span class="badge bg-warning">Menunggu Pembatalan</span>';
            } else {
              return '<span class="badge bg-info">Menunggu</span>';
            }
          }
        },
        {
          "data": "lemburkaryawan_approvaladmin_date",
          "title": "Tanggal Persetujuan",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '-';
          }
        },
        {
          "data": "manager",
          "title": "Penanggung Jawab",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? data.karyawan_name : '-';
          }
        },
        {
          "data": "lemburkaryawan_request_note",
          "title": "Catatan",
          "className": "text-center",
        },
        {
          "data": null,
          "title": "Aksi",
          "className": "text-center",
          "orderable": false,
          "render": function(data, type, row) {
            let btn = `<a class="dropdown-item detail-button" href="javascript:void(0);">
                            <i class="bx bx-show me-1 text-info"></i> Selengkapnya
                        </a>`;
            if (row.lemburkaryawan_status === 'APPROVEDMANAGER') {
              btn += `<a class="dropdown-item btn-edit" href="javascript:void(0);"
                  ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a>
                        <a class="dropdown-item btn-reject" href="javascript:void(0);">
                            <i class="bx bx-block me-1 text-danger"></i> Tidak Disetujui
                        </a>
                        <a class="dropdown-item btn-approve" href="javascript:void(0);">
                            <i class="bx bx-check me-1 text-success"></i> Disetujui
                        </a>`;
            }
            
            return `
              <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                      ${btn}
                  </div>
              </div>`;
          },
        },
      ],
    });

    $("#table-lembur-karyawan").on("click", ".detail-button", function() {
      const rowData = tblLembur.row($(this).closest("tr")).data();
      
      $("#detailModal #nama-karyawan").text(rowData.karyawan.karyawan_name);
      $("#detailModal #tanggal-pengajuan").text(moment(rowData.lemburkaryawan_created_at).format('DD-MM-YYYY HH:mm:ss'));
      $("#detailModal #alasan-pengajuan").text(rowData.lemburkaryawan_request_note);
      $("#detailModal #tanggal-mulai-lembur").text(moment(rowData.lemburkaryawan_start_time).format('DD-MM-YYYY HH:mm:ss'));
      $("#detailModal #tanggal-berakhir-lembur").text(moment(rowData.lemburkaryawan_end_time).format('DD-MM-YYYY HH:mm:ss'));
      $("#detailModal #status").text(getStatusText(rowData.lemburkaryawan_status));
      // $("#detailModal #keputusan-penerimaan").text(rowData.lemburkaryawan_approval_date ? moment(rowData.lemburkaryawan_approval_date).format('DD-MM-YYYY HH:mm:ss') : "-");

      // Populate and show/hide "Alasan Dibatalkan" field based on the status
      $("#detailModal .btn-reject").hide();
      $("#detailModal .btn-approve").hide();
      
      // if(rowData.lemburkaryawan_status == 'WAITING') {
      //   $("#detailModal .btn-reject").show();
      //   $("#detailModal .btn-approve").show();
      // }
      if (rowData.lemburkaryawan_status === "CANCEL" || rowData.lemburkaryawan_status === "WAITINGCANCEL" ) {
          $("#detailModal .alasan-dibatalkan-field").show();
          $("#detailModal #alasan-dibatalkan").text(rowData.lemburkaryawan_cancel_note);
      } else {
          $("#detailModal .alasan-dibatalkan-field").hide();
      }

      if (rowData.lemburkaryawan_status === "REJECTED") {
          $("#detailModal .alasan-ditolak-field").show();
          $("#detailModal #alasan-ditolak").text(rowData.lemburkaryawan_canceladmin_note);
          $("#detailModal .dibatalkan-oleh-field").show();
          $("#detailModal #dibatalkan-oleh").text(rowData.canceladmin.userwajibpajak.userwajibpajak_name);
      } else {
          $("#detailModal .alasan-ditolak-field").hide();
          $("#detailModal .dibatalkan-oleh-field").hide();
      }

      if (rowData.lemburkaryawan_status === "APPROVED") {
          $("#detailModal .disetujui-oleh-field").show();
          if(rowData.admin) {
            // rowData.admin.userwajibpajak.userwajibpajak_name
            $("#detailModal #disetujui-oleh").text(rowData.admin.userwajibpajak.userwajibpajak_name);
          }
      } else {
          $("#detailModal .disetujui-oleh-field").hide();
      }

      // Show the modal
      $("#detailModal").modal("show");
    });
    
    $("#table-lembur-karyawan").on("click", ".btn-approve", function(e) {
      e.preventDefault();

      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblLembur.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const lemburId = rowData.lemburkaryawan_id;
      const status = rowData.lemburkaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if(status !== 'APPROVEDMANAGER') {
        Swal.fire({
          html: 'Tidak dapat mengubah Lembur yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: 'Apakah anda ingin menerima lembur ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
            const swalResult = await Swal.fire({
                html: 'Masukan alasan anda menerima',
                input: 'text',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonText: 'Oke',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Mohon masukkan alasan anda';
                    }
                },
            });

            if (swalResult.isConfirmed) {
                const reason = swalResult.value;
                Swal.showLoading();
                const requestData = {
                    _token: csrfToken,
                    lemburkaryawan_approval_note: reason, // Add the reason to the request data
                };

                return fetch("{{ route('user.lembur.pengajuan.approve', '') }}/"+lemburId+`?menu_id=${currentMenuId}`, {
                    method: 'POST',
                    body: new URLSearchParams($.param(requestData)),
                })
                .then(response => {
                    Swal.close(); // Close the modal

                    if (!response.ok) {
                        return response.text().then(res => {
                            throw new Error(res);
                        })
                    }

                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            }
        },
        allowOutsideClick: () => false
      }).then((result) => {
          result = result.value;
          if(result == undefined) {
              return false;
          }
          
          if (!result.success) {

              Swal.fire({
                  title: result.message,
                  confirmButtonText: "Ok",
                  type: 'error'
              })
              return false;
          }

          toastr.success(result.message);
          tblLembur.draw();
        });
    });
    
    $("#table-lembur-karyawan").on("click", ".btn-reject", function(e) {
      e.preventDefault();

      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblLembur.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const lemburId = rowData.lemburkaryawan_id;
      const status = rowData.lemburkaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if(status === 'APPROVED') {
        Swal.fire({
          html: 'Tidak dapat mengubah Lembur yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: 'Apakah anda ingin membatalkan lembur ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
            const swalResult = await Swal.fire({
                html: 'Masukan alasan anda membatalkan',
                input: 'text',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonText: 'Oke',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Mohon masukkan alasan anda';
                    }
                },
            });

            if (swalResult.isConfirmed) {
                const reason = swalResult.value;
                Swal.showLoading();
                const requestData = {
                    _token: csrfToken,
                    lemburkaryawan_cancel_note: reason, // Add the reason to the request data
                };

                return fetch("{{ route('user.lembur.pengajuan.cancel', '') }}/"+lemburId+`?menu_id=${currentMenuId}`, {
                    method: 'POST',
                    body: new URLSearchParams($.param(requestData)),
                })
                .then(response => {
                    Swal.close(); // Close the modal

                    if (!response.ok) {
                        return response.text().then(res => {
                            throw new Error(res);
                        })
                    }

                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            }
        },
        allowOutsideClick: () => false
      }).then((result) => {
          result = result.value;
          if(result == undefined) {
              return false;
          }
          
          if (!result.success) {

              Swal.fire({
                  title: result.message,
                  confirmButtonText: "Ok",
                  type: 'error'
              })
              return false;
          }

          toastr.success(result.message);
          tblLembur.draw();
        });
    });

    $("#table-lembur-karyawan").on("click", ".btn-edit", function(e) {
      e.preventDefault();
      $("#formPengajuan")[0].reset(); // Reset the form fields

      // Get the row data associated with the clicked "Edit" button
      const rowData = tblLembur.row($(this).closest("tr")).data();
      const status = rowData.lemburkaryawan_status;
      
      if(status === 'APPROVED') {
        $("#formPengajuan :input").prop("disabled", true);
      } else {
        $("#formPengajuan :input").prop("disabled", false);
      }

      $("#lemburkaryawan_id").val(rowData.lemburkaryawan_id);
      $("#karyawan_ids").append(new Option(rowData.karyawan.karyawan_name, rowData.karyawan.karyawan_id, true, true));
      $("#lemburkaryawan_start_time").val(moment(rowData.lemburkaryawan_start_time).format('DD-MM-YYYY HH:mm:ss'));
      $("#lemburkaryawan_end_time").val(moment(rowData.lemburkaryawan_end_time).format('DD-MM-YYYY HH:mm:ss'));
      $("#lemburkaryawan_request_note").val(rowData.lemburkaryawan_request_note);
      $("#formPengajuan").attr("action", "{{route('user.lembur.pengajuan.update', '')}}/"+rowData.lemburkaryawan_id+"?menu_id="+currentMenuId);
      $("#pengajuanModal").modal("show");
    });


    $("#btnPengajuan").click(function() {
      $("#formPengajuan")[0].reset(); // Reset the form fields
      $("#formPengajuan").attr("action", "{{route('user.lembur.pengajuan.create')}}?menu_id="+currentMenuId);
      $("#pengajuanModal").modal("show");
    });

    let formPengajuan = $("#formPengajuan").validate({
      errorPlacement: function(error, element) {
        var isInputGroup = $(element).parent();
        console.log('isInputGroup', isInputGroup.length)
        let elem = $(element);
        if (elem.hasClass("select2-hidden-accessible")) {
            element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container'); 
            error.insertAfter(element);
        } else {
            if (isInputGroup.hasClass('input-group')) {
                error.insertAfter($(element).parent('.input-group'));
            } else {
                error.insertAfter(element);
            }
        }
      },
      rules: {},
      submitHandler: function(form) {
        $(".spinner-box").css({'display': 'table'});

        $.ajax({
          method: form.method,
          url: form.action,
          data: $(form).serialize()+"&"+$.param({
            _token: $("meta[name=csrf-token]").attr('content'),
          }),
          error: function(error) {
            $(".spinner-box").fadeOut();
            console.log('error.responseJSON', error.responseJSON)
            if(error.responseJSON) {
              let errs = error.responseJSON.errors;
              let errorName = [];
              if(errs) {
                let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
                // console.log('errsArr', errsArr)
                errsArr.forEach(err => {
                  console.log(err);
                  errorName.push(err[1]);
                });
              } else {
                errorName = [error.responseJSON.message];
              }

              Swal.fire({
                showCancelButton: false,
                confirmButtonText: "Ok",
                icon: 'error',
                html: errorName
              })
            }
          }, 
          success: function(response) {
              console.log(response, 'response')
              $(".spinner-box").fadeOut();
              if(!response.success) {
                if(Array.isArray(response.message)) {
                  formAuthentication.showErrors({
                    // stgrouptunjanganpegawai_name: response.message
                  })
                } else {
                    toastr.error(response.message);
                }
                  return false;
              }
              
              toastr.success(response.message);
              tblLembur.draw();

              $("#formPengajuan")[0].reset(); // Reset the form fields
              $("#pengajuanModal").modal("hide");

              $("#formPengajuan").attr("action", `{{route('user.lembur.pengajuan.create')}}?menu_id=${currentMenuId}`);
          }
        })
      },
    })

    function getStatusText(status) {
      if (status === "CANCEL") {
        return "Dibatalkan";
      } else if (status === "APPROVED") {
        return "Disetujui";
      } else if (status === "REJECTED") {
        return "Tidak Disetujui";
      } else if (status === "WAITING") {
        return "Menunggu";
      } else {
        return "Menunggu"
      }
      return "Unknown";
    }

    setHtmlTitle('{{$title}}')
  });
</script>