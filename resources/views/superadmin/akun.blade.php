<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f3f2f7;
            color: #1e1b2e;
        }
        .container { margin-left: 234px; padding: 28px 32px; }

        /* Header */
        .page-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .page-head h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
        .page-head p { color: #7a7480; font-size: 0.85rem; margin-top: 4px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem; border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600;
            transition: all 0.25s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #a43d8f, #7b2d6b); color: #fff;
            box-shadow: 0 6px 18px rgba(164,61,143,0.3);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(164,61,143,0.35); }
        .btn-ghost { background: #fff; color: #7b2d6b; border: 1.5px solid #e2dce6; }
        .btn-ghost:hover { background: #f5f0f7; }
        .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.78rem; border-radius: 9px; }
        .btn-danger-ghost { background: #fff; color: #dc2626; border: 1.5px solid #fecaca; }

        /* Flash messages */
        .flash {
            padding: 0.85rem 1rem; border-radius: 12px; margin-bottom: 18px;
            font-size: 0.85rem; font-weight: 500;
        }
        .flash.success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .flash.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Bento Grid */
        .bento-grid {
            display: grid; gap: 16px;
            grid-template-columns: repeat(12, 1fr);
        }
        .bento-card {
            background: #fff; border-radius: 18px; padding: 20px;
            border: 1px solid #eee8f2; box-shadow: 0 4px 20px rgba(30,27,46,0.04);
        }
        .stat-card { position: relative; overflow: hidden; }
        .stat-card .stat-icon {
            position: absolute; right: 16px; top: 16px; opacity: 0.18;
            width: 40px; height: 40px;
        }
        .stat-card h3 { font-size: 0.78rem; font-weight: 600; color: #a05d8d; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .big { font-size: 2.2rem; font-weight: 800; margin-top: 8px; letter-spacing: -0.03em; }
        .stat-card .small { font-size: 0.78rem; color: #7a7480; margin-top: 4px; }
        .col-3 { grid-column: span 3; }
        .col-4 { grid-column: span 4; }
        .col-5 { grid-column: span 5; }
        .col-6 { grid-column: span 6; }
        .col-7 { grid-column: span 7; }
        .col-8 { grid-column: span 8; }
        .col-12 { grid-column: span 12; }

        /* Card title */
        .card-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title h2 { font-size: 1.05rem; font-weight: 700; }
        .badge { font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 999px; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
        thead th {
            text-align: left; padding: 10px 12px; font-size: 0.72rem;
            text-transform: uppercase; letter-spacing: 0.04em; color: #9a8fa4;
            border-bottom: 1.5px solid #eee8f2; white-space: nowrap;
        }
        tbody td { padding: 12px; border-bottom: 1px solid #f3eef6; vertical-align: middle; }
        tbody tr:hover { background: #faf7fc; }

        .role-pill {
            display: inline-block; padding: 4px 12px; border-radius: 999px;
            font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;
        }
        .role-super_admin { background: #ede9fe; color: #6d28d9; }
        .role-admin { background: #e0f2fe; color: #0369a1; }
        .role-operator { background: #f0fdf4; color: #15803d; }

        .scope-chip {
            display: inline-block; padding: 2px 9px; margin: 2px 3px 2px 0;
            border-radius: 6px; font-size: 0.7rem; font-weight: 600; background: #f3eef6; color: #7b2d6b;
        }
        .scope-chip.lv { background: #ede9fe; color: #6d28d9; }
        .scope-chip.grey { background: #f1f5f9; color: #64748b; }

        .actions { display: flex; gap: 6px; }
        .actions button { cursor: pointer; border: none; background: none; padding: 5px; border-radius: 8px; color: #8d8299; transition: all 0.2s; }
        .actions button:hover { background: #f3eef6; }
        .actions button.del:hover { background: #fef2f2; color: #dc2626; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 10000;
            background: rgba(30,27,46,0.5); backdrop-filter: blur(3px);
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff; border-radius: 20px; padding: 26px; width: 100%; max-width: 460px;
            max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,0.25);
        }
        .modal h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #4c4556; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #e2dce6;
            border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 0.85rem; outline: none;
            transition: all 0.2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: #a43d8f; box-shadow: 0 0 0 3px rgba(164,61,143,0.12); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-close { background: #f1f5f9; color: #475569; }
        .btn-close:hover { background: #e2e8f0; }

        .mr-header { display: flex; align-items: center; align-self: center; }
    </style>
</head>
<body>

@include('partials.sidebar-superadmin', ['active' => 'akun'])

<main class="container">
    <div class="page-head">
        <div>
            <h1>Kelola Akun</h1>
            <p>Kelola pengguna, peran, dan lingkup data (UID / UP3 / ULP)</p>
        </div>
        <button class="btn btn-primary" onclick="openModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Akun
        </button>
    </div>

    @if(session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="flash error">{{ $errors->first() }}</div>
    @endif

    <div class="bento-grid">
        <div class="bento-card stat-card col-4">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            <h3>Total Akun</h3>
            <div class="big">{{ $users->count() }}</div>
            <div class="small">Seluruh pengguna terdaftar</div>
        </div>
        <div class="bento-card stat-card col-4">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            <h3>Unit UP3</h3>
            <div class="big">{{ $up3s->count() }}</div>
            <div class="small">Unit Induk Pelaksana Ketenagalistrikan</div>
        </div>
        <div class="bento-card stat-card col-4">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M2 12L12 3l10 9-10 9-10-9z"/></svg>
            <h3>Unit ULP</h3>
            <div class="big">{{ $ulps->count() }}</div>
            <div class="small">Unit Layanan Pelanggan</div>
        </div>

        <div class="bento-card col-12">
            <div class="card-title">
                <h2>Daftar Akun</h2>
                <div class="mr-header">
                    <span class="badge" style="background:#f3eef6;color:#7b2d6b;">{{ $users->count() }} pengguna</span>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Lingkup Data</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td><strong>{{ $u->name }}</strong></td>
                            <td>{{ $u->email }}</td>
                            <td><span class="role-pill role-{{ $u->role }}">{{ $u->role == 'super_admin' ? 'Super Admin' : ($u->role == 'admin' ? 'Admin' : 'Operator') }}</span></td>
                            <td>
                                @if($u->uid)
                                    <span class="scope-chip lv">UID: {{ $u->uid }}</span>
                                @endif
                                @if($u->up3)
                                    <span class="scope-chip">UP3: {{ $u->up3 }}</span>
                                @endif
                                @if($u->ulp)
                                    <span class="scope-chip">ULP: {{ $u->ulp }}</span>
                                @endif
                                @if(!$u->uid && !$u->up3 && !$u->ulp)
                                    <span class="scope-chip grey">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="edit" title="Edit" onclick='editUser(@json($u))'>
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                    @if(!$u->isSuperAdmin())
                                    <form method="POST" action="{{ route('superadmin.akun.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="del" title="Hapus">
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:#9a8fa4;padding:30px;">Belum ada akun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah/Edit -->
<div class="modal-overlay" id="akunModal">
    <div class="modal">
        <h3 id="modalTitle">Tambah Akun</h3>
        <form method="POST" id="akunForm" action="{{ route('superadmin.akun.store') }}">
            @csrf
            <input type="hidden" name="_method" value="POST" id="methodField">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="f_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="f_email" required>
            </div>
            <div class="form-group">
                <label>Peran</label>
                <select name="role" id="f_role" required onchange="toggleScope()">
                    <option value="operator">Operator</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>UID</label>
                    <input type="text" name="uid" id="f_uid" placeholder="Contoh: 2305181034">
                </div>
                <div class="form-group">
                    <label>Unit UP3</label>
                    <select name="up3" id="f_up3">
                        <option value="">- Pilih UP3 -</option>
                        @foreach($up3s as $u3)<option value="{{ $u3->nama }}">{{ $u3->nama }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Unit ULP</label>
                <select name="ulp" id="f_ulp">
                    <option value="">- Pilih ULP -</option>
                    @foreach($ulps as $ul)<option value="{{ $ul->nama }}">{{ $ul->nama }}</option>@endforeach
                </select>
            </div>
            <div class="form-group" id="passGroup">
                <label>Password</label>
                <input type="password" name="password" id="f_password" placeholder="Min. 6 karakter">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-close" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('akunForm').reset();
    document.getElementById('akunForm').setAttribute('action', "{{ route('superadmin.akun.store') }}");
    document.getElementById('methodField').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Akun';
    document.getElementById('passGroup').style.display = '';
    document.getElementById('f_password').required = true;
    document.getElementById('akunModal').classList.add('open');
    toggleScope();
}

function editUser(u) {
    document.getElementById('akunForm').reset();
    document.getElementById('akunForm').setAttribute('action', "{{ url('/admin/akun') }}/" + u.id);
    document.getElementById('methodField').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Edit Akun';
    document.getElementById('f_name').value = u.name;
    document.getElementById('f_email').value = u.email;
    document.getElementById('f_role').value = u.role;
    document.getElementById('f_uid').value = u.uid || '';
    document.getElementById('f_up3').value = u.up3 || '';
    document.getElementById('f_ulp').value = u.ulp || '';
    document.getElementById('passGroup').style.display = 'none';
    document.getElementById('f_password').required = false;
    document.getElementById('akunModal').classList.add('open');
    toggleScope();
}

function toggleScope() {
    var role = document.getElementById('f_role').value;
    var scopeFields = [document.getElementById('f_uid'), document.getElementById('f_up3'), document.getElementById('f_ulp')];
    // Super admin: sembunyikan scope (tidak perlu uid/up3/ulp)
    var hide = (role === 'super_admin');
    scopeFields.forEach(function(el) {
        var wrap = el.closest('.form-group');
        wrap.style.display = hide ? 'none' : '';
        // reset parent row visibility: handled per field
    });
    if (hide) {
        scopeFields.forEach(function(el){ el.value = ''; });
    }
}

function closeModal() {
    document.getElementById('akunModal').classList.remove('open');
}
document.getElementById('akunModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
