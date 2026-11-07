<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-900: #0c3a47; --brand-800: #0f4c5c; --brand-700: #125d72; --brand-500: #14a2ba;
            --brand-100: #e6f6fa; --ink-900: #101b23; --ink-700: #33475a; --ink-500: #64748b;
            --surface:#fff; --canvas:#f3f6f9; --border:#e3e9ef;
            --success-bg:#e7f7ef; --success-fg:#0a7a4d; --success-line:#b6e8cf;
            --warning-bg:#fef3e2; --warning-fg:#a2660a; --warning-line:#f7dfab;
            --neutral-bg:#eef1f5; --neutral-fg:#47566a; --neutral-line:#dde3ea;
            --radius-lg:16px; --radius-md:12px; --radius-sm:8px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter','Segoe UI',sans-serif; background:var(--canvas); color:var(--ink-900); min-height:100vh; }
        .container { padding:1.8rem 2.5rem 2.5rem; max-width:1000px; margin:0 auto; }

        .back-link { display:inline-flex; align-items:center; gap:7px; color:var(--brand-700); font-weight:600; font-size:0.85rem; text-decoration:none; margin-bottom:1.2rem; }
        .back-link:hover { text-decoration:underline; }
        .back-link svg { width:15px; height:15px; }

        .card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:0 6px 20px -6px rgba(16,27,35,0.12); overflow:hidden; }
        .card-head { background:linear-gradient(120deg,var(--brand-900) 0%,var(--brand-500) 130%); padding:1.4rem 1.7rem; color:#fff; }
        .card-head h2 { font-size:1.35rem; font-weight:800; letter-spacing:-0.01em; }
        .card-head .sub { color:rgba(255,255,255,0.75); font-size:0.82rem; margin-top:0.25rem; display:flex; align-items:center; gap:0.6rem; }
        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 12px; border-radius:999px; font-size:0.74rem; font-weight:700; }
        .badge::before { content:''; width:6px; height:6px; border-radius:50%; }
        .badge-tervalidasi { background:#fff; color:var(--success-fg); } .badge-tervalidasi::before{background:var(--success-fg);}
        .badge-pending { background:#fff; color:var(--warning-fg); } .badge-pending::before{background:var(--warning-fg);}
        .badge-ditolak { background:#fff; color:var(--danger-fg); } .badge-ditolak::before{background:var(--danger-fg);}

        .card-body { padding:1.7rem; }
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem 1.25rem; }
        .detail-item { background:var(--canvas); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.8rem 1rem; }
        .detail-item .label { color:var(--ink-500); font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px; }
        .detail-item .value { color:var(--ink-900); font-size:0.9rem; font-weight:600; font-variant-numeric:tabular-nums; }
        .detail-item .value.mono { font-family:'JetBrains Mono',monospace; }

        .section-title { font-size:0.82rem; font-weight:700; color:var(--ink-900); text-transform:uppercase; letter-spacing:0.04em; margin:1.6rem 0 0.8rem; padding-bottom:0.5rem; border-bottom:1.5px solid var(--border); display:flex; align-items:center; gap:6px; }
        .section-title svg { width:14px; height:14px; color:var(--brand-500); }

        .coord-display { background:var(--brand-100); border:1px solid #cdeef4; border-radius:var(--radius-sm); padding:0.65rem 1rem; display:flex; flex-wrap:wrap; gap:1.2rem; font-size:0.8rem; }
        .coord-display .cl { color:var(--ink-500); font-weight:600; margin-right:4px; }
        .coord-display .cv { color:var(--brand-800); font-weight:700; font-family:'JetBrains Mono',monospace; }

        .btn-back { display:inline-block; background:var(--canvas); color:var(--ink-700); border:1px solid var(--border); padding:0.6rem 1.3rem; border-radius:var(--radius-sm); font-size:0.85rem; font-weight:600; text-decoration:none; margin-top:1.4rem; font-family:inherit; transition:all 0.15s; }
        .btn-back:hover { color:var(--ink-900); border-color:var(--ink-300); }

        .foto-wrap { margin-top:1rem; }
        .foto-wrap img { max-width:100%; border-radius:var(--radius-md); border:1px solid var(--border); }

        @media (max-width:700px){ .detail-grid{grid-template-columns:1fr;} .container{padding:1.2rem;} }
    </style>
</head>
<body>
    @include('partials.sidebar', ['active' => 'arsip'])
    <div class="container">
        <a class="back-link" href="{{ url()->previous() ?: route('dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>

        <div class="card">
            <div class="card-head">
                <h2>{{ $item['nama_penyulang'] }}</h2>
                <div class="sub">
                    <span>{{ $item['unit_up3'] }} / {{ $item['unit_ulp'] }}</span>
                    @php
                        $raw = str_replace(['✅','❌'], '', $item['status'] ?? '');
                        $cls = 'badge-pending'; $lbl = $raw ?: 'Pending';
                        if (str_contains($raw, 'Tervalidasi')) { $cls='badge-tervalidasi'; $lbl='Tervalidasi'; }
                        elseif (str_contains($raw, 'Ditolak')) { $cls='badge-ditolak'; $lbl='Ditolak'; }
                    @endphp
                    <span class="badge {{ $cls }}">{{ $lbl }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item"><div class="label">Nama Penyulang</div><div class="value">{{ $item['nama_penyulang'] }}</div></div>
                    <div class="detail-item"><div class="label">Unit UP3</div><div class="value">{{ $item['unit_up3'] }}</div></div>
                    <div class="detail-item"><div class="label">Unit ULP</div><div class="value">{{ $item['unit_ulp'] }}</div></div>
                    <div class="detail-item"><div class="label">Tanggal Padam</div><div class="value">{{ $item['tgl_padam'] ?: '-' }}</div></div>
                    <div class="detail-item"><div class="label">Jam Padam</div><div class="value mono">{{ $item['jam_padam'] ?: '-' }}</div></div>
                    <div class="detail-item"><div class="label">Tanggal Nyala</div><div class="value">{{ $item['tgl_nyala'] ?: '-' }}</div></div>
                    <div class="detail-item"><div class="label">Jam Nyala</div><div class="value mono">{{ $item['jam_nyala'] ?: '-' }}</div></div>
                    <div class="detail-item"><div class="label">Lama Padam</div><div class="value mono">{{ $item['lama_padam'] ?: '-' }}</div></div>
                    <div class="detail-item"><div class="label">Status</div><div class="value">{{ $raw ?: 'Pending' }}</div></div>
                    <div class="detail-item"><div class="label">Jumlah Data Total</div><div class="value">{{ $total }} laporan</div></div>
                </div>

                <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20.5 10c0 6-8.5 12-8.5 12S3.5 16 3.5 10a8.5 8.5 0 0 1 17 0z"/><circle cx="12" cy="10" r="3"/></svg> Lokasi Koordinat</div>
                <div class="coord-display">
                    <div><span class="cl">Latitude:</span><span class="cv">{{ $item['latitude'] ?? '-' }}</span></div>
                    <div><span class="cl">Longitude:</span><span class="cv">{{ $item['longitude'] ?? '-' }}</span></div>
                    <div><span class="cl">Sumber:</span><span>{{ !empty($item['latitude_raw']) ? 'Google Sheets / Geotagged' : 'Tidak tersedia' }}</span></div>
                </div>

                @if (!empty($item['foto']))
                <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg> Dokumentasi Foto</div>
                <div class="foto-wrap"><img src="{{ $item['foto'] }}" alt="Foto lokasi" onerror="this.style.display='none'"></div>
                @endif

                <a class="btn-back" href="{{ route('arsip') }}">Kembali ke Arsip</a>
            </div>
        </div>
    </div>
</body>
</html>
