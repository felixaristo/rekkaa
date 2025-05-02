<div class="pph21">
    <div class="row">
      <!-- Basic Layout -->
        <div class="col-md-6 offset-md-3">
            <h2 class="fw-bold py-3 mt-4 mb-4 text-center text-warning" style="text-decoration: underline;">
                Kalkulator Rekkaa
            </h2>
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">PPh 21 Non Karyawan</h5>
                    <small class="text-muted float-end">Kalkulator</small>
                </div>
                <div class="card-body">

                    <form id="form-cetak" class="row needs-validation" autocomplete="off" novalidate>
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
                            <label class="col-sm-4 col-form-label" for="ptkp"
                            >Status Kawin</label>
                            <div class="col-sm-8">
                                <select name="ptkp" required id="ptkp" style="width: 100%;" data-placeholder="-: Pilih Data :-"></select>
                                <div class="invalid-feedback">
                                    Input wajib diisi
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="sumberpenghasilan"
                            >Sumber Penghasilan</label>
                            <div class="col-sm-8">
                                <select name="sumberpenghasilan" required id="sumberpenghasilan" style="width: 100%;" data-placeholder="-: Pilih Data :-">
                                    <option value="">-: Pilih Data :-</option>
                                    <option value="2">Beberapa Pemberi Kerja</option>
                                    <option value="1">Satu Pemberi Kerja</option>
                                </select>
                                <div class="invalid-feedback">
                                    Input wajib diisi
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="tunjangan_pajak">Metode Perhitungan</label>
                            <div class="col-sm-8">
                                <div class="form-check mt-3 form-check-inline">
                                    <input name="tunjangan_pajak" checked class="form-check-input" type="radio" value="GROSS" id="tunjangan_pajak_ngu">
                                    <label class="form-check-label" for="tunjangan_pajak_ngu">Gross</label>
                                </div>
                                <div class="form-check mt-3 form-check-inline">
                                    <input name="tunjangan_pajak" class="form-check-input" type="radio" value="GROSS_UP" id="tunjangan_pajak_gu">
                                    <label class="form-check-label" for="tunjangan_pajak_gu">Gross Up</label>
                                </div>
                                <div class="invalid-feedback">
                                    Input wajib diisi
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="penghasilan_bruto"
                            >Penghasilan</label>
                            <div class="col-sm-8">
                                <input class="form-control penghasilan-input" value="0" required name="penghasilan_bruto" id="penghasilan_bruto" placeholder="Penghasilan Bruto">
                                <div class="invalid-feedback">
                                    Input wajib diisi
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="penghasilan_dpp"
                            >DPP 50% x Penghasilan</label>
                            <div class="col-sm-8">
                                <input class="form-control" value="0" disabled name="penghasilan_dpp" id="penghasilan_dpp" placeholder="Penghasilan Neto">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="menggunakan_ter">Gunakan Skema Tarif Efektif Rata-Rata (TER)</label>
                            <div class="col-sm-8">
                                <div class="form-check mt-3 form-check-inline">
                                    <input name="menggunakan_ter" checked class="form-check-input" type="radio" value="1" id="menggunakan_ter_y">
                                    <label class="form-check-label" for="menggunakan_ter_y">Ya</label>
                                </div>
                                <div class="form-check mt-3 form-check-inline">
                                    <input name="menggunakan_ter" class="form-check-input" type="radio" value="0" id="menggunakan_ter_t">
                                    <label class="form-check-label" for="menggunakan_ter_t">Tidak</label>
                                </div>
                                <div class="invalid-feedback">
                                    Input wajib diisi
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 box-ter">
                            <label class="col-sm-4 col-form-label" for="perhitungan_kategori_ter"
                            >Kategori TER</label>
                            <div class="col-sm-8">
                                <input class="form-control" value="" disabled name="perhitungan_kategori_ter" id="perhitungan_kategori_ter" placeholder="Kategori TER">
                            </div>
                        </div>
                        <div class="row mb-3 box-ter">
                            <label class="col-sm-4 col-form-label" for="perhitungan_tarif_ter"
                            >Tarif TER</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input class="form-control" value="0" disabled name="perhitungan_tarif_ter" id="perhitungan_tarif_ter" placeholder="Tarif TER">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="pph_terutang">PPh Terutang</label>
                            <div class="col-sm-8">
                                <input class="form-control" disabled name="pph_terutang" value="0" id="pph_terutang" placeholder="PPh Terutang">
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-sm-12 text-right">
                                @if(!session()->get('wajibpajak_current'))
                                <button type="submit" class="btn btn-warning btn-sm register-kalkulator"><i class='bx bxs-analyse'></i> Hitung PPH 21</button>
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
                    <button type="button" class="btn btn-outline-info btn-link btn-sm" data-type="send-email" data-kalkulator="pph-21" data-tipekalkulator="nonkaryawan"><i class='bx bx-envelope' ></i> Kirim via Email</button>
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
        let currentUrl = "<?php echo url('kalkulator/pph-21/non-karyawan/cetak?menu_id='.request()->get('menu_id').'&params='); ?>"
        let params;
        let tarif21 = JSON.parse('<?php echo json_encode($tarif21)  ?>');
        let tarif21Nonnpwp = JSON.parse('<?php echo json_encode($tarif21_nonnpwp) ?>');
        let ptkpDetail = JSON.parse('<?php echo json_encode($ptkp_detail) ?>');
        let filterTarif21;
        let ptkp;
        let optionAutoNumeric = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            currencySymbolPlacement: 'p',
            currencySymbol: 'Rp. ',
            minimumValue: 0,
            decimalPlaces: '0',
            modifyValueOnWheel: false,
        };
        let [penghasilanBruto, penghasilanDpp
        , perhitunganTotalPPHTerutang] = new AutoNumeric.multiple(
            ["#penghasilan_bruto", "#penghasilan_dpp"
            , "#pph_terutang"]
            , optionAutoNumeric
        );
		let kategori = null;
		$("#kepemilikan_npwp").select2().on("select2:select", function(e) {
			let data = e.params.data;
            <?php if(session()->get('wajibpajak_current')) : ?>
			perhitunganTotal();
            <?php endif; ?>
		});
        $("#ptkp").select2({
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
                // // // console.log('data.data', data.data)
                let items = data.data;
                items.map((item, idx) => {
                    item.id = item.ptkp_id;
                    item.text = item.ptkp_description;
                    item.data = {
                        ptkp_id: item.ptkp_id,
                        ptkp_description: item.ptkp_description,
                        ptkp_marriage_status: item.ptkp_marriage_status,
                        ptkp_category: item.ptkp_category,
                        ptkp_rate: parseInt(item.ptkp_rate),
                    };
                    // // // console.log('item.kode', item)
                    return item
                })
                return {
                    results: items
                };
            },
            },
        }).on("select2:select", function(e) {
            let data = e.params.data;
            let item = data.data;
            let ptkpdet = [];
            for(let i=0;i<ptkpDetail.length; i++) {
                if(item.ptkp_category == ptkpDetail[i].ptkpdet_category) {
                    ptkpdet.push(ptkpDetail[i]);
                }
            }
            item.ptkp_detail = ptkpdet;
            ptkp = item;
            console.log('ptkp', item);
            filterTarif21 = tarif21.filter(trf => {
                if(trf.tarif21_startincome <= item.ptkp_rate && trf.tarif21_endincome >= item.ptkp_rate) {
                    return trf;
                }
            })
            
            <?php if(session()->get('wajibpajak_current')) : ?>
            $("#perhitungan_kategori_ter").val(ptkp.ptkp_category);
			perhitunganTotal();
            <?php endif; ?>
        })
        $("#sumberpenghasilan").select2().on("select2:select", function(e) {
			let data = e.params.data;
            <?php if(session()->get('wajibpajak_current')) : ?>
			perhitunganTotal();
            <?php endif; ?>
		});
        
        $(".penghasilan-input").keyup(function(e) {
            e.preventDefault();
            let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
            // penghasilan
            let penghasilanDppTotal = 0;
            if(metodePajak == 'GROSS') {
                penghasilanDppTotal = penghasilanBruto.getNumber() * 50 / 100;
            }
        
            penghasilanDpp.set(Math.floor(penghasilanDppTotal));
            <?php if(session()->get('wajibpajak_current')) : ?>
            perhitunganTotal();
            <?php endif; ?>
        })

        $("input[name=tunjangan_pajak]").on("click", function(e) {
            $(".penghasilan-input").trigger('keyup');
        })
        
        $("input[name=menggunakan_ter]").on("click", function(e) {
            let metodeTER = $("input[name=menggunakan_ter]:checked").val();
            if(metodeTER == 1) {
                $(".box-ter").show();
            } else {
                $(".box-ter").hide();
            }

            $(".penghasilan-input").trigger('keyup');
        })
        // submit pdf
        $("#form-cetak").validate({
            rules: {
                // compound rule
                penghasilan_bruto: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
                let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
                let kepemilikanNpwp = $(`#kepemilikan_npwp`).val();
                let menggunakan_ter = $("input[name=menggunakan_ter]:checked").val();
                let sumberPenghasilan = $("#sumberpenghasilan").val();
                
                let params = `penghasilan_bruto=${penghasilanBruto.getNumber()}&metode=${metodePajak}&knpwp=${kepemilikanNpwp}
                &menggunakan_ter=${menggunakan_ter}
                &sumberpenghasilan=${sumberPenghasilan}
                &ptkpdetratepercentage=${ptkpCategoryRate}
                &ptkpdetratemonth=${ptkpCategoryRateMonth}
                &ptkp=${ptkp.ptkp_id}` ;
                // console.log('params', params)
                params = btoa(params);
                // let url = `{{url('kalkulator/pph-21/non-karyawan/cetak?params=')}}${params}`;
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
                history.pushState({},"",`?page-type=kalkulator-pph21-nonkaryawan&menu_id=<?php echo (request()->get('menu_id') ? request()->get('menu_id') : 27) ?>&params=${params}`);

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

        let ptkpCategoryRate = 0;
        let ptkpCategoryRateMonth = 0;
        function perhitunganTotal() {
            let npwpNilai = $(`#kepemilikan_npwp`).val();
            npwpNilai = (npwpNilai == 'NO-NPWP') ? tarif21Nonnpwp.tarifnonnpwp_rate : 100;
            let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
            let metodeTER = $("input[name=menggunakan_ter]:checked").val();
            let sumberPenghasilan = $("#sumberpenghasilan").val();
            let totalPKP = 0;
            totalPKP = (metodePajak == 'GROSS') ? penghasilanDpp.getNumber() : penghasilanBruto.getNumber();
            let tarif21Rates = getTarif21(tarif21, totalPKP);
            let totalBruto = penghasilanBruto.getNumber();
            totalPKP = (sumberPenghasilan == 1) ? totalPKP - (ptkp.ptkp_rate / 12) : totalPKP;
            
            // console.log('sumberPenghasilan', sumberPenghasilan);
            let totalPPHTerutang = 0;
            let rategrossnet = 0;
            if(metodePajak == 'GROSS') {
                tarif21Rates.forEach(rate => {
                    if(rate.tarif21_endincome > totalPKP) {
                        totalPPHTerutang += totalPKP * rate.tarif21_rate / 100;
                    } else {
                        totalPPHTerutang += rate.tarif21_endincome * rate.tarif21_rate / 100;
                        totalPKP -= rate.tarif21_endincome;
                    }    
                });
            } else { // GROSS UP / NETT
                // tarif21Rates.filter(rate => {
                //     if(rate.tarif21_endincome >= totalPKP) {
                //         rategrossnet = 100 - (50 * rate.tarif21_rate / 100);
                //         return rate
                //     } else {
                //         totalPKP -= rate.tarif21_endincome;
                //     }
                // });
                // totalPKP = totalBruto * 100 / rategrossnet; // pkp
                // console.log('rategrossnet', rategrossnet)
                // console.log('totalPKP', totalPKP)
                let rate = tarif21Rates.filter(rate => {
                    if(rate.tarif21_endincome >= totalPKP) {
                        rategrossnet = 100 - (50 * rate.tarif21_rate / 100);
                        return rate
                    } else {
                        totalPKP -= rate.tarif21_endincome;
                    }
                });
            
                totalPKP = totalBruto * 100 / rategrossnet; // pkp
                let totalDPP = totalPKP * 50/100;
                penghasilanDpp.set(totalDPP);
                totalPPHTerutang = totalPKP - totalBruto;
            }
            if(metodeTER == '1') {
                $(".box-ter").show();

                if(metodePajak == 'GROSS') {
                    totalBruto = penghasilanBruto.getNumber();
                } else {
                    totalBruto = totalPKP;
                }
                // console.log('metodeTER', metodeTER)
                if(ptkp && ptkp.ptkp_detail) {
                    let ptkpdet = ptkp.ptkp_detail;
                    for(let i=0; i<ptkpdet.length; i++) {
                        if(totalBruto < ptkpdet[i].ptkpdet_rate_month) {
                            ptkpCategoryRate = ptkpdet[i].ptkpdet_rate_percentage;
                            ptkpCategoryRateMonth = ptkpdet[i].ptkpdet_rate_month;
                            break;
                        }
                    }
                }
                // console.log('ptkpCategoryRate', ptkpCategoryRate);
                $("#perhitungan_tarif_ter").val(ptkpCategoryRate);
                if(metodePajak == 'GROSS') {
                    totalPKP = penghasilanBruto.getNumber();
                }
                // console.log('totalPKP', totalPKP)
                totalPPHTerutang = Math.floor((npwpNilai / 100) * totalPKP * 50/100 * ptkpCategoryRate / 100);
            } else {
                // console.log('totalPPHTerutangxxx', totalPPHTerutang)
                totalPPHTerutang = Math.floor((npwpNilai / 100) * totalPPHTerutang);
            }
            console.log('totalPPHTerutang', totalPPHTerutang)
            perhitunganTotalPPHTerutang.set(totalPPHTerutang);
        }

        // load cetak if has params
        let urlParams = findGetParameter('params');
        if(urlParams) {
            let decodeParams = atob(urlParams);
            ptkp = JSON.parse(findGetParameter('ptkpdata', decodeParams, false));
            let nilaiBruto = findGetParameter('penghasilan_bruto', decodeParams, false);
            let metode = findGetParameter('metode', decodeParams, false);
            let kepemilikanNpwp = findGetParameter('knpwp', decodeParams, false);
            let menggunakan_ter = findGetParameter('menggunakan_ter', decodeParams, false);
            let sumberPenghasilan = findGetParameter('sumberpenghasilan', decodeParams, false);
            let ptkpCategoryRate = findGetParameter('ptkpdetratepercentage', decodeParams, false);
            let ptkpCategoryRateMonth = findGetParameter('ptkpdetratemonth', decodeParams, false);
            // console.log('decodeParams', decodeParams)
            // console.log('nilaiBruto', nilaiBruto)
            // console.log('kepemilikanNpwp', kepemilikanNpwp)
            // console.log('menggunakan_ter', menggunakan_ter)
            // console.log('sumberPenghasilan', sumberPenghasilan)
            // console.log('ptkpCategoryRate', ptkpCategoryRate)
            // console.log('ptkpCategoryRateMonth', ptkpCategoryRateMonth)

            penghasilanBruto.set(nilaiBruto);
            // alert('kepemilikanNpwp'+kepemilikanNpwp);
            
            $(`#sumberpenghasilan`).val(sumberPenghasilan).trigger('change');
            // setTimeout(function(e) {
                console.log('ooooo', kepemilikanNpwp)
                $(`#kepemilikan_npwp`).val(kepemilikanNpwp).trigger('change');
                $(`[name=tunjangan_pajak][value=${metode}]`).click();
                $(`[name=menggunakan_ter][value=${menggunakan_ter}]`).click();
            // }, 2000)
            $(".penghasilan-input").keyup();
            loadCetakKalkulator(currentUrl, urlParams);
        }

        // set meta title
	    setHtmlTitle('{{$title}}')
    })
  </script>