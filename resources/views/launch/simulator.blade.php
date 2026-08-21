<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>60-Second Sample Journey Simulator | LIMS 2.0 – Ministry of Textiles</title>
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
      --gold-glow: rgba(245, 158, 11, 0.45);
      --cyan-main: #06b6d4;
      --cyan-glow: rgba(6, 182, 212, 0.35);
      --green-main: #10b981;
      --bg-dark: #03050c;
      --card-bg: rgba(15, 23, 42, 0.92);
      --card-border: rgba(245, 158, 11, 0.35);
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
      width: 600px;
      height: 900px;
      pointer-events: none;
      z-index: 1;
      opacity: 0.35;
      transition: opacity 1.5s ease;
    }
    .spotlight-left {
      left: -10%;
      background: radial-gradient(ellipse at top, rgba(6, 182, 212, 0.4) 0%, transparent 70%);
      transform: rotate(-15deg);
    }
    .spotlight-right {
      right: -10%;
      background: radial-gradient(ellipse at top, rgba(245, 158, 11, 0.4) 0%, transparent 70%);
      transform: rotate(15deg);
    }

    /* HEADER NAV */
    .sim-header {
      position: relative;
      z-index: 20;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.1rem 3rem;
      background: rgba(3, 5, 12, 0.94);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--card-border);
      box-shadow: 0 10px 30px rgba(0,0,0,0.8);
      flex-wrap: wrap;
      gap: 1rem;
    }

    .sim-brand {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }
    .sim-brand img {
      height: 52px;
      filter: drop-shadow(0 0 12px var(--gold-glow));
    }
    .sim-brand-text h1 {
      font-size: 1.45rem;
      font-weight: 800;
      background: linear-gradient(135deg, #ffffff 0%, var(--gold-light) 60%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: 0.5px;
    }
    .sim-brand-text p {
      font-size: 0.78rem;
      color: var(--gold-main);
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    .sim-header-actions {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    .btn-sim-hdr {
      background: rgba(30, 41, 59, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #f8fafc;
      padding: 0.55rem 1.2rem;
      border-radius: 40px;
      text-decoration: none;
      font-size: 0.82rem;
      font-weight: 700;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      backdrop-filter: blur(10px);
    }
    .btn-sim-hdr:hover {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000;
      border-color: var(--gold-main);
      box-shadow: 0 0 20px var(--gold-glow);
      transform: translateY(-3px);
    }
    .btn-sim-hdr.active-voice {
      background: rgba(16, 185, 129, 0.2);
      border-color: var(--green-main);
      color: #34d399;
    }

    /* MAIN CONTAINER */
    .sim-container {
      position: relative;
      z-index: 10;
      max-width: 1350px;
      margin: 2rem auto 4rem;
      padding: 0 2rem;
    }

    /* TOP SUMMARY RIBBON */
    .top-summary-ribbon {
      background: linear-gradient(90deg, rgba(30, 58, 138, 0.4), rgba(15, 23, 42, 0.8), rgba(30, 58, 138, 0.4));
      border: 1px solid var(--cyan-glow);
      border-radius: 20px;
      padding: 1.2rem 2rem;
      margin-bottom: 2.2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      backdrop-filter: blur(14px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.5);
      flex-wrap: wrap;
      gap: 1.2rem;
      position: relative;
      overflow: hidden;
    }
    .ribbon-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .ribbon-left-icon {
      font-size: 2.2rem;
      color: var(--gold-main);
      filter: drop-shadow(0 0 10px var(--gold-glow));
      animation: iconPulse 2s ease-in-out infinite;
    }
    @keyframes iconPulse {
      0%, 100% { transform: scale(1); opacity: 0.9; }
      50% { transform: scale(1.12); opacity: 1; filter: drop-shadow(0 0 16px var(--gold-main)); }
    }
    .ribbon-title {
      font-size: 1.2rem;
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

    /* AUTO PLAY TIMER BAR */
    .stage-timer-track {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: rgba(255, 255, 255, 0.08);
    }
    .stage-timer-fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, var(--cyan-main), var(--gold-main), #10b981);
      transition: width 0.1s linear;
      box-shadow: 0 0 10px var(--gold-glow);
    }

    /* 60-SECOND TIMELINE STEPPER */
    .timeline-wrapper {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 24px;
      padding: 1.8rem 2.2rem 1.6rem;
      margin-bottom: 2.5rem;
      backdrop-filter: blur(18px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.6);
      position: relative;
    }

    .stepper-nodes {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      position: relative;
      gap: 1rem;
      z-index: 2;
    }

    /* FIX LINE ALIGNMENT: 57px aligns perfectly through the exact vertical center of the 58px step circles! */
    .stepper-connecting-line {
      position: absolute;
      top: 57px;
      left: 8%;
      right: 8%;
      height: 4px;
      background: rgba(255, 255, 255, 0.12);
      border-radius: 4px;
      z-index: 1;
    }
    .stepper-progress-line {
      position: absolute;
      top: 57px;
      left: 8%;
      height: 4px;
      background: linear-gradient(90deg, var(--cyan-main), var(--gold-main), var(--green-main));
      width: 0%;
      border-radius: 4px;
      transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1;
      box-shadow: 0 0 14px var(--gold-glow);
    }

    .step-node {
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s ease;
    }

    /* 100% OPAQUE SOLID BACKGROUND HIDES CONNECTING LINE BEHIND CIRCLE CLEANLY */
    .step-circle-box {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: #0b1120; /* Opaque solid background */
      border: 3px solid rgba(255, 255, 255, 0.18);
      color: #94a3b8;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
      font-weight: 800;
      position: relative;
      z-index: 2;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .step-node:hover .step-circle-box {
      transform: translateY(-4px) scale(1.08);
      border-color: var(--cyan-main);
      color: #ffffff;
      box-shadow: 0 0 20px var(--cyan-glow);
    }

    .step-node.active .step-circle-box {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      border-color: var(--gold-light);
      color: #000;
      transform: scale(1.18);
      animation: activeGlowPulse 2s ease-in-out infinite;
    }

    @keyframes activeGlowPulse {
      0%, 100% { box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.25), 0 0 22px var(--gold-glow); }
      50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0.4), 0 0 38px var(--gold-main); }
    }

    .step-node.completed .step-circle-box {
      background: #064e3b;
      border-color: var(--green-main);
      color: #34d399;
      box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .step-time-pill {
      font-size: 0.7rem;
      font-weight: 800;
      color: #94a3b8;
      margin-top: 0.75rem;
      background: rgba(0,0,0,0.6);
      padding: 0.2rem 0.65rem;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.08);
      transition: all 0.3s ease;
    }
    .step-node.active .step-time-pill {
      color: var(--gold-light);
      border-color: var(--gold-main);
      background: rgba(245, 158, 11, 0.18);
    }

    .step-node-title {
      font-size: 0.82rem;
      font-weight: 700;
      color: #cbd5e1;
      margin-top: 0.45rem;
      max-width: 140px;
      line-height: 1.3;
      transition: color 0.3s ease;
    }
    .step-node.active .step-node-title {
      color: #ffffff;
      font-weight: 800;
    }

    /* DYNAMIC STAGE SHOWCASE GRID */
    .stage-showcase-grid {
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 2rem;
      align-items: stretch;
    }

    /* LEFT DETAILS PANEL */
    .details-panel {
      background: var(--card-bg);
      border: 2px solid var(--card-border);
      border-radius: 24px;
      padding: 2.5rem;
      backdrop-filter: blur(18px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.8);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      transition: transform 0.4s ease, opacity 0.4s ease;
    }

    .details-panel.animate-panel-change {
      animation: panelSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes panelSlideIn {
      0% { opacity: 0; transform: translateY(16px) scale(0.98); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .stage-badges-line {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin-bottom: 1.2rem;
      flex-wrap: wrap;
    }

    .badge-stage-tag {
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid var(--gold-main);
      color: var(--gold-light);
      padding: 0.4rem 1.1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .badge-time-tag {
      background: rgba(6, 182, 212, 0.15);
      border: 1px solid var(--cyan-main);
      color: #67e8f9;
      padding: 0.4rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .stage-hero-title {
      font-family: 'Cinzel', serif;
      font-size: 2.1rem;
      font-weight: 900;
      color: #ffffff;
      margin-bottom: 0.4rem;
      line-height: 1.2;
    }

    .stage-hero-sub {
      font-size: 1.1rem;
      color: var(--cyan-main);
      font-weight: 600;
      margin-bottom: 1.4rem;
    }

    .meta-pills-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .meta-pill {
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 0.45rem 0.9rem;
      border-radius: 12px;
      font-size: 0.78rem;
      color: #e2e8f0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .meta-pill i {
      color: var(--gold-main);
      font-size: 0.95rem;
    }

    .stage-description-box {
      font-size: 1.05rem;
      line-height: 1.7;
      color: #cbd5e1;
      margin-bottom: 1.8rem;
      background: rgba(0, 0, 0, 0.35);
      padding: 1.2rem 1.5rem;
      border-radius: 16px;
      border-left: 4px solid var(--gold-main);
    }

    .io-flow-card {
      background: linear-gradient(135deg, rgba(6, 182, 212, 0.12), rgba(15, 23, 42, 0.6));
      border: 1px dashed var(--cyan-main);
      padding: 1rem 1.4rem;
      border-radius: 16px;
      font-size: 0.88rem;
      font-weight: 700;
      color: #7dd3fc;
      margin-bottom: 1.8rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .highlights-title {
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--gold-light);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .highlights-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      margin-bottom: 2rem;
    }

    .highlights-list li {
      font-size: 0.92rem;
      color: #f1f5f9;
      display: flex;
      align-items: flex-start;
      gap: 0.7rem;
      animation: fadeInListItem 0.4s ease forwards;
    }
    @keyframes fadeInListItem {
      from { opacity: 0; transform: translateX(-10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .highlights-list li i {
      color: #34d399;
      font-size: 1.1rem;
      margin-top: 0.1rem;
    }

    /* 3 IMPACT METRICS CARDS */
    .stage-metrics-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .metric-card {
      background: rgba(30, 41, 59, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 1rem;
      text-align: center;
      transition: all 0.3s ease;
    }
    .metric-card:hover {
      border-color: var(--gold-main);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    }
    .metric-card-val {
      font-size: 1.35rem;
      font-weight: 900;
      color: var(--gold-light);
      margin-bottom: 0.2rem;
    }
    .metric-card-lbl {
      font-size: 0.75rem;
      color: #94a3b8;
      font-weight: 700;
    }

    /* CONTROLS BAR */
    .sim-controls-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      flex-wrap: wrap;
    }

    .btn-stage-ctl {
      background: rgba(30, 41, 59, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      padding: 0.75rem 1.6rem;
      border-radius: 40px;
      font-size: 0.9rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
    }

    .btn-stage-ctl:hover {
      background: var(--cyan-main);
      color: #000;
      border-color: var(--cyan-main);
      box-shadow: 0 0 20px var(--cyan-glow);
      transform: translateY(-3px);
    }

    .btn-stage-ctl-primary {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      border-color: var(--gold-light);
      color: #000;
      box-shadow: 0 0 20px var(--gold-glow);
    }
    .btn-stage-ctl-primary:hover {
      background: #ffffff;
      color: #000;
      box-shadow: 0 0 30px var(--gold-glow);
    }

    /* RIGHT LIVE WORKBENCH MOCKUP PANEL */
    .workbench-panel {
      background: rgba(15, 23, 42, 0.95);
      border: 2px solid var(--cyan-main);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0,0,0,0.9), 0 0 40px var(--cyan-glow);
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .workbench-title-bar {
      background: rgba(3, 5, 12, 0.95);
      padding: 0.9rem 1.4rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .window-dots {
      display: flex;
      gap: 0.4rem;
    }
    .window-dot {
      width: 11px;
      height: 11px;
      border-radius: 50%;
    }
    .dot-red { background: #ef4444; }
    .dot-yellow { background: #f59e0b; }
    .dot-green { background: #10b981; }

    .workbench-title-text {
      font-size: 0.8rem;
      font-weight: 800;
      color: #94a3b8;
      letter-spacing: 1px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .live-pulse-badge {
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
      animation: livePulse 1.5s infinite;
    }
    @keyframes livePulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.5); opacity: 0.4; }
    }

    .workbench-viewport {
      padding: 1.8rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: radial-gradient(circle at 50% 50%, rgba(30, 58, 138, 0.2) 0%, rgba(3, 5, 12, 0.9) 100%);
      position: relative;
    }

    .mock-card {
      display: none;
      animation: fadeInMock 0.5s ease forwards;
    }
    .mock-card.active-mock {
      display: block;
    }
    @keyframes fadeInMock {
      from { opacity: 0; transform: translateY(20px) scale(0.96); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* MOCK STAGE 1: REGISTRATION & QR */
    .mock-form-box {
      background: rgba(30, 41, 59, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      padding: 1.5rem;
      margin-bottom: 1.2rem;
    }

    .mock-form-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.6rem 0;
      border-bottom: 1px dashed rgba(255,255,255,0.08);
      font-size: 0.85rem;
    }
    .mock-form-label { color: #94a3b8; font-weight: 600; }
    .mock-form-val { color: #ffffff; font-weight: 700; }

    .qr-preview-container {
      background: #ffffff;
      border-radius: 20px;
      padding: 1.4rem;
      text-align: center;
      color: #000;
      box-shadow: 0 0 30px var(--gold-glow);
      max-width: 240px;
      margin: 0 auto;
      position: relative;
    }
    .qr-preview-container img {
      width: 140px;
      height: 140px;
      border: 3px solid #000;
    }
    .qr-scan-beam {
      position: absolute;
      top: 1.4rem;
      left: 1.4rem;
      right: 1.4rem;
      height: 3px;
      background: rgba(239, 68, 68, 0.85);
      box-shadow: 0 0 12px #ef4444;
      animation: scanMove 2s ease-in-out infinite alternate;
    }
    @keyframes scanMove {
      from { top: 1.4rem; }
      to { top: 10.4rem; }
    }

    /* MOCK STAGE 2: BLIND CODING ANIMATIONS */
    .blind-coding-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.5rem;
      text-align: center;
    }

    .original-identity-card {
      background: rgba(225, 29, 72, 0.15);
      border: 1px solid #f43f5e;
      border-radius: 16px;
      padding: 1.2rem 2rem;
      width: 100%;
      color: #fecdd3;
      position: relative;
      overflow: hidden;
    }
    .strike-overlay {
      position: absolute;
      top: 50%;
      left: 10%;
      right: 10%;
      height: 3px;
      background: #f43f5e;
      transform: translateY(-50%) rotate(-3deg);
      box-shadow: 0 0 10px #f43f5e;
      animation: strikeDraw 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes strikeDraw {
      from { width: 0%; opacity: 0; }
      to { width: 80%; opacity: 1; }
    }

    .blind-arrow-icon {
      font-size: 2.2rem;
      color: var(--gold-main);
      animation: pulseDown 1.5s infinite;
    }
    @keyframes pulseDown {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(6px); }
    }

    .blind-barcode-card {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(15, 23, 42, 0.9));
      border: 2px solid #10b981;
      border-radius: 20px;
      padding: 1.5rem 2rem;
      width: 100%;
      box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
      animation: barcodePulse 2s ease-in-out infinite alternate;
    }
    @keyframes barcodePulse {
      from { box-shadow: 0 0 15px rgba(16, 185, 129, 0.3); }
      to { box-shadow: 0 0 35px rgba(16, 185, 129, 0.7); }
    }
    .barcode-svg-sim {
      font-family: monospace;
      font-size: 2rem;
      letter-spacing: 6px;
      color: #34d399;
      font-weight: 900;
      background: rgba(0,0,0,0.6);
      padding: 0.8rem;
      border-radius: 10px;
      margin: 0.8rem 0;
      border: 1px dashed #10b981;
    }

    /* MOCK STAGE 3: ANALYST WORKBENCH */
    .instrument-workbench-box {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .live-stream-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
    }

    .live-data-pill {
      background: rgba(30, 41, 59, 0.8);
      border: 1px solid rgba(255,255,255,0.1);
      padding: 0.8rem 1rem;
      border-radius: 14px;
      display: flex;
      flex-direction: column;
    }
    .live-data-val {
      font-size: 1.2rem;
      font-weight: 800;
      color: #38bdf8;
    }
    .live-data-lbl {
      font-size: 0.72rem;
      color: #94a3b8;
    }

    .audit-terminal-stream {
      background: #000;
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      padding: 0.7rem 1rem;
      font-family: monospace;
      font-size: 0.72rem;
      color: #34d399;
      height: 65px;
      overflow-y: hidden;
      line-height: 1.4;
    }

    /* MOCK STAGE 4: QUALITY REVIEW & STAMP DROP */
    .qc-review-box {
      background: rgba(30, 41, 59, 0.8);
      border: 1px solid var(--gold-main);
      border-radius: 20px;
      padding: 1.5rem;
    }

    .qc-check-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.65rem 0;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      font-size: 0.85rem;
    }
    .qc-check-name { font-weight: 700; color: #fff; display: flex; align-items: center; gap: 0.5rem; }
    .qc-check-badge {
      background: rgba(16, 185, 129, 0.2);
      color: #34d399;
      border: 1px solid #10b981;
      padding: 0.15rem 0.6rem;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 800;
    }

    .esign-stamp-box {
      margin-top: 1.2rem;
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(15, 23, 42, 0.9));
      border: 2px dashed var(--gold-main);
      border-radius: 16px;
      padding: 1rem;
      text-align: center;
      color: var(--gold-light);
      animation: stampDrop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes stampDrop {
      0% { transform: scale(1.35) rotate(-6deg); opacity: 0; }
      70% { transform: scale(0.95) rotate(1deg); opacity: 1; }
      100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    .esign-stamp-box i { font-size: 2rem; display: block; margin-bottom: 0.3rem; }

    /* MOCK STAGE 5: CERTIFICATE PREVIEW WITH SHIMMER */
    .cert-preview-card {
      background: #ffffff;
      color: #0f172a;
      border-radius: 20px;
      padding: 1.5rem;
      box-shadow: 0 0 40px var(--gold-glow);
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cert-preview-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent 45%, rgba(245, 158, 11, 0.2) 50%, transparent 55%);
      animation: shimmerSweep 3.5s infinite;
      pointer-events: none;
    }
    @keyframes shimmerSweep {
      0% { transform: translate(-100%, -100%); }
      100% { transform: translate(100%, 100%); }
    }

    .cert-header-mini {
      font-weight: 900;
      font-size: 0.9rem;
      color: #1e3a8a;
      border-bottom: 2px solid #1e3a8a;
      padding-bottom: 0.5rem;
      margin-bottom: 0.8rem;
    }
    .cert-pass-ribbon {
      background: #10b981;
      color: #fff;
      font-weight: 900;
      font-size: 0.8rem;
      padding: 0.3rem 1rem;
      border-radius: 20px;
      display: inline-block;
      margin-bottom: 1rem;
      letter-spacing: 1px;
    }
    .btn-verify-sim {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a, #0f172a);
      color: #fff;
      border: none;
      padding: 0.7rem;
      border-radius: 12px;
      font-weight: 800;
      font-size: 0.85rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 0.8rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }
    .btn-verify-sim:hover {
      background: var(--gold-main);
      color: #000;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px var(--gold-glow);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .stage-showcase-grid { grid-template-columns: 1fr; }
      .stepper-nodes { grid-template-columns: repeat(5, 1fr); gap: 0.5rem; }
      .step-node-title { font-size: 0.75rem; max-width: 90px; }
      .stage-hero-title { font-size: 1.7rem; }
    }
    @media (max-width: 640px) {
      .sim-header { padding: 0.8rem 1.2rem; flex-direction: column; align-items: stretch; }
      .top-summary-ribbon { flex-direction: column; text-align: center; }
      .ribbon-metrics { width: 100%; justify-content: space-around; }
      .ribbon-metric-item { border-left: none; padding-left: 0; }
      .stepper-nodes { grid-template-columns: repeat(5, 1fr); font-size: 0.8rem; }
      .step-circle-box { width: 44px; height: 44px; font-size: 1rem; }
      .stepper-connecting-line, .stepper-progress-line { top: 50px; }
      .step-node-title { display: none; }
      .stage-metrics-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Background Canvas -->
  <canvas id="bgCanvas"></canvas>
  <div class="spotlight-left"></div>
  <div class="spotlight-right"></div>

  <!-- HEADER -->
  <header class="sim-header">
    <div class="sim-brand">
      <img src="{{ asset('frontAssets/logo_lg.png') }}" alt="Textiles Committee Logo">
      <div class="sim-brand-text">
        <h1>TEXTILES COMMITTEE · LIMS 2.0</h1>
        <p>60-Second Interactive Sample Journey Simulator</p>
      </div>
    </div>
    <div class="sim-header-actions">
      <button class="btn-sim-hdr" id="btnVoiceToggle" onclick="toggleVoice()"><i class="bi bi-megaphone-fill"></i> Voice Narration: ON</button>
      <button class="btn-sim-hdr" id="btnAutoPlayToggle" onclick="toggleAutoPlay()"><i class="bi bi-play-circle-fill"></i> Auto Play (60s)</button>
      <a href="{{ route('launch.ceremony') }}" class="btn-sim-hdr"><i class="bi bi-house-door-fill"></i> Inauguration Stage</a>
    </div>
  </header>

  <!-- MAIN CONTAINER -->
  <main class="sim-container">

    <!-- TOP SUMMARY RIBBON -->
    <div class="top-summary-ribbon">
      <div class="ribbon-left">
        <i class="bi bi-lightning-charge-fill ribbon-left-icon"></i>
        <div>
          <div class="ribbon-title">Next-Gen Digital Lifecycle Walkthrough</div>
          <div class="ribbon-sub">Interactive 5-stage sample journey from QR request to instant global verification.</div>
        </div>
      </div>
      <div class="ribbon-metrics">
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">65%</div>
          <div class="ribbon-metric-lbl">TAT Reduction</div>
        </div>
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">100%</div>
          <div class="ribbon-metric-lbl">Paperless</div>
        </div>
        <div class="ribbon-metric-item">
          <div class="ribbon-metric-val">1 Second</div>
          <div class="ribbon-metric-lbl">QR Verify</div>
        </div>
      </div>

      <!-- AUTO PLAY STAGE TIMER TRACK -->
      <div class="stage-timer-track">
        <div class="stage-timer-fill" id="timerFillBar"></div>
      </div>
    </div>

    <!-- 60-SECOND TIMELINE STEPPER -->
    <div class="timeline-wrapper">
      <!-- PERFECTLY ALIGNED CONNECTING LINE AT TOP 57PX (RUNS BEHIND OPAQUE SOLID STEP CIRCLES) -->
      <div class="stepper-connecting-line"></div>
      <div class="stepper-progress-line" id="progressLine"></div>

      <div class="stepper-nodes">
        @foreach($steps as $idx => $s)
          <div class="step-node {{ $idx === 0 ? 'active' : '' }}" onclick="goToStep({{ $idx }})" id="stepNode-{{ $idx }}">
            <div class="step-circle-box">
              <i class="bi {{ $idx === 0 ? 'bi-qr-code-scan' : ($idx === 1 ? 'bi-shield-lock-fill' : ($idx === 2 ? 'bi-laptop-fill' : ($idx === 3 ? 'bi-patch-check-fill' : 'bi-award-fill'))) }}"></i>
            </div>
            <div class="step-time-pill">{{ $s['time_slot'] }}s</div>
            <div class="step-node-title">{{ $s['title'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- DYNAMIC STAGE SHOWCASE GRID -->
    <div class="stage-showcase-grid">

      <!-- LEFT DETAILS PANEL -->
      <div class="details-panel" id="detailsPanel">
        <div>
          <div class="stage-badges-line">
            <span class="badge-stage-tag" id="displayBadge">{{ $steps[0]['badge'] }}</span>
            <span class="badge-time-tag"><i class="bi bi-clock-history"></i> <span id="displayTimeSlot">{{ $steps[0]['time_slot'] }} Seconds</span></span>
          </div>

          <h2 class="stage-hero-title" id="displayTitle">{{ $steps[0]['title'] }}</h2>
          <div class="stage-hero-sub" id="displaySubtitle">{{ $steps[0]['subtitle'] }}</div>

          <div class="meta-pills-row">
            <div class="meta-pill"><i class="bi bi-person-badge"></i> <strong>Role:</strong> <span id="displayRole">{{ $steps[0]['role'] }}</span></div>
            <div class="meta-pill"><i class="bi bi-bookmark-star"></i> <strong>Standard:</strong> <span id="displayStandards">{{ $steps[0]['standards'] }}</span></div>
          </div>

          <div class="stage-description-box" id="displayDetails">
            {{ $steps[0]['details'] }}
          </div>

          <div class="io-flow-card">
            <i class="bi bi-arrow-left-right" style="font-size: 1.3rem;"></i>
            <span id="displayIO">{{ $steps[0]['input_output'] }}</span>
          </div>

          <div class="highlights-title"><i class="bi bi-stars"></i> Key Stage Breakthroughs</div>
          <ul class="highlights-list" id="displayHighlights">
            @foreach($steps[0]['highlights'] as $hl)
              <li><i class="bi bi-check-circle-fill"></i> {{ $hl }}</li>
            @endforeach
          </ul>

          <!-- IMPACT METRICS -->
          <div class="stage-metrics-row" id="displayMetrics">
            @foreach($steps[0]['metrics'] as $m)
              <div class="metric-card">
                <div class="metric-card-val">{{ $m['val'] }}</div>
                <div class="metric-card-lbl">{{ $m['label'] }}</div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- CONTROLS BAR -->
        <div class="sim-controls-bar">
          <button class="btn-stage-ctl" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Previous Stage</button>
          <button class="btn-stage-ctl" id="btnPausePlay" onclick="toggleAutoPlay()"><i class="bi bi-play-fill"></i> Auto Walkthrough (60s)</button>
          <button class="btn-stage-ctl btn-stage-ctl-primary" onclick="nextStep()">Next Stage <i class="bi bi-arrow-right"></i></button>
        </div>
      </div>

      <!-- RIGHT LIVE WORKBENCH MOCKUP PANEL -->
      <div class="workbench-panel">
        <div class="workbench-title-bar">
          <div class="window-dots">
            <div class="window-dot dot-red"></div>
            <div class="window-dot dot-yellow"></div>
            <div class="window-dot dot-green"></div>
          </div>
          <div class="workbench-title-text"><i class="bi bi-terminal-fill"></i> LIMS 2.0 LIVE WORKBENCH DEMO</div>
          <div class="live-pulse-badge"><div class="pulse-dot"></div> LIVE STAGE</div>
        </div>

        <div class="workbench-viewport">

          <!-- MOCK 1: REGISTRATION -->
          <div class="mock-card active-mock" id="mock-0">
            <div class="mock-form-box">
              <div style="font-size: 0.8rem; font-weight: 800; color: var(--gold-main); margin-bottom: 0.6rem; text-transform: uppercase;">
                <i class="bi bi-file-earmark-plus-fill"></i> Online Sample Request Form
              </div>
              <div class="mock-form-row">
                <span class="mock-form-label">Applicant:</span>
                <span class="mock-form-val">Bharat Textiles & Exports Pvt. Ltd.</span>
              </div>
              <div class="mock-form-row">
                <span class="mock-form-label">Material Type:</span>
                <span class="mock-form-val">100% Organic Cotton Woven Fabric</span>
              </div>
              <div class="mock-form-row">
                <span class="mock-form-label">Requested Tests:</span>
                <span class="mock-form-val">Fibre Composition, Tensile, Azo Dyes</span>
              </div>
            </div>

            <div class="qr-preview-container">
              <div class="qr-scan-beam"></div>
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://lims.textilescommittee.gov.in/verify/TC-MUM-2026-9482" alt="QR Code">
              <div style="font-size: 0.75rem; font-weight: 900; margin-top: 0.5rem; color: #1e3a8a;">
                TRACKING ID: TC/MUM/2026/9482
              </div>
            </div>
          </div>

          <!-- MOCK 2: BLIND CODING -->
          <div class="mock-card" id="mock-1">
            <div class="blind-coding-box">
              <div class="original-identity-card">
                <div class="strike-overlay"></div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; opacity: 0.8;">Customer Identity (Masked)</div>
                <div style="font-size: 1.1rem; font-weight: 900;">Bharat Textiles & Exports Pvt Ltd</div>
                <div style="font-size: 0.8rem;">Factory: Plot 42, Tirupur Textile Park</div>
              </div>

              <i class="bi bi-arrow-down-circle-fill blind-arrow-icon"></i>

              <div class="blind-barcode-card">
                <div style="font-size: 0.8rem; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 1px;">
                  <i class="bi bi-shield-lock-fill"></i> Cryptographic Blind Barcode
                </div>
                <div class="barcode-svg-sim">|||| #BC-88392-X92 ||||</div>
                <div style="font-size: 0.82rem; color: #cbd5e1; font-weight: 600;">
                  <i class="bi bi-check-circle-fill" style="color: #34d399;"></i> Analyst sees ZERO manufacturer details. 100% Impartial.
                </div>
              </div>
            </div>
          </div>

          <!-- MOCK 3: TESTING WORKBENCH -->
          <div class="mock-card" id="mock-2">
            <div class="instrument-workbench-box">
              <div style="font-size: 0.8rem; font-weight: 800; color: #38bdf8; text-transform: uppercase; display: flex; justify-content: space-between;">
                <span><i class="bi bi-laptop-fill"></i> Analyst Digital Workbench: Test Result Entry</span>
                <span style="color: #34d399;">AUTO-CALCULATED</span>
              </div>

              <div class="mock-form-box" style="margin-bottom: 0.8rem; padding: 1rem;">
                <div class="mock-form-row">
                  <span class="mock-form-label">Observed Tensile Load (Warp/Weft):</span>
                  <span class="mock-form-val" style="color: #38bdf8;">450 N / 410 N</span>
                </div>
                <div class="mock-form-row">
                  <span class="mock-form-label">Fiber Blend Percentage:</span>
                  <span class="mock-form-val" style="color: #34d399;">100% Cotton (Verified)</span>
                </div>
                <div class="mock-form-row">
                  <span class="mock-form-label">Tolerance Status:</span>
                  <span class="mock-form-val" style="color: #34d399;"><i class="bi bi-check-circle-fill"></i> Meets BIS/ISO Limit</span>
                </div>
              </div>

              <div class="live-stream-row">
                <div class="live-data-pill">
                  <span class="live-data-val">Auto Calc</span>
                  <span class="live-data-lbl">Mean & Std Deviation</span>
                </div>
                <div class="live-data-pill">
                  <span class="live-data-val">Grade 4-5</span>
                  <span class="live-data-lbl">Color Fastness Score</span>
                </div>
              </div>

              <div class="audit-terminal-stream">
                [00:27.2] Analyst #8492 opened Blind Code #BC-88392-X92.<br>
                [00:29.1] Entered test observations. System computed mean value.<br>
                [00:30.0] Result saved to digital workbench audit log.
              </div>
            </div>
          </div>

          <!-- MOCK 4: QUALITY REVIEW -->
          <div class="mock-card" id="mock-3">
            <div class="qc-review-box">
              <div style="font-size: 0.85rem; font-weight: 800; color: var(--gold-light); text-transform: uppercase; margin-bottom: 0.8rem;">
                <i class="bi bi-card-checklist"></i> NABL ISO 17025 Verification Matrix
              </div>

              <div class="qc-check-item">
                <span class="qc-check-name"><i class="bi bi-check-circle-fill" style="color: #34d399;"></i> Fibre Composition (100% Cotton)</span>
                <span class="qc-check-badge">PASS</span>
              </div>
              <div class="qc-check-item">
                <span class="qc-check-name"><i class="bi bi-check-circle-fill" style="color: #34d399;"></i> Tensile Strength (&gt; 400 N)</span>
                <span class="qc-check-badge">PASS</span>
              </div>
              <div class="qc-check-item">
                <span class="qc-check-name"><i class="bi bi-check-circle-fill" style="color: #34d399;"></i> Eco-Chemical Compliance (Azo Free)</span>
                <span class="qc-check-badge">PASS</span>
              </div>

              <div class="esign-stamp-box">
                <i class="bi bi-shield-check"></i>
                <div style="font-weight: 900; font-size: 0.95rem;">DIGITALLY SIGNED & AUTHORIZED</div>
                <div style="font-size: 0.75rem; color: #cbd5e1;">Dr. R. K. Sharma · Senior Quality Officer (PKI e-Sign)</div>
              </div>
            </div>
          </div>

          <!-- MOCK 5: CERTIFICATE -->
          <div class="mock-card" id="mock-4">
            <div class="cert-preview-card">
              <div class="cert-header-mini">
                TEXTILES COMMITTEE · GOVT OF INDIA<br>
                NABL ACCREDITED TEST CERTIFICATE
              </div>

              <div class="cert-pass-ribbon"><i class="bi bi-check-all"></i> PASSED & ACCREDITED</div>

              <div style="font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.6rem;">
                Report No: TC/MUM/2026/08/9482
              </div>

              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.8rem; margin-bottom: 0.8rem;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://lims.textilescommittee.gov.in/verify/TC-MUM-2026-9482" alt="QR" style="height: 90px; width: 90px;">
              </div>

              <a href="{{ route('launch.qr_demo') }}" class="btn-verify-sim">
                <i class="bi bi-qr-code-scan"></i> Test Live QR Verification Scanner
              </a>
            </div>
          </div>

        </div>
      </div>

    </div>

  </main>

  <script>
    const steps = @json($steps);
    let currentIdx = 0;
    let autoPlayTimer = null;
    let timerProgressInterval = null;
    let voiceEnabled = true;
    let stageStartTime = Date.now();
    const STAGE_DURATION_MS = 12000;

    function renderStep(idx) {
      currentIdx = idx;
      const data = steps[idx];

      // Trigger panel animation
      const detailsPanel = document.getElementById('detailsPanel');
      detailsPanel.classList.remove('animate-panel-change');
      void detailsPanel.offsetWidth; // DOM reflow
      detailsPanel.classList.add('animate-panel-change');

      // Update text fields
      document.getElementById('displayBadge').innerText = data.badge;
      document.getElementById('displayTimeSlot').innerText = `${data.time_slot} Seconds`;
      document.getElementById('displayTitle').innerText = data.title;
      document.getElementById('displaySubtitle').innerText = data.subtitle;
      document.getElementById('displayRole').innerText = data.role;
      document.getElementById('displayStandards').innerText = data.standards;
      document.getElementById('displayDetails').innerText = data.details;
      document.getElementById('displayIO').innerText = data.input_output;

      // Update Highlights with animated list items
      const hlContainer = document.getElementById('displayHighlights');
      hlContainer.innerHTML = '';
      data.highlights.forEach((hl, i) => {
        const li = document.createElement('li');
        li.style.animationDelay = `${i * 0.1}s`;
        li.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${hl}`;
        hlContainer.appendChild(li);
      });

      // Update Metrics
      const metricsContainer = document.getElementById('displayMetrics');
      metricsContainer.innerHTML = '';
      data.metrics.forEach(m => {
        const div = document.createElement('div');
        div.className = 'metric-card';
        div.innerHTML = `<div class="metric-card-val">${m.val}</div><div class="metric-card-lbl">${m.label}</div>`;
        metricsContainer.appendChild(div);
      });

      // Update Stepper Nodes & Stepper Progress Bar
      steps.forEach((_, i) => {
        const node = document.getElementById(`stepNode-${i}`);
        node.classList.remove('active', 'completed');
        if (i === idx) {
          node.classList.add('active');
        } else if (i < idx) {
          node.classList.add('completed');
        }

        // Mock cards
        const mock = document.getElementById(`mock-${i}`);
        if (mock) {
          mock.classList.remove('active-mock');
          if (i === idx) mock.classList.add('active-mock');
        }
      });

      // Progress Line (maps perfectly across 5 nodes: 0%, 25%, 50%, 75%, 100%)
      const pct = (idx / (steps.length - 1)) * 84;
      document.getElementById('progressLine').style.width = `${pct}%`;

      // Reset auto-play stage timer bar
      resetStageTimerBar();

      // Voice Narration
      speakCurrentStep();

      // Confetti on final stage!
      if (idx === steps.length - 1) {
        confetti({
          particleCount: 100,
          spread: 80,
          origin: { y: 0.4 },
          colors: ['#f59e0b', '#06b6d4', '#10b981', '#ffffff']
        });
      }
    }

    function resetStageTimerBar() {
      const fill = document.getElementById('timerFillBar');
      fill.style.width = '0%';
      stageStartTime = Date.now();
    }

    function updateStageTimerBar() {
      if (!autoPlayTimer) return;
      const elapsed = Date.now() - stageStartTime;
      const pct = Math.min((elapsed / STAGE_DURATION_MS) * 100, 100);
      document.getElementById('timerFillBar').style.width = `${pct}%`;
    }

    function nextStep() {
      if (currentIdx < steps.length - 1) {
        renderStep(currentIdx + 1);
      } else {
        renderStep(0);
      }
    }

    function prevStep() {
      if (currentIdx > 0) {
        renderStep(currentIdx - 1);
      }
    }

    function goToStep(idx) {
      renderStep(idx);
    }

    // ----- VOICE NARRATION (SPEECH SYNTHESIS API) -----
    function speakCurrentStep() {
      if (!voiceEnabled || !('speechSynthesis' in window)) return;
      window.speechSynthesis.cancel(); // Stop ongoing speech

      const data = steps[currentIdx];
      const textToSpeak = `${data.badge}. ${data.title}. ${data.details}`;

      const utterance = new SpeechSynthesisUtterance(textToSpeak);
      utterance.rate = 1.0;
      utterance.pitch = 1.0;

      // Select voice if available
      const voices = window.speechSynthesis.getVoices();
      const preferredVoice = voices.find(v => v.lang.includes('en-IN') || v.lang.includes('en-GB') || v.lang.includes('en'));
      if (preferredVoice) utterance.voice = preferredVoice;

      window.speechSynthesis.speak(utterance);
    }

    function toggleVoice() {
      voiceEnabled = !voiceEnabled;
      const btn = document.getElementById('btnVoiceToggle');
      if (voiceEnabled) {
        btn.innerHTML = `<i class="bi bi-megaphone-fill"></i> Voice Narration: ON`;
        btn.classList.add('active-voice');
        speakCurrentStep();
      } else {
        window.speechSynthesis.cancel();
        btn.innerHTML = `<i class="bi bi-megaphone-mute-fill"></i> Voice Narration: OFF`;
        btn.classList.remove('active-voice');
      }
    }

    // ----- AUTO PLAY (60s TOTAL, 12s PER STAGE) -----
    function toggleAutoPlay() {
      const btnHdr = document.getElementById('btnAutoPlayToggle');
      const btnCtl = document.getElementById('btnPausePlay');

      if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        clearInterval(timerProgressInterval);
        autoPlayTimer = null;
        timerProgressInterval = null;
        document.getElementById('timerFillBar').style.width = '0%';
        btnHdr.innerHTML = `<i class="bi bi-play-circle-fill"></i> Auto Play (60s)`;
        btnCtl.innerHTML = `<i class="bi bi-play-fill"></i> Auto Walkthrough (60s)`;
      } else {
        stageStartTime = Date.now();
        timerProgressInterval = setInterval(updateStageTimerBar, 100);

        autoPlayTimer = setInterval(() => {
          nextStep();
        }, STAGE_DURATION_MS);

        btnHdr.innerHTML = `<i class="bi bi-pause-circle-fill"></i> Pause Auto Play`;
        btnCtl.innerHTML = `<i class="bi bi-pause-fill"></i> Pause Auto Play`;
      }
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') nextStep();
      if (e.key === 'ArrowLeft') prevStep();
      if (e.key === ' ') {
        e.preventDefault();
        toggleAutoPlay();
      }
    });

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
    const count = 65;
    for (let i=0; i<count; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random()-0.5)*0.45,
        vy: (Math.random()-0.5)*0.45,
        r: Math.random()*2 + 1
      });
    }

    function drawParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = 'rgba(6, 182, 212, 0.45)';
      ctx.strokeStyle = 'rgba(245, 158, 11, 0.12)';
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
          if (dist < 130) {
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
