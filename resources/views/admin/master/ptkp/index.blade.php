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
            <form id="formPTKP" method="POST" action="{{route('admin.page.master.ptkp.store')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <input type="hidden" name="ptkp_id" id="ptkp_id">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="ptkp_marriage_status">Status Pernikahan</label>
                <div class="col-sm-4">
                  <select required name="ptkp_marriage_status" id="ptkp_marriage_status" class="form-control" data-placeholder="-:Pilih Data:-">
                    <option value=""></option>
                    <option value="Tidak Kawin (TK)">Tidak Kawin (TK)</option>
                    <option value="Kawin (K)">Kawin (K)</option>
                    <option value="Kawin dengan penghasilan istri digabung (K/I)">Kawin dengan penghasilan istri digabung (K/I)</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="ptkp_description">Deskripsi</label>
                <div class="col-sm-4">
                  <input type="text" required name="ptkp_description" id="ptkp_description" class="form-control" placeholder="Masukkan Deskripsi">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="ptkp_rate">Rate</label>
                <div class="col-sm-4">
                  <input type="text" required name="ptkp_rate" id="ptkp_rate" class="form-control" placeholder="Masukkan Rate">
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
              <table class="table table-hover display nowrap" style="width: 100%" id="table-master-ptkp">
                <thead class="table-light">
                  <tr>
                    <th>Status Pernikahan</th>
                    <th>Deskripsi</th>
                    <th>Rate</th>
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
    $("#ptkp_marriage_status").select2();
    let [ptkpRate] = AutoNumeric.multiple(["#ptkp_rate"], { 
      // currencySymbol: "Rp. ",
      // decimalCharacter: ",",
      // digitGroupSeparator: ".",
      minimumValue: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });
    // Begin Table Wajib Pajak
    let tblPTKP = $("#table-master-ptkp").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('admin.page.master.ptkp.datatable')}}",
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
        target: [0,1,3],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "ptkp_marriage_status"
          },
          {
              "data": "ptkp_description"
          },
          {
              "data": "ptkp_rate",
              "className": "text-right",
              "render": function(data, type, row) {
                return formatCurrency(data);
              }
          },
          {
            "data": "ptkp_id",
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

  $("#table-master-ptkp").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    resetForm("#formPTKP");
    // get row
    let row = $(this).closest('tr');
    let data = tblPTKP.row(row).data();
    // console.log('data', data)
    // change url
    $("#formPTKP").attr("action", "{{url('/admin/master/ptkp/update')}}"+"/"+data.ptkp_id);
    // set data
    $("#ptkp_id").val(data.ptkp_id);
    $("#ptkp_marriage_status").val(data.ptkp_marriage_status)
      .trigger('change')
      .attr('disabled', true);
    $("#ptkp_code").val(data.ptkp_code).attr('disabled', true);
    $("#ptkp_description").val(data.ptkp_description);
    $("#ptkp_calculationtype").val(data.ptkp_calculationtype).trigger('change');
    ptkpRate.set(data.ptkp_rate);
  })

  $("#table-master-ptkp").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPTKP.row(row).data();
    Swal.fire({
      html: `Apakah anda ingin menghapus PTKP <b>${data.ptkp_description} (${data.ptkp_rate})</b>?`,
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPTKP.row(row).remove();
          // return true;
          return fetch(`{{url('/admin/master/ptkp/delete')}}/${data.ptkp_id}`, {
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
      tblPTKP.draw();
    });
  })
    // End Table Wajib Pajak

  let formPTKP = $("#formPTKP").validate({
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
                      ptkp_marriage_status: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                $("#formPTKP [type=reset]").click();
                toastr.success(response.message);
                tblPTKP.draw();
                // change url
                $("#formPTKP").attr("action", "{{route('admin.page.master.ptkp.store')}}");
            }
        })
    },
  })

  $("#formPTKP [type=reset]").click(function() {
    // change url
    $("#formPTKP").attr("action", "{{route('admin.page.master.ptkp.store')}}");
    $("#ptkp_marriage_status").removeAttr('disabled')
    $("#ptkp_marriage_status").val(null).trigger('change');
    ptkpRate.set(0);
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>