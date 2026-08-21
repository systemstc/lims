<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <title>Reset Password | Textile Testing LIMS</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            background: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrapper {
            width: 100vw;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ====================================================
           Left Side - 50% Width Pastel Lavender Branding Section
        ==================================================== */
        .branding-section {
            flex: 1;
            width: 50%;
            background: linear-gradient(135deg, #6255f6 0%, #7b71ff 40%, #978dfd 75%, #b4adff 100%);
            color: #ffffff;
            padding: 60px 70px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Background Glows */
        .bg-light-glow-1 {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, transparent 65%);
            filter: blur(60px);
            pointer-events: none;
        }

        .bg-light-glow-2 {
            position: absolute;
            bottom: -80px;
            right: 10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.28) 0%, transparent 70%);
            filter: blur(70px);
            pointer-events: none;
        }

        /* Sparkle Dots */
        .sparkle {
            position: absolute;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 12px 3px #ffffff;
            animation: sparklePulse 3.5s infinite ease-in-out;
            pointer-events: none;
        }
        .sparkle-1 { top: 18%; right: 28%; animation-delay: 0s; }
        .sparkle-2 { top: 42%; left: 14%; animation-delay: 1s; }
        .sparkle-3 { top: 68%; right: 42%; animation-delay: 2s; }
        .sparkle-4 { bottom: 15%; right: 18%; animation-delay: 1.5s; }

        @keyframes sparklePulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.8); }
        }

        /* Wavy Background Graphic SVG */
        .wave-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 42%;
            pointer-events: none;
            opacity: 0.6;
        }

        /* Hyper-Realistic 3D Glossy Bubbles */
        .glossy-orb {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 25%, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.45) 25%, rgba(196, 181, 253, 0.25) 60%, rgba(139, 92, 246, 0.45) 100%);
            box-shadow: 
                inset -5px -7px 14px rgba(124, 58, 237, 0.35),
                inset 5px 7px 12px rgba(255, 255, 255, 0.95),
                0 14px 28px rgba(55, 40, 160, 0.22);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.7);
            pointer-events: none;
        }

        .glossy-orb::after {
            content: '';
            position: absolute;
            top: 15%;
            left: 20%;
            width: 25%;
            height: 25%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
        }

        .orb-top-left {
            top: 10%;
            left: 8%;
            width: 120px;
            height: 120px;
            animation: floatOrb 8s infinite ease-in-out;
        }

        .orb-top-right {
            top: 25%;
            right: 28%;
            width: 72px;
            height: 72px;
            animation: floatOrb 6.5s infinite ease-in-out reverse;
        }

        .orb-bottom-1 {
            bottom: 22%;
            left: 32%;
            width: 55px;
            height: 55px;
            animation: floatOrb 9s infinite ease-in-out 1s;
        }

        .orb-bottom-2 {
            bottom: 12%;
            right: 8%;
            width: 42px;
            height: 42px;
            animation: floatOrb 6.5s infinite ease-in-out reverse;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-16px) scale(1.06); }
        }

        /* Key Security 3D Graphic */
        .key-graphic {
            position: absolute;
            right: 4%;
            bottom: 6%;
            width: 320px;
            height: auto;
            pointer-events: none;
            z-index: 3;
            animation: floatKey 6s infinite ease-in-out;
            filter: drop-shadow(0 22px 40px rgba(55, 40, 160, 0.28));
        }

        @keyframes floatKey {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(2deg); }
        }

        /* Branding Top Section */
        .brand-top {
            z-index: 5;
            position: relative;
            margin-top: 65px;
        }

        .logo-box {
            width: 290px;
            height: 100px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.14);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .logo-box img {
            width: 250px;
            object-fit: contain;
        }

        .brand-title h2 {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            letter-spacing: -0.4px;
        }

        .brand-title h2.sub-title {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            margin-top: 2px;
        }

        .brand-pill-divider {
            width: 48px;
            height: 4px;
            background: #ffffff;
            border-radius: 4px;
            margin-top: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Branding Middle Section */
        .brand-mid {
            z-index: 5;
            position: relative;
            max-width: 440px;
            margin-top: 24px;
        }

        .brand-mid h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .brand-mid p {
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            font-weight: 400;
        }

        /* Branding Footer Section */
        .brand-footer {
            z-index: 5;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .brand-footer svg {
            width: 16px;
            height: 16px;
        }

        /* ====================================================
           Right Side - 50% Width Clean Form Section
        ==================================================== */
        .login-section {
            flex: 1;
            width: 50%;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .bg-radial-glow {
            position: absolute;
            top: 20%;
            right: 15%;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(98, 85, 246, 0.07) 0%, transparent 65%);
            pointer-events: none;
        }

        /* Concentric Arcs Bottom-Right */
        .bg-arc-br-1 {
            position: absolute;
            right: -60px;
            bottom: -60px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            border: 2px solid rgba(98, 85, 246, 0.22);
            pointer-events: none;
            animation: bgArcRotateZoom 14s infinite ease-in-out;
        }

        .bg-arc-br-2 {
            position: absolute;
            right: -140px;
            bottom: -140px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            border: 1.5px solid rgba(98, 85, 246, 0.1);
            pointer-events: none;
            animation: bgArcRotateZoom 18s infinite ease-in-out reverse;
        }

        @keyframes bgArcRotateZoom {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            50% { transform: scale(1.06) rotate(10deg); opacity: 1; }
        }

        /* Top-Right Dot Grid Matrix */
        .bg-dot-grid {
            position: absolute;
            top: 40px;
            right: 50px;
            display: grid;
            grid-template-columns: repeat(5, 8px);
            gap: 13px;
            pointer-events: none;
        }

        .bg-dot-grid span {
            width: 5px;
            height: 5px;
            background-color: #c4b5fd;
            border-radius: 50%;
            display: block;
            animation: dotTwinkleZoom 3.2s infinite ease-in-out;
        }

        @keyframes dotTwinkleZoom {
            0%, 100% { transform: scale(1); opacity: 0.35; background-color: #c4b5fd; }
            50% { transform: scale(1.65); opacity: 0.9; background-color: #6255f6; }
        }

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

        /* Main Form Card Container */
        .card-container {
            width: 100%;
            max-width: 550px;
            background: #ffffff;
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow: 0 20px 50px -12px rgba(98, 85, 246, 0.12), 0 8px 24px -4px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            z-index: 10;
            animation: cardEntrance 0.75s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-container:hover {
            transform: translateY(-3px) scale(1.008);
            box-shadow: 0 24px 60px -12px rgba(98, 85, 246, 0.16), 0 12px 30px -4px rgba(0, 0, 0, 0.04);
        }

        @keyframes cardEntrance {
            0% { opacity: 0; transform: scale(0.88) translateY(30px); }
            60% { opacity: 1; transform: scale(1.02) translateY(-5px); }
            80% { transform: scale(0.99) translateY(2px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Shield Header Icon */
        .icon-box-wrapper {
            position: relative;
            width: 76px;
            height: 76px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #f1edfe;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: shieldBouncePulse 4s infinite ease-in-out;
        }

        .icon-circle svg {
            width: 36px;
            height: 36px;
            color: #6255f6;
        }

        @keyframes shieldBouncePulse {
            0%, 100% { transform: translateY(0) scale(1); box-shadow: 0 0 0 0 rgba(98, 85, 246, 0); }
            50% { transform: translateY(-6px) scale(1.05); box-shadow: 0 10px 25px -5px rgba(98, 85, 246, 0.2); }
        }

        .title {
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: -0.4px;
        }

        .title-dash {
            width: 64px;
            height: 4px;
            background: #6255f6;
            border-radius: 4px;
            margin: 0 auto 26px auto;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 16.5px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 25px;
            height: 25px;
            color: #6255f6;
            pointer-events: none;
            transition: transform 0.25s ease, color 0.25s ease;
        }

        .form-control {
            width: 100%;
            height: 58px;
            padding: 10px 48px 10px 48px;
            font-size: 15px;
            font-family: inherit;
            color: #0f172a;
            background-color: #fcfdfe;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            outline: none;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 17px;
        }

        .form-control:focus {
            border-color: #6255f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(98, 85, 246, 0.14);
            transform: scale(1.01);
        }

        /* Readonly Email Input Styling */
        .form-control.readonly-email {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            cursor: not-allowed;
            border-color: #cbd5e1;
        }

        .readonly-lock-badge {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            pointer-events: none;
        }

        .readonly-lock-badge svg {
            width: 14px;
            height: 14px;
            color: #6255f6;
        }

        .input-wrapper:focus-within .input-icon {
            color: #4f46e5;
            transform: translateY(-50%) scale(1.15);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .password-toggle:hover {
            color: #6255f6;
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle svg {
            width: 25px;
            height: 25px;
        }

        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: block;
            font-weight: 600;
        }

        /* Submit Button with Icon */
        .btn-submit {
            width: 100%;
            height: 58px;
            background: linear-gradient(135deg, #5b50f6 0%, #7c71ff 50%, #988eff 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 19px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 22px rgba(91, 80, 246, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: 26px;
        }

        .btn-submit svg.btn-icon {
            width: 25px;
            height: 25px;
            transition: transform 0.25s ease;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            right: 15%;
            top: 50%;
            transform: translateY(-50%);
            width: 35px;
            height: 35px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .btn-submit:hover {
            opacity: 0.97;
            box-shadow: 0 12px 28px rgba(91, 80, 246, 0.48);
            transform: translateY(-2px) scale(1.015);
        }

        .btn-submit:hover svg.btn-icon {
            transform: translateX(4px) scale(1.15);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .back-to-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 22px;
            font-size: 15.5px;
            font-weight: 700;
            color: #6255f6;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-to-login:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .back-to-login svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 900px) {
            .page-wrapper { flex-direction: column; }
            .branding-section { width: 100%; display: none; }
            .login-section { width: 100%; padding: 24px; }
            .card-container { padding: 32px 24px; }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <!-- Left Side - Equal 50% Width Branding Section -->
        <div class="branding-section">
            
            <!-- Ambient Background Glows & Sparkles -->
            <div class="bg-light-glow-1"></div>
            <div class="bg-light-glow-2"></div>
            <div class="sparkle sparkle-1"></div>
            <div class="sparkle sparkle-2"></div>
            <div class="sparkle sparkle-3"></div>
            <div class="sparkle sparkle-4"></div>

            <!-- Hyper-Realistic 3D Glossy Liquid Glass Bubbles -->
            <div class="glossy-orb orb-top-left"></div>
            <div class="glossy-orb orb-top-right"></div>
            <div class="glossy-orb orb-bottom-1"></div>
            <div class="glossy-orb orb-bottom-2"></div>

            <!-- Wave Background SVG Graphic -->
            <svg class="wave-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 400" preserveAspectRatio="none">
                <path fill="rgba(255, 255, 255, 0.35)" d="M0,224L60,202.7C120,181,240,139,360,149.3C480,160,600,224,720,234.7C840,245,960,203,1080,176C1200,149,1320,139,1380,133.3L1440,128L1440,400L1380,400C1320,400,1200,400,1080,400C960,400,840,400,720,400C600,400,480,400,360,400C240,400,120,400,60,400L0,400Z"></path>
                <path fill="rgba(255, 255, 255, 0.22)" d="M0,128L60,144C120,160,240,192,360,202.7C480,213,600,203,720,181.3C840,160,960,128,1080,138.7C1200,149,1320,203,1380,229.3L1440,256L1440,400L1380,400C1320,400,1200,400,1080,400C960,400,840,400,720,400C600,400,480,400,360,400C240,400,120,400,60,400L0,400Z"></path>
            </svg>

            <!-- 3D Key & Security Lock Graphic -->
            <svg class="key-graphic" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 280 280" fill="none">
                <rect x="70" y="140" width="140" height="100" rx="20" fill="url(#padlockBody)" />
                <path d="M100 140 V95 C100 70, 180 70, 180 95 V140" stroke="url(#padlockShackle)" stroke-width="18" stroke-linecap="round" fill="none" />
                <circle cx="140" cy="180" r="14" fill="#6255f6" />
                <rect x="135" y="186" width="10" height="24" rx="4" fill="#6255f6" />
                <defs>
                    <linearGradient id="padlockBody" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.98" />
                        <stop offset="100%" stop-color="#ddd6fe" stop-opacity="0.9" />
                    </linearGradient>
                    <linearGradient id="padlockShackle" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.95" />
                        <stop offset="100%" stop-color="#c4b5fd" stop-opacity="0.85" />
                    </linearGradient>
                </defs>
            </svg>

            <!-- Top Stacked Logo & Title -->
            <div class="brand-top">
                <div class="logo-box">
                    <img src="{{ asset('frontAssets/logo_lg.png') }}" alt="Textiles Committee Logo">
                </div>
                <div class="brand-title">
                    <h2>Laboratory Information</h2>
                    <h2 class="sub-title">Management System</h2>
                    <div class="brand-pill-divider"></div>
                </div>
            </div>

            <!-- Middle Text Content -->
            <div class="brand-mid">
                <h3>Reset Password</h3>
                <p>Create a new secure password for your LIMS account to restore access to your laboratory portal.</p>
            </div>

            <!-- Footer Badge -->
            <div class="brand-footer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>&copy; 2024 System Cell. All Rights Reserved.</span>
            </div>

        </div>

        <!-- Right Side - Equal 50% Width Clean Form Section -->
        <div class="login-section">

            <!-- Radial Background Glow -->
            <div class="bg-radial-glow"></div>

            <!-- Concentric Arcs Bottom-Right -->
            <div class="bg-arc-br-1"></div>
            <div class="bg-arc-br-2"></div>

            <!-- Top-Right Dot Grid Matrix -->
            <div class="bg-dot-grid">
                <span></span><span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span><span></span>
            </div>

            <!-- Main Form Card Container -->
            <div class="card-container">
                
                <!-- Shield Header Icon -->
                <div class="icon-box-wrapper">
                    <div class="icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121 7.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h3 class="title">Create New Password</h3>
                <div class="title-dash"></div>

                <!-- Form -->
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Hidden Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address (Prefilled & Readonly) -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <input type="email" class="form-control readonly-email" id="email" name="email" value="{{ $email ?? request('email') ?? old('email') }}" readonly required>
                            <div class="readonly-lock-badge" title="Email is verified and non-editable">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                Verified
                            </div>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- New Password Field -->
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required autofocus autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'eyeIcon1')" title="Toggle Password Visibility">
                                <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label class="form-label" for="password-confirm">Confirm New Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="Confirm new password" required autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password-confirm', 'eyeIcon2')" title="Toggle Password Visibility">
                                <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121 7.5z" />
                        </svg>
                        <span>RESET PASSWORD</span>
                    </button>
                </form>

                <!-- Back to Sign In Link -->
                <a href="{{ route('user_login') }}" class="back-to-login">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Back to Sign In</span>
                </a>

            </div>

        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            }
        }
    </script>
</body>
</html>
