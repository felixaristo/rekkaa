<!-- <h4 class="mb-2">Jenis Langganan</h4> -->
<!-- <p class="pb-2 mb-0">It can help you manage and service orders before,<br> during and after fulfilment.</p> -->
<div class="bg-lighter p-4 rounded mt-4">
	<p>{{$subscription->subscription_title}}</p>
	<div class="d-flex align-items-center" style="position: relative;">
		<h1 class="text-heading display-5 pricing-plan">
			<span class="pricing-plan-newprice">Rp. {{number_format($subscription->subscription_price, 0, ',', '.')}}</span>
			<span class="pricing-plan-oldprice text-danger">Rp. 0</span>
		</h1>
		@if($subscription->subscription_type != 'FREE')
		<sub class="duration-plan">/ 1 bulan</sub> 
		@endif
	</div>
	<p class="text-success fs-tiny"><span class="plan-diskon">Rp. 0</span></p>
	@if(!isset($wajibpajak))
	<div class="d-grid">
		<a href="{{env('PRICING_PAGE')}}" class="btn btn-outline-primary btn-ganti">Ubah Langganan</a>
	</div>
	@endif
</div>
	<!-- <div class="d-flex justify-content-between align-items-center mt-3">
		<p class="mb-0">Subtotal</p>
		<h6 class="mb-0">$85.99</h6>
	</div>
	<div class="d-flex justify-content-between align-items-center mt-3">
		<p class="mb-0">Tax</p>
		<h6 class="mb-0">$4.99</h6>
	</div>
	<hr> -->
	<!-- <div class="d-flex justify-content-between align-items-center mt-3 pb-1">
		<p class="mb-0">Diskon</p>
		<h6 class="mb-0 plan-diskon text-success">- Rp. 0</h6>
	</div> -->
	<div class="d-flex justify-content-between align-items-center mt-3 pb-1">
		<p class="mb-0">Jumlah</p>
		<h6 class="mb-0 qty-plan-total">1</h6>
	</div>
	<div class="d-flex justify-content-between align-items-center mt-3 pb-1">
		<p class="mb-0">Add On</p>
		<h6 class="mb-0 addon-plan-total">Rp. 0</h6>
	</div>
	<div class="d-flex justify-content-between align-items-center mt-3 pb-1">
		<p class="mb-0" style="font-size: 1.3rem;">Total</p>
		<h6 class="mb-0 plan-total" style="font-size: 1.3rem;">Rp. {{number_format($subscription->subscription_price, 0, ',', '.')}}</h6>
	</div>
	<div class="d-grid mt-3">
		<button class="btn btn-warning" id="btn-lanjutkan">
			<span class="me-2">Lanjutkan</span>
			<i class="bx bx-right-arrow-alt scaleX-n1-rtl"></i>
		</button>
	</div>

	<!-- <p class="mt-4 pt-2">By continuing, you accept to our Terms of Services and Privacy Policy. Please note that payments are non-refundable.</p> -->
	<?php if(!isset($wajibpajak)) { ?>
	<p class="text-left">
		<span>Sudah Punya Akun?</span>
		<a href="{{url('/login')}}">
			<span> Login Sekarang</span>
		</a>
	</p>
	<?php } ?>
	<p>
		<a href="#" class="text-danger btn-kembali" id="btn-kembali">
			<span class="bx bx-arrow-back"></span> Kembali
		</a>
	</p>
<!-- </div> -->