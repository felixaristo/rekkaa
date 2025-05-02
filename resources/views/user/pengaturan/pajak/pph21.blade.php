<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <form id="formStPajakPPh21" method="POST" action="{{route('user.page.pengaturan.pajakpph21.save', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
                <div class="row mb-3">
                  <label class="col-sm-5 lbl-req" for="stpajakpph21_npwp">NPWP Penanggung Jawab</label>
                  <div class="col-sm-7">
                    <input type="text" required name="stpajakpph21_npwp" id="stpajakpph21_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP Penanggung Jawab" value="{{($stpajakpph21) ? $stpajakpph21->stpajakpph21_npwp : ''}}">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-5 lbl-req" for="stpajakpph21_companyname">Nama Perusahaan</label>
                  <div class="col-sm-7">
                    <input type="text" required name="stpajakpph21_companyname" id="stpajakpph21_companyname" class="form-control" placeholder="Nama Perusahaan" value="{{($stpajakpph21) ? $stpajakpph21->stpajakpph21_companyname : ''}}">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-5 lbl-req" for="stpajakpph21_name">Nama</label>
                  <div class="col-sm-7">
                    <input type="text" required name="stpajakpph21_name" id="stpajakpph21_name" class="form-control" placeholder="Nama" value="{{($stpajakpph21) ? $stpajakpph21->stpajakpph21_name : ''}}">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-5 lbl-req" for="stpajakpph21_posisi">Posisi</label>
                  <div class="col-sm-7">
                    <input type="text" required name="stpajakpph21_posisi" id="stpajakpph21_posisi" class="form-control" placeholder="Posisi" value="{{($stpajakpph21) ? $stpajakpph21->stpajakpph21_posisi : ''}}">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-5 lbl-req" for="stpajakpph21_lokasi">Tempat Penandatanganan</label>
                  <div class="col-sm-7">
                    <input type="text" required name="stpajakpph21_lokasi" id="stpajakpph21_lokasi" class="form-control" placeholder="Tempat Penandatanganan" value="{{($stpajakpph21) ? $stpajakpph21->stpajakpph21_lokasi : ''}}">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-5" for="stpajakpph21_ttd">Upload Tanda Tangan</label>
                  <div class="col-sm-7">
                    <input name="stpajakpph21_ttd" class="form-control" type="file" id="stpajakpph21_ttd">
                    <span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span>
                    <br>
                    <div class="d-block rounded" id="logo-box" style="width: 100px;">
                    @if($stpajakpph21 && $stpajakpph21->stpajakpph21_ttd)
                    <img src="{{$stpajakpph21->stpajakpph21_ttd}}" alt="avatar" class="img-fluid">
                    @endif
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-12">
                    <div class="text-right">
                      <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let formStPajakPPh21 = $("#formStPajakPPh21").validate({
      errorPlacement: function(error, element) {
        // console.log(element);
        var isInputGroup = $(element).parent();
        console.log('isInputGroup', isInputGroup.length)
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
      rules: {},
      submitHandler: function(form) {
          $(".spinner-box").css({'display': 'table'});

          let formData = new FormData();
          let dataArr = $("#formStPajakPPh21").serializeArray();

          for (let i = 0; i < dataArr.length; i++) {
            formData.append(dataArr[i].name, dataArr[i].value);
          }

          formData.append('_token', $("meta[name=csrf-token]").attr('content'));
          if($('#stpajakpph21_ttd')[0].files[0])
            formData.append('stpajakpph21_ttd', $('#stpajakpph21_ttd')[0].files[0]);
          
          $.ajax({
              method: form.method,
              url: form.action,
              processData: false, // tell jQuery not to process the data            
              contentType: false, // tell jQuery not to set contentType  
              data: formData,
              error: function(error) {
                $(".spinner-box").fadeOut();
                console.log('error.responseJSON', error.responseJSON)
                if(error.responseJSON) {
                  let errs = error.responseJSON.errors;
                  let errorName = [];
                  if(errs) {
                    let errsArr = Object.keys(errs).map((key) => [key, errs[key]]);
                    // console.log('errsArr', errsArr)
                    errsArr.forEach(err => {
                      console.log(err);
                      errorName.push(err[1]);
                    });
                  } else {
                    errorName = [error.responseJSON.message];
                  }

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
                    if(Array.isArray(response.message)) {
                      formAuthentication.showErrors({
                        stpajakpph21_name: response.message
                      })
                    } else {
                        toastr.error(response.message);
                    }
                      return false;
                  }
                  
                  if(response.data.stpajakpph21_ttd)
                    $("#logo-box").html(`<img src="${response.data.stpajakpph21_ttd}" alt="avatar" class="img-fluid">`);

                  toastr.success(response.message);
              }
          })
      },
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>