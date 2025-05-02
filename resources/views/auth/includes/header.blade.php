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

        <title>{{$title}}</title>

        <meta name="description" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />


        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon/favicon.png')}}" />

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
        <link data-cfasync="false" rel="stylesheet" data-cfasync="false" href="{{asset('assets/css/styles.css')}}" />
        <!-- Helpers -->
        <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>

        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="{{asset('assets/js/config.js')}}"></script>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_TAG_MANAGER') }}"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', "{{ env('GOOGLE_TAG_MANAGER') }}");
        </script>
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
            /* footer.footer {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
            } */
            .bg-footer-theme {
                background-color: transparent!important;
                color: #000;
            }
            body {
                /* position: sticky; */
            }
            body::before {
                content: "";
                display: block;
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                width: 52%;
                height: inherit;
                z-index: -1;
                opacity: 0.7;
                background-color: #ff8200;
            }
            body::after {
                background-image: url(../../assets/img/illustrations/profile-rekkaa.jpeg);
                content: "";
                display: block;
                background-size: cover;
                background-repeat: no-repeat;
                position: absolute;
                top: 0;
                bottom: 0;
                right: 0;
                width: 48%;
                height: inherit;
                z-index: -1;
                opacity: 0.7;
                background-position: center;
                background-color: #ff8200;
            }
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