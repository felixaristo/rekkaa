<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="row">
        <div class="card-header row">
          <div class="col-sm-6">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <div class="col-sm-6 text-right">
            <a class="btn btn-sm btn-warning" id="btn-kelola-anggota" href="#">
              <i class='bx bx-plus'></i> Anggota
            </a>
          </div>
        </div>
        
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-groupanggota">
                <thead class="table-light">
                  <tr>
                    <th>Group</th>
                    <th>Email</th>
                    <th>Karyawan</th>
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

<!-- Modal Anggota -->
<div class="modal fade" id="modalKelolaAnggota" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Kelola Anggota</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formGroupAnggota" method="POST" action="{{route('user.page.pengaturan.groupanggota.store')}}?menu_id={{request()->get('menu_id')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="user_email">Email</label>
              <div class="col-sm-8">
                <input type="email" required value="" name="user_email" id="user_email" class="form-control" placeholder="Masukkan Email">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="user_name">Nama</label>
              <div class="col-sm-8">
                <input type="text" required value="" name="user_name" id="user_name" class="form-control" placeholder="Masukkan Nama">
              </div>
            </div>
            <!-- <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="user_hp">No. Hp</label>
              <div class="col-sm-8">
                <input type="text" required value="" name="user_hp" id="user_hp" class="form-control" placeholder="Masukkan No. HP">
              </div>
            </div> -->
            
            <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="usergroup_id">Group</label>
              <div class="col-sm-8">
                <select required style="width: 100%;" name="usergroup_id" id="usergroup_id" class="form-control" data-placeholder="Pilih Group"></select>
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

<!-- Modal Karyawan -->
<div class="modal fade" id="modalKaryawan" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- <h5 class="modal-title" id="modalCenterTitle"></h5> -->
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formKaryawan" method="POST" action="{{route('user.page.pengaturan.groupanggota.storekaryawan')}}?menu_id={{request()->get('menu_id')}}" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="user_id">
            <input type="hidden" name="usergroup_id">
            <input type="hidden" name="karyawanemail">
            <div class="row mb-3">
              <div class="list-group list-group-karyawan">
                
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-12 text-right">
                <button type="submit" disabled class="btn btn-warning btn-sm">Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.groupanggota.store', '')}}?menu_id="+currentMenuId;
    let actionUpdateUrl = "{{route('user.page.pengaturan.groupanggota.update', '')}}";
    // Begin Kelola Anggota
    $("#btn-kelola-anggota").click(function(e) {
      e.preventDefault();

      $("#modalKelolaAnggota").modal("show");
    })

    $("#usergroup_id").select2({
  		dropdownParent: $("#modalKelolaAnggota"),
      ajax: {
        url: `{{route('user.page.pengaturan.groupakses.select')}}?menu_id=${currentMenuId}`,
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
              item.id = item.usergroup_id;
              item.text = item.usergroup_name;
              // console.log('item.kode', item)
              return item
          })
          return {
              results: items
          };
        },
      },
    });

    let formGroupAnggota = $("#formGroupAnggota").validate({
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
      rules: {},
      submitHandler: function(form) {
        $(".spinner-box").css({'display': 'table'});
        
        $.ajax({
          method: form.method,
          url: form.action,
          data: $(form).serialize()+"&"+$.param({
            _token: $("meta[name=csrf-token]").attr('content'),
          }),
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
                  usergroup_id: response.message
                })
              } else {
                  toastr.error(response.message);
              }
                return false;
            }
            
            $('#formGroupAnggota [type=reset]').click();
            toastr.success(response.message);
            tblPengaturanGroupAnggota.draw();

            // change url
            $("#formGroupAnggota").attr("action", actionStoreUrl);
            $("#modalKelolaAnggota").modal("hide");
          }
        })
      },
    })

    $("#formGroupAnggota [type=reset]").click(function(e) {
      e.preventDefault();
      resetForm("#formGroupAnggota");
      // change url
      $("#formGroupAnggota").attr("action", actionStoreUrl);
      $("#user_email").removeAttr('disabled');
      $("#usergroup_id").val(null).trigger('change');
      $("#modalKelolaAnggota").modal("hide");
    })
    // End Kelola Anggota

    // Begin Table Kelola Anggota
    let tblPengaturanGroupAnggota = $("#table-pengaturan-groupanggota").DataTable({
      // "filtering": false,
      // "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": `{{route('user.page.pengaturan.groupanggota.datatable', '')}}?menu_id=${currentMenuId}`,
          "type": "GET",
          "data": function(data) {
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [3],
        width: 30
      }, {
        target: [0,3],
        className: 'text-center'
      }],
      "columns": [
          {
            "data": "usergroup_name"
          },
          {
            "data": "user_email"
          },
          {
            "data": "karyawan_name",
            "render": function(data, type, row) {
              if(!data) {
                return `<button type="button" class="btn btn-sm btn-outline-warning btn-add-karyawan"><i class="bx bx-plus"></i> Karyawan</button>`
              }
              return data;
            }
          },
          {
            "data": "ms_user_id",
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
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
                  >
                </div>
              </div>
                `
              return '';
            }
          },
      ],
  });
  // edit kelola anggota
  $("#table-pengaturan-groupanggota").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $('#formGroupAnggota [type=reset]').click();
    
    $("#modalKelolaAnggota").modal("show");

    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupAnggota.row(row).data();
    // console.log('data', data)
    // change url
    $("#formGroupAnggota").attr("action", actionUpdateUrl+"/"+data.user_id+"?menu_id="+currentMenuId);
    // set data
    
    $("#user_email").val(data.user_email).attr('disabled', true);
    $("#user_name").val(data.userwajibpajak_name);
    // $("#user_hp").val(data.userwajibpajak_phone);
    $("#usergroup_id").append(new Option(data.usergroup_name, data.usergroup_id, true, true)).trigger('change');
  })

  // delete kelola anggota
  $("#table-pengaturan-groupanggota").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupAnggota.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus anggota <b>'+ data.user_email +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanTunjangan.row(row).remove();
          // return true;
          return fetch(`{{route('user.page.pengaturan.groupanggota.destroy', '')}}/${data.usergroup_id}?menu_id=${currentMenuId}`, {
              method: 'DELETE',
              body: new URLSearchParams($.param({
                _token: $("meta[name=csrf-token]").attr('content'),
                user_id: data.user_id
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
      if(result == undefined) {
          return false;
      }
      
      if (!result.success) {

          Swal.fire({
              html: result.message,
              showCancelButton: false,
              confirmButtonText: "Ok",
              icon: 'error',
          })
          return false;
      }

      toastr.success(result.message);
      tblPengaturanGroupAnggota.draw();
    });
  })

  // Tambah karyawan
  $("#table-pengaturan-groupanggota").on("click", ".btn-add-karyawan", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupAnggota.row(row).data();
    let email = data.user_email;

    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      method: "POST",
      url: "{{route('user.page.pengaturan.groupanggota.getkaryawan')}}",
      data: {
        _token: $("meta[name=csrf-token]").attr('content'),
        email: email,
      },
      error: function(error) {
        $(".spinner-box").fadeOut();
        // console.log('error.responseJSON', error.responseJSON)
        if(error.responseJSON) {
          let errs = error.responseJSON.errors;

          Swal.fire({
              showCancelButton: false,
              confirmButtonText: "Ok",
              icon: 'error',
              html: errs
          })
        }
      }, 
      success: function(response) {
        console.log(response, 'response')
        $(".spinner-box").fadeOut();
        let listKaryawan = '';
        
        if(response.success) {
          let karyawan = response.data.karyawan;
          listKaryawan = `<div class="list-group-item list-group-item-action flex-column align-items-start" data-userid="${data.user_id}" data-usergroupid="${data.usergroup_id}" data-email="${karyawan.karyawan_email}">
          <div class="d-flex justify-content-between w-100">
            <h6>${karyawan.karyawan_name}</h6>
            <span class="text-primary">${karyawan.masakerja.karyawanmasakerja_status}</span>
          </div>
          <hr>
          <div class="mb-1 row">
            <span class="col-sm-3">Email <span class="float-right">:</span></span>
            <span class="col-sm-9 text-right email">${karyawan.karyawan_email}</span>
          </div>
          <div class="mb-1 row">
            <span class="col-sm-3">Hp <span class="float-right">:</span></span>
            <span class="col-sm-9 text-right">${karyawan.karyawan_phone}</span>
          </div>
          <div class="mb-1 row">
            <span class="col-sm-3">NIK <span class="float-right">:</span></span>
            <span class="col-sm-9 text-right">${karyawan.karyawan_nik}</span>
          </div>
          <div class="mb-1 row">
            <span class="col-sm-3">NPWP <span class="float-right">:</span></span>
            <span class="col-sm-9 text-right">${karyawan.karyawan_npwp}</span>
          </div>
          <div class="mb-1 row">
            <span class="col-sm-3">Posisi <span class="float-right">:</span></span>
            <span class="col-sm-9 text-right">${karyawan.masakerja.karyawanmasakerja_position}</span>
          </div>
        </div>`;
        } else {
          listKaryawan = `<div class="list-group-item flex-column border-danger">
            <div class="text-center">
              <h6 class="text-danger">Karyawan tidak ditemukan!</h6>
            </div>
          </div>`
        }
        
        $("#modalKaryawan .list-group-karyawan").html(listKaryawan);
      
        $("#modalKaryawan").modal("show");  
      }
    })
  });
  $("#modalKaryawan .list-group-karyawan").on("click", ".list-group-item-action", function(e) {
    e.preventDefault();
    let $this = $(this);
    let email = $this.attr('data-email').trim();
    let user_id = $this.attr('data-userid').trim();
    let usergroup_id = $this.attr('data-usergroupid').trim();
    $this.toggleClass('border-active');
    if($this.hasClass('border-active')) {  
      $("#formKaryawan [name=user_id]").val(user_id);
      $("#formKaryawan [name=usergroup_id]").val(usergroup_id);
      $("#formKaryawan [name=karyawanemail]").val(email);
      $("#formKaryawan [type=submit]").removeAttr('disabled');
    } else {
      $("#formKaryawan [name=karyawanemail]").val('');
      $("#formKaryawan [type=submit]").attr('disabled', true);
    }
  })
  $("#formKaryawan").submit(function(e) {
    e.preventDefault();
    let form = $(this);
    let userid = $("#formKaryawan [name=user_id]").val();
    let usergroupid = $("#formKaryawan [name=usergroup_id]").val();
    let email = $("#formKaryawan [name=karyawanemail]").val();

    if(!email && !userid && !usergroupid) {
      Swal.fire({
        showCancelButton: false,
        confirmButtonText: "Ok",
        icon: 'error',
        html: 'Silahkan isi data!'
      });
      return false;
    }

    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      method: form.attr('method'),
      url: form.attr('action'),
      data: {
        _token: $("meta[name=csrf-token]").attr('content'),
        user_id: userid,
        usergroup_id: usergroupid,
        email: email,
      },
      error: function(error) {
        $(".spinner-box").fadeOut();
        // console.log('error.responseJSON', error.responseJSON)
        if(error.responseJSON) {
          let errs = error.responseJSON.errors;

          Swal.fire({
              showCancelButton: false,
              confirmButtonText: "Ok",
              icon: 'error',
              html: errs
          })
        }
      }, 
      success: function(result) {
        console.log(result, 'result')
        $(".spinner-box").fadeOut();

        if (!result.success) {
          Swal.fire({
            html: result.message,
            showCancelButton: false,
            confirmButtonText: "Ok",
            icon: 'error',
          })
          return false;
        }
        $("#modalKaryawan").modal("hide");
        toastr.success(result.message);
        tblPengaturanGroupAnggota.draw();
      }
    })
  });
  // End Table Kelola Anggota

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>