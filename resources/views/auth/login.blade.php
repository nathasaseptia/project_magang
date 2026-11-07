<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PLN WebGIS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; height: 100vh; overflow: hidden; }

        .split-container {
            display: flex;
            height: 100vh;
        }

        /* ===== LEFT PANEL: Ilustrasi ===== */
        .left-panel {
            width: 55%;
            background: linear-gradient(160deg, #0a3d52 0%, #125d72 40%, #14a2ba 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 3rem;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(20,162,186,0.3) 0%, transparent 70%);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .illustration-wrapper {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .illustration-wrapper svg {
            width: 380px;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.2));
        }

        .brand-text {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 2.5rem;
        }
        .brand-text h1 {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }
        .brand-text h1 span { color: #7dd3e8; }
        .brand-text p {
            color: rgba(255,255,255,0.65);
            font-size: 0.95rem;
            margin-top: 0.75rem;
            font-weight: 400;
        }

        .floating-dots {
            position: absolute;
            z-index: 1;
        }
        .dot {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            animation: float 6s ease-in-out infinite;
        }
        .dot:nth-child(1) { width: 8px; height: 8px; top: 15%; left: 10%; animation-delay: 0s; }
        .dot:nth-child(2) { width: 12px; height: 12px; top: 25%; right: 15%; animation-delay: 1s; }
        .dot:nth-child(3) { width: 6px; height: 6px; bottom: 20%; left: 20%; animation-delay: 2s; }
        .dot:nth-child(4) { width: 10px; height: 10px; bottom: 30%; right: 25%; animation-delay: 3s; }
        .dot:nth-child(5) { width: 14px; height: 14px; top: 60%; left: 5%; animation-delay: 1.5s; }
        .dot:nth-child(6) { width: 7px; height: 7px; top: 10%; left: 45%; animation-delay: 0.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); opacity: 0.6; }
            50% { transform: translateY(-20px); opacity: 1; }
        }

        /* ===== RIGHT PANEL: Form Login ===== */
        .right-panel {
            width: 45%;
            background: #f8fbfd;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-card .logo-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2.5rem;
        }
        .login-card .logo-top .logo-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #125d72, #14a2ba);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        .login-card .logo-top .logo-text {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.4;
        }
        .login-card .logo-top .logo-text strong {
            display: block;
            color: #125d72;
            font-size: 1rem;
            font-weight: 700;
        }

        .login-card h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
            letter-spacing: -0.03em;
        }
        .login-card .subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
            letter-spacing: 0.02em;
        }
        .form-group .input-wrap {
            position: relative;
        }
        .form-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
            outline: none;
        }
        .form-group input:focus {
            border-color: #14a2ba;
            box-shadow: 0 0 0 4px rgba(20,162,186,0.1);
        }
        .form-group input::placeholder {
            color: #cbd5e1;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            color: #64748b;
            cursor: pointer;
        }
        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #14a2ba;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #125d72 0%, #14a2ba 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(20,162,186,0.35);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #cbd5e1;
        }

        @media (max-width: 900px) {
            .split-container { flex-direction: column; }
            .left-panel { width: 100%; min-height: 260px; padding: 2rem; }
            .left-panel::before, .left-panel::after { display: none; }
            .illustration-wrapper svg { width: 200px; }
            .brand-text h1 { font-size: 1.3rem; }
            .brand-text p { font-size: 0.8rem; }
            .right-panel { width: 100%; padding: 2rem; overflow-y: auto; }
            body { overflow: auto; }
        }
    </style>
</head>
<body>
    <div class="split-container">

        <!-- LEFT PANEL: Ilustrasi Kelistrikan PLN -->
        <div class="left-panel">
            <div class="floating-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>

            <div class="illustration-wrapper">
                <!-- SVG Ilustrasi Kelistrikan / Menara Transmisi -->
                <svg viewBox="0 0 400 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background glow -->
                    <circle cx="200" cy="200" r="160" fill="url(#glow)" opacity="0.15"/>

                    <!-- Ground -->
                    <ellipse cx="200" cy="380" rx="180" ry="20" fill="rgba(255,255,255,0.08)"/>

                    <!-- Tower base -->
                    <rect x="185" y="200" width="30" height="180" rx="4" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>
                    <rect x="170" y="200" width="60" height="8" rx="2" fill="rgba(255,255,255,0.3)"/>
                    <rect x="175" y="280" width="50" height="6" rx="2" fill="rgba(255,255,255,0.25)"/>
                    <rect x="180" y="340" width="40" height="5" rx="2" fill="rgba(255,255,255,0.2)"/>

                    <!-- Cross arms -->
                    <rect x="120" y="195" width="160" height="6" rx="3" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.5)" stroke-width="1"/>
                    <rect x="140" y="245" width="120" height="5" rx="2.5" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>

                    <!-- Insulators -->
                    <circle cx="130" cy="192" r="6" fill="#7dd3e8" stroke="#fff" stroke-width="1.5"/>
                    <circle cx="200" cy="192" r="6" fill="#7dd3e8" stroke="#fff" stroke-width="1.5"/>
                    <circle cx="270" cy="192" r="6" fill="#7dd3e8" stroke="#fff" stroke-width="1.5"/>
                    <circle cx="150" cy="242" r="5" fill="#7dd3e8" stroke="#fff" stroke-width="1.2"/>
                    <circle cx="250" cy="242" r="5" fill="#7dd3e8" stroke="#fff" stroke-width="1.2"/>

                    <!-- Power lines (cables) -->
                    <path d="M100 188 Q150 175 200 188 Q250 200 300 188" stroke="#7dd3e8" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    <path d="M120 240 Q175 228 200 237 Q225 246 280 240" stroke="#7dd3e8" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.7"/>

                    <!-- Lightning bolt / Energy -->
                    <path d="M195 60 L180 120 L198 120 L183 180" stroke="#facc15" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M205 55 L218 105 L202 105 L215 160" stroke="#facc15" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.6"/>

                    <!-- Energy glow -->
                    <circle cx="195" cy="60" r="8" fill="#facc15" opacity="0.6">
                        <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.6;0.2;0.6" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="195" cy="60" r="4" fill="#fff" opacity="0.9">
                        <animate attributeName="opacity" values="0.9;0.4;0.9" dur="2s" repeatCount="indefinite"/>
                    </circle>

                    <!-- Small house on right -->
                    <rect x="300" y="330" width="55" height="45" rx="3" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                    <polygon points="295,330 327,305 360,330" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                    <rect x="318" y="348" width="14" height="20" rx="2" fill="rgba(20,162,186,0.4)"/>
                    <rect x="305" y="340" width="10" height="10" rx="1" fill="rgba(250,204,21,0.3)"/>
                    <rect x="340" y="340" width="10" height="10" rx="1" fill="rgba(250,204,21,0.3)"/>

                    <!-- Power line to house -->
                    <path d="M280 240 Q290 280 310 305" stroke="#7dd3e8" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.5" stroke-dasharray="4 4"/>

                    <!-- Small house on left -->
                    <rect x="50" y="340" width="45" height="35" rx="3" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                    <polygon points="45,340 72,320 100,340" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>

                    <defs>
                        <radialGradient id="glow">
                            <stop offset="0%" stop-color="#7dd3e8"/>
                            <stop offset="100%" stop-color="transparent"/>
                        </radialGradient>
                    </defs>
                </svg>
            </div>

            <div class="brand-text">
                <h1>Sistem Pemetaan Spasial<br><span>Gangguan Penyulang PLN</span></h1>
                <p>WebGIS &mdash; Monitoring & Visualisasi Data Gangguan Penyulang Secara Realtime</p>
            </div>
        </div>

        <!-- RIGHT PANEL: Form Login -->
        <div class="right-panel">
            <div class="login-card">
                <div class="logo-top">
                    <div class="logo-box">PLN</div>
                    <div class="logo-text">
                        <strong>PLN WebGIS</strong>
                        Portal Sistem Informasi Geografis
                    </div>
                </div>

                <h2>Selamat Datang</h2>
                <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

                @if ($errors->any())
                <div class="error-msg">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 100 14A7 7 0 008 1zM7.25 5a.75.75 0 011.5 0v3a.75.75 0 01-1.5 0V5zM8 10.5a.75.75 0 100 1.5.75.75 0 000-1.5z"/></svg>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13 2 4"/></svg>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@pln.co.id" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>

                <p class="footer-text">PLN WebGIS &copy; 2026 &mdash; PT Perusahaan Listrik Negara</p>
            </div>
        </div>
    </div>
</body>
</html>
