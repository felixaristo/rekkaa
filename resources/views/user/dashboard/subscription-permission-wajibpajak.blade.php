<style>
  .table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto!important;
  }
  .input-error {
    border: 2px solid #ff3e1d;
  }
</style>
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
        <!-- <div class="col-sm-6 text-right">
          <a class="btn btn-sm btn-warning" id="btn-tunjangan" href="#">
            <i class='bx bx-plus'></i> Subscription
          </a>
        </div> -->
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="text-nowrap">
              <table class="table table-hover display nowrap" style="width: 100%" id="table-wajibpajak">
                <thead class="table-light">
                  <tr>
                    <th>Nama</th>
                    <th>NPWP</th>
                    <th>Entitas</th>
                    <th>Tipe</th>
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

<!-- Modal Akses -->
<div class="modal fade" id="modalPermission" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Hak Akses <span class="paketitle"></span> <span class="pakettitlewpname" style="display: none;"></span></h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12 mb-3">
          <div class="row mb-3">
            <label class="col-sm-2" for="harga">Harga</label>
            <div class="col-sm-7">
              <input type="text" name="harga" id="harga" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2" for="periode">Periode Aktif</label>
            <div class="col-sm-7">
              <!-- <select required style="width: 100%;" name="periode" id="periode" class="form-control" data-placeholder="-:Pilih Periode:-">
                <option value=""></option>  
                <option value="1">1 Bulan</option>
                <option value="6">6 Bulan</option>
                <option value="12">12 Bulan</option>
              </select> -->
              <input type="text" name="periode" id="periode" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2" for="notes">Notes</label>
            <div class="col-sm-7">
              <textarea name="notes" id="notes" class="form-control"></textarea>
            </div>
          </div>
          <!-- <div class="row mb-3">
            <label class="col-sm-2" for="tipe">Tipe</label>
            <div class="col-sm-7">
              <select required style="width: 100%;" name="tipe" id="tipe" class="form-control" data-placeholder="-:Pilih Tipe:-">
                <option value=""></option>  
                <option value="FREE">FREE</option>
                <option value="BRONZE">PREMIUM</option>
              </select>
            </div>
          </div> -->
          <div class="row mb-3">
            <div class="col-sm-9 text-right">
              <button type="button" class="btn btn-warning btn-sm" id="btn-simpan-perpanjang">Simpan</button>
            </div>
          </div>
          <form id="formPermission" method="POST" action="{{route('admin.subscription.permission.master.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<div class="row mb-3">
							<div class="col-sm-12 table-responsive">
								<table id="tree-akses" class="table table-bordered">
									<thead>
										<tr>
											<th>Menu</th>
											<th colspan="10">Akses</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
            <div class="row mb-3">
              <div class="col-sm-12 text-right">
                <button type="reset" data-bs-dismiss="modal" class="btn btn-outline-danger btn-sm">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
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
    let actionUpdateUrl = "{{route('admin.subscription.permission.wajibpajak.update', '')}}";
    let wajibpajakId = null;

    // Begin Table
    let tblWajibPajak = $("#table-wajibpajak").DataTable({
      // "filtering": true,
      // "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      "ajax": {
          "url": "{{route('admin.subscription.permission.wajibpajak.datatable')}}",
          "type": "GET",
          "data": function(data) {
            // data.type = 'BADAN';
              //     console.log(data); // send data to server
          }
      },
      // "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      // },
      "autoWidth": true,
      "columnDefs": [{
        target: [4],
        width: 30
      }, {
        target: [0,1,2,3,4],
        className: 'text-center'
      }],
      "columns": [
          {
              "data": "wajibpajak_name"
          },
          {
              "data": "wajibpajak_npwp",
          },
          {
              "data": "wajibpajak_type",
          },
          {
              "data": "wajibpajaksubscription",
              "render": function(data, type, row) {
                return (data) ? `${data.subscription.subscription_type}` : '-';
              }
          },
          {
            "data": "wajibpajak_id",
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
                </div>
              </div>
                `
            }
          },
      ],
    });

    // let dataSource = [];

		// let treeAkses = null;

  $("#modalPermission").on("hide.bs.modal", function () {
    $("#formPermission [type=reset]").click()
    // $(`#formPermission .form-check-input`).prop('checked', false);
    // tblSubscriptionBadan.draw(); // Refresh the DataTable
  });

  [hargaValue] = AutoNumeric.multiple(["#harga"], { 
    currencySymbol: "Rp. ",
    decimalCharacter: ",",
    digitGroupSeparator: ".",
    minimumValue: "0",
    decimalPlaces: "0",
    unformatOnSubmit: true,
    modifyValueOnWheel: false,
  })
  $("#tipe").select2({
    dropdownParent: $("#modalPermission #formPermission"),
  });
  // let tempData = null;
  $("#table-wajibpajak").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formSubscription [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblWajibPajak.row(row).data();
		let subscriptionpermission = JSON.parse(data.wajibpajaksubscription.wajibpajaksubscription_permission);
    console.log('data', data);
    console.log('data', subscriptionpermission);
    $(".pakettitlewpname").text(`${data.wajibpajak_name}`);
    $(".paketitle").text(`${data.wajibpajak_name} - Paket ${data.wajibpajaksubscription.subscription.subscription_type}`);
    // $("#tglaktif").text(moment(data.wajibpajaksubscription.wajibpajaksubscription_expired_at).format('DD MMMM YYYY'))
    let userorder = data.wajibpajaksubscription.userorder;
    let userordertype = (userorder.userorder_subscriptiontype == 'FREE') ? 'FREE' : 'BRONZE'; 
    hargaValue.set(userorder.userorder_total);
    $("#notes").val(userorder.userorder_description);
    $('#periode').data('daterangepicker').setStartDate(moment(data.wajibpajaksubscription.wajibpajaksubscription_expired_at).format('DD-MM-YYYY'));

    // $("#tipe").val(userordertype).trigger("change");
		
		if(subscriptionpermission) {
			subscriptionpermission.forEach(subpermission => {
				// console.log('subpermission', subpermission)
				// let parseSubPermission = (subpermission.subscriptionpermission_permissions) ? JSON.parse(subpermission.subscriptionpermission_permissions) : null;
				// if(parseSubPermission) {
				// 	parseSubPermission.forEach(parsub => {
						// console.log('parsub', parsub);
						let menuId = subpermission.menu_id;
						let entries = Object.entries(subpermission)
						let data = entries.map( ([key, val] = entry) => {
							// console.log(`The ${key} is ${val}`);
							console.log('menuId', menuId);
							console.log('key', key);
							if(key != 'menu_id')
								$(`.menu_${menuId}_${key}`).prop('checked', true);
                if(key == 'Q') {
								  $(`.menu_${menuId}_${key}_kuota`).val(val);
                }
                else if(key == 'Q_COPY_LINK') {
								  $(`.menu_${menuId}_${key}_kuota`).val(val);
                }
                else if(key == 'Q_DOWNLOAD_PDF') {
								  $(`.menu_${menuId}_${key}_kuota`).val(val);
                }
                else if(key == 'Q_SEND_EMAIL') {
								  $(`.menu_${menuId}_${key}_kuota`).val(val);
                }
                else if(key == 'Q_SEND_WA') {
								  $(`.menu_${menuId}_${key}_kuota`).val(val);
                }
						});
						// let keys = Object.keys(parsub);
						// console.log('keys', keys);
						
				// 	});
				// }
			});

		}
    $("#modalPermission").modal("show");
    // change url
    $("#formPermission").attr("action", actionUpdateUrl+"/"+data.wajibpajak_id+"?menu_id="+currentMenuId);

    wajibpajakId = data.wajibpajak_id;
  })

  
  $("#formPermission").submit(function(e) {
    e.preventDefault();
    $(".spinner-box").css({'display': 'table'});
    let form = $(this);
    $.ajax({
      method: form.attr('method'),
      url: form.attr('action'),
      data: form.serialize()+"&"+$.param({
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
          });
        }
      },
      success: function(response) {
        $(".spinner-box").fadeOut();
        console.log('response', response);
        $(`.menu_kuota`).removeClass('input-error');
        if(!response.success) {
          toastr.error(response.message);
          if(response.data) {
            console.log('response.data', response.data)
            
            $(`.menu_${response.data.menu_id}_${response.data.code}_kuota`).addClass('input-error');
          }
          return false;
        }
          
        toastr.success(response.message);

        $("#modalPermission").modal("hide");
        tblWajibPajak.draw();
      }
    });
  });

  $("#btn-simpan-perpanjang").click(function(e) {
    e.preventDefault();

    let wpname = $(".pakettitlewpname").text();
    Swal.fire({
      html: 'Apakah Anda yakin melakukan perubahan detail berlangganan untuk <b>'+ wpname +'</b>?',
      icon: 'question',
      preConfirm: () => {
          Swal.showLoading();
          // tblPengaturanTunjangan.row(row).remove();
          // return true;
          return fetch("{{route('admin.subscription.permission.wajibpajak.orderextend', '')}}/"+wajibpajakId, {
              method: 'POST',
              body: new URLSearchParams($.param({
                _token: $("meta[name=csrf-token]").attr('content'),
                harga: hargaValue.getNumber(),
                tipe: $("#tipe").val(),
                periode: $("#periode").val(),
                notes: $("#notes").val()
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

          Swal.fire({
              html: result.message,
              showCancelButton: false,
              confirmButtonText: "Ok",
              icon: 'error'
          })
          return false;
      }

      toastr.success(result.message);
      tblWajibPajak.draw();
    });
  });

	getmenus();
	function getmenus() {
    $(".spinner-box").css({'display': 'table'});
		// get menus
		$.ajax({
			method: 'GET',
			url: "{{route('admin.subscription.permission.getmenu')}}",
			// data: {},
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
        $(".spinner-box").fadeOut();
				console.log(response, 'response')
				let data = response.data;
				let tablebody = $("#tree-akses tbody");
				let rows = '';
				data.forEach(dt => {
					rows += `<tr class="bg-lighter"><td colspan="11">${dt.menu_title}</td></tr>`;
					let children = dt.children;
					if(children) {
						children.forEach(child => {
							let grandchildren = child.children;
							let classbg = (grandchildren) ? ' bg-lightest' : '';
							let colspan = (grandchildren) ? '11' : '11';
							if(grandchildren)
								rows += `<tr class="${classbg}"><td colspan="${colspan}">${child.menu_title}</td></tr>`;

							if(grandchildren) {
								grandchildren.forEach(gchild => {
									let gpermission = (gchild.permission_json) ? JSON.parse(gchild.permission_json) : null;
									rows += `<tr><td class="bg-lightest" style="padding-left:30px">${gchild.menu_title}</td>`;

									if(gpermission) {
										gpermission.forEach(gpermit => {
                      let qinput = '';
                      let periode = '';
                      if(gpermit.permission_code == 'Q') {
                        qinput = `<input type="number" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="" class="form-control menu_kuota menu_${gchild.menu_id}_${gpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                        periode = `(${gpermit.permission_period})`;
                      }
                      else if(gpermit.permission_code == 'Q_COPY_LINK') {
                        qinput = `<input type="number" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="" class="form-control menu_kuota menu_${gchild.menu_id}_${gpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                        periode = `(${gpermit.permission_period})`;
                      }
                      else if(gpermit.permission_code == 'Q_DOWNLOAD_PDF') {
                        qinput = `<input type="number" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="" class="form-control menu_kuota menu_${gchild.menu_id}_${gpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                        periode = `(${gpermit.permission_period})`;
                      }
                      else if(gpermit.permission_code == 'Q_SEND_EMAIL') {
                        qinput = `<input type="number" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="" class="form-control menu_kuota menu_${gchild.menu_id}_${gpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                        periode = `(${gpermit.permission_period})`;
                      }
                      else if(gpermit.permission_code == 'Q_SEND_WA') {
                        qinput = `<input type="number" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="" class="form-control menu_kuota menu_${gchild.menu_id}_${gpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                        periode = `(${gpermit.permission_period})`;
                      }
											rows += `<td class="text-center">
											<input type="checkbox" name="menu[${gchild.menu_id}][${gpermit.permission_code}]" value="1" class="form-check-input menu_${gchild.menu_id}_${gpermit.permission_code}">
											<br>
                      ${qinput}
											<span style="font-size: 12px">${gpermit.permission_code_name} ${periode}</span>
											</td>`;
										});
									}

									rows += `</tr>`;
								})
							} else {
								let cpermission = (child.permission_json) ? JSON.parse(child.permission_json) : null;
								rows += `<tr><td class="bg-lightest" style="padding-left:30px">${child.menu_title}</td>`;

								if(cpermission) {
									cpermission.forEach(cpermit => {
                    let periode = '';
                    let qinput = '';
                    if(cpermit.permission_code == 'Q') {
                      qinput = `<input type="number" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="" class="form-control menu_kuota menu_${child.menu_id}_${cpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                      periode = `(${cpermit.permission_period})`;
                    }
                    else if(cpermit.permission_code == 'Q_COPY_LINK') {
                      qinput = `<input type="number" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="" class="form-control menu_kuota menu_${child.menu_id}_${cpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                      periode = `(${cpermit.permission_period})`;
                    }
                    else if(cpermit.permission_code == 'Q_DOWNLOAD_PDF') {
                      qinput = `<input type="number" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="" class="form-control menu_kuota menu_${child.menu_id}_${cpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                      periode = `(${cpermit.permission_period})`;
                    }
                    else if(cpermit.permission_code == 'Q_SEND_EMAIL') {
                      qinput = `<input type="number" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="" class="form-control menu_kuota menu_${child.menu_id}_${cpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                      periode = `(${cpermit.permission_period})`;
                    }
                    else if(cpermit.permission_code == 'Q_SEND_WA') {
                      qinput = `<input type="number" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="" class="form-control menu_kuota menu_${child.menu_id}_${cpermit.permission_code}_kuota" placeholder="Kuota"><br>`;
                      periode = `(${cpermit.permission_period})`;
                    }
										rows += `<td class="text-center">
										<input type="checkbox" class="form-check-input menu_${child.menu_id}_${cpermit.permission_code}" name="menu[${child.menu_id}][${cpermit.permission_code}]" value="1">
										<br>
                    ${qinput}
										<span style="font-size: 12px">${cpermit.permission_code_name} ${periode}</span>
										</td>`;
									});
								}
								rows += `</tr>`;
							}
						});
					}
				});

				tablebody.html(rows);
			}
		});
	}

  $("#formPermission [type=reset]").click(function(e) {
    // e.preventDefault();
    $('#formPermission .menu_kuota').val('');
    $(`#formPermission .form-check-input`).prop('checked', false);
    // if($('#modalPermission').is(':visible')) {
      // $("#modalPermission").modal("hide");
    // }
  });

  $("#periode").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    parentEl: '#modalPermission',
    locale: {
      format: 'DD-MM-YYYY', // Display day, month, and year
    },
    minYear: moment().year(),
  })
  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>