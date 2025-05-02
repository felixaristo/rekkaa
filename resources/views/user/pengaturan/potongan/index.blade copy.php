<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">
          <a class="btn btn-sm btn-outline-warning" id="btn-group" href="#">
            <i class='bx bx-plus'></i> Grup
          </a>
          <a class="btn btn-sm btn-warning" id="btn-potongan" href="#">
            <i class='bx bx-plus'></i> Potongan
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-potongan">
                <thead class="table-light">
                  <tr>
                    <th>Nama Potongan</th>
                    <th>Jenis Potongan</th>
                    <th>Metode</th>
                    <th>Besar Potongan</th>
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
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_name">Deskripsi</label>
              <div class="col-sm-7">
                <input type="text" required name="stpotongankaryawan_name" id="stpotongankaryawan_name" class="form-control" placeholder="Masukkan Deskripsi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_group">Grup</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="stpotongankaryawan_group" id="stpotongankaryawan_group" class="form-control" data-placeholder="-:Pilih Grup:-"></select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stpotongankaryawan_type">Jenis Potongan</label>
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
                </select>
              </div>
            </div>
            <div class="row mb-3" id="karyawan-container" style="display: none;">
              <label class="col-sm-5" for="stpotongankaryawan_employees">Karyawan</label>
              <div class="col-sm-7">
                <select multiple style="width: 100%;" name="stpotongankaryawan_employees[]" id="stpotongankaryawan_employees" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
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

<!-- Modal Akses -->
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
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-group">
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

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.potongan.store')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.potongan.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.potongan.delete', '')}}";
    // let updatedKaryawanIds = [];
    let deletedEmployeeIds = [];

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
      if (data.id === "0") {
        $("#karyawan-container").slideDown();
      } else {
        $("#karyawan-container").slideUp();
        $("#stpotongankaryawan_employees").val(null).trigger('change');
      }
    });
    
    let [potongankaryawanValue] = AutoNumeric.multiple(["#stpotongankaryawan_value"], { 
      currencySymbol: "Rp. ",
      decimalCharacter: ",",
      digitGroupSeparator: ".",
      minimumValue: "0",
      decimalPlaces: "0",
      unformatOnSubmit: true,
      modifyValueOnWheel: false,
    });
    $("#stpotongankaryawan_group").select2({
		  dropdownParent: $("#modalPotongan #formPotongan"),
      tags: true,
			ajax: {
				url: "{{route('user.page.pengaturan.potongan.group.select')}}",
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
				url: "{{route('user.page.karyawan.select')}}",
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
    // Begin Table Wajib Pajak
    let tblPengaturanPotongan = $("#table-pengaturan-potongan").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // "preDrawCallback": function () {
      //     return potonganReadyDraw
      // },
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('user.page.pengaturan.potongan.datatable', ['menu_id' => request()->get('menu_id')])}}",
          "type": "GET",
          "data": function(data) {
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [5],
        width: 30
      }, {
        target: [0,1,2,3,4,5],
        className: 'text-center'
      }],
      "columns": [
          {
            "data": "stpotongankaryawan_name"
          },
          {
            "data": "stpotongankaryawan_type",
            "render": function(data, type, row) {
              if(data == 'ABSEN') {
                return 'Potongan Absensi';
              } else if(data == 'TETAP') {
                return 'Potongan Tetap';
              } else if(data == 'TELAT') {
                return 'Potongan Telat';
              }
            }
          },
          {
              "data": "stpotongankaryawan_method",
              "render": function(data, type, row) {
              if(data == 'PRORATA') {
                return 'Prorata';
              } else if(data == 'TETAP') {
                return 'Angka Tetap';
              } else if(data == 'HARI') {
                return 'Harian';
              } else if(data == 'BULAN') {
                return 'Bulanan';
              } else if(data == 'KELIPATAN_HARI') {
                return 'Kelipatan Waktu (Harian)';
              } else if(data == 'KELIPATAN_BULAN') {
                return 'Kelipatan Waktu (Bulanan)';
              }
            }
          },
          {
              "data": "stpotongankaryawan_value",
              "className": "text-right",
              "render": function(data, type, row) {
                if(row.stpotongankaryawan_formula !== null) {
                  // return row.stpotongankaryawan_formula;
                  let grouppotongantxt = (row.stpotongankaryawan_formula.indexOf('GP') > -1) ? ['Gaji Pokok'] : [];
                  let grouptunjanganParse = row.grouptunjangans;
                  if(grouptunjanganParse) {
                    grouptunjanganParse.forEach(gtunjangan => {
                      grouppotongantxt.push(gtunjangan.stgrouptunjangankaryawan_name);
                    })
                  }
                  // console.log('grouppotongantxt', grouppotongantxt);
                  return (grouppotongantxt.length > 0) ? grouppotongantxt.join(' + ') : '';
                } else {
                  return 'Rp. '+formatCurrency(data);
                }
                
              }
          },
          {
              "data": "stpotongankaryawan_active",
              "render": function(data, type, row) {
                return (data == "1") ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
              }
          },
          {
            "data": "stpotongankaryawan_id",
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
                  <!-- <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Delete</a
                  > -->
                </div>
              </div>
                `
            }
          },
      ],
    });

  $("#stpotongankaryawan_formula, #stpotongankaryawan_maxtypevalueformula").select2({
    dropdownParent: $("#modalPotongan #formPotongan"),
    // tags: true,
    ajax: {
      url: "{{route('user.page.pengaturan.tunjangan.group.select')}}",
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
    
  $("#table-pengaturan-potongan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    // $(".spinner-box").css({'display': 'table'});

    $("#formPotongan [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanPotongan.row(row).data();
    let stpotongankaryawan_id = data.stpotongankaryawan_id;

    // change url
    $("#formPotongan").attr("action", actionUpdateUrl+"/"+stpotongankaryawan_id+"?menu_id="+currentMenuId);
    
    $("#modalPotongan").modal("show");
    // set data
    $("#stpotongankaryawan_id").val(data.stpotongankaryawan_id);
    $("#stpotongankaryawan_name").val(data.stpotongankaryawan_name);
    $("#stpotongankaryawan_type").val(data.stpotongankaryawan_type).trigger('change').trigger({
      type: "select2:select",
      params: {
        data: {
          id: data.stpotongankaryawan_type,
          text: data.stpotongankaryawan_type,
        }
      }
    });
    $("#stpotongankaryawan_method").val(data.stpotongankaryawan_method).trigger('change').trigger({
      type: "select2:select",
      params: {
        data: {
          id: data.stpotongankaryawan_method,
          text: data.stpotongankaryawan_method,
        }
      }
    });
    $("#stpotongankaryawan_maxtype").val(data.stpotongankaryawan_maxtype).trigger('change').trigger({
      type: "select2:select",
      params: {
        data: {
          id: data.stpotongankaryawan_maxtype,
          text: data.stpotongankaryawan_maxtype,
        }
      }
    });
    
    potongankaryawanValue.set(data.stpotongankaryawan_value);
    $("#stpotongankaryawan_accumulationtime").val(data.stpotongankaryawan_accumulationtime);
    $(`#formPotongan input[name=stpotongankaryawan_active][value=${data.stpotongankaryawan_active}]`).click();
    $(`#formPotongan input[name=stpotongankaryawan_taxable][value=${data.stpotongankaryawan_taxable}]`).click();

    if(data.stgrouppotongan)
      $("#stpotongankaryawan_group").append(new Option(data.stgrouppotongan.stgrouppotongankaryawan_name, data.stgrouppotongan.stgrouppotongankaryawan_id, true, true)).trigger('change');
    
    let potongandet = (data.stpotongandetailaktif) ? data.stpotongandetailaktif : [];
    console.log('potongandet', potongandet);
    if(potongandet.length > 0) {
      let karyawanOpts = '';
      potongandet.forEach(kr => {
        karyawanOpts += `<option value="${kr.karyawan.karyawan_id}" selected>${kr.karyawan.karyawan_name}</option>`;
      })
      console.log('karyawanOpts', karyawanOpts);
      $("#stpotongankaryawan_employees").html(karyawanOpts);
    }

    if (data.stpotongankaryawan_is_all === false) {
      $("#stpotongankaryawan_is_all").val(0).trigger('change');
      $("#karyawan-container").slideDown();
    } else {
      $("#stpotongankaryawan_is_all").val(1).trigger('change');
      $("#karyawan-container").slideUp();
      $("#stpotongankaryawan_employees").val(null).trigger('change');
    }
    
    let grouptunjanganOpts = (data.stpotongankaryawan_formula && data.stpotongankaryawan_formula.indexOf('GP') > -1) ? '<option value="GP" selected>Gaji Pokok</option>' : '';
    let grouptunjangans = data.grouptunjangans;
    if(grouptunjangans.length > 0) {
      grouptunjangans.forEach(gt => {
        grouptunjanganOpts += `<option value="${gt.stgrouptunjangankaryawan_id}" selected>${gt.stgrouptunjangankaryawan_name}</option>`;
      })
    }
    $("#stpotongankaryawan_formula").html(grouptunjanganOpts);

    let persentasegrouptunjanganOpts = (data.stpotongankaryawan_formula && data.stpotongankaryawan_formula.indexOf('GP') > -1) ? '<option value="GP" selected>Gaji Pokok</option>' : '';
    let persentasegrouptunjangans = data.persentasegrouptunjangans;
    if(persentasegrouptunjangans.length > 0) {
      persentasegrouptunjangans.forEach(gt => {
        persentasegrouptunjanganOpts += `<option value="${gt.stgrouptunjangankaryawan_id}" selected>${gt.stgrouptunjangankaryawan_name}</option>`;
      })
    }
    $("#stpotongankaryawan_maxtypevalueformula").html(persentasegrouptunjanganOpts);

    if(data.stpotongankaryawan_maxtype === 'TETAP') {
      // limitpotongankaryawanValue.set(data.stpotongankaryawan_maxtypevalue);
    } else {
      $("#stpotongankaryawan_limit_persen").val(data.stpotongankaryawan_maxtypevalue);
    }
    if(data.stpotongankaryawan_method === "PRORATA") {
      $("#stpotongankaryawan_valuebox").slideUp();
      $("#stpotongankaryawan_value").attr("disabled", true);
      $("#stpotongankaryawan_formulabox").slideDown();
      $("#stpotongankaryawan_formula").removeAttr("disabled");
    } else {
      $("#stpotongankaryawan_formulabox").slideUp();
      $("#stpotongankaryawan_formula").attr("disabled", true);
      $("#stpotongankaryawan_valuebox").slideDown();
      $("#stpotongankaryawan_value").removeAttr("disabled");
    }
  })

  // $("#modalPotongan").on("hide.bs.modal", function() {
  //   // tblPengaturanPotongan.draw();
  // })

  $("#table-pengaturan-potongan").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanPotongan.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus potongan <b>'+ data.stpotongankaryawan_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanPotongan.row(row).remove();
          // return true;
          return fetch(`${actionDeleteUrl}/${data.stpotongankaryawan_id}?menu_id=${currentMenuId}`, {
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
      tblPengaturanPotongan.draw();
    });
  })
    // End Table Wajib Pajak

  $("#btn-potongan").click(function(e) {
    e.preventDefault();
    // $("#stpotongankaryawan_calculation").attr('disabled', false);
    $("#formPotongan [type=reset]").click();

    $("#modalPotongan").modal("show");
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
                tblPengaturanPotongan.draw();

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

    if($('#modalPotongan').is(':visible')) {
      $("#modalPotongan").modal("hide");
    }
  })

  // Modal Group
  let pengaturanGroupReadyDraw = false;
  let actionStoreGroupUrl = "{{route('user.page.pengaturan.potongan.group.store')}}";
  let actionUpdateGroupUrl = "{{route('user.page.pengaturan.potongan.group.update', '')}}";
  let actionDeleteGroupUrl = "{{route('user.page.pengaturan.potongan.group.delete', '')}}";
    
  $("#btn-group").click(function(e) {
    e.preventDefault();
    // $("#formGroup [type=reset]").click();

    $("#modalGroup").modal("show");
    pengaturanGroupReadyDraw = true;
    tblPengaturanGroup.draw();
  })

  // Begin Table Wajib Pajak
  let tblPengaturanGroup = $("#table-pengaturan-group").DataTable({
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
              tblPengaturanGroup.draw();

              $("#formGroup [type=reset]").click();
              
          }
      })
    },
  })

  $("#table-pengaturan-group").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formGroup [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanGroup.row(row).data();

    // change url
    $("#formGroup").attr("action", actionUpdateGroupUrl+"/"+data.stgrouppotongankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#stgrouppotongankaryawan_id").val(data.stgrouppotongankaryawan_id);
    $("#stgrouppotongankaryawan_name").val(data.stgrouppotongankaryawan_name).focus();
  })

  $("#table-pengaturan-group").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroup.row(row).data();
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
      tblPengaturanGroup.draw();
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