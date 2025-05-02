@include('auth.includes.header')

    <div class="container" id="rekkaa-page-content">
        <div class="col-sm-4 offset-4">
            <div class="authentication-wrapper authentication-basic container-p-y pt-10">
                <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="{{url('/')}}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                            <img
                                src="{{asset('assets/img/logo/logo.png')}}"
                                alt=""
                                class="w-px-100 h-auto"
                            />
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->
                        <p class="mb-4 text-center">
                            Silahkan masukkan email untuk melakukan reset password.
                        </p>

                    <form id="formAuthentication" class="mb-3 form-lbl-dot" action="#" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="email" class="form-label lbl-req nodot-label">Email</label>
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                name="user_email"
                                placeholder="Masukkan email anda"
                                autofocus
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-warning d-grid w-100" type="submit">Kirim</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>Sudah Punya Akun?</span>
                        <a href="{{url('/login')}}">
                            <span> Login Sekarang</span>
                        </a>
                    </p>
                    </div>
                </div>
                <!-- /Register -->
                </div>
            </div>
        </div>
    </div>

    <script>

        $(function() {
            let formAuthentication = $("#formAuthentication").validate({
                errorPlacement: function(error, element) {
                    // console.log(element);
                    var isInputGroup = $(element).parent();
                    console.log('isInputGroup', isInputGroup.length)
                    let elem = $(element);
                    if (elem.hasClass("select2-hidden-accessible")) {
                        element = $("#select2-" + elem.attr("id") + "-container").parent(); 
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
                rules: {
                    // compound rule
                    user_email: {
                        required: true,
                        email: true,
                    },
                },
                submitHandler: function(form) {
                    console.log(form.method);
                    console.log(form.action);
                    console.log($(form).serialize());
                    $(".spinner-box").css({'display': 'table'});
                    $.ajax({
                        method: form.method,
                        url: form.action,
                        data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
                        error: function(error) {
                            $(".spinner-box").hide();
                            // console.log(error.responseJSON.errors.email);
                            if(error.responseJSON) {
                                if(error.responseJSON.errors == undefined) {
                                    Swal.fire({
                                        html: error.responseJSON.message,
                                        icon: 'error'
                                    });
                                    return false;
                                }
                                let errs = error.responseJSON.errors;
                                // for (let key of Object.keys(errs)) {
                                //     console.log(key + " -> " + errs[key])
                                //     if(key == 'password') 
                                //         formAuthentication.showErrors({'password': errs[key]});

                                //     if(key == 'name') 
                                //         formAuthentication.showErrors({'name': errs[key]});
                                // }
                                if(errs['user_email'])
                                    formAuthentication.showErrors({'user_email': errs['user_email']});
                                if(errs['password'])
                                    formAuthentication.showErrors({'password': errs['password']});
                                if(errs['name'])
                                    formAuthentication.showErrors({'name': errs['name']});
                                if(errs['phone'])
                                    formAuthentication.showErrors({'phone': errs['phone']});
                            }
                        }, 
                        success: function(response) {
                            console.log(response, 'response')
                            $(".spinner-box").hide();
                            if(!response.success) {
                                if(Array.isArray(response.message)) {
                                    formAuthentication.showErrors({
                                        email: response.message
                                    })
                                } else {
                                    toastr.error(response.message);
                                }
                                return false;
                            }
                            
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = "{{url('/login')}}";
                            }, 1000);
                        }
                    })
                },
            })
        })
    </script>
@include('auth.includes.footer')