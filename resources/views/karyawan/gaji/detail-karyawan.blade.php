<?php
$payroll = $payroll;
$pph21 = $payroll->pph21;
$tunjangan = json_decode($payroll->payroll_allowance_setting);
$potongan = json_decode($payroll->payroll_deduction_setting);
$bpjssetting = json_decode($payroll->payroll_bpjssetting);
$customtunjangan = json_decode($payroll->payroll_allowance_addition);
$custompengurangan = json_decode($payroll->payroll_deduction_addition);
// dd($bpjssetting);
$bpjskes = 0;
$bpjstk = 0;
if ($bpjssetting) {
	foreach ($bpjssetting as $bset) {
		if ($bset->type == 'KESEHATAN') {
			$bpjskes = 1;
		}
		if ($bset->type == 'TENAGA_KERJA') {
			$bpjstk = 1;
		}
	}
}
$lemburs = $payroll->lembur;
$total_lembur = 0;
foreach($lemburs as $lb) {
	$total_lembur += $lb->lemburkaryawan_total_overtime_amount;
}
?>
<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<div class="card">
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">ID</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawan_enid}}
							</div>
							<label class="col-sm-3 nodot-label">Nama</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawan_name}}
							</div>
						</div>
						<div class="row mb-3">

							<label class="col-sm-3 nodot-label">NIK</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawan_nik}}
							</div>
							<label class="col-sm-3 nodot-label">Jabatan</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawanjabatan_name}}
							</div>
						</div>

						<div class="row mb-3">

							<label class="col-sm-3 nodot-label">NPWP</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawan_npwp}}
							</div>
							<label class="col-sm-3 nodot-label">Status Perkawinan</label>
							<div class="col-sm-3 text-bold">
								{{$pph21->pph21_ptkp_description}}
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-3 nodot-label">Metode</label>
							<div class="col-sm-3 text-bold">
								{{str_replace('_', ' ', $payroll->payroll_method)}}
							</div>
							<label class="col-sm-3 nodot-label">Periode</label>
							<div class="col-sm-3 text-bold">
								<?php
								$payroll_period = Carbon\Carbon::parse($payroll->payroll_period)->translatedFormat('F Y');
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
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<form id="formEntitas" method="POST" action="#" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
							<div class="col-sm-6">
								<h5 class="text-bold">Pendapatan</h5>
								<hr>
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Gaji Pokok</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_prorate_salary, 0, ',', '.')}}
									</div>
								</div>
								@if($tunjangan)
								@foreach($tunjangan as $tj)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">{{$tj->stgrouptunjangankaryawan_name}}</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($tj->sttunjangankaryawan_accumulate_value, 0, ',', '.')}}
									</div>
								</div>
								@endforeach
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan PPh 21</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_pph21, 0, ',', '.')}}
									</div>
								</div>
								@if($bpjskes)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan BPJS Kesehatan</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
									</div>
								</div>
								@endif
								@if($bpjstk)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan BPJS TK</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
									</div>
								</div>
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan Lembur </label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($total_lembur, 0, ',', '.')}}
									</div>
								</div>
								<hr>
								<div class="row mb-3">
									<label class="col-sm-12 nodot-label mb-3">Pendapatan</label>
									<div class="pendapatanbox row">
										<?php if ($customtunjangan) :
											$idx = 0;
											// dd($customtunjangan);
											foreach ($customtunjangan as $ctunjangan) :
												if ($ctunjangan->taxable == '1') {
													$taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
												} else {
													$taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
												}
										?>
												<div class="col-sm-12 pendapatan-item" data-idx="{{$idx}}">
													<div class="row mb-3">
														<label class="col-sm-7 nodot-label pendapatan-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - {{$ctunjangan->name}} ({!!$taxabletxt!!})</label>
														<div class="col-sm-5 text-right pendapatan-currency">
															Rp. {{number_format($ctunjangan->nominal, 0, ',', '.')}}
														</div>
														<hr>
													</div>
												</div>
										<?php
												$idx++;
											endforeach;
										endif ?>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<h5 class="text-bold">Pemotongan</h5>
								<hr>
								@if($potongan)
								@foreach($potongan as $tp)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">{{$tp->stgrouppotongankaryawan_name}}</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($tp->stpotongankaryawan_accumulate_value, 0, ',', '.')}}
									</div>
								</div>
								@endforeach
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan PPh 21</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_deduction_pph21, 0, ',', '.')}}
									</div>
								</div>
								@if($bpjskes)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan BPJS Kesehatan</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_deduction_bpjskes, 0, ',', '.')}}
									</div>
								</div>
								@endif
								@if($bpjstk)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan BPJS TK</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_deduction_bpjstk, 0, ',', '.')}}
									</div>
								</div>
								@endif
								<div class="row mb-3">
									<label class="col-sm-12 nodot-label mb-3 text-bold">Dibayarkan Perusahaan</label>
									<div class="col-sm-12">
										@if($bpjskes)
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS Kesehatan</label>
											<div class="col-sm-6 text-right">
												Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
											</div>
										</div>
										@endif
										@if($bpjstk)
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS TK</label>
											<div class="col-sm-6 text-right">
												Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
											</div>
										</div>
										@endif
									</div>
								</div>
								<hr>
								<div class="row mb-3">
									<label class="col-sm-12 nodot-label mb-3">Pengurangan</label>
									<div class="penguranganbox row">
										<?php if ($custompengurangan) :
											$idx = 0;
											foreach ($custompengurangan as $cpengurangan) :
												if ($cpengurangan->taxable == '1') {
													$taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
												} else {
													$taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
												}
										?>
												<div class="col-sm-12 pengurangan-item" data-idx="{{$idx}}">
													<div class="row mb-3">
														<label class="col-sm-7 nodot-label pengurangan-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - {{$cpengurangan->name}} ({!!$taxabletxt!!})</label>
														<div class="col-sm-5 text-right pengurangan-currency">
															Rp. {{number_format($cpengurangan->nominal, 0, ',', '.')}}
														</div>
														<hr>
													</div>
												</div>
										<?php
												$idx++;
											endforeach;
										endif ?>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="row mb-3">
									<div class="col-sm-6">
										<hr>
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label text-bold">Total Pendapatan</label>
											<div class="col-sm-6 text-right text-bold">
												Rp. {{number_format($payroll->payroll_total_income, 0, ',', '.')}}
											</div>
										</div>
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label text-bold">Gaji Bersih</label>
											<div class="col-sm-6 text-right text-bold bg-label-dark">
												Rp. {{number_format($payroll->payroll_total_netto, 0, ',', '.')}}
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<hr>
										<div class="row">
											<label class="col-sm-6 nodot-label text-bold">Total Pemotongan</label>
											<div class="col-sm-6 text-right text-bold">
												Rp. {{number_format($payroll->payroll_total_outcome, 0, ',', '.')}}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3">
										<a href="{{route('karyawan.gaji.view.karyawan')}}" class="btn btn-sm btn-outline-danger rekkaa-page-link"><i class='bx bx-arrow-back'></i> Kembali</a>
									</div>
									<div class="col-sm-9 text-right">
                                        <button data-type="cetakslip" class="btn btn-sm btn-info"><i class='bx bxs-file-pdf'></i> Lihat Slip</button>
									</div>
								</div>
							</div>
						</form>
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
		let payroll = JSON.parse('<?php echo addslashes(json_encode($payroll)) ?>');

		$("#formEntitas").submit(function(e) {
			e.preventDefault();
			let type = $(document.activeElement).attr('data-type');
			// console.log('type', type);
			let msg = ``;
            window.open(`{{route('karyawan.gaji.view.karyawan.cetak', '')}}/${payroll.payroll_uuid}`, '_blank');

		})
	})
</script>