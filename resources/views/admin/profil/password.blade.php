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
            <form id="formPassword" method="POST" action="{{route('admin.page.password.update')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <div class="row mb-3 form-password-toggle">
                <label class="col-sm-3 lbl-req" for="old_password">Password Lama</label>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <input
                        type="password"
                        id="old_password"
                        class="form-control"
                        name="old_password"
                        placeholder="******"
                        aria-describedby="old_password"
                        required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
              </div>
              <div class="row mb-3 form-password-toggle">
                <label class="col-sm-3 lbl-req" for="new_password">Password Baru</label>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <input
                        type="password"
                        id="new_password"
                        class="form-control"
                        name="new_password"
                        placeholder="******"
                        aria-describedby="new_password"
                        required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
              </div>
              <div class="row mb-3 form-password-toggle">
                <label class="col-sm-3 lbl-req" for="confirm_password">Konfirmasi Password</label>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <input
                        type="password"
                        id="confirm_password"
                        class="form-control"
                        name="confirm_password"
                        placeholder="******"
                        aria-describedby="confirm_password"
                        required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-7 text-right">
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
  let formPassword = $("#formPassword").validate({
    errorPlacement: function(error, element) {
        // console.log(element);
        var isInputGroup = $(element).parent();
        // console.log('isInputGroup', isInputGroup.length)
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
              // console.log(error.responseJSON.errors.email);
              if(error.responseJSON) {
                let errs = error.responseJSON.errors;
                let errorName = [];
                if(errs) {
                  let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
                  console.log('errsArr', errsArr)
                  errsArr.forEach(err => {
                    console.log(err);
                    errorName.push(err[1]);
                  });
                } else {
                  errorName = [error.responseJSON.message];
                }
                Swal.fire({
                  showCancelButton: false,
                  icon: 'error',
                  html: errorName
                })
              }
            }, 
            success: function(response) {
                console.log(response, 'response')
                $(".spinner-box").fadeOut();
                if(!response.success) {
                  Swal.fire({
                    showCancelButton: false,
                    icon: 'error',
                    html: response.message
                  });
                  return false;
                }
                
                toastr.success(response.message);
                resetForm('#formPassword');
            }
        })
    },
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>