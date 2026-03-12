<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\bookingController;
use App\Http\Controllers\channelQuotaController;
use App\Http\Controllers\pricesController;
use App\Http\Controllers\routesController;
use App\Http\Controllers\scheduleController;
use App\Http\Controllers\settingController;
use App\Http\Controllers\stopsController;
use App\Http\Controllers\userController;
use App\Http\Controllers\vehicleTypeController;
use Illuminate\Support\Facades\Route;

// ====================
// Public & Auth Routes
// ====================

Route::get('/', [AdminAuthController::class , 'showLoginForm'])->name('admin.login');
// Proses login
Route::post('/login', [AdminAuthController::class , 'login'])->name('admin.login.submit');
// Proses logout
Route::post('/logout', [AdminAuthController::class , 'logout'])->name('admin.logout');


Route::get('/google-test', function () {
    return view('auth.google');
});

Route::get('/auth/google', [UserController::class , 'redirectToGoogle']);
Route::get('/auth/google/callback', [UserController::class , 'handleGoogleCallback']);

// ====================
// Admin Routes (Protected)
// ====================
Route::prefix('dashboard')->middleware('auth:admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class , 'index'])->name('dashboard.index');

    Route::get('/transaction', function () {
            return view('admin.admin-transaction');
        }
        );


        Route::get('/transactions/{id}', function () {
            return view('admin.admin-transaction-detail');
        }
        );


        Route::get('/vehicle-types', [vehicleTypeController::class , 'index'])->name('vehicle-types.index');
        Route::get('/stops', [stopsController::class , 'index'])->name('stops.index');
        Route::get('/routes', [routesController::class , 'index'])->name('route.index');
        Route::get('/prices', [pricesController::class , 'index'])->name('prices.index');
        Route::get('/schedule', [scheduleController::class , 'index'])->name('schedule.index');
        Route::get('/channel-quota', [channelQuotaController::class , 'index'])->name('channel.index');

        // Booking List
        Route::get('/bookings', [bookingController::class , 'index'])->name('bookings.index');

        // Booking Detail
        Route::get('/bookings/{id}', [bookingController::class , 'show'])->name('bookings.show');


        // Settings Routes
        Route::get('/settings', [settingController::class , 'index'])->name('settings.index');
        Route::post('/settings/title-sistem', [settingController::class , 'updateTitleSistem'])->name('settings.update.title_sistem');
        Route::post('/settings/nama-perusahaan', [settingController::class , 'updateNamaPerusahaan'])->name('settings.update.nama_perusahaan');
        Route::post('/settings/alamat-perusahaan', [settingController::class , 'updateAlamatPerusahaan'])->name('settings.update.alamat_perusahaan');
        Route::post('/settings/nomor-wa', [settingController::class , 'updateNomorWa'])->name('settings.update.nomor_wa');
        Route::post('/settings/banner', [settingController::class , 'storeBanner'])->name('settings.store.banner');
        Route::post('/settings/banner/update', [settingController::class , 'updateBanner'])->name('settings.update.banner');
        Route::delete('/settings/banner/{id}', [settingController::class , 'deleteBanner'])->name('settings.delete.banner');
        Route::post('/settings/admin', [settingController::class , 'storeAdmin'])->name('settings.store.admin');
        Route::post('/settings/admin/update', [settingController::class , 'updateAdmin'])->name('settings.update.admin');
        Route::delete('/settings/admin/{id}', [settingController::class , 'deleteAdmin'])->name('settings.delete.admin');

        // User Routes
        Route::get('/master-user', [userController::class , 'index'])->name('user.index');
        Route::get('/master-user/data', [userController::class , 'data'])->name('user.data');
        Route::post('/master-user', [userController::class , 'store'])->name('user.store');
        Route::put('/master-user/{id}', [userController::class , 'update'])->name('user.update');
        Route::delete('/master-user/{id}', [userController::class , 'destroy'])->name('user.destroy');    });
