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
					<div class="col-md-3">
						<label for="filterPembetulanEmployee">Pembetulan</label>
						<select style="width: 100%;" name="filterPembetulanEmployee" id="filterPembetulanEmployee" class="form-control" data-placeholder=" Pilih Pembetulan" autocomplete="off">
							<option value="0">0</option>
						</select>
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
								Bukti Potong 1721-A1
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item btn-buktipotong" data-format="ebupot-excel21" data-type="xls" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong Bulanan (Excel)
								</a>
								<a class="dropdown-item btn-buktipotong" data-format="a1" data-type="pdf" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong 1721-A1 (Pdf)
								</a>
								<a class="dropdown-item btn-buktipotong" data-format="a1" data-type="xls" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong 1721-A1 (Excel)
								</a>
								<a class="dropdown-item btn-buktipotong" data-format="viii" data-type="pdf" href="#">
									<i class='bx bx-file'></i>
									Bukti Potong Bulanan 1721-VIII (Pdf)
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

						<!-- <a class="btn btn-sm btn-outline-info" id="btn-import" href="#">
							<i class='bx bxs-cloud-upload'></i> Impor Pembetulan
						</a> -->

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
								<th>Posisi</th>
								<th>Metode PPh 21</th>
								<th>Gaji Pokok</th>
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

<!-- Impor & Template Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true" data-bs-focus="false">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importModalLabel">Unduh Templat & Impor Data</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row mb-1">
					<div class="col">
						<div class="form-text duration-helper-text text-muted">
							*) Sebelum mengimpor Data Pembetulan, anda harus mengunduh Templat Impor Pembetulan
						</div>
					</div>
				</div>
				<div class="row mb-1">
					<div class="col">
						<div class="form-text duration-helper-text text-muted">
							*) Sesuaikan Data Pembetulan yang akan di impor dengan Templat Impor Pembetulan sistem REKKAA
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-import-history" class="btn btn-outline-success"><i class='bx bxs-history'></i>Histori Impor</button>
				<div class="ms-auto">
					<button type="button" id="btn-template" class="btn btn-outline-warning"><i class='bx bxs-file-export'></i>Unduh Templat</button>
					<button type="button" id="btn-import-file" class="btn btn-outline-info btn-import"><i class='bx bxs-cloud-upload'></i>Impor Pembetulan Pajak</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="historyModal" data-bs-backdrop="static" aria-hidden="true" data-bs-focus="false">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="historyModalLabel">Histori Impor Karyawan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeHistory"></button>
			</div>
			<div class="modal-body">
				<div class="row align-items-center mb-3">
					<div class="col-md-3">
						<label for="start-date-history" class="small">Tanggal Awal</label>
						<input type="text" id="start-date-history" name="start-date-history" class="form-control form-control-sm" autocomplete="off">
					</div>
					<div class="col-md-3">
						<label for="end-date-history" class="small">Tanggal Akhir</label>
						<input type="text" id="end-date-history" name="end-date-history" class="form-control form-control-sm" autocomplete="off">
					</div>
					<div class="col-md-6 mt-md-4 mt-sm-3 text-md-start text-sm-end">
						<button id="searchButtonHistory" class="btn btn-search btn-outline-warning me-2 btn-sm">
							<i class="bx bx-search-alt"></i> Cari
						</button>
						<button id="resetButtonHistory" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
							<i class="bx bx-reset"></i> Reset
						</button>
					</div>
				</div>
				<!-- DataTable container -->
				<table id="table-history" class="table table-hover display nowrap small-text-datatable" style="width: 100%">
					<!-- Your DataTable content goes here -->
					<thead>
						<tr>
							<th>Tanggal Impor</th>
							<th>File Impor</th>
							<th>Pengunggah</th>
							<th>Status Impor</th>
							<th>Histori File</th>
							<!-- Add more columns as needed -->
						</tr>
					</thead>
				</table>
			</div>
			<!-- <div class="modal-footer"> -->
			<!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
			<!-- </div> -->
		</div>
	</div>
</div>

<!-- Modal Impor -->
<div class="modal fade" id="modalImporPembetulan" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Impor Pembetulan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeImpor"></button>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<form class="form-horizontal form-lbl-dot">
						<div class="form-group row mb-3">
							<label for="file_import" class="col-sm-4 lbl-req">Unggah Data</label>
							<div class="col-sm-8">
								<input type="file" required name="file_import" id="file_import" class="form-control">
							</div>
						</div>
						<div class="form-group row mb-3">
							<div class="col-sm-12 text-right">
								<button type="button" id="importButton" class="btn btn-sm btn-outline-info"><i class='bx bxs-cloud-upload'></i> Impor</button>
							</div>
						</div>
					</form>

					<div class="progress-container" style="display: none;">
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width: 0;"></div>
						</div>
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

		const currentDate = new Date();

		// Calculate the first day of the current month
		const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

		// Calculate the last day of the current month
		const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
		const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
		const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;

		$("#filterPembetulanEmployee").select2();
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
				endDate: '12-' + moment().format('Y'),
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
				url: "{{route('user.page.karyawan.select')}}",
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
				"url": "{{route('user.page.pajakpenghasilan.pph21.karyawan.datatable', ['menu_id' => request()->get('menu_id')])}}",
				"type": "GET",
				"data": function(data) {
					//     console.log(data); // send data to server
					data.periode = filterPeriode;
					data.karyawan_ids = $("#filterListEmployee").val();
				}
			},
			"autoWidth": true,
			"columnDefs": [{
				target: [0],
				width: 30,
				orderable: false,
			}, {
				target: [0, 1, 2, 3, 4, 5, 6],
				className: 'text-center'
			}, ],
			"columns": [{
					className: 'dt-control',
					orderable: false,
					data: null,
					defaultContent: ''
				},
				{
					"data": "payroll_karyawan_name",
					"render": function(data, type, row) {
						return data;
					}
				},
				{
					"data": "payroll_karyawan_npwp",
					"render": function(data, type, row) {
						return data;
					}
				},
				{
					"data": "payroll_karyawanjabatan_name",
					"render": function(data, type, row) {
						return data;
					}
				},
				{
					"data": "payroll_method",
					"render": function(data, type, row) {
						return data.replace('_', ' ');
					}
				},
				{
					"data": "payroll_prorate_salary",
					"className": "text-right",
					"render": function(data, type, row) {
						// console.log('data', data);
						// let pph21 = JSON.parse(data);
						return 'Rp. ' + formatCurrency(data);
					}
				},
				{
					"data": "pph21",
					"className": "text-right",
					"render": function(data, type, row) {
						// console.log('data', data);
						return 'Rp. ' + formatCurrency(data.pph21_total_month);
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
			let karyawan = d.karyawan;

			let ptkp = JSON.parse(d.pph21.pph21_ptkp_data);
			let category_ter = d.pph21.pph21_ptkp_category;
			let rate_ter = d.pph21.pph21_ptkpdet_rate_percentage;
			let bpjssetting = (d.payroll_bpjssetting) ? JSON.parse(d.payroll_bpjssetting) : null;
			let bpjskes = 0;
			let bpjstk = 0;
			let pkptotal = d.pph21.pph21_pkp;
			let netto_pertahuntotal = parseFloat(d.pph21.pph21_netto_year);
			let pph21tahuntotal = parseFloat(d.pph21.pph21_total_year);
			if (bpjssetting) {
				for (let i = 0; i < bpjssetting.length; i++) {
					if (bpjssetting[i].type == 'KESEHATAN') {
						bpjskes = 1;
					}
					if (bpjssetting[i].type == 'TENAGA_KERJA') {
						bpjstk = 1;
					}
				}
			}
			let parseTunjangan = (d.payroll_allowance_setting) ? JSON.parse(d.payroll_allowance_setting) : [];
			let tunjangantxt = '';
			if (parseTunjangan.length > 0) {
				parseTunjangan.forEach(tj => {
					// console.log('pt', pt)
					tunjangantxt += `<dt>${tj.stgrouptunjangankaryawan_name}:</dt>
						<dd>Rp. ${formatCurrency(tj.sttunjangankaryawan_accumulate_value)}</dd>`;
				})
			}

			let parsePotongan = (d.payroll_deduction_setting) ? JSON.parse(d.payroll_deduction_setting) : [];
			let potongantxt = '';
			if (parsePotongan.length > 0) {
				parsePotongan.forEach(pt => {
					console.log('pt', pt)
					potongantxt += `<dt>${pt.stgrouppotongankaryawan_name}:</dt>
						<dd>Rp. ${formatCurrency(pt.stpotongankaryawan_accumulate_value)}</dd>`;
				})
			}
			let potongantotal = parseFloat(d.pph21.pph21_deduction_other);
			let biayajabatantotal = parseFloat(d.pph21.pph21_deduction_position);
			let biayajhttotal = parseFloat(d.pph21.pph21_deduction_jht);
			let biayajptotal = parseFloat(d.pph21.pph21_deduction_jp);

			let month = moment(d.pph21.pph21_period).format('MM');
			let pengurangan_txt = '';
			let perhitungan_pph21_txt = '';
			if (month == 12 || (karyawan.karyawan_contract_end && month == moment(karyawan.karyawan_contract_end).format('M'))) {
				// if(month == 12) {
				pengurangan_txt = `<div class="col-sm-4">
					<h5 class="text-bold"><u>Pengurangan</u></h5>
					<dl>
						<dt>Biaya Jabatan:</dt>
						<dd>Rp. ${formatCurrency(d.pph21.pph21_deduction_position)}</dd>
						<dt>Jaminan Hari Tua ${d.pph21.pph21_deduction_jht_rate}%:</dt>
						<dd>Rp. ${formatCurrency(d.pph21.pph21_deduction_jht)}</dd>
						<dt>Jaminan Pensiun ${d.pph21.pph21_deduction_jp_rate}%:</dt>
						<dd>Rp. ${formatCurrency(d.pph21.pph21_deduction_jp)}</dd>
						${potongantxt}
						<dt>Total:</dt>
						<dd>Rp. ${formatCurrency(potongantotal+biayajabatantotal+biayajhttotal+biayajptotal)}</dd>
					</dl>
				</div>`;

				let perhitungan_netto_txt = `<dt>Penghasilan Netto per Tahun:</dt>
					<dd>Rp. ${formatCurrency(d.pph21.pph21_netto_year)}</dd>`;

				if (d.pph21.pph21_prevcp_netto > 0) {
					perhitungan_netto_txt += `<dt>Penghasilan Netto Perusahaan Sebelumnya:</dt>
					<dd>Rp. ${formatCurrency(d.pph21.pph21_prevcp_netto)}</dd>`;
					pkptotal = parseFloat(d.pph21.pph21_prevcp_netto) + netto_pertahuntotal - d.pph21.pph21_ptkp;
				}

				perhitungan_pph21_txt = `<dt>Penghasilan Netto per Bulan:</dt>
					<dd>Rp. ${formatCurrency(d.pph21.pph21_netto_month)}</dd>
					${perhitungan_netto_txt}
					<dt>Penghasilan Tidak Kena Pajak (PTKP):</dt>
					<dd>Rp. ${formatCurrency(d.pph21.pph21_ptkp)}</dd>
					<dt>Penghasilan Kena Pajak (PKP):</dt>
					<dd>Rp. ${formatCurrency(pkptotal)}</dd>
					<dt>PPh Terutang Setahun</dt>
					<dd>Rp. ${formatCurrency(pph21tahuntotal)}</dd>`;

				if (d.pph21.pph21_prevcp_pph21_total > 0) {
					perhitungan_pph21_txt += `<dt>PPh Perusahaan Sebelumnya</dt>
					<dd>Rp. ${formatCurrency(d.pph21.pph21_prevcp_pph21_total)}</dd>`;
				}
			}

			let bpjskes_penghasilan_txt = '';
			let bpjstk_penghasil_txt = '';
			if (bpjskes == 1) {
				bpjskes_penghasilan_txt = `<dt>Jaminan Kesehatan ${d.pph21.pph21_allowance_jamkes_rate}%:</dt>
				<dd>Rp. ${formatCurrency(d.pph21.pph21_allowance_jamkes)}</dd>`;
			}
			if (bpjstk == 1) {
				bpjstk_penghasil_txt = `<dt>Jaminan Kecelakaan Kerja ${d.pph21.pph21_allowance_jkk_rate}%:</dt>
				<dd>Rp. ${formatCurrency(d.pph21.pph21_allowance_jkk)}</dd>
				<dt>Jaminan Kematian ${d.pph21.pph21_allowance_jkm_rate}%:</dt>
				<dd>Rp. ${formatCurrency(d.pph21.pph21_allowance_jkm)}</dd>`;
			}

			return (
				`<div class="row">
					<div class="col-sm-4">
						<h5 class="text-bold"><u>Penghasilan</u></h5>
						<dl>
							<dt>PTKP:</dt>
							<dd>${ptkp.ptkp_description}</dd>
							<dt>Gaji Pokok:</dt>
							<dd>Rp. ${formatCurrency(d.payroll_prorate_salary)}</dd>
							${tunjangantxt}
							${bpjskes_penghasilan_txt}
							${bpjstk_penghasil_txt}
							<dt>Penghasilan Bruto per Bulan:</dt>
							<dd>Rp. ${formatCurrency(d.pph21.pph21_bruto_month)}</dd>
						</dl>
					</div>
					${pengurangan_txt}
					<div class="col-sm-4">
						<h5 class="text-bold"><u>Perhitungan PPh 21</u></h5>
						<dl>
							<dt>Kategori TER</dt>
							<dd>${category_ter}</dd>
							<dt>Tarif TER (%)</dt>
							<dd>${rate_ter}</dd>
							${perhitungan_pph21_txt}
							<dt>PPh Terutang Perbulan</dt>
							<dd>Rp. ${formatCurrency(d.pph21.pph21_total_month)}</dd>
						</dl>
					</div>
				</div>`
			);
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
			window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
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

			if (pembetulan < 0) {
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
			window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
			$("#modalEspt").modal("hide");
		})

		$(".btn-buktipotong-a1").click(function(e) {
			e.preventDefault();
			Swal.fire({
				html: `<div id="pemotongan-karyawan">
					<div class="row mb-3">
						<label class="col-sm-5">Tgl. Pemotongan<span class="text-danger">*</span><span class="float-right">:</span></label>
						<div class="col-sm-7">
						<input class="form-control" name="pemotongan_tgl" id="pemotongan_tgl" placeholder="Tgl. Pemotongan" />
						</div>
					</div>
					</div>
					`,
				icon: 'question',
				didOpen: function() {
					$(".swal2-actions").css({
						// marginBottom: "11rem",
						zIndex: 0,
					})
					$("#pemotongan_tgl").daterangepicker({
						singleDatePicker: true,
						showDropdowns: true,
						parentEl: "#pemotongan-karyawan",
						locale: {
							format: 'DD-MM-YYYY'
						}
					});
				},
				preConfirm: () => {
					let tglPemotongan = $("#pemotongan_tgl").val();
					return tglPemotongan;
				}
			}).then((result) => {
				console.log('result', result)
				// result = result.value;
				if (result == undefined) {
					return false;
				}
				if (result.isConfirmed == false) {
					return false;
				}
				let t = $(this).attr("data-type");
				let format = $(this).attr("data-format");
				let params = `menu_id=${currentMenuId}&type=${format}&t=${t}`;
				let karyawanIds = $("#filterListEmployee").val();

				let tglPemotongan = result.value;
				console.log('tglPemotongan', tglPemotongan);
				if (!tglPemotongan) {
					toastr.error('Silahkan isi tglPemotongan');
					return false;
				}
				params += `&periodepotong=${tglPemotongan}`;

				if (filterPeriode) {
					params += `&periode=${filterPeriode}`;
				}
				if (karyawanIds) {
					karyawanIds.forEach(kr => {
						params += `&karyawan_ids[]=${kr}`;
					})
				}
				// alert(t+params);
				window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
			})
		})

		// $("#btn-buktipotong-bulan-pdf").click(function(e) {
		// 	e.preventDefault();
		// 	let params = `menu_id=${currentMenuId}&type=viii`;
		// 	let karyawanIds = $("#filterListEmployee").val();

		// 	if (filterPeriode) {
		// 		params += `&periode=${filterPeriode}`;
		// 	}
		// 	if (karyawanIds) {
		// 		karyawanIds.forEach(kr => {
		// 			params += `&karyawan_ids[]=${kr}`;
		// 		})
		// 	}
		// 	window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
		// })

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
			if (format == 'a1') {
				Swal.fire({
					html: `<div id="pemotongan-karyawan">
						<div class="row mb-3">
							<label class="col-sm-5">Tgl. Pemotongan<span class="text-danger">*</span><span class="float-right">:</span></label>
							<div class="col-sm-7">
							<input class="form-control" name="pemotongan_tgl" id="pemotongan_tgl" placeholder="Tgl. Pemotongan" />
							</div>
						</div>
						</div>
						`,
					icon: 'question',
					didOpen: function() {
						$(".swal2-actions").css({
							// marginBottom: "11rem",
							zIndex: 0,
						})
						$("#pemotongan_tgl").daterangepicker({
							singleDatePicker: true,
							showDropdowns: true,
							parentEl: "#pemotongan-karyawan",
							locale: {
								format: 'DD-MM-YYYY'
							}
						});
					},
					preConfirm: () => {
						let tglPemotongan = $("#pemotongan_tgl").val();
						return tglPemotongan;
					}
				}).then((result) => {
					console.log('result', result)
					// result = result.value;
					if (result == undefined) {
						return false;
					}
					if (result.isConfirmed == false) {
						return false;
					}

					let tglPemotongan = result.value;
					// console.log('tglPemotongan', tglPemotongan);
					if (!tglPemotongan) {
						toastr.error('Silahkan isi tglPemotongan');
						return false;
					}
					params += `&periodepotong=${tglPemotongan}`;
					window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
				})
			} else {
				window.open(`{{route('user.page.pajakpenghasilan.pph21.karyawan.export', '')}}?${params}`, '_blank');
			}

		})

		// Begin Impor Pembetulan PPh21
		$("#btn-import").click(function(e) {
			e.preventDefault();

			$("#importModal").modal("show");
		})

		$("#btn-import-file").click(function(e) {
			e.preventDefault();

			$("#importModal").modal("hide");
			$("#modalImporPembetulan").modal("show");
		})

		$("#closeImpor").click(function(e) {
			e.preventDefault();
			$("#importModal").modal("show");
			$("#modalImporPembetulan").modal("hide");
		})

		$("#btn-template").on("click", function() {
			$(".spinner-box").css({
				display: "table"
			});

			// Make the POST request to your API endpoint
			$.ajax({
				url: `{{ route('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.download-template') }}?menu_id=${currentMenuId}`, // Replace with your actual API endpoint URL
				method: "POST",
				headers: {
					"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') // Include the CSRF token in the request headers
				},
				// contentType: "application/json",
				dataType: 'json',
				data: {
					periode: filterPeriode,
					pembetulan: $("#filterPembetulanEmployee").val(),
				},
				success: function(response) {
					$(".spinner-box").fadeOut();
					console.log('response', response)
					if (!response.success) {
						toastr.error(response.message);
						return false;
					}
					setTimeout(function() {
						window.open(response.data.url, '_blank');

						window.close();
					}, 1000);

					// return
				},
				error: function(error) {
					$(".spinner-box").fadeOut();
					// Handle errors, if any
					console.error(error);
				},
			});
		})

		$('#importButton').click(function() {
			// Construct the FormData object here (replace this with your actual code)
			let input = document.getElementById('file_import');
			$(this).prop('disabled', true);
			$(this).css('background-color', 'gray');
			$(this).css('color', 'white');

			let formData = new FormData();
			// return false;
			if (input.files.length < 1) {
				toastr.error('Mohon masukan file dengan format xls');
				$('#importButton').prop('disabled', false);
				$('#importButton').css('background-color', '');
				$('#importButton').css('color', '');
				return false;
			}
			// Show the progress bar container
			$('.progress-container').show();
			setTimeout(function() {
				$(".progress-bar").css("width", "30%");
			}, 300);

			formData.append('_token', $("meta[name=csrf-token]").attr('content'));
			formData.append('file', input.files[0]);

			const csrfToken = $('meta[name="csrf-token"]').attr('content');

			setTimeout(function() {
				$(".progress-bar").css("width", "95%");
			}, 1200);

			// Simulate a 1.8-second delay to set the progress bar to 30%
			setTimeout(function() {
				$.ajax({
					type: 'POST', // or 'GET' depending on your controller action
					url: `{{ route("user.page.pajakpenghasilan.pph21.karyawan.pembetulan.import") }}?menu_id=${currentMenuId}`,
					data: formData,
					headers: {
						"X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
					},
					processData: false,
					contentType: false,
					success: function(response) {
						$('#importButton').prop('disabled', false);
						$('#importButton').css('background-color', '');
						$('#importButton').css('color', '');


						// Show SweetAlert success message
						// setTimeout(function() {
						if (response.success === false) {
							// Show SweetAlert for failure
							Swal.fire({
								icon: 'warning',
								title: 'Gagal Impor',
								html: (response.message) ? response.message : 'Cek file anda atau hubungi Customer Service',
								showCancelButton: false,
								confirmButtonText: 'Oke'
							});
							return false;
						}
						// else {
						$('#modalImporPembetulan').modal('hide');
						input.value = "";
						$('.progress-container').hide();

						// tblDetailImport.clear().draw();;

						// $.each(response.result, function(index, item) {
						//   tblDetailImport.row.add([
						//     item.row, // Row number
						//     item.karyawan_enid, // Id Karyawan
						//     item.karyawan_name, // Nama Karyawan
						//     item.status, // Status
						//     item.remark, // Remark
						//     // Add more columns as needed
						//   ]).draw(false); // 'false' means don't redraw the table until all rows are added
						// });

						// var id = response.history;

						// Find the button element by its ID
						// var button = document.getElementById("btn-unduh-rincian");

						// Set the onclick attribute with the id
						// button.onclick = function(e) {
						// 	e.preventDefault(); // Prevent the default behavior
						// 	generateHistory(id); // Call your function with the id
						// };

						// $('#detailImportModalLabel').text(`Rincian Impor : ${response.totalsuccess} dari ${response.totaldata} data berhasil di impor`);

						// $('#detailImportModal').on('shown.bs.modal', function() {
						//   // Clear the DataTable
						//   tblDetailImport.columns.adjust().draw();
						// });

						// $('#detailImportModal').modal('show');

						$('#historyModal').modal('show');

						tblHistory.draw()

						$(".progress-bar").css("width", "0%");
						// }
						// }, 100)


						// $(".progress-bar").css("width", "0%");
					},
					error: function(error) {
						$('#importButton').prop('disabled', false);
						$('#importButton').css('background-color', '');
						$('#importButton').css('color', '');
						Swal.fire({
							icon: 'warning',
							title: 'Gagal Impor',
							html: 'Cek file anda atau hubungi Customer Service',
							showCancelButton: false,
							confirmButtonText: 'Oke'
						});

						$(".progress-bar").css("width", "0%");
					}
				});
			}, 1800);
		})

		$(`#start-date-history`).daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: formattedFirstDay, // Set the calculated first day as the start date
			endDate: formattedFirstDay,
		});

		$(`#end-date-history`).daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: formattedLastDay, // Set the calculated first day as the start date
			endDate: formattedLastDay,
		});

		let tblHistory = $("#table-history").DataTable({
			"searching": false,
			"language": {
				// "searchPlaceholder": "Cari Nama Karyawan",
				// "emptyTable": "Tidak ada data"
			},
			"lengthChange": false,
			"processing": true,
			"serverSide": true,
			// "paging": true, // Enable pagination
			"lengthMenu": [10], // Set number of records to display per page
			"info": false,
			"ordering": false,
			"ajax": {
				"url": `{{ route('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.import-history') }}?menu_id=${currentMenuId}`, // Replace with your actual route to fetch data
				"type": "GET",
				"data": function(data) {
					// Add any additional data you want to pass to the server here
					data.start_date = $('#start-date-history').val() ? moment($('#start-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
					data.end_date = $('#end-date-history').val() ? moment($('#end-date-history').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : null;
					data.page = data.start / data.length + 1; // Calculate the current page based on start and length
					data.per_page = data.length; // Set the number of records per page
				},
			},
			"fnInitComplete": function() {
				// this.fnAdjustColumnSizing(true);
			},
			"autoWidth": true,
			"columns": [{
					"data": "historyimport_date",
					"title": "Tanggal Impor",
					"className": "text-center",
					"render": function(data, type, row) {
						if (data && (type === "display" || type === "filter")) {
							return data.substring(0, 19); // Display only the date part (YYYY-MM-DD)
						}
						return data; // For sorting and other purposes, return the original data as it is
					}
				},
				{
					"data": "historyimport_originfile_name",
					"title": "File Impor",
					"className": "text-center",
					"render": function(data, type, row) {
						return (data) ? data : row.historyimport_file_name;
					}
				},
				{
					"data": "userwajibpajak_name",
					"title": "Pengunggah",
					"className": "text-center",
				},
				{
					"data": "historyimport_detail_import",
					"title": "Status Impor",
					"className": "text-center",
					"render": function(data, type, row) {
						return (data) ? data : 'Sedang diproses'; //row.historyimport_status;
					}
				},
				{
					"data": "historyimport_id",
					"title": "Histori File",
					"className": "text-center",
					"render": function(data, type, row) {
						return '<a href="javascript:void(0);" onclick="generateHistory(' + data + ');">Unduh</a>';
					}
				},
			]
		})

		$("#btn-import-history").on("click", function(e) {
			// $(".spinner-box").css({
			//   display: "table"
			// });
			// tblHistory.draw();
			e.preventDefault();
			// $("#importModal").modal("hide");
			// $("#historyModal").modal("show");
			$("#importModal").modal("hide");
			$("#historyModal").modal("show");
			// $(".spinner-box").fadeOut();
		});
		$('#historyModal').on('shown.bs.modal', function() {
			// Clear the DataTable
			tblHistory.columns.adjust().draw();
		});
		$("#searchButtonHistory").on("click", function() {
			tblHistory.draw();
		});
		$("#resetButtonHistory").on("click", function() {
			$(`#start-date-history`).val(formattedFirstDay);
			$(`#end-date-history`).val(formattedLastDay);
			tblHistory.draw();
		});

		$("#closeHistory").click(function(e) {
			e.preventDefault();
			$("#importModal").modal("show");
			$("#historyModal").modal("hide");
			$(`#start-date-history`).val(formattedFirstDay);
			$(`#end-date-history`).val(formattedLastDay);
		})

		function generateHistory(id) {
			$(".spinner-box").css({
				display: "table"
			});
			const csrfToken = $('meta[name="csrf-token"]').attr('content');
			const requestData = {
				id: id
			};

			$.ajax({
				url: "{{ route('user.page.pajakpenghasilan.pph21.karyawan.pembetulan.generate-history') }}?menu_id={{request()->get('menu_id')}}", // Concatenate the 'id' to the URL
				method: "POST",
				headers: {
					"X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
				},
				contentType: "application/json",
				data: JSON.stringify(requestData),
				success: function(response) {
					setTimeout(function() {
						window.open(response, '_blank');
						// window.close();
						$(".spinner-box").fadeOut();
					}, 1500);
				},
				error: function(error) {
					// Handle errors, if any
					console.error(error);
				},
			});
		}
		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>