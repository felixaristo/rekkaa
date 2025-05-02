<div class="row">
  <div class="col-lg-12 mb-4 order-0">

    <!-- Bootstrap Table with Header - Light -->
    <div class="card mb-3">
      <div class="row">
        <div class="col-sm-6">
          <h5 class="card-header">{{$title}}</h5>
        </div>
      </div>
      <?php
      // $checked_tapera = $sttapera_karyawan;
      $checked_ditanggung_tapera = 0;
      $sttapera_value = ($sttapera_karyawan) ? json_decode($sttapera_karyawan->sttaperakaryawan_value) : null;
      if($sttapera_value) {
        foreach($sttapera_value as $sttaperaval) {
          if($sttaperaval->type == 'TAPERA') {
            if($sttaperaval->name == 'DITANGGUNG' && $sttaperaval->value == 1) {
              $checked_ditanggung_tapera = 1;
            }
          }
        }
      }
      ?>
    </div>
    <form id="formTapera" method="POST" action="{{route('user.page.pengaturan.tapera.save')}}?menu_id={{request()->get('menu_id')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
      <div class="col-sm-12">
        <div class="card mb-3">
          <div class="row">
            <div class="col-sm-6">
              <div class="card-header">
                Tapera
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row mb-3 text-center">
              <label class="form-check-label col-sm-12" for="menggunakantaperalabel">Apakah perusahaan anda menggunakan Tapera ?</label>
              <div class="col-sm-12">
                <div class="form-check form-check-inline">
                  <input name="menggunakantapera" class="form-check-input" type="radio" value="1" <?php echo ($sttapera_karyawan) ? 'checked' : '' ?> id="menggunakantapera_y">
                  <label class="form-check-label" for="menggunakantapera_y">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="menggunakantapera" class="form-check-input" type="radio" value="0" <?php echo ($sttapera_karyawan) ? '' : 'checked' ?> id="menggunakantapera_t">
                  <label class="form-check-label" for="menggunakantapera_t">Tidak</label>
                </div>
              </div>
            </div>
            <div class="row mb-3 taperabox" style="<?php echo ($sttapera_karyawan) ? '' : 'display: none'; ?>">
              <div class="col-sm-6">
                <ul>
                  @foreach($tapera_rate as $rate)
                  <li>{{$rate->taperarate_rate}}% <span class="text-warning">{{$rate->taperarate_description}}.</span></li>
                  @endforeach
                </ul>
              </div>
              <div class="row mb-3" id="option-container">
                <label for="sttaperakaryawan_is_all" class="col-sm-3 lbl-req">Diberikan Kepada</label>
                <div class="col-sm-4">
                  <select class="form-select" style="width: 100%;" id="sttaperakaryawan_is_all" name="sttaperakaryawan_is_all" required data-placeholder="-:Pilih Data:-">
                    <option value=""></option>
                    <option value="1">Semua Karyawan</option>
                    <option value="0">Karyawan Tertentu</option>
                    <option value="2" selected>Karyawan Tetap</option>
                    <option value="3">Karyawan Kontrak</option>
                    <option value="4">Karyawan Percobaan</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-check">
                  <input name="menggunakantaperaditanggung" class="form-check-input" type="checkbox" value="1" <?php echo $checked_ditanggung_tapera == 1 ? 'checked' : '' ?> id="menggunakantaperaditanggung">
                  <label class="form-check-label text-success" for="menggunakantaperaditanggung">Tapera ditanggung sepenuhnya oleh Perusahaan</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-sm-12 text-right">
                <button type="submit" class="btn btn-warning btn-sm">
                  @if(!$sttapera_karyawan)
                  Simpan
                  @else
                  Perbarui
                  @endif</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    $("#sttaperakaryawan_is_all").select2();
    $("#formTapera [name=menggunakantapera]").click(function(e) {
      let val = $(this).val();
      if (val == 1) {
        $(".taperabox").slideDown();
      } else {
        $(".taperabox").slideUp();
      }
    })
    let formTapera = $("#formTapera").validate({
      errorPlacement: function(error, element) {
        // console.log(element);
        var isInputGroup = $(element).parent();
        // console.log('isInputGroup', isInputGroup.length)
        let elem = $(element);
        // console.log('element', element)
        // console.log('elem', elem)
        if (elem.hasClass("select2-hidden-accessible")) {
          // element = $("#select2-" + elem.attr("id") + "-container").parent(); 
          element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container');
          error.insertAfter(element);
        } else {
          if (isInputGroup.hasClass('input-group')) {
            // $(element).parent('.input-group').insertAfter(error)
            error.insertAfter($(element).parent('.input-group'));
          } else {
            // console.log("elem.attr('[type=checkbox]')", elem.attr('[type=checkbox]'))
            // if(elem.attr('[type=checkbox]')) {
            //   let parent = elem.parent();
            //   parent.append(error);
            // } else {
            error.insertAfter(element);
            // }
          }
        }
      },
      rules: {},
      submitHandler: function(form) {
        let menggunakantapera = $("input[name=menggunakantapera] :checked").val();

        // if (menggunakantapera == '1') {

        // }

        // console.log(form.method);
        // console.log(form.action);
        // console.log($(form).serialize());
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
            let errorName = [];
            if (error.responseJSON) {
              let errs = error.responseJSON.errors;
              let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
              // console.log('errsArr', errsArr)
              errsArr.forEach(err => {
                console.log(err);
                // err[0]
                // let keyErr = err[0];
                // console.log('keyErr', keyErr)
                errorName.push(err[1]);
              });
              Swal.fire({
                showCancelButton: false,
                confirmButtonText: "Ok",
                icon: 'error',
                html: errorName
              })
            }
          },
          success: function(response) {
            console.log(response, 'response')
            $(".spinner-box").fadeOut();
            if (!response.success) {
              toastr.error(response.message);
              return false;
            }

            toastr.success(response.message);

            $("#formTapera [type=submit]").text('Perbarui');
          }
        })
      },
    })

    // set meta title
    setHtmlTitle('{{$title}}')

  })
</script>