<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Unit - PLN WebGIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f3f2f7; color: #1e1b2e;
        }
        .container { margin-left: 234px; padding: 28px 32px; }

        .page-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .page-head h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
        .page-head p { color: #7a7480; font-size: 0.85rem; margin-top: 4px; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem; border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600; transition: all 0.25s;
        }
        .btn-primary { background: linear-gradient(135deg, #a43d8f, #7b2d6b); color: #fff; box-shadow: 0 6px 18px rgba(164,61,143,0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(164,61,143,0.35); }
        .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.78rem; border-radius: 9px; }
        .btn-close { background: #f1f5f9; color: #475569; }

        .flash { padding: 0.85rem 1rem; border-radius: 12px; margin-bottom: 18px; font-size: 0.85rem; font-weight: 500; }
        .flash.success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .flash.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .bento-grid { display: grid; gap: 16px; grid-template-columns: repeat(12, 1fr); }
        .bento-card { background: #fff; border-radius: 18px; padding: 20px; border: 1px solid #eee8f2; box-shadow: 0 4px 20px rgba(30,27,46,0.04); }
        .stat-card { position: relative; overflow: hidden; }
        .stat-card .stat-icon { position: absolute; right: 16px; top: 16px; opacity: 0.18; width: 40px; height: 40px; }
        .stat-card h3 { font-size: 0.78rem; font-weight: 600; color: #a05d8d; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .big { font-size: 2.2rem; font-weight: 800; margin-top: 8px; letter-spacing: -0.03em; }
        .stat-card .small { font-size: 0.78rem; color: #7a7480; margin-top: 4px; }
        .col-4 { grid-column: span 4; }
        .col-6 { grid-column: span 6; }
        .col-12 { grid-column: span 12; }

        .card-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title h2 { font-size: 1.05rem; font-weight: 700; }
        .badge { font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 999px; }

        .unit-type { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .unit-type .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot.up3 { background: #7c3aed; }
        .dot.ulp { background: #0ea5e9; }
        .unit-type span { font-size: 0.8rem; font-weight: 600; color: #6b7280; }

        .unit-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .unit-chip {
            display: flex; align-items: center; gap: 8px;
            background: #faf7fc; border: 1px solid #eee8f2; border-radius: 12px;
            padding: 8px 12px; font-size: 0.82rem; font-weight: 500;
        }
        .unit-chip .kode { color: #9a8fa4; font-size: 0.7rem; font-weight: 600; background: #f3eef6; padding: 2px 7px; border-radius: 6px; }
        .unit-chip .rm { cursor: pointer; border: none; background: none; color: #c4b8cc; padding: 2px; transition: all 0.2s; }
        .unit-chip .rm:hover { color: #dc2626; }
        .unit-chip .edit { cursor: pointer; border: none; background: none; color: #c4b8cc; padding: 2px; transition: all 0.2s; }
        .unit-chip .edit:hover { color: #7b2d6b; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
        thead th { text-align: left; padding: 10px 12px; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; color: #9a8fa4; border-bottom: 1.5px solid #eee8f2; white-space: nowrap; }
        tbody td { padding: 12px; border-bottom: 1px solid #f3eef6; }
        tbody tr:hover { background: #faf7fc; }

        .type-pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
        .type-up3 { background: #ede9fe; color: #6d28d9; }
        .type-ulp { background: #e0f2fe; color: #0369a1; }

        .modal-overlay { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(30,27,46,0.5); backdrop-filter: blur(3px); align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 20px; padding: 26px; width: 100%; max-width: 440px; box-shadow: 0 24px 60px rgba(0,0,0,0.25); }
        .modal h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #4c4556; margin-bottom: 6px; }
        .form-group input, .form-group select { width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #e2dce6; border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 0.85rem; outline: none; transition: all 0.2s; }
        .form-group input:focus, .form-group select:focus { border-color: #a43d8f; box-shadow: 0 0 0 3px rgba(164,61,143,0.12); }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .note { font-size: 0.72rem; color: #9a8fa4; margin-top: 4px; }
    </style>
</head>
<body>

@include('partials.sidebar-superadmin', ['active' => 'unit'])

<main class="container">
    <div class="page-head">
        <div>
            <h1>Kelola Unit</h1>
            <p>Kelola data unit UP3 dan ULP</p>
        </div>
        <button class="btn btn-primary" onclick="openModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Unit
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
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            <h3>Total UP3</h3>
            <div class="big">{{ $up3s->count() }}</div>
            <div class="small">Unit Induk Pelaksana</div>
        </div>
        <div class="bento-card stat-card col-4">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M2 12L12 3l10 9-10 9-10-9z"/></svg>
            <h3>Total ULP</h3>
            <div class="big">{{ $ulps->count() }}</div>
            <div class="small">Unit Layanan Pelanggan</div>
        </div>
        <div class="bento-card stat-card col-4">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            <h3>Total Unit</h3>
            <div class="big">{{ $up3s->count() + $ulps->count() }}</div>
            <div class="small">Keseluruhan unit terdaftar</div>
        </div>

        <div class="bento-card col-12">
            <div class="card-title">
                <h2>Daftar Unit</h2>
                <div class="unit-type">
                    <span><span class="dot up3"></span> UP3</span>
                    <span><span class="dot ulp"></span> ULP</span>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Unit</th>
                            <th>Kode</th>
                            <th>Jenis</th>
                            <th>Parent UP3</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($up3s as $u3)
                        <tr>
                            <td><strong>{{ $u3->nama }}</strong></td>
                            <td>{{ $u3->kode ?: '-' }}</td>
                            <td><span class="type-pill type-up3">UP3</span></td>
                            <td>-</td>
                            <td>{{ $u3->created_at ? $u3->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="unit-chip" style="background:none;border:none;padding:0;">
                                    <button class="edit" onclick='editUnit(@json($u3))'>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.unit.destroy', $u3) }}" onsubmit="return confirm('Hapus unit {{ $u3->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rm" type="submit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @foreach($ulps as $ul)
                        <tr>
                            <td><strong>{{ $ul->nama }}</strong></td>
                            <td>{{ $ul->kode ?: '-' }}</td>
                            <td><span class="type-pill type-ulp">ULP</span></td>
                            <td>{{ $ul->parent_up3 ?: '-' }}</td>
                            <td>{{ $ul->created_at ? $ul->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="unit-chip" style="background:none;border:none;padding:0;">
                                    <button class="edit" onclick='editUnit(@json($ul))'>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.unit.destroy', $ul) }}" onsubmit="return confirm('Hapus unit {{ $ul->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rm" type="submit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah/Edit Unit -->
<div class="modal-overlay" id="unitModal">
    <div class="modal">
        <h3 id="modalTitle">Tambah Unit</h3>
        <form method="POST" id="unitForm" action="{{ route('superadmin.unit.store') }}">
            @csrf
            <input type="hidden" name="_method" value="POST" id="methodField">
            <div class="form-group">
                <label>Jenis Unit</label>
                <select name="jenis" id="f_jenis" required onchange="toggleParent()">
                    <option value="up3">UP3</option>
                    <option value="ulp">ULP</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Unit</label>
                <input type="text" name="nama" id="f_nama" required placeholder="Contoh: UP3 Medan">
            </div>
            <div class="form-group">
                <label>Kode Unit</label>
                <input type="text" name="kode" id="f_kode" placeholder="Contoh: MHN">
            </div>
            <div class="form-group" id="parentGroup">
                <label>UP3 Induk</label>
                <select name="parent_up3" id="f_parent">
                    <option value="">- Pilih UP3 -</option>
                    @foreach($up3s as $u3)<option value="{{ $u3->nama }}">{{ $u3->nama }}</option>@endforeach
                </select>
                <div class="note">Pilih UP3 tempat ULP ini berada.</div>
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
    document.getElementById('unitForm').reset();
    document.getElementById('unitForm').setAttribute('action', "{{ route('superadmin.unit.store') }}");
    document.getElementById('methodField').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Unit';
    document.getElementById('f_jenis').value = 'up3';
    document.getElementById('unitModal').classList.add('open');
    toggleParent();
}

function editUnit(u) {
    document.getElementById('unitForm').reset();
    document.getElementById('unitForm').setAttribute('action', "{{ url('/admin/unit') }}/" + u.id);
    document.getElementById('methodField').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Edit Unit';
    document.getElementById('f_jenis').value = u.jenis;
    document.getElementById('f_nama').value = u.nama;
    document.getElementById('f_kode').value = u.kode || '';
    document.getElementById('f_parent').value = u.parent_up3 || '';
    document.getElementById('unitModal').classList.add('open');
    toggleParent();
}

function toggleParent() {
    var jenis = document.getElementById('f_jenis').value;
    document.getElementById('parentGroup').style.display = (jenis === 'ulp') ? '' : 'none';
    if (jenis !== 'ulp') document.getElementById('f_parent').value = '';
}

function closeModal() {
    document.getElementById('unitModal').classList.remove('open');
}
document.getElementById('unitModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
