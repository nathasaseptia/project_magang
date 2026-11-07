@php
$menu = [
    'dashboard' => [
        'Dashboard',
        route('dashboard'),
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
    ],
    'peta' => [
        'Peta Lokasi',
        route('peta'),
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
    ],
    'arsip' => [
        'Arsip Data',
        route('arsip'),
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>',
    ],
    'manajemen-row' => [
        'Manajemen ROW',
        route('manajemen-row'),
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 5 9h3v4h8V9h3L12 2z"/><path d="M9 21h6"/><path d="M10 21v-8h4v8"/></svg>',
    ],
    'ramalan-cuaca' => [
        'Ramalan Cuaca',
        route('ramalan-cuaca'),
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9z"/></svg>',
    ],
];
@endphp

<style>
    :root { --sb-w: 234px; }
    .container { margin-left: var(--sb-w); }

    .sb-toggle {
        position: fixed; top: 14px; left: 14px; z-index: 9500;
        display: none; align-items: center; justify-content: center;
        width: 42px; height: 42px; border-radius: 12px; cursor: pointer;
        background: linear-gradient(135deg, #14a2ba, #125d72);
        border: none; color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
    }
    .sb-toggle svg { width: 20px; height: 20px; }

    .sb {
        position: fixed; top: 0; left: 0; bottom: 0; width: var(--sb-w); z-index: 9000;
        background: linear-gradient(180deg, #0d2f3c 0%, #0b2530 100%);
        border-right: 1px solid rgba(255,255,255,0.1);
        box-shadow: 4px 0 24px rgba(0,0,0,0.35);
        display: flex; flex-direction: column;
        padding: 1.1rem 0.9rem;
    }
    .sb-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 0.4rem 1.1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sb-logo {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: transparent;
}

.sb-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;

    }
    .sb-brand strong { color: #fff; font-size: 0.92rem; display: block; }
    .sb-brand span { color: #7dd3e3; font-size: 0.66rem; }
    .sb-label {
        color: #5b7a86; font-size: 0.62rem; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        padding: 1rem 0.6rem 0.45rem;
    }
    .sb-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; overflow-y: auto; }
    .sb-link {
        display: flex; align-items: center; gap: 11px;
        padding: 0.62rem 0.7rem; border-radius: 10px;
        color: #a8c4ce; text-decoration: none;
        font-size: 0.82rem; font-weight: 500; transition: all 0.2s;
    }
    .sb-link svg { width: 18px; height: 18px; flex-shrink: 0; color: #5b7a86; transition: color 0.2s; }
    .sb-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .sb-link:hover svg { color: #14a2ba; }
    .sb-link.active {
        background: linear-gradient(135deg, #14a2ba, #125d72); color: #fff;
        box-shadow: 0 4px 14px rgba(20,162,186,0.35);
    }
    .sb-link.active svg { color: #fff; }
    .sb-foot { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.8rem; }
    .sb-logout {
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
        background: rgba(239,68,68,0.15); color: #fca5a5;
        border: 1px solid rgba(239,68,68,0.35);
        padding: 0.55rem; border-radius: 10px;
        font-size: 0.8rem; font-weight: 600; cursor: pointer;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; transition: all 0.2s;
    }
    .sb-logout:hover { background: rgba(239,68,68,0.3); color: #fecaca; }
    .sb-overlay { display: none; }

    @media (max-width: 900px) {
        .container { margin-left: 0; }
        .sb { transform: translateX(-100%); transition: transform 0.3s ease; }
        .sb.open { transform: translateX(0); }
        .sb-toggle { display: flex; }
        .sb-overlay.show {
            display: block; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 8999;
        }
    }
</style>

<button class="sb-toggle" id="sbToggle" aria-label="Buka menu">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<aside class="sb" id="sidebar">
    <div class="sb-brand">
    <div class="sb-logo">
       <div class="sb-logo">
    <img src="{{ asset('assets/logo-pln.png') }}" alt="Logo PLN">
</div>
    </div>

    <div>
        <strong>PLN SCADA</strong>
        <span>Command Center</span>
    </div>
</div>
    <div class="sb-label">Menu Utama</div>
    <nav class="sb-nav">
        @foreach ($menu as $key => $m)
            <a href="{{ $m[1] }}" class="sb-link {{ $active === $key ? 'active' : '' }}">
                {!! $m[2] !!}
                <span>{{ $m[0] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="sb-foot">
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="sb-logout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<div class="sb-overlay" id="sbOverlay"></div>

<script>
    (function () {
        var sb = document.getElementById('sidebar');
        var ov = document.getElementById('sbOverlay');
        var tg = document.getElementById('sbToggle');
        if (tg) tg.addEventListener('click', function () {
            sb.classList.toggle('open');
            ov.classList.toggle('show');
        });
        if (ov) ov.addEventListener('click', function () {
            sb.classList.remove('open');
            ov.classList.remove('show');
        });
    })();
</script>
