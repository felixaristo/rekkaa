<head>
  <style>
    #table-history th,
    #table-history td,
    #table-history .dataTables_paginate {
        font-size: 12px; /* Adjust the font size as desired */
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
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" class="form-control" autocomplete="off">
              <option value="all" selected>Semua</option> <!-- Added "ALL" option -->
              <option value="penyedia">Penyedia</option>
              <option value="pelanggan">Pelanggan</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control" autocomplete="off">
              <option value="all" selected>Semua</option> <!-- Added "ALL" option -->
              <option value="active">Aktif</option>
              <option value="nonactive">Tidak Aktif</option>
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
  </div>
</div>


<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">
          <a class="btn btn-sm btn-outline-info" id="btn-import" href="#">
            <i class='bx bxs-cloud-upload'></i> Impor
          </a>

          <a class="btn btn-sm btn-warning rekkaa-page-link" href="{{route('user.page.lawan-transaksi.create', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bx-plus'></i> Lawan Transaksi
          </a>

        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="text-nowrap">
            <table class="table table-hover display nowrap" id="table-lawan-transaksi" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th>Id Lawan Transaksi</th>
                  <th>Nama Lawan Transaksi</th>
                  <th>Nomor Tlp</th>
                  <th>Email</th>
                  <th>Nama PIC</th>
                  <th>Kategori</th>
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
              *) Sebelum mengimpor Data Lawan Transaksi, anda harus mengunduh Templat Impor Lawan Transaksi
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Lawan Transaksi yang akan di impor dengan Templat Impor Lawan Transaksi sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import-file" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Lawan Transaksi</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalImporLawanTransaksi" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Impor Lawan Transaksi</h5>
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
        <h5 class="modal-title" id="detailImportModalLabel">Rincian Impor Lawan Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeDetail"></button>
      </div>
      <div class="modal-body">
        <table id="table-detail" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
          <!-- Your DataTable content goes here -->
          <thead>
            <tr>
              <th>Baris</th>
              <th>Id Lawan Transaksi</th>
              <th>Nama Lawan Transaksi</th>
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
        url: "{{ route('user.lawan-transaksi.generate-history') }}?menu_id={{request()->get('menu_id')}}", // Concatenate the 'id' to the URL
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
              "url": `{{ route('user.lawan-transaksi.import-history') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
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

        $("#btn-import").click(function(e) {
          e.preventDefault();

          $("#importModal").modal("show");
        })

        $("#btn-import-file").click(function(e) {
          e.preventDefault();

          $("#importModal").modal("hide");
          $("#modalImporLawanTransaksi").modal("show");
        })

        $("#closeImpor").click(function(e) {
          e.preventDefault();
          $("#importModal").modal("show");
          $("#modalImporLawanTransaksi").modal("hide");
        })

        $("#btn-template").on("click", function() {
          $(".spinner-box").css({
            display: "table"
          });
          const csrfToken = $('meta[name="csrf-token"]').attr('content');

          // Make the POST request to your API endpoint
          $.ajax({
            url: `{{ route('user.page.lawan-transaksi.download-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
            },
            contentType: "application/json",
            success: function(response) {
              setTimeout(function() {
                window.open(response, '_blank');
                window.close();
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
          tblPelanggan.draw();
        });

        $("#resetButton").on("click", function() {
          $("#status").val('all'); // Remove all options from the select2 dropdown
          $("#kategori").val('all'); // Remove all options from the select2 dropdown
          tblPelanggan.draw();
        });

        let tblPelanggan = $("#table-lawan-transaksi").DataTable({
            // "language": {
            //   "infoEmpty": "No records available - Got it?",
            // },
            // "dom": 'flrtip',
            "searching": true,
            "language": {
                "searchPlaceholder": "Cari Nama Lawan Transaksi",
            },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "searchDelay": 1050,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": `{{route('user.page.lawan-transaksi.datatable', '')}}?menu_id=${currentMenuId}`,
                "type": "GET",
                "data": function(data) {
                    data.status = $('#status').val();
                    data.kategori = $('#kategori').val()
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
                target: [0,1,2,3,4,5,6,7],
                className: 'text-center'
            }],
            "columns": [
                {
                    "data": "wajibpajaktradeexchange_enid",
                },
                {
                    "data": "wajibpajaktradeexchange_fullname",
                },
                {
                    "data": "wajibpajaktradeexchange_main_number",
                },
                {
                    "data": "wajibpajaktradeexchange_email",
                },
                {
                    "data": "wajibpajaktradeexchange_main_pic_name"
                },
                {
                    "data": "wajibpajaktradeexchange_is_supplier",
                    "render": function (data, type, row) {
                      console.log(row)
                        if (row.wajibpajaktradeexchange_is_supplier === true && row.wajibpajaktradeexchange_is_customer === true) {
                            return "Penyedia & Pelanggan";
                        } else if (row.wajibpajaktradeexchange_is_supplier === true) {
                            return "Penyedia";
                        } else if (row.wajibpajaktradeexchange_is_customer === true) {
                            return "Pelanggan";
                        } else {
                            return "";
                        }
                    }
                },
                {
                    "data": "wajibpajaktradeexchange_active",
                    "render": function(data, type, row) {
                      if (type === 'display' || type === 'filter') {
                        if (data === true) {
                          return '<span class="badge bg-success">Aktif</span>'
                        } else {
                          return '<span class="badge bg-danger">Tidak Aktif</span>';
                        }
                      }
                      return '<span class="badge bg-danger">Tidak Aktif</span>';
                    }
                },
                {
                    "data": "wajibpajaktradeexchange_id",
                    "render": function(data, type, row) {
                      var actionLabel = row.wajibpajaktradeexchange_active ? "Non Aktifkan" : "Aktifkan";
                      var lblClass = row.wajibpajaktradeexchange_active ? "text-danger" : "text-success";
                      var actionIcon = row.wajibpajaktradeexchange_active ? "bx-trash" : "bx-check";
                      var actionClass = row.wajibpajaktradeexchange_active ? "btn-deactive" : "btn-reactive";

                      return `
                          <div class="dropdown">
                              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu">
                                  <a class="dropdown-item btn-edit" href="{{route('user.page.lawan-transaksi.edit', '')}}/${data}?menu_id=${currentMenuId}">
                                      <i class="bx bx-edit-alt me-1 text-info"></i> Edit
                                  </a>
                                  <a class="dropdown-item ${actionClass}" href="javascript:void(0);">
                                      <i class="bx ${actionIcon} me-1 ${lblClass}"></i> ${actionLabel}
                                  </a>
                              </div>
                          </div>
                      `;
                    }
                },
            ],
            });
        
        
          $('#table-lawan-transaksi').on("click", ".btn-deactive", function(e) {
            e.preventDefault();

            // Get the holiday_id from the data-id attribute of the Delete button
            let row = $(this).closest('tr');
            let data = tblPelanggan.row(row).data();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
              html: 'Apakah anda ingin menonaktifkan lawan transaksi?',
              icon: 'question',
              preConfirm: () => {
                Swal.showLoading();
                return fetch(`{{route('user.page.lawan-transaksi.deactive', '')}}?menu_id=${currentMenuId}`, {
                    method: 'POST',
                    body: new URLSearchParams($.param({
                      _token: $("meta[name=csrf-token]").attr('content'),
                      wajibpajaktradeexchange_id: data.wajibpajaktradeexchange_id
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

                Swal.fire({
                  title: result.message,
                  confirmButtonText: "Ok",
                  type: 'error'
                })
                return false;
              }

              toastr.success(result.message);
              tblPelanggan.draw();
            });
          });

          $('#table-lawan-transaksi').on("click", ".btn-reactive", function(e) {
            e.preventDefault();

            // Get the holiday_id from the data-id attribute of the Delete button
            let row = $(this).closest('tr');
            let data = tblPelanggan.row(row).data();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
              html: 'Apakah anda ingin mengaktifkan lawan transaksi?',
              icon: 'question',
              preConfirm: () => {
                Swal.showLoading();
                return fetch(`{{route('user.page.lawan-transaksi.reactive', '')}}?menu_id=${currentMenuId}`, {
                    method: 'POST',
                    body: new URLSearchParams($.param({
                      _token: $("meta[name=csrf-token]").attr('content'),
                      wajibpajaktradeexchange_id: data.wajibpajaktradeexchange_id
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

                Swal.fire({
                  title: result.message,
                  confirmButtonText: "Ok",
                  type: 'error'
                })
                return false;
              }

              toastr.success(result.message);
              tblPelanggan.draw();
            });
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
                url: `{{ route("user.page.lawan-transaksi.import") }}?menu_id=${currentMenuId}`,
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
                  setTimeout(function() {
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
                      $('#modalImporLawanTransaksi').modal('hide');
                      input.value = "";
                      $('.progress-container').hide();

                      tblDetailImport.clear().draw();;

                      $.each(response.result, function(index, item) {
                        tblDetailImport.row.add([
                          item.row, // Row number
                          item.tradeexchange_enid, // Id Karyawan
                          item.tradeexchange_fullname, // Nama Karyawan
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

                      tblPelanggan.draw()
                    }
                  }, 100)


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
    });

</script>