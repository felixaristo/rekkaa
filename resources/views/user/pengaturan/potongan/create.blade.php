<style>
  #modalGroupTable {
    z-index: 1999; 
  }
  #modalGroup {
      z-index: 2000; 
  }
</style>
<!-- Modal Pengaturan Potongan -->
<div class="modal fade" id="modalPotongan" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Pengaturan Potongan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formPotongan" method="POST" action="{{route('user.page.pengaturan.potongan.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="stpotongankaryawan_id" id="stpotongankaryawan_id">
            <div class="row mb-3" style="display: none;" id="excodepotongan-container">
              <label class="col-sm-5" for="excodepotongan">Kode Potongan</label>
              <div class="col-sm-7">
                <input type="text" name="excodepotongan" id="excodepotongan" class="form-control" disabled>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_name">Deskripsi</label>
              <div class="col-sm-7">
                <input type="text" required name="stpotongankaryawan_name" id="stpotongankaryawan_name" class="form-control" placeholder="Masukkan Deskripsi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_type">Jenis</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpotongankaryawan_type" id="stpotongankaryawan_type" class="form-control" data-placeholder="-:Pilih Jenis Potongan:-">
                  <option value=""></option>  
                  <option value="TELAT">Potongan Telat</option>
                  <option value="ABSEN">Potongan Absen</option>
                  <option value="TETAP">Potongan Tetap</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_method">Metode</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpotongankaryawan_method" id="stpotongankaryawan_method" class="form-control" data-placeholder="-:Pilih Metode:-">
                  <option value=""></option>
                </select>
              </div>
            </div>
            <div class="row mb-3" id="stpotongankaryawan_valuebox" style="display: none;">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_value">Besar Potongan</label>
              <div class="col-sm-7">
                <input type="text" disabled  name="stpotongankaryawan_value" id="stpotongankaryawan_value" class="form-control" placeholder="Masukkan Besar Potongan">
              </div>
            </div>
            <div class="row mb-3" id="stpotongankaryawan_formulabox" style="display: none;">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_formula">Perhitungan Prorata (Akumulasi)</label>
              <div class="col-sm-7">
                <select required multiple disabled style="width: 100%;" name="stpotongankaryawan_formula[]" id="stpotongankaryawan_formula" class="form-control" data-placeholder="-:Pilih Data:-"></select>
              </div>
            </div>

            <!-- Kelipatan Waktu -->
            <div class="row mb-3" id="stpotongankaryawan_accumulationtimebox" style="display: none;">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_accumulationtime">Kelipatan Waktu</label>
              <div class="col-sm-7">
                <div class="input-group">
                  <input type="number" disabled required name="stpotongankaryawan_accumulationtime" id="stpotongankaryawan_accumulationtime" class="form-control" placeholder="Masukkan Kelipatan Waktu">
                  <span class="input-group-text">menit</span>
                </div>
              </div>
            </div>
            <!-- <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_maxtype">Maksimal Potongan</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpotongankaryawan_maxtype" id="stpotongankaryawan_maxtype" class="form-control" data-placeholder="-:Pilih Maksimal Potongan:-">
                  <option value=""></option>
                </select>
              </div>
            </div> -->
            <!-- Maksimal Potongan -->
            <!-- <div class="row">
              <div class="col-sm-12">
                <div class="limitpersenbox row " style="display: none;">
                  <div class="col-sm-12">
                    <div class="row mb-3">
                      <label for="" class="col-sm-5 lbl-req">Persentase</label>
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="text" disabled required name="stpotongankaryawan_maxtypevalue" id="stpotongankaryawan_limit_persen" class="form-control" placeholder="Masukkan Maksimal Potongan">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label class="col-sm-5 lbl-req" for="stpotongankaryawan_maxtypevalueformula">Persentase dari (Akumulasi)?</label>
                      <div class="col-sm-7">
                        <select required multiple disabled style="width: 100%;" name="stpotongankaryawan_maxtypevalueformula[]" id="stpotongankaryawan_maxtypevalueformula" class="form-control" data-placeholder="-:Pilih Data:-"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="limittetapbox row mb-3" style="display: none;">
                  <label for="" class="col-sm-5 lbl-req">Nominal</label>
                  <div class="col-sm-7">
                    <div class="input-group">
                      <input type="text" disabled required name="stpotongankaryawan_maxtypevalue" id="stpotongankaryawan_limit_tetap" class="form-control" placeholder="Masukkan Maksimal Potongan">
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="row mb-3" id="option-container">
              <label for="stpotongankaryawan_is_all" class="col-sm-5 lbl-req">Diberikan Kepada</label>
              <div class="col-sm-7">
                <select class="form-select" style="width: 100%;" id="stpotongankaryawan_is_all" name="stpotongankaryawan_is_all" required data-placeholder="-:Pilih Data:-">
                  <option value=""></option>
                  <option value="1">Semua Karyawan</option>
                  <option value="0">Karyawan Tertentu</option>
                  <option value="2">Hanya Perempuan</option>
                  <option value="3">Hanya Laki-laki</option>
                  <option value="4">Berdasarkan Divisi</option>
                  <option value="5">Berdasarkan Jabatan</option>
                </select>
              </div>
            </div>
            <div class="row mb-3" id="karyawan-container" style="display: none;">
              <label class="col-sm-5" for="stpotongankaryawan_employees">Karyawan</label>
              <div class="col-sm-7">
                <select multiple style="width: 100%;" name="stpotongankaryawan_employees[]" id="stpotongankaryawan_employees" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
              </div>
            </div>
            <div class="row mb-3" id="divisi-container" style="display: none;">
              <label class="col-sm-5" for="stpotongankaryawan_divisis">Divisi</label>
              <div class="col-sm-7">
                <!-- <select multiple style="width: 100%;" name="stpotongankaryawan_divisis[]" id="stpotongankaryawan_divisis" class="form-control" data-placeholder="-:Pilih Divisi:-"></select> -->
                <div class="input-group">
                  <select multiple style="width: 85%;" name="stpotongankaryawan_divisis[]" id="stpotongankaryawan_divisis" class="form-control" data-placeholder="-:Pilih Divisi:-"></select>
                  <button class="btn btn-sm btn-outline-info" type="button" id="btn-division"><i class="bx bx-plus"></i></button>
                </div>
              </div>
            </div>
            <div class="row mb-3" id="jabatan-container" style="display: none;">
              <label class="col-sm-5" for="stpotongankaryawan_jabatans">Jabatan</label>
              <div class="col-sm-7">
                <!-- <select multiple style="width: 100%;" name="stpotongankaryawan_jabatans[]" id="stpotongankaryawan_jabatans" class="form-control" data-placeholder="-:Pilih Jabatan:-"></select> -->
                <div class="input-group">
                  <select multiple style="width: 85%;" name="stpotongankaryawan_jabatans[]" id="stpotongankaryawan_jabatans" class="form-control" data-placeholder="-:Pilih Jabatan:-"></select>
                  <button class="btn btn-sm btn-outline-info" type="button" id="btn-position"><i class="bx bx-plus"></i></button>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_group">Grup Slip Gaji</label>
              <div class="col-sm-7">
              @if(request()->get('menu_id') == 53)
                <div class="input-group">
                  <select required style="width: 85%;" name="stpotongankaryawan_group" id="stpotongankaryawan_group" class="form-control" data-placeholder="-:Pilih Grup:-"></select>
                  <button type="button" class="btn btn-sm btn-outline-info" id="btn-group"><i class="bx bx-plus"></i></button>
                </div>
                <span class="help-block"><a href="#" id="btn-lihatgroup" style="font-size: 12px;text-decoration: underline;">Lihat Grup Slip Gaji</a></span>
              @else
                <select required style="width: 100%;" name="stpotongankaryawan_group" id="stpotongankaryawan_group" class="form-control" data-placeholder="-:Pilih Grup:-"></select>
              @endif
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_taxable">Pengurang Pajak</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="stpotongankaryawan_taxable" class="form-check-input" type="radio" value="1" id="stpotongankaryawan_taxable_y">
                  <label class="form-check-label" for="stpotongankaryawan_taxable_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="stpotongankaryawan_taxable" class="form-check-input" type="radio" value="0" id="stpotongankaryawan_taxable_t" checked="">
                  <label class="form-check-label" for="stpotongankaryawan_taxable_t"> Tidak </label>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_active">Aktif</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="stpotongankaryawan_active" class="form-check-input" type="radio" value="1" id="stpotongankaryawan_active_y" checked="">
                  <label class="form-check-label" for="stpotongankaryawan_active_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="stpotongankaryawan_active" class="form-check-input" type="radio" value="0" id="stpotongankaryawan_active_t">
                  <label class="form-check-label" for="stpotongankaryawan_active_t"> Tidak </label>
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

<!-- Modal Group -->
<div class="modal fade" id="modalGroup" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Pengaturan Grup</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formGroup" method="POST" action="{{route('user.page.pengaturan.potongan.group.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="stgrouppotongankaryawan_id" id="stgrouppotongankaryawan_id">
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stgrouppotongankaryawan_name">Nama Grup</label>
              <div class="col-sm-7">
                <input type="text" required name="stgrouppotongankaryawan_name" id="stgrouppotongankaryawan_name" class="form-control" placeholder="Masukkan Nama Grup">
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

<!-- Modal Group -->
<div class="modal fade" id="modalGroupTable" tabindex="-2" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Daftar Grup</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12" id="box-tablegroup">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-grouppotongan">
                <thead class="table-light">
                  <tr>
                    <th>Nama Grup</th>
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
  </div>
</div>

@include('user.master.karyawan.karyawan-divisi')
@include('user.master.karyawan.karyawan-jabatan')

<script>
  var potongankaryawanValue;

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.potongan.store')}}";

    let deletedEmployeeIds = [];
    let deletedDivisiIds = [];
    let deletedJabatanIds = [];

    var element = AutoNumeric.getAutoNumericElement('#stpotongankaryawan_value');
    if (element) {
    // Retrieve the existing value from localStorage
      [potongankaryawanValue] = AutoNumeric.getAutoNumericElement('#stpotongankaryawan_value');
    } else {
      [potongankaryawanValue] = AutoNumeric.multiple(["#stpotongankaryawan_value"], { 
          currencySymbol: "Rp. ",
          decimalCharacter: ",",
          digitGroupSeparator: ".",
          minimumValue: "0",
          decimalPlaces: "0",
          unformatOnSubmit: true,
          modifyValueOnWheel: false,
      })

        // Save the initial value in localStorage
        localStorage.setItem('potongankaryawanValue', 'initialized');
    }

    $(`#stpotongankaryawan_type`).select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;

      let options = `<option value=""></option>`;
      if(data.id == 'TELAT') {
        options += `<option value="HARI">Harian</option>
        <option value="BULAN">Bulanan</option>
        <option value="KELIPATAN_HARI">Kelipatan Waktu (Harian)</option>
        <option value="KELIPATAN_BULAN">Kelipatan Waktu (Bulanan)</option>`;
        $("#stpotongankaryawan_method").html(options);
      } else if(data.id == 'ABSEN') {
        options += `<option value="PRORATA">Prorata</option>
        <option value="TETAP">Angka Tetap</option>`;
        $("#stpotongankaryawan_method").html(options);
      } else if(data.id == 'TETAP') {
        options += `<option value="HARI">Harian</option>
        <option value="BULAN">Bulanan</option>`;
        $("#stpotongankaryawan_method").html(options);
      }
    });
    
    $(`#stpotongankaryawan_method`).select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;

      if(data.id == 'KELIPATAN_HARI' || data.id == 'KELIPATAN_BULAN') {
        $("#stpotongankaryawan_accumulationtimebox").slideDown();
        $("#stpotongankaryawan_accumulationtime").removeAttr("disabled");
      } else {
        $("#stpotongankaryawan_accumulationtimebox").slideUp();
        $("#stpotongankaryawan_accumulationtime").attr("disabled", true);
      }
      
      if(data.id == 'PRORATA') {
        $("#stpotongankaryawan_valuebox").slideUp();
        $("#stpotongankaryawan_value").attr("disabled", true);
        potongankaryawanValue.set(0);
        $("#stpotongankaryawan_formulabox").slideDown();
        $("#stpotongankaryawan_formula").removeAttr("disabled");
      } else {
        $("#stpotongankaryawan_formulabox").slideUp();
        $("#stpotongankaryawan_formula").attr("disabled", true);
        $("#stpotongankaryawan_formula").val(null).trigger('change');
        $("#stpotongankaryawan_valuebox").slideDown();
        $("#stpotongankaryawan_value").removeAttr("disabled");
      }

      let optMaxType = `<option value=""></option>`;

      $(".limittetapbox, .limitpersenbox").slideUp();
      // $("#stpotongankaryawan_limit_tetap, #stpotongankaryawan_limit_persen").attr("disabled", true);

      if(data.id == 'BULAN') {
        optMaxType += `<option value="TIDAK_BERLAKU">Tidak Berlaku</option>`;
        $("#stpotongankaryawan_maxtype").html(optMaxType);
      } else {
        optMaxType += `<option value="TETAP">Angka Tetap</option>
        <option value="PERSEN">Persentase</option>
        <option value="TIDAK_BERLAKU">Tidak Berlaku</option>`;
        $("#stpotongankaryawan_maxtype").html(optMaxType);
      }
    });

    $(`#stpotongankaryawan_maxtype`).select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;

      if(data.id == 'TETAP' || data.id == 'PERSEN') {
        if(data.id == 'TETAP') {
          $(".limitpersenbox").slideUp();
          // $("#stpotongankaryawan_limit_persen").attr("disabled", true);
          $("#stpotongankaryawan_maxtypevalueformula").attr("disabled", true);
          $(".limittetapbox").slideDown();
          // $("#stpotongankaryawan_limit_tetap").removeAttr("disabled");
        } else if(data.id == 'PERSEN') {
          $(".limittetapbox").slideUp();
          // $("#stpotongankaryawan_limit_tetap").attr("disabled", true);
          $(".limitpersenbox").slideDown();
          // $("#stpotongankaryawan_limit_persen").removeAttr("disabled");
          $("#stpotongankaryawan_maxtypevalueformula").removeAttr("disabled");
        }
      } else {
        $(".limittetapbox, .limitpersenbox").slideUp();
        // $("#stpotongankaryawan_limit_tetap, #stpotongankaryawan_limit_persen").attr("disabled", true);
      }
    });

    $("#stpotongankaryawan_is_all").select2({
      dropdownParent: $("#modalPotongan #formPotongan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      console.log('data.id', data.id);
      $("#karyawan-container, #divisi-container, #jabatan-container").hide();
      $("#stpotongankaryawan_employees").val(null).trigger('change');
      if (data.id === "0") {
        $("#karyawan-container").slideDown();
      } else if (data.id === "4") {
        $("#divisi-container").slideDown();
      } else if (data.id === "5") {
        $("#jabatan-container").slideDown();
      }
    });

    $("#stpotongankaryawan_group").select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
      // tags: true,
			ajax: {
				url: `{{route('user.page.pengaturan.potongan.group.select')}}?menu_id=${currentMenuId}`,
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
						item.id = item.stgrouppotongankaryawan_id;
						item.text = item.stgrouppotongankaryawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
      },
		});
    
    $("#stpotongankaryawan_employees").select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
      // tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
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
		})
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let stpotongankaryawan_id = $("#stpotongankaryawan_id").val();

      if(stpotongankaryawan_id) { // update employee's allowance
        deletedEmployeeIds.push(data.id);
      }
    });

    $("#stpotongankaryawan_divisis").select2({
      dropdownParent: $("#modalPotongan #formPotongan"),
      // tags: true,
      ajax: {
        url: `{{route('user.page.karyawan.divisi.select')}}?menu_id=${currentMenuId}`,
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
    })
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let stpotongankaryawan_id = $("#stpotongankaryawan_id").val();

      if(stpotongankaryawan_id) { // update employee's allowance
        deletedDivisiIds.push(data.id);
      }
    });

    $("#stpotongankaryawan_jabatans").select2({
      dropdownParent: $("#modalPotongan #formPotongan"),
      // tags: true,
      ajax: {
        url: `{{route('user.page.karyawan.jabatan.select')}}?menu_id=${currentMenuId}`,
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
    })
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let stpotongankaryawan_id = $("#stpotongankaryawan_id").val();

      if(stpotongankaryawan_id) { // update employee's allowance
        deletedJabatanIds.push(data.id);
      }
    });
    // Begin Table Wajib Pajak

  $("#stpotongankaryawan_formula, #stpotongankaryawan_maxtypevalueformula").select2({
    dropdownParent: $("#modalPotongan #formPotongan"),
    // tags: true,
    ajax: {
      url: `{{route('user.page.pengaturan.tunjangan.group.select')}}?menu_id=${currentMenuId}`,
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
          item.id = item.stgrouptunjangankaryawan_id;
          item.text = item.stgrouptunjangankaryawan_name;
          // console.log('item.kode', item)
          return item
        })
        // insert default gaji pokok
        items.unshift({id: 'GP', text: 'Gaji Pokok'});
        return {
          results: items
        };
      },
    },
  })
  
  let formPotongan = $("#formPotongan").validate({
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
        $(".spinner-box").css({'display': 'table'});

        $.ajax({
            method: form.method,
            url: form.action,
            data: $(form).serialize()+"&"+$.param({
              _token: $("meta[name=csrf-token]").attr('content'),
              deletedemployee_ids: deletedEmployeeIds,
              deleteddivisi_ids: deletedDivisiIds,
              deletedjabatan_ids: deletedJabatanIds,
            }),
            error: function(error) {
              $(".spinner-box").fadeOut();
              console.log('error.responseJSON', error.responseJSON)
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
                  if(Array.isArray(response.message)) {
                    formAuthentication.showErrors({
                      stpotongankaryawan_name: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                toastr.success(response.message);
                // tblPengaturanPotongan.draw();

                $("#formPotongan [type=reset]").click();
                
            }
      })
    },
  })

  $("#formPotongan [type=reset]").click(function(e) {
    e.preventDefault();
    resetForm('#formPotongan');
    // change url
    $("#formPotongan").attr("action", actionStoreUrl+"?menu_id={{request()->get('menu_id')}}");
    // $("#stpotongankaryawan_name").removeAttr('disabled');
    $(`#formPotongan input[name=stpotongankaryawan_active][value=1]`).click();
    $(`#formPotongan input[name=stpotongankaryawan_taxable][value=0]`).click();
    potongankaryawanValue.set(0);
    // limitpotongankaryawanValue.set(0);
    $("#excodepotongan-container").slideUp();
    $("#karyawan-container").slideUp();

    if($('#modalPotongan').is(':visible')) {
      $("#modalPotongan").modal("hide");
    }    
    deletedEmployeeIds = [];
    deletedDivisiIds = [];
    deletedJabatanIds = [];
  })

  // Modal Group
  let pengaturanGroupReadyDraw = false;
  let actionStoreGroupUrl = "{{route('user.page.pengaturan.potongan.group.store')}}";
  let actionUpdateGroupUrl = "{{route('user.page.pengaturan.potongan.group.update', '')}}";
  let actionDeleteGroupUrl = "{{route('user.page.pengaturan.potongan.group.delete', '')}}";

  // // Begin Table Wajib Pajak
  let tblPengaturanGroupPotongan = $("#table-pengaturan-grouppotongan").DataTable({
    // "filtering": false,
    "searching": false,
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "order": [], //Initial no order.
    "searchDelay": 1050,
    "preDrawCallback": function () {
        return pengaturanGroupReadyDraw
    },
    // Load data for the table's content from an Ajax source
    "ajax": {
        "url": "{{route('user.page.pengaturan.potongan.group.datatable', ['menu_id' => request()->get('menu_id')])}}",
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
      target: [1],
      width: 30
    }, {
      target: [0,1],
      className: 'text-center'
    }],
    "columns": [
        {
            "data": "stgrouppotongankaryawan_name"
        },
        {
          "data": "stgrouppotongankaryawan_id",
          "render": function(data, type, row) {
              return `
              <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item btn-edit" href="javascript:void(0);"
                  ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                >
                <a class="dropdown-item btn-delete" href="javascript:void(0);"
                  ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
                >
              </div>
            </div>
              `
          }
        },
    ],
  });

  $("#btn-group").click(function(e) {
    e.preventDefault();
    iseditgroup = false;
    $("#modalGroup").modal("show");
  })
  $("#btn-lihatgroup").click(function(e) {
    e.preventDefault();

    $("#modalGroupTable").modal("show");
  })

  $("#modalGroup").on("show.bs.modal", function () {
      // pengaturanGroupReadyDraw = true;
      // tblPengaturanGroupPotongan.draw(); // Refresh the DataTable
      if(iseditgroup == false) {
        $("#formGroup [type=reset]").click();
        // change url
        $("#formGroup").attr("action", actionStoreGroupUrl+"?menu_id="+currentMenuId);
      }
  });
  
  $("#modalGroupTable").on("show.bs.modal", function () {
      pengaturanGroupReadyDraw = true;
      tblPengaturanGroupPotongan.draw(); // Refresh the DataTable
      // change url
      $("#formGroup").attr("action", actionStoreGroupUrl+"?menu_id="+currentMenuId);
  });

  let formGroup = $("#formGroup").validate({
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
      $(".spinner-box").css({'display': 'table'});

      $.ajax({
          method: form.method,
          url: form.action,
          data: $(form).serialize()+"&"+$.param({
            _token: $("meta[name=csrf-token]").attr('content'),
          }),
          error: function(error) {
            $(".spinner-box").fadeOut();
            console.log('error.responseJSON', error.responseJSON)
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
                if(Array.isArray(response.message)) {
                  formAuthentication.showErrors({
                    stgrouppotongankaryawan_name: response.message
                  })
                } else {
                    toastr.error(response.message);
                }
                  return false;
              }
              
              toastr.success(response.message);
              tblPengaturanGroupPotongan.draw();

              $("#formGroup [type=reset]").click();
              // change url
              $("#formGroup").attr("action", actionStoreGroupUrl+"?menu_id="+currentMenuId);
              iseditgroup = false;

              $("#modalGroup").modal("hide");
          }
      })
    },
  })

  let iseditgroup = false;
  $("#table-pengaturan-grouppotongan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formGroup [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupPotongan.row(row).data();

    // change url
    $("#formGroup").attr("action", actionUpdateGroupUrl+"/"+data.stgrouppotongankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#stgrouppotongankaryawan_id").val(data.stgrouppotongankaryawan_id);
    $("#stgrouppotongankaryawan_name").val(data.stgrouppotongankaryawan_name).focus();

    iseditgroup = true;
    $("#modalGroup").modal("show");
  })

  $("#table-pengaturan-grouppotongan").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupPotongan.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus grup <b>'+ data.stgrouppotongankaryawan_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanPotongan.row(row).remove();
          // return true;
          return fetch(`${actionDeleteGroupUrl}/${data.stgrouppotongankaryawan_id}?menu_id=${currentMenuId}`, {
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
              showCancelButton: false,
              confirmButtonText: "Ok",
              icon: 'error'
          })
          return false;
      }

      toastr.success(result.message);
      tblPengaturanGroupPotongan.draw();
    });
  })

  $("#formGroup [type=reset]").click(function(e) {
    e.preventDefault();
    $("#formGroup").attr("action", actionStoreGroupUrl + "?menu_id=" + currentMenuId);
    resetForm('#formGroup');
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>