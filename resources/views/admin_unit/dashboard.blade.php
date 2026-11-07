<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        :root {
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

            --success-bg: #e7f7ef; --success-fg: #0a7a4d; --success-line: #b6e8cf;
            --danger-bg: #fdeaea;  --danger-fg: #c0281f;  --danger-line: #f6c6c3;
            --warning-bg: #fef3e2; --warning-fg: #a2660a; --warning-line: #f7dfab;
            --neutral-bg: #eef1f5; --neutral-fg: #47566a; --neutral-line: #dde3ea;

            --radius-lg: 16px; --radius-md: 12px; --radius-sm: 8px;
            --shadow-sm: 0 1px 2px rgba(16,27,35,0.06);
            --shadow-md: 0 6px 20px -6px rgba(16,27,35,0.12);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter','Segoe UI',system-ui,sans-serif;
            background:var(--canvas); color:var(--ink-900);
            min-height:100vh; -webkit-font-smoothing:antialiased;
        }
        .container { padding:1.6rem 2rem 2.5rem; max-width:1500px; margin:0 auto; }

        .page-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:1.3rem; }
        .page-header .title-block h2 { font-size:1.45rem; font-weight:800; letter-spacing:-0.015em; }
        .page-header .title-block p { color:var(--ink-500); font-size:0.85rem; margin-top:0.25rem; }
        .live-badge {
            background:var(--success-bg); color:var(--success-fg); border:1px solid var(--success-line);
            padding:0.4rem 0.95rem; border-radius:999px; font-size:0.78rem; font-weight:700;
            display:flex; align-items:center; gap:0.45rem;
        }
        .live-badge::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--success-fg); animation:pulse 1.6s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .filter-card {
            background:var(--surface); border-radius:var(--radius-lg); padding:1.1rem 1.35rem;
            box-shadow:var(--shadow-sm); border:1px solid var(--border); margin-bottom:1.3rem;
            display:flex; align-items:flex-end; gap:1rem; flex-wrap:wrap;
        }
        .filter-card h3 { font-size:0.8rem; font-weight:700; color:var(--ink-900); margin-bottom:0.6rem; display:flex; align-items:center; gap:6px; }
        .filter-card h3 svg { width:15px; height:15px; color:var(--brand-500); }
        .filter-field label { display:block; font-size:0.66rem; font-weight:700; color:var(--ink-500); margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.05em; }
        .filter-field input {
            padding:0.55rem 0.75rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
            font-size:0.82rem; font-family:inherit; color:var(--ink-900); background:var(--canvas); outline:none;
            transition:all 0.15s ease;
        }
        .filter-field input:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(20,162,186,0.12); background:#fff; }
        .btn-primary {
            background:var(--brand-700); color:#fff; border:none; padding:0.58rem 1.3rem; border-radius:var(--radius-sm);
            font-size:0.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:background 0.15s;
        }
        .btn-primary:hover { background:var(--brand-500); }
        .btn-ghost {
            background:var(--canvas); color:var(--ink-500); border:1px solid var(--border); padding:0.58rem 1.1rem;
            border-radius:var(--radius-sm); font-size:0.82rem; font-weight:600; cursor:pointer; text-decoration:none;
            font-family:inherit; transition:all 0.15s;
        }
        .btn-ghost:hover { color:var(--ink-900); border-color:var(--ink-300); }

        .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.3rem; }
        .kpi-card {
            background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg);
            padding:1.2rem 1.3rem; box-shadow:var(--shadow-sm); position:relative; overflow:hidden;
        }
        .kpi-card .kpi-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.8rem; }
        .kpi-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .kpi-icon svg { width:22px; height:22px; }
        .kpi-card .kpi-label { font-size:0.72rem; font-weight:700; color:var(--ink-500); text-transform:uppercase; letter-spacing:0.05em; }
        .kpi-card .kpi-value { font-size:2rem; font-weight:800; letter-spacing:-0.02em; line-height:1.1; margin-top:0.25rem; font-variant-numeric:tabular-nums; }
        .kpi-card .kpi-sub { font-size:0.74rem; color:var(--ink-500); margin-top:0.35rem; font-weight:500; }
        .kpi-total    .kpi-icon { background:var(--brand-100); } .kpi-total .kpi-icon svg { color:var(--brand-700); }
        .kpi-valid    .kpi-icon { background:var(--success-bg); } .kpi-valid .kpi-icon svg { color:var(--success-fg); }
        .kpi-pending  .kpi-icon { background:var(--warning-bg); } .kpi-pending .kpi-icon svg { color:var(--warning-fg); }
        .kpi-tolak    .kpi-icon { background:var(--danger-bg); } .kpi-tolak .kpi-icon svg { color:var(--danger-fg); }

        .kpi-status {
            position:absolute; top:1.2rem; right:1.3rem; font-size:1.5rem; font-weight:800; font-variant-numeric:tabular-nums;
        }
        .sub-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.3rem; margin-bottom:1.3rem; }
        .sub-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1.3rem 1.4rem; box-shadow:var(--shadow-sm); }
        .sub-card h3 { font-size:0.82rem; font-weight:700; color:var(--ink-900); margin-bottom:1rem; display:flex; align-items:center; gap:7px; }
        .sub-card h3 svg { width:15px; height:15px; color:var(--brand-500); }
        .chart-wrap { position:relative; height:230px; }

        .mini-stat { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px dashed var(--border); font-size:0.82rem; }
        .mini-stat:last-child { border-bottom:none; }
        .mini-stat .lbl { color:var(--ink-500); font-weight:500; }
        .mini-stat .val { color:var(--ink-900); font-weight:700; font-variant-numeric:tabular-nums; }

        .card { background:var(--surface); border-radius:var(--radius-lg); box-shadow:var(--shadow-md); border:1px solid var(--border); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        thead th { background:var(--brand-900); color:#fff; padding:0.8rem 1rem; font-size:0.72rem; font-weight:700; text-align:left; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
        tbody td { padding:0.72rem 1rem; font-size:0.83rem; border-bottom:1px solid var(--border); color:var(--ink-700); vertical-align:middle; }
        tbody tr { transition:background 0.12s; }
        tbody tr:hover { background:var(--brand-100); }
        tbody tr:last-child td { border-bottom:none; }
        tbody td strong { color:var(--ink-900); font-weight:700; }
        .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 11px; border-radius:999px; font-size:0.72rem; font-weight:700; border:1px solid transparent; }
        .status-badge::before { content:''; width:6px; height:6px; border-radius:50%; }
        .status-tervalidasi { background:var(--success-bg); color:var(--success-fg); border-color:var(--success-line); } .status-tervalidasi::before{background:var(--success-fg);}
        .status-pending { background:var(--warning-bg); color:var(--warning-fg); border-color:var(--warning-line); } .status-pending::before{background:var(--warning-fg);}
        .status-ditolak { background:var(--danger-bg); color:var(--danger-fg); border-color:var(--danger-line); } .status-ditolak::before{background:var(--danger-fg);}
        .btn-link { color:var(--brand-700); font-weight:700; font-size:0.82rem; text-decoration:none; }
        .btn-link:hover { text-decoration:underline; }

        .empty { text-align:center; padding:3rem 1rem; color:var(--ink-500); }
        @media (max-width:1100px){ .kpi-grid{grid-template-columns:repeat(2,1fr);} .sub-grid{grid-template-columns:1fr;} }
        @media (max-width:700px){ .kpi-grid{grid-template-columns:1fr;} .container{padding:1rem;} }
    </style>
</head>
<body>
    @include('partials.sidebar', ['active' => 'dashboard'])
    <div class="container">
        <div class="page-header">
            <div class="title-block">
                <h2>Dashboard Monitoring Gangguan Penyulang</h2>
                <p>Ringkasan kondisi dan statistik data gangguan dari Google Spreadsheet</p>
            </div>
            <div class="live-badge">Live Data</div>
        </div>

        <form class="filter-card" method="GET" action="{{ route('dashboard') }}">
            <div>
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Filter Rentang Tanggal</h3>
            </div>
            <div class="filter-field">
                <label>Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from }}">
            </div>
            <div class="filter-field">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to }}">
            </div>
            <button type="submit" class="btn-primary">Terapkan</button>
            <a href="{{ route('dashboard') }}" class="btn-ghost">Reset</a>
        </form>

        <div class="kpi-grid">
            <div class="kpi-card kpi-total">
                <div class="kpi-top"><div class="kpi-label">Total Laporan</div>
                    <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></div>
                </div>
                <div class="kpi-value">{{ $kpis['total'] }}</div>
                <div class="kpi-sub">Total gangguan tercatat</div>
            </div>
            <div class="kpi-card kpi-valid">
                <div class="kpi-top"><div class="kpi-label">Tervalidasi</div>
                    <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></div>
                </div>
                <div class="kpi-value">{{ $kpis['tervalidasi'] }}</div>
                <div class="kpi-sub">{{ $kpis['persen_validasi'] }}% dari total</div>
            </div>
            <div class="kpi-card kpi-pending">
                <div class="kpi-top"><div class="kpi-label">Pending</div>
                    <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
                </div>
                <div class="kpi-value">{{ $kpis['pending'] }}</div>
                <div class="kpi-sub">Menunggu validasi</div>
            </div>
            <div class="kpi-card kpi-tolak">
                <div class="kpi-top"><div class="kpi-label">Ditolak</div>
                    <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg></div>
                </div>
                <div class="kpi-value">{{ $kpis['ditolak'] }}</div>
                <div class="kpi-sub">Laporan ditolak</div>
            </div>
        </div>

        <div class="sub-grid">
            <div class="sub-card">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg> Gangguan per Hari</h3>
                <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
            </div>
            <div class="sub-card">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2 5 9h3v4h8V9h3L12 2z"/><path d="M9 21h6M10 21v-8h4v8"/></svg> Statistik Penanganan</h3>
                <div class="mini-stat"><span class="lbl">Rata-rata lama padam</span><span class="val">{{ $kpis['avg_lama'] }}</span></div>
                <div class="mini-stat"><span class="lbl">Lama padam terlama</span><span class="val">{{ $kpis['terlama'] }}</span></div>
                <div class="mini-stat"><span class="lbl">Lama padam tercepat</span><span class="val">{{ $kpis['tercepat'] }}</span></div>
                <div class="mini-stat"><span class="lbl">Unit UP3 terdampak</span><span class="val">{{ $kpis['total_up3'] }}</span></div>
                <div class="mini-stat"><span class="lbl">Unit ULP terdampak</span><span class="val">{{ $kpis['total_ulp'] }}</span></div>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr><th>No</th><th>Nama Penyulang</th><th>Unit UP3</th><th>Unit ULP</th><th>Tgl Padam</th><th>Lama Padam</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($terbaru as $i => $it)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $it['nama_penyulang'] }}</strong></td>
                        <td>{{ $it['unit_up3'] }}</td>
                        <td>{{ $it['unit_ulp'] }}</td>
                        <td>{{ $it['tgl_padam'] }}</td>
                        <td>{{ $it['lama_padam'] ?: '-' }}</td>
                        <td>
                            @php
                                $st = str_replace(['✅','❌'], '', $it['status'] ?? '');
                                $cls = 'status-pending';
                                if (str_contains($st, 'Tervalidasi')) $cls = 'status-tervalidasi';
                                elseif (str_contains($st, 'Ditolak')) $cls = 'status-ditolak';
                            @endphp
                            <span class="status-badge {{ $cls }}">{{ $st ?: 'Pending' }}</span>
                        </td>
                        <td>
                            <a class="btn-link" href="{{ route('laporan.detail', ['idx' => array_search($it, $kpis['filtered'], true)]) }}">Buka Detail →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8"><div class="empty">Tidak ada data dalam rentang tanggal ini.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const perHari = @json($kpis['per_hari']);
        const labels = Object.keys(perHari);
        const values = Object.values(perHari);
        const ctx = document.getElementById('trendChart');
        if (ctx && labels.length) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah gangguan',
                        data: values,
                        backgroundColor: 'rgba(20,162,186,0.65)',
                        borderColor: '#125d72',
                        borderWidth: 2,
                        borderRadius: 8,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, color: '#64748b' }, grid: { color: '#eef1f5' } },
                        x: { ticks: { color: '#64748b' }, grid: { display: false } }
                    }
                }
            });
        }
    </script>
</body>
</html>
