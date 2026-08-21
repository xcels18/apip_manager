<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengawasanApiController;
use App\Http\Controllers\Api\PegawaiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Pengawasan routes
    Route::apiResource('pengawasan', PengawasanApiController::class)->names([
        'index' => 'api.pengawasan.index',
        'store' => 'api.pengawasan.store',
        'show' => 'api.pengawasan.show',
        'update' => 'api.pengawasan.update',
        'destroy' => 'api.pengawasan.destroy',
    ]);

    // Pegawai routes
    Route::apiResource('pegawai', PegawaiApiController::class)->names([
        'index' => 'api.pegawai.index',
        'store' => 'api.pegawai.store',
        'show' => 'api.pegawai.show',
        'update' => 'api.pegawai.update',
        'destroy' => 'api.pegawai.destroy',
    ]);
});

