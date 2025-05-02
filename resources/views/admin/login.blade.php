<!DOCTYPE html>

<!-- =========================================================
    * Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
    ==============================================================

    * Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
    * Created by: ThemeSelection
    * License: You must have a valid license purchased in order to legally use the theme for your project.
    * Copyright ThemeSelection (https://themeselection.com)

    =========================================================
    -->
<!-- beautify ignore:start -->
    <html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path=""
    data-template="vertical-menu-template-free"
    >
    <head>
        <meta charset="utf-8" />
        <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        />

        <title>Rekkaa - Halaman Login Admin</title>

        <meta name="description" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />


        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{asset('assets/img/logo/logo.png')}}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
        />

        <!-- Icons. Uncomment required icon fonts -->
        <link rel="stylesheet" href="{{asset('assets/vendor/fonts/boxicons.css')}}" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" class="template-customizer-core-css')}}" />
        <link rel="stylesheet" href="{{asset('assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css')}}" />
        <!-- <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}" /> -->

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

        <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2-11.5.2/dist/sweetalert2.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/dtbl/datatables.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/dist/css/select2.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/daterangepicker-master/daterangepicker.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/summernote-0.8.18-dist/summernote-lite.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/dzone/dist/dropzone.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr-master/build/toastr.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/jquery-smartwizard/css/smart_wizard_all.min.css')}}" />

        <!-- Page CSS -->
        <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}" />
        <!-- Helpers -->
        <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>

        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="{{asset('assets/js/config.js')}}"></script>

        <style>
            .mr-1 {
                margin-right: 1rem;
            }
            .text-right {
                text-align: right;
            }
            .header-rangking .bx, .header-achievement .bx {
                color: goldenrod;
                font-size: 1.5rem;
            }
            .fs-2rem {
                font-size: 2rem!important;
            }
            .bg-footer-theme {
                background-color: transparent!important;
                color: #000;
            }
            /* body::after {
                background-image: url(../../assets/img/illustrations/profile-rekkaa.jpeg);
                content: "";
                display: block;
                background-size: contain;
                background-repeat: no-repeat;
                position: absolute;
                top: 0;
                bottom: 0;
                width: 100%;
                z-index: -1;
                opacity: 0.7;
                background-position: right center;
                background-color: #ff8200;
            } */
        </style>
        <script>
            (function(w) {
                if (w.$) // jQuery already loaded, we don't need this script
                    return;
                var _funcs = [];
                w.$ = function(f) { // add functions to a queue
                    _funcs.push(f);
                };
                w.defer$ = function() { // move the queue to jQuery's DOMReady
                    while (f = _funcs.shift())
                        $(f);
                };
            })(window);
        </script>
    </head>

    <body>

    <div class="container">
        <div class="col-sm-4 offset-4">
            <div class="authentication-wrapper authentication-basic container-p-y pt-10">
                <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
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
                                Silahkan login menggunakan akun admin anda.
                            </p>

                        <form id="formAuthentication" class="mb-3" action="#" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
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
                                    <label class="form-label" for="password">Password</label>
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
                    </div>
                </div>
                <!-- /Register -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl py-2">
            <div class="mb-2 mb-md-0 text-center">
                Copyright &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script>
                ,  All Rights Reserved.
            </div>
        </div>
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>

    <div class="spinner-box">
        <div class="spinner-box-children">
            <div class="spinner-border spinner-border-lg text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script>
        defer$()
    </script>
    <script src="{{asset('assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{asset('assets/vendor/js/menu.js')}}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/sweetalert2-11.5.2/dist/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/dtbl/datatables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/select2/dist/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/toastr-master/toastr.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/dzone/dist/dropzone.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/daterangepicker-master/moment.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/daterangepicker-master/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/summernote-0.8.18-dist/summernote-lite.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/jquery-validation-master/jquery.validate.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/autoNumeric-4.6.0/autoNumeric.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/jquery-smartwizard/js/jquery.smartWizard.min.js')}}"></script>

    <!-- Main JS -->
    <script src="{{asset('assets/js/main.js')}}"></script>
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script>
        Dropzone.autoDiscover = false;

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
                            console.log(error.responseJSON.errors.email);
                            if(error.responseJSON) {
                                // formAuthentication.showErrors(error.responseJSON.errors.email[0])
                                Swal.fire({
                                    html: error.responseJSON.errors.email,
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
                                    toastr.error(response.message);
                                }
                                
                                return false;
                            }
                            let page = "{{url('/admin/beranda')}}";
                            window.location.href = page
                        }
                    })
                },
            })
        })
    </script>
</body>

</html>
