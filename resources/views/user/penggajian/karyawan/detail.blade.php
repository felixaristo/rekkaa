<?php

use Carbon\Carbon;

$payroll = $payroll;
$pph21 = $payroll->pph21;
$karyawan = $payroll->karyawan;
$tunjangan = json_decode($payroll->payroll_allowance_setting);
$potongan = json_decode($payroll->payroll_deduction_setting);
$bpjssetting = json_decode($payroll->payroll_bpjssetting);
$customtunjangan = json_decode($payroll->payroll_allowance_addition);
$custompengurangan = json_decode($payroll->payroll_deduction_addition);
$month = Carbon::parse($pph21->pph21_period)->format('n');
$pph_period_year = Carbon::parse($pph21->pph21_period)->format('Y');
$karyawan_contract_beginyear = Carbon::parse($karyawan->karyawan_contract_begin)->format('Y');
$karyawan_contract_beginmonth = Carbon::parse($karyawan->karyawan_contract_begin)->format('n');
$karyawan_contract_endmonth = ($karyawan->karyawan_contract_end) ? Carbon::parse($karyawan->karyawan_contract_end)->format('n') : null;
// dd($bpjssetting);
$payroll_karyawan_enid = $karyawan->karyawan_enid;
if($payroll->payroll_lock == 1) {
	$payroll_karyawan_enid = $payroll->payroll_karyawan_enid;
}
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
								{{$payroll->payroll_karyawan_enid}}
							</div>
							<label class="col-sm-3 nodot-label">Nama</label>
							<div class="col-sm-3 text-bold">
								{{$payroll->payroll_karyawan_name}} {!! ($payroll->karyawan->karyawan_active=='0' ? '<span class="badge rounded-pill bg-danger mb-1 mr-1">Tidak Aktif</span>' : '') !!}
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
								$payroll_period = Carbon::parse($payroll->payroll_period)->translatedFormat('F Y');
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
									<label class="col-sm-6 nodot-label">Gaji Pokok <a href="#" class="btn-info-gp"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_prorate_salary, 0, ',', '.')}}
									</div>
								</div>
								@if($tunjangan)
								@foreach($tunjangan as $tj)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">{{$tj->stgrouptunjangankaryawan_name}}
										<a href="#" data-info="{{base64_encode(json_encode($tj))}}" class="btn-info-tunjangan-karyawan"><i class="bx bx-info-circle"></i></a>
									</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($tj->sttunjangankaryawan_accumulate_value, 0, ',', '.')}}
									</div>
								</div>
								@endforeach
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan PPh 21 <a href="#" class="btn-info-pph21" data-type="tunjangan"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_pph21, 0, ',', '.')}}
									</div>
								</div>
								@if($bpjskes)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan BPJS Kesehatan <a href="#" class="btn-info-tunjangan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
									</div>
								</div>
								@endif
								@if($bpjstk)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan BPJS TK <a href="#" class="btn-info-tunjangan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
									</div>
								</div>
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Tunjangan Lembur 
										 <a href="#" class="btn-info-tunjangan-lembur"><i class="bx bx-info-circle"></i></a>
								</label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($total_lembur, 0, ',', '.')}}
									</div>
								</div>
								<hr>
								<div class="row mb-3">
									<label class="col-sm-12 nodot-label mb-3">Pendapatan 
									@if($payroll->payroll_lock == 0)
										<a href="#" class="btn-pendapatan text-info"><i class="bx bx-plus-circle"></i></a>
									@endif
									</label>
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
														@if($payroll->payroll_lock == 0)
														<div class="col-sm-12 text-right">
															<a href="#" class="text-info btn-edit-custom-tunjangan" data-idx="{{$idx}}"><i class="bx bx-edit"></i></a>
															<a href="#" class="text-danger btn-delete-custom-tunjangan" data-idx="{{$idx}}"><i class="bx bx-trash"></i></a>
														</div>
														@endif
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
									<label class="col-sm-6 nodot-label">{{$tp->stgrouppotongankaryawan_name}} <a href="#" data-info="{{base64_encode(json_encode($tp))}}" class="btn-info-potongan-karyawan"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($tp->stpotongankaryawan_accumulate_value, 0, ',', '.')}}
									</div>
								</div>
								@endforeach
								@endif
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan PPh 21 <a href="#" class="btn-info-pph21" data-type="potongan"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format(($payroll->payroll_deduction_pph21 > 0 ? $payroll->payroll_deduction_pph21 : 0), 0, ',', '.')}}
									</div>
								</div>
								@if($bpjskes)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan BPJS Kesehatan <a href="#" class="btn-info-potongan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
									<div class="col-sm-6 text-right">
										Rp. {{number_format($payroll->payroll_deduction_bpjskes, 0, ',', '.')}}
									</div>
								</div>
								@endif
								@if($bpjstk)
								<div class="row mb-3">
									<label class="col-sm-6 nodot-label">Pengurangan BPJS TK <a href="#" class="btn-info-potongan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
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
											<label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS Kesehatan <a href="#" data-dibayarperusahaan="{{$payroll->payroll_bpjskes_paidbycompany}}" class="btn-info-tunjangan-bpjskes"><i class="bx bx-info-circle"></i></a></label>
											<div class="col-sm-6 text-right">
												Rp. {{number_format($payroll->payroll_allowance_bpjskes, 0, ',', '.')}}
											</div>
										</div>
										@endif
										@if($bpjstk)
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BPJS TK <a href="#" data-dibayarperusahaan="{{$payroll->payroll_bpjstk_paidbycompany}}" class="btn-info-tunjangan-bpjstk"><i class="bx bx-info-circle"></i></a></label>
											<div class="col-sm-6 text-right">
												Rp. {{number_format($payroll->payroll_allowance_bpjstk, 0, ',', '.')}}
											</div>
										</div>
										@endif
									</div>
								</div>
								<hr>
								<div class="row mb-3">
									<label class="col-sm-12 nodot-label mb-3">Pengurangan 
									@if($payroll->payroll_lock == 0)
										<a href="#" class="btn-pengurangan text-info"><i class="bx bx-plus-circle"></i></a>
									@endif
									</label>
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
														@if($payroll->payroll_lock == 0)
														<div class="col-sm-12 text-right">
															<a href="#" class="text-info btn-edit-custom-pengurangan" data-idx="{{$idx}}"><i class="bx bx-edit"></i></a>
															<a href="#" class="text-danger btn-delete-custom-pengurangan" data-idx="{{$idx}}"><i class="bx bx-trash"></i></a>
														</div>
														@endif
														<hr>
													</div>
												</div>
										<?php
												$idx++;
											endforeach;
										endif ?>
									</div>
									<!-- <div class="col-sm-12">
										<div class="row mb-3">
											<label class="col-sm-6 nodot-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lembur</label>
											<div class="col-sm-6 text-right">
												Rp. 150.000
											</div>
										</div>
									</div> -->
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
												Rp. {{($payroll->payroll_total_outcome > 0) ? number_format($payroll->payroll_total_outcome, 0, ',', '.') : 0}}
											</div>
										</div>
									</div>
								</div>
							</div>
							@if($month == 12 || ($karyawan_contract_endmonth && $month == $karyawan_contract_endmonth))
							@if($karyawan_contract_beginyear == $pph_period_year)
							<hr>
							<div class="row mb-3">
								<label class="col-sm-12 nodot-label mb-3">Pendapatan di Perusahaan Sebelumnya</label>
							</div>
							<div class="row mb-3">
								<label class="col-sm-3 nodot-label text-bold">Total Penghasilan Netto</label>
								<div class="col-sm-4">
									<input type="text" name="ps_total_netto" id="ps_total_netto" {{$payroll->payroll_status != 0 ? 'disabled' : ''}} value="{{$pph21->pph21_prevcp_netto}}" class="form-control" placeholder="Total Penghasilan Netto">
								</div>
							</div>
							<div class="row mb-3">
								<label class="col-sm-3 nodot-label text-bold">Total PPh21</label>
								<div class="col-sm-4">
									<input type="text" name="ps_total_pph21" id="ps_total_pph21" {{$payroll->payroll_status != 0 ? 'disabled' : ''}} value="{{$pph21->pph21_prevcp_pph21_total}}" class="form-control" placeholder="Total PPh21">
								</div>
							</div>
							@endif
							@endif
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3">
										<a href="{{route('user.page.penggajian.index')}}?menu_id={{request()->get('menu_id')}}" class="btn btn-sm btn-outline-danger rekkaa-page-link"><i class='bx bx-arrow-back'></i> Kembali</a>
									</div>
									<div class="col-sm-9 text-right">
									@if($payroll->payroll_lock == 0)
										@if(in_array('CALCULATE_PAYSLIP', request()->get('permission_codes')))
											<button data-type="kalkulasi" class="btn btn-sm btn-warning"><i class='bx bx-refresh'></i> Kalkulasi Ulang</button>
											@if($payroll->payroll_status == 0)
											<button data-type="finalisasi" class="btn btn-sm btn-info"><i class='bx bx-check'></i> Finalisasi Perhitungan</button>
											@endif
										@endif
									@endif
										@if($payroll->payroll_status == 1 && $payroll->payroll_lock == 0)
											
											@if(in_array('CONFIRM_PAYSLIP', request()->get('permission_codes')))
											<button data-type="konfirmasi" class="btn btn-sm btn-danger btn-konfirmasi"><i class='bx bx-check'></i> Konfirmasi</button>
											@endif
										@endif
										@if($payroll->payroll_status == 2 && $payroll->payroll_lock == 1)
											@if(in_array('PAY_PAYSLIP', request()->get('permission_codes')))
											<button data-type="bayar" class="btn btn-sm btn-success"><i class='bx bx-money'></i> Bayar</button>
											@endif
										@endif
										@if($payroll->payroll_status == 3 && $payroll->payroll_lock == 1)
											@if(in_array('DOWNLOAD_PAYSLIP', request()->get('permission_codes')))
											<button data-type="cetakslip" class="btn btn-sm btn-info"><i class='bx bxs-file-pdf'></i> Lihat Slip</button>
											@endif
											@if(in_array('SEND_PAYSLIP', request()->get('permission_codes')))
											<button data-type="kirimslip" class="btn btn-sm btn-warning"><i class='bx bx-upload'></i> Kirim Slip</button>
											@endif
										@endif
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

<!-- Modal Info -->
<div class="modal fade" id="modalInfo" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalInfoCenterTitle">Info</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<!-- Modal Pendapatan -->
<div class="modal fade" id="modalPendapatan" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Pendapatan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<form id="formPendapatan" method="POST" action="#" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<input type="hidden" name="pendapatan_idx" id="pendapatan_idx">
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pendapatan_name">Nama Pendapatan</label>
							<div class="col-sm-7">
								<input type="text" required name="pendapatan_name" id="pendapatan_name" class="form-control" placeholder="Masukkan Nama Pendapatan">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pendapatan_value">Besar Pendapatan</label>
							<div class="col-sm-7">
								<input type="text" required name="pendapatan_value" id="pendapatan_value" class="form-control" placeholder="Masukkan Besar Pendapatan">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pendapatan_taxable">Dikenakan Pajak</label>
							<div class="col-sm-7">
								<div class="form-check form-check-inline">
									<input name="pendapatan_taxable" required class="form-check-input" type="radio" value="1" id="pendapatan_taxable_y" checked>
									<label class="form-check-label" for="pendapatan_taxable_y"> Ya </label>
								</div>
								<div class="form-check form-check-inline">
									<input name="pendapatan_taxable" required class="form-check-input" type="radio" value="0" id="pendapatan_taxable_t">
									<label class="form-check-label" for="pendapatan_taxable_t"> Tidak </label>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-sm-12 text-right">
								<button type="reset" class="btn btn-outline-danger btn-sm btn-reset">Batal</button>
								<button type="submit" class="btn btn-warning btn-sm">Simpan</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Pengurangan -->
<div class="modal fade" id="modalPengurangan" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Pengurangan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<form id="formPengurangan" method="POST" action="#" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<input type="hidden" name="pengurangan_idx" id="pengurangan_idx">
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pengurangan_name">Nama Pengurangan</label>
							<div class="col-sm-7">
								<input type="text" required name="pengurangan_name" id="pengurangan_name" class="form-control" placeholder="Masukkan Deskripsi">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pengurangan_value">Besar Pengurangan</label>
							<div class="col-sm-7">
								<input type="text" required name="pengurangan_value" id="pengurangan_value" class="form-control" placeholder="Masukkan Besar Potongan">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 lbl-req" for="pengurangan_taxable">Dikenakan Pajak</label>
							<div class="col-sm-7">
								<div class="form-check form-check-inline">
									<input name="pengurangan_taxable" class="form-check-input" type="radio" value="1" id="pengurangan_taxable_y" checked>
									<label class="form-check-label" for="pengurangan_taxable_y"> Ya </label>
								</div>
								<div class="form-check form-check-inline">
									<input name="pengurangan_taxable" class="form-check-input" type="radio" value="0" id="pengurangann_taxable_t">
									<label class="form-check-label" for="pengurangan_taxable_t"> Tidak </label>
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
		let isUpdated = false;
		let bpjstk = "<?php echo $bpjstk ?>";
		let bpjskes = "<?php echo $bpjskes ?>";
		let pendapatanTotalIdx = <?php echo ($customtunjangan) ? count($customtunjangan) : 0 ?>;
		let penguranganTotalIdx = <?php echo ($custompengurangan) ? count($custompengurangan) : 0 ?>;
		let payroll = JSON.parse('<?php echo addslashes(json_encode($payroll)) ?>');
		let pph21 = JSON.parse('<?php echo addslashes(json_encode($pph21)) ?>');
		let karyawan = JSON.parse('<?php echo addslashes(json_encode($karyawan)) ?>');
		let lembur = JSON.parse('<?php echo addslashes(json_encode($lembur)) ?>');
		// console.log('payroll', payroll);
		// console.log('pph21', pph21);
		// let bpjsseting = JSON.parse(payroll.payroll_bpjssetting);
		let allowancesseting = (payroll.payroll_allowance_setting) ? JSON.parse(payroll.payroll_allowance_setting) : [];
		let deductionseting = (payroll.payroll_deduction_setting) ? JSON.parse(payroll.payroll_deduction_setting) : [];
		let customallowancesseting = (payroll.payroll_allowance_addition) ? JSON.parse(payroll.payroll_allowance_addition) : [];
		let customdeductionsseting = (payroll.payroll_deduction_addition) ? JSON.parse(payroll.payroll_deduction_addition) : [];
		let penggajiansseting = (payroll.payroll_penggajiansetting) ? JSON.parse(payroll.payroll_penggajiansetting) : [];
		// console.log(penggajiansseting);
		// console.log(payroll);
		// console.log(bpjsseting);
		// console.log(allowancesseting);
		// console.log('bpjstk', bpjstk)
		// console.log('bpjskes', bpjskes)
		let optAutoNumeric = {
			currencySymbol: "Rp. ",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: true,
			modifyValueOnWheel: false,
			decimalPlaces: 0,
			modifyValueOnWheel: false,
		};
		let [pendapatanValueNum, penguranganValueNum] = AutoNumeric.multiple(["#pendapatan_value", "#pengurangan_value"], optAutoNumeric);
		let psTotalNetto = null;
		let psTotalPPh21 = null;
		if ($("#ps_total_netto").length > 0) {
			[psTotalNetto, psTotalPPh21] = AutoNumeric.multiple(["#ps_total_netto", "#ps_total_pph21"], optAutoNumeric)
		}

		let pendapatanCustomData = (payroll.payroll_allowance_addition) ? JSON.parse(payroll.payroll_allowance_addition) : [];
		// console.log('pendapatanCustomData', pendapatanCustomData)
		let formPendapatan = $("#formPendapatan").validate({
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
				let idx = $("#formPendapatan [name=pendapatan_idx]").val();
				let name = $("#formPendapatan [name=pendapatan_name]").val();
				let nominal = pendapatanValueNum.getNumber();
				let taxable = $("#formPendapatan [name=pendapatan_taxable]:checked").val();
				let taxabletxt = '';
				if (taxable == '1') {
					taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
				} else {
					taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
				}
				let tempData = {
					name: name,
					nominal: nominal,
					taxable
				};
				if (pendapatanCustomData[idx] != undefined) {
					// console.log('idxxxx', idx);
					pendapatanCustomData[idx] = tempData;

					let pendapatanitem = $(".pendapatan-item").eq(idx);
					// console.log('pendapatanitem', pendapatanitem);
					// console.log('pendapatanitem', pendapatanitem.find(".pendapatan-label").html());
					pendapatanitem.find(".pendapatan-label").html(`&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ${name} (${taxabletxt}) <a href="#" class="btn-edit-pendapatan"></a>`);
					pendapatanitem.find(".pendapatan-currency").text(`Rp. ${formatCurrency(nominal)}`);
					// pendapatanitem.find(".pendapatan-currency").text(`Rp. ${formatCurrency(nominal)}`);
				} else {
					$(".pendapatanbox").append(`<div class="col-sm-12 pendapatan-item" data-idx="${pendapatanTotalIdx}">
						<div class="row mb-3">
							<label class="col-sm-6 nodot-label pendapatan-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ${name} (${taxabletxt}) <a href="#" class="btn-edit-pendapatan"></a></label>
							<div class="col-sm-6 text-right pendapatan-currency">
								Rp. ${formatCurrency(nominal)}
							</div>
							<div class="col-sm-12 text-right">
								<a href="#" class="text-info btn-edit-custom-tunjangan" data-idx="${pendapatanTotalIdx}"><i class="bx bx-edit"></i></a>
								<a href="#" class="text-danger btn-delete-custom-tunjangan" data-idx="${pendapatanTotalIdx}"><i class="bx bx-trash"></i></a>
							</div>
							<hr>
						</div>
					</div>`);

					pendapatanCustomData.push(tempData);
					pendapatanTotalIdx++;
				}

				// console.log(pendapatanCustomData[idx])
				$("#formPendapatan [type=reset]").click();

				isUpdated = true;
				enableConfirmBtn();
			}
		});

		$("#formEntitas").on("click", ".btn-delete-custom-tunjangan", function(e) {
			e.preventDefault();
			let $this = $(this);
			let idx = $this.attr("data-idx");
			let cstpendapatan = pendapatanCustomData[idx];
			Swal.fire({
				html: 'Apakah anda ingin menghapus pendapatan <b>' + cstpendapatan.name + '</b>?',
				icon: 'question',
			}).then((result) => {
				console.log('result', result)
				result = result.value;
				if (result == undefined) {
					return false;
				}

				$this.closest('.pendapatan-item').remove();

				delete pendapatanCustomData[idx];

				isUpdated = true;
				enableConfirmBtn();
			})
		})

		$("#formEntitas").on("click", ".btn-edit-custom-tunjangan", function(e) {
			e.preventDefault();
			let $this = $(this);
			let idx = $this.attr("data-idx");
			let cstpendapatan = pendapatanCustomData[idx];
			$(".btn-pendapatan").click();

			$("#formPendapatan [name=pendapatan_idx]").val(idx);
			$("#formPendapatan [name=pendapatan_name]").val(cstpendapatan.name);
			$("#formPendapatan [name=pendapatan_taxable][value=" + cstpendapatan.taxable + "]").click();
			pendapatanValueNum.set(cstpendapatan.nominal);
		})

		$("#formPendapatan [type=reset]").click(function(e) {
			e.preventDefault();
			pendapatanValueNum.set(0);
			resetForm("#formPendapatan");

			if ($('#modalPendapatan').is(':visible')) {
				$("#modalPendapatan").modal("hide");
			}
		})

		$(".btn-pendapatan").click(function(e) {
			e.preventDefault();
			$("#modalPendapatan [type=reset]").click();
			$("#modalPendapatan").modal("show");
		})

		let penguranganCustomData = (payroll.payroll_deduction_addition) ? JSON.parse(payroll.payroll_deduction_addition) : [];
		// console.log('pendapatanCustomData', pendapatanCustomData)
		let formPengurangan = $("#formPengurangan").validate({
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
				let idx = $("#formPengurangan [name=pengurangan_idx]").val();
				let name = $("#formPengurangan [name=pengurangan_name]").val();
				let nominal = penguranganValueNum.getNumber();
				let taxable = $("#formPengurangan [name=pengurangan_taxable]:checked").val();
				let taxabletxt = '';
				if (taxable == '1') {
					taxabletxt = '<span class="text-danger fs-tiny">Kena Pajak</span>';
				} else {
					taxabletxt = '<span class="text-success fs-tiny">Tidak Kena Pajak</span>';
				}
				let tempData = {
					name: name,
					nominal: nominal,
					taxable
				};
				if (penguranganCustomData[idx] != undefined) {
					// console.log('idxxxx', idx);
					penguranganCustomData[idx] = tempData;

					let penguranganitem = $(".pengurangan-item").eq(idx);
					// console.log('pendapatanitem', pendapatanitem);
					// console.log('pendapatanitem', pendapatanitem.find(".pendapatan-label").html());
					penguranganitem.find(".pengurangan-label").html(`&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ${name} (${taxabletxt}) <a href="#" class="btn-edit-pengurangan"></a>`);
					penguranganitem.find(".pengurangan-currency").text(`Rp. ${formatCurrency(nominal)}`);
					// pendapatanitem.find(".pendapatan-currency").text(`Rp. ${formatCurrency(nominal)}`);
				} else {
					$(".penguranganbox").append(`<div class="col-sm-12 pengurangan-item" data-idx="${penguranganTotalIdx}">
						<div class="row mb-3">
							<label class="col-sm-6 nodot-label pengurangan-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ${name} (${taxabletxt}) <a href="#" class="btn-edit-pengurangan"></a></label>
							<div class="col-sm-6 text-right pengurangan-currency">
								Rp. ${formatCurrency(nominal)}
							</div>
							<div class="col-sm-12 text-right">
								<a href="#" class="text-info btn-edit-custom-pengurangan" data-idx="${penguranganTotalIdx}"><i class="bx bx-edit"></i></a>
								<a href="#" class="text-danger btn-delete-custom-pengurangan" data-idx="${penguranganTotalIdx}"><i class="bx bx-trash"></i></a>
							</div>
							<hr>
						</div>
					</div>`);

					penguranganCustomData.push(tempData);
					penguranganTotalIdx++;
				}

				// console.log('idx', idx);
				// console.log(penguranganCustomData[idx])
				$("#formPengurangan [type=reset]").click();

				isUpdated = true;
				enableConfirmBtn();
			}
		});

		$("#formPengurangan [type=reset]").click(function(e) {
			e.preventDefault();
			penguranganValueNum.set(0);
			resetForm("#formPengurangan");

			if ($('#modalPengurangan').is(':visible')) {
				$("#modalPengurangan").modal("hide");
			}
		})

		$(".btn-pengurangan").click(function(e) {
			e.preventDefault();
			$("#modalPengurangan [type=reset]").click();
			$("#modalPengurangan").modal("show");
		})

		$("#formEntitas").on("click", ".btn-delete-custom-pengurangan", function(e) {
			e.preventDefault();
			let $this = $(this);
			let idx = $this.attr("data-idx");
			let cstpengurangan = penguranganCustomData[idx];
			Swal.fire({
				html: 'Apakah anda ingin menghapus pengurangan <b>' + cstpengurangan.name + '</b>?',
				icon: 'question',
			}).then((result) => {
				console.log('result', result)
				result = result.value;
				if (result == undefined) {
					return false;
				}

				$this.closest('.pengurangan-item').remove();

				delete penguranganCustomData[idx];

				isUpdated = true;
				enableConfirmBtn();
			})
		})

		$("#formEntitas").on("click", ".btn-edit-custom-pengurangan", function(e) {
			e.preventDefault();
			let $this = $(this);
			let idx = $this.attr("data-idx");
			let cstpengurangan = penguranganCustomData[idx];
			$(".btn-pengurangan").click();

			$("#formPengurangan [name=pengurangan_idx]").val(idx);
			$("#formPengurangan [name=pengurangan_name]").val(cstpengurangan.name);
			$("#formPengurangan [name=pengurangan_taxable][value=" + cstpengurangan.taxable + "]").click();
			penguranganValueNum.set(cstpengurangan.nominal);
		})
		$(".btn-info-gp").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			// let dibayarperusahaan = $(this).attr('data-dibayarperusahaan')
			// console.log('payroll', payroll)
			let prorate = JSON.parse(payroll.payroll_prorate_data);
			// console.log(penggajiansseting);
			if (prorate.basic_salary == 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo #modalInfoCenterTitle").text(title);
			$("#modalInfo .modal-body").html(`
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Total Hari Kerja : ${prorate.working_days}</label>
				</div>
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Gaji Prorata :</label>
					<label class="col-sm-12">
						${prorate.working_days} / ${prorate.total_days} x ${formatCurrency(prorate.basic_salary)} = ${formatCurrency(prorate.prorate_salary)}
					</label>
				</div>
			`);
			$("#modalInfo").modal("show");
		});

		$(".btn-info-tunjangan-karyawan").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			let info = $(this).attr('data-info')
			// console.log('payroll', info)
			let prorate = JSON.parse(payroll.payroll_prorate_data);
			let parseInfo = JSON.parse(atob(info))
			// console.log('prorate', prorate)
			// console.log('parseInfo', parseInfo)
			if (parseInfo.sttunjangankaryawan_accumulate_value == 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo #modalInfoCenterTitle").text(title);

			let body = '';
			if(parseInfo.sttunjangankaryawan_period == 'HARI') {
				body = `<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Total hari kerja : ${prorate.total_days}</label>
				</div>
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Besaran tunjangan per hari : Rp. ${formatCurrency(parseInfo.sttunjangankaryawan_value)}</label>
				</div>
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Total tunjangan :</label>
					<label class="col-sm-12">
						${prorate.total_days} x ${formatCurrency(parseInfo.sttunjangankaryawan_value)} = ${formatCurrency(parseInfo.sttunjangankaryawan_accumulate_value)}
					</label>
				</div>`
			} else if(parseInfo.sttunjangankaryawan_period == 'BULAN') {
				body = `<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Bulan : {{$payroll_period}}</label>
				</div>
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Besaran tunjangan per bulan : Rp. ${formatCurrency(parseInfo.sttunjangankaryawan_value)}</label>
				</div>
				<div class="row">
					<label class="col-sm-12" for="stpotongankaryawan_name">Total tunjangan :</label>
					<label class="col-sm-12">
						1 x ${formatCurrency(parseInfo.sttunjangankaryawan_value)} = ${formatCurrency(parseInfo.sttunjangankaryawan_accumulate_value)}
					</label>
				</div>`
			} else if(parseInfo.sttunjangankaryawan_period == 'TAHUN') {
				if(parseInfo.sttunjangankaryawan_calculation == 'FORMULA') {
					let parseInfoFormula = JSON.parse(parseInfo.sttunjangankaryawan_formula);
					let parseInfoFormulaTxt = '';
					parseInfoFormula.forEach(fm => {
						parseInfoFormulaTxt += ` ${fm.text} `;
					});
					if(parseInfoFormulaTxt) {
						let attendance_days = (prorate.attendance_days.length > 0) ? prorate.attendance_days.length : 7;
						let diffmonths = 0;
						let wday = attendance_days;
						if(prorate.working_days >= wday) {
							diffmonths += 1;
						}
						let proratemonthsstring = `(${diffmonths} / 12)`;
						let proratemonths = diffmonths / 12;
						// proratemonths = (proratemonths >= 1) ? 1 : proratemonths.toFixed(2);
						parseInfoFormulaTxt += ` x ${proratemonthsstring}`;
					}
					body = `<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Besaran tunjangan per tahun : ${parseInfoFormulaTxt}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Total tunjangan :</label>
						<label class="col-sm-12">
							${parseInfoFormulaTxt} = ${formatCurrency(parseInfo.sttunjangankaryawan_accumulate_value)}
						</label>
					</div>`
				} else {
					body = `<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Besaran tunjangan per tahun : Rp. ${formatCurrency(parseInfo.sttunjangankaryawan_value)}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Total tunjangan :</label>
						<label class="col-sm-12">
							1 x ${formatCurrency(parseInfo.sttunjangankaryawan_value)} = ${formatCurrency(parseInfo.sttunjangankaryawan_accumulate_value)}
						</label>
					</div>`
				}
			}

			$("#modalInfo .modal-body").html(`
				${body}
			`);
			$("#modalInfo").modal("show");
		});

		$(".btn-info-potongan-karyawan").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			let info = $(this).attr('data-info')
			// console.log('payroll', info)
			let prorate = JSON.parse(payroll.payroll_prorate_data);
			let parseInfo = JSON.parse(atob(info))
			console.log('parseInfo', parseInfo)
			$("#modalInfo #modalInfoCenterTitle").text(title);

			let body = '';
			// ['HARI','BULAN','KELIPATAN_HARI','KELIPATAN_BULAN','PRORATA','TETAP']
			if(parseInfo.stpotongankaryawan_type == 'TETAP') {
				if(parseInfo.stpotongankaryawan_method == 'HARI') {
					body = `<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Total hari kerja : ${prorate.total_days}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Besaran potongan per hari : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">${prorate.total_days} x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
					</div>`
				} else if(parseInfo.stpotongankaryawan_method == 'BULAN') {
					body = `<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Bulan : {{$payroll_period}}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Besaran potongan per bulan : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
					</div>
					<div class="row">
						<label class="col-sm-12" for="stpotongankaryawan_name">1 x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
					</div>`
				}
			} else {
				if(parseInfo.stpotongankaryawan_method == 'HARI') {
					body = `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total tidak masuk : ${prorate.absence_days}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Besaran potongan per hari : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">${prorate.absence_days} x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
							</div>`
				} else if(parseInfo.stpotongankaryawan_method == 'TETAP') {
					body = `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total hari kerja : ${prorate.total_days}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Besaran potongan per hari : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">${prorate.total_days} x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
							</div>`
				} else if(parseInfo.stpotongankaryawan_method == 'BULAN') {
					body = `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Bulan : {{$payroll_period}}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Besaran potongan per bulan : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">1 x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
							</div>`
				} else if(parseInfo.stpotongankaryawan_method == 'KELIPATAN_HARI') {
					body = `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Besar potongan per ${parseInfo.stpotongankaryawan_accumulationtime} menit : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
							</div>`
					let late_times = prorate.late_times;

					let total = 0;
					Object.keys(late_times).forEach(function(key) {
						console.log(key, late_times[key]);
						let dt = moment(key).format('DD/MM/YYYY');
						let mnts = moment.duration(late_times[key]).asMinutes();
						let subtotal = mnts / parseInfo.stpotongankaryawan_accumulationtime * parseInfo.stpotongankaryawan_value;
						total += subtotal;
						console.log('mnts',mnts)
						body += `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">${dt} - ${mnts} menit = ${formatCurrency(subtotal)}</label>
							</div>`
					});
					body += `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total = ${formatCurrency(total)}</label>
							</div>`
				} else if(parseInfo.stpotongankaryawan_method == 'KELIPATAN_BULAN') {
					let total_mnts = (prorate.total_lates[0] * 60) + prorate.total_lates[1];
					let qty = total_mnts / parseInfo.stpotongankaryawan_accumulationtime;
					body = `<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Besar potongan per ${parseInfo.stpotongankaryawan_accumulationtime} menit : ${formatCurrency(parseInfo.stpotongankaryawan_value)}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total telat : ${total_mnts} menit</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total telat (qty) : ${total_mnts} / ${parseInfo.stpotongankaryawan_accumulationtime} = ${qty}</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">${qty} x ${formatCurrency(parseInfo.stpotongankaryawan_value)} = ${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
							</div>`;
				} else if(parseInfo.stpotongankaryawan_method == 'PRORATA') {
					let parseformulatext = (parseInfo.stpotongankaryawan_formula_text) ? JSON.parse(parseInfo.stpotongankaryawan_formula_text) : [];
					parseformulatext.forEach(ftext => {
						let total = ftext.absence_days / ftext.total_days * (ftext.total_days * ftext.value);
						body += `<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">Prorata dari : ${ftext.name}</label>
								</div>
								<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">Total Masa kerja : ${ftext.total_days}</label>
								</div>
								<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">Total tidak masuk : ${ftext.absence_days}</label>
								</div>
								<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">Potongan : ${formatCurrency(ftext.value)}</label>
								</div>
								<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">Total potongan :</label>
								</div>
								<div class="row">
									<label class="col-sm-12" for="stpotongankaryawan_name">${ftext.absence_days} / ${ftext.total_days} x (${ftext.total_days} x ${formatCurrency(ftext.value)}) = ${formatCurrency(total)}</label>
								</div>
								<hr>`; 	
							});
					}
					body += `
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">Total :</label>
							</div>
							<div class="row">
								<label class="col-sm-12" for="stpotongankaryawan_name">${formatCurrency(parseInfo.stpotongankaryawan_accumulate_value)}</label>
							</div>`
			}

			if(parseInfo.stpotongankaryawan_accumulate_value == 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}
			$("#modalInfo .modal-body").html(body);
			$("#modalInfo").modal("show");
		});

		$(".btn-info-tunjangan-bpjskes").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			let dibayarperusahaan = $(this).attr('data-dibayarperusahaan')
			console.log('dibayarperusahaan', dibayarperusahaan)
			let jamkesrate = parseFloat(pph21.pph21_allowance_jamkes_rate);
			let bjamkestotal = 0;
			let pjamkestotal = parseFloat(pph21.pph21_allowance_jamkes);
			if (payroll.payroll_bpjskes_paidbycompany == 1) {
				jamkesrate += parseFloat(pph21.pph21_deduction_jamkes_payslip_rate);
				bjamkestotal = parseFloat(pph21.pph21_deduction_jamkes_payslip);
			}
			// if(dibayarperusahaan == 0) {
			// 	pjamkestotal = 0;
			// }

			let total = bjamkestotal + pjamkestotal;

			if (total <= 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo #modalInfoCenterTitle").text(title);
			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">Jamkes (${jamkesrate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
				<hr>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold" for="stpotongankaryawan_name">Total</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
			`);
			$("#modalInfo").modal("show");
		})

		$(".btn-info-tunjangan-bpjstk").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			let dibayarperusahaan = $(this).attr('data-dibayarperusahaan');
			$("#modalInfo #modalInfoCenterTitle").text(title);
			let jhtrate = parseFloat(pph21.pph21_allowance_jht_payslip_rate);
			let jhttotal = parseFloat(pph21.pph21_allowance_jht_payslip);
			let jprate = parseFloat(pph21.pph21_allowance_jp_payslip_rate);
			let jptotal = parseFloat(pph21.pph21_allowance_jp_payslip);
			let jkkrate = parseFloat(pph21.pph21_allowance_jkk_rate);
			let jkktotal = parseFloat(pph21.pph21_allowance_jkk);
			let jkmrate = parseFloat(pph21.pph21_allowance_jkm_rate);
			let jkmtotal = parseFloat(pph21.pph21_allowance_jkm);

			if (payroll.payroll_bpjstk_paidbycompany == 1) {
				jhtrate += parseFloat(pph21.pph21_deduction_jht_rate);
				jhttotal += parseFloat(pph21.pph21_deduction_jht);
				jprate += parseFloat(pph21.pph21_deduction_jp_rate);
				jptotal += parseFloat(pph21.pph21_deduction_jp);
			}

			// if(dibayarperusahaan == 0) {
			// 	jkmtotal = 0;
			// 	jkktotal = 0;
			// }
			let total = jhttotal + jptotal + jkktotal + jkmtotal;

			console.log(total);
			if (total <= 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JHT (${jhtrate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jhttotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JP (${jprate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jptotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JKK (${jkkrate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jkktotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JKM (${jkmrate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jkmtotal)}
					</div>
				</div>
				<hr>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold" for="stpotongankaryawan_name">Total</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
			`);
			$("#modalInfo").modal("show");
		})

		$(".btn-info-potongan-bpjskes").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			// console.log('pph21', pph21)
			let jamkesrate = parseFloat(pph21.pph21_deduction_jamkes_payslip_rate);
			let bjamkesmax = "{{$max_bpjs['KES']}}";
			let bjamkestotal = 0;
			if (payroll.payroll_bpjskes_paidbycompany == 0) {
				bjamkestotal = parseFloat(pph21.pph21_deduction_jamkes_payslip);
			}
			// console.log('oioi',bjamkestotal);
			let total = bjamkestotal;

			if (total <= 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo #modalInfoCenterTitle").text(title);
			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">Jamkes (${jamkesrate}%)
					<br><span class="text-warning">*Dari Maksimal Rp. ${formatCurrency(bjamkesmax)}</span></label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
				<hr>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold" for="stpotongankaryawan_name">Total</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
			`);
			$("#modalInfo").modal("show");
		})

		$(".btn-info-potongan-bpjstk").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			$("#modalInfo #modalInfoCenterTitle").text(title);
			let bjkjpmax = "{{$max_bpjs['TK']}}";
			let jhtrate = parseFloat(pph21.pph21_deduction_jht_rate);
			let jhttotal = 0;
			let jprate = parseFloat(pph21.pph21_deduction_jp_rate);
			let jptotal = 0;

			if (payroll.payroll_bpjstk_paidbycompany == 0) {
				jhttotal = parseFloat(pph21.pph21_deduction_jht);
				jptotal = parseFloat(pph21.pph21_deduction_jp);
			}
			let total = jhttotal + jptotal;

			if (total <= 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}
			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JHT (${jhtrate}%)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jhttotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">JP (${jprate}%)
					<br><span class="text-warning">*Dari Maksimal Rp. ${formatCurrency(bjkjpmax)}</span></label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(jptotal)}
					</div>
				</div>
				<hr>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold" for="stpotongankaryawan_name">Total</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
			`);
			$("#modalInfo").modal("show");
		})

		$(".btn-info-tunjangan-lembur").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			console.log('lembur', lembur)
			
			let jam = parseFloat(lembur['jam'] / 60);
			let total = lembur['total'];

			if (total <= 0) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			$("#modalInfo #modalInfoCenterTitle").text(title);
			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<label class="col-sm-6" for="stpotongankaryawan_name">Total Jam Lembur </label>
					<div class="col-sm-6 text-right">
						${jam} Jam
					</div>
				</div>
				<hr>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold" for="stpotongankaryawan_name">Total Besaran Lembur</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(total)}
					</div>
				</div>
			`);
			$("#modalInfo").modal("show");
		})

		$(".btn-info-pph21").click(function(e) {
			e.preventDefault();
			let title = $(this).parent().text();
			let type = $(this).attr("data-type");
			$("#modalInfo #modalInfoCenterTitle").text(title);
			console.log('pph21', pph21);
			let periodeSebelumnya = '-';
			let gaji = parseFloat(pph21.pph21_prorate_salary);
			let penghasilanjamkesrate = parseFloat(pph21.pph21_allowance_jamkes_rate);
			let penghasilanjamkestotal = 0;
			let penghasilanjkkrate = parseFloat(pph21.pph21_allowance_jkk_rate);
			let penghasilanjkktotal = 0;
			let penghasilanjkmrate = parseFloat(pph21.pph21_allowance_jkm_rate);
			let penghasilanjkmtotal = 0;
			let penghasilanjkrate = parseFloat(pph21.pph21_allowance_jk_rate);
			let penghasilanjktotal = 0;
			let penghasilanjhtrate = parseFloat(pph21.pph21_allowance_jht_payslip_rate);
			let penghasilanjhttotal = 0;
			let penghasilanjprate = parseFloat(pph21.pph21_allowance_jp_payslip_rate);
			let penghasilanjptotal = 0;
			let bruto_perbulantotal = 0;

			let biayajabatantotal = 0;
			let biayajhtrate = parseFloat(pph21.pph21_deduction_jht_rate);
			let biayajhttotal = 0;
			let biayajprate = parseFloat(pph21.pph21_deduction_jp_rate);
			let biayajptotal = 0;
			let biayajamkesrate = parseFloat(pph21.pph21_deduction_jamkes_payslip_rate);
			let biayajamkestotal = 0;
			let potongantotal = 0;
			let netto_perbulantotal = 0;
			let netto_pertahuntotal = 0;
			let ptkptotal = 0;
			let pkptotal = 0;
			let pph21bulantotal = 0;
			let pph21tahuntotal = 0;
			let tunjangan_karyawan_txt = '';
			let potongan_karyawan_txt = '';
			let custom_tunjangan_karyawan_txt = '';
			let custom_pengurangan_karyawan_txt = '';
			let category_ter = pph21.pph21_ptkp_category;
			let rate_ter = pph21.pph21_ptkpdet_rate_percentage;

			if (allowancesseting.length > 0) {
				allowancesseting.forEach(allow => {
					if (allow.sttunjangankaryawan_taxable == 1) {
						tunjangan_karyawan_txt += `<div class="row mb-3">
							<label class="col-sm-6">${allow.stgrouptunjangankaryawan_name}</label>
							<div class="col-sm-6 text-right">
								Rp. ${formatCurrency(allow.sttunjangankaryawan_accumulate_value)}
							</div>
						</div>`;
					}
				});
			}

			if (customallowancesseting.length > 0) {
				customallowancesseting.forEach(allow => {
					if (allow.taxable == 1) {
						custom_tunjangan_karyawan_txt += `<div class="row mb-3">
							<label class="col-sm-6">${allow.name}</label>
							<div class="col-sm-6 text-right">
								Rp. ${formatCurrency(allow.nominal)}
							</div>
						</div>`;
					}
				});
			}

			if (deductionseting.length > 0) {
				deductionseting.forEach(deduct => {
					if (deduct.stpotongankaryawan_taxable == 1) {
						potongan_karyawan_txt += `<div class="row mb-3">
							<label class="col-sm-6">${deduct.stgrouppotongankaryawan_name}</label>
							<div class="col-sm-6 text-right">
								Rp. ${formatCurrency(deduct.stpotongankaryawan_accumulate_value)}
							</div>
						</div>`;
					}
				});
			}

			if (customdeductionsseting.length > 0) {
				customdeductionsseting.forEach(deduct => {
					if (deduct.taxable == 1) {
						custom_pengurangan_karyawan_txt += `<div class="row mb-3">
							<label class="col-sm-6">${deduct.name}</label>
							<div class="col-sm-6 text-right">
								Rp. ${formatCurrency(deduct.nominal)}
							</div>
						</div>`;
					}
				});
			}

			if (type != 'tunjangan' || parseFloat(payroll.payroll_allowance_pph21) > 0) {
				penghasilanjamkesrate = parseFloat(pph21.pph21_allowance_jamkes_rate);
				penghasilanjamkestotal = parseFloat(pph21.pph21_allowance_jamkes);
				penghasilanjkkrate = parseFloat(pph21.pph21_allowance_jkk_rate);
				penghasilanjkktotal = parseFloat(pph21.pph21_allowance_jkk);
				penghasilanjkmrate = parseFloat(pph21.pph21_allowance_jkm_rate);
				penghasilanjkmtotal = parseFloat(pph21.pph21_allowance_jkm);
				penghasilanjkrate = parseFloat(pph21.pph21_allowance_jk_rate);
				penghasilanjktotal = parseFloat(pph21.pph21_allowance_jk);
				penghasilanjhtrate = parseFloat(pph21.pph21_allowance_jht_payslip_rate);
				penghasilanjhttotal = parseFloat(pph21.pph21_allowance_jht);
				penghasilanjprate = parseFloat(pph21.pph21_allowance_jp_payslip_rate);
				penghasilanjptotal = parseFloat(pph21.pph21_allowance_jp);
				bruto_perbulantotal = parseFloat(pph21.pph21_bruto_month);
				bruto_pertahuntotal = parseFloat(pph21.pph21_bruto_year);
				potongantotal = parseFloat(pph21.pph21_deduction_other);
				biayajabatantotal = parseFloat(pph21.pph21_deduction_position);
				biayajhtrate = parseFloat(pph21.pph21_deduction_jht_rate);
				biayajhttotal = parseFloat(pph21.pph21_deduction_jht);
				biayajprate = parseFloat(pph21.pph21_deduction_jp_rate);
				biayajptotal = parseFloat(pph21.pph21_deduction_jp);
				biayajamkesrate = parseFloat(pph21.pph21_deduction_jamkes_payslip_rate);
				biayajamkestotal = parseFloat(pph21.pph21_deduction_jamkes);
				netto_perbulantotal = parseFloat(pph21.pph21_netto_month);
				netto_pertahuntotal = parseFloat(pph21.pph21_netto_year);
				ptkptotal = parseFloat(pph21.pph21_ptkp);
				pkptotal = (pph21.pph21_pkp > 0) ? parseFloat(pph21.pph21_pkp) : 0;
				pph21bulantotal = parseFloat(pph21.pph21_total_month);
				pph21tahuntotal = parseFloat(pph21.pph21_total_year);
			}

			if (pph21bulantotal <= 0 && type == 'tunjangan') {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'info',
					html: 'Tidak ada detail perhitungan.'
				})
				return false;
			}

			let bpjskes_penghasilan_txt = '';
			let bpjstk_penghasilan_txt = '';
			let bpjstk_pengurangan_txt = '';
			if (bpjstk == 1) {
				// console.log('oioi', bpjstk);
				bpjstk_penghasilan_txt = `
				<div class="row mb-3">
					<label class="col-sm-6">Jaminan Kecelakaan Kerja (${penghasilanjkkrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(penghasilanjkktotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Jaminan Kematian (${penghasilanjkmrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(penghasilanjkmtotal)}
					</div>
				</div>
				<!-- <div class="row mb-3">
					<label class="col-sm-6">Jaminan Hari Tua (${penghasilanjhtrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(penghasilanjhttotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Jaminan Pensiun (${penghasilanjprate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(penghasilanjptotal)}
					</div>
				</div> -->`;

				bpjstk_pengurangan_txt = `<div class="row mb-3">
					<label class="col-sm-6">Jaminan Hari Tua (${biayajhtrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(biayajhttotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Jaminan Pensiun (${biayajprate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(biayajptotal)}
					</div>
				</div>`;
			}

			if (bpjskes == 1) {
				bpjskes_penghasilan_txt = `<div class="row mb-3">
					<label class="col-sm-6">Jaminan Kesehatan (${penghasilanjamkesrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(penghasilanjamkestotal)}
					</div>
				</div>`;
			}

			let month = moment(pph21.pph21_period).format('MM');
			let pengurangan_txt = '';
			let perhitungan_pph21_txt = '';
			let info_ter = '';
			// console.log('karyawan.karyawan_contract_end', karyawan.karyawan_contract_end);
			if(month == 12 || (karyawan.karyawan_contract_end && month == moment(karyawan.karyawan_contract_end).format('M'))) {
				periodeSebelumnya = moment(pph21.pph21_period).subtract(1, 'months').format('M') == '12' ? moment(pph21.pph21_period).format('MMM YYYY') : moment(pph21.pph21_period).subtract(1, 'months').format('MMM YYYY');
				pengurangan_txt = `<div class="row mb-3">
					<div class="col-sm-12">
						<h5 class="text-bold mt-3">Pengurang</h3>
						<hr>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Biaya Jabatan</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(biayajabatantotal)}
					</div>
				</div>
				${custom_pengurangan_karyawan_txt}
				<!-- <div class="row mb-3">
					<label class="col-sm-6">Jaminan Kesehatan (${biayajamkesrate} %)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(biayajamkestotal)}
					</div>
				</div> -->
				${bpjstk_pengurangan_txt}
				${potongan_karyawan_txt}
				<div class="row mb-3">
					<label class="col-sm-6">Total</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(potongantotal+biayajabatantotal+biayajhttotal+biayajptotal)}
					</div>
				</div>`;

				// <div class="row mb-3">
				// 	<label class="col-sm-6">Penghasilan Netto per Bulan</label>
				// 	<div class="col-sm-6 text-right">
				// 		Rp. ${formatCurrency(netto_perbulantotal)}
				// 	</div>
				// </div>
				let perhitungan_netto_txt = `
				<div class="row mb-3">
					<label class="col-sm-6">Penghasilan Netto per Tahun</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(netto_pertahuntotal)}
					</div>
				</div>`;
				
				if(pph21.pph21_prevcp_netto > 0) {
					perhitungan_netto_txt += `<div class="row mb-3">
						<label class="col-sm-6">Penghasilan Netto Perusahaan Sebelumnya</label>
						<div class="col-sm-6 text-right">
							Rp. ${formatCurrency(pph21.pph21_prevcp_netto)}
						</div>
					</div>`;
					pkptotal = parseFloat(pph21.pph21_prevcp_netto) + netto_pertahuntotal - pph21.pph21_ptkp;
				}
				perhitungan_pph21_txt = `
				<div class="row mb-3">
					<label class="col-sm-6">Penghasilan Bruto per Tahun</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(bruto_pertahuntotal)}
					</div>
				</div>
				${perhitungan_netto_txt}
				<div class="row mb-3">
					<label class="col-sm-6">Penghasilan Tidak Kena Pajak (PTKP)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(ptkptotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Penghasilan Kena Pajak (PKP)</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(pkptotal)}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6 text-bold">PPh Dipotong Sampai ${periodeSebelumnya}</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(pph21tahuntotal)}
					</div>
				</div>`;
				if(pph21.pph21_prevcp_pph21_total > 0) {
					perhitungan_pph21_txt += `<div class="row mb-3">
						<label class="col-sm-6 text-bold">PPh Dipotong Perusahaan Sebelumnya</label>
						<div class="col-sm-6 text-right text-bold">
							Rp. ${formatCurrency(pph21.pph21_prevcp_pph21_total)}
						</div>
					</div>`;
				}
				
				// <div class="row mb-3">
				// 	<label class="col-sm-6 text-bold">PPh Terutang Setahun</label>
				// 	<div class="col-sm-6 text-right text-bold">
				// 		Rp. ${formatCurrency(pph21tahuntotal)}
				// 	</div>
				// </div>
			} else {
				info_ter = `
				<div class="row mb-3">
					<label class="col-sm-6">Kategori TER</label>
					<div class="col-sm-6 text-right">
						${category_ter}
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Tarif TER (%)</label>
					<div class="col-sm-6 text-right">
						${rate_ter} %
					</div>
				</div>`;
			}

			let lebihbayar_txt = '';
			if(pph21.pph21_total_month < 0) {
				lebihbayar_txt = `<div class="row mb-3 text-danger">
					<label class="col-sm-6">*Lebih bayar</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(pph21.pph21_total_month * -1)}
					</div>
				</div>`;
			}

			$("#modalInfo .modal-body").html(`
				<div class="row mb-3">
					<div class="col-sm-12">
						<h5 class="text-bold">Penghasilan</h3>
						<hr>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-6">Gaji Pokok</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(gaji)}
					</div>
				</div>
				${tunjangan_karyawan_txt}
				${custom_tunjangan_karyawan_txt}
				${bpjskes_penghasilan_txt}
				${bpjstk_penghasilan_txt}
				<div class="row mb-3">
					<label class="col-sm-6">Penghasilan Bruto per Bulan</label>
					<div class="col-sm-6 text-right">
						Rp. ${formatCurrency(bruto_perbulantotal)}
					</div>
				</div>
				${pengurangan_txt}
				<div class="row mb-3">
					<div class="col-sm-12">
						<h5 class="text-bold mt-3">Perhitungan PPh 21</h3>
						<hr>
					</div>
				</div>
				${info_ter}
				${perhitungan_pph21_txt}
				<div class="row mb-3">
					<label class="col-sm-6 text-bold">PPh Terutang Perbulan</label>
					<div class="col-sm-6 text-right text-bold">
						Rp. ${formatCurrency(pph21bulantotal)}
					</div>
				</div>
				${lebihbayar_txt}
			`);
			$("#modalInfo").modal("show");
		})

		$("modalInfo").on("hide.bs.modal", function(e) {
			$("#modalInfo .modal-body").html("");
		})

		$("#formEntitas").submit(function(e) {
			e.preventDefault();
			let type = $(document.activeElement).attr('data-type');
			// console.log('type', type);
			let msg = ``;
			if (type == 'kalkulasi') {
				msg = `Apakah anda ingin melakukan perhitungan ulang penggajian karyawan <b>${payroll.karyawan.karyawan_name}</b>?`;
				processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}`, msg);
			} else if (type == 'finalisasi') {
				msg = `Apakah anda ingin melakukan finalisasi perhitungan penggajian karyawan <b>${payroll.karyawan.karyawan_name}</b>?`;
				processPayslip(`{{route('user.page.penggajian.calculate')}}?menu_id=${currentMenuId}&final=1`, msg);
			} else if (type == 'konfirmasi') {
				msg = `Apakah anda ingin mengkonfirmasi perhitungan penggajian karyawan <b>${payroll.karyawan.karyawan_name}</b>?
				<br><span class="text-danger">*Perhitungan akan di <b>lock</b> dan tidak bisa di kalkulasi ulang.</span>`;
				processPayslip(`{{route('user.page.penggajian.confirmation')}}?menu_id=${currentMenuId}`, msg);
			} else if (type == 'bayar') {
				msg = `Apakah anda ingin melakukan pembayaran karyawan <b>${payroll.karyawan.karyawan_name}</b>?`;
				processPayslip(`{{route('user.page.penggajian.paid')}}?menu_id=${currentMenuId}`, msg);
			} else if (type == 'cetakslip') {
				window.open(`{{route('user.page.penggajian.cetak', '')}}/${payroll.payroll_uuid}?menu_id=${currentMenuId}`, '_blank');
			} else if (type == 'kirimslip') {
				kirimPayslip(payroll);
			}

		})

		function processPayslip(url = '#', msg = '') {
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
								custom_allowance_data: pendapatanCustomData,
								custom_deduction_data: penguranganCustomData,
								ps_total_netto: (psTotalNetto) ? psTotalNetto.getNumber() : 0,
								ps_total_pph21_total: (psTotalPPh21) ? psTotalPPh21.getNumber() : 0,
								payroll_uuids: [
									payroll.payroll_uuid
								]
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
				loadPage("{{url()->full()}}");
			});
		}

		function kirimPayslip(data) {
			if (data.karyawan.karyawan_isuser != 1) {
				Swal.fire({
				html: 'Kirim informasi slip gaji gagal. Karyawan tersebut bukan merupakan user!',
				showCancelButton: false,
				confirmButtonText: "Ok",
				icon: 'error'
				})
				return false;
			}
			kirimData = [data.payroll_uuid];
			let payroll_period = moment(data.payroll_period).format('MMMM YYYY');
			msg = `Apakah anda ingin mengirim info slip periode <b>${payroll_period}</b> kepada karyawan ini?`;
			processPayslip(`{{route('user.page.penggajian.kirim')}}?menu_id=${currentMenuId}`, msg);
		}

		function enableConfirmBtn() {
			if (isUpdated) {
				$(".btn-konfirmasi").attr('disabled', true);
			} else {
				$(".btn-konfirmasi").removeAttr('disabled');
			}
		}
	})
</script>