@extends('layouts.app_back')

@section('content')
    <!-- Google Fonts for Monospace Recovery Codes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

    <div class="nk-content-xxl">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <!-- Header -->
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Two-Factor Authentication (2FA)</h3>
                                <div class="nk-block-des text-soft fs-6">
                                    <p>Manage your account multi-factor authentication security settings.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        @if ($user->tr01_two_factor_confirmed_at && $user->tr01_two_factor_method)
                            <!-- 2FA IS ENABLED -->
                            <div class="card card-bordered mb-4" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(15,23,42,0.03);">
                                <div class="card-inner p-3">
                                    <!-- Top Status Row -->
                                    <div class="row align-items-center g-3 mb-2">
                                        <div class="col-auto">
                                            <div style="width: 52px; height: 52px; border-radius: 14px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; display: flex; align-items: center; justify-content: center;">
                                                @if ($user->tr01_two_factor_method === 'google')
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                                        <path d="m9 12 2 2 4-4"></path>
                                                    </svg>
                                                @elseif ($user->tr01_two_factor_method === 'mobile')
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                        <path d="M8 10h.01"></path>
                                                        <path d="M12 10h.01"></path>
                                                        <path d="M16 10h.01"></path>
                                                    </svg>
                                                @else
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h4 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #0f172a; line-height: 1.3;">Two-Factor Authentication is Active</h4>
                                                <span style="background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.725rem; font-weight: 700; padding: 2px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px;">
                                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span> ENABLED
                                                </span>
                                            </div>
                                            <p class="mt-3" style="margin: 0; font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                                                Primary Method: <strong style="color: #0f172a;">
                                                    @if ($user->tr01_two_factor_method === 'google')
                                                        Authenticator App (TOTP)
                                                    @elseif ($user->tr01_two_factor_method === 'mobile')
                                                        Mobile SMS OTP ({{ $user->getMaskedPhoneNumber() }})
                                                    @else
                                                        Email OTP ({{ $user->tr01_email }})
                                                    @endif
                                                </strong>
                                                <span style="margin: 0 6px;">&bull;</span> Enabled on {{ \Carbon\Carbon::parse($user->tr01_two_factor_confirmed_at)->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                        <div class="col-auto ms-auto">
                                            <form action="{{ route('profile.2fa.disable') }}" method="POST" onsubmit="return confirm('Are you sure you want to disable Two-Factor Authentication? Your account will be less secure.');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger" style="border-radius: 10px; font-weight: 600; padding: 8px 18px; background: #fef2f2; border-color: #fecaca; color: #dc2626;">
                                                    <em class="icon ni ni-shield-off"></em> Disable 2FA
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div style="height: 1px; background: #f1f5f9; margin-bottom: 1.25rem;"></div>

                                    <!-- Description & Switch Method -->
                                    <p style="font-size: 1rem; color: #475569; line-height: 1.5;">
                                        Your account is protected with multi-factor authentication. Every sign-in attempt requires a unique 6-digit verification code.
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">Switch Method</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($user->tr01_two_factor_method !== 'google')
                                                <a href="{{ route('profile.2fa.setup_google') }}" class="btn btn-dim btn-outline-primary" style="border-radius: 10px; font-weight: 600;">
                                                    <em class="icon ni ni-smartphone"></em> Switch to Authenticator App
                                                </a>
                                            @endif
                                            @if ($user->tr01_two_factor_method !== 'email')
                                                <a href="{{ route('profile.2fa.setup_email') }}" class="btn btn-dim btn-outline-primary" style="border-radius: 10px; font-weight: 600;">
                                                    <em class="icon ni ni-mail"></em> Switch to Email OTP
                                                </a>
                                            @endif
                                            @if ($user->tr01_two_factor_method !== 'mobile')
                                                <a href="{{ route('profile.2fa.setup_mobile') }}" class="btn btn-dim btn-outline-success" style="border-radius: 10px; font-weight: 600;">
                                                    <em class="icon ni ni-chat-circle"></em> Switch to Mobile SMS OTP
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RECOVERY CODES SECTION -->
                            @if ($user->tr01_two_factor_recovery_codes)
                                @php
                                    $codes = json_decode(decrypt($user->tr01_two_factor_recovery_codes), true) ?? [];
                                @endphp

                                <div class="card card-bordered" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(15,23,42,0.03);">
                                    <div class="card-inner p-4">
                                        <div class="row align-items-center g-3 mb-3 pb-3 border-bottom">
                                            <div class="col">
                                                <div class="d-flex align-items-center gap-2">
                                                    <h4 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f172a; line-height: 1.3;">Emergency Recovery Codes</h4>
                                                    @if (count($codes) > 0)
                                                        <span class="badge badge-dim badge-outline-primary" style="font-size: 0.75rem; border-radius: 12px;">{{ count($codes) }} Remaining</span>
                                                    @else
                                                        <span class="badge badge-dim badge-danger" style="font-size: 0.75rem; border-radius: 12px;">0 Remaining</span>
                                                    @endif
                                                </div>
                                                <p style="margin: 4px 0 0 0; font-size: 0.95rem; color: #64748b; line-height: 1.5;">Store these codes in a safe password manager. If you lose your phone or email access, each code can be used once to log in.</p>
                                            </div>
                                            <div class="col-auto">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if (count($codes) > 0)
                                                        <button type="button" class="btn btn-outline-light bg-white text-dark shadow-sm me-1" onclick="copyRecoveryCodes()" style="border-radius: 8px; font-weight: 600; padding: 7px 14px;">
                                                            <em class="icon ni ni-copy"></em> Copy All
                                                        </button>
                                                        <button type="button" class="btn btn-outline-light bg-white text-dark shadow-sm me-1" onclick="downloadRecoveryCodes()" style="border-radius: 8px; font-weight: 600; padding: 7px 14px;">
                                                            <em class="icon ni ni-download"></em> Download
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('profile.2fa.regenerate_recovery_codes') }}" method="POST" class="d-inline" onsubmit="return confirm('Generating new recovery codes will immediately invalidate any remaining existing codes. Continue?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-dim btn-primary" style="border-radius: 8px; font-weight: 600; padding: 7px 14px;">
                                                            <em class="icon ni ni-reload"></em> Generate New Codes
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        @if (count($codes) > 0)
                                            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
                                                <div class="row g-3" id="recovery-codes-container">
                                                    @foreach ($codes as $code)
                                                        <div class="col-12 col-sm-6 col-md-3">
                                                            <div class="recovery-code-pill" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 10px; font-family: 'JetBrains Mono', Consolas, monospace !important; font-weight: 700 !important; font-size: 0.875rem !important; color: #0f172a !important; text-align: center; letter-spacing: 0.05em; line-height: 1.5 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                                                {{ $code }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="alert alert-warning border-0 rounded-lg p-3 mb-0" style="background-color: #fff8e6; color: #8a5300;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <em class="icon ni ni-alert-fill fs-18px text-warning"></em>
                                                    <small style="font-size: 0.9rem; line-height: 1.5;">Each recovery code can only be used <strong>once</strong>. You have <strong>{{ count($codes) }}</strong> code(s) remaining.</small>
                                                </div>
                                            </div>
                                        @else
                                            <!-- ALL CODES USED ALERT AREA -->
                                            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 1.5rem; text-align: center;" class="my-2">
                                                <div style="width: 52px; height: 52px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                                    <em class="icon ni ni-alert-fill fs-24px"></em>
                                                </div>
                                                <h5 style="margin: 0 0 6px 0; font-size: 1.15rem; font-weight: 700; color: #991b1b;">All Emergency Recovery Codes Have Been Used!</h5>
                                                <p style="margin: 0 0 1.25rem 0; font-size: 0.95rem; color: #7f1d1d; line-height: 1.5; max-width: 680px; display: inline-block;">
                                                    You have exhausted all of your emergency backup codes. If you get locked out of your authenticator app or email address, you will not be able to log into your account using a backup code. Please generate a new set of codes immediately.
                                                </p>
                                                <div>
                                                    <form action="{{ route('profile.2fa.regenerate_recovery_codes') }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 10px; font-weight: 600; padding: 10px 22px;">
                                                            <em class="icon ni ni-reload me-1"></em> Generate 8 New Recovery Codes Now
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        @else
                            <!-- 2FA IS DISABLED -->
                            <div class="card card-bordered mb-4" style="background: #ffffff; border-radius: 16px; border: 1px solid #fde68a; border-left: 5px solid #f59e0b; box-shadow: 0 4px 14px rgba(15,23,42,0.03);">
                                <div class="card-inner p-3">
                                    <div class="row align-items-center g-3">
                                        <div class="col-auto">
                                            <div style="width: 52px; height: 52px; border-radius: 14px; background: #fffbeb; border: 1px solid #fde68a; color: #d97706; display: flex; align-items: center; justify-content: center;">
                                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h4 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #0f172a; line-height: 1.3;">Two-Factor Authentication is Disabled</h4>
                                                <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.775rem; font-weight: 700; padding: 2px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em;">
                                                    RECOMMENDED SECURITY ACTION
                                                </span>
                                            </div>
                                            <p style="margin: 0; font-size: 1rem; color: #64748b; line-height: 1.5;">Protect your account from unauthorized access by requiring an extra verification step during sign-in.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Choose a Two-Factor Authentication Method</h4>
                            <div class="row g-4">
                                <!-- Option 1: Authenticator App -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="card card-bordered h-100 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                                        <div class="card-inner p-4 d-flex flex-column justify-content-between h-100">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                                        </svg>
                                                    </div>
                                                    <span style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.825rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">Recommended</span>
                                                </div>
                                                <h5 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Authenticator App (TOTP)</h5>
                                                <p style="font-size: 0.975rem; color: #64748b; line-height: 1.5; margin: 0;">Use a security app like <strong>Google Authenticator</strong>, <strong>Authy</strong>, <strong>Microsoft Authenticator</strong>, or 1Password to generate 6-digit verification passcodes. Works offline without cellular signals.</p>
                                            </div>
                                            <div class="pt-4 mt-auto">
                                                <a href="{{ route('profile.2fa.setup_google') }}" class="btn btn-primary btn-block fs-6" style="border-radius: 10px; height: 44px; font-weight: 600;">
                                                    <span>Setup Authenticator App</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Email OTP -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="card card-bordered h-100 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                                        <div class="card-inner p-4 d-flex flex-column justify-content-between h-100">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                            <polyline points="22,6 12,13 2,6"></polyline>
                                                        </svg>
                                                    </div>
                                                    <span style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.825rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">Email Delivery</span>
                                                </div>
                                                <h5 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Email One-Time Passcode (OTP)</h5>
                                                <p style="font-size: 0.975rem; color: #64748b; line-height: 1.5; margin: 0;">Receive a single-use 6-digit security code sent directly to your registered email address <strong>({{ $user->tr01_email }})</strong> every time you log in. Simple and requires no mobile app downloads.</p>
                                            </div>
                                            <div class="pt-4 mt-auto">
                                                <a href="{{ route('profile.2fa.setup_email') }}" class="btn btn-outline-primary btn-block fs-6" style="border-radius: 10px; height: 44px; font-weight: 600;">
                                                    <span>Setup Email OTP</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 3: Mobile SMS OTP -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="card card-bordered h-100 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                                        <div class="card-inner p-4 d-flex flex-column justify-content-between h-100">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                            <path d="M8 10h.01"></path>
                                                            <path d="M12 10h.01"></path>
                                                            <path d="M16 10h.01"></path>
                                                        </svg>
                                                    </div>
                                                    <span style="background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.825rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">SMS Gateway</span>
                                                </div>
                                                <h5 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Mobile SMS OTP (Way2Send)</h5>
                                                <p style="font-size: 0.975rem; color: #64748b; line-height: 1.5; margin: 0;">Receive an instant single-use 6-digit security code directly via SMS to your registered mobile number <strong>({{ $user->getMaskedPhoneNumber() }})</strong> using the high-speed Way2Send gateway.</p>
                                            </div>
                                            <div class="pt-4 mt-auto">
                                                <a href="{{ route('profile.2fa.setup_mobile') }}" class="btn btn-outline-success btn-block fs-6" style="border-radius: 10px; height: 44px; font-weight: 600;">
                                                    <span>Setup Mobile SMS OTP</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

    @if ($user->tr01_two_factor_confirmed_at && $user->tr01_two_factor_recovery_codes)
        <script>
            function getRecoveryCodesList() {
                const elements = document.querySelectorAll('#recovery-codes-container .recovery-code-pill');
                let codes = [];
                elements.forEach(el => codes.push(el.innerText.trim()));
                return codes;
            }

            function copyRecoveryCodes() {
                const codes = getRecoveryCodesList().join('\n');
                navigator.clipboard.writeText(codes).then(() => {
                    alert('Recovery codes copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }

            function downloadRecoveryCodes() {
                const codes = getRecoveryCodesList().join('\n');
                const text = "LIMS Emergency 2FA Recovery Codes\n" +
                             "Generated for: {{ $user->tr01_email }}\n" +
                             "Date: " + new Date().toLocaleDateString() + "\n\n" +
                             "WARNING: Keep these codes secret and store them safely.\n\n" +
                             codes;
                
                const element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
                element.setAttribute('download', 'lims-2fa-recovery-codes.txt');
                element.style.display = 'none';
                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);
            }
        </script>
    @endif
@endsection
