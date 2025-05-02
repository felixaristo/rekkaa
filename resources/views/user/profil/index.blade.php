<div class="row">
  @if($wajibpajak->wajibpajaksubscription->subscription->subscription_type != 'FREE')
  <div class="col-lg-3 mb-4 order-0">
    <div class="row">
      <div class="col-lg-12 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-12">
              <div class="card-body">
                <h5 class="card-title text-warning">{{$wajibpajak->wajibpajaksubscription->subscription->subscription_title}}</h5>
                <hr>
                <p class="mb-4">
                  Upgrade Akun anda untuk menggunakan fitur yang lebih lengkap.
                </p>
                @if($isowner)
                <a href="#" class="btn btn-sm btn-outline-danger" id="btn-upgradeakun"><i class="bx bx-credit-card"></i> Upgrade Akun</a>
                @endif
              </div>
            </div>
            <!-- <div class="col-sm-5 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4">
                <img src="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" height="140" alt="View Badge User" data-app-dark-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" data-app-light-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" />
              </div>
            </div> -->
          </div>
        </div>
      </div>
      <div class="col-lg-12 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-12">
              <div class="card-body">
                <?php 
                $parse_expired_at = \Carbon\Carbon::parse($wajibpajak->wajibpajaksubscription->wajibpajaksubscription_expired_at);
                ?>
                <h5 class="card-title">Aktif Sampai<br><i class="text-info small">{{$parse_expired_at->format('d F Y')}}</i></h5>
                <!-- <p class="mb-2 text-info">
                Perpanjang Sekarang
                </p> -->
                <hr>
                <p class="mb-4">
                  Perpanjang akun anda untuk bisa menggunakan layanan secara berkelanjutan.
                </p>
                @if($isowner)
                <a href="{{route('user.page.subscription.extend')}}" target="_blank" class="btn btn-sm btn-outline-info" id="btn-extendakun"><i class="bx bx-credit-card"></i> Perpanjang Akun</a>
                @endif
              </div>
            </div>
            <!-- <div class="col-sm-5 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4">
                <img src="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" height="140" alt="View Badge User" data-app-dark-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" data-app-light-img="{{asset('assets/img/illustrations/profile-rekkaa.jpeg')}}" />
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="col-lg-9 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="nav-align-top mb-4">
              <ul class="nav nav-tabs mb-3 nav-fill" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-home" aria-controls="navs-tabs-justified-home" tabindex="-1"><i class="tf-icons bx bx-layer-plus me-1"></i> Entitas</button>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-tabs-justified-home" role="tabpanel">
                  @include('user.profil.entitas-form')
                </div>
                <!-- <div class="tab-pane fade" id="navs-tabs-justified-profile" role="tabpanel"> -->
                  {{--@include('user.profil.profil-form')--}}
                <!-- </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

@if($isowner)
<!-- Modal Upgrade -->
<div class="modal fade" id="modalUpgrade" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalCenterTitle">Upgrade</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
        <div class="row">
					@foreach($subscriptions as $sub)
          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-body">
                <h3>{{$sub->subscription_title}}</h3>
                <p>Rp. {{number_format($sub->subscription_price, 0, ',', '.')}} / bln</p>
                <div class="d-grid gap-2 col-sm-12 mx-auto">
                  <a href="{{route('user.page.subscription.upgrade')}}?subscription_id={{$sub->subscription_id}}" target="_blank" class="btn btn-danger ">Pilih</a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-body">
                <h3>&nbsp;</h3>
                <p>Ketahui informasi lebih detail.</p>
                <div class="d-grid gap-2 col-sm-12 mx-auto">
                  <a href="{{env('PRICING_PAGE')}}" target="_blank" class="btn btn-outline-danger btn-block">Lihat detail</a>
                </div>
              </div>
            </div>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>
@endif
<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    $("#btn-upgradeakun").click(function(e) {
      e.preventDefault();

      $("#modalUpgrade").modal("show");
    })
  })
</script>