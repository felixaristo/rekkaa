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
        // dd($stlembur_group1value);
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
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th class="text-center">Jenis Lembur</th>
                <th class="text-center">Jam Kerja Lembur</th>
                <th class="text-center">Besaran Upah Lembur</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                  $i = 0;
                  foreach($stlembur_group1value as $gvalue) : ?>
              <tr>
                @if($i == 0)
                <td class="text-center" rowspan="{{count($stlembur_group1value)}}">Hari Kerja</td>
                @endif
                <td class="text-center">{{$gvalue->jam_awal == 0 ? $gvalue->jam_akhir .' jam pertama' : 'Jam ke-' . $gvalue->jam_akhir}}</td>
                <td class="text-center">{{$gvalue->upah}} x Upah/Jam</td>
              </tr>
              <?php $i++; endforeach; ?>

              <?php 
                  $i = 0;
                  foreach($stlembur_group2value as $gvalue) : ?>
              <tr>
                @if($i == 0)
                <td class="text-center" rowspan="{{count($stlembur_group2value)}}">Hari Libur Resmi & Hari Istirahat Mingguan</td>
                @endif
                <td class="text-center">{{$gvalue->jam_awal == 0 ? $gvalue->jam_akhir .' jam pertama' : 'Jam ke-' . $gvalue->jam_akhir}}</td>
                <td class="text-center">{{$gvalue->upah}} x Upah/Jam</td>
              </tr>
              <?php $i++; endforeach; ?>

              <?php 
                  $i = 0;
                  foreach($stlembur_group3value as $gvalue) : ?>
              <tr>
                @if($i == 0)
                <td class="text-center" rowspan="{{count($stlembur_group3value)}}">Hari Libur Resmi</td>
                @endif
                <td class="text-center">{{$gvalue->jam_awal == 0 ? $gvalue->jam_akhir .' jam pertama' : 'Jam ke-' . $gvalue->jam_akhir}}</td>
                <td class="text-center">{{$gvalue->upah}} x Upah/Jam</td>
              </tr>
              <?php $i++; endforeach; ?>

              <?php 
                  $i = 0;
                  foreach($stlembur_group4value as $gvalue) : ?>
              <tr>
                @if($i == 0)
                <td class="text-center" rowspan="{{count($stlembur_group4value)}}">Hari Istirahat Mingguan</td>
                @endif
                <td class="text-center">{{$gvalue->jam_awal == 0 ? $gvalue->jam_akhir .' jam pertama' : 'Jam ke-' . $gvalue->jam_akhir}}</td>
                <td class="text-center">{{$gvalue->upah}} x Upah/Jam</td>
              </tr>
              <?php $i++; endforeach; ?>
            </tbody>
          </table>
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