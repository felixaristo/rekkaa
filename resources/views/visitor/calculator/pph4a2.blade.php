<div class="pph24a2">
    <div class="row">
		<!-- Basic Layout -->
		<div class="col-md-6 offset-md-3">
			<h2 class="fw-bold py-3 mt-4 mb-4 text-center text-warning" style="text-decoration: underline;">
			Kalkulator Rekkaa
			</h2>
			<div class="card mb-4">
				<div class="card-header d-flex align-items-center justify-content-between">
					<h5 class="mb-0">PPh Pasal 4 Ayat 2</h5>
					<small class="text-muted float-end">Kalkulator</small>
				</div>
				<div class="card-body">
					<form action="#" id="form-cetak" autocomplete="off">
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="kepemilikan_npwp">Kepemilikan NPWP</label>
							<div class="col-sm-8">
								<select name="kepemilikan_npwp" id="kepemilikan_npwp" style="width: 100%;" data-placeholder="-: Pilih Data :-">
									<option value="">-: Pilih Data :-</option>
									@foreach($kepemilikan_npwp as $kepemilikan)
									<option value="{{$kepemilikan->kepemilikannpwp_code}}" data-nilai="{{$kepemilikan->kepemilikannpwp_value}}">{{$kepemilikan->kepemilikannpwp_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="kategori">Kategori</label>
							<div class="col-sm-8">
								<select name="kategori" id="kategori" style="width: 100%;" data-placeholder="-: Pilih Data :-">
									<!-- <option value=""></option> -->
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="jenis_transaksi">Jenis Transaksi</label>
							<div class="col-sm-8">
								<select name="jenis_transaksi" id="jenis_transaksi" style="width: 100%;" data-placeholder="-: Pilih Data :-">
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="tarif">Tarif</label>
							<div class="col-sm-8">
								<input
									disabled
									type="text"
									id="tarif"
									class="form-control tarif_currency"
									placeholder="0"
									aria-label="0"
								/>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="dasar_pajak">Dasar Pengenaan Pajak</label>
							<div class="col-sm-8">
								<input
									type="text"
									id="dasar_pajak"
									name="dasar_pajak"
									class="form-control dasar_pajak_currency"
									placeholder="0"
									/> 
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-4 col-form-label" for="total_pph">PPh Pasal 4 Ayat 2</label>
							<div class="col-sm-8">
								<div class="input-group input-group-merge">
									<input
									disabled
									type="text"
									id="total_pph"
									class="form-control total_pph_currency"
									placeholder="0"
									/> 
								</div>
							</div>
						</div>
						<div class="row justify-content-end">
							<div class="col-sm-12 text-right">
								@if(!session()->get('wajibpajak_current'))
                                <button type="submit" class="btn btn-warning btn-sm register-kalkulator"><i class='bx bxs-analyse'></i> Hitung PPH 4 ayat 2</button>
                                @else
                                <button type="submit" class="btn btn-warning btn-sm"><i class='bx bxs-file'></i> Bagikan</button>
                                @endif
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>
  </div>

@if(!session()->get('wajibpajak_current'))
<div class="modal fade" id="backDropCetakModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        @include('auth.register.register-form-calculator')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="modal fade" id="backDropCetakModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" data-bs-focus="false">
	<div class="modal-dialog modal-xl">
		<form class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="backDropModalTitle">{{$title}} </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-9 mb-3">
						<iframe src="" frameborder="0" width="100%" height="600"></iframe>
					</div>
					<div class="col-sm-3 mb-3">
						<div class="row" id="share-button">
						@if(request()->get('permission_codes') != null && in_array('COPY', request()->get('permission_codes')))
							<div class="col-sm-12 mb-3">
								<button type="button" class="btn btn-outline-warning btn-link btn-sm" data-type="copy"><i class='bx bx-link'></i> Salin Tautan</button>
							</div>
						@endif
						@if(request()->get('permission_codes') != null && in_array('Q_SEND_EMAIL', request()->get('permission_codes')))
							<div class="col-sm-12 mb-3">
								<button type="button" class="btn btn-outline-info btn-link btn-sm" data-type="send-email" data-kalkulator="pph-pasal-4-ayat-2"><i class='bx bx-envelope' ></i> Kirim via Email</button>
							</div>
						@endif
							<div class="col-sm-12 mb-3">
								<button type="button" class="btn btn-outline-success btn-link btn-sm" data-type="send-wa"><i class='bx bxl-whatsapp' ></i> Kirim via Whatsapp</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endif
  
  <script>
    $(function() {
		let npwpNilai = 0;
		let kategori = null;
		let currentUrl = "<?php echo url('kalkulator/pph-pasal-4-ayat-2/cetak?menu_id='.request()->get('menu_id').'&params='); ?>"
		$("#kepemilikan_npwp").select2().on("select2:select", function(e) {
			let data = e.params.data;
			npwpNilai = $(`#kepemilikan_npwp [value=${e.params.data.id}]`).attr("data-nilai");
			<?php if(session()->get('wajibpajak_current')) : ?>
			hitungPPh();
            <?php endif; ?>
		});
		$("#kategori").select2({
			// minimumInputLength: 3,
			ajax: {
			url: "{{route('master.objekcategory.select')}}",
			data: function (params) {
				var query = {
				q: params.term,
					jenis: 'PPH4-2',
					calculator: 1,
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
					item.id = item.objekpajak_category;
					item.text = item.objekpajak_category;
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
			kategori = data.text;
			$("#jenis_transaksi").val(null).trigger('change');
		})

		$("#jenis_transaksi").select2({
			// minimumInputLength: 3,
			ajax: {
			url: "{{route('master.objekpajak.select')}}",
			data: function (params) {
				var query = {
				q: params.term,
					kategori: kategori,
					jenis: 'PPH4-2',
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
		}).on("select2:select", function(e) {
			let data = e.params.data;
			tarif.set(parseFloat(data.objekpajak_rate));
			<?php if(session()->get('wajibpajak_current')) : ?>
			hitungPPh()
			<?php endif; ?>
		})

		let [dasarPajak, totalPPh] = AutoNumeric.multiple([".dasar_pajak_currency", ".total_pph_currency"], { 
			currencySymbol: "Rp. ",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: false,
			modifyValueOnWheel: false,
		});
		let [tarif] = AutoNumeric.multiple([".tarif_currency"], { 
			currencySymbol: " %",
			currencySymbolPlacement: "s",
			decimalCharacter: ",",
			digitGroupSeparator: ".",
			minimumValue: "0",
			unformatOnSubmit: false,
			modifyValueOnWheel: false,
		});
		$(".dasar_pajak_currency").keyup(function(e) {
			<?php if(session()->get('wajibpajak_current')) : ?>
			hitungPPh();
            <?php endif; ?>
		})
		// submit pdf
		$("#form-cetak").validate({
			rules: {
				// compound rule
				kepemilikan_npwp: {
					required: true,
				},
				jenis_transaksi: {
					required: true,
				},
				dasar_pajak: {
					required: true,
				}
			},
			submitHandler: function(form) {
				let knpwp = $("#kepemilikan_npwp").select2('data')[0].id;
				let knpwptext = $("#kepemilikan_npwp").select2('data')[0].text;
				let jttext = $("#jenis_transaksi").select2('data')[0].text;
				let params = `knpwp=${knpwp}
				&knpwptext=${knpwptext}
				&jt=${form.jenis_transaksi.value}
				&jttext=${jttext}
				&kt=${form.kategori.value}
				&tarif=${tarif.get()}
				&dp=${dasarPajak.get()}` ;
				params = btoa(params);
				// let url = `{{url('kalkulator/pph-pasal-4-ayat-2/cetak?params=')}}${params}`;
				// window.open(
				//   url,
				//   '_blank' // <- This is what makes it open in a new window.
				// );
				// $("#backDropCetakModal iframe").attr("src", url);
				// $("#backDropCetakModal").attr("data-params", params);
				// $("#backDropCetakModal").modal("show");
				$("#npwp_type").select2({
                    dropdownParent: $(".modal .modal-body"),
                });
				loadCetakKalkulator(currentUrl, params);

				// const urlParams = new URL(window.location.href);
				// urlParams.searchParams.append('page-type', 'kalkulator-pph21-nonkaryawan');
				// urlParams.searchParams.append('params', params);
				// console.log('params', urlParams.href)
				history.pushState({},"",`?page-type=kalkulator-pph4a2&menu_id=<?php echo (request()->get('menu_id') ? request()->get('menu_id') : 28) ?>&params=${params}`);
			},
			errorPlacement: function(error, element) {
				// console.log(element);
				var isInputGroup = $(element).parent();
				console.log('isInputGroup', isInputGroup.length)
				let elem = $(element);
				if (elem.hasClass("select2-hidden-accessible")) {
					element = $("#select2-" + elem.attr("id") + "-container").parent(); 
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
		});

		function hitungPPh() {
			let total = npwpNilai * (tarif.get() / 100) * dasarPajak.get();
			totalPPh.set(total);
		}

		// load cetak if has params
		let urlParams = findGetParameter('params');
		if(urlParams) {
			let decodeParams = atob(urlParams);
			let knpwp = findGetParameter('knpwp', decodeParams, false);
			// let knpwptext = findGetParameter('knpwptext', decodeParams, false);
			let jt = findGetParameter('jt', decodeParams, false);
			let jttext = findGetParameter('jttext', decodeParams, false);
			let kt = findGetParameter('kt', decodeParams, false);
			let nilaiTarif = findGetParameter('tarif', decodeParams, false);
			let nilaiDp = findGetParameter('dp', decodeParams, false);
			
			$("#kepemilikan_npwp").val(knpwp.trim()).trigger('change');
			$("#jenis_transaksi").append(new Option(jttext.trim(), jt.trim(), true, true)).trigger('change');
			$("#kategori").append(new Option(kt.trim(), kt.trim(), true, true)).trigger('change');
			// console.log('decodeParams', decodeParams)

			npwpNilai = $(`#kepemilikan_npwp [value=${knpwp.trim()}]`).attr("data-nilai");
			tarif.set(nilaiTarif.trim());
			dasarPajak.set(nilaiDp.trim());
			$(".dasar_pajak_currency").keyup();
			loadCetakKalkulator(currentUrl, urlParams);
		}

		// set meta title
	    setHtmlTitle('{{$title}}')
    })
  </script>