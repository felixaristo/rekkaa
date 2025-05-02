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
            <a class="btn btn-sm btn-warning" id="btn-potongan" href="#">
              <i class='bx bx-plus'></i> Potongan
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="text-nowrap">
          <table class="table table-hover display nowrap" style="width: 100%" id="table-pengaturan-potongan">
            <thead class="table-light">
              <tr>
                <th>Kode Potongan</th>
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
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

@include('user.pengaturan.potongan.create')

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let actionUpdateUrl = "{{route('user.page.pengaturan.potongan.update', '')}}";
    let actionDeleteUrl = "{{route('user.page.pengaturan.potongan.delete', '')}}";
    
    // Begin Table Wajib Pajak
    let tblPengaturanPotongan = $("#table-pengaturan-potongan").DataTable({
      // "filtering": false,
      "ordering": true,
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [[0, 'asc']], //Initial no order.
      "searchDelay": 1050,
      "language": {
        // "emptyTable": "Tidak ada data"
        "searchPlaceholder": "Cari Nama / Kode Potongan",
      },
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
        target: [6],
        width: 30,
        orderable: false
      }, {
        target: [0,1,2,3,4,5,6],
        className: 'text-center'
      }],
      "columns": [
          {
            "data": "stpotongankaryawan_id",
            "title": "Kode Potongan",
            "render": function(data, type, row) {
              if (data && (type === 'display' || type === 'filter')) {
                return 'PO' + data;
              }
              return data;
            }
          },
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
                <?php if(in_array('U', request()->get('permission_codes'))): ?>
                  <a class="dropdown-item btn-edit" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
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
      ],
    });
    
  $("#table-pengaturan-potongan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    // $(".spinner-box").css({'display': 'table'});

    $("#formPotongan [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblPengaturanPotongan.row(row).data();
    let stpotongankaryawan_id = data.stpotongankaryawan_id;

    $("#excodepotongan-container").slideDown();
    $("#excodepotongan").val('PO' + stpotongankaryawan_id);

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
    
    // let potongandet = (data.stpotongandetailaktif) ? data.stpotongandetailaktif : [];
    // console.log('potongandet', potongandet);
    // if(potongandet.length > 0) {
    //   let karyawanOpts = '';
    //   potongandet.forEach(kr => {
    //     karyawanOpts += `<option value="${kr.karyawan.karyawan_id}" selected>${kr.karyawan.karyawan_name}</option>`;
    //   })
    //   console.log('karyawanOpts', karyawanOpts);
    //   $("#stpotongankaryawan_employees").html(karyawanOpts);
    // }
    $("#karyawan-container").slideUp();
    $("#divisi-container").slideUp();
    $("#jabatan-container").slideUp();
    $("#stpotongankaryawan_employees").val(null).trigger('change');
    $("#stpotongankaryawan_employees").html('');
    $("#stpotongankaryawan_jabatans").html('');
    $("#stpotongankaryawan_jabatans").val(null).trigger('change');
    $("#stpotongankaryawan_divisis").html('');
    $("#stpotongankaryawan_divisis").val(null).trigger('change');

    if (data.stpotongankaryawan_is_all === false) {
      if(data.stpotongankaryawan_used_for == 0) {
        $("#stpotongankaryawan_is_all").val(0).trigger('change');
        $("#karyawan-container").slideDown();

        getListKaryawan(data.stpotongankaryawan_id);

      } else {
        $("#stpotongankaryawan_is_all").val(data.stpotongankaryawan_used_for).trigger('change');  
        if(data.stpotongankaryawan_used_for == 4 || data.stpotongankaryawan_used_for == 5) {
          if(data.stpotongankaryawan_used_for == 4) {
            $("#divisi-container").slideDown();
          } else if(data.stpotongankaryawan_used_for == 5) {
            $("#jabatan-container").slideDown();
          }
          getListDivisiJabatan(data.stpotongankaryawan_id, data.stpotongankaryawan_used_for);
        }
      }
    } else {
      $("#stpotongankaryawan_is_all").val(1).trigger('change');
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

  $("#modalPotongan").on("hide.bs.modal", function () {
    tblPengaturanPotongan.draw(); // Refresh the DataTable
  });

  let pengaturanGroupReadyDraw = false;
    
  // $("#btn-group").click(function(e) {
  //   e.preventDefault();

  //   $("#box-tablegroup").hide();
  //   $("#modalGroup").modal("show");
  // })
  // $("#btn-lihatgroup").click(function(e) {
  //   e.preventDefault();

  //   $("#box-tablegroup").show();
  //   $("#modalGroup").modal("show");
  // })

  function getListKaryawan(stpotongankaryawan_id) {
    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      url: `{{route('user.page.pengaturan.potongan.listkaryawan', '')}}/${stpotongankaryawan_id}?menu_id=${currentMenuId}`,
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
            $("#stpotongankaryawan_employees").html(karyawanOpts);
          }
        }
      }
    })
  }

  function getListDivisiJabatan(stpotongankaryawan_id, stpotongankaryawan_used_for) {
    $(".spinner-box").css({'display': 'table'});
    $.ajax({
      url: `{{route('user.page.pengaturan.potongan.listdivisijabatan', '')}}/${stpotongankaryawan_id}?menu_id=${currentMenuId}`,
      method: 'get',
      dataType: 'json',
      success: function(res) {
        $(".spinner-box").fadeOut();
        console.log('res', res)
        if(res.data) {
          let divisi_jabatan = res.data;
          if(divisi_jabatan.length > 0) {
            let divisijabatanOpts = '';
            if(stpotongankaryawan_used_for == 4) {
              divisi_jabatan.forEach(dj => {
                divisijabatanOpts += `<option value="${dj.karyawandivisi_id}" selected>${dj.karyawandivisi_name}</option>`;
              })
              $("#stpotongankaryawan_divisis").html(divisijabatanOpts);
            }
            if(stpotongankaryawan_used_for == 5) {
              divisi_jabatan.forEach(dj => {
                divisijabatanOpts += `<option value="${dj.karyawanjabatan_id}" selected>${dj.karyawanjabatan_name}</option>`;
              })
              $("#stpotongankaryawan_jabatans").html(divisijabatanOpts);
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