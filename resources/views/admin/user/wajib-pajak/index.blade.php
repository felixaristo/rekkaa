<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<div class="row">
				<div class="col-sm-6">
				<h5 class="card-header">{{$title}}</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<div class="text-nowrap">
							<table class="table table-hover display nowrap" style="width: 100%" id="table-master-wajibpajak">
								<thead class="table-light">
									<tr>
										<th>Nama</th>
										<th>NPWP</th>
										<th>Email</th>
										<th>No. Telepon</th>
										<th>Tipe</th>
										<th>Subscription</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody class="table-border-bottom-0">
								</tbody>
							</table>
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
		// Begin Table Wajib Pajak
		let tblWajibpajak = $("#table-master-wajibpajak").DataTable({
			// "filtering": false,
			"searching": false,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('admin.page.user.wajibpajak.datatable')}}",
				"type": "GET",
				"data": function(data) {
					//     console.log(data); // send data to server
				}
			},
			"fnInitComplete": function() {
				// this.fnAdjustColumnSizing(true);
				// $(this).find(".cetak-registrasi").select2();
			},
			"autoWidth": true,
			"columnDefs": [{
				target: [6],
				width: 30
			}, {
				target: [0,1,2,3,4,5,6],
				className: 'text-center'
			}],
			"columns": [
				{
					"data": "wajibpajak_name"
				},
				{
					"data": "wajibpajak_npwp"
				},
				{
					"data": "wajibpajak_email"
				},
				{
					"data": "wajibpajak_phone"
				},
				{
					"data": "wajibpajak_type"
				},
				
				{
					"data": "usersubscription",
					"render": function(data, type, row) {
						return '<span class="badge rounded-pill bg-label-success">'+'</span>';
					}
				},
				{
					"data": "wajibpajak_id",
					"render": function(data, type, row) {
					  return `
					  <div class="dropdown">
					  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
					    <i class="bx bx-dots-vertical-rounded"></i>
					  </button>
					  <div class="dropdown-menu">
					    <a class="dropdown-item btn-upgrade" data-upgrade="PLATINUM" href="javascript:void(0);"
					      ><i class="bx bx-trophy me-1 text-info"></i> Upgrade PLATINUM</a
					    >
					  </div>
					</div>
					  `
					// return '-'
					}
				},
			],
		});

		// non aktifkan karyawan
		$("#table-master-wajibpajak").on("click", ".btn-upgrade", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblWajibpajak.row(row).data();
			Swal.fire({
				html: `Apakah anda ingin mengupgrade wajib pajak <b>${data.wajibpajak_name} menjadi PLATINUM</b>?`,
				icon: 'question',
				preConfirm: () => {
					Swal.showLoading();
					
					return fetch(`{{url('/admin/user/wajib-pajak/upgrade')}}/${data.wajibpajak_id}`, {
						method: 'POST',
						body: new URLSearchParams($.param({
							_token: $("meta[name=csrf-token]").attr('content'),
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
				if(result == undefined) {
					return false;
				}
				
				if (!result.success) {
					toastr.error(result.message);
					return false;
				}

				toastr.success(result.message);
				tblWajibpajak.draw();
			});
			})

		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>