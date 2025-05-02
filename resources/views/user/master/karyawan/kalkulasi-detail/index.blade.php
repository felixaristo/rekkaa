<?php 
	$tunjangan = ($kalkulasi->karyawankalkulasi_tunjangan) ? $kalkulasi->karyawankalkulasi_tunjangan : [];
	$tunjangan_nominal = 0;
	foreach($tunjangan as $key => $val) {
		$tunjangan_nominal += $val;
	}
	// dd($kalkulasi);
	$karyawankalkulasi_bpjs = $kalkulasi->karyawankalkulasi_bpjs;
	$karyawan_bpjs = bpjsKaryawan($karyawankalkulasi_bpjs, $kalkulasi->karyawankalkulasi_salary);
	// dd($kalkulasi->karyawankalkulasi_tunjanganjabatan);
	$tunjangan_jabatan = ($kalkulasi->karyawankalkulasi_lock) ? json_decode($kalkulasi->karyawankalkulasi_tunjanganjabatan) : $tunjangan_jabatan;

	$perhitungan = perhitunganTotalPPH21($bpjsrate, $ptkp, $karyawan_bpjs, $kalkulasi->karyawankalkulasi_salary, 
		[
			'tunjangan_jabatan' => $tunjangan_jabatan,
			'tunjangan_nominal' => $tunjangan_nominal
		], $kalkulasi->karyawankalkulasi_method
	);
	$karyawan_status = $kalkulasi->karyawankalkulasi_status;
	if($kalkulasi->karyawankalkulasi_status == 'NONKARYAWAN') {
		$karyawan_status = 'Bukan Karyawan';
	}
	$contract_begin = \Carbon\Carbon::parse($kalkulasi->karyawankalkulasi_contract_begin);
	$contract_begin_dt = $contract_begin->format('d-m-Y');
	$contract_end = ($kalkulasi->karyawan_contract_end) ? null : \Carbon\Carbon::parse($kalkulasi->karyawankalkulasi_contract_end);
	$contract_end_dt = (!$contract_end) ? 'Sekarang' : $contract_end->format('d-m-Y');
	$month = (strlen($kalkulasi->karyawankalkulasi_month) > 1) ? $kalkulasi->karyawankalkulasi_month : '0'.$kalkulasi->karyawankalkulasi_month;
	$iperiode_dt = \Carbon\Carbon::parse($kalkulasi->karyawankalkulasi_year.'-'.$month.'-01');
	$iperiode = $iperiode_dt->translatedFormat('F Y');

	// dd($ptkp);
?>
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<!-- Basic Layout & Basic with Icons -->
			<div class="row">
				<div class="col-xxl">
					<div class="card-header d-flex align-items-center justify-content-between">
					<h5 class="mb-0">{{$title}}</h5>
					<!-- <small class="text-muted float-end">Merged input group</small> -->
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="{{route('user.page.karyawan.index')}}" class="rekkaa-page-link">Karyawan</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{route('user.page.karyawan-kalkulasi.index', ['karyawanmasakerjaId' => request()->segment(3)])}}" class="rekkaa-page-link">Kalkulasi</a>
						</li>
						<li class="breadcrumb-item active">Detail Kalkulasi Pajak</li>
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
										{{$kalkulasi->karyawan_nik}}
										</div>
										<label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$kalkulasi->karyawan_npwp}}
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_name" class="col-sm-2 lbl-req">Nama<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$kalkulasi->karyawan_name}}
										</div>
										<label for="karyawan_email" class="col-sm-2 lbl-req">Email<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$kalkulasi->karyawan_email}}
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$karyawan_status}}
										</div>
										<label for="karyawan_metode" class="col-sm-2 lbl-req">Metode Perhitungan<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{ucwords(strtolower(str_replace('_', ' ', $kalkulasi->karyawankalkulasi_method)))}}
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Kontrak<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$contract_begin_dt}} - {{$contract_end_dt}}
										</div>
										<label for="karyawan_ptkp" class="col-sm-2 lbl-req">Status Perkawinan<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$kalkulasi->ptkp_description}}
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Periode<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$iperiode}}
										</div>
										<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Status Kalkulasi<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{!!($kalkulasi->karyawankalkulasi_lock) ? '<span class="badge rounded-pill bg-label-primary">Terkunci</span>' : '<span class="badge rounded-pill bg-label-dark">Belum Terkunci</span>'!!}
										</div>
									</div>
									@if($kalkulasi->karyawankalkulasi_active == '0')
									<div class="form-group row mb-3">
										<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Alasan (Non Aktif)<span class="float-right">:</span></label>
										<div class="col-sm-4">
										<span class="badge rounded-pill bg-label-warning">{{$kalkulasi->karyawanmasakerja_end_type}}</span>
										</div>
										<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Keterangan (Non Aktif)<span class="float-right">:</span></label>
										<div class="col-sm-4">
										{{$kalkulasi->karyawanmasakerja_end_reason}}
										</div>
									</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<form action="{{route('user.page.karyawan-kalkulasi-detail.update', ['karyawanmasakerjaId' => $kalkulasi->ms_karyawanmasakerja_id, 'kalkulasiId' => $kalkulasi->karyawankalkulasi_id])}}" method="POST" id="formKalkulasi" class="form-horizontal form-lbl-dot" autocomplete="off">
								<div class="card mb-3">
									<div class="card-header">
										<h5 class="mb-0">Penghasilan</h3>
									</div>
									<div class="card-body">
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4 lbl-req">Gaji Pokok</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_gaji_pokok" {{($kalkulasi->karyawankalkulasi_lock == false || $kalkulasi->karyawankalkulasi_active == '0') ? 'disabled' : 'required'}} name="karyawankalkulasi_salary" id="karyawankalkulasi_salary" class="form-control penghasilan-input" value="{{$kalkulasi->karyawankalkulasi_salary}}" placeholder="Gaji Pokok">
											</div>
										</div>
										@foreach($tunjangan as $key => $val)
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Tunjangan {{ucwords(strtolower(str_replace('_', ' ', $key)))}}</label>
											<div class="col-sm-4">
												<input type="text" {{$kalkulasi->karyawankalkulasi_lock == false || $kalkulasi->karyawankalkulasi_active == '0' ? 'disabled' : 'required'}} name="tunjangan[{{$key}}]" id="{{$key}}" class="form-control input_tunjangan penghasilan-input" value="{{$val}}" placeholder="{{$key}}">
											</div>
										</div>
										@endforeach
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Kesehatan ({{getBpjsRate($bpjsrate,'JamKes')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_jamkes" disabled class="form-control input-num" value="{{$perhitungan['penghasilan_jamkes']}}">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Kecelakaan Kerja ({{getBpjsRate($bpjsrate,'JKK')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_jkk" disabled value="{{$perhitungan['penghasilan_jkk']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Kematian ({{getBpjsRate($bpjsrate,'JKM')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_jkm" disabled value="{{$perhitungan['penghasilan_jkm']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Hari Tua ({{getBpjsRate($bpjsrate,'JHT')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_jht" disabled value="{{$perhitungan['penghasilan_jht']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Pensiun ({{getBpjsRate($bpjsrate,'JP')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_jp" disabled value="{{$perhitungan['penghasilan_jp']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Penghasilan Bruto Perbulan</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_bruto_perbulan" disabled value="{{$perhitungan['total_bruto_perbulan']}}" class="form-control input-num">
											</div>
										</div>
									</div>
								</div>

								<div class="card mb-3">
									<div class="card-header">
										<h5 class="mb-0">Pengurang</h3>
									</div>
									<div class="card-body">
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Biaya Jabatan</label>
											<div class="col-sm-4">
												<input type="text" id="pengurangan_biaya_jabatan" disabled value="{{$perhitungan['total_biaya_jabatan']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Kesehatan ({{getBpjsRate($bpjsrate,'JamKesMin')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="pengurangan_jamkes" disabled class="form-control input-num" value="{{$perhitungan['biaya_jamkes']}}">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Hari Tua ({{getBpjsRate($bpjsrate,'JHTMin')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="pengurangan_jht" disabled value="{{$perhitungan['biaya_jht']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Jaminan Pensiun ({{getBpjsRate($bpjsrate,'JPMin')->bpjsrate_rate}} %)</label>
											<div class="col-sm-4">
												<input type="text" id="pengurangan_jp" disabled value="{{$perhitungan['biaya_jp']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Penghasilan Neto Perbulan</label>
											<div class="col-sm-4">
												<input type="text" id="penghasilan_neto_perbulan" disabled value="{{$perhitungan['total_neto_perbulan']}}" class="form-control input-num">
											</div>
										</div>
									</div>
								</div>

								<div class="card mb-3">
									<div class="card-header">
										<h5 class="mb-0">Pajak</h3>
									</div>
									<div class="card-body">
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Penghasilan Neto Setahun</label>
											<div class="col-sm-4">
												<input type="text" id="perhitungan_total_neto_setahun" disabled value="{{$perhitungan['total_neto_pertahun']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Penghasilan Tidak Kena Pajak (PTKP)</label>
											<div class="col-sm-4">
												<input type="text" id="perhitungan_total_ptkp" disabled value="{{$perhitungan['total_ptkp']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">Penghasilan Kena Pajak (PKP)</label>
											<div class="col-sm-4">
												<input type="text" id="perhitungan_total_pkp" disabled value="{{$perhitungan['total_pkp']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">PPh Terutang Setahun</label>
											<div class="col-sm-4">
												<input type="text" id="perhitungan_total_pph_terutang_setahun" disabled value="{{$perhitungan['total_pph_terutang_setahun']}}" class="form-control input-num">
											</div>
										</div>
										<div class="form-group row mb-3">
											<label for="" class="col-sm-4">PPh Terutang Perbulan</label>
											<div class="col-sm-4">
												<input type="text" id="perhitungan_total_pph_terutang_perbulan" disabled value="{{$perhitungan['total_pph_terutang_perbulan']}}" class="form-control input-num">
											</div>
										</div>
									</div>
								</div>
								<div class="card mb-3">
									<div class="card-body">
										<div class="row mb-3">
											<div class="col-sm-12 d-flex justify-content-between">
												<a href="{{route('user.page.karyawan-kalkulasi.index', ['karyawanmasakerjaId' => request()->segment(3)])}}" class="btn rekkaa-page-link btn-outline-danger btn-sm"><i class="bx bx-arrow-back"></i> Kembali</a>
												@if($kalkulasi->karyawankalkulasi_lock && $kalkulasi->karyawankalkulasi_active == '1')
												<button type="submit" class="btn btn-simpan btn-warning btn-sm"><i class="bx bx-save"></i> Simpan</button>
												@endif
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		let tarif21 = JSON.parse('<?php echo json_encode($tarif21)  ?>');
		let tarif21Nonnpwp = JSON.parse('<?php echo json_encode($tarif21_nonnpwp) ?>');
		let bpjsRate = JSON.parse('<?php echo json_encode($bpjsrate)  ?>');
		let tunjanganJabatan = JSON.parse('<?php echo json_encode($tunjangan_jabatan) ?>');
		let ptkp = JSON.parse('<?php echo json_encode($ptkp) ?>');
		let metodePajak = "<?php echo $kalkulasi->karyawankalkulasi_method ?>";
		let kepemilikanNpwp = 'NPWP';
		let bpjsRateJamKes = getBpjsRate(bpjsRate, 'JamKes');
        let bpjsRateJamKesMin = getBpjsRate(bpjsRate, 'JamKesMin');
        let bpjsRateJKK = getBpjsRate(bpjsRate, 'JKK');
        let bpjsRateJKM = getBpjsRate(bpjsRate, 'JKM');
        let bpjsRateJHT = getBpjsRate(bpjsRate, 'JHT');
        let bpjsRateJHTMin = getBpjsRate(bpjsRate, 'JHTMin');
        let bpjsRateJP = getBpjsRate(bpjsRate, 'JP');
        let bpjsRateJPMin = getBpjsRate(bpjsRate, 'JPMin');

    	let optionAutoNumeric = { 
			currencySymbol: "Rp. ",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: true,
            decimalPlaces: '0',
			modifyValueOnWheel: false,
		};
		let tjIdNum = [];
		let penghasilanTunjanganLainnya = 0;
		if($('.input_tunjangan').html() !== undefined) {
			let tunjangan = $('.input_tunjangan');
			console.log('tunjangan', tunjangan.length)
			for(let i=0; i<tunjangan.length; i++) {
				let tjId = tunjangan.eq(i).attr("id");
				let ik = new AutoNumeric(`#${tjId}`, optionAutoNumeric);
				penghasilanTunjanganLainnya += ik.getNumber();
				tjIdNum.push(ik);
			}
		}

		let [penghasilanGajiPokok
        , penghasilanBrutoPerbulan
        , penghasilanJamKes, penghasilanJKK, penghasilanJKM, penghasilanJHT, penghasilanJP
        , penguranganBiayaJabatan, penguranganJamKes, penguranganJHT, penguranganJP, penghasilanNetoPerbulan
        , perhitunganTotalNetoSetahun, perhitunganTotalPTKP, perhitunganTotalPKP, perhitunganTotalPPHTerutangSetahun, perhitunganTotalPPHTerutangPerbulan] = new AutoNumeric.multiple(
            ["#penghasilan_gaji_pokok"
            , "#penghasilan_bruto_perbulan"
            , "#penghasilan_jamkes", "#penghasilan_jkk", "#penghasilan_jkm", "#penghasilan_jht", "#penghasilan_jp"
            , "#pengurangan_biaya_jabatan", "#pengurangan_jamkes", "#pengurangan_jht", "#pengurangan_jp", "#penghasilan_neto_perbulan"
            , "#perhitungan_total_neto_setahun", "#perhitungan_total_ptkp", "#perhitungan_total_pkp", "#perhitungan_total_pph_terutang_setahun", "#perhitungan_total_pph_terutang_perbulan"]
            , optionAutoNumeric
        );

		let formKalkulasi = $("#formKalkulasi").validate({
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
				$.ajax({
					method: form.method,
					url: form.action,
					data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
					error: function(error) {
						$(".spinner-box").fadeOut();
						// console.log(error.responseJSON.errors.email);
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
							});
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

						let href = "<?php echo url('/user/karyawan/'.request()->segment(3).'/kalkulasi') ?>";
						loadPage(href);
					}
				})
			},
		})

		$(".penghasilan-input").keyup(function(e) {
            e.preventDefault();
			penghasilanTunjanganLainnya = 0;
			for(let i=0; i<tjIdNum.length; i++) {
				// console.log('tjIdNumxxx', tjIdNum[i].getNumber());
				penghasilanTunjanganLainnya += tjIdNum[i].getNumber();
			}

            // penghasilan
            let penghasilanJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKes.bpjsrate_rate) / 100;
            let penghasilanJKKTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJKK.bpjsrate_rate) / 100;
            let penghasilanJKMTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJKM.bpjsrate_rate) / 100;
            let penghasilanJHTTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJHT.bpjsrate_rate) / 100;
            let penghasilanJPTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJP.bpjsrate_rate) / 100;
        
            penghasilanJamKes.set(penghasilanJamKesTotal);
            penghasilanJKK.set(penghasilanJKKTotal);
            penghasilanJKM.set(penghasilanJKMTotal);
            penghasilanJHT.set(penghasilanJHTTotal);
            penghasilanJP.set(penghasilanJPTotal);
            
            // pengurangan
            let penguranganJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKesMin.bpjsrate_rate) / 100;
            let penguranganJHTTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJHTMin.bpjsrate_rate) / 100;
            let penguranganJPTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJPMin.bpjsrate_rate) / 100;
            penguranganJamKes.set(penguranganJamKesTotal);
            penguranganJHT.set(penguranganJHTTotal);
            penguranganJP.set(penguranganJPTotal);
            
            if(metodePajak == 'NETT') {
                penghasilanBrutoPerbulan.set(
                    penghasilanGajiPokok.getNumber()
                )
            } else {
                penghasilanBrutoPerbulan.set(
                    penghasilanGajiPokok.getNumber()
                    + penghasilanTunjanganLainnya
                    + penghasilanJamKes.getNumber()
                    + penghasilanJKK.getNumber() 
                    + penghasilanJKM.getNumber()
                    + penghasilanJHT.getNumber()
                    + penghasilanJP.getNumber()
                );
            }

            let tjabatan = penghasilanBrutoPerbulan.getNumber() * tunjanganJabatan.tunjanganjabatan_rate / 100;
            if(tjabatan > tunjanganJabatan.tunjanganjabatan_maximum_allowance) {
                tjabatan = tunjanganJabatan.tunjanganjabatan_maximum_allowance;
            }
            penguranganBiayaJabatan.set(tjabatan);

            console.log('tjabatan', tjabatan)
            let netoPerbulanTotal = 0;
            if(metodePajak == 'NETT') {
                netoPerbulanTotal = penghasilanBrutoPerbulan.getNumber() - tjabatan;
            } else {
                netoPerbulanTotal = penghasilanBrutoPerbulan.getNumber() - tjabatan - penguranganJamKes.getNumber() - penguranganJHT.getNumber() - penguranganJP.getNumber();
            }
            
            if(netoPerbulanTotal < 0) {
                netoPerbulanTotal = 0;
            }
            console.log('penghasilanBrutoPerbulan', penghasilanBrutoPerbulan.getNumber())
            console.log('penguranganBiayaJabatan', penguranganBiayaJabatan.getNumber())
            console.log('penguranganJamKes', penguranganJamKes.getNumber())
            console.log('penguranganJHT', penguranganJHT.getNumber())
            console.log('penguranganJP', penguranganJP.getNumber())
            penghasilanNetoPerbulan.set(netoPerbulanTotal)

            perhitunganTotal();
        })

		function perhitunganTotal() {
			console.log('metodePajak', metodePajak);
            perhitunganTotalNetoSetahun.set(
                penghasilanNetoPerbulan.getNumber()
                * 12
            );
            let ptkpRate = parseInt(ptkp.ptkp_rate);
            if(ptkpRate > perhitunganTotalNetoSetahun.getNumber()) {
                ptkpRate = perhitunganTotalNetoSetahun.getNumber();
            }
            perhitunganTotalPTKP.set(ptkpRate);
            perhitunganTotalPKP.set(
                perhitunganTotalNetoSetahun.getNumber()
                - ptkpRate
            )
            let totalPPHTerutangSetahun = perhitunganTarifPPH21(tarif21, metodePajak, kepemilikanNpwp, perhitunganTotalPKP.getNumber(), tarif21Nonnpwp);
            perhitunganTotalPPHTerutangSetahun.set(totalPPHTerutangSetahun);
            perhitunganTotalPPHTerutangPerbulan.set(totalPPHTerutangSetahun / 12);
        }

		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>