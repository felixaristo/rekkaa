<div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<div class="row">
				<div class="col-sm-6">
					<h5 class="card-header">NPWP</h5>
				</div>
				<div class="col-sm-6">
					<div class="card-header text-right">
					<a class="btn btn-sm btn-warning" id="btnCreateNPWP" href="#">
					+ NPWP
					</a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="card-body">
					<div class="col-sm-12">
						<div class="text-nowrap">
							<table class="table table-hover display nowrap" style="width: 100%" id="table-wajib-pajak">
								<thead class="table-light">
									<tr>
										<!-- <th>No.</th> -->
										<th>Nama</th>
										<th>NPWP</th>
										<th>Tipe</th>
										<th>Tipe</th>
										<th>Status</th>
										<th>Actions</th>
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
		<!-- Bootstrap Table with Header - Light -->
	</div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalCreateNPWP" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">NPWP</h5>
				<button
				type="button"
				class="btn-close"
				data-bs-dismiss="modal"
				aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="formNpwp" method="POST" action="{{route('user.page.npwp.store')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
					<input type="hidden" name="wajibpajak_id" id="wajibpajak_id">
					<div class="col-sm-6">
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_name">Nama</label>
							<div class="col-sm-12">
								<input type="text" required name="wajibpajak_name" id="wajibpajak_name" class="form-control" placeholder="Masukkan Nama">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_email">Email</label>
							<div class="col-sm-12">
								<input type="text" required name="wajibpajak_email" id="wajibpajak_email" class="form-control" placeholder="Masukkan Email">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_phone">No. Telepon</label>
							<div class="col-sm-12">
								<input type="text" required name="wajibpajak_phone" id="wajibpajak_phone" class="form-control" placeholder="Masukkan No. Telepon">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_city">Kota</label>
							<div class="col-sm-12">
								<select required name="wajibpajak_city" style="width: 100%;" id="wajibpajak_city" class="form-control" data-placeholder="Masukkan Kota"></select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_address">Alamat</label>
							<div class="col-sm-12">
								<textarea required name="wajibpajak_address" id="wajibpajak_address" class="form-control" placeholder="Masukkan Alamat"></textarea>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_type">Tipe</label>
							<div class="col-sm-12">
								<select name="wajibpajak_type" required style="width: 100%;" id="wajibpajak_type" class="form-control" data-placeholder="-:Pilih Data:-">
									<option value="BADAN" selected>BADAN</option>
									<option value="INDIVIDU">INDIVIDU</option>
								</select>
							</div>
						</div>
						<div class="row mb-3 register_individu_field">
                            <label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_nik" style="display: none;">NIK</label>
                            <div class="col-sm-12">
                                <input type="text" name="wajibpajak_nik" id="wajibpajak_nik" class="form-control nik-input" placeholder="Masukkan NIK">
                            </div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_npwp">NPWP</label>
							<div class="col-sm-12">
								<input type="text" required name="wajibpajak_npwp" id="wajibpajak_npwp" class="form-control npwp-input" placeholder="Masukkan NPWP">
							</div>
							<div class="col-sm-12">
								<div class="form-check form-check-inline register_kepemilikan_npwp_box" style="display: none;">
									<input name="register_kepemilikan_npwp" disabled class="form-check-input" type="checkbox" value="t" id="register_kepemilikan_npwp">
									<label class="form-check-label" for="register_kepemilikan_npwp">Tidak Memiliki NPWP</label>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="ms_klu_id">KLU</label>
							<div class="col-sm-12">
								<select required style="width: 100%;" name="ms_klu_id" id="ms_klu_id" class="form-control" data-placeholder="-:Pilih KLU:-"></select>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_postal_code">Kode Pos</label>
							<div class="col-sm-12">
								<input required type="text" name="wajibpajak_postal_code" id="wajibpajak_postal_code" class="form-control" placeholder="Masukkan Kode Pos">
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="wajibpajak_periodepembayaran">Periode Pembayaran</label>
							<div class="col-sm-12">
								<select name="wajibpajak_periodepembayaran" required style="width: 100%;" id="wajibpajak_periodepembayaran" class="form-control" data-placeholder="-:Pilih Data:-">
								</select>
							</div>
						</div>
					</div>
					<!-- <div class="col-sm-12">
						<div class="row mb-3">
						<label class="col-sm-12 nodot-label" for="wajibpajak_anggota_tim">Anggota Tim</label>
						<div class="col-sm-12">
							<select name="wajibpajak_anggota_tim[]" style="width: 100%;" multiple id="wajibpajak_anggota_tim" class="form-control" data-placeholder="Masukkan Anggota Tim"></select>
						</div>
						</div>
					</div> -->

					<div class="col-sm-6">
						<div class="row mb-3">
							<label class="col-sm-12 nodot-label lbl-req" for="supscription_id">Subscription</label>
							<div class="col-sm-12">
								<a href="#" class="btn btn-sm btn-outline-danger mb-1" id="subscription-modal-btn"><i class='bx bx-credit-card-front'></i> Pilih Subscription</a>
								<div id="subscription-selected-box"></div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 text-right">
						<button type="submit" class="btn btn-warning btn-sm">Simpan</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalSubcription" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Subscription</h5>
				<button
				type="button"
				class="btn-close"
				data-bs-dismiss="modal"
				aria-label="Close"
				></button>
			</div>
			<div class="modal-body py-5">
				<div class="row subscription-box">
				<table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="70"></th>
                                    <?php
                                    // $group_sub = [];
                                    foreach($subscription as $sc) :
                                        // array_push($group_sub[$sc->subscriptionpermission->])
                                    ?>
                                    <td class="text-bold text-center" style="vertical-align: top;" width="70">
                                        {{$sc->subscription_title}}
                                        <br>
                                        @if($sc->subscription_type != 'PLATINUM' && $sc->subscription_type == 'FREE')
                                        <span class="card-old-subscription text-danger">Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_priceold, 0, ',', '.')}} / bln</span>
                                        <br>
                                        @endif
                                        <b>Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_price, 0, ',', '.')}} / bln</b>
                                        <br>
                                        {!!$sc->subscription_default == '1' ? '<b style="font-size:12px" class="text-warning">Rekomendasi</b>' : ''!!}
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php
                                $group = [];
                                foreach($permission as $pm) :
                                    if(!in_array($pm->permission_group, $group)) :
                                        array_push($group, $pm->permission_group); ?>
                                    <th colspan="{{count($subscription) + 1}}" style="vertical-align: middle">{{$pm->permission_group_name}}</th>
                                    <?php endif; ?>
                                <tr>
                                    <td width="70" style="padding-left: 30px; vertical-align: middle">{{$pm->permission_code_name}}</td>
                                    <?php
                                    $span = '<span class="bx bx-x text-danger fs-3 text-bold"></span>';
                                    foreach($subscription as $sc) :

                                        foreach($sc->subscriptionpermission as $scp) :
                                            if($scp->ms_permission_code == $pm->permission_code) {
                                                $span = ($scp->subscriptionpermission_text) ? $scp->subscriptionpermission_text : '<span class="bx bx-check text-success fs-3 text-bold"></span> ';
                                                break;
                                            }
                                        endforeach;
                                        ?>
                                    <td width="70" class="text-center" style="vertical-align: middle">{!!$span!!}</td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach ?>
                                <tr>
                                    <td width="70" style="vertical-align: middle">Periode Pembayaran</td>
                                    @foreach($subscription as $sc) :
                                    <td width="70" class="text-center">
                                        @if($sc->subscription_type == 'PLATINUM')
                                        <div class="card-choose text-center">
                                            <a href="#" class="btn btn-sm btn-warning"><i class='bx bx-phone'></i> Hubungi Kami</a>
                                        </div>
                                        @else
                                        <div class="card-choose text-center">
                                            <!-- <div class="row mb-3 text-center periodepembayaran">
                                                <div class="col-sm-12">
                                                    <select name="wajibpajak_periodepembayaran" required style="width: 120px;" class="form-control wajibpajak_periodepembayaran" data-placeholder="-:Pilih Data:-">
                                                        <option value="MONTHLY" selected>1 Bulan Sekali (Diskon {{$sc->subscription_monthlydiscount}} %)</option>
                                                        <option value="QUARTELY">4 Bulan Sekali (Diskon {{$sc->subscription_quarterlydiscount}} %)</option>
                                                        <option value="SEMI_ANNUAL">6 Bulan Sekali (Diskon {{$sc->subscription_semiannualdiscount}} %)</option>
                                                        <option value="YEARLY">12 Bulan Sekali (Diskon {{$sc->subscription_yearlydiscount}} %)</option>
                                                    </select>
                                                </div>
                                            </div> -->
                                            <a href="#" class="btn btn-sm {{$sc->subscription_default == '1' ? 'btn-warning' : 'btn-secondary'}} mb-1 btn-choose-packet" data-json="{{base64_encode(json_encode($sc))}}" id="subscription-modal-btn"><i class='bx bx-credit-card'></i> Pilih Paket</a>
                                        </div>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
				@foreach($subscription as $sc)
					<!-- <div class="col-sm-4 mb-5">
						<div class="card shadow-none bg-transparent border {{$sc->subscription_default == '1' ? 'border-warning' : 'border-secondary'}} mb-3">
							<div class="card-body">
								<div class="card-tag">
									<i class="bx bx-purchase-tag {{$sc->subscription_default == '1' ? 'text-warning' : 'text-secondary'}}"></i>
								</div>
								<div class="card-diagonal"></div>

								<h5 class="card-title text-center {{$sc->subscription_default == '1' ? 'border-warning' : 'border-secondary'}}">
									<span class="card-title-subscription mb-1 {{$sc->subscription_default == '1' ? 'text-warning' : 'text-secondary'}}">{{$sc->subscription_title}}</span>
									<br>
									@if($sc->subscription_type != 'PLATINUM' && $sc->subscription_type == 'FREE')
									<span class="card-old-subscription text-danger">Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_priceold, 0, ',', '.')}} / bulan</span>
									<br>
									@endif
									<b>Rp. {{($sc->subscription_type == 'PLATINUM') ? '-' : number_format($sc->subscription_price, 0, ',', '.')}} / bulan</b>
									<br>
									{!!$sc->subscription_default == '1' ? '<b style="font-size:12px" class="text-warning">Rekomendasi</b>' : ''!!}
								</h5>
								<div class="card-text card-subscription-content mb-3" style="min-height: 200px;">{!!$sc->subscription_description!!}</div>
								@if($sc->subscription_type == 'PLATINUM')
								<div class="card-choose text-center">
									<a href="#" class="btn btn-sm btn-warning"><i class='bx bx-phone'></i> Hubungi Kami</a>
								</div>
								@else
								<div class="card-choose text-center">
									<a href="#" class="btn btn-sm {{$sc->subscription_default == '1' ? 'btn-warning' : 'btn-secondary'}} mb-1 btn-choose-packet" data-json="{{base64_encode(json_encode($sc))}}" id="subscription-modal-btn"><i class='bx bx-credit-card'></i> Pilih Paket</a>
								</div>
								@endif
							</div>
						</div>
					</div> -->
				@endforeach
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
$(function() {
	let subscriptionData = null;
	subscriptionData = {subscription_id: 9};

	$(".register_kepemilikan_npwp_box, .register_individu_field").hide();
	$("#register_kepemilikan_npwp").click(function(e) {
		// e.preventDefault
		let isChecked = $(this).is(':checked');
		console.log('isChecked', isChecked);
		if(isChecked) {
			$("#wajibpajak_npwp").val("");
			$("#wajibpajak_npwp").attr('disabled', true);
		} else {
			$("#wajibpajak_npwp").removeAttr('disabled');
		}
	})

	$("#wajibpajak_periodepembayaran").select2({
		dropdownParent: $("#modalCreateNPWP .modal-body"),
	});
	$("#subscription-modal-btn").click(function(e) {
		e.preventDefault();
		$("#modalSubcription").modal("show");
	});
	$(".btn-choose-packet").click(function(e) {
		e.preventDefault();
		let data = $(this).attr("data-json");
		let parseData = JSON.parse(atob(data));
		console.log(parseData);
		subscriptionData = null;
		$("#subscription-selected-box").html("");
		$("#wajibpajak_periodepembayaran").html("");
		if(parseData.subscription_id != undefined) {
			subscriptionData = parseData;

			$("#subscription-selected-box").html(`
				<div class="card shadow-none bg-transparent border border-secondary mb-3">
					<div class="card-body">
						<h5 class="card-title text-center">${subscriptionData.subscription_title}
							<br>
							<b>Rp. ${formatCurrency(subscriptionData.subscription_price)} / Bln</b>
						</h5>
						<div class="card-text">${subscriptionData.subscription_description}</div>
					</div>
				</div>
			`);

			$("#wajibpajak_periodepembayaran").append(`
				<option value="MONTHLY" selected>1 Bulan Sekali (Diskon ${subscriptionData.subscription_monthlydiscount} %)</option>
				<option value="QUARTELY">4 Bulan Sekali (Diskon ${subscriptionData.subscription_quarterlydiscount} %)</option>
				<option value="SEMI_ANNUAL">6 Bulan Sekali (Diskon ${subscriptionData.subscription_semiannualdiscount} %)</option>
				<option value="YEARLY">12 Bulan Sekali (Diskon ${subscriptionData.subscription_yearlydiscount} %)</option>
			`);
		}
		$("#modalSubcription").modal("hide");
	})
	// Begin Table Wajib Pajak
	let tblWajibPajak = $("#table-wajib-pajak").DataTable({
	// "filtering": false,
		"searching": false,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.
		"searchDelay": 1050,
		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": "{{route('user.page.npwp.datatable')}}",
			"type": "GET",
			"data": function(data) {
				//     console.log(data); // send data to server
			}
		},
		"fnInitComplete": function() {
			// this.fnAdjustColumnSizing(true);
			// $(this).find(".cetak-registrasi").select2();
		},
		"autoWidth": true,
		"columnDefs": [{
			target: [5],
			width: 30
		}, {
			target: [1,2,3,4,5],
			className: 'text-center'
		}],
		"columns": [
			{
				"data": "wajibpajak_name"
			},
			{
				"data": "wajibpajak_npwp"
			},
			{
				"data": "wajibpajak_type"
			},
			{
				"data": "usersubscription",
				"render": function(data, type, row) {
					if(data) {
						if(row.usersubscription) {
							return `<span class="badge rounded-pill bg-label-warning"></span>`;
						} else {
							if(row.userorder) {
								return `<span class="badge rounded-pill bg-label-warning">${row.userorder.userorder_subscriptiontype}</span>`;
							} else {
								return `<span class="badge rounded-pill bg-label-secondary">FREE</span>`;
							}
						}
					}
					// console.log('data', data)
					return '';
				}
			},
			{
				"data": "wajibpajak_active",
				"render": function(data, type, row) {
					return (data == '1') ? `<span class="badge rounded-pill bg-label-success">${data}</span>` : `<span class="badge rounded-pill bg-label-danger">${data}</span>`;
				}
			},
			{
				"data": "wajibpajak_id",
				"render": function(data, type, row) {
						let btnTagihan = '';
						if(row.wajibpajak_active == '0') {
							btnTagihan = `<a class="dropdown-item btn-bayartagihan" href="javascript:void(0);"
							><i class="bx bx-dollar me-1 text-warning"></i> Bayar Tagihan</a>`
						} else {
							if(row.usersubscription) {
								let now = moment();
								let then = moment('1990-01-01');
								let diff = moment.duration(moment(then).diff(moment(now)));
								// let d = moment.duration(ms);
								// let s = d.format("hh:mm:ss");
								// console.log('d '+row.wajibpajak_npwp, diff.asDays())
								let diffDays = diff.asDays();
								// console.log("{{env('SUBSCRIPTION_NOTIF_BEFORE_EXPIRED')}}");
								// console.log(diffDays);
								// if(diffDays < parseInt("{{env('SUBSCRIPTION_NOTIF_BEFORE_EXPIRED')}}")) {
									btnTagihan = `<a class="dropdown-item btn-lanjutlangganan" href="javascript:void(0);"
									><i class="bx bx-dollar me-1 text-warning"></i> Lanjutkan Berlangganan</a>`
								// }
							}
						}
						return `
						<div class="dropdown">
						<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
							<i class="bx bx-dots-vertical-rounded"></i>
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item btn-edit" href="javascript:void(0);"
							><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a>
							${btnTagihan}
						</div>
						</div>
					`
				}
			},
		],
	});
	// End Table Wajib Pajak
	$("#rekkaa-page-content").on("click", "#btnCreateNPWP", function(e) {
		e.preventDefault();
		// reset form
		resetForm('#formNpwp');
		// change url
		$("#formNpwp").attr("action", "{{route('user.page.npwp.store')}}");
		
		$("#wajibpajak_type").removeAttr('disabled');
		$("#wajibpajak_npwp").removeAttr('disabled');
		$("#wajibpajak_nik").removeAttr('disabled');
		// $("#wajibpajak_email").removeAttr("disabled");
		$("#register_kepemilikan_npwp").removeAttr("disabled");
		if($("#register_kepemilikan_npwp").is(":checked")) {
			$("#register_kepemilikan_npwp").click();
		}
		
		$("#modalCreateNPWP").modal("show");
	})

	$(document).on("click", "#table-wajib-pajak .btn-edit", function(e) {
		e.preventDefault();
		// reset form
		resetForm('#formNpwp');
		$("#register_kepemilikan_npwp").removeAttr('disabled');
		// $("#wajibpajak_email").attr("disabled", true);
		// get row
		let row = $(this).closest('tr');
		let data = tblWajibPajak.row(row).data();
        console.log('data',data)
		// change url
		$("#formNpwp").attr("action", "{{url('/user/npwp/update')}}"+"/"+data.wajibpajak_id);
		// set data
		$("#wajibpajak_id").val(data.wajibpajak_id)
		$("#wajibpajak_name").val(data.wajibpajak_name)
		$("#wajibpajak_email").val(data.wajibpajak_email)
		$("#wajibpajak_phone").val(data.wajibpajak_phone)
		$("#wajibpajak_address").val(data.wajibpajak_address)
		$("#wajibpajak_postal_code").val(data.wajibpajak_postal_code)
		$("#wajibpajak_anggota_tim").val(data.wajibpajak_anggota_tim)
		$("#wajibpajak_type").attr('disabled', true).val(data.wajibpajak_type).trigger('change')
		
		if(data.klu_code)
			$("#ms_klu_id").append(new Option(data.klu_description+' ('+data.klu_code+')', data.ms_klu_id, true, true)).trigger('change')

		if($("#register_kepemilikan_npwp").is(":checked")) {
			$("#register_kepemilikan_npwp").click();
		}

		if(data.wajibpajak_type == 'BADAN') {
			$(".register_kepemilikan_npwp_box, .register_individu_field").hide();
		} else {
			$(".register_kepemilikan_npwp_box, .register_individu_field").show();

			if(data.wajibpajak_npwp == '00.000.000.0-000.000') {
				$("#register_kepemilikan_npwp").click();
			}
			$("#register_kepemilikan_npwp").attr('disabled', true);
		}

		$("#wajibpajak_npwp").val(data.wajibpajak_npwp).attr('disabled', true);
		$("#wajibpajak_nik").val(data.wajibpajak_nik).attr('disabled', true);

		if(data.ms_regency_id)
			$("#wajibpajak_city").append(new Option(data.regency_name, data.ms_regency_id, true, true)).trigger('change')

		$("#modalCreateNPWP").modal("show");
	})

	$("#wajibpajak_anggota_tim").select2({
		// tags: true
		dropdownParent: $("#modalCreateNPWP"),
		delay: 500,
	});

	$("#wajibpajak_type").select2({
		dropdownParent: $("#modalCreateNPWP"),
		delay: 500,
	}).on('select2:select', function(e) {
		let data = e.params.data;
		console.log('data', data);
		if(data.id == 'INDIVIDU') {
			$(".register_kepemilikan_npwp_box, .register_individu_field").show();
			$(".register_individu_field .nik-input").attr('required', true);
			$("#register_kepemilikan_npwp").removeAttr('disabled');
		} else {
			if($("#register_kepemilikan_npwp").is(':checked') == true) {
				$("#register_kepemilikan_npwp").click();
			}
			$(".register_kepemilikan_npwp_box, .register_individu_field").hide();

			$(".register_individu_field .nik-input").removeAttr('required');
		}
	});

	$("#wajibpajak_city").select2({
		dropdownParent: $("#modalCreateNPWP"),
		delay: 500,
		ajax: {
			url: "{{route('master.kota.select')}}",
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
					item.id = item.regency_id;
					item.text = item.regency_name;
					item.data = {
						regency_id: item.regency_id,
						regency_name: item.regency_name,
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

	$("#ms_klu_id").select2({
		dropdownParent: $("#modalCreateNPWP"),
		delay: 500,
		ajax: {
			url: "{{route('master.klu.select')}}",
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
					item.id = item.klu_id;
					item.text = item.klu_description+' ('+item.klu_code+')';
					item.data = item;
					// console.log('item.kode', item)
					return item
				})
				return {
					results: items
				};
			},
		},
	});

	let formNpwp = $("#formNpwp").validate({
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
		rules: {
			wajibpajak_phone: {
				required: true,
				number: true,
				rangelength: [9, 14],
			},
		},
		submitHandler: function(form) {
			// console.log(form.method);
			// console.log(form.action);
			// console.log($(form).serialize());
			if(!subscriptionData) {
				Swal.fire({
					html: 'Silahkan pilih paket!',
					confirmButtonText: "Ok",
					showCancelButton: false,
					icon: 'error'
				})
				return false;
			}
			$(".spinner-box").css({'display': 'table'});
			$.ajax({
				method: form.method,
				url: form.action,
				data: $(form).serialize()+"&"+$.param({subscription_id: subscriptionData.subscription_id, _token: $("meta[name=csrf-token]").attr('content')}),
				error: function(error) {
					$(".spinner-box").hide();
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
					$(".spinner-box").hide();
					if(!response.success) {
						formNpwp.showErrors({
							email: response.message
						})
						return false;
					}
					// reset form
					resetForm('#formNpwp');

					toastr.success(response.message);
					$("#modalCreateNPWP").modal("hide");
					tblWajibPajak.draw();
					getWajibPajak();

					if(response.data && response.data.invoice_url) {
						window.open(response.data.invoice_url, '_self');
					}
				}
			})
		},
	})

	$(document).on("click", "#table-wajib-pajak .btn-bayartagihan", function(e) {
		e.preventDefault();
		let row = $(this).closest('tr');
		let data = tblWajibPajak.row(row).data();
		Swal.fire({
			html: 'Apakah anda ingin membayar tagihan NPWP <b>'+ data.wajibpajak_npwp +'</b>?',
			icon: 'question',
			preConfirm: () => {
				Swal.showLoading();
				// tblPengaturanTunjangan.row(row).remove();
				// return true;
				return fetch(`{{url('/user/npwp/bayartagihan/')}}/${data.wajibpajak_id}`, {
					method: 'POST',
					body: new URLSearchParams($.param({_token: $("meta[name=csrf-token]").attr('content')}))
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
					confirmButtonText: "Ok",
					type: 'error'
				})
				return false;
			}

			toastr.success(result.message);
			window.open(result.data.invoice_url, '_blank');
		});
	})

	$(document).on("click", "#table-wajib-pajak .btn-lanjutlangganan", function(e) {
		e.preventDefault();
		let row = $(this).closest('tr');
		let data = tblWajibPajak.row(row).data();
		Swal.fire({
			html: 'Apakah anda ingin melanjutkan berlangganan untuk NPWP <b>'+ data.wajibpajak_npwp +'</b>?',
			icon: 'question',
			preConfirm: () => {
				Swal.showLoading();
				// tblPengaturanTunjangan.row(row).remove();
				// return true;
				return fetch(`{{url('/user/npwp/lanjutlangganan/')}}/${data.wajibpajak_id}`, {
					method: 'POST',
					body: new URLSearchParams($.param({_token: $("meta[name=csrf-token]").attr('content')}))
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
					html: result.message,
					confirmButtonText: "Ok",
					type: 'error',
					showCancelButton: false
				})
				return false;
			}

			if(result.data.subscription_type == 'FREE') {
				toastr.success('Perpanjang Layanan Berhasil');
				tblWajibPajak.draw();
			} else {
				toastr.success(result.message);
				window.open(result.data.invoice_url, '_blank');
			}
		});
	})

	// set meta title
	setHtmlTitle('{{$title}}')
})
</script>
