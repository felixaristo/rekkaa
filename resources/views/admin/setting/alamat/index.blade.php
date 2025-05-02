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
            <form id="formSettingAddress" method="POST" action="{{url('/admin/setting/alamat/update/ADDRESS')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="setting_value">Alamat</label>
                <div class="col-sm-10">
                  <textarea name="setting_value" id="setting_value" class="form-control" placeholder="Masukkan Alamat">
                    {!!$alamat->setting_value!!}
                  </textarea>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="setting_description">Deskripsi</label>
                <div class="col-sm-4">
                  <input type="text" required name="setting_description" value="{{$alamat->setting_description}}" id="setting_description" class="form-control" placeholder="Masukkan Deskripsi">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-12 text-right">
                  <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script>
  $(function() {
    $("#setting_value").summernote({
      height: 300
    });

  let formSettingAddress = $("#formSettingAddress").validate({
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
                
                toastr.success(response.message);
            }
        })
    },
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>