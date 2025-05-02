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
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">

        <!-- Page CSS -->
        <link data-cfasync="false" rel="stylesheet" href="{{asset('assets/css/styles.css')}}" />
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
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
          <div class="layout-container">
            @include('admin.includes.sidebar')
              <!-- Layout container -->
              <div class="layout-page">
                <nav
                    class="layout-navbar mb-3 container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar"
                  >
                  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                      <i class="bx bx-menu bx-sm"></i>
                    </a>
                  </div>

                  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <!-- Search -->
                    <!-- <div class="navbar-nav align-items-center">
                      <div class="nav-item d-flex align-items-center">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input
                          type="text"
                          class="form-control border-0 shadow-none"
                          placeholder="Search..."
                          aria-label="Search..."
                        />
                      </div>
                    </div> -->
                    <!-- /Search -->

                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                      <!-- User -->
                      <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <div class="avatar avatar-online">
                            <img src="{{asset('assets/img/avatars/avatar.png')}}" alt class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end menu-user">
                          <li>
                            <a class="dropdown-item" href="#">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar avatar-online">
                                    <img src="{{asset('assets/img/avatars/avatar.png')}}" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <span class="fw-semibold d-block">{{session()->get('admin_data')['admin_name']}}</span>
                                  <small class="text-muted">{{session()->get('admin_data')['admin_type']}}</small>
                                </div>
                              </div>
                            </a>
                          </li>
                          <li>
                            <div class="dropdown-divider"></div>
                          </li>
                          <li>
                            <a class="dropdown-item rekkaa-page-link" href="{{url('/admin/profil')}}">
                              <i class="bx bx-user me-2"></i>
                              <span class="align-middle">Profil</span>
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item rekkaa-page-link" href="{{url('/admin/profil/password')}}">
                              <i class="bx bx-cog me-2"></i>
                              <span class="align-middle">Password</span>
                            </a>
                          </li>
                          <div class="dropdown-divider"></div>
                          
                          <li>
                            <a class="dropdown-item" href="{{url('/admin/logout')}}">
                              <i class="bx bx-power-off me-2"></i>
                              <span class="align-middle">Keluar</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                      <!--/ User -->
                    </ul>
                  </div>
                </nav>