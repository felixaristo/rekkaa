
<!-- Modal Divisi -->
<div class="modal fade" id="modalKdivisi" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Divisi</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formKdivisi" method="POST" action="{{route('user.page.karyawan.divisi.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="karyawandivisi_id" id="karyawandivisi_id">
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="karyawandivisi_name">Nama Divisi</label>
              <div class="col-sm-7">
                <input type="text" required name="karyawandivisi_name" id="karyawandivisi_name" class="form-control" placeholder="Masukkan Nama Divisi">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-12 text-right">
                <button type="reset" class="btn btn-outline-danger btn-sm">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
              </div>
            </div>
          </form>
        </div>
        <!-- <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-kdivisi">
                <thead class="table-light">
                  <tr>
                    <th>Nama Divisi</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                </tbody>
              </table>
            </div>
          </div>
        </div> -->
      </div>
    </div>
  </div>
</div>

<script>
    $(function() {
        // Modal Karyawan Divisi
        let currentMenuId = "{{request()->get('menu_id')}}";
		let KdivisiReadyDraw = false;
		let actionStoreKdivisiUrl = "{{route('user.page.karyawan.divisi.store')}}";
		let actionUpdateKdivisiUrl = "{{route('user.page.karyawan.divisi.update', '')}}";
		let actionDeleteKdivisiUrl = "{{route('user.page.karyawan.divisi.delete', '')}}";

        // Begin Table Karyawan Divisi
		// let tblKdivisi = $("#table-kdivisi").DataTable({
		// 	// "filtering": false,
		// 	"searching": false,
		// 	"processing": true, //Feature control the processing indicator.
		// 	"serverSide": true, //Feature control DataTables' server-side processing mode.
		// 	"order": [], //Initial no order.
		// 	"searchDelay": 1050,
		// 	"preDrawCallback": function() {
		// 		return KdivisiReadyDraw
		// 	},
		// 	// Load data for the table's content from an Ajax source
		// 	"ajax": {
		// 		"url": "{{route('user.page.karyawan.divisi.datatable', '')}}?menu_id="+currentMenuId,
		// 		"type": "GET",
		// 		"data": function(data) {
		// 			//     console.log(data); // send data to server
		// 		}
		// 	},
		// 	"fnInitComplete": function() {
		// 		// this.fnAdjustColumnSizing(true);
		// 		// $(this).find(".cetak-registrasi").select2();
		// 	},
		// 	"autoWidth": true,
		// 	"columnDefs": [{
		// 		target: [1],
		// 		width: 30
		// 	}, {
		// 		target: [0, 1],
		// 		className: 'text-center'
		// 	}],
		// 	"columns": [{
		// 			"data": "karyawandivisi_name"
		// 		},
		// 		{
		// 			"data": "karyawandivisi_id",
		// 			"render": function(data, type, row) {
		// 				return `
		// 			<div class="dropdown">
		// 			<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
		// 				<i class="bx bx-dots-vertical-rounded"></i>
		// 			</button>
		// 			<div class="dropdown-menu">
		// 				<a class="dropdown-item btn-edit" href="javascript:void(0);"
		// 				><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
		// 				>
		// 				<a class="dropdown-item btn-delete" href="javascript:void(0);"
		// 				><i class="bx bx-trash me-1 text-danger"></i> Delete</a
		// 				>
		// 			</div>
		// 			</div>
		// 			`
		// 			}
		// 		},
		// 	],
		// });
        
		$("#btn-division").click(function(e) {
			e.preventDefault();
			// $("#formKdivisi [type=reset]").click();

			$("#modalKdivisi").modal("show");
			KdivisiReadyDraw = true;
			// tblKdivisi.draw();
		})

		let formKdivisi = $("#formKdivisi").validate({
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
				$(".spinner-box").css({
					'display': 'table'
				});

				$.ajax({
					method: form.method,
					url: form.action,
					data: $(form).serialize() + "&" + $.param({
						_token: $("meta[name=csrf-token]").attr('content'),
					}),
					error: function(error) {
						$(".spinner-box").fadeOut();
						console.log('error.responseJSON', error.responseJSON)
						if (error.responseJSON) {
							let errs = error.responseJSON.errors;
							let errorName = [];
							if (errs) {
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
						if (!response.success) {
							if (Array.isArray(response.message)) {
								formAuthentication.showErrors({
									karyawandivisi_name: response.message
								})
							} else {
								toastr.error(response.message);
							}
							return false;
						}

						toastr.success(response.message);
						// tblKdivisi.draw();

						$("#formKdivisi [type=reset]").click();
						$("#modalKdivisi").modal("hide");
					}
				})
			},
		})

		// $("#table-kdivisi").on("click", ".btn-edit", function(e) {
		// 	e.preventDefault();
		// 	$("#formKdivisi [type=reset]").click();
		// 	// get row
		// 	let row = $(this).closest('tr');
		// 	let data = tblKdivisi.row(row).data();

		// 	// change url
		// 	$("#formKdivisi").attr("action", actionUpdateKdivisiUrl + "/" + data.karyawandivisi_id + "?menu_id=" + currentMenuId);
		// 	// set data
		// 	$("#karyawandivisi_id").val(data.karyawandivisi_id);
		// 	$("#karyawandivisi_name").val(data.karyawandivisi_name).focus();
		// })

		// $("#table-kdivisi").on("click", ".btn-delete", function(e) {
		// 	e.preventDefault();
		// 	let row = $(this).closest('tr');
		// 	let data = tblKdivisi.row(row).data();
		// 	Swal.fire({
		// 		html: 'Apakah anda ingin menghapus divisi <b>' + data.karyawandivisi_name + '</b>?',
		// 		icon: 'question',
		// 		preConfirm: () => {
		// 			Swal.showLoading();
		// 			// tblPengaturanTunjangan.row(row).remove();
		// 			// return true;
		// 			return fetch(`${actionDeleteKdivisiUrl}/${data.karyawandivisi_id}?menu_id=${currentMenuId}`, {
		// 					method: 'POST',
		// 					body: new URLSearchParams($.param({
		// 						_token: $("meta[name=csrf-token]").attr('content')
		// 					}))
		// 				})
		// 				.then(response => {
		// 					if (!response.ok) {
		// 						return response.text().then(res => {
		// 							throw new Error(res);
		// 						})
		// 					}
		// 					return response.json()
		// 				})
		// 				.catch(error => {
		// 					Swal.showValidationMessage(`Request failed: ${error}`);
		// 				})
		// 		},
		// 		allowOutsideClick: () => false
		// 	}).then((result) => {
		// 		console.log('result', result)
		// 		result = result.value;
		// 		if (result == undefined) {
		// 			return false;
		// 		}

		// 		if (!result.success) {

		// 			Swal.fire({
		// 				html: result.message,
		// 				showCancelButton: false,
		// 				confirmButtonText: "Ok",
		// 				icon: 'error'
		// 			})
		// 			return false;
		// 		}

		// 		toastr.success(result.message);
		// 		tblKdivisi.draw();
		// 	});
		// })

		$("#formKdivisi [type=reset]").click(function(e) {
			e.preventDefault();
            $("#formKdivisi").attr("action", actionStoreKdivisiUrl + "?menu_id=" + currentMenuId);
			resetForm('#formKdivisi');
		})
    });
</script>