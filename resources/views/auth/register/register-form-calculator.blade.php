<?php use Illuminate\Support\Facades\Request;  ?>


<div class="{{(Request::segment(2) == 'free') ? 'col-sm-6' : ''}}">
<div class="authentication-wrapper authentication-basic {{(request()->get('type') != 'page') ? '' : 'col-sm-6'}}">
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
            <h4 class="mb-2 text-center">Selamat datang di Rekkaa!</h4>
                <p class="mb-4 text-center">
                    Silahkan daftar untuk menggunakan aplikasi Rekkaa.
                </p>

            <form id="formAuthentication" class="mb-3 form-lbl-dot" action="{{route('doregistercalculator')}}" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="form-label lbl-req nodot-label">Email</label>
                    <input
                        type="text"
                        class="form-control"
                        id="email"
                        name="user_email"
                        placeholder="Masukkan email anda"
                        autofocus
                        required
                    />
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label lbl-req nodot-label" for="password">Password</label>
                    </div>
                    <div class="input-group input-group-merge">
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            name="password"
                            placeholder="******"
                            aria-describedby="password"
                            required
                        />
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="no_hp" class="form-label lbl-req nodot-label">No. HP</label>
                        <input
                            type="text"
                            class="form-control"
                            id="no_hp"
                            name="phone"
                            placeholder="No. HP"
                            required
                        />
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="npwp_type" class="form-label lbl-req nodot-label">Jenis Usaha</label>
                        <select name="npwp_type" required style="width: 100%;" id="npwp_type" class="form-control" data-placeholder="-:Pilih Data:-">
                            <option value="BADAN" selected>BADAN</option>
                            <option value="INDIVIDU">INDIVIDU</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row register_individu_field" style="display: none;">
                    <div class="col-sm-12">
                        <label for="nik" class="form-label nodot-label lbl-req">NIK</label>
                        <input
                            type="text"
                            class="form-control nik-input"
                            id="nik"
                            name="nik"
                            placeholder="NIK"
                        />
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="npwp" class="form-label lbl-req nodot-label">NPWP</label>
                        <input
                            type="text"
                            class="form-control npwp-input"
                            id="npwp"
                            name="npwp"
                            placeholder="NPWP"
                            required
                        />
                        <div class="form-check form-check-inline register_kepemilikan_npwp_box" style="display: none;">
                            <input name="register_kepemilikan_npwp" disabled class="form-check-input" type="checkbox" value="t" id="register_kepemilikan_npwp">
                            <label class="form-check-label" for="register_kepemilikan_npwp">Tidak Memiliki NPWP</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="name" class="form-label lbl-req nodot-label">Nama</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            placeholder="Nama"
                            required
                        />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="country_id" class="form-label lbl-req nodot-label">Negara</label>
                    <select name="country_id" required style="width: 100%;" id="country_id" class="form-control" data-placeholder="-:Pilih Data:-">
                        <option value="100" selected>INDONESIA</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="city_id" class="form-label lbl-req nodot-label">Kota</label>
                    <select name="city_id" required style="width: 100%;" id="city_id" class="form-control" data-placeholder="-:Pilih Data:-">
                    </select>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label lbl-req nodot-label">Alamat</label>
                    <textarea
                        type="text"
                        class="form-control"
                        id="address"
                        name="address"
                        placeholder="Alamat"
                        rows="3"
                        required
                    ></textarea>
                </div>
                <!-- <div class="mb-3 row">
                    <div class="col-sm-6">
                        <label for="npwp_type" class="form-label lbl-req nodot-label">Tipe NPWP</label>
                        <select name="npwp_type" required style="width: 100%;" id="npwp_type" class="form-control" data-placeholder="-:Pilih Data:-">
                            <option value="BADAN" selected>BADAN</option>
                            <option value="INDIVIDU" disabled>INDIVIDU</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="npwp" class="form-label lbl-req nodot-label">NPWP Perusahaan</label>
                        <input
                            type="text"
                            class="form-control npwp-input"
                            id="npwp"
                            name="npwp"
                            placeholder="NPWP Perusahaan"
                            required
                        />
                    </div>
                </div> -->
                <!-- <div class="mb-3 row">
                    <div class="col-sm-6">
                        <label for="company_phone" class="form-label lbl-req nodot-label">Telepon Perusahaan</label>
                        <input
                            type="text"
                            class="form-control"
                            id="company_phone"
                            name="company_phone"
                            placeholder="Telepon Perusahaan"
                            required
                        />
                    </div>

                </div> -->
                <div class="mb-3">
                    <button class="btn btn-warning d-grid w-100" type="submit">Register</button>
                </div>
            </form>

            <p class="text-center">
                <span>Sudah Punya Akun?</span>
                <a href="{{url('/login')}}">
                    <span> Login Sekarang</span>
                </a>
            </p>
            </div>
        <!-- </div> -->
        <!-- /Register -->
    <!-- </div> -->
    @if(request()->get('type') != 'page')
    <div class="text-center text-black">
            Copyright &copy; Rekkaa
        <script>
            document.write(new Date().getFullYear());
        </script>
        ,  All Rights Reserved.
    </div>
    @endif
</div>
</div>
<script>
    $(function() {
        let select2Param = {};
        if(findGetParameter('page-type')) {
            select2Param = {
                dropdownParent: $(".modal .modal-body"),
            };

        }
        $(".register_kepemilikan_npwp_box, .register_individu_field").hide();
        $("#npwp_type").select2(select2Param)
        .on('select2:select', function(e) {
            let data = e.params.data;
            console.log('data', data);
            if(data.id == 'INDIVIDU') {
                $(".register_kepemilikan_npwp_box, .register_individu_field").show();
                $(".register_individu_field .nik-input").attr('required', true);
                $("#register_kepemilikan_npwp").removeAttr('disabled');
            } else {
                if($("#register_kepemilikan_npwp").is(':checked') == true) {
                    $("#register_kepemilikan_npwp").click();
                }
                // $("#register_kepemilikan_npwp").attr('disabled', true);
                $(".register_kepemilikan_npwp_box, .register_individu_field").hide();

                $(".register_individu_field .nik-input").removeAttr('required');
            }
        });

        $("#register_kepemilikan_npwp").click(function(e) {
            // e.preventDefault
            let isChecked = $(this).is(':checked');
            console.log('isChecked', isChecked);
            if(isChecked) {
                $("#npwp").val("");
                $("#npwp").attr('disabled', true);
            } else {
                $("#npwp").removeAttr('disabled');
            }
        })

        let select2ParamCity = {
            delay: 500,
            dropdownParent: $("#formAuthentication"),
            ajax: {
                url: "{{route('master.kota.select')}}",
                data: function (params) {
                    var query = {
                        country_id: $("#country_id").val(),
                        q: params.term,
                        type: 'public'
                    }

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    // console.log('data.data', data.data)
                    let items = data.data;
                    items.map((item, idx) => {
                        item.id = item.regency_id;
                        item.text = item.regency_name;
                        item.data = {
                            regency_id: item.regency_id,
                            regency_name: item.regency_name,
                        };
                        // console.log('item.kode', item)
                        return item
                    })
                    return {
                        results: items
                    };
                },
            },
        };

        $("#country_id").select2({
            dropdownParent: $("#formAuthentication"),
            delay: 500,
            ajax: {
                url: "{{route('master.negara.select')}}",
                data: function (params) {
                    var query = {
                        q: params.term,
                        type: 'public'
                    }

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    // console.log('data.data', data.data)
                    let items = data.data;
                    items.map((item, idx) => {
                        item.id = item.country_id;
                        item.text = item.country_name;
                        item.data = {
                            country_id: item.country_id,
                            country_name: item.country_name,
                        };
                        // console.log('item.kode', item)
                        return item
                    })
                    return {
                        results: items
                    };
                },
            },
        }).on("select2:select", function(e) {
            let data = e.params.data;
            // $("#city_id").val(null).trigger('change');
            $("#city_id").html('');

            $("#city_id").select2('destroy');
            if(data.id == 100) { // Indonesia
                $("#city_id").select2(select2ParamCity);
            } else {
                $("#city_id").select2({tags:true, dropdownParent: $("#formAuthentication"),})
            }
        })

        $("#city_id").select2(select2ParamCity);

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
                phone: {
                    required: true,
                    number: true,
                    rangelength: [9, 14],
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
            },
            submitHandler: function(form) {
                console.log(form.method);
                console.log(form.action);
                console.log($(form).serialize());
                $(".spinner-box").css({'display': 'table'});
                $.ajax({
                    method: form.method,
                    url: form.action,
                    data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
                    error: function(error) {
                        $(".spinner-box").hide();
                        // console.log(error.responseJSON.errors.email);
                        if(error.responseJSON) {
                            let errs = error.responseJSON.errors;
                            // for (let key of Object.keys(errs)) {
                            //     console.log(key + " -> " + errs[key])
                            //     if(key == 'password')
                            //         formAuthentication.showErrors({'password': errs[key]});

                            //     if(key == 'name')
                            //         formAuthentication.showErrors({'name': errs[key]});
                            // }
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

                        toastr.success(response.message);

                        let pageType = findGetParameter('page-type');
                        let pageParams = findGetParameter('params');
                        pageParams = '';
                        let redirectUrl = "{{url('/user/beranda')}}";
                        if(pageType == 'kalkulator-pph21-karyawan' && pageParams) {
                            redirectUrl = "{{url('/kalkulator/pph-21?menu_id=26&params=')}}"+pageParams;
                        } else if(pageType == 'kalkulator-pph21-nonkaryawan' && pageParams) {
                            redirectUrl = "{{url('/kalkulator/pph-21/non-karyawan?menu_id=27&params=')}}"+pageParams;
                        } else if(pageType == 'kalkulator-pph4a2' && pageParams) {
                            redirectUrl = "{{url('/kalkulator/pph-pasal-4-ayat-2?menu_id=&params=')}}"+pageParams;
                        }
                        // console.log('pageType', pageType);
                        // console.log('pageParams', pageParams);
                        // console.log('redirectUrl', redirectUrl);
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 1000);
                    }
                })
            },
        })
    })
</script>
