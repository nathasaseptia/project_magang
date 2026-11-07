<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Data Penyulang - PLN WebGIS</title>
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Palette — konsisten dengan halaman Peta */
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
            --neutral-bg: #eef1f5;
            --neutral-fg: #47566a;
            --neutral-line: #dde3ea;

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
            min-height: 100vh;
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
            letter-spacing: 0.02em; box-shadow: var(--shadow-sm);
        }
        .navbar-brand h1 { color: #fff; font-size: 1.05rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.01em; }
        .navbar-brand span { color: rgba(255,255,255,0.7); font-size: 0.72rem; font-weight: 500; letter-spacing: 0.02em; }
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

        .page-header {
            display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 1.1rem;
        }
        .page-header .title-block h2 { color: var(--ink-900); font-size: 1.4rem; font-weight: 800; letter-spacing: -0.015em; }
        .page-header .title-block p { color: var(--ink-500); font-size: 0.85rem; margin-top: 0.2rem; }
        .badge {
            background: var(--brand-100); color: var(--brand-800);
            border: 1px solid #cdeef4;
            padding: 0.4rem 0.95rem; border-radius: 999px;
            font-size: 0.8rem; font-weight: 700; letter-spacing: 0.01em;
            display: flex; align-items: center; gap: 0.4rem; white-space: nowrap;
        }
        .badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--brand-500); flex-shrink: 0; }

        /* ===== Filter card ===== */
        .filter-card {
            background: var(--surface); border-radius: var(--radius-lg); padding: 1.15rem 1.35rem;
            box-shadow: var(--shadow-sm); border: 1px solid var(--border);
            margin-bottom: 1.25rem;
        }
        .filter-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; }
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

        .filter-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.85rem; }
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

        /* ===== Table card ===== */
        .card {
            background: var(--surface); border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md); border: 1px solid var(--border); overflow: hidden;
        }
        .card-body { padding: 0; }
        .table-responsive { overflow-x: auto; max-height: 68vh; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--brand-900);
            color: #fff; padding: 0.8rem 1rem;
            font-size: 0.72rem; font-weight: 700; text-align: left;
            text-transform: uppercase; letter-spacing: 0.04em;
            white-space: nowrap; position: sticky; top: 0; z-index: 2;
        }
        thead th:first-child { border-top-left-radius: 0; }
        tbody td {
            padding: 0.7rem 1rem; font-size: 0.83rem;
            border-bottom: 1px solid var(--border); color: var(--ink-700);
            vertical-align: middle;
        }
        tbody td:first-child { color: var(--ink-500); font-weight: 600; font-variant-numeric: tabular-nums; }
        tbody tr { transition: background 0.12s ease; }
        tbody tr:hover { background: var(--brand-100); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr.hidden { display: none; }
        tbody td strong { color: var(--ink-900); font-weight: 700; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 11px; border-radius: 999px; font-size: 0.72rem; font-weight: 700;
            white-space: nowrap; border: 1px solid transparent;
        }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .status-tervalidasi { background: var(--success-bg); color: var(--success-fg); border-color: var(--success-line); }
        .status-tervalidasi::before { background: var(--success-fg); }
        .status-gangguan { background: var(--danger-bg); color: var(--danger-fg); border-color: var(--danger-line); }
        .status-gangguan::before { background: var(--danger-fg); }
        .status-maintenance { background: var(--warning-bg); color: var(--warning-fg); border-color: var(--warning-line); }
        .status-maintenance::before { background: var(--warning-fg); }
        .status-default { background: var(--neutral-bg); color: var(--neutral-fg); border-color: var(--neutral-line); }
        .status-default::before { background: var(--neutral-fg); }

        .btn-detail {
            background: var(--brand-700);
            color: #fff; border: none;
            padding: 0.4rem 0.95rem; border-radius: var(--radius-sm);
            font-size: 0.76rem; font-weight: 600; cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            white-space: nowrap; font-family: inherit;
        }
        .btn-detail:hover { background: var(--brand-500); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(20,162,186,0.35); }
        .btn-detail:active { transform: translateY(0); }

        .empty-state { text-align: center; padding: 3.5rem 1rem; color: var(--ink-500); }
        .empty-state svg { width: 34px; height: 34px; color: var(--ink-300); margin-bottom: 0.6rem; }
        .empty-state p { font-size: 0.85rem; font-weight: 500; }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(12, 20, 26, 0.55); backdrop-filter: blur(4px);
            z-index: 9999; align-items: center; justify-content: center; padding: 2rem;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: var(--surface); border-radius: 18px; width: 100%; max-width: 700px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: var(--shadow-lg); animation: modalSlide 0.25s ease;
            border: 1px solid var(--border);
        }
        @keyframes modalSlide { from { opacity: 0; transform: translateY(16px) scale(0.99); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-header {
            background: linear-gradient(120deg, var(--brand-900) 0%, var(--brand-500) 130%);
            padding: 1.15rem 1.5rem; border-radius: 18px 18px 0 0;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0;
        }
        .modal-header h3 { color: #fff; font-size: 1.05rem; font-weight: 700; letter-spacing: -0.01em; }
        .modal-close {
            background: rgba(255,255,255,0.16); border: none; color: #fff;
            width: 30px; height: 30px; border-radius: var(--radius-sm);
            font-size: 1.1rem; cursor: pointer; line-height: 1;
            display: flex; align-items: center; justify-content: center; transition: background 0.15s ease;
        }
        .modal-close:hover { background: rgba(255,255,255,0.3); }
        .modal-body { padding: 1.5rem; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.25rem; margin-bottom: 1.5rem; }
        .detail-item {
            background: var(--canvas); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 0.7rem 0.85rem;
        }
        .detail-item .label { color: var(--ink-500); font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .detail-item .value { color: var(--ink-900); font-size: 0.88rem; font-weight: 600; }
        .modal-section-title {
            color: var(--ink-900); font-size: 0.8rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.04em;
            margin-bottom: 0.7rem; padding-bottom: 0.5rem; border-bottom: 1.5px solid var(--border);
            display: flex; align-items: center; gap: 6px;
        }
        .modal-section-title svg { width: 14px; height: 14px; color: var(--brand-500); }
        #modal-detail-map { height: 250px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--canvas); }
        .coord-display {
            background: var(--brand-100); border: 1px solid #cdeef4; border-radius: var(--radius-sm);
            padding: 0.6rem 1rem; margin-top: 0.7rem;
            display: flex; align-items: center; gap: 1.5rem; font-size: 0.78rem;
        }
        .coord-display .coord-label { color: var(--ink-500); font-weight: 600; margin-right: 4px; }
        .coord-display .coord-value { color: var(--brand-800); font-weight: 700; font-family: 'JetBrains Mono', monospace; }

        /* ===== Loading overlay ===== */
        .loading-overlay {
            position: fixed; inset: 0; background: rgba(243, 246, 249, 0.92);
            backdrop-filter: blur(2px);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 10000; gap: 1rem;
        }
        .spinner { width: 38px; height: 38px; border: 3.5px solid var(--border); border-top-color: var(--brand-500); border-radius: 50%; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: var(--brand-800); font-weight: 600; font-size: 0.85rem; letter-spacing: 0.01em; }

        /* ===== Responsive ===== */
        @media (max-width: 1200px) { .filter-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 720px) {
            .navbar { padding: 0.75rem 1.1rem; flex-wrap: wrap; gap: 0.6rem; }
            .container { padding: 1rem; }
            .filter-grid { grid-template-columns: repeat(2, 1fr); }
            .page-header { flex-direction: column; align-items: flex-start; gap: 0.6rem; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Memuat data dari Google Spreadsheet…</div>
    </div>

    @include('partials.sidebar', ['active' => 'arsip'])

    <div class="container">
        <div class="page-header">
            <div class="title-block">
                <h2>Arsip Data Penyulang</h2>
                <p>Riwayat lengkap data pemadaman dan status penyulang</p>
            </div>
            <div class="badge" id="countBadge">{{ count($penyulangs) }} Data</div>
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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyulang</th>
                                <th>Unit UP3</th>
                                <th>Unit ULP</th>
                                <th>Tgl Padam</th>
                                <th>Jam Padam</th>
                                <th>Jam Nyala</th>
                                <th>Lama Padam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($penyulangs as $index => $item)
                            <tr data-nama="{{ strtolower($item['nama_penyulang']) }}"
                                data-up3="{{ $item['unit_up3'] }}"
                                data-ulp="{{ $item['unit_ulp'] }}"
                                data-tgl-padam="{{ $item['tgl_padam'] }}"
                                data-jam-padam="{{ substr($item['jam_padam'] ?? '', 0, 5) }}"
                                data-jam-nyala="{{ substr($item['jam_nyala'] ?? '', 0, 5) }}">
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $item['nama_penyulang'] }}</strong></td>
                                <td>{{ $item['unit_up3'] }}</td>
                                <td>{{ $item['unit_ulp'] }}</td>
                                <td>{{ $item['tgl_padam'] ?: '-' }}</td>
                                <td>{{ $item['jam_padam'] ?: '-' }}</td>
                                <td>{{ $item['jam_nyala'] ?: '-' }}</td>
                                <td>{{ $item['lama_padam'] ?: '-' }}</td>
                                <td>
                                    @php
                                        $sts = str_replace(' âœ…', '', $item['status']);
                                        $cls = 'status-default';
                                        if (str_contains($sts, 'Tervalidasi')) $cls = 'status-tervalidasi';
                                        elseif (str_contains($sts, 'Gangguan')) $cls = 'status-gangguan';
                                        elseif (str_contains($sts, 'Maintenance')) $cls = 'status-maintenance';
                                    @endphp
                                    <span class="status-badge {{ $cls }}">{{ $item['status'] ?: '-' }}</span>
                                </td>
                                <td>
                                    <button class="btn-detail"
                                        data-nama="{{ $item['nama_penyulang'] }}"
                                        data-up3="{{ $item['unit_up3'] }}"
                                        data-ulp="{{ $item['unit_ulp'] }}"
                                        data-tgl-padam="{{ $item['tgl_padam'] }}"
                                        data-jam-padam="{{ $item['jam_padam'] }}"
                                        data-tgl-nyala="{{ $item['tgl_nyala'] }}"
                                        data-jam-nyala="{{ $item['jam_nyala'] }}"
                                        data-lama-padam="{{ $item['lama_padam'] }}"
                                        data-status="{{ $item['status'] }}"
                                        data-lat="{{ $item['latitude'] ?? '' }}"
                                        data-lng="{{ $item['longitude'] ?? '' }}"
                                        onclick="bukaDetail(this)">
                                        Buka Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5"/></svg>
                                        <p>Tidak ada data penyulang yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div class="modal-overlay" id="modalDetail">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalJudul">Detail Penyulang</h3>
                <button class="modal-close" onclick="tutupModal()" aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Unit UP3</div>
                        <div class="value" id="modalUp3">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Unit ULP</div>
                        <div class="value" id="modalUlp">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Status</div>
                        <div class="value" id="modalStatus">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Lama Padam</div>
                        <div class="value" id="modalLama">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Tgl / Jam Padam</div>
                        <div class="value" id="modalPadam">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Tgl / Jam Nyala</div>
                        <div class="value" id="modalNyala">-</div>
                    </div>
                </div>

                <div>
                    <div class="modal-section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 10c0 6-8.5 12-8.5 12S3.5 16 3.5 10a8.5 8.5 0 0 1 17 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Lokasi pada Peta
                    </div>
                    <div id="modal-detail-map"></div>
                    <div class="coord-display">
                        <div><span class="coord-label">Lat:</span><span class="coord-value" id="modalLat">-</span></div>
                        <div><span class="coord-label">Lng:</span><span class="coord-value" id="modalLng">-</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modalMap = null;
        var modalMarker = null;

        var redIcon = L.divIcon({
            className: 'custom-marker',
            html: '<svg width="24" height="34" viewBox="0 0 24 34" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.4 0 0 5.4 0 12c0 8.4 12 22 12 22s12-13.6 12-22C24 5.4 18.6 0 12 0z" fill="#e74c3c" stroke="#c0392b" stroke-width="1.5"/><circle cx="12" cy="11" r="5" fill="#fff"/></svg>',
            iconSize: [24, 34], iconAnchor: [12, 34], popupAnchor: [0, -34]
        });

        function populateFilters() {
            var rows = document.querySelectorAll('#tableBody tr[data-up3]');
            var namaSet = {}, up3Set = {}, ulpSet = {};
            rows.forEach(function(row) {
                var namaCell = row.querySelector('td:nth-child(2) strong');
                if (namaCell) namaSet[namaCell.textContent] = true;
                if (row.dataset.up3) up3Set[row.dataset.up3] = true;
                if (row.dataset.ulp) ulpSet[row.dataset.ulp] = true;
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
            var nama = document.getElementById('filterNama').value.toLowerCase();
            var up3 = document.getElementById('filterUp3').value;
            var ulp = document.getElementById('filterUlp').value;
            var tglPadam = document.getElementById('filterTglPadam').value;
            var jamPadam = document.getElementById('filterJamPadam').value;
            var jamNyala = document.getElementById('filterJamNyala').value;

            var rows = document.querySelectorAll('#tableBody tr[data-up3]');
            var count = 0;

            rows.forEach(function(row) {
                var show = true;

                if (nama && row.dataset.nama !== nama) show = false;
                if (up3 && row.dataset.up3 !== up3) show = false;
                if (ulp && row.dataset.ulp !== ulp) show = false;
                if (tglPadam && row.dataset.tglPadam !== tglPadam) show = false;
                if (jamPadam && row.dataset.jamPadam !== jamPadam) show = false;
                if (jamNyala && row.dataset.jamNyala !== jamNyala) show = false;

                if (show) {
                    row.classList.remove('hidden');
                    count++;
                } else {
                    row.classList.add('hidden');
                }
            });

            document.getElementById('countBadge').textContent = count + ' Data';
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

        function bukaDetail(btn) {
            var d = {
                nama: btn.dataset.nama || '',
                up3: btn.dataset.up3 || '',
                ulp: btn.dataset.ulp || '',
                tglPadam: btn.dataset.tglPadam || '',
                jamPadam: btn.dataset.jamPadam || '',
                tglNyala: btn.dataset.tglNyala || '',
                jamNyala: btn.dataset.jamNyala || '',
                lamaPadam: btn.dataset.lamaPadam || '',
                status: btn.dataset.status || '',
                lat: btn.dataset.lat ? parseFloat(btn.dataset.lat) : null,
                lng: btn.dataset.lng ? parseFloat(btn.dataset.lng) : null
            };
            document.getElementById('modalJudul').textContent = d.nama;
            document.getElementById('modalUp3').textContent = d.up3;
            document.getElementById('modalUlp').textContent = d.ulp;
            document.getElementById('modalLama').textContent = d.lamaPadam || '-';
            document.getElementById('modalPadam').textContent = (d.tglPadam || '-') + ' ' + (d.jamPadam || '');
            document.getElementById('modalNyala').textContent = (d.tglNyala || '-') + ' ' + (d.jamNyala || '');
            document.getElementById('modalStatus').textContent = d.status || '-';
            document.getElementById('modalLat').textContent = d.lat !== null ? d.lat : '-';
            document.getElementById('modalLng').textContent = d.lng !== null ? d.lng : '-';

            document.getElementById('modalDetail').classList.add('active');

            setTimeout(function() {
                if (d.lat !== null && d.lng !== null) {
                    if (modalMap === null) {
                        modalMap = L.map('modal-detail-map', { zoomControl: true, scrollWheelZoom: false });
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap', maxZoom: 18
                        }).addTo(modalMap);
                    }
                    modalMap.setView([d.lat, d.lng], 13);
                    if (modalMarker) modalMap.removeLayer(modalMarker);
                    modalMarker = L.marker([d.lat, d.lng], { icon: redIcon })
                        .addTo(modalMap).bindPopup('<strong>' + d.nama + '</strong>').openPopup();
                    modalMap.invalidateSize();
                }
            }, 350);
        }

        function tutupModal() {
            document.getElementById('modalDetail').classList.remove('active');
        }

        document.getElementById('modalDetail').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tutupModal();
        });

        var overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    </script>
</body>
</html>