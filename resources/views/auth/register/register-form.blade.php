<?php 
    $requiredordisabled = 'required';
    $email = '';
    $password = '';
    $name = '';
    $phone = '';
    $npwp = '';
    $address = '';
    $nik = '';
    $country_id = '';
    $country_name = '';
    $regency_id = '';
    $regency_name = '';
    $subscription_entity_type = '';
    $doaction = route('doregister');
    if(isset($user_continue)) {
        $email = $user_continue->wajibpajak->user->user_email;
        $name = $user_continue->wajibpajak->wajibpajak_name;
        $phone = $user_continue->wajibpajak->wajibpajak_phone;
        $npwp = $user_continue->wajibpajak->wajibpajak_npwp;
        $address = $user_continue->wajibpajak->wajibpajak_address;
        $nik = $user_continue->wajibpajak->wajibpajak_nik;
        if($user_continue->wajibpajak->country) {
            $country_id =  $user_continue->wajibpajak->country->country_id;
            $country_name = $user_continue->wajibpajak->country->country_name;
        }
        if($user_continue->wajibpajak->regency) {
            $regency_id =  $user_continue->wajibpajak->regency->regency_id;
            $regency_name = $user_continue->wajibpajak->regency->regency_name;
        }
        $subscription_entity_type = $subscription->subscription_entity_type;
    }
    if(isset($wajibpajak)) {
        $doaction = route('user.page.subscription.doextend');
        if(request()->segment(3) == 'upgrade') {
            $doaction = route('user.page.subscription.doupgrade');
        }
        $requiredordisabled = 'disabled';
        $email = $wajibpajak->user->user_email;
        $name = $wajibpajak->wajibpajak_name;
        $phone = $wajibpajak->wajibpajak_phone;
        $npwp = $wajibpajak->wajibpajak_npwp;
        $address = $wajibpajak->wajibpajak_address;
        $nik = $wajibpajak->wajibpajak_nik;
        if($wajibpajak->country) {
            $country_id =  $wajibpajak->country->country_id;
            $country_name = $wajibpajak->country->country_name;
        }
        if($wajibpajak->regency) {
            $regency_id =  $wajibpajak->regency->regency_id;
            $regency_name = $wajibpajak->regency->regency_name;
        }
        $subscription_entity_type = $wajibpajak->wajibpajaksubscription->subscription->subscription_entity_type;
    }
?>
<form id="form-1" class="mb-3 form-lbl-dot row" action="{{$doaction}}" method="POST" autocomplete="off">
    <div class="mb-3 col-sm-6">
        <label for="email" class="form-label lbl-req nodot-label">Email</label>
        <input
            type="text"
            class="form-control"
            id="email"
            name="user_email"
            placeholder="Masukkan email anda"
            value="{{$email}}"
            autofocus
            {{$requiredordisabled}}
        />
    </div>
    <div class="mb-3 col-sm-6 form-password-toggle">
        <label class="form-label lbl-req nodot-label" for="password">Password</label>
        <div class="input-group input-group-merge">
            <input
                type="password"
                id="password"
                class="form-control"
                name="password"
                placeholder="******"
                value="{{$password}}"
                aria-describedby="password"
                {{$requiredordisabled}}
            />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
    </div>
    
    <div class="mb-3 col-sm-6">
        <label for="name" class="form-label lbl-req nodot-label">Nama</label>
        <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            placeholder="Nama"
            value="{{$name}}"
            {{$requiredordisabled}}
        />
    </div>
    
    <div class="mb-3 col-sm-6">
        <label for="no_hp" class="form-label lbl-req nodot-label">No. HP</label>
        <input
            type="text"
            class="form-control"
            id="no_hp"
            name="phone"
            placeholder="No. HP (085xxx)"
            value="{{$phone}}"
            {{$requiredordisabled}}
        />
    </div>
    <div class="mb-3 col-sm-6">
        <label for="npwp_type" class="form-label lbl-req nodot-label">Jenis Usaha</label>
        <select name="npwp_type" disabled style="width: 100%;" id="npwp_type" class="form-control" data-placeholder="-:Pilih Data:-">
            @if($subscription_entity_type)
            <option value="{{$subscription_entity_type}}" selected>{{$subscription_entity_type}}</option>
            @else
            <option value="BADAN" {{($subscription->subscription_entity_type == 'BADAN' ? 'selected' : '')}}>BADAN</option>
            <option value="INDIVIDU" {{($subscription->subscription_entity_type == 'INDIVIDU' ? 'selected' : '')}}>INDIVIDU</option>
            @endif
        </select>
    </div>
    <div class="mb-3 col-sm-6">
        <label for="npwp" class="form-label lbl-req nodot-label">NPWP</label>
        <input
            type="text"
            class="form-control npwp-input"
            id="npwp"
            name="npwp"
            placeholder="NPWP"
            value="{{$npwp}}"
            {{$requiredordisabled}}
        />
        <div class="form-check form-check-inline register_kepemilikan_npwp_box" style="display: none;">
            <input name="register_kepemilikan_npwp" disabled class="form-check-input" type="checkbox" value="t" id="register_kepemilikan_npwp">
            <label class="form-check-label" for="register_kepemilikan_npwp">Tidak Memiliki NPWP</label>
        </div>
    </div>
    <div class="mb-3 col-sm-6">
        <label for="country_id" class="form-label lbl-req nodot-label">Negara</label>
        <select name="country_id" {{$requiredordisabled}} style="width: 100%;" id="country_id" class="form-control" data-placeholder="-:Pilih Data:-">
            @if($country_id)
            <option value="{{$country_id}}">{{$country_name}}</option>
            @else
            <option value="100" selected>INDONESIA</option>
            @endif
        </select>
    </div>
    <div class="mb-3 col-sm-6 register_individu_field" style="display: none;">
        
        <label for="nik" class="form-label nodot-label lbl-req">NIK</label>
        <input
            type="text"
            class="form-control nik-input"
            id="nik"
            name="nik"
            placeholder="NIK"
            value="{{$nik}}"
            {{$requiredordisabled}}
        />
    </div>
    <div class="mb-3 col-sm-6">
        <label for="city_id" class="form-label lbl-req nodot-label">Kota</label>
        <select name="city_id" {{$requiredordisabled}} style="width: 100%;" id="city_id" class="form-control" data-placeholder="-:Pilih Data:-">
            @if($regency_id)
            <option value="{{$regency_id}}" selected>{{$regency_name}}</option>
            @endif
        </select>
    </div>
    <div class="mb-3 col-sm-6">
        <label for="address" class="form-label lbl-req nodot-label">Alamat</label>
        <textarea
            type="text"
            class="form-control"
            id="address"
            name="address"
            placeholder="Alamat"
            rows="3"
            {{$requiredordisabled}}
        >{{$address}}</textarea>
    </div>
    
    <!-- <div class="mb-3">
        <button class="btn btn-warning d-grid w-100" type="submit">Register</button>
    </div> -->
</form>
<script>
    $(function() {
        let select2Param = {};
        let SubscriptionEntityType = "<?php echo $subscription->subscription_entity_type ?>"
        if(SubscriptionEntityType == 'INDIVIDU') {
            $(".register_kepemilikan_npwp_box, .register_individu_field").slideDown();
            $(".register_individu_field .nik-input").attr('required', true);
            $("#register_kepemilikan_npwp").removeAttr('disabled');
        } else {
            if($("#register_kepemilikan_npwp").is(':checked') == true) {
                $("#register_kepemilikan_npwp").click();
            }
            // $("#register_kepemilikan_npwp").attr('disabled', true);
            $(".register_kepemilikan_npwp_box, .register_individu_field").slideUp();

            $(".register_individu_field .nik-input").removeAttr('required');
        }
        $("#npwp_type").select2(select2Param)
        .on('select2:select', function(e) {
            // let data = e.params.data;
            // console.log('data', data);
            
        });

        let select2ParamCity = {
            delay: 500,
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
                $("#city_id").select2({tags:true})
            }
        })

        $("#city_id").select2(select2ParamCity);

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
    })
</script>