<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live QR Code Verification Demo | LIMS 2.0 – Ministry of Textiles</title>
  <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --gold-light: #fef08a;
      --gold-main: #f59e0b;
      --gold-dark: #b45309;
      --gold-glow: rgba(245, 158, 11, 0.5);
      --cyan-main: #06b6d4;
      --cyan-glow: rgba(6, 182, 212, 0.4);
      --green-main: #10b981;
      --green-glow: rgba(16, 185, 129, 0.45);
      --bg-dark: #03050c;
      --glass-card: rgba(15, 23, 42, 0.85);
      --glass-border: rgba(245, 158, 11, 0.4);
      --text-primary: #f8fafc;
      --text-muted: #cbd5e1;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg-dark);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    #bgCanvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 0;
      pointer-events: none;
    }

    /* Ambient Spotlights */
    .spotlight-left, .spotlight-right {
      position: fixed;
      top: -100px;
      width: 500px;
      height: 900px;
      pointer-events: none;
      z-index: 1;
      opacity: 0.35;
      transition: opacity 1.8s ease;
    }
    .spotlight-left {
      left: 0%;
      background: radial-gradient(ellipse at top, rgba(245, 158, 11, 0.6) 0%, transparent 70%);
      transform: rotate(-18deg);
    }
    .spotlight-right {
      right: 0%;
      background: radial-gradient(ellipse at top, rgba(6, 182, 212, 0.6) 0%, transparent 70%);
      transform: rotate(18deg);
    }

    /* HEADER NAV (100% IDENTICAL TO CEREMONY & SIMULATOR) */
    .ceremony-header {
      position: relative;
      z-index: 20;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.9rem 3rem;
      background: rgba(3, 5, 12, 0.92);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--glass-border);
      box-shadow: 0 8px 32px rgba(0,0,0,0.7);
      flex-wrap: wrap;
      gap: 1rem;
    }
    .header-brand {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }
    .header-brand img {
      height: 54px;
      filter: drop-shadow(0 0 16px rgba(245, 158, 11, 0.5));
    }
    .header-brand-info h1 {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, #ffffff 0%, var(--gold-light) 50%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: 0.5px;
    }
    .header-brand-info p {
      font-size: 0.75rem;
      color: var(--gold-main);
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
    }
    .header-controls {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      flex-wrap: wrap;
    }
    .btn-stage-hdr {
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255,255,255,0.15);
      color: #f8fafc;
      padding: 0.5rem 1.1rem;
      border-radius: 40px;
      text-decoration: none;
      font-size: 0.8rem;
      font-weight: 700;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      backdrop-filter: blur(10px);
      letter-spacing: 0.3px;
    }
    .btn-stage-hdr:hover {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000;
      border-color: var(--gold-main);
      box-shadow: 0 0 24px var(--gold-glow);
      transform: translateY(-2px);
    }
    .btn-stage-hdr.active-voice {
      background: rgba(16, 185, 129, 0.25);
      border-color: var(--green-main);
      color: #34d399;
    }

    /* MAIN CONTAINER */
    .qr-container {
      position: relative;
      z-index: 10;
      max-width: 1350px;
      margin: 2rem auto 4rem;
      padding: 0 2rem;
    }

    /* TOP SUMMARY RIBBON */
    .top-summary-ribbon {
      background: linear-gradient(90deg, rgba(7,10,17,0.98), rgba(30,58,138,0.5), rgba(7,10,17,0.98));
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 1.2rem 2.2rem;
      margin-bottom: 2.2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      backdrop-filter: blur(16px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.6), 0 0 30px var(--gold-glow);
      flex-wrap: wrap;
      gap: 1.2rem;
    }
    .ribbon-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .ribbon-left-icon {
      font-size: 2.3rem;
      color: var(--gold-main);
      filter: drop-shadow(0 0 12px var(--gold-glow));
      animation: shieldPulse 2s ease-in-out infinite;
    }
    @keyframes shieldPulse {
      0%, 100% { transform: scale(1); opacity: 0.9; }
      50% { transform: scale(1.12); opacity: 1; filter: drop-shadow(0 0 20px var(--gold-main)); }
    }
    .ribbon-title {
      font-size: 1.25rem;
      font-weight: 800;
      color: #fff;
    }
    .ribbon-sub {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .ribbon-metrics {
      display: flex;
      gap: 1.5rem;
    }
    .ribbon-metric-item {
      text-align: right;
      border-left: 1px solid rgba(255,255,255,0.1);
      padding-left: 1.2rem;
    }
    .ribbon-metric-val {
      font-size: 1.25rem;
      font-weight: 900;
      color: var(--gold-light);
    }
    .ribbon-metric-lbl {
      font-size: 0.72rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* GRID LAYOUT */
    .qr-stage-grid {
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 2.2rem;
      align-items: stretch;
    }

    /* LEFT SCANNER CARD */
    .qr-scanner-card {
      background: var(--glass-card);
      border: 2px solid var(--glass-border);
      border-radius: 24px;
      padding: 2rem 1.8rem;
      backdrop-filter: blur(18px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.8), 0 0 35px var(--gold-glow);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      text-align: center;
      position: relative;
    }

    .scanner-top-bar {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 1.2rem;
    }

    .scanner-top-title {
      font-size: 0.8rem;
      font-weight: 800;
      color: var(--gold-light);
      letter-spacing: 1px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .scanner-live-tag {
      background: rgba(16, 185, 129, 0.2);
      border: 1px solid #10b981;
      color: #34d399;
      padding: 0.2rem 0.6rem;
      border-radius: 20px;
      font-size: 0.68rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .pulse-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #34d399;
      animation: pulseDot 1.5s infinite;
    }
    @keyframes pulseDot {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.5); opacity: 0.4; }
    }

    .qr-viewport-frame {
      position: relative;
      width: 250px;
      height: 250px;
      background: #ffffff;
      border-radius: 24px;
      padding: 18px;
      box-shadow: 0 0 35px var(--gold-glow), inset 0 0 20px rgba(0,0,0,0.2);
      border: 4px solid var(--gold-main);
      margin: 1rem 0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .qr-viewport-frame img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .scan-laser-beam {
      position: absolute;
      top: 18px;
      left: 18px;
      right: 18px;
      height: 4px;
      background: #10b981;
      box-shadow: 0 0 16px #10b981, 0 0 30px #34d399;
      animation: laserScan 2.2s ease-in-out infinite alternate;
      z-index: 5;
    }
    @keyframes laserScan {
      0% { top: 18px; }
      100% { top: 228px; }
    }

    .fast-scan .scan-laser-beam {
      animation: laserFastScan 0.5s ease-in-out infinite alternate !important;
      background: var(--gold-main) !important;
      box-shadow: 0 0 20px var(--gold-main) !important;
    }
    @keyframes laserFastScan {
      0% { top: 18px; }
      100% { top: 228px; }
    }

    .btn-scan-trigger {
      width: 100%;
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000000;
      border: none;
      padding: 0.85rem;
      border-radius: 16px;
      font-weight: 900;
      font-size: 0.95rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      box-shadow: 0 8px 25px var(--gold-glow);
      margin-top: 1rem;
    }
    .btn-scan-trigger:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 12px 35px var(--gold-glow);
      background: linear-gradient(135deg, #ffffff, var(--gold-light));
    }

    .hash-badge-box {
      margin-top: 1.2rem;
      width: 100%;
      background: rgba(0, 0, 0, 0.5);
      border: 1px dashed rgba(255, 255, 255, 0.15);
      border-radius: 14px;
      padding: 0.75rem 1rem;
      font-family: monospace;
      font-size: 0.75rem;
      color: #94a3b8;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .nabl-stamp-pill {
      margin-top: 1rem;
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid var(--gold-main);
      color: var(--gold-light);
      padding: 0.4rem 1rem;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    /* RIGHT VERIFICATION PANEL */
    .verification-panel {
      background: var(--glass-card);
      border: 2px solid var(--glass-border);
      border-radius: 24px;
      padding: 2.5rem;
      backdrop-filter: blur(18px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.85), 0 0 50px var(--gold-glow);
      position: relative;
      animation: panelFadeIn 0.6s ease forwards;
    }
    @keyframes panelFadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .verified-header {
      display: flex;
      align-items: center;
      gap: 1.4rem;
      margin-bottom: 2rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .verified-icon-box {
      width: 75px;
      height: 75px;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(180, 83, 9, 0.9));
      border: 3px solid var(--gold-main);
      color: var(--gold-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      box-shadow: 0 0 30px var(--gold-glow);
      animation: pulseShieldIcon 2s ease-in-out infinite;
      flex-shrink: 0;
    }
    @keyframes pulseShieldIcon {
      0%, 100% { transform: scale(1); box-shadow: 0 0 20px var(--gold-glow); }
      50% { transform: scale(1.08); box-shadow: 0 0 38px var(--gold-main); }
    }

    .verified-title-group h2 {
      font-family: 'Cinzel', serif;
      font-size: 1.9rem;
      color: var(--gold-light);
      font-weight: 900;
      margin-bottom: 0.2rem;
      letter-spacing: 0.5px;
      line-height: 1.2;
    }

    .verified-title-group p {
      font-size: 0.92rem;
      color: #cbd5e1;
      font-weight: 500;
    }

    .verified-timestamp-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid #10b981;
      color: #34d399;
      padding: 0.25rem 0.8rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 800;
      margin-top: 0.4rem;
    }

    /* 4 METRICS CARDS */
    .verification-metrics-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .vm-card {
      background: rgba(30, 41, 59, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 0.9rem;
      text-align: center;
      transition: all 0.3s ease;
    }
    .vm-card:hover {
      border-color: var(--gold-main);
      transform: translateY(-3px);
    }
    .vm-card-val {
      font-size: 1.15rem;
      font-weight: 900;
      color: #ffffff;
      margin-bottom: 0.2rem;
    }
    .vm-card-lbl {
      font-size: 0.72rem;
      color: #94a3b8;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* METADATA GRID */
    .report-meta-card {
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .meta-card-title {
      font-size: 0.82rem;
      font-weight: 800;
      color: var(--gold-light);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 1.1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .meta-grid-2col {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.2rem;
    }

    .meta-item-box strong {
      display: block;
      font-size: 0.74rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.2rem;
    }

    .meta-item-box span {
      font-size: 0.95rem;
      color: #ffffff;
      font-weight: 700;
    }

    /* PARAMETERS TABLE */
    .table-section-title {
      font-size: 0.9rem;
      font-weight: 800;
      color: #ffffff;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .test-table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(15, 23, 42, 0.6);
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      margin-bottom: 2rem;
    }

    .test-table th {
      text-align: left;
      padding: 0.9rem 1.1rem;
      font-size: 0.78rem;
      color: var(--gold-light);
      text-transform: uppercase;
      background: rgba(30, 41, 59, 0.9);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      font-weight: 800;
    }

    .test-table td {
      padding: 0.95rem 1.1rem;
      font-size: 0.88rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .badge-pass {
      background: rgba(16, 185, 129, 0.2);
      color: #34d399;
      border: 1px solid #10b981;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    /* DIGITAL SEAL CARD */
    .esign-seal-card {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(15, 23, 42, 0.9));
      border: 2px dashed var(--gold-main);
      border-radius: 20px;
      padding: 1.4rem 1.8rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1.2rem;
    }

    .esign-seal-info {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .esign-seal-icon {
      font-size: 2.8rem;
      color: var(--gold-main);
      filter: drop-shadow(0 0 10px var(--gold-glow));
    }

    .esign-seal-text h4 {
      font-size: 1.05rem;
      font-weight: 900;
      color: #ffffff;
      margin-bottom: 0.2rem;
    }

    .esign-seal-text p {
      font-size: 0.8rem;
      color: #94a3b8;
    }

    .btn-download-cert {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000;
      border: none;
      padding: 0.75rem 1.6rem;
      border-radius: 40px;
      font-size: 0.88rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }
    .btn-download-cert:hover {
      box-shadow: 0 0 25px var(--gold-glow);
      color: #000;
      transform: translateY(-3px);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .qr-stage-grid { grid-template-columns: 1fr; }
      .verification-metrics-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
      .ceremony-header { padding: 0.8rem 1.2rem; flex-direction: column; align-items: stretch; }
      .header-brand { flex-wrap: wrap; }
      .header-controls { justify-content: center; }
      .meta-grid-2col { grid-template-columns: 1fr; }
      .verification-metrics-row { grid-template-columns: 1fr; }
      .esign-seal-card { flex-direction: column; text-align: center; justify-content: center; }
      .esign-seal-info { flex-direction: column; }
    }
  </style>
</head>
<body>

  <!-- Spotlights -->
  <div class="spotlight-left"></div>
  <div class="spotlight-right"></div>

  <!-- Particle Canvas -->
  <canvas id="bgCanvas"></canvas>

  <!-- HEADER (100% SYNCHRONIZED WITH CEREMONY & SIMULATOR) -->
  <header class="ceremony-header">
    <div class="header-brand">
      <img src="{{ asset('frontAssets/logo_lg.png') }}" alt="Textiles Committee Logo">
      <div class="header-brand-info">
        <h1>TEXTILES COMMITTEE · LIMS 2.0</h1>
        <p>Ministry of Textiles, Govt. of India</p>
      </div>
    </div>
    <div class="header-controls">
      <button class="btn-stage-hdr" id="btnVoiceToggle" onclick="toggleVoice()"><i class="bi bi-megaphone-fill"></i> Voice Narration: ON</button>
      <a href="{{ route('launch.map') }}" class="btn-stage-hdr"><i class="bi bi-map-fill"></i> Pan-India Map</a>
      <a href="{{ route('launch.simulator') }}" class="btn-stage-hdr"><i class="bi bi-play-circle-fill"></i> 60-Sec Walkthrough</a>
      <a href="{{ route('launch.ceremony') }}" class="btn-stage-hdr"><i class="bi bi-house-door-fill"></i> Inauguration Stage</a>
      <a href="{{ url('/') }}" class="btn-stage-hdr"><i class="bi bi-house-fill"></i> Main Portal</a>
    </div>
  </header>

  <!-- MAIN CONTAINER -->
  <main class="qr-container">

    <!-- TOP SUMMARY RIBBON -->
    <div class="top-summary-ribbon">
      <div class="ribbon-left">
        <i class="bi bi-shield-check ribbon-left-icon"></i>
        <div>
          <div class="ribbon-title">Global Anti-Counterfeit QR Verification Portal</div>
          <div class="ribbon-sub">Instant 1-second public report validation backed by NABL ISO/IEC 17025 cryptographic seals.</div>
        </div>
      </div>
      <div class="ribbon-metrics">
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">0.18s</div>
          <div class="ribbon-metric-lbl">Verify Speed</div>
        </div>
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">SHA-256</div>
          <div class="ribbon-metric-lbl">Security Hash</div>
        </div>
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">NABL ISO</div>
          <div class="ribbon-metric-lbl">17025 Certified</div>
        </div>
      </div>
    </div>

    <!-- MAIN STAGE GRID -->
    <div class="qr-stage-grid">

      <!-- LEFT SCANNER CARD -->
      <div class="qr-scanner-card">
        <div class="scanner-top-bar">
          <span class="scanner-top-title"><i class="bi bi-camera-video-fill"></i> QR Scanner Feed</span>
          <span class="scanner-live-tag"><div class="pulse-dot"></div> LIVE SCANNER</span>
        </div>

        <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 0.5rem;">
          Simulating live camera QR scan of official report card on stage:
        </p>

        <div class="qr-viewport-frame" id="qrFrame">
          <div class="scan-laser-beam"></div>
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=https://lims.textilescommittee.gov.in/verify/TC-MUM-2026-9482" alt="Sample QR Code">
        </div>

        <button class="btn-scan-trigger" onclick="triggerLiveScan()">
          <i class="bi bi-qr-code-scan"></i> SIMULATE LIVE QR SCAN ⚡
        </button>

        <div class="hash-badge-box">
          <span><i class="bi bi-hash"></i> HASH: {{ $sample['hash'] }}</span>
          <i class="bi bi-copy" style="cursor: pointer;" onclick="copyHash()" title="Copy Hash"></i>
        </div>

        <div class="nabl-stamp-pill">
          <i class="bi bi-award-fill"></i> ISO/IEC 17025 ACCREDITED LAB
        </div>
      </div>

      <!-- RIGHT VERIFICATION PANEL -->
      <div class="verification-panel">

        <!-- HEADER VERIFIED BANNER -->
        <div class="verified-header">
          <div class="verified-icon-box">
            <i class="bi bi-shield-fill-check"></i>
          </div>
          <div class="verified-title-group">
            <h2>AUTHENTIC TEST REPORT VERIFIED</h2>
            <p>Digitally Authenticated by Textiles Committee LIMS Cryptographic Engine</p>
            <div class="verified-timestamp-tag">
              <i class="bi bi-clock-history"></i> Issued: {{ $sample['issued_at'] }}
            </div>
          </div>
        </div>

        <!-- 4 VERIFICATION METRICS -->
        <div class="verification-metrics-row">
          <div class="vm-card">
            <div class="vm-card-val" style="color: #34d399;">PASSED</div>
            <div class="vm-card-lbl">Overall Result</div>
          </div>
          <div class="vm-card">
            <div class="vm-card-val" style="color: var(--gold-light);">ISO 17025</div>
            <div class="vm-card-lbl">Accreditation</div>
          </div>
          <div class="vm-card">
            <div class="vm-card-val" style="color: #38bdf8;">0.18s</div>
            <div class="vm-card-lbl">Scan Time</div>
          </div>
          <div class="vm-card">
            <div class="vm-card-val" style="color: #a78bfa;">PKI e-Signed</div>
            <div class="vm-card-lbl">Signature</div>
          </div>
        </div>

        <!-- REPORT METADATA -->
        <div class="report-meta-card">
          <div class="meta-card-title"><i class="bi bi-file-earmark-text-fill"></i> Official Report Metadata</div>
          <div class="meta-grid-2col">
            <div class="meta-item-box">
              <strong>Test Report Number</strong>
              <span>{{ $sample['report_no'] }}</span>
            </div>
            <div class="meta-item-box">
              <strong>Certificate ID</strong>
              <span>{{ $sample['certificate_id'] }}</span>
            </div>
            <div class="meta-item-box">
              <strong>Sample Description</strong>
              <span>{{ $sample['sample_name'] }}</span>
            </div>
            <div class="meta-item-box">
              <strong>Applicant / Exporter</strong>
              <span>{{ $sample['applicant'] }}</span>
            </div>
            <div class="meta-item-box">
              <strong>Testing Laboratory</strong>
              <span>{{ $sample['lab_location'] }}</span>
            </div>
            <div class="meta-item-box">
              <strong>Date of Testing</strong>
              <span>{{ $sample['date_tested'] }}</span>
            </div>
          </div>
        </div>

        <!-- PARAMETERS TABLE -->
        <div class="table-section-title">
          <span><i class="bi bi-card-checklist" style="color: #34d399;"></i> Certified Laboratory Parameter Results</span>
          <span style="font-size: 0.75rem; color: #34d399;"><i class="bi bi-check-circle-fill"></i> 5 Parameters Passed</span>
        </div>

        <table class="test-table">
          <thead>
            <tr>
              <th>Parameter Tested</th>
              <th>Observed Value</th>
              <th>Test Standard</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sample['tests'] as $t)
              <tr>
                <td><strong style="color: #ffffff;">{{ $t['name'] }}</strong></td>
                <td style="color: #7dd3fc; font-weight: 700;">{{ $t['result'] }}</td>
                <td style="color: #94a3b8;">{{ $t['standard'] }}</td>
                <td><span class="badge-pass"><i class="bi bi-check-circle-fill"></i> {{ $t['status'] }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <!-- DIGITAL SEAL CARD -->
        <div class="esign-seal-card">
          <div class="esign-seal-info">
            <i class="bi bi-patch-check-fill esign-seal-icon"></i>
            <div class="esign-seal-text">
              <h4>{{ $sample['signatory'] }}</h4>
              <p>{{ $sample['signatory_title'] }}</p>
            </div>
          </div>
          <button class="btn-download-cert" onclick="downloadPdfSim()">
            <i class="bi bi-file-earmark-pdf-fill"></i> Download Official PDF Report
          </button>
        </div>

      </div>

    </div>

  </main>

  <script>
    let voiceEnabled = true;
    let audioCtx = null;

    function initAudio() {
      if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      }
      if (audioCtx.state === 'suspended') audioCtx.resume();
    }

    function playBeepSound() {
      initAudio();
      const now = audioCtx.currentTime;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(1200, now);
      gain.gain.setValueAtTime(0.4, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start(now);
      osc.stop(now + 0.2);
    }

    function playSuccessChime() {
      initAudio();
      const now = audioCtx.currentTime;
      const notes = [523.25, 659.25, 783.99, 1046.50];
      notes.forEach((freq, i) => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, now + i * 0.08);
        gain.gain.setValueAtTime(0.3, now + i * 0.08);
        gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.08 + 0.4);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now + i * 0.08);
        osc.stop(now + i * 0.08 + 0.4);
      });
    }

    function triggerLiveScan() {
      const qrFrame = document.getElementById('qrFrame');
      qrFrame.classList.add('fast-scan');
      playBeepSound();

      setTimeout(() => {
        qrFrame.classList.remove('fast-scan');
        playSuccessChime();

        confetti({
          particleCount: 90,
          spread: 70,
          origin: { y: 0.5 },
          colors: ['#f59e0b', '#34d399', '#ffffff', '#06b6d4']
        });

        speakVerification();
      }, 1200);
    }

    function speakVerification() {
      if (!voiceEnabled || !('speechSynthesis' in window)) return;
      window.speechSynthesis.cancel();

      const text = "Authentic NABL test report verified for Bharat Textiles & Exports Pvt Ltd. All 5 parameters passed.";
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.rate = 1.0;
      utterance.pitch = 1.0;

      const voices = window.speechSynthesis.getVoices();
      const preferred = voices.find(v => v.lang.includes('en-IN') || v.lang.includes('en-GB') || v.lang.includes('en'));
      if (preferred) utterance.voice = preferred;

      window.speechSynthesis.speak(utterance);
    }

    function toggleVoice() {
      voiceEnabled = !voiceEnabled;
      const btn = document.getElementById('btnVoiceToggle');
      if (voiceEnabled) {
        btn.innerHTML = `<i class="bi bi-megaphone-fill"></i> Voice Narration: ON`;
        btn.classList.add('active-voice');
        speakVerification();
      } else {
        window.speechSynthesis.cancel();
        btn.innerHTML = `<i class="bi bi-megaphone-mute-fill"></i> Voice Narration: OFF`;
        btn.classList.remove('active-voice');
      }
    }

    function copyHash() {
      navigator.clipboard.writeText("{{ $sample['hash'] }}");
      alert("Cryptographic Report Hash copied to clipboard!");
    }

    function downloadPdfSim() {
      alert("Simulating secure download of NABL Accredited Test Certificate PDF...");
    }

    // Initial load voice synthesis
    setTimeout(() => {
      speakVerification();
    }, 1000);

    // Particle Canvas (100% Synchronized with Ceremony & Simulator Engine)
    const canvas = document.getElementById('bgCanvas');
    const ctx = canvas.getContext('2d');
    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    const particles = [];
    const count = 80;
    for (let i=0; i<count; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random()-0.5)*0.5,
        vy: (Math.random()-0.5)*0.5,
        r: Math.random()*2 + 1.2
      });
    }

    function drawParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = 'rgba(245, 158, 11, 0.5)';
      ctx.strokeStyle = 'rgba(6, 182, 212, 0.15)';
      ctx.lineWidth = 0.8;

      for (let i=0; i<count; i++) {
        const p = particles[i];
        p.x += p.vx;
        p.y += p.vy;
        if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
        ctx.fill();

        for (let j=i+1; j<count; j++) {
          const p2 = particles[j];
          const dist = Math.hypot(p.x-p2.x, p.y-p2.y);
          if (dist < 140) {
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            ctx.lineTo(p2.x, p2.y);
            ctx.stroke();
          }
        }
      }
      requestAnimationFrame(drawParticles);
    }
    drawParticles();
  </script>
</body>
</html>
