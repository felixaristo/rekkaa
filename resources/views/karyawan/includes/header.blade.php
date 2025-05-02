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
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/BooTree/bootree.css')}}">
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">

        <!-- Page CSS -->
        <link data-cfasync="false" rel="stylesheet" href="{{asset('assets/css/styles.css')}}" />
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
            
            /* .dropdown-menu-end[data-bs-popper] {
              right: auto;
            } */
            @media (min-width: 992px) {
              .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page, .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
                padding-left: 0px;
              }
              .layout-menu {
                display: none;
              }

              /* ul.dropdown-menu.dropdown-menu-end.menu-user.show:before {
                min-height: 50px;
                height: auto;
              } */
            }
            @media (max-width: 991px) {
              .navbar-desktop {
                display: none;
              }
              .nav-desktop-item {
                display: none;
              }
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
          <div class="layout-container" id="main-container">
            @include('karyawan.includes.sidebar')
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
                    <div class="navbar-nav align-items-center navbar-desktop">
                      <div class="nav-item d-flex align-items-center">
                        <!-- Karyawan -->
                        <a href="{{url('karyawan/beranda')}}" class="app-brand-link">
                          <span class="app-brand-logo demo">
                          <img
                            src="{{asset('assets/img/logo/logo.png')}}"
                            alt=""
                            class="w-px-100 h-auto"
                          />
                          </span>
                        </a>
                        
                      </div>
                    </div>

                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                      <li class="nav-item nav-desktop-item">
                        <a class="nav-link" href="{{url('karyawan/beranda')}}">
                          <i class="menu-icon tf-icons bx bx-home-circle"></i> Beranda
                        </a>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'kehadiran' ? 'active' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-calendar"></i> Absensi
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <!-- <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'absensi' ? 'active' : '' }}" href="{{ url('karyawan/kehadiran/absensi') }}">
                              <span class="align-middle">Rekam Absensi</span>
                            </a>
                          </li> -->
                          <li>
                            <a class="dropdown-item rekkaa-page-link  {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'kehadiran-karyawan' ? 'active' : '' }}" href="{{ url('karyawan/kehadiran/view/karyawan') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Riwayat Absensi</span>
                            </a>
                          </li>
                          @if(request()->get('karyawan')->karyawan_ismanager == 1)
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'kehadiran-manager' ? 'active' : '' }}" href="{{ url('karyawan/kehadiran/view/manager') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Daftar Absensi</span>
                            </a>
                          </li>
                          @endif
                        </ul>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'cuti' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-calendar-check"></i> Cuti
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'cuti' && request()->segment(3) == 'cuti-karyawan' ? 'active' : '' }}" href="{{ url('karyawan/cuti/view/karyawan') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Pengajuan Cuti</span>
                            </a>
                          </li>
                          @if(request()->get('karyawan')->karyawan_ismanager == 1)
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'cuti' && request()->segment(3) == 'cuti-manager' ? 'active' : '' }}" href="{{ url('karyawan/cuti/view/manager') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Daftar Cuti</span>
                            </a>
                          </li>
                          @endif
                        </ul>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'lembur' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-timer"></i> Lembur
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'lembur' && request()->segment(3) == 'lembur-karyawan' ? 'active' : '' }}" href="{{ url('karyawan/lembur/view/karyawan') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Pengajuan Lembur</span>
                            </a>
                          </li>
                          @if(request()->get('karyawan')->karyawan_ismanager == 1)
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'lembur' && request()->segment(3) == 'lembur-manager' ? 'active' : '' }}" href="{{ url('karyawan/lembur/view/manager') }}">
                              <!-- <i class="bx bx-user me-2"></i> -->
                              <span class="align-middle">Daftar Lembur</span>
                            </a>
                          </li>
                          @endif
                        </ul>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'gaji' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-dollar"></i> Gaji
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'gaji' && request()->segment(3) == 'gaji-karyawan' ? 'active' : '' }}" href="{{url('karyawan/gaji/view/karyawan')}}">
                              <span class="align-middle">Riwayat Gaji</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                      <!-- <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'pinjaman' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-credit-card"></i> Pinjaman
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'pinjaman' && request()->segment(3) == 'pinjaman-karyawan' ? 'active' : '' }}" href="{{url('karyawan/beranda')}}">
                              <span class="align-middle">Pengajuan Pinjaman</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'klaim' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-receipt"></i> Klaim
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'klaim' && request()->segment(3) == 'klaim-karyawan' ? 'active' : '' }}" href="{{url('karyawan/beranda')}}">
                              <span class="align-middle">Pengajuan Klaim</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                      <li class="nav-item nav-desktop-item navbar-dropdown  dropdown">
                        <a class="nav-link dropdown-toggle show-arrow {{ request()->segment(2) == 'gaji' ? 'active open' : '' }}" href="javascript:void(0);" data-bs-toggle="dropdown">
                          <i class="menu-icon tf-icons bx bx-dollar"></i> Gaji
                        </a>
                        <ul class="dropdown-menu dropdown-karyawan-menu dropdown-menu-end menu-karyawan">
                          <li>
                            <a class="dropdown-item rekkaa-page-link {{ request()->segment(2) == 'gaji' && request()->segment(3) == 'gaji-karyawan' ? 'active' : '' }}" href="{{url('karyawan/beranda')}}">
                              <span class="align-middle">Riwayat Gaji</span>
                            </a>
                          </li>
                        </ul>
                      </li> -->
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
                                  <span class="fw-semibold d-block">{{request()->get('karyawan')->karyawan_name}}</span>
                                  <small class="text-muted">{{request()->get('karyawan')->wajibpajak->wajibpajak_name}}</small>
                                </div>
                              </div>
                            </a>
                          </li>
                          <li>
                            <div class="dropdown-divider"></div>
                          </li>
                          <li>
                            <a class="dropdown-item rekkaa-page-link" href="{{url('/karyawan/profil')}}">
                              <i class="bx bx-user me-2"></i>
                              <span class="align-middle">Profil</span>
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item rekkaa-page-link" href="{{url('/karyawan/profil/password')}}">
                              <i class="bx bx-cog me-2"></i>
                              <span class="align-middle">Password</span>
                            </a>
                          </li>
                          <div class="dropdown-divider"></div>

                          <li>
                            <a class="dropdown-item" href="{{url('/karyawan/logout')}}">
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