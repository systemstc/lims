<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIMS - Laboratory Information Management System | Textile Committee</title>
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">
    <style>
        /* =========================================
           CSS VARIABLES
        ========================================= */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e293b;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --text-dark: #334155;
            --textile-red: #c41e3a;
            --textile-gold: #d4af37;
            --navbar-h: 68px;
        }

        /* =========================================
           BASE
        ========================================= */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
            width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
            width: 100%;
            -webkit-text-size-adjust: 100%;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* =========================================
           NAVBAR
        ========================================= */
        .navbar {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            box-shadow: 0 2px 15px rgba(196, 30, 58, 0.25);
            padding: 0;
            height: var(--navbar-h);
            z-index: 1030;
        }

        .navbar .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            flex-shrink: 0;
            max-width: calc(100% - 56px);
        }

        .navbar-brand img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: white;
            object-fit: contain;
            flex-shrink: 0;
        }

        .navbar-brand-text .brand-name {
            display: block;
            font-weight: 700;
            font-size: 0.95rem;
            color: white;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-brand-text .brand-sub {
            display: block;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.8);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-toggler {
            border: none;
            padding: 0.4rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            background: rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 22px;
            height: 22px;
            display: block;
        }

        /* Desktop nav */
        .desktop-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .desktop-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.45rem 0.85rem !important;
            border-radius: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .desktop-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .desktop-nav .btn-outline-light {
            color: white;
            border: 1.5px solid rgba(255, 255, 255, 0.7);
            padding: 0.4rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .desktop-nav .btn-outline-light:hover {
            background: white;
            color: var(--textile-red);
        }

        /* =========================================
           MOBILE DRAWER
        ========================================= */
        .mobile-nav-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            backdrop-filter: blur(3px);
        }

        .mobile-nav-overlay.active {
            display: block;
            animation: fadeIn 0.25s ease;
        }

        .mobile-nav-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: min(300px, 82vw);
            height: 100%;
            height: 100dvh;
            background: linear-gradient(160deg, #1a0a10 0%, #2d1020 45%, #1a1a2e 100%);
            z-index: 1050;
            transition: right 0.32s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-nav-drawer.active {
            right: 0;
        }

        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(196, 30, 58, 0.25);
            flex-shrink: 0;
        }

        .drawer-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .drawer-brand img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: white;
            object-fit: contain;
            padding: 3px;
        }

        .drawer-brand-text strong {
            display: block;
            font-size: 0.88rem;
            color: white;
            line-height: 1.2;
        }

        .drawer-brand-text small {
            font-size: 0.66rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .drawer-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .drawer-close:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .drawer-nav {
            padding: 1.25rem 0.875rem;
            flex: 1;
        }

        .drawer-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.2rem;
            font-size: 0.92rem;
            font-weight: 500;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            min-height: 48px;
            /* touch target */
        }

        .drawer-nav-item:hover,
        .drawer-nav-item:focus {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.15);
        }

        .drawer-nav-item .nav-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(196, 30, 58, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .drawer-cta {
            padding: 0.75rem 1.25rem 2rem;
            flex-shrink: 0;
        }

        .drawer-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0 0 1rem;
        }

        .drawer-cta .btn-access {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.85rem 1.25rem;
            background: linear-gradient(135deg, #c41e3a, #d4af37);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.92rem;
            text-decoration: none;
            cursor: pointer;
            min-height: 48px;
        }

        /* Show/hide based on screen */
        @media (min-width: 992px) {

            .mobile-nav-drawer,
            .mobile-nav-overlay,
            .navbar-toggler {
                display: none !important;
            }

            .desktop-nav {
                display: flex !important;
            }
        }

        @media (max-width: 991.98px) {
            .desktop-nav {
                display: none !important;
            }
        }

        /* Drawer animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(24px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .mobile-nav-drawer.active .drawer-nav-item {
            animation: slideInRight 0.28s ease forwards;
            opacity: 0;
        }

        .mobile-nav-drawer.active .drawer-nav-item:nth-child(1) {
            animation-delay: 0.06s;
        }

        .mobile-nav-drawer.active .drawer-nav-item:nth-child(2) {
            animation-delay: 0.11s;
        }

        .mobile-nav-drawer.active .drawer-nav-item:nth-child(3) {
            animation-delay: 0.16s;
        }

        .mobile-nav-drawer.active .drawer-nav-item:nth-child(4) {
            animation-delay: 0.21s;
        }

        .mobile-nav-drawer.active .drawer-nav-item:nth-child(5) {
            animation-delay: 0.26s;
        }

        body.drawer-open {
            overflow: hidden;
        }

        /* =========================================
           HERO
        ========================================= */
        .hero-section {
            /* background-attachment:fixed breaks on iOS — use scroll */
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.82), rgba(228, 56, 84, 0.45)),
                url('{{ asset('frontAssets/header-image.jpeg') }}') center/cover no-repeat scroll;
            color: white;
            min-height: 100svh;
            display: flex;
            align-items: center;
            position: relative;
            padding-top: calc(var(--navbar-h) + 2.5rem);
            padding-bottom: 3rem;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="t" patternUnits="userSpaceOnUse" width="20" height="20"><rect fill="none" stroke="%23fff" stroke-width="0.1" width="20" height="20"/></pattern></defs><rect width="100" height="100" fill="url(%23t)"/></svg>');
            opacity: 0.08;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: clamp(1.6rem, 5vw, 3.2rem);
            line-height: 1.2;
            font-weight: 800;
        }

        .hero-content p.lead {
            font-size: clamp(0.88rem, 2.2vw, 1.1rem);
            opacity: 0.92;
        }

        .hero-image {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.5), rgba(228, 56, 84, 0.25));
            border-radius: 0.5rem;
            z-index: 1;
            pointer-events: none;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            max-width: 460px;
            border-radius: 0.5rem;
            margin: 0 auto;
        }

        /* Hero buttons — stack on very small screens */
        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        @media (min-width: 992px) {
            .hero-buttons {
                justify-content: flex-start;
            }
        }

        /* =========================================
           STATS
        ========================================= */
        .stats-section {
            background: linear-gradient(135deg, var(--light-gray), #fff);
            padding: 2.5rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem 0.5rem;
        }

        .stat-number {
            font-size: clamp(1.8rem, 6vw, 3.2rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            line-height: 1.1;
            margin-bottom: 0.35rem;
        }

        .stat-item p {
            font-size: clamp(0.78rem, 2vw, 0.95rem);
            margin: 0;
        }

        /* =========================================
           FEATURES
        ========================================= */
        .textile-bg {
            background: url('{{ asset('frontAssets/branding.jpeg') }}') center/cover no-repeat scroll;
            position: relative;
        }

        .textile-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.96);
        }

        .textile-bg>* {
            position: relative;
            z-index: 1;
        }

        .feature-card {
            background: white;
            border-radius: 18px;
            padding: 2rem 1.5rem;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.07);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            height: 100%;
            border: 1px solid var(--medium-gray);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--textile-red), var(--textile-gold));
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(196, 30, 58, 0.13);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.6rem;
        }

        /* =========================================
           PROCESS
        ========================================= */
        .process-step {
            background: white;
            border-radius: 14px;
            padding: 1.75rem 1.25rem 1.25rem;
            text-align: center;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.07);
            position: relative;
            margin-top: 1.5rem;
            border: 1px solid var(--medium-gray);
            transition: transform 0.28s ease, box-shadow 0.28s ease;
        }

        .process-step:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(196, 30, 58, 0.11);
        }

        .process-number {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        /* =========================================
           SLIDER
        ========================================= */
        .slider-section {
            background: linear-gradient(135deg, var(--light-gray), #fff);
            padding: 3.5rem 0 4rem;
        }

        .slider-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            touch-action: pan-y;
            /* allow vertical scroll, handle horizontal swipe in JS */
        }

        .slider {
            display: flex;
            transition: transform 0.45s ease-in-out;
        }

        .slide {
            min-width: 100%;
            position: relative;
        }

        .slide img {
            width: 100%;
            height: clamp(200px, 42vw, 480px);
            object-fit: cover;
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.72));
            color: white;
            padding: clamp(0.75rem, 3vw, 1.75rem);
            text-align: center;
        }

        .slide-title {
            font-size: clamp(1rem, 3.5vw, 1.75rem);
            font-weight: 800;
            margin-bottom: 0.3rem;
            background: linear-gradient(135deg, rgba(247, 62, 38, 0.85), rgba(243, 177, 53, 0.85));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .slide-description {
            font-size: clamp(0.75rem, 2vw, 0.95rem);
            opacity: 0.88;
        }

        .slider-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.25rem;
            flex-wrap: wrap;
        }

        .slider-btn {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .slider-btn:hover {
            transform: scale(1.08);
        }

        .slider-dots {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 200px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--medium-gray);
            cursor: pointer;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        .dot.active {
            background: var(--textile-red);
            transform: scale(1.35);
        }

        /* =========================================
           LABS SECTION
        ========================================= */
        .lab-card {
            background: white;
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: box-shadow 0.2s;
        }

        .lab-card:hover {
            box-shadow: 0 6px 18px rgba(196, 30, 58, 0.1);
        }

        .lab-card h6 {
            font-size: 0.88rem;
            margin-bottom: 0.2rem;
        }

        .lab-card small {
            font-size: 0.75rem;
            color: var(--text-dark);
            opacity: 0.6;
        }

        /* =========================================
           CONTACT
        ========================================= */
        .contact-section {
            background: linear-gradient(135deg, var(--secondary-color), var(--textile-red));
            color: white;
            padding: 4rem 0 5rem;
            position: relative;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('frontAssets/labimg14.jpeg') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.08;
        }

        .contact-section>.container {
            position: relative;
            z-index: 1;
        }

        .form-control {
            border: 1px solid var(--medium-gray);
            border-radius: 8px;
            padding: 0.7rem 0.9rem;
            background: white;
            width: 100%;
            font-size: 0.9rem;
            transition: border-color 0.25s, box-shadow 0.25s;
        }

        .form-control:focus {
            border-color: var(--textile-red);
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.13);
            outline: none;
        }

        .form-label {
            font-size: 0.88rem;
            margin-bottom: 0.35rem;
        }

        /* =========================================
           BUTTONS (global)
        ========================================= */
        .btn-primary {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 4px 14px rgba(196, 30, 58, 0.28);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            min-height: 44px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(196, 30, 58, 0.38);
            color: white;
        }

        .btn-outline-light-hero {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0.8rem 2rem;
            border: 1.5px solid rgba(255, 255, 255, 0.75);
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            text-decoration: none;
            transition: background 0.22s, color 0.22s;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .btn-outline-light-hero:hover {
            background: white;
            color: var(--textile-red);
        }

        /* =========================================
           FOOTER
        ========================================= */
        .footer {
            background: var(--secondary-color);
            color: white;
            padding: 2.5rem 0 1rem;
        }

        .footer a {
            transition: opacity 0.2s;
        }

        .footer a:hover {
            opacity: 0.75;
        }

        .footer-divider {
            border-color: rgba(255, 255, 255, 0.12);
            margin: 1.25rem 0 1rem;
        }

        .footer-copy {
            font-size: 0.82rem;
        }

        /* =========================================
           SECTION HEADINGS (fluid)
        ========================================= */
        .section-title {
            font-size: clamp(1.4rem, 4.5vw, 2.2rem);
            font-weight: 800;
        }

        .section-sub {
            font-size: clamp(0.88rem, 2.2vw, 1.05rem);
        }

        /* =========================================
           UTILITIES / MOBILE-SPECIFIC
        ========================================= */
        /* Prevent horizontal overflow on small screens */
        section,
        footer,
        nav {
            max-width: 100vw;
        }

        /* Better tap spacing on small touchscreens */
        @media (max-width: 575.98px) {
            .hero-section {
                padding-top: calc(var(--navbar-h) + 1.5rem);
                padding-bottom: 2rem;
            }

            .stats-section {
                padding: 1.75rem 0;
            }

            .stat-item {
                padding: 0.6rem 0.25rem;
            }

            .slider-section {
                padding: 2.5rem 0 3rem;
            }

            .contact-section {
                padding: 3rem 0 4rem;
            }

            .feature-card {
                padding: 1.5rem 1.1rem;
            }

            .process-step {
                padding: 1.5rem 1rem 1rem;
            }

            .footer {
                padding: 2rem 0 1rem;
            }

            .navbar-brand-text .brand-sub {
                display: none;
            }

            /* hide on xs to save space */
        }

        /* Fix fixed background on iOS */
        @supports (-webkit-touch-callout: none) {

            .hero-section,
            .textile-bg {
                background-attachment: scroll !important;
            }
        }
    </style>
</head>

<body>

    <!-- ======= MOBILE NAV OVERLAY ======= -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <!-- ======= MOBILE DRAWER ======= -->
    <div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Navigation Menu">
        <div class="drawer-header">
            <div class="drawer-brand">
                <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textile Committee">
                <div class="drawer-brand-text">
                    <strong>Textile Committee</strong>
                    <small>LIMS Portal</small>
                </div>
            </div>
            <button class="drawer-close" id="drawerClose" aria-label="Close menu">&#x2715;</button>
        </div>

        <nav class="drawer-nav" aria-label="Mobile Navigation">
            <a href="#home" class="drawer-nav-item" data-drawer-link>
                <span class="nav-icon"><em class="icon ni ni-home"></em></span>
                Home
            </a>
            <a href="#features" class="drawer-nav-item" data-drawer-link>
                <span class="nav-icon"><em class="icon ni ni-star"></em></span>
                Features
            </a>
            <a href="#process" class="drawer-nav-item" data-drawer-link>
                <span class="nav-icon"><em class="icon ni ni-list-check"></em></span>
                Process
            </a>
            <a href="#labs" class="drawer-nav-item" data-drawer-link>
                <span class="nav-icon"><em class="icon ni ni-location"></em></span>
                Laboratories
            </a>
            <a href="#contact" class="drawer-nav-item" data-drawer-link>
                <span class="nav-icon"><em class="icon ni ni-call"></em></span>
                Contact
            </a>
        </nav>

        <div class="drawer-cta">
            <hr class="drawer-divider">
            <a href="#" class="btn-access" data-bs-toggle="modal" data-bs-target="#loginModal" data-drawer-link>
                <em class="icon ni ni-signin"></em>
                Access System
            </a>
        </div>
    </div>

    <!-- ======= NAVBAR ======= -->
    <nav class="navbar fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textile Committee of India">
                <div class="navbar-brand-text">
                    <span class="brand-name">Textile Committee</span>
                    <span class="brand-sub">Laboratory Information Management System</span>
                </div>
            </a>

            <!-- Mobile hamburger -->
            <button class="navbar-toggler" type="button" id="mobileMenuToggle" aria-label="Open navigation menu"
                aria-expanded="false" aria-controls="mobileNavDrawer">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop nav -->
            <ul class="desktop-nav">
                <li><a class="nav-link" href="#home">Home</a></li>
                <li><a class="nav-link" href="#features">Features</a></li>
                <li><a class="nav-link" href="#process">Process</a></li>
                <li><a class="nav-link" href="#labs">Laboratories</a></li>
                <li><a class="nav-link" href="#contact">Contact</a></li>
                <li style="margin-left:0.5rem;">
                    <a href="#" class="btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <em class="icon ni ni-signin" style="margin-right:0.4rem;"></em>Access System
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-content text-center text-lg-start">
                        <h1 id="typewriter" class="mb-3">&nbsp;</h1>
                        <p class="lead mb-4">
                            Official Laboratory Information Management System developed by the Textile Committee
                            for standardized sample management, testing protocols, and quality assurance across textile
                            laboratories.
                        </p>
                        <div class="hero-buttons">
                            <a href="#features" class="btn-primary">
                                <em class="icon ni ni-info" style="margin-right:0.45rem;"></em>System Overview
                            </a>
                            <a href="#contact" class="btn-outline-light-hero">
                                <em class="icon ni ni-call" style="margin-right:0.45rem;"></em>Technical Support
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 text-center">
                    <div class="hero-image">
                        <img src="{{ asset('frontAssets/branding.jpeg') }}"
                            alt="Government Textile Testing Laboratory" class="img-fluid rounded-3 shadow-lg"
                            style="max-width:420px; margin:0 auto;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-target="15" data-suffix="+">0+</span>
                        <p class="mb-0 fw-semibold">Textile Labs</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-target="25000" data-suffix="+">0+</span>
                        <p class="mb-0 fw-semibold">Tests Processed</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-target="75" data-suffix="+">0+</span>
                        <p class="mb-0 fw-semibold">Standard Methods</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number" data-target="100" data-suffix="%">0%</span>
                        <p class="mb-0 fw-semibold">Compliance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="textile-bg" id="features">
        <div class="container py-5">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">System Capabilities</h2>
                    <p class="lead">Comprehensive laboratory management system designed for textile
                        testing facilities with full compliance to national and international standards.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-activity"></em></div>
                        <h5 class="fw-bold mb-3">Sample Management</h5>
                        <p>Complete sample lifecycle tracking from registration to disposal with chain of custody
                            documentation and barcode integration.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-opt-alt"></em></div>
                        <h5 class="fw-bold mb-3">Test Management</h5>
                        <p>Standardized test protocols for fiber analysis, fabric testing, and performance evaluation
                            with automated workflows as per BIS and international standards.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-growth"></em></div>
                        <h5 class="fw-bold mb-3">Quality Assurance</h5>
                        <p>Built-in quality control checks, statistical analysis, and regulatory compliance monitoring
                            for maintaining testing accuracy and certification requirements.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-file-text"></em></div>
                        <h5 class="fw-bold mb-3">Report Generation</h5>
                        <p>Automated report generation with standardized templates, digital signatures, and official
                            certification with Textile Committee branding.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-users"></em></div>
                        <h5 class="fw-bold mb-3">User Management</h5>
                        <p>Role-based access control with comprehensive audit trails ensuring data security and
                            regulatory compliance across all laboratory personnel.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><em class="icon ni ni-setting"></em></div>
                        <h5 class="fw-bold mb-3">Equipment Integration</h5>
                        <p>Direct integration with textile testing instruments and automated data capture with approved
                            calibration and validation protocols.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 bg-light" id="process">
        <div class="container py-4">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-6 fw-bold mb-4">Laboratory Workflow</h2>
                    <p class="lead">Standardized testing workflow from sample receipt to report delivery following
                        Textile Committee guidelines and protocols.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">1</div>
                        <div class="mt-3">
                            <em class="icon ni ni-list-check fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Sample Registration</h6>
                            <p class="text-muted small mb-0">Register samples with unique IDs and test requirements
                                following protocols</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">2</div>
                        <div class="mt-3">
                            <em class="icon ni ni-task fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Test Assignment</h6>
                            <p class="text-muted small mb-0">Automatic assignment based on sample type and standardized
                                testing requirements</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <div class="mt-3">
                            <em class="icon ni ni-activity fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Test Execution</h6>
                            <p class="text-muted small mb-0">Guided testing procedures with real-time data capture and
                                validation against standard methods</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <div class="mt-3">
                            <em class="icon ni ni-award fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Report Generation</h6>
                            <p class="text-muted small mb-0">Official report generation with certification and digital
                                authentication</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Laboratories Section -->
    <section class="py-5" id="labs">
        <div class="container py-4">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-6 fw-bold mb-4">Textile Laboratories</h2>
                    <p class="lead">LIMS implementation across textile testing laboratories under the Textile
                        Committee network</p>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="lab-card bg-light">
                                <h6 class="mb-0">Central Textile Laboratory</h6>
                                <small class="text-muted">Mumbai</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="lab-card bg-light">
                                <h6 class="mb-0">Regional Testing Center</h6>
                                <small class="text-muted">Delhi</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="lab-card bg-light">
                                <h6 class="mb-0">Quality Evaluation Lab</h6>
                                <small class="text-muted">Chennai</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="lab-card bg-light">
                                <h6 class="mb-0">Fiber Testing Division</h6>
                                <small class="text-muted">Kolkata</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Slider Section -->
    <section class="slider-section p-0">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-6 fw-bold mb-4">Our Laboratory Facilities</h2>
                    <p class="lead">State-of-the-art textile testing laboratories equipped with modern instruments
                        and technology</p>
                </div>
            </div>
            <div class="slider-container">
                <div class="slider" id="imageSlider"></div>
                <div class="slider-controls">
                    <button class="slider-btn" id="prevBtn" aria-label="Previous slide">
                        <em class="icon ni ni-chevron-left"></em>
                    </button>
                    <div class="slider-dots" id="sliderDots"></div>
                    <button class="slider-btn" id="nextBtn" aria-label="Next slide">
                        <em class="icon ni ni-chevron-right"></em>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-6 fw-bold mb-3 text-white">Technical Support</h2>
                    <p class="lead">Contact the LIMS technical team for system support and laboratory assistance.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form id="contactForm" class="row g-3" method="POST" action="{{ route('contact_support') }}">
                        @csrf
                        <div class="col-md-6">
                            <label for="txt_first_name" class="form-label text-white">First Name</label>
                            <input type="text" class="form-control" id="txt_first_name"
                                value="{{ old('txt_first_name') }}" name="txt_first_name" required>
                            @error('txt_first_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="txt_last_name" class="form-label text-white">Last Name</label>
                            <input type="text" class="form-control" id="txt_last_name"
                                value="{{ old('txt_last_name') }}" name="txt_last_name" required>
                            @error('txt_last_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="txt_email" class="form-label text-white">Official Email</label>
                            <input type="email" class="form-control" id="txt_email"
                                value="{{ old('txt_email') }}" name="txt_email" required>
                            @error('txt_email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="txt_phone" class="form-label text-white">Contact Number</label>
                            <input type="tel" class="form-control" id="txt_phone"
                                value="{{ old('txt_phone') }}" name="txt_phone">
                            @error('txt_phone')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="txt_organization"
                                class="form-label text-white">Laboratory/Organization</label>
                            <input type="text" class="form-control" id="txt_organization"
                                value="{{ old('txt_organization') }}" name="txt_organization">
                            @error('txt_organization')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="txt_lab_type" class="form-label text-white">Laboratory Type</label>
                            <select class="form-control" id="txt_lab_type" name="txt_lab_type">
                                <option value="">Select Laboratory Type</option>
                                <option value="textile_testing">Textile Testing Laboratory</option>
                                <option value="fiber_analysis">Fiber Analysis Laboratory</option>
                                <option value="quality_control">Quality Control Laboratory</option>
                                <option value="research">Research Laboratory</option>
                                <option value="regional">Regional Testing Center</option>
                            </select>
                            @error('txt_lab_type')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="txt_message" class="form-label text-white">Message</label>
                            <textarea class="form-control" id="txt_message" name="txt_message" rows="4"
                                placeholder="Describe your technical requirements or support needs...">{{ old('txt_message') }}</textarea>
                            @error('txt_message')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn-primary" id="contactSubmitBtn"
                                style="border:none;cursor:pointer;">
                                <em class="icon ni ni-send" style="margin-right:0.4rem;"></em>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Logo"
                            style="height:52px;width:52px;flex-shrink:0;border-radius:6px;">
                        <div>
                            <div class="fw-bold text-white" style="font-size:0.92rem;line-height:1.2;">Textile
                                Committee</div>
                            <small class="text-light" style="opacity:0.7;font-size:0.75rem;">LIMS</small>
                        </div>
                    </div>
                    <p class="text-light mb-0" style="font-size:0.84rem;">Laboratory information management system for
                        textile testing facilities.</p>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <h6 class="fw-bold mb-3 text-white" style="font-size:0.87rem;">Quick Links</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.84rem;">
                        <li class="mb-1"><a href="#home" class="text-light text-decoration-none">Home</a></li>
                        <li class="mb-1"><a href="#features" class="text-light text-decoration-none">Features</a>
                        </li>
                        <li class="mb-1"><a href="#process" class="text-light text-decoration-none">Process</a>
                        </li>
                        <li><a href="#contact" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-6 col-sm-3 col-lg-3">
                    <h6 class="fw-bold mb-3 text-white" style="font-size:0.87rem;">System Modules</h6>
                    <ul class="list-unstyled mb-0 text-light" style="font-size:0.84rem;">
                        <li class="mb-1">Sample Management</li>
                        <li class="mb-1">Test Management</li>
                        <li class="mb-1">Quality Control</li>
                        <li class="mb-1">Report Generation</li>
                        <li>Equipment Integration</li>
                    </ul>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <h6 class="fw-bold mb-3 text-white" style="font-size:0.87rem;">Contact Information</h6>
                    <p class="text-light mb-2" style="font-size:0.84rem;"><em
                            class="icon ni ni-map-pin me-1"></em>Mumbai, Maharashtra, India</p>
                    <p class="text-light mb-2" style="font-size:0.84rem;word-break:break-all;"><em
                            class="icon ni ni-mail me-1"></em>lims@textilescommittee.gov.in</p>
                    <p class="text-light mb-0" style="font-size:0.84rem;"><em
                            class="icon ni ni-call me-1"></em>+91-22-XXXX-XXXX</p>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-md-6 text-center text-md-start">
                    <p class="mb-0 text-light footer-copy">© 2025 System Cell. All rights reserved.</p>
                </div>
                <div class="col-12 col-md-6 text-center text-md-end">
                    <a href="#" class="text-light text-decoration-none me-3 footer-copy">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none footer-copy">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">LIMS System Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="loginForm" method="POST" action="{{ route('user_login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="login_email" class="form-label">Official Email</label>
                            <input type="email" class="form-control" id="login_email" name="txt_email" value="{{ old('txt_email') }}" required>
                            @error('txt_email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="login_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="login_password" name="txt_password"
                                required>
                            @error('txt_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3" id="loginSubmitBtn">
                            <em class="icon ni ni-send me-2"></em>Access System
                        </button>
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Contact IT Support</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backAssets/js/bundle.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>

    <script>
        // ========== MOBILE DRAWER NAV ==========
        const drawer = document.getElementById('mobileNavDrawer');
        const overlay = document.getElementById('mobileNavOverlay');
        const toggleBtn = document.getElementById('mobileMenuToggle');
        const closeBtn = document.getElementById('drawerClose');

        function openDrawer() {
            drawer.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('drawer-open');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }

        function closeDrawer() {
            drawer.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('drawer-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }

        toggleBtn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        // Close drawer when a nav link is clicked
        document.querySelectorAll('[data-drawer-link]').forEach(link => {
            link.addEventListener('click', () => {
                closeDrawer();
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && drawer.classList.contains('active')) closeDrawer();
        });

        // ========== SMOOTH SCROLLING ==========
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const offset = 70; // navbar height
                    const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({
                        top,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // ========== TYPEWRITER ==========
        function typeWriterEffect(elementId, parts, speed = 70, delay = 2000) {
            const el = document.getElementById(elementId);
            const fullText = parts.map(p => p.text).join("");
            let i = 0;

            function type() {
                let typed = fullText.slice(0, i);
                let result = "";
                let idx = 0;
                parts.forEach(p => {
                    let part = typed.slice(idx, idx + p.text.length);
                    result += p.class ? `<span class="${p.class}">${part}</span>` : part;
                    idx += p.text.length;
                });
                el.innerHTML = result;
                if (i++ < fullText.length) {
                    setTimeout(type, speed);
                } else {
                    setTimeout(() => {
                        i = 0;
                        type();
                    }, delay);
                }
            }
            type();
        }

        typeWriterEffect("typewriter", [{
                text: "LIMS for",
                class: "text-light"
            },
            {
                text: " Textile Testing",
                class: "text-warning"
            }
        ], 70, 5000);

        // ========== STATS COUNTER ==========
        function animateStats() {
            document.querySelectorAll(".stat-number").forEach(counter => {
                const target = +counter.getAttribute("data-target");
                const suffix = counter.getAttribute("data-suffix") || "";
                let current = 0;
                const increment = target / 150;

                function update() {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.ceil(current) + suffix;
                        requestAnimationFrame(update);
                    } else {
                        counter.textContent = target + suffix;
                    }
                }
                update();
            });
        }

        const statsSection = document.querySelector(".stats-section");
        let statsStarted = false;
        window.addEventListener("scroll", () => {
            const rect = statsSection.getBoundingClientRect();
            if (!statsStarted && rect.top < window.innerHeight - 100) {
                statsStarted = true;
                animateStats();
            }
        });

        // ========== IMAGE SLIDER ==========
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('imageSlider');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dotsContainer = document.getElementById('sliderDots');

            const slides = [{
                    image: "{{ asset('frontAssets/labimg1.jpeg') }}",
                    title: "Fiber Analysis Laboratory",
                    description: "Advanced equipment for precise fiber identification and characterization"
                },
                {
                    image: "{{ asset('frontAssets/labimg3.jpeg') }}",
                    title: "Quality Control Division",
                    description: "Dedicated section for quality assurance and compliance verification"
                },
                {
                    image: "{{ asset('frontAssets/labimg4.jpeg') }}",
                    title: "Chemical Testing Lab",
                    description: "Specialized facility for chemical analysis and safety testing"
                },
                {
                    image: "{{ asset('frontAssets/labimg5.jpeg') }}",
                    title: "Sample Preparation Area",
                    description: "Organized workspace for sample conditioning and preparation"
                },
                {
                    image: "{{ asset('frontAssets/labimg6.jpeg') }}",
                    title: "Color Measurement Lab",
                    description: "Precision instruments for color fastness and shade matching"
                },
                {
                    image: "{{ asset('frontAssets/labimg7.jpeg') }}",
                    title: "Physical Testing Section",
                    description: "Equipment for tensile strength, abrasion, and pilling tests"
                },
                {
                    image: "{{ asset('frontAssets/labimg8.jpeg') }}",
                    title: "Microscopy Laboratory",
                    description: "High-resolution microscopy for detailed fiber and fabric analysis"
                },
                {
                    image: "{{ asset('frontAssets/labimg9.jpeg') }}",
                    title: "Environmental Testing",
                    description: "Climate-controlled chambers for environmental resistance testing"
                },
                {
                    image: "{{ asset('frontAssets/labimg11.jpeg') }}",
                    title: "Data Analysis Center",
                    description: "Digital processing of test results and report generation"
                },
                {
                    image: "{{ asset('frontAssets/labimg12.jpeg') }}",
                    title: "Technical Library",
                    description: "Comprehensive collection of standards and reference materials"
                },
                {
                    image: "{{ asset('frontAssets/labimg13.jpeg') }}",
                    title: "Training Facility",
                    description: "Modern training center for laboratory personnel and technicians"
                },
                {
                    image: "{{ asset('frontAssets/labimg14.jpeg') }}",
                    title: "Central Laboratory",
                    description: "Main testing facility with integrated LIMS implementation"
                }
            ];

            let currentSlide = 0;

            function initSlider() {
                slides.forEach((slide, index) => {
                    const slideEl = document.createElement('div');
                    slideEl.className = 'slide';
                    slideEl.innerHTML = `
                        <img src="${slide.image}" alt="${slide.title}" loading="lazy">
                        <div class="slide-content">
                            <h3 class="slide-title">${slide.title}</h3>
                            <p class="slide-description">${slide.description}</p>
                        </div>`;
                    slider.appendChild(slideEl);

                    const dot = document.createElement('div');
                    dot.className = 'dot' + (index === 0 ? ' active' : '');
                    dot.addEventListener('click', () => goToSlide(index));
                    dotsContainer.appendChild(dot);
                });
                updateSlider();
            }

            function updateSlider() {
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                document.querySelectorAll('.dot').forEach((dot, i) => dot.classList.toggle('active', i ===
                    currentSlide));
            }

            function goToSlide(index) {
                currentSlide = index;
                updateSlider();
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                updateSlider();
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                updateSlider();
            }

            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);

            let slideInterval = setInterval(nextSlide, 5000);
            slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
            slider.addEventListener('mouseleave', () => {
                slideInterval = setInterval(nextSlide, 5000);
            });

            // Touch swipe support
            let touchStartX = 0;
            let touchEndX = 0;
            slider.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, {
                passive: true
            });
            slider.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 40) {
                    diff > 0 ? nextSlide() : prevSlide();
                }
            }, {
                passive: true
            });

            initSlider();

            // ========== CONTACT FORM WITH SWEETALERT ==========
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('contactSubmitBtn');
                    const originalText = submitBtn.innerHTML;

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Sending...';

                    const formData = new FormData(contactForm);

                    fetch(contactForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            Swal.fire({
                                icon: 'success',
                                title: 'Request Submitted!',
                                html: `
                                <p style="color:#334155;">${data.success || 'Thank you! Your message has been received.'}</p>
                                <p style="color:#64748b;font-size:0.9rem;">Our LIMS technical team will get back to you within <strong>1–2 business days</strong>.</p>
                            `,
                                confirmButtonText: 'Got it',
                                confirmButtonColor: '#c41e3a',
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                },
                                customClass: {
                                    confirmButton: 'btn btn-primary px-4'
                                },
                                buttonsStyling: false,
                                timer: 10000,
                                timerProgressBar: true,
                            });

                            contactForm.reset();
                        })
                        .catch(errors => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            // Handle Laravel validation errors
                            if (errors && errors.errors) {
                                const messages = Object.values(errors.errors).flat().join('<br>');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: messages,
                                    confirmButtonColor: '#c41e3a',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong',
                                    text: errors.error ||
                                        'Please try again or contact IT support directly.',
                                    confirmButtonColor: '#c41e3a',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false,
                                });
                            }
                        });
                });
            }

            // ========== LOGIN FORM WITH SWEETALERT ==========
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('loginSubmitBtn');
                    const originalText = submitBtn.innerHTML;

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Accessing...';

                    const formData = new FormData(loginForm);

                    fetch(loginForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Access Granted!',
                                text: data.success || 'Welcome back to LIMS.',
                                confirmButtonColor: '#c41e3a',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                didClose: () => {
                                    window.location.href = data.redirect;
                                }
                            });
                        })
                        .catch(errors => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            if (errors && errors.errors) {
                                const messages = Object.values(errors.errors).flat().join('<br>');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Access Denied',
                                    html: messages,
                                    confirmButtonColor: '#c41e3a',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong',
                                    text: 'Please try again or contact IT support directly.',
                                    confirmButtonColor: '#c41e3a',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false,
                                });
                            }
                        });
                });
            }

            // Auto-reopen login modal if validation errors exist
            @if($errors->has('txt_email') || $errors->has('txt_password'))
                if (typeof bootstrap !== 'undefined') {
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                }
            @endif
        });
    </script>
</body>

</html>
