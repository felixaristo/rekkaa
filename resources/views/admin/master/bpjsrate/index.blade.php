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
            <form id="formBpjsrate" method="POST" action="{{route('admin.page.master.bpjsrate.store')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <input type="hidden" name="bpjsrate_id" id="bpjsrate_id">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="bpjsrate_category">Kategori</label>
                <div class="col-sm-4">
                  <select required name="bpjsrate_category" id="bpjsrate_category" class="form-control" data-placeholder="-:Pilih Data:-">
                    <option value=""></option>
                    <option value="TK">TK</option>
                    <option value="KES">KES</option>
                    <option value="JP">JP</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="bpjsrate_code">Kode</label>
                <div class="col-sm-4">
                  <input type="text" required name="bpjsrate_code" id="bpjsrate_code" class="form-control" placeholder="Masukkan Kode">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="bpjsrate_description">Deskripsi</label>
                <div class="col-sm-4">
                  <input type="text" required name="bpjsrate_description" id="bpjsrate_description" class="form-control" placeholder="Masukkan Deskripsi">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="bpjsrate_calculationtype">Tipe</label>
                <div class="col-sm-4">
                  <select required name="bpjsrate_calculationtype" id="bpjsrate_calculationtype" class="form-control" data-placeholder="-:Pilih Data:-">
                    <option value=""></option>
                    <option value="PENAMBAH">PENAMBAH</option>
                    <option value="PENGURANG">PENGURANG</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="bpjsrate_rate">Rate</label>
                <div class="col-sm-4">
                  <input type="text" required name="bpjsrate_rate" id="bpjsrate_rate" class="form-control" placeholder="Masukkan Rate">
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
              <table class="table table-hover display nowrap" style="width: 100%" id="table-master-bpjsrate">
                <thead class="table-light">
                  <tr>
                    <th>Kategori</th>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Tipe</th>
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
    $("#bpjsrate_category, #bpjsrate_calculationtype").select2();
    let [bpjsrateRate] = AutoNumeric.multiple(["#bpjsrate_rate"], { 
      // currencySymbol: "Rp. ",
      // decimalCharacter: ",",
      // digitGroupSeparator: ".",
      minimumValue: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });
    // Begin Table Wajib Pajak
    let tblBpjsRate = $("#table-master-bpjsrate").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('admin.page.master.bpjsrate.datatable')}}",
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
        target: [5],
        width: 30
      }, {
        target: [0,1,3,4,5],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "bpjsrate_category"
          },
          {
              "data": "bpjsrate_code"
          },
          {
              "data": "bpjsrate_description"
          },
          {
              "data": "bpjsrate_calculationtype",
          },
          {
              "data": "bpjsrate_rate",
              // "className": "text-right",
              // "render": function(data, type, row) {
              //   return formatCurrency(data);
              // }
          },
          {
            "data": "bpjsrate_id",
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

  $("#table-master-bpjsrate").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    resetForm("#formBpjsrate");
    // get row
    let row = $(this).closest('tr');
    let data = tblBpjsRate.row(row).data();
    // console.log('data', data)
    // change url
    $("#formBpjsrate").attr("action", "{{url('/admin/master/bpjsrate/update')}}"+"/"+data.bpjsrate_id);
    // set data
    $("#bpjsrate_id").val(data.bpjsrate_id);
    $("#bpjsrate_category").val(data.bpjsrate_category)
      .trigger('change')
      .attr('disabled', true);
    $("#bpjsrate_code").val(data.bpjsrate_code).attr('disabled', true);
    $("#bpjsrate_description").val(data.bpjsrate_description);
    $("#bpjsrate_calculationtype").val(data.bpjsrate_calculationtype).trigger('change');
    bpjsrateRate.set(data.bpjsrate_rate);
  })

  $("#table-master-bpjsrate").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblBpjsRate.row(row).data();
    Swal.fire({
      html: `Apakah anda ingin menghapus Rate BPJS <b>${data.bpjsrate_code} (${data.bpjsrate_rate})</b>?`,
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblBpjsRate.row(row).remove();
          // return true;
          return fetch(`{{url('/admin/master/bpjsrate/delete')}}/${data.bpjsrate_id}`, {
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

  let formBpjsrate = $("#formBpjsrate").validate({
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
                      bpjsrate_category: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                $("#formBpjsrate [type=reset]").click();
                toastr.success(response.message);
                tblBpjsRate.draw();
                // change url
                $("#formBpjsrate").attr("action", "{{route('admin.page.master.bpjsrate.store')}}");
            }
        })
    },
  })

  $("#formBpjsrate [type=reset]").click(function() {
    // change url
    $("#formBpjsrate").attr("action", "{{route('admin.page.master.bpjsrate.store')}}");
    $("#bpjsrate_category").removeAttr('disabled')
    $("#bpjsrate_code").removeAttr('disabled')
    $("#bpjsrate_category").val(null).trigger('change');
    $("#bpjsrate_calculationtype").val(null).trigger('change');
    bpjsrateRate.set(0);
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>