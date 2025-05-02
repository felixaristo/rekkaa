<div class="accordion mb-4" id="accordionExample">
  <div class="card accordion-item active">
    <h2 class="accordion-header" id="headingFilter">
      <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse" role="tabpanel">
        Filter
      </button>
    </h2>

    <div id="filterCollapse" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <!-- Start Date and End Date inputs here -->
        <div class="row align-items-center">
          <div class="col-md-3">
            <label for="filterPeriode">Periode</label>
            <input type="text" id="filterPeriode" name="filterPeriode" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3">
            <label for="filterWajibPajak">Wajib Pajak</label>
            <select style="width: 100%;" name="filterWajibPajak" id="filterWajibPajak" class="form-control" data-placeholder=" Pilih Wajib Pajak " autocomplete="off"></select>
          </div>
					<div class="col-md-4">
            <label for="filterStPenggajian">Setting Penggajian</label>
            <select style="width: 100%;" name="filterStPenggajian" id="filterStPenggajian" class="form-control" data-placeholder=" Pilih Penggajian " autocomplete="off"></select>
          </div>
          <div class="col-md-2 mt-md-4 mt-sm-3 text-md-start text-sm-end">
            <button id="calculateButton" class="btn btn-calculate btn-outline-warning me-2 btn-sm">
              <i class="bx bx-refresh"></i> Kalkulasi
            </button>
            <!-- <button id="resetButton" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
              <i class="bx bx-reset"></i> Reset
            </button> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    $(function() {
			let filterPeriode = moment().format('MM-YYYY');
			$("#filterPeriode").datepicker({
			language: "id-ID",
			format: "MM-yyyy",
			startView: "months", 
			minViewMode: "months"
			}).datepicker( "setDate", filterPeriode)
			.on('hide', function(e) {
					// `e` here contains the extra attributes
					let dt = $('#filterPeriode').datepicker("getDate");
					filterPeriode = (dt) ? moment(dt).format('MM-YYYY') : moment().format('MM-YYYY');
			});
			
			$("#filterWajibPajak").select2({
				ajax: {
					url: `{{route('admin.calculate.payslip.selectwajibpajak')}}`,
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
							item.id = item.wajibpajak_id;
							item.text = item.wajibpajak_name;
							// item.info = item;
							// console.log('item.kode', item)
							return item
						})
						return {
							results: items
						};
					},
				},
			}).on("select2:select", function(e) {
				$("#filterStPenggajian").val(null).trigger('change');	
			})

			$("#filterStPenggajian").select2({
				ajax: {
					url: `{{route('admin.calculate.payslip.selectstpenggajian')}}`,
					data: function (params) {
						var query = {
							q: params.term,
							wajibpajak_id: $("#filterWajibPajak").val(),
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
							item.id = item.stpenggajiankaryawan_id;
							item.text = item.stpenggajiankaryawan_name;
							// item.info = item;
							// console.log('item.kode', item)
							return item
						})
						return {
							results: items
						};
					},
				},
				templateResult: formatResult, // Custom function for displaying results
				templateSelection: formatResult, // Custom function for displaying selected item
			})

			$("#calculateButton").click(function(e) {
				e.preventDefault();
				let stpenggajian_id = $("#filterStPenggajian").val();
				if(!stpenggajian_id) {
					toastr.error('Silahkan pilih setting penggajian!');
					return false;
				}

				Swal.fire({
					html: 'Apakah anda yakin melakukan kalkulasi pada data penggajian ini?',
					icon: 'question',
					preConfirm: () => {
						Swal.showLoading();
						// tblPengaturanPotongan.row(row).remove();
						// return true;
						return fetch("{{route('admin.calculate.payslip.calculate')}}", {
								method: 'POST',
								body: new URLSearchParams($.param({
									_token: $("meta[name=csrf-token]").attr('content'),
									stpenggajian_id: stpenggajian_id,
									periode: filterPeriode
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
					if (result == undefined) {
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
					$("#filterStPenggajian").val(null).trigger('change');
				});
			});

			function formatResult(state) {
				// console.log('state', $(state.element).attr('data-price'))
				// let price = $(state.element).attr('data-price');
				if (!state.id) {
						return state.text;
				}
				console.log('state', state)
				$stateCustom = $(
						`<span>${state.text}<br><b>${state.wajibpajak.wajibpajak_name}<br>${state.wajibpajak.wajibpajak_npwp}</b></span>`
				);
				// // console.log('stateCustom', $stateCustom)

				return $stateCustom;
		}
    })
</script>