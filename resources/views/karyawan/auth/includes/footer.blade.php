    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl py-2">
            <div class="mb-2 mb-md-0 text-center text-bold">
                <!-- Copyright &copy; Rekkaa
                <script>
                    document.write(new Date().getFullYear());
                </script>
                ,  All Rights Reserved. -->
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
    <script src="{{asset('assets/vendor/libs/jquery-validation-master/jquery.validate.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/autoNumeric-4.6.0/autoNumeric.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/jquery-smartwizard/js/jquery.smartWizard.min.js')}}"></script>
    <!-- Main JS -->
    <script src="{{asset('assets/js/main.js')}}"></script>
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script data-cfasync="false" src="{{asset('assets/js/script.js')}}"></script>
    <script>
        $(function() {
            let documentHeight = $(document).height();
            $("body").height(documentHeight);
            // $("#rekkaa-page-content").height(documentHeight1--)
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
