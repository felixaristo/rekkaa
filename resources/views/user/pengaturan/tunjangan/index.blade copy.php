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
          <a class="btn btn-sm btn-warning" id="btn-tunjangan" href="#">
            <i class='bx bx-plus'></i> Tunjangan
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-tunjangan">
                <thead class="table-light">
                  <tr>
                    <th>Nama Tunjangan</th>
                    <th>Besar Tunjangan</th>
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

@include('user.pengaturan.tunjangan.create')

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionStoreUrl = "{{route('user.page.pengaturan.tunjangan.store')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.tunjangan.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.tunjangan.delete', '')}}";
    // let updatedKaryawanIds = [];
    let deletedEmployeeIds = [];
    
    $("#sttunjangankaryawan_is_all").select2({
      dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      console.log('data.id', data.id);
      if (data.id === "0") {
        $("#karyawan-container").slideDown();
      } else {
        $("#karyawan-container").slideUp();
        $("#sttunjangankaryawan_employees").val(null).trigger('change');
      }
    });

    // let formulaInput = document.getElementById("sttunjangankaryawan_formula_txt");
    let valueInput = document.getElementById("sttunjangankaryawan_value");

    // document.getElementById('sttunjangankaryawan_formula_txt').addEventListener('keydown', function (e) {
    //   e.preventDefault();
    // });

    // $("#sttunjangankaryawan_calculation").change(function() {
    //     var selectedOption = $(this).val();

    //     if (selectedOption === "JUMLAH_TETAP") {
    //         $("#jumlahTetapForms").slideDown();
    //         $("#formulaForms").slideUp();
    //         closeCalculator();
    //         formulaInput.required = false;
    //         valueInput.required = true;
    //     } else if (selectedOption === "FORMULA") {
    //         $("#jumlahTetapForms").slideUp();
    //         $("#formulaForms").slideDown();
    //         formulaInput.required = true;
    //         valueInput.required = false;
    //     } else {
    //         $("#jumlahTetapForms").slideUp();
    //         $("#formulaForms").slideUp();
    //         closeCalculator();
    //         formulaInput.required = false;
    //         valueInput.required = false;
    //     }

    //     loadComponentButtons();
    //     $("#calculator").show();
    // });

    function validateOperation(input) {
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
      
      return stack.length === 0; // Check if all brackets are closed
    }

    // formulaInput.addEventListener("click", function() {
    //   $("#calculator").slideDown();
    // });

    $(document).on("click", ".calculator-button", function(e) {
        e.preventDefault();
        // alert();
        var valueToAppend = $(this).data("append");
        var textToAppend = $(this).data("text");
        console.log('textToAppend', textToAppend);
        appendToFormula(valueToAppend, textToAppend);
    });

    function appendToFormula(value, text = '') {
      if (value === 'Delete') {
        // const currentFormula = formulaInput.value;

        // // Find the last space in the formula
        // const lastSpaceIndex = currentFormula.lastIndexOf(' ');

        // if (lastSpaceIndex !== -1) {
        //   // Remove the last word and the space following it
        //   formulaInput.value = currentFormula.slice(0, lastSpaceIndex).trim();
        // } else {
        //   // If there is no space, just clear the input
        //   formulaInput.value = '';
        // }
        $(".formula_txt").children().last().remove();
      } else if (value === 'Reset') {
        // formulaInput.value = '';

        $(".formula_txt").html(``);
      } else {
        // if (formulaInput.value === '') {
        //   // formulaInput.value += value;
        // } else {
        //   formulaInput.value += ' ' + value;
        // }
        text = (text) ? text : value;
        
        $(".formula_txt").append(`<span class="selected-formula" data-value="${value}">${text}</span>`);
      }
      
      
    }

    var uniqueButtonIdentifiers = new Set();

    function loadComponentButtons() {
        // Make an AJAX request to fetch the Component buttons
        $.ajax({
            url: "{{ route('user.page.pengaturan.tunjangan.group.select') }}",
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
                            // button.onclick = function(e) {
                            //     e.preventDefault()
                            //     appendToFormula(component.stgrouptunjangankaryawan_name);
                            // };
                            componentDiv.appendChild(button);

                            // Add the identifier to the set
                            uniqueButtonIdentifiers.add(component.stgrouptunjangankaryawan_id);
                        }
                    });
                // }
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

    // $("#sttunjangankaryawan_formula_txt").click(function() {
    //     loadComponentButtons();
    //     $("#calculator").show();
    // });

    // $("#calculator").click(function(event) {
    //     event.stopPropagation();
    // });

    // $("#close-calculator").click(function(e) {
    //     e.preventDefault()
    //     closeCalculator()
    // });

    $("#sttunjangankaryawan_paymentperiod").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
    });

    $("#sttunjangankaryawan_calculation").select2({
      dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      var selectedOption = data.id;

      if (selectedOption === "JUMLAH_TETAP") {
          $("#jumlahTetapForms").slideDown();
          $("#formulaForms").slideUp();
          closeCalculator();
          // formulaInput.required = false;
          // valueInput.required = true;
      } else if (selectedOption === "FORMULA") {
          $("#jumlahTetapForms").slideUp();
          $("#formulaForms").slideDown();
          // formulaInput.required = true;
          // valueInput.required = false;
          loadComponentButtons();
          $("#calculator").show();
      } else {
          $("#jumlahTetapForms").slideUp();
          $("#formulaForms").slideUp();
          closeCalculator();
          // formulaInput.required = false;
          // valueInput.required = false;
      }
    })
    $("#sttunjangankaryawan_period").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
    }).on("select2:select", function(e) {
      let data = e.params.data;
      if(data.id == 'TAHUN') {
        $("#sttunjangankaryawan_paymentperiod").removeAttr('disabled');
      } else {
        $("#sttunjangankaryawan_paymentperiod").val(null).trigger('change');
        $("#sttunjangankaryawan_paymentperiod").attr('disabled', true);
      }
    });
    // let [tunjanganpegawaiValue] = AutoNumeric.multiple(["#sttunjangankaryawan_value"], { 
    //   currencySymbol: "Rp. ",
    //   decimalCharacter: ",",
    //   digitGroupSeparator: ".",
    //   minimumValue: "0",
    //   decimalPlaces: "0",
    //   unformatOnSubmit: true,
    //   modifyValueOnWheel: false,
    // });
    $("#sttunjangankaryawan_group").select2({
		  dropdownParent: $("#modalTunjangan #formTunjangan"),
      tags: true,
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
    // .on("select2:select", function(e) {
    //   let data = e.params.data;
    //   let sttunjangankaryawan_id = $("#sttunjangankaryawan_id").val();

    //   if(sttunjangankaryawan_id) { // update employee's allowance
    //     updatedKaryawanIds.push(data.id);
    //   }
    // })
    .on("select2:unselect", function(e) {
      let data = e.params.data;
      let sttunjangankaryawan_id = $("#sttunjangankaryawan_id").val();

      if(sttunjangankaryawan_id) { // update employee's allowance
        deletedEmployeeIds.push(data.id);
      }
    });
    // Begin Table Wajib Pajak
    let tblPengaturanTunjangan = $("#table-pengaturan-tunjangan").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('user.page.pengaturan.tunjangan.datatable', ['menu_id' => request()->get('menu_id')])}}",
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
        target: [3],
        width: 30
      }, {
        target: [0,1,2,3],
        className: 'text-center'
      }, {
        target: [4],
        "visible": false,
        "searchable": false,
      }],
      "columns": [
          {
              "data": "sttunjangankaryawan_name"
          },
          {
              "data": "sttunjangankaryawan_value",
              "className": "text-right",
              "render": function(data, type, row) {
                if(row.sttunjangankaryawan_calculation == 'FORMULA') {
                  let parseFormula = (row.sttunjangankaryawan_formula) ? JSON.parse(row.sttunjangankaryawan_formula) : null;
                  let formulatxt = '';
                  parseFormula.forEach(fm => {
                    formulatxt += `${fm.text} `;
                  }) 
                  return formulatxt
                } else {
                  return 'Rp. '+formatCurrency(data);
                }
                
              }
          },
          {
              "data": "sttunjangankaryawan_active",
              "render": function(data, type, row) {
                return (data == "1") ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
              }
          },
          {
            "data": "sttunjangankaryawan_id",
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
          
          {
            "data": "sttunjangankaryawan_formula",
          },
      ],
    });

  $("#modalTunjangan").on("hide.bs.modal", function () {
    tblPengaturanTunjangan.draw(); // Refresh the DataTable
  });

  let tempData = null;
  $("#table-pengaturan-tunjangan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formTunjangan [type=reset]").click();
    $("#modalTunjangan").modal("show");
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanTunjangan.row(row).data();
    tempData = data;
    
    // change url
    $("#formTunjangan").attr("action", actionUpdateUrl+"/"+data.sttunjangankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#sttunjangankaryawan_id").val(data.sttunjangankaryawan_id);
    $("#sttunjangankaryawan_name").val(data.sttunjangankaryawan_name);
    $("#sttunjangankaryawan_period").val(data.sttunjangankaryawan_period).trigger('change');
    $("#sttunjangankaryawan_calculation").val(data.sttunjangankaryawan_calculation).trigger('change');
    if(data.stgrouptunjangan)
      $("#sttunjangankaryawan_group").append(new Option(data.stgrouptunjangan.stgrouptunjangankaryawan_name, data.stgrouptunjangan.stgrouptunjangankaryawan_id, true, true)).trigger('change');
    
    let tunjangandet = (data.sttunjangandetailaktif) ? data.sttunjangandetailaktif : [];
    console.log('tunjangandet', tunjangandet);
    if(tunjangandet.length > 0) {
      let karyawanOpts = '';
      tunjangandet.forEach(kr => {
        karyawanOpts += `<option value="${kr.karyawan.karyawan_id}" selected>${kr.karyawan.karyawan_name}</option>`;
      })
      console.log('karyawanOpts', karyawanOpts);
      $("#sttunjangankaryawan_employees").html(karyawanOpts);
    }

    if (data.sttunjangankaryawan_is_all === false) {
      $("#sttunjangankaryawan_is_all").val(0).trigger('change');
      $("#karyawan-container").slideDown();
    } else {
      $("#sttunjangankaryawan_is_all").val(1).trigger('change');
      $("#karyawan-container").slideUp();
      $("#sttunjangankaryawan_employees").val(null).trigger('change');
    }

    console.log('data.sttunjangankaryawan_calculation', data.sttunjangankaryawan_calculation)
    if(data.sttunjangankaryawan_calculation === "FORMULA") {
      $("#jumlahTetapForms").slideUp();
      $("#formulaForms").slideDown();

      loadComponentButtons();
      $("#calculator").show();
      let parseFormula = (data.sttunjangankaryawan_formula) ? JSON.parse(data.sttunjangankaryawan_formula) : [];
      parseFormula.forEach(fm => {
        appendToFormula(fm.value, fm.text);
      }) 
      
    } else {
      $("#jumlahTetapForms").slideDown();
      $("#formulaForms").slideUp();
      
      closeCalculator();
      
      // tunjanganpegawaiValue.set(data.sttunjangankaryawan_value);
    }

    $("#sttunjangankaryawan_calculation").attr('disabled', true);

    setTimeout(function() {
      console.log('data.sttunjangankaryawan_active', data.sttunjangankaryawan_active)
      enabledFormField("#sttunjangankaryawan_paymentperiod", 'TAHUN', data.sttunjangankaryawan_period, data.sttunjangankaryawan_paymentperiod, 'select');
      $(`#formTunjangan input[name=sttunjangankaryawan_active][value=${data.sttunjangankaryawan_active}]`).click();
      // $(`#formTunjangan input[name=sttunjangankaryawan_recieveabsence][value=${data.sttunjangankaryawan_recieveabsence}]`).click();
      // $(`#formTunjangan input[name=sttunjangankaryawan_recievelate][value=${data.sttunjangankaryawan_recievelate}]`).click();
      $(`#formTunjangan input[name=sttunjangankaryawan_taxable][value=${data.sttunjangankaryawan_taxable}]`).click();
      // checkSwitchFormField("#sttunjangankaryawan_recievelate", data.sttunjangankaryawan_recievelate);
    }, 100)
  })

  $("#modalTunjangan").on("hide.bs.modal", function() {
    // tblPengaturanTunjangan.draw();
  })

  $("#table-pengaturan-tunjangan").on("click", ".btn-delete", function(e) {
    e.preventDefault();
    let row = $(this).closest('tr');
    let data = tblPengaturanTunjangan.row(row).data();
    Swal.fire({
      html: 'Apakah anda ingin menghapus tunjangan <b>'+ data.sttunjangankaryawan_name +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanTunjangan.row(row).remove();
          // return true;
          return fetch(`${actionDeleteUrl}/${data.sttunjangankaryawan_id}?menu_id=${currentMenuId}`, {
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
      tblPengaturanTunjangan.draw();
    });
  })
    // End Table Wajib Pajak

  $("#btn-tunjangan").click(function(e) {
    e.preventDefault();
    $("#sttunjangankaryawan_calculation").attr('disabled', false);
    $("#formTunjangan [type=reset]").click();

    $("#modalTunjangan").modal("show");
  })

  
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

        let formulatxt = $("#sttunjangankaryawan_formula_txt").text();
        let selectedformula = $("#sttunjangankaryawan_formula_txt .selected-formula");
        let formula = [];
        // console.log('data formula', formulatxt);
        if(validateOperation(formulatxt) === false) {
          toastr.error('Invalid formula');
          $(".spinner-box").fadeOut();
          return false;
        }
        console.log('selectedformula.length', selectedformula.length);
        console.log('selectedformula.html', selectedformula.html);
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

        // console.log("formula", formula);
        // console.log("validateOperation");
        // return false;

        $.ajax({
            method: form.method,
            url: form.action,
            data: $(form).serialize()+"&"+$.param({
              _token: $("meta[name=csrf-token]").attr('content'),
              deletedemployee_ids: deletedEmployeeIds,
              formula: formula,
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
                tblPengaturanTunjangan.draw();

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
    // tunjanganpegawaiValue.set(0);
    $("#karyawan-container").slideUp();
    if($('#modalTunjangan').is(':visible')) {
      closeCalculator();
      $("#modalTunjangan").modal("hide");
    }
    deletedEmployeeIds = [];
  })

  // Modal Group
  let pengaturanGroupReadyDraw = false;
  let actionStoreGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.store')}}";
  let actionUpdateGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.update', '')}}";
  let actionDeleteGroupUrl = "{{route('user.page.pengaturan.tunjangan.group.delete', '')}}";
    
  $("#btn-group").click(function(e) {
    e.preventDefault();

    $("#modalGroup").modal("show");
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