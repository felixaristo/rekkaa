<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h5 class="mb-0">{{$title}}</h5>
          </div>
          <?php if(in_array('C', request()->get('permission_codes'))): ?>
          <div class="col-sm-6 text-right">
            <!-- <a class="btn btn-sm btn-outline-warning" id="btn-group" href="#">
              <i class='bx bx-plus'></i> Grup Slip Gaji
            </a> -->
            <a class="btn btn-sm btn-warning" id="btn-tunjangan" href="#">
              <i class='bx bx-plus'></i> Tunjangan
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-tunjangan">
            <thead class="table-light">
              <tr>
                <th>Kode Tunjangan</th>
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
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

@include('user.pengaturan.tunjangan.create')

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionCreateUrl = "{{route('user.page.pengaturan.tunjangan.store', '')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.tunjangan.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.tunjangan.delete', '')}}";

    // Begin Table Tunjangan
    let tblPengaturanTunjangan = $("#table-pengaturan-tunjangan").DataTable({
      // "filtering": false,
      "ordering": true,
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Nama / Kode Tunjangan",
      },
      "order": [[0, 'asc']], //Initial no order.
      "searchDelay": 1050,
      "ajax": {
          "url": "{{route('user.page.pengaturan.tunjangan.datatable', ['menu_id' => request()->get('menu_id')])}}",
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
        target: [4],
        width: 30,
        orderable: false
      }, {
        target: [0,1,2,3,4],
        className: 'text-center'
      }, {
        target: [5],
        "visible": false,
        "searchable": false,
      }],
      "columns": [
          {
            "data": "sttunjangankaryawan_id",
            "title": "Kode Tunjangan",
            "render": function(data, type, row) {
              if (data && (type === 'display' || type === 'filter')) {
                return 'TU' + data;
              }
              return data;
            }
          },
          {
              "data": "sttunjangankaryawan_name"
          },
          {
              "data": "sttunjangankaryawan_value",
              "className": "text-right",
              "render": function(data, type, row) {
                if(row.sttunjangankaryawan_calculation == 'FORMULA') {
                  let parseFormula = (row.sttunjangankaryawan_formula) ? JSON.parse(row.sttunjangankaryawan_formula) : null;
                  // console.log('parseFormula', parseFormula);
                  let formulatxt = '';
                  if(parseFormula) {
                    parseFormula.forEach(fm => {
                      formulatxt += `${fm.text} `;
                    }) 
                  }
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
                  <?php if(in_array('U', request()->get('permission_codes'))): ?>
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                  <?php endif ?>
                  <?php if(in_array('SD', request()->get('permission_codes'))): ?>
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-power-off me-1 text-danger"></i> Nonaktif</a
                  >
                  <?php endif; ?>
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
    closeCalculator();
    
    $("#jumlahTetapForms").slideUp();
    $("#formulaForms").slideUp();
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
    
    $("#excodetunjangan-container").slideDown();
    $("#excodetunjangan").val('TU' + data.sttunjangankaryawan_id);
    
    // change url
    $("#formTunjangan").attr("action", actionUpdateUrl+"/"+data.sttunjangankaryawan_id+"?menu_id="+currentMenuId);
    // set data
    $("#sttunjangankaryawan_id").val(data.sttunjangankaryawan_id);
    $("#sttunjangankaryawan_name").val(data.sttunjangankaryawan_name);
    $("#sttunjangankaryawan_period").val(data.sttunjangankaryawan_period).trigger('change');
    $("#sttunjangankaryawan_calculation").val(data.sttunjangankaryawan_calculation).trigger('change');
    if(data.stgrouptunjangan)
      $("#sttunjangankaryawan_group").append(new Option(data.stgrouptunjangan.stgrouptunjangankaryawan_name, data.stgrouptunjangan.stgrouptunjangankaryawan_id, true, true)).trigger('change');
    
    // let tunjangandet = (data.sttunjangandetailaktif) ? data.sttunjangandetailaktif : [];
    // console.log('tunjangandet', tunjangandet);
    // if(tunjangandet.length > 0) {
      // let karyawanOpts = '';
      // tunjangandet.forEach(kr => {
      //   karyawanOpts += `<option value="${kr.karyawan.karyawan_id}" selected>${kr.karyawan.karyawan_name}</option>`;
      // })
      // console.log('karyawanOpts', karyawanOpts);
      $("#karyawan-container").slideUp();
      $("#divisi-container").slideUp();
      $("#jabatan-container").slideUp();
      $("#sttunjangankaryawan_employees").val(null).trigger('change');
      $("#sttunjangankaryawan_employees").html('');
      $("#stpotongankaryawan_jabatans").html('');
      $("#stpotongankaryawan_jabatans").val(null).trigger('change');
      $("#stpotongankaryawan_divisis").html('');
      $("#stpotongankaryawan_divisis").val(null).trigger('change');
    // }

    if (data.sttunjangankaryawan_is_all === false) {
      if(data.sttunjangankaryawan_used_for == 0) {
        $("#sttunjangankaryawan_is_all").val(0).trigger('change');
        $("#karyawan-container").slideDown();

        getListKaryawan(data.sttunjangankaryawan_id);
      } else {
        $("#sttunjangankaryawan_is_all").val(data.sttunjangankaryawan_used_for).trigger('change');  
        if(data.sttunjangankaryawan_used_for == 4 || data.sttunjangankaryawan_used_for == 5) {
          if(data.sttunjangankaryawan_used_for == 4) {
            $("#divisi-container").slideDown();
          } else if(data.sttunjangankaryawan_used_for == 5) {
            $("#jabatan-container").slideDown();
          }
          getListDivisiJabatan(data.sttunjangankaryawan_id, data.sttunjangankaryawan_used_for);
        }
      }
    } else {
      $("#sttunjangankaryawan_is_all").val(1).trigger('change');
    }

    console.log('data', data)
    if(data.sttunjangankaryawan_calculation === "FORMULA") {
      $("#jumlahTetapForms").slideUp();
      $("#formulaForms").slideDown();

      loadComponentButtons();
      $("#calculator").show();
      let parseFormula = (data.sttunjangankaryawan_formula) ? JSON.parse(data.sttunjangankaryawan_formula) : [];
      parseFormula.forEach(fm => {
        if (fm.value === 'Delete') {
          $(".formula_txt").children().last().remove();
        } else if (fm.value === 'Reset') {
          $(".formula_txt").html(``);
        } else {
          fm.text = (fm.text) ? fm.text : fm.value;

          $(".formula_txt").append(`<span class="selected-formula" data-value="${fm.value}">${fm.text}</span>`);
        } 
      }) 
    } else {
      closeCalculator();
      $("#jumlahTetapForms").slideDown();
      $("#formulaForms").slideUp();
      
      tunjanganValue.set(data.sttunjangankaryawan_value);
    }

    $("#sttunjangankaryawan_calculation").attr('disabled', true);

    if(data.sttunjangankaryawan_period == 'TAHUN') {
      $(".field-tahun").slideDown();
      $("#sttunjangankaryawan_type").removeAttr("disabled");
      $("#sttunjangankaryawan_type").val(data.sttunjangankaryawan_type).trigger('change');
    } else {
      $(".field-tahun").slideUp();
      $("#sttunjangankaryawan_type").attr("disabled", true);
    }
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
  // End Table Tunjangan

  $("#btn-tunjangan").click(function(e) {
    e.preventDefault();
    $("#sttunjangankaryawan_calculation").attr('disabled', false);
    $("#formTunjangan [type=reset]").click();

    $("#modalTunjangan").modal("show");
  })

  function getListKaryawan(sttunjangankaryawan_id) {
    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      url: `{{route('user.page.pengaturan.tunjangan.listkaryawan', '')}}/${sttunjangankaryawan_id}?menu_id=${currentMenuId}`,
      method: 'get',
      dataType: 'json',
      success: function(res) {
        $(".spinner-box").fadeOut();
        console.log('res', res)
        if(res.data) {
          let karyawans = res.data;
          if(karyawans.length > 0) {
            let karyawanOpts = '';
            karyawans.forEach(kr => {
              karyawanOpts += `<option value="${kr.karyawan_id}" selected>${kr.karyawan_name}</option>`;
            })
            $("#sttunjangankaryawan_employees").html(karyawanOpts);
          }
        }
      }
    })
  }

  function getListDivisiJabatan(sttunjangankaryawan_id, sttunjangankaryawan_used_for) {
    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      url: `{{route('user.page.pengaturan.tunjangan.listdivisijabatan', '')}}/${sttunjangankaryawan_id}?menu_id=${currentMenuId}`,
      method: 'get',
      dataType: 'json',
      success: function(res) {
        $(".spinner-box").fadeOut();
        console.log('res', res)
        if(res.data) {
          let divisi_jabatan = res.data;
          if(divisi_jabatan.length > 0) {
            let divisijabatanOpts = '';
            if(sttunjangankaryawan_used_for == 4) {
              divisi_jabatan.forEach(dj => {
                divisijabatanOpts += `<option value="${dj.karyawandivisi_id}" selected>${dj.karyawandivisi_name}</option>`;
              })
              $("#sttunjangankaryawan_divisis").html(divisijabatanOpts);
            }
            if(sttunjangankaryawan_used_for == 5) {
              divisi_jabatan.forEach(dj => {
                divisijabatanOpts += `<option value="${dj.karyawanjabatan_id}" selected>${dj.karyawanjabatan_name}</option>`;
              })
              $("#sttunjangankaryawan_jabatans").html(divisijabatanOpts);
            }
          }
        }
      }
    })
  }

  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>