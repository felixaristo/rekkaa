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
            <form id="formKepemilikannpwp" method="POST" action="{{route('admin.page.master.kepemilikannpwp.store')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <input type="hidden" name="kepemilikannpwp_id" id="kepemilikannpwp_id">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="kepemilikannpwp_code">Kode</label>
                <div class="col-sm-4">
                  <select required name="kepemilikannpwp_code" id="kepemilikannpwp_code" class="form-control" data-placeholder="-:Pilih Data:-">
                    <option value=""></option>
                    <option value="NPWP">NPWP</option>
                    <option value="NO-NPWP">NO-NPWP</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="kepemilikannpwp_name">Nama</label>
                <div class="col-sm-4">
                  <input type="text" required name="kepemilikannpwp_name" id="kepemilikannpwp_name" class="form-control" placeholder="Masukkan Nama">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="kepemilikannpwp_value">Nilai</label>
                <div class="col-sm-4">
                  <input type="text" required name="kepemilikannpwp_value" id="kepemilikannpwp_value" class="form-control" placeholder="Masukkan Rate">
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
              <table class="table table-hover display nowrap" style="width: 100%" id="table-master-kepemilikannpwp">
                <thead class="table-light">
                  <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nilai</th>
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
    $("#kepemilikannpwp_code").select2();
    let [kepemilikannpwpValue] = AutoNumeric.multiple(["#kepemilikannpwp_value"], { 
      // currencySymbol: "Rp. ",
      // decimalCharacter: ",",
      // digitGroupSeparator: ".",
      minimumValue: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });
    // Begin Table Wajib Pajak
    let tblBpjsRate = $("#table-master-kepemilikannpwp").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('admin.page.master.kepemilikannpwp.datatable')}}",
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
        target: [0,1,2,3],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "kepemilikannpwp_code"
          },
          {
              "data": "kepemilikannpwp_name"
          },
          {
              "data": "kepemilikannpwp_value",
              "className": "text-right",
              "render": function(data, type, row) {
                return formatCurrency(data);
              }
          },
          {
            "data": "kepemilikannpwp_id",
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
            }
          },
      ],
  });

  $("#table-master-kepemilikannpwp").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    resetForm("#formKepemilikannpwp");
    // get row
    let row = $(this).closest('tr');
    let data = tblBpjsRate.row(row).data();
    // console.log('data', data)
    // change url
    $("#formKepemilikannpwp").attr("action", "{{url('/admin/master/kepemilikannpwp/update')}}"+"/"+data.kepemilikannpwp_id);
    // set data
    $("#kepemilikannpwp_id").val(data.kepemilikannpwp_id);
    $("#kepemilikannpwp_code").val(data.kepemilikannpwp_code).trigger('change').attr('disabled', true);
    $("#kepemilikannpwp_name").val(data.kepemilikannpwp_name);
    kepemilikannpwpValue.set(data.kepemilikannpwp_value);
  })

  $("#table-master-kepemilikannpwp").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblBpjsRate.row(row).data();
    Swal.fire({
      html: `Apakah anda ingin menghapus Kepemilikan NPWP <b>${data.kepemilikannpwp_code} (${data.kepemilikannpwp_value})</b>?`,
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblBpjsRate.row(row).remove();
          // return true;
          return fetch(`{{url('/admin/master/kepemilikannpwp/delete')}}/${data.kepemilikannpwp_id}`, {
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
              title: result.message,
              confirmButtonText: "Ok",
              type: 'error'
          })
          return false;
      }

      toastr.success(result.message);
      tblBpjsRate.draw();
    });
  })
    // End Table Wajib Pajak

  let formKepemilikannpwp = $("#formKepemilikannpwp").validate({
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
            data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
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
                      kepemilikannpwp_category: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                $("#formKepemilikannpwp [type=reset]").click();
                toastr.success(response.message);
                tblBpjsRate.draw();
                // change url
                $("#formKepemilikannpwp").attr("action", "{{route('admin.page.master.kepemilikannpwp.store')}}");
            }
        })
    },
  })

  $("#formKepemilikannpwp [type=reset]").click(function() {
    // change url
    $("#formKepemilikannpwp").attr("action", "{{route('admin.page.master.kepemilikannpwp.store')}}");
    $("#kepemilikannpwp_code").removeAttr('disabled')
    $("#kepemilikannpwp_code").val(null).trigger('change');
    kepemilikannpwpValue.set(0);
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>