<?php

use App\Http\Controllers\HasilUploadBonusController;
use App\Http\Controllers\HasilUploadCashBackController;
use App\Http\Controllers\HasilUploadRollingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDataController;
use App\Http\Controllers\UploadBonusController;
use App\Http\Controllers\UploadCashBackController;
use App\Http\Controllers\UploadRollingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.perform');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard', ['type_menu' => 'home']);
    })->name('home');

    Route::resource('users', UserController::class);
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    Route::resource('uploadsbonus', UploadBonusController::class);
    Route::post('/uploadsbonus/send/{id}', [UploadBonusController::class, 'send'])->name('uploadsbonus.send');
    Route::resource('uploadscashback',UploadCashBackController::class);
    Route::post('/uploadscashback/send/{id}', [UploadCashBackController::class, 'send'])->name('uploadscashback.send');
    Route::resource('uploadsrolling',UploadRollingController::class);
    Route::post('/uploadsrolling/send/{id}', [UploadRollingController::class, 'send'])->name('uploadsrolling.send');
    Route::resource('hasilbonus',HasilUploadBonusController::class);
    Route::get('bonus-export', [HasilUploadBonusController::class, 'export'])->name('hasilbonus.export');
    Route::resource('hasilcashback',HasilUploadCashBackController::class);
    Route::get('cashback-export', [HasilUploadCashBackController::class, 'export'])->name('hasilcashback.export');
    Route::resource('hasilrolling',HasilUploadRollingController::class);
    Route::get('rolling-export', [HasilUploadRollingController::class, 'export'])->name('hasilrolling.export');
    Route::resource('memberdata', MemberDataController::class);
});
