<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5>Pengaturan Absensi</h5>
          </div>
          <?php if (in_array('C', request()->get('permission_codes'))) : ?>
            <div class="col-sm-6 text-end">
              <button type="button" class="btn btn-sm btn-warning" id="btnAddJadwal">+ Jadwal</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-absensi">
          <thead class="table-light">
            <tr>
              <th>Kode Absensi</th>
              <th>Deskripsi</th>
              <th>Masuk</th>
              <th>Mulai Istirahat</th>
              <th>Selesai Istirahat</th>
              <th>Keluar</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

@include('user.pengaturan.kehadiran.create')

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";

    function formatTimeToHHmm(time) {
      const timeParts = time.split(":");
      return timeParts[0] + ":" + timeParts[1];
    }

    function openAddJadwalModal() {
      $("#formAddJadwal")[0].reset(); // Reset the form fields

      jQuery("#addJadwalModal").modal("show");

    }

    let tblPengaturanAbsensi = $("#table-pengaturan-absensi").DataTable({
      // DataTable configuration options
      "searching": true,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Deskripsi / Kode Absensi",
      },
      "info": false,
      "ordering": true,
      "ajax": {
        "url": `{{ route('user.page.pengaturan.kehadiran.datatable') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {}
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "attendance_id",
          "title": "Kode Absensi",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === 'display' || type === 'filter')) {
              return 'AB' + data;
            }
            return data;
          }
        },
        {
          "data": "attendance_description",
          "title": "Deskripsi",
          "className": "text-center"
        },
        {
          "data": "attendance_check_in",
          "title": "Masuk",
          "className": "text-center",
          "render": function(data, type, row, meta) {
            // Format the time data to be "HH:mm" (remove seconds part)
            if (type === 'display' || type === 'filter') {
              const timeParts = data.split(":");
              return timeParts[0] + ":" + timeParts[1];
            }
            return data;
          }
        },
        {
          "data": "attendance_start_break",
          "title": "Mulai Istirahat",
          "className": "text-center",
          "render": function(data, type, row, meta) {
            // Format the time data to be "HH:mm" (remove seconds part)
            if (data && (type === 'display' || type === 'filter')) {
              const timeParts = data.split(":");
              return timeParts[0] + ":" + timeParts[1];
            }
            return data;
          }
        },
        {
          "data": "attendance_end_break",
          "title": "Selesai Istirahat",
          "className": "text-center",
          "render": function(data, type, row, meta) {
            // Format the time data to be "HH:mm" (remove seconds part)
            if (data && (type === 'display' || type === 'filter')) {
              const timeParts = data.split(":");
              return timeParts[0] + ":" + timeParts[1];
            }
            return data;
          }
        },
        {
          "data": "attendance_check_out",
          "title": "Keluar",
          "className": "text-center",
          "render": function(data, type, row, meta) {
            // Format the time data to be "HH:mm" (remove seconds part)
            if (type === 'display' || type === 'filter') {
              const timeParts = data.split(":");
              return timeParts[0] + ":" + timeParts[1];
            }
            return data;
          }
        },
        {
          'data': "attendance_status_active",
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
                  <a class="dropdown-item edit-jadwal" href="javascript:void(0);" data-id="${row.attendance_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item deactivate-jadwal" href="javascript:void(0);" data-id="${row.attendance_id}"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                <?php endif; ?>
    
                </div>
              </div>
                `;
          }
        },
        {
          "data": "attendance_working_day",
        },
        {
          'data': "attendance_check_in_tolerance"
        },
        {
          'data': "attendance_location_address"
        },
        {
          'data': "attendance_location_longitude"
        },
        {
          'data': "attendance_location_latitude"
        },
        {
          'data': "attendance_break_type"
        },
        {
          'data': "attendance_check_in_status_photo"
        },
        {
          'data': "attendance_check_out_status_photo"
        },
        {
          'data': "attendance_break_status_photo"
        },
        {
          'data': "attendance_break_type"
        },
        {
          'data': "karyawan"
        },
      ],
      "columnDefs": [
        // Define which columns to hide
        {
          "targets": [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
        {
          "target": [7],
          "orderable": false,
        }
      ],
    });

    // $("#table-pengaturan-absensi").on("click", ".delete-jadwal", function(e) {
    //   e.preventDefault();

    //   // Get the attendance_id from the data-id attribute of the Delete button
    //   const attendanceId = $(this).data("id");

    //   const csrfToken = $('meta[name="csrf-token"]').attr('content');
    //   Swal.fire({
    //       html: 'Apakah anda ingin menghapus jadwal ini?',
    //       icon: 'question',
    //       preConfirm: () => {
    //           Swal.showLoading();
    //           return fetch(`{{url('/user/pengaturan/kehadiran/delete')}}/${attendanceId}?menu_id=${currentMenuId}`, {
    //               method: 'DELETE',
    //               body: new URLSearchParams($.param({_token: $("meta[name=csrf-token]").attr('content')}))
    //           })
    //           .then(response => {
    //               if (!response.ok) {
    //                   return response.text().then(res => {
    //                       throw new Error(res);
    //                   })
    //               }
    //               return response.json()
    //           })
    //           .catch(error => {
    //             const errorMessage = error.message ? JSON.parse(error.message).message : error;

    //             // Display the error message using SweetAlert
    //             Swal.showValidationMessage(`${errorMessage}`);
    //           })
    //       },
    //       allowOutsideClick: () => false
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
    //       tblPengaturanAbsensi.row($(this).closest("tr")).remove().draw();
    //     });
    // });

    $("#table-pengaturan-absensi").on("click", ".deactivate-jadwal", function() {
      // Get the attendance_id from the data-id attribute of the Delete button
      const rowData = tblPengaturanAbsensi.row($(this).closest("tr")).data();

      // Get the attendance_id and attendance_status_active from the row data
      const attendanceId = rowData.attendance_id;
      const status = rowData.attendance_status_active;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if (status === false) {
        Swal.fire({
          html: 'Jadwal sudah di nonaktifkan.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: 'Apakah anda ingin menonaktifkan jadwal ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        preConfirm: async () => {
          Swal.showLoading();

          const requestData = {
            _token: csrfToken,
            statusOnly: true,
            isEdit: true,
            attendance_id: attendanceId,
            attendance_status_active: false
          };

          return fetch(`{{ route('user.page.pengaturan.kehadiran.save') }}?menu_id=${currentMenuId}`, {
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
              tblPengaturanAbsensi.ajax.reload();

              return response.json();
            })
            .catch(error => {
              Swal.showValidationMessage(`Request failed: ${error}`);
            });
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
        tblPengaturanAbsensi.row($(this).closest("tr")).remove().draw();
      });
    });

    $('#table-pengaturan-absensi').on("click", ".edit-jadwal", function(e) {
      // isEdit = true
      e.preventDefault();
      $('#formAddJadwal [type=reset]').click();

      // Get the row data associated with the clicked "Edit" button
      const rowData = tblPengaturanAbsensi.row($(this).closest("tr")).data();

      // Now, populate the edit modal with the data from the rowData
      populateEditJadwalModal(rowData);
    });

    // let marker;

    function initMap(latitude, longitude) {
      const mapOptions = {
        zoom: 15,
        center: {
          lat: latitude,
          lng: longitude
        },
      };

      const map = new google.maps.Map(document.getElementById("map"), mapOptions);

      // Create a marker with the initial position
      const marker = new google.maps.Marker({
        position: {
          lat: latitude,
          lng: longitude
        },
        map: map,
        draggable: true,
      });

      // Add an event listener to update latitude and longitude on map click
      google.maps.event.addListener(map, "click", function(event) {
        const clickedLocation = event.latLng;

        // Update marker position
        marker.setPosition(clickedLocation);

        // Update latitude and longitude inputs
        $("#latitude").val(clickedLocation.lat());
        $("#longitude").val(clickedLocation.lng());

        // Reverse geocode to get the address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({
          location: clickedLocation
        }, function(results, status) {
          if (status === "OK") {
            if (results[0]) {
              const address = results[1].formatted_address;
              $("#companyAddress").val(address);
            }
          }
        });
      });

      // Add an event listener to update latitude, longitude, and address on marker drag
      google.maps.event.addListener(marker, "dragend", function(event) {
        const draggedLocation = event.latLng;

        // Update latitude and longitude inputs
        $("#latitude").val(draggedLocation.lat());
        $("#longitude").val(draggedLocation.lng());

        // Reverse geocode to get the address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({
          location: draggedLocation
        }, function(results, status) {
          if (status === "OK") {
            if (results[0]) {
              const address = results[1].formatted_address;
              $("#companyAddress").val(address);
            }
          }
        });
      });
    }

    function subtractTime(time1, time2) {
      // Parse the time strings and extract hours and minutes
      const [hours1, minutes1] = time1.split(':').map(Number);
      const [hours2, minutes2] = time2.split(':').map(Number);

      // Calculate the total minutes for each time
      const totalMinutes1 = hours1 * 60 + minutes1;
      const totalMinutes2 = hours2 * 60 + minutes2;

      // Perform the subtraction
      const differenceMinutes = totalMinutes1 - totalMinutes2;

      // Calculate hours and minutes for the result
      const resultHours = Math.floor(differenceMinutes / 60);
      const resultMinutes = differenceMinutes % 60;

      // Format the result as "HH:mm"
      const result = `${String(resultHours).padStart(2, '0')}:${String(resultMinutes).padStart(2, '0')}`;
      return result;
    }

    function populateEditJadwalModal(rowData) {
      if (rowData.attendance_status_active === true) {
        $("#activeTrue").prop("checked", true);
      } else {
        $("#activeFalse").prop("checked", true);
      }

      if (rowData.attendance_is_all === true) {
        $("#optionEmployee").val("allEmployee");
      } else {
        $("#optionEmployee").val("specificEmployee");
        $("#karyawan-container").slideDown();

        let karyawans = rowData.karyawan;
        if (karyawans.length > 0) {
          let karyawanOpts = '';
          karyawans.forEach(kr => {
            karyawanOpts += `<option value="${parseInt(kr.karyawan_id)}" selected>${kr.karyawan_name}</option>`;
            // selectedEmployeeId.push(parseInt(kr.karyawan_id))
          })
          $("#jadwalEmployee").html(karyawanOpts);
        }
      }


      $("#excodeattendance-container").slideDown();
      $("#excodeattendance").val('AB' + rowData.attendance_id);

      // Update the form fields with the rowData
      $("#attendance_id").val(rowData.attendance_id);
      $("#isEdit").val('1');

      // console.log($("#isEdit").val(), 'isEdit 2')

      $("#description").val(rowData.attendance_description);
      // Populate other form fields similarly

      if (rowData.attendance_working_day.length !== 0 && rowData.attendance_working_day !== "") {
        const workingDay = JSON.parse(rowData.attendance_working_day)
        workingDay.forEach(function(day) {
          $(`input[name="workingDays[]"][value="${day}"]`).prop("checked", true);
        });
      }

      $("#start").val(formatTimeToHHmm(rowData.attendance_check_in));
      $("#end").val(formatTimeToHHmm(rowData.attendance_check_out));
      $("#attendance_id").val(rowData.attendance_id);
      if (rowData.attendance_check_in_tolerance) {
        $("#lateTolerance").val(formatTimeToHHmm(rowData.attendance_check_in_tolerance));
      }

      $("#companyAddress").val(rowData.attendance_location_address);

      if (rowData.attendance_location_latitude) {
        $("#latitude").val(rowData.attendance_location_latitude);
      }

      if (rowData.attendance_location_longitude) {
        $("#longitude").val(rowData.attendance_location_longitude);
      }

      // Set the breaktime related fields based on the data
      if (rowData.attendance_break_status) {
        $("#useBreaktime").prop("checked", true);
        $("#breaktimeForms").slideDown();
        $("#startBreak").val(formatTimeToHHmm(rowData.attendance_start_break))
      } else {
        $("#useBreaktime").prop("checked", false);
        $("#breaktimeForms").slideUp();
      }

      if (rowData.attendance_location_status) {
        $("#useLocation").prop("checked", true);
        $("#locationForms").slideDown();
      } else {
        $("#useLocation").prop("checked", false);
        $("#locationForms").slideUp();
      }

      if (rowData.attendance_break_type === "SPECIFIC") {
        $("#specificTimeOption").prop("checked", true);
        $("#durationOption").prop("checked", false);
        $("#specificTimeForm").slideDown();
        $("#durationForm").slideUp();
        $("#specificTime").val(formatTimeToHHmm(rowData.attendance_end_break));
      } else if (rowData.attendance_break_type === "DURATION") {
        $("#specificTimeOption").prop("checked", false);
        $("#durationOption").prop("checked", true);
        $("#specificTimeForm").slideUp();
        $("#durationForm").slideDown();
        $("#durationTime").val(formatTimeToHHmm(subtractTime(rowData.attendance_end_break, rowData.attendance_start_break)));
      } else {
        $("#specificTimeOption").prop("checked", false);
        $("#durationOption").prop("checked", false);
        $("#specificTimeForm").slideUp();
        $("#durationForm").slideUp();
      }

      // Set the checkInPhoto and checkOutPhoto checkboxes based on the data
      $("#checkInPhoto").prop("checked", rowData.attendance_check_in_status_photo);
      $("#checkOutPhoto").prop("checked", rowData.attendance_check_out_status_photo);
      $("#breakPhoto").prop("checked", rowData.attendance_break_status_photo);

      // Open the edit modal
      $("#addJadwalModal").modal("show");

      if (rowData.attendance_location_status && rowData.attendance_location_latitude && rowData.attendance_location_longitude) {
        initMap(
          parseFloat(rowData.attendance_location_latitude),
          parseFloat(rowData.attendance_location_longitude)
        );

        $("#map").show();
        $("#map-container").show();
      }

      // $("#addJadwalModal").on("hidden.bs.modal", function () {
      //   resetAndSlideUpForms() // Reset the form fields when the modal is closed
      // });
    }

    $("#btnAddJadwal").click(function() {
      openAddJadwalModal();
    });

    $("#addJadwalModal").on("hidden.bs.modal", function() {
      tblPengaturanAbsensi.draw(); // Refresh the DataTable
    });

    setHtmlTitle('{{$title}}')
  });
</script>