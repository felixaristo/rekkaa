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
          <a class="btn btn-sm btn-warning rekkaa-page-link" href="{{route('user.page.akuntansi.jurnalumum.create', ['menu_id' => request()->get('menu_id')])}}">
            <i class='bx bx-plus'></i> Posting
          </a>
          @endif

        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="text-nowrap">
            <table class="table table-hover display nowrap" id="table-jurnal" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th>No. Jurnal</th>
                  <th>Tgl</th>
                  <th>No. Bukti</th>
                  <th>Keterangan</th>
                  <th>Nominal</th>
                  <th>Tipe Jurnal</th>
                  <th>Posting</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr>
                  <td>JU-2287</td>
                  <td>2024-05-27 23:06:09</td>
                  <td>BK-36</td>
                  <td>Biaya dibayar kepada MBAK SITI RUJAK (tes gani)</td>
                  <td>600.000</td>
                  <td>BIAYA</td>
                  <td><span class="text-danger">Belum</span></td>
                  <td><button class="btn btn-info btn-sm">Detail</button></td>
                </tr>
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
    let tblJurnal = $("#table-jurnal").DataTable({
      // "language": {
      //   "infoEmpty": "No records available - Got it?",
      // },
      // "dom": 'flrtip',
      "searching": true,
      "language": {
        "searchPlaceholder": "Cari No. Bukti",
      },
      // "processing": true, //Feature control the processing indicator.
      // "serverSide": true, //Feature control DataTables' server-side processing mode.
      // "order": [], //Initial no order.
      // "searchDelay": 1050,
      // // Load data for the table's content from an Ajax source
      // "ajax": {
      //   "url": `{{route('user.page.akuntansi.jurnalumum.datatable', '')}}`,
      //   "type": "GET",
      //   "data": function(data) {
      //     data.menu_id = currentMenuId;
      //     //     console.log(data); // send data to server
      //   }
      // },
      // "fnInitComplete": function() {
      //   // this.fnAdjustColumnSizing(true);
      //   // $(this).find(".cetak-registrasi").select2();
      // },
      // "autoWidth": true,
      // "columnDefs": [{
      //   target: [8],
      //   width: 30,
      //   orderable: false,
      // }, {
      //   target: [0, 1, 2, 4, 7, 8],
      //   className: 'text-center'
      // }],
      // "columns": [{
      //     "data": "akunjurnal_nomor",
      //   },
      //   {
      //     "data": "akunjurnal_period",
      //   },
      //   {
      //     "data": "akunjurnal_nobukti"
      //   },
      //   {
      //     "data": "akunjurnal_id",
      //   },
      //   {
      //     "data": "akunjurnal_id",
      //   },
      //   {
      //     "data": "akunjurnal_id"
      //   },
      //   {
      //     "data": "akunjurnal_total"
      //   },
      //   {
      //     "data": "akunjurnal_description"
      //   },
      //   {
      //     "data": "akunjurnal_id",
      //     "render": function(data, type, row) {
      //       return `
      //           <div class="dropdown">
      //           <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      //             <i class="bx bx-dots-vertical-rounded"></i>
      //           </button>
      //           <div class="dropdown-menu">
      //           <?php if (in_array('R', request()->get('permission_codes'))) : ?>
      //           //   <a class="dropdown-item btn-profile" href="{{route('user.page.karyawan.profile', '')}}/${data}?menu_id=${currentMenuId}"
      //           //     ><i class="bx bxs-user-detail me-1 text-primary"></i> Detail</a
      //           //   >
      //           <?php endif; ?>
      //           <?php if (in_array('U', request()->get('permission_codes'))) : ?>
      //             <a class="dropdown-item btn-edit" href="{{route('user.page.karyawan.edit', '')}}/${data}?menu_id=${currentMenuId}"
      //               ><i class="bx bx-edit-alt me-1 text-info"></i> Edit</a
      //             >
      //           <?php endif; ?>
      //           <?php if (in_array('SD', request()->get('permission_codes'))) : ?>
      //             <a class="dropdown-item btn-delete" href="javascript:void(0);"
      //               ><i class="bx bx-trash me-1 text-danger"></i> Hapus</a
      //             >
      //           <?php endif; ?>
      //           </div>
      //         </div>
      //           `
      //     }
      //   },
      // ],
    });

    $("#table-jurnal").on("click", ".btn-edit", function(e) {
      e.preventDefault();
      let href = $(this).attr('href');
      loadPage(href);
    })

    // non aktifkan karyawan
    $("#table-jurnal").on("click", ".btn-delete", function(e) {
      e.preventDefault();
      let row = $(this).closest('tr');
      let data = tblJurnal.row(row).data();
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
        tblJurnal.draw();
      });
    })

    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>