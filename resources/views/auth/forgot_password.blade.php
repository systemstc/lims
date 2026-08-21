<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <title>Reset Password | LIMS</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(124, 58, 237, 0.08) 0%, transparent 45%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* ----------------------------------------------------
           Keyframe Animations
        ---------------------------------------------------- */
        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: scale(0.88) translateY(30px);
            }
            60% {
                opacity: 1;
                transform: scale(1.02) translateY(-5px);
            }
            80% {
                transform: scale(0.99) translateY(2px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes lockBouncePulse {
            0%, 100% {
                transform: translateY(0) scale(1);
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
            }
            50% {
                transform: translateY(-7px) scale(1.06);
                box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.22);
            }
        }

        @keyframes lockIconZoom {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.12) rotate(-3deg); }
        }

        @keyframes floatZoom1 {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.75;
            }
            50% {
                transform: translate(-5px, -7px) scale(1.35);
                opacity: 1;
            }
        }

        @keyframes floatZoom2 {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.75;
            }
            50% {
                transform: translate(6px, 7px) scale(1.4);
                opacity: 1;
            }
        }

        @keyframes plusRotateBounce1 {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            50% { transform: translate(5px, -5px) rotate(90deg) scale(1.3); }
        }

        @keyframes plusRotateBounce2 {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            50% { transform: translate(-5px, 5px) rotate(-90deg) scale(1.3); }
        }

        @keyframes bgArcRotateZoom {
            0%, 100% {
                transform: scale(1) rotate(0deg);
                opacity: 0.85;
            }
            50% {
                transform: scale(1.06) rotate(10deg);
                opacity: 1;
            }
        }

        @keyframes dotTwinkleZoom {
            0%, 100% {
                transform: scale(1);
                opacity: 0.4;
                background-color: #a78bfa;
            }
            50% {
                transform: scale(1.6);
                opacity: 0.95;
                background-color: #6366f1;
            }
        }

        /* ----------------------------------------------------
           Background Concentric Arcs (Bottom-Left & Top-Right)
        ---------------------------------------------------- */
        /* Bottom-Left Concentric Arcs - Clearly Visible */
        .bg-arc-bl-1 {
            position: absolute;
            left: -60px;
            bottom: -60px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, 0.35);
            pointer-events: none;
            animation: bgArcRotateZoom 12s infinite ease-in-out;
            transform-origin: center center;
        }
        
        .bg-arc-bl-2 {
            position: absolute;
            left: -140px;
            bottom: -140px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, 0.22);
            pointer-events: none;
            animation: bgArcRotateZoom 16s infinite ease-in-out reverse;
            transform-origin: center center;
        }

        .bg-arc-bl-3 {
            position: absolute;
            left: -220px;
            bottom: -220px;
            width: 640px;
            height: 640px;
            border-radius: 50%;
            border: 1.5px solid rgba(99, 102, 241, 0.12);
            pointer-events: none;
            animation: bgArcRotateZoom 20s infinite ease-in-out;
            transform-origin: center center;
        }

        /* Top-Right Concentric Arcs */
        .bg-arc-tr-1 {
            position: absolute;
            right: -60px;
            top: -60px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, 0.35);
            pointer-events: none;
            animation: bgArcRotateZoom 14s infinite ease-in-out reverse;
            transform-origin: center center;
        }

        .bg-arc-tr-2 {
            position: absolute;
            right: -140px;
            top: -140px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, 0.22);
            pointer-events: none;
            animation: bgArcRotateZoom 18s infinite ease-in-out;
            transform-origin: center center;
        }

        /* ----------------------------------------------------
           Background Dot Grids (Top-Left & Bottom-Right)
        ---------------------------------------------------- */
        .bg-dot-grid {
            position: absolute;
            display: grid;
            grid-template-columns: repeat(5, 8px);
            gap: 14px;
            pointer-events: none;
        }

        .bg-dot-grid-tl {
            top: 50px;
            left: 60px;
        }

        .bg-dot-grid-br {
            bottom: 50px;
            right: 60px;
        }

        .bg-dot-grid span {
            width: 5px;
            height: 5px;
            background-color: #a78bfa;
            border-radius: 50%;
            display: block;
            animation: dotTwinkleZoom 3.2s infinite ease-in-out;
        }

        /* Staggered Delays for Dot Grids */
        .bg-dot-grid span:nth-child(1) { animation-delay: 0.0s; }
        .bg-dot-grid span:nth-child(2) { animation-delay: 0.2s; }
        .bg-dot-grid span:nth-child(3) { animation-delay: 0.4s; }
        .bg-dot-grid span:nth-child(4) { animation-delay: 0.6s; }
        .bg-dot-grid span:nth-child(5) { animation-delay: 0.8s; }
        .bg-dot-grid span:nth-child(6) { animation-delay: 0.3s; }
        .bg-dot-grid span:nth-child(7) { animation-delay: 0.5s; }
        .bg-dot-grid span:nth-child(8) { animation-delay: 0.7s; }
        .bg-dot-grid span:nth-child(9) { animation-delay: 0.9s; }
        .bg-dot-grid span:nth-child(10) { animation-delay: 1.1s; }
        .bg-dot-grid span:nth-child(11) { animation-delay: 0.6s; }
        .bg-dot-grid span:nth-child(12) { animation-delay: 0.8s; }
        .bg-dot-grid span:nth-child(13) { animation-delay: 1.0s; }
        .bg-dot-grid span:nth-child(14) { animation-delay: 1.2s; }
        .bg-dot-grid span:nth-child(15) { animation-delay: 1.4s; }

        /* ----------------------------------------------------
           Main Card Container
        ---------------------------------------------------- */
        .card-container {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border-radius: 20px;
            padding: 44px 40px 36px 40px;
            box-shadow: 0 15px 45px -10px rgba(99, 102, 241, 0.12), 0 20px 25px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
            position: relative;
            z-index: 10;
            animation: cardEntrance 0.75s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-container:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 20px 50px -10px rgba(99, 102, 241, 0.18), 0 20px 30px -5px rgba(0, 0, 0, 0.03);
        }

        /* ----------------------------------------------------
           Header Lock Circle & Micro Accents
        ---------------------------------------------------- */
        .icon-box-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 24px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1edfe;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: lockBouncePulse 4s infinite ease-in-out;
            transition: transform 0.3s ease;
        }

        .icon-circle svg {
            width: 36px;
            height: 36px;
            color: #6366f1;
            animation: lockIconZoom 4s infinite ease-in-out;
        }

        /* Floating Accents around Icon Circle */
        .accent-circle-tl {
            position: absolute;
            top: 2px;
            left: -4px;
            width: 8px;
            height: 8px;
            border: 1.5px solid #c084fc;
            border-radius: 50%;
            animation: floatZoom1 3s infinite ease-in-out;
        }

        .accent-circle-br {
            position: absolute;
            bottom: 6px;
            right: -2px;
            width: 9px;
            height: 9px;
            border: 1.5px solid #c084fc;
            border-radius: 50%;
            animation: floatZoom2 3.5s infinite ease-in-out;
        }

        .accent-plus-tr {
            position: absolute;
            top: 14px;
            right: -16px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 300;
            line-height: 1;
            animation: plusRotateBounce1 4s infinite ease-in-out;
        }

        .accent-plus-bl {
            position: absolute;
            bottom: 14px;
            left: -16px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 300;
            line-height: 1;
            animation: plusRotateBounce2 4.2s infinite ease-in-out;
        }

        /* ----------------------------------------------------
           Title & Description
        ---------------------------------------------------- */
        .title {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: -0.4px;
        }

        .description {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            line-height: 1.55;
            margin-bottom: 28px;
            font-weight: 400;
            padding: 0 8px;
        }

        /* ----------------------------------------------------
           Form Controls
        ---------------------------------------------------- */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #6366f1;
            pointer-events: none;
            transition: transform 0.25s ease, color 0.25s ease;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 10px 16px 10px 46px;
            font-size: 14.5px;
            font-family: inherit;
            color: #0f172a;
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            outline: none;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            transform: scale(1.01);
        }

        .input-wrapper:focus-within .input-icon {
            color: #4f46e5;
            transform: translateY(-50%) scale(1.15);
        }

        .is-invalid {
            border-color: #ef4444 !important;
            animation: shakeError 0.4s ease-in-out;
        }

        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 12.5px;
            margin-top: 6px;
            display: block;
            font-weight: 500;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
            line-height: 1.4;
            animation: cardEntrance 0.5s ease-out;
        }

        /* ----------------------------------------------------
           Submit Button
        ---------------------------------------------------- */
        .btn-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        .btn-submit:hover {
            opacity: 0.96;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.48);
            transform: translateY(-2px) scale(1.02);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-submit:hover svg {
            transform: translateX(4px) translateY(-3px) scale(1.2);
        }

        /* ----------------------------------------------------
           Divider Line OR
        ---------------------------------------------------- */
        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background-color: #e2e8f0;
        }

        .divider-text {
            padding: 0 16px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        /* ----------------------------------------------------
           Back to Login Link
        ---------------------------------------------------- */
        .back-to-login {
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14.5px;
            font-weight: 600;
            color: #4f46e5;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .back-link:hover {
            color: #3730a3;
            transform: translateX(-4px) scale(1.03);
        }

        .back-link svg {
            width: 16px;
            height: 16px;
            transition: transform 0.25s ease;
        }

        .back-link:hover svg {
            transform: translateX(-3px) scale(1.15);
        }

        @media (max-width: 600px) {
            .card-container {
                padding: 32px 24px;
            }
            .bg-dot-grid {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Bottom-Left Concentric Arcs (Crisp & Visible) -->
    <div class="bg-arc-bl-1"></div>
    <div class="bg-arc-bl-2"></div>
    <div class="bg-arc-bl-3"></div>

    <!-- Top-Right Concentric Arcs (Fills Top-Right Blank Space) -->
    <div class="bg-arc-tr-1"></div>
    <div class="bg-arc-tr-2"></div>

    <!-- Top-Left Dot Grid (Fills Top-Left Blank Space) -->
    <div class="bg-dot-grid bg-dot-grid-tl">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <!-- Bottom-Right Dot Grid -->
    <div class="bg-dot-grid bg-dot-grid-br">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <!-- Central Card -->
    <div class="card-container">
        
        <!-- Header Illustration Icon -->
        <div class="icon-box-wrapper">
            <div class="accent-circle-tl"></div>
            <div class="accent-circle-br"></div>
            <div class="accent-plus-tr">+</div>
            <div class="accent-plus-bl">+</div>
            <div class="icon-circle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    <circle cx="12" cy="15" r="1.2" fill="currentColor" />
                </svg>
            </div>
        </div>

        <!-- Title & Description -->
        <h2 class="title">Reset Password</h2>
        <p class="description">Enter your email address and we'll send you a link to reset your password.</p>

        <!-- Session Flash Message -->
        @if (session('success'))
            <div class="alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email"
                        value="{{ old('email') }}" 
                        placeholder="Enter your email address"
                        required 
                        autocomplete="email" 
                        autofocus>
                </div>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                <span>Send Password Reset Link</span>
            </button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">OR</span>
            <div class="divider-line"></div>
        </div>

        <!-- Back to Login -->
        <div class="back-to-login">
            <a href="{{ route('user_login') }}" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Login</span>
            </a>
        </div>

    </div>

</body>
</html>
