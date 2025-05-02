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
            <form id="formProfil" method="POST" action="{{route('admin.page.profil.update')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="profil_email">Email</label>
                <div class="col-sm-4">
                  <input type="email" disabled value="{{$admin->admin_email}}" name="profil_email" id="profil_email" class="form-control" placeholder="Masukkan Email">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="profil_name">Nama</label>
                <div class="col-sm-4">
                  <input type="text" required value="{{$admin->admin_name}}" name="profil_name" id="profil_name" class="form-control" placeholder="Masukkan Nama">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 lbl-req" for="profil_hp">No. Hp</label>
                <div class="col-sm-4">
                  <input type="text" required value="{{$admin->admin_phone}}" name="profil_hp" id="profil_hp" class="form-control" placeholder="Masukkan No. HP">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6 text-right">
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
  let formProfil = $("#formProfil").validate({
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
                    formProfil.showErrors({
                        email: response.message
                    })
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