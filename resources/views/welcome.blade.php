<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIMS - Laboratory Information Management System | Textile Committee of India</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --textile-red: #c41e3a;
            --textile-blue: #1d4ed8;
            --textile-gold: #d4af37;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
        }

        .navbar {
            background: linear-gradient(135deg, var(--textile-red) 0%, #a91731 100%);
            box-shadow: 0 2px 15px rgba(196, 30, 58, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(196, 30, 58, 0.8)), url('{{ asset('frontAssets/header-image.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="textile" patternUnits="userSpaceOnUse" width="20" height="20"><rect fill="none" stroke="%23ffffff" stroke-width="0.2" width="20" height="20"/><circle cx="10" cy="10" r="2" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23textile)"/></svg>');
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
            padding: 5rem 0;
            position: relative;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
            padding: 2rem;
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
            background-image: url('https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
            border: 2px solid var(--medium-gray);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
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

        .textile-pattern {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f8fafc"/><path d="M0 0L100 100" stroke="%23e2e8f0" stroke-width="1"/><path d="M100 0L0 100" stroke="%23e2e8f0" stroke-width="1"/></svg>');
            background-size: 20px 20px;
        }

        .testimonial-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin: 1rem 0;
            border-left: 4px solid var(--textile-red);
        }

        .client-logo {
            height: 60px;
            width: auto;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .client-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .textile-bg {
            background: url('{{ asset('frontAssets/branding.jpg') }}');
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

        .textile-icon {
            color: var(--textile-red);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                min-height: 70vh;
                text-align: center;
            }

            .stat-number {
                font-size: 2rem;
            }

            .feature-card,
            .process-step {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top p-0">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('frontAssets/logo.png') }}" alt="Textile Committee of India"
                    class="me-3 rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                    <div class="fw-bold">Textile Committee of India</div>
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
                        <a class="nav-link" href="#clients">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="#" class="btn btn-outline-light" data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
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
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4">
                            Advanced <span class="text-warning">LIMS</span> for Textile Testing
                        </h1>
                        <p class="lead mb-4">
                            Empowering textile laboratories with a comprehensive Laboratory Information Management
                            System
                            developed by the Textile Committee of India. Manage samples, track testing processes, and
                            generate
                            reports with government-approved efficiency and accuracy.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#features" class="btn btn-primary btn-lg">
                                <i class="fas fa-rocket me-2"></i>Explore Features
                            </a>
                            <a href="#contact" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Get Started
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image mt-5 mt-lg-0 floating-animation">
                        <img src="{{ asset('frontAssets/branding.jpg') }}" alt="Textile Testing Laboratory"
                            class="img-fluid rounded-3 shadow-lg" style="max-width: 500px;">
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
                        <span class="stat-number">50+</span>
                        <p class="mb-0">Government Labs</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">25K+</span>
                        <p class="mb-0">Tests Processed</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">75+</span>
                        <p class="mb-0">Test Methods</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <p class="mb-0">Accuracy</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 textile-bg" id="features">
        <div class="container py-5">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Comprehensive LIMS Features</h2>
                    <p class="lead">Our Laboratory Information Management System is specifically designed for textile
                        testing facilities, offering complete sample lifecycle management with government-approved
                        technology.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-vials"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Sample Management</h4>
                        <p>Track samples from registration to disposal with complete chain of custody documentation and
                            barcode integration for seamless workflow management as per government standards.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Test Management</h4>
                        <p>Comprehensive test protocols for fiber analysis, fabric testing, colorfastness, and
                            performance testing with automated workflows and real-time monitoring as per BIS standards.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Quality Control</h4>
                        <p>Built-in QC checks, control charts, and statistical analysis for maintaining testing accuracy
                            and regulatory compliance with international and national standards.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Report Generation</h4>
                        <p>Automated report generation with customizable templates, digital signatures, and multi-format
                            export capabilities for professional documentation with government branding.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h4 class="fw-bold mb-3">User Management</h4>
                        <p>Role-based access control with comprehensive audit trails, ensuring data security and
                            regulatory compliance across all user levels in government testing facilities.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Instrument Integration</h4>
                        <p>Seamless integration with textile testing instruments and automated data capture from various
                            equipment manufacturers with government-approved calibration protocols.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 textile-pattern" id="process">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">How LIMS Works</h2>
                    <p class="lead">Streamlined workflow from sample receipt to report delivery with complete
                        automation and quality assurance as per Textile Committee guidelines</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">1</div>
                        <div class="mt-3">
                            <i class="fas fa-plus-circle fa-2x textile-icon"></i>
                            <h5 class="fw-bold">Sample Registration</h5>
                            <p class="text-muted small">Register samples with unique IDs, customer information, and
                                test requirements following government protocols</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">2</div>
                        <div class="mt-3">
                            <i class="fas fa-tasks fa-2x textile-icon"></i>
                            <h5 class="fw-bold">Test Planning</h5>
                            <p class="text-muted small">Automatic test assignment based on sample type and standardized
                                testing requirements</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <div class="mt-3">
                            <i class="fas fa-play-circle fa-2x textile-icon"></i>
                            <h5 class="fw-bold">Test Execution</h5>
                            <p class="text-muted small">Guided testing procedures with real-time data capture and
                                validation against standardized methods</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <div class="mt-3">
                            <i class="fas fa-file-download fa-2x textile-icon"></i>
                            <h5 class="fw-bold">Report Delivery</h5>
                            <p class="text-muted small">Automated report generation and delivery with official
                                government certification and digital signatures</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section class="py-5 bg-light" id="clients">
        <div class="container py-5">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Trusted by Government Textile Laboratories</h2>
                    <p class="lead">Our LIMS solution is implemented across textile testing laboratories under the
                        Textile Committee of India</p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-5 align-items-center">
                        <div class="text-center">
                            <div class="bg-danger p-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                                style="height: 100px; width: 200px;">
                                <h5 class="m-0 text-white">Central Textile Laboratory</h5>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-warning p-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                                style="height: 100px; width: 200px;">
                                <h5 class="m-0 text-white">Regional Testing Center</h5>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary p-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                                style="height: 100px; width: 200px;">
                                <h5 class="m-0 text-white">Quality Evaluation Lab</h5>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-success p-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                                style="height: 100px; width: 200px;">
                                <h5 class="m-0 text-white">Fiber Testing Division</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"
                                    class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Dr. Rajesh Kumar</h6>
                                <small class="text-muted">Director, Central Textile Laboratory</small>
                            </div>
                        </div>
                        <p class="mb-0">"The LIMS has transformed our lab operations. We've reduced reporting time by
                            60% and improved data accuracy significantly while maintaining all government protocols."
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                                    class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Dr. Priya Sharma</h6>
                                <small class="text-muted">Senior Scientist, Textile Committee</small>
                            </div>
                        </div>
                        <p class="mb-0">"Instrument integration capabilities have eliminated manual data entry errors
                            and saved our technicians hours of work each day while ensuring data integrity."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="User"
                                    class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Mr. Vikram Singh</h6>
                                <small class="text-muted">Quality Manager, Regional Testing Center</small>
                            </div>
                        </div>
                        <p class="mb-0">"The customizable reporting feature allows us to meet specific ministry
                            requirements while maintaining standardization across all our testing centers."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Connect with Us</h2>
                    <p class="lead">Contact us if you have any querry or Suggestions Regarding LIMS.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="organization" class="form-label">Organization</label>
                            <input type="text" class="form-control" id="organization" name="organization">
                        </div>
                        <div class="col-md-6">
                            <label for="labType" class="form-label">Laboratory Type</label>
                            <select class="form-control" id="labType" name="lab_type">
                                <option selected>Choose...</option>
                                <option value="textile_testing">Textile Testing Lab</option>
                                <option value="fiber_analysis">Fiber Analysis Lab</option>
                                <option value="quality_control">Quality Control Lab</option>
                                <option value="research">Research Lab</option>
                                <option value="other">Other Government Lab</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4"
                                placeholder="Tell us about your laboratory requirements..."></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
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
                        <img src="{{ asset('frontAssets/logo.png') }}" alt="Logo" class="me-3 rounded-circle"
                            style="height: 40px; width: 40px; object-fit: cover; background: #fff; padding: 3px;">
                        <div>
                            <h5 class="mb-0">Textile Committee of India</h5>
                            <small class="text-light">LIMS Division</small>
                        </div>
                    </div>

                    <p class="text-light">Empowering textile laboratories with cutting-edge information management
                        solutions under the Ministry of Textiles, Government of India.</p>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="#features" class="text-light text-decoration-none">Features</a></li>
                        <li><a href="#process" class="text-light text-decoration-none">Process</a></li>
                        <li><a href="#contact" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="text-light">Sample Management</li>
                        <li class="text-light">Test Management</li>
                        <li class="text-light">Quality Control</li>
                        <li class="text-light">Report Generation</li>
                        <li class="text-light">Instrument Integration</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-5">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <p class="text-light mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Mumbai, Maharashtra, India
                    </p>
                    <p class="text-light mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        lims@textilescommittee.gov.in
                    </p>
                    <p class="text-light">
                        <i class="fas fa-phone me-2"></i>
                        +91-22-XXXX-XXXX
                    </p>
                </div>
            </div>
            <hr class="mt-4 mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light">© {{ date('Y') }} Textile Committee of India. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light me-3">Privacy Policy</a>
                    <a href="#" class="text-light">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Login to LIMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" action="{{ route('user_login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="txt_email" class="form-label">Email Address</label>
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
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Forgot Password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
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

        // Contact form handling
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
            alert('Thank you for your interest! We will contact you soon.');
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'linear-gradient(135deg, #a91731 0%, #8b0000 100%)';
            } else {
                navbar.style.background = 'linear-gradient(135deg, var(--primary-color) 0%, #a91731 100%)';
            }
        });

        // Counter animation for stats
        function animateCounter(element, target) {
            let current = 0;
            const originalText = element.textContent;
            const suffix = originalText.includes('+') ? '+' : originalText.includes('%') ? '%' : '';
            const increment = target / 100;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + suffix;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + suffix;
                }
            }, 20);
        }

        // Trigger counter animation when stats section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = document.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        let target;
                        const text = stat.textContent;
                        if (text.includes('K')) {
                            target = parseInt(text.replace(/\D/g, '')) * 1000;
                        } else if (text.includes('.')) {
                            target = parseFloat(text.replace(/[^\d.]/g, ''));
                        } else {
                            target = parseInt(text.replace(/\D/g, ''));
                        }
                        animateCounter(stat, target);
                    });
                    observer.unobserve(entry.target);
                }
            });
        });

        if (document.querySelector('.stats-section')) {
            observer.observe(document.querySelector('.stats-section'));
        }

        // Add loading animation to cards
        const cards = document.querySelectorAll('.feature-card, .process-step');
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            cardObserver.observe(card);
        });

        // Enhanced form validation and submission
        const contactForm = document.querySelector('#contact form');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Basic validation
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (isValid) {
                    // Simulate form submission
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                    submitBtn.disabled = true;

                    setTimeout(() => {
                        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Message Sent!';
                        submitBtn.classList.remove('btn-primary');
                        submitBtn.classList.add('btn-success');

                        setTimeout(() => {
                            this.reset();
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-success');
                            submitBtn.classList.add('btn-primary');
                        }, 2000);
                    }, 1500);
                }
            });
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Add parallax effect to hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const navbar = document.querySelector('.navbar');
            const heroSection = document.querySelector('.hero-section');

            // Navbar background change
            if (scrolled > 50) {
                navbar.style.background =
                    'linear-gradient(135deg, var(--primary-light) 0%, var(--accent-color) 100%)';
                navbar.style.backdropFilter = 'blur(15px)';
            } else {
                navbar.style.background =
                    'linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%)';
                navbar.style.backdropFilter = 'blur(10px)';
            }

            // Parallax effect for hero
            if (heroSection) {
                const rate = scrolled * -0.5;
                heroSection.style.transform = `translateY(${rate}px)`;
            }
        });

        // Add typing effect to hero title
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';

            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Initialize typing effect when page loads
        window.addEventListener('load', function() {
            const heroTitle = document.querySelector('.hero-content h1');
            if (heroTitle) {
                const originalText = heroTitle.textContent;
                setTimeout(() => {
                    typeWriter(heroTitle, originalText, 50);
                }, 500);
            }
        });

        // Add hover effects to navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.3s ease';
            });

            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add floating animation to feature icons
        const featureIcons = document.querySelectorAll('.feature-icon');
        featureIcons.forEach((icon, index) => {
            icon.style.animation = `float 3s ease-in-out infinite ${index * 0.5}s`;
        });

        // CSS for float animation
        const floatKeyframes = `
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
        `;
        const style = document.createElement('style');
        style.textContent = floatKeyframes;
        document.head.appendChild(style);
    </script>
</body>

</html>
