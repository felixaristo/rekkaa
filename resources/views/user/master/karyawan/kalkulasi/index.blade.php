	<style>
	.tahun-kontrak-box {
		width: 200px;
	}
	</style>
	<?php 
	$karyawanmasakerja_status = $karyawanmasakerja->karyawanmasakerja_status;
	if($karyawanmasakerja->karyawanmasakerja_status == 'NONKARYAWAN') {
		$karyawanmasakerja_status = 'Bukan Karyawan';
	}
	$contract_begin = \Carbon\Carbon::parse($karyawanmasakerja->karyawanmasakerja_contract_begin);
	$contract_begin_dt = $contract_begin->format('d-m-Y');
	$contract_end = (!$karyawanmasakerja->karyawanmasakerja_contract_end) ? null : \Carbon\Carbon::parse($karyawanmasakerja->karyawanmasakerja_contract_end);
	$contract_end_dt = (!$karyawanmasakerja->karyawanmasakerja_contract_end) ? 'Sekarang' : $contract_end->format('d-m-Y');
	?>
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
				<a href="{{route('user.page.karyawan.index')}}" class="rekkaa-page-link">Karyawan</a>
				</li>
				<li class="breadcrumb-item active">Kalkulasi</li>
			</ol>
			</nav>
		</div>
		<div class="card-body">
			<div class="col-sm-12 mb-5">
			<div class="card">
				<div class="card-body">
				<div class="form-group row mb-3">
					<label for="karyawan_nik" class="col-sm-2 lbl-req">NIK<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawan_nik}}
					</div>
					<label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawan_npwp}}
					</div>
				</div>
				<div class="form-group row mb-3">
					<label for="karyawan_name" class="col-sm-2 lbl-req">Nama<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawan_name}}
					</div>
					<label for="karyawan_email" class="col-sm-2 lbl-req">Email<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawan_email}}
					</div>
				</div>
				<div class="form-group row mb-3">
					<label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja_status}}
					</div>
					<label for="karyawan_metode" class="col-sm-2 lbl-req">Metode Perhitungan<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{ucwords(strtolower(str_replace('_', ' ', $karyawanmasakerja->karyawanmasakerja_calculation_method)))}}
					</div>
				</div>
				<div class="form-group row mb-3">
					<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Kontrak<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$contract_begin_dt}} / {{$contract_end_dt}}
					</div>
					<label for="karyawan_ptkp" class="col-sm-2 lbl-req">Status Perkawinan<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->ptkp_description}}
					</div>
				</div>
				@if($karyawanmasakerja->karyawanmasakerja_active == '0')
				<div class="form-group row mb-3">
					<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Alasan (Non Aktif)<span class="float-right">:</span></label>
					<div class="col-sm-4">
					<span class="badge rounded-pill bg-label-warning">{{$karyawanmasakerja->karyawanmasakerja_end_type}}</span>
					</div>
					<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Keterangan (Non Aktif)<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawanmasakerja_end_reason}}
					</div>
				</div>
				@endif
				</div>
			</div>
			</div>
			<div class="col-sm-12">
			<div class="text-nowrap table-responsive">
				<table class="table" id="table-kalkulasi" style="width: 100%">
				<thead class="table-light">
					<tr>
					<th class="text-center">Bulan</th>
					<th class="text-center">Gaji Pokok</th>
					<th class="text-center">Total Tunjangan</th>
					<th class="text-center">PPh 21</th>
					<th class="text-center">Metode</th>
					<th class="text-center">Status</th>
					<th class="text-center">Actions</th>
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

	<script>
	$(function() {
		let tahunKontrakAwal = moment("<?php echo $karyawanmasakerja->karyawanmasakerja_contract_begin ?>").format('YYYY');
		let tahunKontrakAkhir = moment("<?php echo ($karyawanmasakerja->karyawanmasakerja_contract_end) ? $karyawanmasakerja->karyawanmasakerja_contract_end : now() ?>").format('YYYY');
		let tblKalkulasi = $("#table-kalkulasi").DataTable({
		"sDom": "l<'tahun-kontrak-box'>tipr",
		"pageLength": 12,
		"lengthMenu": [ 12 ],
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
			"url": "<?php echo url('user/karyawan/'.request()->segment(3).'/kalkulasi/datatable') ?>",
			"type": "GET",
			"data": function(data) {
				data.tahun = ($("#table-kalkulasi_wrapper #tahun-kontrak-select").val()) ? $("#table-kalkulasi_wrapper #tahun-kontrak-select").val() : tahunKontrakAkhir;
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
			target: [0,4,5,6],
			className: 'text-center'
		}, {
			target: [1,2,3],
			className: 'text-right'
		}],
		"columns": [
			{
				"data": "karyawankalkulasi_month",
				"render": function(data, type, row) {
				// console.log('bulan(data)', bulan(data))
				let selectedBulan = bulan(data);
				return selectedBulan.text;
				}
			},
			{
				"data": "karyawankalkulasi_salary",
				"render": function(data, type, row) {
				return formatCurrency(data);
				}
			},
			{
				"data": "karyawankalkulasi_data",
				"render": function(data, type, row) {
				let parseData = (data) ? JSON.parse(data) : null;
				if(parseData) {
					return formatCurrency(parseData.nominal_tunjangan_lain);
				} 
				return 0;
				}
			},
			{
				"data": "karyawankalkulasi_data",
				"render": function(data, type, row) {
				let parseData = (data) ? JSON.parse(data) : null;
				if(parseData) {
					return formatCurrency(parseData.total_pph_terutang_perbulan);
				} 
				return 0;
				}
			},
			{
				"data": "karyawankalkulasi_method",
				"render": function(data, type, row) {
				return (data) ? (data.replace('_', ' ')).toUpperCase() : '';
				}
			},
			{
				"data": "karyawankalkulasi_lock",
				"render": function(data, type, row) {
				return (data) ? `<i class="bx bx-lock-alt text-primary" title="Lock"></i>` : `<i class="bx bx-lock-open-alt text-default" title="Unlock"></i>`;
				}
			},
			{
				"data": "karyawankalkulasi_id",
				"render": function(data, type, row) {
				let lockHtml = '';
				if(row.karyawankalkulasi_active == '1') {
					if(!row.karyawankalkulasi_lock) {
					lockHtml = `<a class="dropdown-item btn-kalkulasi-lock" href="#"
						><i class="bx bx-lock-alt me-1 text-primary"></i> Lock Kalkulasi</a>`;
					} else {
					lockHtml = `<a class="dropdown-item btn-kalkulasi-lock" href="#"
						><i class="bx bx-lock-open-alt me-1 text-default"></i> Unlock Kalkulasi</a>`;
					}
					return `
					<div class="dropdown">
					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
						<i class="bx bx-dots-vertical-rounded"></i>
					</button>
					<div class="dropdown-menu">
						${lockHtml}
						<a class="dropdown-item rekkaa-page-link" href="<?php echo url('user/karyawan/'.request()->segment(3).'/kalkulasi/detail') ?>/${data}"
						><i class='bx bx-detail text-info'></i> Detail</a
						>
					</div>
					</div>
					`;
				} else {
					if(row.karyawankalkulasi_lock) {
					return `<div class="dropdown">
					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
						<i class="bx bx-dots-vertical-rounded"></i>
					</button>
					<div class="dropdown-menu">
					<a class="dropdown-item rekkaa-page-link" href="<?php echo url('user/karyawan/'.request()->segment(3).'/kalkulasi/detail') ?>/${data}"
						><i class='bx bx-detail text-info'></i> Detail</a
						>
					</div>
					</div>`
					}
					return '';
				}
				}
			},
		]
		});

		$("#table-kalkulasi_wrapper .tahun-kontrak-box").addClass('float-right').append('Tahun: <select class="form-control" data-placeholder="-:Pilih Tahun:-" id="tahun-kontrak-select" style="width:75%"></select>');

		
		let tahunData = [];
		for(let i=tahunKontrakAwal; i<=tahunKontrakAkhir; i++) {
		tahunData.push({
			id: i,
			text: i,
		});
		}
		
		// console.log('tahunData', tahunData)
		$("#table-kalkulasi_wrapper #tahun-kontrak-select").select2({
		data: tahunData
		}).on("select2:select", function(e) {
		let data = e.params.data;
		tblKalkulasi.draw();
		});
		$("#table-kalkulasi_wrapper #tahun-kontrak-select").val(tahunKontrakAkhir).trigger('change');

		$("#table-kalkulasi").on("click", ".btn-kalkulasi-lock", function(e) {
		e.preventDefault();
		let row = $(this).closest('tr');
		let data = tblKalkulasi.row(row).data();
		let parseKalkulasiData = JSON.parse(data.karyawankalkulasi_data);
		// console.log('data', data)
		let method = (data.karyawankalkulasi_method) ? (data.karyawankalkulasi_method.replace('_', ' ')).toUpperCase() : ''
		let title = 'lock';
		let helpinfo = ''
		if(data.karyawankalkulasi_lock) {
			title = 'me-unlock';
			helpinfo = '<p class="text-danger">PPh 21 akan diakumulasi ulang!</p>';
		}
		Swal.fire({
			html: `Apakah anda ingin ${title} kalkulasi ini?<br>
			${helpinfo}
			<div class="row" style="width:100%; margin:auto; line-height: 25px;">
				<div class="col-sm-5 text-left">
				Bulan:
				</div>
				<div class="col-sm-7 text-right">
				<b>${bulan(data.karyawankalkulasi_month).text}</b>
				</div>
				<div class="col-sm-5 text-left">
				Tahun:
				</div>
				<div class="col-sm-7 text-right">
				<b>${data.karyawankalkulasi_year}</b>
				</div>
				<div class="col-sm-5 text-left">
				Metode:
				</div>
				<div class="col-sm-7 text-right">
				<b>${method}</b>
				</div>
				<div class="col-sm-5 text-left">
				Gaji Pokok:
				</div>
				<div class="col-sm-7 text-right">
				<b>Rp. ${formatCurrency(data.karyawankalkulasi_salary)}</b>
				</div>
				<div class="col-sm-5 text-left">
				Tunjangan:
				</div>
				<div class="col-sm-7 text-right">
				<b>Rp. ${formatCurrency(parseKalkulasiData.nominal_tunjangan_lain)}</b>
				</div>
				<div class="col-sm-5 text-left">
				PPH 21:
				</div>
				<div class="col-sm-7 text-right">
				<b>Rp. ${formatCurrency(data.karyawankalkulasi_pph21)}</b>
				</div>
			</div>
			
			`,
			icon: 'question',
			preConfirm: () => {
				Swal.showLoading();
				// tblPengaturanTunjangan.row(row).remove();
				// return true;
				return fetch(`<?php echo url('user/karyawan/'.request()->segment(3).'/kalkulasi/lock') ?>`, {
					method: 'POST',
					body: new URLSearchParams($.param({_token: $("meta[name=csrf-token]").attr('content'), karyawankalkulasi_id: data.karyawankalkulasi_id}))
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

				Swal.fire({
					title: result.message,
					type: 'error'
				})
				return false;
			}

			toastr.success(result.message);
			tblKalkulasi.draw();
		});
		})

		// set meta title
		setHtmlTitle('{{$title}}')
	})
	
	</script>