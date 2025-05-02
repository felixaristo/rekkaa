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
            <form id="formSubscription" method="POST" action="{{route('admin.page.master.subscription.store')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <input type="hidden" name="subscription_id" id="subscription_id">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_title">Nama</label>
                <div class="col-sm-4">
                  <input type="text" required name="subscription_title" id="subscription_title" class="form-control" placeholder="Masukkan Nama">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_price">Harga Baru</label>
                <div class="col-sm-4">
                  <input type="text" required name="subscription_price" id="subscription_price" class="form-control" placeholder="Masukkan Harga Baru">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_priceold">Harga Lama</label>
                <div class="col-sm-4">
                  <input type="text" required name="subscription_priceold" id="subscription_priceold" class="form-control" placeholder="Masukkan Harga Baru">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_type">Tipe</label>
                <div class="col-sm-4">
                    <select required name="subscription_type" id="subscription_type" class="form-control" data-placeholder="-:Pilih Data:-">
                      <option value=""></option>
                      <option value="FREE">FREE</option>
                      <option value="BRONZE">BRONZE</option>
                      <option value="SILVER">SILVER</option>
                      <option value="GOLD">GOLD</option>
                      <option value="PLATINUM">PLATINUM</option>
                    </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_default">Rekomendasi</label>
                <div class="col-sm-4">
                  <div class="form-check form-check-inline">
                    <input name="subscription_default" class="form-check-input" type="radio" value="1" id="subscription_default_y">
                    <label class="form-check-label" for="subscription_default_y"> Ya </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input name="subscription_default" class="form-check-input" type="radio" value="0" id="subscription_default_t" checked="">
                    <label class="form-check-label" for="subscription_default_t"> Tidak </label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="subscription_order">Urutan</label>
                <div class="col-sm-4">
                  <input type="number" required name="subscription_order" id="subscription_order" class="form-control" placeholder="Masukkan Urutan">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2" for="subscription_description">Deskripsi</label>
                <div class="col-sm-10">
                  <textarea name="subscription_description" id="subscription_description" class="form-control" placeholder="Masukkan Deskripsi"></textarea>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-12 text-right">
                  <button type="reset" class="btn btn-outline-danger btn-sm">Batal</button>
                  <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
                </div>
              </div>
            </form>
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-master-subscription">
                <thead class="table-light">
                  <tr>
                    <th>Nama</th>
                    <th>Harga Baru</th>
                    <th>Harga Lama</th>
                    <th>Tipe</th>
                    <th>Rekomendasi</th>
                    <th>Urutan</th>
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
    $("#subscription_type").select2();
    $("#subscription_description").summernote({
      height: 300
    });
    let [subscriptionPrice, subscriptionPriceOld] = AutoNumeric.multiple(["#subscription_price", "#subscription_priceold"], { 
      currencySymbol: "Rp. ",
      decimalCharacter: ",",
      digitGroupSeparator: ".",
      minimumValue: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });
    // Begin Table Wajib Pajak
    let tblSubscription = $("#table-master-subscription").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('admin.page.master.subscription.datatable')}}",
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
        target: [6],
        width: 30
      }, {
        target: [0,3,4,5,6],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "subscription_title"
          },
          {
              "data": "subscription_price",
              "className": "text-right",
              "render": function(data, type, row) {
                return formatCurrency(data);
              }
          },
          {
              "data": "subscription_priceold",
              "className": "text-right",
              "render": function(data, type, row) {
                return 'Rp. '+formatCurrency(data);
              }
          },
          {
              "data": "subscription_type"
          },
          {
              "data": "subscription_default",
              "render": function(data, type, row) {
                return (data == 1) ? '<span class="badge rounded-pill bg-label-warning">Rekomendasi</span>' : '';
              }
          },
          {
              "data": "subscription_order"
          },
          {
            "data": "subscription_id",
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

  $("#table-master-subscription").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    resetForm("#formSubscription");
    // get row
    let row = $(this).closest('tr');
    let data = tblSubscription.row(row).data();
    console.log('data', data)
    // change url
    $("#formSubscription").attr("action", "{{url('/admin/master/subscription/update')}}"+"/"+data.subscription_id);
    // set data
    $("#subscription_id").val(data.subscription_id);
    $("#subscription_title").val(data.subscription_title);
    $("[name=subscription_default][value="+data.subscription_default+"]").click();
    subscriptionPrice.set(data.subscription_price);
    subscriptionPriceOld.set(data.subscription_priceold);
    
    $("#subscription_order").val(data.subscription_order);
    $("#subscription_type").val(data.subscription_type).trigger('change');
    $("#subscription_description").summernote('reset');
    $("#subscription_description").summernote('pasteHTML', data.subscription_description);
  })

  $("#table-master-subscription").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblSubscription.row(row).data();
    Swal.fire({
      html: `Apakah anda ingin menghapus Subscription <b>${data.subscription_title}</b>?`,
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblSubscription.row(row).remove();
          // return true;
          return fetch(`{{url('/admin/master/subscription/delete')}}/${data.subscription_id}`, {
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
      tblSubscription.draw();
    });
  })
    // End Table Wajib Pajak

  let formSubscription = $("#formSubscription").validate({
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
                      subscription_marriage_status: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                $("#formSubscription [type=reset]").click();
                toastr.success(response.message);
                tblSubscription.draw();
                // change url
                $("#formSubscription").attr("action", "{{route('admin.page.master.subscription.store')}}");
            }
        })
    },
  })

  $("#formSubscription [type=reset]").click(function() {
    // change url
    $("#formSubscription").attr("action", "{{route('admin.page.master.subscription.store')}}");
    subscriptionPrice.set(0);
    subscriptionPriceOld.set(0);
    $("#subscription_description").summernote('reset');
    $("[name=subscription_default][value=0]").click();
    $("#subscription_type").val(null).trigger('change');
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>