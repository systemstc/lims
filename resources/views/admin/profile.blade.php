@extends('layouts.app_back')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="nk-block-head nk-block-head-sm mb-4">
            <div class="nk-block-between align-items-center flex-wrap" style="gap: 15px;">
                <div class="nk-block-head-content">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Admin Profile</li>
                        </ul>
                    </nav>
                    <h3 class="nk-block-title page-title fw-bold mb-1">Super Admin Profile</h3>
                    <div class="nk-block-des text-soft">
                        <p class="mb-0">Manage your master administrator credentials, security settings, and view recent access logs.</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light bg-white" style="border-radius: 8px;">
                        <em class="icon ni ni-arrow-left me-1"></em><span>Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Hero Banner Card (Overlapping Fixed) -->
        <div class="card card-bordered mb-4 shadow-sm" style="border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div class="p-4 p-md-5" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #312e81 100%); color: #ffffff;">
                <div class="row align-items-center g-4">
                    <div class="col-auto">
                        <div style="width: 82px; height: 82px; border-radius: 22px; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; color: #ffffff; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4); border: 3px solid rgba(255,255,255,0.25);">
                            {{ strtoupper(substr($admin->m00_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <!-- Row 1: Admin Name & Badges -->
                        <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                            <h2 class="text-white mb-0 fw-bold" style="letter-spacing: -0.5px; line-height: 1.2;">{{ $admin->m00_name }}</h2>
                            <span class="badge" style="background: linear-gradient(90deg, #f59e0b, #d97706); font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 700; text-transform: uppercase; padding: 5px 12px; border-radius: 20px; color: #ffffff;">
                                <em class="icon ni ni-shield-star me-1"></em>Super Admin
                            </span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.75rem; padding: 5px 12px; border-radius: 20px;">
                                <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%; display: inline-block; margin-right: 6px; vertical-align: middle;"></span>ACTIVE
                            </span>
                        </div>

                        <!-- Row 2: Email, ID and Last Active (Separate Distinct Line, No Overlap) -->
                        <div class="d-flex align-items-center flex-wrap mb-3 text-white-50" style="gap: 16px; font-size: 0.925rem; line-height: 1.6;">
                            <div class="d-inline-flex align-items-center">
                                <em class="icon ni ni-mail me-1 text-white-50"></em>
                                <span>{{ $admin->m00_email }}</span>
                            </div>
                            <span class="text-white-50 opacity-50">&bull;</span>
                            <div class="d-inline-flex align-items-center">
                                <em class="icon ni ni-id-card me-1 text-white-50"></em>
                                <span>Admin ID #{{ $admin->m00_admin_id }}</span>
                            </div>
                            @if ($stats['last_login'])
                                <span class="text-white-50 opacity-50">&bull;</span>
                                <div class="d-inline-flex align-items-center">
                                    <em class="icon ni ni-clock me-1 text-white-50"></em>
                                    <span>Last Active: {{ \Carbon\Carbon::parse($stats['last_login'])->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Row 3: Metrics & System Access (Border-Top separated) -->
                        <div class="d-flex align-items-center flex-wrap pt-3 border-top" style="border-color: rgba(255,255,255,0.15) !important; gap: 20px;">
                            <div class="d-inline-flex align-items-center text-white-50 small">
                                <span class="badge me-2" style="background: rgba(255,255,255,0.18); color: #ffffff; padding: 4px 9px; font-weight: 700;">{{ $stats['total_ros'] }}</span> Regional Offices
                            </div>
                            <div class="d-inline-flex align-items-center text-white-50 small">
                                <span class="badge me-2" style="background: rgba(255,255,255,0.18); color: #ffffff; padding: 4px 9px; font-weight: 700;">{{ $stats['total_employees'] }}</span> Staff Members
                            </div>
                            <div class="d-inline-flex align-items-center text-white-50 small">
                                <em class="icon ni ni-lock text-success me-1"></em> Full System Access Granted
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Settings 2-Column Grid -->
        <div class="row g-4 mb-4">
            <!-- Edit Profile Details Card -->
            <div class="col-lg-6">
                <div class="card card-bordered h-100 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-inner p-4 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="card-head d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                <div>
                                    <h5 class="card-title fw-bold text-dark mb-1">
                                        <em class="icon ni ni-user-edit text-primary me-2"></em>General Account Information
                                    </h5>
                                    <p class="text-muted small mb-0">Update your super admin display name and official notification email.</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.profile.update') }}" method="POST" id="profileUpdateForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="txt_name" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Full Name <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-user text-muted"></em>
                                        </div>
                                        <input type="text" class="form-control form-control-lg @error('txt_name') is-invalid @enderror" id="txt_name" name="txt_name" value="{{ old('txt_name', $admin->m00_name) }}" required placeholder="e.g. Master Administrator" style="border-radius: 10px;">
                                    </div>
                                    @error('txt_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="txt_email" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Official Email Address <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-mail text-muted"></em>
                                        </div>
                                        <input type="email" class="form-control form-control-lg @error('txt_email') is-invalid @enderror" id="txt_email" name="txt_email" value="{{ old('txt_email', $admin->m00_email) }}" required placeholder="admin@example.com" style="border-radius: 10px;">
                                    </div>
                                    <div class="form-note text-muted small mt-1">This email is used to log in to the admin console and receive system alerts.</div>
                                    @error('txt_email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end pt-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                                        <em class="icon ni ni-save me-1"></em> Save Profile Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="col-lg-6">
                <div class="card card-bordered h-100 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-inner p-4 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="card-head d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                <div>
                                    <h5 class="card-title fw-bold text-dark mb-1">
                                        <em class="icon ni ni-lock-alt text-warning me-2"></em>Security & Password
                                    </h5>
                                    <p class="text-muted small mb-0">Ensure your account is protected by setting a strong master password.</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.profile.password') }}" method="POST" id="passwordUpdateForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Current Password <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <a href="javascript:void(0)" class="form-icon form-icon-right passcode-switch lg" data-target="current_password">
                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye-off" style="display: none;"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Enter current password" style="border-radius: 10px;">
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">New Password <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <a href="javascript:void(0)" class="form-icon form-icon-right passcode-switch lg" data-target="new_password">
                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye-off" style="display: none;"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required placeholder="Minimum 6 characters" minlength="6" style="border-radius: 10px;">
                                    </div>
                                    @error('new_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="new_password_confirmation" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <a href="javascript:void(0)" class="form-icon form-icon-right passcode-switch lg" data-target="new_password_confirmation">
                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye-off" style="display: none;"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Re-type new password" minlength="6" style="border-radius: 10px;">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end pt-2">
                                    <button type="submit" class="btn btn-warning px-4 py-2 text-dark fw-bold" style="border-radius: 10px;">
                                        <em class="icon ni ni-shield-check me-1"></em> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Login Security Logs Card -->
        <div class="card card-bordered shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
            <div class="card-inner p-4">
                <div class="card-head d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom flex-wrap" style="gap: 10px;">
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-1">
                            <em class="icon ni ni-shield-alert text-info me-2"></em>Recent Admin Login Activity
                        </h5>
                        <p class="text-muted small mb-0">Audit history of recent sign-in sessions for this administrator account.</p>
                    </div>
                    <div>
                        <a href="{{ route('view_login_logs') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                            <em class="icon ni ni-reports-alt me-1"></em> View All System Logs
                        </a>
                    </div>
                </div>

                @if ($recentLogins->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <em class="icon ni ni-info fs-2 text-soft d-block mb-2"></em>
                        <p class="mb-0">No login history records found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.5px;">
                                <tr>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Device / User Agent</th>
                                    <th>Login Timestamp</th>
                                    <th>Time Ago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentLogins as $log)
                                    <tr>
                                        <td>
                                            @if ($log->tr00_successful)
                                                <span class="badge bg-success-dim text-success" style="border-radius: 20px; font-size: 0.75rem; padding: 4px 10px;">
                                                    <em class="icon ni ni-check-circle me-1"></em> Successful
                                                </span>
                                            @else
                                                <span class="badge bg-danger-dim text-danger" style="border-radius: 20px; font-size: 0.75rem; padding: 4px 10px;">
                                                    <em class="icon ni ni-cross-circle me-1"></em> Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-dark fw-bold">{{ $log->tr00_ip_address ?? '127.0.0.1' }}</code>
                                        </td>
                                        <td>
                                            <span class="text-muted small" title="{{ $log->tr00_user_agent }}">
                                                {{ Str::limit($log->tr00_user_agent ?? 'Web Browser', 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">
                                                {{ $log->tr00_login_at ? \Carbon\Carbon::parse($log->tr00_login_at)->format('d M, Y h:i A') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-muted" style="border-radius: 8px;">
                                                {{ $log->tr00_login_at ? \Carbon\Carbon::parse($log->tr00_login_at)->diffForHumans() : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Passcode show/hide toggle logic
        document.querySelectorAll('.passcode-switch').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = this.getAttribute('data-target');
                var input = document.getElementById(targetId);
                var showIcon = this.querySelector('.icon-show');
                var hideIcon = this.querySelector('.icon-hide');

                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (showIcon) showIcon.style.display = 'none';
                        if (hideIcon) hideIcon.style.display = 'inline-block';
                    } else {
                        input.type = 'password';
                        if (showIcon) showIcon.style.display = 'inline-block';
                        if (hideIcon) hideIcon.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
@endsection
