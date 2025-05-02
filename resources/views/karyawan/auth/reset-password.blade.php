@include('karyawan.auth.includes.header')

    <div class="container" id="rekkaa-page-content">
        <div class="col-sm-4 offset-4">
            <div class="authentication-wrapper authentication-basic container-p-y pt-10">
                <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{route('karyawan.doreset')}}" class="app-brand-link gap-2">
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
                                @if(request()->get('reset'))
                                Silahkan masukkan sandi baru untuk melakukan reset sandi.
                                @else
                                Silahkan atur kata sandi anda.
                                @endif
                            </p>

                        <form id="formAuthentication" class="mb-3 form-lbl-dot" action="#" method="POST" autocomplete="off">
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label lbl-req nodot-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input
                                        type="password"
                                        id="password"
                                        class="form-control"
                                        name="password"
                                        placeholder="******"
                                        aria-describedby="password"
                                        required
                                    />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label lbl-req nodot-label" for="konf_password">Konfirmasi Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input
                                        type="password"
                                        id="konf_password"
                                        class="form-control"
                                        name="konf_password"
                                        placeholder="******"
                                        aria-describedby="konfirmasi password"
                                        required
                                    />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-warning d-grid w-100" type="submit">Simpan</button>
                            </div>
                        </form>
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
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    konf_password: {
                        required: true,
                        minlength: 6,
                        equalTo: "#password"
                    },
                },
                submitHandler: function(form) {
                    // console.log(form.method);
                    // console.log(form.action);
                    // console.log($(form).serialize());
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
                                if(errs['password'])
                                    formAuthentication.showErrors({'password': errs['password']});
                                if(errs['konf_password'])
                                    formAuthentication.showErrors({'konf_password': errs['konf_password']});
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
                                window.location.href = "{{url('/karyawan/login')}}";
                            }, 2000);
                        }
                    })
                },
            })
        })
    </script>
@include('karyawan.auth.includes.footer')
