<?php 
use Carbon\Carbon;
$joindate = Carbon::parse($user->user_created_at);
$joindatey = $joindate->format('Y');
?>
<div class="accordion mb-4" id="accordionExample">
	<div class="card accordion-item active">
		<h2 class="accordion-header" id="headingFilter">
			<button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse" role="tabpanel">
				Filter Lanjutan
			</button>
		</h2>

		<div id="filterCollapse" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<!-- Start Date and End Date inputs here -->
				<div class="row align-items-center">
					<div class="col-md-3">
						<label for="filterPeriode">Periode</label>
						<input type="text" id="filterPeriode" name="filterPeriode" class="form-control" autocomplete="off">
					</div>
					<div class="col-md-3">
						<label for="filterListEmployee">Karyawan</label>
						<select multiple style="width: 100%;" name="filterListEmployee[]" id="filterListEmployee" class="form-control" data-placeholder=" Pilih Karyawan" autocomplete="off"></select>
					</div>
					<div class="col-md-3 mt-md-4 mt-sm-3 text-md-start text-sm-end">
						<button id="searchButton" class="btn btn-search btn-outline-warning me-2 btn-sm">
							<i class="bx bx-search-alt"></i> Cari
						</button>
						<button id="resetButton" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
							<i class="bx bx-reset"></i> Reset
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-sm-5">
						<h5 class="mb-0">{{$title}}</h5>
					</div>
					<div class="col-sm-7 text-right">
						@if(in_array('EXPORT_BUKTIPOTONG', request()->get('permission_codes')))
						<div class="dropdown" style="display: inline-block;">
							<button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown">
							Bukti Potong 1721-VI
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item btn-buktipotong" data-type="excel" data-format="ebupot-excel21" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong 1721-VI (Excel)
								</a>
								<!-- <a class="dropdown-item btn-buktipotong" data-type="pdf" data-format="vi" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong 1721-VI (Pdf)
								</a> -->
								<a class="dropdown-item btn-buktipotong" data-type="pdf" data-format="vi-bulan" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong Bulanan 1721-VI (Pdf)
								</a>
							</div>
						</div>
						@endif
						@if(in_array('EXPORT_EXCEL_REKAP', request()->get('permission_codes')))
						<a class="btn btn-sm btn-warning" id="btn-rekap" href="#">
							<i class='bx bx-file'></i>
							Ekspor Rekap Perhitungan
						</a>
						@endif
						@if(in_array('EXPORT_EXCEL_ESPT', request()->get('permission_codes')))
						<!-- <a class="btn btn-sm btn-warning" id="btn-modal-espt" href="#">
							<i class='bx bx-file'></i>
							Ekpor e-SPT
						</a> -->
						@endif
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="text-nowrap">
					<table class="table table-hover display nowrap" style="width: 100%" id="table-pajakpenghasilan">
						<thead class="table-light">
							<tr>
								<th></th>
								<th>Nama</th>
								<th>NPWP</th>
								<th>NIK</th>
								<th>Metode PPh 21</th>
								<th>Pendapatan Kotor</th>
								<!-- <th>DPP Kumulatif</th>
								<th>Rate Tarif (%)</th> -->
								<th>PPh 21</th>
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

<!-- Modal -->
<div class="modal fade" id="modalEspt">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Ekspor e-SPT</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row mb-3">
					<label class="col-sm-12 nodot-label" for="pembetulan">Pembetulan</label>
					<div class="col-sm-12">
						<select name="pembetulan" style="width: 100%;" id="pembetulan" class="form-control" data-placeholder="Masukkan Pembetulan">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
						</select>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-12">
						<button type="button" id="btn-espt" class="btn btn-sm btn-warning"><i class='bx bx-file'></i> Ekspor e-SPT</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
	$(function() {
		let currentMenuId = "{{request()->get('menu_id')}}";
		$("#pembetulan").select2({
			dropdownParent: $("#modalEspt"),
		});
		let filterPeriode = moment().format('MM-YYYY');
		$("#filterPeriode").datepicker({
				language: "id-ID",
				format: "MM-yyyy",
				startView: "months",
				minViewMode: "months",
				startDate: '01-<?php echo $joindatey ?>',
        		endDate: '12-'+moment().format('Y'),
			}).datepicker("setDate", filterPeriode)
			.on('hide', function(e) {
				// `e` here contains the extra attributes
				let dt = $('#filterPeriode').datepicker("getDate");
				filterPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
			});

		$("#filterListEmployee").select2({
			// dropdownParent: $("#formAddCuti"),
			// tags: true,
			ajax: {
				url: "{{route('user.page.nonkaryawan.select')}}?menu_id="+currentMenuId,
				data: function(params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function(data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.karyawan_id;
						item.text = item.karyawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		})
		$("#searchButton").click(function(e) {
			e.preventDefault();
			tablePajakPenghasilan.draw();
		});

		$("#resetButton").click(function(e) {
			e.preventDefault();
			$("filterPeriode").val(null).trigger('change');
			$("filterListEmployee").val(null).trigger('change');
			tablePajakPenghasilan.draw();
		});

		// Begin Table Wajib Pajak
		let tablePajakPenghasilan = $("#table-pajakpenghasilan").DataTable({
			// "filtering": true,
			"ordering": true,
			"searching": false,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('user.page.pajakpenghasilan.pph21.nonkaryawan.datatable', ['menu_id' => request()->get('menu_id')])}}",
				"type": "GET",
				"data": function(data) {
					//     console.log(data); // send data to server
					data.periode = filterPeriode;
					data.karyawan_ids = $("#filterListEmployee").val();
				}
			},
			// "fnInitComplete": function() {
			//     this.fnAdjustColumnSizing(true);
			// },
			"autoWidth": true,
			"columnDefs": [{
					target: [5],
					width: 30,
					orderable: false,
				}, {
					target: [0, 1, 2, 3, 4],
					className: 'text-center'
				},
			],
			"columns": [{
					className: 'dt-control',
					orderable: false,
					data: null,
					defaultContent: ''
				},
				{
					"data": "payroll",
					"render": function(data, type, row) {
						return (data.length>0) ? data[0].payroll_karyawan_name : row.karyawan_name;
					}
				},
				{
					"data": "payroll",
					"render": function(data, type, row) {
						return (data.length>0) ? data[0].payroll_karyawan_npwp : row.karyawan_npwp;
					}
				},
				{
					"data": "payroll",
					"render": function(data, type, row) {
						return (data.length>0) ? data[0].payroll_karyawan_nik : row.karyawan_nik;
					}
				},
				{
					"data": "payroll",
					"render": function(data, type, row) {
						return (data.length>0) ? data[0].payroll_method.replace('_', ' ') : row.karyawan_calculation_method.replace('_', ' ');
					}
				},
				{
					"data": "payroll_total_income",
					"className": "text-right",
					"render": function(data, type, row) {
						// console.log('data', data);
						let total = (data) ? data : 0;
						// let pph21 = JSON.parse(data);
						return 'Rp. ' + formatCurrency(total);
					}
				},
				// {
				// 	"data": "pph21_total_dpp_kumulatif",
				// 	"className": "text-right",
				// 	"render": function(data, type, row) {
				// 		// console.log('data', data);
				// 		// let pph21 = JSON.parse(data);
				// 		let total = (row.payroll_total_income > 0 && data) ? data : 0;
				// 		return 'Rp. ' + formatCurrency(total);
				// 	}
				// },
				// {
				// 	"data": "pph21_tarif21_data",
				// 	"className": "text-right",
				// 	"render": function(data, type, row) {
				// 		// console.log('data', data);
				// 		let tarif21 = JSON.parse(data);
				// 		let tarif21lbl = '';
				// 		if(row.payroll_total_income > 0 && tarif21) {
				// 			tarif21.forEach((tf21, idx) => {
				// 				tarif21lbl += tf21.tarif21_rate+' %';
				// 				if(idx < tarif21.length - 1) {
				// 					tarif21lbl += ', ';
				// 				}
				// 			})
				// 		}
						
				// 		return tarif21lbl;
				// 	}
				// },
				{
					"data": "pph21_total_netto",
					"className": "text-right",
					"render": function(data, type, row) {
						// console.log('data', data);
						let total = (row.payroll_total_income > 0 && data) ? data : 0;
						return 'Rp. ' + formatCurrency(total);
					}
				},
			],
		});

		// Add event listener for opening and closing details
		tablePajakPenghasilan.on('click', 'td.dt-control', function(e) {
			let tr = e.target.closest('tr');
			let row = tablePajakPenghasilan.row(tr);

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
			} else {
				// Open this row
				row.child(formatRow(row.data())).show();
			}
		});

		// Formatting function for row details - modify as you need
		function formatRow(d) {
			// `d` is the original data object for the row
			console.log('d', d)
			let payrolls = d.payroll;
			let rows = ``;
			payrolls.forEach(py => {
				let pph21 = py.pph21;
				let trxat = moment(pph21.pph21_trx_at).format('DD-MM-YYYY');
				rows += `<tr>
					<td class="text-center">${trxat}</td>
					<td class="text-right">${formatCurrency(pph21.pph21_bruto_month)}</td>
					<td class="text-right">${formatCurrency(pph21.pph21_dpp)}</td>
					<td class="text-center">${pph21.pph21_ptkp_category}</td>
					<td class="text-center">${pph21.pph21_ptkpdet_rate_percentage}</td>
					<td class="text-right">${formatCurrency(pph21.pph21_total_month)}</td>
				</tr>`;
			})
			return `<table>
			<thead>
				<tr>
					<th class="text-center">Tgl. Transaksi</th>
					<th class="text-center" width="150">Pendapatan Kotor</th>
					<th class="text-center" width="150">DPP</th>
					<th class="text-center">Kategori TER</th>
					<th class="text-center">Tarif TER (%)</th>
					<th class="text-center" width="150">Total Pajak</th>
				</tr>
			</thead>
			<tbody>${rows}</tbody>
			</table>`
		}

		$("#btn-rekap").click(function(e) {
			e.preventDefault();
			let params = `menu_id=${currentMenuId}&type=rekap`;
			let karyawanIds = $("#filterListEmployee").val();
			if (filterPeriode) {
				params += `&periode=${filterPeriode}`;
			}
			if (karyawanIds) {
				karyawanIds.forEach(kr => {
					params += `&karyawan_ids[]=${kr}`;
				})
			}
			window.open(`{{route('user.page.pajakpenghasilan.pph21.nonkaryawan.export', '')}}?${params}`, '_blank');
		})

		$("#btn-modal-espt").click(function(e) {
			e.preventDefault();

			$("#modalEspt").modal("show");
		})

		$("#btn-espt").click(function(e) {
			e.preventDefault();
			let params = `menu_id=${currentMenuId}&type=espt`;
			let karyawanIds = $("#filterListEmployee").val();
			let pembetulan = $("#pembetulan").val();

			if(pembetulan < 0) {
				toastr.error('Pembetulan wajib diisi!');
				return false;
			}
			params += `&pembetulan=${pembetulan}`;
			if (filterPeriode) {
				params += `&periode=${filterPeriode}`;
			}
			if (karyawanIds) {
				karyawanIds.forEach(kr => {
					params += `&karyawan_ids[]=${kr}`;
				})
			}
			window.open(`{{route('user.page.pajakpenghasilan.pph21.nonkaryawan.export', '')}}?${params}`, '_blank');
			$("#modalEspt").modal("hide");
		})

		$(".btn-buktipotong").click(function(e) {
			e.preventDefault();
			
			let t = $(this).attr("data-type");
			let format = $(this).attr("data-format");
			let params = `menu_id=${currentMenuId}&type=${format}&t=${t}`;
			let karyawanIds = $("#filterListEmployee").val();

			if (filterPeriode) {
				params += `&periode=${filterPeriode}`;
			}
			if (karyawanIds) {
				karyawanIds.forEach(kr => {
					params += `&karyawan_ids[]=${kr}`;
				})
			}
			window.open(`{{route('user.page.pajakpenghasilan.pph21.nonkaryawan.export', '')}}?${params}`, '_blank');
			
		})

		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>