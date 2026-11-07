<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected string $spreadsheetId;
    protected string $gid;
    protected string $csvUrl;

    public function __construct()
    {
        $this->spreadsheetId = config('services.spreadsheet.id');
        $this->gid = config('services.spreadsheet.gid');
        $this->csvUrl = "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}/export?format=csv&gid={$this->gid}";
    }

    public function fetchData(): array
    {
        try {
            $response = Http::timeout(20)->retry(3, 200)->get($this->csvUrl);

            if ($response->failed()) {
                Log::error('Gagal mengambil data dari Google Spreadsheet', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $csvContent = $response->body();
            return $this->parseCsv($csvContent);
        } catch (\Exception $e) {
            Log::error('Error fetching Google Spreadsheet: ' . $e->getMessage());
            return [];
        }
    }

    protected function parseCsv(string $csvContent): array
    {
        $rows = array_map('str_getcsv', explode("\n", $csvContent));
        $data = [];

        if (count($rows) < 4) {
            return [];
        }

        // Row ke-0 & ke-1 = kosong, row ke-2 = header, data mulai row ke-3
        $headerRow = $rows[2];

        for ($i = 3; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (count($row) < 10) {
                continue;
            }

            // Skip baris kosong
            $nama = trim($row[1] ?? '');
            if (empty($nama)) {
                continue;
            }

            $latStr = trim($row[11] ?? '');
            $lngStr = trim($row[12] ?? '');

            $data[] = [
                'nama_penyulang' => $nama,
                'unit_up3'       => trim($row[2] ?? ''),
                'unit_ulp'       => trim($row[3] ?? ''),
                'tgl_padam'      => trim($row[4] ?? ''),
                'jam_padam'      => trim($row[5] ?? ''),
                'tgl_nyala'      => trim($row[6] ?? ''),
                'jam_nyala'      => trim($row[7] ?? ''),
                'lama_padam'     => trim($row[8] ?? ''),
                'status'         => trim($row[9] ?? ''),
                'foto'           => trim($row[10] ?? ''),
                'latitude'       => $this->parseCoordinate($latStr),
                'longitude'      => $this->parseCoordinate($lngStr),
                'latitude_raw'   => $latStr,
                'longitude_raw'  => $lngStr,
            ];
        }

        return $data;
    }

    /**
     * Convert format Indonesia "3.677.004" -> 3.677004
     * Jika sudah format standar "3.677004" return as-is
     */
    protected function parseCoordinate(string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        // "3.677.004" -> remove dots -> "3677004" -> need smart parse
        // Pattern: last 3 digits after last dot are decimal
        // "3.677.004" => 3.677004
        // "98.691.737" => 98.691737
        // "3.162.676" => 3.162676
        $cleaned = preg_replace('/\.(?=\d{3}$)/', '', $value);
        $result = (float) $cleaned;

        return $result ?: null;
    }

    /**
     * Hitung statistik KPI dashboard dari data gangguan.
     * Karena data mentah datang dari Google Sheets, seluruh perhitungan
     * dilakukan di sisi PHP dari array hasil fetch/scope.
     *
     * Status:
     *  - Tervalidasi ✅ / Tervalidasi  -> tervalidasi
     *  - Ditolak / Ditolak ❌          -> ditolak
     *  - selain itu (kosong, Pending, dll) -> pending
     *
     * @param array  $data Data yang sudah di-scope (boleh kosong)
     * @param string|null $fromTanggal Filter awal (DD/MM/YYYY) nullable
     * @param string|null $toTanggal   Filter akhir (DD/MM/YYYY) nullable
     * @return array
     */
    public function computeKpis(array $data, ?string $fromTanggal = null, ?string $toTanggal = null): array
    {
        // Normalisasi & filter berdasarkan rentang tanggal padam
        $filtered = [];
        foreach ($data as $row) {
            $tgl = trim($row['tgl_padam'] ?? '');
            if ($fromTanggal && $this->normalizeDate($tgl) < $this->normalizeDate($fromTanggal)) {
                continue;
            }
            if ($toTanggal && $this->normalizeDate($tgl) > $this->normalizeDate($toTanggal)) {
                continue;
            }
            $filtered[] = $row;
        }

        $total      = count($filtered);
        $tervalidasi = 0;
        $ditolak    = 0;
        $pending    = 0;
        $totalDetik = 0;
        $durasiList = [];
        $ulpCount   = [];
        $up3Count   = [];
        $perHari    = [];

        foreach ($filtered as $row) {
            $status = strtolower(trim(str_replace(['✅', '❌'], '', $row['status'] ?? '')));
            if (str_contains($status, 'tervalidasi')) {
                $tervalidasi++;
            } elseif (str_contains($status, 'ditolak')) {
                $ditolak++;
            } else {
                $pending++;
            }

            // Durasi padam (HH:MM:SS)
            $detik = $this->parseDuration($row['lama_padam'] ?? '');
            if ($detik > 0) {
                $totalDetik += $detik;
                $durasiList[] = $detik;
            }

            $up3 = trim($row['unit_up3'] ?? '');
            $ulp = trim($row['unit_ulp'] ?? '');
            if ($up3 !== '') $up3Count[$up3] = ($up3Count[$up3] ?? 0) + 1;
            if ($ulp !== '') $ulpCount[$ulp] = ($ulpCount[$ulp] ?? 0) + 1;

            $day = $this->normalizeDate($row['tgl_padam'] ?? '');
            if ($day) $perHari[$day] = ($perHari[$day] ?? 0) + 1;
        }

        ksort($perHari);

        $avgDetik = $totalDetik > 0 ? (int) round($totalDetik / max(1, count($durasiList))) : 0;
        $terlama  = count($durasiList) > 0 ? max($durasiList) : 0;
        $tercepat = count($durasiList) > 0 ? min($durasiList) : 0;

        // Sortir unit paling banyak terdampak
        arsort($up3Count);
        arsort($ulpCount);

        return [
            'total'         => $total,
            'tervalidasi'   => $tervalidasi,
            'pending'       => $pending,
            'ditolak'       => $ditolak,
            'persen_validasi' => $total > 0 ? round(($tervalidasi / $total) * 100, 1) : 0,
            'avg_lama'      => $this->formatDuration($avgDetik),
            'terlama'       => $this->formatDuration($terlama),
            'tercepat'      => $this->formatDuration($tercepat),
            'up3_count'     => $up3Count,
            'ulp_count'     => $ulpCount,
            'per_hari'      => $perHari,
            'total_up3'     => count($up3Count),
            'total_ulp'     => count($ulpCount),
            'filtered'      => $filtered,
        ];
    }

    /** DD/MM/YYYY -> YYYY-MM-DD agar mudah dibandingkan */
    protected function normalizeDate(?string $tgl): string
    {
        $tgl = trim((string) $tgl);
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $tgl, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
        }
        return $tgl;
    }

    /** HH:MM[:SS] -> detik */
    protected function parseDuration(?string $dur): int
    {
        $dur = trim((string) $dur);
        if (preg_match('#(\d+):(\d{1,2})(?::(\d{1,2}))?#', $dur, $m)) {
            $h = (int)$m[1];
            $i = (int)$m[2];
            $s = isset($m[3]) ? (int)$m[3] : 0;
            return $h * 3600 + $i * 60 + $s;
        }
        return 0;
    }

    /** detik -> "HH jam MM mnt" atau "MM mnt" */
    protected function formatDuration(int $detik): string
    {
        if ($detik <= 0) {
            return '0 mnt';
        }
        $h = intdiv($detik, 3600);
        $m = intdiv($detik % 3600, 60);
        if ($h > 0) {
            return $h . ' jam ' . $m . ' mnt';
        }
        return $m . ' mnt';
    }

    public function getPenyulangWithCoords(): array
    {
        $allData = $this->fetchData();

        $filtered = array_filter($allData, function ($item) {
            return $item['latitude'] !== null && $item['longitude'] !== null;
        });

        return array_values($this->scopeData($filtered));
    }

    /**
     * Filter data gangguan berdasarkan scope role pengguna yang sedang login.
     * - UID  : lihat semua
     * - UP3  : hanya unit_up3 miliknya
     * - ULP  : hanya unit_ulp miliknya
     * - Super admin tidak melihat data unit (dikembalikan kosong).
     */
    public function scopeData(array $data): array
    {
        $user = Auth::user();

        if (!$user || !$user->canViewUnitData()) {
            return [];
        }

        $scope = $user->dataScope();

        if ($scope['level'] === 'uid') {
            return $data;
        }

        $value = strtolower(trim((string) $scope['value']));

        return array_values(array_filter($data, function ($item) use ($scope, $value) {
            if (empty($value)) {
                return true;
            }
            if ($scope['level'] === 'up3') {
                return str_contains(strtolower(trim($item['unit_up3'] ?? '')), $value)
                    || str_contains($value, strtolower(trim($item['unit_up3'] ?? '')));
            }
            return str_contains(strtolower(trim($item['unit_ulp'] ?? '')), $value)
                || str_contains($value, strtolower(trim($item['unit_ulp'] ?? '')));
        }));
    }
}
