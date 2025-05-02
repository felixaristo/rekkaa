<head>
  <style>
    #start-date {
      width: 100%; /* Adjust the width as needed */
    }

    #detailModal label em {
      font-style: italic;
    }

    #detailModal p.font-weight-bold {
      font-weight: bold;
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
                      <input type="text" id="start-date" name="start-date" class="form-control">
                  </div>
                  <div class="col-md-3">
                      <label for="status">Status</label>
                      <select id="status" name="status" class="form-control">
                          <option value="" selected>Semua</option> <!-- Added "ALL" option -->
                          <option value="WAITING">Menunggu</option>
                          <option value="CANCEL">Dibatalkan</option>
                          <option value="APPROVED">Disetujui</option>
                          <option value="REJECTED">Tidak Disetujui</option>
                          <option value="WAITINGCANCEL">Menunggu Pembatalan</option>
                      </select>
                  </div>
                  <div class="col-md-6 mt-md-4 mt-sm-3 text-md-start text-sm-end">
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
      <div class="card-header m-2">
        <div class="row">
          <div class="col-sm-6">
            <h5>Pengajuan Cuti</h5>
          </div>
          <div class="col-sm-6 text-end">
            <button type="button" class="btn btn-sm btn-warning" id="btnPengajuan">+ Ajukan Cuti</button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <table class="table table-hover display nowrap" style="width: 100%" id="table-cuti">
              <thead>
                <tr>
                    <th>Jenis Cuti</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Mulai Cuti</th>
                    <th>Tanggal Berakhir Cuti</th>
                    <th>Status</th>
                    <th>Tanggal Persetujuan</th>
                    <th>Aksi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Cuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
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
          <div class="form-group alasan-ditolak-field" style="display: none;">
            <label for="alasan-ditolak"><em>Alasan Tidak Diizinkan</em></label>
            <p id="alasan-ditolak" class="font-weight-bold"></p>
          </div>
          <div class="form-group disetujui-oleh-field" style="display: none;">
            <label for="disetujui-oleh"><em>Disetujui Oleh</em></label>
            <p id="disetujui-oleh" class="font-weight-bold"></p>
          </div>
          <div class="form-group alasan-dibatalkan-field" style="display: none;">
            <label for="alasan-dibatalkan"><em>Alasan Dibatalkan</em></label>
            <p id="alasan-dibatalkan" class="font-weight-bold"></p>
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
        <h5 class="modal-title" id="pengajuanModalLabel">Ajukan Cuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formPengajuan" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="st_leave_id" name="st_leave_id" value="">
        <input type="hidden" id="leavekaryawan_id" name="leavekaryawan_id" value="">
        <div class="modal-body">
          <div class="mb-3">
            <label for="leaveType" class="form-label title-case-jadwal lbl-req">Tipe Cuti</label>
            <select class="form-select" id="leaveType" name="leaveType" required>
              <option value="" disabled selected>Pilih Tipe Cuti</option>
            </select>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="tanggalMulai" class="form-label title-case-jadwal lbl-req">Tanggal Mulai Cuti</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="tanggalMulai" name="tanggalMulai" required pattern="\d{2}-\d{2}-\d{4}" maxlength="10" placeholder="DD-MM-YYYY">
                <span class="icon"><i class="fas fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="tanggalBerakhir" class="form-label title-case-jadwal lbl-req">Tanggal Berakhir Cuti</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="tanggalBerakhir" name="tanggalBerakhir" required pattern="\d{2}-\d{2}-\d{4}" maxlength="10" placeholder="DD-MM-YYYY">
                <span class="icon"><i class="fas fa-calendar"></i></span>
              </div>
            </div>    
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="totalDays" class="form-label title-case-jadwal">Pemakaian Cuti</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="totalDays" name="totalDays">  
              </div>
            </div>    
          </div>
          <div class="form-group textbox-address mb-3">
            <label for="cutiNotes" class="col-sm-12 col-form-label title-case-jadwal lbl-req">Alasan Cuti</label>
            <textarea name="cutiNotes" required id="cutiNotes" class="form-control"></textarea>
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                <!-- *)Tanggal Mulai dan Tanggal Berakhir harus sama untuk Cuti 1 Hari. -->
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
  function formatDateInput(inputElement) {
    let value = inputElement.value.replace(/[^\d]/g, ''); // Remove non-numeric characters
    if (value.length > 0) {
      // Add '-' after day and month components
      if (value.length >= 2 && value.charAt(2) !== '-') {
        value = value.substring(0, 2) + '-' + value.substring(2);
      }
      if (value.length >= 5 && value.charAt(5) !== '-') {
        value = value.substring(0, 5) + '-' + value.substring(5);
      }
      // Ensure year has at most 4 digits
      if (value.length > 10) {
        value = value.substring(0, 10);
      }
    }
    inputElement.value = value;
  }

  $(function() {    
    $("#totalDays").prop('readonly', true);

    function isValidDateFormat(dateStr) {
      // Regular expression pattern for "DD-MM-YYYY" format
      const pattern = /^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/;
      return pattern.test(dateStr);
    }
    
    let isEdit = false

    // function calculateDateRange(dateStrA, dateStrB) {
    //     // Parse the date strings with the DD-MM-YYYY format
    //     const [dayA, monthA, yearA] = dateStrA.split('-').map(Number);
    //     const [dayB, monthB, yearB] = dateStrB.split('-').map(Number);
    //     const dateA = new Date(yearA, monthA - 1, dayA); // Month is 0-indexed
    //     const dateB = new Date(yearB, monthB - 1, dayB);
        
    //     const oneDay = 24 * 60 * 60 * 1000; // Number of milliseconds in a day
    //     const diffInDays = Math.round((dateB - dateA) / oneDay) + 1; // Adding 1 to include both start and end dates

    //     return diffInDays;
    // }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1);
        const day = (date.getDate() < 10 ? '0' : '') + date.getDate();
        
        return `${year}-${month}-${day}`;
    }

    function formatDateDd(date) {
      const year = date.getFullYear();
      const month = (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1);
      const day = (date.getDate() < 10 ? '0' : '') + date.getDate();
      
      return `${day}-${month}-${year}`;
    }

    function extractNumber(inputString) {
        // Split the input string by '(' and ')' to get the portion inside parentheses
        var parts = inputString.split("(");
        if (parts.length >= 2) {
            var numberString = parts[1].split(")")[0].trim(); // Extract the number part
            return numberString;
        }
        return null; // Return null if the input format is not as expected
    }

    $("#formPengajuan").submit(function(event) {
      event.preventDefault();
      $(".spinner-box").css({'display': 'table'});
      $(".error-message").remove();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      const formData = $(this).serializeArray();
      const jsonData = {}; // To store the JSON data

      formData.forEach(function(item) {
        if(item.name === "leaveType" && item.value !== '') {
          const selectedOption = $("#leaveType option:selected");
          jsonData["quota_left"] = extractNumber(selectedOption.text());
        } else if(item.name === "tanggalMulai") {
          jsonData["leavekaryawan_start_date"] = item.value
        } else if(item.name === "tanggalBerakhir") {
          jsonData["leavekaryawan_end_date"] = item.value
        } else if(item.name === "cutiNotes") {
          jsonData["leavekaryawan_request_note"] = item.value 
        } else if (item.name === 'st_leave_id' && item.value !== '') {
          jsonData["st_leave_id"] = item.value
        } else if (item.name === 'leavekaryawan_id' && item.value !== '') {
          jsonData["leavekaryawan_id"] = item.value
        } else if (item.name === 'totalDays' && item.value !== '') {
          jsonData["leavekaryawan_quota_use"] = parseInt(item.value)
        }

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      }); 

      let hasErrors = false;

      if(!jsonData["quota_left"]) {
        Swal.fire({
          html: 'Mohon pilih tipe cuti!',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        $(".spinner-box").fadeOut();
        return false
      }

      if(!jsonData["leavekaryawan_request_note"]) {
        appendError($("#cutiNotes"), "Mohon isi alasan anda cuti.");
        hasErrors = true;
      }

      if(!jsonData["leavekaryawan_start_date"]) {
        appendError($("#tanggalMulai"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if(!jsonData["leavekaryawan_end_date"]) {
        appendError($("#tanggalBerakhir"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if(jsonData["quota_left"] === 0) {
        appendError($("#leaveType"), "Anda sudah tidak memiliki sisa cuti untuk cuti ini.");
        hasErrors = true;
      }

      if(jsonData["leavekaryawan_start_date"] && jsonData["leavekaryawan_start_date"] !== '' && isValidDateFormat(jsonData["leavekaryawan_start_date"])) { 
        const dateStr1 = jsonData["leavekaryawan_start_date"];
        const dateParts1 = dateStr1.split('-');
        const day1 = parseInt(dateParts1[0], 10);
        const month1 = parseInt(dateParts1[1] - 1, 10);
        const year1 = parseInt(dateParts1[2], 10);
        const date1 = new Date(year1, month1, day1);
        if(currentCuti.leave_grace_period === null) {
          const date2 = new Date(currentCuti.leave_active_end_date);

          if(date1 > date2) {
            appendError($("#tanggalMulai"), "Tanggal cuti melebihi periode cuti.");
            hasErrors = true;
          }
        } else {
          const date2 = new Date(currentCuti.leave_grace_period);
          const date3 = new Date(currentCuti.leave_active_end_date)

          if(date1 > date2 && date1 > date3) {
            appendError($("#tanggalMulai"), "Tanggal cuti melebihi periode cuti.");
            hasErrors = true;
          }
        }
        
        const date3 = new Date(currentCuti.leave_active_start_date);

        if(date1 < date3) {
          appendError($("#tanggalMulai"), "Tanggal belum memasuki periode cuti.");
          hasErrors = true;
        } 
      }

      if(jsonData["leavekaryawan_end_date"] && jsonData["leavekaryawan_end_date"] !== '' && isValidDateFormat(jsonData["leavekaryawan_end_date"])) {
        const dateStr1 = jsonData["leavekaryawan_end_date"];
        const dateParts1 = dateStr1.split('-');
        const day1 = parseInt(dateParts1[0], 10);
        const month1 = parseInt(dateParts1[1] - 1, 10);
        const year1 = parseInt(dateParts1[2], 10);
        const date1 = new Date(year1, month1, day1);
        if(jsonData["leavekaryawan_start_date"] && jsonData["leavekaryawan_start_date"] !== '' && isValidDateFormat(jsonData["leavekaryawan_start_date"])) {
          const dateStr2 = jsonData["leavekaryawan_start_date"];
          const dateParts2 = dateStr2.split('-');
          const day2 = parseInt(dateParts2[0], 10);
          const month2 = parseInt(dateParts2[1] - 1, 10);
          const year2 = parseInt(dateParts2[2], 10);
          const date2 = new Date(year2, month2, day2);

          if(date1 < date2) {
            appendError($("#tanggalBerakhir"), "Tanggal berakhir kurang dari tanggal mulai.");
            hasErrors = true;
          }
        } else if(currentCuti.leave_grace_period === null) {
          const date2 = new Date(currentCuti.leave_active_end_date);

          if(date1 > date2) {
            appendError($("#tanggalBerakhir"), "Tanggal cuti melebihi periode cuti.");
            hasErrors = true;
          }
        } else if(currentCuti.leave_grace_period !== null) {
          const date2 = new Date(currentCuti.leave_grace_period);

          if(date1 > date2) {
            appendError($("#tanggalBerakhir"), "Tanggal cuti melebihi periode cuti.");
            hasErrors = true;
          }
        } else {
          const date2 = new Date(currentCuti.leave_active_start_date);

          if(date1 < date2) {
            appendError($("#tanggalBerakhir"), "Tanggal belum memasuki periode cuti.");
            hasErrors = true;
          }
        }
      }

      if(parseInt(jsonData["leavekaryawan_quota_use"]) > parseInt(jsonData["quota_left"])) {
        appendError($("#tanggalBerakhir"), "Total cuti melebihi sisa cuti.");
        hasErrors = true;
      }
      
      if(jsonData["leavekaryawan_start_date"] && jsonData["leavekaryawan_start_date"] !== '' && !isValidDateFormat(jsonData["leavekaryawan_start_date"])) {
        appendError($("#tanggalMulai"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }

      if(jsonData["leavekaryawan_end_date"] && jsonData["leavekaryawan_end_date"] !== '' && !isValidDateFormat(jsonData["leavekaryawan_end_date"])) {
        appendError($("#tanggalBerakhir"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }
      
      if(isEdit === false) {
        jsonData["leavekaryawan_status"] = "WAITING"

        const currentDate = new Date();
        jsonData["leavekaryawan_request_date"] = formatDate(currentDate);
      }

      jsonData["leavekaryawan_type"] = "REQUEST"

      if (hasErrors) {
        $(".spinner-box").fadeOut();
        return false;
      }

      jsonData["isEdit"] = isEdit

      $.ajax({
        type: "POST",
        url: "{{ route('karyawan.cuti.pengajuan') }}",
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        dataType: "json",
        success: function(response) {
          $(".spinner-box").fadeOut();
          $(".error-message").remove();
          if (response.success) {
            toastr.success(response.message);
            isEdit = false
            $("#pengajuanModal").modal("hide");
            tblCuti.draw(); // Refresh the DataTable
          } else {
            toastr.error(response.message);
          }

          // $(`#tanggalMulai`).data('daterangepicker').setStartDate(formattedDate);
          // $(`#tanggalMulai`).data('daterangepicker').setEndDate(formattedDate);
          // $(`#tanggalBerakhir`).data('daterangepicker').setStartDate(formattedDate);
          // $(`#tanggalBerakhir`).data('daterangepicker').setEndDate(formattedDate);
        },
        error: function(xhr, status, error) {
          // $(`#tanggalMulai`).data('daterangepicker').setStartDate(formattedDate);
          // $(`#tanggalMulai`).data('daterangepicker').setEndDate(formattedDate);
          // $(`#tanggalBerakhir`).data('daterangepicker').setStartDate(formattedDate);
          // $(`#tanggalBerakhir`).data('daterangepicker').setEndDate(formattedDate);
          $(".spinner-box").fadeOut();
          toastr.error("An error occurred while submitting the form.");
        }
      });
    });

    // $("#pengajuanModal").on("click", ".btn-close", function(e) {
    //   e.preventDefault();

    //   isEdit = false;
    // })

    $("#table-cuti").on("click", ".btn-edit", function(e) {
      isEdit = true
      e.preventDefault();

      // Get the row data associated with the clicked "Edit" button
      const rowData = tblCuti.row($(this).closest("tr")).data();
      const status = rowData.leavekaryawan_status;
      
      if(status !== 'WAITING') {
        $("#formPengajuan :input").prop("disabled", true);
      } else {
        $("#formPengajuan :input").prop("disabled", false);
      }

      // Now, populate the edit modal with the data from the rowData
      populateEditCutiModal(rowData);
    });

    $("#table-cuti").on("click", ".btn-delete", function(e) {
      e.preventDefault();

      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const cutiId = rowData.leavekaryawan_id;
      const status = rowData.leavekaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if(status !== 'WAITING' && status !== 'APPROVED') {
        Swal.fire({
          html: 'Tidak dapat mengubah Cuti yang sudah di tindak.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: 'Apakah anda ingin membatalkan cuti ini?',
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
                    isEdit: true,
                    leavekaryawan_id: cutiId,
                    leavekaryawan_status: "CANCEL",
                    leavekaryawan_type: "REQUEST",
                    leavekaryawan_cancel_note: reason, // Add the reason to the request data
                };

                return fetch("{{ route('karyawan.cuti.pengajuan') }}", {
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
          
        });
    });

    function updateTotalDays() {
      const startDate = $("#tanggalMulai").val();
      const endDate = $("#tanggalBerakhir").val();

    if (startDate !== '' && endDate !== '') {
        const dateParts1 = startDate.split('-');
        const day1 = parseInt(dateParts1[0], 10);
        const month1 = parseInt(dateParts1[1]) - 1; // Adjust month value
        const year1 = parseInt(dateParts1[2], 10);
        const date1 = new Date(year1, month1, day1);

        const dateParts2 = endDate.split('-');
        const day2 = parseInt(dateParts2[0], 10);
        const month2 = parseInt(dateParts2[1]) - 1; // Adjust month value
        const year2 = parseInt(dateParts2[2], 10);
        const date2 = new Date(year2, month2, day2);

        const formattedStartDate = `${year1}-${(month1 + 1).toString().padStart(2, '0')}-${day1.toString().padStart(2, '0')}`;
        const formattedEndDate = `${year2}-${(month2 + 1).toString().padStart(2, '0')}-${day2.toString().padStart(2, '0')}`;
        
        // Make an AJAX request to get total_leave
        $.ajax({
          type: "GET",
          url: "{{ route('karyawan.cuti.total-leave') }}",
          data: {
            start_date: formattedStartDate,
            end_date: formattedEndDate
          },
          success: function (response) {
            $("#totalDays").val(response.data);
          },
          error: function () {
            // Handle error if necessary
            $("#totalDays").val('');
          }
        });
      }
    }

    // Call the function on input change
    $("#tanggalMulai, #tanggalBerakhir").on("change", updateTotalDays);

    function populateEditCutiModal(rowData) {
      // Update the form fields with the rowData
      
      currentCuti = dataCuti.find(cuti => cuti.leave_id.includes(`${rowData.st_leave_id}`));
      $("#st_leave_id").val(currentCuti.leave_id);
      console.log(currentCuti)
      $("#leavekaryawan_id").val(rowData.leavekaryawan_id);
      $("#leaveType").val(rowData.st_leave_id);

      // Find the option element with the matching data-leave-id attribute
      const $selectedOption = $("#leaveType").find(`option[data-leave-id="${currentCuti.leave_id}"]`);

      // If the option is found, mark it as selected
      if ($selectedOption.length > 0) {
        $selectedOption.prop("selected", true);
      }

      $("#tanggalMulai").val(formatDateDd(new Date(rowData.leavekaryawan_start_date)));
      $("#tanggalBerakhir").val(formatDateDd(new Date(rowData.leavekaryawan_end_date)));

      $("#tanggalMulai").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        parentEl: '#pengajuanModal',
        locale: {
          format: 'DD-MM-YYYY', // Display only day and month
        },
        startDate : $("#tanggalMulai").val(),
        endDate : $("#tanggalMulai").val()
      });

      $("#tanggalBerakhir").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        parentEl: '#pengajuanModal',
        locale: {
          format: 'DD-MM-YYYY', // Display only day and month
        },
        startDate : $("#tanggalBerakhir").val(),
        endDate : $("#tanggalBerakhir").val()
      });

      $("#totalDays").val(rowData.leavekaryawan_another_quota_use !== null ? rowData.leavekaryawan_another_quota_use + rowData.leavekaryawan_quota_use : rowData.leavekaryawan_quota_use);
      
      $("#cutiNotes").val(rowData.leavekaryawan_request_note);

      // Open the edit modal
      $("#pengajuanModal").modal("show");

      $("#pengajuanModal").on("hidden.bs.modal", function () {
        $(".error-message").remove();
        $("#formPengajuan")[0].reset(); // Reset the form fields
      });
    }

    $("#btnPengajuan").click(function() {
      if(dataTipeCuti.responseJSON.data.length === 0) {
        Swal.fire({
          html: 'Perusahaan anda belum memasukan pilihan Cuti!',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      $("#formPengajuan :input").prop("disabled", false);

      isEdit = false;

      openPengajuanModal();
    });

    function appendError($inputElement, errorMessage) {
      // Add the "error" class to the form-group container
      $inputElement.closest(".form-group").addClass("error");

      if ($inputElement.is("textarea")) {
        $inputElement.closest(".form-group").addClass("error");
        // Apply specific styling to the textarea, e.g., changing the text color
        $errorElement = $("<div>")
        .addClass("error-message")
        .addClass("error-text")
        .text(errorMessage);
        
        $inputElement.closest(".form-group").after($errorElement);       
      } else {
        $inputElement.addClass("error");

        $errorElement = $("<div>")
        .addClass("error-message")
        .addClass("error-text")
        .text(errorMessage);
        
        $inputElement.after($errorElement);       
      }

      // Create the error message element
     
      // Insert the error message after the form-group container
      $inputElement.closest(".form-group").after($errorElement);
    }

    $("input").focus(function() {
      $(this).removeClass("error");
      $(this).next(".error-message").remove();
    });

    function openPengajuanModal() {
      $(".error-message").remove();
      $("#formPengajuan")[0].reset(); // Reset the form fields
      jQuery("#pengajuanModal").modal("show");
    }

    const today = new Date();

    // Get the individual components of the date
    const day = today.getDate().toString().padStart(2, '0'); // Ensure two digits for day
    const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const year = today.getFullYear();

    // Create the formatted date string in "DD-MM-YYYY" format
    const formattedDate = `${day}-${month}-${year}`;

    $(`#tanggalMulai, #tanggalBerakhir`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY',
      },
      minYear: moment().year(),
      maxYear: moment().year() + 2,
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
        format: 'DD-MM-YYYY',
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedLastDay, 
    });
    
    $("#searchButton").on("click", function () {
      tblCuti.draw();
    });
    
    $("#resetButton").on("click", function () {
        $("#start-date").val(`${formattedFirstDay} - ${formattedLastDay}`);
        $('#start-date').data('daterangepicker').setStartDate(formattedFirstDay);
        $('#start-date').data('daterangepicker').setEndDate(formattedLastDay);
        tblCuti.draw();
    });

    let dataCuti = [];
    let currentCuti;

    const dataTipeCuti = $.ajax({
      url: "/karyawan/cuti/sisa-cuti", // Replace with your actual endpoint
      type: "GET",
      success: function(data) {
        // Clear existing options
        $("#leaveType").empty();

        // console.log(data.data, 'tipe cuti');

        // Add default disabled option
        $("#leaveType").append('<option value="" disabled selected>Pilih Tipe Cuti</option>');

        // Populate select options from the fetched data
        data.data.forEach(function(option) {
          $("#leaveType").append('<option value="' + option.leave_description + '" data-leave-id="' + option.leave_id + '">' + option.leave_description + '</option>');
        });
        
        dataCuti = data.data;
      },
      error: function(error) {
        console.error("Error fetching Tipe Cuti options:", error);
      }
    });

    $("#leaveType").on("change", function() {
        const selectedOption = $(this).find(":selected");
        const leaveId = selectedOption.data("leave-id");
        $("#st_leave_id").val(leaveId);
        currentCuti = dataCuti.find(cuti => cuti.leave_id == leaveId);
        console.log(currentCuti)
    });

    
    let tblCuti = $("#table-cuti").DataTable({
      // DataTable configuration options
      "searching": false,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        // "emptyTable": "Tidak ada data"
      },
    //   "paging": true, // Enable pagination
      "lengthMenu": [10, 25, 50], // Set number of records to display per page
      "info": false,
      "ordering": false,
      "ajax": {
        "url": "{{ route('karyawan.cuti.list.karyawan') }}", // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          // Add any additional data you want to pass to the server here
          const dates = $('#start-date').val().split(' - ');
          data.status = $('#status').val()
          data.start_date = dates[0]
          data.end_date = dates[1]
          data.page = data.start / data.length + 1; // Calculate the current page based on start and length
          data.per_page = data.length; // Set the number of records per page
        },
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [
        {
          "data": "st_leave.leave_description",
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
              if(data === 'APPROVED') {
                return '<span class="badge bg-success">Disetujui</span>'
              } else if(data === 'REJECTED') {
                return '<span class="badge bg-danger">Tidak Disetujui</span>';
              } else if(data === 'CANCELED' || data === 'CANCEL') {
                return '<span class="badge bg-warning">Dibatalkan</span>';
              } else if(data === 'WAITINGCANCEL') {
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
          "data": null,
          "title": "Aksi",
          "className": "text-center",
          "orderable": false,
          "render": function(data, type, row) {
            if(row.leavekaryawan_status === 'WAITING') {
              return `
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item btn-edit" href="javascript:void(0);" data-id="${row.leavekaryawan_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                  <a class="dropdown-item btn-delete" href="javascript:void(0);" data-id="${row.leavekaryawan_id}"
                    ><i class="bx bx-x me-1 text-danger"></i> Batalkan</a
                  >
                </div>
              </div>`;
            } else if(row.leavekaryawan_status === 'APPROVED') {
              return `
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item detail-button" href="javascript:void(0);">
                            <i class="bx bx-show me-1 text-info"></i> Selengkapnya
                        </a>
                        <a class="dropdown-item btn-delete" href="javascript:void(0);" data-id="${row.leavekaryawan_id}"
                          ><i class="bx bx-x me-1 text-danger"></i> Batalkan</a
                        >
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
          }
        },
        {
          "data": "leavekaryawan_id",
        },
        {
          "data": "leavekaryawan_quota_use",
        },
        {
          "data": "leavekaryawan_another_quota_use",
        },
        {
          "data": "leavekaryawan_another_st_id",
        },
        {
          "data" : "approval_name"
        }
      ],
      "columnDefs": [
        {
          "targets": [7, 8, 9, 10, 11], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
      ]
    });

    function getStatusText(status) {
      if (status === "CANCEL") {
        return "Dibatalkan";
      } else if (status === "APPROVED") {
        return "Disetujui";
      } else if (status === "REJECTED") {
        return "Tidak Disetujui";
      } else if (status === "WAITING") {
        return "Menunggu";
      } else if (status === 'WAITINGCANCEL') {
        return "Menunggu Pembatalan"
      }
      return "Unknown";
    }

    $("#table-cuti").on("click", ".detail-button", function() {
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Populate the paragraph elements with the row data
      // $("#detailModal #nama-karyawan").text(rowData.karyawan_name);
      $("#detailModal #jenis-cuti").text(rowData.st_leave.leave_description);
      $("#detailModal #tanggal-pengajuan").text(rowData.leavekaryawan_request_date);
      $("#detailModal #alasan-pengajuan").text(rowData.leavekaryawan_request_note);
      $("#detailModal #tanggal-mulai-cuti").text(rowData.leavekaryawan_start_date);
      $("#detailModal #tanggal-berakhir-cuti").text(rowData.leavekaryawan_end_date);
      $("#detailModal #status").text(getStatusText(rowData.leavekaryawan_status));
      $("#detailModal #keputusan-penerimaan").text(rowData.leavekaryawan_approval_date ? rowData.leavekaryawan_approval_date : "-");

      // Populate and show/hide "Alasan Dibatalkan" field based on the status
      if (rowData.leavekaryawan_status === "CANCEL" || rowData.leavekaryawan_status === "WAITINGCANCEL" ) {
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

      // Show the modal
      $("#detailModal").modal("show");
    });
    
    function formatTimeToHHmm(time) {
      const splitTime = time.split(' ')[1]
      const timeParts = splitTime.split(":");
      return timeParts[0] + ":" + timeParts[1];
    }

    setHtmlTitle('{{$title}}')
  });
</script>