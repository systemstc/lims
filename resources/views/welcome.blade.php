<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIMS - Laboratory Information Management System | Textile Committee</title>
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --secondary-color: #1e293b;
            --accent-color: #06b6d4;
            --accent-light: #0891b2;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #64748b;
            --text-dark: #334155;
            --textile-red: #c41e3ae1;
            --textile-blue: #1d4ed8;
            --textile-gold: #d4af37e1;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            box-shadow: 0 2px 15px rgba(196, 30, 58, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 0;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(228, 56, 84, 0.4)), url('{{ asset('frontAssets/header-image.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 100px 0 50px;
        }

        .hero-image {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(228, 56, 84, 0.3));
            border-radius: 0.5rem;
            z-index: 1;
            pointer-events: none;
        }

        .hero-image img {
            position: relative;
            z-index: 0;
            width: 100%;
            height: auto;
            max-width: 500px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="textile" patternUnits="userSpaceOnUse" width="20" height="20"><rect fill="none" stroke="%23ffffff" stroke-width="0.1" width="20" height="20"/></pattern></defs><rect width="100" height="100" fill="url(%23textile)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(196, 30, 58, 0.15);
            border-color: var(--textile-red);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2rem;
            box-shadow: 0 8px 25px rgba(196, 30, 58, 0.3);
        }

        .stats-section {
            background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
            position: relative;
            padding: 4rem 0;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            opacity: 0.05;
            z-index: 0;
        }

        .stats-section>.container {
            position: relative;
            z-index: 1;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        /* Image Slider Styles */
        .slider-section {
            background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
            padding: 5rem 0;
            position: relative;
        }

        .slider-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            position: relative;
        }

        .slide img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .slide-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, rgba(247, 62, 38, 0.829), rgba(243, 177, 53, 0.822));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .slide-description {
            font-size: 1rem;
            opacity: 0.9;
        }

        .slider-nav {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 1rem;
        }

        .slider-btn {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.3);
        }

        .slider-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(196, 30, 58, 0.4);
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 0.5rem;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--medium-gray);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: var(--textile-red);
            transform: scale(1.2);
        }

        .contact-section {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--textile-red) 100%);
            color: white;
            padding: 6rem 0;
            position: relative;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('frontAssets/labimg14.jpeg') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 0;
        }

        .contact-section>.container {
            position: relative;
            z-index: 1;
        }

        .form-control {
            border: 1px solid var(--medium-gray);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease;
            background-color: white;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--textile-red);
            box-shadow: 0 0 0 0.25rem rgba(196, 30, 58, 0.15);
            background-color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            border: none;
            padding: 0.875rem 2.5rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(196, 30, 58, 0.4);
            background: linear-gradient(135deg, #a91731, #c9971c);
        }

        .btn-outline-light {
            color: var(--light-gray);
        }

        .btn-outline-light:hover {
            background-color: white;
            color: var(--textile-red);
        }

        .footer {
            background: var(--secondary-color);
            color: white;
            padding: 3rem 0 1rem;
        }

        .process-step {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.08);
            position: relative;
            margin-bottom: 2rem;
            border: 1px solid var(--medium-gray);
            transition: all 0.3s ease;
        }

        .process-step:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(196, 30, 58, 0.12);
        }

        .process-number {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.3);
        }

        .testimonial-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin: 1rem 0;
            border-left: 4px solid var(--textile-red);
        }

        .gov-badge {
            background: linear-gradient(135deg, var(--textile-red), var(--textile-gold));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .textile-bg {
            background: url('{{ asset('frontAssets/branding.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        .textile-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
        }

        .textile-bg>* {
            position: relative;
            z-index: 1;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-section {
                padding: 90px 0 40px;
            }

            .feature-card {
                padding: 2rem;
            }
        }

        @media (max-width: 992px) {
            .hero-section {
                padding: 80px 0 30px;
                min-height: 80vh;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .feature-card {
                padding: 1.5rem;
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }

            .stat-number {
                font-size: 2.8rem;
            }

            .slide img {
                height: 400px;
            }

            .slide-content {
                padding: 1.5rem;
            }

            .slide-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 70px 0 20px;
                min-height: 70vh;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p.lead {
                font-size: 1rem;
            }

            .stat-number {
                font-size: 2.2rem;
            }

            .feature-card,
            .process-step {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .slide img {
                height: 300px;
            }

            .slide-content {
                padding: 1rem;
            }

            .slide-title {
                font-size: 1.5rem;
            }

            .slide-description {
                font-size: 0.9rem;
            }

            .slider-btn {
                width: 40px;
                height: 40px;
            }

            .btn-primary,
            .btn-outline-light {
                padding: 0.75rem 1.5rem;
                font-size: 0.85rem;
            }

            .navbar-brand img {
                height: 40px;
            }

            .navbar-brand div {
                font-size: 0.9rem;
            }

            .navbar-brand small {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: 60px 0 15px;
                min-height: 60vh;
            }

            .hero-content h1 {
                font-size: 1.8rem;
            }

            .hero-content p.lead {
                font-size: 0.9rem;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .feature-card,
            .process-step {
                padding: 1.25rem;
                margin-bottom: 1.25rem;
            }

            .feature-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
                margin-bottom: 1.25rem;
            }

            .slide img {
                height: 250px;
            }

            .slide-content {
                padding: 0.75rem;
            }

            .slide-title {
                font-size: 1.2rem;
            }

            .slide-description {
                font-size: 0.8rem;
            }

            .slider-btn {
                width: 35px;
                height: 35px;
            }

            .btn-primary,
            .btn-outline-light {
                padding: 0.65rem 1.25rem;
                font-size: 0.8rem;
            }

            .navbar-brand img {
                height: 35px;
            }

            .navbar-brand div {
                font-size: 0.8rem;
            }

            .navbar-brand small {
                font-size: 0.65rem;
            }

            .footer {
                padding: 2rem 0 1rem;
            }

            .footer .col-lg-4,
            .footer .col-lg-2,
            .footer .col-lg-3 {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 400px) {
            .hero-content h1 {
                font-size: 1.6rem;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .btn-primary,
            .btn-outline-light {
                padding: 0.6rem 1rem;
                font-size: 0.75rem;
            }

            .process-step {
                padding: 1rem;
            }

            .process-number {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }

        /* Ensure all images are responsive */
        img {
            max-width: 100%;
            height: auto;
        }

        /* Fix for navbar collapse on mobile */
        .navbar-collapse {
            text-align: center;
        }

        .navbar-nav .nav-item {
            margin: 0.25rem 0;
        }

        .navbar-nav .btn {
            margin: 0.5rem 0;
        }

        /* Fix for form responsiveness */
        .form-row .col-md-6 {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top p-0">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textile Committee of India"
                    class="me-2 me-md-3 rounded-circle bg-light"
                    style="width: 60px; height: 60px; object-fit: contain;">
                <div>
                    <div class="fw-bold">Textile Committee</div>
                    <small class="opacity-75">Laboratory Information Management System</small>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#process">Process</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#labs">Laboratories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="#" class="btn btn-outline-light" data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                            <em class="icon ni ni-signin me-2"></em>Access System
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="{{ route('admin_login') }}" class="btn btn-outline-light">
                            <em class="icon ni ni-signin me-2"></em>Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-content">
                        <h1 id="typewriter" class="display-4 fw-bold mb-4 text-light"></h1>
                        <p class="lead mb-4">
                            Official Laboratory Information Management System developed by the Textile Committee
                            for standardized sample management, testing protocols, and quality assurance across textile
                            laboratories.
                        </p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                            <a href="#features" class="btn btn-primary btn-lg">
                                <em class="icon ni ni-info me-2"></em>System Overview
                            </a>
                            <a href="#contact" class="btn btn-outline-light btn-lg">
                                <em class="icon ni ni-call me-2"></em>Technical Support
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 text-center mb-4 mb-lg-0">
                    <div class="hero-image">
                        <img src="{{ asset('frontAssets/branding.jpeg') }}" alt="Government Textile Testing Laboratory"
                            class="img-fluid rounded-3 shadow-lg">
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
                        <div class="feature-icon">
                            <em class="icon ni ni-activity"></em>
                        </div>
                        <h5 class="fw-bold mb-3">Sample Management</h5>
                        <p>Complete sample lifecycle tracking from registration to disposal with chain of custody
                            documentation and barcode integration.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <em class="icon ni ni-opt-alt"></em>
                        </div>
                        <h5 class="fw-bold mb-3">Test Management</h5>
                        <p>Standardized test protocols for fiber analysis, fabric testing, and performance evaluation
                            with automated workflows as per BIS and international standards.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <em class="icon ni ni-growth"></em>
                        </div>
                        <h5 class="fw-bold mb-3">Quality Assurance</h5>
                        <p>Built-in quality control checks, statistical analysis, and regulatory compliance
                            monitoring for maintaining testing accuracy and certification requirements.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <em class="icon ni ni-file-text"></em>
                        </div>
                        <h5 class="fw-bold mb-3">Report Generation</h5>
                        <p>Automated report generation with standardized templates, digital signatures,
                            and official certification with Textile Committee branding.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <em class="icon ni ni-users"></em>
                        </div>
                        <h5 class="fw-bold mb-3">User Management</h5>
                        <p>Role-based access control with comprehensive audit trails ensuring data security
                            and regulatory compliance across all laboratory personnel.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <em class="icon ni ni-setting"></em>
                        </div>
                        <h5 class="fw-bold mb-3">Equipment Integration</h5>
                        <p>Direct integration with textile testing instruments and automated data capture
                            with approved calibration and validation protocols.</p>
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
                    <p class="lead">Standardized testing workflow from sample receipt to report delivery
                        following Textile Committee guidelines and protocols.</p>
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
                            <p class="text-muted small mb-0">Automatic assignment based on sample type and
                                standardized testing requirements</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <div class="mt-3">
                            <em class="icon ni ni-activity fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Test Execution</h6>
                            <p class="text-muted small mb-0">Guided testing procedures with real-time data capture
                                and validation against standard methods</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <div class="mt-3">
                            <em class="icon ni ni-award fs-1 text-primary mb-3"></em>
                            <h6 class="fw-bold">Report Generation</h6>
                            <p class="text-muted small mb-0">Official report generation with certification
                                and digital authentication</p>
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
                    <p class="lead">LIMS implementation across textile testing laboratories under the
                        Textile Committee network</p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-12">
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="mb-0">Central Textile Laboratory</h6>
                                <small class="text-muted">Mumbai</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="mb-0">Regional Testing Center</h6>
                                <small class="text-muted">Delhi</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h6 class="mb-0">Quality Evaluation Lab</h6>
                                <small class="text-muted">Chennai</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center p-3 bg-light rounded">
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
                <div class="slider" id="imageSlider">
                    <!-- Slides will be dynamically added here -->
                </div>

                <div class="slider-nav">
                    <button class="slider-btn" id="prevBtn">
                        <em class="icon ni ni-chevron-left"></em>
                    </button>
                    <button class="slider-btn" id="nextBtn">
                        <em class="icon ni ni-chevron-right"></em>
                    </button>
                </div>

                <div class="slider-dots" id="sliderDots">
                    <!-- Dots will be dynamically added here -->
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
                    <form class="row g-3" method="POST" action="{{ route('contact_support') }}">
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
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
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
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Logo" class="me-2"
                            style="height: 80px; width: 80px;">
                        <div>
                            <h6 class="mb-0 text-white">Textile Committee</h6>
                            <small class="text-light">LIMS</small>
                        </div>
                    </div>
                    <p class="text-light">Laboratory information management system for
                        textile testing facilities.</p>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3 text-white">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="#features" class="text-light text-decoration-none">Features</a></li>
                        <li><a href="#process" class="text-light text-decoration-none">Process</a></li>
                        <li><a href="#contact" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3 text-white">System Modules</h6>
                    <ul class="list-unstyled text-light">
                        <li>Sample Management</li>
                        <li>Test Management</li>
                        <li>Quality Control</li>
                        <li>Report Generation</li>
                        <li>Equipment Integration</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-5">
                    <h6 class="fw-bold mb-3 text-white">Contact Information</h6>
                    <p class="text-light mb-2">
                        <em class="icon ni ni-map-pin"></em>
                        Mumbai, Maharashtra, India
                    </p>
                    <p class="text-light mb-2">
                        <em class="icon ni ni-mail"></em>
                        lims@textilescommittee.gov.in
                    </p>
                    <p class="text-light">
                        <em class="icon ni ni-call"></em>
                        +91-22-XXXX-XXXX
                    </p>
                </div>
            </div>
            <hr class="mt-4 mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light">© 2025 System Cell. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light me-3">Privacy Policy</a>
                    <a href="#" class="text-light">Terms of Use</a>
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
                    <form method="POST" action="{{ route('user_login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="txt_email" class="form-label">Official Email</label>
                            <input type="email" class="form-control" id="txt_email" name="txt_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="txt_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="txt_password" name="txt_password"
                                required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <em class="icon ni ni-send me-2"></em></i>Access System
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
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

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
                text: "LIMS for"
            },
            {
                text: " Textile Testing",
                class: "text-warning"
            }
        ], 70, 5000);

        // Counting Effect for the stats section
        function animateStats() {
            const counters = document.querySelectorAll(".stat-number");

            counters.forEach(counter => {
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

        // Trigger when section is visible
        const section = document.querySelector(".stats-section");
        let started = false;

        window.addEventListener("scroll", () => {
            const rect = section.getBoundingClientRect();
            if (!started && rect.top < window.innerHeight - 100) {
                started = true;
                animateStats();
            }
        });

        // Image Slider Implementation
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('imageSlider');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dotsContainer = document.getElementById('sliderDots');

            // Sample data for the slider
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

            // Initialize slider
            function initSlider() {
                // Create slides
                slides.forEach((slide, index) => {
                    const slideElement = document.createElement('div');
                    slideElement.className = 'slide';
                    slideElement.innerHTML = `
                        <img src="${slide.image}" alt="${slide.title}">
                        <div class="slide-content">
                            <h3 class="slide-title">${slide.title}</h3>
                            <p class="slide-description">${slide.description}</p>
                        </div>
                    `;
                    slider.appendChild(slideElement);

                    // Create dots
                    const dot = document.createElement('div');
                    dot.className = 'dot';
                    if (index === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => goToSlide(index));
                    dotsContainer.appendChild(dot);
                });

                updateSlider();
            }

            // Update slider position
            function updateSlider() {
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;

                // Update active dot
                document.querySelectorAll('.dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }

            // Go to specific slide
            function goToSlide(index) {
                currentSlide = index;
                updateSlider();
            }

            // Next slide
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                updateSlider();
            }

            // Previous slide
            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                updateSlider();
            }

            // Event listeners
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);

            // Auto slide
            let slideInterval = setInterval(nextSlide, 5000);

            // Pause on hover
            slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
            slider.addEventListener('mouseleave', () => {
                slideInterval = setInterval(nextSlide, 5000);
            });

            // Initialize the slider
            initSlider();
        });
    </script>
</body>

</html>
