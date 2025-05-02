<head>
  <style>
    #table-history th,
    #table-history td,
    #table-history .dataTables_paginate {
      font-size: 12px;
      /* Adjust the font size as desired */
    }
  </style>
</head>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">
          <!-- <a class="btn btn-sm btn-outline-warning me-3" id="lock-kalkulasi-karyawan" href="#">
            <span class="bx bx-lock"></span>
            Lock Kalkulasi Karyawan
          </a> -->
          @if(in_array('RD', request()->get('permission_codes')))
          <a class="btn btn-sm btn-outline-danger rekkaa-page-link" id="btn-nonaktifkaryawan" href="{{route('user.page.karyawannonaktif.index', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bxs-user-x'></i> Non Aktif Karyawan
          </a>
          @endif
          @if(in_array('IMPORT', request()->get('permission_codes')))
          <a class="btn btn-sm btn-outline-info" id="btn-import" href="#">
            <i class='bx bxs-cloud-upload'></i> Impor
          </a>
          @endif

          @if(in_array('C', request()->get('permission_codes')))
          <a class="btn btn-sm btn-warning rekkaa-page-link" href="{{route('user.page.karyawan.create', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bx-plus'></i> Karyawan
          </a>
          @endif

        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="text-nowrap">
            <table class="table table-hover display nowrap" id="table-karyawan" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th>Id Karyawan</th>
                  <th>NIK</th>
                  <th>NPWP</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>Telepon</th>
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
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<!-- Impor & Template Modal -->
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
              *) Sebelum mengimpor Data Karyawan, anda harus mengunduh Templat Impor Karyawan
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Karyawan yang akan di impor dengan Templat Impor Karyawan sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import-file" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Karyawan</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="historyModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Histori Impor Karyawan</h5>
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

<!-- Modal Impor -->
<div class="modal fade" id="modalImporKaryawan" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Impor Profil Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeImpor"></button>
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
        <h5 class="modal-title" id="detailImportModalLabel">Rincian Impor Non Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeDetail"></button>
      </div>
      <div class="modal-body">
        <table id="table-detail" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
          <!-- Your DataTable content goes here -->
          <thead>
            <tr>
              <th>Baris</th>
              <th>Id Karyawan</th>
              <th>Nama Karyawan</th>
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

<script src="{{asset('assets/js/reload.js')}}"></script>

<script>
  function generateHistory(id) {
    $(".spinner-box").css({
      display: "table"
    });
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const requestData = {
      id: id
    };

    $.ajax({
      url: "{{ route('user.karyawan.generate-history') }}?menu_id={{request()->get('menu_id')}}", // Concatenate the 'id' to the URL
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
      },
      contentType: "application/json",
      data: JSON.stringify(requestData),
      success: function(response) {
        setTimeout(function() {
          window.open(response, '_blank');
          // window.close();
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

    const currentDate = new Date();

    // Calculate the first day of the current month
    const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Calculate the last day of the current month
    const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
    const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;

    $("#btn-template").on("click", function() {
      $(".spinner-box").css({
        display: "table"
      });
      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      // Make the POST request to your API endpoint
      $.ajax({
        url: `{{ route('user.page.karyawan.download-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        contentType: "application/json",
        success: function(response) {
          setTimeout(function() {
            window.open(response, '_blank');
            $(".spinner-box").fadeOut();
            
            window.close();
          }, 1500);

          return
        },
        error: function(error) {
          // Handle errors, if any
          console.error(error);
        },
      });
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
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [0, 1, 2, 3, 4],
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
      if (input.files.length < 1) {
        toastr.error('Mohon masukan file dengan format xls');
        $('#importButton').prop('disabled', false);
        $('#importButton').css('background-color', '');
        $('#importButton').css('color', '');
        return false;
      }
      // Show the progress bar container
      $('.progress-container').show();
      setTimeout(function() {
        $(".progress-bar").css("width", "30%");
      }, 300);

      formData.append('_token', $("meta[name=csrf-token]").attr('content'));
      formData.append('file', input.files[0]);

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      setTimeout(function() {
        $(".progress-bar").css("width", "95%");
      }, 1200);

      // Simulate a 1.8-second delay to set the progress bar to 30%
      setTimeout(function() {
        $.ajax({
          type: 'POST', // or 'GET' depending on your controller action
          url: `{{ route("user.page.karyawan.import") }}?menu_id=${currentMenuId}`,
          data: formData,
          headers: {
            "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
          },
          processData: false,
          contentType: false,
          success: function(response) {
            $('#importButton').prop('disabled', false);
            $('#importButton').css('background-color', '');
            $('#importButton').css('color', '');


            // Show SweetAlert success message
            // setTimeout(function() {
              if (response.success === false) {
                // Show SweetAlert for failure
                Swal.fire({
                  icon: 'warning',
                  title: 'Gagal Impor',
                  html: (response.message) ? response.message : 'Cek file anda atau hubungi Customer Service',
                  showCancelButton: false,
                  confirmButtonText: 'Oke'
                });
                return false;
              } 
              // else {
                $('#modalImporKaryawan').modal('hide');
                input.value = "";
                $('.progress-container').hide();

                tblDetailImport.clear().draw();;

                $.each(response.result, function(index, item) {
                  tblDetailImport.row.add([
                    item.row, // Row number
                    item.karyawan_enid, // Id Karyawan
                    item.karyawan_name, // Nama Karyawan
                    item.status, // Status
                    item.remark, // Remark
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

                $('#detailImportModal').on('shown.bs.modal', function() {
                  // Clear the DataTable
                  tblDetailImport.columns.adjust().draw();
                });

                // $('#detailImportModal').modal('show');

                // tblKaryawan.draw()
                $('#historyModal').modal('show');
              
                tblHistory.draw()

                $(".progress-bar").css("width", "0%");
              // }
            // }, 100)


            $(".progress-bar").css("width", "0%");
          },
          error: function(error) {
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
        "url": `{{ route('user.karyawan.import-history') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
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
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
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
          "data": "historyimport_originfile_name",
          "title": "File Impor",
          "className": "text-center",
          "render": function(data, type, row) {
            return (data) ? data : row.historyimport_file_name;
          }
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
          "render": function(data, type, row) {
            return (data) ? data : 'Sedang diproses';//row.historyimport_status;
          }
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
      // $(".spinner-box").css({
      //   display: "table"
      // });
      // tblHistory.draw();
      e.preventDefault();
      // $("#importModal").modal("hide");
      // $("#historyModal").modal("show");
      $("#importModal").modal("hide");
      $("#historyModal").modal("show");
      // $(".spinner-box").fadeOut();
    });
    $('#historyModal').on('shown.bs.modal', function() {
      // Clear the DataTable
      tblHistory.columns.adjust().draw();
    });
    $("#searchButtonHistory").on("click", function() {
      tblHistory.draw();
    });

    $("#resetButtonHistory").on("click", function() {
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

    let tblKaryawan = $("#table-karyawan").DataTable({
      // "language": {
      //   "infoEmpty": "No records available - Got it?",
      // },
      // "dom": 'flrtip',
      "searching": true,
      "language": {
        "searchPlaceholder": "Cari NIK,NPWP,Nama Karyawan",
      },
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": `{{route('user.page.karyawan.datatable', '')}}`,
        "type": "GET",
        "data": function(data) {
          data.menu_id = currentMenuId;
          //     console.log(data); // send data to server
        }
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
        // $(this).find(".cetak-registrasi").select2();
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [7],
        width: 30,
        orderable: false,
      }, {
        target: [0, 1, 2, 5, 6, 7],
        className: 'text-center'
      }],
      "columns": [{
          "data": "karyawan_enid",
        },
        {
          "data": "karyawan_nik",
        },
        {
          "data": "karyawan_npwp"
        },
        {
          "data": "karyawan_name",
          "render": function(data, type, row) {
            if (row.karyawan_end_type === 'RESIGN') {
              return `${data} <span class="badge rounded-pill bg-label-warning">Resign<span>`;
            }
            return data;
          }
        },
        {
          "data": "karyawan_address",
          "render": function(data, type, row) {
            if (type === 'display' && data !== null && data.length > 15) {
              return data.substr(0, 15) + '...';
            }
            return data;
          }
        },
        {
          "data": "karyawan_phone"
        },
        {
          "data": "karyawan_status",
          "render": function(data, type, row) {
            // console.log('data', data)
            let badge = '';
            if (data == 'NONKARYAWAN') {
              badge = '<span class="badge rounded-pill bg-info">Bukan Karyawan</span>';
            } else if (data == 'TETAP') {
              badge = '<span class="badge rounded-pill bg-primary">Tetap</span>';
            } else if (data == 'KONTRAK') {
              badge = '<span class="badge rounded-pill bg-secondary">Kontrak</span>';
            } else if (data == 'PERCOBAAN') {
              badge = '<span class="badge rounded-pill bg-warning">Percobaan</span>';
            }
            return badge;
          }
        },
        {
          "data": "karyawan_id",
          "render": function(data, type, row) {
            return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                <?php if (in_array('R', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-profile" href="{{route('user.page.karyawan.profile', '')}}/${data}?menu_id=${currentMenuId}"
                    ><i class="bx bxs-user-detail me-1 text-primary"></i> Profil</a
                  >
                <?php endif; ?>
                <?php if (in_array('U', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-edit" href="{{route('user.page.karyawan.edit', '')}}/${data}?menu_id=${currentMenuId}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Non Aktifkan</a
                  >
                <?php endif; ?>
                </div>
              </div>
                `
          }
        },
      ],
    });

    $("#table-karyawan").on("click", ".btn-edit", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    $("#table-karyawan").on("click", ".btn-profile", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    // non aktifkan karyawan
    $("#table-karyawan").on("click", ".btn-delete", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblKaryawan.row(row).data();
      Swal.fire({
        html: `Apakah anda ingin menonaktifkan karyawan <b>${data.karyawan_name}</b>?
        <div class="mt-3 text-left">
          <div class="row mb-3">
            <label class="col-sm-5">Tgl. Nonaktif<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <input class="form-control" name="nonaktif_tgl" id="nonaktif_tgl" placeholder="Tgl. Nonaktif" />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Alasan<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <select class="form-control" name="nonaktif_alasan" style="width:100%" id="nonaktif_alasan" data-placeholder="-: Pilih Data :-">
                <option value="RESIGN" selected>Berhenti Kerja</option>
                <option value="KONTRAK_HABIS">Kontrak Habis</option>
                <option value="LAINNYA">Lainnya</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Keterangan</label>
            <div class="col-sm-7">
              <textarea class="form-control"name="nonaktif_keterangan" id="nonaktif_keterangan" placeholder="Keterangan"></textarea>
            </div>
          </div>
        </div>
        `,
        icon: 'question',
        didOpen: function() {
          $("#nonaktif_tgl").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: moment().format('DD-MM-YYYY'),
            locale: {
              format: 'DD-MM-YYYY'
            },
          });

          $("#nonaktif_alasan").select2({
            dropdownParent: $('#swal2-html-container')
          }).on("select2:select", function(e) {
            let data = e.params.data;
            // console.log('data', data);
            // if(data.id == 'LAINNYA') {
            //   $("#nonaktif_keterangan").removeAttr("disabled");
            // } else {
            //   $("#nonaktif_keterangan").val('');
            //   $("#nonaktif_keterangan").attr("disabled", true);
            // }
          });
        },
        preConfirm: () => {
          Swal.showLoading();
          let nonaktif_tgl = $('#nonaktif_tgl').val();
          let nonaktif_alasan = $('#nonaktif_alasan').val();
          let nonaktif_keterangan = $('#nonaktif_keterangan').val();

          if (!nonaktif_tgl || !nonaktif_alasan) {
            toastr.error('Silahkan isi data!');
            return false;
          }
          // if(nonaktif_alasan == 'LAINNYA') {
          //   if(!nonaktif_keterangan) {
          //     toastr.error('Silahkan isi keterangan!');
          //     return false;
          //   }
          // }
          return fetch(`{{route('user.page.karyawan.delete', '')}}/${data.karyawan_id}?menu_id=${currentMenuId}`, {
              method: 'POST',
              body: new URLSearchParams($.param({
                _token: $("meta[name=csrf-token]").attr('content'),
                nonaktif_tgl: nonaktif_tgl,
                nonaktif_alasan: nonaktif_alasan,
                nonaktif_keterangan: nonaktif_keterangan,
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
            .then(jsondata => {
              if (!jsondata.success) {
                toastr.error(jsondata.message);
                return false;
              }
              return jsondata;
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

          Swal.fire({
            title: result.message,
            confirmButtonText: "Ok",
            showCancelButton: false,
            icon: 'error'
          })
        }

        toastr.success(result.message);
        tblKaryawan.draw();
      });
    })

    // Begin Impor Karyawan
    $("#btn-import").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("show");
    })

    $("#btn-import-file").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("hide");
      $("#modalImporKaryawan").modal("show");
    })

    $("#closeImpor").click(function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
      $("#modalImporKaryawan").modal("hide");
    })



    let formKaryawanImporProfil = $("#formKaryawanImporProfil").validate({
      errorPlacement: function(error, element) {
        // console.log(element);
        var isInputGroup = $(element).parent();
        console.log('element', element)
        let elem = $(element);
        console.log('elem', elem)
        if (elem.hasClass("select2-hidden-accessible")) {
          // element = $("#select2-" + elem.attr("id") + "-container").parent(); 
          element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container');
          error.insertAfter(element);
        } else {
          if (isInputGroup.hasClass('input-group')) {
            // $(element).parent('.input-group').insertAfter(error)
            error.insertAfter($(element).parent('.input-group'));
          } else {
            error.insertAfter(element);
          }
        }
      },
      rules: {},
      submitHandler: function(form) {
        $(".spinner-box").css({
          'display': 'table'
        });
        let input = document.getElementById('file_import');

        let formData = new FormData();
        // return false;
        if (input.files.length < 1) {
          toastr.error('Please upload xls/csv');
          return false;
        }
        formData.append('_token', $("meta[name=csrf-token]").attr('content'));
        formData.append('file', input.files[0]);
        $.ajax({
          method: form.method,
          url: form.action,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          },
          processData: false, // tell jQuery not to process the data
          contentType: false, // tell jQuery not to set contentType
          data: formData,
          error: function(error) {
            $(".spinner-box").fadeOut();
            if (error.responseJSON) {
              let errs = error.responseJSON.errors;
              let errorName = [];
              if (errs) {
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
                icon: 'error',
                html: errorName
              })
            }
          },
          success: function(response) {
            console.log(response, 'response')
            $(".spinner-box").fadeOut();
            if (!response.success) {
              toastr.error(response.message);
              return false;
            }

            toastr.success(response.message);

            $("#modalImporKaryawan").modal("hide");
            tblKaryawan.draw()
          }
        })
      },
    })
    // End Impor Karyawan

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>