<style>
  table.dataTable tbody tr td {
    word-wrap: break-word;
    word-break: break-all;
  }
</style>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <?php if(in_array('C', request()->get('permission_codes'))) : ?>
          <div class="col-sm-6 text-right">
            <a class="btn btn-sm btn-warning" id="btn-grup-akses" href="#">
              <i class='bx bx-plus'></i> Grup Akses
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-groupakses">
            <thead class="table-light">
              <tr>
                <th>Kode Group Akses</th>
                <th>Nama Group Akses</th>
                <th>User</th>
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

<!-- Modal Akses -->
<div class="modal fade" id="modalGrupAkses" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Grup Akses</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formGroupAkses" method="POST" action="{{route('user.page.pengaturan.groupakses.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="usergroup_id" id="usergroup_id">
            <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="usergroup_name">Nama</label>
              <div class="col-sm-8">
                <input type="text" required name="usergroup_name" id="usergroup_name" class="form-control" placeholder="Masukkan Nama">
              </div>
            </div>
            <div class="row mb-4">
              <label class="col-sm-4 lbl-req" for="usergroup_akses">Hak Akses</label>
              <div class="col-sm-8">
                <div id="tree-akses"></div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-4 lbl-req" for="usergroup_email">Email User</label>
              <div class="col-sm-8">
                <select required style="width: 100%;" name="usergroup_email[]" multiple id="usergroup_email" class="form-control" data-placeholder="Masukkan Email User"></select>
                <br>
                <span class="fs-tiny"><i class="text-danger">*</i> Pisahkan dengan tanda koma atau spasi.</span>
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

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";

    let actionStoreUrl = "{{route('user.page.pengaturan.groupakses.store', '')}}?menu_id="+currentMenuId;
    let actionUpdateUrl = "{{route('user.page.pengaturan.groupakses.update', '')}}";
    // Begin Kelola Anggota
    $("#btn-grup-akses").click(function(e) {
      e.preventDefault();
      $("#formGroupAkses [type=reset]").click();

      $("#modalGrupAkses").modal("show");
    })

    $("#usergroup_email").select2({
      dropdownParent: $("#modalGrupAkses #formGroupAkses"),
      tags: true,
      tokenSeparators: [',', ' '],
      createTag: function (params) {
        // Don't offset to create a tag if there is no @ symbol
        if (params.term.indexOf('@') === -1) {
          // Return null to disable tag creation
          return null;
        }

        return {
          id: params.term,
          text: params.term
        }
      }
    });

    let permissionData = <?php echo json_encode(getSubscriptionPermission()) ?>;
    // console.log('permissionData',permissionData);
    let dataSource = [];
    if(permissionData.length > 0) {
      permissionData.forEach(dt => {
        let tmpchildren = [];  
        let dtChildren = dt.children;
        let dtPermission = dt.permission_json;
        // console.log('dtChildrenxxx', dtChildren)
        if(dtChildren) {
          dtChildren.forEach(dtc => {
            let tmpgrandchildren = [];  
            let dtGrandChildren = dtc.children;
            let dtPermission = dtc.permission_json;
            console.log('dtPermission', dtPermission)
            if(dtGrandChildren) {
              dtGrandChildren.forEach(dtgc => {
                let dtGrandGrandChildren = dtgc.children;
                let tmpgrandgrandchildren = [];
                let dtGPermission = dtgc.permission_json;

                if(dtGPermission) {
                  dtGPermission.forEach(dtgp => {
                    tmpgrandgrandchildren.push({
                      id: dtgp.permission_id,
                      text: dtgp.permission_code_name
                    })
                  });
                }
                
                tmpgrandchildren.push({
                  id: 'NAN',
                  text: dtgc.menu_title,
                  children: tmpgrandgrandchildren
                })
              })
            } else {
              if(dtPermission) {
                dtPermission.forEach(dtp => {
                  tmpgrandchildren.push({
                    id: dtp.permission_id,
                    text: dtp.permission_code_name
                  })
                })
              }
            }
            
            tmpchildren.push({
              id: 'NAN',
              text: dtc.menu_title,
              children: tmpgrandchildren
            })
          })
        }
        let tmp = {
          id: 'NAN',
          text: dt.menu_title,
          children: tmpchildren
        }
        dataSource.push(tmp);
      });
    }
    let treeAkses = $('#tree-akses').tree({
      primaryKey: 'id',
      uiLibrary: 'bootstrap',
      //dataSource: '/Locations/Get',
      // dataSource: [ { id: 12, text: 'foo', children: [ { id: 23, text: 'bar' } ] } ]
      dataSource: dataSource,
      // dataSource: [
      //   { id: 1, text: 'Apple', children: [ { id: 2, text: 'Avocado' } ] },
      //   { id: 3, text: 'Banana', children: [
      //       { id: 4, text: 'Beans' },
      //       { id: 5, text: 'Broccoli',
      //         children: [ { id: 6, text: 'Bunch Grape' } ]
      //       }
      //     ]
      //   }
      // ],
      checkboxes: true
    });
    // Begin Table Wajib Pajak
    let tblPengaturanGroupAkses = $("#table-pengaturan-groupakses").DataTable({
      // "filtering": false,
      "ordering": true,
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [[0, 'asc']], //Initial no order.
      "searchDelay": 1050,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Nama Group Akses",
      },
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": `{{route('user.page.pengaturan.groupakses.datatable', '')}}?menu_id=${currentMenuId}`,
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
        width: 30,
        orderable: false,
      }, {
        target: [0,1,2,3],
        className: 'text-center'
      }],
      "columns": [
          {
            "data": "usergroup_id",
            "render": function(data, type, row) {
              if (data && (type === 'display' || type === 'filter')) {
                return 'UA' + data;
              }
              return data;
            }
          },
          {
              "data": "usergroup_name"
          },
          {
              "data": "user",
              "render": function(data, type, row) {
                let emails = [];
                if(data) {
                  data.forEach(dt => {
                    emails.push(dt.user_email);
                  })
                }
                return emails.join(', ');
              }  
          },
          {
            "data": "usergroup_id",
            "render": function(data, type, row) {
              console.log('row', row);
              let btn = '';
              <?php if(in_array('SD', request()->get('permission_codes'))) : ?>
              if(row.usergroup_name != 'Admin' && row.usergroup_code != 'REKKAA') {
                btn = `<a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Nonaktif</a
                  >`;
              }
              <?php endif; ?>
              return `
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                <?php if(in_array('U', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                  ${btn}
                </div>
              </div>
                `
            }
          },
      ],
  });

  $("#table-pengaturan-groupakses").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formGroupAkses [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupAkses.row(row).data();
    // console.log('data', data)
    let usergroupapermission = data.usergroupaccess.usergroupaccess_permissions;
    let arrpermsission = usergroupapermission.split(',');
    console.log('arrpermsission', arrpermsission);
    if(arrpermsission.length > 0) {
      arrpermsission.forEach(pm => {
        console.log('pm', pm);
        let nod = treeAkses.getNodeById(pm);
        console.log('nod', nod);
        if(nod)
          treeAkses.check(nod);
      })
    }

    // change url
    $("#formGroupAkses").attr("action", actionUpdateUrl+"/"+data.usergroup_id+"?menu_id="+currentMenuId);
    // set data
    $("#usergroup_id").val(data.usergroup_id);
    $("#usergroup_name").val(data.usergroup_name).attr('disabled', true);
    // if(data.usergroup_code == 'REKKAA' && data.usergroup_name == 'Admin') {
    //   $(".list-group-item [type=checkbox]").attr("disabled", true);
    // }

    let users = data.user;
    let emails = '';
    if(users) {
      users.forEach(dt => {
        emails += `<option value="${dt.user_email}" selected>${dt.user_email}</option>`;
      })
    }
    $("#usergroup_email").html(emails);
    
    $("#modalGrupAkses").modal("show");
  })

  $("#modalGrupAkses").on("hide.bs.modal", function(e) {
    $(".list-group-item [type=checkbox]").removeAttr("disabled");
  });

  $("#table-pengaturan-groupakses").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupAkses.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus Group Akses <b>'+ data.usergroup_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanGroupAkses.row(row).remove();
          // return true;
          return fetch(`{{route('user.page.pengaturan.groupakses.destroy', '')}}/${data.usergroup_id}?menu_id={{request()->get('menu_id')}}`, {
              method: 'DELETE',
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
              title: result.message,
              confirmButtonText: "Ok",
              type: 'error'
          })
          return false;
      }

      toastr.success(result.message);
      tblPengaturanGroupAkses.draw();
    });
  })
    // End Table Wajib Pajak

  let formGroupAkses = $("#formGroupAkses").validate({
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
        // console.log('tree', treeAkses.getCheckedNodes());
        // return false;
        $(".spinner-box").css({'display': 'table'});
        let tempPermissionIds = treeAkses.getCheckedNodes();
        tempPermissionIds = tempPermissionIds.filter(tp => {
          return !isNaN(tp);
        })
        console.log(tempPermissionIds);
        // console.log(tmp);
        // return false;
        $.ajax({
            method: form.method,
            url: form.action,
            data: $(form).serialize()+"&"+$.param({
              _token: $("meta[name=csrf-token]").attr('content'),
              permission_ids: tempPermissionIds
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
                      usergroup_name: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                $("#formGroupAkses [type=reset]").click();
                toastr.success(response.message);
                tblPengaturanGroupAkses.draw();

                // $("#usergroup_name").removeAttr('disabled');
                // change url
                $("#formGroupAkses").attr("action", actionStoreUrl);
            }
        })
    },
  })

  $("#formGroupAkses [type=reset]").click(function(e) {
    e.preventDefault();
    resetForm("#formGroupAkses");
    // change url
    $("#formGroupAnggota").attr("action", actionStoreUrl);
    $("#usergroup_name").removeAttr('disabled');
    treeAkses.uncheckAll();
    $("#modalGrupAkses").modal("hide");
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>