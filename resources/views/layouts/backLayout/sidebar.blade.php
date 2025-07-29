@php
    use App\Models\Menu;

    $roleId = Session::get('role_id') ?? -1;

    if ($roleId != -1) {
        $menus = Menu::where('m05_status', 'ACTIVE')
            ->whereNull('m05_parent_id')
            ->whereRaw('FIND_IN_SET(?, m05_role_view)', [$roleId])
            ->orderBy('m05_menu_id')
            ->with([
                'children' => function ($q) use ($roleId) {
                    $q->where('m05_status', 'ACTIVE')->whereRaw('FIND_IN_SET(?, m05_role_view)', [$roleId]);
                },
            ])
            ->get();
    } else {
        $menus = Menu::where('m05_status', 'ACTIVE')
            ->whereNull('m05_parent_id')
            ->orderBy('m05_menu_id')
            ->with('children')
            ->get();
    }
@endphp

<div class="nk-sidebar nk-sidebar-fixed is-light" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ url('/') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('backAssets/images/logo2x.png') }}" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('backAssets/images/logo-dark.png') }}" alt="logo-dark">
                <img class="logo-small logo-img logo-img-small" src="{{ asset('backAssets/images/logo-small.png') }}"
                    alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Dashboards</h6>
                    </li>

                    @foreach ($menus as $menu)
                        @if ($menu->m05_has_submenu == 'NO')
                            <li class="nk-menu-item">
                                <a href="{{ route($menu->m05_route_view) }}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="{{ $menu->m05_icon }}"></em></span>
                                    <span class="nk-menu-text">{{ $menu->m05_title }}</span>
                                </a>
                            </li>
                        @else
                            <li class="nk-menu-item has-sub">
                                <a href="#" class="nk-menu-link nk-menu-toggle">
                                    <span class="nk-menu-icon"><em class="{{ $menu->m05_icon }}"></em></span>
                                    <span class="nk-menu-text">{{ $menu->m05_title }}</span>
                                </a>
                                <ul class="nk-menu-sub">
                                    @foreach ($menu->children as $child)
                                        <li class="nk-menu-item">
                                            <a href="{{ route($child->m05_route_view) }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ $child->m05_title }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
