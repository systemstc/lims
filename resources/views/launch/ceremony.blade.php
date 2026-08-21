<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>National Digital Launch | LIMS 2.0 – Ministry of Textiles</title>
  <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Cinzel:wght@600;700;800;900&display=swap" rel="stylesheet">
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
      --cyan-glow: rgba(6, 182, 212, 0.4);
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

    /* --------------------------------------------------------------------------
       REALISTIC 3D THEATER VELVET CURTAIN OVERLAY
    -------------------------------------------------------------------------- */
    .curtain-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      pointer-events: none;
      display: flex;
      overflow: hidden;
    }

    .curtain-panel {
      width: 50vw;
      height: 100vh;
      background: 
        repeating-linear-gradient(
          90deg,
          #450a0a 0px,
          #991b1b 30px,
          #7f1d1d 60px,
          #b91c1c 90px,
          #450a0a 120px
        );
      box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.95), 0 0 80px rgba(245, 158, 11, 0.6);
      transition: transform 1.0s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .curtain-panel::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 45px;
      background: linear-gradient(180deg, #f59e0b 0%, #b45309 80%, transparent 100%);
      border-bottom: 4px dotted #fef08a;
      box-shadow: 0 5px 15px rgba(0,0,0,0.6);
      z-index: 10;
    }

    .curtain-panel-left {
      transform: translateX(-100%);
      border-right: 4px solid var(--gold-main);
    }
    .curtain-panel-right {
      transform: translateX(100%);
      border-left: 4px solid var(--gold-main);
    }

    .curtain-overlay.closed .curtain-panel-left { transform: translateX(0); }
    .curtain-overlay.closed .curtain-panel-right { transform: translateX(0); }

    .curtain-overlay.unveiling .curtain-panel {
      transition: transform 2.0s cubic-bezier(0.65, 0, 0.35, 1);
    }
    .curtain-overlay.unveiling .curtain-panel-left { transform: translateX(-100%); }
    .curtain-overlay.unveiling .curtain-panel-right { transform: translateX(100%); }

    /* --------------------------------------------------------------------------
       AMAZING CINEMATIC 3D COUNTDOWN DISC EMBLEM
    -------------------------------------------------------------------------- */
    .curtain-timer-banner {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10000;
      pointer-events: none;
      display: none;
      text-align: center;
    }

    .countdown-disc-container {
      position: relative;
      width: 280px;
      height: 280px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }

    .countdown-disc-glass {
      width: 210px;
      height: 210px;
      border-radius: 50%;
      background: radial-gradient(circle at 35% 35%, rgba(30, 58, 138, 0.95) 0%, rgba(15, 23, 42, 0.98) 70%, rgba(3, 5, 12, 1) 100%);
      border: 4px solid var(--gold-main);
      box-shadow: 0 0 60px var(--gold-glow), inset 0 0 40px rgba(245, 158, 11, 0.4);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      backdrop-filter: blur(16px);
      z-index: 5;
    }

    .countdown-ring-1 {
      position: absolute;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      border: 2px dashed var(--gold-main);
      animation: rotateClockwise 14s linear infinite;
    }
    .countdown-ring-2 {
      position: absolute;
      width: 230px;
      height: 230px;
      border-radius: 50%;
      border: 2px solid var(--cyan-glow);
      border-top-color: var(--gold-light);
      animation: rotateCounter 8s linear infinite;
    }

    .curtain-timer-header {
      color: var(--gold-main);
      font-weight: 800;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-size: 0.75rem;
      margin-bottom: 0.2rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .curtain-timer-number {
      font-family: 'Cinzel', serif;
      font-size: 6.5rem;
      font-weight: 900;
      color: var(--gold-light);
      line-height: 1;
      text-shadow: 0 0 30px var(--gold-main), 0 0 60px rgba(245, 158, 11, 0.9);
      transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .curtain-timer-number.pop-animate {
      animation: popZoom 0.5s ease-out;
    }

    @keyframes popZoom {
      0% { transform: scale(1.6); opacity: 0.3; filter: drop-shadow(0 0 40px var(--gold-light)); }
      50% { transform: scale(1.1); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* --------------------------------------------------------------------------
       SLEEK GLASSMORPHIC PAN-INDIA DIGITAL LAB NETWORK WIDGET (LEFT SIDE)
    -------------------------------------------------------------------------- */
    .lab-radar-widget {
      position: absolute;
      top: 52%;
      left: 3%;
      transform: translateY(-50%);
      width: 320px;
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 1.5rem;
      z-index: 2;
      backdrop-filter: blur(18px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.6), 0 0 30px var(--gold-glow);
      transition: opacity 1s ease;
    }

    .radar-widget-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.2rem;
      border-bottom: 1px solid rgba(245, 158, 11, 0.2);
      padding-bottom: 0.8rem;
    }

    .radar-widget-title {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--gold-light);
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .radar-widget-badge {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid #10b981;
      color: #34d399;
      padding: 0.25rem 0.6rem;
      border-radius: 30px;
      font-size: 0.7rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .radar-scan-box {
      position: relative;
      width: 100px;
      height: 100px;
      margin: 0.5rem auto 1.2rem;
      border-radius: 50%;
      border: 2px solid var(--cyan-glow);
      background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, rgba(3, 5, 12, 0.8) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 0 20px rgba(6, 182, 212, 0.2);
    }

    .radar-sweep-hand {
      position: absolute;
      width: 50%;
      height: 50%;
      top: 0;
      left: 50%;
      transform-origin: bottom left;
      background: linear-gradient(45deg, rgba(6, 182, 212, 0.6), transparent);
      animation: radarRotate 4s linear infinite;
      border-radius: 100% 0 0 0;
    }

    @keyframes radarRotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .radar-center-icon {
      font-size: 2.2rem;
      color: var(--gold-main);
      z-index: 2;
      filter: drop-shadow(0 0 10px var(--gold-glow));
    }

    .radar-lab-list {
      display: flex;
      flex-direction: column;
      gap: 0.55rem;
    }

    .radar-lab-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(30, 41, 59, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.08);
      padding: 0.5rem 0.8rem;
      border-radius: 12px;
      font-size: 0.8rem;
    }

    .radar-lab-name {
      font-weight: 700;
      color: #ffffff;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .radar-lab-name i {
      color: var(--gold-main);
    }

    .radar-lab-status {
      font-weight: 800;
      color: #34d399;
      font-size: 0.72rem;
    }

    .btn-radar-link {
      margin-top: 1rem;
      width: 100%;
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000;
      border: none;
      padding: 0.6rem;
      border-radius: 12px;
      font-weight: 800;
      font-size: 0.8rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
    }

    .btn-radar-link:hover {
      box-shadow: 0 0 20px var(--gold-glow);
      color: #000;
      transform: translateY(-2px);
    }

    .spotlight-left, .spotlight-right {
      position: fixed;
      top: -100px;
      width: 500px;
      height: 900px;
      pointer-events: none;
      z-index: 1;
      opacity: 0.3;
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

    /* HEADER */
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
    .btn-reset-stage {
      background: rgba(225, 29, 72, 0.25);
      border-color: #f43f5e;
    }
    .btn-reset-stage:hover {
      background: #f43f5e;
      color: #fff;
      box-shadow: 0 0 24px rgba(244, 63, 94, 0.6);
    }

    /* COUNTDOWN */
    .countdown-strip {
      position: relative;
      z-index: 10;
      background: linear-gradient(90deg, rgba(7,10,17,0.98), rgba(30,58,138,0.5), rgba(7,10,17,0.98));
      border-bottom: 1px solid rgba(245, 158, 11, 0.3);
      padding: 0.7rem 1.5rem;
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 2rem;
      font-size: 0.95rem;
      flex-wrap: wrap;
    }
    .timer-box {
      display: flex;
      gap: 0.8rem;
    }
    .timer-unit {
      background: rgba(0,0,0,0.7);
      border: 1px solid var(--gold-main);
      padding: 0.3rem 0.9rem;
      border-radius: 8px;
      font-weight: 800;
      color: var(--gold-light);
      font-family: monospace;
      font-size: 1.1rem;
      box-shadow: 0 0 16px rgba(245, 158, 11, 0.25);
      backdrop-filter: blur(4px);
    }

    /* STAGE MAIN */
    .stage-main {
      position: relative;
      z-index: 10;
      max-width: 1280px;
      margin: 2rem auto 4rem;
      padding: 0 2rem;
      text-align: center;
    }

    .vip-crown-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(6, 182, 212, 0.15));
      border: 1px solid var(--gold-main);
      color: var(--gold-light);
      padding: 0.6rem 2.2rem;
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 800;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 1.8rem;
      box-shadow: 0 0 30px var(--gold-glow);
      backdrop-filter: blur(6px);
    }
    .vip-crown-badge i {
      font-size: 1.3rem;
      color: var(--gold-light);
    }

    .hero-title {
      font-family: 'Cinzel', serif;
      font-size: 3.6rem;
      font-weight: 900;
      background: linear-gradient(135deg, #ffffff 0%, #fef08a 30%, #f59e0b 70%, #ffffff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
      letter-spacing: 1px;
      line-height: 1.1;
      filter: drop-shadow(0 12px 28px rgba(0,0,0,0.9));
    }
    .hero-sub {
      font-size: 1.4rem;
      color: var(--text-muted);
      margin-bottom: 2.5rem;
      font-weight: 500;
    }
    .minister-highlight {
      color: #ffffff;
      font-weight: 800;
      background: linear-gradient(120deg, transparent 0%, rgba(245, 158, 11, 0.3) 40%, rgba(6, 182, 212, 0.2) 80%, transparent 100%);
      padding: 0.3rem 1.2rem;
      border-radius: 40px;
      border-bottom: 2px solid var(--gold-main);
      display: inline-block;
    }

    /* PEDESTAL LAUNCH BUTTON */
    .pedestal-stage {
      position: relative;
      margin: 2rem auto 3rem;
      width: 340px;
      height: 340px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .orb-ring-1 {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      border: 2px dashed var(--gold-main);
      animation: rotateClockwise 22s linear infinite;
      opacity: 0.7;
    }
    .orb-ring-2 {
      position: absolute;
      width: 84%;
      height: 84%;
      border-radius: 50%;
      border: 2px solid var(--cyan-glow);
      border-top-color: var(--gold-light);
      animation: rotateCounter 14s linear infinite;
      opacity: 0.8;
    }
    @keyframes rotateClockwise { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes rotateCounter { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

    .btn-launch-trigger {
      position: relative;
      z-index: 5;
      width: 220px;
      height: 220px;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #38bdf8 0%, #1d4ed8 40%, #0f172a 85%, #040711 100%);
      border: 6px solid var(--gold-main);
      color: #fff;
      cursor: pointer;
      outline: none;
      box-shadow: 0 0 70px rgba(6, 182, 212, 0.6), inset 0 0 50px rgba(255,255,255,0.3);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.3rem;
      user-select: none;
    }
    .btn-launch-trigger:hover {
      transform: scale(1.08);
      box-shadow: 0 0 100px var(--gold-glow), 0 0 60px rgba(56, 189, 248, 0.8);
      border-color: var(--gold-light);
    }
    .btn-launch-trigger:active { transform: scale(0.94); }
    .launch-power-icon {
      font-size: 4.2rem;
      color: var(--gold-light);
      filter: drop-shadow(0 0 18px var(--gold-main));
      animation: powerPulse 2.2s ease-in-out infinite;
    }
    @keyframes powerPulse {
      0%, 100% { opacity: 0.8; transform: scale(1); }
      50% { opacity: 1; transform: scale(1.12); }
    }
    .btn-launch-label {
      font-size: 1.2rem;
      font-weight: 900;
      letter-spacing: 2.5px;
      color: #fff;
      text-transform: uppercase;
      text-shadow: 0 0 20px rgba(0,0,0,0.8);
    }

    /* INAUGURATION PLAQUE (Second Screen - revealed post 2-sec curtain unveil) */
    .inauguration-plaque {
      display: {{ $isLaunched ? 'block' : 'none' }};
      background: linear-gradient(145deg, rgba(15, 23, 42, 0.97), rgba(30, 41, 59, 0.94));
      border: 2px solid var(--gold-main);
      border-radius: 36px;
      padding: 4rem 2.8rem;
      max-width: 980px;
      margin: 2rem auto;
      box-shadow: 0 30px 80px rgba(0,0,0,0.95), 0 0 60px var(--gold-glow);
      position: relative;
      backdrop-filter: blur(20px);
      animation: plaqueRise 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes plaqueRise {
      from { opacity: 0; transform: translateY(50px) scale(0.94); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .plaque-emblem {
      height: 105px;
      margin-bottom: 1.5rem;
      filter: drop-shadow(0 0 24px rgba(245, 158, 11, 0.6));
    }
    .plaque-title {
      font-family: 'Cinzel', serif;
      font-size: 2.6rem;
      color: var(--gold-light);
      font-weight: 800;
      margin-bottom: 1.2rem;
      letter-spacing: 1px;
    }
    .plaque-body-text {
      font-size: 1.3rem;
      line-height: 1.9;
      color: #e2e8f0;
      max-width: 800px;
      margin: 0 auto 1.8rem;
    }
    .plaque-dignitary-title {
      font-size: 2.6rem;
      color: #ffffff;
      font-weight: 900;
      margin-bottom: 0.2rem;
      letter-spacing: 1px;
    }
    .plaque-dignitary-role {
      color: var(--gold-main);
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 2rem;
    }
    .plaque-stamp-badge {
      display: inline-block;
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid #10b981;
      color: #34d399;
      padding: 0.7rem 2rem;
      border-radius: 50px;
      font-weight: 800;
      font-size: 1rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      backdrop-filter: blur(4px);
    }
    .plaque-stamp-badge i { margin-right: 0.5rem; }

    /* STAGE NAV GRID */
    .stage-nav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      margin-top: 4rem;
    }
    .stage-card-link {
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 2rem 1.8rem;
      text-decoration: none;
      color: #fff;
      text-align: left;
      transition: all 0.4s ease;
      backdrop-filter: blur(12px);
      display: flex;
      gap: 1.5rem;
      align-items: flex-start;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    .stage-card-link:hover {
      border-color: var(--gold-main);
      transform: translateY(-8px);
      background: rgba(30, 41, 59, 0.95);
      box-shadow: 0 20px 48px rgba(0,0,0,0.7), 0 0 30px var(--gold-glow);
    }
    .stage-card-icon {
      font-size: 2.8rem;
      color: var(--gold-main);
      flex-shrink: 0;
    }
    .stage-card-content h3 {
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 0.3rem;
      color: #ffffff;
    }
    .stage-card-content p {
      font-size: 0.9rem;
      color: #94a3b8;
      line-height: 1.6;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
      .lab-radar-widget { display: none; }
    }
    @media (max-width: 768px) {
      .ceremony-header { padding: 0.8rem 1.2rem; flex-direction: column; align-items: stretch; }
      .header-brand { flex-wrap: wrap; }
      .header-controls { justify-content: center; }
      .hero-title { font-size: 2.4rem; }
      .hero-sub { font-size: 1rem; }
      .pedestal-stage { width: 280px; height: 280px; }
      .btn-launch-trigger { width: 180px; height: 180px; }
      .launch-power-icon { font-size: 3.2rem; }
      .inauguration-plaque { padding: 2rem 1.2rem; }
      .plaque-title { font-size: 1.8rem; }
      .plaque-dignitary-title { font-size: 2rem; }
      .stage-nav-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Real Recorded Crowd Applause Audio -->
  <audio id="realApplauseAudio" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2018/2018-preview.mp3" type="audio/mpeg">
    <source src="https://assets.mixkit.co/active_storage/sfx/470/470-preview.mp3" type="audio/mpeg">
  </audio>

  <!-- Spotlights -->
  <div class="spotlight-left" id="spotlightLeft"></div>
  <div class="spotlight-right" id="spotlightRight"></div>

  <!-- Particle Canvas -->
  <canvas id="bgCanvas"></canvas>

  <!-- 3D Velvet Theater Curtain Overlay -->
  <div class="curtain-overlay" id="curtainOverlay">
    <div class="curtain-panel curtain-panel-left"></div>
    <div class="curtain-panel curtain-panel-right"></div>
  </div>

  <!-- AMAZING CINEMATIC 3D COUNTDOWN DISC EMBLEM -->
  <div class="curtain-timer-banner" id="curtainTimerBanner">
    <div class="countdown-disc-container">
      <div class="countdown-ring-1"></div>
      <div class="countdown-ring-2"></div>
      <div class="countdown-disc-glass">
        <div class="curtain-timer-header"><i class="bi bi-stars"></i> LAUNCHING </br> LIMS 2.0 <i class="bi bi-stars"></i></div>
        <div class="curtain-timer-number" id="curtainCountNumber">3</div>
      </div>
    </div>
  </div>

  <!-- SLEEK GLASSMORPHIC PAN-INDIA DIGITAL LAB NETWORK WIDGET (LEFT SIDE) -->
  <div class="lab-radar-widget" id="indiaMapBg" style="{{ $isLaunched ? 'opacity: 0;' : 'opacity: 1;' }}">
    <div class="radar-widget-header">
      <div class="radar-widget-title"><i class="bi bi-radar"></i> Pan-India Labs</div>
      <span class="radar-widget-badge"><i class="bi bi-broadcast"></i> LIVE STAGE</span>
    </div>

    <!-- Live Animated Radar Scanner -->
    <div class="radar-scan-box">
      <div class="radar-sweep-hand"></div>
      <i class="bi bi-shield-check radar-center-icon"></i>
    </div>

    <!-- Accredited Lab Network Badges -->
    <div class="radar-lab-list">
      <div class="radar-lab-item">
        <span class="radar-lab-name"><i class="bi bi-building-fill-check"></i> Mumbai Central HQ</span>
        <span class="radar-lab-status">ONLINE</span>
      </div>
      <div class="radar-lab-item">
        <span class="radar-lab-name"><i class="bi bi-check-circle-fill"></i> 19 Regional Labs</span>
        <span class="radar-lab-status">NABL ISO</span>
      </div>
      <div class="radar-lab-item">
        <span class="radar-lab-name"><i class="bi bi-cpu-fill"></i> QR Certification</span>
        <span class="radar-lab-status">READY</span>
      </div>
    </div>

    <a href="{{ route('launch.map') }}" class="btn-radar-link">
      <i class="bi bi-map-fill"></i> View Pan-India Map (19 Labs)
    </a>
  </div>

  <!-- HEADER -->
  <header class="ceremony-header">
    <div class="header-brand">
      <img src="{{ asset('frontAssets/logo_lg.png') }}" alt="Textiles Committee Logo">
      <div class="header-brand-info">
        <h1>TEXTILES COMMITTEE · LIMS 2.0</h1>
        <p>Ministry of Textiles, Govt. of India</p>
      </div>
    </div>
    <div class="header-controls">
      <button class="btn-stage-hdr" id="btnAudioToggle" onclick="toggleAudio()"><i class="bi bi-volume-up-fill"></i> Sound: ON</button>
      <button class="btn-stage-hdr btn-reset-stage" onclick="resetStage()"><i class="bi bi-arrow-counterclockwise"></i> Re-Test Button 🔄</button>
      <a href="{{ route('launch.map') }}" class="btn-stage-hdr"><i class="bi bi-map-fill"></i> Pan-India Map</a>
      <a href="{{ route('launch.simulator') }}" class="btn-stage-hdr"><i class="bi bi-play-circle-fill"></i> 60-Sec Walkthrough</a>
      <a href="{{ route('launch.qr_demo') }}" class="btn-stage-hdr"><i class="bi bi-qr-code"></i> QR Verify</a>
      <a href="{{ url('/') }}" class="btn-stage-hdr"><i class="bi bi-house-fill"></i> Main Portal</a>
    </div>
  </header>

  <!-- COUNTDOWN -->
  <div class="countdown-strip">
    <span>🇮🇳 OFFICIAL LAUNCH CEREMONY: <strong>22 AUGUST 2026</strong></span>
    <div class="timer-box">
      <div class="timer-unit" id="timerDays">00d</div>
      <div class="timer-unit" id="timerHours">00h</div>
      <div class="timer-unit" id="timerMins">00m</div>
      <div class="timer-unit" id="timerSecs">00s</div>
    </div>
  </div>

  <!-- MAIN STAGE -->
  <main class="stage-main">
    <div class="vip-crown-badge">
      <i class="bi bi-crown-fill"></i> National E-Inauguration · Ministry of Textiles
    </div>

    <h1 class="hero-title">NATIONAL DIGITAL LAUNCH OF </br> LIMS 2.0</h1>
    <p class="hero-sub">
      Dedicated to the nation by <span class="minister-highlight">Shri Giriraj Singh Ji</span>, Hon’ble Union Minister of Textiles
    </p>

    <!-- LAUNCH PEDESTAL -->
    <div class="pedestal-stage" id="launchPedestal" style="{{ $isLaunched ? 'display: none;' : '' }}">
      <div class="orb-ring-1"></div>
      <div class="orb-ring-2"></div>
      <button class="btn-launch-trigger" id="btnLaunchTrigger" onclick="executeInauguration()">
        <i class="bi bi-power launch-power-icon"></i>
        <span class="btn-launch-label">LAUNCH LIMS</span>
      </button>
    </div>

    <!-- INAUGURATION PLAQUE (Second Screen - revealed post 2-sec curtain unveil) -->
    <div class="inauguration-plaque" id="inaugurationPlaque">
      <img src="{{ asset('frontAssets/logo_lg.png') }}" alt="Textiles Committee Emblem" class="plaque-emblem">
      <h2 class="plaque-title">OFFICIALLY DEDICATED TO THE NATION</h2>
      <p class="plaque-body-text">
        The Next-Generation <strong>Laboratory Information Management System (LIMS 2.0)</strong> of the Textiles Committee, Ministry of Textiles, has been officially launched and dedicated to the nation by
      </p>
      <h1 class="plaque-dignitary-title">SHRI GIRIRAJ SINGH JI</h1>
      <div class="plaque-dignitary-role">Hon’ble Union Minister of Textiles, Government of India</div>
      <div style="margin-bottom: 2rem;">
        <span class="plaque-stamp-badge"><i class="bi bi-check-circle-fill"></i> Inaugurated on <span id="launchedAtDisplay">{{ $launchedAt }}</span></span>
      </div>
      <button class="btn-stage-hdr" onclick="resetStage()" style="padding: 0.85rem 2.5rem; font-size: 1rem; background: linear-gradient(135deg, var(--gold-main), var(--gold-dark)); color: #000; border: none; font-weight: 800; border-radius: 40px;">
        <i class="bi bi-arrow-counterclockwise"></i> Re-Test Button & Fireworks 🔄
      </button>
    </div>

    <!-- NAV GRID -->
    <div class="stage-nav-grid">
      <a href="{{ route('launch.map') }}" class="stage-card-link">
        <i class="bi bi-map stage-card-icon"></i>
        <div class="stage-card-content">
          <h3>Pan-India Lab Network</h3>
          <p>Interactive map of 19 accredited regional laboratories with live sample capacity.</p>
        </div>
      </a>
      <a href="{{ route('launch.simulator') }}" class="stage-card-link">
        <i class="bi bi-play-circle stage-card-icon"></i>
        <div class="stage-card-content">
          <h3>60-Second Workflow</h3>
          <p>Stage-ready 5-step sample lifecycle from QR receipt to digital certificate.</p>
        </div>
      </a>
      <a href="{{ route('launch.qr_demo') }}" class="stage-card-link">
        <i class="bi bi-qr-code stage-card-icon"></i>
        <div class="stage-card-content">
          <h3>Live QR Scan Demo</h3>
          <p>Anti-counterfeit report verification scanner for live stage presentation.</p>
        </div>
      </a>
    </div>
  </main>

  <script>
    // ----- AUDIO SYNTHESIZER & COUNTDOWN TICK -----
    let audioEnabled = true;
    let audioCtx = null;

    function initAudio() {
      if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      }
      if (audioCtx.state === 'suspended') audioCtx.resume();
    }

    function playTickSound() {
      if (!audioEnabled) return;
      initAudio();
      const now = audioCtx.currentTime;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(880, now);
      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start(now);
      osc.stop(now + 0.15);
    }

    function playFanfare() {
      if (!audioEnabled) return;
      initAudio();
      const now = audioCtx.currentTime;
      const notes = [261.63, 329.63, 392.00, 523.25, 659.25, 783.99, 1046.50];
      notes.forEach((freq, i) => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, now + i * 0.08);
        gain.gain.setValueAtTime(0.3, now + i * 0.08);
        gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.08 + 0.8);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now + i * 0.08);
        osc.stop(now + i * 0.08 + 0.8);
      });
    }

    // ----- PHYSICAL ACOUSTIC HUMAN HAND CLAP & CROWD APPLAUSE ENGINE -----
    function playApplauseSound() {
      if (!audioEnabled) return;
      initAudio();
      const now = audioCtx.currentTime;
      const duration = 4.5;

      // Check if pre-loaded local audio element is ready
      const localAudio = document.getElementById('realApplauseAudio');
      if (localAudio && localAudio.readyState >= 2) {
        localAudio.currentTime = 0;
        localAudio.volume = 0.9;
        localAudio.play().catch(() => playPhysicalHumanClapEngine(now, duration));
        return;
      }

      // Execute Physical Acoustic Human Hand Clap Generator
      playPhysicalHumanClapEngine(now, duration);
    }

    function playPhysicalHumanClapEngine(now, duration) {
      const numClaps = 380;

      for (let i = 0; i < numClaps; i++) {
        // Human applause timing distribution (dense initial cheer, tapering over 4.5s)
        const timeOffset = Math.pow(Math.random(), 1.15) * duration;
        const startTime = now + timeOffset;
        const vol = (0.25 + Math.random() * 0.45) * (1 - (timeOffset / duration) * 0.65);

        // A) Palm Helmholtz Cavity Resonant Pop (550Hz - 1000Hz)
        const popOsc = audioCtx.createOscillator();
        const popGain = audioCtx.createGain();
        const popFreq = 550 + Math.random() * 450;
        popOsc.type = 'sine';
        popOsc.frequency.setValueAtTime(popFreq, startTime);
        popOsc.frequency.exponentialRampToValueAtTime(popFreq * 0.5, startTime + 0.025);
        popGain.gain.setValueAtTime(vol * 0.45, startTime);
        popGain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.025);
        popOsc.connect(popGain);
        popGain.connect(audioCtx.destination);
        popOsc.start(startTime);
        popOsc.stop(startTime + 0.03);

        // B) High Frequency Skin-on-Skin Slap Impulse (1800Hz - 3400Hz)
        const bufLen = Math.floor(audioCtx.sampleRate * 0.025);
        const noiseBuf = audioCtx.createBuffer(1, bufLen, audioCtx.sampleRate);
        const nData = noiseBuf.getChannelData(0);
        for (let k = 0; k < bufLen; k++) {
          nData[k] = (Math.random() * 2 - 1) * Math.exp(-k / (bufLen * 0.25));
        }

        const slapNoise = audioCtx.createBufferSource();
        slapNoise.buffer = noiseBuf;

        const slapFilter = audioCtx.createBiquadFilter();
        slapFilter.type = 'bandpass';
        slapFilter.frequency.setValueAtTime(1800 + Math.random() * 1600, startTime);
        slapFilter.Q.setValueAtTime(2.2 + Math.random() * 1.5, startTime);

        const slapGain = audioCtx.createGain();
        slapGain.gain.setValueAtTime(vol * 0.65, startTime);
        slapGain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.025);

        slapNoise.connect(slapFilter);
        slapFilter.connect(slapGain);
        slapGain.connect(audioCtx.destination);

        slapNoise.start(startTime);
        slapNoise.stop(startTime + 0.03);

        // C) Auditorium Hall Early Reflections (Acoustic Echoes at +16ms and +35ms)
        const echoTime1 = startTime + 0.016;
        const echoGain1 = audioCtx.createGain();
        echoGain1.gain.setValueAtTime(vol * 0.18, echoTime1);
        echoGain1.gain.exponentialRampToValueAtTime(0.001, echoTime1 + 0.02);
        const echo1 = audioCtx.createBufferSource();
        echo1.buffer = noiseBuf;
        echo1.connect(slapFilter);
        slapFilter.connect(echoGain1);
        echoGain1.connect(audioCtx.destination);
        echo1.start(echoTime1);
        echo1.stop(echoTime1 + 0.025);
      }

      // 3. Auditorium Crowd Cheering & Vocal Swell
      const swellLen = Math.floor(audioCtx.sampleRate * duration);
      const swellBuf = audioCtx.createBuffer(1, swellLen, audioCtx.sampleRate);
      const sData = swellBuf.getChannelData(0);
      for (let s = 0; s < swellLen; s++) {
        sData[s] = (Math.random() * 2 - 1);
      }
      const swellSrc = audioCtx.createBufferSource();
      swellSrc.buffer = swellBuf;

      const swellFilter = audioCtx.createBiquadFilter();
      swellFilter.type = 'bandpass';
      swellFilter.frequency.setValueAtTime(600, now);
      swellFilter.frequency.linearRampToValueAtTime(1250, now + 1.2);
      swellFilter.frequency.linearRampToValueAtTime(450, now + duration);
      swellFilter.Q.setValueAtTime(1.2, now);

      const swellGain = audioCtx.createGain();
      swellGain.gain.setValueAtTime(0.01, now);
      swellGain.gain.linearRampToValueAtTime(0.28, now + 0.8);
      swellGain.gain.exponentialRampToValueAtTime(0.001, now + duration);

      swellSrc.connect(swellFilter);
      swellFilter.connect(swellGain);
      swellGain.connect(audioCtx.destination);

      swellSrc.start(now);
      swellSrc.stop(now + duration);
    }

    function toggleAudio() {
      audioEnabled = !audioEnabled;
      const btn = document.getElementById('btnAudioToggle');
      btn.innerHTML = audioEnabled ? '<i class="bi bi-volume-up-fill"></i> Sound: ON' : '<i class="bi bi-volume-mute-fill"></i> Sound: OFF';
    }

    // ----- EXACT 6-SECOND TIMELINE SEQUENCE WITH CINEMATIC COUNTDOWN DISC -----
    function executeInauguration() {
      playFanfare();

      const curtainOverlay = document.getElementById('curtainOverlay');
      const curtainBanner = document.getElementById('curtainTimerBanner');
      const countNum = document.getElementById('curtainCountNumber');

      // PHASE 1 (0s to 1s): Close curtains over 1 second
      curtainOverlay.classList.remove('unveiling');
      curtainOverlay.classList.add('closed');

      // After 1 second (curtain fully closed), reveal 3D Glass Countdown Disc
      setTimeout(() => {
        curtainBanner.style.display = 'block';
        let count = 3;
        countNum.innerText = count;
        countNum.classList.add('pop-animate');
        playTickSound();

        // PHASE 2 (1s to 4s): 3-second countdown tick with zoom pulse & audio tick
        const countdownInterval = setInterval(() => {
          count--;
          if (count > 0) {
            countNum.innerText = count;
            countNum.classList.remove('pop-animate');
            void countNum.offsetWidth; // Trigger DOM reflow for CSS animation restart
            countNum.classList.add('pop-animate');
            playTickSound();
          } else {
            clearInterval(countdownInterval);
            curtainBanner.style.display = 'none';

            // PHASE 3 (4s to 6s): Open curtains over 2 seconds to reveal second screen
            curtainOverlay.classList.remove('closed');
            curtainOverlay.classList.add('unveiling');

            // PLAY CROWD CLAPPING & APPLAUSE SOUND EFFECT RIGHT WHEN CURTAINS OPEN!
            playApplauseSound();
            playFanfare();

            // Switch stage view
            document.getElementById('launchPedestal').style.display = 'none';
            document.getElementById('indiaMapBg').style.opacity = '0';
            document.getElementById('spotlightLeft').style.opacity = '0.85';
            document.getElementById('spotlightRight').style.opacity = '0.85';

            const plaque = document.getElementById('inaugurationPlaque');
            plaque.style.display = 'block';

            // Fire COMBINED Side Fireworks Cannons AND Top Confetti Rain Drops!
            fireRoyalFireworks();
            fireTopConfettiPops();

            // Save launch state via API
            fetch("{{ route('launch.trigger') }}", {
              method: "POST",
              headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
              }
            })
            .then(res => res.json())
            .then(data => {
              document.getElementById('launchedAtDisplay').innerText = data.launched_at;
            })
            .catch(() => {});
          }
        }, 1000);
      }, 1000);
    }

    // Side Fireworks Cannon Pops
    function fireRoyalFireworks() {
      const duration = 4.5 * 1000;
      const end = Date.now() + duration;
      (function frame() {
        confetti({ particleCount: 10, angle: 60, spread: 70, origin: { x: 0 }, colors: ['#f59e0b', '#ffffff', '#dc2626', '#06b6d4'] });
        confetti({ particleCount: 10, angle: 120, spread: 70, origin: { x: 1 }, colors: ['#f59e0b', '#fef08a', '#06b6d4', '#ffffff'] });
        if (Date.now() < end) requestAnimationFrame(frame);
      })();
    }

    // Top Confetti Rain Drops
    function fireTopConfettiPops() {
      const duration = 4.5 * 1000;
      const animationEnd = Date.now() + duration;

      confetti({
        particleCount: 120,
        spread: 120,
        origin: { y: 0.02 },
        colors: ['#f59e0b', '#ffffff', '#06b6d4', '#fef08a']
      });

      const interval = setInterval(function() {
        const timeLeft = animationEnd - Date.now();
        if (timeLeft <= 0) return clearInterval(interval);

        confetti({
          particleCount: 18,
          angle: 90,
          spread: 90,
          origin: { x: 0.5, y: 0 },
          colors: ['#f59e0b', '#fef08a', '#ffffff', '#06b6d4']
        });
      }, 200);
    }

    function resetStage() {
      fetch("{{ route('launch.reset') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Content-Type": "application/json"
        }
      })
      .then(() => {
        const curtainOverlay = document.getElementById('curtainOverlay');
        curtainOverlay.classList.remove('closed', 'unveiling');
        document.getElementById('inaugurationPlaque').style.display = 'none';
        document.getElementById('launchPedestal').style.display = 'flex';
        document.getElementById('indiaMapBg').style.opacity = '1';
        document.getElementById('spotlightLeft').style.opacity = '0.3';
        document.getElementById('spotlightRight').style.opacity = '0.3';
      })
      .catch(() => {
        document.getElementById('inaugurationPlaque').style.display = 'none';
        document.getElementById('launchPedestal').style.display = 'flex';
        document.getElementById('indiaMapBg').style.opacity = '1';
      });
    }

    // ----- COUNTDOWN TO LAUNCH -----
    const targetDate = new Date('2026-08-22T11:00:00+05:30').getTime();
    function updateCountdown() {
      const now = Date.now();
      const dist = targetDate - now;
      if (dist < 0) {
        document.getElementById('timerDays').innerText = '00d';
        document.getElementById('timerHours').innerText = '00h';
        document.getElementById('timerMins').innerText = '00m';
        document.getElementById('timerSecs').innerText = '00s';
        return;
      }
      const d = Math.floor(dist / (1000*60*60*24));
      const h = Math.floor((dist % (1000*60*60*24)) / (1000*60*60));
      const m = Math.floor((dist % (1000*60*60)) / (1000*60));
      const s = Math.floor((dist % (1000*60)) / 1000);
      document.getElementById('timerDays').innerText = String(d).padStart(2,'0')+'d';
      document.getElementById('timerHours').innerText = String(h).padStart(2,'0')+'h';
      document.getElementById('timerMins').innerText = String(m).padStart(2,'0')+'m';
      document.getElementById('timerSecs').innerText = String(s).padStart(2,'0')+'s';
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();

    // ----- PARTICLE CANVAS (CONSTELLATION ENGINE) -----
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