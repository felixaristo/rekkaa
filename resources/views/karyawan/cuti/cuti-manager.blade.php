<head>
  <style>
    #start-date,
    #end-date {
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

<script>
    // Check if karyawan_ismanager is equal to 0
    @if(request()->get('karyawan')->karyawan_ismanager == 0)
        // Redirect to the desired URL
        window.location.href = "{{ url('karyawan/cuti/view/karyawan') }}";
    @endif
</script>

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
                      <select id="status" name="status" class="form-control">
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
                    <select multiple style="width: 100%; height: auto; padding: 8px; margin-top: 5px;" name="listEmployee[]" id="listEmployee" class="form-control" autocomplete="off" data-placeholder=" Pilih Karyawan"></select>
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
      <div class="card-header m-2">
        <div class="row">
          <div class="col-sm-6">
            <h5>Daftar Cuti</h5>
          </div>
          <div class="col-sm-6 d-flex justify-content-end">
             <!-- "Export" button -->
                <div class="export-button-container">
                    <button type="button" class="btn btn-sm btn-outline-warning"><i class='bx bxs-file-export'></i> Export</button>
                </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <table class="table table-hover display nowrap" style="width: 100%" id="table-cuti">
              <thead>
                <tr>
                    <th>Nama Karyawan</th>
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

<!-- Modal -->
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

<!-- Add your JavaScript code here -->
<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {  
    let selectedEmployeeId = [];
    
    $("#detailModal").on("click", ".btn-approve", function(e) {
      e.preventDefault();
      
      const rowData = tblCuti.row($(this).closest("tr")).data();

      const cutiId = $("#leavekaryawan_id").val();
      const status = $("#status").val();

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
            const response = await fetch("{{ route('karyawan.cuti.pengajuan') }}", {
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

    $("#detailModal").on("click", ".btn-reject", function() {
      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const cutiId = rowData.leavekaryawan_id;
      const status = rowData.leavekaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if(status !== 'WAITING' && status !== 'WAITINGCANCEL') {
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
            if(rowData.leavekaryawan_status === 'WAITING') {
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
          console.log('result', result)
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
          tblCuti.row($(this).closest("tr")).remove().draw();
        });
    });

    $("#listEmployee").select2({
		  // dropdownParent: $("#formAddCuti"),
      // tags: true,
			ajax: {
				url: "{{route('karyawan.cuti.karyawan.select')}}",
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
        format: 'DD-MM-YYYY',
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedLastDay, 
    });

    // $("#start-date").on("apply.daterangepicker", function () {
    //     tblCuti.draw();
    // });
    
    $("#searchButton").on("click", function () {
      tblCuti.draw();
    });
    
    $("#resetButton").on("click", function () {
        $("#start-date").val(`${formattedFirstDay} - ${formattedLastDay}`);
        $('#start-date').data('daterangepicker').setStartDate(formattedFirstDay);
        $('#start-date').data('daterangepicker').setEndDate(formattedLastDay);
        $("#listEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
        $("#status").val(''); // Remove all options from the select2 dropdown
        selectedEmployeeId = [];
        tblCuti.draw();
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
      // "paging": true, // Enable pagination
      "lengthMenu": [10, 25, 50], // Set number of records to display per page
      "info": false,
      "ordering": false,
      "ajax": {
        "url": "{{ route('karyawan.cuti.list.manager') }}", // Replace with your actual route to fetch data
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
        },
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [
        {
          "data": "karyawan_name",
          "title": "Nama Karyawan",
          "className": "text-center",
        },
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
            if(row.leavekaryawan_status === 'WAITING' || row.leavekaryawan_status === 'WAITINGCANCEL') {
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


          }
        },
        {
          "data": "leavekaryawan_id",
        },
        {
          "data": "leavekaryawan_cancel_note",
        },
        {
          "data" : "approval_name"
        }
      ],
      "columnDefs": [
        {
          "targets": [8, 9, 10], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
      ]
    });

    $("#table-cuti").on("click", ".detail-button", function() {
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Populate the paragraph elements with the row data
      $("#detailModal #nama-karyawan").text(rowData.karyawan_name);
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
          $("#detailModal #dibatalkan-oleh").text(rowData.approval_by);
      } else {
          $("#detailModal .alasan-ditolak-field").hide();
          $("#detailModal .dibatalkan-oleh-field").hide();
      }

      if (rowData.leavekaryawan_status === "APPROVED") {
          $("#detailModal .disetujui-oleh-field").show();
          $("#detailModal #disetujui-oleh").text(rowData.approval_by);
      } else {
          $("#detailModal .disetujui-oleh-field").hide();
      }

      // Show the modal
      $("#detailModal").modal("show");
    });

    // $("#table-cuti").on("click", ".cancel-button", function() {
    //   // Get the leave_id from the data-id attribute of the Delete button
    //   const rowData = tblCuti.row($(this).closest("tr")).data();

    //   // Get the leave_id and leavekaryawan_status from the row data
    //   const cutiId = rowData.leavekaryawan_id;
    //   const status = rowData.leavekaryawan_status;

    //   const csrfToken = $('meta[name="csrf-token"]').attr('content');

    //   if(status !== 'WAITING' && status !== 'APPROVED') {
    //     Swal.fire({
    //       html: 'Tidak dapat mengubah Cuti yang sudah di tindak.',
    //       confirmButtonText: "Ok",
    //       showCancelButton: false,
    //       icon: 'error'
    //     })
    //     return false
    //   }

    //   Swal.fire({
    //     html: 'Apakah anda ingin membatalkan cuti ini?',
    //     icon: 'question',
    //     showCancelButton: true,
    //     confirmButtonText: 'Ya',
    //     cancelButtonText: 'Tidak',
    //     reverseButtons: true,
    //     preConfirm: async () => {
    //         const swalResult = await Swal.fire({
    //             html: 'Masukan alasan anda membatalkan',
    //             input: 'text',
    //             showCancelButton: true,
    //             allowOutsideClick: false,
    //             confirmButtonText: 'Oke',
    //             cancelButtonText: 'Tidak',
    //             reverseButtons: true,
    //             inputValidator: (value) => {
    //                 if (!value) {
    //                     return 'Mohon masukkan alasan anda';
    //                 }
    //             },
    //         });

    //         if (swalResult.isConfirmed) {
    //             const reason = swalResult.value;
    //             Swal.showLoading();
    //             const requestData = {
    //                 _token: csrfToken,
    //                 isEdit: true,
    //                 leavekaryawan_id: cutiId,
    //                 leavekaryawan_status: "CANCEL",
    //                 leavekaryawan_type: "APPROVAL",
    //                 leavekaryawan_cancel_note: reason, // Add the reason to the request data
    //             };

    //             return fetch("{{ route('karyawan.cuti.pengajuan') }}", {
    //                 method: 'POST',
    //                 body: JSON.stringify(requestData),
    //                 headers: {
    //                     'Content-Type': 'application/json'
    //                 }
    //             })
    //             .then(response => {
    //                 Swal.close(); // Close the modal

    //                 if (!response.ok) {
    //                     return response.text().then(res => {
    //                         throw new Error(res);
    //                     })
    //                 }
    //                 tblCuti.ajax.reload();

    //                 return response.json();
    //             })
    //             .catch(error => {
    //                 Swal.showValidationMessage(`Request failed: ${error}`);
    //             });
    //         }
    //     },
    //     allowOutsideClick: () => false
    //   }).then((result) => {
    //       console.log('result', result)
    //       result = result.value;
    //       if(result == undefined) {
    //           return false;
    //       }
          
    //       if (!result.success) {

    //           Swal.fire({
    //               title: result.message,
    //               confirmButtonText: "Ok",
    //               type: 'error'
    //           })
    //           return false;
    //       }

    //       toastr.success(result.message);
    //       tblCuti.row($(this).closest("tr")).remove().draw();
    //     });
    // });

    function formatDate(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1);
        const day = (date.getDate() < 10 ? '0' : '') + date.getDate();
        
        return `${year}-${month}-${day}`;
    }

    $("#table-cuti").on("click", ".approve-button", function() {
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
            const response = await fetch("{{ route('karyawan.cuti.pengajuan') }}", {
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

    $("#table-cuti").on("click", ".reject-button", function() {
      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblCuti.row($(this).closest("tr")).data();

      // Get the leave_id and leavekaryawan_status from the row data
      const cutiId = rowData.leavekaryawan_id;
      const status = rowData.leavekaryawan_status;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if(status !== 'WAITING' && status !== 'WAITINGCANCEL') {
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
            if(rowData.leavekaryawan_status === 'WAITING') {
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
          console.log('result', result)
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
          tblCuti.row($(this).closest("tr")).remove().draw();
        });
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

    // $.fn.dataTable.ext.search.push(
    //   function(settings, data, dataIndex) {
    //     const dates = $('#start-date').val().split(' - ');

    //     const startDate = new Date($(dates[0]).val());
    //     const endDate = new Date($(dates[1]).val());
    //     const dateStr = data[0]; // Assuming "Tanggal" column is the first column in your data

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

    $(".export-button-container button").on("click", function() {
        $(".spinner-box").css({ display: "table" });
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        // Get input values
        const dates = $('#start-date').val().split(' - ');

        const startDate = dates[0];
        const endDate = dates[1];

        // Create the data object for the POST request
        const data = {
            start_date: startDate || null,
            end_date: endDate || null,
        };

        // Make the POST request to your API endpoint
        $.ajax({
            url: "{{ route('karyawan.cuti.manager.export') }}", // Replace with your actual API endpoint URL
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
            },
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function(response) {
              window.open(response, '_blank')
              window.close()
              $(".spinner-box").fadeOut();
            },
            error: function(error) {
              // Handle errors, if any
              console.error(error);
              $(".spinner-box").fadeOut();
            },
        });
    });

    function formatTimeToHHmm(time) {
      const splitTime = time.split(' ')[1]
      const timeParts = splitTime.split(":");
      return timeParts[0] + ":" + timeParts[1];
    }

    setHtmlTitle('{{$title}}')
  });
</script>