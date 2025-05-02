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
        <form action="{{route('user.page.karyawan.import')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawanImportProfil" autocomplete="off" enctype="multipart/form-data">
          <div class="form-group row mb-3">
            <label for="file_import" class="col-sm-2 lbl-req">Upload Data</label>
            <div class="col-sm-4">
              <input type="file" required name="file_import" id="file_import" class="form-control">
              <span class="help-block"><a href="{{asset('assets/import/Profil_Karyawan.xlsx')}}" download="Rekkaa_Profil_Karyawan">Download Template</a></span>
            </div>
          </div>
          <div class="form-group row mb-3">
            <div class="col-sm-6 text-right">
              <button type="submit" class="btn btn-sm btn-outline-warning"><i class='bx bxs-file-import'></i> Import</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script>
  $(function() {
    let formKaryawanImportProfil = $("#formKaryawanImportProfil").validate({
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
				$(".spinner-box").css({'display': 'table'});
				let input = document.getElementById('file_import');

				let formData = new FormData();
				// return false;
				if(input.files.length < 1) {
					toastr.error('Please upload xls/csv');
					return false;
				}
				formData.append('_token', $("meta[name=csrf-token]").attr('content'));
				formData.append('file', input.files[0]);
						$.ajax({
							method: form.method,
							url: form.action,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				},
				processData: false,  // tell jQuery not to process the data
				contentType: false,  // tell jQuery not to set contentType
				data: formData,
					error: function(error) {
						$(".spinner-box").fadeOut();
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
						if(!response.success) {
							toastr.error(response.message);
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