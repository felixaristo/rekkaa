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
					<div class="card-body">
						<div class="col-sm-12">
							<table class="table table-hover display nowrap" id="table-subscription" style="width: 100%">
								<thead class="table-light">
								<tr>
									<th>NPWP</th>
									<th>Tipe</th>
									<th>Total</th>
									<th>Periode</th>
									<th>Tgl. Kadaluarsa</th>
									<th>Tgl. Order</th>
									<th>No. Order</th>
									<th>Jenis</th>
									<th>Deskripsi</th>
									<th>Status</th>
									<!-- <th>Actions</th> -->
								</tr>
								</thead>
								<tbody class="table-border-bottom-0">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- Bootstrap Table with Header - Light -->
		</div>
	</div>

	<script src="{{asset('assets/js/reload.js')}}"></script>
	<script>
	$(function() {
		// Begin Table Wajib Pajak
		let tblSubscription = $("#table-subscription").DataTable({
			// "filtering": false,
			// "responsive": true,
			"searching": false,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('user.page.subscription.riwayatorder.datatable')}}",
				"type": "GET",
				"data": function(data) {
					//     console.log(data); // send data to server
				}
			},
			// "fnInitComplete": function() {
			// 	this.fnAdjustColumnSizing(true);
			// 	// $(this).find(".cetak-registrasi").select2();
			// },
			"autoWidth": true,
			"columnDefs": [{
				target: [7],
				width: 30
			}, {
				target: [0,1,2,3,4,6,7],
				className: 'text-center'
			}],
			"columns": [
				{
					"data": "wajibpajak",
					"render": function(data, type, row) {
						return (data) ? data.wajibpajak_npwp : '';
					}
				},
				{
					"data": "userorder_subscriptiontype"
				},
				{
					"data": "userorder_total",
					"render": function(data, type, row) {
						return formatCurrency(data);
					}
				},
				{
					"data": "userorder_paymentperiode"
				},
				{
					"data": "userorder_expired_at"
				},
				{
					"data": "userorder_created_at"
				},
				{
					"data": "userorder_no"
				},
				{
					"data": "userorder_kind"
				},
				{
					"data": "userorder_description"
				},
				{
					"data": "userorder_status",
					  "render": function(data, type, row) {
						return (data == 'PAID') ? `<span class="badge rounded-pill bg-label-success">${data}</span>` : `<span class="badge rounded-pill bg-label-danger">${data}</span>`;
					  }
				},
				//   {
				//     "data": "sttunjangankaryawan_id",
				//     "render": function(data, type, row) {
				//         return `
				//         <div class="dropdown">
				//         <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
				//           <i class="bx bx-dots-vertical-rounded"></i>
				//         </button>
				//         <div class="dropdown-menu">
				//           <a class="dropdown-item btn-edit" href="javascript:void(0);"
				//             ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
				//           >
				//           <a class="dropdown-item btn-delete" href="javascript:void(0);"
				//             ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
				//           >
				//         </div>
				//       </div>
				//         `
				//     }
				//   },
			],
		});

		// set meta title
		setHtmlTitle('{{$title}}')
	})
	</script>