<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5>Pengaturan Libur</h5>
          </div>
          <?php if (in_array('C', request()->get('permission_codes'))) : ?>
            <div class="col-sm-6 text-end">
              <a class="btn btn-sm btn-outline-info" id="btn-import-modal" href="#">
                <i class='bx bxs-cloud-upload'></i>
                Impor
              </a>
              <button type="button" class="btn btn-sm btn-warning" id="btnAddLibur">+ Libur</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-libur">
          <thead class="table-light">
            <tr>
              <th>Kode Libur</th>
              <th>Deskripsi</th>
              <th>Tanggal Mulai Berlaku</th>
              <th>Tanggal Akhir Berlaku</th>
              <!-- <th>Deduksi Cuti</th> -->
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addLiburModal" tabindex="-1" aria-labelledby="addLiburModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLiburModalLabel">Pengaturan Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddLibur" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="holiday_id" name="holiday_id" value="">
        <div class="modal-body">
          <div class="mb-3" style="display: none;" id="excodeholiday-container">
            <label for="excodeholiday" class="title-case-jadwal">Kode Libur</label>
            <input type="text" class="form-control" id="excodeholiday" name="excodeholiday" disabled>
          </div>
          <div class="mb-3" id="description-container">
            <label for="description" class="form-label title-case-jadwal lbl-req">Deskripsi</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
          <div class="row mb-3" id="start-container">
            <div class="col">
              <label for="tanggalMulai" class="form-label title-case-jadwal lbl-req">Tanggal Mulai</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="tanggalMulai" name="tanggalMulai" required maxlength="10" placeholder="DD-MM-YYYY">
                <span class="icon"><i class="fas fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="row mb-3" id="end-container">
            <div class="col">
              <label for="tanggalBerakhir" class="form-label title-case-jadwal lbl-req">Tanggal Berakhir</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="tanggalBerakhir" name="tanggalBerakhir" required maxlength="10" placeholder="DD-MM-YYYY">
                <span class="icon"><i class="fas fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="mb-3" id="active-container">
            <label class="form-label title-case-jadwal lbl-req" id="labelAktif" style="margin-right: 15px;">Apakah libur ini aktif?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeTrue" value="activeTrue" checked>
              <label class="form-check-label title-case-jadwal" for="activeTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeFalse" value="activeFalse">
              <label class="form-check-label title-case-jadwal" for="activeFalse">Tidak</label>
            </div>
          </div>
          <!-- <div class="mb-3" id="deduct-container">
            <label class="form-label title-case-jadwal lbl-req" id="labelDeduct" style="margin-right: 15px;">Apakah libur ini mengurangi kuota cuti?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="deductOption" id="deductTrue" value="deductTrue">
              <label class="form-check-label title-case-jadwal" for="deductTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="deductOption" id="deductFalse" value="deductFalse" checked>
              <label class="form-check-label title-case-jadwal" for="deductFalse">Tidak</label>
            </div>
          </div> -->
          <div class="mb-3" id="catatan-container">
            <label for="catatan" class="form-label title-case-jadwal">Catatan</label>
            <input type="text" class="form-control" id="catatan" name="catatan" required>
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                <!-- *)Tanggal Mulai dan Tanggal Berakhir harus sama untuk Libur 1 Hari. -->
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
              *) Sebelum mengimpor Data Pengaturan Libur, anda harus mengunduh Templat Pengaturan Libur.
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Pengaturan Libur yang akan di impor dengan Templat Pengaturan Libur sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-unduh-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Libur</button>
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
        <h5 class="modal-title" id="modalCenterTitle">Impor Data Transaksi</h5>
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
              <th>ID Karyawan</th>
              <th>Tanggal Transaksi</th>
              <th>Penghasilan Bruto</th>
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
        <h5 class="modal-title" id="historyModalLabel">Histori Impor Data Transaksi</h5>
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

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    $("#tanggalMulai, #tanggalBerakhir").daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      parentEl: '#addLiburModal',
      locale: {
        format: 'DD-MM-YYYY', // Display day, month, and year
      },
      minYear: moment().year(),
      maxYear: moment().year() + 1
    })

    function isValidDateFormat(dateStr) {
      // Regular expression pattern for "DD-MM-YYYY" format
      const pattern = /^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/;
      return pattern.test(dateStr);
    }

    function formatDateFromISO(dateStr) {
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        return parts[2] + '-' + parts[1] + '-' + parts[0];
      } else {
        return dateStr;
      }
    }

    let tblPengaturanLibur = $("#table-pengaturan-libur").DataTable({
      // DataTable configuration options
      "searching": true,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Deskripsi / Kode Libur",
      },
      "info": false,
      "ordering": true,
      "ajax": {
        "url": `{{ route('user.page.pengaturan.libur.datatable') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {}
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "holiday_id",
          "title": "Kode Libur",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === 'display' || type === 'filter')) {
              return 'LI' + data;
            }
            return data;
          }
        },
        {
          "data": "holiday_description",
          "title": "Deskripsi",
          "className": "text-center"
        },
        {
          "data": "holiday_start_date",
          "title": "Tanggal Mulai Berlaku",
          "className": "text-center"
        },
        {
          "data": "holiday_end_date",
          "title": "Tanggal Akhir Berlaku",
          "className": "text-center"
        },
        // {
        //   "data": "holiday_is_cut_leave",
        //   "title": "Deduksi Cuti",
        //   "className": "text-center",
        //   "render": function(data, type, row) {
        //     if (data && (type === "display" || type === "filter")) {
        //       if (data === true) {
        //         return 'Ya'
        //       }
        //     }
        //     return 'Tidak'; // For sorting and other purposes, return the original data as it is
        //   }
        // },
        {
          "data": "holiday_status_active",
          "title": "Status",
          "className": "text-center",
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
          "data": null,
          "title": 'Aksi',
          "className": "text-center",
          "render": function(data, type, row) {
            return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                <?php if (in_array('U', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item edit-libur" href="javascript:void(0);" data-id="${row.holiday_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif ?>
                  <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item delete-libur" href="javascript:void(0);" data-id="${row.holiday_id}"
                    ><i class="bx bx-trash me-1 text-danger"></i> Nonaktif</a
                  >
                  <?php endif; ?>
                </div>
              </div>
                `;
          }
        },
      ],
      "columnDefs": [
        // Define which columns to hide
        {
          "targets": [], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
        {
          "targets": [5], // Indexes of the columns to be hidden
          "orderable": false,
        },
      ],
    });

    let isEdit = false

    $('#table-pengaturan-libur').on("click", ".delete-libur", function(e) {
      e.preventDefault();

      // Get the holiday_id from the data-id attribute of the Delete button
      const liburId = $(this).data("id");

      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      Swal.fire({
        html: 'Apakah anda ingin menghapus libur ini?',
        icon: 'question',
        preConfirm: () => {
          Swal.showLoading();
          return fetch(`{{url('/user/pengaturan/libur/delete')}}/${liburId}?menu_id=${currentMenuId}`, {
              method: 'DELETE',
              body: new URLSearchParams($.param({
                _token: $("meta[name=csrf-token]").attr('content')
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
        tblPengaturanLibur.row($(this).closest("tr")).remove().draw();
      });
    });

    const today = new Date();

    // Get the individual components of the date
    const day = today.getDate().toString().padStart(2, '0'); // Ensure two digits for day
    const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const year = today.getFullYear();

    // Create the formatted date string in "DD-MM-YYYY" format
    const formattedDate = `${day}-${month}-${year}`;

    $('#table-pengaturan-libur').on("click", ".edit-libur", function(e) {
      isEdit = true
      e.preventDefault();

      // Get the row data associated with the clicked "Edit" button
      const rowData = tblPengaturanLibur.row($(this).closest("tr")).data();

      const rowDataEndDate = new Date(rowData.holiday_end_date);

      if (rowDataEndDate < today) {
        $("#formAddLibur :input").prop("disabled", true);
      }

      // Now, populate the edit modal with the data from the rowData
      populateEditLiburModal(rowData);
    });

    function populateEditLiburModal(rowData) {
      $('#formAddLibur [type=reset]').click();
      // Update the form fields with the rowData
      $("#holiday_id").val(rowData.holiday_id);
      $("#description").val(rowData.holiday_description);
      $("#catatan").val(rowData.holiday_remark);
      if (rowData.holiday_status_active === true) {
        $("#activeTrue").prop("checked", true);
      } else {
        $("#activeFalse").prop("checked", true);
      }
      // if (rowData.holiday_is_cut_leave === true) {
      //   $("#deductTrue").prop("checked", true);
      // } else {
      //   $("#deductFalse").prop("checked", true);
      // }
      // Populate other form fields similarly

      // Format the ISO dates to DD-MM-YYYY format
      const formattedStartDate = formatDateFromISO(rowData.holiday_start_date);
      const formattedEndDate = formatDateFromISO(rowData.holiday_end_date);

      // Populate the input fields with formatted date values
      $("#tanggalMulai").val(formattedStartDate);
      $("#tanggalBerakhir").val(formattedEndDate);


      $("#excodeholiday-container").slideDown();
      $("#excodeholiday").val('LI' + rowData.holiday_id);
      $("#excodeholiday").prop('disabled', true);

      // Open the edit modal
      $("#addLiburModal").modal("show");

      $("#addLiburModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    function resetAndSlideUpForms() {
      isEdit = false
      $(".error-message").remove();
      $("#excodeholiday-container").slideUp();
      $("#formAddLibur")[0].reset();
    }

    // $("#addLiburModal").on("click", ".btn-close", function(e) {
    //   e.preventDefault();

    //   isEdit = false;
    //   resetAndSlideUpForms()
    // })

    function openAddLiburModal() {
      resetAndSlideUpForms()
      jQuery("#addLiburModal").modal("show");

      $("#addLiburModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    $("#formAddLibur").submit(function(event) {
      event.preventDefault();
      $(".spinner-box").css({
        'display': 'table'
      });
      $(".error-message").remove();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      const formData = $(this).serializeArray();
      const jsonData = {}; // To store the JSON data

      formData.forEach(function(item) {
        if (item.name === "description") {
          jsonData["holiday_description"] = item.value
        } else if (item.name === "tanggalMulai") {
          jsonData["holiday_start_date"] = item.value
        } else if (item.name === "tanggalBerakhir") {
          jsonData["holiday_end_date"] = item.value
        } else if (item.name === 'holiday_id' && item.value !== '') {
          jsonData["holiday_id"] = item.value
        } else if (item.name === "catatan") {
          jsonData["holiday_remark"] = item.value
        }

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      });

      let hasErrors = false;

      if (!jsonData["holiday_description"]) {
        appendError($("#description"), "Mohon isi deskripsi.");
        hasErrors = true;
      }

      jsonData["isEdit"] = isEdit
      jsonData["holiday_status_active"] = $("#activeTrue").prop("checked")
      // jsonData["holiday_is_cut_leave"] = $("#deductTrue").prop("checked");
      console.log(jsonData, 'jsondata')

      if (!jsonData["holiday_start_date"]) {
        appendError($("#tanggalMulai"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if (!jsonData["holiday_end_date"]) {
        appendError($("#tanggalBerakhir"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if (jsonData["holiday_start_date"] && jsonData["holiday_start_date"] !== '' && !isValidDateFormat(jsonData["holiday_start_date"])) {
        appendError($("#tanggalMulai"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }

      if (jsonData["holiday_end_date"] && jsonData["holiday_end_date"] !== '' && !isValidDateFormat(jsonData["holiday_end_date"])) {
        appendError($("#tanggalBerakhir"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }

      console.log(isValidDateFormat(jsonData["holiday_end_date"]), isValidDateFormat(jsonData["holiday_start_date"]), "test")

      if (jsonData["holiday_end_date"] && jsonData["holiday_end_date"] !== '' && isValidDateFormat(jsonData["holiday_end_date"]) && jsonData["holiday_start_date"] && jsonData["holiday_start_date"] !== '' && isValidDateFormat(jsonData["holiday_start_date"])) {
        const dateStr1 = jsonData["holiday_start_date"];
        const dateParts1 = dateStr1.split('-');
        const day1 = parseInt(dateParts1[0], 10);
        const month1 = parseInt(dateParts1[1] - 1, 10);
        const year1 = parseInt(dateParts1[2], 10);
        const date1 = new Date(year1, month1, day1);

        const dateStr2 = jsonData["holiday_end_date"];
        const dateParts2 = dateStr2.split('-');
        const day2 = parseInt(dateParts2[0], 10);
        const month2 = parseInt(dateParts2[1] - 1, 10);
        const year2 = parseInt(dateParts2[2], 10);
        const date2 = new Date(year2, month2, day2);

        console.log(date1, date2)

        if (date2 < date1) {
          appendError($("#tanggalBerakhir"), "Tanggal berakhir kurang dari tanggal mulai.");
          hasErrors = true;
        }
      }

      if (hasErrors) {
        $(".spinner-box").fadeOut();
        return false;
      }

      $.ajax({
        type: "POST",
        url: `{{ route('user.page.pengaturan.libur.save') }}?menu_id=${currentMenuId}`,
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        dataType: "json",
        success: function(response) {
          $(".spinner-box").fadeOut();
          if (response.success) {
            toastr.success(response.message);
            resetAndSlideUpForms()
            isEdit = false
            $("#addLiburModal").modal("hide");
            tblPengaturanLibur.draw(); // Refresh the DataTable
          } else {
            toastr.error(response.message);
          }
          $(`#tanggalMulai`).data('daterangepicker').setStartDate(formattedDate);
          $(`#tanggalMulai`).data('daterangepicker').setEndDate(formattedDate);
          $(`#tanggalBerakhir`).data('daterangepicker').setStartDate(formattedDate);
          $(`#tanggalBerakhir`).data('daterangepicker').setEndDate(formattedDate);
        },
        error: function(xhr, status, error) {
          $(".spinner-box").fadeOut();
          toastr.error("An error occurred while submitting the form.");
          console.log(xhr.responseText);
          $(`#tanggalMulai`).data('daterangepicker').setStartDate(formattedDate);
          $(`#tanggalMulai`).data('daterangepicker').setEndDate(formattedDate);
          $(`#tanggalBerakhir`).data('daterangepicker').setStartDate(formattedDate);
          $(`#tanggalBerakhir`).data('daterangepicker').setEndDate(formattedDate);
        }
      });
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

    $("#btnAddLibur").click(function() {
      $("#formAddLibur :input").prop("disabled", false);
      isEdit = false;
      openAddLiburModal();
    });

    // import
    $("#btn-import-modal").on("click", function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
    });
    $("#modalImporFile").on("hide.bs.modal", function(e) {
      $("#importModal").modal("show");
    });
    $("#btn-import").click(function(e) {
      e.preventDefault();
      $("#modalImporFile").modal("show");
      $("#importModal").modal("hide");
    });
    $('#importButton').click(function(e) {
      e.preventDefault();
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

      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      formData.append('_token', csrfToken);
      formData.append('file', input.files[0]);

      setTimeout(function() {
        $(".progress-bar").css("width", "95%");
      }, 1200);

      $.ajax({
        type: 'POST', // or 'GET' depending on your controller action
        url: `{{ route("user.page.pengaturan.libur.import") }}?menu_id=${currentMenuId}`,
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
          console.log(response);

          if (response.success === false) {
            Swal.fire({
              icon: 'warning',
              title: 'Gagal Impor',
              html: response.message,
              showCancelButton: false,
              confirmButtonText: 'Oke'
            });
            return false
          }

          $('#modalImporFile').modal('hide');
          input.value = "";
          $('.progress-container').hide();

          tblDetailImport.clear().draw();;

          $.each(response.result, function(index, item) {
            let no = index + 1;
            tblDetailImport.row.add([
              no, // Row number
              item.karyawan_enid, // Id Karyawan
              item.trxdate, // Date
              formatCurrency(item.bruto), // Date
              (item.status == 'Sukses') ? `<span class="text-success">${item.status}</span>` : `<span class="text-danger">${item.status}</span>`, // Status
              item.remark, // Remark
              // Add more columns as needed
            ]);
          });

          let id = response.history;

          $("#btn-unduh-rincian").attr("data-id", id);
          // Find the button element by its ID
          // var button = document.getElementById("btn-unduh-rincian");

          // Set the onclick attribute with the id
          // button.onclick = function(e) {
          //   e.preventDefault(); // Prevent the default behavior
          //   generateHistory(id); // Call your function with the id
          // };

          // $('#detailImportModalLabel').text(`Rincian Impor : ${response.totalsuccess} dari ${response.totaldata} data berhasil di impor`);

          // $('#detailImportModal').modal('show');
          $('#historyModal').modal('show');

          tblPengaturanLibur.draw();

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
      // "fnInitComplete": function() {
      //   this.fnAdjustColumnSizing(true);
      // },
      "autoWidth": true,
      "columnDefs": [{
        target: [0, 1, 2, 3, 4, 5],
        className: 'text-center'
      }],
    })

    $('#detailImportModal').on('shown.bs.modal', function() {
      // Clear the DataTable
      tblDetailImport.columns.adjust().draw();
    });

    // import history
    $(`#start-date-history`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
    });

    $(`#end-date-history`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
    });

    $("#searchButtonHistori").on("click", function() {
      tblHistori.draw();
    });
    $("#resetButtonHistori").on("click", function() {
      $(`#start-date-history`).val(moment().format('DD-MM-YYYY'));
      $(`#end-date-history`).val(moment().format('DD-MM-YYYY'));
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
        "url": `{{ route('user.page.pengaturan.libur.importhistory') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          // Add any additional data you want to pass to the server here
          data.start_date = $('#start-date-history').val() ? moment($('#start-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
          data.end_date = $('#end-date-history').val() ? moment($('#end-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
        },
      },
      // "fnInitComplete": function() {
      //   this.fnAdjustColumnSizing(true);
      // },
      "autoWidth": true,
      "columns": [{
          "data": "historyimport_date",
          // "title": "Tanggal Impor",
          "className": "text-center",
          "render": function(data, type, row) {
            return moment(data).format('DD MMMM YYYY HH:mm:ss'); // For sorting and other purposes, return the original data as it is
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
          "className": "text-center",
          "render": function(data, type, row) {
            return (row.historyimport_status != 'PROCESSED') ? `<a href="#" class="btn-unduh-rincian" data-id="${data}">Unduh</a>` : '';
          }
        },
      ]
    })

    $("#table-history").on("click", ".btn-unduh-rincian", function(e) {
      e.preventDefault();
      let id = $(this).attr("data-id");

      window.open(`{{ route('user.page.pengaturan.libur.generatehistory') }}?menu_id=${currentMenuId}&id=${id}`, '_blank');
      window.close();
    });

    $("#btn-import-history").on("click", function(e) {
      // tblHistori.draw();
      e.preventDefault();
      $("#importModal").modal("hide");
      $("#historyModal").modal("show");
    });

    $('#historyModal').on('shown.bs.modal', function() {
      // Clear the DataTable
      tblHistori.columns.adjust().draw();
    });

    $("#historyModal").on("hide.bs.modal", function(e) {
      $("#importModal").modal("show");
    });

    $("#btn-unduh-rincian").click(function(e) {
      e.preventDefault();
      let id = $(this).attr("data-id");

      window.open(`{{ route('user.page.penggajian.nonkaryawan.generatehistory') }}?menu_id=${currentMenuId}&id=${id}`, '_blank');
      window.close();
    });

    $("#btn-unduh-template").on("click", function() {
      window.open(`{{ route('user.page.pengaturan.libur.download-template') }}?menu_id=${currentMenuId}`, '_blank');
      window.close();
    })

    setHtmlTitle('{{$title}}')
  });
</script>