<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// ============================================================
// HOME REDIRECT - Ke halaman peminjaman guest
// ============================================================
Route::get('/', function () {
    return redirect()->route('peminjaman.guest');
});

// ============================================================
// GUEST ROUTES (Tanpa Login)
// ============================================================
Route::get('/peminjaman-guest', [PeminjamanController::class, 'guestForm'])
    ->name('peminjaman.guest')
    ->middleware('guest');

Route::post('/peminjaman-guest', [PeminjamanController::class, 'guestStore'])
    ->name('peminjaman.guest.store')
    ->middleware('guest');

// ============================================================
// Authentication Routes
// ============================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// Protected Routes (Auth Required)
// ============================================================

// Dashboard - All authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ============================================================
// ALAT ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    // View alat - Admin, Petugas & Peminjam bisa akses
    Route::get('/alat', [AlatController::class, 'index'])
        ->name('alat.index')
        ->middleware('role:admin,petugas,peminjam');
    
    // CRUD alat - Hanya Admin
    Route::get('/alat/create', [AlatController::class, 'create'])
        ->name('alat.create')
        ->middleware('role:admin');
    Route::post('/alat', [AlatController::class, 'store'])
        ->name('alat.store')
        ->middleware('role:admin');
    Route::get('/alat/{alat}/edit', [AlatController::class, 'edit'])
        ->name('alat.edit')
        ->middleware('role:admin');
    Route::put('/alat/{alat}', [AlatController::class, 'update'])
        ->name('alat.update')
        ->middleware('role:admin');
    Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])
        ->name('alat.destroy')
        ->middleware('role:admin');
});


// ============================================================
// QR CODE ROUTES
// ============================================================

// API: Scan QR (tanpa auth - buat guest form)
Route::post('/api/scan-qr', [App\Http\Controllers\QrCodeController::class, 'scanQr']);

// Admin routes untuk generate QR
Route::middleware(['auth', 'role:admin'])->group(function () {
     Route::get('/qr-management', [App\Http\Controllers\QrCodeController::class, 'indexManagement'])
        ->name('qr-management');
    Route::patch('/qr-generate/{alat}', [App\Http\Controllers\QrCodeController::class, 'generateQr'])
        ->name('qr-generate');
    
    Route::get('/qr-generate-all', [App\Http\Controllers\QrCodeController::class, 'generateAllQr'])
        ->name('qr-generate-all');

    
});

// ============================================================
// PEMINJAMAN ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    // View peminjaman - Semua role
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])
        ->name('peminjaman.index')
        ->middleware('role:admin,petugas,peminjam');
    
    // Create peminjaman - Admin & Peminjam
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])
        ->name('peminjaman.create')
        ->middleware('role:admin,peminjam');
    
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])
        ->name('peminjaman.store')
        ->middleware('role:admin,peminjam');
    
    // Update Status - Admin & Petugas
    Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])
        ->name('peminjaman.update')
        ->middleware('role:admin,petugas');
    
    // Delete peminjaman - Hanya Admin
    Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])
        ->name('peminjaman.destroy')
        ->middleware('role:admin');
    
    // Approve peminjaman - Admin & Petugas
    Route::patch('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve'])
        ->name('peminjaman.approve')
        ->middleware('role:admin,petugas');
});

// ============================================================
// PENGEMBALIAN ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    // View pengembalian - Semua role
    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->name('pengembalian.index')
        ->middleware('role:admin,petugas,peminjam');
    
    // Create/Store pengembalian - Admin, Petugas & Peminjam
    Route::post('/pengembalian', [PengembalianController::class, 'store'])
        ->name('pengembalian.store')
        ->middleware('role:admin,petugas,peminjam');
    
    // Bayar Denda - Admin only
    Route::post('/pengembalian/bayar', [PengembalianController::class, 'bayar'])
        ->name('pengembalian.bayar')
        ->middleware('role:admin');
    
    // Delete pengembalian - Admin only
    Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy'])
        ->name('pengembalian.destroy')
        ->middleware('role:admin');
});

// ============================================================
// USER MANAGEMENT ROUTES - Admin Only
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// ============================================================
// KATEGORI ROUTES - Admin Only
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});

// ============================================================
// LOG AKTIVITAS ROUTES - Admin Only
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
});

// ============================================================
// LAPORAN ROUTES - Admin & Petugas
// ============================================================
Route::middleware(['auth', 'role:admin,petugas'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
});