<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Lokasi Penyulang - PLN WebGIS</title>
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Palette */
            --brand-900: #0c3a47;
            --brand-800: #0f4c5c;
            --brand-700: #125d72;
            --brand-500: #14a2ba;
            --brand-400: #3fb8cc;
            --brand-100: #e6f6fa;

            --ink-900: #101b23;
            --ink-700: #33475a;
            --ink-500: #64748b;
            --ink-300: #cbd5e1;

            --surface: #ffffff;
            --canvas: #f3f6f9;
            --border: #e3e9ef;

            --success-bg: #e7f7ef;
            --success-fg: #0a7a4d;
            --success-line: #b6e8cf;
            --danger-bg: #fdeaea;
            --danger-fg: #c0281f;
            --danger-line: #f6c6c3;
            --warning-bg: #fef3e2;
            --warning-fg: #a2660a;
            --warning-line: #f7dfab;

            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;

            --shadow-sm: 0 1px 2px rgba(16, 27, 35, 0.06);
            --shadow-md: 0 6px 20px -6px rgba(16, 27, 35, 0.12);
            --shadow-lg: 0 16px 40px -12px rgba(16, 27, 35, 0.18);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--canvas);
            color: var(--ink-900);
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Navbar ===== */
        .navbar {
            background: linear-gradient(120deg, var(--brand-900) 0%, var(--brand-500) 130%);
            padding: 0.9rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 16px rgba(12, 58, 71, 0.25);
            position: sticky; top: 0; z-index: 500;
        }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; }
        .navbar-brand .logo-icon {
            width: 38px; height: 38px; background: #fff; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: var(--brand-700); font-size: 12px;
            letter-spacing: 0.02em;
            box-shadow: var(--shadow-sm);
        }
        .navbar-brand .brand-text h1 { color: #fff; font-size: 1.05rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.01em; }
        .navbar-brand .brand-text span { color: rgba(255,255,255,0.7); font-size: 0.72rem; font-weight: 500; letter-spacing: 0.02em; }
        .navbar-right { display: flex; align-items: center; gap: 1.25rem; }
        .navbar-nav { display: flex; gap: 0.25rem; }
        .navbar-nav a {
            color: rgba(255,255,255,0.72); text-decoration: none;
            padding: 0.45rem 0.9rem; border-radius: var(--radius-sm);
            font-size: 0.83rem; font-weight: 500; transition: background 0.15s ease, color 0.15s ease;
        }
        .navbar-nav a:hover { color: #fff; background: rgba(255,255,255,0.12); }
        .navbar-nav a.active { color: var(--brand-900); background: #fff; font-weight: 600; }
        .btn-logout {
            background: rgba(255,255,255,0.12); color: #fff;
            border: 1px solid rgba(255,255,255,0.28);
            padding: 0.42rem 1rem; border-radius: var(--radius-sm);
            font-size: 0.8rem; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: background 0.15s ease, border-color 0.15s ease;
            font-family: inherit;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.22); border-color: rgba(255,255,255,0.4); }

        /* ===== Layout ===== */
        .container { padding: 1.5rem 2rem 2rem; max-width: 1600px; margin: 0 auto; }

        .map-header {
            display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 1.1rem;
        }
        .map-header .title-block h2 { color: var(--ink-900); font-size: 1.4rem; font-weight: 800; letter-spacing: -0.015em; }
        .map-header .title-block p { color: var(--ink-500); font-size: 0.85rem; margin-top: 0.2rem; }
        .header-right { display: flex; align-items: center; gap: 0.75rem; }
        .badge {
            background: var(--brand-100); color: var(--brand-800);
            border: 1px solid #cdeef4;
            padding: 0.4rem 0.95rem; border-radius: 999px;
            font-size: 0.8rem; font-weight: 700; letter-spacing: 0.01em;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .badge::before {
            content: ''; width: 7px; height: 7px; border-radius: 50%;
            background: var(--brand-500); flex-shrink: 0;
        }

        /* ===== Filter card ===== */
        .filter-card {
            background: var(--surface); border-radius: var(--radius-lg); padding: 1.15rem 1.35rem;
            box-shadow: var(--shadow-sm); border: 1px solid var(--border);
            margin-bottom: 1.25rem;
        }
        .filter-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem;
        }
        .filter-header h3 {
            font-size: 0.82rem; font-weight: 700; color: var(--ink-900);
            display: flex; align-items: center; gap: 7px; letter-spacing: 0.01em;
        }
        .filter-header h3 svg { width: 15px; height: 15px; color: var(--brand-500); flex-shrink: 0; }
        .btn-reset {
            background: var(--canvas); color: var(--ink-500); border: 1px solid var(--border);
            padding: 0.35rem 0.85rem; border-radius: var(--radius-sm); font-size: 0.75rem;
            font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.15s ease;
            display: flex; align-items: center; gap: 5px;
        }
        .btn-reset svg { width: 12px; height: 12px; }
        .btn-reset:hover { background: #eef1f5; color: var(--ink-900); border-color: var(--ink-300); }

        .filter-grid {
            display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.85rem;
        }
        .filter-group label {
            display: block; font-size: 0.68rem; font-weight: 700; color: var(--ink-500);
            margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .filter-group .input-wrap { position: relative; }
        .filter-group .input-wrap svg {
            position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%);
            width: 14px; height: 14px; color: var(--ink-300); pointer-events: none;
        }
        .filter-group select,
        .filter-group input {
            width: 100%; padding: 0.55rem 0.65rem 0.55rem 2rem; border: 1.5px solid var(--border);
            border-radius: var(--radius-sm); font-size: 0.8rem; font-family: inherit;
            color: var(--ink-900); background: var(--canvas); transition: all 0.15s ease; outline: none;
            appearance: none;
        }
        .filter-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.7rem center;
            padding-right: 1.9rem;
        }
        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(20,162,186,0.12); background: #fff;
        }
        .filter-group select:hover,
        .filter-group input:hover { border-color: var(--ink-300); }

        /* ===== Map ===== */
        .map-wrapper {
            background: var(--surface); border-radius: var(--radius-lg); overflow: hidden;
            box-shadow: var(--shadow-md); border: 1px solid var(--border);
            position: relative;
        }
        #map-utama { height: 65vh; min-height: 460px; width: 100%; background: #eef2f5; }

        .leaflet-popup-content-wrapper { border-radius: var(--radius-md) !important; box-shadow: var(--shadow-lg) !important; padding: 2px !important; }
        .leaflet-popup-content { margin: 12px 14px !important; }
        .leaflet-popup-tip { box-shadow: var(--shadow-md) !important; }
        .leaflet-control-zoom { border-radius: var(--radius-sm) !important; overflow: hidden; box-shadow: var(--shadow-md) !important; border: none !important; }
        .leaflet-control-zoom a { color: var(--brand-800) !important; }

        .popup-card { min-width: 230px; }
        .popup-card h3 {
            color: var(--ink-900); font-size: 0.95rem; font-weight: 800; margin-bottom: 9px;
            padding-bottom: 9px; border-bottom: 1.5px solid var(--border); letter-spacing: -0.01em;
        }
        .popup-card .info-row {
            display: flex; align-items: center; justify-content: space-between;
            gap: 10px; margin-bottom: 6px; font-size: 0.8rem;
        }
        .popup-card .info-row .label { color: var(--ink-500); font-weight: 600; }
        .popup-card .info-row .value { color: var(--ink-900); font-weight: 600; text-align: right; }
        .popup-card .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 11px; border-radius: 999px; font-size: 0.72rem; font-weight: 700;
            margin-top: 6px; border: 1px solid transparent;
        }
        .popup-card .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .status-tervalidasi { background: var(--success-bg); color: var(--success-fg); border-color: var(--success-line); }
        .status-tervalidasi::before { background: var(--success-fg); }
        .status-gangguan { background: var(--danger-bg); color: var(--danger-fg); border-color: var(--danger-line); }
        .status-gangguan::before { background: var(--danger-fg); }
        .status-maintenance { background: var(--warning-bg); color: var(--warning-fg); border-color: var(--warning-line); }
        .status-maintenance::before { background: var(--warning-fg); }

        .legend {
            background: var(--surface); border-radius: var(--radius-md); padding: 0.9rem 1.05rem;
            box-shadow: var(--shadow-md); font-size: 0.78rem; line-height: 1.5;
            border: 1px solid var(--border);
        }
        .legend h4 {
            font-size: 0.72rem; color: var(--ink-500); margin-bottom: 7px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; color: var(--ink-700); font-weight: 500; padding: 2px 0; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.25); flex-shrink: 0; }

        /* ===== Loading overlay ===== */
        .loading-overlay {
            position: fixed; inset: 0; background: rgba(243, 246, 249, 0.92);
            backdrop-filter: blur(2px);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 10000; gap: 1rem;
        }
        .spinner {
            width: 38px; height: 38px; border: 3.5px solid var(--border);
            border-top-color: var(--brand-500); border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: var(--brand-800); font-weight: 600; font-size: 0.85rem; letter-spacing: 0.01em; }

        /* ===== Responsive ===== */
        @media (max-width: 1200px) {
            .filter-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 720px) {
            .navbar { padding: 0.75rem 1.1rem; flex-wrap: wrap; gap: 0.6rem; }
            .container { padding: 1rem; }
            .filter-grid { grid-template-columns: repeat(2, 1fr); }
            .map-header { flex-direction: column; align-items: flex-start; gap: 0.6rem; }
            #map-utama { height: 55vh; min-height: 380px; }
        }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Memuat data dari Google Spreadsheet…</div>
    </div>

    @include('partials.sidebar', ['active' => 'peta'])

    <div class="container">
        <div class="map-header">
            <div class="title-block">
                <h2>Peta Lokasi Penyulang PLN</h2>
                <p>Sebaran lokasi dan status gangguan penyulang secara real-time</p>
            </div>
            <div class="header-right">
                <div class="badge" id="countBadge">{{ count($penyulangs) }} Lokasi</div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter Data
                </h3>
                <button class="btn-reset" onclick="resetFilter()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    Reset Filter
                </button>
            </div>
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Nama Penyulang</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.5 8.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/><path d="M2 22l4.35-4.35"/></svg>
                        <select id="filterNama" onchange="applyFilter()"><option value="">Semua Penyulang</option></select>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Unit UP3</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M9 13h1M14 9h1M14 13h1"/></svg>
                        <select id="filterUp3" onchange="applyFilter()"><option value="">Semua UP3</option></select>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Unit ULP</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M9 13h1M14 9h1M14 13h1"/></svg>
                        <select id="filterUlp" onchange="applyFilter()"><option value="">Semua ULP</option></select>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Tanggal Padam</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <input type="date" id="filterTglPadam" onchange="applyFilter()">
                    </div>
                </div>
                <div class="filter-group">
                    <label>Jam Padam</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        <input type="time" id="filterJamPadam" onchange="applyFilter()">
                    </div>
                </div>
                <div class="filter-group">
                    <label>Jam Nyala</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        <input type="time" id="filterJamNyala" onchange="applyFilter()">
                    </div>
                </div>
            </div>
        </div>

        <div class="map-wrapper">
            <div id="map-utama"></div>
        </div>
    </div>

    <script>
        var penyulangData = {!! json_encode($penyulangs ?? [], JSON_HEX_TAG | JSON_HEX_AMP) !!};
        if (!Array.isArray(penyulangData)) penyulangData = [];

        var map = L.map('map-utama', { center: [2.5, 99.0], zoom: 7, zoomControl: true });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 18
        }).addTo(map);

        var redIcon = L.divIcon({
            className: 'custom-marker',
            html: '<svg width="28" height="40" viewBox="0 0 28 40" xmlns="http://www.w3.org/2000/svg"><path d="M14 0C6.3 0 0 6.3 0 14c0 9.8 14 26 14 26s14-16.2 14-26C28 6.3 21.7 0 14 0z" fill="#e74c3c" stroke="#c0392b" stroke-width="1.5"/><circle cx="14" cy="13" r="6" fill="#fff"/><text x="14" y="16.5" text-anchor="middle" fill="#e74c3c" font-size="9" font-weight="bold">P</text></svg>',
            iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40]
        });

        var statusColors = {
            'Tervalidasi': 'status-tervalidasi', 'Tervalidasi OCR': 'status-tervalidasi',
            'Tervalidasi (Otomatis)': 'status-tervalidasi',
            'Gangguan': 'status-gangguan', 'Maintenance': 'status-maintenance'
        };

        var allMarkers = [];

        function buildPopup(item) {
            var statusKey = (item.status || '').replace(/ \u2705/g, '').trim();
            var badgeClass = statusColors[statusKey] || '';
            return '<div class="popup-card">' +
                '<h3>' + item.nama_penyulang + '</h3>' +
                '<div class="info-row"><span class="label">UP3</span><span class="value">' + item.unit_up3 + '</span></div>' +
                '<div class="info-row"><span class="label">ULP</span><span class="value">' + item.unit_ulp + '</span></div>' +
                '<div class="info-row"><span class="label">Tgl Padam</span><span class="value">' + (item.tgl_padam || '-') + '</span></div>' +
                '<div class="info-row"><span class="label">Jam</span><span class="value">' + (item.jam_padam || '-') + ' - ' + (item.jam_nyala || '-') + '</span></div>' +
                '<div class="info-row"><span class="label">Lama</span><span class="value">' + (item.lama_padam || '-') + '</span></div>' +
                '<div class="info-row"><span class="label">Status</span><span class="status-badge ' + badgeClass + '">' + (item.status || '-') + '</span></div>' +
                '</div>';
        }

        penyulangData.forEach(function(item) {
            if (item.latitude != null && item.longitude != null) {
                var marker = L.marker([item.latitude, item.longitude], { icon: redIcon })
                    .addTo(map).bindPopup(buildPopup(item));
                allMarkers.push({ marker: marker, data: item });
            }
        });

        function populateFilters() {
            var namaSet = {}, up3Set = {}, ulpSet = {};
            penyulangData.forEach(function(item) {
                if (item.nama_penyulang) namaSet[item.nama_penyulang] = true;
                if (item.unit_up3) up3Set[item.unit_up3] = true;
                if (item.unit_ulp) ulpSet[item.unit_ulp] = true;
            });
            var selNama = document.getElementById('filterNama');
            Object.keys(namaSet).sort().forEach(function(v) {
                var opt = document.createElement('option'); opt.value = v; opt.textContent = v; selNama.appendChild(opt);
            });
            var selUp3 = document.getElementById('filterUp3');
            Object.keys(up3Set).sort().forEach(function(v) {
                var opt = document.createElement('option'); opt.value = v; opt.textContent = v; selUp3.appendChild(opt);
            });
            var selUlp = document.getElementById('filterUlp');
            Object.keys(ulpSet).sort().forEach(function(v) {
                var opt = document.createElement('option'); opt.value = v; opt.textContent = v; selUlp.appendChild(opt);
            });
        }

        function applyFilter() {
            var nama = document.getElementById('filterNama').value;
            var up3 = document.getElementById('filterUp3').value;
            var ulp = document.getElementById('filterUlp').value;
            var tglPadam = document.getElementById('filterTglPadam').value;
            var jamPadam = document.getElementById('filterJamPadam').value;
            var jamNyala = document.getElementById('filterJamNyala').value;

            var count = 0;
            allMarkers.forEach(function(obj) {
                var d = obj.data;
                var show = true;

                if (nama && d.nama_penyulang !== nama) show = false;
                if (up3 && d.unit_up3 !== up3) show = false;
                if (ulp && d.unit_ulp !== ulp) show = false;
                if (tglPadam && d.tgl_padam !== tglPadam) show = false;
                if (jamPadam && (d.jam_padam || '').substring(0,5) !== jamPadam) show = false;
                if (jamNyala && (d.jam_nyala || '').substring(0,5) !== jamNyala) show = false;

                if (show) {
                    obj.marker.addTo(map);
                    count++;
                } else {
                    map.removeLayer(obj.marker);
                }
            });

            document.getElementById('countBadge').textContent = count + ' Lokasi';
        }

        function resetFilter() {
            document.getElementById('filterNama').value = '';
            document.getElementById('filterUp3').value = '';
            document.getElementById('filterUlp').value = '';
            document.getElementById('filterTglPadam').value = '';
            document.getElementById('filterJamPadam').value = '';
            document.getElementById('filterJamNyala').value = '';
            applyFilter();
        }

        populateFilters();

        var legend = L.control({ position: 'bottomright' });
        legend.onAdd = function() {
            var div = L.DomUtil.create('div', 'legend');
            div.innerHTML = '<h4>Legenda</h4>' +
                '<div class="legend-item"><div class="legend-dot" style="background:#059669;"></div> Tervalidasi</div>' +
                '<div class="legend-item"><div class="legend-dot" style="background:#e74c3c;"></div> Gangguan</div>' +
                '<div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div> Maintenance</div>';
            return div;
        };
        legend.addTo(map);

        var overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    </script>
</body>
</html>