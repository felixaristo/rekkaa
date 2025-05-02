<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <?php if(in_array('C', request()->get('permission_codes'))): ?>
          <div class="col-sm-6 text-right">
            <a class="btn btn-sm btn-warning" id="btn-penggajian" href="#">
              <i class='bx bx-plus'></i> Penggajian
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-penggajian">
            <thead class="table-light">
              <tr>
                <th>Kode Penggajian</th>
                <th>Nama</th>
                <th>Metode</th>
                <th>Periode</th>
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
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

@include('user.pengaturan.penggajian.create')

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.penggajian.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.penggajian.delete', '')}}";
    // let updatedKaryawanIds = [];
    let deletedEmployeeIds = [];
    
    // Begin Table Penggajian
    let tblPengaturanPenggajian = $("#table-pengaturan-penggajian").DataTable({
      // "filtering": false,
      "ordering": true,
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Nama / Kode Penggajian",
      },
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('user.page.pengaturan.penggajian.datatable', ['menu_id' => request()->get('menu_id')])}}",
          "type": "GET",
          "data": function(data) {
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [5],
        width: 30,
        orderable: false,
      }, {
        target: [0,1,2,3,4,5],
        className: 'text-center'
      }],
      "columns": [
          {
            "data": "stpenggajiankaryawan_id",
            "title": "Kode Penggajian",
            "render": function(data, type, row) {
              if (data && (type === 'display' || type === 'filter')) {
                return 'PE' + data;
              }
              return data;
            }
          },
          {
              "data": "stpenggajiankaryawan_name"
          },
          {
              "data": "stpenggajiankaryawan_method",
              "render": function(data, type, row) {
                let method = '';
                if(row.stpenggajiankaryawan_method == 'KALENDER') {
                  method = 'Hari Kalender';
                } else if(row.stpenggajiankaryawan_method == 'KERJA') {
                  method = 'Hari Kerja';
                } else if(row.stpenggajiankaryawan_method == 'TETAP') {
                  method = 'Angka Tetap';
                }
                
                return method;
              }
          },
          {
              "data": "stpenggajiankaryawan_period",
              "render": function(data, type, row) {
                return (row.stpenggajiankaryawan_period == 'KALENDER') ? 'Bulan Kalender' : 'Tanggal Spesifik';
              }
          },
          {
              "data": "stpenggajiankaryawan_active",
              "render": function(data, type, row) {
                return (data == "1") ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
              }
          },
          {
            "data": "stpenggajiankaryawan_id",
            "render": function(data, type, row) {
                return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                <?php if(in_array('U', request()->get('permission_codes'))): ?>
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                <?php if(in_array('SD', request()->get('permission_codes'))): ?>
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                <?php endif; ?>
                </div>
              </div>
                `
            }
          },
      ],
    });

  let tempData = null;
  $("#table-pengaturan-penggajian").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formStPenggajian [type=reset]").click();
    $("#modalPenggajian").modal("show");
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanPenggajian.row(row).data();
    tempData = data;
    console.log('data.stpenggajiankaryawan_period', data.stpenggajiankaryawan_period);
    // change url
    $("#formStPenggajian").attr("action", actionUpdateUrl+"/"+data.stpenggajiankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#stpenggajiankaryawan_id").val(data.stpenggajiankaryawan_id);
    $("#stpenggajiankaryawan_name").val(data.stpenggajiankaryawan_name).attr('disabled', true);
    $("#stpenggajiankaryawan_method").val(data.stpenggajiankaryawan_method).trigger('change');
    $("#stpenggajiankaryawan_period").val(data.stpenggajiankaryawan_period).trigger('change');
    $("#stpenggajiankaryawan_paymentdate").val(data.stpenggajiankaryawan_paymentdate);
    $("#stpenggajiankaryawan_pic").val(data.stpenggajiankaryawan_pic);
    $("#stpenggajiankaryawan_location").val(data.stpenggajiankaryawan_location);

    if(data.stpenggajiankaryawan_method == 'TETAP') {
      $("#stpenggajiankaryawan_day").val(data.stpenggajiankaryawan_day).removeAttr('disabled');
    } else {
      $("#stpenggajiankaryawan_day").val('').attr('disabled', true);
    }

    if(data.stpenggajiankaryawan_period == 'TANGGAL') {
      $("#stpenggajiankaryawan_startdate").val(data.stpenggajiankaryawan_startdate).removeAttr("disabled");
      $("#stpenggajiankaryawan_enddate").val(data.stpenggajiankaryawan_enddate).removeAttr("disabled");
    } else {
      $("#stpenggajiankaryawan_startdate").val('').attr("disabled", true);
      $("#stpenggajiankaryawan_enddate").val('').attr("disabled", true);
    }
    
    $(`#formStPenggajian input[name=stpenggajiankaryawan_weekendoption][value=${data.stpenggajiankaryawan_weekendoption}]`).click();
    $(`#formStPenggajian input[name=stpenggajiankaryawan_autoemailpayslip][value=${data.stpenggajiankaryawan_autoemailpayslip}]`).click();
    $(`#formStPenggajian input[name=stpenggajiankaryawan_active][value=${data.stpenggajiankaryawan_active}]`).click();
    
    if (data.stpenggajiankaryawan_is_all === false) {
      $("#stpenggajiankaryawan_is_all").val(0).trigger('change');
      $("#karyawan-container").slideDown();

      getListKaryawan(data.stpenggajiankaryawan_id);
    } else {
      $("#stpenggajiankaryawan_is_all").val(1).trigger('change');
      $("#karyawan-container").slideUp();
      $("#stpenggajiankaryawan_employees").val(null).trigger('change');
    }

    $("#excodepenggajian-container").slideDown();
    $("#excodepenggajian").val('PE' + data.stpenggajiankaryawan_id);

    $("#logo-box").html('');
    if(data.stpenggajiankaryawan_logo)
      $("#logo-box").html(`<img src="${data.stpenggajiankaryawan_logo}" alt="avatar" class="img-fluid">`);
    if(data.stpenggajiankaryawan_isnonemployee)
      $("#stpenggajiankaryawan_isnonemployee_y").prop('checked', true);
  })
  $("#table-pengaturan-penggajian").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanPenggajian.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus penggajian <b>'+ data.stpenggajiankaryawan_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanPotongan.row(row).remove();
          // return true;
          return fetch(`${actionDeleteUrl}/${data.stpenggajiankaryawan_id}?menu_id=${currentMenuId}`, {
              method: 'POST',
              body: new URLSearchParams($.param({_token: $("meta[name=csrf-token]").attr('content')}))
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
      if(result == undefined) {
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
      tblPengaturanPenggajian.draw();
    });
  });
  // End Table Penggajian

  $("#btn-penggajian").click(function(e) {
    e.preventDefault();
    $("#formStPenggajian [type=reset]").click();

    $("#modalPenggajian").modal("show");
  })

  $("#modalPenggajian").on("hide.bs.modal", function () {
    tblPengaturanPenggajian.draw(); // Refresh the DataTable
  });

  function getListKaryawan(stpenggajian_id) {
    $.ajax({
      url: `{{route('user.page.pengaturan.penggajian.listkaryawan', '')}}/${stpenggajian_id}?menu_id=${currentMenuId}`,
      method: 'get',
      dataType: 'json',
      success: function(res) {
        console.log('res', res)
        if(res.data) {
          let karyawans = res.data;
          if(karyawans.length > 0) {
            let karyawanOpts = '';
            karyawans.forEach(kr => {
              karyawanOpts += `<option value="${kr.karyawan_id}" selected>${kr.karyawan_name}</option>`;
            })
            $("#stpenggajiankaryawan_employees").html(karyawanOpts);
          }
        }
      }
    })
  }

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>