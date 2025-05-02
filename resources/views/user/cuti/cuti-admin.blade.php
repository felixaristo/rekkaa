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
            <h5>Daftar Cuti Karyawan</h5>
          </div>
          <div class="col-sm-6 d-flex justify-content-end">
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
        <table class="table table-hover display nowrap" style="width: 100%" id="table-cuti-karyawan">
          <thead>
            <tr>
              <th>Nama Karyawan</th>
              <th>Jenis Cuti</th>
              <th>Tanggal Pengajuan</th>
              <th>Tanggal Mulai Cuti</th>
              <th>Tanggal Berakhir Cuti</th>
              <th>Status</th>
              <th>Tanggal Persetujuan</th>
              <th>Penanggung Jawab</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="historyModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Histori Impor Cuti</h5>
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

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Cuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <input type="hidden" id="leavekaryawan_id" name="leavekaryawan_id" value="">
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="nama-karyawan"><em>Nama Karyawan</em></label>
            <p id="nama-karyawan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="jenis-cuti"><em>Jenis Cuti</em></label>
            <p id="jenis-cuti" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-pengajuan"><em>Tanggal Pengajuan</em></label>
            <p id="tanggal-pengajuan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-mulai-cuti"><em>Tanggal Mulai Cuti</em></label>
            <p id="tanggal-mulai-cuti" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="tanggal-berakhir-cuti"><em>Tanggal Berakhir Cuti</em></label>
            <p id="tanggal-berakhir-cuti" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="alasan-pengajuan"><em>Alasan Cuti</em></label>
            <p id="alasan-pengajuan" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="status"><em>Status</em></label>
            <p id="status" class="font-weight-bold"></p>
          </div>
          <div class="form-group">
            <label for="keputusan-penerimaan"><em>Tanggal Persetujuan</em></label>
            <p id="keputusan-penerimaan" class="font-weight-bold"></p>
          </div>
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
              *) Sebelum mengimpor Data Cuti Karyawan, anda harus mengunduh Templat Cuti
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Cuti Karyawan yang akan di impor dengan Templat Cuti sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Cuti</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Impor -->
<div class="modal fade" id="modalImporFile" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Impor Data Cuti</h5>
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

          <!-- Progress bar container -->
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
              <th>Kode Cuti</th>
              <th>Jenis Cuti</th>
              <th>Tanggal Pengajuan</th>
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

<!-- Add your JavaScript code here -->
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
      url: "{{ route('user.cuti.generate-history') }}?menu_id={{request()->get('menu_id')}}", // Concatenate the 'id' to the URL
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
    $("#btn-import").click(function(e) {
      e.preventDefault();
      $(".spinner-box").css({
        display: "table"
      });

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      $.ajax({
        type: 'GET', // or 'GET' depending on your controller action
        url: `{{ route("user.cuti.import-handler") }}?menu_id=${currentMenuId}`,
        headers: {
          "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success === false) {
            Swal.fire({
              icon: 'warning',
              title: 'Akses Ditolak',
              html: 'Mohon daftarkan karyawan atau buat jenis cuti terlebih dahulu.',
              showCancelButton: false,
              confirmButtonText: 'Oke'
            });
          } else {
            $("#importModal").modal("hide");
            $("#modalImporFile").modal("show");
          }

          $(".spinner-box").fadeOut()
        },
        error: function(error) {
          toastr.error("Ada kendala untuk Import data. Mohon hubungi Customer Service.");

          $(".spinner-box").fadeOut()
        }
      });
    })

    $("#closeImpor").click(function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
      $("#modalImporFile").modal("hide");
    })

    let tblDetailImport = $("#table-detail").DataTable({
      "searching": false,
      "language": {
        // "searchPlaceholder": "Cari Nama Karyawan",
        // "emptyTable": "Tidak ada data"
      },
      "lengthChange": false,
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
        target: [0, 1, 2, 3, 4, 5, 6, 7],
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
          url: `{{ route("user.cuti.import-cuti") }}?menu_id=${currentMenuId}`,
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
            // Hide the progress container

            setTimeout(function() {
              if (response.success === false) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Gagal Impor',
                  html: 'Cek file anda atau hubungi Customer Service',
                  showCancelButton: false,
                  confirmButtonText: 'Oke'
                });
              } else {
                $('#modalImporFile').modal('hide');
                input.value = "";
                $('.progress-container').hide();

                tblDetailImport.clear().draw();;

                $.each(response.result, function(index, item) {
                  tblDetailImport.row.add([
                    item.row, // Row number
                    item.karyawan_id, // Id Karyawan
                    item.karyawan_name ? item.karyawan_name : null, // Nama Karyawan
                    `CU${item.leave_id}`, // Id Karyawan
                    item.leave_description ? item.leave_description : null, // Nama Karyawan
                    item.leavekaryawan_request_date, // Data Attendance
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

                $('#detailImportModal').modal('show');

                tblCuti.draw()
              }
            }, 100);

            $(".progress-bar").css("width", "0%");
          },
          error: function(error) {
            Swal.fire({
              icon: 'warning',
              title: 'Gagal Impor',
              html: 'Cek file anda atau hubungi Customer Service',
              showCancelButton: false,
              confirmButtonText: 'Oke'
            });

            $('#importButton').prop('disabled', false);
            $('#importButton').css('background-color', '');
            $('#importButton').css('color', '');
          }
        });
      }, 1800);
    })

    $("#btn-template").on("click", function() {
      $(".spinner-box").css({
        display: "table"
      });
      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      // Make the POST request to your API endpoint
      $.ajax({
        url: `{{ route('user.cuti.import-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
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

    $(".import-button-container button").on("click", function() {
      $("#importModal").modal("show");
    });

    let selectedEmployeeId = [];
    $("#statusfilter").select2();
    $("#listEmployee").select2({
      // dropdownParent: $("#formAddCuti"),
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

    $("#listEmployee").on("select2:select", function(e) {
      const selectedId = parseInt(e.params.data.id);

      if (!selectedEmployeeId.includes(selectedId)) {
        selectedEmployeeId.push(selectedId);
      }
    });

    $("#listEmployee").on("select2:unselect", function(e) {
      const unselectedId = parseInt(e.params.data.id);

      const selectedIndex = selectedEmployeeId.indexOf(unselectedId);
      if (selectedIndex > -1) {
        selectedEmployeeId.splice(selectedIndex, 1);
      }
    });

    const currentDate = new Date();

    // Calculate the first day of the current month
    const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Calculate the last day of the current month
    const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    // Convert the calculated dates to the 'DD-MM-YYYY' format
    const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
    const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;

    $(`#start-date`).daterangepicker({
      singleDatePicker: false,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedLastDay,
    });

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
        "url": `{{ route('user.cuti.import-history') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
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
      $(".spinner-box").css({
        display: "table"
      });
      tblHistory.draw();
      e.preventDefault();
      // $("#importModal").modal("hide");
      // $("#historyModal").modal("show");
      $("#importModal").modal("hide");
      $("#historyModal").modal("show");
      $(".spinner-box").fadeOut();
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

    $("#searchButton").on("click", function() {
      tblCuti.draw();
    });

    $("#resetButton").on("click", function() {
      $("#start-date").val(`${formattedFirstDay} - ${formattedLastDay}`);
      $('#start-date').data('daterangepicker').setStartDate(formattedFirstDay);
      $('#start-date').data('daterangepicker').setEndDate(formattedLastDay);
      $("#listEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
      $("#statusfilter").val(''); // Remove all options from the select2 dropdown
      selectedEmployeeId = [];
      tblCuti.draw();
    });

    let tblCuti = $("#table-cuti-karyawan").DataTable({
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
        "url": `{{ route('user.cuti.list.admin') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          // Add any additional data you want to pass to the server here
          const dates = $('#start-date').val().split(' - ');
          data.status = $('#status').val()
          data.start_date = dates[0]
          data.end_date = dates[1]
          data.karyawan = selectedEmployeeId
          data.page = data.start / data.length + 1; // Calculate the current page based on start and length
          data.per_page = data.length; // Set the number of records per page
          // data.karyawan_name = $("#table-cuti-karyawan_filter input").val();
        },
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "karyawan_name",
          "title": "Nama Karyawan",
          "className": "text-center",
        },
        {
          "data": "leave_description",
          "title": "Jenis Cuti",
          "className": "text-center",
        },
        {
          "data": "leavekaryawan_request_date",
          "title": "Tanggal Pengajuan",
          "className": "text-center",
        },
        {
          "data": "leavekaryawan_start_date",
          "title": "Tanggal Mulai Cuti",
          "className": "text-center",
        },
        {
          "data": "leavekaryawan_end_date",
          "title": "Tanggal Berakhir Cuti",
          "className": "text-center",
        },
        {
          "data": "leavekaryawan_status",
          "title": "Status",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === "display" || type === "filter")) {
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

            return data; // For sorting and other purposes, return the original data as it is
          }
        },
        {
          "data": "leavekaryawan_approval_date",
          "title": "Tanggal Persetujuan",
          "className": "text-center"
        },
        {
          "data": "manager_name",
          "title": "Penanggung Jawab",
          "className": "text-center",
        },
        {
          "data": null,
          "title": "Aksi",
          "className": "text-center",
          "orderable": false,
          "render": function(data, type, row) {
            if (row.leavekaryawan_status === 'WAITING' || row.leavekaryawan_status === 'WAITINGCANCEL') {
              return `
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item detail-button" href="javascript:void(0);">
                            <i class="bx bx-show me-1 text-info"></i> Selengkapnya
                        </a>
                        <a class="dropdown-item reject-button" href="javascript:void(0);">
                            <i class="bx bx-block me-1 text-danger"></i> Tidak Disetujui
                        </a>
                        <a class="dropdown-item approve-button" href="javascript:void(0);">
                            <i class="bx bx-check me-1 text-success"></i> Disetujui
                        </a>
                    </div>
                </div>`;
            } else {
              return `
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item detail-button" href="javascript:void(0);">
                            <i class="bx bx-show me-1 text-info"></i> Selengkapnya
                        </a>
                    </div>
                </div>`;
            }
          },
        },
        {
          "data": "leavekaryawan_id",
        },
        {
          "data": "leavekaryawan_cancel_note",
        },
        {
          "data": "approval_name"
        }
      ],
      "columnDefs": [{
          "targets": [9, 10, 11], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
        {
          "targets": [8],
          "orderable": true,
        },
      ]
    });

    // $("#start-date").on("apply.daterangepicker", function () {
    //     tblCuti.draw();
    // });

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
        return "Menunggu Pembatalan"
      }
      return "Unknown";
    }

    function formatDate(date) {
      const year = date.getFullYear();
      const month = (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1);
      const day = (date.getDate() < 10 ? '0' : '') + date.getDate();

      return `${year}-${month}-${day}`;
    }

    $("#table-cuti-karyawan").on("click", ".approve-button", function() {
      const rowData = tblCuti.row($(this).closest("tr")).data();

      const cutiId = rowData.leavekaryawan_id;
      const status = rowData.leavekaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if (status !== 'WAITING' && status !== 'WAITINGCANCEL') {
        Swal.fire({
          html: 'Tidak dapat mengubah cuti yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        });
        return false;
      }

      Swal.fire({
        html: rowData.leavekaryawan_status === 'WAITINGCANCEL' ? 'Apakah anda ingin mengizinkan pembatalan cuti ini?' : 'Apakah anda ingin mengizinkan cuti ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
          Swal.showLoading();
          const currentDate = new Date();

          const requestData = {
            _token: csrfToken,
            isEdit: true,
            leavekaryawan_id: cutiId,
            leavekaryawan_status: rowData.leavekaryawan_status === 'WAITINGCANCEL' ? "CANCEL" : "APPROVED", // Corrected status
            leavekaryawan_type: "APPROVAL"
          };

          if (rowData.leavekaryawan_status !== 'WAITINGCANCEL') {
            requestData.leavekaryawan_approval_date = formatDate(currentDate);
          }

          try {
            const response = await fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
              method: 'POST',
              body: JSON.stringify(requestData),
              headers: {
                'Content-Type': 'application/json'
              }
            });

            if (!response.ok) {
              throw new Error(await response.text());
            }

            Swal.close(); // Close the modal
            tblCuti.ajax.reload();
            return response.json();
          } catch (error) {
            Swal.showValidationMessage(`Request failed: ${error}`);
            throw error;
          }
        },
        allowOutsideClick: () => false
      }).then((result) => {
        console.log('result', result);
        result = result.value;
        if (result == undefined) {
          return false;
        }

        if (!result.success) {
          
          Swal.fire({
            title: result.message,
            confirmButtonText: "Ok",
            type: 'error'
          });
          return false;
        }

        toastr.success(result.message);
      });
    });

    $("#detailModal").on("click", ".btn-approve", function(e) {
      e.preventDefault();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      const jsonData = {}; // To store the JSON data

      jsonData["leavekaryawan_id"] = $("#leavekaryawan_id").val();
      jsonData["leavekaryawan_status"] = $("#status").val();

      console.log(jsonData, 'json');

      const status = jsonData["leavekaryawan_status"];

      if (status !== 'WAITING' && status !== 'WAITINGCANCEL') {
        Swal.fire({
          html: 'Tidak dapat mengubah cuti yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        });
        return;
      }

      Swal.fire({
        html: status === 'WAITINGCANCEL' ? 'Apakah anda ingin mengizinkan pembatalan cuti ini?' : 'Apakah anda ingin mengizinkan cuti ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
          Swal.showLoading();
          const currentDate = new Date();

          const requestData = {
            _token: csrfToken,
            isEdit: true,
            leavekaryawan_id: jsonData["leavekaryawan_id"],
            leavekaryawan_status: status === 'WAITINGCANCEL' ? "CANCEL" : "APPROVED",
            leavekaryawan_type: "APPROVAL"
          };

          if (jsonData["leavekaryawan_status"] !== 'WAITINGCANCEL') {
            requestData.leavekaryawan_approval_date = formatDate(currentDate);
          }

          try {
            const response = await fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
              method: 'POST',
              body: JSON.stringify(requestData),
              headers: {
                'Content-Type': 'application/json'
              }
            });

            console.log(response, 'response')

            if (!response.ok) {
              throw new Error(await response.text());
            }

            Swal.close(); // Close the modal
            tblCuti.ajax.reload();
            return response.json();
          } catch (error) {
            Swal.showValidationMessage(`Request failed: ${error}`);
            throw error;
          }
        },
        allowOutsideClick: () => false
      }).then((result) => {
        if (!result.value) {
          return;
        }

        if (!result.value.success) {
          Swal.fire({
            title: result.value.message,
            confirmButtonText: "Ok",
            type: 'error'
          });
          return;
        }

        toastr.success(result.value.message);
        $("#detailModal").modal("hide"); // Close the modal
      });
    });


    $("#detailModal").on("click", ".btn-reject", function(e) {
      e.preventDefault();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      const jsonData = {}; // To store the JSON data

      jsonData["leavekaryawan_id"] = $("#leavekaryawan_id").val();
      jsonData["leavekaryawan_status"] = $("#status").val();

      console.log(jsonData, 'json');

      const status = jsonData["leavekaryawan_status"];

      if (status !== 'WAITING' && status !== 'WAITINGCANCEL') {
        Swal.fire({
          html: 'Tidak dapat mengubah Cuti yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: status === 'WAITINGCANCEL' ? 'Apakah anda tidak mengizinkan pembatalan cuti ini?' : 'Apakah anda tidak mengizinkan cuti ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
          if (status === 'WAITING') {
            const swalResult = await Swal.fire({
              html: 'Masukan alasan anda tidak mengizinkan',
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

              const currentDate = new Date();

              const requestData = {
                _token: csrfToken,
                isEdit: true,
                leavekaryawan_id: jsonData["leavekaryawan_id"],
                leavekaryawan_status: "REJECTED",
                leavekaryawan_type: "APPROVAL",
                leavekaryawan_approval_date: formatDate(currentDate),
                leavekaryawan_approval_note: reason
              };

              return fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
                  method: 'POST',
                  body: JSON.stringify(requestData),
                  headers: {
                    'Content-Type': 'application/json'
                  }
                })
                .then(response => {
                  Swal.close(); // Close the modal

                  if (!response.ok) {
                    return response.text().then(res => {
                      throw new Error(res);
                    })
                  }
                  tblCuti.ajax.reload();

                  return response.json();
                })
                .catch(error => {
                  Swal.showValidationMessage(`Request failed: ${error}`);
                });
            }
          } else {
            Swal.showLoading();

            const currentDate = new Date();

            const requestData = {
              _token: csrfToken,
              isEdit: true,
              leavekaryawan_id: jsonData["leavekaryawan_id"],
              leavekaryawan_status: "APPROVED",
              leavekaryawan_type: "APPROVAL"
            };

            return fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
                method: 'POST',
                body: JSON.stringify(requestData),
                headers: {
                  'Content-Type': 'application/json'
                }
              })
              .then(response => {
                Swal.close(); // Close the modal

                if (!response.ok) {
                  return response.text().then(res => {
                    throw new Error(res);
                  })
                }
                tblCuti.ajax.reload();

                return response.json();
              })
              .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
              });
          }
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
            type: 'error'
          })
          return false;
        }

        toastr.success(result.message);
      });
    });

    $("#table-cuti-karyawan").on("click", ".reject-button", function() {
      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const cutiId = rowData.leavekaryawan_id;
      const status = rowData.leavekaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if (status !== 'WAITING' && status !== 'WAITINGCANCEL') {
        Swal.fire({
          html: 'Tidak dapat mengubah Cuti yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: rowData.leavekaryawan_status === 'WAITINGCANCEL' ? 'Apakah anda tidak mengizinkan pembatalan cuti ini?' : 'Apakah anda tidak mengizinkan cuti ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
          if (rowData.leavekaryawan_status === 'WAITING') {
            const swalResult = await Swal.fire({
              html: 'Masukan alasan anda tidak mengizinkan',
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

              const currentDate = new Date();

              const requestData = {
                _token: csrfToken,
                isEdit: true,
                leavekaryawan_id: cutiId,
                leavekaryawan_status: "REJECTED",
                leavekaryawan_type: "APPROVAL",
                leavekaryawan_approval_date: formatDate(currentDate),
                leavekaryawan_approval_note: reason
              };

              return fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
                  method: 'POST',
                  body: JSON.stringify(requestData),
                  headers: {
                    'Content-Type': 'application/json'
                  }
                })
                .then(response => {
                  Swal.close(); // Close the modal

                  if (!response.ok) {
                    return response.text().then(res => {
                      throw new Error(res);
                    })
                  }
                  tblCuti.ajax.reload();

                  return response.json();
                })
                .catch(error => {
                  Swal.showValidationMessage(`Request failed: ${error}`);
                });
            }
          } else {
            Swal.showLoading();

            const currentDate = new Date();

            const requestData = {
              _token: csrfToken,
              isEdit: true,
              leavekaryawan_id: cutiId,
              leavekaryawan_status: "APPROVED",
              leavekaryawan_type: "APPROVAL"
            };

            return fetch(`{{ route('user.cuti.pengajuan') }}?menu_id=${currentMenuId}`, {
                method: 'POST',
                body: JSON.stringify(requestData),
                headers: {
                  'Content-Type': 'application/json'
                }
              })
              .then(response => {
                Swal.close(); // Close the modal

                if (!response.ok) {
                  return response.text().then(res => {
                    throw new Error(res);
                  })
                }
                tblCuti.ajax.reload();

                return response.json();
              })
              .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
              });
          }
        },
        allowOutsideClick: () => false
      }).then((result) => {
        console.log('result', result)
        result = result.value;
        if (result == undefined) {
          return false;
        }

        if (!result.success) {

          if(result.message) {
            Swal.fire({
              title: result.message,
              confirmButtonText: "Ok",
              icon: 'error'
            })
          }
          return false;
        }

        toastr.success(result.message);
      });
    });

    $("#table-cuti-karyawan").on("click", ".detail-button", function() {
      const rowData = tblCuti.row($(this).closest("tr")).data();



      // Populate the paragraph elements with the row data
      $("#detailModal #nama-karyawan").text(rowData.karyawan_name);
      $("#detailModal #jenis-cuti").text(rowData.leave_description);
      $("#detailModal #tanggal-pengajuan").text(rowData.leavekaryawan_request_date);
      $("#detailModal #alasan-pengajuan").text(rowData.leavekaryawan_request_note);
      $("#detailModal #tanggal-mulai-cuti").text(rowData.leavekaryawan_start_date);
      $("#detailModal #tanggal-berakhir-cuti").text(rowData.leavekaryawan_end_date);
      $("#detailModal #status").text(getStatusText(rowData.leavekaryawan_status));
      $("#detailModal #keputusan-penerimaan").text(rowData.leavekaryawan_approval_date ? rowData.leavekaryawan_approval_date : "-");

      // Populate and show/hide "Alasan Dibatalkan" field based on the status
      if (rowData.leavekaryawan_status === "CANCEL" || rowData.leavekaryawan_status === "WAITINGCANCEL") {
        $("#detailModal .alasan-dibatalkan-field").show();
        $("#detailModal #alasan-dibatalkan").text(rowData.leavekaryawan_cancel_note);
      } else {
        $("#detailModal .alasan-dibatalkan-field").hide();
      }

      if (rowData.leavekaryawan_status === "REJECTED") {
        $("#detailModal .alasan-ditolak-field").show();
        $("#detailModal #alasan-ditolak").text(rowData.leavekaryawan_approval_note);
        $("#detailModal .dibatalkan-oleh-field").show();
        $("#detailModal #dibatalkan-oleh").text(rowData.approval_name);
      } else {
        $("#detailModal .alasan-ditolak-field").hide();
        $("#detailModal .dibatalkan-oleh-field").hide();
      }

      if (rowData.leavekaryawan_status === "APPROVED") {
        $("#detailModal .disetujui-oleh-field").show();
        $("#detailModal #disetujui-oleh").text(rowData.approval_name);
      } else {
        $("#detailModal .disetujui-oleh-field").hide();
      }

      $("#leavekaryawan_id").val(rowData.leavekaryawan_id);
      $("#status").val(rowData.leavekaryawan_status);

      // Show the modal
      $("#detailModal").modal("show");
    });

    // $('#start-date, #end-date').on('change', function() {
    //   tblCuti.draw(); // Redraw the table to apply filtering
    // });

    $(".export-button-container button").on("click", function() {
      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      // Get input values
      const dates = $('#start-date').val().split(' - ');
      const startDate = dates[0];
      const endDate = dates[1];

      // Create the data object for the POST request
      const data = {
        start_date: startDate || null,
        end_date: endDate || null,
        status: $('#status').val()
      };

      if (selectedEmployeeId.length > 0) {
        data.karyawan = selectedEmployeeId
      }

      // Make the POST request to your API endpoint
      $.ajax({
        url: `{{ route('user.cuti.admin.export') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        data: JSON.stringify(data),
        contentType: "application/json",
        success: function(response) {
          window.open(response, '_blank')
          // window.close()
        },
        error: function(error) {
          // Handle errors, if any
          console.error(error);
        },
      });
    });

    // $.fn.dataTable.ext.search.push(
    //   function(settings, data, dataIndex) {
    //     const startDate = new Date($('#start-date').val());
    //     const endDate = new Date($('#end-date').val());
    //     const dateStr = data[0]; // Assuming "Date" column is the first column in your data

    //     if (startDate == '' && endDate == '') {
    //       return true; // No filtering if both dates are empty
    //     }

    //     const currentDate = new Date(dateStr);
    //     if (startDate != '' && currentDate < startDate) {
    //       return false;
    //     }
    //     if (endDate != '' && currentDate > endDate) {
    //       return false;
    //     }

    //     return true;
    //   }
    // );

    setHtmlTitle('{{$title}}')
  });
</script>