<style>
  /* .table-condensed thead tr:nth-child(2),
  .table-condensed tbody {
    display: none
  } */
</style>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">{{$title}}</h5>
        <!-- <small class="text-muted float-end">Merged input group</small> -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{route('user.page.karyawan.index')}}">Karyawan</a>
            </li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ol>
        </nav>
      </div>
      <div class="card-body">
        <form action="{{route('user.page.karyawan-export-kalkulasi.export')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawanExportKalkulasi" autocomplete="off">
          <div class="form-group row mb-3">
            <label for="export_masapajak_dt" class="col-sm-2 lbl-req">Masa Pajak</label>
            <div class="col-sm-4">
              <input type="text" required name="export_masapajak_dt" id="export_masapajak_dt" class="form-control" placeholder="Masukkan Masa Pajak">
            </div>
            <label for="export_pembetulan" class="col-sm-2 lbl-req">Pembetulan</label>
            <div class="col-sm-4">
              <select style="width: 100%;" required name="export_pembetulan" id="export_pembetulan" class="form-control" placeholder="-:Pilih Data:-">
                <?php for($i=0; $i<5; $i++) : ?>
                <option value="{{$i}}">{{$i}}</option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          <div class="form-group row mb-3">
            <label for="export_jenispajak" class="col-sm-2 lbl-req">Jenis Pajak</label>
            <div class="col-sm-4">
              <div class="form-check form-check-inline">
                <input name="export_jenispajak" class="form-check-input" type="radio" value="1" id="export_jenispajak_karyawan" checked="">
                <label class="form-check-label" for="export_jenispajak_karyawan"> Karyawan </label>
              </div>
              <div class="form-check form-check-inline">
                <input name="export_jenispajak" class="form-check-input" type="radio" value="0" id="export_jenispajak_nonkaryawan">
                <label class="form-check-label" for="export_jenispajak_nonkaryawan"> Non Karyawan </label>
              </div>
            </div>
            <div class="col-sm-6 text-right">
              <button type="submit" class="btn btn-sm btn-outline-warning"><i class='bx bxs-file-export'></i> Export</button>
            </div>
          </div>
        </form>
      </div>
      <!-- <div class="table-responsive text-nowrap">
        <table class="table" id="table-kalkulasi">
          <thead class="table-light">
            <tr>
              <th>Bulan</th>
              <th>Gaji Pokok</th>
              <th>Total Tunjangan</th>
              <th>PPh 21</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
          </tbody>
        </table>
      </div> -->
    </div>
  <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script>
  $(function() {
    let masaPajak = moment().format('MM-YYYY');
    $("#export_masapajak_dt").datepicker({
      // language: "id-ID",
			format: "MM-yyyy",
      startView: "months", 
      minViewMode: "months"
		}).on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#export_masapajak_dt').datepicker("getDate");
        // console.log(moment(dt).format('MM-YYYY'));
        masaPajak = moment(dt).format('MM-YYYY');
    });
    $("#export_pembetulan").select2()

    let formKaryawanExportKalkulasi = $("#formKaryawanExportKalkulasi").validate({
			errorPlacement: function(error, element) {
				// console.log(element);
				var isInputGroup = $(element).parent();
				console.log('element', element)
				let elem = $(element);
				console.log('elem', elem)
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
        let export_masapajak = masaPajak;
				// console.log(form.method);
				// console.log(form.action);
				console.log($(form).serialize());
				$(".spinner-box").css({'display': 'table'});
				$.ajax({
					method: form.method,
					url: form.action,
					data: $(form).serialize()+"&export_masapajak="+export_masapajak+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
					error: function(error) {
						$(".spinner-box").fadeOut();
						// console.log(error.responseJSON.errors.email);
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
								icon: 'error',
								html: errorName
							})
						}
					}, 
					success: function(response) {
						console.log(response, 'response')
						$(".spinner-box").fadeOut();
            window.open(response, "_blank");
            window.close();
						// if(!response.success) {
						// 	toastr.error(response.message);
						// 	return false;
						// }
						
						// toastr.success(response.message);
            // var $a = $("<a>");
            // $a.attr("href",response.file);
            // $("body").append($a);
            // // $a.attr("download","file.xls");
            // $a[0].click();
            // $a.remove();
					}
				})
			},
		})
    // set meta title
		setHtmlTitle('{{$title}}')
  })
  
</script>