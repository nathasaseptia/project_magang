<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $up3s = Unit::where('jenis', 'up3')->orderBy('nama')->get();
        $ulps = Unit::where('jenis', 'ulp')->orderBy('nama')->get();

        return view('superadmin.akun', compact('users', 'up3s', 'ulps'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($request->input('password'));

        User::create($data);

        return redirect()->route('superadmin.akun')->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin() && Auth()->user()->id !== $user->id) {
            return back()->with('error', 'Tidak dapat mengubah akun super admin lain.');
        }

        $data = $this->validated($request, $user->id);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return redirect()->route('superadmin.akun')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Akun super admin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('superadmin.akun')->with('success', 'Akun berhasil dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'role'     => 'required|in:super_admin,admin,operator',
            'uid'      => 'nullable|string|max:50',
            'up3'      => 'nullable|string|max:100',
            'ulp'      => 'nullable|string|max:100',
        ];

        if ($ignoreId === null) {
            $rules['email']    = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:6';
        } else {
            $rules['email'] = 'required|email|unique:users,email,' . $ignoreId;
        }

        return $request->validate($rules);
    }
}
