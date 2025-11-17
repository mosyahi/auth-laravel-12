<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA
|--------------------------------------------------------------------------
*/
// DEBUG PERMISSIONS
Route::get('/debug-permissions', function () {
    $user = auth()->user();
    if (! $user) return 'not logged in';

    return [
        'id' => $user->id,
        'name' => $user->name,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions(),
        'has_role_admin' => $user->hasRole('admin'),
        'has_permission_home' => $user->hasPermissionTo('home'),
    ];
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->middleware('permission:home')->name('home');

    // Dummy
    Route::get('/kategori', [HomeController::class, 'index'])->middleware('permission:kategori')->name('kategori');
    Route::get('/jenis', [HomeController::class, 'index'])->middleware('permission:jenis')->name('jenis');
    // End Dummy


    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
});

/*
|--------------------------------------------------------------------------
| GUEST AREA (tidak bisa diakses jika sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('redirect.if.auth')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');

    Route::get('register', [LoginController::class, 'showRegister'])->name('auth.register');
    Route::post('register', [LoginController::class, 'register'])->name('auth.register.post');

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('forgot-password', [LoginController::class, 'showForgotForm'])->name('forgot.form');
        Route::post('forgot-password', [LoginController::class, 'sendResetLink'])->name('forgot.send');

        Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('reset.form');
        Route::post('reset-password', [LoginController::class, 'resetPassword'])->name('reset.update');
    });

    Route::get('auth/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| OTP AREA — tetap boleh diakses meskipun sudah login
| (tetapi user belum benar² login karena belum verifikasi OTP)
|--------------------------------------------------------------------------
*/
Route::get('otp/verify', [LoginController::class, 'showOtpForm'])->name('auth.otp.form');
Route::post('otp/verify', [LoginController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::post('otp/resend', [LoginController::class, 'resendOtp'])->name('auth.otp.resend');
