<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">
          <a class="btn btn-sm btn-warning" id="btn-penggajian" href="#">
            <i class='bx bx-plus'></i> Penggajian
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-penggajian">
                <thead class="table-light">
                  <tr>
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
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<!-- Modal Pengaturan Penggajian -->
<div class="modal fade" id="modalPenggajian" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Pengaturan Penggajian</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formPenggajian" method="POST" action="{{route('user.page.pengaturan.penggajian.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="stpenggajiankaryawan_id" id="stpenggajiankaryawan_id">
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_name">Deskripsi</label>
              <div class="col-sm-7">
                <input type="text" required name="stpenggajiankaryawan_name" id="stpenggajiankaryawan_name" class="form-control" placeholder="Masukkan Deskripsi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_period">Periode Penggajian</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpenggajiankaryawan_period" id="stpenggajiankaryawan_period" class="form-control" data-placeholder="-:Pilih Periode:-">
                  <option value=""></option>  
                  <option value="KALENDER">Bulan Kalender</option>
                  <option value="TANGGAL">Tanggal Spesifik</option>
                </select>
              </div>
            </div>
            <div class="row mb-3 tgl_spesifik_box">
              <label class="col-sm-5" for="stpenggajiankaryawan_startdate">Tanggal Awal / Akhir</label>
              <div class="col-sm-3">
                <input type="number" min="1" disabled required name="stpenggajiankaryawan_startdate" id="stpenggajiankaryawan_startdate" class="form-control" placeholder="Awal">
              </div>
              <div class="col-sm-3">
                <input type="number" min="1" disabled required name="stpenggajiankaryawan_enddate" id="stpenggajiankaryawan_enddate" class="form-control" placeholder="Akhir">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_paymentdate">Tanggal Pembayaran</label>
              <div class="col-sm-7">
                <input type="number" min="1" required name="stpenggajiankaryawan_paymentdate" id="stpenggajiankaryawan_paymentdate" class="form-control" placeholder="Tanggal Pembayaran">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_method">Metode Prorata Gaji Pokok</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpenggajiankaryawan_method" id="stpenggajiankaryawan_method" class="form-control" data-placeholder="-:Pilih Metode:-">
                  <option value=""></option>  
                  <option value="KALENDER">Hari Kalender</option>
                  <option value="KERJA">Hari Kerja</option>
                  <option value="TETAP">Angka Tetap</option>
                </select>
              </div>
            </div>
            <div class="row mb-3 hari_tetap_box">
              <label class="col-sm-5" for="stpenggajiankaryawan_day">Angka Tetap</label>
              <div class="col-sm-7">
                <div class="input-group">
                  <input type="number" min="1" disabled required name="stpenggajiankaryawan_day" id="stpenggajiankaryawan_day" class="form-control" placeholder="Hari">
                  <span class="input-group-text">Hari</span>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_employees">Karyawan</label>
              <div class="col-sm-7">
                <select multiple style="width: 100%;" name="stpenggajiankaryawan_employees[]" id="stpenggajiankaryawan_employees" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_weekendoption">Opsi Hari Libur</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_weekendoption" class="form-check-input" type="radio" value="MAJU" id="stpenggajiankaryawan_weekendoption_1">
                  <label class="form-check-label" for="stpenggajiankaryawan_weekendoption_1"> Maju </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_weekendoption" class="form-check-input" type="radio" value="MUNDUR" id="stpenggajiankaryawan_weekendoption_2" checked="">
                  <label class="form-check-label" for="stpenggajiankaryawan_weekendoption_2"> Mundur </label>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_autoemailpayslip">Auto Email Payslip</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_autoemailpayslip" class="form-check-input" type="radio" value="1" id="stpenggajiankaryawan_autoemailpayslip_y">
                  <label class="form-check-label" for="stpenggajiankaryawan_autoemailpayslip_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_autoemailpayslip" class="form-check-input" type="radio" value="0" id="stpenggajiankaryawan_autoemailpayslip_t" checked="">
                  <label class="form-check-label" for="stpenggajiankaryawan_autoemailpayslip_t"> Tidak </label>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpenggajiankaryawan_active">Aktif</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_active" class="form-check-input" type="radio" value="1" id="stpenggajiankaryawan_active_y" checked="">
                  <label class="form-check-label" for="stpenggajiankaryawan_active_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_active" class="form-check-input" type="radio" value="0" id="stpenggajiankaryawan_active_t">
                  <label class="form-check-label" for="stpenggajiankaryawan_active_t"> Tidak </label>
                </div>
              </div>
            </div>
            <fieldset>
              <legend>Slip Gaji</legend>
              <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_pic">Penanggung Jawab</label>
              <div class="col-sm-7">
                <input type="text" required name="stpenggajiankaryawan_pic" id="stpenggajiankaryawan_pic" class="form-control" placeholder="Penanggung Jawab">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_location">Domisili Usaha</label>
              <div class="col-sm-7">
                <input type="text" required name="stpenggajiankaryawan_location" id="stpenggajiankaryawan_location" class="form-control" placeholder="Lokasi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_logo">Logo</label>
              <div class="col-sm-7">
                <input name="stpenggajiankaryawan_logo" class="form-control" type="file" id="stpenggajiankaryawan_logo">
                <!-- <span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span> -->
                <br>
                <div class="d-block rounded" id="logo-box"></div>
              </div>
            </div>
            </fieldset>
            
            <div class="row mb-3">
              <div class="col-sm-12 text-right">
                <button type="reset" class="btn btn-outline-danger btn-sm">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.penggajian.store')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.penggajian.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.penggajian.delete', '')}}";
    // let updatedKaryawanIds = [];
    let deletedEmployeeIds = [];
    $("#stpenggajiankaryawan_method").select2({
		  dropdownParent: $("#modalPenggajian #formPenggajian"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      if(data.id == 'TETAP') {
        $('.hari_tetap_box label').append('<span class="text-danger">*</span>')
        $("#stpenggajiankaryawan_day").removeAttr('disabled');
      } else {
        $('.hari_tetap_box label > .text-danger').remove()
        $("#stpenggajiankaryawan_day").val(null).attr('disabled', true);
      }
    });;
    
    $("#stpenggajiankaryawan_period").select2({
		  dropdownParent: $("#modalPenggajian #formPenggajian"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      if(data.id == 'KALENDER') {
        $('.tgl_spesifik_box label > .text-danger').remove()
        $("#stpenggajiankaryawan_startdate").val(null).attr('disabled', true);
        $("#stpenggajiankaryawan_enddate").val(null).attr('disabled', true);
      } else {
        $('.tgl_spesifik_box label').append('<span class="text-danger">*</span>')
        $("#stpenggajiankaryawan_startdate").removeAttr('disabled');
        $("#stpenggajiankaryawan_enddate").removeAttr('disabled');
      }
    });
    $("#stpenggajiankaryawan_employees").select2({
		  dropdownParent: $("#modalPenggajian #formPenggajian"),
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
    // .on("select2:select", function(e) {
    //   let data = e.params.data;
    //   let stpenggajiankaryawan_id = $("#stpenggajiankaryawan_id").val();

    //   if(stpenggajiankaryawan_id) { // update employee's allowance
    //     updatedKaryawanIds.push(data.id);
    //   }
    // })
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let stpenggajiankaryawan_id = $("#stpenggajiankaryawan_id").val();

      if(stpenggajiankaryawan_id) { // update employee's allowance
        deletedEmployeeIds.push(data.id);
      }
    });
    // Begin Table Penggajian
    let tblPengaturanPenggajian = $("#table-pengaturan-penggajian").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
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
        target: [4],
        width: 30
      }, {
        target: [0,1,2,3,4],
        className: 'text-center'
      }],
      "columns": [
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
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
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
    $("#formPenggajian [type=reset]").click();
    $("#modalPenggajian").modal("show");
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanPenggajian.row(row).data();
    tempData = data;
    console.log('data.stpenggajiankaryawan_period', data.stpenggajiankaryawan_period);
    // change url
    $("#formPenggajian").attr("action", actionUpdateUrl+"/"+data.stpenggajiankaryawan_id+"?menu_id="+currentMenuId);
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
    
    $(`#formPenggajian input[name=stpenggajiankaryawan_weekendoption][value=${data.stpenggajiankaryawan_weekendoption}]`).click();
    $(`#formPenggajian input[name=stpenggajiankaryawan_autoemailpayslip][value=${data.stpenggajiankaryawan_autoemailpayslip}]`).click();
    $(`#formPenggajian input[name=stpenggajiankaryawan_active][value=${data.stpenggajiankaryawan_active}]`).click();
    
    let karyawans = data.karyawans;
    if(karyawans.length > 0) {
      let karyawanOpts = '';
      karyawans.forEach(kr => {
        karyawanOpts += `<option value="${kr.karyawan_id}" selected>${kr.karyawan_name}</option>`;
      })
      $("#stpenggajiankaryawan_employees").html(karyawanOpts);
    }

    $("#logo-box").html('');
    if(data.stpenggajiankaryawan_logo)
      $("#logo-box").html(`<img src="${data.stpenggajiankaryawan_logo}" alt="avatar" class="img-fluid">`);
  })
  // End Table Penggajian

  $("#btn-penggajian").click(function(e) {
    e.preventDefault();
    $("#formPenggajian [type=reset]").click();

    $("#modalPenggajian").modal("show");
  })
  let formPenggajian = $("#formPenggajian").validate({
    errorPlacement: function(error, element) {
      // console.log(element);
      var isInputGroup = $(element).parent();
      console.log('isInputGroup', isInputGroup.length)
      let elem = $(element);
      if (elem.hasClass("select2-hidden-accessible")) {
          // element = $("#select2-" + elem.attr("id") + "-container").parent(); 
          element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container'); 
          error.insertAfter(element);
      } else {
          if (isInputGroup.hasClass('input-group')) {
              // $(element).parent('.input-group').insertAfter(error)
              error.insertAfter($(element).parent('.input-group'));
          } else {
              error.insertAfter(element);
          }
      }
    },
    rules: {
      stpenggajiankaryawan_paymentdate: {
        min: 1,
        max: 31,
      },
      stpenggajiankaryawan_startdate: {
        min: 1,
        max: 31,
      },
      stpenggajiankaryawan_enddate: {
        min: 1,
        max: 31,
      },
      stpenggajiankaryawan_day: {
        min: 1,
        max: 31,
      }
    },
    submitHandler: function(form) {
        $(".spinner-box").css({'display': 'table'});

        let formData = new FormData();
        let dataArr = $("#formPenggajian").serializeArray();

        for (let i = 0; i < dataArr.length; i++) {
          formData.append(dataArr[i].name, dataArr[i].value);
        }

        formData.append('_token', $("meta[name=csrf-token]").attr('content'));
        formData.append('stpenggajiankaryawan_logo', $('#stpenggajiankaryawan_logo')[0].files[0]);
        formData.append('deletedemployee_ids', deletedEmployeeIds);
        $.ajax({
            method: form.method,
            url: form.action,
            processData: false, // tell jQuery not to process the data            
				    contentType: false, // tell jQuery not to set contentType  
            data: formData,
            error: function(error) {
              $(".spinner-box").fadeOut();
              console.log('error.responseJSON', error.responseJSON)
              if(error.responseJSON) {
                let errs = error.responseJSON.errors;
                let errorName = [];
                if(errs) {
                  let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
                  // console.log('errsArr', errsArr)
                  errsArr.forEach(err => {
                    console.log(err);
                    errorName.push(err[1]);
                  });
                } else {
                  errorName = [error.responseJSON.message];
                }

                Swal.fire({
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    icon: 'error',
                    html: errorName
                })
              }
            }, 
            success: function(response) {
                console.log(response, 'response')
                $(".spinner-box").fadeOut();
                if(!response.success) {
                  if(Array.isArray(response.message)) {
                    formAuthentication.showErrors({
                      stpenggajiankaryawan_name: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                toastr.success(response.message);
                tblPengaturanPenggajian.draw();

                $("#formPenggajian [type=reset]").click();
                
            }
        })
    },
  })

  $("#formPenggajian [type=reset]").click(function(e) {
    e.preventDefault();
    resetForm('#formPenggajian');
    deletedEmployeeIds = []
    // change url
    $("#formPenggajian").attr("action", actionStoreUrl+"?menu_id={{request()->get('menu_id')}}");
    $("#stpenggajiankaryawan_name").removeAttr('disabled');
    $(`#formPenggajian input[name=stpenggajiankaryawan_active][value=1]`).click();

    if($('#modalPenggajian').is(':visible')) {
      $("#modalPenggajian").modal("hide");
    }
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>