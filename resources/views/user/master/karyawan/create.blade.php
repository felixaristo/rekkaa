<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<!-- Basic Layout & Basic with Icons -->
			<div class="row">
				<div class="col-xxl">
					<div class="card-header d-flex align-items-center justify-content-between">
						<h5 class="mb-0">{{$title}}</h5>
						<!-- <nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item">
									<a href="{{route('user.page.karyawan.index')}}" class="rekkaa-page-link">Karyawan</a>
								</li>
								<li class="breadcrumb-item active">{{$title}}</li>
							</ol>
						</nav> -->
					</div>

					<div class="card-body">
						<div id="smartwizard" dir="rtl-">
							<ul class="nav nav-progress">
								<li class="nav-item">
									<a class="nav-link" href="#step-1">
										<span class="num">1</span>
										<i class="bx bx-user"></i>
										Data Identitas
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#step-2">
										<span class="num">2</span>
										<i class='bx bx-briefcase'></i>
										Data Pekerjaan
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " href="#step-3">
										<span class="num">3</span>
										<i class='bx bx-money'></i>
										Data Penggajian
									</a>
								</li>
								<!-- <li class="nav-item">
									<a class="nav-link " href="#step-3">
										<span class="num">4</span>
										<i class='bx bx-message-square-add' ></i>
										Data Tambahan
									</a>
								</li> -->
							</ul>

							<div class="tab-content" style="padding: 0;">
								<div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
									<form action="{{route('user.page.karyawan.store')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawan" autocomplete="off">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_enid" class="col-sm-2 lbl-req">ID Karyawan</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_enid" id="karyawan_enid" class="form-control" placeholder="Masukkan ID Karyawan">
													</div>
													<label for="karyawan_name" class="col-sm-2 lbl-req">Nama Lengkap</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_name" id="karyawan_name" class="form-control" placeholder="Masukkan Nama Lengkap">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_nik" class="col-sm-2 lbl-req">NIK</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_nik" id="karyawan_nik" class="form-control nik-input" placeholder="Masukkan NIK">
													</div>
													<label for="karyawan_phone" class="col-sm-2">No. Telepon</label>
													<div class="col-sm-4">
														<input type="text" name="karyawan_phone" id="karyawan_phone" class="form-control" placeholder="Masukkan No. Telepon">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_npwp" id="karyawan_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
													</div>
													<label for="karyawan_email" class="col-sm-2">Email</label>
													<div class="col-sm-4">
														<input type="email" name="karyawan_email" id="karyawan_email" class="form-control" placeholder="Masukkan Email">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthplace" class="col-sm-2">Tempat Lahir</label>
													<div class="col-sm-4">
														<input type="text" name="karyawan_birthplace" id="karyawan_birthplace" class="form-control" placeholder="Masukkan Tempat Lahir">
													</div>
													<label for="karyawan_citizenship" class="col-sm-2">Kewarganegaraan</label>
													<div class="col-sm-4">
														<select required name="karyawan_citizenship" required id="karyawan_citizenship" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="WNI" selected>WNI</option>
															<option value="WNA" disabled>WNA</option>
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthdate" class="col-sm-2">Tgl. Lahir</label>
													<div class="col-sm-4">
														<input type="text" name="karyawan_birthdate" id="karyawan_birthdate" class="form-control" placeholder="Masukkan Tanggal Lahir">
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
															<input name="karyawan_gender" class="form-check-input" type="radio" value="L" id="karyawan_gender_l" checked="">
															<label class="form-check-label" for="karyawan_gender_l"> Laki-laki </label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_gender" class="form-check-input" type="radio" value="P" id="karyawan_gender_p">
															<label class="form-check-label" for="karyawan_gender_p"> Perempuan </label>
														</div>
													</div>
													<label for="karyawan_address" class="col-sm-2">Alamat</label>
													<div class="col-sm-4">
														<textarea name="karyawan_address" id="karyawan_address" class="form-control" placeholder="Masukkan Alamat"></textarea>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="ptkp_id" class="col-sm-2 lbl-req">Status Perkawinan</label>
													<div class="col-sm-4">
														<select name="ptkp_id" required id="ptkp_id" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
													</div>
													<label for="karyawan_photo" class="col-sm-2">Upload Foto</label>
													<div class="col-sm-4">
														<input name="karyawan_photo" class="form-control" type="file" id="karyawan_photo">
														<span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span>
													</div>
												</div>
												<div class="form-group row mb-3 userbx">
													<div class="col-sm-6">
														<div class="border py-3 px-3" style="border-color: #f6830f!important">
															<div class="form-check form-check-inline">
																<input name="karyawan_isuser" disabled class="form-check-input" type="checkbox"  value="1" id="karyawan_isuser">
																<label class="form-check-label" for="karyawan_isuser">Tambahkan Sebagai User</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
									<div style="font-style: italic;">
										<p style="color: red; margin: 0;">Catatan :</p>
										<p style="color: grey; margin: 0;">- Data dengan simbol (<span style="color: red;">*</span>) tidak boleh dikosongkan.</p>
										<p style="color: grey; margin: 0;">- Mohon isi dengan <span style="color: red;">00.000.000.0-000.000</span> jika tidak memimliki NPWP.</p>
										<p style="color: grey; margin: 0;">- Perubahan PTKP dan Metode PPh 21 hanya bisa dilakukan di Januari tahun berikutnya.</p>
										<p style="color: grey; margin: 0;">- "Tambahkan Sebagai User" hanya bisa diaktifkan jika email karyawan tersedia.</p>
									</div>
								</div>
								<div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
									<form action="{{route('user.page.karyawan.store')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formPekerjaan" autocomplete="off">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan</label>
													<div class="col-sm-4">
														<select name="karyawan_status" required id="karyawan_status" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="TETAP">Karyawan Tetap</option>
															<option value="KONTRAK">Karyawan Kontrak</option>
															<option value="PERCOBAAN">Percobaan</option>
														</select>
													</div>
													<label for="karyawan_contract_begin" class="col-sm-2 lbl-req">Tgl. Masuk</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_contract_begin" id="karyawan_contract_begin" class="form-control" placeholder="Tanggal Masuk">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_contract_end" class="col-sm-2">Kontrak Berakhir</label>
													<div class="col-sm-4">
														<input type="text" disabled required name="karyawan_contract_end" id="karyawan_contract_end" class="form-control" placeholder="Tanggal Kontrak Berakhir">
													</div>
													<label for="karyawan_division" class="col-sm-2">Divisi</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" name="karyawan_division" id="karyawan_division" data-placeholder="-: Pilih Data :-"></select>
															<button class="btn btn-sm btn-outline-info" type="button" id="btn-division"><i class="bx bx-plus"></i></button>
														</div>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_manager" class="col-sm-2">Supervisor</label>
													<div class="col-sm-4">
														<select style="width: 100%;" name="karyawan_manager" id="karyawan_manager" data-placeholder="-: Pilih Data :-" data-allow-clear="true"></select>
													</div>

													<label for="karyawan_position" class="col-sm-2">Jabatan</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" name="karyawan_position" id="karyawan_position" data-placeholder="-: Pilih Data :-"></select>
															<button class="btn btn-sm btn-outline-info" type="button" id="btn-position"><i class="bx bx-plus"></i></button>
														</div>
													</div>
													
												</div>
												<!-- <div class="form-group row mb-3">
													<div class="col-sm-6"></div>
													<label for="karyawan_attendance" class="col-sm-2">Absensi</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" name="karyawan_attendance" id="karyawan_attendance" data-placeholder="-: Pilih Data :-">
															@if($st_attendance)
																@foreach($st_attendance as $attendance)
																<option value="{{$attendance->attendance_id}}" selected>{{$attendance->attendance_description}}</option>
																@endforeach
															@endif</select>
															<button type="button" id="btn-attendance" class="btn btn-sm btn-outline-info"><i class="bx bx-plus"></i></button>
														</div>
													</div>
												</div> -->
											</div>
										</div>
									</form>
									<div style="font-style: italic;">
										<p style="color: red; margin: 0;">Catatan :</p>
										<p style="color: grey; margin: 0;">- Data dengan simbol (<span style="color: red;">*</span>) tidak boleh dikosongkan.</p>
										<p style="color: grey; margin: 0;">- Mohon isi dengan <span style="color: red;">00.000.000.0-000.000</span> jika tidak memimliki NPWP.</p>
									</div>
								</div>
								<div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
									<form action="{{route('user.page.karyawan.store')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formPenggajian" autocomplete="off">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_salary" class="col-sm-2 lbl-req">Gaji Pokok</label>
													<div class="col-sm-4">
														<input type="text" required name="karyawan_salary" value="0" id="karyawan_salary" class="form-control" placeholder="Masukkan Gaji Pokok">
													</div>
													<!-- <label for="karyawan_payroll" class="col-sm-2 lbl-req">Penggajian</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" required name="karyawan_payroll" class="form-control" id="karyawan_payroll" data-placeholder="-: Pilih Data :-">
															@if($st_penggajian)
																@foreach($st_penggajian as $penggajian)
																<option value="{{$penggajian->stpenggajiankaryawan_id}}" selected>{{$penggajian->stpenggajiankaryawan_name}}</option>
																@endforeach
															@endif</select>
															<button type="button"  id="btn-penggajian" class="btn btn-sm btn-outline-info"><i class="bx bx-plus"></i></button>
														</div>
													</div> -->
													<label for="karyawan_calculation_method" class="col-sm-2 lbl-req">Metode PPh 21</label>
													<div class="col-sm-4">
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" checked class="form-check-input" type="radio" value="GROSS" id="karyawan_calculation_method_g">
															<label class="form-check-label" for="karyawan_calculation_method_g">Gross</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" class="form-check-input" type="radio" value="GROSS_UP" id="karyawan_calculation_method_gu">
															<label class="form-check-label" for="karyawan_calculation_method_gu">Gross Up</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" class="form-check-input" type="radio" value="NETT" id="karyawan_calculation_method_net">
															<label class="form-check-label" for="karyawan_calculation_method_net">Nett</label>
														</div>
														<!-- <div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled class="form-check-input" type="radio" value="MIX" id="karyawan_calculation_method_mix">
															<label class="form-check-label" for="karyawan_calculation_method_mix">MIX</label>
														</div> -->
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_allowance" class="col-sm-2">Tunjangan</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" multiple name="karyawan_allowance[]" class="form-control" id="karyawan_allowance" data-placeholder="-: Pilih Data :-">
															@if($sttunjangankaryawan_data)
																@foreach($sttunjangankaryawan_data as $tj)
																<?php 
																	$tjlocked = '';
																	if($tj->sttunjangankaryawan_used_for == 1) :
																		$tjlocked = 'locked="locked"';
																?>
																<option value="{{$tj->sttunjangankaryawan_id}}" selected {!! $tjlocked !!}>{{$tj->sttunjangankaryawan_name}}</option>
																<?php endif; ?>
																@endforeach
															@endif</select>
															<button type="button" id="btn-tunjangan" class="btn btn-sm btn-outline-info"><i class="bx bx-plus"></i></button>
														</div>
													</div>
													<label for="karyawan_deduction" class="col-sm-2">Potongan</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" multiple name="karyawan_deduction[]" class="form-control" id="karyawan_deduction" data-placeholder="-: Pilih Data :-">
															@if($stpotongankaryawan_data)
																@foreach($stpotongankaryawan_data as $pt)
																<?php 
																	$ptlocked = '';
																	if($pt->stpotongankaryawan_used_for == 1) :
																		$ptlocked = 'locked="locked"';
																?>
																<option value="{{$pt->stpotongankaryawan_id}}" selected {!! $ptlocked !!}>{{$pt->stpotongankaryawan_name}}</option>
																<?php endif; ?>
																@endforeach
															@endif</select>
															<button type="button" id="btn-potongan" class="btn btn-sm btn-outline-info"><i class="bx bx-plus"></i></button>
														</div>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_bankid" class="col-sm-2">Nama Bank</label>
													<div class="col-sm-4">
														<select name="karyawan_bankid" style="width: 100%;" id="karyawan_bankid" class="form-control" data-placeholder="-:Pilih Data:-"></select>
													</div>
													<label for="karyawan_banknokartu" class="col-sm-2">No. Rekening</label>
													<div class="col-sm-4">
														<input name="karyawan_banknokartu" disabled id="karyawan_banknokartu" class="form-control" placeholder="No. Rekening">
													</div>
												</div>
												<!-- <div class="form-group row mb-3">
													<label for="karyawan_calculation_method" class="col-sm-2 lbl-req">Metode PPh 21</label>
													<div class="col-sm-4">
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" checked class="form-check-input" type="radio" value="GROSS" id="karyawan_calculation_method_g">
															<label class="form-check-label" for="karyawan_calculation_method_g">Gross</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" class="form-check-input" type="radio" value="GROSS_UP" id="karyawan_calculation_method_gu">
															<label class="form-check-label" for="karyawan_calculation_method_gu">Gross Up</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" class="form-check-input" type="radio" value="NETT" id="karyawan_calculation_method_net">
															<label class="form-check-label" for="karyawan_calculation_method_net">Nett</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled class="form-check-input" type="radio" value="MIX" id="karyawan_calculation_method_mix">
															<label class="form-check-label" for="karyawan_calculation_method_mix">MIX</label>
														</div>
													</div>
												</div> -->
												<?php if ($stbpjskaryawan_data) :
													$bpjskes = false;
													$bpjstk = false;
													$stbpjskes_lainnya = false;
													$bpjskes_lainnya = 0;
													$stbpjstk_lainnya = false;
													$bpjstk_lainnya = 0;
													foreach ($stbpjskaryawan_data as $stbpjs) {
														if ($stbpjs->type == 'KESEHATAN') {
															$bpjskes = true;
															if($stbpjs->name == 'LAINNYA') {
																$stbpjskes_lainnya = true;
																$bpjskes_lainnya = $stbpjs->value;
															}
														}
														if ($stbpjs->type == 'TENAGA_KERJA') {
															$bpjstk = true;
															if($stbpjs->name == 'LAINNYA') {
																$stbpjstk_lainnya = true;
																$bpjstk_lainnya = $stbpjs->value;
															}
														}
													}
												?>
													@if($bpjskes)
													<div class="divider divider-info">
														<div class="divider-text text-bold">BPJS Kesehatan</div>
													</div>
													<div class="form-group row mb-3">
														<label for="karyawan_isgetbpjskes" class="col-sm-3">Mendapatkan BPJS?</label>
														<div class="col-sm-3">
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjskes" checked class="form-check-input" type="radio" value="1" id="karyawan_isgetbpjskes_y">
																<label class="form-check-label" for="karyawan_isgetbpjskes_y">Ya</label>
															</div>
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjskes" class="form-check-input" type="radio" value="0" id="karyawan_isgetbpjskes_t">
																<label class="form-check-label" for="karyawan_isgetbpjskes_t">Tidak</label>
															</div>
														</div>
													</div>
													<div class="form-group row mb-3 karyawan_isgetbpjskes_box">
														<label for="karyawan_bpjskesdate" class="col-sm-2 lbl-req">Tgl. Berlaku</label>
														<div class="col-sm-4">
															<input type="text" required name="karyawan_bpjskesdate" id="karyawan_bpjskesdate" class="form-control" placeholder="Masukkan Tanggal Berlaku">
														</div>
														<label for="karyawan_bpjskesno" class="col-sm-2 lbl-req">No. Kartu</label>
														<div class="col-sm-4">
															<input type="text" required name="karyawan_bpjskesno" id="karyawan_bpjskesno" class="form-control" placeholder="Masukkan Nomor Kartu">
														</div>
													</div>
													@if($stbpjskes_lainnya)
													<div class="form-group row mb-3 karyawan_isgetbpjskes_box">
														<label for="karyawan_bpjskeslainnya" class="col-sm-2 lbl-req">Maks. Nominal</label>
														<div class="col-sm-3">
															<input type="text" required value="{{$bpjskes_lainnya}}" name="karyawan_bpjskeslainnya" id="karyawan_bpjskeslainnya" class="form-control" placeholder="Masukkan Maksimal Nominal">
														</div>
													</div>
													@endif
													@endif

													@if($bpjstk)
													<div class="divider divider-info">
														<div class="divider-text text-bold">BPJS Tenaga Kerja</div>
													</div>
													<div class="form-group row mb-3">
														<label for="karyawan_isgetbpjstk" class="col-sm-3">Mendapatkan BPJS?</label>
														<div class="col-sm-3">
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjstk" checked class="form-check-input" type="radio" value="1" id="karyawan_isgetbpjstk_y">
																<label class="form-check-label" for="karyawan_isgetbpjstk_y">Ya</label>
															</div>
															<div class="form-check form-check-inline">
																<input name="karyawan_isgetbpjstk" class="form-check-input" type="radio" value="0" id="karyawan_isgetbpjstk_t">
																<label class="form-check-label" for="karyawan_isgetbpjstk_t">Tidak</label>
															</div>
														</div>
													</div>
													<div class="form-group row mb-3 karyawan_isgetbpjstk_box">
														<label for="karyawan_bpjstkdate" class="col-sm-2 lbl-req">Tgl. Berlaku</label>
														<div class="col-sm-4">
															<input type="text" required name="karyawan_bpjstkdate" id="karyawan_bpjstkdate" class="form-control" placeholder="Masukkan Tanggal Berlaku">
														</div>
														<label for="karyawan_bpjstkno" class="col-sm-2 lbl-req">No. Kartu</label>
														<div class="col-sm-4">
															<input type="text" required name="karyawan_bpjstkno" id="karyawan_bpjstkno" class="form-control" placeholder="Masukkan Nomor Kartu">
														</div>
													</div>
													@if($stbpjstk_lainnya)
													<div class="form-group row mb-3 karyawan_isgetbpjstk_box">
														<label for="karyawan_bpjstklainnya" class="col-sm-2 lbl-req">Maks. Nominal</label>
														<div class="col-sm-3">
															<input type="text" required value="{{$bpjstk_lainnya}}" name="karyawan_bpjstklainnya" id="karyawan_bpjstklainnya" class="form-control" placeholder="Masukkan Maksimal Nominal">
														</div>
													</div>
													@endif
													@endif
												<?php endif; ?>
											</div>
										</div>
									</form>
									<div style="font-style: italic;">
										<p style="color: red; margin: 0;">Catatan :</p>
										<p style="color: grey; margin: 0;">- Data dengan simbol (<span style="color: red;">*</span>) tidak boleh dikosongkan.</p>
										<p style="color: grey; margin: 0;">- Mohon isi dengan <span style="color: red;">00.000.000.0-000.000</span> jika tidak memimliki NPWP.</p>
									</div>
								</div>
								<!-- <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
									<form action="{{route('user.page.karyawan.store')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formTambahan" autocomplete="off">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_tambahankey" class="col-sm-2">Data Tambahan</label>
													<div class="col-sm-5">
														<div class="input-group">
															<input type="text" name="karyawan_tambahankey" id="karyawan_tambahankey" class="form-control" placeholder="Masukkan Data Tambahan">
															<button type="button" class="btn btn-sm btn-outline-info" id="btn-tambahan"><i class="bx bx-plus"></i></button>
														</div>
													</div>
													<hr class="mt-3 mb-3">
												</div>
												<div id="formtambahanbox">
													<?php 
													$fields = null;
													if($karyawan_infofield) :
														$i = 0;
														$fields = ($karyawan_infofield->karyawaninfofield_fields) ? explode(',', $karyawan_infofield->karyawaninfofield_fields) : null;
														if($fields) :
															foreach($fields as $ifield) :
													?>
														<div class="form-group row mb-3 fieldtambahan">
															<div class="col-sm-7">
																<div class="input-group">
																	<input type="hidden" value="{{$ifield}}" name="karyawan_tambahan[{{$i}}][nama]" id="karyawan_tambahannama_{{$i}}" class="form-control" placeholder="Masukkan Nama">
																	<span class="input-group-text">{{$ifield}}</span>
																	<input type="text" name="karyawan_tambahan[{{$i}}][keterangan]" id="karyawan_tambahanketerangan_{{$i}}" class="form-control" placeholder="Masukkan Keterangan">
																	<button type="button" class="btn btn-sm btn-danger btn-delete-tambahan"><i class="bx bx-trash"></i></button>
																</div>
															</div>
															<hr class="mt-3 mb-3">
														</div>
													<?php $i++;
															endforeach;
														endif;
													endif; ?>
												</div>
											</div>
										</div>
									</form>
									<div style="font-style: italic;">
										<p style="color: red; margin: 0;">Catatan :</p>
										<p style="color: grey; margin: 0;">- Data dengan simbol (<span style="color: red;">*</span>) tidak boleh dikosongkan.</p>
										<p style="color: grey; margin: 0;">- Mohon isi dengan <span style="color: red;">00.000.000.0-000.000</span> jika tidak memimliki NPWP.</p>
									</div>
								</div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@include('user.master.karyawan.karyawan-divisi')
@include('user.master.karyawan.karyawan-jabatan')
@include('user.pengaturan.tunjangan.create')
@include('user.pengaturan.potongan.create')

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
	
	$(function() {
		let currentMenuId = "{{request()->get('menu_id')}}";
		let sttunjangankaryawan_data = JSON.parse('<?php echo addslashes(json_encode($sttunjangankaryawan_data)) ?>');
		let stpotongankaryawan_data = JSON.parse('<?php echo addslashes(json_encode($stpotongankaryawan_data)) ?>');
		// console.log(sttunjangankaryawan_data, 'sttunjangankaryawan_data');
		// console.log(stpotongankaryawan_data, 'stpotongankaryawan_data');

		function openAddJadwalModal() {
			$("#formAddJadwal")[0].reset(); // Reset the form fields
			jQuery("#addJadwalModal").modal("show");
		}
		
		// $("#btn-attendance").click(function(e) {
		// 	e.preventDefault()
		// 	openAddJadwalModal();
		// });

		$("#btn-tunjangan").click(function(e) {
			e.preventDefault();
			$("#sttunjangankaryawan_calculation").attr('disabled', false);
			$("#formTunjangan [type=reset]").click();

			$("#modalTunjangan").modal("show");
		})

		$("#btn-potongan").click(function(e) {
			e.preventDefault();
			// $("#stpotongankaryawan_calculation").attr('disabled', false);
			$("#formPotongan [type=reset]").click();

			$("#modalPotongan").modal("show");
		});

		$("#formKaryawan [name=karyawan_gender]").click(function(e) {
			let val = $(this).val();
			setTunjangan();
			setPotongan();
		});

		// $("#btn-penggajian").click(function(e) {
		// 	e.preventDefault();
		// 	// $("#stpotongankaryawan_calculation").attr('disabled', false);
		// 	$("#formStPenggajian [type=reset]").click();

		// 	$("#modalPenggajian").modal("show");
		// })

		// Smart Wizard
		$('#smartwizard').smartWizard({
			selected: 0,
			autoAdjustHeight: false,
			enableUrlHash: false,
			theme: 'dots', // basic, arrows, square, round, dots
			transition: {
				animation: 'fade'
			},
			toolbar: {
				showNextButton: false, // show/hide a Next button
				showPreviousButton: false, // show/hide a Previous button
				position: 'bottom', // none/ top/ both bottom
				extraHtml: `
				<button class="btn btn-outline-danger btn-prev mr-1 btn-sm" type="button">&laquo; Sebelumnya</button>
                <button class="btn btn-outline-info btn-next mr-1 btn-sm" type="button">Selanjutnya &raquo;</button>
                <button type="button" class="btn btn-warning btn-sm" id="btnFinish" style="display:none;">Simpan</button>
                `
			},
			anchor: {
				enableNavigation: true, // Enable/Disable anchor navigation 
				enableNavigationAlways: true, // Activates all anchors clickable always
				enableDoneState: true, // Add done state on visited steps
				markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
				unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
				enableDoneStateNavigation: true // Enable/Disable the done state navigation
			},
			style: {
				btnCss: 'btn-sm',
				// btnPrevCss: 'btn-outline-danger btn-prev mr-1',
				// btnNextCss: 'btn-outline-info btn-next mr-1'
			},
			keyboard: {
				keyNavigation: false,
			},
			lang: { // Language variables for button
				next: 'Selanjutnya',
				previous: 'Sebelumnya'
			},
		});

		// Leave step event is used for validating the forms
		$("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx, stepDirection) {
			console.log('currentStepIdx', currentStepIdx)
			// console.log('nextStepIdx', nextStepIdx)
			// Validate only on forward movement  
			// if (stepDirection == 'forward') {
			// 	let formKaryawan = document.getElementById('formKaryawan');
			// 	let formPekerjaan = document.getElementById('formPekerjaan');
			// 	let formPenggajian = document.getElementById('formPenggajian');
				// if(currentStepIdx == 0) {
				// 	if (formKaryawan) {
				// 		if (!formKaryawan.checkValidity()) {
				// 			$('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
				// 			$("#smartwizard").smartWizard('fixHeight');
				// 			return false;
				// 		}
				// 	}
				// }
				// if(currentStepIdx == 1) {
				// 	if (formPekerjaan) {
				// 		if (!formPekerjaan.checkValidity()) {
				// 			$('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
				// 			$("#smartwizard").smartWizard('fixHeight');
				// 			return false;
				// 		}
				// 	}
				// }

				// if(currentStepIdx == 2) {
				// 	if (formPenggajian) {
				// 		if (!formPenggajian.checkValidity()) {
				// 			$('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
				// 			$("#smartwizard").smartWizard('fixHeight');
				// 			return false;
				// 		}
				// 	}
				// }

				// $('#smartwizard').smartWizard("unsetState", [currentStepIdx], 'error');
			// }
		});

		// Step show event
		$("#smartwizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
			$("#smartwizard .btn-prev").removeClass('disabled').prop('disabled', false);
			$("#smartwizard .btn-next").removeClass('disabled').prop('disabled', false);
			if (stepPosition === 'first') {
				$("#smartwizard .btn-prev").addClass('disabled').prop('disabled', true);
			} else if (stepPosition === 'last') {
				$("#smartwizard .btn-next").addClass('disabled').prop('disabled', true);
			} else {
				$("#smartwizard .btn-prev").removeClass('disabled').prop('disabled', false);
				$("#smartwizard .btn-next").removeClass('disabled').prop('disabled', false);
			}

			// Get step info from Smart Wizard
			let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
			$("#sw-current-step").text(stepInfo.currentStep + 1);
			$("#sw-total-step").text(stepInfo.totalSteps);

			if (stepPosition == 'last') {
				//   showConfirm();
				$("#btnFinish").prop('disabled', false);
				$("#btnFinish").show();
			} else {
				$("#btnFinish").prop('disabled', true);
				$("#btnFinish").hide();
			}
		});
		$(".btn-next").click(function(e) {
			let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
			console.log('stepInfo.currentStep', stepInfo.currentStep);
			if (stepInfo.currentStep == 0) {
				$("#formKaryawan").submit();
			}
			if (stepInfo.currentStep == 1) {
				$("#formPekerjaan").submit();
			}
			if (stepInfo.currentStep == 2) {
				$("#formPenggajian").submit();
			}
		});
		
		$(".btn-prev").click(function(e) {
			$("#smartwizard").smartWizard("prev");
		})

		$("#karyawan_division").select2({
			tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.divisi.select')}}?menu_id=${currentMenuId}`,
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
		}).on("select2:select", function(e) {
			let data = e.params.data;
			setTunjangan();
			setPotongan();
		});
		$("#karyawan_position").select2({
			tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.jabatan.select')}}?menu_id=${currentMenuId}`,
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
		}).on("select2:select", function(e) {
			let data = e.params.data;
			setTunjangan();
			setPotongan();
		});

		// $("#karyawan_attendance").select2({
		// 	ajax: {
		// 		url: `{{route('user.page.pengaturan.kehadiran.select')}}?menu_id=${currentMenuId}`,
		// 		data: function(params) {
		// 			var query = {
		// 				q: params.term,
		// 				type: 'public'
		// 			}

		// 			// Query parameters will be ?search=[term]&type=public
		// 			return query;
		// 		},
		// 		processResults: function(data) {
		// 			// Transforms the top-level key of the response object from 'items' to 'results'
		// 			// console.log('data.data', data.data)
		// 			let items = data.data;
		// 			items.map((item, idx) => {
		// 				item.id = item.attendance_id;
		// 				item.text = item.attendance_description;
		// 				// console.log('item.kode', item)
		// 				return item
		// 			})
		// 			return {
		// 				results: items
		// 			};
		// 		},
		// 	},
		// });

		$("#karyawan_manager").select2({
			ajax: {
				url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
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
				url: `{{route('user.page.pengaturan.tunjangan.select')}}?menu_id=${currentMenuId}`,
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
			templateSelection : function (tag, container){
				// here we are finding option element of tag and
				// if it has property 'locked' we will add class 'locked-tag' 
				// to be able to style element in select
				var $option = $('#karyawan_allowance option[value="'+tag.id+'"]');
				if ($option.attr('locked')){
				$(container).addClass('locked-tag');
					tag.locked = true; 
				}
				return tag.text;
			},
		}).on('select2:unselecting', function(e){
			// before removing tag we check option element of tag and 
			// if it has property 'locked' we will create error to prevent all select2 functionality
			if ($(e.params.args.data.element).attr('locked')) {
				e.select2.pleaseStop();
			}
		});

		$("#karyawan_deduction").select2({
			ajax: {
				url: `{{route('user.page.pengaturan.potongan.select')}}?menu_id=${currentMenuId}`,
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
						item.id = item.stpotongankaryawan_id;
						item.text = item.stpotongankaryawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
			templateSelection : function (tag, container){
				// here we are finding option element of tag and
				// if it has property 'locked' we will add class 'locked-tag' 
				// to be able to style element in select
				var $option = $('#karyawan_deduction option[value="'+tag.id+'"]');
				if ($option.attr('locked')){
				$(container).addClass('locked-tag');
					tag.locked = true; 
				}
				return tag.text;
			},
		}).on('select2:unselecting', function(e){
			// before removing tag we check option element of tag and 
			// if it has property 'locked' we will create error to prevent all select2 functionality
			if ($(e.params.args.data.element).attr('locked')) {
				e.select2.pleaseStop();
			}
		});

		// $("#karyawan_payroll").select2({
		// 	ajax: {
		// 		url: `{{route('user.page.pengaturan.penggajian.select')}}?menu_id=${currentMenuId}`,
		// 		data: function(params) {
		// 			var query = {
		// 				q: params.term,
		// 				type: 'public'
		// 			}

		// 			// Query parameters will be ?search=[term]&type=public
		// 			return query;
		// 		},
		// 		processResults: function(data) {
		// 			// Transforms the top-level key of the response object from 'items' to 'results'
		// 			// console.log('data.data', data.data)
		// 			let items = data.data;
		// 			items.map((item, idx) => {
		// 				item.id = item.stpenggajiankaryawan_id;
		// 				item.text = item.stpenggajiankaryawan_name;
		// 				// console.log('item.kode', item)
		// 				return item
		// 			})
		// 			return {
		// 				results: items
		// 			};
		// 		},
		// 	},
		// });

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
			tags: true, // Enable tags to add a clear button
			templateResult: formatResult, // Custom function for displaying results
    		templateSelection: formatSelection, // Custom function for displaying selected item
		}).on("select2:select", function(e) {
			$("#karyawan_banknokartu").removeAttr('disabled');
			// $("#karyawan_banknokartu").parent().prev('label').append('<span class="text-danger">*</span>');
		}).on("select2:unselect", function (e) {
			$("#karyawan_banknokartu").val('').attr('disabled', 'disabled');
		});

		function formatResult(result) {
			if (result.loading) return result.text;
			return result.text;
		}

		function formatSelection(selection) {
			if (!selection.id) return selection.text;

			// Add padding and margin to the selected item text
			var $clearButton = $('<span class="clear-button" onclick="clearSelection()">&times;</span>');
			$clearButton.css({
				'color': 'red', // Set the "x" font color to red
				'margin-left': '10px', // Set margin to the left of the "x" button
				'margin-right': '10px', // Set margin to the right of the "x" button
				'padding-left': '5px', // Set padding to the left of the selected item text
				'padding-right': '5px' // Set padding to the right of the selected item text
			});

			$clearButton.on('click', function() {
				$("#karyawan_bankid").val(null).trigger('change');
				$("#karyawan_banknokartu").val('').attr('disabled', 'disabled');
			});

			return $('<div>').append('<div style="display: inline-block;">' + selection.text + '</div>').append($clearButton);
		}

		$("input[name=karyawan_isgetbpjskes]").click(function(e) {
			let val = $(this).val();
			if (val == 1) {
				$(".karyawan_isgetbpjskes_box").slideDown();
				$("#karyawan_bpjskesdate").attr("required", true);
				$("#karyawan_bpjskesno").attr("required", true);
			} else {
				$(".karyawan_isgetbpjskes_box").slideUp();
				$("#karyawan_bpjskesdate").removeAttr("required");
				$("#karyawan_bpjskesno").removeAttr("required");
			}
		})

		$("input[name=karyawan_isgetbpjstk]").click(function(e) {
			let val = $(this).val();
			if (val == 1) {
				$(".karyawan_isgetbpjstk_box").slideDown();
				$("#karyawan_bpjstkdate").attr("required", true);
				$("#karyawan_bpjstkno").attr("required", true);
			} else {
				$(".karyawan_isgetbpjstk_box").slideUp();
				$("#karyawan_bpjstkdate").removeAttr("required");
				$("#karyawan_bpjstkno").removeAttr("required");
			}
		})

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
		let karyawanBPJSKesNum = null;
		let karyawanBPJSTKNum = null;
		if($("#karyawan_bpjskeslainnya").length > 0) {
			[karyawanBPJSKesNum] = AutoNumeric.multiple(["#karyawan_bpjskeslainnya"], optAutoNumeric);
		}
		if($("#karyawan_bpjstklainnya").length > 0) {
			[karyawanBPJSTKNum] = AutoNumeric.multiple(["#karyawan_bpjstklainnya"], optAutoNumeric);
		}
		
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
		
		$(`#karyawan_birthdate, #karyawan_contract_begin, #karyawan_contract_end, #karyawan_bpjskesdate, #karyawan_bpjstkdate`).daterangepicker({
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

		$("#karyawan_citizenship").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if (data.id == 'WNA') {
				$("#country_id").removeAttr('disabled').attr('required', true);
			} else {
				$("#country_id").removeAttr('required').attr('disabled', true);
			}
		});

		$(".box-bpjs-tk").slideUp();
		$(".box-bpjs-kes").slideUp();
		$("#karyawan_status").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if(data.id == 'PERCOBAAN' || data.id == 'KONTRAK') {
				$("#karyawan_contract_end").removeAttr('disabled');
				$("#karyawan_contract_end").parent().prev('label').append('<span class="text-danger">*</span>');
			} else {
				$("#karyawan_contract_end").attr('disabled', true);
				$("#karyawan_contract_end").parent().prev('label').find('span.text-danger').remove();
			}
		});

		$("#karyawan_taxtype").select2({
			ajax: {
				url: `{{route('master.objekcategory.select')}}?menu_id=${currentMenuId}`,
				data: function(params) {
					var query = {
						q: params.term,
						bukankaryawan: 1,
						jenis: 'PPH-21',
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
						item.id = item.objekpajak_code;
						item.text = item.objekpajak_description;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
			templateResult: formatStateObjekPajak,
			templateSelection: formatStateObjekPajak
		});

		$("#ptkp_id").select2({
			ajax: {
				url: `{{route('master.ptkp.select')}}?menu_id=${currentMenuId}`,
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

		$("#karyawan_enid").change(function() {
			let $this = $(this);
			let val = $this.val();
			if (val) {
				if ($this.valid()) {
					$(".spinner-box").css({
						'display': 'table'
					});
					$.ajax({
						method: 'POST',
						url: "{{route('user.info.getkaryawanbykode')}}",
						data: {
							_token: $("meta[name=csrf-token]").attr('content'),
							karyawan_enid: val
						},
						error: function(error) {
							$(".spinner-box").fadeOut();
							// console.log(error.responseJSON.errors.email);
							if (error.responseJSON) {
								let errs = error.responseJSON.errors;
								if (error.responseJSON) {
									let errs = error.responseJSON.errors;
									if (errs['karyawan_enid'])
										formKaryawan.showErrors({
											'karyawan_enid': errs['karyawan_enid']
										});
								}
							}
						},
						success: function(response) {
							$(".spinner-box").fadeOut();
							console.log('response', response);
							if (!response.success) {
								Swal.fire({
									showCancelButton: false,
									confirmButtonText: "Ok",
									icon: 'error',
									html: response.message
								})
								return false;
							}
						}
					})
				}
			}
		});

		$("#karyawan_nik").change(function() {
			let $this = $(this);
			let val = $this.val();
			if (val) {
				if ($this.valid()) {
					$(".spinner-box").css({
						'display': 'table'
					});
					$.ajax({
						method: 'POST',
						url: "{{route('user.info.getkaryawanbynik')}}",
						data: {
							_token: $("meta[name=csrf-token]").attr('content'),
							karyawan_nik: val
						},
						error: function(error) {
							$(".spinner-box").fadeOut();
							// console.log(error.responseJSON.errors.email);
							if (error.responseJSON) {
								let errs = error.responseJSON.errors;
								if (error.responseJSON) {
									let errs = error.responseJSON.errors;
									if (errs['karyawan_nik'])
										formKaryawan.showErrors({
											'karyawan_nik': errs['karyawan_nik']
										});
								}
							}
						},
						success: function(response) {
							$(".spinner-box").fadeOut();
							// console.log('response', response);
							if (!response.success) {
								// Swal.fire({
								// 	showCancelButton: false,
								// 	confirmButtonText: "Ok",
								// 	icon: 'error',
								// 	html: response.message
								// })
								formKaryawan.showErrors({
									'karyawan_nik': response.message
								});
								return false;
							}
						}
					})
				}
			}
		});

		$("#karyawan_npwp").change(function() {
			let $this = $(this);
			let val = $this.val();
			if (val) {
				if ($this.valid()) {
					$(".spinner-box").css({
						'display': 'table'
					});
					$.ajax({
						method: 'POST',
						url: "{{route('user.info.getkaryawanbynpwp')}}",
						data: {
							_token: $("meta[name=csrf-token]").attr('content'),
							karyawan_npwp: val
						},
						error: function(error) {
							$(".spinner-box").fadeOut();
							// console.log(error.responseJSON.errors.email);
							if (error.responseJSON) {
								let errs = error.responseJSON.errors;
								if (error.responseJSON) {
									let errs = error.responseJSON.errors;
									if (errs['karyawan_npwp'])
										formKaryawan.showErrors({
											'karyawan_npwp': errs['karyawan_npwp']
										});
								}
							}
						},
						success: function(response) {
							$(".spinner-box").fadeOut();
							// console.log('response', response);
							if (!response.success) {
								// Swal.fire({
								// 	showCancelButton: false,
								// 	confirmButtonText: "Ok",
								// 	icon: 'error',
								// 	html: response.message
								// })
								formKaryawan.showErrors({
									'karyawan_npwp': response.message
								});
								return false;
							}
						}
					})
				}
			}
		});

		$("#karyawan_email").change(function() {
			let $this = $(this);
			let val = $this.val();
			// $(".userbx").html('');
			$("#karyawan_isuser").prop('checked', false);
			$("#karyawan_isuser").attr('disabled', true);
			if (val) {
				if ($this.valid()) {
					$(".spinner-box").css({
						'display': 'table'
					});
					$.ajax({
						method: 'POST',
						url: "{{route('user.info.getkaryawanbyemail')}}",
						data: {
							_token: $("meta[name=csrf-token]").attr('content'),
							karyawan_email: val
						},
						error: function(error) {
							$(".spinner-box").fadeOut();
							// console.log(error.responseJSON.errors.email);
							if (error.responseJSON) {
								let errs = error.responseJSON.errors;
								if (error.responseJSON) {
									let errs = error.responseJSON.errors;
									if (errs['karyawan_email'])
										formKaryawan.showErrors({
											'karyawan_email': errs['karyawan_email']
										});
								}
							}
						},
						success: function(response) {
							$(".spinner-box").fadeOut();
							// console.log('response', response);
							// $(".userbx").html('');
							$("#karyawan_isuser").prop('checked', false);
							$("#karyawan_isuser").attr('disabled', true);
							if (!response.success) {
								Swal.fire({
									showCancelButton: false,
									confirmButtonText: "Ok",
									icon: 'error',
									html: response.message
								})
								return false;
							}

							// $(".userbx").html(`<div class="col-sm-6">
							// 	<div class="border py-3 px-3" style="border-color: #f6830f!important">
							// 		<div class="form-check form-check-inline">
							// 			<input name="karyawan_isuser" class="form-check-input" type="checkbox"  value="1" id="karyawan_isuser">
							// 			<label class="form-check-label" for="karyawan_isuser">Tambahkan Sebagai User</label>
							// 		</div>
							// 	</div>
							// </div>`);
							$("#karyawan_isuser").removeAttr('disabled');
						}
					})
				}
			}
		});

		$(document).on("click", "#karyawan_isuser", function(e) {
			let ischecked = $(this).is(":checked");
			let email = $("#karyawan_email").val();
			if (ischecked) {
				if (!email) {
					$(this).prop("checked", false);
					Swal.fire({
						showCancelButton: false,
						confirmButtonText: "Ok",
						icon: 'error',
						html: "Silahkan isi email terlebih dahulu!"
					})
					return false;
				}
			}
		})

		let tambahanidx = Number.parseInt("{{$fields ? count($fields) : 0}}");
		$("#btn-tambahan").click(function(e) {
			e.preventDefault();
			let val = $("#karyawan_tambahankey").val();
			if(!val) {
				Swal.fire({
					showCancelButton: false,
					confirmButtonText: "Ok",
					icon: 'error',
					html: "Silahkan isi Nama Inputan!"
				})
				return false;
			}
			$("#karyawan_tambahankey").val('');
			tambahanidx = tambahanidx + 1;
			$("#formtambahanbox").append(`<div class="form-group row mb-3 fieldtambahan">
				<div class="col-sm-7">
					<div class="input-group">
						<input type="hidden" readonly value="${val}" name="karyawan_tambahan[${tambahanidx}][nama]" id="karyawan_tambahannama_${tambahanidx}" class="form-control" placeholder="Masukkan Nama">
						<span class="input-group-text">${val}</span>
						<input type="text" required name="karyawan_tambahan[${tambahanidx}][keterangan]" id="karyawan_tambahanketerangan_${tambahanidx}" class="form-control" placeholder="Masukkan Keterangan">
						<button type="button" class="btn btn-sm btn-danger btn-delete-tambahan"><i class="bx bx-trash"></i></button>
					</div>
				</div>
				<hr class="mt-3 mb-3">
			</div>`);
			
			$("#formTambahan").validate();
		})

		$(document).on("click", ".btn-delete-tambahan", function(e) {
			e.preventDefault();
			Swal.fire({
				html: 'Anda yakin ingin menghapus data?<br><small class="text-danger">Penghapusan data akan berdampak pada semua karyawan.</small>',
				// showCancelButton: true,
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {
					$(this).parents(".fieldtambahan").remove();
				}
			})
		})

		let formKaryawan = $("#formKaryawan").validate({
			ignore: [],
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
						console.log("test", element)
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
				karyawan_npwp: {
					required: true,
					minlength: 20,
					maxlength: 20,
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
				$("#smartwizard").smartWizard("next");
			},
		})

		let formPekerjaan = $("#formPekerjaan").validate({
			ignore: [],
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
				karyawan_status: {
					required: true,
				},
				karyawan_contract_begin: {
					required: true,
				},
			},
			submitHandler: function(form) {
				$("#smartwizard").smartWizard("next");
			}
		})

		let formPenggajian = $("#formPenggajian").validate({
			ignore: [],
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
				karyawan_salary: {
					required: true,
				},
				karyawan_calculation_method: {
					required: true,
				},
				// karyawan_payroll: {
				// 	required: true,
				// },
				// karyawan_bankid: {
				// 	required: true,
				// },
				karyawan_banknokartu: {
				// 	required: true,
				},

			},
			submitHandler: function(form) {
				$("#smartwizard").smartWizard("next");
			}
		})

		let formTambahan = $("#formTambahan").validate({
			ignore: [], 
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
			// rules: {},
			submitHandler: function(form) {}
		});
		
		$("#btnFinish").click(function(e) {
			e.preventDefault();
			if(!$("#formPenggajian").valid()) {
				return false;
			}

			let form = $("#formKaryawan");
			$(".spinner-box").css({
				'display': 'table'
			});
			let karyawan_salary = karyawanSalaryNum.getNumber();
			let karyawan_bpjskeslainnya = 0;
			let karyawan_bpjstklainnya = 0;
			if($("#karyawan_bpjskeslainnya").length > 0) {
				let karyawan_bpjskeslainnya = karyawanBPJSKesNum.getNumber();
			}
			if($("#karyawan_bpjstklainnya").length > 0) {
				karyawan_bpjstklainnya = karyawanBPJSTKNum.getNumber();
			}
			
			let formData = new FormData();
			let dataArr = $("#formKaryawan, #formPekerjaan, #formPenggajian, #formTambahan").serializeArray();

			for (let i = 0; i < dataArr.length; i++) {
				formData.append(dataArr[i].name, dataArr[i].value);
			}

			formData.append('_token', $("meta[name=csrf-token]").attr('content'));
			formData.append('karyawan_salary', karyawan_salary);
			formData.append('karyawan_bpjskeslainnya', karyawan_bpjskeslainnya);
			formData.append('karyawan_bpjstklainnya', karyawan_bpjstklainnya);
			formData.append('karyawan_photo', $('#karyawan_photo')[0].files[0]);

			$.ajax({
				method: form.attr("method"),
				url: form.attr("action"),
				processData: false, // tell jQuery not to process the data            
				contentType: false, // tell jQuery not to set contentType  
				// data: $("#formKaryawan, #formPekerjaan, #formPenggajian, #formTambahan").serialize()+"&"+$.param({
				// 	_token: $("meta[name=csrf-token]").attr('content'),
				// 	karyawan_salary: karyawan_salary
				// }),
				data: formData,
				error: function(error) {
					$(".spinner-box").fadeOut();
					// console.log(error.responseJSON.errors.email);
					if (error.responseJSON) {
						let errs = error.responseJSON.errors;
						let errorName = [];
						if (errs) {
							let errtab = 0;
							console.log('errs', errs);
							// if(errs['karyawan_payroll']) {
							// 	errtab = 2;
                            //     formPenggajian.showErrors({'karyawan_payroll': errs['karyawan_payroll']});
							// }

							if(errs['karyawan_calculation_method']) {
								errtab = 2;
                                formPenggajian.showErrors({'karyawan_calculation_method': errs['karyawan_calculation_method']});
							}
							if(errs['karyawan_bpjskesno']) {
								errtab = 2;
                                formPenggajian.showErrors({'karyawan_bpjskesno': errs['karyawan_bpjskesno']});
							}
							if(errs['karyawan_bpjstkno']) {
								errtab = 2;
                                formPenggajian.showErrors({'karyawan_bpjstkno': errs['karyawan_bpjstkno']});
							}

							// if(errs['karyawan_bankid']) {
							// 	errtab = 2;
                            //     formPenggajian.showErrors({'karyawan_bankid': errs['karyawan_bankid']});
							// }

							// if(errs['karyawan_banknokartu']) {
							// 	errtab = 2;
                            //     formPenggajian.showErrors({'karyawan_banknokartu': errs['karyawan_banknokartu']});
							// }

							if(errs['karyawan_status']) {
								errtab = 1;
                                formPekerjaan.showErrors({'karyawan_status': errs['karyawan_status']});
							}

							if(errs['karyawan_contract_begin']) {
								errtab = 1;
                                formPekerjaan.showErrors({'karyawan_contract_begin': errs['karyawan_contract_begin']});
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

							$('#smartwizard').smartWizard("goToStep", errtab, true);
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

					let href = `{{route('user.page.karyawan.index')}}?menu_id=${currentMenuId}`;
					loadPage(href);
				}
			})
		})

		

		function setTunjangan() {
			let tjdataopt = '';
			let gender = $("#formKaryawan [name=karyawan_gender]:checked").val();
			let div = $("#karyawan_division").val();
			let pos = $("#karyawan_position").val();
			if(sttunjangankaryawan_data && sttunjangankaryawan_data.length > 0) {
				for(let i=0; i<sttunjangankaryawan_data.length; i++) {
					let sttj = sttunjangankaryawan_data[i];
					let sttjdivids = (sttj.sttunjangankaryawan_division_jabatan) ? sttj.sttunjangankaryawan_division_jabatan : null;
					sttjdivids = (sttjdivids != null) ? sttjdivids.split(',') : [];
					// console.log('sttjdivids', sttjdivids);

					let sttjposids = (sttj.sttunjangankaryawan_division_jabatan) ? sttj.sttunjangankaryawan_division_jabatan : null;
					sttjposids = (sttjposids != null) ? sttjposids.split(',') : [];
					// console.log('sttjposids', sttjposids);
					
					if(sttj.sttunjangankaryawan_used_for == 1) {
						tjdataopt += `<option value="${sttj.sttunjangankaryawan_id}" selected locked="locked">${sttj.sttunjangankaryawan_name}</option>`;
					}
					
					if(gender == 'P' && sttj.sttunjangankaryawan_used_for == 2) {
						tjdataopt += `<option value="${sttj.sttunjangankaryawan_id}" selected locked="locked">${sttj.sttunjangankaryawan_name}</option>`;
					}

					if(gender == 'L' && sttj.sttunjangankaryawan_used_for == 3) {
						tjdataopt += `<option value="${sttj.sttunjangankaryawan_id}" selected locked="locked">${sttj.sttunjangankaryawan_name}</option>`;
					}
						
					if(sttjdivids && sttjdivids.indexOf(div) > -1 && sttj.sttunjangankaryawan_used_for == 4) {
						
						tjdataopt += `<option value="${sttj.sttunjangankaryawan_id}" selected locked="locked">${sttj.sttunjangankaryawan_name}</option>`;
					}

					if(sttjdivids && sttjdivids.indexOf(pos) > -1 && sttj.sttunjangankaryawan_used_for == 5) {
						tjdataopt += `<option value="${sttj.sttunjangankaryawan_id}" selected locked="locked">${sttj.sttunjangankaryawan_name}</option>`;
					}
				}
			}
			$("#karyawan_allowance").html(tjdataopt);
		}

		function setPotongan() {
			let tjdataopt = '';
			let gender = $("#formKaryawan [name=karyawan_gender]:checked").val();
			let div = $("#karyawan_division").val();
			let pos = $("#karyawan_position").val();
			if(stpotongankaryawan_data && stpotongankaryawan_data.length > 0) {
				for(let i=0; i<stpotongankaryawan_data.length; i++) {
					let sttj = stpotongankaryawan_data[i];
					let sttjdivids = (sttj.stpotongankaryawan_division_jabatan) ? sttj.stpotongankaryawan_division_jabatan : null;
					sttjdivids = (sttjdivids != null) ? sttjdivids.split(',') : [];
					// console.log('sttjdivids', sttjdivids);

					let sttjposids = (sttj.stpotongankaryawan_division_jabatan) ? sttj.stpotongankaryawan_division_jabatan : null;
					sttjposids = (sttjposids != null) ? sttjposids.split(',') : [];
					// console.log('sttjposids', sttjposids);
					
					if(sttj.stpotongankaryawan_used_for == 1) {
						tjdataopt += `<option value="${sttj.stpotongankaryawan_id}" selected locked="locked">${sttj.stpotongankaryawan_name}</option>`;
					}
					
					if(gender == 'P' && sttj.stpotongankaryawan_used_for == 2) {
						tjdataopt += `<option value="${sttj.stpotongankaryawan_id}" selected locked="locked">${sttj.stpotongankaryawan_name}</option>`;
					}

					if(gender == 'L' && sttj.stpotongankaryawan_used_for == 3) {
						tjdataopt += `<option value="${sttj.stpotongankaryawan_id}" selected locked="locked">${sttj.stpotongankaryawan_name}</option>`;
					}
						
					if(sttjdivids && sttjdivids.indexOf(div) > -1 && sttj.stpotongankaryawan_used_for == 4) {
						
						tjdataopt += `<option value="${sttj.stpotongankaryawan_id}" selected locked="locked">${sttj.stpotongankaryawan_name}</option>`;
					}

					if(sttjdivids && sttjdivids.indexOf(pos) > -1 && sttj.stpotongankaryawan_used_for == 5) {
						tjdataopt += `<option value="${sttj.stpotongankaryawan_id}" selected locked="locked">${sttj.stpotongankaryawan_name}</option>`;
					}
				}
			}
			$("#karyawan_deduction").html(tjdataopt);
		}
		
		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>