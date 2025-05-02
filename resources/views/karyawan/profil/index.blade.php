<?php
	$cbegindate = \Carbon\Carbon::parse($karyawan->karyawan_contract_begin);
	$dt_cbegin = $cbegindate->translatedFormat('d-m-Y');

	$dt_birthdate = null;
	$dt_bpjskesdate = null;
	$dt_bpjstkdate = null;
	$dt_probationdate = null;

	$tunjangan = $tunjangan_karyawan; 
	if($karyawan->karyawan_birthdate) {
		$birthdate = \Carbon\Carbon::parse($karyawan->karyawan_birthdate);
		$dt_birthdate = $birthdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_bpjskesdate) {
		$bpjskesdate = \Carbon\Carbon::parse($karyawan->karyawan_bpjskesdate);
		$dt_bpjskesdate = $bpjskesdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_bpjstkdate) {
		$bpjstkdate = \Carbon\Carbon::parse($karyawan->karyawan_bpjstkdate);
		$dt_bpjstkdate = $bpjstkdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_probation_end) {
		$probationdate = \Carbon\Carbon::parse($karyawan->karyawan_probation_end);
		$dt_probationdate = $probationdate->translatedFormat('d-m-Y');
	}

	$additional_info = [];
	$additional_arr = [];
	if($karyawan->karyawan_additionalinfo) {
		$additional_info = json_decode($karyawan->karyawan_additionalinfo);
		if($additional_info) {
			foreach($additional_info as $info) {
				$additional_arr[] = $info->nama;
			}
		}
	}
  
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
					</div>

					<div class="card-body">
						<form action="#" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawan" autocomplete="off">
							<div class="nav-align-top mb-4">
								<ul class="nav nav-tabs mb-3 nav-fill" role="tablist">
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-identitas" aria-controls="navs-tabs-justified-identitas" tabindex="-1"><i class="bx bx-user"></i> Data Identitas</button>
									</li>
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-pekerjaan" aria-controls="navs-tabs-justified-pekerjaan" tabindex="-1"><i class='bx bx-briefcase'></i> Data Pekerjaan</button>
									</li>
									<li class="nav-item" role="presentation">
									<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-penggajian" aria-controls="navs-tabs-justified-penggajian" tabindex="-1"><i class='bx bx-money'></i> Data Penggajian</button>
									</li>
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-tambahan" aria-controls="navs-tabs-justified-tambahan" tabindex="-1"><i class='bx bx-message-square-add' ></i> Data Tambahan</button>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade active show" id="navs-tabs-justified-identitas" role="tabpanel">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_enid" class="col-sm-2 lbl-req">ID Karyawan</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_enid}}" name="karyawan_enid" id="karyawan_enid" class="form-control" placeholder="Masukkan ID Karyawan">
													</div>
													<label for="karyawan_name" class="col-sm-2 lbl-req">Nama Lengkap</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_name}}" name="karyawan_name" id="karyawan_name" class="form-control" placeholder="Masukkan Nama Lengkap">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_nik" class="col-sm-2 lbl-req">NIK</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_nik}}" name="karyawan_nik" id="karyawan_nik" class="form-control nik-input" placeholder="Masukkan NIK">
													</div>
													<label for="karyawan_phone" class="col-sm-2">No. Telepon</label>
													<div class="col-sm-4">
														<input type="text" disabled name="karyawan_phone" value="{{$karyawan->karyawan_phone}}" id="karyawan_phone" class="form-control" placeholder="Masukkan No. Telepon">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_npwp" class="col-sm-2">NPWP</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_npwp}}" name="karyawan_npwp" id="karyawan_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
													</div>
													<label for="karyawan_email" class="col-sm-2">Email</label>
													<div class="col-sm-4">
														<input type="email" disabled value="{{$karyawan->karyawan_email}}" name="karyawan_email" id="karyawan_email" class="form-control" placeholder="Masukkan Email">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthplace" class="col-sm-2">Tempat Lahir</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_birthplace}}" name="karyawan_birthplace" id="karyawan_birthplace" class="form-control" placeholder="Masukkan Tempat Lahir">
													</div>
													<label for="karyawan_citizenship" class="col-sm-2">Kewarganegaraan</label>
													<div class="col-sm-4">
														<select disabled name="karyawan_citizenship" required id="karyawan_citizenship" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="WNI" {{ $karyawan->karyawan_citizenship == 'WNI' ? 'selected' : '' }}>WNI</option>
															<option value="WNA" {{ $karyawan->karyawan_citizenship == 'WNA' ? 'selected' : 'disabled' }}>WNA</option>
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthdate" class="col-sm-2">Tgl. Lahir</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{($dt_birthdate) ? $dt_birthdate : '' }}" name="karyawan_birthdate" id="karyawan_birthdate" class="form-control" placeholder="Masukkan Tanggal Lahir">
													</div>
													<label for="country_id" class="col-sm-2">Negara</label>
													<div class="col-sm-4">
														<select disabled name="country_id" required id="country_id" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_gender" class="col-sm-2 lbl-req">Jenis Kelamin</label>
													<div class="col-sm-4">
														<div class="form-check form-check-inline">
															<input disabled name="karyawan_gender" class="form-check-input" type="radio" value="L" id="karyawan_gender_l" {{$karyawan->karyawan_gender == 'L' ? 'checked' : ''}}>
															<label class="form-check-label" for="karyawan_gender_l"> Laki-laki </label>
														</div>
														<div class="form-check form-check-inline">
															<input disabled name="karyawan_gender" class="form-check-input" type="radio" value="P" id="karyawan_gender_p" {{$karyawan->karyawan_gender == 'P' ? 'checked' : ''}}>
															<label class="form-check-label" for="karyawan_gender_p"> Perempuan </label>
														</div>
													</div>
													<label for="karyawan_address" class="col-sm-2">Alamat</label>
													<div class="col-sm-4">
														<textarea name="karyawan_address" disabled id="karyawan_address" class="form-control" placeholder="Masukkan Alamat">{{$karyawan->karyawan_address}}</textarea>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="ptkp_id" class="col-sm-2 lbl-req">Status Perkawinan</label>
													<div class="col-sm-4">
														<select name="ptkp_id" disabled id="ptkp_id" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="{{$karyawan->ptkp->ptkp_id}}" selected>{{$karyawan->ptkp->ptkp_description}}</option>
														</select>
													</div>
													<label for="karyawan_photo" class="col-sm-2">Foto</label>
													<div class="col-sm-4">
														@if($karyawan->karyawan_photo)
														<div class="d-block rounded">
															<img src="{{$karyawan->karyawan_photo}}" alt="avatar" class="img-fluid">
														</div>
														@endif
													</div>
												</div>
												<div class="form-group row mb-3 userbx">
													@if($karyawan->karyawan_isuser == 1)
													<div class="col-sm-6">
														<div class="border py-3 px-3" style="border-color: #f6830f!important">
															<div class="form-check form-check-inline">
																<input name="karyawan_isuser" disabled checked class="form-check-input" type="checkbox"  value="1" id="karyawan_isuser">
																<label class="form-check-label" for="karyawan_isuser">Tambahkan Sebagai User</label>
															</div>
														</div>
													</div>
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="navs-tabs-justified-pekerjaan" role="tabpanel">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan</label>
													<div class="col-sm-4">
														<select name="karyawan_status" disabled id="karyawan_status" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="TETAP" {{$karyawan->karyawan_status == 'TETAP' ? 'selected' : ''}}>Karyawan Tetap</option>
															<option value="KONTRAK" {{$karyawan->karyawan_status == 'KONTRAK' ? 'selected' : ''}}>Karyawan Kontrak</option>
															<option value="PERCOBAAN" {{$karyawan->karyawan_status == 'PERCOBAAN' ? 'selected' : ''}}>Percobaan</option>
														</select>
													</div>
													<label for="karyawan_contract_begin" class="col-sm-2 lbl-req">Tgl. Masuk</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$dt_cbegin}}" name="karyawan_contract_begin" id="karyawan_contract_begin" class="form-control" placeholder="Tanggal Masuk">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_probation_end" class="col-sm-2 lbl-req">Percobaan Berakhir</label>
													<div class="col-sm-4">
														<input type="text" disabled {{($karyawan->karyawan_status == 'PERCOBAAN') ? '' : 'disabled'}} value="{{($karyawan->karyawan_status == 'PERCOBAAN') ? $dt_probationdate : ''}}" required name="karyawan_probation_end" id="karyawan_probation_end" class="form-control" placeholder="Tanggal Percobaan Berakhir">
													</div>
													<label for="karyawan_division" class="col-sm-2">Divisi</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled name="karyawan_division" id="karyawan_division" data-placeholder="-: Pilih Data :-">
														@if($karyawan->divisi)
														<option value="{{$karyawan->divisi->karyawandivisi_id}}" selected>{{$karyawan->divisi->karyawandivisi_name}}</option>
														@endif
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_manager" class="col-sm-2">Supervisor</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled data-val="{{json_encode($karyawan->manager)}}" name="karyawan_manager" id="karyawan_manager" data-placeholder="-: Pilih Data :-">
														@if($karyawan->manager)
														<option value="{{$karyawan->manager->karyawan_id}}" selected>{{$karyawan->manager->karyawan_name}}</option>
														@endif
														</select>
													</div>
													<label for="karyawan_position" class="col-sm-2">Jabatan</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled name="karyawan_position" id="karyawan_position" data-placeholder="-: Pilih Data :-">
														@if($karyawan->jabatan)
														<option value="{{$karyawan->jabatan->karyawanjabatan_id}}" selected>{{$karyawan->jabatan->karyawanjabatan_name}}</option>
														@endif
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<div class="col-sm-6"></div>
													<label for="karyawan_attendance" class="col-sm-2">Absensi</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled name="karyawan_attendance" id="karyawan_attendance" data-placeholder="-: Pilih Data :-">
														@if($karyawan->attendance)
															<option value="{{$karyawan->attendance->attendance_id}}" selected>{{$karyawan->attendance->attendance_description}}</option>
														@endif
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="navs-tabs-justified-penggajian" role="tabpanel">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_salary" class="col-sm-2 lbl-req">Gaji Pokok</label>
													<div class="col-sm-4">
														<input type="text" disabled value="{{$karyawan->karyawan_salary}}" name="karyawan_salary" value="0" id="karyawan_salary" class="form-control" placeholder="Masukkan Gaji Pokok">
													</div>
													<label for="karyawan_calculation_method" class="col-sm-2 lbl-req">Metode PPh 21</label>
													<div class="col-sm-4">
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled {{($karyawan->karyawan_calculation_method == 'GROSS') ? 'checked' : ''}} class="form-check-input" type="radio" value="GROSS" id="karyawan_calculation_method_g">
															<label class="form-check-label" for="karyawan_calculation_method_g">Gross</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled {{($karyawan->karyawan_calculation_method == 'GROSS_UP') ? 'checked' : ''}} class="form-check-input" type="radio" value="GROSS_UP" id="karyawan_calculation_method_gu">
															<label class="form-check-label" for="karyawan_calculation_method_gu">Gross Up</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled {{($karyawan->karyawan_calculation_method == 'NETT') ? 'checked' : ''}} class="form-check-input" type="radio" value="NETT" id="karyawan_calculation_method_net">
															<label class="form-check-label" for="karyawan_calculation_method_net">Nett</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled class="form-check-input" type="radio" value="MIX" id="karyawan_calculation_method_mix">
															<label class="form-check-label" for="karyawan_calculation_method_mix">MIX</label>
														</div>
													</div>
												</div>

												<div class="form-group row mb-3">
													<label for="karyawan_allowance" class="col-sm-2">Tunjangan</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled name="karyawan_allowance[]" class="form-control" id="karyawan_allowance" data-placeholder="-: Pilih Data :-">
														@if($tunjangan)
															@foreach($tunjangan as $tj)
															<option value="{{$tj->sttunjangankaryawan_id}}" selected>{{$tj->sttunjangankaryawan_name}}</option>
															@endforeach
														@endif
														</select>
													</div>
													<label for="karyawan_payroll" class="col-sm-2 lbl-req">Penggajian</label>
													<div class="col-sm-4">
														<select style="width: 100%;" disabled name="karyawan_payroll" class="form-control" id="karyawan_payroll" data-placeholder="-: Pilih Data :-">
															<option value="{{$karyawan->penggajian->stpenggajiankaryawan_id}}" selected>{{$karyawan->penggajian->stpenggajiankaryawan_name}}</option>
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_bankid" class="col-sm-2 lbl-req">Nama Bank</label>
													<div class="col-sm-4">
														<select name="karyawan_bankid" disabled style="width: 100%;" id="karyawan_bankid" class="form-control" data-placeholder="-:Pilih Data:-">
															@if($karyawan->bank)
															<option value="{{$karyawan->bank->bank_id}}" selected>{{$karyawan->bank->bank_name}}</option>
															@endif
														</select>
													</div>
													<label for="karyawan_banknokartu" class="col-sm-2 lbl-req">No. Rekening</label>
													<div class="col-sm-4">
														<input disabled name="karyawan_banknokartu" value="{{$karyawan->karyawan_bankno}}" id="karyawan_banknokartu" class="form-control" placeholder="No. Rekening">
													</div>
												</div>
												<?php if ($stbpjskaryawan_data) :
													$bpjskes = false;
													$bpjstk = false;
													foreach ($stbpjskaryawan_data as $stbpjs) {
														if ($stbpjs->type == 'KESEHATAN') {
															$bpjskes = true;
														}
														if ($stbpjs->type == 'TENAGA_KERJA') {
															$bpjstk = true;
														}
													}
												?>
													<div class="divider divider-info">
														<div class="divider-text text-bold">BPJS Kesehatan</div>
													</div>
													@if($bpjskes)
													<div class="form-group row mb-3">
														<label for="karyawan_isgetbpjskes" class="col-sm-3">Mendapatkan BPJS?</label>
														<div class="col-sm-3">
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjskes" disabled {{$karyawan->karyawan_isbpjskes == 1 ? 'checked' : ''}} class="form-check-input" type="radio" value="1" id="karyawan_isgetbpjskes_y">
																<label class="form-check-label" for="karyawan_isgetbpjskes_y">Ya</label>
															</div>
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjskes" disabled {{$karyawan->karyawan_isbpjskes == 0 ? 'checked' : ''}} class="form-check-input" type="radio" value="0" id="karyawan_isgetbpjskes_t">
																<label class="form-check-label" for="karyawan_isgetbpjskes_t">Tidak</label>
															</div>
														</div>
													</div>
													<div class="form-group row mb-3 karyawan_isgetbpjskes_box" style="{{$karyawan->karyawan_isbpjskes == 0 ? 'display:none;' : ''}}">
														<label for="karyawan_bpjskesdate" class="col-sm-2 lbl-req">Tgl. Berlaku</label>
														<div class="col-sm-4">
															<input type="text" disabled value="{{$dt_bpjskesdate}}" name="karyawan_bpjskesdate" id="karyawan_bpjskesdate" class="form-control" placeholder="Masukkan Tanggal Berlaku">
														</div>
														<label for="karyawan_bpjskesno" class="col-sm-2 lbl-req">No. Kartu</label>
														<div class="col-sm-4">
															<input type="text" disabled value="{{$karyawan->karyawan_bpjskesno}}" name="karyawan_bpjskesno" id="karyawan_bpjskesno" class="form-control" placeholder="Masukkan Nomor Kartu">
														</div>
													</div>
													@endif

													@if($bpjstk)
													<div class="divider divider-info">
														<div class="divider-text text-bold">BPJS Tenaga Kerja</div>
													</div>
													<div class="form-group row mb-3">
														<label for="karyawan_isgetbpjstk" class="col-sm-3">Mendapatkan BPJS?</label>
														<div class="col-sm-3">
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjstk" disabled {{$karyawan->karyawan_isbpjstk == 1 ? 'checked' : ''}} class="form-check-input" type="radio" value="1" id="karyawan_isgetbpjstk_y">
																<label class="form-check-label" for="karyawan_isgetbpjstk_y">Ya</label>
															</div>
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjstk" disabled {{$karyawan->karyawan_isbpjstk == 0 ? 'checked' : ''}} class="form-check-input" type="radio" value="0" id="karyawan_isgetbpjstk_t">
																<label class="form-check-label" for="karyawan_isgetbpjstk_t">Tidak</label>
															</div>
														</div>
													</div>
													<div class="form-group row mb-3 karyawan_isgetbpjstk_box" style="{{$karyawan->karyawan_isbpjstk == 0 ? 'display:none;' : ''}}">
														<label for="karyawan_bpjstkdate" class="col-sm-2 lbl-req">Tgl. Berlaku</label>
														<div class="col-sm-4">
															<input type="text" value="{{$dt_bpjstkdate}}" disabled name="karyawan_bpjstkdate" id="karyawan_bpjstkdate" class="form-control" placeholder="Masukkan Tanggal Berlaku">
														</div>
														<label for="karyawan_bpjstkno" class="col-sm-2 lbl-req">No. Kartu</label>
														<div class="col-sm-4">
															<input type="text" value="{{$karyawan->karyawan_bpjstkno}}" disabled name="karyawan_bpjstkno" id="karyawan_bpjstkno" class="form-control" placeholder="Masukkan Nomor Kartu">
														</div>
													</div>
													@endif
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="navs-tabs-justified-tambahan" role="tabpanel">
										<div class="row">
											<div class="col-lg-12">
												<div id="formtambahanbox">
													<?php 
													$fields = null;
													if($karyawan_infofield) :
														$i = 0;
														$fields = ($karyawan_infofield->karyawaninfofield_fields) ? explode(',', $karyawan_infofield->karyawaninfofield_fields) : null;
														if($fields) :
															foreach($fields as $ifield) :
																$idx = (array_keys($additional_arr, $ifield)) ? array_keys($additional_arr, $ifield)[0] : null;
																$idxfieldvalue = isset($additional_info[$idx]) ? $additional_info[$idx]->keterangan : null;
													?>
														<div class="form-group row mb-3 fieldtambahan">
															<div class="col-sm-7">
																<div class="input-group">
																	<input type="hidden" disabled value="{{$ifield}}" name="karyawan_tambahan[{{$i}}][nama]" id="karyawan_tambahannama_{{$i}}" class="form-control" placeholder="Masukkan Nama">
																	<span class="input-group-text">{{$ifield}}</span>
																	<input type="text" 
                                  disabled
																	value="{{$idxfieldvalue}}"
																	name="karyawan_tambahan[{{$i}}][keterangan]" id="karyawan_tambahanketerangan_{{$i}}" class="form-control" placeholder="Masukkan Keterangan">
																</div>
															</div>
															<hr class="mt-3 mb-3">
														</div>
													<?php $i++;
															endforeach;
														else: ?>
                            <p>Tidak ada data.</p>
                          <?php endif;
                         endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- <div class="text-right">
								<button type="submit" class="btn btn-warning btn-sm">Simpan</button>
							</div> -->
						</form>
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
		let karyawanId = "{{$karyawan->karyawan_id}}";

    $("#ptkp_id").select2({
			ajax: {
				url: "{{route('master.ptkp.select')}}",
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
					return {
						results: $.map(select2GroupBy(items, 'ptkp_marriage_status'), function(item, key) {
							let children = [];
							for (var k in item) {
								let childItem = item[k];
								childItem.id = item[k].ptkp_id;
								childItem.text = item[k].ptkp_description;
								children.push(childItem);
							}
							return {
								text: key,
								children: children,
							}
						})
					}
				},
			},
		});

    $("#karyawan_citizenship").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if (data.id == 'WNA') {
				$("#country_id").removeAttr('disabled').attr('required', true);
			} else {
				$("#country_id").removeAttr('required').attr('disabled', true);
			}
		});
    
		$("#country_id").select2({
			ajax: {
				url: "{{route('master.negara.select')}}",
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
						item.id = item.country_id;
						item.text = item.country_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

		$("#karyawan_division").select2({
			tags: true,
			ajax: {
				url: "{{route('user.page.karyawan.divisi.select')}}",
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
						item.id = item.karyawandivisi_id;
						item.text = item.karyawandivisi_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});
		$("#karyawan_position").select2({
			tags: true,
			ajax: {
				url: "{{route('user.page.karyawan.jabatan.select')}}",
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
						item.id = item.karyawanjabatan_id;
						item.text = item.karyawanjabatan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

    $("#karyawan_status").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if(data.id == 'PERCOBAAN') {
				$("#karyawan_probation_end").removeAttr('disabled');
			} else {
				$("#karyawan_probation_end").attr('disabled', true);
			}
		});

		$("#karyawan_attendance").select2({
			ajax: {
				url: "{{route('user.page.pengaturan.kehadiran.select')}}",
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
						item.id = item.attendance_id;
						item.text = item.attendance_description;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

		$("#karyawan_manager").select2({
			ajax: {
				url: "{{route('user.page.karyawan.select')}}",
				data: function(params) {
					var query = {
						q: params.term,
						is_manager: 1,
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
		});

		$("#karyawan_allowance").select2({
			ajax: {
				url: "{{route('user.page.pengaturan.tunjangan.select')}}",
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
						item.id = item.sttunjangankaryawan_id;
						item.text = item.sttunjangankaryawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

		$("#karyawan_payroll").select2({
			ajax: {
				url: "{{route('user.page.pengaturan.penggajian.select')}}",
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
						item.id = item.stpenggajiankaryawan_id;
						item.text = item.stpenggajiankaryawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

		$("#karyawan_bankid").select2({
			ajax: {
				url: "{{route('master.bank.select')}}",
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
						item.id = item.bank_id;
						item.text = item.bank_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
		});

		let karyawanTaxTypeDefault = null;
		let optAutoNumeric = {
			currencySymbol: "Rp. ",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: true,
			modifyValueOnWheel: false,
		};
		let [karyawanSalaryNum] = AutoNumeric.multiple(["#karyawan_salary"], optAutoNumeric);
		
		$(`#karyawan_birthdate, #karyawan_contract_begin, #karyawan_probation_end, #karyawan_bpjskesdate, #karyawan_bpjstkdate`).daterangepicker({
			autoUpdateInput: false,
			autoApply: true,
			singleDatePicker: true,
			showDropdowns: true,
			locale: {
				format: 'DD-MM-YYYY'
			}
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD-MM-YYYY'));
		});

		let formKaryawan = $("#formKaryawan").validate({
			// ignore: [],
			errorPlacement: function(error, element) {
				// console.log(element);
				var isInputGroup = $(element).parent();
				console.log('element', element)
				let elem = $(element);
				console.log('elem', elem)
				if (elem.hasClass("select2-hidden-accessible")) {
					// element = $("#select2-" + elem.attr("id") + "-container").parent(); 
					element = $("#select2-" + elem.attr("id") + "-container").parents('.select2-container');
					console.log('element', element);
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
			rules: {
				karyawan_nik: {
					required: true,
					number: true,
					minlength: 16,
					maxlength: 16,
				},
				ptkp_id: {
					required: true,
				},
				karyawan_phone: {
					// required: true,
					number: true,
					rangelength: [9, 14],
				},
				karyawan_email: {
					email: true,
				}
			},
			submitHandler: function(form) {
				$(".spinner-box").css({
					'display': 'table'
				});

				let karyawan_salary = karyawanSalaryNum.getNumber();
				let formData = new FormData();
				let dataArr = $("#formKaryawan").serializeArray();

				for (let i = 0; i < dataArr.length; i++) {
					formData.append(dataArr[i].name, dataArr[i].value);
				}

				formData.append('_token', $("meta[name=csrf-token]").attr('content'));
				formData.append('karyawan_salary', karyawan_salary);
				formData.append('karyawan_photo', $('#karyawan_photo')[0].files[0]);
				// return false;
				$.ajax({
					method: form.method,
					url: form.action,
					processData: false, // tell jQuery not to process the data            
					contentType: false, // tell jQuery not to set contentType 
					data: formData,
					error: function(error) {
						$(".spinner-box").fadeOut();
						console.log(error.responseJSON);
						if (error.responseJSON) {
							let errs = error.responseJSON.errors;
							let errorName = [];
							if (errs) {
								let errtab = 0;

								if(errs['karyawan_payroll']) {
									errtab = 2;
									formKaryawan.showErrors({'karyawan_payroll': errs['karyawan_payroll']});
								}

								if(errs['karyawan_calculation_method']) {
									errtab = 2;
									formKaryawan.showErrors({'karyawan_calculation_method': errs['karyawan_calculation_method']});
								}

								if(errs['karyawan_bankid']) {
									errtab = 2;
									formKaryawan.showErrors({'karyawan_bankid': errs['karyawan_bankid']});
								}

								if(errs['karyawan_banknokartu']) {
									errtab = 2;
									formKaryawan.showErrors({'karyawan_banknokartu': errs['karyawan_banknokartu']});
								}

								if(errs['karyawan_status']) {
									errtab = 1;
									formKaryawan.showErrors({'karyawan_status': errs['karyawan_status']});
								}

								if(errs['karyawan_contract_begin']) {
									errtab = 1;
									formKaryawan.showErrors({'karyawan_contract_begin': errs['karyawan_contract_begin']});
								}

								if(errs['karyawan_email']) {
									errtab = 0;
									formKaryawan.showErrors({'karyawan_email': errs['karyawan_email']});
								}
								if(errs['karyawan_enid']) {
									errtab = 0;
									formKaryawan.showErrors({'karyawan_enid': errs['karyawan_enid']});
								}
								if(errs['karyawan_name']) {
									errtab = 0;
									formKaryawan.showErrors({'karyawan_name': errs['karyawan_name']});
								}
								if(errs['karyawan_nik']) {
									errtab = 0;
									formKaryawan.showErrors({'karyawan_nik': errs['karyawan_nik']});
								}
								if(errs['ptkp_id']) {
									errtab = 0;
									formKaryawan.showErrors({'ptkp_id': errs['ptkp_id']});
								}

							} else {
								errorName = [error.responseJSON.message];
							
								Swal.fire({
									showCancelButton: false,
									confirmButtonText: "Ok",
									icon: 'error',
									html: errorName
								})
							}
						}
					},
					success: function(response) {
						console.log(response, 'response')
						$(".spinner-box").fadeOut();
						if (!response.success) {
							toastr.error(response.message);
							return false;
						}

						toastr.success(response.message);

						let href = "{{route('user.page.karyawan.index')}}";
						loadPage(href);
					}
				})
			},
		})
		
		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>