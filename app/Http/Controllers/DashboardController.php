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

        // Tabel 5 laporan terbaru (terakhir di sheet)
        $terbaru = array_slice(array_reverse($kpis['filtered']), 0, 5);

        return view('admin_unit.dashboard', compact('kpis', 'terbaru', 'all', 'from', 'to'));
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

    public function manajemenRow()
    {
        $penyulangs = $this->sheetService->getPenyulangWithCoords();

        // Skenario ROW: tiang/penyulang menjadi titik infrastruktur,
        // vegetasi (pohon) dibangkitkan di sekitar tiang dengan jarak acak.
        $rows = [];
        $pohonId = 1;

        foreach ($penyulangs as $p) {
            $lat = $p['latitude'];
            $lng = $p['longitude'];
            $nama = $p['nama_penyulang'];

            // Ekstrak kode penyulang (3-4 huruf pertama, misal "GH1")
            $kode = preg_replace('/\s+/', '', strtoupper($nama));
            $kode = substr($kode, 0, 4) ?: 'PLN';

            // 1 tiang utama
            $rows[] = [
                'id'         => 'tiang-' . strtolower($kode),
                'jenis'      => 'tiang',
                'nama'       => 'Tiang ' . $kode,
                'kode'       => $kode,
                'latitude'   => $lat,
                'longitude'  => $lng,
                'jarak_m'    => 0,
                'status'     => 'Aman',
            ];

            // 3-5 pohon di sekitar tiang
            $jumlahPohon = random_int(3, 5);
            for ($i = 0; $i < $jumlahPohon; $i++) {
                $jarak = round((mt_rand() / mt_getrandmax()) * 6, 2);
                $status = $jarak > 1.5 ? 'Aman' : ($jarak >= 0.5 ? 'Pantau' : 'Kritis');

                $offsetLat = ((mt_rand() / mt_getrandmax()) - 0.5) * 0.0035;
                $offsetLng = ((mt_rand() / mt_getrandmax()) - 0.5) * 0.0035;

                $rows[] = [
                    'id'        => 'pohon-' . $pohonId++,
                    'jenis'     => 'pohon',
                    'nama'      => 'Pohon #' . ($pohonId - 1),
                    'kode'      => $kode,
                    'latitude'  => round($lat + $offsetLat, 6),
                    'longitude' => round($lng + $offsetLng, 6),
                    'jarak_m'   => $jarak,
                    'status'    => $status,
                ];
            }
        }

        return view('admin_unit.manajemen-row', compact('rows', 'penyulangs'));
    }

    public function ramalanCuaca()
    {
        return view('admin_unit.ramalan-cuaca');
    }
}
