<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <!-- Bootstrap Table with Header - Light -->
        <div class="card">
                <!-- Basic Layout & Basic with Icons -->
              <div class="row">
                <div class="col-xxl">
					<div class="card-header d-flex align-items-center justify-content-between">
						<h5 class="mb-0">{{$title}}</h5>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item">
									<a href="{{route('user.page.karyawan.index')}}" class="rekkaa-page-link">Karyawan</a>
								</li>
								<li class="breadcrumb-item active">{{$title}}</li>
							</ol>
						</nav>
					</div>

                    <div class="card-body">
						<form action="{{route('user.page.karyawan.store')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawan" autocomplete="off">
							<div class="card mb-3">
								<div class="card-header">
									<h5 class="mb-0">{{$title}}</h3>
								</div>
								<div class="card-body">
									<div class="form-group row mb-3">
										<label for="karyawan_nik" class="col-sm-2 lbl-req">NIK</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_nik" id="karyawan_nik" class="form-control nik-input" placeholder="Masukkan NIK">
										</div>
										<label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_npwp" id="karyawan_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_name" class="col-sm-2 lbl-req">Nama Lengkap</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_name" id="karyawan_name" class="form-control" placeholder="Masukkan Nama Lengkap">
										</div>
										<label for="karyawan_birthdate" class="col-sm-2 lbl-req">Tgl. Lahir</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_birthdate" id="karyawan_birthdate" class="form-control" placeholder="Masukkan Tanggal Lahir">
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_phone" class="col-sm-2 lbl-req">No. Telepon</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_phone" id="karyawan_phone" class="form-control" placeholder="Masukkan No. Telepon">
										</div>
										<label for="karyawan_email" class="col-sm-2">Email</label>
										<div class="col-sm-4">
											<input type="email" name="karyawan_email" id="karyawan_email" class="form-control" placeholder="Masukkan Email">
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_citizenship" class="col-sm-2 lbl-req">Kewarganegaraan</label>
										<div class="col-sm-4">
											<select required name="karyawan_citizenship" required id="karyawan_citizenship" style="width: 100%;" data-placeholder="-: Pilih Data :-">
												<option value="">-: Pilih Data :-</option>
												<option value="WNI" selected>WNI</option>
												<option value="WNA" disabled>WNA</option>
											</select>
										</div>
										<label for="ptkp_id" class="col-sm-2 lbl-req">Status Perkawinan</label>
										<div class="col-sm-4">
											<select name="ptkp_id" required id="ptkp_id" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="country_id" class="col-sm-2 lbl-req">Negara</label>
										<div class="col-sm-4">
											<select disabled name="country_id" required id="country_id" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
										</div>
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
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_address" class="col-sm-2 lbl-req">Alamat</label>
										<div class="col-sm-4">
											<textarea name="karyawan_address" required id="karyawan_address" class="form-control" placeholder="Masukkan Alamat"></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="card mb-3">
								<div class="card-header">
									<h5 class="mb-0">Informasi Kontrak</h3>
								</div>
								<div class="card-body">
									<div class="form-group row mb-3">
										<label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan</label>
										<div class="col-sm-4">
											<select name="karyawan_status" required id="karyawan_status" style="width: 100%;" data-placeholder="-: Pilih Data :-">
												<option value="">-: Pilih Data :-</option>
												<option value="TETAP">Karyawan Tetap</option>
												<option value="KONTRAK">Karyawan Kontrak</option>
												<option value="NONKARYAWAN">Bukan Karyawan</option>
											</select>
										</div>
										<label for="karyawan_taxtype" class="col-sm-2 lbl-req">Jenis Pajak</label>
										<div class="col-sm-4">
											<select name="karyawan_taxtype" disabled id="karyawan_taxtype" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
										</div>
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_contract_begin" class="col-sm-2 lbl-req">Tgl. Berlaku</label>
										<div class="col-sm-4">
											<div class="row">
												<div class="col-sm-12 mb-2">
													<input type="text" required name="karyawan_contract_begin" id="karyawan_contract_begin" class="form-control" placeholder="Masukkan Tanggal Mulai">
												</div>
											</div>
										</div>
										<label for="karyawan_calculation_method" class="col-sm-2 lbl-req">Metode Perhitungan</label>
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
									</div>
									<div class="form-group row mb-3">
										<label for="karyawan_salary" class="col-sm-2 lbl-req">Gaji Pokok</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_salary" value="0" id="karyawan_salary" class="form-control" placeholder="Masukkan Gaji Pokok">
										</div>
										<label for="karyawan_position" class="col-sm-2 lbl-req">Jabatan</label>
										<div class="col-sm-4">
											<input type="text" required name="karyawan_position" id="karyawan_position" class="form-control" placeholder="Masukkan Jabatan">
										</div>
									</div>
								</div>
							</div>
							<div class="card mb-3" id="card-bpjs">
								<div class="card-header">
									<h5 class="mb-0">BPJS</h5>
								</div>
								<div class="card-body">
									<div class="form-group row mb-3">
										<label for="karyawan_bpjs_tk" class="col-sm-2">BPJS</label>
										<div class="col-sm-4">
											<div class="form-check form-check-inline">
												<input name="karyawan_bpjs[]" class="form-check-input karyawan_bpjs" type="checkbox" data-type="TENAGA_KERJA" value="TENAGA_KERJA" id="karyawan_bpjs_tk">
												<label class="form-check-label" for="karyawan_bpjs_tk">Tenaga Kerja</label>
											</div>
											<div class="form-check form-check-inline">
												<input name="karyawan_bpjs[]" class="form-check-input karyawan_bpjs" type="checkbox" data-type="KESEHATAN" value="KESEHATAN" id="karyawan_bpjs_kes">
												<label class="form-check-label" for="karyawan_bpjs_kes">Kesehatan</label>
											</div>
										</div>
									</div>
									<div class="form-group row mb-3" id="bpjs-box">
										<?php
											$bpjs_tk_html = '';
											$bpjs_tk_html_lainnya = '';
											$bpjs_kes_html = '';
											$bpjs_kes_html_lainnya = '';
										?>
										<?php 
										// $is_gaji_pokok_kes_exist = false;
										// $is_lainnya_kes_exist = false;
										// $is_gaji_pokok_tk_exist = false;
										// $is_lainnya_tk_exist = false;
										if($stbpjskaryawan_data) {
											foreach($stbpjskaryawan_data as $stbpjskaryawan) {
												if($stbpjskaryawan->stbpjskaryawan_type == 'TENAGA_KERJA') {
													$stbpjs_name = ucwords(strtolower(str_replace('_', ' ', $stbpjskaryawan->stbpjskaryawan_name)));
													$stbpjs_checked = ($stbpjskaryawan->stbpjskaryawan_active) ? 'checked' : '';
													$className = ($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') ? '' : 'kontrak_bpjs_tk';
													// $valuenominal_bpjs = ($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') ? $stbpjskaryawan->stbpjskaryawan_value : '1';
													$valuenominal_bpjs = $stbpjskaryawan->stbpjskaryawan_value;
													$bpjs_tk_html .= '<div class="form-group mb-3">
														<div class="form-check form-check-inline">
															<input name="bpjs_tk['.$stbpjskaryawan->stbpjskaryawan_name.']" '.$stbpjs_checked.' class="form-check-input bpjstk-input '.$className.'" type="checkbox" value="'.$valuenominal_bpjs.'"  id="TK_'.$stbpjskaryawan->stbpjskaryawan_name.'">
															<label class="form-check-label" for="TK_'.$stbpjskaryawan->stbpjskaryawan_name.'">'.$stbpjs_name.'</label>
														</div>
													</div>';

													if($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') {
														$cssdisplay = ($stbpjs_checked) ? 'style="display:block"' : 'style="display:none"';
														$cssdisabled = ($stbpjs_checked) ? '' : 'disabled';
														$bpjs_tk_html_lainnya = '<div class="row mb-3 bpjs-tk-lainnya-box" '.$cssdisplay.'>
															<label class="col-sm-4 lbl-req" for="bpjs_tk_tunjangan_lainnya">Tunjangan Lainnya</label>
															<div class="col-sm-5">
															<input type="text" '.$cssdisabled.' required name="bpjs_tk_tunjangan_lainnya" id="bpjs_tk_tunjangan_lainnya" value="'.$stbpjskaryawan->stbpjskaryawan_value.'" class="form-control" placeholder="Masukkan Tunjangan Lainnya">
															</div>
														</div>';
													}
												}

												if($stbpjskaryawan->stbpjskaryawan_type == 'KESEHATAN') {
													$stbpjs_name = ucwords(strtolower(str_replace('_', ' ', $stbpjskaryawan->stbpjskaryawan_name)));
													$stbpjs_checked = ($stbpjskaryawan->stbpjskaryawan_active) ? 'checked' : '';
													$className = ($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') ? '' : 'kontrak_bpjs_kes';
													// $valuenominal_bpjs = ($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') ? $stbpjskaryawan->stbpjskaryawan_value : '1';
													$valuenominal_bpjs = $stbpjskaryawan->stbpjskaryawan_value;
													$bpjs_kes_html .= '<div class="form-group mb-3">
														<div class="form-check form-check-inline">
															<input name="bpjs_kes['.$stbpjskaryawan->stbpjskaryawan_name.']" '.$stbpjs_checked.' class="form-check-input bpjskes-input '.$className.'" type="checkbox" value="'.$valuenominal_bpjs.'" id="KES_'.$stbpjskaryawan->stbpjskaryawan_name.'">
															<label class="form-check-label" for="KES_'.$stbpjskaryawan->stbpjskaryawan_name.'">'.$stbpjs_name.'</label>
														</div>
													</div>';

													if($stbpjskaryawan->stbpjskaryawan_name == 'LAINNYA') {
														$cssdisplay = ($stbpjs_checked) ? 'style="display:block"' : 'style="display:none"';
														$cssdisabled = ($stbpjs_checked) ? '' : 'disabled';
														$bpjs_kes_html_lainnya = '<div class="row mb-3 bpjs-kes-lainnya-box" '.$cssdisplay.'>
															<label class="col-sm-4 lbl-req" for="bpjs_kes_tunjangan_lainnya">Tunjangan Lainnya</label>
															<div class="col-sm-5">
															<input type="text" '.$cssdisabled.' required name="bpjs_kes_tunjangan_lainnya" id="bpjs_kes_tunjangan_lainnya" value="'.$stbpjskaryawan->stbpjskaryawan_value.'" class="form-control" placeholder="Masukkan Tunjangan Lainnya">
															</div>
														</div>';
													}
												}
											} 
										} ?>
										<div class="col-sm-6 box-bpjs-tk" style="display: none;">
											<label for="" class="nodot-label"><u>BPJS TENAGA KERJA</u></label>
											<?php echo $bpjs_tk_html ?>
											<?php echo $bpjs_tk_html_lainnya ?>
										</div>
										<div class="col-sm-6 box-bpjs-kes" style="display: none;">
											<label for="" class="nodot-label"><u>BPJS KESEHATAN</u></label>
											<?php echo $bpjs_kes_html ?>
											<?php echo $bpjs_kes_html_lainnya ?>
										</div>
									</div>
								</div>
							</div>
							<div class="card mb-3" id="card-tunjangan">
								<div class="card-header">
									<h5 class="mb-0">Tunjangan</h5>
								</div>
								<div class="card-body">
									<?php 
									if($sttunjangankaryawan_data) :
										foreach($sttunjangankaryawan_data as $sttunjangankaryawan) : ?>
									<div class="row mb-3">
										<label for="karyawan_tunjangan" class="col-sm-2">{{$sttunjangankaryawan->sttunjangankaryawan_name}}</label>
										<div class="col-sm-6">
											<input type="text" value="{{$sttunjangankaryawan->sttunjangankaryawan_value}}" name="karyawan_tunjangan[{{$sttunjangankaryawan->sttunjangankaryawan_code}}]" id="karyawan_tunjangan_{{$sttunjangankaryawan->sttunjangankaryawan_code}}" class="form-control input_tunjangan" placeholder="Masukkan {{$sttunjangankaryawan->sttunjangankaryawan_name}}">
										</div>
									</div>
									<?php 
										endforeach;
									endif;
									?>
								</div>
							</div>
							<div class="card mb-3" style="display: none;">
								<!-- Basic Layout & Basic with Icons -->
								<div class="card-header">
									<h5 class="mb-0">Informasi Bank</h5>
									<p>Verifikasi Akun Anda untuk mengaktifkan fitur Pembayaran Payroll. <a href="#">Klik disini.</a></p>
								</div>
								<div class="card-body row">
									<div class="col-sm-6 mb-3">
										<select name="nama_bank" disabled style="width: 100%;" id="nama_bank" class="form-control" data-placeholder="-:Pilih Nama Bank:-">
											<option value="">-:Pilih Nama Bank:-</option>
										</select>
										
									</div>
									<div class="col-sm-6 mb-3">
										<input name="nomor_akun" disabled id="nomor_akun" class="form-control" placeholder="Nomor Akun">
										
									</div>
									<div class="col-sm-6 mb-3">
										<input name="nama_akun" disabled id="nama_akun" class="form-control" placeholder="Nama Akun">
										
									</div>
									<div class="col-sm-6 mb-3">
										<input name="kota_akun" disabled id="kota_akun" class="form-control" placeholder="Kota Akun">
										
									</div>
								</div>
							</div>
							<div class="card mb-3">
								<div class="card-body">
									<div class="row mb-3">
										<div class="col-sm-12 d-flex justify-content-between">
											<a href="{{route('user.page.karyawan.index')}}" class="btn rekkaa-page-link btn-outline-danger btn-sm"><i class="bx bx-arrow-back"></i> Kembali</a>
											<button type="submit" class="btn btn-simpan btn-warning btn-sm"><i class="bx bx-save"></i> Simpan</button>
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
<script>
	$(function() {
		let karyawanTaxTypeDefault = null;
		let optAutoNumeric = { 
			currencySymbol: "Rp. ",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: true,
		};
		let [karyawanSalaryNum] = AutoNumeric.multiple(["#karyawan_salary"], optAutoNumeric);
		let bpjsKesTunjanganLainnya, bpjsTkTunjanganLainnya;
		let tunjanganKaryawanNum = [];
		if($('.input_tunjangan').html() !== undefined) {
			if (AutoNumeric.getAutoNumericElement('.input_tunjangan') === null) {
				let inTunjangan = $('.input_tunjangan');
				for(let i=0; i<inTunjangan.length; i++) {
					let id = inTunjangan.eq(i).attr('id');
					// console.log('id', id); 
					tunjanganKaryawanNum[i] = AutoNumeric.multiple(['#'+id], optAutoNumeric)
				}
				// [tunjanganKaryawanNum] = AutoNumeric.multiple(['.input_tunjangan'], optAutoNumeric);
			}
		}
		if($('#bpjs_kes_tunjangan_lainnya').html() !== undefined) {
			if (AutoNumeric.getAutoNumericElement('#bpjs_kes_tunjangan_lainnya') === null) {
				[bpjsKesTunjanganLainnya] = AutoNumeric.multiple(['#bpjs_kes_tunjangan_lainnya'], optAutoNumeric);
			}
		}
		if($('#bpjs_tk_tunjangan_lainnya').html() !== undefined) {
			if (AutoNumeric.getAutoNumericElement('#bpjs_tk_tunjangan_lainnya') === null) {
				[bpjsTkTunjanganLainnya] = AutoNumeric.multiple(['#bpjs_tk_tunjangan_lainnya'], optAutoNumeric);
			}
		}
			
		// check bpjs checkbox
		if($("#TK_LAINNYA").is(":checked")) {
			$(".kontrak_bpjs_tk").attr('disabled', true);
		}
		if($("#KES_LAINNYA").is(":checked")) {
			$(".kontrak_bpjs_kes").attr('disabled', true);
		}
		$("#country_id").select2({
			ajax: {
				url: "{{route('master.negara.select')}}",
				data: function (params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
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
		// $("#karyawan_status").select2();
		// $("#karyawan_status").select2();
		$(`#karyawan_birthdate, #karyawan_contract_begin`).daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {
				format: 'DD-MM-YYYY'
			}
		});
		$("#karyawan_citizenship").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if(data.id == 'WNA') {
				$("#country_id").removeAttr('disabled').attr('required', true);
			} else {
				$("#country_id").removeAttr('required').attr('disabled', true);
			}
		});

		$(".box-bpjs-tk").slideUp();
		$(".box-bpjs-kes").slideUp();
		$("#karyawan_status").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if(data.id == 'NONKARYAWAN') {
				$("#karyawan_taxtype").removeAttr('disabled').attr('required', true);
				$("#karyawan_calculation_method_g").click();
				$("[name=karyawan_calculation_method]").attr('disabled', true);
				// $(".box-bpjs-tk").slideUp();
				// $(".box-bpjs-kes").slideUp();

				// bpjs dan tunjangan
				// uncheck
				if($("#karyawan_bpjs_tk").is(":checked")) {
					$("#karyawan_bpjs_tk").click();
				}
				if($("#karyawan_bpjs_kes").is(":checked")) {
					$("#karyawan_bpjs_kes").click();
				}

				$(".karyawan_bpjs").attr('disabled', true);
				$("#card-bpjs").slideUp();
				$("#card-tunjangan").slideUp();

				karyawanSalaryNum.set(0);
				$("#karyawan_salary").attr('disabled', true);
			} else {
				$("#karyawan_taxtype").val('').trigger('change');
				$("#karyawan_taxtype").removeAttr('required').attr('disabled', true);
				$("[name=karyawan_calculation_method]").not('#karyawan_calculation_method_mix').removeAttr('disabled');
				// $(".box-bpjs-tk").slideDown();
				// $(".box-bpjs-kes").slideDown();
				$(".karyawan_bpjs").removeAttr('disabled');
				$("#card-bpjs").slideDown();
				$("#card-tunjangan").slideDown();
				$("#karyawan_salary").attr('disabled', false);
			}
		});

		$("#karyawan_taxtype").select2({
			ajax: {
				url: "{{route('master.objekcategory.select')}}",
				data: function (params) {
					var query = {
						q: params.term,
						bukankaryawan: 1,
						jenis: 'PPH-21',
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
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
				url: "{{route('master.ptkp.select')}}",
				data: function (params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.ptkp_id;
						item.text = item.ptkp_description;
						item.data = {
							ptkp_id: item.ptkp_id,
							ptkp_description: item.ptkp_description,
							ptkp_marriage_status: item.ptkp_marriage_status,
							ptkp_rate: parseInt(item.ptkp_rate),
						};
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
            	},
            },
        });
		
		// show bpjs item
		$(".karyawan_bpjs").click(function(e) {
			let $this = $(this);
			let attr = $this.attr('data-type');
			if(attr == 'TENAGA_KERJA' && $this.is(':checked')) {
				$(".box-bpjs-tk").slideDown();
			} else if(attr == 'TENAGA_KERJA' && !$this.is(':checked')) {
				$(".box-bpjs-tk").slideUp();
			}

			if(attr == 'KESEHATAN' && $this.is(':checked')) {
				$(".box-bpjs-kes").slideDown();
			} else if(attr == 'KESEHATAN' && !$this.is(':checked')) {
				$(".box-bpjs-kes").slideUp();
			}
		});
		// show bpjs lainnya
		$("#KES_LAINNYA").click(function(e) {
			// console.log($(this))
			let checked = $(this).is(':checked');
			if(checked) {
				$(".kontrak_bpjs_kes").attr('disabled', true);
				$("#bpjs_kes_tunjangan_lainnya").removeAttr('disabled');
				$(".bpjs-kes-lainnya-box").slideDown();
			} else {
				$(".kontrak_bpjs_kes").removeAttr('disabled')
				$("#bpjs_kes_tunjangan_lainnya").attr('disabled', true);
				$(".bpjs-kes-lainnya-box").slideUp();
			}
		})

		$("#TK_LAINNYA").click(function(e) {
			console.log($(this))
			let checked = $(this).is(':checked');
			if(checked) {
				$(".kontrak_bpjs_tk").attr('disabled', true);
				$("#bpjs_tk_tunjangan_lainnya").removeAttr('disabled');
				$(".bpjs-tk-lainnya-box").slideDown();
			} else {
				$(".kontrak_bpjs_tk").removeAttr('disabled');
				$("#bpjs_tk_tunjangan_lainnya").attr('disabled', true);
				$(".bpjs-tk-lainnya-box").slideUp();
			}
		})
		$("#bpjs_kes_tunjangan_lainnya").change(function() {
			$("#KES_LAINNYA").val(bpjsKesTunjanganLainnya.getNumber());
		})
		$("#bpjs_tk_tunjangan_lainnya").change(function() {
			$("#TK_LAINNYA").val(bpjsTkTunjanganLainnya.getNumber());
		})
		// end bpjs item

		let formKaryawan = $("#formKaryawan").validate({
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
			rules: {
				karyawan_nik: {
					required: true,
					number: true,
					minlength: 16,
					maxlength: 16,
				},
				karyawan_phone: {
					required: true,
					number: true,
					rangelength: [9, 14],
				},
				karyawan_email: {
					email: true,
				}
			},
			submitHandler: function(form) {
				// validasi kontrak bpjs
				let kontrak_bpjs_kes = [];
				let kontrak_bpjs_tk = [];
				$(".bpjskes-input").filter(function() {
					console.log('chk', $(this).val())
					// console.log('chkval', $(this).val())
					let val = $(this).val();
					if($(this).is(':checked') && !$(this).is(':disabled')) {
						kontrak_bpjs_kes.push(val);
					}
				}).get();
				$(".bpjstk-input").filter(function() {
					console.log('chk', $(this).val())
					// console.log('chkval', $(this).val())
					let val = $(this).val();
					if($(this).is(':checked') && !$(this).is(':disabled')) {
						kontrak_bpjs_tk.push(val);
					}
				}).get();
				console.log('kontrak_bpjs_kes', kontrak_bpjs_kes)
				console.log('kontrak_bpjs_tk', kontrak_bpjs_tk)
				
				if($("#karyawan_status").val() != 'NONKARYAWAN') {
					if(kontrak_bpjs_kes.length < 1 || kontrak_bpjs_tk.length < 1) {
						Swal.fire({
							html: 'Silahkan pilih minimal 1 pengaturan BPJS!',
							confirmButtonText: "Ok",
							showCancelButton: false,
							icon: 'error'
						})
						return false;
					}
				}
				// console.log(form.method);
				// console.log(form.action);
				// console.log($(form).serialize());
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
							})
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