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
        $taxable = $stlembur_karyawan->stlemburkaryawan_taxable;
        $stlembur_group1value = $stlembur_group1->stlemburkaryawan_value;
        $stlembur_group2value = $stlembur_group2->stlemburkaryawan_value;
        $stlembur_group3value = $stlembur_group3->stlemburkaryawan_value;
        $stlembur_group4value = $stlembur_group4->stlemburkaryawan_value;
      ?>
    </div>
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="row">
          <div class="col-sm-6">
            <div class="card-header">
              <div class="form-check form-check-inline">
                <input name="tax" class="form-check-input" type="checkbox" id="taxable-lembur" value="1" id="tax" {{$taxable ? 'checked' : ''}}>
                <label class="form-check-label" for="taxable-lembur"> Dikenakan Pajak </label>
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
              Hari Kerja
            </div>
          </div>
        </div>
        <div class="card-body">
          <form id="formBupah1" method="POST" action="{{route('user.page.pengaturan.lembur.save')}}?menu_id={{request()->get('menu_id')}}&type=1" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row">
              <div class="col-sm-5">
                <label class="col-sm-12" for="bllabel"><u>Besaran Lembur</u></label>
                <br>
                <div class="form-check">
                  <input name="besaranlembur[]" disabled checked class="form-check-input besaranlembur" type="checkbox" value="GAJI_POKOK" id="besaranlembur_gaji_pokok1">
                  <label class="form-check-label" for="besaranlembur_gaji_pokok1">Gaji Pokok</label>
                </div>
              </div>
              <div class="col-sm-7">
                <div class="box-bupah mb-3">
                  <?php 
                  $i = 0;
                  foreach($stlembur_group1value as $gvalue) : ?>
                  <div class="box-border mb-3">
                    <div class="row mb-3">
                      <label class="col-sm-3" for="jklabel">Jam Kerja</label>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Awal</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_awal}}" name="jklabelawal[]" class="form-control jklabelawal" placeholder="Jam Awal">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Akhir</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_akhir}}" name="jklabelakhir[]" class="form-control jklabelakhir" placeholder="Jam Akhir">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-sm-3" for="bulabel">Besaran Upah</label>
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="number" min="1" disabled value="{{$gvalue->upah}}" name="bupah[]" class="form-control bupah" placeholder="Besaran Upah">
                          <span class="input-group-text">x Upah/Jam</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php $i++; endforeach; ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="row">
          <div class="col-sm-6">
            <div class="card-header">
              Hari Libur Resmi & Hari Istirahat Mingguan
            </div>
          </div>
        </div>
        <div class="card-body">
          <form id="formBupah2" method="POST" action="{{route('user.page.pengaturan.lembur.save')}}?menu_id={{request()->get('menu_id')}}&type=2" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row">
              <div class="col-sm-5">
                <label class="col-sm-12" for="bllabel"><u>Besaran Lembur</u></label>
                <br>
                <div class="form-check">
                  <input name="besaranlembur[]" disabled checked
                  class="form-check-input besaranlembur" type="checkbox" value="GAJI_POKOK" id="besaranlembur_gaji_pokok2">
                  <label class="form-check-label" for="besaranlembur_gaji_pokok2">Gaji Pokok</label>
                </div>
              </div>
              <div class="col-sm-7">
                <div class="box-bupah mb-3">
                  <?php 
                  $i = 0;
                  foreach($stlembur_group2value as $gvalue) : ?>
                  <div class="box-border mb-3">
                    <div class="row mb-3">
                      <label class="col-sm-3" for="jklabel">Jam Kerja</label>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Awal</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_awal}}" name="jklabelawal[]" class="form-control jklabelawal" placeholder="Jam Awal">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Akhir</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_akhir}}" name="jklabelakhir[]" class="form-control jklabelakhir" placeholder="Jam Akhir">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-sm-3" for="bulabel">Besaran Upah</label>
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="number" min="1" disabled value="{{$gvalue->upah}}" name="bupah[]" class="form-control bupah" placeholder="Besaran Upah">
                          <span class="input-group-text">x Upah/Jam</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php $i++; endforeach; ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="row">
          <div class="col-sm-6">
            <div class="card-header">
              Hari Libur Resmi
            </div>
          </div>
        </div>
        <div class="card-body">
          <form id="formBupah3" method="POST" action="{{route('user.page.pengaturan.lembur.save')}}?menu_id={{request()->get('menu_id')}}&type=3" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row">
              <div class="col-sm-5">
                <label class="col-sm-12" for="bllabel"><u>Besaran Lembur</u></label>
                <br>
                <div class="form-check">
                  <input name="besaranlembur[]" disabled checked
                  class="form-check-input besaranlembur" type="checkbox" value="GAJI_POKOK" id="besaranlembur_gaji_pokok3">
                  <label class="form-check-label" for="besaranlembur_gaji_pokok3">Gaji Pokok</label>
                </div>
              </div>
              <div class="col-sm-7">
                <div class="box-bupah mb-3">
                  <?php 
                  $i = 0;
                  foreach($stlembur_group2value as $gvalue) : ?>
                  <div class="box-border mb-3">
                    <div class="row mb-3">
                      <label class="col-sm-3" for="jklabel">Jam Kerja</label>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Awal</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_awal}}" name="jklabelawal[]" class="form-control jklabelawal" placeholder="Jam Awal">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Akhir</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_akhir}}" name="jklabelakhir[]" class="form-control jklabelakhir" placeholder="Jam Akhir">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-sm-3" for="bulabel">Besaran Upah</label>
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="number" min="1" disabled value="{{$gvalue->upah}}" name="bupah[]" class="form-control bupah" placeholder="Besaran Upah">
                          <span class="input-group-text">x Upah/Jam</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php $i++; endforeach; ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="row">
          <div class="col-sm-6">
            <div class="card-header">
              Hari Istirahat Mingguan
            </div>
          </div>
        </div>
        <div class="card-body">
          <form id="formBupah4" method="POST" action="{{route('user.page.pengaturan.lembur.save')}}?menu_id={{request()->get('menu_id')}}&type=4" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row">
              <div class="col-sm-5">
                <label class="col-sm-12" for="bllabel"><u>Besaran Lembur</u></label>
                <br>
                <div class="form-check">
                  <input name="besaranlembur[]" disabled checked
                  class="form-check-input besaranlembur" type="checkbox" value="GAJI_POKOK" id="besaranlembur_gaji_pokok4">
                  <label class="form-check-label" for="besaranlembur_gaji_pokok4">Gaji Pokok</label>
                </div>
              </div>
              <div class="col-sm-7">
                <div class="box-bupah mb-3">
                  <?php 
                  $i = 0;
                  foreach($stlembur_group4value as $gvalue) : ?>
                  <div class="box-border mb-3">
                    <div class="row mb-3">
                      <label class="col-sm-3" for="jklabel">Jam Kerja</label>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Awal</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_awal}}" name="jklabelawal[]" class="form-control jklabelawal" placeholder="Jam Awal">
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">Akhir</span>
                          <input type="number" min="1" disabled value="{{$gvalue->jam_akhir}}" name="jklabelakhir[]" class="form-control jklabelakhir" placeholder="Jam Akhir">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-sm-3" for="bulabel">Besaran Upah</label>
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="number" min="1" disabled value="{{$gvalue->upah}}" name="bupah[]" class="form-control bupah" placeholder="Besaran Upah">
                          <span class="input-group-text">x Upah/Jam</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php $i++; endforeach; ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
$(function() {
  let currentMenuId = "{{request()->get('menu_id')}}";
  // let [besaranlemburLainnya] = AutoNumeric.multiple(["#besaranlembur_tunjangan_lainnya1", "#besaranlembur_tunjangan_lainnya2", "#besaranlembur_tunjangan_lainnya3", "#besaranlembur_tunjangan_lainnya4"], { 
  //   currencySymbol: "Rp. ",
  //   decimalCharacter: ",",
  //   digitGroupSeparator: ".",
  //   minimumValue: "0",
  //   unformatOnSubmit: true,
  //   modifyValueOnWheel: false,
  // });
  $(".besaranlembur_lainnya").click(function(e) {
      // console.log($(this))
      let checked = $(this).is(':checked');
      if(checked) {
        $(this).closest('form').find(".besaranlembur").prop('checked', false);
        $(this).closest('form').find(".besaranlembur").attr('disabled', true)
        $(this).parent('.form-check').siblings(".besaranlembur-lainnya-box").slideDown();
      } else {
        $(this).closest('form').find(".besaranlembur").removeAttr('disabled')
        $(this).parent('.form-check').siblings(".besaranlembur-lainnya-box").slideUp();
      }
    })

  $("#taxable-lembur").click(function(e) {
    let ischecked = $(this).is(":checked");
    let val = (ischecked) ? 1 : 0;
    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      method: 'POST',
      url: "{{route('user.page.pengaturan.lembur.savetax')}}?menu_id="+currentMenuId,
      data: {_token: $("meta[name=csrf-token]").attr('content'), 'taxable': val},
      dataType: 'json',
      error: function(error) {
          $(".spinner-box").fadeOut();
          console.log(error);
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
      }
    })
  })

  $(".btn-tambah-bupah").click(function(e) {
    e.preventDefault();
    let boxbupah = $(this).parent().parent('.box-btn').prev('.box-bupah');
    console.log('boxbupah', boxbupah.html());
    boxbupah.append(`<div class="box-border mb-3 item-bupah">
                  <div class="row mb-3">
                    <label class="col-sm-3" for="jklabel">Jam Kerja</label>
                    <div class="col-sm-4">
                      <div class="input-group">
                        <span class="input-group-text">Awal</span>
                        <input type="number" min="1" required name="jklabelawal[]" class="form-control jklabelawal" placeholder="Jam Awal">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="input-group">
                        <span class="input-group-text">Akhir</span>
                        <input type="number" min="1" required name="jklabelakhir[]" class="form-control jklabelakhir" placeholder="Jam Akhir">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-3" for="bulabel">Besaran Upah</label>
                    <div class="col-sm-7">
                      <div class="input-group">
                        <input type="number" min="1" required name="bupah[]" class="form-control bupah" placeholder="Besaran Upah">
                        <span class="input-group-text">x Upah/Jam</span>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <button class="btn btn-danger btn-sm btn-delete-bupah"><i class="bx bx-trash"></i></button>
                    </div>
                  </div>
                </div>`);
  })
  $(".box-bupah").on("click", ".btn-delete-bupah", function(e) {
    e.preventDefault();
    let $this = $(this);
    Swal.fire({
        html: 'Apakah anda ingin menghapus data ini?',
        icon: 'question',
        allowOutsideClick: () => false
    }).then((result) => {
        console.log('result', result)
        if(result.isConfirmed == false) {
          return false;
        }
        $this.parents(".item-bupah").remove();
      });
  });

  $("form").submit(function(e) {
    e.preventDefault();
    let form = $(this);
    $(".spinner-box").css({'display': 'table'});
      $.ajax({
          method: form.attr('method'),
          url: form.attr('action'),
          data: form.serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
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
              
              // $("#formBpjs [type=submit]").text('Update');
          }
      })
  })
  // set meta title
  setHtmlTitle('{{$title}}')

})
</script>