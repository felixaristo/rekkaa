<style>
  #modalGroupTable {
    z-index: 1999; 
  }
  #modalGroup {
      z-index: 2000; 
  }
</style>
<!-- Modal Pengaturan Tunjangan -->
<div class="modal fade" id="modalTunjangan" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Pengaturan Tunjangan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formTunjangan" method="POST" action="{{route('user.page.pengaturan.tunjangan.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="sttunjangankaryawan_id" id="sttunjangankaryawan_id">
            <div class="row mb-3" style="display: none;" id="excodetunjangan-container">
              <label class="col-sm-5" for="excodetunjangan">Kode Tunjangan</label>
              <div class="col-sm-7">
                <input type="text" name="excodetunjangan" id="excodetunjangan" class="form-control" disabled>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_name">Deskripsi</label>
              <div class="col-sm-7">
                <input type="text" required name="sttunjangankaryawan_name" id="sttunjangankaryawan_name" class="form-control" placeholder="Masukkan Deskripsi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_period">Periode</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="sttunjangankaryawan_period" id="sttunjangankaryawan_period" class="form-control" data-placeholder="-:Pilih Periode:-">
                  <option value=""></option>  
                  <option value="HARI">Harian</option>
                  <!-- <option value="MINGGU">Mingguan</option> -->
                  <option value="BULAN">Bulanan</option>
                  <option value="TAHUN">Tahunan</option>
                </select>
              </div>
            </div>
            <!-- <div class="row mb-3 field-tahun" style="display: none;">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_type">Tipe</label>
              <div class="col-sm-7">
                <select style="width: 100%;" name="sttunjangankaryawan_type" id="sttunjangankaryawan_type" class="form-control" data-placeholder="-:Pilih Tipe:-">
                  <option value=""></option>  
                  <option value="BONUS">BONUS</option>
                  <option value="THR">THR</option>
                </select>
              </div>
            </div> -->
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_paymentperiod">Periode Pembayaran</label>
              <div class="col-sm-7">
                <select required disabled style="width: 100%;" name="sttunjangankaryawan_paymentperiod" id="sttunjangankaryawan_paymentperiod" class="form-control" data-placeholder="-:Pilih Periode Pembayaran:-">
                  <option value=""></option>
                  @foreach(getMonths() as $key => $val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_calculation">Kalkulasi</label>
              <div class="col-sm-7">
                <select required style="width: 100%;" name="sttunjangankaryawan_calculation" id="sttunjangankaryawan_calculation" class="form-control" data-placeholder="-:Pilih Kalkulasi:-">
                  <option value=""></option>
                  <option value="JUMLAH_TETAP">Jumlah Tetap</option>
                  <option value="FORMULA">Formula</option>
                </select>
              </div>
            </div>
            <div class="row mb-3" id="jumlahTetapForms" style="display: none;">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_value">Besar Tunjangan</label>
              <div class="col-sm-7">
                <input type="text" required name="sttunjangankaryawan_value" id="sttunjangankaryawan_value" class="form-control" placeholder="Masukkan Besar Tunjangan">
              </div>
            </div>
            <div class="row mb-3" id="formulaForms" style="display: none;">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_formula_txt">Formula Tunjangan</label>
              <div class="col-sm-7">
                <div class="formula_txt" name="sttunjangankaryawan_formula_txt" id="sttunjangankaryawan_formula_txt" style="border: 1px solid #ccc; min-height: 100px"></div>
              </div>
            </div>
            <div class="row mb-3 calculator-container" id="calculator" style="display: none;">
              <div>
                  <label class="calculator-label">Operation</label><br>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="+">+</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="-">-</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="*">*</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="/">/</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="%">%</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append="(">(</button>
                  <button class="calculator-button btn btn-warning" data-type="OPERATOR" data-append=")">)</button>
                  <button class="calculator-button btn btn-warning" data-append="Delete">Delete</button>
                  <button class="calculator-button btn btn-warning" data-append="Reset">Reset</button>
              </div>
              <div>
                  <label class="calculator-label">Number</label><br>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="1">1</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="2">2</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="3">3</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="4">4</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="5">5</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="6">6</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="7">7</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="8">8</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="9">9</button>
                  <button class="calculator-button btn btn-warning" data-type="NUMBER" data-append="0">0</button>
              </div>
              <div>
                <label class="calculator-label">Component</label><br>
                <button class="calculator-button btn btn-warning" data-type="TUNJANGAN" data-append="GP" data-text="Gaji Pokok">Gaji Pokok</button>
              </div>
            </div>
            <div class="row mb-3" id="option-container">
              <label for="sttunjangankaryawan_is_all" class="col-sm-5 lbl-req">Diberikan Kepada</label>
              <div class="col-sm-7">
                <select class="form-select" style="width: 100%;" id="sttunjangankaryawan_is_all" name="sttunjangankaryawan_is_all" required data-placeholder="-:Pilih Data:-">
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
              <label class="col-sm-5" for="sttunjangankaryawan_employees">Karyawan</label>
              <div class="col-sm-7">
                <select multiple style="width: 100%;" name="sttunjangankaryawan_employees[]" id="sttunjangankaryawan_employees" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
              </div>
            </div>
            <div class="row mb-3" id="divisi-container" style="display: none;">
              <label class="col-sm-5" for="sttunjangankaryawan_divisis">Divisi</label>
              <div class="col-sm-7">
                <!-- <select multiple style="width: 100%;" name="sttunjangankaryawan_divisis[]" id="sttunjangankaryawan_divisis" class="form-control" data-placeholder="-:Pilih Divisi:-"></select> -->
                <div class="input-group">
                  <select multiple style="width: 85%;" name="sttunjangankaryawan_divisis[]" id="sttunjangankaryawan_divisis" class="form-control" data-placeholder="-:Pilih Divisi:-"></select>
                  <button class="btn btn-sm btn-outline-info" type="button" id="btn-division"><i class="bx bx-plus"></i></button>
                </div>
              </div>
            </div>
            <div class="row mb-3" id="jabatan-container" style="display: none;">
              <label class="col-sm-5" for="sttunjangankaryawan_jabatans">Jabatan</label>
              <div class="col-sm-7">
                <!-- <select multiple style="width: 100%;" name="sttunjangankaryawan_jabatans[]" id="sttunjangankaryawan_jabatans" class="form-control" data-placeholder="-:Pilih Jabatan:-"></select> -->
                <div class="input-group">
                  <select multiple style="width: 85%;" name="sttunjangankaryawan_jabatans[]" id="sttunjangankaryawan_jabatans" class="form-control" data-placeholder="-:Pilih Jabatan:-"></select>
                  <button class="btn btn-sm btn-outline-info" type="button" id="btn-position"><i class="bx bx-plus"></i></button>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_group">Grup Slip Gaji</label>
              <div class="col-sm-7">
                @if(request()->get('menu_id') == 5)
                <div class="input-group">
                  <select required style="width: 85%;" name="sttunjangankaryawan_group" id="sttunjangankaryawan_group" class="form-control" data-placeholder="-:Pilih Grup:-"></select>
                  <button type="button" class="btn btn-sm btn-outline-info" id="btn-group"><i class="bx bx-plus"></i></button>
                </div>
                <span class="help-block"><a href="#" id="btn-lihatgroup" style="font-size: 12px;text-decoration: underline;">Lihat Grup Slip Gaji</a></span>
                @else
                <select required style="width: 100%;" name="sttunjangankaryawan_group" id="sttunjangankaryawan_group" class="form-control" data-placeholder="-:Pilih Grup:-"></select>
                @endif
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_taxable">Dikenakan Pajak</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="sttunjangankaryawan_taxable" class="form-check-input" type="radio" value="1" id="sttunjangankaryawan_taxable_y">
                  <label class="form-check-label" for="sttunjangankaryawan_taxable_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="sttunjangankaryawan_taxable" class="form-check-input" type="radio" value="0" id="sttunjangankaryawan_taxable_t" checked="">
                  <label class="form-check-label" for="sttunjangankaryawan_taxable_t"> Tidak </label>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="sttunjangankaryawan_active">Aktif</label>
              <div class="col-sm-7">
                <div class="form-check form-check-inline">
                  <input name="sttunjangankaryawan_active" class="form-check-input" type="radio" value="1" id="sttunjangankaryawan_active_y" checked="">
                  <label class="form-check-label" for="sttunjangankaryawan_active_y"> Ya </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="sttunjangankaryawan_active" class="form-check-input" type="radio" value="0" id="sttunjangankaryawan_active_t">
                  <label class="form-check-label" for="sttunjangankaryawan_active_t"> Tidak </label>
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
          <form id="formGroup" method="POST" action="{{route('user.page.pengaturan.tunjangan.group.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <input type="hidden" name="stgrouptunjangankaryawan_id" id="stgrouptunjangankaryawan_id">
            <div class="row mb-3">
              <label class="col-sm-5 lbl-req" for="stgrouptunjangankaryawan_name">Nama Grup</label>
              <div class="col-sm-7">
                <input type="text" required name="stgrouptunjangankaryawan_name" id="stgrouptunjangankaryawan_name" class="form-control" placeholder="Masukkan Nama Grup">
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
<div class="modal fade" id="modalGroupTable" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
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
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-grouptunjangan">
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
  var tunjanganValue; // Declare the variable in a global scope

  var uniqueButtonIdentifiers = new Set();

  function loadComponentButtons() {
      // Make an AJAX request to fetch the Component buttons
      $.ajax({
          url: "{{ route('user.page.pengaturan.tunjangan.group.select') }}?menu_id={{request()->get('menu_id')}}",
          method: "GET",
          dataType: "json",
          success: function(response) {
              // Append the Component buttons to the calculator UI
              var componentDiv = document.querySelector('.calculator-container > div:last-child');

              var tidakAdaComponentButton = componentDiv.querySelector('.disabled-button');

              // if (response.data.length === 0 && !tidakAdaComponentButton) {
              //     // If the data is empty and the button doesn't exist, create a disabled button
              //     var disabledButton = document.createElement('button');
              //     disabledButton.className = 'calculator-button btn-outline-danger component-button disabled-button';
              //     disabledButton.textContent = 'Tidak ada component';
              //     componentDiv.appendChild(disabledButton);
              // } else {
                if(response.data) {
                  response.data.forEach(function(component) {
                      // Check if the identifier is already in the set
                      if (!uniqueButtonIdentifiers.has(component.stgrouptunjangankaryawan_id)) {
                          var button = document.createElement('button');
                          let tunjanganstring = JSON.stringify({stgrouptunjangankaryawan_id: component.stgrouptunjangankaryawan_id, stgrouptunjangankaryawan_name: component.stgrouptunjangankaryawan_name});
                          console.log('tunjanganstring', tunjanganstring);
                          button.className = 'calculator-button btn btn-warning component-button';
                          button.textContent = component.stgrouptunjangankaryawan_name;
                          button.setAttribute('data-append', component.stgrouptunjangankaryawan_id);
                          button.setAttribute('data-text', component.stgrouptunjangankaryawan_name);
                          button.setAttribute('data-type', 'TUNJANGAN');
                          // button.onclick = function(e) {
                          //     e.preventDefault()
                          //     appendToFormula(component.stgrouptunjangankaryawan_name);
                          // };
                          componentDiv.appendChild(button);

                          // Add the identifier to the set
                          uniqueButtonIdentifiers.add(component.stgrouptunjangankaryawan_id);
                      }
                  });
                }
          },
          error: function(error) {
              console.error("Error fetching Component buttons:", error);
          }
      });
  }

  function closeCalculator() {
    $("#calculator").slideUp(function() {
      // Clear the content of the calculator container for buttons with the "component-button" class
      uniqueButtonIdentifiers = new Set()
      $(".calculator-container .component-button").remove();

      $(".formula_txt").html("");
    });
  }

  $(function() {
    var element = AutoNumeric.getAutoNumericElement('#sttunjangankaryawan_value');
    if (element) {
    // Retrieve the existing value from localStorage
      [tunjanganValue] = AutoNumeric.getAutoNumericElement('#sttunjangankaryawan_value');
    } else {
      [tunjanganValue] = AutoNumeric.multiple(["#sttunjangankaryawan_value"], { 
          currencySymbol: "Rp. ",
          decimalCharacter: ",",
          digitGroupSeparator: ".",
          minimumValue: "0",
          decimalPlaces: "0",
          unformatOnSubmit: true,
          modifyValueOnWheel: false,
      })

        // Save the initial value in localStorage
        localStorage.setItem('tunjanganValue', 'initialized');
    }

    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.tunjangan.store')}}";

    let deletedEmployeeIds = [];
    let deletedDivisiIds = [];
    let deletedJabatanIds = [];
    
    $("#sttunjangankaryawan_is_all").select2({
      dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      console.log('data.id', data.id);
      $("#karyawan-container, #divisi-container, #jabatan-container").hide();
      $("#sttunjangankaryawan_employees").val(null).trigger('change');
      if (data.id === "0") {
        $("#karyawan-container").slideDown();
      } else if (data.id === "4") {
        $("#divisi-container").slideDown();
      } else if (data.id === "5") {
        $("#jabatan-container").slideDown();
      }
    });
    

    // let formulaInput = document.getElementById("sttunjangankaryawan_formula_txt");
    let valueInput = document.getElementById("sttunjangankaryawan_value");
    
    function appendToFormula(value, text = '', type = '') {
      if (value === 'Delete') {

        $(".formula_txt").children().last().remove();
      } else if (value === 'Reset') {
        $(".formula_txt").html(``);
      } else {
        text = (text) ? text : value;
        
        $(".formula_txt").append(`<span class="selected-formula" data-type="${type}" data-value="${value}">${text}</span>`);
      } 
    }

    function validateOperation(input, inputhtml = '') {
      // Regular expression for matching valid input
      const validPattern = /^[\w\s\+\-\*\/\%\(\)]*$/;
      
      // Check for valid characters and brackets pairing
      if (!validPattern.test(input)) {
        return false;
      }
      
      // Check for double operators
      const doubleOperatorPattern = /[\+\-\*\/\%]{2,}/;
      if (doubleOperatorPattern.test(input)) {
        return false;
      }
      
      // Check for matching brackets
      const stack = [];
      for (let i = 0; i < input.length; i++) {
        const char = input[i];
        if (char === '(') {
          stack.push(char);
        } else if (char === ')') {
          if (stack.length === 0) {
            return false; // Unmatched closing bracket
          }
          stack.pop();
        }
      }

      // Check if there is no operator in multiple allowance
      // console.log('inputhtml',inputhtml);
      // console.log($(inputhtml).length);
      let inputhtmllength = $(inputhtml).length;
      if(inputhtmllength > 0) {
        for(let i = 0; i < inputhtmllength; i++) {
          let nexti = 1+i;
          let type = $(inputhtml).eq(i).data('type');
          if(type == 'TUNJANGAN') { // check next if tunjangan
            
            if($(inputhtml).eq(nexti).html() != undefined) {
              let nexttype = $(inputhtml).eq(nexti).data('type');
              if(type == nexttype) {
                return false; // Unmatched closing bracket
              }

              if(nexttype == 'NUMBER') {
                return false; // Unmatched closing bracket
              }
            }
          }

          if(type == 'NUMBER') { // check next if number
            
            if($(inputhtml).eq(nexti).html() != undefined) {
              let nexttype = $(inputhtml).eq(nexti).data('type');

              if(nexttype == 'TUNJANGAN') {
                return false; // Unmatched closing bracket
              }
            }
          }
        }

        // console.log('axx', $(inputhtml).eq(inputhtmllength-1).html());
        // console.log('axx', $(inputhtml).eq(inputhtmllength-1).data('type'));
        // console.log('inputhtmllength-1', inputhtmllength-1);
        // check the last is not operator
        if($(inputhtml).eq(inputhtmllength-1).data('type') == 'OPERATOR') {
          return false;
        }
      }
      
      return stack.length === 0; // Check if all brackets are closed
    }

    $(".calculator-container").on("click", ".calculator-button", function(e) {
        e.preventDefault();
        // alert();
        var valueToAppend = $(this).data("append");
        var textToAppend = $(this).data("text");
        var typeToAppend = $(this).data("type");
        console.log('textToAppend', textToAppend);
        console.log('typeToAppend', typeToAppend);
        appendToFormula(valueToAppend, textToAppend, typeToAppend);
    });

    $("#sttunjangankaryawan_paymentperiod").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
    });
    // $("#sttunjangankaryawan_type").select2({
		//   dropdownParent: $("#modalTunjangan #formTunjangan"),
    // });

    $("#sttunjangankaryawan_calculation").select2({
      dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      var selectedOption = data.id;

      if (selectedOption === "JUMLAH_TETAP") {
          $("#jumlahTetapForms").slideDown();
          $("#formulaForms").slideUp();
          closeCalculator();
      } else if (selectedOption === "FORMULA") {
          $("#jumlahTetapForms").slideUp();
          $("#formulaForms").slideDown();
          loadComponentButtons();
          $("#calculator").show();
      } else {
          $("#jumlahTetapForms").slideUp();
          $("#formulaForms").slideUp();
          closeCalculator();
      }
    });

    $("#sttunjangankaryawan_period").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      let id = data.id;
      let opts = `<option value=""></option>
                  <option value="JUMLAH_TETAP">Jumlah Tetap</option>`;
      if(id == 'TAHUN') {
        $("#sttunjangankaryawan_paymentperiod").removeAttr('disabled');
        opts += `<option value="FORMULA">Formula</option>`


        $(".field-tahun").slideDown();
        // $("#sttunjangankaryawan_type").removeAttr("disabled");
      } else {
        $("#sttunjangankaryawan_paymentperiod").attr('disabled', true);

        $(".field-tahun").slideUp();
        // $("#sttunjangankaryawan_type").attr("disabled", true);

        $("#jumlahTetapForms").slideUp();
        $("#formulaForms").slideUp();
        closeCalculator();
      }

      $("#sttunjangankaryawan_calculation").html(opts);
    })

    $("#sttunjangankaryawan_group").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
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
					return {
						results: items
					};
				},
      },
		});

  $("#sttunjangankaryawan_employees").select2({
    dropdownParent: $("#modalTunjangan #formTunjangan"),
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
    let sttunjangankaryawan_id = $("#sttunjangankaryawan_id").val();

    if(sttunjangankaryawan_id) { // update employee's allowance
      deletedEmployeeIds.push(data.id);
    }
  });

  $("#sttunjangankaryawan_divisis").select2({
    dropdownParent: $("#modalTunjangan #formTunjangan"),
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
    let sttunjangankaryawan_id = $("#sttunjangankaryawan_id").val();

    if(sttunjangankaryawan_id) { // update employee's allowance
      deletedDivisiIds.push(data.id);
    }
  });

  $("#sttunjangankaryawan_jabatans").select2({
    dropdownParent: $("#modalTunjangan #formTunjangan"),
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
    let sttunjangankaryawan_id = $("#sttunjangankaryawan_id").val();

    if(sttunjangankaryawan_id) { // update employee's allowance
      deletedJabatanIds.push(data.id);
    }
  });

  let tempData = null;

  let formTunjangan = $("#formTunjangan").validate({
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

        let selectedCalculation = $("#sttunjangankaryawan_calculation").val();
        let formula = [];

        if (selectedCalculation === "FORMULA") {
          let formulatxt = $("#sttunjangankaryawan_formula_txt").text();
          let formulahtml = $("#sttunjangankaryawan_formula_txt").html();
          let selectedformula = $("#sttunjangankaryawan_formula_txt .selected-formula");
 
          console.log('data formula', formulatxt);
          if(validateOperation(formulatxt, formulahtml) === false) {
            toastr.error('Invalid formula');
            $(".spinner-box").fadeOut();
            return false;
          }
          // console.log('selectedformula.length', selectedformula.length);
          // console.log('selectedformula.html', selectedformula.html);
          // return false;
          // // alert();
          if(selectedformula.length > 0) {
            for(let i=0; i<selectedformula.length; i++) {
          //     console.log($(fm).attr("data-value"));
              let fmvalue = $(".selected-formula").eq(i).attr("data-value");
              let fmtext = $(".selected-formula").eq(i).text();
              // console.log('fm', isNaN(fm) + ' => ' + fm);
              formula.push({value: fmvalue, text: fmtext});
            }
          }

          console.log("formula", formula);
          console.log("formulatext", formulatxt);
          // return false;
        }
        
        $.ajax({
            method: form.method,
            url: form.action,
            data: $(form).serialize()+"&"+$.param({
              _token: $("meta[name=csrf-token]").attr('content'),
              deletedemployee_ids: deletedEmployeeIds,
              sttunjangankaryawan_formula: formula,
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
                      sttunjangankaryawan_name: response.message
                    })
                  } else {
                      toastr.error(response.message);
                  }
                    return false;
                }
                
                closeCalculator();
                toastr.success(response.message);
                // tblPengaturanTunjangan.draw();

                $("#formTunjangan [type=reset]").click();
            }
        })
    },
  })

  $("#formTunjangan [type=reset]").click(function(e) {
    e.preventDefault();
    resetForm('#formTunjangan');
    // change url
    $("#formTunjangan").attr("action", actionStoreUrl+"?menu_id={{request()->get('menu_id')}}");
    // $("#sttunjangankaryawan_name").removeAttr('disabled');
    $(`#formTunjangan input[name=sttunjangankaryawan_active][value=1]`).click();
      // $(`#formTunjangan input[name=sttunjangankaryawan_recieveabsence][value=0]`).click();
      // $(`#formTunjangan input[name=sttunjangankaryawan_recievelate][value=0]`).click();
    $(`#formTunjangan input[name=sttunjangankaryawan_taxable][value=0]`).click();

    $("#excodetunjangan-container").slideUp();
    tunjanganValue.set(0);
    $("#karyawan-container").slideUp();
    $("#divisi-container").slideUp();
    $("#jabatan-container").slideUp();
    if($('#modalTunjangan').is(':visible')) {
      closeCalculator();
      $("#modalTunjangan").modal("hide");
    }
    deletedEmployeeIds = [];
    deletedDivisiIds = [];
    deletedJabatanIds = [];
  })

  // Modal Group
  let pengaturanGroupReadyDraw = false;
  let actionStoreGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.store')}}";
  let actionUpdateGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.update', '')}}";
  let actionDeleteGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.delete', '')}}";
    
  // Begin Table Wajib Pajak
  let tblPengaturanGroupTunjangan = $("#table-pengaturan-grouptunjangan").DataTable({
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
        "url": "{{route('user.page.pengaturan.tunjangan.group.datatable', ['menu_id' => request()->get('menu_id')])}}",
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
          "data": "stgrouptunjangankaryawan_name"
        },
        {
          "data": "stgrouptunjangankaryawan_id",
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
      // tblPengaturanGroupTunjangan.draw(); // Refresh the DataTable
      if(iseditgroup == false) {
        $("#formGroup [type=reset]").click();
        // change url
        $("#formGroup").attr("action", actionStoreGroupUrl+"?menu_id="+currentMenuId);
      }
  });
  $("#modalGroupTable").on("show.bs.modal", function () {
      pengaturanGroupReadyDraw = true;
      tblPengaturanGroupTunjangan.draw(); // Refresh the DataTable
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
                    stgrouptunjanganpegawai_name: response.message
                  })
                } else {
                    toastr.error(response.message);
                }
                  return false;
              }
              
              toastr.success(response.message);
              tblPengaturanGroupTunjangan.draw();

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
  $("#table-pengaturan-grouptunjangan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formGroup [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupTunjangan.row(row).data();

    // change url
    $("#formGroup").attr("action", actionUpdateGroupUrl+"/"+data.stgrouptunjangankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#stgrouptunjangankaryawan_id").val(data.stgrouptunjangankaryawan_id);
    $("#stgrouptunjangankaryawan_name").val(data.stgrouptunjangankaryawan_name).focus();

    iseditgroup = true;
    $("#modalGroup").modal("show");
  })

  $("#table-pengaturan-grouptunjangan").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanGroupTunjangan.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus grup <b>'+ data.stgrouptunjangankaryawan_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanTunjangan.row(row).remove();
          // return true;
          return fetch(`${actionDeleteGroupUrl}/${data.stgrouptunjangankaryawan_id}?menu_id=${currentMenuId}`, {
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
      tblPengaturanGroupTunjangan.draw();
    });
  })

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>