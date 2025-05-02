@include('karyawan.auth.includes.header')

    <div class="container">
        <div class="col-sm-4 offset-sm-4">
            <div class="authentication-wrapper authentication-basic container-p-y pt-10">
                <div class="authentication-inner">
                    <!-- Register -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center">
                                <a href="{{url('/karyawan/login')}}" class="app-brand-link gap-2">
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

                            <form id="formAuthentication" class="mb-3 form-lbl-dot" action="#" method="POST" autocomplete="off">
                                <div class="mb-3">
                                    <label for="email" class="form-label lbl-req nodot-label">Email</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        autofocus
                                    />
                                </div>
                                <div class="mb-3 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label lbl-req nodot-label" for="password">Password</label>
                                        <a href="{{url('/karyawan/lupa-password')}}">
                                        <small>Lupa Password?</small>
                                        </a>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input
                                            type="password"
                                            id="password"
                                            class="form-control"
                                            name="password"
                                            placeholder="******"
                                            aria-describedby="password"
                                        />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-warning d-grid w-100" type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <!-- /Register -->
                </div>
                <div class="text-center text-black">
                        Copyright &copy; Rekkaa
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    ,  All Rights Reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Entity -->
    <div class="modal fade" id="modalEntity" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalCenterTitle">Pilih Perusahaan</h5>
            <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
            ></button>
        </div>
        <div class="modal-body">
            <div class="col-sm-12">
                <div class="row mb-3">
                    <div class="list-group list-entity">
                        
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>

    <script>
        $(function() {
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
                    email: {
                        required: true,
                        email: true,
                    },
                    password: {
                        required: true,
                    },
                },
                submitHandler: function(form) {
                    $(".spinner-box").css({'display': 'table'});
                    $.ajax({
                        method: form.method,
                        url: form.action,
                        data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
                        error: function(error) {
                            $(".spinner-box").hide();
                            // console.log(error.responseJSON.errors.email);
                            if(error.responseJSON) {
                                if(error.responseJSON.errors == undefined) {
                                    Swal.fire({
                                        html: error.responseJSON.message,
                                        icon: 'error'
                                    });
                                    return false;
                                }
                                // formAuthentication.showErrors(error.responseJSON.errors.email[0])
                                Swal.fire({
                                    html: error.responseJSON.errors.email,
                                    icon: 'error'
                                })
                            }
                        }, 
                        success: function(response) {
                            $(".spinner-box").hide();
                            console.log(response.data, 'response')
                            if(!response.success) {
                                formAuthentication.showErrors({
                                    email: response.message
                                })
                                
                                return false;
                            }
                               
                            let karyawans = response.data.karyawan;
                            if(karyawans.length > 1) { // multiple account
                                let html = '';
                                karyawans.forEach(kr => {
                                    html += `<div class="list-group-item list-group-item-action" style="cursor:pointer;" data-id="${kr.karyawan_id}">
                                        <div class="">
                                            <h6>${kr.wajibpajak_name}</h6>
                                            <span>${kr.wajibpajak_npwp}</span>
                                        </div>
                                        </div>`;
                                })

                                $("#modalEntity .list-entity").html(html);
                                $("#modalEntity").modal("show");
                            } else { // single aacount
                                let page = "{{url('/karyawan/beranda')}}";
                                
                                window.location.href = page
                            }
                        }
                    })
                },
            })

            $("#modalEntity").on("click", ".list-group-item-action", function(e) {
                e.preventDefault();
                let id = $(this).attr("data-id");
                
                if(id) {
                    $("#modalEntity").modal("hide");
                    $(".spinner-box").css({'display': 'table'});

                    $.ajax({
                        method: 'POST',
                        url: "{{route('karyawan.getaccess')}}",
                        data: $.param({_token: $("meta[name=csrf-token]").attr('content'), id: id}),
                        error: function(error) {
                            $(".spinner-box").hide();
                            if(error.responseJSON) {
                                Swal.fire({
                                    html: error.responseJSON.message,
                                    icon: 'error'
                                })
                            }
                        }, 
                        success: function(response) {
                            $(".spinner-box").hide();
                            console.log(response.data, 'response')
                            if(!response.success) {
                                Swal.fire({
                                    html: response.message,
                                    icon: 'error'
                                })
                                
                                return false;
                            }
                           
                            let page = "{{url('/karyawan/beranda')}}";
                            
                            window.location.href = page
                        }
                    })
                }
            })
        })
    </script>
@include('karyawan.auth.includes.footer')
