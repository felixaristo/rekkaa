
<?php
    // $trad
?>

<head>
  <style>
   .fileDropZone {
		border: 2px dashed #ccc;
		padding: 20px;
		text-align: center;
		cursor: pointer;
	}

	.primaryLabel {
		font-size: 16px;
		font-weight: bold;
	}

	.secondaryLabel {
		color: #888;
	}

	/* Optional: Hide the default file input */
	.hiddenInput {
		display: none;
	}
  </style>
</head>

<div class="row">
    <div class="col-lg-12 order-0">
        <form id="formProfilLawanTransaksi" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" id="wajibpajaktradeexchange_id" name="wajibpajaktradeexchange_id" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_id}}">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bxs-category-alt"></i> Kategori
                    </h5>
                    <button type="submit" class="btn btn-sm btn-warning">Ubah</button>
                </div>
                <div class="card-body mt-1">
                    <div class="form-group row mb-4">
                        <div class="col-sm-2">
                            <div class="form-check" style="margin-right: 1rem;">
                                <label for="is_supplier" class="day-label" style="color: #697A8D;">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_supplier" name="is_supplier" {{ $wajibpajaktradeexchange->wajibpajaktradeexchange_is_supplier ? 'checked' : '' }}>
                                    Penyedia (Supplier)
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-check" style="margin-right: 1rem;">
                                <label for="is_customer" class="day-label" style="color: #697A8D;">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_customer" name="is_customer" {{ $wajibpajaktradeexchange->wajibpajaktradeexchange_is_customer ? 'checked' : '' }}>
                                    Pelanggan (Customer)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 order-0">
            <div class="card mb-4 form-lbl-dot">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-user"></i> Profil & Kontak
                    </h5>
                </div>
                <div class="card-body mt-1">
                    <div class="form-group row mb-3">
                        <div class="col-sm-12">
                            <label for="label_billing" style="color: orange;"><strong><em>Detail Lawan Transaksi</em></strong></label>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <label for="tradeexchange_enid" class="lbl-req mb-1">ID Lawan Transaksi</label>
                            <input type="text" required name="tradeexchange_enid" id="tradeexchange_enid" class="form-control" placeholder="Masukkan ID" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_enid}}">
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_fullname" class="lbl-req mb-1">Nama Lawan Transaksi</label>
                            <input type="text" required name="tradeexchange_fullname" id="tradeexchange_fullname" class="form-control" placeholder="Masukkan Nama Lengkap" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_fullname}}">
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_email" class="lbl-req mb-1">Email Lawan Transaksi</label>
                            <input type="text" required name="tradeexchange_email" id="tradeexchange_email" class="form-control" placeholder="Masukkan Email" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_email}}">
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_mainnumber" class="lbl-req mb-1">Nomor Tlp</label>
                            <input type="text" required name="tradeexchange_mainnumber" id="tradeexchange_mainnumber" class="form-control" placeholder="Masukkan Nomor Tlp" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_main_number}}">
                        </div>
                    </div>
                    <div class="form-group row mb-5">
                        <div class="col-sm-3">
                            <label for="tradeexchange_additionalnumber" class="mb-1">Nomor Tlp (Tambahan)</label>
                            <input type="text" name="tradeexchange_additionalnumber" id="tradeexchange_additionalnumber" class="form-control" placeholder="Masukkan Nomor Tlp" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_additional_number}}">
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_fax" class="mb-1">Fax</label>
                            <input type="text" name="tradeexchange_fax" id="tradeexchange_fax" class="form-control" placeholder="Masukkan Fax" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_fax}}">
                        </div>
                    </div>
                    <div class="form-group row mb-3 mt-4">
                        <div class="col-sm-12">
                            <label for="label_tradeexchange" style="color: orange;"><strong><em>Penanggung Jawab</em></strong></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="cols-sm-12" id="tradeexchange-pic-form">
                            <div class="form-group row mb-4">
                                <div class="col-sm-3">
                                    <label for="tradeexchange_main_pic_name" class="lbl-req mb-1">Nama</label>
                                    <input type="text" name="tradeexchange_main_pic_name" id="tradeexchange_main_pic_name" class="form-control" placeholder="Masukkan Nama" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_main_pic_name}}">
                                </div>
                                <div class="col-sm-3">
                                    <label for="tradeexchange_main_pic_number" class="lbl-req mb-1">Nomor Tlp</label>
                                    <input type="text" name="tradeexchange_main_pic_number" id="tradeexchange_main_pic_number" class="form-control" placeholder="Masukkan Nomor Tlp" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_main_pic_number}}">
                                </div>
                                <div class="col-sm-3">
                                    <label for="tradeexchange_main_pic_email" class="lbl-req mb-1">Email</label>
                                    <input type="text" name="tradeexchange_main_pic_email" id="tradeexchange_main_pic_email" class="form-control" placeholder="Masukkan Email" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_main_pic_email}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 order-0">
            <div class="card mb-4 form-lbl-dot">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-map"></i> Alamat Lawan Transaksi
                    </h5>
                </div>
                <div class="card-body mt-1">
                    <div class="form-group row mb-3">
                        <div class="col-sm-12">
                            <label for="label_billing" style="color: orange;"><strong><em>Alamat Tagihan</em></strong></label>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-12">
                            <label for="billing_address" class="lbl-req mb-1">Alamat Lengkap</label>
                            <input type="text" required name="billing_address" id="billing_address" class="form-control" placeholder="Masukkan Alamat Lengkap" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_billing_address}}">
                        </div>
                    </div>
                    <div class="form-group row mb-5">
                        <div class="col-sm-3">
                            <label for="billing_countries" class="lbl-req mb-1">Negara</label>
                            <select name="billing_countries" style="width: 100%;" id="billing_countries" class="form-control" data-placeholder="-:Pilih Negara:-">
                                @if($wajibpajaktradeexchange->billing_country)
                                    <option value="{{$wajibpajaktradeexchange->billing_country->country_id}}" selected>{{$wajibpajaktradeexchange->billing_country->country_name}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="billing_province" class="lbl-req mb-1">Provinsi</label>
                            <select name="billing_province" style="width: 100%;" id="billing_province" class="form-control" data-placeholder="-:Pilih Provinsi:-">
                                @if($wajibpajaktradeexchange->billing_province)
                                    <option value="{{$wajibpajaktradeexchange->billing_province->province_id}}" selected>{{$wajibpajaktradeexchange->billing_province->province_name}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="billing_city" class="lbl-req mb-1">Kota</label>
                            <select name="billing_city" style="width: 100%;" id="billing_city" class="form-control" data-placeholder="-:Pilih Kota:-">
                                @if($wajibpajaktradeexchange->billing_regency)
                                    <option value="{{$wajibpajaktradeexchange->billing_regency->regency_id}}" selected>{{$wajibpajaktradeexchange->billing_regency->regency_name}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="billing_postalcode" class="lbl-req mb-1">Kodepos</label>
                            <input type="text" required name="billing_postalcode" id="billing_postalcode" class="form-control" placeholder="Masukkan Kodepos" maxlength="6" pattern="\d*" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_billing_postal_code}}">
                        </div>
                    </div>
                    <div class="form-group row mb-2 mt-4">
						<div class="col-sm-12">
                            <label for="label_shipping" style="color: orange;"><strong><em>Alamat Pengiriman</em></strong></label>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-12">
                            <div class="form-check" style="margin-right: 1rem;">
                                <label for="is_same_address" class="day-label" style="color: #697A8D;">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_same_address" name="is_same_address" {{ $wajibpajaktradeexchange->wajibpajaktradeexchange_is_billing_shipping ? 'checked' : '' }}>
                                    Samakan dengan alamat pengiriman
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="cols-sm-12" id="shipping-form" @if($wajibpajaktradeexchange->wajibpajaktradeexchange_is_billing_shipping) style="display:none;" @endif>
                            <div class="form-group row mb-4">
                                <div class="col-sm-12">
                                    <label for="shipping_address" class="mb-1">Alamat Lengkap</label>
                                    <input type="text" name="shipping_address" id="shipping_address" class="form-control" placeholder="Masukkan Alamat Lengkap" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_shipping_address}}">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-sm-3">
                                    <label for="shipping_countries" class="lbl-req mb-1">Negara</label>
                                    <select name="shipping_countries" style="width: 100%;" id="shipping_countries" class="form-control" data-placeholder="-:Pilih Negara:-">
                                        @if($wajibpajaktradeexchange->shipping_country)
                                            <option value="{{$wajibpajaktradeexchange->shipping_country->country_id}}" selected>{{$wajibpajaktradeexchange->shipping_country->country_name}}</option>
                                        @endif
                                    </select>
                                    <!-- <input type="text" required name="shipping_countries" id="shipping_countries" class="form-control" placeholder="Masukkan Negara"> -->
                                </div>
                                <div class="col-sm-3">
                                    <label for="shipping_province" class="lbl-req mb-1">Provinsi</label>
                                    <select name="shipping_province" style="width: 100%;" id="shipping_province" class="form-control" data-placeholder="-:Pilih Provinsi:-">
                                        @if($wajibpajaktradeexchange->shipping_province)
                                            <option value="{{$wajibpajaktradeexchange->shipping_province->province_id}}" selected>{{$wajibpajaktradeexchange->shipping_province->province_name}}</option>
                                        @endif
                                    </select>
                                    <!-- <input type="text" required name="shipping_province" id="shipping_province" class="form-control" placeholder="Masukkan Provinsi"> -->
                                </div>
                                <div class="col-sm-3">
                                    <label for="shipping_city" class="lbl-req mb-1">Kota</label>
                                    <select name="shipping_city" style="width: 100%;" id="shipping_city" class="form-control" data-placeholder="-:Pilih Kota:-">
                                        @if($wajibpajaktradeexchange->shipping_regency)
                                            <option value="{{$wajibpajaktradeexchange->shipping_regency->regency_id}}" selected>{{$wajibpajaktradeexchange->shipping_regency->regency_name}}</option>
                                        @endif
                                    </select>
                                    <!-- <input type="text" required name="shipping_city" id="shipping_city" class="form-control" placeholder="Masukkan Kota"> -->
                                </div>
                                <div class="col-sm-3">
                                    <label for="shipping_postalcode" class="lbl-req mb-1">Kodepos</label>
                                    <input type="text" required name="shipping_postalcode" id="shipping_postalcode" class="form-control" placeholder="Masukkan Kodepos" maxlength="6" pattern="\d*" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_shipping_postal_code}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 order-0">
            <div class="card mb-4 form-lbl-dot">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-dollar"></i> Info Keuangan
                    </h5>
                </div>
                <div class="card-body mt-1">
                    <div class="form-group row mb-5">
                        <div class="col-sm-3">
                            <label for="tradeexchange_bank_name" class="lbl-req mb-1">Nama Bank</label>
                            <select name="tradeexchange_bank_name" style="width: 100%;" id="tradeexchange_bank_name" class="form-control" data-placeholder="-:Pilih Nama Bank:-">
                                @if($wajibpajaktradeexchange->bank)
                                    <option value="{{$wajibpajaktradeexchange->bank->bank_id}}" selected>{{$wajibpajaktradeexchange->bank->bank_name}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_bank_owner" class="lbl-req mb-1">Pemilik Bank</label>
                            <input type="text" required name="tradeexchange_bank_owner" id="tradeexchange_bank_owner" class="form-control" placeholder="Masukkan Nama Pemilik" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_bank_account}}" @if(!$wajibpajaktradeexchange->bank) disabled @endif>
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_bank_account" class="lbl-req mb-1">Nomor Rekening</label>
                            <input type="text" required name="tradeexchange_bank_account" id="tradeexchange_bank_account" class="form-control" placeholder="Masukkan Nomor Rekening" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_bank_number}}" @if(!$wajibpajaktradeexchange->bank) disabled @endif>
                        </div>
                        <div class="col-sm-3">
                            <label for="tradeexchange_npwp" class="lbl-req mb-1">Nomor NPWP</label>
                            <input type="text" required name="tradeexchange_npwp" id="tradeexchange_npwp" class="form-control" placeholder="Masukkan Nomor NPWP" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_npwp}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4 order-0">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-file"></i> Catatan & File
                    </h5>
                </div>
                <div class="card-body mt-1">
                    <div class="form-group row mb-4">
                        <div class="col-sm-12">
                            <label for="tradeexchange_note" class="mb-1">Catatan</label>
                            <textarea name="tradeexchange_note" id="tradeexchange_note" class="form-control" value="{{$wajibpajaktradeexchange->wajibpajaktradeexchange_note}}"></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-12">
                            <label for="tradeexchange_attachment" class="mb-1">File</label>
                            <div class="fileDropZone" role="button" tabindex="0" aria-label="Unggah File" automationid="file-drop-zone" fdprocessedid="mgpcmc">
                                <div>
                                    <div class="primaryLabel">Unggah File</div>
                                </div>
                                <div class="secondaryLabel">Maksimum ukuran file: 5 MB</div>
                                <input type="file" name="tradeexchange_attachment" id="tradeexchange_attachment" class="hiddenInput">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>


<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
    $(function() {
        // console.log('WajibPajakTradeExchange Data:', @json($wajibpajaktradeexchange));
        let currentMenuId = "{{request()->get('menu_id')}}";

    
        $("#formProfilLawanTransaksi")[0].reset(); // Reset the form fields

        $('#is_same_address').change(function() {
            if ($(this).is(':checked')) {
                // If the checkbox is checked, hide the shipping-form
                $('#shipping-form').slideUp();
            } else {
                // If the checkbox is unchecked, show the shipping-form
                $('#shipping-form').slideDown();
            }
        });

		$("#formProfilLawanTransaksi").submit(function(event) {
            event.preventDefault();
            $(".spinner-box").css({
                'display': 'table'
            });
            $(".error-message").remove();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            const formData = $(this).serializeArray();
            const jsonData = {}; // To store the JSON data

			
			jsonData["wajibpajaktradeexchange_is_billing_shipping"] = $("#is_same_address").prop("checked");
			jsonData["wajibpajaktradeexchange_is_supplier"] = $("#is_supplier").prop("checked");
			jsonData["wajibpajaktradeexchange_is_customer"] = $("#is_customer").prop("checked");
            
			jsonData["_token"] = csrfToken;

            formData.forEach(function(item) {
                if (item.name === "wajibpajaktradeexchange_id") {
					jsonData["wajibpajaktradeexchange_id"] = item.value;
				} else if (item.name === "tradeexchange_enid") {
					jsonData["wajibpajaktradeexchange_enid"] = item.value;
				} else if (item.name === "tradeexchange_fullname") {
					jsonData["wajibpajaktradeexchange_fullname"] = item.value;
				} else if (item.name === "tradeexchange_email") {
					jsonData["wajibpajaktradeexchange_email"] = item.value;
				} else if (item.name === "tradeexchange_mainnumber") {
					jsonData["wajibpajaktradeexchange_main_number"] = item.value;
				} else if (item.name === "tradeexchange_additionalnumber") {
					jsonData["wajibpajaktradeexchange_additional_number"] = item.value;
				} else if (item.name === "tradeexchange_fax") {
					jsonData["wajibpajaktradeexchange_fax"] = item.value;
				}  else if (item.name === "tradeexchange_main_pic_name") {
					jsonData["wajibpajaktradeexchange_main_pic_name"] = item.value;
				} else if (item.name === "tradeexchange_main_pic_number") {
					jsonData["wajibpajaktradeexchange_main_pic_number"] = item.value;
				} else if (item.name === "tradeexchange_main_pic_email") {
					jsonData["wajibpajaktradeexchange_main_pic_email"] = item.value;
				} else if (item.name === "billing_address") {
					jsonData["wajibpajaktradeexchange_billing_address"] = item.value;
				} else if (item.name === "billing_countries") {
					jsonData["wajibpajaktradeexchange_billing_countries"] = item.value;
				} else if (item.name === "billing_province") {
					jsonData["wajibpajaktradeexchange_billing_province"] = item.value;
				} else if (item.name === "billing_city") {
					jsonData["wajibpajaktradeexchange_billing_city"] = item.value;
				} else if (item.name === "billing_postalcode") {
					jsonData["wajibpajaktradeexchange_billing_postal_code"] = item.value;
				} else if (item.name === "tradeexchange_note") {
					jsonData["wajibpajaktradeexchange_note"] = item.value;
				} else if (item.name === "tradeexchange_bank_name") {
					jsonData["ms_bank_id"] = item.value;
				} else if (item.name === "tradeexchange_bank_owner") {
					jsonData["wajibpajaktradeexchange_bank_account"] = item.value;
				} else if (item.name === "tradeexchange_bank_account") {
					jsonData["wajibpajaktradeexchange_bank_number"] = item.value;
				} else if (item.name === "tradeexchange_npwp") {
					jsonData["wajibpajaktradeexchange_npwp"] = item.value;
				}
            });

			if(jsonData["wajibpajaktradeexchange_is_billing_shipping"] === false) {
				const shippingFormData = $(this).find('#shipping-form :input').serializeArray();

				shippingFormData.forEach(function (item) {
					// Adjust the keys as needed based on your server-side expectations
					if (item.name === "shipping_address") {
						jsonData["wajibpajaktradeexchange_shipping_address"] = item.value;
					} else if (item.name === "shipping_countries") {
						jsonData["wajibpajaktradeexchange_shipping_countries"] = item.value;
					} else if (item.name === "shipping_province") {
						jsonData["wajibpajaktradeexchange_shipping_province"] = item.value;
					} else if (item.name === "shipping_city") {
						jsonData["wajibpajaktradeexchange_shipping_city"] = item.value;
					} else if (item.name === "shipping_postalcode") {
						jsonData["wajibpajaktradeexchange_shipping_postal_code"] = item.value;
					}
				});
			} else {
				jsonData["wajibpajaktradeexchange_shipping_address"] = jsonData["wajibpajaktradeexchange_billing_address"];
				jsonData["wajibpajaktradeexchange_shipping_countries"] = jsonData["wajibpajaktradeexchange_billing_countries"];
				jsonData["wajibpajaktradeexchange_shipping_province"] = jsonData["wajibpajaktradeexchange_billing_province"];
				jsonData["wajibpajaktradeexchange_shipping_city"] = jsonData["wajibpajaktradeexchange_billing_city"];
				jsonData["wajibpajaktradeexchange_shipping_postal_code"] = jsonData["wajibpajaktradeexchange_billing_postal_code"];
			}

			let hasErrors = false;

			if(!jsonData["wajibpajaktradeexchange_enid"]) {
				appendError($("#tradeexchange_enid"), "Mohon isi ID.");
				hasErrors = true;
			}

			if(!jsonData["wajibpajaktradeexchange_fullname"]) {
				appendError($("#tradeexchange_fullname"), "Mohon isi Nama.");
				hasErrors = true;
			}

			if(!jsonData["wajibpajaktradeexchange_email"]) {
				appendError($("#tradeexchange_email"), "Mohon isi Email.");
				hasErrors = true;
			}

            if(!jsonData["wajibpajaktradeexchange_main_number"]) {
				appendError($("#tradeexchange_mainnumber"), "Mohon isi Nomor Tlp.");
				hasErrors = true;
			}

			if(!jsonData["wajibpajaktradeexchange_main_pic_name"]) {
				appendError($("#tradeexchange_main_pic_name"), "Mohon isi Nama.");
				hasErrors = true;
			}

			if(!jsonData["wajibpajaktradeexchange_main_pic_email"]) {
				appendError($("#tradeexchange_main_pic_email"), "Mohon isi Email.");
				hasErrors = true;
			}

            if(!jsonData["wajibpajaktradeexchange_main_pic_number"]) {
				appendError($("#tradeexchange_main_pic_number"), "Mohon isi Nomor Tlp.");
				hasErrors = true;
			}

            if (hasErrors) {
                $(".spinner-box").fadeOut();
                return false;
            }

            
            if(jsonData["wajibpajaktradeexchange_is_supplier"] === false && jsonData["wajibpajaktradeexchange_is_customer"] === false) {
                $(".error-message").remove();
                $(".spinner-box").fadeOut();
                Swal.fire({
                    html: 'Mohon pilih minimum 1 kategori!',
                    confirmButtonText: "Ok",
                    showCancelButton: false,
                    icon: 'error'
                })
                return false
            }

            if(!jsonData["wajibpajaktradeexchange_billing_address"] || !jsonData["wajibpajaktradeexchange_billing_countries"] || !jsonData["wajibpajaktradeexchange_billing_province"] || !jsonData["wajibpajaktradeexchange_billing_city"] || !jsonData["wajibpajaktradeexchange_billing_postal_code"]) {
                $(".error-message").remove();
                $(".spinner-box").fadeOut();
                Swal.fire({
                    html: 'Mohon lengkapi alamat tagihan!',
                    confirmButtonText: "Ok",
                    showCancelButton: false,
                    icon: 'error'
                })
                return false
            }

            if(!jsonData["ms_bank_id"] || !jsonData["wajibpajaktradeexchange_bank_account"] || !jsonData["wajibpajaktradeexchange_bank_number"] || !jsonData["wajibpajaktradeexchange_npwp"]) {
                $(".error-message").remove();
                $(".spinner-box").fadeOut();
                Swal.fire({
                    html: 'Mohon lengkapi info keuangan!',
                    confirmButtonText: "Ok",
                    showCancelButton: false,
                    icon: 'error'
                })
                return false
            }

            $.ajax({
                type: "POST",
                url: `{{ route('user.page.lawan-transaksi.update') }}?menu_id=${currentMenuId}`,
                data: JSON.stringify(jsonData),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    $(".spinner-box").fadeOut();
                    $(".error-message").remove();
                    if (response.success) {
                        toastr.success(response.message);

                        let href = `{{route('user.page.lawan-transaksi.index')}}?menu_id=${currentMenuId}`;
                        loadPage(href);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $(".spinner-box").fadeOut();

                    var response = JSON.parse(xhr.responseText);
                    if(response && response.message) {
                        var errorMessage = response.message;
                        toastr.error(errorMessage);
                    } else {
                        toastr.error("An error occurred while submitting the form.");
                    }
                    console.log(xhr.responseText);
                }
            });
        });

		function appendError($inputElement, errorMessage) {
			// Add the "error" class to the form-group container
			$inputElement.addClass("error");

			// Create the error message element
			$errorElement = $("<div>")
				.addClass("error-message")
				.addClass("error-text")
				.text(errorMessage);

			// Insert the error message after the form-group container
			$inputElement.after($errorElement);
		}

		$("input").focus(function() {
			$(this).removeClass("error");
			$(this).next(".error-message").remove();
		});

        $("#tradeexchange_bank_name").select2({
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
			$("#tradeexchange_bank_account").removeAttr('disabled');
			$("#tradeexchange_bank_owner").removeAttr('disabled');
			// $("#tradeexchange_bank_account").parent().prev('label').append('<span class="text-danger">*</span>');
		}).on("select2:unselect", function (e) {
			$("#tradeexchange_bank_account").val('').attr('disabled', 'disabled');
			$("#tradeexchange_bank_owner").val('').attr('disabled', 'disabled');
		});

        function formatResult(result) {
			if (result.loading) return result.text;
			return result.text;
		}

		function formatSelection(selection) {
			if (!selection.id) return selection.text;

			// Add padding and margin to the selected item text
			// var $clearButton = $('<span class="clear-button" onclick="clearSelection()">&times;</span>');
			// $clearButton.css({
			// 	'color': 'red', // Set the "x" font color to red
			// 	'margin-left': '10px', // Set margin to the left of the "x" button
			// 	'margin-right': '10px', // Set margin to the right of the "x" button
			// 	'padding-left': '5px', // Set padding to the left of the selected item text
			// 	'padding-right': '5px' // Set padding to the right of the selected item text
			// });

			// $clearButton.on('click', function() {
			// 	$("#tradeexchange_bank_name").val(null).trigger('change');
			// 	$("#tradeexchange_bank_account").val('').attr('disabled', 'disabled');
			// 	$("#tradeexchange_bank_owner").val('').attr('disabled', 'disabled');
			// });

			return $('<div>').append('<div style="display: inline-block;">' + selection.text + '</div>');
		}

        let select2ParamProvince = {
            delay: 500,
            ajax: {
                url: "{{route('master.provinsi.select')}}",
                data: function (params) {
                    var query = {
                        country_id: $("#billing_countries").val(),
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
                        item.id = item.province_id;
                        item.text = item.province_name;
                        item.data = {
                            province_id: item.province_id,
                            province_name: item.province_name,
                        };
                        // console.log('item.kode', item)
                        return item
                    })
                    return {
                        results: items
                    };
                },
            },
        };

        let select2ParamCity = {
            delay: 500,
            ajax: {
                url: "{{route('master.kota.select')}}",
                data: function (params) {
                    var query = {
                        country_id: $("#billing_countries").val(),
                        province_id: $("#billing_province").val(),
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
        };

        $("#billing_countries").select2({
            delay: 500,
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
                        item.data = {
                            country_id: item.country_id,
                            country_name: item.country_name,
                        };
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
            // $("#city_id").val(null).trigger('change');
            $("#billing_province").html('');

            $("#billing_province").select2('destroy');
            if(data.id == 100) { // Indonesia
                $("#billing_province").select2(select2ParamProvince);
            } else {
                $("#billing_province").select2({tags:true})
            }
        })

        $("#billing_province").select2(select2ParamProvince).on("select2:select", function(e) {
            let data = e.params.data;
            // $("#city_id").val(null).trigger('change');
            $("#billing_city").html('');

            $("#billing_city").select2('destroy');
            if(data.id) { // Indonesia
                $("#billing_city").select2(select2ParamCity);
            } else {
                $("#billing_city").select2({tags:true})
            }
        });

        $("#billing_city").select2(select2ParamCity)

        let select2ParamProvinceShipping = {
            delay: 500,
            ajax: {
                url: "{{route('master.provinsi.select')}}",
                data: function (params) {
                    var query = {
                        country_id: $("#shipping_countries").val(),
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
                        item.id = item.province_id;
                        item.text = item.province_name;
                        item.data = {
                            province_id: item.province_id,
                            province_name: item.province_name,
                        };
                        // console.log('item.kode', item)
                        return item
                    })
                    return {
                        results: items
                    };
                },
            },
        };

        let select2ParamCityShipping = {
            delay: 500,
            ajax: {
                url: "{{route('master.kota.select')}}",
                data: function (params) {
                    var query = {
                        country_id: $("#shipping_countries").val(),
                        province_id: $("#shipping_province").val(),
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
        };

        $("#shipping_countries").select2({
            delay: 500,
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
                        item.data = {
                            country_id: item.country_id,
                            country_name: item.country_name,
                        };
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
            // $("#city_id").val(null).trigger('change');
            $("#shipping_province").html('');

            $("#shipping_province").select2('destroy');
            if(data.id == 100) { // Indonesia
                $("#shipping_province").select2(select2ParamProvinceShipping);
            } else {
                $("#shipping_province").select2({tags:true})
            }
        })

        $("#shipping_province").select2(select2ParamProvinceShipping).on("select2:select", function(e) {
            let data = e.params.data;
            // $("#city_id").val(null).trigger('change');
            $("#shipping_city").html('');

            $("#shipping_city").select2('destroy');
            if(data.id) { // Indonesia
                $("#shipping_city").select2(select2ParamCityShipping);
            } else {
                $("#shipping_city").select2({tags:true})
            }
        });

        $("#shipping_city").select2(select2ParamCityShipping)
    });
</script>