<div class="authentication-wrapper authentication-basic">
    <div class="authentication-inner">
        <!-- Register -->
        <!-- <div class="card">
            <div class="card-body"> -->
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
            <!-- <h4 class="mb-2 text-center">Selamat datang di Rekkaa!</h4> -->
                <!-- <p class="mb-4 text-center">
                    Silahkan daftar untuk menggunakan aplikasi Rekkaa.
                </p> -->
            <div id="smartwizard" dir="rtl-">
                <ul class="nav nav-progress">
                    <li class="nav-item">
                        <a class="nav-link" href="#step-1">
                            <span class="num">1</span>
                            @if(isset($wajibpajak) && $wajibpajak)
                            Entitas
                            @else
                            Registrasi
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#step-2">
                            <span class="num">2</span>
                            Add On
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="#step-3">
                            <span class="num">3</span>
                            Konfirmasi Pembayaran
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding: 0;">
                    <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                        <div class="card px-3">
                            <div class="row">
                                <div class="col-lg-8 card-body border-end">
                                @include('auth.register.register-form')
                                </div>
                                <div class="col-lg-4 card-body">
                                @include('auth.register.register-plan')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                        <div class="card px-3">
                            <div class="row">
                                <div class="col-lg-8 card-body border-end py-0">
                                @include('auth.register.register-addon')
                                </div>
                                <div class="col-lg-4 card-body">
                                @include('auth.register.register-plan')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                        <div class="card px-3">
                            <div class="row">
                                <div class="col-lg-8 card-body border-end py-0">
                                @include('auth.register.register-summary')
                                </div>
                                <div class="col-lg-4 card-body">
                                @include('auth.register.register-plan')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <!-- </div> -->
        <!-- /Register -->
    <!-- </div> -->
    <div class="text-center text-black">
            Copyright &copy; Rekkaa
        <script>
            document.write(new Date().getFullYear());
        </script>
        ,  All Rights Reserved.
    </div>
</div>
<script>
    // console.log(window.location.href);
    // var currentUrl = window.location.href;
    var subscriptionPrice = Number.parseInt("<?php echo $subscription->subscription_price ?>");
    var subscriptionDiscount = 0;
    var subscriptionDiscountType = '';
    
    $(function() {
        let userId = null;

        let dataStorage = getLocalStorage();
        let listaddon = (localStorage.getItem('listaddon')) ? JSON.parse(localStorage.getItem('listaddon')) : [];
		let discount = (localStorage.getItem('discount')) ? JSON.parse(localStorage.getItem('discount')) : null;
        // if(!localStorage.getItem('data')) {
        //     if(currentUrl.indexOf('step-2') > 0 || currentUrl.indexOf('step-3') > 0) {
        //         // currentUrl.indexOf('step-2')
        //         window.location.href = currentUrl.replace('', 'step');
        //     }
        // }
        
        let formAuthentication = $("#form-1").validate({
            errorPlacement: function(error, element) {
                // console.log(element);
                var isInputGroup = $(element).parent();
                // console.log('isInputGroup', isInputGroup.length)
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
                user_email: {
                    required: true,
                    email: true,
                },
                name: {
                    required: true,
                },
                password: {
                    required: true,
                    minlength: 8,
                },
                phone: {
                    required: true,
                    number: true,
                    rangelength: [9, 14],
                },
                nik: {
					required: true,
					number: true,
					minlength: 16,
					maxlength: 16,
				},
                npwp: {
                    required: true,
                    minlength: 20,
					maxlength: 20,
                },
                npwp_type: {
                    required: true,
                },
                company_name: {
                    required: true,
                },
                company_phone: {
                    required: true,
                    number: true,
                    rangelength: [9, 14],
                },
                company_npwp: {
                    required: true,
                },
                country_id: {
                    required: true,
                },
                city_id: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                $(".spinner-box").css({'display': 'table'});

                $.ajax({
                    method: form.method,
                    url: form.action,
                    data: $(form).serialize()+"&"+$.param({
                        _token: $("meta[name=csrf-token]").attr('content'),
                        subscription_id: "<?php echo request()->get('subscription_id') ?>"
                    }),
                    error: function(error) {
                        $(".spinner-box").hide();

                        if(error.responseJSON) {
                            if(error.responseJSON.errors == undefined) {
                                Swal.fire({
                                    html: error.responseJSON.message,
                                    icon: 'error'
                                });
                                return false;
                            }
                            
                            let errs = error.responseJSON.errors;
                            if(errs['user_email'])
                                formAuthentication.showErrors({'user_email': errs['user_email']});
                            if(errs['password'])
                                formAuthentication.showErrors({'password': errs['password']});
                            if(errs['name'])
                                formAuthentication.showErrors({'name': errs['name']});
                            if(errs['phone'])
                                formAuthentication.showErrors({'phone': errs['phone']});
                            if(errs['npwp_type'])
                                formAuthentication.showErrors({'npwp_type': errs['npwp_type']});
                            if(errs['company_name'])
                                formAuthentication.showErrors({'company_name': errs['company_name']});
                            if(errs['company_phone'])
                                formAuthentication.showErrors({'company_phone': errs['company_phone']});
                            if(errs['npwp'])
                                formAuthentication.showErrors({'npwp': errs['npwp']});
                            if(errs['nik'])
                                formAuthentication.showErrors({'nik': errs['nik']});
                            if(errs['country_id'])
                                formAuthentication.showErrors({'country_id': errs['country_id']});
                            if(errs['regency_id'])
                                formAuthentication.showErrors({'regency_id': errs['regency_id']});
                        }
                    },
                    success: function(response) {
                        // console.log(response, 'response')
                        $(".spinner-box").hide();

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
                
                        let data = {
                            email: $("#form-1 [name=user_email]").val(),
                            password: $("#form-1 [name=password]").val(),
                            name: $("#form-1 [name=name]").val(),
                            phone: $("#form-1 [name=phone]").val(),
                            npwp_type: $("#form-1 [name=npwp_type]").val(),
                            npwp: $("#form-1 [name=npwp]").val(),
                            register_kepemilikan_npwp: $("#form-1 [name=register_kepemilikan_npwp]").val(),
                            address: $("#form-1 [name=address]").val(),
                            nik: $("#form-1 [name=nik]").val(),
                            country_id: $("#form-1 [name=country_id]").val(),
                            country_name: $("#form-1 [name=country_id] :selected").text(),
                            city_id: $("#form-1 [name=city_id]").val(),
                            city_name: $("#form-1 [name=city_id] :selected").text(),
                        }

                        setLocalStorage(data);
                        $("#smartwizard").smartWizard("next");
                    }
                })
            },
        });

        let formAddon = $("#form-2").validate({
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
                        error.insertAfter($(element).parent('.input-group'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            },
            rules: {
                // compound rule
                subscription_periode: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                dataStorage = getLocalStorage();
                listaddon = JSON.parse(localStorage.getItem('listaddon'));
                discount = JSON.parse(localStorage.getItem('discount'));
                // console.log('dataStorage', dataStorage)
                if(!dataStorage) {
                    $('#smartwizard').smartWizard("goToStep", 0, true);
                    return false;
                }
                $(".spinner-box").css({'display': 'table'});
                $.ajax({
                    method: form.method,
                    url: form.action,
                    data: $(form).serialize()+"&"+$.param({
                        _token: $("meta[name=csrf-token]").attr('content'),
                        subscription_id: "<?php echo request()->get('subscription_id') ?>",
                        user_email: dataStorage.email,
                        npwp: dataStorage.npwp,
                        nik: dataStorage.nik,
                        listaddon: listaddon,
                        discount: discount,
                    }),
                    error: function(error) {
                        $(".spinner-box").hide();
                        if(error.responseJSON) {
                            let errs = error.responseJSON.errors;
                        }
                    },
                    success: function(response) {
                        console.log(response, 'response')
                        $(".spinner-box").hide();
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
                        let data = response.data;
                        
                        let subData = (data.order.userorder_subscriptiondata) ? JSON.parse(data.order.userorder_subscriptiondata) : null;
                        // set addon
                        listaddon = (subData.listaddon) ? subData.listaddon : null; 
                        localStorage.setItem('listaddon', JSON.stringify(listaddon));

                        // set discount
                        console.log('subscriptionDiscountType', subscriptionDiscountType)
                        discount = {
                            period: data.order.userorder_paymentperiode, 
                            value: (subscriptionDiscountType == 'TETAP') ? data.order.userorder_discount : data.order.userorder_discount / data.order.userorder_price * 100,
                            type: subscriptionDiscountType
                        }
                        localStorage.setItem('discount', JSON.stringify(discount));
			            
                        totalAddon();
                
                        $("#smartwizard").smartWizard("next");
                    }
                })
            },
        });

        let formPaymentConfirmation = $("#form-3").validate({
            submitHandler: function(form) {
                dataStorage = getLocalStorage();
                $(".spinner-box").css({'display': 'table'});
                $.ajax({
                    method: form.method,
                    url: form.action,
                    data: $(form).serialize()+"&"+$.param({
                        _token: $("meta[name=csrf-token]").attr('content'),
                        subscription_id: "<?php echo request()->get('subscription_id') ?>",
                        user_email: dataStorage.email,
                        npwp: dataStorage.npwp,
                        nik: dataStorage.nik,
                        listaddon: listaddon,
                        discount: discount,
                    }),
                    error: function(error) {
                        $(".spinner-box").hide();
                        if(error.responseJSON) {
                            let errs = error.responseJSON.errors;
                        }
                    },
                    success: function(response) {
                        console.log(response, 'response')
                        $(".spinner-box").hide();
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
                        
                        if(response.data.invoice_url) {
                            // clear local storage
                            localStorage.removeItem('data');
                            localStorage.removeItem('listaddon');
                            localStorage.removeItem('discount');
                            window.location.href = response.data.invoice_url;
                        } else {
                            window.location.href = `{{url('/login')}}`;
                        }
                    }
                })
            },
        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: 0,
            autoAdjustHeight: false,
            theme: 'arrows', // basic, arrows, square, round, dots
            transition: {
                animation:'fade'
            },
            toolbar: {
                showNextButton: false, // show/hide a Next button
                showPreviousButton: false, // show/hide a Previous button
                position: 'bottom', // none/ top/ both bottom
            },
            anchor: {
                enableNavigation: true, // Enable/Disable anchor navigation 
                enableNavigationAlways: false, // Activates all anchors clickable always
                enableDoneState: true, // Add done state on visited steps
                markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
                enableDoneStateNavigation: true // Enable/Disable the done state navigation
            },
            style: {
                btnCss: 'btn-sm',
                btnPrevCss: 'btn-outline-danger btn-prev mr-1',
                btnNextCss: 'btn-outline-info btn-next mr-1'
            },
            keyboard: {
                keyNavigation: false,
            }
        });

        if(!dataStorage || dataStorage.email == '') {
            $('#smartwizard').smartWizard("goToStep", 0, true);
        }
        // let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
        // if(stepInfo.currentStep == -1) {
        //     $(".btn-kembali").hide();
        // }
        $("#smartwizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
        // alert("You are on step "+stepIndex+" now");
            if(stepIndex == 0) {
                $(".btn-kembali").hide();
            } else {
                $(".btn-kembali").show();
            }
        });

        
        // Leave step event is used for validating the forms
        $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx, stepDirection) {
            // Validate only on forward movement
            dataStorage = getLocalStorage();
            // console.log('dataStorage', dataStorage)
            
            // if(!dataStorage || dataStorage.email == '') {
            //     $('#smartwizard').smartWizard("goToStep", 0, true);
            //     return false;
            // }

            if(currentStepIdx == 1) {
                setFormData('form-1');
                $("#btn-kembali").hide();
                
            } 
            if(currentStepIdx > 1) {
                $("#btn-kembali").show();
            }

            console.log('currentStepIdx',currentStepIdx)
            if(currentStepIdx >= 1) {
                listaddon = JSON.parse(localStorage.getItem('listaddon'));

                if(listaddon) {
                    let el = '';
                    listaddon.forEach(ad => {
                        el += generateElAddon(ad);
                    });
                    $("#list-group-addon-summary").html(el);
                }
            }
        });

        $(document).on("click", "#btn-lanjutkan", function(e) {
            e.preventDefault();
            let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
            // console.log(stepInfo);
            if(stepInfo.currentStep == 0) {
                $("#form-1").submit();
            } else if(stepInfo.currentStep == 1) {
                $("#form-2").submit();
            } else if(stepInfo.currentStep == 2) {
                $("#form-3").submit();
            }
        })
        $(document).on("click", "#btn-kembali", function(e) {
            e.preventDefault();
            $("#smartwizard").smartWizard("prev");
        })

		if(listaddon) {
            let elInput = '';
            let el = '';
            listaddon.forEach(ad => {
                elInput += generateInputAddon(ad);
                el += generateElAddon(ad);
            });
            $("#list-group-addon").html(elInput);
            $("#list-group-addon-summary").html(el);
        }
        if(discount) {
            $(`.form-check-input[value=${discount.period}]`).click();
        } else {
            let period = $(".form-check-input:checked").val();
            subscriptionDiscount = $(".form-check-input:checked").attr("data-value");
            subscriptionDiscountType = $(".form-check-input:checked").attr("data-vtype");
            
            discount = {
                period: period,
                value: subscriptionDiscount,
                type: subscriptionDiscountType
            };
            localStorage.setItem('discount', JSON.stringify(discount));
        }
        
        totalAddon();

        function setLocalStorage(data) {
            localStorage.setItem('data', JSON.stringify(data));
        }

        function getLocalStorage() {
            return JSON.parse(localStorage.getItem('data'));
        }

        setFormData('form-1');
        function setFormData(formId) {
            let data = getLocalStorage();
            if(data) {
                $(`#${formId} [name=user_email]`).val(data.email);
                $(`#${formId} [name=password]`).val(data.password);
                $(`#${formId} [name=name]`).val(data.name);
                $(`#${formId} [name=phone]`).val(data.phone);
                $(`#${formId} [name=npwp_type]`).val(data.npwp_type);
                $(`#${formId} [name=npwp]`).val(data.npwp);
                $(`#${formId} [name=register_kepemilikan_npwp]`).val(data.register_kepemilikan_npwp);
                $(`#${formId} [name=address]`).val(data.address);
                $(`#${formId} [name=nik]`).val(data.nik);
                $(`#${formId} [name=city_id]`).append(new Option(data.city_name, data.city_id, true, true)).trigger('change');
                $(`#${formId} [name=country_id]`).append(new Option(data.country_name, data.country_id, true, true)).trigger('change');

                let npwp = '';
                let nik = '';
                if(data.npwp_type == 'BADAN') {
                    npwp = `NPWP <span class="float-right">${data.npwp}</span>`;
                } else {
                    nik = `NIK <span class="float-right">${data.nik}</span>`;
                }
                
                $(".summary-entity").html(`${data.name}<br>
                    <span class="text-info">${data.npwp_type}</span> <br>
                    ${data.address}<br>
                    ${data.city_name}, ${data.country_name}<br>
                    <hr>
                    No. HP
                    <span class="float-right">${data.phone}</span><br>
                    ${npwp}<br>
                    ${nik}<br>`);
                $(".summary-account").html(`${data.email}<br>*****<br>`);
            }
        }
    })
</script>
