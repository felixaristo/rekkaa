<div class="row mb-5">
	<?php 
		$doaction = route('doregisteraddon');
		$userorder_paymentperiode = 1;
		$listaddon = [];
		$isperpanjang = false;
		// dd($wajibpajak->userorder);
		if(isset($wajibpajak)) {
			$doaction = route('user.page.subscription.doextendaddon');
			
			$userorder_paymentperiode = $wajibpajak->userorder->userorder_paymentperiode;
			$subscriptiondata = ($wajibpajak->userorder->userorder_subscriptiondata) ? json_decode($wajibpajak->userorder->userorder_subscriptiondata) : null;
			if($subscriptiondata) {
				$listaddon = $subscriptiondata->listaddon;
				if($listaddon) {
					foreach($listaddon as &$lad) {
						$lad->readonly = true;
					}
				}
			}
			
			if(request()->segment(3) == 'perpanjang') {
				$isperpanjang = true;
			}

			if(request()->segment(3) == 'upgrade') {
				$doaction = route('user.page.subscription.doupgradeaddon');
				$isperpanjang = true;
				$listaddon = [];
			}
		}
	?>
	<form id="form-2" class="mb-3 form-lbl-dot row" action="{{$doaction}}" method="POST" autocomplete="off">
		<div class="divider">
			<div class="divider-text">
			<h3>Masa Berlangganan</h3>
			</div>
		</div>
		<?php
			// var_dump($listaddon);
			$subscription_discount = ($subscription->subscription_discount) ? json_decode($subscription->subscription_discount) : [];
			// var_dump($subscription_discount);
			if(count($subscription_discount) > 0) :
				foreach($subscription_discount as $disc) :
					if($isperpanjang) {
						if($disc->period < $userorder_paymentperiode) {
							continue;
						}
					}

					if($disc->type == 'DISCOUNT') :
						$discountlabel = ($disc->value_type == 'PERSEN') ? 'Diskon '.$disc->value.' %' : ($disc->value > 0 ? 'Rp.'. number_format($disc->value, 0, ',', '.') : '&nbsp;' );
		?>
		<div class="col-md mb-md-0 mb-2">
			<div class="card card-bayar {{$disc->period == 1 ? 'active' : ''}}">
				<div class="card-body">
					<div class="form-check custom-option custom-option-basic">
						<label class="form-check-label custom-option-content form-check-input-payment gap-3 align-items-center text-right" for="customRadio{{$disc->period}}">
							<input name="subscription_periode" data-vtype="{{$disc->value_type}}" data-value="{{$disc->value}}" class="form-check-input" type="radio" value="{{$disc->period}}" id="customRadio{{$disc->period}}" {{$disc->period == $userorder_paymentperiode ? 'checked' : ''}}>
							<span class="custom-option-body text-right">
								<span class="ms-3">{{$disc->period}} Bulan
								<br><span class="fs-tiny text-warning">{!!$discountlabel!!}</span>
								</span>
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<?php endif; endforeach; endif; ?>
	</div>

	@if($subscription->subscription_type != 'FREESSS')
	<div class="divider">
		<div class="divider-text">
		<h3>Add On</h3>
		</div>
	</div>
    <div class="row">
		
		<div class="mb-3 col-sm-6">
			<select name="selected_addon" style="width: 100%;" class="form-control" id="selected_addon" data-placeholder="Pilih Add-on">
				<option value=""></option>
				@foreach($permission as $pm)
				<option value="{{$pm->permission_id}}" data-price="{{$pm->permission_price}}">{{$pm->permission_code_name}}</option>
				@endforeach
			</select>
		</div>
		<div class="mb-3 col-sm-3">
			<input type="number" name="qty_addon" id="qty_addon" value="1" class="form-control" min="1" placeholder="Jumlah">
		</div>
		<div class="mb-3 col-sm-3">
			<button type="button" id="btn-addon" class="btn btn-outline-success">Tambah</button>
		</div>
		<hr>
		<ul class="list-group" id="list-group-addon">
            
		</ul>
	</div>
	@endif
</form>
<script>
    $(function() {
		<?php if ($listaddon) : ?>
			let listaddonstring = '<?php echo json_encode($listaddon) ?>';
			console.log('listaddonstring', listaddonstring);
			localStorage.setItem('listaddon', listaddonstring);
		<?php endif ?>
		let listaddon = (localStorage.getItem('listaddon')) ? JSON.parse(localStorage.getItem('listaddon')) : [];
		// console.log('listaddon', listaddon);
		// $("#selected_service").select2({
		// 	templateResult: formatStateAddon,
		// 	templateSelection: formatStateAddon
		// });

		$("#selected_addon").select2({
			templateResult: formatStateAddon,
			templateSelection: formatStateAddon
		});

		$(".form-check-input").click(function(e) {
			let input = $(this);
			let lbl = $(this).parent('.form-check-label').find('[name=subscription_periode]').val();
			$('.card-bayar').removeClass('active');
			// console.log(input.is(':checked'))
			if(input.is(':checked')) {
				$(this).parents('.card-bayar').addClass('active')
				// $(".duration-plan").text("/ 1 Bulan");
				let value = $(this).attr('data-value');
				let vtype = $(this).attr('data-vtype');
				
				subscriptionDiscount = value;
				subscriptionDiscountType = vtype;
				// console.log('value', value)
				// console.log('subscriptionDiscount', subscriptionDiscount)
				discount = {
					period: input.val(), 
					value: subscriptionDiscount,
					type: subscriptionDiscountType
				}
				localStorage.setItem('discount', JSON.stringify(discount));
				totalAddon();
			}
		})

		$("#btn-addon").click(function(e) {
			e.preventDefault();
			let qty = $("#qty_addon").val();
			let price = $("#selected_addon :selected").attr('data-price');
			let id = $("#selected_addon").val();
			let text = $("#selected_addon :selected").text();
			let total = Number.parseInt(qty) * Number.parseInt(price);
			
			if(!id) {
				Swal.fire({
					html: 'Silahkan pilih add on',
					confirmButtonText: "Ok",
            		showCancelButton: false,
					icon: 'error'
				});
				return false;
			}
			if(qty < 1) {
				Swal.fire({
					html: 'Jumlah minimal adalah 1',
					confirmButtonText: "Ok",
            		showCancelButton: false,
					icon: 'error'
				});
				return false;
			}
			
			// check if addon exist
			if(listaddon.length > 0) {
				let existaddon = listaddon.find(ad => ad.id == id);
				if(existaddon) {
					Swal.fire({
						html: 'Add on sudah ada! Silahkan edit add on dibawah.',
						confirmButtonText: "Ok",
						showCancelButton: false,
						icon: 'error'
					});
					return false;
				}
			}
			listaddon.push({
				id: id,
				text: text,
				price: price,
				qty: qty
			});
			localStorage.setItem('listaddon', JSON.stringify(listaddon));
			totalAddon();
			
			$("#list-group-addon").append(`<li class="list-group-item align-items-center" data-id="${id}">
				<div class="row">
              <div class="col-sm-4">
			  ${text} 
			  </div>
			  <div class="col-sm-2">
			  	<span data-price="${price}">Rp. ${formatCurrency(price)}</span>
			  </div>
			  <div class="col-sm-1">
			  	x
			  </div>
			  <div class="col-sm-2">
				  <input type="number" style="width:45px;padding:5px" value="${qty}" class="form-control addon-item-qty">
			  </div>
			  <div class="col-sm-3">
				<span style="font-weight: bold;" class="addon-item-total">Rp. ${formatCurrency(total)}</span>
				<br><i class="bx bx-trash me-2 text-danger float-right btn-addon-item-delete" style="cursor: pointer"></i>
			  </div></div>
            </li>`);
			
			$("#qty_addon").val(1);
			$("#selected_addon").val(null).trigger('change');
		})

		$("#list-group-addon").on("change", ".addon-item-qty", function(e) {
			e.preventDefault();

			let id = $(this).closest(".list-group-item").attr("data-id");
			let qty = $(this).val();
			qty = Number.parseInt(qty);
			if(qty < 1) {
				$(this).val(1);
				Swal.fire({
					html: 'Jumlah minimal adalah 1',
					confirmButtonText: "Ok",
            		showCancelButton: false,
					icon: 'error'
				});
				return false;
			}
			
				// console.log(id);
			let item = listaddon.find(ad => ad.id == id);
			// console.log('item', item)
			let total = qty * Number.parseInt(item.price);

			listaddon.find(function(ad) {
				if(ad.id == id) {
					ad.qty = qty;
				}
			});
			// console.log('total', total);
			$(this).closest(".list-group-item").find(".addon-item-total").text(`Rp. ${formatCurrency(total)}`);

			localStorage.setItem('listaddon', JSON.stringify(listaddon));
			totalAddon();
		})
		// delete addon
		$("#list-group-addon").on("click", ".btn-addon-item-delete", function(e) {
			e.preventDefault();

			let id = $(this).closest(".list-group-item").attr("data-id");
			// console.log(id);
			listaddon = listaddon.filter(ad => ad.id != id);
			localStorage.setItem('listaddon', JSON.stringify(listaddon));
			// console.log('listaddon', listaddon)
			totalAddon();
			// console.log('total', total);
			$(this).closest(".list-group-item").remove();
		})

    })
</script>`