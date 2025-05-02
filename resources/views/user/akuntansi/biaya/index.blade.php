<head>
  <style>
    #table-history th,
    #table-history td,
    #table-history .dataTables_paginate {
      font-size: 12px;
      /* Adjust the font size as desired */
    }
  </style>
</head>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <div class="col-sm-6 text-right">

          @if(in_array('C', request()->get('permission_codes')))
          <a class="btn btn-sm btn-warning rekkaa-page-link" href="{{route('user.page.akuntansi.biaya.create', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bx-plus'></i> Biaya
          </a>
          @endif

        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="text-nowrap">
            <table class="table table-hover display nowrap" id="table-biaya" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th>No. Biaya</th>
                  <th>Tgl. Transaksi</th>
                  <th>No. Bukti</th>
                  <th>Penerima</th>
                  <th>Cara Pembayaran</th>
                  <th>Bayar Dari</th>
                  <th>Total</th>
                  <th>Keterangan</th>
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

<script src="{{asset('assets/js/reload.js')}}"></script>

<script>

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    let tblBiaya = $("#table-biaya").DataTable({
      // "language": {
      //   "infoEmpty": "No records available - Got it?",
      // },
      // "dom": 'flrtip',
      "searching": true,
      "language": {
        "searchPlaceholder": "Cari No. Bukti",
      },
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": `{{route('user.page.akuntansi.biaya.datatable', '')}}`,
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
        target: [8],
        width: 30,
        orderable: false,
      }, {
        target: [0, 1, 2, 4, 7, 8],
        className: 'text-center'
      }],
      "columns": [{
          "data": "akunbiaya_nomor",
        },
        {
          "data": "akunbiaya_period",
        },
        {
          "data": "akunbiaya_nobukti"
        },
        {
          "data": "akunbiaya_id",
        },
        {
          "data": "akunbiaya_id",
        },
        {
          "data": "akunbiaya_id"
        },
        {
          "data": "akunbiaya_total"
        },
        {
          "data": "akunbiaya_description"
        },
        {
          "data": "akunbiaya_id",
          "render": function(data, type, row) {
            return `
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                <?php if (in_array('R', request()->get('permission_codes'))) : ?>
                //   <a class="dropdown-item btn-profile" href="{{route('user.page.karyawan.profile', '')}}/${data}?menu_id=${currentMenuId}"
                //     ><i class="bx bxs-user-detail me-1 text-primary"></i> Detail</a
                //   >
                <?php endif; ?>
                <?php if (in_array('U', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-edit" href="{{route('user.page.karyawan.edit', '')}}/${data}?menu_id=${currentMenuId}"
                    ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
                  >
                <?php endif; ?>
                <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
                  <a class="dropdown-item btn-delete" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1 text-danger"></i> Hapus</a
                  >
                <?php endif; ?>
                </div>
              </div>
                `
          }
        },
      ],
    });

    $("#table-biaya").on("click", ".btn-edit", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    $("#table-biaya").on("click", ".btn-profile", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    // non aktifkan karyawan
    $("#table-biaya").on("click", ".btn-delete", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblBiaya.row(row).data();
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
                <option value="KONTRAK_HABIS">Kontrak Habis</option>
                <option value="LAINNYA">Lainnya</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-5">Keterangan</label>
            <div class="col-sm-7">
              <textarea class="form-control"name="nonaktif_keterangan" id="nonaktif_keterangan" placeholder="Keterangan"></textarea>
            </div>
          </div>
        </div>
        `,
        icon: 'question',
        didOpen: function() {
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
            // console.log('data', data);
            // if(data.id == 'LAINNYA') {
            //   $("#nonaktif_keterangan").removeAttr("disabled");
            // } else {
            //   $("#nonaktif_keterangan").val('');
            //   $("#nonaktif_keterangan").attr("disabled", true);
            // }
          });
        },
        preConfirm: () => {
          Swal.showLoading();
          let nonaktif_tgl = $('#nonaktif_tgl').val();
          let nonaktif_alasan = $('#nonaktif_alasan').val();
          let nonaktif_keterangan = $('#nonaktif_keterangan').val();

          if (!nonaktif_tgl || !nonaktif_alasan) {
            toastr.error('Silahkan isi data!');
            return false;
          }
          // if(nonaktif_alasan == 'LAINNYA') {
          //   if(!nonaktif_keterangan) {
          //     toastr.error('Silahkan isi keterangan!');
          //     return false;
          //   }
          // }
          return fetch(`{{route('user.page.karyawan.delete', '')}}/${data.karyawan_id}?menu_id=${currentMenuId}`, {
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
            .then(jsondata => {
              if (!jsondata.success) {
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
        if (result == undefined) {
          return false;
        }

        if (!result.success) {

          Swal.fire({
            title: result.message,
            confirmButtonText: "Ok",
            showCancelButton: false,
            icon: 'error'
          })
        }

        toastr.success(result.message);
        tblBiaya.draw();
      });
    })

    // Begin Impor Karyawan
    $("#btn-import").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("show");
    })

    $("#btn-import-file").click(function(e) {
      e.preventDefault();

      $("#importModal").modal("hide");
      $("#modalImporKaryawan").modal("show");
    })

    $("#closeImpor").click(function(e) {
      e.preventDefault();
      $("#importModal").modal("show");
      $("#modalImporKaryawan").modal("hide");
    })



    let formKaryawanImporProfil = $("#formKaryawanImporProfil").validate({
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
        $(".spinner-box").css({
          'display': 'table'
        });
        let input = document.getElementById('file_import');

        let formData = new FormData();
        // return false;
        if (input.files.length < 1) {
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
          processData: false, // tell jQuery not to process the data
          contentType: false, // tell jQuery not to set contentType
          data: formData,
          error: function(error) {
            $(".spinner-box").fadeOut();
            if (error.responseJSON) {
              let errs = error.responseJSON.errors;
              let errorName = [];
              if (errs) {
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
            if (!response.success) {
              toastr.error(response.message);
              return false;
            }

            toastr.success(response.message);

            $("#modalImporKaryawan").modal("hide");
            tblBiaya.draw()
          }
        })
      },
    })
    // End Impor Karyawan

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>