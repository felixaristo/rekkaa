        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl py-2">
                <div class="mb-2 mb-md-0 text-center text-bold">
                    Copyright &copy; Rekkaa
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
                <div class="spinner-border spinner-border-lg text-primary" role="status">
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
        <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
        
        <!-- Main JS -->
        <script src="{{asset('assets/js/main.js')}}"></script>

        <!-- Page JS -->
        <script data-cfasync="false" src="{{asset('assets/js/script.js')}}"></script>
        <script data-cfasync="false" src="{{asset('assets/js/pph21.js')}}"></script>
        <!-- <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script> -->

        <!-- Place this tag in your head or just before your close body tag. -->
        <!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
        <script>
            Dropzone.autoDiscover = false;

            $(function() {
                $(".navbar-toggler-main-menu").click(function(e) {
                    e.preventDefault();
                    let target = $(this).attr('data-target');
                    $(target).toggleClass('show');
                })

                $("#share-button .btn-link").click(function(e) {
                    e.preventDefault();
                    let type = $(this).attr("data-type");
                    let params = $("#backDropCetakModal").attr("data-params");
                    let url = $("#backDropCetakModal iframe").attr("src");
                    if(type === 'copy') {
                    // Copy the text inside the text field
                        navigator.clipboard.writeText(url);
                        // Alert the copied text
                        toastr.success("Link berhasil disimpan di clipboard.");
                    }
                    else {
                        toastr.warning("Silahkan login untuk menggunakan layanan ini.");
                    }
                })
            })

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