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
        <script src="{{asset('assets/vendor/libs/dtbl/DataTables-1.12.1/js/dataTables.select.min.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/select2/dist/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/toastr-master/toastr.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/dzone/dist/dropzone.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/daterangepicker-master/moment.min.js')}}"></script>
        <script src="{{asset('assets/vendor/libs/daterangepicker-master/moment-with-locales.js')}}"></script>
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
                moment.locale('id');

                $(document).on("click", "#share-button .btn-link", function(e) {
                    e.preventDefault();
                    let type = $(this).attr("data-type");
                    let kalkulatorType = $(this).attr("data-kalkulator");
                    let tipekalkulator = $(this).attr("data-tipekalkulator");
                    let params = $("#backDropCetakModal").attr("data-params");
                    let url = $("#backDropCetakModal iframe").attr("src");
                    if(type === 'copy') {
                        // Copy the text inside the text field
                        navigator.clipboard.writeText(url);
                        // Alert the copied text
                        toastr.success("Link berhasil disimpan di clipboard.");
                    } 
                    else if(type === 'send-email') {
                        Swal.fire({
                            html: '<h5>Masukkan alamat email</h5>',
                            input: 'email',
                            inputAttributes: {
                                autocapitalize: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            showLoaderOnConfirm: true,
                            preConfirm: (email) => {
                            if(!email) {
                                // Swal.showValidationMessage('Input wajib diisi!');
                                toastr.error("Input wajib diisi!");
                                return false;
                            }
                            // // console.log('email', email)
                            // let menuId = "{{request()->get('menu_id')}}"
                            let urlParams = new URLSearchParams(window.location.search);
                            let menuId = urlParams.get('menu_id');
                            return fetch(`{{url('/kalkulator/${kalkulatorType}/send-email?menu_id=${menuId}')}}`,{
                                    method: 'POST',
                                    dataType: 'json',
                                    body: new URLSearchParams($.param({email: email, tipekalkulator: tipekalkulator, params: params, _token: $('meta[name="csrf-token"]').attr('content')}))
                                })
                                .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                                })
                                .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                )
                                })
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            // console.log('result', result);
                            if (result.isConfirmed) {
                                if(result.value.success) {
                                    toastr.success(result.value.message);
                                } else {
                                    toastr.error(result.value.message);
                                }
                            }
                        })
                    } 
                    else {
                        toastr.warning("Mohon maaf, layanan masih dalam proses pengembangan!");
                    }
                })
            });

            let originalData = [];
            let currentSelectedWajibPajakID = null;

            // set active menu
            $(".menu-item.active").parents(".menu-item").addClass("active open");

            $(document).ajaxComplete(function(event, request, settings ) {
                // if session timeout
                // // console.log('request.responseJSON', request.responseJSON)
                // // console.log('request.responseJSON', typeof request.responseJSON)
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