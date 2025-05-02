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
			.bg-footer-theme {
                background-color: transparent!important;
                color: #000;
            }
            .fs-2rem {
                font-size: 2rem!important;
            }
            body {
              background-image: url("{{asset('assets/img/illustrations/tax-rekkaa.jpeg')}}");
              background-repeat: no-repeat;
              background-position: right bottom;
              background-color: #fff;
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
		
        <div class="layout-wrapper layout-content-navbar layout-without-menu">
            <div class="layout-container">
                <!-- Layout container -->
                <div class="layout-page">
                    <!-- Navbar -->
                    <nav
						class="layout-navbar container-xxl navbar navbar-expand-xl align-items-center bg-navbar-theme"
						id="layout-navbar"
						style="border-bottom: 1px solid #ddd; box-shadow: 0 2px 6px 0 rgb(67 89 113 / 12%)">
							<!-- <div
							class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none"
							>
							<a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
								<i class="bx bx-menu bx-sm"></i>
							</a>
							</div> -->
							<button class="navbar-toggler-main-menu" type="button" data-toggle="collapse" data-target="#navbar-collapsemain-menu" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

    					<div
							class="navbar-nav-right d-flex align-items-center"
							id="navbar-collapse">
							<!-- Logo -->
							<div class="navbar-nav align-items-center">
								<div class="nav-item d-flex align-items-center">
									<a href="/">
										<img
											src="{{asset('assets/img/logo/logo.png')}}"
											alt=""
											class="w-px-150 h-auto"/>
									</a>
								</div>
							</div>
							<!-- /Logo -->
							<ul class="navbar-nav flex-row align-items-center ms-auto">
								<!-- Place this tag where you want the button to render. -->
								<li class="nav-item lh-1 me-3">
									<span></span>
								</li>

								<!-- PPH Calculator -->
								<li class="nav-item lh-1 me-3 navbar-dropdown dropdown-user dropdown">
									<a
										class="nav-link dropdown-toggle hide-arrow visible-desktop"
										href="javascript:void(0);"
										data-bs-toggle="dropdown">
										<!-- <div class="avatar avatar-online">
										<img
											src="@/assets/img/avatars/1.png"
											alt=""
											class="w-px-40 h-auto rounded-circle"
										/>
										</div> -->
										PPh Kalkulator
										<i class="bx bx-chevron-down"></i>
									</a>
									<ul class="dropdown-menu dropdown-menu-end" id="navbar-collapsemain-menu">
										<li>
											<div class="dropdown-item" href="#">
												<div class="d-flex">
													<!-- <div class="flex-shrink-0 me-3">
														<div class="avatar">
														<img
															src="@/assets/img/avatars/1.png"
															alt=""
															class="w-px-40 h-auto rounded-circle"
														/>
														</div>
													</div> -->
													<div class="flex-grow-1">
														<span class="fw-semibold d-block">Rekkaa</span>
														<small class="text-muted">PPh Kalkulator</small>
													</div>
												</div>
											</div>
										</li>
										<li>
											<div class="dropdown-divider"></div>
										</li>
										<li>
											<a href="/kalkulator/pph-21" class="dropdown-item {{request()->segment(2) == 'pph-21' && request()->segment(3) == null ? 'active' : ''}}">
												<i class="bx bxs-calculator me-1"></i>
												<span class="align-middle">PPh 21 Karyawan</span>
											</a>
										</li>
										<li>
											<a href="/kalkulator/pph-21/non-karyawan" class="dropdown-item {{request()->segment(2) == 'pph-21' && request()->segment(3) == 'non-karyawan' ? 'active' : ''}}">
												<i class="bx bxs-calculator me-1"></i>
												<span class="align-middle">PPh 21 Non Karyawan</span>
											</a>
										</li>
										<li>
											<a href="/kalkulator/pph-pasal-4-ayat-2" class="dropdown-item {{request()->segment(2) == 'pph-pasal-4-ayat-2' ? 'active' : ''}}">
												<i class="bx bxs-calendar me-1"></i>
												<span class="align-middle">PPh Pasal 4 Ayat 2</span>
											</a>
										</li>
										<!-- <li>
										<a class="dropdown-item" href="#">
											<span class="d-flex align-items-center align-middle">
											<i class="flex-shrink-0 bx bx-credit-card me-2"></i>
											<span class="flex-grow-1 align-middle">Billing</span>
											<span
												class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20"
												>4</span
											>
											</span>
										</a>
										</li>
										<li>
										<div class="dropdown-divider"></div>
										</li>
										<li>
										<a class="dropdown-item" href="auth-login-basic.html">
											<i class="bx bx-power-off me-2"></i>
											<span class="align-middle">Log Out</span>
										</a>
										</li> -->
									</ul>
								</li>
								<!--/ PPH Calculator -->
								<!-- Login -->
								<li class="nav-item lh-1 me-3">
									<a href="/login" class="btn btn-warning">
										<i class="bx bx-log-in-circle"></i> Login
									</a>
								</li>
								<!-- / Login -->
							</ul>
						</div>
					</nav>
                    <!-- / Navbar -->