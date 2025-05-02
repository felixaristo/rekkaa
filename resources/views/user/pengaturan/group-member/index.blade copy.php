<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="row">
        <div class="col-sm-6">
          <h5 class="card-header">{{$title}}</h5>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <form id="formGroupAnggota" method="POST" action="{{route('user.page.pengaturan.groupanggota.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <input type="hidden" name="usergroupmember_id" id="usergroupmember_id">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="usergroup_id">Group</label>
                <div class="col-sm-4">
                  <select required name="usergroup_id" id="usergroup_id" class="form-control" data-placeholder="Pilih Group"></select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="usergroup_member">Anggota</label>
                <div class="col-sm-4">
                  <select required multiple name="usergroup_member[]" id="usergroup_member" class="form-control" data-placeholder="Pilih Anggota"></select>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6 text-right">
                  <button type="reset" class="btn btn-outline-danger btn-sm">Batal</button>
                  <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
                </div>
              </div>
            </form>
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-groupanggota">
                <thead class="table-light">
                  <tr>
                    <th>Group</th>
                    <th>Anggota</th>
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

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";

    $("#usergroup_id").select2({
      ajax: {
        url: "{{route('user.page.pengaturan.groupakses.select')}}",
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

    $("#usergroup_member").select2({
      ajax: {
        url: "{{route('user.page.user.select')}}",
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
              item.id = item.user_id;
              item.text = item.user_email;
              // console.log('item.kode', item)
              return item
          })
          return {
              results: items
          };
        },
      },
    });

    // Begin Table Group Member
    let tblPengaturanGroupAnggota = $("#table-pengaturan-groupanggota").DataTable({
      // "filtering": false,
      "searching": false,
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
        target: [1],
        width: 30
      }, {
        target: [0,1,2],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "usergroup_name"
          },
          {
              "data": "usergroupmember_members"
          },
          {
            "data": "usergroupmember_id",
            "render": function(data, type, row) {
              //   return `
              //   <div class="dropdown">
              //   <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              //     <i class="bx bx-dots-vertical-rounded"></i>
              //   </button>
              //   <div class="dropdown-menu">
              //     <a class="dropdown-item btn-edit" href="javascript:void(0);"
              //       ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
              //     >
              //     <a class="dropdown-item btn-delete" href="javascript:void(0);"
              //       ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
              //     >
              //   </div>
              // </div>
              //   `
              return '';
            }
          },
      ],
  });
  // End Table User Group Anggota

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
                
                resetForm('#formGroupAnggota');
                toastr.success(response.message);
                tblPengaturanGroupAnggota.draw();

                // $("#usergroup_name").removeAttr('disabled');
                // change url
                $("#formGroupAnggota").attr("action", "{{route('user.page.pengaturan.groupanggota.store', ['menu_id' => request()->get('menu_id')])}}");
            }
        })
    },
  })

  $("#formGroupAnggota [type=reset]").click(function() {
    // change url
    $("#formGroupAnggota").attr("action", "{{route('user.page.pengaturan.groupanggota.store', ['menu_id' => request()->get('menu_id')])}}");
    $("#usergroup_name").removeAttr('disabled')
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>