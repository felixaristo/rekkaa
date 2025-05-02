<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{url('karyawan/beranda')}}" class="app-brand-link">
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
      <a href="{{url('/karyawan/beranda')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Beranda</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->segment(2) == 'kehadiran' ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-calendar"></i>
          <div data-i18n="Absensi">Absensi</div>
      </a>
      <ul class="menu-sub">
        <!-- <li class="menu-item {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'absensi' ? 'active' : '' }}">
            <a href="{{ url('karyawan/kehadiran/absensi') }}" class="menu-link rekkaa-page-link">
                <div data-i18n="Absensi">Rekam Absensi</div>
            </a>
        </li> -->
        <li class="menu-item {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'kehadiran-karyawan' ? 'active' : '' }}">
          <a href="{{ url('karyawan/kehadiran/view/karyawan') }}" class="menu-link rekkaa-page-link">
            <div data-i18n="Riwayat Absensi">Riwayat Absensi</div>
          </a>
        </li>
        @if(request()->get('karyawan')->karyawan_ismanager == 1)
        <li class="menu-item {{ request()->segment(2) == 'kehadiran' && request()->segment(3) == 'kehadiran-manager' ? 'active' : '' }}">
          <a href="{{ url('karyawan/kehadiran/view/manager') }}" class="menu-link rekkaa-page-link">
            <div data-i18n="Daftar Absensi">Daftar Absensi</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    <li class="menu-item {{ request()->segment(2) == 'cuti' ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-calendar-check"></i>
          <div data-i18n="Cuti">Cuti</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->segment(2) == 'cuti' && request()->segment(3) == 'cuti-karyawan' ? 'active' : '' }}">
          <a href="{{ url('karyawan/cuti/view/karyawan') }}" class="menu-link rekkaa-page-link">
            <div data-i18n="Pengajuan Cuti">Pengajuan Cuti</div>
          </a>
        </li>
        @if(request()->get('karyawan')->karyawan_ismanager == 1)
        <li class="menu-item {{ request()->segment(2) == 'cuti' && request()->segment(3) == 'cuti-manager' ? 'active' : '' }}">
          <a href="{{ url('karyawan/cuti/view/manager') }}" class="menu-link rekkaa-page-link">
            <div data-i18n="Daftar Cuti">Daftar Cuti</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    <li class="menu-item {{ request()->segment(2) == 'pinjaman' ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-credit-card"></i>
          <div data-i18n="Pinjaman">Pinjaman</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->segment(2) == 'pinjaman' && request()->segment(3) == 'pinjaman-karyawan' ? 'active' : '' }}">
          <a href="javascript:void(0);" class="menu-link rekkaa-page-link">
            <div data-i18n="Pengajuan Pinjaman">Pengajuan Pinjaman</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{ request()->segment(2) == 'klaim' ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-receipt"></i>
          <div data-i18n="Klaim">Klaim</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->segment(2) == 'klaim' && request()->segment(3) == 'klaim-karyawan' ? 'active' : '' }}">
          <a href="javascript:void(0);" class="menu-link rekkaa-page-link">
            <div data-i18n="Pengajuan Klaim">Pengajuan Klaim</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{ request()->segment(2) == 'gaji' ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-dollar"></i>
          <div data-i18n="Gaji">Gaji</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->segment(2) == 'gaji' && request()->segment(3) == 'gaji-karyawan' ? 'active' : '' }}">
          <a href="javascript:void(0);" class="menu-link rekkaa-page-link">
            <div data-i18n="Riwayat Gaji">Riwayat Gaji</div>
          </a>
        </li>
      </ul>
    </li>
    <!-- End of Kehadiran -->
  </ul>
</aside>
<!-- / Menu -->