<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --brand-900: #0a2540;
            --brand-800: #0c3a47;
            --brand-700: #0f5c99;
            --brand-600: #125d72;
            --brand-500: #14a2ba;
            --brand-100: #e6f6fa;
            --gold: #ffc72c;

            --ink-900: #0f172a;
            --ink-700: #334155;
            --ink-500: #64748b;
            --ink-300: #cbd5e1;

            --surface: #ffffff;
            --canvas: #f4f7fa;
            --border: #e4eaf1;

            --success-bg: #e7f7ef; --success-fg: #0a7a4d; --success-line: #b6e8cf;
            --danger-bg: #fdeaea;  --danger-fg: #c0281f;  --danger-line: #f6c6c3;
            --warning-bg: #fef3e2; --warning-fg: #a2660a; --warning-line: #f7dfab;

            --radius-lg: 18px; --radius-md: 12px; --radius-sm: 9px;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px -14px rgba(10, 37, 64, 0.22);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Plus Jakarta Sans','Inter','Segoe UI',system-ui,sans-serif;
            background:var(--canvas); color:var(--ink-900);
            min-height:100vh; -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility;
        }
        .container { padding:1.6rem 2rem 2.5rem; max-width:1500px; margin:0 auto; }

        /* ===== Hero ===== */
        .hero {
            position:relative; overflow:hidden; border-radius:24px;
            padding:clamp(1.4rem, 2.6vw, 2.2rem);
            background:
                radial-gradient(620px 320px at 88% -20%, rgba(20,162,186,0.55), transparent 62%),
                radial-gradient(480px 260px at 108% 130%, rgba(255,199,44,0.20), transparent 60%),
                linear-gradient(135deg, #0a2540 0%, #0f4c5c 58%, #125d72 100%);
            color:#fff; box-shadow:0 26px 55px -34px rgba(10,37,64,0.85);
            margin-bottom:1.3rem;
        }
        .hero-eyebrow {
            display:inline-flex; align-items:center; gap:.5rem;
            font-size:.68rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase;
            color:#8fe3f2;
        }
        .hero-eyebrow::before {
            content:''; width:7px; height:7px; border-radius:50%;
            background:#34e0c8; box-shadow:0 0 0 4px rgba(52,224,200,0.2);
        }
        .hero h1 {
            margin-top:.55rem; font-size:clamp(1.45rem, 2.6vw, 2.1rem);
            font-weight:800; letter-spacing:-0.02em; line-height:1.16;
        }
        .hero p {
            margin-top:.5rem; max-width:38rem; font-size:.88rem; line-height:1.6;
            color:rgba(255,255,255,0.76);
        }
        .hero-chips { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1.15rem; }
        .chip {
            display:inline-flex; align-items:center; gap:.42rem;
            padding:.4rem .8rem; border-radius:999px;
            font-size:.72rem; font-weight:600; letter-spacing:.01em;
            color:#e6f9ff; background:rgba(255,255,255,0.1);
            border:1px solid rgba(255,255,255,0.18);
            backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        }
        .chip svg { width:13px; height:13px; opacity:.9; }
        .chip-live { color:#d7fbff; border-color:rgba(125,211,232,0.45); background:rgba(8,32,51,0.35); }
        .chip-live::before {
            content:''; width:7px; height:7px; border-radius:50%; background:#34e0c8;
            box-shadow:0 0 0 4px rgba(52,224,200,0.2); animation:pulse 1.8s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.35} }
        .hero-art {
            position:absolute; right:-10px; top:50%; transform:translateY(-50%);
            width:270px; color:#7dd3e8; opacity:.22; pointer-events:none;
        }
        .hero-art svg { width:100%; height:auto; display:block; }

        /* ===== Filter ===== */
        .filter-card {
            background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg);
            padding:1.05rem 1.3rem; box-shadow:var(--shadow-sm);
            display:flex; align-items:flex-end; gap:1rem; flex-wrap:wrap;
            margin-bottom:1.3rem;
        }
        .filter-head { display:flex; align-items:center; gap:.7rem; padding-bottom:.15rem; }
        .filter-head .ic {
            width:36px; height:36px; border-radius:11px; flex:0 0 auto;
            display:grid; place-items:center; color:var(--brand-700);
            background:var(--brand-100);
        }
        .filter-head .ic svg { width:18px; height:18px; }
        .filter-head strong { display:block; font-size:.84rem; font-weight:700; }
        .filter-head span { display:block; margin-top:1px; font-size:.72rem; color:var(--ink-500); }
        .filter-field label {
            display:block; margin-bottom:.35rem; font-size:.64rem; font-weight:700;
            color:var(--ink-500); text-transform:uppercase; letter-spacing:.06em;
        }
        .filter-field input {
            padding:.56rem .75rem; font-family:inherit; font-size:.82rem; color:var(--ink-900);
            background:var(--canvas); border:1.5px solid var(--border); border-radius:var(--radius-sm);
            outline:none; transition:all .15s ease;
        }
        .filter-field input:focus {
            background:#fff; border-color:var(--brand-500);
            box-shadow:0 0 0 3px rgba(20,162,186,0.12);
        }
        .filter-actions { display:flex; gap:.5rem; margin-left:auto; }
        .btn-primary {
            padding:.58rem 1.3rem; font-family:inherit; font-size:.82rem; font-weight:700;
            color:#fff; border:none; border-radius:var(--radius-sm); cursor:pointer;
            background:linear-gradient(135deg, #0f5c99, #14a2ba);
            box-shadow:0 10px 20px -10px rgba(20,162,186,0.7);
            transition:transform .15s ease, filter .15s ease;
        }
        .btn-primary:hover { transform:translateY(-1px); filter:brightness(1.05); }
        .btn-ghost {
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.58rem 1.1rem; font-family:inherit; font-size:.82rem; font-weight:600;
            color:var(--ink-500); text-decoration:none; cursor:pointer;
            background:var(--canvas); border:1px solid var(--border); border-radius:var(--radius-sm);
            transition:all .15s ease;
        }
        .btn-ghost:hover { color:var(--ink-900); border-color:var(--ink-300); background:#fff; }

        .notice {
            display:flex; align-items:flex-start; gap:.6rem;
            margin-bottom:1.3rem; padding:.9rem 1.1rem;
            font-size:.82rem; line-height:1.55; color:var(--warning-fg);
            background:var(--warning-bg); border:1px solid var(--warning-line); border-radius:var(--radius-md);
        }
        .notice svg { width:17px; height:17px; flex:0 0 auto; margin-top:1px; }

        /* ===== KPI ===== */
        .kpi-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem; margin-bottom:1.3rem; }
        .kpi-card {
            position:relative; overflow:hidden; background:var(--surface);
            border:1px solid var(--border); border-radius:var(--radius-lg);
            padding:1.15rem 1.25rem; box-shadow:var(--shadow-sm);
        }
        .kpi-card::before { content:''; position:absolute; inset:0 0 auto 0; height:4px; background:var(--accent); }
        .kpi-total   { --accent:linear-gradient(90deg,#0f5c99,#14a2ba); }
        .kpi-valid   { --accent:linear-gradient(90deg,#0a7a4d,#34c98a); }
        .kpi-pending { --accent:linear-gradient(90deg,#a2660a,#f0b429); }
        .kpi-tolak   { --accent:linear-gradient(90deg,#c0281f,#ef6b61); }
        .kpi-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem; }
        .kpi-label { font-size:.68rem; font-weight:700; color:var(--ink-500); text-transform:uppercase; letter-spacing:.06em; }
        .kpi-icon { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; }
        .kpi-icon svg { width:20px; height:20px; }
        .kpi-total .kpi-icon   { background:var(--brand-100); color:var(--brand-700); }
        .kpi-valid .kpi-icon   { background:var(--success-bg); color:var(--success-fg); }
        .kpi-pending .kpi-icon { background:var(--warning-bg); color:var(--warning-fg); }
        .kpi-tolak .kpi-icon   { background:var(--danger-bg);  color:var(--danger-fg); }
        .kpi-value {
            font-size:2.05rem; font-weight:800; letter-spacing:-0.03em; line-height:1.05;
            font-variant-numeric:tabular-nums;
        }
        .kpi-sub { margin-top:.35rem; font-size:.74rem; font-weight:500; color:var(--ink-500); }
        .kpi-meter { height:6px; margin-top:.7rem; border-radius:999px; background:#eef2f6; overflow:hidden; }
        .kpi-meter span { display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,#0a7a4d,#34c98a); }

        /* ===== Panels ===== */
        .panel {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden;
        }
        .panel-head {
            display:flex; align-items:center; justify-content:space-between; gap:1rem;
            padding:1.1rem 1.3rem; border-bottom:1px solid var(--border);
        }
        .panel-head h3 { display:flex; align-items:center; gap:.5rem; font-size:.88rem; font-weight:700; }
        .panel-head h3 svg { width:16px; height:16px; color:var(--brand-500); }
        .panel-head p { margin-top:.18rem; font-size:.72rem; color:var(--ink-500); }
        .panel-body { padding:1.2rem 1.3rem; }

        .analytics-grid { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(0,1fr); gap:1.2rem; margin-bottom:1.2rem; }
        .chart-wrap { position:relative; height:250px; }

        .unit-block + .unit-block { margin-top:1.15rem; }
        .unit-block h4 {
            font-size:.64rem; font-weight:700; color:var(--ink-500);
            text-transform:uppercase; letter-spacing:.07em; margin-bottom:.65rem;
        }
        .unit-row { margin-bottom:.6rem; }
        .unit-row:last-child { margin-bottom:0; }
        .unit-meta { display:flex; align-items:baseline; justify-content:space-between; gap:.6rem; margin-bottom:.3rem; }
        .unit-name {
            font-size:.79rem; font-weight:600; color:var(--ink-700);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .unit-val { font-size:.78rem; font-weight:800; color:var(--ink-900); font-variant-numeric:tabular-nums; }
        .unit-bar { height:7px; border-radius:999px; background:#eef2f6; overflow:hidden; }
        .unit-bar span {
            display:block; height:100%; border-radius:999px;
            background:linear-gradient(90deg,#14a2ba,#0f5c99);
        }
        .unit-block.ulp .unit-bar span { background:linear-gradient(90deg,#ffc72c,#f0a20a); }
        .unit-empty { font-size:.78rem; color:var(--ink-500); padding:.5rem 0; }

        .lower-grid { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(0,1fr); gap:1.2rem; margin-bottom:1.2rem; }
        #miniMap { height:296px; width:100%; background:#eef2f5; }
        .map-empty {
            position:absolute; inset:0; display:none; place-items:center;
            font-size:.82rem; color:var(--ink-500); text-align:center; padding:1rem;
            background:rgba(244,247,250,0.85);
        }
        .map-holder { position:relative; }

        .leaflet-popup-content-wrapper { border-radius:var(--radius-md) !important; box-shadow:var(--shadow-md) !important; padding:2px !important; }
        .leaflet-popup-content { margin:11px 13px !important; }
        .leaflet-control-zoom { border-radius:var(--radius-sm) !important; overflow:hidden; box-shadow:var(--shadow-sm) !important; border:none !important; }
        .leaflet-control-zoom a { color:var(--brand-800) !important; }
        .popup-card { min-width:210px; }
        .popup-card h4 { font-size:.88rem; font-weight:800; margin-bottom:8px; padding-bottom:8px; border-bottom:1.5px solid var(--border); }
        .popup-card .row { display:flex; justify-content:space-between; gap:10px; font-size:.76rem; margin-bottom:5px; }
        .popup-card .row .k { color:var(--ink-500); font-weight:600; }
        .popup-card .row .v { color:var(--ink-900); font-weight:600; text-align:right; }
        .popup-badge { display:inline-flex; align-items:center; gap:5px; margin-top:5px; padding:3px 10px; border-radius:999px; font-size:.7rem; font-weight:700; }
        .popup-badge::before { content:''; width:6px; height:6px; border-radius:50%; }
        .st-tervalidasi { background:var(--success-bg); color:var(--success-fg); }
        .st-tervalidasi::before { background:var(--success-fg); }
        .st-ditolak { background:var(--danger-bg); color:var(--danger-fg); }
        .st-ditolak::before { background:var(--danger-fg); }
        .st-pending { background:var(--warning-bg); color:var(--warning-fg); }
        .st-pending::before { background:var(--warning-fg); }

        .ratio { display:flex; align-items:center; gap:.9rem; margin-bottom:.5rem; }
        .ratio-num { font-size:2.1rem; font-weight:800; letter-spacing:-0.03em; line-height:1; color:var(--brand-700); font-variant-numeric:tabular-nums; }
        .ratio-num small { font-size:1rem; font-weight:700; margin-left:1px; }
        .ratio-info span { display:block; font-size:.78rem; font-weight:700; }
        .ratio-info small { display:block; margin-top:1px; font-size:.72rem; color:var(--ink-500); }
        .meter { height:8px; border-radius:999px; background:#eef2f6; overflow:hidden; margin-bottom:1.15rem; }
        .meter span { display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,#0f5c99,#14a2ba,#34c98a); }
        .mini-stat {
            display:flex; align-items:center; justify-content:space-between; gap:1rem;
            padding:.6rem 0; border-bottom:1px dashed var(--border); font-size:.81rem;
        }
        .mini-stat:last-child { border-bottom:none; padding-bottom:0; }
        .mini-stat .lbl { color:var(--ink-500); font-weight:500; }
        .mini-stat .val { color:var(--ink-900); font-weight:700; font-variant-numeric:tabular-nums; }

        /* ===== Table ===== */
        .table-panel { margin-bottom:1.2rem; }
        table { width:100%; border-collapse:collapse; }
        thead th {
            background:linear-gradient(135deg, #0a2540, #0f4c5c);
            color:#fff; padding:.78rem 1rem; font-size:.68rem; font-weight:700;
            text-align:left; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap;
        }
        tbody td { padding:.72rem 1rem; font-size:.82rem; border-bottom:1px solid var(--border); color:var(--ink-700); vertical-align:middle; }
        tbody tr { transition:background .12s ease; }
        tbody tr:hover { background:#f2fbfd; }
        tbody tr:last-child td { border-bottom:none; }
        tbody td strong { color:var(--ink-900); font-weight:700; }
        .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 11px; border-radius:999px; font-size:.71rem; font-weight:700; border:1px solid transparent; }
        .status-badge::before { content:''; width:6px; height:6px; border-radius:50%; }
        .status-tervalidasi { background:var(--success-bg); color:var(--success-fg); border-color:var(--success-line); }
        .status-tervalidasi::before { background:var(--success-fg); }
        .status-pending { background:var(--warning-bg); color:var(--warning-fg); border-color:var(--warning-line); }
        .status-pending::before { background:var(--warning-fg); }
        .status-ditolak { background:var(--danger-bg); color:var(--danger-fg); border-color:var(--danger-line); }
        .status-ditolak::before { background:var(--danger-fg); }
        .btn-link { display:inline-flex; align-items:center; gap:.25rem; color:var(--brand-700); font-weight:700; font-size:.8rem; text-decoration:none; white-space:nowrap; }
        .btn-link:hover { text-decoration:underline; }
        .empty { text-align:center; padding:2.6rem 1rem; color:var(--ink-500); font-size:.85rem; }

        .dash-foot { text-align:center; font-size:.72rem; color:#9aa8b8; padding:.4rem 0 .2rem; }

        /* ===== Responsive ===== */
        @media (max-width:1200px) {
            .analytics-grid, .lower-grid { grid-template-columns:1fr; }
            .kpi-grid { grid-template-columns:repeat(2, 1fr); }
        }
        @media (max-width:900px) {
            .container { padding:1rem 1rem 2rem; }
            .hero-art { display:none; }
        }
        @media (max-width:700px) {
            .kpi-grid { grid-template-columns:1fr; }
            .filter-actions { margin-left:0; width:100%; }
            .filter-actions .btn-primary, .filter-actions .btn-ghost { flex:1; justify-content:center; }
            .panel-head { flex-wrap:wrap; }
            .hero { padding:1.3rem; border-radius:20px; }
        }
    </style>
</head>
<body>
    @include('partials.sidebar', ['active' => 'dashboard'])

    @php
        $scopeLevel = $scope['level'] ?? 'uid';
        $scopeValue = $scope['value'] ?? null;
        if ($scopeLevel === 'up3' && $scopeValue) {
            $scopeLabel = 'Cakupan UP3 · ' . $scopeValue;
        } elseif ($scopeLevel === 'ulp' && $scopeValue) {
            $scopeLabel = 'Cakupan ULP · ' . $scopeValue;
        } else {
            $scopeLabel = 'Cakupan UID · Semua Unit';
        }

        $up3Top = array_slice($kpis['up3_count'], 0, 5, true);
        $up3Max = !empty($up3Top) ? max($up3Top) : 1;
        $ulpTop = array_slice($kpis['ulp_count'], 0, 5, true);
        $ulpMax = !empty($ulpTop) ? max($ulpTop) : 1;

        $statusOf = function ($status) {
            $clean = trim(str_replace(['✅', '❌'], '', $status ?? ''));
            $s = strtolower($clean);
            if (str_contains($s, 'tervalidasi')) return [$clean, 'status-tervalidasi'];
            if (str_contains($s, 'ditolak'))     return [$clean, 'status-ditolak'];
            return [$clean !== '' ? $clean : 'Pending', 'status-pending'];
        };

        $hasFilter = $from || $to;
    @endphp

    <div class="container">
        <header class="hero">
            <div class="hero-art" aria-hidden="true">
                <svg viewBox="0 0 220 160" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 120h30l12-28 16 56 18-78 16 46 12-20h28l14-32 16 64 12-28h32" opacity="0.9"/>
                    <circle cx="180" cy="34" r="26" opacity="0.5"/>
                    <circle cx="180" cy="34" r="42" opacity="0.28"/>
                </svg>
            </div>
            <span class="hero-eyebrow">PLN WebGIS · Monitoring Gangguan</span>
            <h1>Dashboard Monitoring Penyulang</h1>
            <p>Ringkasan gangguan, durasi padam, dan sebaran lokasi penyulang dalam satu tampilan.</p>
            <div class="hero-chips">
                <span class="chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ $scopeLabel }}
                </span>
                <span class="chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    Sinkron {{ $syncedAt->translatedFormat('d M Y, H:i') }} WIB
                </span>
                <span class="chip chip-live">Data Spreadsheet</span>
            </div>
        </header>

        <form class="filter-card" method="GET" action="{{ route('dashboard') }}">
            <div class="filter-head">
                <span class="ic">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                </span>
                <div>
                    <strong>Filter Rentang Tanggal</strong>
                    <span>Menampilkan {{ number_format($kpis['total'], 0, ',', '.') }} laporan</span>
                </div>
            </div>
            <div class="filter-field">
                <label for="from">Dari Tanggal</label>
                <input type="date" id="from" name="from" value="{{ $from }}">
            </div>
            <div class="filter-field">
                <label for="to">Sampai Tanggal</label>
                <input type="date" id="to" name="to" value="{{ $to }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('dashboard') }}" class="btn-ghost">Reset</a>
            </div>
        </form>

        @if ($kpis['total'] === 0 && ! $hasFilter)
            <div class="notice">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                <span>Data belum tersedia. Kemungkinan sumber Google Spreadsheet sedang tidak bisa diakses atau memang belum ada laporan.</span>
            </div>
        @endif

        <section class="kpi-grid">
            <div class="kpi-card kpi-total">
                <div class="kpi-top">
                    <span class="kpi-label">Total Laporan</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/></svg>
                    </span>
                </div>
                <div class="kpi-value">{{ number_format($kpis['total'], 0, ',', '.') }}</div>
                <div class="kpi-sub">Total gangguan tercatat</div>
            </div>

            <div class="kpi-card kpi-valid">
                <div class="kpi-top">
                    <span class="kpi-label">Tervalidasi</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                    </span>
                </div>
                <div class="kpi-value">{{ number_format($kpis['tervalidasi'], 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ $kpis['persen_validasi'] }}% dari total laporan</div>
                <div class="kpi-meter"><span style="width: {{ $kpis['persen_validasi'] }}%"></span></div>
            </div>

            <div class="kpi-card kpi-pending">
                <div class="kpi-top">
                    <span class="kpi-label">Pending</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    </span>
                </div>
                <div class="kpi-value">{{ number_format($kpis['pending'], 0, ',', '.') }}</div>
                <div class="kpi-sub">Menunggu validasi</div>
            </div>

            <div class="kpi-card kpi-tolak">
                <div class="kpi-top">
                    <span class="kpi-label">Ditolak</span>
                    <span class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                    </span>
                </div>
                <div class="kpi-value">{{ number_format($kpis['ditolak'], 0, ',', '.') }}</div>
                <div class="kpi-sub">Laporan ditolak</div>
            </div>
        </section>

        <section class="analytics-grid">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                            Gangguan per Hari
                        </h3>
                        <p>Tren jumlah gangguan berdasarkan tanggal padam</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M9 13h1M14 9h1M14 13h1"/></svg>
                            Unit Terdampak
                        </h3>
                        <p>Lima unit dengan gangguan terbanyak</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="unit-block">
                        <h4>Unit UP3</h4>
                        @forelse ($up3Top as $name => $count)
                            <div class="unit-row">
                                <div class="unit-meta">
                                    <span class="unit-name" title="{{ $name }}">{{ $name }}</span>
                                    <span class="unit-val">{{ number_format($count, 0, ',', '.') }}</span>
                                </div>
                                <div class="unit-bar"><span style="width: {{ $up3Max > 0 ? round(($count / $up3Max) * 100) : 0 }}%"></span></div>
                            </div>
                        @empty
                            <div class="unit-empty">Belum ada data unit UP3.</div>
                        @endforelse
                    </div>

                    <div class="unit-block ulp">
                        <h4>Unit ULP</h4>
                        @forelse ($ulpTop as $name => $count)
                            <div class="unit-row">
                                <div class="unit-meta">
                                    <span class="unit-name" title="{{ $name }}">{{ $name }}</span>
                                    <span class="unit-val">{{ number_format($count, 0, ',', '.') }}</span>
                                </div>
                                <div class="unit-bar"><span style="width: {{ $ulpMax > 0 ? round(($count / $ulpMax) * 100) : 0 }}%"></span></div>
                            </div>
                        @empty
                            <div class="unit-empty">Belum ada data unit ULP.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="lower-grid">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Sebaran Lokasi Gangguan
                        </h3>
                        <p>{{ count($mapRows) }} titik koordinat sesuai filter</p>
                    </div>
                    <a href="{{ route('peta') }}" class="btn-ghost">Buka Peta</a>
                </div>
                <div class="map-holder">
                    <div id="miniMap"></div>
                    <div class="map-empty" id="mapEmpty">Belum ada data koordinat untuk ditampilkan pada peta.</div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            Statistik Penanganan
                        </h3>
                        <p>Ringkasan durasi padam dan cakupan unit</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="ratio">
                        <div class="ratio-num">{{ $kpis['persen_validasi'] }}<small>%</small></div>
                        <div class="ratio-info">
                            <span>Tingkat validasi</span>
                            <small>{{ number_format($kpis['tervalidasi'], 0, ',', '.') }} dari {{ number_format($kpis['total'], 0, ',', '.') }} laporan</small>
                        </div>
                    </div>
                    <div class="meter"><span style="width: {{ $kpis['persen_validasi'] }}%"></span></div>

                    <div class="mini-stat"><span class="lbl">Rata-rata lama padam</span><span class="val">{{ $kpis['avg_lama'] }}</span></div>
                    <div class="mini-stat"><span class="lbl">Lama padam terlama</span><span class="val">{{ $kpis['terlama'] }}</span></div>
                    <div class="mini-stat"><span class="lbl">Lama padam tercepat</span><span class="val">{{ $kpis['tercepat'] }}</span></div>
                    <div class="mini-stat"><span class="lbl">Unit UP3 terdampak</span><span class="val">{{ $kpis['total_up3'] }}</span></div>
                    <div class="mini-stat"><span class="lbl">Unit ULP terdampak</span><span class="val">{{ $kpis['total_ulp'] }}</span></div>
                </div>
            </div>
        </section>

        <section class="panel table-panel">
            <div class="panel-head">
                <div>
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                        Laporan Terbaru
                    </h3>
                    <p>Lima laporan gangguan terakhir sesuai filter</p>
                </div>
                <a href="{{ route('arsip') }}" class="btn-ghost">Lihat Semua</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Penyulang</th>
                        <th>Unit UP3</th>
                        <th>Unit ULP</th>
                        <th>Tgl Padam</th>
                        <th>Lama Padam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($terbaru as $i => $it)
                        @php [$statusText, $statusClass] = $statusOf($it['status'] ?? ''); @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $it['nama_penyulang'] }}</strong></td>
                            <td>{{ $it['unit_up3'] }}</td>
                            <td>{{ $it['unit_ulp'] }}</td>
                            <td>{{ $it['tgl_padam'] }}</td>
                            <td>{{ $it['lama_padam'] ?: '-' }}</td>
                            <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                            <td>
                                <a class="btn-link" href="{{ route('laporan.detail', ['idx' => $it['_idx'], 'nama' => $it['nama_penyulang'], 'tgl' => $it['tgl_padam']]) }}">Detail →</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="empty">Tidak ada data dalam rentang tanggal ini.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <footer class="dash-foot">
            PLN WebGIS · Data bersumber dari Google Spreadsheet · Sinkron {{ $syncedAt->translatedFormat('d M Y H:i') }} WIB
        </footer>
    </div>

    <script>
        (function () {
            const perHari = @json($kpis['per_hari']);
            const ctx = document.getElementById('trendChart');
            const rawLabels = Object.keys(perHari);
            const labels = rawLabels.map(function (d) {
                const parts = d.split('-');
                return parts.length === 3 ? parts[2] + '/' + parts[1] : d;
            });

            if (ctx && typeof Chart !== 'undefined') {
                if (!labels.length) {
                    ctx.parentElement.innerHTML = '<div class="empty" style="height:100%;display:grid;place-items:center;">Belum ada data untuk ditampilkan.</div>';
                } else {
                    const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                    g.addColorStop(0, 'rgba(20, 162, 186, 0.9)');
                    g.addColorStop(1, 'rgba(20, 162, 186, 0.35)');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah gangguan',
                                data: Object.values(perHari),
                                backgroundColor: g,
                                hoverBackgroundColor: 'rgba(15, 92, 153, 0.95)',
                                borderRadius: 8,
                                borderSkipped: false,
                                maxBarThickness: 46
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0a2540',
                                    padding: 10,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function (c) { return c.parsed.y + ' gangguan'; }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, color: '#64748b' },
                                    grid: { color: '#eef1f5' },
                                    border: { display: false }
                                },
                                x: {
                                    ticks: { color: '#64748b' },
                                    grid: { display: false },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                }
            }
        })();

        (function () {
            const rows = @json($mapRows);
            const el = document.getElementById('miniMap');
            if (!el || typeof L === 'undefined') return;

            const map = L.map('miniMap', {
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: true
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 18
            }).addTo(map);

            function esc(v) {
                return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            function statusMeta(status) {
                const s = String(status || '').toLowerCase();
                if (s.includes('tervalidasi')) return { cls: 'st-tervalidasi', color: '#0a7a4d' };
                if (s.includes('ditolak')) return { cls: 'st-ditolak', color: '#c0281f' };
                return { cls: 'st-pending', color: '#a2660a' };
            }

            function pin(color) {
                return '<svg width="26" height="36" viewBox="0 0 28 40" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M14 0C6.3 0 0 6.3 0 14c0 9.8 14 26 14 26s14-16.2 14-26C28 6.3 21.7 0 14 0z" fill="' + color + '" stroke="rgba(0,0,0,0.18)" stroke-width="1.5"/>' +
                    '<circle cx="14" cy="13" r="5.5" fill="#fff"/></svg>';
            }

            const markers = [];
            rows.forEach(function (item) {
                if (item.latitude == null || item.longitude == null) return;
                const meta = statusMeta(item.status);
                const icon = L.divIcon({
                    className: 'dash-marker',
                    html: pin(meta.color),
                    iconSize: [26, 36],
                    iconAnchor: [13, 36],
                    popupAnchor: [0, -34]
                });
                const popup = '<div class="popup-card">' +
                    '<h4>' + esc(item.nama_penyulang) + '</h4>' +
                    '<div class="row"><span class="k">UP3</span><span class="v">' + esc(item.unit_up3) + '</span></div>' +
                    '<div class="row"><span class="k">ULP</span><span class="v">' + esc(item.unit_ulp) + '</span></div>' +
                    '<div class="row"><span class="k">Tgl Padam</span><span class="v">' + esc(item.tgl_padam || '-') + '</span></div>' +
                    '<div class="row"><span class="k">Lama</span><span class="v">' + esc(item.lama_padam || '-') + '</span></div>' +
                    '<span class="popup-badge ' + meta.cls + '">' + esc((item.status || 'Pending').replace(/[✅❌]/g, '').trim() || 'Pending') + '</span>' +
                    '</div>';

                markers.push(L.marker([item.latitude, item.longitude], { icon: icon }).bindPopup(popup));
            });

            if (markers.length) {
                const group = L.featureGroup(markers).addTo(map);
                if (markers.length === 1) {
                    map.setView(markers[0].getLatLng(), 12);
                } else {
                    map.fitBounds(group.getBounds().pad(0.2));
                }
            } else {
                map.setView([-2.5, 118], 4);
                const empty = document.getElementById('mapEmpty');
                if (empty) empty.style.display = 'grid';
            }
        })();
    </script>
</body>
</html>
