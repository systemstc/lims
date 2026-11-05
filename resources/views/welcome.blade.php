<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TC-LIMS | Laboratories of Textile Committee | Ministry of Textiles</title>
    <meta name="description"
        content="Official Laboratory Information Management System (TC-LIMS) for Laboratories of the Textiles Committee, Ministry of Textiles, Government of India. End-to-end digital sample registration, testing manuscripts, NABL accredited reports, and live tracking.">
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">

    <!-- Bootstrap 5.3.2 & FontAwesome 6.5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Official Typography: Inter & Noto Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">

    <style>
        /* ========================================================================= */
        /* 1. OFFICIAL LABORATORIES OF TEXTILES COMMITTEE THEME (NAVY, GOLD & SLATE) */
        /* ========================================================================= */
        :root {
            --tc-navy: #0e244d;              /* Deep Institutional Navy */
            --tc-navy-mid: #163772;          /* Royal Navy Blue */
            --tc-blue: #1d4ed8;              /* Technical Lab Blue */
            --tc-blue-dark: #0a1b38;         /* Deepest Navy */
            --tc-blue-tint: #f0f5fc;         /* Soft Surface Tint */
            --tc-blue-border: #cbdbe9;       /* Subtle Border Tint */

            --tc-gold: #d4af37;              /* Official Emblem Gold */
            --tc-gold-dark: #b89222;         /* Deep Gold */
            --tc-gold-amber: #f59e0b;        /* Bright Gold Accent */

            --tc-green: #059669;             /* NABL Accredited Green */
            --tc-green-light: #ecfdf5;       /* Green Surface Tint */

            --tc-maroon: #7a1528;            /* Official Deep Maroon Accent */

            --text-heading: #0f172a;         /* Deep Slate Heading */
            --text-body: #334155;            /* Slate Body Text */
            --text-muted: #64748b;           /* Muted Text */

            --navbar-h: 74px;
            --shadow-sm: 0 2px 8px rgba(14, 36, 77, 0.06);
            --shadow-md: 0 8px 24px rgba(14, 36, 77, 0.10);
            --shadow-lg: 0 16px 36px rgba(14, 36, 77, 0.14);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', 'Noto Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-body);
            background-color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
            width: 100%;
            padding-top: var(--navbar-h);
        }

        a {
            text-decoration: none;
            transition: all 0.25s ease;
        }

        /* ========================================================================= */
        /* 2. SINGLE PRIMARY NAVIGATION BAR (AT THE VERY TOP)                       */
        /* ========================================================================= */
        .navbar-main {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--navbar-h);
            background: linear-gradient(135deg, var(--tc-navy) 0%, var(--tc-navy-mid) 60%, var(--tc-blue-dark) 100%);
            box-shadow: 0 4px 20px rgba(14, 36, 77, 0.25);
            border-bottom: 2.5px solid var(--tc-gold);
            z-index: 1040;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .navbar-main .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand-group {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .tc-logo-wrap {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
            border: 2px solid rgba(255, 255, 255, 0.95);
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .navbar-brand-group:hover .tc-logo-wrap {
            transform: scale(1.06);
        }

        .tc-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-divider {
            width: 1px;
            height: 36px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.15));
            flex-shrink: 0;
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .navbar-brand-text .brand-name {
            font-weight: 800;
            font-size: 1.05rem;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        .navbar-brand-text .brand-sub {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.88);
            font-weight: 500;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        /* Desktop Nav Items */
        .desktop-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .desktop-nav .nav-link {
            color: rgba(255, 255, 255, 0.92) !important;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 12px !important;
            border-radius: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .desktop-nav .nav-link:hover,
        .desktop-nav .nav-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.16);
        }

        /* 3TS Logo Wrap on Navbar Right */
        .tts-logo-wrap {
            /* background: #ffffff; */
            padding: 4px 4px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            flex-shrink: 0;
        }

        .tts-logo-wrap:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .tts-logo-wrap img {
            max-height: 35px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        /* Nav Buttons */
        .btn-tc-gold {
            background: linear-gradient(135deg, var(--tc-gold) 0%, var(--tc-gold-dark) 100%);
            color: #0e244d !important;
            font-weight: 700;
            font-size: 0.82rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 6px;
            padding: 7px 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .btn-tc-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.4);
            color: #000000 !important;
        }

        .btn-tc-outline {
            background: rgba(255, 255, 255, 0.10);
            border: 1.5px solid rgba(255, 255, 255, 0.55);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.82rem;
            border-radius: 6px;
            padding: 7px 14px;
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .btn-tc-outline:hover {
            background: #ffffff;
            color: var(--tc-navy) !important;
            border-color: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        /* Mobile Drawer */
        .mobile-nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(14, 36, 77, 0.65);
            backdrop-filter: blur(3px);
            z-index: 1080;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-nav-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav-drawer {
            position: fixed;
            top: 0;
            right: -320px;
            width: 300px;
            height: 100%;
            background: #ffffff;
            z-index: 1090;
            box-shadow: -4px 0 25px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        .mobile-nav-drawer.active {
            right: 0;
        }

        .mobile-drawer-header {
            padding: 18px 20px;
            background: linear-gradient(135deg, var(--tc-navy), var(--tc-navy-mid));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--tc-gold);
        }

        .mobile-drawer-body {
            padding: 20px;
        }

        .mobile-drawer-body .nav-link {
            color: var(--text-heading) !important;
            font-weight: 600;
            padding: 12px 14px !important;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.92rem;
        }

        .mobile-drawer-body .nav-link:hover {
            background-color: var(--tc-blue-tint);
            color: var(--tc-navy-mid) !important;
        }

        /* ========================================================================= */
        /* 3. HERO BANNER SECTION (tclims.jpeg + BRANDING)                           */
        /* ========================================================================= */
        .hero-banner-section {
            background-color: #f8fafc;
            position: relative;
            border-bottom: 1px solid var(--tc-blue-border);
        }

        .hero-main-banner-img {
            width: 100%;
            /* max-height: 500px; */
            object-fit: cover;
            object-position: center;
            display: block;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Floating Sample Tracker Card */
        .hero-tracker-card {
            background: #ffffff;
            border: 2px solid var(--tc-blue-border);
            border-radius: 14px;
            padding: 16px 22px;
            box-shadow: 0 10px 30px rgba(14, 36, 77, 0.12);
            margin-top: -34px;
            position: relative;
            z-index: 20;
        }

        /* Branding Cards Strip Below Banner */
        .branding-strip {
            background: linear-gradient(180deg, #ffffff 0%, var(--tc-blue-tint) 100%);
            padding: 34px 0 40px 0;
            border-bottom: 1px solid var(--tc-blue-border);
        }

        .branding-card {
            background: #ffffff;
            border: 1px solid var(--tc-blue-border);
            border-top: 3.5px solid var(--tc-navy-mid);
            border-radius: 12px;
            padding: 20px 18px;
            height: 100%;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
        }

        .branding-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--tc-navy-mid);
        }

        .branding-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--tc-blue-tint);
            color: var(--tc-navy-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            border: 1px solid var(--tc-blue-border);
        }

        /* ========================================================================= */
        /* 4. MAP AND MINISTERS SECTION (NAVY & GOLD THEME)                          */
        /* ========================================================================= */
        .map-ministers-section {
            background: #ffffff;
            padding: 65px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-eyebrow {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--tc-navy-mid);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--tc-blue-tint);
            border: 1px solid var(--tc-blue-border);
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .section-main-title {
            font-size: 1.95rem;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.25;
            margin-bottom: 8px;
        }

        .section-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        /* India Vector Map Container Card */
        .lims-map-card {
            background: #ffffff;
            border: 1.5px solid var(--tc-blue-border);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow-md);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .india-map-viewport {
            width: 100%;
            height: 520px;
            background: radial-gradient(circle at 50% 50%, #ffffff 45%, #f1f5f9 120%);
            border-radius: 14px;
            border: 1.5px solid var(--tc-blue-border);
            box-shadow: inset 0 2px 12px rgba(14, 36, 77, 0.04);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        #indiaMapContainer {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #indiaMapContainer svg {
            width: 100%;
            height: 100%;
            max-height: 500px;
            display: block;
            margin: auto;
            filter: drop-shadow(0 6px 16px rgba(14, 36, 77, 0.1));
        }

        .india-state-path {
            fill: #f1f5f9;
            stroke: #94a3b8;
            stroke-width: 0.9px;
            stroke-linejoin: round;
            stroke-linecap: round;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .india-state-path:hover {
            fill: #dbeafe !important;
            stroke: var(--tc-navy-mid) !important;
            stroke-width: 1.6px;
        }

        .india-state-path.state-lab-host {
            fill: #e8f0fe;
            stroke: var(--tc-navy-mid);
            stroke-width: 1.25px;
        }

        /* Map SVG Pin Markers */
        .tra-svg-pin {
            cursor: pointer;
        }

        .tra-pin-radar {
            fill: rgba(22, 55, 114, 0.2);
            stroke: var(--tc-navy-mid);
            stroke-width: 1.8px;
            animation: radarPulseLims 2.2s infinite ease-out;
            pointer-events: none;
        }

        @keyframes radarPulseLims {
            0% { r: 8px; opacity: 0.95; }
            60% { r: 24px; opacity: 0.25; }
            100% { r: 28px; opacity: 0; }
        }

        .tra-pin-core {
            fill: var(--tc-navy);
            stroke: var(--tc-gold);
            stroke-width: 2px;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));
            transition: all 0.2s ease;
        }

        .tra-svg-pin:hover .tra-pin-core,
        .tra-svg-pin.active .tra-pin-core {
            fill: var(--tc-blue);
            stroke: #ffffff;
            stroke-width: 2.5px;
            transform: scale(1.18);
        }

        .tra-pin-dot {
            fill: #ffffff;
            pointer-events: none;
        }

        .tra-tag-bg {
            fill: var(--tc-navy);
            stroke: var(--tc-gold);
            stroke-width: 1.4px;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));
            transition: all 0.2s ease;
        }

        .tra-tag-text {
            font-size: 11px;
            font-weight: 800;
            fill: #ffffff;
            text-anchor: middle;
            dominant-baseline: central;
            letter-spacing: 0.5px;
            pointer-events: none;
        }

        .tra-svg-pin:hover .tra-tag-bg,
        .tra-svg-pin.active .tra-tag-bg {
            fill: var(--tc-blue);
            stroke: #ffffff;
            stroke-width: 1.8px;
        }

        /* Floating Lab Popup Card */
        .lims-lab-popup {
            position: absolute;
            z-index: 100;
            background: #ffffff;
            border: 1.5px solid var(--tc-blue-border);
            border-top: 4px solid var(--tc-navy-mid);
            border-radius: 12px;
            padding: 14px 16px;
            width: 280px;
            box-shadow: 0 12px 30px rgba(14, 36, 77, 0.18);
            animation: popupFadeLims 0.22s ease-out;
        }

        @keyframes popupFadeLims {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .state-hover-tooltip {
            position: absolute;
            z-index: 90;
            background: rgba(14, 36, 77, 0.94);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
            transform: translate(-50%, -130%);
        }

        .lab-pill-btn {
            font-size: 0.76rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            background: var(--tc-blue-tint);
            color: var(--tc-navy-mid);
            border: 1.2px solid var(--tc-blue-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lab-pill-btn:hover, .lab-pill-btn.active {
            background: var(--tc-navy-mid);
            color: #ffffff;
            border-color: var(--tc-navy-mid);
            transform: translateY(-1px);
        }

        /* Ministers Cards (3 Columns side-by-side) */
        .dignitary-card {
            background: #ffffff;
            border: 1.5px solid var(--tc-blue-border);
            border-radius: 14px;
            padding: 16px 10px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .dignitary-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--tc-navy-mid);
        }

        .dignitary-avatar {
            width: 100%;
            max-width: 130px;
            height: 145px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 12px;
            border: 3px solid var(--tc-navy-mid);
            box-shadow: 0 4px 12px rgba(14, 36, 77, 0.15);
            background: var(--tc-blue-tint);
        }

        .dignitary-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }

        .dignitary-badge {
            background: var(--tc-navy);
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 3px 8px;
            border-radius: 12px;
            margin-bottom: 6px;
            white-space: nowrap;
        }

        .dignitary-name {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 2px;
            line-height: 1.25;
        }

        .dignitary-title {
            font-size: 0.74rem;
            font-weight: 600;
            color: var(--tc-navy-mid);
            margin-bottom: 6px;
            line-height: 1.35;
        }

        .leadership-quote-card {
            background: var(--tc-blue-tint);
            border: 1px solid var(--tc-blue-border);
            border-radius: 12px;
            padding: 14px 18px;
        }

        /* ========================================================================= */
        /* 5. ABOUT TCLIMS SECTION (GENUINELY & COMPLETELY ABOUT LIMS)               */
        /* ========================================================================= */
        .about-section {
            background-color: var(--tc-blue-tint);
            padding: 70px 0;
            border-bottom: 1px solid var(--tc-blue-border);
        }

        .about-pillar-card {
            background: #ffffff;
            border: 1px solid var(--tc-blue-border);
            border-left: 5px solid var(--tc-navy-mid);
            border-radius: 12px;
            padding: 20px 18px;
            margin-bottom: 16px;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
        }

        .about-pillar-card:hover {
            transform: translateX(6px);
            box-shadow: var(--shadow-md);
            border-left-color: var(--tc-gold);
        }

        .about-pillar-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--tc-blue-tint);
            color: var(--tc-navy-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            border: 1px solid var(--tc-blue-border);
        }

        /* Workflow Step Cards */
        .workflow-step-card {
            background: #ffffff;
            border: 1.5px solid var(--tc-blue-border);
            border-radius: 12px;
            padding: 18px;
            height: 100%;
            transition: all 0.25s ease;
            position: relative;
        }

        .workflow-step-card:hover {
            transform: translateY(-4px);
            border-color: var(--tc-navy-mid);
            box-shadow: var(--shadow-md);
        }

        .step-number-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--tc-navy-mid);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        /* ========================================================================= */
        /* 6. CONTACT US SECTION                                                     */
        /* ========================================================================= */
        .contact-section {
            background-color: #ffffff;
            padding: 70px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .contact-form-card {
            background: #ffffff;
            border: 1.5px solid var(--tc-blue-border);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow-md);
            height: 100%;
        }

        .contact-info-card {
            background: linear-gradient(145deg, var(--tc-navy) 0%, var(--tc-blue-dark) 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 12px 30px rgba(14, 36, 77, 0.25);
            height: 100%;
            border: 2px solid var(--tc-gold);
        }

        .contact-meta-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .contact-icon-pill {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            color: var(--tc-gold);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* ========================================================================= */
        /* 7. LABORATORY INFRASTRUCTURE CAROUSEL                                     */
        /* ========================================================================= */
        .carousel-section {
            background-color: #f8fafc;
            padding: 70px 0;
            border-bottom: 1px solid var(--tc-blue-border);
        }

        .lims-carousel-box {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--tc-blue-border);
        }

        .lims-carousel-slide {
            height: 460px;
            position: relative;
        }

        .lims-carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .lims-carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(14, 36, 77, 0.88) 60%, rgba(10, 27, 56, 0.98) 100%);
            color: #ffffff;
            padding: 32px 36px;
        }

        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.55);
            border: none;
            margin: 0 5px;
            transition: all 0.25s ease;
        }

        .carousel-indicators .active {
            background-color: var(--tc-gold);
            width: 28px;
            border-radius: 6px;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(14, 36, 77, 0.85);
            border-radius: 50%;
            padding: 24px;
            background-size: 50%;
        }

        /* ========================================================================= */
        /* 8. NATIONAL IMPACT & STATS STRIP                                          */
        /* ========================================================================= */
        .stats-section {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 55px 0;
        }

        .stat-item-box {
            text-align: center;
            padding: 16px 10px;
            transition: transform 0.25s ease;
        }

        .stat-item-box:hover {
            transform: translateY(-4px);
        }

        .stat-number {
            font-size: 2.85rem;
            font-weight: 900;
            color: var(--tc-navy);
            line-height: 1;
            margin-bottom: 6px;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.5px;
        }

        .stat-label {
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
            line-height: 1.35;
        }

        /* ========================================================================= */
        /* 9. REFINED FOOTER                                                         */
        /* ========================================================================= */
        .lims-footer {
            background: #f8fafc;
            color: var(--text-body);
            font-size: 0.86rem;
            border-top: 4px solid var(--tc-navy-mid);
        }

        .lims-footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .lims-footer a:hover {
            color: var(--tc-navy-mid);
            text-decoration: underline;
        }

        .lims-footer h5 {
            color: var(--tc-navy);
            font-size: 0.98rem;
            font-weight: 700;
            margin-bottom: 14px;
            letter-spacing: 0.3px;
        }

        .footer-bottom-bar {
            background: var(--tc-blue-tint);
            border-top: 1px solid var(--tc-blue-border);
            padding: 16px 0;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .back-to-top-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--tc-navy);
            color: #ffffff;
            border: 2px solid var(--tc-gold);
            box-shadow: 0 4px 16px rgba(14, 36, 77, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            z-index: 1030;
            transition: all 0.25s ease;
        }

        .back-to-top-btn:hover {
            background: var(--tc-navy-mid);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(14, 36, 77, 0.5);
        }

        .btn-lims-primary {
            background: linear-gradient(135deg, var(--tc-navy) 0%, var(--tc-navy-mid) 100%) !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .btn-lims-primary:hover {
            box-shadow: 0 4px 16px rgba(14, 36, 77, 0.35);
            transform: translateY(-2px);
        }

        @media (max-width: 991.98px) {
            .india-map-viewport {
                height: 420px;
            }
            .lims-carousel-slide {
                height: 360px;
            }
            .hero-main-banner-img {
                max-height: 380px;
            }
        }
    </style>
</head>

<body>

    <!-- ========================================================================= -->
    <!-- 1. SINGLE UNIFIED NAVIGATION BAR (AT THE VERY TOP)                       -->
    <!-- ========================================================================= -->
    <nav class="navbar-main" aria-label="Main Navigation">
        <div class="container-fluid px-3 px-lg-5">

            <!-- Brand: Logo + Divider + Name -->
            <a class="navbar-brand-group" href="{{ url('/') }}" title="Laboratories of Textile Committee">
                <div class="tc-logo-wrap">
                    <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textiles Committee">
                </div>
                <div class="brand-divider d-none d-sm-block"></div>
                <div class="navbar-brand-text">
                    <span class="brand-name">
                        Laboratories of Textile Committee
                        <span class="badge bg-warning text-dark fw-bold ms-1" style="font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">LIMS</span>
                    </span>
                    <span class="brand-sub d-none d-sm-block">Laboratory Information Management System (TC-LIMS)</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <div class="d-none d-xl-flex align-items-center">
                <ul class="desktop-nav">
                    <li><a class="nav-link active" href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Home</a></li>
                    <li><a class="nav-link" href="#leadership-section"><i class="fas fa-landmark me-1"></i> Leadership</a></li>
                    <li><a class="nav-link" href="#map-section"><i class="fas fa-map-marked-alt me-1"></i> Labs Map</a></li>
                    <li><a class="nav-link" href="#about-section"><i class="fas fa-info-circle me-1"></i> About LIMS</a></li>
                    <li><a class="nav-link" href="#contact-section"><i class="fas fa-envelope me-1"></i> Contact Us</a></li>
                    {{-- <li><a class="nav-link" href="#carousel-section"><i class="fas fa-images me-1"></i> Lab Showcase</a></li>
                    <li><a class="nav-link" href="#stats-section"><i class="fas fa-chart-line me-1"></i> Stats</a></li> --}}
                </ul>
            </div>

            <!-- Right Actions: Tracker + Portal / Staff Login + 3TS Logo (#ts logo) + Mobile Toggle -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">

                <!-- Quick Track Button -->
                {{-- <a href="{{ url('/search-tracker') }}" class="btn btn-tc-outline d-none d-md-inline-flex align-items-center gap-1">
                    <i class="fas fa-search-location text-warning"></i>
                    <span>Track Sample</span>
                </a> --}}

                <!-- Customer Portal Button -->
                {{-- <a href="{{ route('customer.login') }}" class="btn btn-tc-gold d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="fas fa-user-circle"></i>
                    <span>Customer Portal</span>
                </a> --}}

                <!-- Staff Login / Dashboard -->
                @if (session()->has('admin_id') || session()->has('user_id'))
                    <a href="{{ route('dashboard') }}" class="btn btn-tc-outline d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <button type="button" class="btn btn-tc-outline d-none d-sm-inline-flex align-items-center gap-1"
                        data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Access System</span>
                    </button>
                @endif

                <!-- 3TS Logo on Navbar Right -->
                <div class="tts-logo-wrap" title="3TS - Textile Testing & Technical Services">
                    <img src="{{ asset('backAssets/images/3TS.png') }}" alt="3TS - Textile Testing & Technical Services"
                        onerror="this.onerror=null; this.src='{{ asset('frontAssets/textiles_logo_200.png') }}';">
                </div>

                <!-- Mobile Hamburger Toggler -->
                <button class="btn btn-sm text-white d-xl-none p-1 ms-1" type="button" id="mobileMenuToggle" aria-label="Open menu">
                    <i class="fas fa-bars fa-lg"></i>
                </button>

            </div>

        </div>
    </nav>

    <!-- Mobile Drawer & Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
    <div class="mobile-nav-drawer" id="mobileNavDrawer">
        <div class="mobile-drawer-header">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Logo"
                    style="height: 34px; background: #fff; border-radius: 50%; padding: 2px;">
                <span class="fw-bold">TC-LIMS Portal</span>
            </div>
            <button class="btn btn-sm text-white" id="drawerClose"><i class="fas fa-times fa-lg"></i></button>
        </div>
        <div class="mobile-drawer-body">
            <ul class="navbar-nav">
                <li><a class="nav-link" href="{{ url('/') }}"><i class="fas fa-home text-primary"></i> Home</a></li>
                <li><a class="nav-link" href="#leadership-section"><i class="fas fa-landmark text-secondary"></i> Leadership</a></li>
                <li><a class="nav-link" href="#map-section"><i class="fas fa-map-marked-alt text-success"></i> Laboratories Map</a></li>
                <li><a class="nav-link" href="#about-section"><i class="fas fa-info-circle text-info"></i> About LIMS</a></li>
                <li><a class="nav-link" href="#contact-section"><i class="fas fa-envelope text-warning"></i> Contact Us</a></li>
                {{-- <li><a class="nav-link" href="#carousel-section"><i class="fas fa-images text-dark"></i> Lab Showcase</a></li>
                <li><a class="nav-link" href="#stats-section"><i class="fas fa-chart-line text-success"></i> Impact & Stats</a></li> --}}
                {{-- <li><a class="nav-link" href="{{ url('/search-tracker') }}"><i class="fas fa-search-location text-primary"></i> Track Sample / Manuscript</a></li> --}}
                {{-- <li><a class="nav-link" href="{{ route('customer.login') }}"><i class="fas fa-user-circle text-primary"></i> Customer Portal Login</a></li> --}}
            </ul>

            <div class="mt-4 pt-3 border-top text-center">
                <div class="tts-logo-wrap mb-3 mx-auto">
                    <img src="{{ asset('backAssets/images/3TS.png') }}" alt="3TS Logo">
                </div>
                <div class="small text-muted fw-bold">Laboratories of Textile Committee</div>
                <div class="small text-muted">Ministry of Textiles, Government of India</div>
            </div>
        </div>
    </div>

    <main id="main-content">

        <!-- ========================================================================= -->
        <!-- 2. BELOW NAV BAR BANNER (tclims.jpeg + LIMS BRANDING & FLOATING TRACKER)  -->
        <!-- ========================================================================= -->
        <section class="hero-banner-section p-0">
            <div class="container-fluid px-0">
                <!-- Banner Image A:\wamp64\www\lims\frontAssets\tclims.jpeg -->
                <div class="position-relative">
                    <img src="{{ asset('frontAssets/tclims.jpeg') }}"
                        alt="TC-LIMS - Laboratory Information Management System - Textiles Committee"
                        class="hero-main-banner-img"
                        onerror="this.onerror=null; this.src='{{ asset('frontAssets/branding.jpeg') }}';"
                        loading="eager">
                </div>
            </div>

            <!-- Floating Quick Sample Tracker Bar -->
            <div class="container px-3 px-lg-4">
                <div class="hero-tracker-card">
                    <form action="{{ url('/search-tracker') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-lg-4 col-md-5">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle p-2 text-white d-flex align-items-center justify-content-center"
                                    style="background: var(--tc-navy); width: 40px; height: 40px;">
                                    <i class="fas fa-barcode text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Track Sample</h6>
                                    <span class="text-muted small" style="font-size: 0.74rem;">Enter Sample ID, Barcode, or Customer Reference No.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-5">
                            <div class="input-group">
                                {{-- <span class="input-group-text bg-white border-end-0" style="border-color: var(--tc-blue-border);">
                                    <i class="fas fa-search text-muted"></i>
                                </span> --}}
                                {{-- <input type="text" name="query" class="form-control border-start-0 ps-0"
                                    placeholder="e.g. Sample Reference ID..."
                                    required style="border-color: var(--tc-blue-border); font-size: 0.92rem;" disabled> --}}
                            </div>
                        </div>
                        {{-- <div class="col-lg-2 col-md-2">
                            <button type="submit" class="btn btn-lims-primary w-100 fw-bold py-2 shadow-sm" style="border-radius: 8px;">
                                <i class="fas fa-search me-1"></i> Track Now
                            </button>
                        </div> --}}
                    </form>
                </div>
            </div>

            <!-- LIMS Branding Feature Highlights Strip -->
            <div class="branding-strip mt-4">
                <div class="container-fluid px-3 px-lg-5">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="branding-card">
                                <div class="branding-card-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Sample Inward & Barcoding</h6>
                                    <p class="small text-muted mb-0">Automatic barcode tag generation and secure custody tracking from reception to storage.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="branding-card">
                                <div class="branding-card-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">NABL Accredited Testing</h6>
                                    <p class="small text-muted mb-0">Over 450 standardized parameters compliant with ISO/IEC 17025, ASTM, AATCC, and BIS.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="branding-card">
                                <div class="branding-card-icon">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Digital Analyst Manuscripts</h6>
                                    <p class="small text-muted mb-0">Direct instrument calculation, formula auto-check, and two-tier technical validation.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="branding-card">
                                <div class="branding-card-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">QR-Secured Final Reports</h6>
                                    <p class="small text-muted mb-0">Tamper-evident, digitally signed test certificates with instant verification by global buyers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- 3. MAP AND MINISTERS SECTION (NAVY & GOLD THEME)                          -->
        <!-- ========================================================================= -->
        <section class="map-ministers-section" id="map-section">
            <div class="container-fluid px-3 px-lg-5">
                <div class="row g-4 align-items-stretch">

                    <!-- Left Column: Interactive India Vector Map of Testing Laboratories -->
                    <div class="col-lg-6 col-xl-6">
                        <div class="lims-map-card">

                            <!-- Map Header Strip -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge text-uppercase px-2 py-1 fw-bold"
                                    style="background: var(--tc-blue-tint); color: var(--tc-navy); border: 1px solid var(--tc-blue-border); font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="fas fa-map-marked-alt text-primary me-1"></i> Pan-India Testing Network
                                </span>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold"
                                    onclick="resetIndiaMapView()" style="font-size: 0.72rem; color: var(--tc-navy-mid);"
                                    title="Reset whole India view">
                                    <i class="fas fa-compress-arrows-alt me-1"></i> Reset View
                                </button>
                            </div>

                            <h3 class="fw-bold mb-1" style="font-size: 1.15rem; color: var(--text-heading);">
                                Textiles Committee Laboratories Network
                            </h3>
                            <p class="text-muted small mb-2" style="font-size: 0.78rem; line-height: 1.4;">
                                Interactive map of Textiles Committee testing laboratories across India. Click pins or pills below to view laboratory specialties.
                            </p>

                            <!-- Interactive India Vector Map Viewport -->
                            <div class="india-map-viewport position-relative" id="indiaMapViewport">
                                <!-- SVG Map Canvas -->
                                <div id="indiaMapContainer"></div>

                                <!-- Floating Lab Info Popup Card -->
                                <div id="limsLabPopup" class="lims-lab-popup" style="display: none;"></div>

                                <!-- State Hover Tooltip -->
                                <div id="stateHoverTooltip" class="state-hover-tooltip" style="display: none;"></div>
                            </div>

                            <!-- Quick Lab Selector Pills Strip -->
                            <div class="d-flex flex-wrap gap-1 justify-content-center mt-2 mb-1">
                                <span class="lab-pill-btn" id="pill-MUMBAI" onclick="selectLabOnMap('MUMBAI')">Mumbai HO</span>
                                <span class="lab-pill-btn" id="pill-DELHI" onclick="selectLabOnMap('DELHI')">Delhi</span>
                                <span class="lab-pill-btn" id="pill-TIRUPUR" onclick="selectLabOnMap('TIRUPUR')">Tirupur</span>
                                <span class="lab-pill-btn" id="pill-LUDHIANA" onclick="selectLabOnMap('LUDHIANA')">Ludhiana</span>
                                <span class="lab-pill-btn" id="pill-COIMBATORE" onclick="selectLabOnMap('COIMBATORE')">Coimbatore</span>
                                <span class="lab-pill-btn" id="pill-AHMEDABAD" onclick="selectLabOnMap('AHMEDABAD')">Ahmedabad</span>
                                <span class="lab-pill-btn" id="pill-SURAT" onclick="selectLabOnMap('SURAT')">Surat</span>
                                <span class="lab-pill-btn" id="pill-KOLKATA" onclick="selectLabOnMap('KOLKATA')">Kolkata</span>
                                <span class="lab-pill-btn" id="pill-BENGALURU" onclick="selectLabOnMap('BENGALURU')">Bengaluru</span>
                                <span class="lab-pill-btn" id="pill-KANPUR" onclick="selectLabOnMap('KANPUR')">Kanpur</span>
                                <span class="lab-pill-btn" id="pill-JAIPUR" onclick="selectLabOnMap('JAIPUR')">Jaipur</span>
                                <span class="lab-pill-btn" id="pill-KARUR" onclick="selectLabOnMap('KARUR')">Karur</span>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Ministers & Administrative Leadership (Navy & Gold Theme) -->
                    <div class="col-lg-6 col-xl-6 d-flex flex-column justify-content-between" id="leadership-section">
                        <div>
                            <!-- Eyebrow & Title -->
                            <div class="mb-3">
                                <div class="section-eyebrow mb-2">
                                    <i class="fas fa-landmark text-primary"></i> Ministry Leadership & Governance
                                </div>
                                <h2 class="section-main-title text-start mb-1">
                                    Hon'ble Ministers & Administrative Leadership
                                </h2>
                                <p class="text-muted small mb-3">
                                    Guiding India's textile quality ecosystem, laboratory standardisation, and LIMS digital governance under the Ministry of Textiles.
                                </p>
                            </div>

                            <!-- 3 Ministers in 3 adjacent columns -->
                            <div class="row g-2 g-xl-3 align-items-stretch">

                                <!-- 1. Union Minister: Shri Giriraj Singh -->
                                <div class="col-4">
                                    <div class="dignitary-card">
                                        <div class="dignitary-avatar">
                                            <img src="{{ asset('frontAssets/minister_giriraj.jpg') }}"
                                                alt="Shri Giriraj Singh, Hon'ble Union Minister of Textiles"
                                                onerror="this.onerror=null; this.src='https://itrams.org.in/assets/images/8a165a5d1bf108941aaae571aca38ff2.jpg';"
                                                loading="lazy">
                                        </div>
                                        <span class="dignitary-badge">Union Minister</span>
                                        <h3 class="dignitary-name">Shri Giriraj Singh</h3>
                                        <div class="dignitary-title">
                                            Hon'ble Minister of Textiles<br>
                                            <span class="small text-muted">Government of India</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Minister of State: Shri Pabitra Margherita -->
                                <div class="col-4">
                                    <div class="dignitary-card">
                                        <div class="dignitary-avatar">
                                            <img src="{{ asset('frontAssets/minister_pabitra.jpg') }}"
                                                alt="Shri Pabitra Margherita, Hon'ble Minister of State"
                                                onerror="this.onerror=null; this.src='https://itrams.org.in/assets/images/d5040c43ed61552117efaf05e4ab5bb1.jpg';"
                                                loading="lazy">
                                        </div>
                                        <span class="dignitary-badge">Minister of State</span>
                                        <h3 class="dignitary-name">Shri Pabitra Margherita</h3>
                                        <div class="dignitary-title">
                                            Hon'ble Minister of State<br>
                                            <span class="small text-muted">External Affairs & Textiles</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Secretary (Textiles): Smt. Neelam Shami Rao -->
                                <div class="col-4">
                                    <div class="dignitary-card">
                                        <div class="dignitary-avatar">
                                            <img src="{{ asset('frontAssets/secretary_neelam.jpg') }}"
                                                alt="Smt. Neelam Shami Rao, Secretary, Ministry of Textiles"
                                                onerror="this.onerror=null; this.src='https://itrams.org.in/assets/images/02574267bbb256ea9bfd78db0bf033ca.jpg';"
                                                loading="lazy">
                                        </div>
                                        <span class="dignitary-badge" style="background: var(--tc-navy-mid);">Secretary (Textiles)</span>
                                        <h3 class="dignitary-name">Smt. Neelam Shami Rao</h3>
                                        <div class="dignitary-title">
                                            Secretary, Ministry of Textiles<br>
                                            <span class="small text-muted">Government of India</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Leadership Quote Strip -->
                        <div class="leadership-quote-card mt-3 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-quote-left text-primary opacity-50 fa-lg"></i>
                                <span class="small fw-semibold text-secondary" style="font-size: 0.78rem;">
                                    Transforming quality assurance across India's textile supply chain with NABL-accredited precision, paperless custody, and digital LIMS speed.
                                </span>
                            </div>
                            <a href="https://texmin.gov.in" target="_blank" rel="noopener noreferrer"
                                class="btn btn-sm btn-outline-primary text-nowrap py-1 px-3"
                                style="font-size: 0.75rem; border-radius: 20px;">
                                Ministry Hub <i class="fas fa-chevron-right fa-xs ms-1"></i>
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- 4. ABOUT TCLIMS SECTION (GENUINELY & COMPLETELY ABOUT LIMS)               -->
        <!-- ========================================================================= -->
        <section class="about-section" id="about-section">
            <div class="container-fluid px-3 px-lg-5">

                <!-- Heading Row -->
                <div class="row align-items-center g-5 mb-5">
                    <div class="col-lg-6">
                        <div class="section-eyebrow mb-2">
                            <i class="fas fa-cogs text-primary"></i> Enterprise Digital Architecture
                        </div>
                        <h2 class="section-main-title text-start mb-3">
                            About TC-LIMS Platform
                        </h2>
                        <p class="text-secondary mb-3" style="line-height: 1.7; font-size: 1.02rem;">
                            The <strong>Textiles Committee Laboratory Information Management System (TC-LIMS)</strong> is the comprehensive digital enterprise platform powering the laboratory network of the <strong>Textiles Committee, Ministry of Textiles, Government of India</strong> (established under the Textiles Committee Act, 1963).
                        </p>
                        <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 0.95rem;">
                            TC-LIMS replaces fragmented manual testing processes with a single, synchronized digital environment. From sample inward registration to barcode allocation, analyst assignment, digital manuscript computation, two-tier scientific review, invoice settlement, and QR-coded report generation, TC-LIMS guarantees 100% data integrity and turnaround speed.
                        </p>

                        <!-- Key Checkpoints -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                    <div>
                                        <strong class="d-block text-dark small">Complete Chain of Custody</strong>
                                        <span class="small text-muted">Barcode and QR identifiers track sample transit from desk to analyst bay.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                    <div>
                                        <strong class="d-block text-dark small">Zero Manual Arithmetic Error</strong>
                                        <span class="small text-muted">Automated mathematical formulas directly evaluate raw readings and tolerances.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                    <div>
                                        <strong class="d-block text-dark small">Two-Tier Quality Validation</strong>
                                        <span class="small text-muted">Rigorous review by Section Incharge and Quality Manager prior to release.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                    <div>
                                        <strong class="d-block text-dark small">Instant Online Verification</strong>
                                        <span class="small text-muted">Buyers and customs authorities verify report authenticity in seconds via QR scan.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('customer.login') }}" class="btn btn-lims-primary px-4 py-2" style="border-radius: 8px;">
                            Access Customer Self-Service Portal <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>

                    <!-- Right Column: 4 Strategic Pillars of TC-LIMS -->
                    <div class="col-lg-6">
                        <div class="about-pillar-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="about-pillar-icon">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">1. Sample Inward & Barcoded Custody</h4>
                                    <span class="small text-muted">Tamper-proof sample tracking from registration to disposal</span>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                Every sample received at Central or Regional laboratories is assigned a unique tracking identifier. Multi-parameter testing orders generate routing slips and sub-sample labels automatically.
                            </p>
                        </div>

                        <div class="about-pillar-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="about-pillar-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">2. Paperless Digital Analyst Manuscripts</h4>
                                    <span class="small text-muted">Accredited calculation formulas replacing paper registers</span>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                Certified analysts enter raw specimen measurements (grams, newtons, optical density, color differences) directly into standardized digital manuscripts with automatic calculation according to ISO/ASTM standards.
                            </p>
                        </div>

                        <div class="about-pillar-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="about-pillar-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">3. Multi-Tier Review & Digital Approvals</h4>
                                    <span class="small text-muted">Section Incharge and Quality Manager validation</span>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                Built-in audit trail enforces review of calibration curves, environmental humidity/temperature records, and tolerances before digital approval signatures are affixed.
                            </p>
                        </div>

                        <div class="about-pillar-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="about-pillar-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">4. Cryptographic QR Reports & Invoicing</h4>
                                    <span class="small text-muted">Instant customer download & verifiable authenticity</span>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                Generated PDF test reports embed a verifiable cryptographic QR code and NABL accreditation symbol. Automated invoicing and Razorpay gateway integration enable effortless online payments.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 6 Step Visual Lifecycle of a Sample in TC-LIMS -->
                <div class="mt-4 pt-4 border-top border-primary border-opacity-15">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1 mb-2">END-TO-END PROCESS</span>
                        <h4 class="fw-bold text-dark">The 6-Stage Digital Lifecycle in TC-LIMS</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">1</div>
                                <h6 class="fw-bold text-dark mb-1">Inward & Booking</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Sample registered with customer & buyer specs; barcode issued.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">2</div>
                                <h6 class="fw-bold text-dark mb-1">Parameter Allocation</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Assigned to qualified analyst bays based on test discipline.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">3</div>
                                <h6 class="fw-bold text-dark mb-1">Analyst Manuscript</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Direct entry of raw readings; formulas auto-calculate results.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">4</div>
                                <h6 class="fw-bold text-dark mb-1">Technical Review</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Section Incharge verifies tolerances & standard procedures.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">5</div>
                                <h6 class="fw-bold text-dark mb-1">Quality Approval</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Authorized signatory approves test certificate release.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="workflow-step-card text-center">
                                <div class="step-number-badge mx-auto">6</div>
                                <h6 class="fw-bold text-dark mb-1">QR Report Dispatch</h6>
                                <p class="small text-muted mb-0" style="font-size: 0.74rem;">Tamper-proof QR PDF report published to client portal.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- 5. CONTACT US SECTION                                                     -->
        <!-- ========================================================================= -->
        <section class="contact-section" id="contact-section">
            <div class="container-fluid px-3 px-lg-5">

                <div class="text-center mb-5">
                    <div class="section-eyebrow">
                        <i class="fas fa-headset text-primary"></i> Support & Technical Inquiries
                    </div>
                    <h2 class="section-main-title">Contact TC-LIMS Technical Support</h2>
                    <p class="text-muted small mx-auto" style="max-width: 650px;">
                        Have questions about laboratory test parameters, sample submission, fee structure, or consignment certification? Get in touch with our team.
                    </p>
                </div>

                <div class="row g-4 align-items-stretch">

                    <!-- Left: Interactive Contact Form -->
                    <div class="col-lg-7">
                        <div class="contact-form-card">
                            <h4 class="fw-bold text-dark mb-3" style="font-size: 1.25rem;">
                                Submit Your Query / Testing Requirement
                            </h4>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form id="contactForm" method="POST" action="{{ route('contact_support') }}" class="row g-3">
                                @csrf

                                <div class="col-md-6">
                                    <label for="txt_first_name" class="form-label small fw-bold text-dark">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="txt_first_name" name="txt_first_name"
                                        value="{{ old('txt_first_name') }}" placeholder="Enter first name" required>
                                    @error('txt_first_name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="txt_last_name" class="form-label small fw-bold text-dark">Last Name</label>
                                    <input type="text" class="form-control" id="txt_last_name" name="txt_last_name"
                                        value="{{ old('txt_last_name') }}" placeholder="Enter last name">
                                    @error('txt_last_name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="txt_email" class="form-label small fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="txt_email" name="txt_email"
                                        value="{{ old('txt_email') }}" placeholder="name@company.com" required>
                                    @error('txt_email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="txt_phone" class="form-label small fw-bold text-dark">Phone / Mobile <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="txt_phone" name="txt_phone"
                                        value="{{ old('txt_phone') }}" placeholder="e.g. 9876543210" required>
                                    @error('txt_phone')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="txt_organization" class="form-label small fw-bold text-dark">Organization / Mill Name</label>
                                    <input type="text" class="form-control" id="txt_organization" name="txt_organization"
                                        value="{{ old('txt_organization') }}" placeholder="Company or Mill Name">
                                    @error('txt_organization')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="txt_lab_type" class="form-label small fw-bold text-dark">Testing Discipline <span class="text-danger">*</span></label>
                                    <select class="form-select" id="txt_lab_type" name="txt_lab_type" required>
                                        <option value="">Select Testing Discipline...</option>
                                        <option value="textile_testing" {{ old('txt_lab_type') == 'textile_testing' ? 'selected' : '' }}>Physical & Mechanical Fabric Testing</option>
                                        <option value="fiber_analysis" {{ old('txt_lab_type') == 'fiber_analysis' ? 'selected' : '' }}>Fiber & Yarn Quality (HVI / Tensile)</option>
                                        <option value="quality_control" {{ old('txt_lab_type') == 'quality_control' ? 'selected' : '' }}>Chemical & Eco-Testing (Azo, REACH, OEKO-TEX)</option>
                                        <option value="research" {{ old('txt_lab_type') == 'research' ? 'selected' : '' }}>Protective & Medical Textiles Lab</option>
                                        <option value="regional" {{ old('txt_lab_type') == 'regional' ? 'selected' : '' }}>Regional Laboratory Testing Center</option>
                                    </select>
                                    @error('txt_lab_type')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="txt_message" class="form-label small fw-bold text-dark">Testing Requirement Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="txt_message" name="txt_message" rows="4"
                                        placeholder="Describe your sample type, parameters, testing standard (ISO/ASTM/AATCC/BIS), or any specific technical requirements..."
                                        required>{{ old('txt_message') }}</textarea>
                                    @error('txt_message')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-lims-primary px-4 py-2 fw-bold shadow-sm" id="contactSubmitBtn">
                                        <i class="fas fa-paper-plane me-2"></i> Submit Inquiry
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Headquarters & Contact Details -->
                    <div class="col-lg-5">
                        <div class="contact-info-card">
                            <h4 class="fw-bold mb-3" style="color: #ffffff; font-size: 1.3rem;">
                                Central Laboratory & Headquarters
                            </h4>
                            <p class="small text-white-50 mb-4">
                                The Textiles Committee operates 15+ NABL accredited testing centers nationwide to support textile mills, exporters, and government procurement.
                            </p>

                            <div class="contact-meta-row">
                                <div class="contact-icon-pill">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-white small">Textiles Committee Headquarters</div>
                                    <div class="small text-white-50">
                                        P. Balu Road, Prabhadevi, Mumbai - 400 025, Maharashtra, India
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="contact-meta-row">
                                <div class="contact-icon-pill">
                                    <i class="fas fa-landmark"></i>
                                </div>
                                 <div>
                                    <div class="fw-bold text-white small">Ministry of Textiles, GOI</div>
                                    <div class="small text-white-50">
                                        Udyog Bhawan, New Delhi - 110 011, India
                                    </div>
                                </div> 
                            </div> --}}

                            <div class="contact-meta-row">
                                <div class="contact-icon-pill">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-white small">Testing Helpline</div>
                                    <div class="small text-white-50">
                                        +022-66527519 / +022-66527541
                                    </div>
                                </div>
                            </div>

                            <div class="contact-meta-row">
                                <div class="contact-icon-pill">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-white small">Official Inquiries & Support</div>
                                    <div class="small text-white-50">+022-66527519
                                    </div>
                                </div>
                            </div>

                            <div class="contact-meta-row mb-0">
                                {{-- <div class="contact-icon-pill">
                                    <i class="fas fa-clock"></i>
                                </div> --}}
                                {{-- <div>
                                    <div class="fw-bold text-white small">Operating Hours</div>
                                    <div class="small text-white-50">
                                        Monday to Friday: 09:30 AM to 06:00 PM IST 
                                    </div>
                                </div> --}}
                            </div>

                            <div class="mt-4 pt-3 border-top border-white border-opacity-15 text-center">
                                <a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 w-100 fw-bold">
                                    <i class="fas fa-sign-in-alt me-1"></i> Customer Login to Submit Samples Directly
                                </a>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- 6. LABORATORY INFRASTRUCTURE CAROUSEL                                     -->
        <!-- ========================================================================= -->
        <section class="carousel-section" id="carousel-section">
            <div class="container-fluid px-3 px-lg-5">

                <div class="text-center mb-5">
                    <div class="section-eyebrow">
                        <i class="fas fa-images text-primary"></i> Testing Capabilities & Instrumentation
                    </div>
                    <h2 class="section-main-title">State-of-the-Art Laboratory Infrastructure</h2>
                    <p class="text-muted small mx-auto" style="max-width: 680px;">
                        Explore our NABL-accredited laboratory facilities equipped with advanced instrumentation and manned by certified textile scientists.
                    </p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div id="labShowcaseCarousel" class="carousel slide lims-carousel-box" data-bs-ride="carousel" data-bs-interval="4500">

                            <!-- Carousel Indicators -->
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
                                <button type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
                            </div>

                            <!-- Carousel Slides -->
                            <div class="carousel-inner">

                                <!-- Slide 1: labimg1 -->
                                <div class="carousel-item active lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg1.jpeg') }}"
                                        alt="Spectrophotometer & Color Fastness Lab">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-warning text-dark fw-bold mb-2">SPECTROPHOTOMETRY</span>
                                        <h4 class="fw-bold mb-1">Color Fastness & Computer Color Matching</h4>
                                        <p class="small text-white-50 mb-0">
                                            Spectral reflectance, metamerism, washing fastness (ISO 105 C06), perspiration fastness, and artificial light fastness compliant with global retail standards.
                                        </p>
                                    </div>
                                </div>

                                <!-- Slide 2: labimg3 -->
                                <div class="carousel-item lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg3.jpeg') }}"
                                        alt="Chemical & Eco-Testing Lab">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-success fw-bold mb-2">ECO-TESTING</span>
                                        <h4 class="fw-bold mb-1">Analytical Wet Chemistry & Banned Azo Amines</h4>
                                        <p class="small text-white-50 mb-0">
                                            Gas Chromatography - Mass Spectrometry (GC-MS) and HPLC screening for banned carcinogenic aryl amines, chlorophenols, and REACH SVHC compliance.
                                        </p>
                                    </div>
                                </div>

                                <!-- Slide 3: labimg4 -->
                                <div class="carousel-item lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg4.jpeg') }}"
                                        alt="Universal Tensile Strength Tester">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-primary fw-bold mb-2">MECHANICAL TESTING</span>
                                        <h4 class="fw-bold mb-1">Universal Tensile & Tear Strength Analysis</h4>
                                        <p class="small text-white-50 mb-0">
                                            Electromechanical universal testing instruments analyzing Grab tensile, Strip tensile, Bursting strength, Seam slippage, and tear resistance.
                                        </p>
                                    </div>
                                </div>

                                <!-- Slide 4: labimg5 -->
                                <div class="carousel-item lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg5.jpeg') }}"
                                        alt="Fiber Fineness & Maturity Analysis">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-info fw-bold mb-2">FIBER LABORATORY</span>
                                        <h4 class="fw-bold mb-1">High Volume Instrument (HVI) & Fiber Maturity</h4>
                                        <p class="small text-white-50 mb-0">
                                            Automated measurement of cotton micronaire, upper half mean length (UHML), fiber bundle strength, uniformity index, and blend composition.
                                        </p>
                                    </div>
                                </div>

                                <!-- Slide 5: labimg7 -->
                                <div class="carousel-item lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg7.jpeg') }}"
                                        alt="Water Repellency & Hydrostatic Head">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-secondary fw-bold mb-2">PROTECTIVE APPAREL</span>
                                        <h4 class="fw-bold mb-1">Water Repellency & Hydrostatic Barrier Testing</h4>
                                        <p class="small text-white-50 mb-0">
                                            Precision hydrostatic head testing (AATCC 127), Bundesmann rain testing, and air permeability for medical textiles and defense apparel.
                                        </p>
                                    </div>
                                </div>

                                <!-- Slide 6: labimg8 -->
                                <div class="carousel-item lims-carousel-slide">
                                    <img src="{{ asset('frontAssets/labimg8.jpeg') }}"
                                        alt="Chromatography & Heavy Metal Analysis">
                                    <div class="lims-carousel-caption">
                                        <span class="badge bg-warning text-dark fw-bold mb-2">ADVANCED INSTRUMENTATION</span>
                                        <h4 class="fw-bold mb-1">Ultra-Trace Heavy Metal & Formaldehyde Testing</h4>
                                        <p class="small text-white-50 mb-0">
                                            Inductively Coupled Plasma (ICP) spectroscopy determining extractable Lead, Cadmium, and Nickel content for baby-wear safety and international export.
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <!-- Carousel Controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#labShowcaseCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- 7. NATIONAL IMPACT & STATS STRIP                                          -->
        <!-- ========================================================================= -->
        <section class="stats-section" id="stats-section">
            <div class="container-fluid px-3 px-lg-5">
                <div class="row g-4 align-items-center">

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="15" data-suffix="+">15+</div>
                        <div class="stat-label">NABL Accredited Labs</div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="125000" data-suffix="+">1,25,000+</div>
                        <div class="stat-label">Samples Tested Annually</div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="450" data-suffix="+">450+</div>
                        <div class="stat-label">Standard Test Parameters</div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="99.8" data-suffix="%">99.8%</div>
                        <div class="stat-label">On-Time SLA Dispatch</div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="10000" data-suffix="+">10,000+</div>
                        <div class="stat-label">Mills & Exporters Served</div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2 stat-item-box">
                        <div class="stat-number" data-target="100" data-suffix="%">100%</div>
                        <div class="stat-label">QR Verifiable Test Reports</div>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <!-- ========================================================================= -->
    <!-- 8. REFINED FOOTER                                                         -->
    <!-- ========================================================================= -->
    <footer class="lims-footer">
        <div class="container-fluid px-3 px-lg-5 pt-5">
            <div class="row g-4">

                <!-- Col 1: Ministry & TC-LIMS Identity -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textiles Committee"
                            style="height: 56px; background: #fff; border-radius: 50%; padding: 4px; border: 1.5px solid var(--tc-blue-border);">
                        <div>
                            <div class="fw-bold" style="color: var(--tc-navy-mid); font-size: 0.95rem;">वस्त्र समिति</div>
                            <div class="fw-bold" style="color: var(--tc-navy); font-size: 1.05rem;">TEXTILES COMMITTEE</div>
                            <div class="fw-bold" style="color: var(--tc-navy); font-size: 0.95rem;">Ministry of Textiles</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">Government of India</div>
                        </div>
                    </div>
                    <p class="small text-muted" style="line-height: 1.6;">
                        The Textiles Committee Laboratory Information Management System (TC-LIMS) is the digital backbone ensuring
                        scientific integrity, transparent testing, and quality compliance across India's
                        textile and apparel manufacturing clusters.
                    </p>
                </div>

                <!-- Col 2: Official Portals -->
                <div class="col-lg-3 col-md-6">
                    <h5>Official Government Portals</h5>
                    <ul class="list-unstyled lh-lg small">
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="https://india.gov.in" target="_blank" rel="noopener noreferrer">National Portal of India (india.gov.in)</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="https://texmin.gov.in" target="_blank" rel="noopener noreferrer">Ministry of Textiles (texmin.gov.in)</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="https://textilescommittee.gov.in" target="_blank" rel="noopener noreferrer">Textiles Committee Portal</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="https://mygov.in" target="_blank" rel="noopener noreferrer">MyGov India (mygov.in)</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="https://digitalindia.gov.in" target="_blank" rel="noopener noreferrer">Digital India Portal</a></li>
                    </ul>
                </div>

                <!-- Col 3: Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5>Quick Navigation</h5>
                    <ul class="list-unstyled lh-lg small">
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="{{ url('/') }}">Home</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="#leadership-section">Leadership & Ministers</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="#map-section">Pan-India Laboratories</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="#about-section">About TC-LIMS</a></li>
                        {{-- <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="{{ url('/search-tracker') }}">Track Sample Status</a></li>
                        <li><i class="fas fa-chevron-right fa-xs me-2 text-primary"></i><a href="{{ route('customer.login') }}">Customer Portal Login</a></li> --}}
                    </ul>
                </div>

                <!-- Col 4: Contact & Headquarters -->
                <div class="col-lg-3 col-md-6">
                    <h5>Contact & Headquarters</h5>
                    <div class="small text-muted mb-2">
                        <i class="fas fa-building me-2 text-primary"></i>
                        <strong>Textiles Committee</strong><br>
                        P. Balu Road, Prabhadevi, Mumbai - 400025
                    </div>
                    {{-- <div class="small text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <strong>Ministry of Textiles</strong><br>
                        Udyog Bhawan, New Delhi - 110011, India
                    </div> --}}
                    <div class="small text-muted mb-1">
                        <i class="fas fa-phone-alt me-2 text-primary"></i> Helpline: +91-22-66527519 / 41
                    </div>
                    {{-- <div class="small text-muted">
                        <i class="fas fa-envelope me-2 text-primary"></i> 
                    </div> --}}
                </div>

            </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="footer-bottom-bar">
            <div class="container-fluid px-3 px-lg-5">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        &copy; 2026 <strong>Laboratories of Textile Committee</strong>, Ministry of Textiles, Government of India. All rights reserved.
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="small">Laboratory Information Management System (TC-LIMS)</span>
                        <div class="tts-logo-wrap" style="height: 26px; padding: 2px 6px;">
                            <img src="{{ asset('backAssets/images/3TS.png') }}" alt="3TS" style="max-height: 20px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button type="button" id="backToTopBtn" class="back-to-top-btn" aria-label="Back to top" title="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- ========================================================================= -->
    <!-- 9. STAFF LOGIN MODAL                                                      -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--tc-navy) 0%, var(--tc-navy-mid) 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Logo"
                            style="height: 34px; background: #fff; border-radius: 50%; padding: 2px;">
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="loginModalLabel" style="font-size: 1.1rem;">TC-LIMS Staff Access</h5>
                            <span class="small text-white-50" style="font-size: 0.72rem;">Authorized Laboratory Officers & Analysts</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <form id="loginForm" method="POST" action="{{ route('user_login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="login_email" class="form-label small fw-bold text-dark">Official Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control border-start-0" id="login_email" name="txt_email"
                                    value="{{ old('txt_email') }}" placeholder="officer@textilescommittee.gov.in" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="login_password" class="form-label small fw-bold text-dark">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="login_password" name="txt_password"
                                    placeholder="Enter your security password" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label small text-muted" for="rememberMe">Remember Session</label>
                            </div>
                            <a href="{{ route('customer.login') }}" class="small fw-semibold text-decoration-none" style="color: var(--tc-navy-mid);">
                                Customer Login?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-lims-primary w-100 py-2 fw-bold" id="loginSubmitBtn" style="border-radius: 8px;">
                            <i class="fas fa-sign-in-alt me-1"></i> Secure Officer Sign In
                        </button>
                    </form>
                </div>

                <div class="modal-footer bg-light py-2 justify-content-center">
                    <span class="small text-muted"><i class="fas fa-shield-alt text-success me-1"></i> Protected by GOI Cyber Security & 2FA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. SCRIPTS                                                               -->
    <!-- ========================================================================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>

    <script>
        // =========================================================================
        // 1. TEXTILES COMMITTEE LABORATORIES COORDINATES ON INDIA SVG MAP
        // =========================================================================
        const labLocations = [
            {
                code: 'MUMBAI',
                x: 98,
                y: 416,
                labelX: -65,
                labelY: 15,
                name: "Central Testing Laboratory (HQ)",
                city: "Prabhadevi, Mumbai, Maharashtra",
                est: "1963",
                badge: "Apex Central Lab",
                specialty: "Physical, Chemical, Eco-Testing, HPLC, GC-MS & NABL Hub",
                phone: "+91-22-66527500",
                email: "secy.tc@nic.in"
            },
            {
                code: 'DELHI',
                x: 180,
                y: 198,
                labelX: 52,
                labelY: -10,
                name: "Regional Testing Laboratory (Delhi)",
                city: "Naraina Industrial Area, New Delhi",
                est: "1975",
                badge: "North Hub",
                specialty: "Garment Testing, Color Fastness, Flammability & REACH",
                phone: "+91-11-25791240",
                email: "delhi.tc@nic.in"
            },
            {
                code: 'TIRUPUR',
                x: 198,
                y: 590,
                labelX: 55,
                labelY: -5,
                name: "Textiles Committee Laboratory (Tirupur)",
                city: "Tirupur, Tamil Nadu",
                est: "1989",
                badge: "Knitwear Hub",
                specialty: "Knitwear Testing, Oeko-Tex, Azo Dyes & Dimensional Stability",
                phone: "+91-421-2223842",
                email: "tirupur.tc@nic.in"
            },
            {
                code: 'LUDHIANA',
                x: 165,
                y: 155,
                labelX: 52,
                labelY: -15,
                name: "Regional Testing Laboratory (Ludhiana)",
                city: "Focal Point, Ludhiana, Punjab",
                est: "1981",
                badge: "Wool & Knitwear",
                specialty: "Wool Blends, Thermal Resistance, Fiber Micronaire & Pilling",
                phone: "+91-161-2670295",
                email: "ludhiana.tc@nic.in"
            },
            {
                code: 'COIMBATORE',
                x: 182,
                y: 605,
                labelX: -70,
                labelY: 15,
                name: "Regional Laboratory (Coimbatore)",
                city: "Coimbatore, Tamil Nadu",
                est: "1972",
                badge: "Spinning Hub",
                specialty: "Yarn Evenness, Tensile Properties, Fiber Maturity & HVI",
                phone: "+91-422-2231268",
                email: "cbe.tc@nic.in"
            },
            {
                code: 'AHMEDABAD',
                x: 92,
                y: 326,
                labelX: -68,
                labelY: -15,
                name: "Textiles Committee Laboratory (Ahmedabad)",
                city: "Ahmedabad, Gujarat",
                est: "1970",
                badge: "Denim & Cotton",
                specialty: "Denim & Weaving Quality, High Volume Testing & Effluents",
                phone: "+91-79-26581452",
                email: "ahmedabad.tc@nic.in"
            },
            {
                code: 'SURAT',
                x: 98,
                y: 366,
                labelX: -55,
                labelY: 12,
                name: "Textiles Committee Laboratory (Surat)",
                city: "Surat, Gujarat",
                est: "1982",
                badge: "Synthetic Hub",
                specialty: "Man-Made Fibers, Polyester/Nylon Filaments & Dye Quality",
                phone: "+91-261-2463321",
                email: "surat.tc@nic.in"
            },
            {
                code: 'KOLKATA',
                x: 424,
                y: 334,
                labelX: 55,
                labelY: 0,
                name: "Regional Testing Laboratory (Kolkata)",
                city: "Salt Lake, Kolkata, West Bengal",
                est: "1968",
                badge: "East Hub & Jute",
                specialty: "Jute & Technical Textiles, Chemical & Microbiological",
                phone: "+91-33-23572834",
                email: "kolkata.tc@nic.in"
            },
            {
                code: 'BENGALURU',
                x: 172,
                y: 520,
                labelX: -68,
                labelY: 0,
                name: "Textiles Committee Laboratory (Bengaluru)",
                city: "Bengaluru, Karnataka",
                est: "1985",
                badge: "Silk & Apparel",
                specialty: "Silk Purity, Garment Performance & REACH Substances",
                phone: "+91-80-22264871",
                email: "bengaluru.tc@nic.in"
            },
            {
                code: 'KANPUR',
                x: 250,
                y: 245,
                labelX: 52,
                labelY: 0,
                name: "Regional Testing Laboratory (Kanpur)",
                city: "Civil Lines, Kanpur, Uttar Pradesh",
                est: "1978",
                badge: "Central Hub",
                specialty: "Protective Workwear, Heavy Canvas, Tensile & Water Repellency",
                phone: "+91-512-2303827",
                email: "kanpur.tc@nic.in"
            },
            {
                code: 'JAIPUR',
                x: 145,
                y: 240,
                labelX: -60,
                labelY: -15,
                name: "Textiles Committee Laboratory (Jaipur)",
                city: "Jaipur, Rajasthan",
                est: "1988",
                badge: "Prints & Crafts",
                specialty: "Natural Dye Identification, Traditional Prints & Eco-Safety",
                phone: "+91-141-2742918",
                email: "jaipur.tc@nic.in"
            },
            {
                code: 'KARUR',
                x: 195,
                y: 565,
                labelX: 52,
                labelY: 10,
                name: "Textiles Committee Laboratory (Karur)",
                city: "Karur, Tamil Nadu",
                est: "1995",
                badge: "Home Textiles",
                specialty: "Bed & Bath Linen, Fiber Blend Verification & Light Fastness",
                phone: "+91-4324-221294",
                email: "karur.tc@nic.in"
            }
        ];

        const hostStates = ['mh', 'dl', 'tn', 'pb', 'gj', 'wb', 'ka', 'up', 'rj'];
        let activeLabCode = null;

        // Initialize SVG Map
        function initIndiaMap() {
            const container = document.getElementById('indiaMapContainer');
            if (!container) return;

            const localSvgUrl = "{{ asset('frontAssets/india.svg') }}";
            const cachedSvg = localStorage.getItem('tc_india_vector_svg');

            if (cachedSvg && cachedSvg.includes('<svg') && cachedSvg.includes('viewBox')) {
                renderIndiaSvg(cachedSvg);
                return;
            }

            fetch(localSvgUrl)
                .then(res => {
                    if (!res.ok) throw new Error('Local SVG fetch failed');
                    return res.text();
                })
                .then(svgText => {
                    if (svgText && svgText.includes('<svg')) {
                        try { localStorage.setItem('tc_india_vector_svg', svgText); } catch(e) {}
                        renderIndiaSvg(svgText);
                    }
                })
                .catch(() => {
                    fetch('https://cdn.jsdelivr.net/npm/@svg-maps/india@1.0.1/india.svg')
                        .then(r => r.text())
                        .then(text => renderIndiaSvg(text))
                        .catch(err => console.error('Map loading error:', err));
                });
        }

        function renderIndiaSvg(svgText) {
            const container = document.getElementById('indiaMapContainer');
            const frame = document.getElementById('indiaMapViewport');
            const tooltip = document.getElementById('stateHoverTooltip');
            if (!container || !frame) return;

            const parser = new DOMParser();
            const doc = parser.parseFromString(svgText, 'image/svg+xml');
            const parsedSvg = doc.querySelector('svg');
            if (!parsedSvg) return;

            const svg = document.importNode(parsedSvg, true);
            svg.setAttribute('id', 'indiaMapSvg');
            svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
            svg.setAttribute('aria-label', 'Map of India showing Textiles Committee Laboratories');

            const paths = svg.querySelectorAll('path');
            paths.forEach(p => {
                p.classList.add('india-state-path');
                const stateId = (p.getAttribute('id') || '').toLowerCase();
                const stateName = p.getAttribute('aria-label') || p.getAttribute('name') || '';

                if (hostStates.includes(stateId)) {
                    p.classList.add('state-lab-host');
                }

                if (stateName) {
                    p.addEventListener('mouseenter', (e) => {
                        if (tooltip) {
                            tooltip.textContent = stateName;
                            tooltip.style.display = 'block';
                            positionTooltip(tooltip, e.clientX, e.clientY, frame);
                        }
                    });
                    p.addEventListener('mousemove', (e) => {
                        if (tooltip && tooltip.style.display === 'block') {
                            positionTooltip(tooltip, e.clientX, e.clientY, frame);
                        }
                    });
                    p.addEventListener('mouseleave', () => {
                        if (tooltip) tooltip.style.display = 'none';
                    });
                }
            });

            const svgNs = "http://www.w3.org/2000/svg";
            const pinsGroup = document.createElementNS(svgNs, 'g');
            pinsGroup.setAttribute('id', 'labPinsGroup');

            labLocations.forEach(lab => {
                const pinG = document.createElementNS(svgNs, 'g');
                pinG.setAttribute('class', 'tra-svg-pin');
                pinG.setAttribute('id', 'pin-' + lab.code);
                pinG.setAttribute('data-code', lab.code);
                pinG.setAttribute('transform', `translate(${lab.x}, ${lab.y})`);

                const radar = document.createElementNS(svgNs, 'circle');
                radar.setAttribute('class', 'tra-pin-radar');
                radar.setAttribute('cx', '0');
                radar.setAttribute('cy', '0');
                radar.setAttribute('r', '8');
                pinG.appendChild(radar);

                const core = document.createElementNS(svgNs, 'circle');
                core.setAttribute('class', 'tra-pin-core');
                core.setAttribute('cx', '0');
                core.setAttribute('cy', '0');
                core.setAttribute('r', '6.5');
                pinG.appendChild(core);

                const dot = document.createElementNS(svgNs, 'circle');
                dot.setAttribute('class', 'tra-pin-dot');
                dot.setAttribute('cx', '0');
                dot.setAttribute('cy', '0');
                dot.setAttribute('r', '2.2');
                pinG.appendChild(dot);

                const tagG = document.createElementNS(svgNs, 'g');
                tagG.setAttribute('transform', `translate(${lab.labelX}, ${lab.labelY})`);

                const tagW = lab.code.length > 5 ? 88 : 56;
                const tagH = 20;

                const tagBg = document.createElementNS(svgNs, 'rect');
                tagBg.setAttribute('class', 'tra-tag-bg');
                tagBg.setAttribute('x', -tagW / 2);
                tagBg.setAttribute('y', -tagH / 2);
                tagBg.setAttribute('width', tagW);
                tagBg.setAttribute('height', tagH);
                tagBg.setAttribute('rx', '4');
                tagG.appendChild(tagBg);

                const tagText = document.createElementNS(svgNs, 'text');
                tagText.setAttribute('class', 'tra-tag-text');
                tagText.setAttribute('x', '0');
                tagText.setAttribute('y', '0');
                tagText.textContent = lab.code;
                tagG.appendChild(tagText);

                pinG.appendChild(tagG);

                pinG.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectLabOnMap(lab.code);
                });

                pinsGroup.appendChild(pinG);
            });

            svg.appendChild(pinsGroup);
            container.innerHTML = '';
            container.appendChild(svg);
        }

        function positionTooltip(el, clientX, clientY, frame) {
            const rect = frame.getBoundingClientRect();
            const x = clientX - rect.left;
            const y = clientY - rect.top;
            el.style.left = `${x}px`;
            el.style.top = `${y}px`;
        }

        function selectLabOnMap(code) {
            const lab = labLocations.find(l => l.code === code);
            if (!lab) return;

            activeLabCode = code;

            document.querySelectorAll('.lab-pill-btn').forEach(p => p.classList.remove('active'));
            const activePill = document.getElementById('pill-' + code);
            if (activePill) activePill.classList.add('active');

            document.querySelectorAll('.tra-svg-pin').forEach(pin => pin.classList.remove('active'));
            const activePin = document.getElementById('pin-' + code);
            if (activePin) activePin.classList.add('active');

            const popup = document.getElementById('limsLabPopup');
            const frame = document.getElementById('indiaMapViewport');
            if (popup && frame) {
                popup.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge text-uppercase fw-bold" style="background: var(--tc-blue-tint); color: var(--tc-navy); font-size: 0.68rem;">${lab.badge}</span>
                        <button type="button" class="btn-close btn-sm" onclick="closeLabPopup()" style="font-size: 0.7rem;"></button>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">${lab.name}</h6>
                    <div class="small text-muted mb-2"><i class="fas fa-map-marker-alt text-primary me-1"></i> ${lab.city}</div>
                    <div class="p-2 rounded mb-2" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.74rem;">
                        <strong>Key Testing:</strong> ${lab.specialty}
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-1 border-top" style="font-size: 0.72rem;">
                        <span class="text-muted"><i class="fas fa-phone me-1"></i> ${lab.phone}</span>
                        <a href="#contact-section" class="fw-bold text-decoration-none" style="color: var(--tc-navy-mid);">Submit Sample &rarr;</a>
                    </div>
                `;
                popup.style.display = 'block';
                popup.style.top = '14px';
                popup.style.right = '14px';
            }
        }

        function closeLabPopup() {
            const popup = document.getElementById('limsLabPopup');
            if (popup) popup.style.display = 'none';
            document.querySelectorAll('.tra-svg-pin').forEach(pin => pin.classList.remove('active'));
            document.querySelectorAll('.lab-pill-btn').forEach(p => p.classList.remove('active'));
        }

        function resetIndiaMapView() {
            closeLabPopup();
        }

        // =========================================================================
        // 2. STATS COUNTER ANIMATION
        // =========================================================================
        function initStatsCounter() {
            const counters = document.querySelectorAll('.stat-number');
            let animated = false;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !animated) {
                        animated = true;
                        counters.forEach(counter => {
                            const target = parseFloat(counter.getAttribute('data-target'));
                            const suffix = counter.getAttribute('data-suffix') || '';
                            const duration = 2000;
                            const startTime = performance.now();

                            function updateCount(currentTime) {
                                const elapsed = currentTime - startTime;
                                const progress = Math.min(elapsed / duration, 1);
                                const current = Math.floor(progress * target);

                                if (target >= 1000) {
                                    counter.textContent = current.toLocaleString('en-IN') + suffix;
                                } else if (target % 1 !== 0) {
                                    counter.textContent = (progress * target).toFixed(1) + suffix;
                                } else {
                                    counter.textContent = current + suffix;
                                }

                                if (progress < 1) {
                                    requestAnimationFrame(updateCount);
                                } else {
                                    counter.textContent = target.toLocaleString('en-IN') + suffix;
                                }
                            }
                            requestAnimationFrame(updateCount);
                        });
                    }
                });
            }, { threshold: 0.3 });

            const statsSection = document.getElementById('stats-section');
            if (statsSection) observer.observe(statsSection);
        }

        // =========================================================================
        // 3. EVENT LISTENERS & SWEETALERT FOR FORMS
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function () {
            initIndiaMap();
            initStatsCounter();

            const toggle = document.getElementById('mobileMenuToggle');
            const drawer = document.getElementById('mobileNavDrawer');
            const overlay = document.getElementById('mobileNavOverlay');
            const closeBtn = document.getElementById('drawerClose');

            function openDrawer() {
                drawer.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                drawer.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggle) toggle.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (overlay) overlay.addEventListener('click', closeDrawer);

            document.querySelectorAll('.mobile-drawer-body .nav-link').forEach(link => {
                link.addEventListener('click', closeDrawer);
            });

            // Back to Top Button
            const backToTopBtn = document.getElementById('backToTopBtn');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 350) {
                    backToTopBtn.style.display = 'flex';
                } else {
                    backToTopBtn.style.display = 'none';
                }
            });

            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Contact Form
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function (e) {
                    const submitBtn = document.getElementById('contactSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
                    }
                });
            }

            // Staff Login Form with SweetAlert2
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('loginSubmitBtn');
                    const originalHtml = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Authenticating...';

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
                            text: data.success || 'Welcome to TC-LIMS portal.',
                            confirmButtonColor: '#0e244d',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            didClose: () => {
                                window.location.href = data.redirect || "{{ route('dashboard') }}";
                            }
                        });
                    })
                    .catch(errors => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;

                        let msg = 'Invalid credentials. Please verify your official email and password.';
                        if (errors && errors.errors) {
                            msg = Object.values(errors.errors).flat().join('<br>');
                        } else if (errors && errors.message) {
                            msg = errors.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Authentication Failed',
                            html: msg,
                            confirmButtonColor: '#0e244d',
                        });
                    });
                });
            }

        });
    </script>
</body>

</html>
