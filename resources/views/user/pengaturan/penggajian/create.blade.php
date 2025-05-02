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
          <form id="formStPenggajian" method="POST" action="{{route('user.page.pengaturan.penggajian.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="stpenggajiankaryawan_id" id="stpenggajiankaryawan_id">
            <div class="row mb-3" style="display: none;" id="excodepenggajian-container">
              <label class="col-sm-5" for="excodepenggajian">Kode Penggajian</label>
              <div class="col-sm-7">
                <input type="text" name="excodepenggajian" id="excodepenggajian" class="form-control" disabled>
              </div>
            </div>
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
            <div class="row mb-3" id="option-container">
              <label for="stpenggajiankaryawan_is_all" class="col-sm-5 lbl-req">Diberikan Kepada</label>
              <div class="col-sm-7">
                <select class="form-select" style="width: 100%;" id="stpenggajiankaryawan_is_all" name="stpenggajiankaryawan_is_all" required data-placeholder="-:Pilih Data:-">
                  <option value=""></option>
                  <option value="1">Semua Karyawan</option>
                  <option value="0">Karyawan Tertentu</option>
                </select>
              </div>
            </div>
            <div class="row mb-3" id="karyawan-container" style="display: none;">
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
            <div class="divider">
              <div class="divider-text">Informasi Slip Gaji</div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_location">Domisili Usaha</label>
              <div class="col-sm-7">
                <input type="text" required name="stpenggajiankaryawan_location" id="stpenggajiankaryawan_location" class="form-control" placeholder="Lokasi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_pic">Penanggung Jawab</label>
              <div class="col-sm-7">
                <input type="text" required name="stpenggajiankaryawan_pic" id="stpenggajiankaryawan_pic" class="form-control" placeholder="Penanggung Jawab">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5" for="stpenggajiankaryawan_logo">Logo Perusahaan</label>
              <div class="col-sm-7">
                <input name="stpenggajiankaryawan_logo" class="form-control" type="file" id="stpenggajiankaryawan_logo">
                <span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span>
                <br>
                <div class="d-block rounded" id="logo-box" style="width: 100px;"></div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-12">
                <div class="form-check form-check-inline">
                  <input name="stpenggajiankaryawan_isnonemployee" class="form-check-input" type="checkbox" value="1" id="stpenggajiankaryawan_isnonemployee_y">
                  <label class="form-check-label" for="stpenggajiankaryawan_isnonemployee_y"> Terapkan informasi slip gaji untuk non karyawan </label>
                </div>
              </div>
            </div>
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

    // let updatedKaryawanIds = [];
    let deletedEmployeeIds = [];
    $("#stpenggajiankaryawan_is_all").select2({
      dropdownParent: $("#modalPenggajian #formStPenggajian"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      console.log('data.id', data.id);
      if (data.id === "0") {
        $("#karyawan-container").slideDown();
      } else {
        $("#karyawan-container").slideUp();
        $("#stpenggajiankaryawan_employees").val(null).trigger('change');
      }
    });

    $("#stpenggajiankaryawan_method").select2({
		  dropdownParent: $("#modalPenggajian #formStPenggajian"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      if(data.id == 'TETAP') {
        $('.hari_tetap_box label').append('<span class="text-danger">*</span>')
        $("#stpenggajiankaryawan_day").removeAttr('disabled');
      } else {
        $('.hari_tetap_box label > .text-danger').remove()
        $("#stpenggajiankaryawan_day").val(null).attr('disabled', true);
      }
    });
    
    $("#stpenggajiankaryawan_period").select2({
		  dropdownParent: $("#modalPenggajian #formStPenggajian"),
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
		  dropdownParent: $("#modalPenggajian #formStPenggajian"),
      // tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
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
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let stpenggajiankaryawan_id = $("#stpenggajiankaryawan_id").val();

      if(stpenggajiankaryawan_id) { // update employee's allowance
        deletedEmployeeIds.push(data.id);
      }
    });

    let formStPenggajian = $("#formStPenggajian").validate({
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
          let dataArr = $("#formStPenggajian").serializeArray();

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
                  // tblPengaturanPenggajian.draw();

                  $("#formStPenggajian [type=reset]").click();
                  
              }
          })
      },
    })

    $("#formStPenggajian [type=reset]").click(function(e) {
      e.preventDefault();
      resetForm('#formStPenggajian');
      deletedEmployeeIds = []
      // change url
      $("#formStPenggajian").attr("action", actionStoreUrl+"?menu_id={{request()->get('menu_id')}}");
      $("#stpenggajiankaryawan_name").removeAttr('disabled');
      $(`#formStPenggajian input[name=stpenggajiankaryawan_active][value=1]`).click();

      $("#excodepenggajian-container").slideUp();
      $("#logo-box").html('');
      $("#stpenggajiankaryawan_isnonemployee_y").prop('checked', false);
      $("#karyawan-container").slideUp();
      
      if($('#modalPenggajian').is(':visible')) {
        $("#modalPenggajian").modal("hide");
      }
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>