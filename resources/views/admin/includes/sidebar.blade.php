<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="index.html" class="app-brand-link">
      <span class="app-brand-logo demo">
      <img
        src="{{asset('assets/img/logo/logo.png')}}"
        alt=""
        class="w-px-100 h-auto"
      />
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{request()->segment(2) == 'beranda' ? 'active' : ''}}">
      <a href="{{url('/admin/beranda')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>
    <li class="menu-item {{request()->segment(2) == 'user' ? 'active open' : ''}}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-notepad"></i>
        <div data-i18n="Master">User</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{request()->segment(2) == 'user' && request()->segment(3) == 'aktif' ? 'active' : ''}}">
          <a href="{{route('admin.page.user.aktif.index')}}" class="menu-link rekkaa-page-link">
            <div data-i18n="User Aktif">User Aktif</div>
          </a>
        </li>
        <li class="menu-item {{request()->segment(2) == 'user' && request()->segment(3) == 'wajib-pajak' ? 'active' : ''}}">
          <a href="{{route('admin.page.user.wajibpajak.index')}}" class="menu-link rekkaa-page-link">
            <div data-i18n="Wajib Pajak">Wajib Pajak</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{request()->segment(2) == 'master' ? 'active open' : ''}}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-notepad"></i>
          <div data-i18n="Master">Master</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item {{request()->segment(3) == 'bpjsrate' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.bpjsrate.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Rate BPJS">Rate BPJS</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'kepemilikannpwp' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.kepemilikannpwp.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Kepemilikan Npwp">Kepemilikan Npwp</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'ptkp' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.ptkp.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="PTKP">PTKP</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'tarifpph21' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.tarifpph21.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Tarif PPh 21">Tarif PPh 21</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'tarifnonnpwp' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.tarifnonnpwp.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Tarif Non NPWP">Tarif Non NPWP</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'tunjanganjabatan' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.tunjanganjabatan.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Tunjangan Jabatan">Tunjangan Jabatan</div>
            </a>
          </li>
          <li class="menu-item {{request()->segment(3) == 'subscription' ? 'active' : ''}}">
            <a href="{{route('admin.page.master.subscription.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Subscription / Paket">Subscription / Paket</div>
            </a>
          </li>
        </ul>
      </li>
      <li class="menu-item {{request()->segment(2) == 'setting' ? 'active open' : ''}}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-notepad"></i>
          <div data-i18n="Master">Setting</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item {{request()->segment(3) == '' ? 'active' : ''}}">
            <a href="{{route('admin.page.setting.alamat.index')}}" class="menu-link rekkaa-page-link">
              <div data-i18n="Alamat">Alamat</div>
            </a>
          </li>
        </ul>
      </li>
  </ul>
</aside>
<!-- / Menu -->