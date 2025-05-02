<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-sm-6">
						<h5 class="mb-0">{{$title}}</h5>
					</div>
					<div class="col-sm-6 text-right">
						<a class="btn btn-sm btn-warning" id="btn-menu" href="#">
							<i class='bx bx-plus'></i> Menu
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="text-nowrap">
					<table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-menu">
						<thead class="table-light">
							<tr>
								<th>Parent</th>
								<th>Menu</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody class="table-border-bottom-0">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- Bootstrap Table with Header - Light -->
	</div>
</div>

<!-- Modal Menu -->
<div class="modal fade" id="modalMenu" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<form id="formMenu" method="POST" action="{{route('admin.subscription.menu.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<input type="hidden" name="menu_id" id="menu_id">
						<!-- <div class="row mb-3" style="display: none;" id="excodepenggajian-container">
							<label class="col-sm-5" for="excodepenggajian">Kode Menu</label>
							<div class="col-sm-7">
								<input type="text" name="excodepenggajian" id="excodepenggajian" class="form-control" disabled>
							</div>
						</div> -->
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="menu_title">Nama Menu</label>
							<div class="col-sm-7">
								<input type="text" required name="menu_title" id="menu_title" class="form-control" placeholder="Masukkan Nama Menu">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="menu_parent">Parent Menu</label>
							<div class="col-sm-7">
								<select required style="width: 100%;" name="menu_parent" id="menu_parent" class="form-control" data-placeholder="-:Pilih Parent:-">
									<option value=""></option>
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="menu_position">Posisi Menu</label>
							<div class="col-sm-7">
								<input type="number" required name="menu_position" id="menu_position" class="form-control" placeholder="Masukkan Posisi Menu">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="menu_link">Link Menu</label>
							<div class="col-sm-7">
								<input type="text" name="menu_link" id="menu_link" class="form-control" placeholder="Masukkan Link Menu">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="menu_icon">Icon Menu</label>
							<div class="col-sm-7">
								<input type="text" name="menu_icon" id="menu_icon" class="form-control" placeholder="Masukkan Icon Menu">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="menu_description">Deskripsi Menu</label>
							<div class="col-sm-7">
								<textarea name="menu_description" id="menu_description" class="form-control" placeholder="Masukkan Deskripsi Menu"></textarea>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="menu_active">Aktif</label>
							<div class="col-sm-7">
								<div class="form-check form-check-inline">
									<input name="menu_active" class="form-check-input" type="radio" value="1" id="menu_active_y" checked="">
									<label class="form-check-label" for="menu_active_y"> Ya </label>
								</div>
								<div class="form-check form-check-inline">
									<input name="menu_active" class="form-check-input" type="radio" value="0" id="menu_active_t">
									<label class="form-check-label" for="menu_active_t"> Tidak </label>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="menu_permission">Permission</label>
							<div class="col-sm-7">
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="R" id="lihat">
									<label class="form-check-label" for="lihat">Lihat</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="C" id="tambah">
									<label class="form-check-label" for="tambah">Tambah</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="U" id="edit">
									<label class="form-check-label" for="edit">Edit</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="SD" id="hapus">
									<label class="form-check-label" for="hapus">Hapus</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="EXPORT" id="export">
									<label class="form-check-label" for="export">Export</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="IMPORT" id="import">
									<label class="form-check-label" for="import">Import</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="Q" id="maks">
									<label class="form-check-label" for="maks">Maksimal Kuota Data</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="COPY" id="copy">
									<label class="form-check-label" for="copy">Copy Link</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="Q_SEND_EMAIL" id="maksemail">
									<label class="form-check-label" for="maksemail">Maksimal Kuota Kirim Email</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="Q_SEND_WA" id="makswa">
									<label class="form-check-label" for="makswa">Maksimal Kuota Kirim Whatsapp</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="EXPORT_EXCEL" id="exportxls">
									<label class="form-check-label" for="exportxls">Export Excel</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="EXPORT_BUKTIPOTONG" id="exportbupot">
									<label class="form-check-label" for="exportbupot">Export Bukti Potong</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="EXPORT_EXCEL_ESPT" id="exportxlsspt">
									<label class="form-check-label" for="exportxlsspt">Export E-SPT PPh21</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="EXPORT_EXCEL_REKAP" id="exportxlsrekap">
									<label class="form-check-label" for="exportxlsrekap">Export Rekap PPh21</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="IMPORT_PAYSLIP" id="importpayslip">
									<label class="form-check-label" for="importpayslip">Import Payslip</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="DOWNLOAD_PAYSLIP" id="downloadpayslip">
									<label class="form-check-label" for="downloadpayslip">Download Payslip</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="CONFIRM_PAYSLIP" id="confirmpayslip">
									<label class="form-check-label" for="confirmpayslip">Confirm Payslip</label>
								</div>
								<div class="form-check form-check-inline">
									<input name="permissions[]" class="form-check-input permissions" type="checkbox" value="CALCULATE_PAYSLIP" id="calculatepayslip">
									<label class="form-check-label" for="calculatepayslip">Calculate Payslip</label>
								</div>
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
			</div>
		</div>
	</div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
	$(function() {
		let currentMenuId = "{{request()->get('menu_id')}}";
		let actionStoreUrl = "{{route('admin.subscription.menu.store', '')}}";
		let actionUpdateUrl = "{{route('admin.subscription.menu.update', '')}}";
		let actionDeleteUrl = "{{route('admin.subscription.menu.delete', '')}}";
		// let updatedKaryawanIds = [];
		let deletedEmployeeIds = [];

		// Begin Table Menu
		let tblMenu = $("#table-pengaturan-menu").DataTable({
			// "filtering": false,
			"ordering": true,
			"searching": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			"language": {
				// "emptyTable": "Tidak ada data"
				"searchPlaceholder": "Cari Nama / Kode Menu",
			},
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('admin.subscription.menu.datatable', ['menu_id' => request()->get('menu_id')])}}",
				"type": "GET",
				"data": function(data) {
					//     console.log(data); // send data to server
				}
			},
			"fnInitComplete": function() {
				// this.fnAdjustColumnSizing(true);
			},
			"autoWidth": true,
			"columnDefs": [{
				target: [3],
				width: 30,
				orderable: false,
			}, {
				target: [0, 1, 2, 3],
				className: 'text-center'
			}],
			"columns": [{
					"data": "parent_title"
				},
				{
					"data": "menu_title",
				},
				{
					"data": "menu_active",
					"render": function(data, type, row) {
						return (data == "1") ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
					}
				},
				{
					"data": "menu_id",
					"render": function(data, type, row) {
						return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                </div>
              </div>
                `
					}
				},
			],
		});

		let tempData = null;
		$("#table-pengaturan-menu").on("click", ".btn-edit", function(e) {
			e.preventDefault();
			$("#formMenu [type=reset]").click();
			$("#modalMenu").modal("show");
			// get row
			let row = $(this).closest('tr');
			let data = tblMenu.row(row).data();
			// change url
			$("#formMenu").attr("action", actionUpdateUrl + "/" + data.menu_id + "?menu_id=" + currentMenuId);
			// set data
			$("#menu_id").val(data.menu_id);
			$("#menu_title").val(data.menu_title);
			$("#menu_position").val(data.menu_position);
			$("#menu_link").val(data.menu_link);
			$("#menu_icon").val(data.menu_icon);
			$("#menu_description").val(data.menu_description);
			$("#menu_parent").append(new Option(data.parent_title, data.parent_id, true, true)).trigger('change');
			$(`#formMenu input[name=menu_active][value=${data.menu_active}]`).click();

		})

		$("#table-pengaturan-menu").on("click", ".btn-delete", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblMenu.row(row).data();
			Swal.fire({
				html: 'Apakah anda ingin menghapus menu <b>' + data.menu_title + '</b>?',
				icon: 'question',
				preConfirm: () => {
					Swal.showLoading();
					// tblPengaturanPotongan.row(row).remove();
					// return true;
					return fetch(`${actionDeleteUrl}/${data.menu_id}?menu_id=${currentMenuId}`, {
							method: 'POST',
							body: new URLSearchParams($.param({
								_token: $("meta[name=csrf-token]").attr('content')
							}))
						})
						.then(response => {
							if (!response.ok) {
								return response.text().then(res => {
									throw new Error(res);
								})
							}
							return response.json()
						})
						.catch(error => {
							Swal.showValidationMessage(`Request failed: ${error}`);
						})
				},
				allowOutsideClick: () => false
			}).then((result) => {
				console.log('result', result)
				result = result.value;
				if (result == undefined) {
					return false;
				}

				if (!result.success) {

					Swal.fire({
						html: result.message,
						showCancelButton: false,
						confirmButtonText: "Ok",
						icon: 'error'
					})
					return false;
				}

				toastr.success(result.message);
				tblMenu.draw();
			});
		});

		let formMenu = $("#formMenu").validate({
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
      submitHandler: function(form) {
          $(".spinner-box").css({'display': 'table'});

          $.ajax({
              method: form.method,
              url: form.action,
              data: $(form).serialize()+"&"+$.param({
              	_token: $("meta[name=csrf-token]").attr('content'),
							}),
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
                        menu_title: response.message
                      })
                    } else {
                        toastr.error(response.message);
                    }
                      return false;
                  }
                  
                  toastr.success(response.message);

                  $("#formMenu [type=reset]").click();
                  $("#modalMenu").modal("hide");
              }
          })
      },
    })
		// End Table Menu

		$("#menu_parent").select2({
		  dropdownParent: $("#modalMenu #formMenu"),
      // tags: true,
			ajax: {
				url: `{{route('admin.subscription.menu.select')}}?menu_id=${currentMenuId}`,
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
						item.id = item.menu_id;
						item.text = item.menu_title;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
      },
		});

		$("#btn-menu").click(function(e) {
			e.preventDefault();
			$("#formMenu [type=reset]").click();

			$("#modalMenu").modal("show");
		})

		$("#modalMenu").on("hide.bs.modal", function() {
			tblMenu.draw(); // Refresh the DataTable
		});

		
		$("#formMenu [type=reset]").click(function(e) {
			e.preventDefault();
			resetForm('#formMenu');
			$('.permissions').prop('checked', false);

			$("#formMenu").attr("action", actionStoreUrl + "?menu_id=" + currentMenuId);
		});

		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>