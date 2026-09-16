<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanBarangController;
use App\Http\Controllers\MinimalStockController;
use App\Http\Controllers\PenerimaanBarangController;
use App\Http\Controllers\DaftarBelanjaController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\AsetInventarisController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| 1. ROUTE GUEST (Tamu / Belum Login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthenticatedSessionController::class, 'showLoginForm'])->name('signin');
    Route::post('/signin', [AuthenticatedSessionController::class, 'signin'])->name('signin.post');
});


/*
|--------------------------------------------------------------------------
| 2. ROUTE AUTHENTICATED (Wajib Login & Anti-Back Cache)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back', 'admin-barang-keluar'])->group(function () {

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Route Export (Ditaruh di atas route {id} agar tidak bentrok)
    Route::get('/barang-keluar/export', [BarangKeluarController::class, 'exportCsv'])->name('barang-keluar.export');
    Route::get('/pengajuan-barang/export', [PengajuanBarangController::class, 'exportCsv'])->name('pengajuan-barang.export');
    Route::get('/penerimaan-barang/export', [PenerimaanBarangController::class, 'exportCsv'])->name('penerimaan-barang.export');
    Route::get('/daftar-belanja/export', [DaftarBelanjaController::class, 'exportCsv'])->name('daftar-belanja.export');
    Route::get('/stok-minimal/export', [MinimalStockController::class, 'exportCsv'])->name('stok-minimal.export');
    Route::get('/aset-inventaris/export', [AsetInventarisController::class, 'exportXlsx'])->name('aset-inventaris.export');
    Route::get('/master-lokasi/export', [MasterLokasiController::class, 'exportXlsx'])->name('master-lokasi.export');

    // Route Pengajuan Barang
    Route::get('/pengajuan-barang', [PengajuanBarangController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan-barang/create', [PengajuanBarangController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan-barang', [PengajuanBarangController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan-barang/{id}/edit', [PengajuanBarangController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/pengajuan-barang/{id}', [PengajuanBarangController::class, 'update'])->name('pengajuan.update');
    Route::patch('/pengajuan-barang/{id}/status', [PengajuanBarangController::class, 'updateStatus'])->name('pengajuan.update-status');
    Route::delete('/pengajuan-barang/{id}', [PengajuanBarangController::class, 'destroy'])->name('pengajuan.destroy');

    // Route Daftar Belanja
    Route::get('/daftar-belanja', [DaftarBelanjaController::class, 'index'])->name('daftar-belanja.index');

    // Route Penerimaan Barang
    Route::get('/penerimaan-barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan.index');
    Route::get('/penerimaan-barang/{id}/edit', [PenerimaanBarangController::class, 'edit'])->name('penerimaan.edit');
    Route::put('/penerimaan-barang/{id}', [PenerimaanBarangController::class, 'update'])->name('penerimaan.update');
    Route::delete('/penerimaan-barang/{id}', [PenerimaanBarangController::class, 'destroy'])->name('penerimaan.destroy');

    // Route Stock Minimal
    Route::get('/stok-minimal', [MinimalStockController::class, 'index'])->name('stok-minimal.index');
    Route::get('/stok-minimal/create', [MinimalStockController::class, 'create'])->name('stok-minimal.create');
    Route::post('/stok-minimal', [MinimalStockController::class, 'store'])->name('stok-minimal.store');
    Route::get('/stok-minimal/{id}/edit', [MinimalStockController::class, 'edit'])->name('stok-minimal.edit');
    Route::put('/stok-minimal/{id}', [MinimalStockController::class, 'update'])->name('stok-minimal.update');
    Route::delete('/stok-minimal/{id}', [MinimalStockController::class, 'destroy'])->name('stok-minimal.destroy');

    // Route Barang Keluar
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::get('/barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    Route::get('/barang-keluar/{id}/edit', [BarangKeluarController::class, 'edit'])->name('barang-keluar.edit');
    Route::put('/barang-keluar/{id}', [BarangKeluarController::class, 'update'])->name('barang-keluar.update');
    Route::delete('/barang-keluar/{id}', [BarangKeluarController::class, 'destroy'])->name('barang-keluar.destroy');

    // Route Aset & Inventaris
    Route::get('/aset-inventaris', [AsetInventarisController::class, 'index'])->name('aset-inventaris.index');
    Route::get('/aset-inventaris/scan', [AsetInventarisController::class, 'scan'])->name('aset-inventaris.scan');
    Route::get('/aset-inventaris/create', [AsetInventarisController::class, 'create'])->name('aset-inventaris.create');
    Route::post('/aset-inventaris', [AsetInventarisController::class, 'store'])->name('aset-inventaris.store');
    Route::get('/aset-inventaris/{id}/edit', [AsetInventarisController::class, 'edit'])->name('aset-inventaris.edit');
    Route::put('/aset-inventaris/{id}', [AsetInventarisController::class, 'update'])->name('aset-inventaris.update');
    Route::get('/aset-inventaris/{id}/history-pdf', [AsetInventarisController::class, 'historyPdf'])->name('aset-inventaris.history-pdf');
    Route::get('/aset-inventaris/{id}', [AsetInventarisController::class, 'show'])->name('aset-inventaris.show');
    Route::post('/aset-inventaris/{id}/maintenance', [AsetInventarisController::class, 'storeMaintenance'])->name('aset-inventaris.maintenance.store');
    Route::patch('/maintenance/{id}/status', [AsetInventarisController::class, 'updateMaintenanceStatus'])->name('aset-inventaris.maintenance.status');

    // Master Lokasi
    Route::get('/master-lokasi', [MasterLokasiController::class, 'index'])->name('master-lokasi.index');
    Route::get('/master-lokasi/create', [MasterLokasiController::class, 'create'])->name('master-lokasi.create');
    Route::post('/master-lokasi', [MasterLokasiController::class, 'store'])->name('master-lokasi.store');
    Route::get('/master-lokasi/{id}/edit', [MasterLokasiController::class, 'edit'])->name('master-lokasi.edit');
    Route::put('/master-lokasi/{id}', [MasterLokasiController::class, 'update'])->name('master-lokasi.update');

    // Profile Pages
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Error Pages
    // Route::get('/error-404', function () {
    //     return view('pages.errors.error-404', ['title' => 'Error 404']);
    // })->name('error-404');
});
