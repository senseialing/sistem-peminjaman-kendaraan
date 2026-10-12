<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\OtorisasiController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\SirkulasiController;
use App\Http\Controllers\LaporanController;
use App\Services\QrCodeService;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Peminjaman Kendaraan Dinas (Sirkulasi Kendaraan)
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dynamic QR SVG Renderer
Route::get('/qr-image/{code}', function ($code) {
    $svg = QrCodeService::generateSvg(urldecode($code), 220);
    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
})->name('qr.image');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Karyawan & Shared Routes
    Route::middleware('role:Karyawan,Staf Admin,Manajer')->group(function () {
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::post('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'ajukanPengembalian'])->name('peminjaman.kembalikan');
    });

    // Manajer Routes
    Route::middleware('role:Manajer,Staf Admin')->group(function () {
        Route::get('/otorisasi', [OtorisasiController::class, 'index'])->name('otorisasi.index');
        Route::post('/otorisasi/{id}/approve', [OtorisasiController::class, 'approve'])->name('otorisasi.approve');
        Route::post('/otorisasi/{id}/reject', [OtorisasiController::class, 'reject'])->name('otorisasi.reject');
    });

    // Staf Admin Routes
    Route::middleware('role:Staf Admin')->group(function () {
        // CRUD Kendaraan
        Route::resource('kendaraan', KendaraanController::class);

        // Sirkulasi Lapangan
        Route::get('/sirkulasi', [SirkulasiController::class, 'index'])->name('sirkulasi.index');
        Route::post('/sirkulasi/{id}/serah-terima', [SirkulasiController::class, 'prosesSerahTerima'])->name('sirkulasi.serah-terima');
        Route::post('/sirkulasi/{id}/pengembalian', [SirkulasiController::class, 'prosesPengembalian'])->name('sirkulasi.pengembalian');
        Route::get('/sirkulasi/scan', [SirkulasiController::class, 'scan'])->name('sirkulasi.scan');

        // Laporan Sirkulasi & Audit Log
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    });
});
