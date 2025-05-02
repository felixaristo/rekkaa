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
        </div>
        <!-- / Layout page -->
        </div>
        </div>
        <!-- / Layout wrapper -->

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
        <script src="{{asset('assets/vendor/libs/BooTree/bootree.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
        <!-- <script src="{{asset('assets/vendor/libs/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script> -->
        

        <!-- Main JS -->
        <script src="{{asset('assets/js/main.js')}}"></script>

        <!-- Page JS -->
        <!-- <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script> -->

        <!-- Place this tag in your head or just before your close body tag. -->
        <!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
        <!-- Page JS -->
        <script data-cfasync="false" src="{{asset('assets/js/script.js')}}"></script>
        <script data-cfasync="false" src="{{asset('assets/js/pph21.js')}}"></script>
        <script>
            Dropzone.autoDiscover = false;

            $.extend($.fn.DataTable.defaults, {
                "scrollX": true,
                "language" : {
                    "emptyTable": "<img class='mt-2' src='{{asset('assets/img/illustrations/data-rekkaa.png')}}' style='max-width:20%; opacity: 0.6'><h4 class='mt-2'>Tidak ada data</h4>",
                }
            });

            $(function() {
            });

            // set active menu
            $(".menu-item.active").parents(".menu-item").addClass("active open");

            $(document).ajaxComplete(function(event, request, settings ) {
                // if session timeout
                // console.log('request.responseJSON', request.responseJSON)
                // console.log('request.responseJSON', typeof request.responseJSON)
                if(typeof request.responseJSON !== "undefined") {
                    if(typeof request.responseJSON.session_timeout !== "undefined" && request.responseJSON.session_timeout) {
                        Swal.fire({
                            html: '<h3>Sesi habis!</h3> <p>Silahkan login ulang untuk memulai sesi baru.</p>',
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonText: "Ok",
                            didDestroy: function() {
                                location.reload();
                            }
                        });
                    }
                }
            });

            $(document).ajaxSend(function(evt, request, settings) {
                // console.log(setting);
                gtag('set', 'page_path', settings.url);
                gtag('event', 'page_view');
            });
            window.onerror = function (err, url, line)
            {
                // console.log('err', err);
                // console.log('url', url);
                // console.log('line', line);
                gtag('event', 'exception', {
                    'description': line + " " + err,
                    'fatal': true
                });
            };
        </script>
    </body>

</html>