<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pln-navy: #0a2540;
            --pln-blue: #0f5c99;
            --pln-teal: #14a2ba;
            --pln-cyan: #7dd3e8;
            --pln-yellow: #ffc72c;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background: #f6f9fc;
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            overflow-x: hidden;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 0.92fr 1.08fr;
            background:
                radial-gradient(760px 420px at 100% 0%, rgba(20, 162, 186, 0.10), transparent 62%),
                radial-gradient(560px 320px at 0% 100%, rgba(15, 92, 153, 0.08), transparent 62%),
                #f6f9fc;
        }

        .showcase {
            display: flex;
            padding: clamp(1rem, 2vw, 1.6rem);
        }

        .photo-card {
            position: relative;
            flex: 1;
            min-height: calc(100vh - clamp(2rem, 4vw, 3.2rem));
            border-radius: 26px;
            overflow: hidden;
            background:
                radial-gradient(900px 460px at 20% 0%, rgba(20, 162, 186, 0.42), transparent 62%),
                linear-gradient(155deg, #082033 0%, #0a3350 48%, #0e5875 100%);
            box-shadow: 0 30px 70px -34px rgba(10, 37, 64, 0.5);
            isolation: isolate;
        }
        .photo-card > img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }
        .photo-card::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(180deg, rgba(8, 32, 51, 0.55) 0%, rgba(8, 32, 51, 0.08) 32%, rgba(8, 32, 51, 0.12) 52%, rgba(8, 32, 51, 0.82) 100%),
                linear-gradient(90deg, rgba(8, 32, 51, 0.35) 0%, transparent 55%);
            pointer-events: none;
        }

        .photo-top,
        .photo-bottom {
            position: absolute;
            left: 0;
            right: 0;
            z-index: 2;
            padding: clamp(1.2rem, 2.4vw, 1.9rem);
            color: #fff;
        }
        .photo-top {
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .photo-bottom { bottom: 0; }
        .brand { display: flex; align-items: center; gap: 0.7rem; }
        .brand-logo {
            display: flex;
            align-items: center;
            height: 56px;
            flex: 0 0 auto;
        }
        .brand-logo img {
            height: 100%;
            width: auto;
            display: block;
            filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.55)) drop-shadow(0 0 1px rgba(255, 255, 255, 0.4));
        }
        .brand-text strong {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.15;
        }
        .brand-text span {
            display: block;
            margin-top: 3px;
            font-size: 0.73rem;
            font-weight: 500;
            letter-spacing: 0.01em;
            color: rgba(255, 255, 255, 0.72);
        }

        .live-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #d7fbff;
            background: rgba(8, 32, 51, 0.4);
            border: 1px solid rgba(125, 211, 232, 0.42);
            border-radius: 999px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            white-space: nowrap;
        }
        .live-chip::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34e0c8;
            box-shadow: 0 0 0 4px rgba(52, 224, 200, 0.22);
            animation: livePulse 1.8s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(52, 224, 200, 0.22); }
            50% { box-shadow: 0 0 0 7px rgba(52, 224, 200, 0.06); }
        }

        .photo-bottom h1 {
            font-size: clamp(1.45rem, 2.1vw, 1.95rem);
            line-height: 1.22;
            font-weight: 800;
            letter-spacing: -0.015em;
            max-width: 20rem;
        }
        .photo-bottom h1 span {
            background: linear-gradient(92deg, #7dd3e8 0%, #34e0c8 50%, #ffc72c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .photo-bottom p {
            margin-top: 0.55rem;
            max-width: 22rem;
            font-size: 0.82rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.76);
        }

        .form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1.5rem, 4vw, 3.5rem);
        }

        .form-card {
            position: relative;
            width: 100%;
            max-width: 430px;
            padding: clamp(1.6rem, 3vw, 2.6rem);
            background: #fff;
            border: 1px solid #e6eef5;
            border-radius: 24px;
            box-shadow:
                0 26px 60px -26px rgba(10, 37, 64, 0.32),
                0 2px 8px rgba(10, 37, 64, 0.05);
        }
        .form-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            border-radius: 24px 24px 0 0;
            background: linear-gradient(90deg, var(--pln-blue), var(--pln-teal) 55%, var(--pln-yellow));
        }

        .card-brand { display: flex; align-items: center; gap: 0.7rem; margin-bottom: 1.6rem; }
        .card-brand .logo {
            display: flex;
            align-items: center;
            height: 50px;
            flex: 0 0 auto;
        }
        .card-brand .logo img { height: 100%; width: auto; display: block; }
        .card-brand strong {
            display: block;
            font-size: 0.98rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.15;
            color: var(--pln-navy);
        }
        .card-brand span {
            display: block;
            margin-top: 3px;
            font-size: 0.74rem;
            font-weight: 500;
            color: var(--muted);
        }

        .form-card h2 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.25;
            color: var(--pln-navy);
            margin-bottom: 0.4rem;
        }
        .form-card .subtitle {
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 500;
            line-height: 1.6;
            margin-bottom: 1.8rem;
        }

        .error-msg {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 1.2rem;
            padding: 0.75rem 0.95rem;
            font-size: 0.82rem;
            color: #c0281f;
            background: #fdeaea;
            border: 1px solid #f6c6c3;
            border-radius: 12px;
        }
        .error-msg svg { flex: 0 0 auto; margin-top: 1px; }

        .form-group { margin-bottom: 1.15rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.01em;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-group input {
            width: 100%;
            padding: 0.78rem 1rem 0.78rem 2.75rem;
            font-family: inherit;
            font-size: 0.9rem;
            color: #1e293b;
            background: #fbfdff;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-group input.has-toggle { padding-right: 3rem; }
        .form-group input::placeholder { color: #b6c2d1; }
        .form-group input:focus {
            background: #fff;
            border-color: var(--pln-teal);
            box-shadow: 0 0 0 4px rgba(20, 162, 186, 0.12);
        }
        .input-wrap:focus-within .input-icon { color: var(--pln-teal); }

        .toggle-pass {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            padding: 7px;
            color: #94a3b8;
            background: transparent;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
        }
        .toggle-pass:hover { color: var(--pln-teal); background: #eff7fb; }
        .toggle-pass svg { width: 18px; height: 18px; }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.25rem 0 1.6rem;
        }
        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.81rem;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }
        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--pln-teal);
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.95rem;
            font-family: inherit;
            font-size: 0.94rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #0f5c99 0%, #14a2ba 62%, #17b3a3 100%);
            border: none;
            border-radius: 13px;
            cursor: pointer;
            box-shadow: 0 14px 28px -12px rgba(20, 162, 186, 0.62);
            transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 18px 34px -12px rgba(20, 162, 186, 0.72);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { cursor: wait; opacity: 0.8; transform: none; }
        .btn-login .btn-arrow { width: 18px; height: 18px; transition: transform 0.18s; }
        .btn-login:hover .btn-arrow { transform: translateX(3px); }
        .btn-login .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        .btn-login.is-loading .spinner { display: inline-block; }
        .btn-login.is-loading .btn-arrow { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1.35rem;
            font-size: 0.71rem;
            color: #94a3b8;
            text-align: center;
        }
        .secure-note svg { width: 14px; height: 14px; flex: 0 0 auto; }

        .footer-text {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.72rem;
            color: #b6c2d1;
        }

        @media (max-width: 960px) {
            .login-shell { grid-template-columns: 1fr; }
            .showcase { padding: 1rem 1rem 0; }
            .photo-card { min-height: 230px; }
            .photo-bottom h1 { font-size: 1.3rem; max-width: 16rem; }
            .photo-bottom p { display: none; }
            .form-side { padding: 1.25rem 1rem 2rem; }
            .form-card { max-width: 460px; }
        }

        @media (max-width: 420px) {
            .brand-logo { height: 44px; }
            .card-brand .logo { height: 44px; }
            .live-chip { display: none; }
            .form-card { padding: 1.4rem; border-radius: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <aside class="showcase">
            <div class="photo-card">
                <img src="{{ asset('assets/pln-field.jpg') }}" alt="Petugas PLN sedang bertugas di lapangan" onerror="this.style.display='none'">

                <div class="photo-top">
                    <div class="brand">
                        <div class="brand-logo">
                            <img src="{{ asset('assets/logo-pln-full.png') }}" alt="Logo PLN">
                        </div>
                        <div class="brand-text">
                            <strong>PLN WebGIS</strong>
                            <span>Command Center &middot; Distribusi</span>
                        </div>
                    </div>
                    <span class="live-chip">Realtime</span>
                </div>

                <div class="photo-bottom">
                    <h1>Petugas di Lapangan,<br><span>Data di Ujung Jari</span></h1>
                    <p>Sinergi petugas lapangan dan command center dalam menjaga keandalan jaringan distribusi listrik.</p>
                </div>
            </div>
        </aside>

        <section class="form-side">
            <div class="form-card">
                <div class="card-brand">
                    <div class="logo">
                        <img src="{{ asset('assets/logo-pln-full.png') }}" alt="Logo PLN">
                    </div>
                    <div>
                        <strong>PLN WebGIS</strong>
                        <span>Portal Sistem Informasi Geografis</span>
                    </div>
                </div>

                <h2>Selamat Datang</h2>
                <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

                @if ($errors->any())
                <div class="error-msg" role="alert">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1a7 7 0 100 14A7 7 0 008 1zM7.25 5a.75.75 0 011.5 0v3a.75.75 0 01-1.5 0V5zM8 10.5a.75.75 0 100 1.5.75.75 0 000-1.5z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13 2 4"/></svg>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@pln.co.id" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </span>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                            <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan kata sandi">
                                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="spinner" aria-hidden="true"></span>
                        <span class="btn-label">Masuk</span>
                        <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>

                <div class="secure-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Koneksi aman &mdash; akses terbatas untuk personel berwenang
                </div>

                <p class="footer-text">PLN WebGIS &copy; 2026 &middot; PT Perusahaan Listrik Negara</p>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var toggle = document.getElementById('togglePass');
            var pass = document.getElementById('password');
            var eye = document.getElementById('eyeIcon');
            var openEye = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>';
            var closedEye = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';

            if (toggle && pass) {
                toggle.addEventListener('click', function () {
                    var show = pass.type === 'password';
                    pass.type = show ? 'text' : 'password';
                    eye.innerHTML = show ? closedEye : openEye;
                    toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                });
            }

            var form = document.getElementById('loginForm');
            var btn = document.getElementById('loginBtn');
            if (form && btn) {
                form.addEventListener('submit', function () {
                    btn.classList.add('is-loading');
                    btn.disabled = true;
                    btn.querySelector('.btn-label').textContent = 'Memproses...';
                });
            }
        })();
    </script>
</body>
</html>
