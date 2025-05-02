<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5>Pengaturan Pengumuman</h5>
          </div>
          <?php if (in_array('C', request()->get('permission_codes'))) : ?>
            <div class="col-sm-6 text-end">
              <button type="button" class="btn btn-sm btn-warning" id="btnAddPengumuman">+ Pengumuman</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-pengumuman">
          <thead class="table-light">
            <tr>
              <th>Kode Pengumuman</th>
              <th>Title</th>
              <th>Deskripsi</th>
              <th>Tanggal Mulai Aktif</th>
              <th>Tanggal Akhir Aktif</th>
              <th>Target Pengumuman</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addPengumumanModal" tabindex="-1" aria-labelledby="addPengumumanModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPengumumanModalLabel">Atur Pengumuman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddPengumuman" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="announcement_id" name="announcement_id" value="">
        <div class="modal-body">
          <div class="mb-3" id="title-container">
            <label for="title" class="form-label title-case-jadwal lbl-req">Judul</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="mb-3" id="description-container">
            <label for="description" class="form-label title-case-jadwal lbl-req">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
          </div>
          <div class="row mb-3" id="start-container">
            <div class="col">
              <label for="tanggalMulai" class="form-label title-case-jadwal lbl-req">Tanggal Mulai Aktif</label>
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
              <label for="tanggalBerakhir" class="form-label title-case-jadwal lbl-req">Tanggal Berakhir Aktif</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control" id="tanggalBerakhir" name="tanggalBerakhir" required maxlength="10" placeholder="DD-MM-YYYY">
                <span class="icon"><i class="fas fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="mb-3" id="option-container">
            <label for="optionEmployee" class="form-label title-case-jadwal lbl-req">Tampil Untuk</label>
            <select class="form-select" style="width: 100%;" id="optionEmployee" name="optionEmployee" required>
              <option value="" disabled selected>Pilih Tampilkan Kepada Siapa</option>
              <option value="allEmployee">Semua Karyawan</option>
              <option value="specificEmployee">Karyawan Tertentu</option>
            </select>
          </div>
          <div class="mb-3" id="karyawan-container" style="display: none;">
            <label for="announcementEmployee">Karyawan</label>
            <select multiple style="width: 100%;" name="announcementEmployee[]" id="announcementEmployee" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let selectedEmployeeId = [];

    $("#optionEmployee").select2({
      dropdownParent: $("#addPengumumanModal #formAddPengumuman"),
    });
    $("select[name='optionEmployee']").change(function() {
      const selectedOption = $(this).val();

      if (selectedOption === "specificEmployee") {
        $("#karyawan-container").slideDown();
      } else {
        $("#karyawan-container").slideUp();
      }
    });

    $("#announcementEmployee").on("select2:select", function(e) {
      const selectedId = parseInt(e.params.data.id);

      if (!selectedEmployeeId.includes(selectedId)) {
        selectedEmployeeId.push(selectedId);
      }
    });

    $("#announcementEmployee").on("select2:unselect", function(e) {
      const unselectedId = parseInt(e.params.data.id);

      const selectedIndex = selectedEmployeeId.indexOf(unselectedId);
      if (selectedIndex > -1) {
        selectedEmployeeId.splice(selectedIndex, 1);
      }
    });

    $("#announcementEmployee").select2({
      dropdownParent: $("#formAddPengumuman"),
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

    $("#tanggalMulai, #tanggalBerakhir").daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      parentEl: '#addPengumumanModal',
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

    let tblPengaturanPengumuman = $("#table-pengaturan-pengumuman").DataTable({
      // DataTable configuration options
      "searching": true,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Judul Pengumuman",
      },
      "info": false,
      "ordering": true,
      "ajax": {
        "url": `{{ route('user.page.pengaturan.pengumuman.datatable') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {}
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "announcement_id",
          "title": "Kode Pengumuman",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === 'display' || type === 'filter')) {
              return 'PM' + data;
            }
            return data;
          }
        },
        {
          "data": "announcement_title",
          "title": "Judul",
          "className": "text-center"
        },
        {
          "data": "announcement_description",
          "title": "Deskripsi",
          "className": "text-center",
          "render": function(data, type, row) {
            if (type === 'display' && data.length > 15) {
              return data.substr(0, 15) + '...';
            }
            return data;
          }
        },
        {
          "data": "announcement_start_date",
          "title": "Tanggal Mulai Aktif",
          "className": "text-center"
        },
        {
          "data": "announcement_end_date",
          "title": "Tanggal Akhir Aktif",
          "className": "text-center"
        },
        {
          "data": "announcement_is_all",
          "title": "Target Pengumuman",
          "className": "text-center",
          "render": function(data, type, row) {
            if (type === 'display' && data === true) {
              return 'Semua Karyawan'
            } else {
              return 'Spesifik Karyawan'
            }
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
                  <a class="dropdown-item edit-pengumuman" href="javascript:void(0);" data-id="${row.announcement_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif ?>
                <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                <?php endif; ?>
                </div>
              </div>
                `;
          }
        },
        {
          "data": "karyawan_data",
        }
      ],
      "columnDefs": [
        // {
        //   "targets": [6], // Indexes of the columns to be hidden
        //   "visible": false,
        //   "searchable": false,
        // },
        {
          "targets": [4, 5, 6], // Indexes of the columns to be hidden
          "orderable": false,
        },
        {
          "targets": [7], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        }
      ],
    });

    let isEdit = false

    // $('#table-pengaturan-pengumuman').on("click", ".delete-pengumuman", function(e) {
    //   e.preventDefault();

    //   // Get the announcement_id from the data-id attribute of the Delete button
    //   const pengumumanId = $(this).data("id");

    //   const csrfToken = $('meta[name="csrf-token"]').attr('content');
    //   Swal.fire({
    //       html: 'Apakah anda ingin menghapus pengumuman ini?',
    //       icon: 'question',
    //       preConfirm: () => {
    //           Swal.showLoading();
    //           return fetch(`{{url('/user/pengaturan/pengumuman/delete')}}/${pengumumanId}?menu_id=${currentMenuId}`, {
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
    //               Swal.showValidationMessage(`Request failed: ${error}`);
    //           })
    //       },
    //       allowOutsideClick: () => false
    //   }).then((result) => {
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
    //       tblPengaturanPengumuman.row($(this).closest("tr")).remove().draw();
    //     });
    // });

    const today = new Date();

    // Get the individual components of the date
    const day = today.getDate().toString().padStart(2, '0'); // Ensure two digits for day
    const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const year = today.getFullYear();

    // Create the formatted date string in "DD-MM-YYYY" format
    const formattedDate = `${day}-${month}-${year}`;

    $('#table-pengaturan-pengumuman').on("click", ".edit-pengumuman", function(e) {
      isEdit = true
      e.preventDefault();

      // Get the row data associated with the clicked "Edit" button
      const rowData = tblPengaturanPengumuman.row($(this).closest("tr")).data();

      // Now, populate the edit modal with the data from the rowData
      populateEditPengumumanModal(rowData);
    });

    $("#table-pengaturan-pengumuman").on("click", ".btn-delete", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblPengaturanPengumuman.row(row).data();
      Swal.fire({
        html: 'Apakah anda ingin menghapus pengumuman <b>' + data.announcement_title + '</b>?',
        icon: 'question',
        preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanTunjangan.row(row).remove();
          // return true;
          return fetch(`{{route('user.page.pengaturan.pengumuman.destroy', '')}}/${data.announcement_id}?menu_id=${currentMenuId}`, {
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
            html: result.message,
            showCancelButton: false,
            confirmButtonText: "Ok",
            icon: 'error'
          })
          return false;
        }

        toastr.success(result.message);
        tblPengaturanPengumuman.draw();
      });
    })

    function populateEditPengumumanModal(rowData) {
      resetForm('#formAddPengumuman');
      // Update the form fields with the rowData
      $("#announcement_id").val(rowData.announcement_id);
      $("#title").val(rowData.announcement_title);
      $("#description").val(rowData.announcement_description);

      // Format the ISO dates to DD-MM-YYYY format
      const formattedStartDate = formatDateFromISO(rowData.announcement_start_date);
      const formattedEndDate = formatDateFromISO(rowData.announcement_end_date);

      // Populate the input fields with formatted date values
      $("#tanggalMulai").val(formattedStartDate);
      $("#tanggalBerakhir").val(formattedEndDate);

      if (rowData.announcement_is_all === true) {
        $("#optionEmployee").val("allEmployee");
      } else {
        $("#optionEmployee").val("specificEmployee");
        $("#karyawan-container").slideDown();

        let karyawans = rowData.karyawan_data;
        if (karyawans.length > 0) {
          let karyawanOpts = '';
          karyawans.forEach(kr => {
            karyawanOpts += `<option value="${parseInt(kr.karyawan_id)}" selected>${kr.karyawan_name}</option>`;
            selectedEmployeeId.push(parseInt(kr.karyawan_id))
          })
          $("#announcementEmployee").html(karyawanOpts);
        }
      }

      // Open the edit modal
      $("#addPengumumanModal").modal("show");

      $("#addPengumumanModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    function resetAndSlideUpForms() {
      isEdit = false
      selectedEmployeeId = []
      $(".error-message").remove();
      resetForm('#formAddPengumuman');
      $("#karyawan-container").slideUp();
      $("#announcementEmployee").empty().trigger("change");
    }

    function openAddPengumumanModal() {
      resetAndSlideUpForms()
      jQuery("#addPengumumanModal").modal("show");

      $("#addPengumumanModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    $("#formAddPengumuman").submit(function(event) {
      event.preventDefault();
      $(".spinner-box").css({
        'display': 'table'
      });
      $(".error-message").remove();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      const formData = $(this).serializeArray();
      const jsonData = {}; // To store the JSON data

      formData.forEach(function(item) {
        if (item.name === "title") {
          jsonData["announcement_title"] = item.value
        } else if (item.name === "description") {
          jsonData["announcement_description"] = item.value
        } else if (item.name === "tanggalMulai") {
          jsonData["announcement_start_date"] = item.value
        } else if (item.name === "tanggalBerakhir") {
          jsonData["announcement_end_date"] = item.value
        } else if (item.name === 'announcement_id' && item.value !== '') {
          jsonData["announcement_id"] = item.value
        } else if (item.name === "optionEmployee") {
          jsonData["announcement_is_all"] = item.value === 'allEmployee' ? true : false
        }

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      });

      let hasErrors = false;

      if (!jsonData["announcement_title"]) {
        appendError($("#title"), "Mohon isi judul.");
        hasErrors = true;
      }

      if (!jsonData["announcement_description"]) {
        appendError($("#description"), "Mohon isi deskripsi.");
        hasErrors = true;
      }

      if (!jsonData["announcement_start_date"]) {
        appendError($("#tanggalMulai"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if (!jsonData["announcement_end_date"]) {
        appendError($("#tanggalBerakhir"), "Mohon masukan tanggal.");
        hasErrors = true;
      }

      if (jsonData["announcement_start_date"] && jsonData["announcement_start_date"] !== '' && !isValidDateFormat(jsonData["announcement_start_date"])) {
        appendError($("#tanggalMulai"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }

      if (jsonData["announcement_end_date"] && jsonData["announcement_end_date"] !== '' && !isValidDateFormat(jsonData["announcement_end_date"])) {
        appendError($("#tanggalBerakhir"), "Mohon masukan dengan format DD-MM-YYYY.");
        hasErrors = true;
      }

      if (jsonData["announcement_end_date"] && jsonData["announcement_end_date"] !== '' && isValidDateFormat(jsonData["announcement_end_date"]) && jsonData["announcement_start_date"] && jsonData["announcement_start_date"] !== '' && isValidDateFormat(jsonData["announcement_start_date"])) {
        const dateStr1 = jsonData["announcement_start_date"];
        const dateParts1 = dateStr1.split('-');
        const day1 = parseInt(dateParts1[0], 10);
        const month1 = parseInt(dateParts1[1] - 1, 10);
        const year1 = parseInt(dateParts1[2], 10);
        const date1 = new Date(year1, month1, day1);

        const dateStr2 = jsonData["announcement_end_date"];
        const dateParts2 = dateStr2.split('-');
        const day2 = parseInt(dateParts2[0], 10);
        const month2 = parseInt(dateParts2[1] - 1, 10);
        const year2 = parseInt(dateParts2[2], 10);
        const date2 = new Date(year2, month2, day2);

        if (date2 < date1) {
          appendError($("#tanggalBerakhir"), "Tanggal berakhir kurang dari tanggal mulai.");
          hasErrors = true;
        }
      }

      if (hasErrors) {
        $(".spinner-box").fadeOut();
        return false;
      }

      if (!jsonData["announcement_is_all"] && !selectedEmployeeId) {
        Swal.fire({
          title: 'Error',
          text: 'Mohon pilih bagian "Tampil Untuk"',
          icon: 'error',
          showCancelButton: false,
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        });

        $(".spinner-box").fadeOut();
        
        $(".error-message").remove();
        return false;
      }

      jsonData["announcement_employee"] = selectedEmployeeId
      jsonData["isEdit"] = isEdit

      $.ajax({
        type: "POST",
        url: `{{ route('user.page.pengaturan.pengumuman.save') }}?menu_id=${currentMenuId}`,
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        dataType: "json",
        success: function(response) {
          $(".spinner-box").fadeOut();
          if (response.success) {
            toastr.success(response.message);
            resetAndSlideUpForms()
            isEdit = false
            $("#addPengumumanModal").modal("hide");
            tblPengaturanPengumuman.draw(); // Refresh the DataTable
          } else {
            toastr.error(response.message);
          }
        },
        error: function(xhr, status, error) {
          $(".spinner-box").fadeOut();
          console.log(xhr.responseText);
          if(xhr.responseText.includes("message")) {
            let response = JSON.parse(xhr.responseText)
            toastr.error(response.message)  
          } else {
            toastr.error("An error occurred while submitting the form.");
          }
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

    $("#btnAddPengumuman").click(function() {
      isEdit = false;
      openAddPengumumanModal();
    });

    setHtmlTitle('{{$title}}')
  });
</script>