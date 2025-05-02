<head>
  <style>
    #start-date,
    #end-date {
      width: 100%;
      /* Adjust the width as needed */
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
            <label for="start-date">Tanggal Awal</label>
            <input type="text" id="start-date" name="start-date" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3">
            <label for="end-date">Tanggal Akhir</label>
            <input type="text" id="end-date" name="end-date" class="form-control" autocomplete="off">
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
            <h5>Daftar Absensi Karyawan</h5>
          </div>
          <div class="col-sm-6 d-flex justify-content-end">
            <!-- "Ekspor" button -->
            @if(in_array('IMPORT', request()->get('permission_codes')))
            <div class="import-button-container me-2">
              <button type="button" class="btn btn-sm btn-outline-info"><i class='bx bxs-cloud-upload'></i> Impor</button>
            </div>
            @endif
            @if(in_array('EXPORT', request()->get('permission_codes')))
            <div class="export-button-container">
              <button type="button" class="btn btn-sm btn-outline-warning"><i class='bx bxs-file-export'></i> Ekspor</button>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-kehadiran-karyawan">
          <thead>
            <tr>
              <th>Nama Karyawan</th>
              <th>Tanggal</th>
              <th>Masuk</th>
              <th>Mulai Istirahat</th>
              <th>Selesai Istirahat</th>
              <th>Keluar</th>
              <th>Remark</th>
              <th>Penanggung Jawab</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="historyModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="historyModalLabel">Histori Impor Absensi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeHistori"></button>
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
              <button id="searchButtonHistori" class="btn btn-search btn-outline-warning me-2 btn-sm">
                <i class="bx bx-search-alt"></i> Cari
              </button>
              <button id="resetButtonHistori" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
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

  <!-- Detail Modal -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Detail Kehadiran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <input type="hidden" id="attendancekaryawan_id" name="attendancekaryawan_id" value="">
        <input type="hidden" id="leavekaryawan_id" name="leavekaryawan_id" value="">
        <div class="modal-body">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" id="detailTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="checkInTab" data-bs-toggle="tab" data-bs-target="#checkIn" type="button" role="tab" aria-controls="checkIn" aria-selected="true">Check In</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="startBreakTab" data-bs-toggle="tab" data-bs-target="#startBreak" type="button" role="tab" aria-controls="startBreak" aria-selected="false">Mulai Istirahat</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="endBreakTab" data-bs-toggle="tab" data-bs-target="#endBreak" type="button" role="tab" aria-controls="endBreak" aria-selected="false">Selesai Istirahat</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="checkOutTab" data-bs-toggle="tab" data-bs-target="#checkOut" type="button" role="tab" aria-controls="checkOut" aria-selected="false">Keluar</button>
            </li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content" id="detailTabContent">
            <!-- Tab 1: Check In -->
            <div class="tab-pane fade show active" id="checkIn" role="tabpanel" aria-labelledby="checkInTab">
              <!-- Check In details content goes here -->
            </div>

            <!-- Tab 2: Mulai Istirahat -->
            <div class="tab-pane fade" id="startBreak" role="tabpanel" aria-labelledby="startBreakTab">
              <!-- Mulai Istirahat details content goes here -->
            </div>

            <!-- Tab 3: Selesai Istirahat -->
            <div class="tab-pane fade" id="endBreak" role="tabpanel" aria-labelledby="endBreakTab">
              <!-- Selesai Istirahat details content goes here -->
            </div>

            <!-- Tab 4: Keluar -->
            <div class="tab-pane fade" id="checkOut" role="tabpanel" aria-labelledby="checkOutTab">
              <!-- Keluar details content goes here -->
            </div>
          </div>
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
                *) Sebelum mengimpor Data Absen Karyawan, anda harus mengunduh Templat Absen
              </div>
            </div>
          </div>
          <div class="row mb-1">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                *) Jika ada Data Absen Karyawan dengan tanggal yang sama, maka akan memperbarui data lama
              </div>
            </div>
          </div>
          <div class="row mb-1">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                *) Sesuaikan Data Absen Karyawan yang akan di impor dengan Templat Absen milik REKKAA
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
          <div class="ms-auto">
            <button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
            <button type="button" id="btn-import" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Absensi</button>
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
          <h5 class="modal-title" id="modalCenterTitle">Impor Data Absen</h5>
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
                <th>Tanggal Absen</th>
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
        url: `{{ route('user.kehadiran.generate-history') }}?menu_id={{request()->get('menu_id')}}`, // Concatenate the 'id' to the URL
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
        },
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function(response) {
          setTimeout(function() {
            // let w = window.open("data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64, " + response);
            // w.document.title = 'dooooo';

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
      let selectedEmployeeId = [];
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
          target: [0, 1, 2, 3, 4, 5],
          className: 'text-center'
        }],
      })

      const currentDate = new Date();

      // Calculate the first day of the current month
      const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

      // Calculate the last day of the current month
      const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

      // Convert the calculated dates to the 'DD-MM-YYYY' format
      const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
      const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;


      $(`#start-date, #start-date-history`).daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
          format: 'DD-MM-YYYY'
        },
        startDate: formattedFirstDay, // Set the calculated first day as the start date
        endDate: formattedFirstDay,
      });

      $(`#end-date, #end-date-history`).daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
          format: 'DD-MM-YYYY'
        },
        startDate: formattedLastDay, // Set the calculated first day as the start date
        endDate: formattedLastDay,
      });

      $("#btn-import").click(function(e) {
        e.preventDefault();
        $(".spinner-box").css({
          display: "table"
        });

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
          type: 'GET', // or 'GET' depending on your controller action
          url: `{{ route("user.kehadiran.import-handler") }}?menu_id=${currentMenuId}`,
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
                html: 'Mohon daftarkan karyawan atau buat jadwal absensi terlebih dahulu.',
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

      var isFileDialogOpen = false;
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
            url: `{{ route("user.kehadiran.import-absen") }}?menu_id=${currentMenuId}`,
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
                      item.attendancekaryawan_date, // Data Attendance
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

                  tblKehadiran.draw()
                }
              }, 100);

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
      });

      $("#btn-import-history").on("click", function(e) {
        $(".spinner-box").css({
          display: "table"
        });
        tblHistori.draw();
        e.preventDefault();
        $("#importModal").modal("hide");
        $("#historyModal").modal("show");
        $(".spinner-box").fadeOut();
      });

      $("#closeHistori").click(function(e) {
        e.preventDefault();
        $("#importModal").modal("show");
        $("#historyModal").modal("hide");
        $(`#start-date-history`).val(formattedFirstDay);
        $(`#end-date-history`).val(formattedLastDay);
      })

      $("#btn-template").on("click", function() {
        $(".spinner-box").css({
          display: "table"
        });
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Make the POST request to your API endpoint
        $.ajax({
          url: `{{ route('user.kehadiran.import-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
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

      $("#searchButton").on("click", function() {
        tblKehadiran.draw();
      });

      $("#searchButtonHistori").on("click", function() {
        tblHistori.draw();
      });

      $("#resetButton").on("click", function() {
        $(`#start-date`).val(formattedFirstDay);
        $(`#end-date`).val(formattedLastDay);
        $("#listEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
        selectedEmployeeId = [];
        tblKehadiran.draw();
      });

      $("#resetButtonHistori").on("click", function() {
        $(`#start-date-history`).val(formattedFirstDay);
        $(`#end-date-history`).val(formattedLastDay);
        tblHistori.draw();
      });

      let tblHistori = $("#table-history").DataTable({
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
          "url": `{{ route('user.kehadiran.import-history') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
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

      let tblKehadiran = $("#table-kehadiran-karyawan").DataTable({
        // DataTable configuration options
        "searching": false,
        "language": {
          "searchPlaceholder": "Cari Nama Karyawan",
          // "emptyTable": "Tidak ada data"
        },
        "processing": true,
        "serverSide": true,
        //   "paging": true, // Enable pagination
        "lengthMenu": [10, 25, 50], // Set number of records to display per page
        "info": false,
        "ordering": true,
        "ajax": {
          "url": `{{ route('user.kehadiran.list.admin') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
          "type": "GET",
          "data": function(data) {
            // Add any additional data you want to pass to the server here
            data.start_date = $('#start-date').val() ? moment($('#start-date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
            data.end_date = $('#end-date').val() ? moment($('#end-date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
            data.page = data.start / data.length + 1; // Calculate the current page based on start and length
            data.per_page = data.length; // Set the number of records per page
            data.karyawan = selectedEmployeeId
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
            "data": "attendancekaryawan_check_in",
            "title": "Tanggal",
            "className": "text-center",
            "render": function(data, type, row) {
              if (data && (type === "display" || type === "filter")) {
                return data.substring(0, 10); // Display only the date part (YYYY-MM-DD)
              }
              return data; // For sorting and other purposes, return the original data as it is
            }
          },
          {
            "data": "attendancekaryawan_check_in",
            "title": "Masuk",
            "className": "text-center",
            "render": function(data, type, row) {
              if (data && (type === "display" || type === "filter")) {
                const lateData = row.attendancekaryawan_check_in_late;
                if (lateData !== '00:00' && lateData !== '' && lateData !== null) {
                  return formatTimeToHHmm(data) + ' (<span style="color: red;">' + lateData + '</span>)';
                } else {
                  return formatTimeToHHmm(data);
                }
              }
              return data; // For sorting and other purposes, return the original data as it is
            }
          },
          {
            "data": "attendancekaryawan_break_start",
            "title": "Mulai Istirahat",
            "className": "text-center",
            "render": function(data, type, row) {
              if (data && (type === "display" || type === "filter")) {
                const lateData = row.attendancekaryawan_break_start_late;
                const earlyData = row.attendancekaryawan_break_start_early;
                if (lateData !== '00:00' && lateData !== '' && lateData !== null) {
                  return formatTimeToHHmm(data) + ' (<span style="color: orange;">' + lateData + '</span>)';
                } else if (earlyData !== '00:00' && earlyData !== '' && earlyData !== null) {
                  return formatTimeToHHmm(data) + ' (<span style="color: red;">' + earlyData + '</span>)';
                } else {
                  return formatTimeToHHmm(data); // Display only HH:mm
                }
              }
              return data; // For sorting and other purposes, return the original data as it is
            }
          },
          {
            "data": "attendancekaryawan_break_end",
            "title": "Selesai Istirahat",
            "className": "text-center",
            "render": function(data, type, row) {
              if (data && (type === "display" || type === "filter")) {
                const lateData = row.attendancekaryawan_break_end_late;
                if (lateData !== '00:00' && lateData !== '' && lateData !== null) {
                  return formatTimeToHHmm(data) + ' (<span style="color: red;">' + lateData + '</span>)';
                } else {
                  return formatTimeToHHmm(data);
                }
              }
              return data; // For sorting and other purposes, return the original data as it is
            }
          },
          {
            "data": "attendancekaryawan_check_out",
            "title": "Keluar",
            "className": "text-center",
            "render": function(data, type, row) {
              if (data && (type === "display" || type === "filter")) {
                const lateData = row.attendancekaryawan_check_out_early;
                if (lateData !== '00:00' && lateData !== '' && lateData !== null) {
                  return formatTimeToHHmm(data) + ' (<span style="color: red;">' + lateData + '</span>)';
                } else {
                  return formatTimeToHHmm(data);
                }
              }
              return data; // For sorting and other purposes, return the original data as it is
            }
          },
          {
            "data": "attendancekaryawan_check_in_note",
            "title": "Remark",
            "className": "text-center"
          },
          {
            "data": "manager_name",
            "title": "Penanggung Jawab",
            "className": "text-center"
          },
          {
            "data": null,
            "title": "Aksi",
            "className": "text-center",
            "orderable": false,
            "render": function(data, type, row) {
              return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item detail-button" href="javascript:void(0);""
                    ><i class="bx bx-show me-1 text-info"></i> Selengkapnya</a
                  >
                </div>
              </div>`
            }
          },
          {
            "data": "attendancekaryawan_check_out_early",
          },
          {
            "data": "attendancekaryawan_break_end_late",
          },
          {
            "data": "attendancekaryawan_break_start_early",
          },
          {
            "data": "attendancekaryawan_check_in_photo",
          },
          {
            "data": "attendancekaryawan_check_out_photo",
          },
          {
            "data": "attendancekaryawan_break_start_photo",
          },
          {
            "data": "attendancekaryawan_break_end_photo",
          },
          {
            "data": "attendancekaryawan_break_end_note",
          },
          {
            "data": "attendancekaryawan_break_start_note",
          },
          {
            "data": "attendancekaryawan_check_out_note",
          },
          {
            "data": "attendancekaryawan_break_start_late"
          },
          {
            "data": "attendancekaryawan_check_in_location"
          },
          {
            "data": "attendancekaryawan_break_start_location"
          },
          {
            "data": "attendancekaryawan_break_end_location"
          },
          {
            "data": "attendancekaryawan_check_out_location"
          },
          {
            "data": "attendancekaryawan_id"
          },
          {
            "data": "leavekaryawan_id"
          },
          {
            "data": "attendancekaryawan_check_in_late_note"
          }
        ],
        "columnDefs": [{
            "targets": [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26], // Indexes of the columns to be hidden
            "visible": false,
            "searchable": false,
          },
          {
            "targets": [8],
            "orderable": false,
          },
        ]
      });

      // $("#start-date, #end-date").on("apply.daterangepicker", function () {
      //     tblKehadiran.draw();
      // });

      $("#table-kehadiran-karyawan").on("click", ".detail-button", function() {
        const rowData = tblKehadiran.row($(this).closest("tr")).data();
        $("#attendancekaryawan_id").val(rowData.attendancekaryawan_id);
        $("#leavekaryawan_id").val(rowData.leavekaryawan_id);

        const checkInTabContent = `
        <div class="check-in-tab-content">
          <div class="form-group mb-3">
            <label for="checkInLocation"><strong>Lokasi:</strong></label>
            <textarea id="checkInLocation" class="form-control">${rowData.attendancekaryawan_check_in_location ? rowData.attendancekaryawan_check_in_location : '-'}</textarea>
          </div>
          <div class="form-group mb-1">
            <label for="checkInTime"><strong>Jam Masuk:</strong></label>
            <input type="text" id="checkInTime" class="form-control" value="${rowData.attendancekaryawan_check_in ? (formatTimeToHHmm(rowData.attendancekaryawan_check_in) !== null ? formatTimeToHHmm(rowData.attendancekaryawan_check_in) : '-') : '-'}">
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                Format jam harus HH:mm atau '-' untuk mengosongkan data
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="checkInNote"><strong>Alasan Diluar Kantor:</strong></label>
            <input type="text" id="checkInNote" class="form-control" value="${rowData.attendancekaryawan_check_in_note ? rowData.attendancekaryawan_check_in_note : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="checkInLateNote"><strong>Alasan Keterlambatan:</strong></label>
            <input type="text" id="checkInLateNote" class="form-control" value="${rowData.attendancekaryawan_check_in_late_note ? rowData.attendancekaryawan_check_in_late_note : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="checkInLateTime"><strong>Durasi Keterlambatan:</strong></label>
            <input type="text" id="checkInLateTime" class="form-control" value="${rowData.attendancekaryawan_check_in_late ? rowData.attendancekaryawan_check_in_late : '-'}">
          </div>
          <div class="text-center mb-3">
            ${rowData.attendancekaryawan_check_in_photo ? `<img class="small-image mb-3" style="max-width: 100%;" src="${rowData.attendancekaryawan_check_in_photo}" alt="" />` : ''}
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="edit-button btn btn-sm btn-warning">Simpan Absen Masuk</button>
        </div>
        `;
        $("#checkIn").html(checkInTabContent);

        const checkOutTabContent = `
        <div class="check_out-tab-content">
          <div class="form-group mb-3">
            <label for="checkOutLocation"><strong>Lokasi:</strong></label>
            <textarea id="checkOutLocation" class="form-control">${rowData.attendancekaryawan_check_out_location ? rowData.attendancekaryawan_check_out_location : '-'}</textarea>
          </div>
          <div class="form-group mb-1">
            <label for="checkOutTime"><strong>Jam Keluar:</strong></label>
            <input type="text" id="checkOutTime" class="form-control" value="${rowData.attendancekaryawan_check_out ? formatTimeToHHmm(rowData.attendancekaryawan_check_out) : '-'}">
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                Format jam harus HH:mm atau '-' untuk mengosongkan data
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="checkOutNote"><strong>Alasan Keluar Dini:</strong></label>
            <input type="text" id="checkOutNote" class="form-control" value="${rowData.attendancekaryawan_check_out_note ? rowData.attendancekaryawan_check_out_note : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="checkOutEarly"><strong>Durasi Keluar Dini:</strong></label>
            <input type="text" id="checkOutEarly" class="form-control" value="${rowData.attendancekaryawan_check_out_early ? rowData.attendancekaryawan_check_out_early : '-'}">
          </div>
          <div class="text-center mb-3">
            ${rowData.attendancekaryawan_check_out_photo ? `<img class="small-image mb-3" style="max-width: 100%;" src="${rowData.attendancekaryawan_check_out_photo}" alt="" />` : ''}
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="edit-button btn btn-sm btn-warning">Simpan Absen Keluar</button>
        </div>
        `;
        $("#checkOut").html(checkOutTabContent);


        const startBreakTabContent = `
        <div class="start-break-tab-content">
          <div class="form-group mb-3">
            <label for="startBreakLocation"><strong>Lokasi:</strong></label>
            <textarea id="startBreakLocation" class="form-control">${rowData.attendancekaryawan_break_start_location ? rowData.attendancekaryawan_break_start_location : '-'}</textarea>
          </div>
          <div class="form-group mb-1">
            <label for="startBreakTime"><strong>Jam Mulai Istirahat:</strong></label>
            <input type="text" id="startBreakTime" class="form-control" value="${rowData.attendancekaryawan_break_start ? formatTimeToHHmm(rowData.attendancekaryawan_break_start) : '-'}">
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                Format jam harus HH:mm atau '-' untuk mengosongkan data
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="startBreakNote"><strong>Alasan Perubahan Mulai Istirahat:</strong></label>
            <input type="text" id="startBreakNote" class="form-control" value="${rowData.attendancekaryawan_break_start_note ? rowData.attendancekaryawan_break_start_note : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="startBreakEarly"><strong>Durasi Istirahat Dini:</strong></label>
            <input type="text" id="startBreakEarly" class="form-control" value="${rowData.attendancekaryawan_break_start_early ? rowData.attendancekaryawan_break_start_early : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="startBreakLate"><strong>Durasi Keterlambatan Istirahat:</strong></label>
            <input type="text" id="startBreakLate" class="form-control" value="${rowData.attendancekaryawan_break_start_late ? rowData.attendancekaryawan_break_start_late : '-'}">
          </div>
          <div class="text-center mb-3">
            ${rowData.attendancekaryawan_break_start_photo ? `<img class="small-image mb-3" style="max-width: 100%;" src="${rowData.attendancekaryawan_break_start_photo}" alt="" />` : ''}
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="edit-button btn btn-sm btn-warning">Simpan Mulai Istirahat</button>
        </div>
        `;
        $("#startBreak").html(startBreakTabContent);


        const endBreakTabContent = `
        <div class="end-break-tab-content">
          <div class="form-group mb-3">
            <label for="endBreakLocation"><strong>Lokasi:</strong></label>
            <textarea id="endBreakLocation" class="form-control">${rowData.attendancekaryawan_break_end_location ? rowData.attendancekaryawan_break_end_location : '-'}</textarea>
          </div>
          <div class="form-group mb-1">
            <label for="endBreakTime"><strong>Jam Selesai Istirahat:</strong></label>
            <input type="text" id="endBreakTime" class="form-control" value="${rowData.attendancekaryawan_break_end ? formatTimeToHHmm(rowData.attendancekaryawan_break_end) : '-'}">
          </div><div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                Format jam harus HH:mm atau '-' untuk mengosongkan data
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="endBreakNote"><strong>Alasan Istirahat Tambahan:</strong></label>
            <input type="text" id="endBreakNote" class="form-control" value="${rowData.attendancekaryawan_break_end_note ? rowData.attendancekaryawan_break_end_note : '-'}">
          </div>
          <div class="form-group mb-3">
            <label for="endBreakLate"><strong>Durasi Istirahat Tambahan:</strong></label>
            <input type="text" id="endBreakLate" class="form-control" value="${rowData.attendancekaryawan_break_end_late ? rowData.attendancekaryawan_break_end_late : '-'}">
          </div>
          <div class="text-center mb-3">
            ${rowData.attendancekaryawan_break_end_photo ? `<img class="small-image mb-3" style="max-width: 100%;" src="${rowData.attendancekaryawan_break_end_photo}" alt="" />` : ''}
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="edit-button btn btn-sm btn-warning">Simpan Selesai Istirahat</button>
        </div>
        `;
        $("#endBreak").html(endBreakTabContent);

        // Show the modal
        $("#detailModal").modal("show");
      });

      function appendError($inputElement, errorMessage) {
        // Add the "error" class to the form-group container
        $inputElement.addClass("error");

        // Create the error message element
        $errorElement = $("<div>")
          .addClass("error-message")
          .addClass("error-text")
          .text(errorMessage);

        // Insert the error message after the form-group container
        $inputElement.after($errorElement);
      }

      $("input").focus(function() {
        $(this).removeClass("error");
        $(this).next(".error-message").remove();
      });


      $("#detailModal").on("click", ".edit-button", function() {
        $(".spinner-box").css({
          display: "table"
        });
        const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
        const activeTab = $("#detailTabs .nav-link.active").attr("id");

        // Construct the attendancekaryawan_type based on the active tab
        let attendanceType;
        switch (activeTab) {
          case "checkInTab":
            attendanceType = "CHECKIN";
            break;
          case "startBreakTab":
            attendanceType = "STARTBREAK";
            break;
          case "endBreakTab":
            attendanceType = "ENDBREAK";
            break;
          case "checkOutTab":
            attendanceType = "CHECKOUT";
            break;
        }

        // Collect data from the input fields within the active tab
        const formData = new FormData();
        const jsonData = {}; // To store the JSON data
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        jsonData["attendancekaryawan_type"] = attendanceType
        jsonData["attendancekaryawan_id"] = $("#attendancekaryawan_id").val();
        jsonData["leavekaryawan_id"] = $("#leavekaryawan_id").val();

        if (["leavekaryawan_id"] !== null && jsonData["leavekaryawan_id"] !== "") {
          $(".spinner-box").fadeOut()
          $(".error-message").remove();
          return toastr.error("Tidak dapat mengubah Absen yang berstatus Cuti.");
        }

        jsonData["_token"] = csrfToken;
        let hasErrors = false;

        // Collect data from the input fields within the active tab
        formData.append("isEdit", true);
        if (attendanceType === "CHECKIN") {
          jsonData["attendancekaryawan_check_in_location"] = $("#checkInLocation").val();
          jsonData["attendancekaryawan_check_in"] = $("#checkInTime").val();
          jsonData["attendancekaryawan_check_in_note"] = $("#checkInNote").val();
          jsonData["attendancekaryawan_check_in_late_note"] = $("#checkInLateNote").val();
          console.log(jsonData)
          if (jsonData["attendancekaryawan_check_in"] !== '-' && (jsonData["attendancekaryawan_check_in"].trim() === '' || !timePattern.test(jsonData["attendancekaryawan_check_in"]))) {
            appendError($("#checkInTime"), "Format jam harus 24 Jam atau '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_check_in_note"].trim() === '') {
            appendError($("#checkInNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_check_in_location"].trim() === '') {
            appendError($("#checkInLocation"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }
        } else if (attendanceType === "STARTBREAK") {
          jsonData["attendancekaryawan_break_start_location"] = $("#startBreakLocation").val();
          jsonData["attendancekaryawan_break_start"] = $("#startBreakTime").val();
          jsonData["attendancekaryawan_break_start_note"] = $("#startBreakNote").val();
          if (jsonData["attendancekaryawan_break_start"].trim() === '' || !timePattern.test(jsonData["attendancekaryawan_break_start"])) {
            appendError($("#startBreakTime"), "Format jam harus 24 Jam atau '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_break_start_note"].trim() === '') {
            appendError($("#startBreakNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_break_start_location"].trim() === '') {
            appendError($("#startBreakLocation"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }
        } else if (attendanceType === "ENDBREAK") {
          jsonData["attendancekaryawan_break_end_location"] = $("#endBreakLocation").val();
          jsonData["attendancekaryawan_break_end"] = $("#endBreakTime").val();
          jsonData["attendancekaryawan_break_end_note"] = $("#endBreakNote").val();
          if (jsonData["attendancekaryawan_break_end"].trim() === '' || !timePattern.test(jsonData["attendancekaryawan_break_end"])) {
            appendError($("#endBreakTime"), "Format jam harus 24 Jam atau '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_break_end_note"].trim() === '') {
            appendError($("#endBreakNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_break_end_location"].trim() === '') {
            appendError($("#endBreakLocation"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }
        } else if (attendanceType === "CHECKOUT") {
          jsonData["attendancekaryawan_check_out_location"] = $("#checkOutLocation").val();
          jsonData["attendancekaryawan_check_out"] = $("#checkOutTime").val();
          jsonData["attendancekaryawan_check_out_note"] = $("#checkOutNote").val();
          if (jsonData["attendancekaryawan_check_out"].trim() === '' || !timePattern.test(jsonData["attendancekaryawan_check_out"])) {
            appendError($("#checkOutTime"), "Format jam harus 24 Jam atau '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_check_out_note"].trim() === '') {
            appendError($("#checkOutNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if (jsonData["attendancekaryawan_check_out_location"].trim() === '') {
            appendError($("#checkOutLocation"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }
        }

        if (hasErrors) {
          $(".spinner-box").fadeOut()
          return false;
        }

        $.ajax({
          url: `{{ route('user.kehadiran.edit') }}?menu_id=${currentMenuId}`,
          method: "POST",
          data: JSON.stringify(jsonData),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            $(".spinner-box").fadeOut()
            if (response.success) {
              toastr.success(response.message);
              tblKehadiran.draw();
              $(".error-message").remove();
            } else {
              $(".error-message").remove();
              toastr.error(response.message);
            }
          },
          error: function(error) {
            $(".spinner-box").fadeOut()
            $(".error-message").remove();
            // toastr.error("An error occurred while submitting the form.");
            toastr.error(error.message);
          }
        });
      });

      $(".export-button-container button").on("click", function() {
        $(".spinner-box").css({
          display: "table"
        });
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        // Get input values
        const startDate = $("#start-date").val();
        const endDate = $("#end-date").val();
        const karyawanName = $("#table-kehadiran-karyawan_filter input").val();

        // Create the data object for the POST request
        const data = {
          start_date: startDate || null,
          end_date: endDate || null,
          karyawan: selectedEmployeeId,
        };

        // Make the POST request to your API endpoint
        $.ajax({
          url: `{{ route('user.kehadiran.export') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
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

        return $(".spinner-box").fadeOut();
      });

      $(".import-button-container button").on("click", function() {
        $("#importModal").modal("show");
      });

      $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
          const startDate = new Date($('#start-date').val());
          const endDate = new Date($('#end-date').val());
          const dateStr = data[0]; // Assuming "Date" column is the first column in your data

          if (startDate == '' && endDate == '') {
            return true; // No filtering if both dates are empty
          }

          const currentDate = new Date(dateStr);
          if (startDate != '' && currentDate < startDate) {
            return false;
          }
          if (endDate != '' && currentDate > endDate) {
            return false;
          }

          return true;
        }
      );

      function formatTimeToHHmm(time) {
        const splitTime = time.split(' ')
        if (splitTime[1]) {
          const timeParts = splitTime[1].split(":");
          return timeParts[0] + ":" + timeParts[1];
        } else {
          return null
        }
      }

      setHtmlTitle('{{$title}}')
    });
  </script>