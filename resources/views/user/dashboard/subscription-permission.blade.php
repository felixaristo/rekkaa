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
            <div class="nav-align-top mb-4">
              <ul class="nav nav-tabs mb-3 nav-fill" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-badan" aria-controls="navs-tabs-justified-badan" tabindex="-1"><i class="tf-icons bx bx-layer-plus me-1"></i> Badan</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-individu" aria-controls="navs-tabs-justified-individu" tabindex="-1"><i class="tf-icons bx bx-layer-plus me-1"></i> Individu</button>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-tabs-justified-badan" role="tabpanel">
                  <div class="text-nowrap">
                    <table class="table table-hover display nowrap" style="width: 100%" id="table-subscription-badan">
                      <thead class="table-light">
                        <tr>
                          <th>Nama</th>
                          <th>Harga</th>
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
                <div class="tab-pane fade" id="navs-tabs-justified-individu" role="tabpanel">
                  <div class="text-nowrap">
                  <table class="table table-hover display nowrap" style="width: 100%" id="table-subscription-individu">
                    <thead class="table-light">
                      <tr>
                        <th>Nama</th>
                        <th>Harga</th>
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
        <h5 class="modal-title" id="modalCenterTitle">Hak Akses <span class="paketitle"></span></h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="col-sm-12">
          <form id="formPermission" method="POST" action="{{route('admin.subscription.permission.master.store', ['menu_id' => request()->get('menu_id')])}}" class="row needs-validation form-lbl-dot" novalidate autocomplete="off">
						<div class="row">
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
    let actionStoreUrl = "{{route('admin.subscription.permission.master.store', '')}}";
    let actionUpdateUrl = "{{route('admin.subscription.permission.master.update', '')}}";
    // let actionDeleteUrl = "{{route('user.page.pengaturan.tunjangan.delete', '')}}";

    // Begin Table Subscription Badan
    let tblSubscriptionBadan = $("#table-subscription-badan").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      "ajax": {
          "url": "{{route('admin.subscription.permission.datatable')}}",
          "type": "GET",
          "data": function(data) {
            data.type = 'BADAN';
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      },
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
              "data": "subscription_title"
          },
          {
              "data": "subscription_price",
              "className": "text-right",
              "render": function(data, type, row) {
                  return 'Rp. '+formatCurrency(data);
              }
          },
          {
              "data": "subscription_entity_type",
          },
          {
              "data": "subscription_type",
          },
          {
            "data": "subscription_id",
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
      ],
    });

    // let dataSource = [];

		// let treeAkses = null;

  $("#modalPermission").on("hide.bs.modal", function () {
    $("#formPermission [type=reset]").click()
    // $(`#formPermission .form-check-input`).prop('checked', false);
    // tblSubscriptionBadan.draw(); // Refresh the DataTable
  });

  // let tempData = null;
  $("#table-subscription-badan").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formSubscription [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblSubscriptionBadan.row(row).data();
		let subscriptionpermission = data.subscriptionpermission;
    console.log('data', data);
    $(".paketitle").text(`${data.subscription_entity_type} - Paket ${data.subscription_title}`);

		
		if(subscriptionpermission) {
			subscriptionpermission.forEach(subpermission => {
				// console.log('subpermission', subpermission)
				let parseSubPermission = (subpermission.subscriptionpermission_permissions) ? JSON.parse(subpermission.subscriptionpermission_permissions) : null;
				if(parseSubPermission) {
					parseSubPermission.forEach(parsub => {
						// console.log('parsub', parsub);
						let menuId = parsub.menu_id;
						let entries = Object.entries(parsub)
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
						
					});
				}
			});

      $("#modalPermission").modal("show");
		}
    // change url
    $("#formPermission").attr("action", actionUpdateUrl+"/"+data.subscription_id+"?menu_id="+currentMenuId);
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
        tblSubscriptionBadan.draw();
        tblSubscriptionIndividu.draw();
      }
    });
  });

  $(".nav-link").on('shown.bs.tab', function(e) {
    console.log()
    let target = $(this).attr("data-bs-target");
    if(target.indexOf('individu') > -1) {
      tblSubscriptionIndividu.draw();
    }
    // tblSubscriptionBadan.draw();
  })

  // Begin Table Subscription Badan
  let tblSubscriptionIndividu = $("#table-subscription-individu").DataTable({
      // "filtering": false,
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "searchDelay": 1050,
      "ajax": {
          "url": "{{route('admin.subscription.permission.datatable')}}",
          "type": "GET",
          "data": function(data) {
            data.type = 'INDIVIDU';
              //     console.log(data); // send data to server
          }
      },
      "fnInitComplete": function() {
          // this.fnAdjustColumnSizing(true);
          // $(this).find(".cetak-registrasi").select2();
      },
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
              "data": "subscription_title"
          },
          {
              "data": "subscription_price",
              "className": "text-right",
              "render": function(data, type, row) {
                  return 'Rp. '+formatCurrency(data);
              }
          },
          {
              "data": "subscription_entity_type",
          },
          {
              "data": "subscription_type",
          },
          {
            "data": "subscription_id",
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
      ],
  });
  $("#table-subscription-individu").on("click", ".btn-edit", function(e) {
    e.preventDefault();
    $("#formSubscription [type=reset]").click();
    // get row
    let row = $(this).closest('tr');
    let data = tblSubscriptionIndividu.row(row).data();
		let subscriptionpermission = data.subscriptionpermission;
    console.log('data', data);
    $(".paketitle").text(`${data.subscription_entity_type} - Paket ${data.subscription_title}`);

		$("#modalPermission").modal("show");
		
		if(subscriptionpermission) {
			subscriptionpermission.forEach(subpermission => {
				// console.log('subpermission', subpermission)
				let parseSubPermission = (subpermission.subscriptionpermission_permissions) ? JSON.parse(subpermission.subscriptionpermission_permissions) : null;
				if(parseSubPermission) {
					parseSubPermission.forEach(parsub => {
						// console.log('parsub', parsub);
						let menuId = parsub.menu_id;
						let entries = Object.entries(parsub)
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
						
					});
				}
			});
		}
    // change url
    $("#formPermission").attr("action", actionUpdateUrl+"/"+data.subscription_id+"?menu_id="+currentMenuId);
    // // set data
    // $("#sttunjangankaryawan_id").val(data.sttunjangankaryawan_id);
  })

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
  // set meta title
  setHtmlTitle('{{$title}}')
})
</script>