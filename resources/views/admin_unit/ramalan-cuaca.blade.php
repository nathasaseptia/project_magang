<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramalan Cuaca &amp; Atmosfer - PLN SCADA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(160deg, #0b2530 0%, #0e3a47 55%, #0b2530 100%);
            min-height: 100vh; color: #e2e8f0;
        }
        .glass {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
        }

        /* NAVBAR */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.85rem 2rem; margin: 1rem 1.5rem 0;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .logo-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, #14a2ba, #125d72);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: #fff; font-size: 13px;
        }
        .navbar-brand h1 { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .navbar-brand span { font-size: 0.72rem; color: #7dd3e3; }
        .navbar-right { display: flex; align-items: center; gap: 1rem; }
        .navbar-nav { display: flex; gap: 0.35rem; }
        .navbar-nav a {
            color: rgba(255,255,255,0.7); text-decoration: none;
            padding: 0.45rem 0.9rem; border-radius: 10px;
            font-size: 0.82rem; font-weight: 500; transition: all 0.2s;
        }
        .navbar-nav a:hover, .navbar-nav a.active {
            color: #fff; background: rgba(255,255,255,0.15);
        }
        .btn-logout {
            background: rgba(255,255,255,0.12); color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.4rem 1rem; border-radius: 10px;
            font-size: 0.78rem; font-weight: 500; cursor: pointer;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; transition: all 0.2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.22); }

        .container { padding: 1.25rem 1.5rem; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .page-header h2 { font-size: 1.3rem; font-weight: 700; color: #fff; }
        .page-header h2 span { color: #14a2ba; }
        .subtitle { font-size: 0.78rem; color: #94a3b8; margin-top: 2px; }
        .loc-chip {
            display: flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,0.09); border: 1px solid rgba(255,255,255,0.16);
            padding: 0.4rem 0.85rem; border-radius: 30px; font-size: 0.74rem; font-weight: 600;
        }
        .loc-dot { width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; }
        .loc-dot.ok { background: #10b981; }

        /* LAYOUT PETA */
        .weather-layout { display: grid; grid-template-columns: 1fr 320px; gap: 1rem; align-items: stretch; }
        .map-wrap { position: relative; overflow: hidden; border-radius: 16px; min-height: 540px; }
        .windy-frame { width: 100%; height: 100%; min-height: 540px; border: none; display: block; border-radius: 16px; }

        .map-controls {
            position: absolute; top: 14px; right: 14px; z-index: 700;
            display: flex; flex-direction: column; gap: 8px;
        }
        .map-btn {
            background: rgba(11,37,48,0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.22);
            color: #fff; padding: 0.55rem 0.9rem; border-radius: 12px;
            font-size: 0.74rem; font-weight: 600; cursor: pointer;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; transition: all 0.2s;
            display: flex; align-items: center; gap: 7px; white-space: nowrap;
        }
        .map-btn:hover { background: rgba(20,162,186,0.7); border-color: #14a2ba; }

        /* AI FLOAT BOX */
        .ai-float {
            position: absolute; top: 14px; left: 14px; z-index: 700;
            max-width: 320px; padding: 0.85rem 1rem; border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            background: rgba(11,37,48,0.7);
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
        }
        .ai-float .ai-kicker { font-size: 0.66rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
        .ai-float .ai-title { font-size: 0.95rem; font-weight: 800; margin-bottom: 3px; }
        .ai-float .ai-desc { font-size: 0.74rem; line-height: 1.5; color: #cbd5e1; }
        .ai-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 30px; font-size: 0.72rem; font-weight: 800;
        }
        .badge-danger { background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid #ef4444; }
        .badge-warn { background: rgba(245,158,11,0.22); color: #fcd34d; border: 1px solid #f59e0b; }
        .badge-safe { background: rgba(16,185,129,0.22); color: #6ee7b7; border: 1px solid #10b981; }

        /* SIDE PANEL */
        .data-panel { padding: 1rem 1.1rem; overflow-y: auto; }
        .data-panel.hidden { display: none; }
        .panel-title { font-size: 0.8rem; font-weight: 700; color: #fff; margin-bottom: 0.8rem; display: flex; align-items: center; justify-content: space-between; }
        .panel-title .refresh-chip { font-size: 0.68rem; color: #7dd3e3; font-weight: 600; }
        .big-temp { display: flex; align-items: center; gap: 1rem; padding: 0.8rem; border-radius: 14px; background: rgba(255,255,255,0.06); margin-bottom: 0.9rem; }
        .big-temp .temp-val { font-size: 2.4rem; font-weight: 800; color: #fff; line-height: 1; }
        .big-temp .temp-meta { font-size: 0.72rem; color: #94a3b8; }
        .big-temp .temp-desc { font-size: 0.82rem; font-weight: 600; color: #7dd3e3; margin-top: 2px; }
        .panel-row { display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid rgba(255,255,255,0.08); font-size: 0.78rem; }
        .panel-row:last-child { border-bottom: none; }
        .panel-row .k { color: #94a3b8; }
        .panel-row .v { font-weight: 700; color: #fff; }
        .mini-bar { height: 5px; border-radius: 3px; background: rgba(255,255,255,0.1); overflow: hidden; margin-top: 4px; }
        .mini-bar .fill { height: 100%; border-radius: 3px; transition: width 0.8s ease; }

        /* AI BELOW MAP */
        .ai-card { padding: 1rem 1.2rem; margin-top: 1rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .ai-card .ai-badge { font-size: 0.85rem; padding: 6px 16px; }
        .ai-card .ai-msg { font-size: 0.84rem; color: #cbd5e1; flex: 1; min-width: 240px; }
        .ai-card .ai-msg b { color: #fff; }

        /* CARDS GRID */
        .cards-title { margin: 1.2rem 0 0.7rem; font-size: 0.85rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px; }
        .cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem; margin-bottom: 1.5rem; }
        .wc-card { padding: 0.95rem 1rem; }
        .wc-card .c-head { display: flex; align-items: center; gap: 8px; margin-bottom: 0.5rem; }
        .wc-card .c-head svg { width: 18px; height: 18px; color: #14a2ba; flex-shrink: 0; }
        .wc-card .c-label { font-size: 0.68rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }
        .wc-card .c-value { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 2px 0 6px; }
        .wc-card .c-value small { font-size: 0.72rem; font-weight: 600; color: #94a3b8; }
        .c-bar { height: 7px; border-radius: 4px; background: rgba(255,255,255,0.1); overflow: hidden; margin-bottom: 7px; }
        .c-bar .fill { height: 100%; border-radius: 4px; transition: width 0.8s ease; }
        .c-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.66rem; font-weight: 700; }

        .status-line { margin-top: 1rem; text-align: center; font-size: 0.78rem; color: #94a3b8; padding-bottom: 1.2rem; }

        .loading-overlay {
            position: fixed; inset: 0; background: rgba(11,37,48,0.9);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 10000; gap: 1rem; backdrop-filter: blur(6px);
        }
        .spinner {
            width: 42px; height: 42px; border: 4px solid rgba(255,255,255,0.15);
            border-top-color: #14a2ba; border-radius: 50%; animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: #7dd3e3; font-weight: 600; font-size: 0.9rem; }

        @media (max-width: 1100px) { .cards-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 860px) {
            .weather-layout { grid-template-columns: 1fr; }
            .data-panel { max-height: 340px; }
        }
        @media (max-width: 560px) { .cards-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Menghubungkan Windy Engine &amp; Geolokasi...</div>
    </div>

    @include('partials.sidebar', ['active' => 'ramalan-cuaca'])

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Ramalan Cuaca &amp; <span>Atmosfer</span> (Windy Engine)</h2>
                <div class="subtitle">Pemantauan cuaca real-time untuk keselamatan regu penebang pohon ROW</div>
            </div>
            <div class="loc-chip" id="locChip">
                <span class="loc-dot" id="locDot"></span>
                <span id="locText">Mendeteksi lokasi...</span>
            </div>
        </div>

        <div class="weather-layout">
            <div class="map-wrap glass" id="mapWrap">
                <iframe id="windyFrame" class="windy-frame" allowfullscreen title="Windy Weather"></iframe>

                <div class="ai-float" id="aiFloat">
                    <div class="ai-kicker">AI Sistem Pakar K3</div>
                    <div class="ai-badge badge-safe" id="aiFloatBadge">Menunggu Data</div>
                    <div class="ai-title" id="aiFloatTitle" style="color:#fff;">Menganalisis cuaca...</div>
                    <div class="ai-desc" id="aiFloatDesc">Memuat data atmosfer dari Open-Meteo.</div>
                </div>

                <div class="map-controls">
                    <button class="map-btn" onclick="togglePanel()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        <span id="panelBtnText">Sembunyikan Panel</span>
                    </button>
                    <button class="map-btn" onclick="toggleFullscreen()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M21 8V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3M16 21h3a2 2 0 0 0 2-2v-3"/></svg>
                        <span id="fsBtnText">Layar Penuh</span>
                    </button>
                    <button class="map-btn" onclick="reloadWeather()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        <span>Muat Ulang</span>
                    </button>
                </div>
            </div>

            <aside class="data-panel glass" id="dataPanel">
                <div class="panel-title">Kondisi Saat Ini <span class="refresh-chip" id="updateTime">-</span></div>
                <div class="big-temp">
                    <div>
                        <div class="temp-val" id="ptTemp">--</div>
                        <div class="temp-meta" id="ptFeels">Terasa seperti --</div>
                    </div>
                    <div style="flex:1;">
                        <div class="temp-desc" id="ptDesc">Mendapatkan data...</div>
                        <div class="temp-meta" id="ptCode">WMO --</div>
                    </div>
                </div>
                <div class="panel-row"><span class="k">Kecepatan Angin</span><span class="v" id="ptWind">--</span></div>
                <div class="mini-bar"><div class="fill" id="ptWindBar" style="width:0%"></div></div>
                <div class="panel-row"><span class="k">Arah Angin</span><span class="v" id="ptDir">--</span></div>
                <div class="panel-row"><span class="k">Hembusan Angin</span><span class="v" id="ptGust">--</span></div>
                <div class="panel-row"><span class="k">Jarak Pandang</span><span class="v" id="ptVis">--</span></div>
                <div class="panel-row"><span class="k">Indeks UV</span><span class="v" id="ptUv">--</span></div>
                <div class="panel-row"><span class="k">Kelembapan</span><span class="v" id="ptRh">--</span></div>
                <div class="mini-bar"><div class="fill" id="ptRhBar" style="width:0%"></div></div>
                <div class="panel-row"><span class="k">Tutupan Awan</span><span class="v" id="ptCloud">--</span></div>
            </aside>
        </div>

        <div class="ai-card glass" id="aiCard">
            <span class="ai-badge badge-safe" id="aiCardBadge">Menunggu Data</span>
            <div class="ai-msg" id="aiCardMsg">AI Sistem Pakar K3 sedang menganalisis kondisi cuaca untuk rekomendasi keselamatan kerja.</div>
        </div>

        <div class="cards-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#14a2ba" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            Kartu Data Detail Atmosfer
        </div>

        <div class="cards-grid" id="cardsGrid">
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M2 12h4M18 12h4M5 5l2.5 2.5M16.5 16.5 19 19M19 5l-2.5 2.5M7.5 16.5 5 19"/><circle cx="12" cy="12" r="4"/></svg><span class="c-label">Suhu Udara</span></div><div class="c-value" id="cTemp">--</div><div class="c-bar"><div class="fill" id="cTempBar" style="width:0%"></div></div><span class="c-badge" id="cTempBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg><span class="c-label">Kecepatan Angin</span></div><div class="c-value" id="cWind">--</div><div class="c-bar"><div class="fill" id="cWindBar" style="width:0%"></div></div><span class="c-badge" id="cWindBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg><span class="c-label">Arah Angin</span></div><div class="c-value" id="cDir">--</div><div class="c-bar"><div class="fill" id="cDirBar" style="width:0%"></div></div><span class="c-badge" id="cDirBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg><span class="c-label">Jarak Pandang</span></div><div class="c-value" id="cVis">--</div><div class="c-bar"><div class="fill" id="cVisBar" style="width:0%"></div></div><span class="c-badge" id="cVisBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6"/><path d="M12 16v6"/><path d="M2 12h6"/><path d="M16 12h6"/><path d="M12 8a4 4 0 0 1 4 4h-8a4 4 0 0 1 4-4z"/></svg><span class="c-label">Indeks UV</span></div><div class="c-value" id="cUv">--</div><div class="c-bar"><div class="fill" id="cUvBar" style="width:0%"></div></div><span class="c-badge" id="cUvBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8H8a4 4 0 0 0 0 8h6"/><circle cx="16" cy="9" r="3"/><path d="M16 15a3 3 0 0 1 0 6H9"/></svg><span class="c-label">Hembusan Angin</span></div><div class="c-value" id="cGust">--</div><div class="c-bar"><div class="fill" id="cGustBar" style="width:0%"></div></div><span class="c-badge" id="cGustBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c-5 0-9 4-9 9 0 5 4 9 9 9s9-4 9-9c0-5-4-9-9-9z"/><path d="M12 7c-2.5 0-4 1.8-4 4 0 2.2 1.8 4 4 4s4-1.8 4-4c0-2.2-1.8-4-4-4z"/></svg><span class="c-label">Kelembapan</span></div><div class="c-value" id="cRh">--</div><div class="c-bar"><div class="fill" id="cRhBar" style="width:0%"></div></div><span class="c-badge" id="cRhBadge">-</span></div>
            <div class="wc-card glass"><div class="c-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 1 0-2-8.5A6 6 0 0 0 4 13a4 4 0 0 0 1 7h12.5z"/></svg><span class="c-label">Tutupan Awan</span></div><div class="c-value" id="cCloud">--</div><div class="c-bar"><div class="fill" id="cCloudBar" style="width:0%"></div></div><span class="c-badge" id="cCloudBadge">-</span></div>
        </div>

        <div class="status-line" id="statusLine">Menunggu respon GPS dan data cuaca...</div>
    </div>

    <script>
        var DEFAULT_LAT = 3.5952, DEFAULT_LNG = 98.6722;
        var DEFAULTS = { name: 'Medan (default)' };
        var USE_GPS = false;

        function buildWindyUrl(lat, lng) {
            return 'https://embed.windy.com/embed2.html?lat=' + lat + '&lon=' + lng +
                '&detailLat=' + lat + '&detailLon=' + lng +
                '&width=650&height=450&zoom=9&level=surface&overlay=wind&product=ecmwf&menu=&message=true&marker=&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=km%2Fh&metricTemp=%C2%B0C&radarRange=-1';
        }

        function setLocationChip(text, ok) {
            document.getElementById('locText').textContent = text;
            var dot = document.getElementById('locDot');
            dot.classList.toggle('ok', !!ok);
        }

        function updateWindy(lat, lng) {
            document.getElementById('windyFrame').src = buildWindyUrl(lat, lng);
        }

        var currentLat = DEFAULT_LAT, currentLng = DEFAULT_LNG;

        function fetchWeather(lat, lng, attempt) {
            attempt = attempt || 0;
            currentLat = lat;
            currentLng = lng;
            setStatus('Mengambil data cuaca dari Open-Meteo' + (attempt > 0 ? ' (percobaan ' + (attempt + 1) + ')' : '') + '...');
            var url = 'https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lng +
                '&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,cloud_cover,wind_speed_10m,wind_direction_10m,wind_gusts_10m,visibility' +
                '&daily=uv_index_max&timezone=auto';

            fetch(url)
                .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function(data) {
                    var w = data.current || {};
                    var uv = (data.daily && data.daily.uv_index_max && data.daily.uv_index_max[0]) || 0;
                    renderDashboard(w, uv, data);
                })
                .catch(function(err) {
                    if (attempt < 2) {
                        setStatus('Koneksi gagal, mencoba ulang dalam beberapa detik...');
                        setTimeout(function() { fetchWeather(lat, lng, attempt + 1); }, 2500);
                    } else {
                        setStatus('Gagal mengambil data cuaca (' + err.message + '). Gunakan tombol Muat Ulang.');
                    }
                });
        }

        function reloadWeather() {
            fetchWeather(currentLat, currentLng, 0);
        }

        function wmoDesc(code) {
            var map = {
                0: 'Cerah', 1: 'Sebagian Berawan', 2: 'Berawan', 3: 'Mendung',
                45: 'Kabut', 48: 'Kabut Beku',
                51: 'Gerimis Ringan', 53: 'Gerimis Sedang', 55: 'Gerimis Lebat',
                56: 'Gerimis Membeku', 57: 'Gerimis Membeku Lebat',
                61: 'Hujan Ringan', 63: 'Hujan Sedang', 65: 'Hujan Lebat',
                66: 'Hujan Membeku', 67: 'Hujan Membeku Lebat',
                71: 'Salju Ringan', 73: 'Salju Sedang', 75: 'Salju Lebat', 77: 'Butiran Salju',
                80: 'Hujan Lokal Ringan', 81: 'Hujan Lokal Sedang', 82: 'Hujan Lokal Lebat',
                85: 'Salju Lokal', 86: 'Salju Lokal Lebat',
                95: 'Badai Petir', 96: 'Badai Petir + Hujan Es', 99: 'Badai Petir + Hujan Es Lebat'
            };
            return map[code] || 'Kondisi Tidak Diketahui';
        }

        function cardinal(dir) {
            var idx = Math.round(((dir % 360) + 360) % 360 / 45) % 8;
            return ['U', 'TL', 'T', 'TG', 'S', 'BD', 'B', 'BL'][idx];
        }

        function k3Analyze(w, uv) {
            var level = 'safe', title = 'AMAN', desc = 'Kondisi cuaca mendukung pekerjaan penebangan pohon ROW. Silakan lanjutkan aktivitas dengan tetap mematuhi SOP.';
            var code = w.weather_code || 0;
            var wind = w.wind_speed_10m || 0;
            var vis = w.visibility != null ? w.visibility : 10000;

            var dangerCodes = [95, 96, 99, 80, 81, 82];
            var warnCodes = [51, 53, 55, 56, 57, 61, 66, 67, 71, 73, 75, 45, 48];

            if (dangerCodes.indexOf(code) !== -1 || wind > 25) {
                level = 'danger';
                title = 'BAHAYA - STOP KERJA';
                desc = 'Deteksi badai petir, hujan deras, atau kecepatan angin melebihi 25 km/jam. Seluruh kegiatan penebangan pohon ROW WAJIB DIHENTIKAN dan personel dievakuasi ke lokasi aman.';
            } else if (warnCodes.indexOf(code) !== -1 || wind > 15 || vis < 2000) {
                level = 'warn';
                title = 'WASPADA - HATI-HATI';
                desc = 'Terdeteksi hujan ringan, gerimis, kabut, angin di atas 15 km/jam, atau jarak pandang di bawah 2 km. Kerja boleh dilanjutkan dengan pengawasan ekstra dan APD lengkap.';
            }

            return { level: level, title: title, desc: desc, code: code, wind: wind, vis: vis };
        }

        function renderAi(k3) {
            var badgeClass = { danger: 'badge-danger', warn: 'badge-warn', safe: 'badge-safe' }[k3.level];
            var badgeText = { danger: 'BAHAYA / STOP KERJA', warn: 'WASPADA', safe: 'AMAN' }[k3.level];

            var fb = document.getElementById('aiFloatBadge');
            var ct = document.getElementById('aiFloatTitle');
            var cd = document.getElementById('aiFloatDesc');
            fb.className = 'ai-badge ' + badgeClass;
            fb.textContent = badgeText;
            ct.style.color = { danger: '#fca5a5', warn: '#fcd34d', safe: '#6ee7b7' }[k3.level];
            ct.textContent = k3.title;
            cd.textContent = k3.desc;

            var cb = document.getElementById('aiCardBadge');
            var cm = document.getElementById('aiCardMsg');
            cb.className = 'ai-badge ' + badgeClass;
            cb.textContent = badgeText;
            cm.innerHTML = '<b>' + k3.title + '</b><br>' + k3.desc;
        }

        function setBar(el, pct, color) {
            el.style.width = pct + '%';
            el.style.background = color || '#14a2ba';
        }

        function setBadge(el, text, cls) {
            el.textContent = text;
            el.className = 'c-badge ' + (cls || '');
        }

        function renderDashboard(w, uv, raw) {
            var temp = w.temperature_2m != null ? w.temperature_2m : null;
            var feels = w.apparent_temperature != null ? w.apparent_temperature : null;
            var rh = w.relative_humidity_2m != null ? w.relative_humidity_2m : null;
            var wind = w.wind_speed_10m != null ? w.wind_speed_10m : null;
            var gust = w.wind_gusts_10m != null ? w.wind_gusts_10m : null;
            var dir = w.wind_direction_10m != null ? w.wind_direction_10m : null;
            var vis = w.visibility != null ? w.visibility : null;
            var cloud = w.cloud_cover != null ? w.cloud_cover : null;
            var code = w.weather_code != null ? w.weather_code : null;

            var desc = wmoDesc(code);
            var k3 = k3Analyze(w, uv);

            document.getElementById('ptTemp').textContent = temp != null ? Math.round(temp) + '\u00B0C' : '--';
            document.getElementById('ptFeels').textContent = feels != null ? 'Terasa seperti ' + Math.round(feels) + '\u00B0C' : 'Terasa seperti --';
            document.getElementById('ptDesc').textContent = desc;
            document.getElementById('ptCode').textContent = 'WMO Code: ' + (code != null ? code : '--');

            // SIDE PANEL
            document.getElementById('ptWind').textContent = wind != null ? wind.toFixed(1) + ' km/h' : '--';
            setBar(document.getElementById('ptWindBar'), wind != null ? Math.min(wind / 60 * 100, 100) : 0, wind > 25 ? '#ef4444' : (wind > 15 ? '#f59e0b' : '#10b981'));
            document.getElementById('ptDir').textContent = dir != null ? cardinal(dir) + ' (' + Math.round(dir) + '\u00B0)' : '--';
            document.getElementById('ptGust').textContent = gust != null ? gust.toFixed(1) + ' km/h' : '--';
            document.getElementById('ptVis').textContent = vis != null ? (vis / 1000).toFixed(1) + ' km' : '--';
            document.getElementById('ptUv').textContent = uv != null ? uv.toFixed(1) : '--';
            document.getElementById('ptRh').textContent = rh != null ? rh + ' %' : '--';
            setBar(document.getElementById('ptRhBar'), rh != null ? rh : 0, rh >= 85 ? '#3b82f6' : '#10b981');
            document.getElementById('ptCloud').textContent = cloud != null ? cloud + ' %' : '--';

            // CARDS
            document.getElementById('cTemp').innerHTML = (temp != null ? Math.round(temp) : '--') + ' <small>\u00B0C</small>';
            setBar(document.getElementById('cTempBar'), temp != null ? Math.min(temp / 45 * 100, 100) : 0, temp >= 35 ? '#ef4444' : (temp >= 28 ? '#f59e0b' : '#10b981'));
            setBadge(document.getElementById('cTempBadge'), temp >= 35 ? 'Panas' : (temp >= 28 ? 'Hangat' : (temp <= 15 ? 'Dingin' : 'Normal')), temp >= 35 ? 'badge-danger' : (temp >= 28 ? 'badge-warn' : 'badge-safe'));

            document.getElementById('cWind').innerHTML = (wind != null ? wind.toFixed(1) : '--') + ' <small>km/h</small>';
            setBar(document.getElementById('cWindBar'), wind != null ? Math.min(wind / 60 * 100, 100) : 0, wind > 25 ? '#ef4444' : (wind > 15 ? '#f59e0b' : '#10b981'));
            setBadge(document.getElementById('cWindBadge'), wind > 25 ? 'Kencang' : (wind > 15 ? 'Sedang' : 'Tenang'), wind > 25 ? 'badge-danger' : (wind > 15 ? 'badge-warn' : 'badge-safe'));

            document.getElementById('cDir').innerHTML = dir != null ? cardinal(dir) + ' <small>' + Math.round(dir) + '\u00B0</small>' : '--';
            setBar(document.getElementById('cDirBar'), dir != null ? (dir % 360) / 360 * 100 : 0, '#8b5cf6');
            setBadge(document.getElementById('cDirBadge'), dir != null ? 'Arah ' + cardinal(dir) : '-', 'badge-safe');

            document.getElementById('cVis').innerHTML = vis != null ? (vis / 1000).toFixed(1) + ' <small>km</small>' : '--';
            setBar(document.getElementById('cVisBar'), vis != null ? Math.min(vis / 10000 * 100, 100) : 0, vis < 2000 ? '#ef4444' : (vis < 5000 ? '#f59e0b' : '#10b981'));
            setBadge(document.getElementById('cVisBadge'), vis < 2000 ? 'Berkabut' : (vis < 5000 ? 'Cukup' : (vis < 10000 ? 'Normal' : 'Sangat Jelas')), vis < 2000 ? 'badge-danger' : (vis < 5000 ? 'badge-warn' : 'badge-safe'));

            document.getElementById('cUv').innerHTML = (uv != null ? uv.toFixed(1) : '--') + ' <small>max</small>';
            setBar(document.getElementById('cUvBar'), uv != null ? Math.min(uv / 11 * 100, 100) : 0, uv >= 8 ? '#ef4444' : (uv >= 6 ? '#f59e0b' : (uv >= 3 ? '#8b5cf6' : '#10b981')));
            setBadge(document.getElementById('cUvBadge'), uv >= 8 ? 'Sangat Tinggi' : (uv >= 6 ? 'Tinggi' : (uv >= 3 ? 'Sedang' : 'Rendah')), uv >= 8 ? 'badge-danger' : (uv >= 6 ? 'badge-warn' : 'badge-safe'));

            document.getElementById('cGust').innerHTML = (gust != null ? gust.toFixed(1) : '--') + ' <small>km/h</small>';
            setBar(document.getElementById('cGustBar'), gust != null ? Math.min(gust / 80 * 100, 100) : 0, gust > 40 ? '#ef4444' : (gust > 25 ? '#f59e0b' : (gust > 15 ? '#8b5cf6' : '#10b981')));
            setBadge(document.getElementById('cGustBadge'), gust > 40 ? 'Ekstrem' : (gust > 25 ? 'Kuat' : (gust > 15 ? 'Sedang' : 'Normal')), gust > 40 ? 'badge-danger' : (gust > 25 ? 'badge-warn' : 'badge-safe'));

            document.getElementById('cRh').innerHTML = (rh != null ? rh : '--') + ' <small>%</small>';
            setBar(document.getElementById('cRhBar'), rh != null ? rh : 0, rh >= 85 ? '#3b82f6' : (rh >= 60 ? '#10b981' : (rh < 40 ? '#f59e0b' : '#10b981')));
            setBadge(document.getElementById('cRhBadge'), rh >= 85 ? 'Lembab' : (rh >= 60 ? 'Normal' : (rh < 40 ? 'Kering' : 'Nyaman')), rh >= 85 ? 'badge-warn' : 'badge-safe');

            document.getElementById('cCloud').innerHTML = (cloud != null ? cloud : '--') + ' <small>%</small>';
            setBar(document.getElementById('cCloudBar'), cloud != null ? cloud : 0, cloud >= 80 ? '#64748b' : (cloud >= 50 ? '#94a3b8' : '#14a2ba'));
            setBadge(document.getElementById('cCloudBadge'), cloud >= 80 ? 'Mendung' : (cloud >= 50 ? 'Berawan' : (cloud >= 25 ? 'Sebagian' : 'Cerah')), cloud >= 80 ? 'badge-warn' : 'badge-safe');

            var now = new Date();
            document.getElementById('updateTime').textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

            renderAi(k3);
            setStatus('Data cuaca diperbarui pada ' + now.toLocaleString('id-ID'));
        }

        function setStatus(msg) {
            document.getElementById('statusLine').textContent = msg;
        }

        function togglePanel() {
            var panel = document.getElementById('dataPanel');
            panel.classList.toggle('hidden');
            document.getElementById('panelBtnText').textContent =
                panel.classList.contains('hidden') ? 'Tampilkan Panel' : 'Sembunyikan Panel';
        }

        function toggleFullscreen() {
            var wrap = document.getElementById('mapWrap');
            if (!document.fullscreenElement) {
                if (wrap.requestFullscreen) wrap.requestFullscreen();
                else if (wrap.webkitRequestFullscreen) wrap.webkitRequestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', function() {
            document.getElementById('fsBtnText').textContent =
                document.fullscreenElement ? 'Keluar Layar Penuh' : 'Layar Penuh';
        });

        function geolocate() {
            if (!navigator.geolocation) {
                useDefault();
                return;
            }
            setStatus('Meminta akses lokasi GPS...');
            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                USE_GPS = true;
                setLocationChip('Lokasi GPS: ' + lat.toFixed(4) + ', ' + lng.toFixed(4), true);
                setStatus('GPS diizinkan. Memindahkan peta ke koordinat Anda...');
                updateWindy(lat, lng);
                fetchWeather(lat, lng);
            }, function() {
                useDefault();
            }, { timeout: 10000, maximumAge: 60000 });
        }

        function useDefault() {
            setLocationChip('Lokasi: ' + DEFAULTS.name, false);
            setStatus('GPS ditolak / tidak tersedia. Menggunakan lokasi default ' + DEFAULTS.name + ' (3.5952, 98.6722).');
            updateWindy(DEFAULT_LAT, DEFAULT_LNG);
            fetchWeather(DEFAULT_LAT, DEFAULT_LNG);
        }

        geolocate();

        var overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    </script>
</body>
</html>
