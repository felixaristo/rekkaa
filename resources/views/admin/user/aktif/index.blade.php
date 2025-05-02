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
							<table class="table table-hover display nowrap" style="width: 100%" id="table-master-user">
								<thead class="table-light">
									<tr>
										<th>Nama</th>
										<th>Email</th>
										<th>No. Telepon</th>
										<th>Verifikasi</th>
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
		let tblUser = $("#table-master-user").DataTable({
			// "filtering": false,
			"searching": false,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('admin.page.user.aktif.datatable')}}",
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
				target: [4],
				width: 30
			}, {
				target: [0,1,2,3,4],
				className: 'text-center'
			}],
			"columns": [
				{
					"data": "user_name"
				},
				{
					"data": "user_email"
				},
				{
					"data": "user_phone"
				},
				{
					"data": "user_email_verified_at",
					"render": function(data, type, row) {
						return (data) ? '<span class="badge rounded-pill bg-label-success">Terverifikasi</span>' : '<span class="badge rounded-pill bg-label-danger">Belum Terverifikasi</span>';
					}
				},
				{
					"data": "user_id",
					"render": function(data, type, row) {
					//   return `
					//   <div class="dropdown">
					//   <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
					//     <i class="bx bx-dots-vertical-rounded"></i>
					//   </button>
					//   <div class="dropdown-menu">
					//     <a class="dropdown-item btn-edit" href="javascript:void(0);"
					//       ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
					//     >
					//     <a class="dropdown-item btn-delete" href="javascript:void(0);"
					//       ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
					//     >
					//   </div>
					// </div>
					//   `
					return '-'
					}
				},
			],
		});

		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>