<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                        class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="javascript:void(0)" class="logo-link">
                    <img class="logo-light logo-img" src="{{ asset('backAssets/images/logo.png') }}" alt="logo">
                    <img class="logo-dark logo-img" src="{{ asset('backAssets/images/logo.png') }}" alt="logo">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-search ms-3 ms-xl-0">
                <em class="icon ni ni-search"></em>
                <form action="{{ route('search_tracker') }}" method="GET">
                    <input type="text" name="tracker_id" class="form-control border-transparent form-focus-none"
                        placeholder="Search by Tracker ID">
                </form>
            </div><!-- .nk-header-news -->
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown notification-dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="icon-status icon-status-info"><em class="icon ni ni-bell"></em></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                <a href="javascript:void(0)" onclick="if (typeof Swal !== 'undefined') { Swal.fire({title:'Notifications', text:'All notifications marked as read.', icon:'success', timer: 2000, showConfirmButton: false}); }">Mark All as Read</a>
                            </div>
                            <div class="dropdown-body">
                                <div class="nk-notification">
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">You have requested to
                                                <span>Withdrawal</span>
                                            </div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed
                                            </div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                </div><!-- .nk-notification -->
                            </div><!-- .nk-dropdown-body -->
                            <div class="dropdown-foot center">
                                <a href="javascript:void(0)" onclick="if (typeof Swal !== 'undefined') { Swal.fire({title:'All Notifications', text:'You have no additional unread notifications.', icon:'info', confirmButtonText: 'Close'}); }">View All</a>
                            </div>
                        </div>
                    </li>
                    @php
                        $isAdmin = Session::get('role_id') == -1 || Session::get('role') === 'ADMIN' || Session::has('admin_id');
                        
                        if ($isAdmin) {
                            $statusTitle = 'Super Admin';
                            $userRoleLabel = 'Super Admin';
                            $roNameLabel = 'Administrator';
                        } else {
                            $userRoleLabel = Session::get('role', 'User');
                            $roName = Session::get('ro_name');
                            if (!$roName && Session::has('ro_id')) {
                                $roModel = \App\Models\Ro::find(Session::get('ro_id'));
                                $roName = $roModel ? $roModel->m04_name : null;
                            }
                            $roNameLabel = $roName ?: 'N/A';
                            $statusTitle = $roNameLabel;
                        }

                        $userName = Session::get('name', 'User');
                        $userEmail = Session::get('email', '');
                        $userInitial = strtoupper(substr($userName, 0, 1));
                    @endphp
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-status user-status-unverified">{{ $statusTitle }}</div>
                                    <div class="user-name dropdown-indicator">{{ $userName }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar bg-primary">
                                        <span>{{ $userInitial }}</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ $userName }}</span>
                                        <span class="sub-text">{{ $userEmail }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="{{ $isAdmin ? route('admin.profile') : route('profile.2fa.index') }}">
                                            <em class="icon ni ni-user-alt"></em>
                                            <span>View Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" style="cursor: default;">
                                            <em class="icon ni ni-building"></em>
                                            <span><strong>RO:</strong> {{ $roNameLabel }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" style="cursor: default;">
                                            <em class="icon ni ni-shield-check"></em>
                                            <span><strong>Role:</strong> {{ $userRoleLabel }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @php
                                $userRoles = Session::get('user_roles', []);
                                $activeRoleId = Session::get('role_id');
                            @endphp
                            @if (count($userRoles) > 1)
                                <div class="dropdown-inner bg-light py-2 px-3 border-top border-bottom">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="sub-text fw-bold text-dark" style="font-size: 0.72rem; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <em class="icon ni ni-swap me-1 text-primary"></em> Switch Active Role
                                        </span>
                                        <span class="badge bg-outline-primary" style="font-size: 0.65rem;">{{ count($userRoles) }} Roles</span>
                                    </div>
                                </div>
                                <div class="dropdown-inner py-1">
                                    <ul class="link-list">
                                        @foreach ($userRoles as $uRole)
                                            @php $isActive = ($activeRoleId == $uRole['role_id']); @endphp
                                            <li>
                                                <a href="{{ $isActive ? 'javascript:void(0)' : route('switch_role', $uRole['role_id']) }}"
                                                   class="d-flex align-items-center justify-content-between py-1 px-2 {{ $isActive ? 'fw-bold text-primary active' : '' }}"
                                                   style="{{ $isActive ? 'background: rgba(33, 90, 241, 0.08); border-radius: 4px; pointer-events: none;' : '' }}">
                                                    <span>
                                                        <em class="icon ni {{ $isActive ? 'ni-check-circle-fill text-primary' : 'ni-shield-check text-muted' }} me-1"></em>
                                                        {{ $uRole['role_name'] }}
                                                    </span>
                                                    @if ($isActive)
                                                        <span class="badge bg-primary-dim text-primary" style="font-size: 0.65rem;">Active</span>
                                                    @else
                                                        <span class="text-muted small" style="font-size: 0.7rem;"><em class="icon ni ni-arrow-right"></em> Switch</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="#" class="dark-switch" data-bs-placement="left"><em
                                                class="icon ni ni-moon"></em><span data-text="Dark Mode">Dark
                                                Mode</span></a></li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    @php
                                        $route = $isAdmin ? 'admin_logout' : 'user_logout';
                                    @endphp
                                    <li><a href="{{ route($route) }}"><em class="icon ni ni-signout"></em><span>Sign
                                                out</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
