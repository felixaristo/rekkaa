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
      $checked_kes = [];
      $checked_lainnya_value_kes = 0;
      $checked_tk = [];
      $checked_lainnya_value_tk = 0;
      $checked_gp_kes = '';
      $checked_gp_tk = '';
      $checked_ditanggung_kes = 0;
      $checked_ditanggung_tk = 0;
      $jkk_rate_id = null;
      if($stbpjs_value) {
        foreach($stbpjs_value as $stbpjsval) {
          if($stbpjsval->type == 'KESEHATAN') {
            array_push($checked_kes, $stbpjsval->name);
            if($stbpjsval->name == 'LAINNYA') {
              $checked_lainnya_value_kes = $stbpjsval->value;
            }
            if($stbpjsval->name == 'DITANGGUNG' && $stbpjsval->value == 1) {
              $checked_ditanggung_kes = 1;
            }
          }
          if($stbpjsval->type == 'TENAGA_KERJA') {
            array_push($checked_tk, $stbpjsval->name);
            if($stbpjsval->name == 'LAINNYA') {
              $checked_lainnya_value_tk = $stbpjsval->value;
            }
            if($stbpjsval->name == 'DITANGGUNG' && $stbpjsval->value == 1) {
              $checked_ditanggung_tk = 1;
            }
            if($stbpjsval->name == 'JKK_RATE') {
              $jkk_rate_id = $stbpjsval->value;
            }
          }
        }
      }

      $rate_kesehatan = [];
      $rate_tk = [];
      foreach($bpjs_rate as $rate) {
        if($rate->bpjsrate_category == 'TK') {
          array_push($rate_tk, $rate);
        }
        if($rate->bpjsrate_category == 'KES') {
          array_push($rate_kesehatan, $rate);
        }
      }
      usort($rate_kesehatan, function($a, $b) {
        return $a['bpjsrate_calculationtype'] <=> $b['bpjsrate_calculationtype'];
      });
      usort($rate_tk, function($a, $b) {
        return $a['bpjsrate_calculationtype'] <=> $b['bpjsrate_calculationtype'];
      });
      ?>
    </div>
    <form id="formBpjs" method="POST" action="{{route('user.page.pengaturan.bpjs.save')}}?menu_id={{request()->get('menu_id')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
      <div class="col-sm-12">
        <div class="card mb-3">
          <div class="row">
            <div class="col-sm-6">
              <div class="card-header">
                BPJS Kesehatan
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row mb-3 text-center">
              <label class="form-check-label col-sm-12" for="menggunakanbpjskeslabel">Apakah perusahaan anda menggunakan BPJS Kesehatan ?</label>
              <div class="col-sm-12">
                <div class="form-check form-check-inline">
                  <input name="menggunakanbpjskes" class="form-check-input" type="radio" value="1" <?php echo ($checked_kes) ? 'checked' : '' ?> id="menggunakanbpjskes_y">
                  <label class="form-check-label" for="menggunakanbpjskes_y">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="menggunakanbpjskes" class="form-check-input" type="radio" value="0" <?php echo ($checked_kes) ? '' : 'checked' ?> id="menggunakanbpjskes_t">
                  <label class="form-check-label" for="menggunakanbpjskes_t">Tidak</label>
                </div>
              </div>
            </div>
            <div class="row mb-3 bpjskesbox" style="<?php echo ($checked_kes) ? '' : 'display: none';?>">
              <div class="col-sm-6">
                <div class="form-check">
                  <input name="kontrak_bpjs_kes[]" <?php echo (in_array('GAJI_POKOK', $checked_kes)) ? 'checked' : '' ?> 
                  <?php echo (in_array('LAINNYA', $checked_kes)) ? 'disabled' : '' ?>
                  class="form-check-input kontrak_bpjs_kes" type="checkbox" value="GAJI_POKOK" id="kontrak_bpjs_kes_gaji_pokok">
                  <label class="form-check-label" for="kontrak_bpjs_kes_gaji_pokok">Gaji Pokok</label>
                </div>
                @foreach($stgrouptunjangan_karyawan as $grouptunjangan)
                <div class="form-check">
                  <input name="kontrak_bpjs_kes[]" <?php echo (in_array($grouptunjangan->stgrouptunjangankaryawan_id, $stbpjs_group_kesehatan)) ? 'checked' : '' ?> 
                  <?php echo (in_array('LAINNYA', $checked_kes)) ? 'disabled' : '' ?>
                  class="form-check-input kontrak_bpjs_kes" type="checkbox" value="{{$grouptunjangan->stgrouptunjangankaryawan_id}}" id="kontrak_bpjs_kes_{{$grouptunjangan->stgrouptunjangankaryawan_id}}">
                  <label class="form-check-label" for="kontrak_bpjs_kes_{{$grouptunjangan->stgrouptunjangankaryawan_id}}">{{$grouptunjangan->stgrouptunjangankaryawan_name}}</label>
                </div>
                @endforeach
                <div class="form-check">
                  <input name="kontrak_bpjs_kes[]" <?php echo (in_array('LAINNYA', $checked_kes)) ? 'checked' : '' ?> class="form-check-input" type="checkbox" value="LAINNYA" id="kontrak_bpjs_kes_lainnya">
                  <label class="form-check-label" for="kontrak_bpjs_kes_lainnya">Lainnya</label>
                </div>
                <div class="col-sm-12 bpjs-kes-lainnya-box" style="<?php echo (in_array('LAINNYA', $checked_kes)) ? '' : 'display: none;' ?>">
                  <input type="text" name="kontrak_bpjs_kes_tunjangan_lainnya" id="kontrak_bpjs_kes_tunjangan_lainnya" value="{{$checked_lainnya_value_kes}}" class="form-control" placeholder="Masukkan Tunjangan Lainnya">
                </div>
              </div>
              <div class="col-sm-6">
                <ul>
                @foreach($rate_kesehatan as $rkes)
                <li>{{$rkes->bpjsrate_description}} {{$rkes->bpjsrate_rate}}% ({!!$rkes->bpjsrate_calculationtype == 'PENAMBAH' ? '<span class="text-success">Ditanggung pemberi kerja.</span>' : '<span class="text-warning">Ditanggung pekerja.</span>'!!})</li>
                @endforeach
                </ul>
              </div>
              <hr class="mb-3 mt-3">
              <div class="col-sm-12">
                <div class="form-check">
                  <input name="menggunakanbpjskesditanggung" class="form-check-input" type="checkbox" value="1" <?php echo $checked_ditanggung_kes == 1 ? 'checked' : '' ?> id="menggunakanbpjskesditanggung">
                  <label class="form-check-label text-success" for="menggunakanbpjskesditanggung">BPJS Kesehatan ditanggung sepenuhnya oleh Perusahaan</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card mb-3">
          <div class="row">
            <div class="col-sm-6">
              <div class="card-header">
                BPJS Tenaga Kerja
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row mb-3 text-center">
              <label class="form-check-label col-sm-12" for="menggunakanbpjstklabel">Apakah perusahaan anda menggunakan BPJS Tenaga Kerja ?</label>
              <div class="col-sm-12">
                <div class="form-check form-check-inline">
                  <input name="menggunakanbpjstk"<?php echo ($checked_tk) ? 'checked' : '' ?> class="form-check-input" type="radio" value="1" id="menggunakanbpjstk_y">
                  <label class="form-check-label" for="menggunakanbpjstk_y">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="menggunakanbpjstk" <?php echo ($checked_tk) ? '' : 'checked' ?> class="form-check-input" type="radio" value="0" id="menggunakanbpjstk_t">
                  <label class="form-check-label" for="menggunakanbpjstk_t">Tidak</label>
                </div>
              </div>
            </div>
            <div class="row mb-3 bpjstkbox" style="<?php echo ($checked_tk) ? '' : 'display: none';?>">
              <div class="col-sm-6">
                <div class="form-check ">
                  <input name="kontrak_bpjs_tk[]"  <?php echo (in_array('GAJI_POKOK', $checked_tk)) ? 'checked' : '' ?> 
                  <?php echo (in_array('LAINNYA', $checked_tk)) ? 'disabled' : '' ?>
                  class="form-check-input kontrak_bpjs_tk" type="checkbox" value="GAJI_POKOK" id="kontrak_bpjs_tk_gaji_pokok">
                  <label class="form-check-label" for="kontrak_bpjs_tk_gaji_pokok">Gaji Pokok</label>
                </div>
                @foreach($stgrouptunjangan_karyawan as $grouptunjangan)
                <div class="form-check ">
                  <input name="kontrak_bpjs_tk[]" <?php echo (in_array($grouptunjangan->stgrouptunjangankaryawan_id, $stbpjs_group_tk)) ? 'checked' : '' ?> 
                  <?php echo (in_array('LAINNYA', $checked_tk)) ? 'disabled' : '' ?>
                  class="form-check-input kontrak_bpjs_tk" type="checkbox" value="{{$grouptunjangan->stgrouptunjangankaryawan_id}}" id="kontrak_bpjs_tk_{{$grouptunjangan->stgrouptunjangankaryawan_id}}">
                  <label class="form-check-label" for="kontrak_bpjs_tk_{{$grouptunjangan->stgrouptunjangankaryawan_id}}">{{$grouptunjangan->stgrouptunjangankaryawan_name}}</label>
                </div>
                @endforeach
                <div class="form-check ">
                  <input name="kontrak_bpjs_tk[]" <?php echo (in_array('LAINNYA', $checked_tk)) ? 'checked' : '' ?> class="form-check-input" type="checkbox" value="LAINNYA" id="kontrak_bpjs_tk_lainnya">
                  <label class="form-check-label" for="kontrak_bpjs_tk_lainnya">Lainnya</label>
                </div>
                <div class="col-sm-12 bpjs-tk-lainnya-box" style="<?php echo (in_array('LAINNYA', $checked_tk)) ? '' : 'display: none;' ?>">
                  <input type="text" name="kontrak_bpjs_tk_tunjangan_lainnya" id="kontrak_bpjs_tk_tunjangan_lainnya" value="{{$checked_lainnya_value_tk}}" class="form-control" placeholder="Masukkan Tunjangan Lainnya">
                </div>
              </div>
              <div class="col-sm-6">
                <ul>
                @foreach($rate_tk as $rtk)
                <li>
                  {{$rtk->bpjsrate_description}} <span class="tk-rate">{{$rtk->bpjsrate_rate}}</span>% ({!!$rtk->bpjsrate_calculationtype == 'PENAMBAH' ? '<span class="text-success">Ditanggung pemberi kerja.</span>' : '<span class="text-warning">Ditanggung pekerja.</span>'!!})
                  @if($rtk->bpjsrate_code == 'JKK')
                  <a href="#" class="btn btn-sm btn-outline-info btn-edit-jkk"><i class="bx bx-edit"></i> Edit Nilai</a>
                  @endif
                </li>
                @endforeach
                </ul>
              </div>
              <hr class="mb-3 mt-3">
              <div class="col-sm-12">
                <div class="form-check">
                  <input name="menggunakanbpjstkditanggung" class="form-check-input" type="checkbox" value="1" id="menggunakanbpjstkditanggung" <?php echo $checked_ditanggung_tk == 1 ? 'checked' : '' ?>>
                  <label class="form-check-label text-success" for="menggunakanbpjstkditanggung">BPJS Ketenagakerjaan ditanggung sepenuhnya oleh Perusahaan.</label>
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
                @if(!$stbpjs_karyawan)  
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

<!-- Modal TKJKKRate -->
<div class="modal fade" id="modalTKJKKRate" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Nilai Jaminan Kecelakaan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formTKJKKRate" method="POST" action="#" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="tkjkk_rate">Nilai</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="tkjkk_rate" id="tkjkk_rate" class="form-control" data-placeholder="-:Pilih Nilai:-">
                  <option value=""></option>
                  @foreach($bpjs_rate_jkk as $ratejkk)
                  <option value="{{$ratejkk->bpjsrate_id}}" <?php echo ($jkk_rate_id == $ratejkk->bpjsrate_id) ? 'selected' : '' ?> data-rate="{{$ratejkk->bpjsrate_rate}}">{{$ratejkk->bpjsrate_label}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let jkkRateId = 1;
    let [kontrakBPJSKesTunjanganLainnya, kontrakBPJSTKTunjanganLainnya] = AutoNumeric.multiple(["#kontrak_bpjs_kes_tunjangan_lainnya", "#kontrak_bpjs_tk_tunjangan_lainnya"], { 
      currencySymbol: "Rp. ",
      decimalCharacter: ",",
      digitGroupSeparator: ".",
      minimumValue: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });

    $("#formBpjs [name=menggunakanbpjskes]").click(function(e) {
      let val = $(this).val();
      if(val == 1) {
        $(".bpjskesbox").slideDown();
      } else {
        $(".bpjskesbox").slideUp();
      }
    })

    $("#formBpjs [name=menggunakanbpjstk]").click(function(e) {
      let val = $(this).val();
      if(val == 1) {
        $(".bpjstkbox").slideDown();
      } else {
        $(".bpjstkbox").slideUp();
      }
    })

    $("#kontrak_bpjs_kes_lainnya").click(function(e) {
      // console.log($(this))
      let checked = $(this).is(':checked');
      if(checked) {
        $(".kontrak_bpjs_kes").prop('checked', false);
        $(".kontrak_bpjs_kes").attr('disabled', true);
        // $("#LAINNYA_BPJS_KES").removeAttr('disabled');
        $(".bpjs-kes-lainnya-box").slideDown();
      } else {
        $(".kontrak_bpjs_kes").removeAttr('disabled')
        $(".bpjs-kes-lainnya-box").slideUp();
      }
    })

    $("#kontrak_bpjs_tk_lainnya").click(function(e) {
      console.log($(this))
      let checked = $(this).is(':checked');
      if(checked) {
        $(".kontrak_bpjs_tk").prop('checked', false);
        $(".kontrak_bpjs_tk").attr('disabled', true);
        // $("#LAINNYA_BPJS_KES").removeAttr('disabled');
        $(".bpjs-tk-lainnya-box").slideDown();
      } else {
        $(".kontrak_bpjs_tk").removeAttr('disabled')
        $(".bpjs-tk-lainnya-box").slideUp();
      }
    })

    let formBpjs = $("#formBpjs").validate({
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
      // validasi kontrak bpjs
        let kontrak_bpjs_kes = [];
        let kontrak_bpjs_tk = [];
        $("input[name='kontrak_bpjs_kes[]']").filter(function() {
          // console.log('chk', $(this).val())
          // console.log('chkval', $(this).val())
          let val = $(this).val();
          if($(this).is(':checked') && !$(this).is(':disabled')) {
            kontrak_bpjs_kes.push(val);
          }
        }).get();
        $("input[name='kontrak_bpjs_tk[]']").filter(function() {
          // console.log('chk', $(this).val())
          // console.log('chkval', $(this).val())
          let val = $(this).val();
          if($(this).is(':checked') && !$(this).is(':disabled')) {
            kontrak_bpjs_tk.push(val);
          }
        }).get();

        let menggunakanbpjskes = $("input[name=menggunakanbpjskes] :checked").val();
        let menggunakanbpjstk = $("input[name=menggunakanbpjstk] :checked").val();
        
        if(menggunakanbpjskes == '1') {
          if(kontrak_bpjs_kes.length < 1) {
            Swal.fire({
              html: 'Silahkan pilih minimal 1 pengaturan BPJS Kesehatan!',
              confirmButtonText: "Ok",
              showCancelButton: false,
              icon: 'error'
            })
            return false;
          }
        }

        if(menggunakanbpjstk == '1') {
          if(kontrak_bpjs_tk.length < 1) {
            Swal.fire({
              html: 'Silahkan pilih minimal 1 pengaturan BPJS Tenaga Kerja!',
              confirmButtonText: "Ok",
              showCancelButton: false,
              icon: 'error'
            })
            return false;
          }
        }
        
        // console.log(form.method);
        // console.log(form.action);
        // console.log($(form).serialize());
        $(".spinner-box").css({'display': 'table'});
        $.ajax({
            method: form.method,
            url: form.action,
            data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content'), 'jkk_bpjsrate_id' : jkkRateId}),
            error: function(error) {
                $(".spinner-box").fadeOut();
                // console.log(error.responseJSON.errors.email);
                let errorName = [];
                if(error.responseJSON) {
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
                if(!response.success) {
                    toastr.error(response.message);
                    return false;
                }
                
                toastr.success(response.message);
                
                $("#formBpjs [type=submit]").text('Perbarui');
            }
        })
      },
  })

  // set jkk rate
  let jkkrate = 0.24;
  <?php if($jkk_rate_id) : ?>
  jkkRateId = "<?php echo $jkk_rate_id ?>";
  jkkrate = $("#tkjkk_rate :selected").attr('data-rate');
  <?php endif; ?>
  $(".btn-edit-jkk").siblings(".tk-rate").text(jkkrate);
  // edit jkk
  $("#tkjkk_rate").select2({
    dropdownParent: $("#modalTKJKKRate #formTKJKKRate"),
    templateResult: formatStateJkkRate,
    templateSelection: formatStateJkkRate
  }).on("select2:select", function(e) {
    let data = e.params.data;
    // console.log('data', data);
    let rate = $(data.element).attr('data-rate');
    // rate
    jkkRateId = data.id;
    $(".btn-edit-jkk").siblings(".tk-rate").text(rate);
    $("#modalTKJKKRate").modal("hide");
  })

  $(".btn-edit-jkk").click(function(e) {
    e.preventDefault();
    $("#modalTKJKKRate").modal("show");
  })
  // set meta title
  setHtmlTitle('{{$title}}')

  function formatStateJkkRate(state) {
    let rate = $(state.element).attr('data-rate');
    if (!state.id) {
        return state.text;
    }
    // console.log('state.text', state.text)
    $stateCustom = $(
        `<span>${state.text}<br><b> ${rate}%</b></span>`
    );
    // console.log('stateCustom', $stateCustom)

    return $stateCustom;
}
})
</script>