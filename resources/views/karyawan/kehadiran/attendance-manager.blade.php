<head>
  <style>
    #start-date,
    #end-date {
      width: 100%; /* Adjust the width as needed */
    }
  </style>
</head>

<script>
    // Check if karyawan_ismanager is equal to 0
    @if(request()->get('karyawan')->karyawan_ismanager == 0)
        // Redirect to the desired URL
        window.location.href = "{{ url('karyawan/kehadiran/view/karyawan') }}";
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
                      <label for="start-date">Tanggal Awal</label>
                      <input type="text" id="start-date" name="start-date" class="form-control">
                  </div>
                  <div class="col-md-3">
                      <label for="end-date">Tanggal Akhir</label>
                      <input type="text" id="end-date" name="end-date" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label for="listEmployee">Karyawan</label>
                    <select multiple style="width: 100%; height: auto; padding: 8px; margin-top: 5px;" name="listEmployee[]" id="listEmployee" class="form-control" data-placeholder=" Pilih Karyawan" autocomplete="off"></select>
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
              <h5>Daftar Absensi</h5>
            </div>
        </div>
        </div>
    <div class="row">
        <div class="col-sm-12">
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

<!-- Add your JavaScript code here -->
<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {    
    let selectedEmployeeId = [];
    $("#listEmployee").select2({
		  // dropdownParent: $("#formAddCuti"),
      // tags: true,
			ajax: {
				url: "{{route('user.page.karyawan.select')}}",
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
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedFirstDay, 
    });

    $(`#end-date`).daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedLastDay, // Set the calculated first day as the start date
      endDate: formattedLastDay, 
    });

    $("#searchButton").on("click", function () {
      tblKehadiran.draw();
    });
    
    $("#resetButton").on("click", function () {
        $(`#start-date`).val(formattedFirstDay);
        $(`#end-date`).val(formattedLastDay);
        $("#listEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
        selectedEmployeeId = [];
        tblKehadiran.draw();
    });

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
      "ordering": false,
      "ajax": {
        "url": "{{ route('karyawan.kehadiran.list.manager') }}", // Replace with your actual route to fetch data
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
      "columns": [
        {
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
          "data" : "attendancekaryawan_check_in_photo",
        },
        {
          "data" : "attendancekaryawan_check_out_photo",
        },
        {
          "data" : "attendancekaryawan_break_start_photo",
        },
        {
          "data" : "attendancekaryawan_break_end_photo",
        },
        {
          "data" : "attendancekaryawan_break_end_note",
        },
        {
          "data" : "attendancekaryawan_break_start_note",
        },
        {
          "data" : "attendancekaryawan_check_out_note",
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
          "data" : "attendancekaryawan_id"
        },
        {
          "data" : "leavekaryawan_id"
        },
        {
          "data" : "attendancekaryawan_check_in_late_note"
        }
      ],
      "columnDefs": [
        {
          "targets": [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
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
      $(".spinner-box").css({ display: "table" });
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

      if(["leavekaryawan_id"] !== null && jsonData["leavekaryawan_id"] !== "") { 
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

          if(jsonData["attendancekaryawan_check_in_note"].trim() === '') {
            appendError($("#checkInNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if(jsonData["attendancekaryawan_check_in_location"].trim() === '') {
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

          if(jsonData["attendancekaryawan_break_start_note"].trim() === '') {
            appendError($("#startBreakNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if(jsonData["attendancekaryawan_break_start_location"].trim() === '') {
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

          if(jsonData["attendancekaryawan_break_end_note"].trim() === '') {
            appendError($("#endBreakNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if(jsonData["attendancekaryawan_break_end_location"].trim() === '') {
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
          
          if(jsonData["attendancekaryawan_check_out_note"].trim() === '') {
            appendError($("#checkOutNote"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }

          if(jsonData["attendancekaryawan_check_out_location"].trim() === '') {
            appendError($("#checkOutLocation"), "Mohon hanya gunakan '-' untuk mengosongkan.");
            hasErrors = true;
          }
      }

      if (hasErrors) {
        $(".spinner-box").fadeOut()
        return false;
      }

      $.ajax({
        url: `{{ route('karyawan.kehadiran.edit') }}`,
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
          toastr.error("An error occurred while submitting the form.");
        }
      });
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
      if(splitTime[1]) {
        const timeParts = splitTime[1].split(":");
        return timeParts[0] + ":" + timeParts[1];
      } else {
        return null
      }
    }

    setHtmlTitle('{{$title}}')
  });
</script>