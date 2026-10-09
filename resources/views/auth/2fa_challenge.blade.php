<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="author" content="LIMS">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('backAssets/images/logo.jpeg') }}">
    <!-- Page Title  -->
    <title>Two-Factor Authentication | LIMS</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <!-- DashLite StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css?ver=3.2.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css?ver=3.2.0') }}">

    <style>
        :root {
            --primary-color: #6255f6;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body.pg-auth-2fa {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 1.5rem;
        }

        /* Ambient Glow Background Accents */
        .auth-bg-glow-1 {
            position: absolute;
            top: -120px;
            left: -120px;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(98, 85, 246, 0.12) 0%, rgba(98, 85, 246, 0) 70%);
            pointer-events: none;
        }

        .auth-bg-glow-2 {
            position: absolute;
            bottom: -120px;
            right: -120px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0) 70%);
            pointer-events: none;
        }

        .two-fa-container {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        .brand-logo-wrapper {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .brand-logo-wrapper img {
            max-height: 100px;
            width: auto;
            object-fit: contain;
            transition: transform 0.2s ease;
        }

        .brand-logo-wrapper img:hover {
            transform: scale(1.03);
        }

        .two-fa-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px rgba(15, 23, 42, 0.1);
            padding: 2.25rem 2.5rem;
            transition: all 0.3s ease;
        }

        @media (max-width: 576px) {
            .two-fa-container {
                max-width: 100%;
            }
            .two-fa-card {
                padding: 1.75rem 1.25rem;
            }
        }

        .icon-badge-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .icon-badge {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 0 1px rgba(98, 85, 246, 0.15);
            animation: pulseSoft 3s infinite ease-in-out;
        }

        @keyframes pulseSoft {
            0%, 100% {
                box-shadow: inset 0 0 0 1px rgba(98, 85, 246, 0.15), 0 0 0 0 rgba(98, 85, 246, 0.2);
            }
            50% {
                box-shadow: inset 0 0 0 1px rgba(98, 85, 246, 0.25), 0 0 0 10px rgba(98, 85, 246, 0);
            }
        }

        .two-fa-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .two-fa-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .two-fa-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.45;
            margin-bottom: 0;
        }

        .method-info-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-top: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-align: left;
        }

        .method-info-badge .badge-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .method-info-badge .badge-text {
            font-size: 0.85rem;
            color: #334155;
            line-height: 1.4;
        }

        .method-info-badge .badge-text strong {
            color: var(--text-dark);
            font-weight: 600;
        }

        .custom-alert {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border: 1px solid transparent;
        }

        .custom-alert-success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .custom-alert-danger {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #475569;
            margin-bottom: 0.4rem;
            display: block;
            text-align: center;
        }

        .code-input-wrapper {
            position: relative;
            margin-bottom: 0.35rem;
        }

        .code-input {
            font-family: 'JetBrains Mono', monospace, sans-serif !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.08em !important;
            text-align: center !important;
            height: 54px !important;
            border-radius: 12px !important;
            border: 2px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02) !important;
            width: 100% !important;
        }

        .code-input:focus {
            background-color: #ffffff !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 4px rgba(98, 85, 246, 0.18) !important;
            outline: none !important;
        }

        .code-input::placeholder {
            color: #cbd5e1 !important;
            letter-spacing: 0.2em !important;
            font-weight: 500 !important;
        }

        .input-hint {
            font-size: 0.775rem;
            color: #94a3b8;
            text-align: center;
            margin-top: 0.35rem;
            margin-bottom: 1.25rem;
        }

        .btn-verify {
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            box-shadow: 0 8px 18px -4px rgba(98, 85, 246, 0.38);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-verify:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px -4px rgba(98, 85, 246, 0.48);
            background: linear-gradient(135deg, #5345ed 0%, #4338ca 100%);
            color: #ffffff;
        }

        .btn-verify:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px -2px rgba(98, 85, 246, 0.3);
        }

        .back-link-wrapper {
            text-align: center;
            margin-top: 1.15rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .back-link {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
</head>

<body class="nk-body bg-white npc-general pg-auth pg-auth-2fa">

    <div class="page-wrapper">
        <!-- Ambient background glows -->
        <div class="auth-bg-glow-1"></div>
        <div class="auth-bg-glow-2"></div>

        <div class="two-fa-container">
            <!-- Logo -->
            <div class="brand-logo-wrapper">
                <a href="{{ url('/') }}" class="logo-link">
                    <img src="{{ asset('backAssets/images/logo_lg.png') }}" alt="LIMS Logo">
                </a>
            </div>

            <!-- 2FA Card -->
            <div class="two-fa-card">
                <!-- Icon Badge -->
                <div class="icon-badge-wrapper">
                    <div class="icon-badge">
                        @if ($user->tr01_two_factor_method === 'google')
                            <!-- Authenticator Shield SVG Icon -->
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        @elseif ($user->tr01_two_factor_method === 'mobile')
                            <!-- Mobile SMS SVG Icon -->
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                <path d="M8 10h.01"></path>
                                <path d="M12 10h.01"></path>
                                <path d="M16 10h.01"></path>
                            </svg>
                        @else
                            <!-- Email Lock SVG Icon -->
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                <!-- Header -->
                <div class="two-fa-header">
                    <h4 class="two-fa-title">Two-Factor Authentication</h4>
                    <p class="two-fa-description">Enhance your account security with 2FA verification.</p>

                    <!-- Method Info -->
                    <div class="method-info-badge">
                        <div class="badge-icon">
                            @if ($user->tr01_two_factor_method === 'google')
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                </svg>
                            @elseif ($user->tr01_two_factor_method === 'mobile')
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    <path d="M8 10h.01"></path>
                                    <path d="M12 10h.01"></path>
                                    <path d="M16 10h.01"></path>
                                </svg>
                            @else
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            @endif
                        </div>
                        <div class="badge-text">
                            @if ($user->tr01_two_factor_method === 'google')
                                Open your <strong>Authenticator App</strong> and enter the 6-digit verification code.
                            @elseif($user->tr01_two_factor_method === 'mobile')
                                We sent a 6-digit code via SMS to your registered mobile number: <strong>{{ $user->getMaskedPhoneNumber() }}</strong>
                            @elseif($user->tr01_two_factor_method === 'email')
                                We sent a 6-digit code to <strong>{{ preg_replace('/(?<=...).(?=.*@)/', '*', $user->tr01_email) }}</strong>
                            @else
                                Enter your 6-digit authentication or recovery code below.
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('auth.2fa.verify') }}" method="POST">
                    @csrf

                    @if (session('success'))
                        <div class="custom-alert custom-alert-success">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="custom-alert custom-alert-danger">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <div class="form-group mb-0">
                        <label class="form-label-custom" for="code">Security Code</label>
                        <div class="code-input-wrapper">
                            <input type="text"
                                class="form-control code-input"
                                id="code" name="code" placeholder="••••••" required
                                autofocus
                                autocomplete="one-time-code"
                                maxlength="32"
                                oninput="this.value = this.value.replace(/[^0-9a-zA-Z-]/g, '');">
                        </div>
                        <div class="input-hint">
                            Enter 6-digit authentication code or emergency recovery code
                        </div>
                    </div>

                    <div class="form-group mt-2 mb-0">
                        <button type="submit" class="btn-verify">
                            <span>Verify &amp; Continue</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>

                    @if (in_array($user->tr01_two_factor_method, ['mobile', 'email']))
                        <div class="text-center mt-3">
                            <button type="button" id="btn-resend-otp" class="btn btn-link btn-sm text-primary text-decoration-none" style="font-weight: 600; font-size: 0.85rem;">
                                <span id="resend-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                <span id="resend-text">Didn't receive code? Resend {{ $user->tr01_two_factor_method === 'mobile' ? 'SMS' : 'Email' }} OTP</span>
                            </button>
                            <div id="resend-status" class="small mt-1 d-none font-weight-bold"></div>
                        </div>
                    @endif
                </form>

                <div class="back-link-wrapper">
                    <a href="{{ route('user_login') }}" class="back-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Cancel and return to login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('backAssets/assets/js/bundle.js?ver=3.2.0') }}"></script>
    <script src="{{ asset('backAssets/assets/js/scripts.js?ver=3.2.0') }}"></script>

    <script>
        const btnResend = document.getElementById('btn-resend-otp');
        if (btnResend) {
            btnResend.addEventListener('click', function() {
                const spinner = document.getElementById('resend-spinner');
                const text = document.getElementById('resend-text');
                const status = document.getElementById('resend-status');

                btnResend.disabled = true;
                spinner.classList.remove('d-none');
                status.classList.add('d-none');

                fetch('{{ route('auth.2fa.resend') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('d-none');
                    status.classList.remove('d-none');

                    if (data.success) {
                        status.className = 'small mt-1 text-success font-weight-bold';
                        status.innerText = data.message || 'OTP resent successfully!';
                        
                        // Start 30s countdown
                        let countdown = 30;
                        const originalText = text.innerText;
                        const interval = setInterval(() => {
                            text.innerText = `Resend in ${countdown}s`;
                            countdown--;
                            if (countdown < 0) {
                                clearInterval(interval);
                                text.innerText = originalText;
                                btnResend.disabled = false;
                            }
                        }, 1000);
                    } else {
                        btnResend.disabled = false;
                        status.className = 'small mt-1 text-danger font-weight-bold';
                        status.innerText = data.message || 'Failed to resend code. Please try again.';
                    }
                })
                .catch(err => {
                    spinner.classList.add('d-none');
                    btnResend.disabled = false;
                    status.classList.remove('d-none');
                    status.className = 'small mt-1 text-danger font-weight-bold';
                    status.innerText = 'Network error. Please try again.';
                });
            });
        }
    </script>
</body>

</html>
