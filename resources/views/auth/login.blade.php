@include('auth.includes.header')

    <div class="container">
        <div class="col-sm-4 offset-sm-1">
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
                        <h4 class="mb-2 text-center">Selamat datang di Rekkaa!</h4>
                            <p class="mb-4 text-center">
                                Silahkan login menggunakan akun anda.
                            </p>

                        <form id="formAuthentication" class="mb-3 form-lbl-dot" action="#" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="email" class="form-label lbl-req nodot-label">Email</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your email"
                                    autofocus
                                />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label lbl-req nodot-label" for="password">Password</label>
                                    <a href="{{url('/lupa-password')}}">
                                    <small>Lupa Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input
                                        type="password"
                                        id="password"
                                        class="form-control"
                                        name="password"
                                        placeholder="******"
                                        aria-describedby="password"
                                    />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-warning d-grid w-100" type="submit">Login</button>
                            </div>
                        </form>

                        @if(env('APP_ENV') != 'productionxxx')
                        <p class="text-center">
                            <span>Belum Punya Akun?</span>
                            <a href="{{route('registerfree')}}" class="register-kalkulator">
                                <span> Daftar Sekarang</span>
                            </a>
                        </p>
                        @endif
                        </div>
                    </div>
                <!-- /Register -->
                </div>
                <div class="text-center text-black">
                        Copyright &copy; Rekkaa
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    ,  All Rights Reserved.
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
                    email: {
                        required: true,
                        email: true,
                    },
                    password: {
                        required: true,
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
                            if(error.responseJSON) {
                                Swal.fire({
                                    html: (error.responseJSON.errors != undefined) ? error.responseJSON.errors.email : error.responseJSON.message,
                                    icon: 'error'
                                })
                            }
                        }, 
                        success: function(response) {
                            $(".spinner-box").hide();
                            console.log(response, 'response')
                            if(!response.success) {
                                if(Array.isArray(response.message)) {
                                    formAuthentication.showErrors({
                                        email: response.message
                                    })
                                } else {
                                    if(response.data != undefined && response.data.status == 0) {
                                        let subscription_id = response.data.subscription_id;
                                        Swal.fire({
                                            html: response.message,
                                            showCancelButton: false,
                                            confirmButtonText: "Ok",
                                            icon: 'error',
                                            didClose: function() {
                                                window.location.href = "<?php echo route('register') ?>?subscription_id="+subscription_id;
                                            }
                                        })
                                    } else {
                                        toastr.error(response.message);
                                    }
                                }
                                
                                return false;
                            }
                            let page = "{{url('/user/beranda')}}";
                            // if(response.data.role === 'admin') {
                            //     page = "{{url('/admin/beranda')}}"
                            // }
                            // clear local storage
                            localStorage.removeItem('data');
                            localStorage.removeItem('listaddon');
                            localStorage.removeItem('discount');
                            
                            window.location.href = page
                        }
                    })
                },
            })
        })
    </script>
@include('auth.includes.footer')
