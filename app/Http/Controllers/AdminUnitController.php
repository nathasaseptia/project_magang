<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class AdminUnitController extends Controller
{
    public function index()
    {
        $up3s = Unit::where('jenis', 'up3')->orderBy('nama')->get();
        $ulps = Unit::where('jenis', 'ulp')->orderBy('nama')->get();

        return view('superadmin.unit', compact('up3s', 'ulps'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($data['jenis'] === 'ulp' && $request->filled('parent_up3')) {
            $data['parent_up3'] = $request->input('parent_up3');
        } else {
            $data['parent_up3'] = null;
        }

        Unit::create($data);

        return redirect()->route('superadmin.unit')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $this->validated($request, $unit->id);

        if ($data['jenis'] === 'ulp' && $request->filled('parent_up3')) {
            $data['parent_up3'] = $request->input('parent_up3');
        } else {
            $data['parent_up3'] = null;
        }

        $unit->update($data);

        return redirect()->route('superadmin.unit')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('superadmin.unit')->with('success', 'Unit berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'nama'  => 'required|string|max:255',
            'jenis' => 'required|in:up3,ulp',
            'kode'  => 'nullable|string|max:50',
        ];

        return $request->validate($rules);
    }
}
