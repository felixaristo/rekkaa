<div class="pph21">
    <div class="row">
      <!-- Basic Layout -->
      <div class="col-md-12">
        <h2 class="fw-bold py-3 mt-4 mb-4 text-center text-warning" style="text-decoration: underline;">
            Kalkulator Rekkaa
        </h2>
        <div class="card mb-4">
          <div
            class="card-header d-flex align-items-center justify-content-between"
          >
            <h5 class="mb-0">PPh 21 Karyawan</h5>
            <small class="text-muted float-end">Kalkulator</small>
          </div>
          <div class="card-body">

            <div id="smartwizard" dir="rtl-">
                <ul class="nav nav-progress">
                    <li class="nav-item">
                        <a class="nav-link" href="#step-1">
                            <span class="num">1</span>
                            Konfigurasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#step-2">
                            <span class="num">2</span>
                            Penghasilan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="#step-3">
                            <span class="num">3</span>
                            Pengurang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="#step-4">
                            <span class="num">4</span>
                            Penghitungan PPh Pasal 21
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                        <form id="form-1" class="row needs-validation" novalidate>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="kepemilikan_npwp"
                                >Kepemilikan NPWP</label>
                                <div class="col-sm-8">
                                    <select name="kepemilikan_npwp" required id="kepemilikan_npwp" style="width: 100%;" data-placeholder="-: Pilih Data :-">
                                        <option value="">-: Pilih Data :-</option>
                                        @foreach($kepemilikan_npwp as $kepemilikan)
                                        <option value="{{$kepemilikan->kepemilikannpwp_code}}" data-nilai="{{$kepemilikan->kepemilikannpwp_value}}">{{$kepemilikan->kepemilikannpwp_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
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
                                    <!-- <div class="form-check mt-3 form-check-inline">
                                        <input name="tunjangan_pajak" class="form-check-input" type="radio" value="NETT" id="tunjangan_pajak_net">
                                        <label class="form-check-label" for="tunjangan_pajak_net">Nett</label>
                                    </div> -->
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="menggunakan_bpjs_kes">Menggunakan BPJS Kesehatan</label>
                                <div class="col-sm-8">
                                    <div class="form-check mt-3 form-check-inline">
                                        <input name="menggunakan_bpjs_kes" checked class="form-check-input" type="radio" value="1" id="menggunakan_bpjs_kes_y">
                                        <label class="form-check-label" for="menggunakan_bpjs_kes_y">Ya</label>
                                    </div>
                                    <div class="form-check mt-3 form-check-inline">
                                        <input name="menggunakan_bpjs_kes" class="form-check-input" type="radio" value="0" id="menggunakan_bpjs_kes_t">
                                        <label class="form-check-label" for="menggunakan_bpjs_kes_t">Tidak</label>
                                    </div>
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
                                </div>
                                <label class="col-sm-4 col-form-label" for="menggunakan_bpjs_tk">Menggunakan BPJS Tenaga Kerja</label>
                                <div class="col-sm-8">
                                    <div class="form-check mt-3 form-check-inline">
                                        <input name="menggunakan_bpjs_tk" checked class="form-check-input" type="radio" value="1" id="menggunakan_bpjs_tk_y">
                                        <label class="form-check-label" for="menggunakan_bpjs_tk_y">Ya</label>
                                    </div>
                                    <div class="form-check mt-3 form-check-inline">
                                        <input name="menggunakan_bpjs_tk" class="form-check-input" type="radio" value="0" id="menggunakan_bpjs_tk_t">
                                        <label class="form-check-label" for="menggunakan_bpjs_tk_t">Tidak</label>
                                    </div>
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
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
                        </form>
                    </div>
                    <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                        <form id="step-2" class="row needs-validation" autocomplete="off" novalidate>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="penghasilan_gaji_pokok"
                                >Gaji Pokok</label>
                                <div class="col-sm-8">
                                    <input class="form-control penghasilan-input" value="0" required name="penghasilan_gaji_pokok" id="penghasilan_gaji_pokok" placeholder="Gaji Pokok">
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="penghasilan_tunjangan_lainnya"
                                >Tunjangan Lainnya, Uang Lembur, dan sebagainya</label>
                                <div class="col-sm-8">
                                    <input class="form-control penghasilan-input" value="0" required name="penghasilan_tunjangan_lainnya" id="penghasilan_tunjangan_lainnya" placeholder="Tunjangan Lainnya, Uang Lembur, dan sebagainya">
                                    <div class="invalid-feedback">
                                        Input wajib diisi
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3" id="penghasilan_jamkes_container">
                                <label class="col-sm-4 col-form-label penghasilan_jamkes_label" for="penghasilan_jamkes">Jaminan Kesehatan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="penghasilan_jamkes" id="penghasilan_jamkes" placeholder="JK">
                                    <span class="help-block text-warning" style="font-size: 11px!important;">*Dari Maksimal Rp. 12.000.000</span>
                                </div>
                            </div>
                            <div class="row mb-3" id="penghasilan_jkk_container">
                                <label class="col-sm-4 col-form-label penghasilan_jkk_label" for="penghasilan_jkk">Jaminan Kecelakaan Kerja <span class="rate_label"></span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <button class="btn btn-outline-primary jamkes-toggle dropdown-toggle" type="button" data-rate="0.24" data-bs-toggle="dropdown" aria-expanded="false">0.24 %</button>
                                        <ul class="dropdown-menu dropdown-menu-jamkes"></ul>
                                        <input class="form-control" value="0" disabled name="penghasilan_jkk" id="penghasilan_jkk" placeholder="Jaminan Kecelakaan Kerja">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3" id="penghasilan_jkm_container">
                                <label class="col-sm-4 col-form-label penghasilan_jkm_label" for="penghasilan_jkm">Jaminan Kematian</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="penghasilan_jkm" id="penghasilan_jkm" placeholder="Jaminan Kematian">
                                </div>
                            </div>
                            <div class="row mb-3" id="penghasilan_jht_container" style="display: none;">
                                <label class="col-sm-4 col-form-label penghasilan_jht_label" for="penghasilan_jht">Jaminan Hari Tua</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="penghasilan_jht" id="penghasilan_jht" placeholder="Jaminan Hari Tua">
                                </div>
                            </div>
                            <div class="row mb-3" id="penghasilan_jp_container" style="display: none;">
                                <label class="col-sm-4 col-form-label penghasilan_jp_label" for="penghasilan_jp">Jaminan Pensiun</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="penghasilan_jp" id="penghasilan_jp" placeholder="JP">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="penghasilan_bruto_perbulan">Penghasilan Bruto Perbulan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="penghasilan_bruto_perbulan" value="0" id="penghasilan_bruto_perbulan" placeholder="Penghasilan Bruto Perbulan">
                                </div>
                            </div>
                        </form>  
                    </div>
                    <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">

                        <form id="step-3" class="row needs-validation" autocomplete="off" novalidate>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="pengurangan_biaya_jabatan">Biaya Jabatan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="pengurangan_biaya_jabatan" id="pengurangan_biaya_jabatan" placeholder="Biaya Jabatan">
                                </div>
                            </div>
                            <div class="row mb-3" id="pengurangan_jamkes_container" style="display: none;">
                                <label class="col-sm-4 col-form-label pengurangan_jamkes_label" for="pengurangan_jamkes">Jaminan Kesehatan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="pengurangan_jamkes" id="pengurangan_jamkes" placeholder="Jaminan Kesehatan">
                                </div>
                            </div>
                            <div class="row mb-3" id="pengurangan_jht_container">
                                <label class="col-sm-4 col-form-label pengurangan_jht_label" for="pengurangan_jht">Jaminan Hari Tua</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="pengurangan_jht" id="pengurangan_jht" placeholder="Jaminan Hari Tua">
                                </div>
                            </div>
                            <div class="row mb-3" id="pengurangan_jp_container">
                                <label class="col-sm-4 col-form-label pengurangan_jp_label" for="pengurangan_jp">Jaminan Pensiun</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="pengurangan_jp" id="pengurangan_jp" placeholder="Jaminan Pensiun">
                                    <span class="help-block text-warning" style="font-size: 11px!important;">*Dari Maksimal Rp. 10.042.300</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="penghasilan_neto_perbulan">Penghasilan Neto Perbulan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" value="0" disabled name="penghasilan_neto_perbulan" id="penghasilan_neto_perbulan" placeholder="Penghasilan Neto Perbulan">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">

                        <form id="step-4" class="row needs-validation" novalidate autocomplete="off">
                            <div class="row mb-3 mt-3 box-terbulan">
                                <h5>PPh Terutang Tarif Efektif Rata-rata (TER) Masa Januari - November</h5>
                            </div>
                            <div class="row mb-3 box-terbulan">
                                <label class="col-sm-4 col-form-label" for="ter_bruto_perbulan">Total Bruto</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="ter_bruto_perbulan" id="ter_bruto_perbulan" placeholder="Total Bruto">
                                </div>
                            </div>
                            <div class="row mb-3 box-terbulan">
                                <label class="col-sm-4 col-form-label" for="perhitungan_kategori_ter">Kategori TER</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_kategori_ter" id="perhitungan_kategori_ter" placeholder="Kategori TER">
                                </div>
                            </div>
                            <div class="row mb-3 box-terbulan">
                                <label class="col-sm-4 col-form-label" for="perhitungan_tarif_ter">Tarif TER</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input class="form-control" disabled name="perhitungan_tarif_ter" id="perhitungan_tarif_ter" placeholder="Tarif TER">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 box-terbulan">
                                <label class="col-sm-4 col-form-label" for="perhitungan_total_pph_terutang_perbulan_11">PPh 21 Terutang</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_pph_terutang_perbulan_11" id="perhitungan_total_pph_terutang_perbulan_11" placeholder="PPh 21 Terutang">
                                </div>
                            </div>
                            <div class="row mb-3 mt-3 title-terdesember">
                                <h5>PPh Terutang Masa Pajak Desember</h5>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="perhitungan_total_neto_setahun">Penghasilan Neto Setahun</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_neto_setahun" id="perhitungan_total_neto_setahun" placeholder="Penghasilan Neto Setahun">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="perhitungan_total_ptkp">Penghasilan Tidak Kena Pajak (PTKP)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_ptkp" id="perhitungan_total_ptkp" placeholder="Penghasilan Tidak Kena Pajak">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="perhitungan_total_pkp">Penghasilan Kena Pajak (PKP)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_pkp" id="perhitungan_total_pkp" placeholder="Penghasilan Kena Pajak">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label" for="perhitungan_total_pph_terutang_setahun">PPh Terutang Setahun</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_pph_terutang_setahun" id="perhitungan_total_pph_terutang_setahun" placeholder="PPh Terutang Setahun">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label label-terdesember" for="perhitungan_total_pph_terutang_perbulan">PPh Terutang</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled name="perhitungan_total_pph_terutang_perbulan" id="perhitungan_total_pph_terutang_perbulan" placeholder="PPh Terutang">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
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
								<button type="button" class="btn btn-outline-info btn-link btn-sm" data-type="send-email" data-kalkulator="pph-21"><i class='bx bx-envelope' ></i> Kirim via Email</button>
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
        
		let currentUrl = "<?php echo url('kalkulator/pph-21/cetak?menu_id='.request()->get('menu_id').'&params='); ?>"
        let params;
        let tarif21 = JSON.parse('<?php echo json_encode($tarif21)  ?>');
        let bpjsRate = JSON.parse('<?php echo json_encode($bpjs_rate)  ?>');
        let bpjsSetting = JSON.parse('<?php echo json_encode($setting_bpjs)  ?>');
        let tarif21Nonnpwp = JSON.parse('<?php echo json_encode($tarif21_nonnpwp) ?>');
        let ptkpDetail = JSON.parse('<?php echo json_encode($ptkp_detail) ?>');
        let filterTarif21;
        let ptkp;
        let maxBpjsKes;
        let maxBpjsTk;
        let tunjanganJabatan = JSON.parse('<?php echo json_encode($tunjangan_jabatan) ?>')
        for (const setting of bpjsSetting) {
            if (setting.setting_key === 'BPJS_KES_MAX_AMOUNT') {
                maxBpjsKes = parseInt(setting.setting_value)
            } else {
                let vals = setting.setting_value;
                maxBpjsTk = vals[0].value;
            }
        }

        $("#kepemilikan_npwp").select2().on("select2:select", function(e) {
            if(ptkp) {
                $(".penghasilan-input").trigger('keyup');
            }
        })
        $("input[name=tunjangan_pajak]").on("click", function(e) {
            if(ptkp) {
                $(".penghasilan-input").trigger('keyup');
            }
        })
        $("input[name=menggunakan_ter]").on("click", function(e) {
            let metodeTER = $("input[name=menggunakan_ter]:checked").val();
            if(metodeTER == 1) {
                $(".label-terdesember").text('PPh Terutang');
                $(".title-terdesember, .box-terbulan").show();
            } else {
                $(".label-terdesember").text('PPh Terutang Perbulan');
                $(".title-terdesember, .box-terbulan").hide();
            }
            
            if(ptkp) {
                $(".penghasilan-input").trigger('keyup');
            }
        })

        $("input[name=menggunakan_bpjs_kes]").on("click", function(e) {
            let val = $(this).val();
            if(val == '1') { // ya
                $('#penghasilan_jamkes_container').show();
                bpjsRateJamKes = getBpjsRate(bpjsRate, 'JamKes');
                bpjsRateJamKesMin = getBpjsRate(bpjsRate, 'JamKesMin');

                // bpjsRateJKK = $(".jamkes-toggle.dropdown-toggle").attr("data-rate");
                // bpjsRateJKM = getBpjsRate(bpjsRate, 'JKM');
                // bpjsRateJHT = getBpjsRate(bpjsRate, 'JHT');
                // bpjsRateJHTMin = getBpjsRate(bpjsRate, 'JHTMin');
                // bpjsRateJP = getBpjsRate(bpjsRate, 'JP');
                // bpjsRateJPMin = getBpjsRate(bpjsRate, 'JPMin');
            } else {
                $(".jamkes-toggle.dropdown-toggle").html("0.24 %")   
                $('#penghasilan_jamkes_container').hide();
                penghasilanJamKes.set(0)
                // penghasilanJKK.set(0) 
                // penghasilanJKM.set(0) 
                // penghasilanJHT.set(0)
                // penghasilanJP.set(0)
                penguranganJamKes.set(0)
                // penguranganJHT.set(0)
                // penguranganJP.set(0)

                bpjsRateJamKes = {bpjsrate_rate: 0};
                bpjsRateJamKesMin = {bpjsrate_rate: 0};
                // bpjsRateJKK = 0;
                // bpjsRateJKM = {bpjsrate_rate: 0};
                // bpjsRateJHT = {bpjsrate_rate: 0};
                // bpjsRateJHTMin = {bpjsrate_rate: 0};
                // bpjsRateJP = {bpjsrate_rate: 0};
                // bpjsRateJPMin = {bpjsrate_rate: 0};
            }
            $(".penghasilan-input").trigger('keyup');
        })

         $("input[name=menggunakan_bpjs_tk]").on("click", function(e) {
            let val = $(this).val();
            if(val == '1') { // ya
                $('#penghasilan_jkk_container, #penghasilan_jkm_container, #pengurangan_jht_container, #pengurangan_jp_container').show();
                // bpjsRateJamKes = getBpjsRate(bpjsRate, 'JamKes');
                // bpjsRateJamKesMin = getBpjsRate(bpjsRate, 'JamKesMin');
                bpjsRateJKK = $(".jamkes-toggle.dropdown-toggle").attr("data-rate");
                bpjsRateJKM = getBpjsRate(bpjsRate, 'JKM');
                bpjsRateJHT = getBpjsRate(bpjsRate, 'JHT');
                bpjsRateJHTMin = getBpjsRate(bpjsRate, 'JHTMin');
                bpjsRateJP = getBpjsRate(bpjsRate, 'JP');
                bpjsRateJPMin = getBpjsRate(bpjsRate, 'JPMin');
            } else {
                $(".jamkes-toggle.dropdown-toggle").html("0.24 %")   
                $('#penghasilan_jkk_container, #penghasilan_jkm_container, #pengurangan_jht_container, #pengurangan_jp_container').hide();
                // penghasilanJamKes.set(0)
                penghasilanJKK.set(0) 
                penghasilanJKM.set(0) 
                penghasilanJHT.set(0)
                penghasilanJP.set(0)
                // penguranganJamKes.set(0)
                penguranganJHT.set(0)
                penguranganJP.set(0)

                // bpjsRateJamKes = {bpjsrate_rate: 0};
                // bpjsRateJamKesMin = {bpjsrate_rate: 0};
                bpjsRateJKK = 0;
                bpjsRateJKM = {bpjsrate_rate: 0};
                bpjsRateJHT = {bpjsrate_rate: 0};
                bpjsRateJHTMin = {bpjsrate_rate: 0};
                bpjsRateJP = {bpjsrate_rate: 0};
                bpjsRateJPMin = {bpjsrate_rate: 0};
            }
            $(".penghasilan-input").trigger('keyup');
        })

        $(document).on("click", "ul.dropdown-menu-jamkes a.dropdown-item", function(e) {
            e.preventDefault();

            let rate = $(this).attr("data-rate");
            let isbpjstk = $('[name=menggunakan_bpjs_tk]:checked').val();
            $(".jamkes-toggle").attr("data-rate", rate).html(rate);
            $(".penghasilan_jkk_label .rate_label").html(`(${rate} %)`);
            
            bpjsRateJKK = 0;
            let penghasilanJKKTotal = 0;
            if(isbpjstk == "1") {
                penghasilanJKKTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJKK) / 100;
                bpjsRateJKK = rate;
            }
            penghasilanJKK.set(penghasilanJKKTotal);
            $(".penghasilan-input").trigger('keyup');
        })

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
            filterTarif21 = tarif21.filter(trf => {
                if(trf.tarif21_startincome <= item.ptkp_rate && trf.tarif21_endincome >= item.ptkp_rate) {
                    return trf;
                }
            })
            
            <?php if(session()->get('wajibpajak_current')) : ?>
                
            $("#perhitungan_kategori_ter").val(item.ptkp_category);
			perhitunganTotal();
            <?php endif; ?>
        })
        
        let optionAutoNumeric = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            currencySymbolPlacement: 'p',
            currencySymbol: 'Rp. ',
            // minimumValue: 0,
            decimalPlaces: '0',
            modifyValueOnWheel: false,
        };
        let [penghasilanGajiPokok, penghasilanTunjanganLainnya
        , penghasilanBrutoPerbulan
        , penghasilanJamKes, penghasilanJKK, penghasilanJKM, penghasilanJHT, penghasilanJP
        , penguranganBiayaJabatan, penguranganJamKes, penguranganJHT, penguranganJP, penghasilanNetoPerbulan
        , perhitunganPPH21Perbulan11
        , perhitunganBrutoPerbulan11
        , perhitunganTotalNetoSetahun, perhitunganTotalPTKP, perhitunganTotalPKP, perhitunganTotalPPHTerutangSetahun, perhitunganTotalPPHTerutangPerbulan] = new AutoNumeric.multiple(
            ["#penghasilan_gaji_pokok", "#penghasilan_tunjangan_lainnya"
            , "#penghasilan_bruto_perbulan"
            , "#penghasilan_jamkes", "#penghasilan_jkk", "#penghasilan_jkm", "#penghasilan_jht", "#penghasilan_jp"
            , "#pengurangan_biaya_jabatan", "#pengurangan_jamkes", "#pengurangan_jht", "#pengurangan_jp", "#penghasilan_neto_perbulan"
            , "#perhitungan_total_pph_terutang_perbulan_11",
            , "#ter_bruto_perbulan",
            , "#perhitungan_total_neto_setahun", "#perhitungan_total_ptkp", "#perhitungan_total_pkp", "#perhitungan_total_pph_terutang_setahun", "#perhitungan_total_pph_terutang_perbulan"]
            , optionAutoNumeric
        );
        $(".penghasilan-input").keyup(function(e) {
            e.preventDefault();
            let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
            
            // penghasilan
            let penghasilanJamKesTotal;
            
            if(maxBpjsKes < penghasilanGajiPokok.getNumber()) {
                penghasilanJamKesTotal = maxBpjsKes * parseFloat(bpjsRateJamKes.bpjsrate_rate) / 100;
            } else {
                penghasilanJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKes.bpjsrate_rate) / 100;
            }

            // let penghasilanJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKes.bpjsrate_rate) / 100;
            let penghasilanJKKTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJKK) / 100;
            let penghasilanJKMTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJKM.bpjsrate_rate) / 100;
            // let penghasilanJHTTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJHT.bpjsrate_rate) / 100;
            let penghasilanJHTTotal = 0;
            // let penghasilanJPTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJP.bpjsrate_rate) / 100;
            let penghasilanJPTotal = 0;
        
            penghasilanJamKes.set(penghasilanJamKesTotal);
            penghasilanJKK.set(penghasilanJKKTotal);
            penghasilanJKM.set(penghasilanJKMTotal);
            penghasilanJHT.set(penghasilanJHTTotal);
            penghasilanJP.set(penghasilanJPTotal);
            
            // pengurangan
            // let penguranganJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKesMin.bpjsrate_rate) / 100;
            let penguranganJamKesTotal = 0;
            let penguranganJHTTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJHTMin.bpjsrate_rate) / 100;
            // let penguranganJPTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJPMin.bpjsrate_rate) / 100;

            let penguranganJPTotal;
            
            if(maxBpjsTk < penghasilanGajiPokok.getNumber()) {
                penguranganJPTotal = maxBpjsTk * parseFloat(bpjsRateJPMin.bpjsrate_rate) / 100;
            } else {
                penguranganJPTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJPMin.bpjsrate_rate) / 100;
            }

            penguranganJamKes.set(penguranganJamKesTotal);
            penguranganJHT.set(penguranganJHTTotal);
            penguranganJP.set(penguranganJPTotal);
            
            if(metodePajak == 'NETT') {
                penghasilanBrutoPerbulan.set(
                    penghasilanGajiPokok.getNumber()
                )
            } else {
                penghasilanBrutoPerbulan.set(
                    penghasilanGajiPokok.getNumber()
                    + penghasilanTunjanganLainnya.getNumber() 
                    + penghasilanJamKes.getNumber()
                    + penghasilanJKK.getNumber() 
                    + penghasilanJKM.getNumber()
                    + penghasilanJHT.getNumber()
                    + penghasilanJP.getNumber()
                );
            }

            let tjabatan = penghasilanBrutoPerbulan.getNumber() * tunjanganJabatan.tunjanganjabatan_rate / 100;
            if(tjabatan > tunjanganJabatan.tunjanganjabatan_maximum_allowance) {
                tjabatan = tunjanganJabatan.tunjanganjabatan_maximum_allowance;
            }
            penguranganBiayaJabatan.set(tjabatan);

            let netoPerbulanTotal = 0;
            if(metodePajak == 'NETT') {
                netoPerbulanTotal = penghasilanBrutoPerbulan.getNumber() - tjabatan;
            } else {
                netoPerbulanTotal = penghasilanBrutoPerbulan.getNumber() - tjabatan - penguranganJamKes.getNumber() - penguranganJHT.getNumber() - penguranganJP.getNumber();
            }
            
            if(netoPerbulanTotal < 0) {
                netoPerbulanTotal = 0;
            }
            penghasilanNetoPerbulan.set(netoPerbulanTotal)

            <?php if(session()->get('wajibpajak_current')) : ?>
            perhitunganTotal();
            <?php endif; ?>
        })

        // $("#smartwizard").smartWizard('goToStep', 1);
        // $('#smartwizard').smartWizard("reset")
        // Leave step event is used for validating the forms
        $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx, stepDirection) {
            // Validate only on forward movement  
            if (stepDirection == 'forward') {
                let form = document.getElementById('form-' + (currentStepIdx + 1));
                if (form) {
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        $('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
                        $("#smartwizard").smartWizard('fixHeight');
                        return false;
                    }
                    $('#smartwizard').smartWizard("unsetState", [currentStepIdx], 'error');
                }
            }
        });

        // Step show event
        $("#smartwizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
            $("#smartwizard .btn-prev").removeClass('disabled').prop('disabled', false);
            $("#smartwizard .btn-next").removeClass('disabled').prop('disabled', false);
            if(stepPosition === 'first') {
                $("#smartwizard .btn-prev").addClass('disabled').prop('disabled', true);
            } else if(stepPosition === 'last') {
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

                <?php if(!session()->get('wajibpajak_current')) : ?>
                    // scroll buttom
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $(document).height() + 3000
                        }, 'fast');
                    }, 500)
                <?php endif; ?>
            } else {
                $("#btnFinish").prop('disabled', true);
                $("#btnFinish").hide();
            }

            // Focus first name
            if (stepIndex == 1) {
                setTimeout(() => {
                $('#first-name').focus();
                }, 0);
            }
        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: 0,
            // autoAdjustHeight: false,
            theme: 'arrows', // basic, arrows, square, round, dots
            transition: {
                animation:'none'
            },
            toolbar: {
                showNextButton: false, // show/hide a Next button
                showPreviousButton: false, // show/hide a Previous button
                position: 'bottom', // none/ top/ both bottom
                extraHtml: `
                <button class="btn btn-outline-danger btn-prev mr-1 btn-sm" type="button">&laquo; Sebelumnya</button>
                <button class="btn btn-outline-info btn-next mr-1 btn-sm" type="button">Selanjutnya &raquo;</button>
                <?php if(!session()->get('wajibpajak_current')) : ?>
                <a class="btn btn-warning btn-sm register-kalkulator" id="btnFinish" style="display:none;" href="#" target="_blank"><i class='bx bxs-analyse'></i> Hitung PPH 21</a>
                <?php else: ?>
                <a class="btn btn-warning btn-sm" id="btnFinish" style="display:none;" href="${currentUrl+params}" target="_blank"><i class='bx bxs-file'></i> Bagikan</a>
                <?php endif; ?>
                `
            },
            anchor: {
                enableNavigation: false, // Enable/Disable anchor navigation 
                enableNavigationAlways: false, // Activates all anchors clickable always
                enableDoneState: true, // Add done state on visited steps
                markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
                enableDoneStateNavigation: true // Enable/Disable the done state navigation
            },
            style: {
                btnCss: 'btn-sm',
                btnPrevCss: 'btn-outline-danger btn-prev mr-1',
                btnNextCss: 'btn-outline-info btn-next mr-1'
            },
            lang: {
                previous: 'Sebelumnya',
                next: 'Selanjutnya',
            },
            keyboard: {
                keyNavigation: false,
            }
        });
        $('#smartwizard').smartWizard("reset");

        $("#state_selector").on("change", function() {
            $('#smartwizard').smartWizard("setState", [$('#step_to_style').val()], $(this).val(), !$('#is_reset').prop("checked"));
            return true;
        });

        $("#style_selector").on("change", function() {
            $('#smartwizard').smartWizard("setStyle", [$('#step_to_style').val()], $(this).val(), !$('#is_reset').prop("checked"));
            return true;
        });

        let ptkpCategoryRate = 0;
        let ptkpCategoryRateMonth = 0;
        function perhitunganTotal() {
            let metodeTER = $("input[name=menggunakan_ter]:checked").val();
            let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
            let kepemilikanNpwp = $("#kepemilikan_npwp").val();

            perhitunganTotalNetoSetahun.set(
                penghasilanNetoPerbulan.getNumber()
                * 12
            );
            let ptkpRate = (ptkp) ? parseInt(ptkp.ptkp_rate) : 0;
            let ptkpCategory = (ptkp) ? ptkp.ptkp_category : '';
            perhitunganTotalPTKP.set(ptkpRate);
            perhitunganTotalPKP.set(
                perhitunganTotalNetoSetahun.getNumber()
                - ptkpRate
            )
            let totalPPHTerutangSetahun = perhitunganTarifPPH21(tarif21, metodePajak, kepemilikanNpwp, perhitunganTotalPKP.getNumber(), tarif21Nonnpwp);
            perhitunganTotalPPHTerutangSetahun.set(totalPPHTerutangSetahun);
            
            if(metodeTER == '1') {
                let brutoPerbulan11 = penghasilanBrutoPerbulan.getNumber();
                let brutoPertahun = penghasilanBrutoPerbulan.getNumber() * 12;
                if(ptkp && ptkp.ptkp_detail) {
                    let ptkpdet = ptkp.ptkp_detail;
                    for(let i=0; i<ptkpdet.length; i++) {
                        if(brutoPerbulan11 < ptkpdet[i].ptkpdet_rate_month) {
                            ptkpCategoryRate = ptkpdet[i].ptkpdet_rate_percentage;
                            ptkpCategoryRateMonth = ptkpdet[i].ptkpdet_rate_month;
                            break;
                        }
                    }
                }
                
                let pph21Perbulan11 = 0;
                
                let rateNonNPWP = kepemilikanNpwp == "NO-NPWP" ? tarif21Nonnpwp.tarifnonnpwp_rate : 100;
                if(metodePajak == 'GROSS') {
                    pph21Perbulan11 = Math.floor(brutoPerbulan11 * ptkpCategoryRate / 100);
                } else {
                    pph21Perbulan11GrossUp = Math.floor(brutoPerbulan11 * ptkpCategoryRate / (100 - ptkpCategoryRate));
                    brutoPerbulan11GrossUp = brutoPerbulan11+pph21Perbulan11GrossUp;
                    let ptkpCategoryRateGrossUp = 0;
                    let ptkpCategoryRateMonthGrossUp = 0;
                    if(ptkp && ptkp.ptkp_detail) {
                        let ptkpdet = ptkp.ptkp_detail;
                        for(let i=0; i<ptkpdet.length; i++) {
                            if(brutoPerbulan11GrossUp < ptkpdet[i].ptkpdet_rate_month) {
                                ptkpCategoryRateGrossUp = ptkpdet[i].ptkpdet_rate_percentage;
                                ptkpCategoryRateMonthGrossUp = ptkpdet[i].ptkpdet_rate_month;
                                break;
                            }
                        }
                    }

                    if(ptkpCategoryRate != ptkpCategoryRateGrossUp) {
                        ptkpCategoryRate = ptkpCategoryRateGrossUp;
                        ptkpCategoryRateMonth = ptkpCategoryRateMonthGrossUp;
                    }
                    pph21Perbulan11 = Math.floor(brutoPerbulan11 * (ptkpCategoryRate / (100 - ptkpCategoryRate)));
                }
                
                pph21Perbulan11 = (pph21Perbulan11 > 0) ? Math.floor(pph21Perbulan11 * rateNonNPWP / 100) : 0;
                $("#perhitungan_tarif_ter").val(ptkpCategoryRate);
                if(metodePajak != 'GROSS') {
                    brutoPerbulan11 += pph21Perbulan11; 
                }
                perhitunganBrutoPerbulan11.set(brutoPerbulan11);
                perhitunganPPH21Perbulan11.set(pph21Perbulan11);
                let pengurangPertahun = (penguranganBiayaJabatan.getNumber() * 12) + (penguranganJamKes.getNumber() * 12) + (penguranganJHT.getNumber() * 12) + (penguranganJP.getNumber() * 12);
                let netoSetahun = brutoPertahun - pengurangPertahun;
                perhitunganTotalNetoSetahun.set(netoSetahun);
                let pkpTotal = netoSetahun - ptkpRate;
                let totalTarif21 = perhitunganTarifPPH21TER(tarif21, metodePajak, kepemilikanNpwp, pkpTotal, tarif21Nonnpwp);

                let totalPPhTerutangDesember = totalTarif21 - (pph21Perbulan11*11);
                let totalPPHTerutangSetahun = totalPPhTerutangDesember + (pph21Perbulan11 * 11);
                totalPPhTerutangDesember = (totalPPhTerutangDesember > 0) ? totalPPhTerutangDesember : 0;
                perhitunganTotalPPHTerutangPerbulan.set(totalPPhTerutangDesember);
                perhitunganTotalPPHTerutangSetahun.set(totalPPHTerutangSetahun);
            } else {
                totalPPHTerutangSetahun = (totalPPHTerutangSetahun > 0) ? totalPPHTerutangSetahun : 0;
                perhitunganTotalPPHTerutangPerbulan.set(Math.floor(totalPPHTerutangSetahun/12));
            }
            
        }

        let bpjsRateJamKes = getBpjsRate(bpjsRate, 'JamKes');
        let bpjsRateJamKesMin = getBpjsRate(bpjsRate, 'JamKesMin');
        let bpjsRateJKK = $(".jamkes-toggle.dropdown-toggle").attr("data-rate");
        let bpjsRateJKM = getBpjsRate(bpjsRate, 'JKM');
        let bpjsRateJHT = getBpjsRate(bpjsRate, 'JHT');
        let bpjsRateJHTMin = getBpjsRate(bpjsRate, 'JHTMin');
        let bpjsRateJP = getBpjsRate(bpjsRate, 'JP');
        let bpjsRateJPMin = getBpjsRate(bpjsRate, 'JPMin');

        setLabelBjsRate(bpjsRate);

        $("#btnFinish").click(function(e) {
            e.preventDefault();

            let knpwp = $("#kepemilikan_npwp").val();
            let knpwptext = $("#kepemilikan_npwp").text();
            let metode = $('[name=tunjangan_pajak]:checked').val();
            let menggunakan_ter = $('[name=menggunakan_ter]:checked').val();
            let isbpjskes = $('[name=menggunakan_bpjs_kes]:checked').val();
            let isbpjstk = $('[name=menggunakan_bpjs_tk]:checked').val();
            
            let ptkpselected = $('#ptkp').val();
            let ptkptext = $('#ptkp').text();
            let penghasilan_gaji_pokok = penghasilanGajiPokok.getNumber();
            let penghasilan_tunjangan_lainnya = penghasilanTunjanganLainnya.getNumber();

            let ptkp_detail = ptkp.ptkp_detail
            ptkp.ptkp_detail = null;
            let params = `knpwp=${knpwp}
            &jkkrate=${bpjsRateJKK}
            &isbpjskes=${isbpjskes}
            &isbpjstk=${isbpjstk}
            &knpwptext=${knpwptext}
            &metode=${metode}
            &menggunakan_ter=${menggunakan_ter}
            &ptkp=${ptkpselected}
            &ptkpdetratepercentage=${ptkpCategoryRate}
            &ptkpdetratemonth=${ptkpCategoryRateMonth}
            &ptkptext=${ptkptext}
            &ptkpdata=${JSON.stringify(ptkp)}
            &penghasilan_gaji_pokok=${penghasilan_gaji_pokok}
            &penghasilan_tunjangan_lainnya=${penghasilan_tunjangan_lainnya}` ;
            params = btoa(params);
            ptkp.ptkp_detail = ptkp_detail;
            $("#npwp_type").select2({
                dropdownParent: $(".modal .modal-body"),
            });
            loadCetakKalkulator(currentUrl, params);

            // const urlParams = new URL(window.location.href);
            // urlParams.searchParams.append('page-type', 'kalkulator-pph21-nonkaryawan');
            // urlParams.searchParams.append('params', params);
            history.pushState({},"",`?page-type=kalkulator-pph21-karyawan&menu_id=<?php echo (request()->get('menu_id') ? request()->get('menu_id') : 26) ?>&params=${params}`);
        })

        // load cetak if has params
		let urlParams = findGetParameter('params');
		if(urlParams) {
			let decodeParams = atob(urlParams);
			ptkp = JSON.parse(findGetParameter('ptkpdata', decodeParams, false));
			let knpwp = findGetParameter('knpwp', decodeParams, false);
			let metode = findGetParameter('metode', decodeParams, false);
			let ptkpselected = findGetParameter('ptkp', decodeParams, false);
			let jkkrate = findGetParameter('jkkrate', decodeParams, false);
			let isbpjskes = findGetParameter('isbpjskes', decodeParams, false);
			let isbpjstk = findGetParameter('isbpjstk', decodeParams, false);
			let menggunakan_ter = findGetParameter('menggunakan_ter', decodeParams, false);
			ptkpCategoryRate = findGetParameter('ptkpdetratepercentage', decodeParams, false);
			ptkpCategoryRateMonth = findGetParameter('ptkpdetratemonth', decodeParams, false);
            
			let ptkptext = findGetParameter('ptkptext', decodeParams, false);
			let nilaiGP = findGetParameter('penghasilan_gaji_pokok', decodeParams, false);
			let nilaiTunjangan = findGetParameter('penghasilan_tunjangan_lainnya', decodeParams, false);
			
            $(".penghasilan_jkk_label .rate_label").html(`(${jkkrate} %)`);
            $(".jamkes-toggle").attr("data-rate", jkkrate).html(jkkrate);
            bpjsRateJKK = jkkrate;
            let penghasilanJKKTotal = nilaiGP * parseFloat(bpjsRateJKK) / 100;
            penghasilanJKK.set(penghasilanJKKTotal);
            $("input[name=menggunakan_bpjs_kes][value="+isbpjskes+"]").click();
            $("input[name=menggunakan_bpjs_kes][value="+isbpjstk+"]").click();

			$("#kepemilikan_npwp").val(knpwp.trim()).trigger('change');
			$("#ptkp").append(new Option(ptkptext.trim(), ptkpselected.trim(), true, true)).trigger('change');
			$("[name=tunjangan_pajak][value="+metode+"]").click();
			$("[name=menggunakan_ter][value="+menggunakan_ter+"]").click();
			$("[name=perhitungan_kategori_ter]").val(ptkp.ptkp_category);

			penghasilanGajiPokok.set(nilaiGP.trim());
			penghasilanTunjanganLainnya.set(nilaiTunjangan.trim());
			$(".penghasilan-input").trigger('keyup');
			loadCetakKalkulator(currentUrl, urlParams);
		}

        // set meta title
	    setHtmlTitle('{{$title}}')
    })
  </script>