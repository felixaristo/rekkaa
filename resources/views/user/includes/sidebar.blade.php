<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{url('user/beranda')}}" class="app-brand-link">
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
      <a href="{{url('/user/beranda')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Beranda</div>
      </a>
    </li>

    @foreach(getCurrentAccessGroup() as $menu)
    <li class="menu-item {{request()->get('menu_id') == $menu['menu_id'] ? 'active' : ''}}">
      <a href="#" class="menu-link {{(isset($menu['children']) && count($menu['children']) > 0) ? 'menu-toggle' : 'rekkaa-page-link' }}">
      <i class="menu-icon tf-icons bx {{$menu['menu_icon']}}"></i>
        <div data-i18n="{{$menu['menu_title']}}">{{$menu['menu_title']}}</div>
      </a>
          
      @if(isset($menu['children']) && count($menu['children']) > 0)
      <ul class="menu-sub">
        @foreach($menu['children'] as $submenu)
          <li class="menu-item {{request()->get('menu_id') == $submenu['menu_id'] ? 'active' : ''}}">
            <a href="{{url($submenu['menu_link'])}}?menu_id={{$submenu['menu_id']}}" class="menu-link {{(isset($submenu['children']) && count($submenu['children']) > 0) ? 'menu-toggle' : 'rekkaa-page-link' }}">
              <i class="menu-icon tf-icons bx {{$submenu['menu_icon']}}"></i>
              <div data-i18n="{{$submenu['menu_title']}}">{{$submenu['menu_title']}}</div>
            </a>
            @if(isset($submenu['children']) && count($submenu['children']) > 0)
            <ul class="menu-sub">
              @foreach($submenu['children'] as $subsubmenu)
              <li class="menu-item {{request()->get('menu_id') == $subsubmenu['menu_id'] ? 'active' : ''}}">
                <a href="{{url($subsubmenu['menu_link'])}}?menu_id={{$subsubmenu['menu_id']}}" class="menu-link {{(isset($subsubmenu['children']) && count($subsubmenu['children']) > 0) ? 'menu-toggle' : 'rekkaa-page-link' }}">
                  <i class="menu-icon tf-icons bx {{$subsubmenu['menu_icon']}}"></i>
                  <div data-i18n="{{$subsubmenu['menu_title']}}">{{$subsubmenu['menu_title']}}</div>
                </a>
              </li>
              @endforeach
            </ul>
            @endif
          </li>
        @endforeach
      </ul>
      @endif
    </li>
   
    @endforeach
  </ul>
</aside>
<!-- / Menu -->