<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminUnitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/', function () {
    return redirect()->route('login');
});

// Logout - semua role yang sudah login
Route::middleware('auth.check')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected routes - semua role (unit data)
Route::middleware(['auth.check', 'role:admin,operator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/peta', [DashboardController::class, 'petaLokasi'])->name('peta');
    Route::get('/arsip', [DashboardController::class, 'arsipData'])->name('arsip');
    Route::get('/manajemen-row', [DashboardController::class, 'manajemenRow'])->name('manajemen-row');
    Route::get('/ramalan-cuaca', [DashboardController::class, 'ramalanCuaca'])->name('ramalan-cuaca');
    Route::get('/laporan/detail', [DashboardController::class, 'detail'])->name('laporan.detail');
});

// Protected routes - Super Admin (kelola sistem)
Route::middleware(['auth.check', 'role:super_admin'])->group(function () {
    Route::get('/admin/akun', [AdminAccountController::class, 'index'])->name('superadmin.akun');
    Route::post('/admin/akun', [AdminAccountController::class, 'store'])->name('superadmin.akun.store');
    Route::put('/admin/akun/{user}', [AdminAccountController::class, 'update'])->name('superadmin.akun.update');
    Route::delete('/admin/akun/{user}', [AdminAccountController::class, 'destroy'])->name('superadmin.akun.destroy');
    Route::get('/admin/unit', [AdminUnitController::class, 'index'])->name('superadmin.unit');
    Route::post('/admin/unit', [AdminUnitController::class, 'store'])->name('superadmin.unit.store');
    Route::put('/admin/unit/{unit}', [AdminUnitController::class, 'update'])->name('superadmin.unit.update');
    Route::delete('/admin/unit/{unit}', [AdminUnitController::class, 'destroy'])->name('superadmin.unit.destroy');
});
