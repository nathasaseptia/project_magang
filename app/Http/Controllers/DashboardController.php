<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function petaLokasi()
    {
        $penyulangs = $this->sheetService->getPenyulangWithCoords();

        return view('admin_unit.peta', compact('penyulangs'));
    }

    public function index(Request $request)
    {
        $all = $this->sheetService->fetchData();
        $all = $this->sheetService->scopeData($all);

        $from = $request->input('from') ?: null;
        $to   = $request->input('to') ?: null;

        $kpis = $this->sheetService->computeKpis($all, $from, $to);

        // 5 laporan terbaru berdasarkan tanggal & jam padam.
        // Simpan index asli di $all agar link detail selalu menunjuk baris yang benar
        // walaupun sedang memakai filter rentang tanggal.
        $rows = [];
        foreach ($kpis['filtered'] as $row) {
            $idx = array_search($row, $all, true);
            $rows[] = ['idx' => $idx === false ? 0 : $idx, 'row' => $row];
        }
        usort($rows, fn ($a, $b) => $this->sortKey($b['row']) <=> $this->sortKey($a['row']));

        $terbaru = array_map(
            fn ($x) => $x['row'] + ['_idx' => $x['idx']],
            array_slice($rows, 0, 5)
        );

        // Titik koordinat untuk mini map (mengikuti filter tanggal)
        $mapRows = array_values(array_filter(
            $kpis['filtered'],
            fn ($r) => $r['latitude'] !== null && $r['longitude'] !== null
        ));

        $user  = $request->user();
        $scope = $user ? $user->dataScope() : ['level' => 'uid', 'value' => null];
        $syncedAt = now();

        return view('admin_unit.dashboard', compact(
            'kpis', 'terbaru', 'all', 'from', 'to', 'mapRows', 'scope', 'syncedAt'
        ));
    }

    /** Kunci urut laporan: tanggal + jam padam (DD/MM/YYYY HH:MM[:SS]). */
    private function sortKey(array $row): int
    {
        $tgl = trim((string) ($row['tgl_padam'] ?? ''));
        $jam = trim((string) ($row['jam_padam'] ?? ''));
        $jam = $jam !== '' ? $jam : '00:00:00';

        $dt = \DateTime::createFromFormat('d/m/Y H:i:s', $tgl . ' ' . $jam)
            ?: \DateTime::createFromFormat('d/m/Y H:i', $tgl . ' ' . substr($jam, 0, 5));

        return $dt ? $dt->getTimestamp() : 0;
    }

    public function detail(Request $request)
    {
        $all = $this->sheetService->scopeData($this->sheetService->fetchData());

        $target = null;
        $idx    = (int) $request->integer('idx', -1);
        if ($idx >= 0 && isset($all[$idx])) {
            $target = $all[$idx];
        }

        // Jika tidak ditemukan via index, cari berdasarkan parameter lain
        if (!$target) {
            $nama = $request->input('nama');
            $tgl  = $request->input('tgl');
            foreach ($all as $row) {
                if (trim($row['nama_penyulang'] ?? '') === $nama && trim($row['tgl_padam'] ?? '') === $tgl) {
                    $target = $row;
                    break;
                }
            }
        }

        abort_if(!$target, 404);

        return view('admin_unit.detail', ['item' => $target, 'total' => count($all)]);
    }

    public function arsipData()
    {
        $penyulangs = $this->sheetService->fetchData();
        $penyulangs = $this->sheetService->scopeData($penyulangs);

        return view('admin_unit.arsip', compact('penyulangs'));
    }

    public function ramalanCuaca()
    {
        return view('admin_unit.ramalan-cuaca');
    }
}
