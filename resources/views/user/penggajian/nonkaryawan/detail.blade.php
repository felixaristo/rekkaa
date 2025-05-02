<?php
$menu_id = request()->get('menu_id');
$payroll = $karyawan->payroll;
// $pph21 = (count($payroll) > 0) ? $payroll->pph21 : null;
// dd($karyawan->jabatan);
$karyawan_id = $karyawan->karyawan_id;
$karyawan_nik = $karyawan->karyawan_nik;
$karyawan_enid = $karyawan->karyawan_enid;
$karyawan_npwp = $karyawan->karyawan_npwp;
$karyawan_name = $karyawan->karyawan_name;
$karyawan_email = $karyawan->karyawan_email;
$karyawan_method = $karyawan->karyawan_calculation_method;
$jabatan_name = ($karyawan->jabatan) ? $karyawan->jabatan->karyawanjabatan_name : '-';
$karyawan_ismultiple = $karyawan->karyawan_ismultiple;
$karyawan_active = $karyawan->karyawan_active;
$ptkp_description = $karyawan->ptkp->ptkp_description;
$objekpajak_description = ($karyawan->objekpajak) ? $karyawan->objekpajak->objekpajak_description : null;
$ptkp = $karyawan->ptkp;

$ptkpdet = [];
foreach ($ptkp_detail as $pdet) {
	if ($ptkp->ptkp_category == $pdet->ptkpdet_category) {
		array_push($ptkpdet, $pdet);
	}
}
$ptkp->ptkp_detail = $ptkpdet;
$periode = request()->get('periode');
?>
<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<div class="card">
			<!-- <div class="card-header row">
				<div class="col-sm-12">
					<h5 class="mb-0">Data</h5>
				</div>
			</div> -->
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">ID</label>
							<div class="col-sm-3 text-bold">
								{{$karyawan_enid}}
							</div>
							<label class="col-sm-3 nodot-label">Nama</label>
							<div class="col-sm-3 text-bold">
								{{$karyawan_name}} {!! ($karyawan_active=='0' ? '<span class="badge rounded-pill bg-danger mb-1 mr-1">Tidak Aktif</span>' : '') !!}
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">NIK</label>
							<div class="col-sm-3 text-bold">
								{{$karyawan_nik}}
							</div>
							<label class="col-sm-3 nodot-label">Jabatan</label>
							<div class="col-sm-3 text-bold">
								{{$jabatan_name}}
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">NPWP</label>
							<div class="col-sm-3 text-bold">
								{{$karyawan_npwp}}
							</div>
							<label class="col-sm-3 nodot-label">Status Perkawinan</label>
							<div class="col-sm-3 text-bold">
								{{$ptkp_description}}
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">Metode</label>
							<div class="col-sm-3 text-bold">
								{{str_replace('_', ' ', $karyawan_method)}}
							</div>
							<label class="col-sm-3 nodot-label">Periode</label>
							<div class="col-sm-3 text-bold">
								<?php
								$payroll_period = Carbon\Carbon::createFromFormat('!m-Y', $periode)->translatedFormat('F Y');
								?>
								{{$payroll_period}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<div class="card">
			<div class="card-header row">
				<div class="col-sm-3">
					<!-- <h5 class="mb-0">Data</h5> -->
				</div>
				<div class="col-sm-9 text-right">
					@if(in_array('CONFIRM_PAYSLIP', request()->get('permission_codes')))
					<a class="btn btn-sm btn-danger disabled" id="btn-konfirmasi" href="#">
						<i class='bx bx-check'></i>
						Konfirmasi
					</a>
					@endif
					@if(in_array('PAY_PAYSLIP', request()->get('permission_codes')))
					<a class="btn btn-sm btn-success disabled" id="btn-bayar" href="#">
						<i class='bx bx-money'></i>
						Bayar
					</a>
					@endif
					@if(in_array('CALCULATE_PAYSLIP', request()->get('permission_codes')))
					<a class="btn btn-sm btn-outline-warning" id="btn-tambah" href="#">
						<i class='bx bx-plus'></i>
						Tambah
					</a>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<div class="col-lg-12 mb-4 order-0">
							<div class="text-nowrap table-responsive">
								<table class="table" id="table-kalkulasi" style="width: 100%">
									<thead class="table-light">
										<tr>
											<th>
												<div class="form-check form-check-inline">
													<input name="karyawan_ischeck" class="form-check-input" type="checkbox" value="1" id="karyawan_ischeck">
												</div>
											</th>
											<th class="text-center">Tgl. Transaksi</th>
											<th class="text-center">Pendapatan Kotor</th>
											<th class="text-center">DPP</th>
											<th class="text-center">Kategori TER</th>
											<th class="text-center">Tarif TER (%)</th>
											<th class="text-center">Total Pajak</th>
											<th class="text-center">Status</th>
											<th class="text-center">Aksi</th>
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
		</div>
	</div>
</div>

<!-- Modal Kalkulasi -->
<div class="modal fade" id="modalKalkulasi" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Tambah Transaksi</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<form id="formKalkulasi" method="POST" action="{{route('user.page.penggajian.nonkaryawan.calculate', '')}}/{{$karyawan_id}}?menu_id={{$menu_id}}&periode={{$periode}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<input type="hidden" name="nonkaryawan_payroll_uuid" id="nonkaryawan_payroll_uuid">
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="nonkaryawan_tgltrx">Tgl. Transaksi</label>
							<div class="col-sm-7">
								<input type="text" required name="nonkaryawan_tgltrx" id="nonkaryawan_tgltrx" class="form-control" placeholder="Masukkan Tgl. Transaksi">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="nonkaryawan_pendapatankotor">Penghasilan Bruto</label>
							<div class="col-sm-7">
								<input type="text" required name="nonkaryawan_pendapatankotor" id="nonkaryawan_pendapatankotor" class="form-control" placeholder="Masukkan Pendapatan Kotor">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="nonkaryawan_pendapatanbersih">Pendapatan Netto (DPP)</label>
							<div class="col-sm-7">
								<input type="text" disabled name="nonkaryawan_pendapatanbersih" id="nonkaryawan_pendapatanbersih" class="form-control" value="0">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="kategori_ter">Kategori TER</label>
							<div class="col-sm-7">
								<input type="text" disabled name="kategori_ter" id="kategori_ter" class="form-control" value="{{$ptkp->ptkp_category}}">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="kategori_ter">Tarif TER</label>
							<div class="col-sm-7">
								<div class="input-group">
									<input class="form-control" value="0" disabled name="tarif_ter" id="tarif_ter" placeholder="Tarif TER">
									<span class="input-group-text">%</span>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5" for="nonkaryawan_pajak">PPh Terutang</label>
							<div class="col-sm-7">
								<input type="text" disabled name="nonkaryawan_pajak" id="nonkaryawan_pajak" class="form-control" value="0">
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
		let tarif21 = JSON.parse('<?php echo json_encode($tarif21)  ?>');
		let ptkp = JSON.parse('<?php echo json_encode($ptkp)  ?>');
		let tarif21Nonnpwp = JSON.parse('<?php echo json_encode($tarif21_nonnpwp) ?>');
		let karyawan_ismultiple = "{{$karyawan_ismultiple}}";
		let periode = "{{$periode}}";
		let isUpdated = false;

		let optionAutoNumeric = {
			digitGroupSeparator: '.',
			decimalCharacter: ',',
			currencySymbolPlacement: 'p',
			currencySymbol: 'Rp. ',
			minimumValue: 0,
			unformatOnSubmit: true,
			decimalPlaces: '0',
			modifyValueOnWheel: false,
		};

		let pendapatannettoSebelumnya = 0;
		let [penghasilanBruto, perhitunganTotalPPHTerutang] = new AutoNumeric.multiple(
			["#nonkaryawan_pendapatankotor", "#nonkaryawan_pajak"], optionAutoNumeric
		);
		let optionMinusAutoNumeric = {
			digitGroupSeparator: '.',
			decimalCharacter: ',',
			currencySymbolPlacement: 'p',
			currencySymbol: 'Rp. ',
			// minimumValue: 0,
			unformatOnSubmit: true,
			decimalPlaces: '0',
			modifyValueOnWheel: false,
		};
		let [penghasilanNetto] = new AutoNumeric.multiple(
			["#nonkaryawan_pendapatanbersih"], optionMinusAutoNumeric
		);

		let tblKalkulasi = $("#table-kalkulasi").DataTable({
			// "sDom": "l<'tahun-kontrak-box'>tipr",
			"pageLength": 100,
			// "lengthMenu": [ 12 ],
			"searching": false,
			"ordering": false,
			"paging": false,
			"info": false,
			"cache": false,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"searchDelay": 1050,
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "{{route('user.page.penggajian.nonkaryawan.datatable_detail', ['menu_id' => request()->get('menu_id')])}}",
				"type": "GET",
				"data": function(data) {
					data.periode = "{{$periode}}";
					data.karyawan_id = "{{$karyawan_id}}";
				}
			},
			// "fnInitComplete": function() {
			// 	this.fnAdjustColumnSizing(true);
			// 	// $(this).find(".cetak-registrasi").select2();
			// },
			"drawCallback": function(settings) {
				let api = this.api();
				// console.log(settings.json);
			},
			"autoWidth": true,
			"columnDefs": [{
				target: [3, 4],
				width: 30
			}, {
				target: [0, 3, 4, 5],
				className: 'text-center'
			}, {
				target: [1, 2, 6],
				className: 'text-right'
			}],
			"columns": [{
					"data": "payroll",
					"render": function(data, type, row) {
						return `<div class="form-check form-check-inline">
							<input class="form-check-input karyawan_checked" type="checkbox"  value="1">
						</div>`;
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						if (data)
							return moment(data.pph21_trx_at).format('DD-MM-YYYY');

						return '';
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						if (data)
							return formatCurrency(data.pph21_bruto_month);
						return '';
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						if (data)
							return (data.pph21_dpp) ? formatCurrency(data.pph21_dpp) : 0;
						return '';
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						let tarif21lbl = data.pph21_ptkp_category;

						return tarif21lbl;
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						let tarif21lbl = data.pph21_ptkpdet_rate_percentage;

						return tarif21lbl;
					}
				},
				{
					"data": "pph21",
					"render": function(data, type, row) {
						if (data)
							return formatCurrency(data.pph21_total_month);
						return '';
					}
				},
				{
					"data": "payroll_status",
					"render": function(data, type, row) {
						if (data == 1) {
							status = `<span class="badge bg-info">Menunggu Konfirmasi</span>`;
						} else if (data == 2) {
							status = `<span class="badge bg-danger">Menunggu Pembayaran</span>`;
						} else if (data == 3) {
							status = `<span class="badge bg-success">Dibayarkan</span>`;
						}
						return status;
					}
				},
				{
					"data": "payroll_id",
					"render": function(data, type, row) {
						let payroll_status = row.payroll_status;
						let btn = '';
						if (row.payroll_lock == 0) {
							<?php if (in_array('U', request()->get('permission_codes'))) : ?>
								btn += `<a class="dropdown-item btn-edit" href="javascript:void(0);"
						><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
						>`
							<?php endif; ?>
							<?php if (in_array('SD', request()->get('permission_codes'))) : ?>
								btn += `<a class="dropdown-item btn-delete" href="javascript:void(0);"
						><i class="bx bx-trash me-1 text-danger"></i> Delete</a
						>`;
							<?php endif; ?>
						}

						if (payroll_status == 1) {
							<?php if (in_array('CONFIRM_PAYSLIP', request()->get('permission_codes'))) : ?>
								btn += ` <a href="#" class="dropdown-item btn-konfirmasi"><i class="bx bx-check text-danger"></i> Konfirmasi</a>`
							<?php endif; ?>
						} else if (payroll_status == 2) {
							<?php if (in_array('PAY_PAYSLIP', request()->get('permission_codes'))) : ?>
								btn += ` <a href="#" class="dropdown-item btn-bayar"><i class="bx bx-money text-success"></i> Bayar</a>`;
							<?php endif; ?>
						} else if (payroll_status == 3) {
							<?php if (in_array('DOWNLOAD_PAYSLIP', request()->get('permission_codes'))) : ?>
								btn += ` <a href="#" class="dropdown-item btn-cetakslip"><i class="bx bxs-file-pdf text-info"></i> Lihat Slip</a>`;
							<?php endif; ?>
						}
						return `
						<div class="dropdown">
							<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
								<i class="bx bx-dots-vertical-rounded"></i>
							</button>
							<div class="dropdown-menu">
								${btn}
							</div>
						</div>
						`;
					}
				},
			]
		});

		let kalkulasiData = [];
		let bayarData = [];
		let kirimData = [];
		let finishData = [];
		$("#table-kalkulasi").on("click", ".karyawan_checked", function(e) {
			let ischecked = $(this).is(':checked');
			let row = $(this).closest('tr');

			console.log('ischecked', ischecked);
			console.log('row', row);
			if (ischecked == false)
				deselectData(ischecked, row);
			else
				selectData(row);
		});
		$('#karyawan_ischeck').on('click', function() {
			if ($('#karyawan_ischeck').is(':checked')) {

				$(".karyawan_checked").prop("checked", true).trigger("change");
				selectData();
			} else {
				deselectData();
				$(".karyawan_checked").prop("checked", false).trigger("change");
			}
		});

		function enableBtn() {
			console.log('kalkulasiData', kalkulasiData);
			console.log('bayarData', bayarData);
			console.log('finishData', finishData);
			
			$("#btn-konfirmasi, #btn-bayar").addClass('disabled');
			if (kalkulasiData.length > 0 && bayarData.length <= 0 && finishData.length <= 0)
				$("#btn-konfirmasi").removeClass('disabled');
			if (bayarData.length > 0 && kalkulasiData.length <= 0 && finishData.length <= 0)
				$("#btn-bayar").removeClass('disabled');

		}

		function selectData(row = null) {
			// console.log('row', row);
			if (row) {
				tblKalkulasi.row(row).select();
				let selecteddata = tblKalkulasi.row(row).data();
				// console.log('singledata', selecteddata);
				let payroll = selecteddata;

				if (payroll.payroll_status == 1)
					kalkulasiData.push(payroll.payroll_uuid);
				if (payroll.payroll_status == 2)
					bayarData.push(payroll.payroll_uuid);
				if (payroll.payroll_status == 3)
					finishData.push(payroll.payroll_uuid);
			} else {
				tblKalkulasi.rows().select();

				let selecteddata = tblKalkulasi.rows('.selected').data();
				// console.log('selecteddata', selecteddata);
				for (let i = 0; i < selecteddata.length; i++) {
					let data = selecteddata[i];
					let payroll = data;

					if (payroll.payroll_status == 1)
						kalkulasiData.push(payroll.payroll_uuid);
					if (payroll.payroll_status == 2)
						bayarData.push(payroll.payroll_uuid);
					if (payroll.payroll_status == 3)
						finishData.push(payroll.payroll_uuid);
				}
			}
			//   console.log(kalkulasiData, kirimData, bayarData);
			enableBtn();
		}

		function deselectData(ischecked = false, row = null) {
			if (ischecked == false && row) {
				// BUG HERE
				tblKalkulasi.row(row).deselect();
				let singledata = tblKalkulasi.row(row).data();
				let payroll = singledata;
				// console.log('payrolls', payrolls);
				// payrolls.forEach(py => {
				let kalkulasiidx = kalkulasiData.indexOf(payroll.payroll_uuid);
				let bayaridx = bayarData.indexOf(payroll.payroll_uuid);
				let finishidx = finishData.indexOf(payroll.payroll_uuid);

				if (kalkulasiidx > -1)
					kalkulasiData.splice(kalkulasiidx, 1);
				if (bayaridx > -1)
					bayarData.splice(bayaridx, 1);
				if (finishidx > -1)
					finishData.splice(finishidx, 1);

			} else {
				$('#karyawan_ischeck').prop("checked", false).trigger("change");
				tblKalkulasi.rows().deselect();
				kalkulasiData = [];
				bayarData = [];
				kirimData = [];
				finishData = [];
			}
			console.log(kalkulasiData, kirimData, bayarData, finishData);
			enableBtn();
		}

		$("#btn-tambah").click(function(e) {
			e.preventDefault();

			$("#modalKalkulasi").modal("show");
		});

		// console.log(moment(periode, 'MM-YYYY').format('MM-YYYY'))
		// console.log('01-' + moment(periode, 'MM-YYYY').format('MM-YYYY'))
		// console.log('30-' + moment(periode, 'MM-YYYY').format('MM-YYYY'))
		// console.log('periode', periode)
		let minDate = '01-' + moment(periode, 'MM-YYYY').format('MM-YYYY');
		// console.log('minDate',minDate);
		let maxDate = moment(periode, 'MM-YYYY').daysInMonth() + '-' + moment(periode, 'MM-YYYY').format('MM-YYYY');
		// console.log('maxDate',maxDate);
		$("#nonkaryawan_tgltrx").daterangepicker({
			singleDatePicker: true,
			// showDropdowns: true,
			//   timePicker: true,
			// maxDate: moment().format('DD-MM-YYYY'),
			minDate: minDate, //+moment(periode, 'MM-YYYY').format('MM-YYYY'),
			maxDate: maxDate, //+moment(periode, 'MM-YYYY').format('MM-YYYYY'),
			locale: {
				format: 'DD-MM-YYYY'
			}
		})
		$("#nonkaryawan_tgltrx").data('daterangepicker').setStartDate(minDate);
		$("#nonkaryawan_tgltrx").data('daterangepicker').setEndDate(minDate);

		$("#nonkaryawan_pendapatankotor").keyup(function(e) {
			e.preventDefault();
			// penghasilan
			let penghasilanNettoTotal = penghasilanBruto.getNumber() * 50 / 100;

			penghasilanNetto.set(penghasilanNettoTotal);
			perhitunganTotal();
		})

		let currentMenuId = "{{request()->get('menu_id')}}";
		let actionStorePenggajianUrl = `{{route('user.page.penggajian.nonkaryawan.calculate', '')}}/{{$karyawan_id}}?menu_id=${currentMenuId}periode={{$periode}}`;
		// let actionUpdatePenggajianpUrl = "{{route('user.page.penggajian.nonkaryawan.update', '')}}";
		let actionDeletePenggajianUrl = "{{route('user.page.penggajian.nonkaryawan.delete', '')}}";
		let isEdit = false;
		$("#table-kalkulasi").on("click", ".btn-delete", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblKalkulasi.row(row).data();
			Swal.fire({
				html: 'Apakah anda ingin menghapus penggajian dengan tanggal transaksi <b>' + data.payroll_trx_at + '</b>?',
				icon: 'question',
				preConfirm: () => {
					Swal.showLoading();
					// tblPengaturanTunjangan.row(row).remove();
					// return true;
					return fetch(`${actionDeletePenggajianUrl}/${data.payroll_uuid}?menu_id=${currentMenuId}`, {
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
				tblKalkulasi.draw();
			});
		})

		$("#table-kalkulasi").on("click", ".btn-edit", function(e) {
			e.preventDefault();
			$("#formKalkulasi [type=reset]").click();
			isEdit = true;
			// get row
			let row = $(this).closest('tr');
			let data = tblKalkulasi.row(row).data();
			let pph21 = data.pph21;
			$("#nonkaryawan_payroll_uuid").val(data.payroll_uuid);
			$("#nonkaryawan_tgltrx").val(moment(data.payroll_trx_at).format('DD-MM-YYYY'));
			$("#kategori_ter").val(pph21.pph21_ptkp_category);
			$("#tarif_ter").val(pph21.pph21_ptkpdet_rate_percentage);
			penghasilanNetto.set(pph21.pph21_dpp);
			penghasilanBruto.set(data.payroll_prorate_salary);
			perhitunganTotalPPHTerutang.set(data.payroll_deduction_pph21);

			$("#modalKalkulasi").modal("show");
		})

		function perhitunganTotal() {
			let npwpNilai = "{{$karyawan_npwp != '00.000.000.0-000.000' ? 'NPWP' : 'NO-NPWP'}}";
			// npwpNilai = (npwpNilai == 'NO-NPWP') ? tarif21Nonnpwp.tarifnonnpwp_rate : 100;
			npwpNilai = 100;
			let metodePajak = "{{$karyawan_method}}";
			// let metodePajak = "GROSS";
			let totalPKP = penghasilanBruto.getNumber();

			let ptkpCategoryRate = 0;
			let ptkpCategoryRateMonth = 0;
			if (ptkp && ptkp.ptkp_detail) {
				let ptkpdet = ptkp.ptkp_detail;
				for (let i = 0; i < ptkpdet.length; i++) {
					if (totalPKP <= ptkpdet[i].ptkpdet_rate_month) {
						ptkpCategoryRate = ptkpdet[i].ptkpdet_rate_percentage;
						ptkpCategoryRateMonth = ptkpdet[i].ptkpdet_rate_month;
						break;
					}
				}
			}
			$("#tarif_ter").val(ptkpCategoryRate);
			let totalPPHTerutang = 0;
			if (totalPKP > 0) {
				if (metodePajak == 'GROSS' || metodePajak == 'NETT') {
					totalPKP = penghasilanBruto.getNumber();
				} else {
					let dpp = penghasilanNetto.getNumber();
					let tarif21Rates = getTarif21(tarif21, dpp);
					let dppRate = (tarif21Rates.length > 0) ? tarif21Rates[tarif21Rates.length - 1].tarif21_rate : 0;
					let diffGrossUp = 100 - (50 * dppRate / 100);
					let totalDPPGrossUp = dpp * 100 / diffGrossUp; // dpp
					totalPKP = totalDPPGrossUp * 100 / 50;

					penghasilanNetto.set(totalDPPGrossUp);
				}
			}
			ptkpCategoryRate = (ptkpCategoryRate) ? ptkpCategoryRate : 34; // 34 is hardcode the highest

			totalPPHTerutang = Math.floor((npwpNilai / 100) * totalPKP * 50 / 100 * ptkpCategoryRate / 100);
			perhitunganTotalPPHTerutang.set(totalPPHTerutang);
		}

		let formKalkulasi = $("#formKalkulasi").validate({
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
						_token: $("meta[name=csrf-token]").attr('content')
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
								confirmButtonText: "Ok",
								showCancelButton: false,
								icon: 'error',
								html: errorName
							})
						}
					},
					success: function(response) {
						console.log(response, 'response')
						$(".spinner-box").fadeOut();
						if (!response.success) {
							toastr.error(response.message);
							return false;
						}

						$("#formKalkulasi [type=reset]").click();
						toastr.success(response.message);
						tblKalkulasi.draw();

						// change url
						// $("#formKalkulasi").attr("action", actionStorePenggajianUrl+`?menu_id=${currentMenuId}`);
					}
				})
			},
		})

		$("#modalKalkulasi").on("hidden.bs.modal", function(e) {
			$("#formKalkulasi [type=reset]").click();
		})
		$("#formKalkulasi [type=reset]").click(function(e) {
			e.preventDefault();
			// change url
			// $("#formKalkulasi").attr("action", actionStorePenggajianUrl+`?menu_id=${currentMenuId}`);
			isEdit = false;
			penghasilanNetto.set(0);
			penghasilanBruto.set(0);
			perhitunganTotalPPHTerutang.set(0);
			$("#nonkaryawan_tgltrx").val(minDate);
			$("#nonkaryawan_payroll_uuid").val(null);
			tblKalkulasi.draw();

			$("#modalKalkulasi").modal("hide");
		})
		// konfirmasi
		$("#btn-konfirmasi").click(function(e) {
			e.preventDefault();
			// console.log('kalkulasiData', kalkulasiData);
			msg = `Apakah anda ingin mengkonfirmasi perhitungan penggajian karyawan?
      		<br><span class="text-danger">*Perhitungan akan di <b>lock</b> dan tidak bisa di kalkulasi ulang.</span>`;
			processPayslipNonKaryawan(`{{route('user.page.penggajian.nonkaryawan.confirmation')}}?menu_id=${currentMenuId}`, msg);
		})
		// bayar
		$("#btn-bayar").click(function(e) {
			e.preventDefault();
			// console.log('kalkulasiData', kalkulasiData);
			msg = `Apakah anda ingin melakukan pembayaran karyawan?`;
			processPayslipNonKaryawan(`{{route('user.page.penggajian.nonkaryawan.paid')}}?menu_id=${currentMenuId}`, msg);
		})
		$("#table-kalkulasi").on("click", ".btn-konfirmasi", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblKalkulasi.row(row).data();
			kalkulasiData = [data.payroll_uuid];
			msg = `Apakah anda ingin mengkonfirmasi perhitungan penggajian karyawan?
      <br><span class="text-danger">*Perhitungan akan di <b>lock</b> dan tidak bisa di kalkulasi ulang.</span>`;
			processPayslipNonKaryawan(`{{route('user.page.penggajian.nonkaryawan.confirmation')}}?menu_id=${currentMenuId}`, msg);
		})

		$("#table-kalkulasi").on("click", ".btn-bayar", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblKalkulasi.row(row).data();
			bayarData = [data.payroll_uuid];
			msg = `Apakah anda ingin melakukan pembayaran karyawan?`;
			processPayslipNonKaryawan(`{{route('user.page.penggajian.nonkaryawan.paid')}}?menu_id=${currentMenuId}`, msg);
		})

		$("#table-kalkulasi").on("click", ".btn-cetakslip", function(e) {
			e.preventDefault();
			let row = $(this).closest('tr');
			let data = tblKalkulasi.row(row).data();
			// console.log('data', data)
			let payroll_uuid = data.payroll_uuid;
			window.open(`{{route('user.page.penggajian.nonkaryawan.cetak', '')}}/` + payroll_uuid + '?menu_id=' + currentMenuId, '_blank');
		});

		function processPayslipNonKaryawan(url = '#', msg = '') {
			Swal.fire({
				html: msg,
				icon: 'question',
				preConfirm: () => {
					Swal.showLoading();
					// tblPengaturanPotongan.row(row).remove();
					// return true;
					return fetch(`${url}`, {
							method: 'POST',
							body: new URLSearchParams($.param({
								_token: $("meta[name=csrf-token]").attr('content'),
								payroll_uuids: kalkulasiData.concat(bayarData, kirimData),
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
					let data = result.data;
					let infokaryawan = "<br><br>";
					if (data != undefined) {
						let karyawan = data.karyawan;
						karyawan.forEach(kr => {
							infokaryawan += `<li class="text-bold">${kr.name} (${kr.enid})</li>`;
						});
					}
					Swal.fire({
						html: result.message + infokaryawan,
						showCancelButton: false,
						confirmButtonText: "Ok",
						icon: 'error'
					})
					return false;
				}

				toastr.success(result.message);
				// deselectData();
				kalkulasiData = [];
				bayarData = [];
				kirimData = [];
				finishData = [];

				tblKalkulasi.draw();

				$(".karyawan_checked").prop("checked", false).trigger("change");
				$("#karyawan_ischeck").prop("checked", false).trigger("change");
				deselectData();
			});
		}
	})
</script>