<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen ROW - PLN SCADA</title>
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('vendor/leaflet/leaflet-heat.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #081c24;
            --bg-mid: #0b2530;
            --bg-glow: #0e3a47;
            --accent: #14a2ba;
            --accent-light: #5fd3e6;
            --accent-dim: #0d5d70;

            --glass-fill: rgba(255,255,255,0.055);
            --glass-fill-strong: rgba(255,255,255,0.09);
            --glass-border: rgba(255,255,255,0.13);
            --glass-border-strong: rgba(255,255,255,0.22);

            --text-primary: #f1f6f8;
            --text-secondary: #a9c2cb;
            --text-muted: #6f8890;

            --safe: #10b981;
            --safe-fg: #6ee7b7;
            --watch: #f59e0b;
            --watch-fg: #fcd34d;
            --critical: #ef4444;
            --critical-fg: #fca5a5;
            --tower: #3b82f6;
            --route: #8b5cf6;

            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 9px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(1100px 600px at 12% -10%, rgba(20,162,186,0.16), transparent 60%),
                radial-gradient(900px 500px at 100% 0%, rgba(139,92,246,0.09), transparent 55%),
                linear-gradient(160deg, var(--bg-deep) 0%, var(--bg-glow) 55%, var(--bg-deep) 100%);
            background-attachment: fixed;
            min-height: 100vh; color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }
        .mono { font-family: 'JetBrains Mono', monospace; }

        .glass {
            background: var(--glass-fill);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
        }

        /* ===== NAVBAR ===== */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.8rem 1.75rem; margin: 1rem 1.5rem 0;
            position: sticky; top: 1rem; z-index: 900;
        }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .logo-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dim));
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: #fff; font-size: 12px;
            box-shadow: 0 4px 14px rgba(20,162,186,0.35);
        }
        .navbar-brand h1 { font-size: 1.02rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
        .navbar-brand span { font-size: 0.7rem; color: var(--accent-light); font-weight: 500; letter-spacing: 0.02em; }
        .navbar-right { display: flex; align-items: center; gap: 1.1rem; }
        .navbar-nav { display: flex; gap: 0.25rem; }
        .navbar-nav a {
            color: var(--text-secondary); text-decoration: none;
            padding: 0.45rem 0.9rem; border-radius: var(--radius-sm);
            font-size: 0.81rem; font-weight: 500; transition: all 0.15s ease;
        }
        .navbar-nav a:hover { color: #fff; background: var(--glass-fill-strong); }
        .navbar-nav a.active { color: #fff; background: var(--glass-fill-strong); border: 1px solid var(--glass-border-strong); }
        .btn-logout {
            background: var(--glass-fill-strong); color: #fff;
            border: 1px solid var(--glass-border-strong);
            padding: 0.4rem 1rem; border-radius: var(--radius-sm);
            font-size: 0.78rem; font-weight: 600; cursor: pointer;
            font-family: inherit; transition: all 0.15s ease;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.18); }

        /* ===== LAYOUT ===== */
        .container { padding: 1.35rem 1.5rem 1.5rem; max-width: 1700px; margin: 0 auto; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .page-header h2 { font-size: 1.32rem; font-weight: 800; color: #fff; letter-spacing: -0.015em; }
        .page-header h2 span { color: var(--accent-light); }
        .subtitle { font-size: 0.8rem; color: var(--text-secondary); margin-top: 3px; }

        /* ===== TOOLBAR ===== */
        .toolbar {
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
            padding: 0.85rem 1.1rem; margin-bottom: 1rem;
        }
        .toolbar .filter-block { display: flex; align-items: center; gap: 0.6rem; }
        .toolbar .lbl {
            font-size: 0.7rem; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
        }
        .select-wrap { position: relative; }
        .select-wrap svg {
            position: absolute; right: 0.7rem; top: 50%; transform: translateY(-50%);
            width: 10px; height: 10px; color: var(--text-secondary); pointer-events: none;
        }
        .toolbar select {
            background: var(--glass-fill-strong); color: #fff;
            border: 1px solid var(--glass-border-strong);
            padding: 0.5rem 2rem 0.5rem 0.9rem; border-radius: var(--radius-sm);
            font-size: 0.8rem; font-family: inherit; outline: none; appearance: none;
            min-width: 180px; transition: border-color 0.15s ease, background 0.15s ease;
        }
        .toolbar select option { background: #0e3a47; color: #fff; }
        .toolbar select:hover { background: rgba(255,255,255,0.13); }
        .toolbar select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(20,162,186,0.18); }

        .mode-group { display: flex; gap: 0.4rem; margin-left: auto; background: rgba(0,0,0,0.16); padding: 4px; border-radius: calc(var(--radius-sm) + 4px); border: 1px solid var(--glass-border); }
        .mode-btn {
            background: transparent; color: var(--text-secondary);
            border: none;
            padding: 0.45rem 1rem; border-radius: var(--radius-sm);
            font-size: 0.78rem; font-weight: 600; cursor: pointer;
            font-family: inherit; transition: all 0.15s ease;
        }
        .mode-btn:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .mode-btn.active {
            background: linear-gradient(135deg, var(--accent), var(--accent-dim));
            color: #fff;
            box-shadow: 0 4px 14px rgba(20,162,186,0.4);
        }

        /* ===== MAP + PANEL ===== */
        .row-layout { display: grid; grid-template-columns: 1fr 300px; gap: 1rem; align-items: start; }
        .map-frame { padding: 0; overflow: hidden; position: relative; }
        #rowMap { height: calc(100vh - 235px); min-height: 480px; border-radius: var(--radius-lg); z-index: 0; }

        .map-legend {
            position: absolute; left: 14px; bottom: 18px; z-index: 800;
            padding: 0.75rem 0.95rem; font-size: 0.72rem; min-width: 172px;
        }
        .map-legend .lg-title { font-weight: 700; color: #fff; margin-bottom: 0.5rem; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em; }
        .lg-item { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; color: var(--text-secondary); }
        .lg-item:last-child { margin-bottom: 0; }
        .lg-dot { width: 11px; height: 11px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.4); flex-shrink: 0; box-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        .lg-line { width: 18px; height: 3px; border-radius: 2px; flex-shrink: 0; }

        .map-badge {
            position: absolute; top: 14px; right: 14px; z-index: 800;
            padding: 0.4rem 0.8rem; font-size: 0.72rem; font-weight: 600; color: var(--text-secondary);
            display: flex; align-items: center; gap: 6px;
        }
        .map-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--safe); box-shadow: 0 0 6px var(--safe); }

        /* ===== SIDEBAR STATS ===== */
        .stats-panel { padding: 1.1rem 1.15rem; }
        .stats-title {
            font-size: 0.78rem; font-weight: 700; color: #fff; margin-bottom: 0.9rem;
            display: flex; align-items: center; justify-content: space-between;
            text-transform: uppercase; letter-spacing: 0.03em;
        }
        .stats-title .count-chip {
            font-size: 0.68rem; color: var(--accent-light); font-weight: 700;
            background: rgba(20,162,186,0.14); border: 1px solid rgba(20,162,186,0.3);
            padding: 3px 9px; border-radius: 999px; text-transform: none; letter-spacing: 0;
        }
        .stat-row { display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .stat-row:last-child { border-bottom: none; }
        .stat-row .k { display: flex; align-items: center; gap: 9px; font-size: 0.81rem; color: var(--text-secondary); font-weight: 500; }
        .stat-row .v { font-size: 0.95rem; font-weight: 700; color: #fff; font-family: 'JetBrains Mono', monospace; }
        .pulse-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

        .info-banner { margin-top: 1.1rem; padding: 0.9rem 1rem; font-size: 0.78rem; line-height: 1.55; color: var(--text-secondary); }
        .info-banner b { color: var(--accent-light); font-weight: 700; display: block; margin-bottom: 2px; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .info-banner .row-status-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 10px; }
        .row-status-box { text-align: center; padding: 0.6rem 0.3rem; border-radius: var(--radius-sm); font-size: 0.66rem; font-weight: 700; line-height: 1.4; }
        .box-aman { background: rgba(16,185,129,0.14); color: var(--safe-fg); border: 1px solid rgba(16,185,129,0.32); }
        .box-pantau { background: rgba(245,158,11,0.14); color: var(--watch-fg); border: 1px solid rgba(245,158,11,0.32); }
        .box-kritis { background: rgba(239,68,68,0.14); color: var(--critical-fg); border: 1px solid rgba(239,68,68,0.32); }

        /* ===== LOADING ===== */
        .loading-overlay {
            position: fixed; inset: 0; background: rgba(8,28,36,0.94);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 10000; gap: 1rem; backdrop-filter: blur(6px);
        }
        .spinner {
            width: 40px; height: 40px; border: 3.5px solid rgba(255,255,255,0.12);
            border-top-color: var(--accent); border-radius: 50%; animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: var(--accent-light); font-weight: 600; font-size: 0.85rem; letter-spacing: 0.01em; }

        /* ===== LEAFLET OVERRIDES ===== */
        .leaflet-control-zoom { border-radius: var(--radius-sm) !important; overflow: hidden; border: none !important; box-shadow: 0 6px 20px rgba(0,0,0,0.35) !important; }
        .leaflet-control-zoom a { background: rgba(14,42,53,0.92) !important; color: #fff !important; border-color: rgba(255,255,255,0.1) !important; }
        .leaflet-control-zoom a:hover { background: var(--accent-dim) !important; }
        .leaflet-control-attribution { background: rgba(8,28,36,0.75) !important; color: var(--text-muted) !important; border-radius: 6px 0 0 0; }
        .leaflet-control-attribution a { color: var(--accent-light) !important; }

        .popup-box { font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif; min-width: 150px; }
        .popup-box .pb-nama { font-weight: 700; color: #125d72; font-size: 0.84rem; }
        .popup-box .pb-meta { font-size: 0.73rem; color: #475569; margin-top: 4px; line-height: 1.5; }
        .popup-box .pb-status {
            display: inline-flex; align-items: center; gap: 4px;
            margin-top: 6px; padding: 3px 10px; border-radius: 999px; font-size: 0.68rem; font-weight: 700; color: #fff;
        }
        .leaflet-popup-content-wrapper { border-radius: var(--radius-sm) !important; box-shadow: 0 12px 30px rgba(0,0,0,0.3) !important; }

        @media (max-width: 900px) {
            .navbar { margin: 0.75rem 0.75rem 0; padding: 0.7rem 1rem; }
            .container { padding: 1rem; }
            .row-layout { grid-template-columns: 1fr; }
            #rowMap { height: 62vh; min-height: 420px; }
            .mode-group { margin-left: 0; width: 100%; justify-content: space-between; }
            .toolbar { align-items: stretch; }
            .toolbar select { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Memuat Data ROW…</div>
    </div>

    @include('partials.sidebar', ['active' => 'manajemen-row'])

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Manajemen <span>ROW</span> (Right of Way)</h2>
                <div class="subtitle">Pemantauan jarak aman vegetasi terhadap jaringan listrik penyulang</div>
            </div>
        </div>

        <div class="toolbar glass">
            <div class="filter-block">
                <span class="lbl">Kode Penyulang</span>
                <div class="select-wrap">
                    <select id="filterKode" onchange="applyFilter()">
                        <option value="">Semua Penyulang</option>
                    </select>
                    <svg viewBox="0 0 10 6" fill="none"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <div class="mode-group">
                <button class="mode-btn active" data-mode="titik" onclick="setMode('titik')">Titik</button>
                <button class="mode-btn" data-mode="heat" onclick="setMode('heat')">Heatmap Kritis</button>
                <button class="mode-btn" data-mode="rute" onclick="setMode('rute')">Rute Jalur</button>
            </div>
        </div>

        <div class="row-layout">
            <div class="map-frame glass">
                <div id="rowMap"></div>
                <div class="map-badge glass"><span class="dot"></span> Live</div>
                <div class="map-legend glass">
                    <div class="lg-title">Legenda</div>
                    <div class="lg-item"><span class="lg-dot" style="background:#3b82f6"></span> Tiang Penyulang</div>
                    <div class="lg-item"><span class="lg-dot" style="background:#10b981"></span> Vegetasi Aman (&gt;1.5 m)</div>
                    <div class="lg-item"><span class="lg-dot" style="background:#f59e0b"></span> Vegetasi Pantau (0.5–1.5 m)</div>
                    <div class="lg-item"><span class="lg-dot" style="background:#ef4444"></span> Vegetasi Kritis (&lt;0.5 m)</div>
                    <div class="lg-item"><span class="lg-line" style="background:#8b5cf6"></span> Rute Jalur (Polyline)</div>
                </div>
            </div>

            <div class="stats-panel glass">
                <div class="stats-title">Ringkasan Inventaris <span class="count-chip" id="totalChip">0 item</span></div>
                <div class="stat-row"><div class="k"><span class="pulse-dot" style="background:#3b82f6"></span> Total Tiang</div><div class="v" id="statTiang">0</div></div>
                <div class="stat-row"><div class="k"><span class="pulse-dot" style="background:#34d399"></span> Total Pohon</div><div class="v" id="statPohon">0</div></div>
                <div class="stat-row"><div class="k"><span class="pulse-dot" style="background:#f59e0b"></span> Pantau</div><div class="v" id="statPantau">0</div></div>
                <div class="stat-row"><div class="k"><span class="pulse-dot" style="background:#ef4444"></span> Kritis</div><div class="v" id="statKritis">0</div></div>
                <div class="stat-row"><div class="k"><span class="pulse-dot" style="background:#10b981"></span> Aman</div><div class="v" id="statAman">0</div></div>

                <div class="info-banner glass">
                    <b>Klasifikasi Status Vegetasi</b>
                    <div class="row-status-grid">
                        <div class="row-status-box box-aman">AMAN<br>&gt; 1.5 m</div>
                        <div class="row-status-box box-pantau">PANTAU<br>0.5–1.5 m</div>
                        <div class="row-status-box box-kritis">KRITIS<br>&lt; 0.5 m</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var rows = {!! count($rows) ? json_encode($rows, JSON_UNESCAPED_UNICODE) : '[]' !!};

        var SAMPLE = [
            { id:'tiang-gh1', jenis:'tiang', nama:'Tiang GH1', kode:'GH1', latitude:3.5952, longitude:98.6722, jarak_m:0, status:'Aman' },
            { id:'pohon-1', jenis:'pohon', nama:'Pohon #1', kode:'GH1', latitude:3.5957, longitude:98.6729, jarak_m:0.35, status:'Kritis' },
            { id:'pohon-2', jenis:'pohon', nama:'Pohon #2', kode:'GH1', latitude:3.5946, longitude:98.6718, jarak_m:1.1, status:'Pantau' },
            { id:'pohon-3', jenis:'pohon', nama:'Pohon #3', kode:'GH1', latitude:3.5959, longitude:98.6720, jarak_m:2.3, status:'Aman' },
            { id:'pohon-4', jenis:'pohon', nama:'Pohon #4', kode:'GH1', latitude:3.5944, longitude:98.6725, jarak_m:0.45, status:'Kritis' },
            { id:'tiang-gh2', jenis:'tiang', nama:'Tiang GH2', kode:'GH2', latitude:3.6099, longitude:98.6543, jarak_m:0, status:'Aman' },
            { id:'pohon-5', jenis:'pohon', nama:'Pohon #5', kode:'GH2', latitude:3.6105, longitude:98.6548, jarak_m:0.3, status:'Kritis' },
            { id:'pohon-6', jenis:'pohon', nama:'Pohon #6', kode:'GH2', latitude:3.6093, longitude:98.6537, jarak_m:1.8, status:'Aman' },
            { id:'pohon-7', jenis:'pohon', nama:'Pohon #7', kode:'GH2', latitude:3.6102, longitude:98.6551, jarak_m:0.8, status:'Pantau' },
            { id:'tiang-gh3', jenis:'tiang', nama:'Tiang GH3', kode:'GH3', latitude:3.5811, longitude:98.6912, jarak_m:0, status:'Aman' },
            { id:'pohon-8', jenis:'pohon', nama:'Pohon #8', kode:'GH3', latitude:3.5817, longitude:98.6919, jarak_m:0.4, status:'Kritis' },
            { id:'pohon-9', jenis:'pohon', nama:'Pohon #9', kode:'GH3', latitude:3.5805, longitude:98.6906, jarak_m:1.4, status:'Pantau' },
            { id:'pohon-10', jenis:'pohon', nama:'Pohon #10', kode:'GH3', latitude:3.5819, longitude:98.6908, jarak_m:2.6, status:'Aman' }
        ];

        if (rows.length === 0) rows = SAMPLE;

        var map = L.map('rowMap').setView([3.5952, 98.6722], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
        }).addTo(map);

        var tiangLayer = L.layerGroup().addTo(map);
        var pohonLayer = L.layerGroup().addTo(map);
        var heatGroup = L.layerGroup();
        var routeLayer = L.layerGroup();
        var currentMode = 'titik';
        var heatLayer = null;

        var towerIcon = L.divIcon({
            className: 'row-tower-icon',
            html: '<svg width="26" height="34" viewBox="0 0 26 34" xmlns="http://www.w3.org/2000/svg"><path d="M13 2 L4 12 L7 15 L5 17 L7 19 L4 22 L8 32 L13 32 L12 22 L11 19 L13 17 L15 19 L14 22 L18 32 L22 22 L19 19 L21 17 L19 15 L22 12 L13 2 Z" fill="#3b82f6" stroke="#1d4ed8" stroke-width="1"/><circle cx="13" cy="7" r="2.5" fill="#fff"/></svg>',
            iconSize: [26, 34], iconAnchor: [13, 32], popupAnchor: [0, -32]
        });

        function statusColor(s) {
            if (s === 'Kritis') return '#ef4444';
            if (s === 'Pantau') return '#f59e0b';
            return '#10b981';
        }
        function statusBg(s) {
            if (s === 'Kritis') return '#ef4444';
            if (s === 'Pantau') return '#d97706';
            return '#059669';
        }

        function renderMap() {
            tiangLayer.clearLayers();
            pohonLayer.clearLayers();
            heatGroup.clearLayers();
            routeLayer.clearLayers();

            var kode = document.getElementById('filterKode').value;
            var tiangByKode = {};
            var heatPoints = [];
            var stats = { tiang: 0, pohon: 0, Aman: 0, Pantau: 0, Kritis: 0 };
            var bounds = [];

            rows.forEach(function(r) {
                if (kode && r.kode !== kode) return;
                bounds.push([r.latitude, r.longitude]);

                if (r.jenis === 'tiang') {
                    stats.tiang++;
                    var m = L.marker([r.latitude, r.longitude], { icon: towerIcon, title: r.nama });
                    m.bindPopup('<div class="popup-box"><div class="pb-nama">' + r.nama + '</div>' +
                        '<div class="pb-meta">Kode: ' + r.kode + '<br>Lat: ' + r.latitude + ' | Lng: ' + r.longitude + '</div>' +
                        '<span class="pb-status" style="background:' + statusBg(r.status) + '">' + r.status + '</span></div>');
                    tiangLayer.addLayer(m);
                    if (!tiangByKode[r.kode]) tiangByKode[r.kode] = [];
                    tiangByKode[r.kode].push([r.latitude, r.longitude]);
                } else {
                    stats.pohon++;
                    stats[r.status] = (stats[r.status] || 0) + 1;
                    var c = L.circleMarker([r.latitude, r.longitude], {
                        radius: 8, color: '#fff', weight: 1.5,
                        fillColor: statusColor(r.status), fillOpacity: 0.95
                    });
                    c.bindPopup('<div class="popup-box"><div class="pb-nama">' + r.nama + '</div>' +
                        '<div class="pb-meta">Kode: ' + r.kode + '<br>Jarak aman: <b>' + r.jarak_m + ' m</b></div>' +
                        '<span class="pb-status" style="background:' + statusBg(r.status) + '">' + r.status + '</span></div>');
                    pohonLayer.addLayer(c);

                    if (r.status === 'Kritis') {
                        var intensity = Math.max(0.3, 1 - Math.min(r.jarak_m / 0.5, 1));
                        heatPoints.push([r.latitude, r.longitude, intensity]);
                    }
                }
            });

            if (heatPoints.length) {
                if (typeof L.heatLayer === 'function') {
                    heatLayer = L.heatLayer(heatPoints, {
                        radius: 30, blur: 22, maxZoom: 16, max: 1,
                        gradient: { 0.3: '#fbbf24', 0.6: '#f97316', 1: '#ef4444' }
                    });
                } else {
                    heatLayer = L.layerGroup(heatPoints.map(function(p) {
                        return L.circle([p[0], p[1]], {
                            radius: 90, color: null, fillColor: '#ef4444', fillOpacity: 0.14
                        });
                    }));
                }
                heatGroup.addLayer(heatLayer);
            }

            var palette = ['#8b5cf6', '#f59e0b', '#14a2ba', '#10b981', '#ef4444', '#3b82f6', '#ec4899'];
            var i = 0;
            Object.keys(tiangByKode).forEach(function(k) {
                var pts = tiangByKode[k];
                if (pts.length < 2) {
                    routeLayer.addLayer(L.circleMarker(pts[0], { radius: 5, color: '#8b5cf6', fillColor: '#8b5cf6', fillOpacity: 0.9 }));
                } else {
                    routeLayer.addLayer(L.polyline(pts, {
                        color: palette[i % palette.length], weight: 4,
                        opacity: 0.85, dashArray: '8 6'
                    }));
                    routeLayer.addLayer(L.marker(pts[0], { icon: towerIcon }));
                    routeLayer.addLayer(L.marker(pts[pts.length - 1], { icon: towerIcon }));
                }
                i++;
            });

            document.getElementById('statTiang').textContent = stats.tiang;
            document.getElementById('statPohon').textContent = stats.pohon;
            document.getElementById('statPantau').textContent = stats.Pantau || 0;
            document.getElementById('statKritis').textContent = stats.Kritis || 0;
            document.getElementById('statAman').textContent = stats.Aman || 0;
            document.getElementById('totalChip').textContent = (stats.tiang + stats.pohon) + ' item';

            if (kode && bounds.length) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }
        }

        function setMode(mode) {
            currentMode = mode;
            document.querySelectorAll('.mode-btn').forEach(function(b) {
                b.classList.toggle('active', b.dataset.mode === mode);
            });
            map.removeLayer(tiangLayer);
            map.removeLayer(pohonLayer);
            map.removeLayer(heatGroup);
            map.removeLayer(routeLayer);

            if (mode === 'titik') {
                map.addLayer(tiangLayer);
                map.addLayer(pohonLayer);
            } else if (mode === 'heat') {
                map.addLayer(heatGroup);
            } else if (mode === 'rute') {
                map.addLayer(tiangLayer);
                map.addLayer(routeLayer);
            }
        }

        function applyFilter() {
            renderMap();
            setMode(currentMode);
        }

        (function populate() {
            var seen = {};
            var sel = document.getElementById('filterKode');
            rows.forEach(function(r) {
                if (r.kode && !seen[r.kode]) {
                    seen[r.kode] = true;
                    var opt = document.createElement('option');
                    opt.value = r.kode; opt.textContent = r.kode;
                    sel.appendChild(opt);
                }
            });
        })();

        renderMap();

        var overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    </script>
</body>
</html>