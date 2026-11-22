# PLN WebGIS — Monitoring Gangguan Penyulang

Aplikasi web berbasis Laravel untuk memantau gangguan penyulang jaringan distribusi PLN: dashboard, peta interaktif, arsip data, detail laporan, serta halaman kondisi cuaca pendukung monitoring. Akses dibatasi dengan **Role-Based Access Control (RBAC)** (peran `super_admin`, `admin`, `operator`) dan pembatasan data sesuai cakupan (UID/UP3/ULP).

## Tujuan

Menyediakan tampilan terpusat informasi gangguan penyulang dari sumber data terintegrasi, mendukung pemantauan dan pengambilan keputusan operasional.

## Fitur utama (sesuai kondisi saat ini)

- **Autentikasi & RBAC**: login, redirect sesuai peran, middleware `auth.check` dan `role`.
- **Dashboard**: ringkasan KPI, tren gangguan per hari, pemetaan unit terdampak (UP3/ULP), mini peta, statistik durasi padam, filter rentang tanggal, dan tautan ke detail/arsip/peta.
- **Peta lokasi**: peta interaktif Leaflet dengan marker dari data gangguan (koordinat valid), filter, dan popup informasi.
- **Arsip data**: daftar laporan dengan filter dan akses ke detail.
- **Detail laporan**: informasi lengkap satu laporan (termasuk foto jika tersedia pada sumber data).
- **Ramalan Cuaca & Atmosfer**: tampilan peta cuaca (Windy) dan data cuaca (Open-Meteo) sebagai informasi pendukung; analisis kondisi cuaca berbasis aturan ambang (bukan model AI/machine learning).
- **Manajemen akun & unit** (khusus `super_admin`): kelola akun pengguna (role, UID/UP3/ULP) dan data unit (UP3/ULP).

Fitur **Manajemen ROW** telah dihapus dari aplikasi final.

## Sumber data

- **Data gangguan (operasional)**: diambil dari **Google Sheets** melalui ekspor CSV (konfigurasi `GOOGLE_SPREADSHEET_ID` dan `GOOGLE_SPREADSHEET_GID`). Data diambil pada saat permintaan dan diproses di aplikasi (parsing/normalisasi, filter cakupan, agregasi untuk tampilan).
- **Data akun & unit**: disimpan di database **MySQL** (tabel `users`, `units`, `sessions`).
- **Peta dasar**: OpenStreetMap (Leaflet). **Cuaca**: Open-Meteo dan embed Windy (informasi pendukung).

## Alur data (singkat)

`Google Sheets (CSV)` → `GoogleSheetService` (fetch & parse) → `scopeData` sesuai role → `computeKpis`/tampilan → `Dashboard`, `Peta`, `Arsip`, `Detail`.

## Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, CSS kustom, Leaflet, Chart.js
- **Database**: MySQL

## Catatan keamanan & akses

- Route non-publik dilindungi middleware autentikasi dan pemeriksaan peran.
- `super_admin` tidak mengakses halaman data gangguan (diarahkan ke pengelolaan akun). Halaman data unit hanya untuk `admin` dan `operator` sesuai cakupan.
- Pastikan variabel lingkungan (termasuk kredensial database dan ID/`gid` spreadsheet) diset di `.env` dan **tidak** dicommit ke repositori publik.

## Lisensi

Kerangka aplikasi menggunakan lisensi MIT sesuai ketentuan Laravel (lihat berkas `LICENSE` pada repositori framework jika diperlukan).
