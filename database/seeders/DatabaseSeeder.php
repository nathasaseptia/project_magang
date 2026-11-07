<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@pln.co.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@pln.co.id'],
            [
                'name'     => 'Admin UP3 MEDAN',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'up3'      => 'MEDAN',
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@pln.co.id'],
            [
                'name'     => 'Operator ULP MEDAN SELATAN',
                'password' => Hash::make('password'),
                'role'     => 'operator',
                'ulp'      => 'MEDAN SELATAN',
            ]
        );

        // Unit contoh
        $up3s = [
            ['nama' => 'UP3 Medan',     'jenis' => 'up3', 'kode' => 'MDN'],
            ['nama' => 'UP3 Binjai',    'jenis' => 'up3', 'kode' => 'BNJ'],
            ['nama' => 'UP3 Deli Serdang', 'jenis' => 'up3', 'kode' => 'DLS'],
        ];
        foreach ($up3s as $u) {
            Unit::updateOrCreate(['nama' => $u['nama'], 'jenis' => 'up3'], $u);
        }

        $ulps = [
            ['nama' => 'ULP Medan Kota',     'jenis' => 'ulp', 'kode' => 'MK', 'parent_up3' => 'UP3 Medan'],
            ['nama' => 'ULP Medan Barat',    'jenis' => 'ulp', 'kode' => 'MB', 'parent_up3' => 'UP3 Medan'],
            ['nama' => 'ULP Binjai Kota',    'jenis' => 'ulp', 'kode' => 'BK', 'parent_up3' => 'UP3 Binjai'],
        ];
        foreach ($ulps as $u) {
            Unit::updateOrCreate(['nama' => $u['nama'], 'jenis' => 'ulp'], $u);
        }
    }
}
