<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5>Pengaturan Cuti</h5>
          </div>
          <?php if (in_array('C', request()->get('permission_codes'))) : ?>
            <div class="col-sm-6 text-end">
              <button type="button" class="btn btn-sm btn-warning" id="btnAddCuti">+ Cuti</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-cuti">
          <thead class="table-light">
            <tr>
              <th>Kode Cuti</th>
              <th>Deskripsi</th>
              <th>Tipe</th>
              <th>Tanggal Mulai Berlaku</th>
              <th>Tanggal Akhir Berlaku</th>
              <th>Masa Tenggang</th>
              <th>Kuota Cuti</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addCutiModal" tabindex="-1" aria-labelledby="addCutiModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered mx-auto" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCutiModalLabel">Pengaturan Cuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddCuti" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="leave_id" name="leave_id" value="">
        <div class="modal-body">
          <div class="mb-3" style="display: none;" id="excodeleave-container">
            <label for="excodeleave" class="title-case-jadwal">Kode Cuti</label>
            <input type="text" class="form-control" id="excodeleave" name="excodeleave" disabled>
          </div>
          <div class="mb-3" id="description-container">
            <label for="description" class="form-label title-case-jadwal lbl-req">Deskripsi</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
          <div class="mb-3" id="tipe-container">
            <label for="leaveType" class="form-label title-case-jadwal lbl-req">Tipe Cuti</label>
            <select class="form-select" style="width: 100%;" id="leaveType" name="leaveType" required>
              <option value="" disabled selected>Pilih Tipe Cuti</option>
              <option value="PAID">Dibayar</option>
              <option value="UNPAID">Tidak Dibayar</option>
            </select>
          </div>
          <div class="mb-3" id="description-container">
            <label for="masaBerlaku" class="form-label title-case-jadwal">Masa Berlaku</label>
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
          <div class="mb-3" id="grace-container">
            <label for="masaTenggang" class="form-label title-case-jadwal lbl-req">Masa Tenggang</label>
            <select class="form-select" id="masaTenggang" style="width: 100%;" name="masaTenggang" required>
              <option value="" disabled selected>Pilih Masa Tenggang</option>
              <option value="specificDay">Spesifik Tanggal</option>
              <option value="notApplicable">Tidak Berlaku</option>
            </select>
          </div>
          <div id="specificDateForm" style="display: none;">
            <div class="row mb-3" id="grace-date-container">
              <div class="col">
                <label for="specificDate" class="form-label title-case-jadwal lbl-req">Tanggal Masa Tenggang</label>
              </div>
              <div class="col">
                <div class="input-container">
                  <input type="text" class="form-control" id="specificDate" name="specificDate" required maxlength="10" placeholder="DD-MM-YYYY">
                  <span class="icon"><i class="fas fa-calendar"></i></span>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-3" id="kuota-container">
            <label for="kuotaCuti" class="form-label title-case-jadwal">Kuota Cuti</label>
            <input type="number" min="1" class="form-control" id="kuotaCuti" name="kuotaCuti" required>
          </div>
          <div class="mb-3" id="option-container">
            <div class="col mb-2">
              <label for="optionEmployee" class="title-case-jadwal lbl-req">Diberikan Kepada</label>
            </div>
            <select class="form-select" style="width: 100%;" id="optionEmployee" name="optionEmployee" required>
              <option value="" disabled selected>Pilih Target Karyawan</option>
              <option value="allEmployee">Semua Karyawan</option>
              <option value="specificEmployee">Karyawan Tertentu</option>
              <option value="2">Hanya Perempuan</option>
              <option value="3">Hanya Laki-laki</option>
              <option value="4">Berdasarkan Divisi</option>
              <option value="5">Berdasarkan Jabatan</option>
            </select>
          </div>
          <div class="mb-3" id="karyawan-container" style="display: none;">
            <label class="col-sm-5 title-case-jadwal mb-2" for="cutiEmployee">Karyawan</label>
            <select multiple style="width: 100%;" name="cutiEmployee[]" id="cutiEmployee" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
          </div>
          
          <div class="row mb-3" id="divisi-container" style="display: none;">
            <label class="col-sm-5" for="cutiDivisis">Divisi</label>
            <!-- <div class="col-sm-7"> -->
              
              <div class="input-group">
                <select multiple style="width: 92%;" name="cutiDivisis[]" id="cutiDivisis" class="form-control" data-placeholder="-:Pilih Divisi:-"></select>
                <button class="btn btn-sm btn-outline-info" type="button" id="btn-division"><i class="bx bx-plus"></i></button>
              </div>
            <!-- </div> -->
          </div>
          <div class="row mb-3" id="jabatan-container" style="display: none;">
            <label class="col-sm-5" for="cutiJabatans">Jabatan</label>
            <!-- <div class="col-sm-7"> -->
              
              <div class="input-group">
                <select multiple style="width: 92%;" name="cutiJabatans[]" id="cutiJabatans" class="form-control" data-placeholder="-:Pilih Jabatan:-"></select>
                <button class="btn btn-sm btn-outline-info" type="button" id="btn-position"><i class="bx bx-plus"></i></button>
              </div>
            <!-- </div> -->
          </div>
          <div class="mb-3">
            <label class="form-label title-case-jadwal lbl-req" id="labelMonth" style="margin-right: 15px;">Apakah kuota cuti ini berdasarkan bulan join?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="monthOption" id="monthTrue" value="monthTrue">
              <label class="form-check-label title-case-jadwal" for="monthTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="monthOption" id="monthFalse" value="monthFalse" checked>
              <label class="form-check-label title-case-jadwal" for="monthFalse">Tidak</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label title-case-jadwal lbl-req" id="labelTahunan" style="margin-right: 15px;">Apakah cuti ini selalu ada setiap tahun?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="repeatOption" id="repeatTrue" value="repeatTrue">
              <label class="form-check-label title-case-jadwal" for="repeatTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="repeatOption" id="repeatFalse" value="repeatFalse" checked>
              <label class="form-check-label title-case-jadwal" for="repeatFalse">Tidak</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label title-case-jadwal lbl-req" id="labelProbation" style="margin-right: 15px;">Apakah probation dapat menggunakan cuti ini?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="probationOption" id="probationTrue" value="probationTrue">
              <label class="form-check-label title-case-jadwal" for="probationTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="probationOption" id="probationFalse" value="probationFalse" checked>
              <label class="form-check-label title-case-jadwal" for="probationFalse">Tidak</label>
            </div>
          </div>
          <div class="mb-3" id="active-container">
            <label class="form-label title-case-jadwal lbl-req" id="labelAktif" style="margin-right: 15px;">Apakah cuti ini aktif?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeTrue" value="activeTrue" checked>
              <label class="form-check-label title-case-jadwal" for="activeTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeFalse" value="activeFalse">
              <label class="form-check-label title-case-jadwal" for="activeFalse">Tidak</label>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                *) Cuti Tahunan yang sudah di simpan hanya dapat merubah Karyawan & Status Aktif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="submit-button" class="btn btn-sm btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('user.master.karyawan.karyawan-divisi')
@include('user.master.karyawan.karyawan-jabatan')

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
    let currentMenuId = "{{request()->get('menu_id')}}";
    let selectedEmployeeId = [];
    let deletedEmployeeId = [];
    let selectedDivisiId = [];
    let selectedJabatanId = [];

    $("#optionEmployee, #leaveType, #masaTenggang").select2({
      dropdownParent: $("#addCutiModal #formAddCuti"),
    });

    $("select[name='optionEmployee']").change(function() {
      const selectedOption = $(this).val();
      $("#karyawan-container, #divisi-container, #jabatan-container").hide();

      if (selectedOption === "specificEmployee") {
        $("#karyawan-container").slideDown();
      } else {
        if(selectedOption == '4') {
          $("#divisi-container").slideDown();
        } else if(selectedOption == '5') {
          $("#jabatan-container").slideDown();
        }
      }
    });



    $("#cutiEmployee").select2({
      dropdownParent: $("#formAddCuti"),
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

    $("#cutiEmployee").on("select2:select", function(e) {
      const selectedId = parseInt(e.params.data.id);

      const deletedIndex = deletedEmployeeId.indexOf(selectedId);
      if (deletedIndex > -1) {
        deletedEmployeeId.splice(deletedIndex, 1);
      }

      if (!selectedEmployeeId.includes(selectedId)) {
        selectedEmployeeId.push(selectedId);
      }

      console.log("selected", selectedEmployeeId);
      console.log("deleted", deletedEmployeeId);
    });

    $("#cutiEmployee").on("select2:unselect", function(e) {
      const unselectedId = parseInt(e.params.data.id);

      const selectedIndex = selectedEmployeeId.indexOf(unselectedId);
      if (selectedIndex > -1) {
        selectedEmployeeId.splice(selectedIndex, 1);
      }

      if (isEdit === true && !deletedEmployeeId.includes(unselectedId)) {
        deletedEmployeeId.push(unselectedId);
      }

      console.log("selected", selectedEmployeeId);
      console.log("deleted", deletedEmployeeId);
    });

    $("#kuotaCuti").on("wheel", function(e) {
      e.preventDefault();
    });

    // $("#modalKdivisi").on("show.bs.modal", function(e) {
    //   $("#addCutiModal").modal("hide");
    // });

    // $("#modalKdivisi").on("hidden.bs.modal", function() {
    //   $("#addCutiModal").modal("show");
    // });

    // $("#modalKjabatan").on("show.bs.modal", function(e) {
    //   $("#addCutiModal").modal("hide");
    // });

    // $("#modalKjabatan").on("hidden.bs.modal", function() {
    //   $("#addCutiModal").modal("show");
    // });

    $("#cutiDivisis").select2({
      dropdownParent: $("#addCutiModal #formAddCuti"),
      // tags: true,
      ajax: {
        url: `{{route('user.page.karyawan.divisi.select')}}?menu_id=${currentMenuId}`,
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
            item.id = item.karyawandivisi_id;
            item.text = item.karyawandivisi_name;
            // console.log('item.kode', item)
            return item
          })
          return {
            results: items
          };
        },
      },
    }).on("select2:select", function(e) {
      let data = e.params.data;
      selectedDivisiId.push(data.id);
    }).on("select2:unselect", function(e) {
      let data = e.params.data;
      let idx = selectedDivisiId.indexOf(data.id);
      if(idx > -1) {
        selectedDivisiId.splice(idx, 1);
      }
      console.log('selectedDivisiId', selectedDivisiId);
    });

    $("#cutiJabatans").select2({
      dropdownParent: $("#addCutiModal #formAddCuti"),
      // tags: true,
      ajax: {
        url: `{{route('user.page.karyawan.jabatan.select')}}?menu_id=${currentMenuId}`,
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
            item.id = item.karyawanjabatan_id;
            item.text = item.karyawanjabatan_name;
            // console.log('item.kode', item)
            return item
          })
          return {
            results: items
          };
        },
      },
    }).on("select2:select", function(e) {
      let data = e.params.data;
      selectedJabatanId.push(data.id);
    }).on("select2:unselect", function(e) {
      let data = e.params.data;
      let idx = selectedJabatanId.indexOf(data.id);
      if(idx > -1) {
        selectedJabatanId.splice(idx, 1);
      }
      console.log('selectedJabatanId', selectedJabatanId);
    });

    // function processInputDate(input, isRepeat) {
    //   const currentDate = new Date();
    //   const inputParts = input.split('-');

    //   const inputDay = parseInt(inputParts[0]);
    //   const inputMonth = inputParts[1];
    //   const inputMonthIndex = new Date(`${inputMonth} 1, 2000`).getMonth();

    //   const currentYear = currentDate.getFullYear();
    //   const nextYear = isRepeat ? currentYear + 1 : currentYear + (currentDate.getMonth() >= inputMonthIndex ? 1 : 0);

    //   const processedDate = new Date(nextYear, inputMonthIndex, inputDay);

    //   return processedDate.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '-');
    // }

    $("#kuotaCuti").on("input", function() {
      var value = $(this).val();
      if (value !== "" && parseInt(value) < 1) {
        $(this).val(1);
      }
    });

    $("#tanggalMulai, #tanggalBerakhir, #specificDate").daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      parentEl: '#addCutiModal',
      locale: {
        format: 'DD-MM-YYYY', // Display day, month, and year
      },
      minYear: moment().year(),
      maxYear: moment().year() + 5,
    });

    $(".drp-calendar").css("font-size", "");

    function isValidDateFormat(dateStr) {
      // Regular expression pattern for "DD-MM-YYYY" format
      const pattern = /^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/;
      return pattern.test(dateStr);
    }

    let tblPengaturanCuti = $("#table-pengaturan-cuti").DataTable({
      // DataTable configuration options
      "searching": true,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        "searchPlaceholder": "Cari Deskripsi / Kode Cuti",
        // "emptyTable": "Tidak ada data"
      },
      "info": false,
      "ordering": true,
      "ajax": {
        "url": `{{ route('user.page.pengaturan.cuti.datatable') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {}
      },
      "fnInitComplete": function() {
        // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columns": [{
          "data": "leave_id",
          "title": "Kode Cuti",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === 'display' || type === 'filter')) {
              return 'CU' + data;
            }
            return data;
          }
        },
        {
          "data": "leave_description",
          "title": "Deskripsi",
          "className": "text-center"
        },
        {
          "data": "leave_type",
          "title": "Tipe",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === "display" || type === "filter")) {
              if (data === 'PAID') {
                return 'Dibayar'
              } else {
                return 'Tidak Dibayar'
              }
            }
            return data; // For sorting and other purposes, return the original data as it is
          }
        },
        {
          "data": "leave_active_start_date",
          "title": "Tanggal Mulai Berlaku",
          "className": "text-center"
        },
        {
          "data": "leave_active_end_date",
          "title": "Tanggal Akhir Berlaku",
          "className": "text-center"
        },
        {
          "data": "leave_grace_period",
          "title": "Masa Tenggang",
          "className": "text-center",
          "render": function(data, type, row) {
            if (data && (type === "display" || type === "filter")) {
              if (data !== null) {
                return data
              } else {
                return "Tidak Berlaku"
              }
            } else {
              return "Tidak Berlaku"
            }
          }
        },
        {
          "data": "leave_quota",
          "title": "Kuota Cuti",
          "className": "text-center"
        },
        {
          "data": "leave_status_active",
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
                  <a class="dropdown-item edit-cuti" href="javascript:void(0);" data-id="${row.leave_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item deactivate-cuti" href="javascript:void(0);" data-id="${row.leave_id}"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                <?php endif ?>
                </div>
              </div>
                `;
          }
        },
        {
          "data": "leave_repeat_status",
        },
        {
          "data": "leave_base_month",
        },
        {
          "data": "leave_probation_status",
        },
        // {
        //   "data": "karyawan",
        // },
      ],
      "columnDefs": [
        // Define which columns to hide
        {
          "targets": [9, 10, 11], // Indexes of the columns to be hidden
          "visible": false,
          "searchable": false,
        },
        {
          "target": [8],
          "orderable": false,
        }
      ],
    });

    let isEdit = false

    function formatDateFromISO(dateStr) {
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        return parts[2] + '-' + parts[1] + '-' + parts[0];
      } else {
        return dateStr;
      }
    }

    // $('#table-pengaturan-cuti').on("click", ".delete-cuti", function(e) {
    //   e.preventDefault();

    //   // Get the leave_id from the data-id attribute of the Delete button
    //   const cutiId = $(this).data("id");

    //   const csrfToken = $('meta[name="csrf-token"]').attr('content');
    //   Swal.fire({
    //       html: 'Apakah anda ingin menghapus cuti ini?',
    //       icon: 'question',
    //       preConfirm: () => {
    //           Swal.showLoading();
    //           return fetch(`{{url('/user/pengaturan/cuti/delete')}}/${cutiId}?menu_id=${currentMenuId}`, {
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
    //       tblPengaturanCuti.row($(this).closest("tr")).remove().draw();
    //     });
    // });

    $('#table-pengaturan-cuti').on("click", ".edit-cuti", function(e) {
      e.preventDefault();
      // Get the row data associated with the clicked "Edit" button
      const rowData = tblPengaturanCuti.row($(this).closest("tr")).data();
      isEdit = true

      if (isEdit === true && rowData.leave_repeat_status === true) {
        // Get the form element
        var form = document.getElementById('formAddCuti');

        // Loop through all form elements and disable them except for active-container and karyawan-container
        var formElements = form.elements;
        for (var i = 0; i < formElements.length; i++) {
          var element = formElements[i];
          var elementId = element.id;

          // Skip elements that should remain enabled
          if (elementId === 'leave_id' || elementId === 'activeTrue' || elementId === 'activeFalse' || elementId === 'cutiEmployee' || elementId === 'submit-button' || elementId === 'optionEmployee') {
            continue;
          }

          // Disable other form elements
          element.disabled = true;
        }
      }

      // Now, populate the edit modal with the data from the rowData
      populateEditCutiModal(rowData);
    });

    $("#table-pengaturan-cuti").on("click", ".deactivate-cuti", function() {
      // Get the leave_id from the data-id attribute of the Delete button
      const rowData = tblPengaturanCuti.row($(this).closest("tr")).data();

      // Get the leave_id and leave_status_active from the row data
      const cutiId = rowData.leave_id;
      const status = rowData.leave_status_active;

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      if (status === false) {
        Swal.fire({
          html: 'Cuti sudah di nonaktifkan.',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      Swal.fire({
        html: 'Apakah anda ingin menonaktifkan cuti ini?',
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
            leave_repeat_status: false,
            isEdit: true,
            leave_id: cutiId,
            leave_status_active: false
          };

          return fetch(`{{ route('user.page.pengaturan.cuti.save') }}?menu_id=${currentMenuId}`, {
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
              tblPengaturanCuti.ajax.reload();

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
        tblPengaturanCuti.row($(this).closest("tr")).remove().draw();
      });
    });

    function populateEditCutiModal(rowData) {
      // $('#formAddCuti [type=reset]').click();
      resetForm('#formAddCuti');
      // Update the form fields with the rowData
      $("#leave_id").val(rowData.leave_id);
      if (rowData.leave_status_active === true) {
        $("#activeTrue").prop("checked", true);
      } else {
        $("#activeFalse").prop("checked", true);
      }
      $("#description").val(rowData.leave_description);

      $("#cutiEmployee").html('');
      $("#cutiEmployee").val(null).trigger('change');
      $("#cutiJabatans").html('');
      $("#cutiJabatans").val(null).trigger('change');
      $("#cutiDivisis").html('');
      $("#cutiDivisis").val(null).trigger('change');

      $("#karyawan-container").slideUp();
      $("#divisi-container").slideUp();
      $("#jabatan-container").slideUp();
      
      if (rowData.leave_is_all === true) {
        $("#optionEmployee").val("allEmployee").trigger('change');
      } else {
        if(rowData.leave_used_for == 0) { // karyawan
          $("#optionEmployee").val("specificEmployee").trigger('change');
          $("#karyawan-container").slideDown();
          getListKaryawan(rowData.leave_id);
        } else if(rowData.leave_used_for == 4) { // divisi
          $("#optionEmployee").val("4").trigger('change');
          $("#divisi-container").slideDown();
          getListDivisiJabatan(rowData.leave_id, rowData.leave_used_for);
        } else if(rowData.leave_used_for == 5) { // jabatan
          $("#optionEmployee").val("5").trigger('change');
          $("#jabatan-container").slideDown();
          getListDivisiJabatan(rowData.leave_id, rowData.leave_used_for);
        } else {
          $("#optionEmployee").val(rowData.leave_used_for).trigger('change');
        }
      }


      $("#excodeleave-container").slideDown();
      $("#excodeleave").val('CU' + rowData.leave_id);

      if (rowData.leave_repeat_status === true) {
        $("#repeatTrue").prop("checked", true);
      } else {
        $("#repeatFalse").prop("checked", true);
      }

      if (rowData.leave_probation_status === true) {
        $("#probationTrue").prop("checked", true);
      } else {
        $("#probationFalse").prop("checked", true);
      }

      if (rowData.leave_base_month === true) {
        $("#monthTrue").prop("checked", true);
      } else {
        $("#monthFalse").prop("checked", true);
      }

      const formattedStartDate = formatDateFromISO(rowData.leave_active_start_date);
      const formattedEndDate = formatDateFromISO(rowData.leave_active_end_date);

      // Populate the input fields with formatted date values
      $("#tanggalMulai").val(formattedStartDate);
      $("#tanggalBerakhir").val(formattedEndDate);

      $("#tanggalMulai").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        parentEl: '#addCutiModal',
        locale: {
          format: 'DD-MM-YYYY', // Display day, month, and year
        },
        startDate: $("#tanggalMulai").val(),
        endDate: $("#tanggalMulai").val()
      });

      $("#tanggalBerakhir").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        parentEl: '#addCutiModal',
        locale: {
          format: 'DD-MM-YYYY', // Display day, month, and year
        },
        startDate: $("#tanggalBerakhir").val(),
        endDate: $("#tanggalBerakhir").val()
      });


      $(".drp-calendar").css("font-size", ""); // Reset the font-size to show the year text
      // Remove the added style for year column

      $("#kuotaCuti").val(rowData.leave_quota);

      if (rowData.leave_grace_period !== null) {
        $("#masaTenggang").val("specificDay").trigger('change');
        const formattedGraceDate = formatDateFromISO(rowData.leave_grace_period)
        $("#specificDate").val(formattedGraceDate);

        $("#specificDate").daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          parentEl: '#addCutiModal',
          locale: {
            format: 'DD-MM-YYYY', // Display day, month, and year
          },
          startDate: formattedGraceDate,
          endDate: formattedGraceDate
        });

        $("#specificDateForm").slideDown(); // Slide down the specificDate input
      } else {
        $("#masaTenggang").val("notApplicable").trigger('change');
        $("#specificDateForm").slideUp(); // Slide down the specificDate input
      }

      if (rowData.leave_type === "PAID") {
        $("#leaveType").val("PAID").trigger('change');
      } else {
        $("#leaveType").val("UNPAID").trigger('change');
      }

      $("#excodeleave").prop('disabled', true);

      // Open the edit modal
      $("#addCutiModal").modal("show");

      $("#addCutiModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    function resetAndSlideUpForms() {
      var form = document.getElementById('formAddCuti');

      // Loop through all form elements and disable them except for active-container and karyawan-container
      var formElements = form.elements;
      for (var i = 0; i < formElements.length; i++) {
        var element = formElements[i];
        var elementId = element.id;

        // Disable other form elements
        element.disabled = false;
      }
      isEdit = false
      selectedEmployeeId = []
      deletedEmployeeId = []
      $(".error-message").remove();
      // $("#formAddCuti")[0].reset();
      $("#excodeleave-container").slideUp();
      $("#karyawan-container").slideUp();
      $("#cutiEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
      $("#specificDateForm").slideUp(); // Slide down the specificDate input
    }

    // $("#formAddCuti").on("click", ".btn-close", function(e) {
    //   e.preventDefault();

    //   isEdit = false;
    //   resetAndSlideUpForms()
    // })

    $("select[name='masaTenggang']").change(function() {
      const selectedOption = $(this).val();

      if (selectedOption === "specificDay") {
        $("#specificDateForm").slideDown();
      } else {
        $("#specificDateForm").slideUp();
      }
    });

    function openAddCutiModal() {
      resetForm('#formAddCuti');
      resetAndSlideUpForms()
      jQuery("#addCutiModal").modal("show");

      $("#addCutiModal").on("hidden.bs.modal", function() {
        resetAndSlideUpForms() // Reset the form fields when the modal is closed
      });
    }

    $("#formAddCuti").submit(function(event) {
      event.preventDefault();
      $(".spinner-box").css({
        'display': 'table'
      });
      $(".error-message").remove();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      const formData = $(this).serializeArray();
      const jsonData = {}; // To store the JSON data

      formData.forEach(function(item) {
        jsonData["leave_repeat_status"] = $("#repeatTrue").prop("checked");
        jsonData["leave_status_active"] = $("#activeTrue").prop("checked");
        jsonData["leave_probation_status"] = $("#repeatTrue").prop("checked");
        jsonData["leave_base_month"] = $("#monthTrue").prop("checked");

        if (item.name === "description") {
          jsonData["leave_description"] = item.value
        } else if (item.name === "leaveType") {
          jsonData["leave_type"] = item.value
        } else if (item.name === "kuotaCuti") {
          jsonData["leave_quota"] = item.value
        } else if (item.name === "tanggalMulai") {
          jsonData["leave_active_start_date"] = item.value
        } else if (item.name === "tanggalBerakhir") {
          jsonData["leave_active_end_date"] = item.value
        } else if (item.name === "specificDate") {
          jsonData["leave_grace_period"] = item.value
        } else if (item.name === 'leave_id' && item.value !== '') {
          jsonData["leave_id"] = item.value
        } else if (item.name === "masaTenggang") {
          jsonData["leave_period"] = item.value
        } else if (item.name === "optionEmployee") {
          jsonData["leave_is_all"] = item.value === 'allEmployee' ? true : false
          jsonData["leave_used_for"] = item.value === 'allEmployee' ? 1 : (item.value === 'specificEmployee' ? 0 : item.value)
        }

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      });

      let hasErrors = false;

      if (isEdit === false) {
        if (!jsonData["leave_description"]) {
          appendError($("#description"), "Mohon isi deskripsi.");
          hasErrors = true;
        }
      }

      jsonData["isEdit"] = isEdit
      console.log(jsonData, 'jsondata')

      if ((isEdit === true && jsonData["leave_repeat_status"] === false) || isEdit === false) {
        if (!jsonData["leave_description"]) {
          appendError($("#description"), "Mohon isi deskripsi.");
          hasErrors = true;
        }

        if (!jsonData["leave_quota"]) {
          appendError($("#kuotaCuti"), "Mohon masukan batas pengambilan Cuti.");
          hasErrors = true;
        }

        if (!jsonData["leave_active_start_date"]) {
          appendError($("#tanggalMulai"), "Mohon masukan tanggal.");
          hasErrors = true;
        }

        if (!jsonData["leave_active_end_date"]) {
          appendError($("#tanggalBerakhir"), "Mohon masukan tanggal.");
          hasErrors = true;
        }

        if ($("#masaTenggang").val() === "specificDay" && !jsonData["leave_grace_period"]) {
          appendError($("#specificDate"), "Mohon masukan tanggal.");
          hasErrors = true;
        }

        if (jsonData["leave_active_start_date"] && jsonData["leave_active_start_date"] !== '' && !isValidDateFormat(jsonData["leave_active_start_date"])) {
          appendError($("#tanggalMulai"), "Mohon masukan dengan format DD-MM-YYYY.");
          hasErrors = true;
        }

        if (jsonData["leave_active_end_date"] && jsonData["leave_active_end_date"] !== '' && !isValidDateFormat(jsonData["leave_active_end_date"])) {
          appendError($("#tanggalBerakhir"), "Mohon masukan dengan format DD-MM-YYYY.");
          hasErrors = true;
        }

        if ($("#masaTenggang").val() === "specificDay" && jsonData["leave_grace_period"] && jsonData["leave_grace_period"] !== '' && !isValidDateFormat(jsonData["leave_grace_period"])) {
          appendError($("#specificDate"), "Mohon masukan dengan format DD-MM-YYYY.");
          hasErrors = true;
        }

        if (jsonData["leave_active_start_date"] && jsonData["leave_active_start_date"] !== '' && isValidDateFormat(jsonData["leave_active_start_date"]) && jsonData["leave_active_end_date"] && jsonData["leave_active_end_date"] !== '' && isValidDateFormat(jsonData["leave_active_end_date"])) {
          const dateStr1 = jsonData["leave_active_start_date"];
          const dateParts1 = dateStr1.split('-');
          const day1 = parseInt(dateParts1[0], 10);
          const month1 = parseInt(dateParts1[1] - 1, 10);
          const year1 = parseInt(dateParts1[2], 10);
          const date1 = new Date(year1, month1, day1);

          const dateStr2 = jsonData["leave_active_end_date"];
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

        if ($("#masaTenggang").val() === "specificDay" && jsonData["leave_grace_period"] && jsonData["leave_grace_period"] !== '' && isValidDateFormat(jsonData["leave_grace_period"]) && jsonData["leave_active_end_date"] && jsonData["leave_active_end_date"] !== '' && isValidDateFormat(jsonData["leave_active_end_date"])) {
          const dateStr1 = jsonData["leave_active_end_date"];
          const dateParts1 = dateStr1.split('-');
          const day1 = parseInt(dateParts1[0], 10);
          const month1 = parseInt(dateParts1[1] - 1, 10);
          const year1 = parseInt(dateParts1[2], 10);
          const date1 = new Date(year1, month1, day1);

          const dateStr2 = jsonData["leave_grace_period"];
          const dateParts2 = dateStr2.split('-');
          const day2 = parseInt(dateParts2[0], 10);
          const month2 = parseInt(dateParts2[1] - 1, 10);
          const year2 = parseInt(dateParts2[2], 10);
          const date2 = new Date(year2, month2, day2);

          if (date2 < date1) {
            appendError($("#specificDate"), "Tanggal masa tenggang kurang dari tanggal berakhir.");
            hasErrors = true;
          }
        }

        if (!jsonData["leave_period"]) {
          appendError($("#masaTenggang"), "Mohon pilih masa tenggang!");
          hasErrors = true;
        }

        if (!jsonData["leave_type"]) {
          appendError($("#leaveType"), "Mohon pilih tipe cuti!");
          hasErrors = true;
        }

        // if (jsonData["leave_repeat_status"] === true && jsonData["leave_grace_period"] && jsonData["leave_grace_period"] !== null && jsonData["leave_grace_period"] !== '') {
        //   jsonData["leave_grace_period"] = processInputDate(jsonData["leave_grace_period"], true);
        // }

        if (hasErrors) {
          $(".spinner-box").fadeOut();
          return false;
        }

        

        // if (!jsonData["leave_type"]) {
        //   $(".spinner-box").fadeOut();
        //   $(".error-message").remove();
        //   Swal.fire({
        //     html: 'Mohon pilih tipe cuti!',
        //     confirmButtonText: "Ok",
        //     showCancelButton: false,
        //     icon: 'error'
        //   })
        //   return false
        // }

        if (jsonData["leave_period"] === "notApplicable") {
          jsonData["leave_grace_period"] = null
        }
      }



      jsonData["leave_employee"] = selectedEmployeeId
      jsonData["leave_deleted_employee"] = deletedEmployeeId

      jsonData["leave_divisi"] = selectedDivisiId
      jsonData["leave_jabatan"] = selectedJabatanId

      $.ajax({
        type: "POST",
        url: `{{ route('user.page.pengaturan.cuti.save') }}?menu_id=${currentMenuId}`,
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        dataType: "json",
        success: function(response) {
          $(".spinner-box").fadeOut();
          if (response.success) {
            toastr.success(response.message);
            resetAndSlideUpForms()
            isEdit = false
            $("#addCutiModal").modal("hide");
            tblPengaturanCuti.draw(); // Refresh the DataTable
            
          } else {
            toastr.error(response.message);
          }
        },
        error: function(xhr, status, error) {
          $(".spinner-box").fadeOut();
          let res = xhr.responseJSON;
          if(res.message) {
            toastr.error(res.message);
          } else {
            toastr.error("An error occurred while submitting the form.");
          }
          console.log(xhr.responseJSON);
        }
      });
    });

    function appendError($inputElement, errorMessage) {
      if (!$inputElement.next(".error-message").length) {
        if($inputElement.is('select')) {
            // console.log('$inputElement.attr("id")', $inputElement.attr("id"))
            element = $("#select2-" + $inputElement.attr("id") + "-container").parents('.select2-container'); 
            // console.log('element', element);
            $errorElement = $("<div>")
            .addClass("error-message")
            .addClass("error-text")
            .text(errorMessage);
            element.after($errorElement);
        } else {
          $inputElement.addClass("error");
          $errorElement = $("<div>")
            .addClass("error-message")
            .addClass("error-text")
            .text(errorMessage);
          $inputElement.after($errorElement);
          
            
        }
      }
    }

    $("input").focus(function() {
      $(this).removeClass("error");
      $(this).next(".error-message").remove();
    });

    $("#btnAddCuti").click(function() {
      // if (isEdit === false && document.getElementById('repeatTrue').checked) {
      //   // Get the form element
      //   var form = document.getElementById('formAddCuti');

      //   // Loop through all form elements and disable them except for active-container and karyawan-container
      //   var formElements = form.elements;
      //   for (var i = 0; i < formElements.length; i++) {
      //     var element = formElements[i];
      //     var elementId = element.id;

      //     // Skip elements that should remain enabled
      //     if (elementId === 'active-container' || elementId === 'karyawan-container') {
      //       continue;
      //     }

      //     // Disable other form elements
      //     element.disabled = false;
      //   }
      // }

      isEdit = false;

      openAddCutiModal();
    });

    function getListKaryawan(id) {
      $(".spinner-box").css({'display': 'table'});
      $.ajax({
        url: `{{route('user.page.pengaturan.cuti.listkaryawan', '')}}/${id}?menu_id=${currentMenuId}`,
        method: 'get',
        dataType: 'json',
        success: function(res) {
          $(".spinner-box").fadeOut();
          console.log('res', res)
          if(res.data) {
            let karyawans = res.data;
            if(karyawans.length > 0) {
              let karyawanOpts = '';
              karyawans.forEach(kr => {
                karyawanOpts += `<option value="${kr.karyawan_id}" selected>${kr.karyawan_name}</option>`;
                selectedEmployeeId.push(parseInt(kr.karyawan_id));
              })
              $("#cutiEmployee").html(karyawanOpts);
            }
          }
        }
      })
    }

    function getListDivisiJabatan(leave_id, leave_used_for) {
      $(".spinner-box").css({'display': 'table'});
      $.ajax({
        url: `{{route('user.page.pengaturan.cuti.listdivisijabatan', '')}}/${leave_id}?menu_id=${currentMenuId}`,
        method: 'get',
        dataType: 'json',
        success: function(res) {
          $(".spinner-box").fadeOut();
          console.log('res', res)
          if(res.data) {
            let divisi_jabatan = res.data;
            if(divisi_jabatan.length > 0) {
              let divisijabatanOpts = '';
              if(leave_used_for == 4) {
                divisi_jabatan.forEach(dj => {
                  divisijabatanOpts += `<option value="${dj.karyawandivisi_id}" selected>${dj.karyawandivisi_name}</option>`;
                })
                $("#cutiDivisis").html(divisijabatanOpts);
              }
              if(leave_used_for == 5) {
                divisi_jabatan.forEach(dj => {
                  divisijabatanOpts += `<option value="${dj.karyawanjabatan_id}" selected>${dj.karyawanjabatan_name}</option>`;
                })
                $("#cutiJabatans").html(divisijabatanOpts);
              }
            }
          }
        }
      })
    }

    setHtmlTitle('{{$title}}')
  });
</script>