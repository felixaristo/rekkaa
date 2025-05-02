<style>
  .tahun-kontrak-box {
    width: 200px;
  }
</style>
<?php 
  $karyawan_status = $karyawanmasakerja->karyawanmasakerja_status;
  if($karyawanmasakerja->karyawanmasakerja_status == 'NONKARYAWAN') {
    $karyawan_status = 'Bukan Karyawan';
  }
  $contract_begin = \Carbon\Carbon::parse($karyawanmasakerja->karyawanmasakerja_contract_begin);
  $contract_begin_dt = $contract_begin->format('d-m-Y');
  $contract_end = ($karyawanmasakerja->karyawanmasakerja_contract_end == null) ? null : \Carbon\Carbon::parse($karyawanmasakerja->karyawanmasakerja_contract_end);
  $contract_end_dt = ($karyawanmasakerja->karyawanmasakerja_contract_end == null) ? 'Sekarang' : $contract_end->format('d-m-Y');
?>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">{{$title}}</h5>
        <!-- <small class="text-muted float-end">Merged input group</small> -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{route('user.page.karyawan.index')}}" class="rekkaa-page-link">Karyawan</a>
            </li>
            <li class="breadcrumb-item active">Kalkulasi</li>
          </ol>
        </nav>
      </div>
      <div class="card-body">
        <div class="col-sm-12 mb-5">
          <div class="card">
            <div class="card-body">
              <div class="form-group row mb-3">
                <label for="karyawan_nik" class="col-sm-2 lbl-req">NIK<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawanmasakerja->karyawan_nik}}
                </div>
                <label for="karyawan_npwp" class="col-sm-2 lbl-req">NPWP<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawanmasakerja->karyawan_npwp}}
                </div>
              </div>
              <div class="form-group row mb-3">
                <label for="karyawan_name" class="col-sm-2 lbl-req">Nama<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawanmasakerja->karyawan_name}}
                </div>
                <label for="karyawan_email" class="col-sm-2 lbl-req">Email<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawanmasakerja->karyawan_email}}
                </div>
              </div>
              <div class="form-group row mb-3">
                <label for="karyawan_status" class="col-sm-2 lbl-req">Status Karyawan<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawan_status}}
                </div>
                <label for="karyawan_metode" class="col-sm-2 lbl-req">Metode Perhitungan<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{ucwords(strtolower(str_replace('_', ' ', $karyawanmasakerja->karyawanmasakerja_calculation_method)))}}
                </div>
              </div>
              <div class="form-group row mb-3">
                <label for="karyawan_kontrak" class="col-sm-2 lbl-req">Kontrak<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$contract_begin_dt}} - {{$contract_end_dt}}
                </div>
                <label for="karyawan_ptkp" class="col-sm-2 lbl-req">Status Perkawinan<span class="float-right">:</span></label>
                <div class="col-sm-4">
                {{$karyawanmasakerja->ptkp_description}}
                </div>
              </div>
              <div class="form-group row mb-3">
                <label for="karyawan_kontrak" class="col-sm-2 lbl-req">Objek Pajak<span class="float-right">:</span></label>
                <div class="col-sm-10">
                <b>[{{$karyawanmasakerja->objekpajak_code}}]</b> {{$karyawanmasakerja->objekpajak_description}}
                </div>
              </div>
			  @if($karyawanmasakerja->karyawanmasakerja_active == '0')
				<div class="form-group row mb-3">
					<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Alasan (Non Aktif)<span class="float-right">:</span></label>
					<div class="col-sm-4">
					<span class="badge rounded-pill bg-label-warning">{{$karyawanmasakerja->karyawanmasakerja_end_type}}</span>
					</div>
					<label for="karyawan_kontrak" class="col-sm-2 lbl-req">Keterangan (Non Aktif)<span class="float-right">:</span></label>
					<div class="col-sm-4">
					{{$karyawanmasakerja->karyawanmasakerja_end_reason}}
					</div>
				</div>
				@endif
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <form id="formKalkulasi" method="POST" action="{{url('user/karyawan/'.request()->segment(3).'/kalkulasi/locknonkaryawan')}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
            <div class="row mb-3">
              <label class="col-sm-3 lbl-req" for="nonkaryawan_tgltrx">Tgl. Transaksi</label>
              <div class="col-sm-4">
                <input type="text" required name="nonkaryawan_tgltrx" id="nonkaryawan_tgltrx" class="form-control" placeholder="Masukkan Tgl. Transaksi">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-3" for="nonkaryawan_pendapatankotorsebelumnya">Akumulasi Pendapatan Kotor Sebelumnya</label>
              <div class="col-sm-4">
                <input type="text" disabled value="0" name="nonkaryawan_pendapatankotorsebelumnya" id="nonkaryawan_pendapatankotorsebelumnya" class="form-control" placeholder="Masukkan Akumulasi Pendapatan Kotor Sebelumnya">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-3 lbl-req" for="nonkaryawan_pendapatankotor">Pendapatan Kotor</label>
              <div class="col-sm-4">
                <input type="text" required name="nonkaryawan_pendapatankotor" id="nonkaryawan_pendapatankotor" class="form-control" placeholder="Masukkan Pendapatan Kotor">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-3" for="nonkaryawan_pendapatanbersih">Pendapatan Bersih / Netto</label>
              <div class="col-sm-4">
                <input type="text" disabled name="nonkaryawan_pendapatanbersih" id="nonkaryawan_pendapatanbersih" class="form-control" value="0">
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-3" for="nonkaryawan_pajak">Total Pajak</label>
              <div class="col-sm-4">
                <input type="text" disabled name="nonkaryawan_pajak" id="nonkaryawan_pajak" class="form-control" value="0">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-7 text-right">
                <button type="reset" class="btn btn-outline-danger btn-sm">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
              </div>
            </div>
          </form>
          <div class="text-nowrap table-responsive">
            <table class="table" id="table-kalkulasi" style="width: 100%">
              <thead class="table-light">
                <tr>
                  <th class="text-center">Tgl. Transaksi</th>
                  <th class="text-center">Pendapatan Kotor</th>
                  <th class="text-center">Total Pajak</th>
                  <th class="text-center">Actions</th>
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

<script>
  $(function() {
    let tarif21 = JSON.parse('<?php echo json_encode($tarif21)  ?>');
    let optionAutoNumeric = {
		digitGroupSeparator: '.',
		decimalCharacter: ',',
		currencySymbolPlacement: 'p',
		currencySymbol: 'Rp. ',
		minimumValue: 0,
		unformatOnSubmit: true,
		decimalPlaces: '0',
    modifyValueOnWheel: false,
    };
	let pendapatannettoSebelumnya = 0;
    let [pendapatankotorSebelumnya, penghasilanBruto, penghasilanNetto
      , perhitunganTotalPPHTerutang] = new AutoNumeric.multiple(
          ["#nonkaryawan_pendapatankotorsebelumnya", "#nonkaryawan_pendapatankotor", "#nonkaryawan_pendapatanbersih"
          , "#nonkaryawan_pajak"]
          , optionAutoNumeric
      );
    
      $("#nonkaryawan_tgltrx").daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
	//   timePicker: true,
      // maxDate: moment().format('DD-MM-YYYY'),
      locale: {
        format: 'DD-MM-YYYY'
      }
    });

    $("#nonkaryawan_pendapatankotor").keyup(function(e) {
      e.preventDefault();
      // penghasilan
      let penghasilanNettoTotal = penghasilanBruto.getNumber() * 50 / 100;
  
      penghasilanNetto.set(penghasilanNettoTotal);
      perhitunganTotal();
    })
    
    let tblKalkulasi = $("#table-kalkulasi").DataTable({
      // "sDom": "l<'tahun-kontrak-box'>tipr",
      // "pageLength": 12,
      // "lengthMenu": [ 12 ],
      "searching": false,
      "ordering": false,
      // "paging": false,
      // "info": false,
      "cache": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "<?php echo url('user/karyawan/'.request()->segment(3).'/kalkulasi/datatable_nonkaryawan') ?>",
          "type": "GET",
          "data": function(data) {
            // data.tahun = ($("#table-kalkulasi_wrapper #tahun-kontrak-select").val()) ? $("#table-kalkulasi_wrapper #tahun-kontrak-select").val() : tahunKontrakAkhir;
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      },
      "drawCallback": function( settings ) {
        let api = this.api();

        // Output the data for the visible rows to the browser's console
        console.log( api.rows( {page:'current'} ).data() );
		let dt = api.rows( {page:'current'} ).data();
		
        if(dt[0] != undefined) {
        //   console.log('dt.eq(0).karyawankalkulasi_bruto_total', dt[0])
			let akumulasi = JSON.parse(dt[0].karyawankalkulasi_akumulasi);
			pendapatankotorSebelumnya.set(akumulasi.karyawankalkulasi_bruto_total);
			pendapatannettoSebelumnya = akumulasi.karyawankalkulasi_bruto_total / 2;
        } else {
			pendapatankotorSebelumnya.set(0);
			pendapatannettoSebelumnya = 0;
		}
      },
      "autoWidth": true,
      "columnDefs": [{
        target: [3],
        width: 30
      }, {
        target: [0,3],
        className: 'text-center'
      }, {
        target: [1,2],
        className: 'text-right'
      }],
      "columns": [
        {
            "data": "karyawankalkulasi_lock_at",
            "render": function(data, type, row) {
              return moment(data).format('DD-MM-YYYY');
            }
        },
        {
            "data": "karyawankalkulasi_bruto",
            "render": function(data, type, row) {
              return formatCurrency(data);
            }
        },
        {
            "data": "karyawankalkulasi_pph21",
            "render": function(data, type, row) {
              return formatCurrency(data);
            }
        },
        {
            "data": "karyawankalkulasi_id",
            "render": function(data, type, row) {
              let lockHtml = '';
              if(!row.karyawankalkulasi_lock) {
                lockHtml = `<a class="dropdown-item btn-kalkulasi-lock" href="#"
                    ><i class="bx bx-lock-alt me-1 text-primary"></i> Lock Kalkulasi</a>`;
              } else {
                lockHtml = `<a class="dropdown-item btn-kalkulasi-lock" href="#"
                    ><i class="bx bx-lock-open-alt me-1 text-danger"></i> Unlock Kalkulasi</a>`;
              }
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
                `;
            }
        },
      ]
    });

    $("#table-kalkulasi").on("click", ".btn-edit", function(e) {
		e.preventDefault();
		// $("#formKalkulasi [type=reset]").click();
		// get row
		let row = $(this).closest('tr');
		let rowNext = $(row).next('tr');
		let data = tblKalkulasi.row(row).data();
		let dataNext = tblKalkulasi.row(rowNext).data();
		console.log('dataNext', dataNext)
		// console.log('data.karyawankalkulasi_bruto', data.karyawankalkulasi_bruto)
		let dataKalParse = JSON.parse(data.karyawankalkulasi_data);
		// change url
		$("#formKalkulasi").attr("action", "{{url('/user/karyawan/'.request()->segment(3).'/kalkulasi/locknonkaryawanupdate')}}"+"/"+data.karyawankalkulasi_id);
		// set data
		$("#nonkaryawan_tgltrx").val(data.karyawankalkulasi_lock_at);
		penghasilanBruto.set(data.karyawankalkulasi_bruto);
		penghasilanNetto.set(data.karyawankalkulasi_bruto / 2);
		pendapatankotorSebelumnya.set(dataKalParse.nonkaryawan_pendapatankotor_sebelumnya);
		perhitunganTotalPPHTerutang.set(data.karyawankalkulasi_pph21);

		if(dataNext) {
			Swal.fire({
				html: 'Apakah anda yakin mengedit data ini? <br><span class="text-danger">Data berikutnya harus disesuaikan ulang secara manual!</span>',
				icon: 'question',
				allowOutsideClick: () => false
			}).then((result) => {
				// console.log('result', result.isConfirmed)
				if(!result.isConfirmed) {
					$("#formKalkulasi [type=reset]").click();
				}
			});
			$("#nonkaryawan_pendapatankotorsebelumnya").removeAttr('disabled');
		} else {
			$("#nonkaryawan_pendapatankotorsebelumnya").attr('disabled', true);
		}
	})
	
    let formKalkulasi = $("#formKalkulasi").validate({
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
				data: $(form).serialize()+"&"+$.param({_token: $("meta[name=csrf-token]").attr('content')}),
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
						confirmButtonText: "Ok",
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
						formKalkulasi.showErrors({
							email: response.message
						})
						return false;
					}
					
					$("#formKalkulasi [type=reset]").click();
					toastr.success(response.message);
					tblKalkulasi.draw();

					// change url
					// $("#formKalkulasi").attr("action", "{{route('user.page.pengaturan.tunjangan.store')}}");
				}
			})
		},
    })

	$("#table-kalkulasi").on("click", ".btn-delete", function(e) {
		e.preventDefault();
		let row = $(this).closest('tr');
		let rowNext = $(row).next('tr');
		let data = tblKalkulasi.row(row).data();
		let dataNext = tblKalkulasi.row(rowNext).data();

		let infoHtml = (dataNext) ? ' <br><span class="text-danger">Data berikutnya harus disesuaikan ulang secara manual!</span>' : '';
		Swal.fire({
			html: 'Apakah anda ingin menghapus kalkulasi ini?'+infoHtml,
			icon: 'question',
			preConfirm: () => {
				Swal.showLoading();
				// tblPengaturanTunjangan.row(row).remove();
				// return true;
				return fetch(`{{url('/user/karyawan/'.request()->segment(3).'/kalkulasi/locknonkaryawandelete')}}/${data.karyawankalkulasi_id}`, {
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
					title: result.message,
					confirmButtonText: "Ok",
					type: 'error'
				})
				return false;
			}

			toastr.success(result.message);
			tblKalkulasi.draw();
		});
	})

    $("#formKalkulasi [type=reset]").click(function(e) {
		e.preventDefault();
    //   // change url
      $("#formKalkulasi").attr("action", "{{url('user/karyawan/'.request()->segment(3).'/kalkulasi/locknonkaryawan')}}");
		penghasilanNetto.set(0);
		penghasilanBruto.set(0);
		pendapatankotorSebelumnya.set(0);
		perhitunganTotalPPHTerutang.set(0);
		$("#nonkaryawan_tgltrx").val(moment().format('DD-MM-YYYY'));
		$("#nonkaryawan_pendapatankotorsebelumnya").attr('disabled', true);
		tblKalkulasi.draw();
    })

    function perhitunganTotal() {
		let penghasilanNettoAkumulasi = pendapatankotorSebelumnya.getNumber() + penghasilanBruto.getNumber();
		console.log('penghasilanNettoAkumulasi', penghasilanNettoAkumulasi);
		let tarif21Rates = getTarif21(tarif21, penghasilanNettoAkumulasi)
		let totalPKP = penghasilanNetto.getNumber() + parseInt(pendapatannettoSebelumnya);
		let totalPPHTerutang = 0;
		// console.log('tarif21Rates', tarif21Rates)
		// console.log('totalPKP', totalPKP)
		tarif21Rates.forEach(rate => {
			if(rate.tarif21_endincome > totalPKP) {
				totalPPHTerutang += totalPKP * rate.tarif21_rate / 100;
			} else {
				totalPPHTerutang += rate.tarif21_endincome * rate.tarif21_rate / 100;
				totalPKP -= rate.tarif21_endincome;
			}
			// console.log('totalPPHTerutangxx',totalPPHTerutang)
		});
		perhitunganTotalPPHTerutang.set(totalPPHTerutang);
	}

	// set meta title
	setHtmlTitle('{{$title}}')
  })
  
</script>