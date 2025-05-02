<?php 
use Carbon\Carbon;
$joindate = Carbon::parse($user->user_created_at);
$joindatey = $joindate->format('Y');
?>
<div class="accordion mb-4" id="accordionExample">
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
            <label for="filterPeriode">Periode</label>
            <input type="text" id="filterPeriode" name="filterPeriode" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3">
            <label for="filterListEmployee">Non Karyawan</label>
            <select multiple style="width: 100%;" name="filterListEmployee[]" id="filterListEmployee" class="form-control" data-placeholder=" Pilih Non Karyawan" autocomplete="off"></select>
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
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-4">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <div class="col-sm-8 text-right">
            @if(in_array('IMPORT_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-outline-info" id="btn-import-modal" href="#">
              <i class='bx bxs-cloud-upload'></i>
              Impor
            </a>
            @endif
            @if(in_array('DOWNLOAD_PAYSLIP', request()->get('permission_codes')))
            <a class="btn btn-sm btn-info disabled" id="btn-download-slip" href="#">
              <i class='bx bx-download'></i>
              Download Slip
            </a>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-penggajian">
            <thead class="table-light">
              <tr>
                <th>
                  <div class="form-check form-check-inline">
                    <input name="karyawan_ischeck" class="form-check-input" type="checkbox" value="1" id="karyawan_ischeck">
                  </div>
                </th>
                <th>Nama Non Karyawan</th>
                <th>Periode</th>
                <th>Gaji Bersih</th>
                <!-- <th>Status</th> -->
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
          </table>
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
              *) Sebelum mengimpor Data Penggajian Non Karyawan, anda harus mengunduh Templat Penggajian Non Karyawan.
            </div>
          </div>
        </div>
        <div class="row mb-1">
          <div class="col">
            <div class="form-text duration-helper-text text-muted">
              *) Sesuaikan Data Penggajian Non Karyawan yang akan di impor dengan Templat Penggajian Non Karyawan sistem REKKAA
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
        <div class="ms-auto">
          <button type="button" id="btn-unduh-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
          <button type="button" id="btn-import" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Transaksi</button>
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
    let filterPeriode = moment().format('MM-YYYY');

    $("#filterPeriode").datepicker({
        language: "id-ID",
        format: "MM-yyyy",
        startView: "months",
        minViewMode: "months",
        startDate: '01-<?php echo $joindatey ?>',
        endDate: '12-'+moment().format('Y'),
      }).datepicker("setDate", filterPeriode)
      .on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#filterPeriode').datepicker("getDate");
        filterPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
      });

    $("#filterStatus").select2();
    $("#filterListEmployee").select2({
      // dropdownParent: $("#formAddCuti"),
      // tags: true,
      ajax: {
        url: "{{route('user.page.nonkaryawan.select')}}?menu_id="+currentMenuId,
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
    $("#searchButton").click(function(e) {
      e.preventDefault();
      deselectData();
      tablePenggajian.draw();
    });

    $("#resetButton").click(function(e) {
      e.preventDefault();
      $("filterPeriode").val(null).trigger('change');
      $("filterListEmployee").val(null).trigger('change');
      $("filterStatus").val(null).trigger('change');
      deselectData();
      tablePenggajian.draw();
    });

    // Begin Table Wajib Pajak
    let tablePenggajian = $("#table-penggajian").DataTable({
      // "filtering": true,
      "ordering": true,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "{{route('user.page.penggajian.nonkaryawan.datatable', ['menu_id' => request()->get('menu_id')])}}",
        "type": "GET",
        "data": function(data) {
          //     console.log(data); // send data to server
          data.periode = filterPeriode;
          data.karyawan_ids = $("#filterListEmployee").val();
          data.status = $("#filterStatus").val();
        }
      },
      "autoWidth": true,
      "columnDefs": [{
          target: [0, 4],
          width: 30,
          orderable: false,
        }, {
          target: [2],
          orderable: false,
        }, {
          target: [0, 1, 2, 3, 4],
          className: 'text-center'
        },
        //  {
        //   target: [4],
        //   "visible": false,
        //   "searchable": false,
        // }
      ],
      "columns": [{
          "data": "payroll",
          "render": function(data, type, row) {
            return `<div class="form-check form-check-inline">
                <input class="form-check-input karyawan_checked" type="checkbox"  value="1">
              </div>`;
          }
        },
        {
          "data": "karyawan_name",
          // "className": "text-left",
          // "render": function(data, type, row) {
          //   return data;
          // }
        },
        {
          "data": "payroll",
          "render": function(data, type, row) {
            console.log('filterPeriode', filterPeriode)
            return (row.payroll_period) ? moment(row.payroll_period).format('MMMM - YYYY') : moment(filterPeriode, 'MM-YYYY').format('MMMM - YYYY');
          }
        },
        {
          "data": "payroll",
          "className": "text-right",
          "render": function(data, type, row) {
            let netto = (row.payroll_total_netto) ? row.payroll_total_netto : 0;
            // console.log('data', row.netto_total)
            // if (data) {
            // netto = row.payroll_total_netto;
            // }
            return 'Rp. ' + formatCurrency(netto);
          }
        },
        // {
        //   "data": "payroll",
        //   "render": function(data, type, row) {
        //     let status = `<span class="badge bg-warning">Menunggu Perhitungan</span>`;
        //     let statusid = row.payroll_status;
        //     if (statusid) {
        //       // let payroll_status = data.payroll_status; 
        //       if (statusid == 1) {
        //         status = `<span class="badge bg-info">Menunggu Konfirmasi</span>`;
        //       } else if (statusid == 2) {
        //         status = `<span class="badge bg-danger">Menunggu Pembayaran</span>`;
        //       } else if (statusid == 3) {
        //         status = `<span class="badge bg-success">Dibayarkan</span>`;
        //       }
        //     }
        //     return status;
        //   }
        // },
        {
          "data": "payroll",
          "render": function(data, type, row) {
            console.log('data', data);
            let statusid = row.payroll_status;
            let btn = `<a class="dropdown-item rekkaa-page-link" href="{{route('user.page.penggajian.nonkaryawan.detail', '')}}/${row.karyawan_id}?menu_id=${currentMenuId}&periode=${filterPeriode}"><i class="bx bx-list-ul me-1 text-info"></i> Detail</a>`;
            return `
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  ${btn}
                </div>
              </div>
                `
          }
        },
      ],
    });

    // download
    $("#btn-download-slip").click(function(e) {
      e.preventDefault();
      let payroll_uuids = kirimData;
      let params = '';
      if (payroll_uuids.length > 0) {
        for (let i = 0; i < payroll_uuids.length; i++) {
          if (i == 0)
            params += '&payroll_uuids[]=' + payroll_uuids[i];
          else
            params += '&payroll_uuids[]=' + payroll_uuids[i];
        }
      }
      window.open(`{{route('user.page.penggajian.nonkaryawan.multicetakperiode', '')}}?menu_id=${currentMenuId}` + params, '_blank');
    })

    let kalkulasiData = [];
    let bayarData = [];
    let kirimData = [];
    $("#table-penggajian").on("click", ".karyawan_checked", function(e) {
      let ischecked = $(this).is(':checked');
      let row = $(this).closest('tr');

      // console.log('kalkulasiData', kalkulasiData);
      if (ischecked == false)
        deselectData(ischecked, row);
      else
        selectData(row);
    });
    $('#karyawan_ischeck').on('click', function() {
      if ($('#karyawan_ischeck').is(':checked')) {

        $(".karyawan_checked").prop("checked", true).trigger("change");
        selectData();
      } else {
        deselectData();
        $(".karyawan_checked").prop("checked", false).trigger("change");
      }
    });

    function enableBtn() {
      $("#btn-kalkulasi, #btn-konfirmasi, #btn-bayar, #btn-kirim-slip, #btn-download-slip").addClass('disabled');
      if (kalkulasiData.length > 0 && kirimData.length <= 0 && bayarData.length <= 0)
        $("#btn-kalkulasi, #btn-konfirmasi").removeClass('disabled');

      if (bayarData.length > 0 && kalkulasiData.length <= 0 && kirimData.length <= 0)
        $("#btn-bayar").removeClass('disabled');

      if (kirimData.length > 0 && kalkulasiData.length <= 0 && bayarData.length <= 0)
        $("#btn-kirim-slip, #btn-download-slip").removeClass('disabled');

      // console.log(kalkulasiData,kirimData,bayarData);
    }

    function selectData(row = null) {
      // console.log('row', row);
      if (row) {
        tablePenggajian.row(row).select();

      } else {
        tablePenggajian.rows().select();
      }

      let selecteddata = tablePenggajian.rows('.selected').data();
      // console.log('selecteddata', selecteddata);
      for (let i = 0; i < selecteddata.length; i++) {
        let data = selecteddata[i];
        let payrolls = data.payroll;
        // let payroll_ids = [];
        payrolls.forEach(py => {
          // payroll_ids.push(py.payroll_uuid);
          if (py.payroll_status == 1)
            kalkulasiData.push(py.payroll_uuid);
          if (py.payroll_status == 2)
            bayarData.push(py.payroll_uuid);
          if (py.payroll_status == 3)
            kirimData.push(py.payroll_uuid);
        })

      }
      console.log(kalkulasiData, kirimData, bayarData);
      enableBtn();
    }

    function deselectData(ischecked = false, row = null) {
      if (ischecked == false && row) {
        // BUG HERE
        tablePenggajian.row(row).deselect();
        let singledata = tablePenggajian.row(row).data();
        let payrolls = singledata.payroll;
        // console.log('payrolls', payrolls);
        payrolls.forEach(py => {
          let kalkulasiidx = kalkulasiData.indexOf(py.payroll_uuid);
          let bayaridx = bayarData.indexOf(py.payroll_uuid);
          let kirimidx = kirimData.indexOf(py.payroll_uuid);

          if (kalkulasiidx > -1)
            kalkulasiData.splice(kalkulasiidx, 1);
          if (bayaridx > -1)
            bayarData.splice(bayaridx, 1);
          if (kirimidx > -1)
            kirimData.splice(kirimidx, 1);

        });
      } else {
        $('#karyawan_ischeck').prop("checked", false).trigger("change");
        tablePenggajian.rows().deselect();
        kalkulasiData = [];
        bayarData = [];
        kirimData = [];
      }
      console.log(kalkulasiData, kirimData, bayarData);
      enableBtn();
    }

    function processPayslipNonKaryawan(url = '#', msg = '') {
      // Swal.fire({
      //   html: msg,
      //   icon: 'question',
      //   preConfirm: () => {
      //     Swal.showLoading();
      //     // tblPengaturanPotongan.row(row).remove();
      //     // return true;
      //     return fetch(`${url}`, {
      //         method: 'POST',
      //         body: new URLSearchParams($.param({
      //           _token: $("meta[name=csrf-token]").attr('content'),
      //           payroll_uuids: kalkulasiData.concat(kalkulasiData, bayarData, kirimData),
      //         }))
      //       })
      //       .then(response => {
      //         if (!response.ok) {
      //           return response.text().then(res => {
      //             throw new Error(res);
      //           })
      //         }
      //         return response.json()
      //       })
      //       .catch(error => {
      //         Swal.showValidationMessage(`Request failed: ${error}`);
      //       })
      //   },
      //   allowOutsideClick: () => false
      // }).then((result) => {
      //   console.log('result', result)
      //   result = result.value;
      //   if (result == undefined) {
      //     return false;
      //   }

      //   if (!result.success) {
      //     let data = result.data;
      //     let infokaryawan = "<br><br>";
      //     if (data != undefined) {
      //       let karyawan = data.karyawan;
      //       karyawan.forEach(kr => {
      //         infokaryawan += `<li class="text-bold">${kr.name} (${kr.enid})</li>`;
      //       });
      //     }
      //     Swal.fire({
      //       html: result.message + infokaryawan,
      //       showCancelButton: false,
      //       confirmButtonText: "Ok",
      //       icon: 'error'
      //     })
      //     return false;
      //   }

      //   toastr.success(result.message);
      //   deselectData();
      //   tablePenggajian.draw();
      // });
    }

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
        url: `{{ route("user.page.penggajian.nonkaryawan.importpenggajian") }}?menu_id=${currentMenuId}`,
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

          $('#detailImportModalLabel').text(`Rincian Impor : ${response.totalsuccess} dari ${response.totaldata} data berhasil di impor`);

          $('#detailImportModal').modal('show');

          tablePenggajian.draw();

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
        "url": `{{ route('user.page.penggajian.nonkaryawan.importhistory') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
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
          "data": "historyimport_file_name",
          "className": "text-center",
        },
        {
          "data": "userwajibpajak_name",
          "className": "text-center",
        },
        {
          "data": "historyimport_detail_import",
          "title": "Status Impor",
          "className": "text-center",
        },
        {
          "data": "historyimport_id",
          "className": "text-center",
          "render": function(data, type, row) {
            return `<a href="#" class="btn-unduh-rincian" data-id="${data}">Unduh</a>`;
          }
        },
      ]
    })

    $("#table-history").on("click", ".btn-unduh-rincian", function(e) {
      e.preventDefault();
      let id = $(this).attr("data-id");

      window.open(`{{ route('user.page.penggajian.nonkaryawan.generatehistory') }}?menu_id=${currentMenuId}&id=${id}`, '_blank');
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
      window.open(`{{ route('user.page.penggajian.nonkaryawan.generatetemplate') }}?menu_id=${currentMenuId}`, '_blank');
      window.close();
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>