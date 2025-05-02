<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">
          <!-- <a class="btn btn-sm btn-outline-warning me-3" id="lock-kalkulasi-karyawan" href="#">
            <span class="bx bx-lock"></span>
            Lock Kalkulasi Karyawan
          </a> -->

          <a class="btn btn-sm btn-outline-info" id="btn-import" href="#">
            <i class='bx bxs-cloud-upload'></i> Import
          </a>

          <a class="btn btn-sm btn-warning rekkaa-page-link" href="{{route('user.page.karyawan.create', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bx-plus'></i> Karyawan
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="text-nowrap">
            <table class="table table-hover display nowrap" id="table-karyawan" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th>NIK</th>
                  <th>NPWP</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>Telepon</th>
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
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalLockKalkulasiKaryawan" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Lock Kalkulasi Karyawan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <form id="formLockKalkulasiKaryawan" method="POST" action="{{route('user.page.karyawan.lockkalkulasi')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
          <div class="row mb-3">
            <label class="col-sm-12 nodot-label" for="lock_karyawan_id">Karyawan</label>
            <div class="col-sm-12">
              <select name="lock_karyawan_id[]" multiple style="width: 100%;" id="lock_karyawan_id" class="form-control" data-placeholder="Masukkan Karyawan"></select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-12 nodot-label" for="lock_periode">Periode</label>
            <div class="col-sm-12">
              <input name="lock_periode" id="lock_periode" class="form-control" placeholder="Masukkan Periode">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-12 nodot-label" for="lock_metode_id">Metode Perhitungan</label>
            <div class="col-sm-12">
              <div class="form-check form-check-inline">
                <input name="lock_metode_id[]" checked class="form-check-input" type="checkbox" value="GROSS" id="lock_metode_id_g">
                <label class="form-check-label" for="lock_metode_id_g">Gross</label>
              </div>
              <div class="form-check form-check-inline">
                <input name="lock_metode_id[]" checked class="form-check-input" type="checkbox" value="GROSS_UP" id="lock_metode_id_gu">
                <label class="form-check-label" for="lock_metode_id_gu">Gross Up</label>
              </div>
              <div class="form-check form-check-inline">
                <input name="lock_metode_id[]" checked class="form-check-input" type="checkbox" value="NETT" id="lock_metode_id_net">
                <label class="form-check-label" for="lock_metode_id_net">Nett</label>
              </div>
              <div class="form-check form-check-inline">
                <input name="lock_metode_id[]" disabled class="form-check-input" type="checkbox" value="MIX" id="lock_metode_id_mix">
                <label class="form-check-label" for="lock_metode_id_mix">MIX</label>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-12 nodot-label" for="lock_status_id">Status Karyawan</label>
            <div class="col-sm-12">
              <select name="lock_status_id[]" multiple style="width: 100%;" id="lock_status_id" class="form-control" data-placeholder="Masukkan Status Karyawan" data-allow-clear="true">
                <option value="">-: Pilih Data :-</option>
                <option value="TETAP">Karyawan Tetap</option>
                <option value="KONTRAK">Karyawan Kontrak</option>
                <option value="NONKARYAWAN">Bukan Karyawan</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-12 text-right">
              <button type="submit" class="btn btn-outline-warning btn-sm"><i class="bx bx-lock"></i> Lock</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImportKaryawan" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Import Profil Karyawan</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form action="{{route('user.page.karyawan.import')}}" method="POST" class="form-horizontal form-lbl-dot" id="formKaryawanImportProfil" autocomplete="off" enctype="multipart/form-data">
            <div class="form-group row mb-3">
              <label for="file_import" class="col-sm-4 lbl-req">Upload Data</label>
              <div class="col-sm-8">
                <input type="file" required name="file_import" id="file_import" class="form-control">
                <span class="help-block"><a href="{{asset('assets/import/Profil_Karyawan.xlsx')}}" download="Rekkaa_Profil_Karyawan">Download Template</a></span>
              </div>
            </div>
            <div class="form-group row mb-3">
              <div class="col-sm-12 text-right">
                <button type="submit" class="btn btn-sm btn-outline-info"><i class='bx bxs-cloud-upload'></i> Import</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    
    // Begin Lock Kalkulasi Karyawan
    $("#lock_karyawan_id").select2({
      dropdownParent: $("#modalLockKalkulasiKaryawan"),
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
    });
    let lockPeriode = moment().format('MM-YYYY');
    // $("#lock_periode").datepicker({
    //   // language: "id-ID",
		// 	format: "MM-yyyy",
    //   startView: "months", 
    //   minViewMode: "months"
		// }).datepicker( "setDate", lockPeriode)
    .on('hide', function(e) {
        // `e` here contains the extra attributes
        let dt = $('#lock_periode').datepicker("getDate");
        // console.log('dt', dt);
        lockPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
    });
    $("#lock_status_id").select2({
      dropdownParent: $("#modalLockKalkulasiKaryawan")
    });
    $("#lock-kalkulasi-karyawan").click(function(e) {
      e.preventDefault();
      $("#modalLockKalkulasiKaryawan").modal("show");
    })

    $("#formLockKalkulasiKaryawan").submit(function(e) {
      e.preventDefault();
      $(".spinner-box").css({'display': 'table'});
      let form = $(this);
      $.ajax({
        method: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize()+"&"+$.param({'lock_periode_format': lockPeriode, _token: $("meta[name=csrf-token]").attr('content')}),
        error: function(error) {
          $(".spinner-box").fadeOut();

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
              confirmButtonText: "Ok",
							showCancelButton: false,
              icon: 'error',
              html: errorName
            })
          }
        },
        success: function(res) {
          // console.log(res)
          $(".spinner-box").fadeOut();
          if(!res.success) {
            toastr.error(res.message);
            return false;
          }

          toastr.success(res.message);
        }
      });
    })
    // End Lock Kalkulasi Karyawan

    let tblKaryawan = $("#table-karyawan").DataTable({
      // "language": {
      //   "infoEmpty": "No records available - Got it?",
      // },
      // "dom": 'flrtip',
      "searching": true,
      "language": {
        "searchPlaceholder": "Cari NIK,NPWP,Nama Karyawan",
        // "emptyTable": "<img src='http://localhost:8000/assets/img/illustrations/man-with-laptop-light.png' style='max-width:20%'><h4 class='mt-2'>No data available in table</h4>",
      },
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": `{{route('user.page.karyawan.datatable', '')}}`,
          "type": "GET",
          "data": function(data) {
            data.menu_id = currentMenuId;
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [6],
        width: 30
      }, {
        target: [0,1,4,5,6],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "karyawan_nik",
          },
          {
              "data": "karyawan_npwp"
          },
          {
              "data": "karyawan_name",
              "render": function(data, type, row) {
                if(row.karyawan_end_type === 'RESIGN') {
                  return `${data} <span class="badge rounded-pill bg-label-warning">Resign<span>`;
                }
                return data;
              }
          },
          {
              "data": "karyawan_address"
          },
          {
              "data": "karyawan_phone"
          },
          {
              "data": "masakerja",
              "render": function(data, type, row) {
                // console.log('data', data)
                let badge = '';
                if(data.karyawanmasakerja_status == 'NONKARYAWAN') {
                  badge = '<span class="badge rounded-pill bg-info">Bukan Karyawan</span>';
                } else if(data.karyawanmasakerja_status == 'TETAP') {
                  badge = '<span class="badge rounded-pill bg-primary">Tetap</span>';
                } else if(data.karyawanmasakerja_status == 'KONTRAK') {
                  badge = '<span class="badge rounded-pill bg-secondary">Kontrak</span>';
                }
                return badge;
              }
          },
          {
            "data": "masakerja",
            "render": function(data, type, row) {
                return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item btn-edit" href="{{route('user.page.karyawan.edit', '')}}/${data.karyawanmasakerja_id}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                  <a class="dropdown-item rekkaa-page-link" href="{{route('user.page.karyawan.edit', '')}}/${data.karyawanmasakerja_id}/kalkulasi"
                    ><i class='bx bxs-user-detail text-primary'></i> Kalkulasi</a
                  >
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Non Aktifkan</a
                  >
                </div>
              </div>
                `
            }
          },
      ],
    });

    $("#table-karyawan").on("click", ".btn-edit", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    // non aktifkan karyawan
    $("#table-karyawan").on("click", ".btn-delete", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblKaryawan.row(row).data();
      Swal.fire({
        html: `Apakah anda ingin menonaktifkan karyawan <b>${data.karyawan_name}</b>?
        <div class="mt-3 text-left">
          <div class="row mb-3">
            <label class="col-sm-5">Tgl. Nonaktif<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <input class="form-control" name="nonaktif_tgl" id="nonaktif_tgl" placeholder="Tgl. Nonaktif" />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Alasan<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <select class="form-control" name="nonaktif_alasan" style="width:100%" id="nonaktif_alasan" data-placeholder="-: Pilih Data :-">
                <option value="RESIGN" selected>Berhenti Kerja</option>
                <option value="LAINNYA">Lainnya</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Keterangan</label>
            <div class="col-sm-7">
              <textarea  class="form-control"name="nonaktif_keterangan" disabled id="nonaktif_keterangan" placeholder="Keterangan"></textarea>
            </div>
          </div>
        </div>
        `,
        icon: 'question',
        didOpen: function () {
          $("#nonaktif_tgl").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: moment().format('DD-MM-YYYY'),
            locale: {
              format: 'DD-MM-YYYY'
            },
          });

          $("#nonaktif_alasan").select2({
            dropdownParent: $('#swal2-html-container')
          }).on("select2:select", function(e) {
            let data = e.params.data;
            console.log('data', data);
            if(data.id == 'LAINNYA') {
              $("#nonaktif_keterangan").removeAttr("disabled");
            } else {
              $("#nonaktif_keterangan").val('');
              $("#nonaktif_keterangan").attr("disabled", true);
            }
          });
        },
        preConfirm: () => {
            Swal.showLoading();
            let nonaktif_tgl = $('#nonaktif_tgl').val();
            let nonaktif_alasan = $('#nonaktif_alasan').val();
            let nonaktif_keterangan = $('#nonaktif_keterangan').val();
            
            if(!nonaktif_tgl || !nonaktif_alasan) {
              toastr.error('Silahkan isi data!');
              return false;
            }
            if(nonaktif_alasan == 'LAINNYA') {
              if(!nonaktif_keterangan) {
                toastr.error('Silahkan isi keterangan!');
                return false;
              }
            }
            return fetch(`{{route('user.page.karyawan.delete', '')}}/${data.masakerja.karyawanmasakerja_id}`, {
                method: 'POST',
                body: new URLSearchParams($.param({
                  _token: $("meta[name=csrf-token]").attr('content'),
                  nonaktif_tgl: nonaktif_tgl,
                  nonaktif_alasan: nonaktif_alasan,
                  nonaktif_keterangan: nonaktif_keterangan,
                }))
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

            // Swal.fire({
            //     title: result.message,
            //     confirmButtonText: "Ok",
						// 	  showCancelButton: false,
            //     type: 'error'
            // })
            toastr.error(result.message);
            return false;
        }

        toastr.success(result.message);
        tblKaryawan.draw();
      });
    })

    // Begin Import Karyawan
    $("#btn-import").click(function(e) {
      e.preventDefault();

      $("#modalImportKaryawan").modal("show");
    })
    let formKaryawanImportProfil = $("#formKaryawanImportProfil").validate({
			errorPlacement: function(error, element) {
				// console.log(element);
				var isInputGroup = $(element).parent();
				console.log('element', element)
				let elem = $(element);
				console.log('elem', elem)
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
				let input = document.getElementById('file_import');

				let formData = new FormData();
				// return false;
				if(input.files.length < 1) {
					toastr.error('Please upload xls/csv');
					return false;
				}
				formData.append('_token', $("meta[name=csrf-token]").attr('content'));
				formData.append('file', input.files[0]);
        $.ajax({
          method: form.method,
          url: form.action,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          },
          processData: false,  // tell jQuery not to process the data
          contentType: false,  // tell jQuery not to set contentType
          data: formData,
					error: function(error) {
						$(".spinner-box").fadeOut();
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
								icon: 'error',
								html: errorName
							})
						}
					}, 
					success: function(response) {
						console.log(response, 'response')
						$(".spinner-box").fadeOut();
						if(!response.success) {
							toastr.error(response.message);
							return false;
						}
						
						toastr.success(response.message);
            
          $("#modalImportKaryawan").modal("hide");
            tblKaryawan.draw()
					}
				})
			},
		})
    // End Import Karyawan

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>