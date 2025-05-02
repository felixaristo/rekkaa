<?php
	$cbegindate = \Carbon\Carbon::parse($karyawan->karyawan_contract_begin);
	$dt_cbegin = $cbegindate->translatedFormat('d-m-Y');

	$dt_birthdate = null;
	$dt_contractend = null;

	if($karyawan->karyawan_birthdate) {
		$birthdate = \Carbon\Carbon::parse($karyawan->karyawan_birthdate);
		$dt_birthdate = $birthdate->translatedFormat('d-m-Y');
	}

	if($karyawan->karyawan_contract_end) {
		$contractend = \Carbon\Carbon::parse($karyawan->karyawan_contract_end);
		$dt_contractend = $contractend->translatedFormat('d-m-Y');
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
						<form action="{{route('user.page.nonkaryawan.update', ['karyawanId' => $karyawan->karyawan_id])}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawan" autocomplete="off">
							<div class="nav-align-top mb-4">
								<ul class="nav nav-tabs mb-3 nav-fill" role="tablist">
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-identitas" aria-controls="navs-tabs-justified-identitas" tabindex="-1"><i class="bx bx-user"></i> Data Identitas</button>
									</li>
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-pekerjaan" aria-controls="navs-tabs-justified-pekerjaan" tabindex="-1"><i class='bx bx-briefcase'></i> Data Pekerjaan</button>
									</li>
									<!-- <li class="nav-item" role="presentation">
									<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-penggajian" aria-controls="navs-tabs-justified-penggajian" tabindex="-1"><i class='bx bx-money'></i> Data Penggajian</button>
									</li>
									<li class="nav-item" role="presentation">
										<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-tambahan" aria-controls="navs-tabs-justified-tambahan" tabindex="-1"><i class='bx bx-message-square-add' ></i> Data Tambahan</button>
									</li> -->
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade active show" id="navs-tabs-justified-identitas" role="tabpanel">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group row mb-3">
													<label for="karyawan_enid" class="col-sm-2 lbl-req">ID Non Karyawan</label>
													<div class="col-sm-4">
														<input type="text" required value="{{$karyawan->karyawan_enid}}" name="karyawan_enid" id="karyawan_enid" class="form-control" placeholder="Masukkan ID Non Karyawan">
													</div>
													<label for="karyawan_name" class="col-sm-2 lbl-req">Nama Lengkap</label>
													<div class="col-sm-4">
														<input type="text" required value="{{$karyawan->karyawan_name}}" name="karyawan_name" id="karyawan_name" class="form-control" placeholder="Masukkan Nama Lengkap">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_nik" class="col-sm-2 lbl-req">NIK</label>
													<div class="col-sm-4">
														<input type="text" required value="{{$karyawan->karyawan_nik}}" name="karyawan_nik" id="karyawan_nik" class="form-control nik-input" placeholder="Masukkan NIK">
													</div>
													<label for="karyawan_phone" class="col-sm-2">No. Telepon</label>
													<div class="col-sm-4">
														<input type="text" name="karyawan_phone" value="{{$karyawan->karyawan_phone}}" id="karyawan_phone" class="form-control" placeholder="Masukkan No. Telepon">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP</label>
													<div class="col-sm-4">
														<input type="text" value="{{$karyawan->karyawan_npwp}}" name="karyawan_npwp" id="karyawan_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
													</div>
													<label for="karyawan_email" class="col-sm-2">Email</label>
													<div class="col-sm-4">
														<input type="email" value="{{$karyawan->karyawan_email}}" name="karyawan_email" id="karyawan_email" class="form-control" placeholder="Masukkan Email">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthplace" class="col-sm-2">Tempat Lahir</label>
													<div class="col-sm-4">
														<input type="text" value="{{$karyawan->karyawan_birthplace}}" name="karyawan_birthplace" id="karyawan_birthplace" class="form-control" placeholder="Masukkan Tempat Lahir">
													</div>
													<label for="karyawan_citizenship" class="col-sm-2">Kewarganegaraan</label>
													<div class="col-sm-4">
														<select required name="karyawan_citizenship" required id="karyawan_citizenship" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="WNI" {{ $karyawan->karyawan_citizenship == 'WNI' ? 'selected' : '' }}>WNI</option>
															<option value="WNA" {{ $karyawan->karyawan_citizenship == 'WNA' ? 'selected' : 'disabled' }}>WNA</option>
														</select>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_birthdate" class="col-sm-2">Tgl. Lahir</label>
													<div class="col-sm-4">
														<input type="text" value="{{($dt_birthdate) ? $dt_birthdate : '' }}" name="karyawan_birthdate" id="karyawan_birthdate" class="form-control" placeholder="Masukkan Tanggal Lahir">
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
															<input name="karyawan_gender" class="form-check-input" type="radio" value="L" id="karyawan_gender_l" {{$karyawan->karyawan_gender == 'L' ? 'checked' : ''}}>
															<label class="form-check-label" for="karyawan_gender_l"> Laki-laki </label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_gender" class="form-check-input" type="radio" value="P" id="karyawan_gender_p" {{$karyawan->karyawan_gender == 'P' ? 'checked' : ''}}>
															<label class="form-check-label" for="karyawan_gender_p"> Perempuan </label>
														</div>
													</div>
													<label for="karyawan_address" class="col-sm-2">Alamat</label>
													<div class="col-sm-4">
														<textarea name="karyawan_address" id="karyawan_address" class="form-control" placeholder="Masukkan Alamat">{{$karyawan->karyawan_address}}</textarea>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="ptkp_id" class="col-sm-2 lbl-req">Status Perkawinan</label>
													<div class="col-sm-4">
														<select name="ptkp_id" required id="ptkp_id" {{$karyawan->payroll_isexist > 0 ? 'disabled' : ''}} style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="{{$karyawan->ptkp->ptkp_id}}" selected>{{$karyawan->ptkp->ptkp_description}}</option>
														</select>
														{!! $karyawan->payroll_isexist > 0 ? '<span class="text-danger">*Silahkan hapus transaksi yang belum di konfirmasi untuk melakukan perubahan status perkawinan</span>' : '' !!}
													</div>
													<label for="karyawan_photo" class="col-sm-2">Upload Foto</label>
													<div class="col-sm-4">
														<input name="karyawan_photo" class="form-control" type="file" id="karyawan_photo">
														<span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span>
														<br>
														@if($karyawan->karyawan_photo)
														<div class="d-block rounded">
															<img src="{{$karyawan->karyawan_photo}}" alt="avatar" class="img-fluid">
														</div>
														@endif
													</div>
												</div>
												<div class="form-group row mb-3 userbx">
													@if($karyawan->karyawan_isuser == 1 || $karyawan->karyawan_email)
													<div class="col-sm-6">
														<div class="border py-3 px-3" style="border-color: #f6830f!important">
															<div class="form-check form-check-inline">
																<input name="karyawan_isuser" {{($karyawan->karyawan_isuser == '1') ? 'checked' : ''}} class="form-check-input" type="checkbox"  value="1" id="karyawan_isuser">
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
													<!-- <label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan</label>
													<div class="col-sm-4">
														<select name="karyawan_status" required id="karyawan_status" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="TETAP" {{$karyawan->karyawan_status == 'TETAP' ? 'selected' : ''}}>Karyawan Tetap</option>
															<option value="KONTRAK" {{$karyawan->karyawan_status == 'KONTRAK' ? 'selected' : ''}}>Karyawan Kontrak</option>
															<option value="PERCOBAAN" {{$karyawan->karyawan_status == 'PERCOBAAN' ? 'selected' : ''}}>Percobaan</option>
														</select>
													</div> -->
													<label for="karyawan_contract_begin" class="col-sm-2 lbl-req">Tgl. Masuk</label>
													<div class="col-sm-4">
														<input type="text" required value="{{$dt_cbegin}}" name="karyawan_contract_begin" id="karyawan_contract_begin" class="form-control" placeholder="Tanggal Masuk">
													</div>

													<label for="karyawan_contract_end" class="col-sm-2">Tgl. Berakhir</label>
													<div class="col-sm-4">
														<input type="text" value="{{$dt_contractend}}" name="karyawan_contract_end" id="karyawan_contract_end" class="form-control" placeholder="Tanggal Berakhir">
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="jenis_transaksi" class="col-sm-2 lbl-req">Kode Objek Pajak</label>
													<div class="col-sm-4">
														<select name="jenis_transaksi" style="width: 100%;" id="jenis_transaksi" class="form-control" data-placeholder="-:Pilih Data:-">
															@if($karyawan->masakerja && $karyawan->masakerja->ms_objekpajak_code)
																<option value="{{$karyawan->masakerja->ms_objekpajak_code}}" selected>{{$karyawan->masakerja->objekpajak->objekpajak_description}}</option>
															@endif
														</select>
													</div>
													<label for="karyawan_calculation_method" class="col-sm-2 lbl-req">Metode PPh 21</label>
													<div class="col-sm-4">
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" {{$karyawan->payroll_isexist > 0 ? 'disabled' : ''}} {{($karyawan->karyawan_calculation_method == 'GROSS') ? 'checked' : ''}} class="form-check-input" type="radio" value="GROSS" id="karyawan_calculation_method_g">
															<label class="form-check-label" for="karyawan_calculation_method_g">Gross</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" {{$karyawan->payroll_isexist > 0 ? 'disabled' : ''}} {{($karyawan->karyawan_calculation_method == 'GROSS_UP') ? 'checked' : ''}} class="form-check-input" type="radio" value="GROSS_UP" id="karyawan_calculation_method_gu">
															<label class="form-check-label" for="karyawan_calculation_method_gu">Gross Up</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" {{$karyawan->payroll_isexist > 0 ? 'disabled' : ''}} {{($karyawan->karyawan_calculation_method == 'NETT') ? 'checked' : ''}} class="form-check-input" type="radio" value="NETT" id="karyawan_calculation_method_net">
															<label class="form-check-label" for="karyawan_calculation_method_net">Nett</label>
														</div>
														<div class="form-check form-check-inline">
															<input name="karyawan_calculation_method" disabled class="form-check-input" type="radio" value="MIX" id="karyawan_calculation_method_mix">
															<label class="form-check-label" for="karyawan_calculation_method_mix">MIX</label>
														</div>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_ismultiple" class="col-sm-2 lbl-req">Sumber Penghasilan</label>
													<div class="col-sm-4">
														<select name="karyawan_ismultiple" required id="karyawan_ismultiple" style="width: 100%;" data-placeholder="-: Pilih Data :-">
															<option value="">-: Pilih Data :-</option>
															<option value="1" {{$karyawan->karyawan_ismultiple == 1 ? 'selected' : ''}}>Beberapa Pemberi Kerja</option>
															<option value="0" {{$karyawan->karyawan_ismultiple == 0 ? 'selected' : ''}}>Satu Pemberi Kerja</option>
														</select>
													</div>
													<label for="karyawan_position" class="col-sm-2">Jabatan</label>
													<div class="col-sm-4">
														<div class="input-group">
															<select style="width: 85%;" name="karyawan_position" id="karyawan_position" data-placeholder="-: Pilih Data :-">
																@if($karyawan->jabatan)
																	<option value="{{$karyawan->jabatan->karyawanjabatan_id}}" selected>{{$karyawan->jabatan->karyawanjabatan_name}}</option>
																@endif
															</select>
															<button class="btn btn-sm btn-outline-info" type="button" id="btn-position"><i class="bx bx-plus"></i></button>
														</div>
													</div>
												</div>
												<div class="form-group row mb-3">
													<label for="karyawan_bankid" class="col-sm-2 lbl-req">Nama Bank</label>
													<div class="col-sm-4">
														<select name="karyawan_bankid" style="width: 100%;" id="karyawan_bankid" class="form-control" data-placeholder="-:Pilih Data:-">
															@if($karyawan->bank)
																<option value="{{$karyawan->bank->bank_id}}" selected>{{$karyawan->bank->bank_name}}</option>
															@endif
														</select>
													</div>
													<label for="karyawan_banknokartu" class="col-sm-2">No. Rekening</label>
													<div class="col-sm-4">
													<input
															name="karyawan_banknokartu"
															value="{{$karyawan->karyawan_bankno}}"
															id="karyawan_banknokartu"
															class="form-control"
															placeholder="No. Rekening"
															@if(!$karyawan->bank)
																disabled
															@endif
														>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div style="font-style: italic;">
									<p style="color: red; margin: 0;">Catatan :</p>
									<p style="color: grey; margin: 0;">- Data dengan simbol (<span style="color: red;">*</span>) tidak boleh dikosongkan.</p>
									<p style="color: grey; margin: 0;">- Mohon isi dengan <span style="color: red;">00.000.000.0-000.000</span> jika tidak memimliki NPWP.</p>
								</div>
							<div class="text-right">
								<button type="submit" class="btn btn-warning btn-sm">Simpan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('user.master.karyawan.karyawan-jabatan')
<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
	$(function() {
		let currentMenuId = "{{request()->get('menu_id')}}";
		let karyawanId = "{{$karyawan->karyawan_id}}";
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

		$("#jenis_transaksi").select2({
			// minimumInputLength: 3,
			ajax: {
			url: `{{route('master.objekpajak.select')}}?menu_id=${currentMenuId}`,
			data: function (params) {
				var query = {
				q: params.term,
					kategori: 'PPH-21',
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
		})

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
		
		$(`#karyawan_birthdate, #karyawan_contract_begin, #karyawan_contract_end`).daterangepicker({
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

		$("#karyawan_ismultiple").select2();

		$("#karyawan_citizenship").select2().on("select2:select", function(e) {
			let data = e.params.data;
			if (data.id == 'WNA') {
				$("#country_id").removeAttr('disabled').attr('required', true);
			} else {
				$("#country_id").removeAttr('required').attr('disabled', true);
			}
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
							karyawan_enid: val,
							karyawan_id: karyawanId,
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
							karyawan_nik: val,
							karyawan_id: karyawanId,
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
							karyawan_npwp: val,
							karyawan_id: karyawanId,
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
			$(".userbx").html('');
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
							karyawan_email: val,
							karyawan_id: karyawanId,
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
							$(".userbx").html('');
							if (!response.success) {
								Swal.fire({
									showCancelButton: false,
									confirmButtonText: "Ok",
									icon: 'error',
									html: response.message
								})
								return false;
							}

							$(".userbx").html(`<div class="col-sm-6">
								<div class="border py-3 px-3" style="border-color: #f6830f!important">
									<div class="form-check form-check-inline">
										<input name="karyawan_isuser" class="form-check-input" type="checkbox"  value="1" id="karyawan_isuser">
										<label class="form-check-label" for="karyawan_isuser">Tambahkan Sebagai User</label>
									</div>
								</div>
							</div>`);
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
				},
				karyawan_npwp: {
					required: true,
					minlength: 20,
					maxlength: 20,
				},
				karyawan_calculation_method: {
					required: true,
				},
				jenis_transaksi: {
					required: true,
				}
			},
			submitHandler: function(form) {
				$(".spinner-box").css({
					'display': 'table'
				});

				console.log("test")

				// let karyawan_salary = karyawanSalaryNum.getNumber();
				let formData = new FormData();
				let dataArr = $("#formKaryawan").serializeArray();

				for (let i = 0; i < dataArr.length; i++) {
					formData.append(dataArr[i].name, dataArr[i].value);
				}

				formData.append('_token', $("meta[name=csrf-token]").attr('content'));
				// formData.append('karyawan_salary', karyawan_salary);
				formData.append('karyawan_photo', $('#karyawan_photo')[0].files[0]);
				console.log('dataArr', dataArr);
				console.log('formData', formData);
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

								if(errs['karyawan_ismultiple']) {
									errtab = 1;
									formKaryawan.showErrors({'karyawan_ismultiple': errs['karyawan_ismultiple']});
								}

								if(errs['jenis_transaksi']) {
									errtab = 1;
									formKaryawan.showErrors({'jenis_transaksi': errs['jenis_transaksi']});
								}

								// if(errs['karyawan_bankid']) {
								// 	errtab = 1;
								// 	formKaryawan.showErrors({'karyawan_bankid': errs['karyawan_bankid']});
								// }

								if(errs['karyawan_calculation_method']) {
									errtab = 1;
									formKaryawan.showErrors({'karyawan_calculation_method': errs['karyawan_calculation_method']});
								}

								// if(errs['karyawan_banknokartu']) {
								// 	errtab = 1;
								// 	formKaryawan.showErrors({'karyawan_banknokartu': errs['karyawan_banknokartu']});
								// }

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
								if(errs['karyawan_npwp']) {
									errtab = 0;
									formKaryawan.showErrors({'karyawan_npwp': errs['karyawan_npwp']});
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

						let href = `{{route('user.page.nonkaryawan.index')}}?menu_id=${currentMenuId}`;
						loadPage(href);
					}
				})
			},
		})
		
		// set meta title
		setHtmlTitle('{{$title}}')
	})
</script>