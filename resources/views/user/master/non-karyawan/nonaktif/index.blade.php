<div class="row">
    <div class="col-lg-12 mb-4 order-0">
      <!-- Bootstrap Table with Header - Light -->
		<div class="card">
			<div class="card-header d-flex align-items-center justify-content-between">
				<h5 class="mb-0">{{$title}}</h5>
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="{{route('user.page.nonkaryawannonaktif.index')}}" class="rekkaa-page-link">Non Karyawan</a>
						</li>
						<li class="breadcrumb-item active">{{$title}}</li>
					</ol>
				</nav>
			</div>
			<div class="row">
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
		</div>
      <!-- Bootstrap Table with Header - Light -->
    </div>
</div>

<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let tblKaryawan = $("#table-karyawan").DataTable({
      // "dom": 'flrtip',
      "searching": true,
      "language": {
        "searchPlaceholder": "Cari NIK,NPWP,Nama Karyawan",
      },
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "{{route('user.page.nonkaryawannonaktif.datatable')}}",
          "type": "GET",
          "data": function(data) {
            data.menu_id = currentMenuId;
              //     console.log(data); // send data to server
          }
      },
      // "fnInitComplete": function() {
      //     this.fnAdjustColumnSizing(true);
      // },
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
                if(row.karyawan_contract_end) {
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
              "data": "karyawan_status",
              "render": function(data, type, row) {
                console.log('data', data)
                let badge = '';
                if(data == 'NONKARYAWAN') {
                  badge = '<span class="badge rounded-pill bg-info">Bukan Karyawan</span>';
                } else if(data == 'TETAP') {
                  badge = '<span class="badge rounded-pill bg-primary">Tetap</span>';
                } else if(data == 'KONTRAK') {
                  badge = '<span class="badge rounded-pill bg-secondary">Kontrak</span>';
                }
                return badge;
              }
          },
          {
            "data": "karyawan_id",
            "render": function(data, type, row) {
              console.log('data', data)
                return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item btn-view rekkaa-page-link" href="{{route('user.page.nonkaryawannonaktif.profile', '')}}/${data}?menu_id=${currentMenuId}"
                    ><i class="bx bxs-user-detail me-1 text-warning"></i> Profil</a
                  >
                  <?php if(in_array('ACTIVATE', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-active" href="javascript:void(0);"
                    ><i class="bx bx-user-check me-1 text-success"></i> Aktifkan</a
                  >
                  <?php endif; ?>
                </div>
              </div>
                `
            }
          },
      ],
    });

    // $("#table-karyawan").on("click", ".btn-edit", function(e) {
    //   e.preventDefault();
    //   let href = $(this).attr('href');
    //   loadPage(href);
    // })

    // non aktifkan karyawan
    $("#table-karyawan").on("click", ".btn-active", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblKaryawan.row(row).data();
      Swal.fire({
        html: `Apakah anda ingin mengaktifkan karyawan <b>${data.karyawan_name}</b>?
        <div class="mt-3 text-left" id="activate-nonkaryawan">
          <div class="row mb-3">
            <label class="col-sm-5">Tgl. Aktif<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <input class="form-control" name="aktif_tgl" id="aktif_tgl" placeholder="Tgl. Aktif" />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Tipe Kontrak<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <select class="form-control" name="tipe_kontrak" style="width:100%" id="tipe_kontrak" data-placeholder="-: Pilih Data :-">
                <option value="BARU" selected>Baru</option>
                <option value="PERPANJANG">Perpanjang</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Status<span class="text-danger">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <select class="form-control" name="aktif_status" style="width:100%" id="aktif_status" data-placeholder="-: Pilih Data :-">
                <option value="TETAP">Tetap</option>
                <option value="KONTRAK">Kontrak</option>
                <option value="PERCOBAAN">Percobaan</option>
                <option value="NONKARYAWAN" selected>Non Karyawan</option>
              </select>
            </div>
          </div>
          <div class="row" id="akhir_tgl_box">
            <label class="col-sm-5">Tgl. Berakhir Kontrak<span class="text-danger required-tgl-akhir">*</span><span class="float-right">:</span></label>
            <div class="col-sm-7">
              <input class="form-control" name="akhir_tgl" id="akhir_tgl" placeholder="Tgl. Berakhir Kontrak" />
            </div>
          </div>
        </div>
        `,
        icon: 'question',
        didOpen: function () {
          $(".swal2-actions").css({
            marginBottom: "11rem",
            zIndex: 0,
          })
          $("#aktif_tgl").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            parentEl: "#activate-nonkaryawan",
            // maxDate: moment().format('DD-MM-YYYY'),
            locale: {
              format: 'DD-MM-YYYY'
            }
          });
          $("#akhir_tgl").daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            parentEl: "#activate-nonkaryawan",
            locale: {
              format: 'DD-MM-YYYY'
            }
          }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
          });
          
          $("#aktif_status").select2({
            dropdownParent: $('#swal2-html-container')
          }).on("select2:select", function(e) {
            let data = e.params.data;
            if(data.id == 'TETAP' || data.id == 'NONKARYAWAN') {
              $("#akhir_tgl").removeAttr("required");
              $("#akhir_tgl_box").hide();
              // $(".required-tgl-akhir").hide();
            } else {
              $("#akhir_tgl").attr("required", true);
              $("#akhir_tgl_box").show();
              // $(".required-tgl-akhir").show();
            }
          });

          $("#tipe_kontrak").select2({
            dropdownParent: $('#swal2-html-container')
          });
        },
        preConfirm: () => {
            Swal.showLoading();
            let aktif_tgl = $('#aktif_tgl').val();
            let aktif_status = $('#aktif_status').val();
            let akhir_tgl = $('#akhir_tgl').val();
            let tipe_kontrak = $('#tipe_kontrak').val();
            
            if(!aktif_tgl || !aktif_status) {
              toastr.error('Silahkan isi data!');
              return false;
            }
            return fetch(`{{route('user.page.nonkaryawannonaktif.activate', '')}}/${data.karyawan_id}?menu_id=${currentMenuId}`, {
                method: 'POST',
                body: new URLSearchParams($.param({
                  _token: $("meta[name=csrf-token]").attr('content'),
                  aktif_tgl: aktif_tgl,
                  akhir_tgl: akhir_tgl,
                  aktif_status: aktif_status,
                  tipe_kontrak: tipe_kontrak,
                }))
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(res => {
                        throw new Error(res);
                    })
                }
                return response.json()
            }).then(jsondata => {
              if(!jsondata.success) {
                toastr.error(jsondata.message);
                return false;
              }
              return jsondata;
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
            confirmButtonText: "Ok",
            showCancelButton: false,
            icon: 'error'
          })
          return false;
        }

        toastr.success(result.message);
        tblKaryawan.draw();
      });
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>