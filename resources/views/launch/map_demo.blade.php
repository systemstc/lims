<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pan-India Lab Network | LIMS 2.0 – Ministry of Textiles</title>
  <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
      --glass-card: rgba(15, 23, 42, 0.88);
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

    /* HEADER NAV (SYNCHRONIZED WITH LAUNCH SUITE) */
    .ceremony-header {
      position: relative;
      z-index: 20;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.9rem 3rem;
      background: rgba(3, 5, 12, 0.94);
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

    /* MAP MAIN LAYOUT */
    .map-layout-wrapper {
      position: relative;
      z-index: 10;
      display: grid;
      grid-template-columns: 380px 1fr;
      height: calc(100vh - 75px);
      overflow: hidden;
    }

    /* SIDEBAR PANEL */
    .sidebar-panel {
      background: rgba(15, 23, 42, 0.95);
      border-right: 1px solid var(--glass-border);
      padding: 1.6rem;
      overflow-y: auto;
      backdrop-filter: blur(18px);
      box-shadow: 10px 0 30px rgba(0,0,0,0.8);
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
    }

    .metrics-card {
      background: linear-gradient(135deg, rgba(30, 58, 138, 0.3), rgba(15, 23, 42, 0.9));
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 1.3rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }

    .metrics-card-title {
      font-size: 0.85rem;
      font-weight: 800;
      color: var(--gold-light);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.8rem;
    }

    .metric-item {
      text-align: center;
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.08);
      padding: 0.8rem 0.5rem;
      border-radius: 14px;
      transition: all 0.3s ease;
    }
    .metric-item:hover {
      border-color: var(--gold-main);
      transform: translateY(-2px);
    }

    .metric-value {
      font-size: 1.3rem;
      font-weight: 900;
      color: var(--gold-light);
    }

    .metric-label {
      font-size: 0.7rem;
      color: #94a3b8;
      text-transform: uppercase;
      font-weight: 700;
      margin-top: 0.2rem;
    }

    /* SEARCH INPUT */
    .lab-search-box {
      position: relative;
    }
    .lab-search-box input {
      width: 100%;
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 30px;
      padding: 0.65rem 1.2rem 0.65rem 2.6rem;
      color: #ffffff;
      font-size: 0.85rem;
      outline: none;
      transition: all 0.3s ease;
    }
    .lab-search-box input:focus {
      border-color: var(--gold-main);
      box-shadow: 0 0 15px var(--gold-glow);
    }
    .lab-search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gold-main);
      font-size: 0.95rem;
    }

    .lab-list-title {
      font-size: 0.82rem;
      font-weight: 800;
      color: #cbd5e1;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .lab-list {
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
    }

    .lab-card {
      background: rgba(30, 41, 59, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 1.1rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .lab-card:hover, .lab-card.active-lab {
      border-color: var(--gold-main);
      background: rgba(245, 158, 11, 0.12);
      transform: translateX(6px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.6), 0 0 20px var(--gold-glow);
    }

    .lab-card h4 {
      font-size: 0.98rem;
      font-weight: 800;
      margin-bottom: 0.2rem;
      color: #ffffff;
    }

    .lab-card p {
      font-size: 0.8rem;
      color: #94a3b8;
      margin-bottom: 0.5rem;
    }

    .lab-card .badge-type {
      display: inline-block;
      font-size: 0.72rem;
      padding: 0.25rem 0.7rem;
      border-radius: 20px;
      background: rgba(16, 185, 129, 0.15);
      color: #34d399;
      border: 1px solid #10b981;
      font-weight: 800;
    }

    /* RIGHT MAP CONTAINER */
    .map-container-viewport {
      position: relative;
      width: 100%;
      height: 100%;
      background: #050811;
    }

    #mapContainer {
      width: 100%;
      height: 100%;
    }

    /* MAP LAYER SWITCHER CONTROLS OVERLAY */
    .map-layer-switcher {
      position: absolute;
      top: 1.2rem;
      right: 1.5rem;
      z-index: 1000;
      background: rgba(15, 23, 42, 0.9);
      border: 1px solid var(--glass-border);
      border-radius: 40px;
      padding: 0.4rem;
      display: flex;
      gap: 0.4rem;
      backdrop-filter: blur(14px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.7);
    }

    .btn-map-layer {
      background: transparent;
      border: none;
      color: #cbd5e1;
      padding: 0.45rem 1rem;
      border-radius: 30px;
      font-size: 0.78rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .btn-map-layer.active-layer {
      background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
      color: #000;
      box-shadow: 0 0 15px var(--gold-glow);
    }

    /* CRITICAL FIX: OVERRIDE LEAFLET DIV ICON DEFAULT BOX-MODEL FOR 100% LAT/LNG PRECISION */
    .leaflet-marker-icon.custom-radar-pin {
      background: transparent !important;
      border: none !important;
      border-radius: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    .pin-container-box {
      position: absolute;
      top: 0;
      left: 0;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: auto;
    }

    .radar-pin-dot {
      width: 16px;
      height: 16px;
      background: radial-gradient(circle, #ffffff 0%, #f59e0b 60%, #b45309 100%);
      border: 2.5 solid #ffffff;
      border-radius: 50%;
      box-shadow: 0 0 14px #f59e0b, 0 0 24px rgba(245, 158, 11, 0.9);
      z-index: 10;
      cursor: pointer;
      transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .radar-pin-ring {
      position: absolute;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      border: 2px solid #f59e0b;
      animation: pinRadarPulse 2s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
      z-index: 5;
      pointer-events: none;
    }
    @keyframes pinRadarPulse {
      0% { transform: scale(0.3); opacity: 1; }
      100% { transform: scale(1.6); opacity: 0; }
    }

    .leaflet-marker-icon:hover .radar-pin-dot {
      transform: scale(1.5);
      background: #ffffff;
      box-shadow: 0 0 25px #f59e0b;
    }

    /* LEAFLET POPUP OVERLAY STYLING */
    .leaflet-popup-content-wrapper {
      background: rgba(15, 23, 42, 0.96) !important;
      color: #fff !important;
      border: 2px solid var(--gold-main) !important;
      border-radius: 20px !important;
      box-shadow: 0 15px 40px rgba(0,0,0,0.9), 0 0 30px var(--gold-glow) !important;
      backdrop-filter: blur(16px);
      padding: 0.4rem;
    }
    .leaflet-popup-tip {
      background: rgba(15, 23, 42, 0.96) !important;
      border: 1px solid var(--gold-main);
    }
    .popup-lab-title {
      font-family: 'Cinzel', serif;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--gold-light);
      margin-bottom: 0.3rem;
      border-bottom: 1px solid rgba(245, 158, 11, 0.3);
      padding-bottom: 0.4rem;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
      .map-layout-wrapper { grid-template-columns: 1fr; height: auto; }
      .sidebar-panel { height: 400px; }
      .map-container-viewport { height: 500px; }
    }
    @media (max-width: 640px) {
      .ceremony-header { padding: 0.8rem 1.2rem; flex-direction: column; align-items: stretch; }
      .header-brand { flex-wrap: wrap; }
      .header-controls { justify-content: center; }
      .metrics-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Spotlights -->
  <div class="spotlight-left"></div>
  <div class="spotlight-right"></div>

  <!-- Particle Canvas -->
  <canvas id="bgCanvas"></canvas>

  <!-- HEADER (100% SYNCHRONIZED WITH CEREMONY, SIMULATOR & QR DEMO) -->
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
      <a href="{{ route('launch.simulator') }}" class="btn-stage-hdr"><i class="bi bi-play-circle-fill"></i> 60-Sec Walkthrough</a>
      <a href="{{ route('launch.qr_demo') }}" class="btn-stage-hdr"><i class="bi bi-qr-code"></i> QR Verify</a>
      <a href="{{ route('launch.ceremony') }}" class="btn-stage-hdr"><i class="bi bi-house-door-fill"></i> Inauguration Stage</a>
      <a href="{{ url('/') }}" class="btn-stage-hdr"><i class="bi bi-house-fill"></i> Main Portal</a>
    </div>
  </header>

  <!-- MAIN MAP LAYOUT -->
  <div class="map-layout-wrapper">

    <!-- SIDEBAR PANEL -->
    <div class="sidebar-panel">

      <!-- METRICS CARD -->
      <div class="metrics-card">
        <div class="metrics-card-title">
          <i class="bi bi-globe-asia-australia"></i> National Infrastructure
        </div>
        <div class="metrics-grid">
          <div class="metric-item">
            <div class="metric-value">{{ $metrics['total_labs'] }}</div>
            <div class="metric-label">Regional Labs</div>
          </div>
          <div class="metric-item">
            <div class="metric-value">{{ $metrics['monthly_samples'] }}</div>
            <div class="metric-label">Monthly Samples</div>
          </div>
          <div class="metric-item">
            <div class="metric-value">{{ $metrics['turnaround_days'] }}</div>
            <div class="metric-label">Avg Turnaround</div>
          </div>
          <div class="metric-item">
            <div class="metric-value">{{ $metrics['nabl_accredited_params'] }}</div>
            <div class="metric-label">NABL Parameters</div>
          </div>
        </div>
      </div>

      <!-- SEARCH BOX -->
      <div class="lab-search-box">
        <i class="bi bi-search lab-search-icon"></i>
        <input type="text" id="labSearchInput" onkeyup="filterLabs()" placeholder="Search lab by city, state or specialty...">
      </div>

      <div class="lab-list-title">
        <span><i class="bi bi-building-fill-check" style="color: var(--gold-main);"></i> Accredited Laboratories</span>
        <span style="font-size: 0.72rem; color: #34d399;"><i class="bi bi-broadcast"></i> LIVE MAP</span>
      </div>

      <!-- LAB LIST -->
      <div class="lab-list" id="labListContainer">
        @foreach($labs as $lab)
          <div class="lab-card" id="labCard-{{ $lab['id'] }}" onclick="focusLab({{ $lab['lat'] }}, {{ $lab['lng'] }}, {{ $lab['id'] }})">
            <h4>{{ $lab['name'] }}</h4>
            <p><i class="bi bi-geo-alt-fill" style="color: var(--gold-main);"></i> {{ $lab['city'] }}</p>
            <span class="badge-type"><i class="bi bi-check-circle-fill"></i> {{ $lab['nabl'] }}</span>
            <div style="font-size: 0.78rem; color: #cbd5e1; margin-top: 0.5rem; display: flex; justify-content: space-between;">
              <span>Monthly Capacity:</span>
              <strong style="color: var(--gold-light);">{{ $lab['tests_monthly'] }}</strong>
            </div>
          </div>
        @endforeach
      </div>

    </div>

    <!-- MAP CONTAINER VIEWPORT -->
    <div class="map-container-viewport">

      <!-- MAP LAYER SWITCHER OVERLAY -->
      <div class="map-layer-switcher">
        <button class="btn-map-layer active-layer" id="btnLayerVoyager" onclick="switchMapLayer('voyager')">
          <i class="bi bi-map-fill"></i> Voyager Map
        </button>
        <button class="btn-map-layer" id="btnLayerOsm" onclick="switchMapLayer('osm')">
          <i class="bi bi-compass-fill"></i> Street Map
        </button>
        <button class="btn-map-layer" id="btnLayerCyber" onclick="switchMapLayer('cyber')">
          <i class="bi bi-moon-stars-fill"></i> Cyber Dark
        </button>
      </div>

      <div id="mapContainer"></div>

    </div>

  </div>

  <script>
    const labs = @json($labs);
    let map = null;
    let currentTileLayer = null;
    let voiceEnabled = true;
    const markers = {};

    // Map Tile Layer URLs
    const tileLayers = {
      // CartoDB Voyager: Highly visible, vivid, crisp, perfectly readable geography for presentations!
      voyager: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
      // OpenStreetMap: High-contrast detailed street and state boundaries map
      osm: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      // CartoDB Dark: Sleek cyber dark mode
      cyber: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
    };

    function initMap() {
      // Center of India coordinates
      map = L.map('mapContainer', {
        center: [20.5937, 78.9629],
        zoom: 5,
        zoomControl: true
      });

      // Default to CartoDB Voyager (Vivid, highly readable and clear map of India!)
      currentTileLayer = L.tileLayer(tileLayers.voyager, {
        attribution: '&copy; Textiles Committee LIMS 2.0 • Govt of India',
        maxZoom: 18
      }).addTo(map);

      // Add Custom Radar Markers with exact center anchor
      labs.forEach(lab => {
        const customIcon = L.divIcon({
          className: 'custom-radar-pin',
          html: `<div class="pin-container-box"><div class="radar-pin-ring"></div><div class="radar-pin-dot"></div></div>`,
          iconSize: [44, 44],
          iconAnchor: [22, 22],
          popupAnchor: [0, -20]
        });

        const marker = L.marker([lab.lat, lab.lng], { icon: customIcon }).addTo(map);

        const popupContent = `
          <div style="padding: 0.4rem;">
            <div class="popup-lab-title">${lab.name}</div>
            <p style="margin: 0.4rem 0; color: #94a3b8; font-size: 0.85rem; font-weight: 600;"><i class="bi bi-building"></i> ${lab.type}</p>
            <div style="font-size: 0.82rem; line-height: 1.6; color: #e2e8f0;">
              <strong><i class="bi bi-stars" style="color: #f59e0b;"></i> Specialties:</strong> ${lab.speciality}<br>
              <strong><i class="bi bi-activity" style="color: #38bdf8;"></i> Monthly Capacity:</strong> ${lab.tests_monthly}<br>
              <div style="margin-top: 0.6rem;">
                <span style="background: rgba(16,185,129,0.2); color:#34d399; border:1px solid #10b981; padding: 0.2rem 0.7rem; border-radius: 20px; font-weight: 800; font-size: 0.75rem;"><i class="bi bi-check-circle-fill"></i> ${lab.nabl}</span>
              </div>
            </div>
          </div>
        `;

        marker.bindPopup(popupContent);
        markers[lab.id] = marker;
      });

      // Recalculate map container size after DOM render
      setTimeout(() => {
        map.invalidateSize();
      }, 250);
    }

    function switchMapLayer(layerKey) {
      if (!map || !tileLayers[layerKey]) return;

      if (currentTileLayer) map.removeLayer(currentTileLayer);

      currentTileLayer = L.tileLayer(tileLayers[layerKey], {
        attribution: '&copy; Textiles Committee LIMS 2.0 • Govt of India',
        maxZoom: 18
      }).addTo(map);

      // Update button active states
      document.querySelectorAll('.btn-map-layer').forEach(btn => btn.classList.remove('active-layer'));
      if (layerKey === 'voyager') document.getElementById('btnLayerVoyager').classList.add('active-layer');
      if (layerKey === 'osm') document.getElementById('btnLayerOsm').classList.add('active-layer');
      if (layerKey === 'cyber') document.getElementById('btnLayerCyber').classList.add('active-layer');
    }

    function focusLab(lat, lng, id) {
      map.flyTo([lat, lng], 11, { duration: 1.5 });
      markers[id].openPopup();

      // Highlight active card
      document.querySelectorAll('.lab-card').forEach(c => c.classList.remove('active-lab'));
      const activeCard = document.getElementById(`labCard-${id}`);
      if (activeCard) {
        activeCard.classList.add('active-lab');
        activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }

      // Voice Narration
      const lab = labs.find(l => l.id === id);
      if (lab && voiceEnabled) {
        speakLabDetails(lab);
      }
    }

    function filterLabs() {
      const q = document.getElementById('labSearchInput').value.toLowerCase();
      labs.forEach(lab => {
        const card = document.getElementById(`labCard-${lab.id}`);
        const match = lab.name.toLowerCase().includes(q) || lab.city.toLowerCase().includes(q) || lab.speciality.toLowerCase().includes(q);
        if (card) card.style.display = match ? 'block' : 'none';
      });
    }

    // ----- VOICE NARRATION -----
    function speakLabDetails(lab) {
      if (!voiceEnabled || !('speechSynthesis' in window)) return;
      window.speechSynthesis.cancel();

      const text = `${lab.name}. Located in ${lab.city}. Specialty: ${lab.speciality}. Monthly capacity: ${lab.tests_monthly} samples. NABL accredited.`;
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
      } else {
        window.speechSynthesis.cancel();
        btn.innerHTML = `<i class="bi bi-megaphone-mute-fill"></i> Voice Narration: OFF`;
        btn.classList.remove('active-voice');
      }
    }

    // Initialize Map
    initMap();

    window.addEventListener('resize', () => {
      if (map) map.invalidateSize();
    });

    // Particle Canvas Engine (100% Synchronized with Ceremony Suite)
    const canvas = document.getElementById('bgCanvas');
    const ctx = canvas.getContext('2d');
    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    const particles = [];
    const count = 75;
    for (let i=0; i<count; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random()-0.5)*0.4,
        vy: (Math.random()-0.5)*0.4,
        r: Math.random()*2 + 1
      });
    }

    function drawParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = 'rgba(245, 158, 11, 0.45)';
      ctx.strokeStyle = 'rgba(6, 182, 212, 0.12)';
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
