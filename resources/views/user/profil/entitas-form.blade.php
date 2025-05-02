<?php
$disabledfield = $isowner ? '' :'disabled';
?>
<form id="formEntitas" method="POST" action="{{route('user.page.pengaturan.profilentitas.save')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
  <div class="col-sm-6">
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_name">Nama</label>
      <div class="col-sm-12">
        <input type="text" required {{$disabledfield}} value="{{$wajibpajak->wajibpajak_name}}" name="wajibpajak_name" id="wajibpajak_name" class="form-control" placeholder="Masukkan Nama">
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label" for="wajibpajak_email">Email</label>
      <div class="col-sm-12">
        <input type="text" disabled value="{{$wajibpajak->wajibpajak_email}}" name="wajibpajak_email" id="wajibpajak_email" class="form-control" placeholder="Masukkan Email">
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_phone">No. Telepon</label>
      <div class="col-sm-12">
        <input type="text" required {{$disabledfield}} value="{{$wajibpajak->wajibpajak_phone}}" name="wajibpajak_phone" id="wajibpajak_phone" class="form-control" placeholder="Masukkan No. Telepon">
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_country">Negara</label>
      <div class="col-sm-12">
        <select name="wajibpajak_country" {{$disabledfield}} style="width: 100%;" id="wajibpajak_country" class="form-control" data-placeholder="-:Pilih Data:-">
            <option value="{{$wajibpajak->country->country_id}}" selected>{{$wajibpajak->country->country_name}}</option>
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_address">Alamat</label>
      <div class="col-sm-12">
        <textarea required {{$disabledfield}} name="wajibpajak_address" id="wajibpajak_address" class="form-control" placeholder="Masukkan Alamat">{{$wajibpajak->wajibpajak_address}}</textarea>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label" for="wajibpajak_type">Tipe</label>
      <div class="col-sm-12">
        <select name="wajibpajak_type" disabled style="width: 100%;" id="wajibpajak_type" class="form-control" data-placeholder="-:Pilih Data:-">
          <option value="BADAN" {{$wajibpajak->wajibpajak_type == 'BADAN' ? 'selected' : ''}}>BADAN</option>
          <option value="INDIVIDU" {{$wajibpajak->wajibpajak_type == 'INDIVIDU' ? 'selected' : ''}}>INDIVIDU</option>
        </select>
      </div>
    </div>
    <div class="row mb-3 register_individu_field">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_nik" style="display: none;">NIK</label>
      <div class="col-sm-12">
        <input type="text" {{$disabledfield}} value="{{$wajibpajak->wajibpajak_nik}}" name="wajibpajak_nik" id="wajibpajak_nik" class="form-control nik-input" placeholder="Masukkan NIK">
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_npwp">NPWP</label>
      <div class="col-sm-12">
        <input type="text" required {{$disabledfield}} value="{{$wajibpajak->wajibpajak_npwp}}" name="wajibpajak_npwp" id="wajibpajak_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
      </div>
      <div class="col-sm-12">
        <div class="form-check form-check-inline register_kepemilikan_npwp_box" style="display: none;">
          <input name="register_kepemilikan_npwp" disabled class="form-check-input" type="checkbox" value="t" id="register_kepemilikan_npwp">
          <label class="form-check-label" for="register_kepemilikan_npwp">Tidak Memiliki NPWP</label>
        </div>
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="ms_klu_id">KLU</label>
      <div class="col-sm-12">
        <select required {{$disabledfield}} style="width: 100%;" name="ms_klu_id" id="ms_klu_id" class="form-control" data-placeholder="-:Pilih KLU:-">
          @if($wajibpajak->klu)
          <option value="{{$wajibpajak->klu->klu_id}}" selected>{{$wajibpajak->klu->klu_description}} [{{$wajibpajak->klu->klu_code}}]</option>
          @endif
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_city">Kota</label>
      <div class="col-sm-12">
        <select required {{$disabledfield}} name="wajibpajak_city" style="width: 100%;" id="wajibpajak_city" class="form-control" data-placeholder="Masukkan Kota">
          @if($wajibpajak->regency)
          <option value="{{$wajibpajak->regency->regency_id}}">{{$wajibpajak->regency->regency_name}}</option>
          @endif
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_postal_code">Kode Pos</label>
      <div class="col-sm-12">
        <input required {{$disabledfield}} type="text" value="{{$wajibpajak->wajibpajak_postal_code}}" name="wajibpajak_postal_code" id="wajibpajak_postal_code" class="form-control" placeholder="Masukkan Kode Pos">
      </div>
    </div>
  </div>
  @if($isowner)
  <div class="col-sm-12 text-right">
    <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
  </div>
  @endif
</form>

<script>
  $(function() {
    $(".register_kepemilikan_npwp_box, .register_individu_field").hide();
    $("#register_kepemilikan_npwp").click(function(e) {
      // e.preventDefault
      let isChecked = $(this).is(':checked');
      console.log('isChecked', isChecked);
      if (isChecked) {
        $("#wajibpajak_npwp").val("");
        $("#wajibpajak_npwp").attr('disabled', true);
      } else {
        $("#wajibpajak_npwp").removeAttr('disabled');
      }
    })

    $("#wajibpajak_type").select2({

      delay: 500,
    }).on('select2:select', function(e) {
      let data = e.params.data;
      console.log('data', data);
      if (data.id == 'INDIVIDU') {
        $(".register_kepemilikan_npwp_box, .register_individu_field").show();
        $(".register_individu_field .nik-input").attr('required', true);
        $("#register_kepemilikan_npwp").removeAttr('disabled');
      } else {
        if ($("#register_kepemilikan_npwp").is(':checked') == true) {
          $("#register_kepemilikan_npwp").click();
        }
        $(".register_kepemilikan_npwp_box, .register_individu_field").hide();

        $(".register_individu_field .nik-input").removeAttr('required');
      }
    });

    let select2ParamCity = {
      delay: 500,
      ajax: {
        url: "{{route('master.kota.select')}}",
        data: function(params) {
          var query = {
            country_id: $("#wajibpajak_country").val(),
            q: params.term,
            type: 'public'
          }

          // Query parameters will be ?search=[term]&type=public
          return query;
        },
        processResults: function(data) {
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

    $("#wajibpajak_country").select2({
      delay: 500,
      ajax: {
        url: "{{route('master.negara.select')}}",
        data: function(params) {
          var query = {
            q: params.term,
            type: 'public'
          }

          // Query parameters will be ?search=[term]&type=public
          return query;
        },
        processResults: function(data) {
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
      $("#wajibpajak_city").html('');

      $("#wajibpajak_city").select2('destroy');
      if (data.id == 100) { // Indonesia
        $("#wajibpajak_city").select2(select2ParamCity);
      } else {
        $("#wajibpajak_city").select2({
          tags: true
        })
      }
    })

    $("#wajibpajak_city").select2(select2ParamCity);

    // $("#wajibpajak_city").select2({
    //   delay: 500,
    //   ajax: {
    //     url: "{{route('master.kota.select')}}",
    //     data: function(params) {
    //       var query = {
    //         q: params.term,
    //         type: 'public'
    //       }

    //       // Query parameters will be ?search=[term]&type=public
    //       return query;
    //     },
    //     processResults: function(data) {
    //       // Transforms the top-level key of the response object from 'items' to 'results'
    //       // console.log('data.data', data.data)
    //       let items = data.data;
    //       items.map((item, idx) => {
    //         item.id = item.regency_id;
    //         item.text = item.regency_name;
    //         item.data = {
    //           regency_id: item.regency_id,
    //           regency_name: item.regency_name,
    //         };
    //         // console.log('item.kode', item)
    //         return item
    //       })
    //       return {
    //         results: items
    //       };
    //     },
    //   },
    // });

    $("#ms_klu_id").select2({
      delay: 500,
      ajax: {
        url: "{{route('master.klu.select')}}",
        data: function(params) {
          var query = {
            q: params.term,
            type: 'public'
          }

          // Query parameters will be ?search=[term]&type=public
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          // console.log('data.data', data.data)
          let items = data.data;
          items.map((item, idx) => {
            item.id = item.klu_id;
            item.text = item.klu_description + ' (' + item.klu_code + ')';
            item.data = item;
            // console.log('item.kode', item)
            return item
          })
          return {
            results: items
          };
        },
      },
    });

    let formEntitas = $("#formEntitas").validate({
      errorPlacement: function(error, element) {
        // console.log(element);
        var isInputGroup = $(element).parent();
        // console.log('isInputGroup', isInputGroup.length)
        let elem = $(element);
        if (elem.hasClass("select2-hidden-accessible")) {
          // element = $("#select2-" + elem.attr("id") + "-container").parent(); 
          element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container');
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
        wajibpajak_postal_code: {
          number: true,
          minlength: 5,
          maxlength: 5,
        }
      },
      submitHandler: function(form) {
        $(".spinner-box").css({
          'display': 'table'
        });

        $.ajax({
          method: form.method,
          url: form.action,
          data: $(form).serialize() + "&" + $.param({
            _token: $("meta[name=csrf-token]").attr('content')
          }),
          error: function(error) {
            $(".spinner-box").fadeOut();
            // console.log(error.responseJSON.errors.email);
            if (error.responseJSON) {
              let errs = error.responseJSON.errors;
              let errorName = [];
              if (errs) {
                let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
                console.log('errsArr', errsArr)
                errsArr.forEach(err => {
                  console.log(err);
                  errorName.push(err[1]);
                });
              } else {
                errorName = [error.responseJSON.message];
              }
              Swal.fire({
                showCancelButton: false,
                icon: 'error',
                html: errorName
              })
            }
          },
          success: function(response) {
            console.log(response, 'response')
            $(".spinner-box").fadeOut();
            if (!response.success) {
              formEntitas.showErrors({
                email: response.message
              })
              return false;
            }

            toastr.success(response.message);
          }
        })
      },
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>