<?php

use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\PersonnelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MaintenanceRequestController::class, 'create']);
Route::get('/user', [MaintenanceRequestController::class, 'create'])->name('request.create');
Route::post('/requests', [MaintenanceRequestController::class, 'store'])->name('request.store');
Route::post('/requests/track', [MaintenanceRequestController::class, 'track'])->name('request.track');

Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'store'])->name('admin.login.store');
Route::redirect('/login', '/admin/login')->name('login');
Route::post('/admin/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');

Route::get('/admin', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('admin.login');
})->name('admin.home');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminRequestController::class, 'index'])->name('dashboard');
    Route::get('/personnel', [PersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
    Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');
    Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
    Route::patch('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');
    Route::patch('/personnel/{personnel}/duty', [PersonnelController::class, 'updateDuty'])->name('personnel.duty.update');
    Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');
    Route::get('/requests/{maintenanceRequest}', [AdminRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{maintenanceRequest}', [AdminRequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{maintenanceRequest}', [AdminRequestController::class, 'destroy'])->name('requests.destroy');
});
