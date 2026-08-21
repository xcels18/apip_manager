<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengawasanController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Master Data Pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');

    // Pengawasan
    Route::resource('pengawasan', PengawasanController::class);
    Route::get('/pengawasan/{id}/cetak-surat-tugas', [PengawasanController::class, 'cetakSuratTugas'])->name('pengawasan.cetak-surat-tugas');
    Route::get('/pengawasan/{id}/cetak-sppd/{pegawai_id}', [PengawasanController::class, 'cetakSppd'])->name('pengawasan.cetak-sppd');
    Route::get('/pengawasan/{id}/cetak-kwitansi/{pegawai_id}', [PengawasanController::class, 'cetakKwitansi'])->name('pengawasan.cetak-kwitansi');
    Route::get('/api/kalender-data', [PengawasanController::class, 'getKalenderData'])->name('kalender.data');
    Route::post('/api/pengawasan/check-personil-availability', [PengawasanController::class, 'checkPersonilAvailability'])->name('pengawasan.check-availability');

    // Laporan
    Route::get('/laporan', [PengawasanController::class, 'laporan'])->name('laporan.index');

    // Rekap Data
    Route::get('/rekap', [App\Http\Controllers\RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export', [App\Http\Controllers\RekapController::class, 'export'])->name('rekap.export');

    // Setting Akun
    Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting/profile', [App\Http\Controllers\SettingController::class, 'updateProfile'])->name('setting.update-profile');
    Route::put('/setting/password', [App\Http\Controllers\SettingController::class, 'updatePassword'])->name('setting.update-password');
    Route::post('/setting/system', [App\Http\Controllers\SettingController::class, 'updateSystem'])->name('setting.update-system');

    // Test kalender
    Route::get('/test-kalender', function () {
        return view('test-kalender');
    });
});
