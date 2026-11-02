
<?php

use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\PersonnelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USER PORTAL
|--------------------------------------------------------------------------
| campuscare.buzz
*/

Route::domain('campuscare.buzz')->group(function () {
    Route::get('/', [MaintenanceRequestController::class, 'create'])
        ->name('request.create');

    Route::get('/user', [MaintenanceRequestController::class, 'create'])
        ->name('request.user');

    Route::post('/requests', [MaintenanceRequestController::class, 'store'])
        ->name('request.store');

    Route::post('/requests/track', [MaintenanceRequestController::class, 'track'])
        ->name('request.track');
});

/*
|--------------------------------------------------------------------------
| ADMIN PORTAL
|--------------------------------------------------------------------------
| admin.campuscare.buzz
*/

Route::domain('admin.campuscare.buzz')->group(function () {
    Route::get('/', function () {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    })->name('admin.home');

    Route::get('/admin/login', [AuthController::class, 'create'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'store'])
        ->name('admin.login.store');

    Route::redirect('/login', '/admin/login')
        ->name('login');

    Route::post('/admin/logout', [AuthController::class, 'destroy'])
        ->middleware(['auth', 'admin'])
        ->name('admin.logout');

    Route::middleware(['auth', 'admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminRequestController::class, 'index'])
                ->name('dashboard');

            Route::get('/personnel', [PersonnelController::class, 'index'])
                ->name('personnel.index');

            Route::get('/personnel/create', [PersonnelController::class, 'create'])
                ->name('personnel.create');

            Route::post('/personnel', [PersonnelController::class, 'store'])
                ->name('personnel.store');

            Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])
                ->name('personnel.edit');

            Route::patch('/personnel/{personnel}', [PersonnelController::class, 'update'])
                ->name('personnel.update');

            Route::patch('/personnel/{personnel}/duty', [PersonnelController::class, 'updateDuty'])
                ->name('personnel.duty.update');

            Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])
                ->name('personnel.destroy');

            Route::get('/requests/{maintenanceRequest}', [AdminRequestController::class, 'show'])
                ->name('requests.show');

            Route::patch('/requests/{maintenanceRequest}', [AdminRequestController::class, 'update'])
                ->name('requests.update');

            Route::delete('/requests/{maintenanceRequest}', [AdminRequestController::class, 'destroy'])
                ->name('requests.destroy');
        });
});

/*
|--------------------------------------------------------------------------
| ORIGINAL RAILWAY DOMAIN
|--------------------------------------------------------------------------
| campuscare-production-331f.up.railway.app
| Kept for compatibility. All route names are unique.
*/

Route::domain('campuscare-production-331f.up.railway.app')->group(function () {
    Route::get('/', [MaintenanceRequestController::class, 'create'])
        ->name('railway.home');

    Route::get('/user', [MaintenanceRequestController::class, 'create'])
        ->name('railway.user');

    Route::post('/requests', [MaintenanceRequestController::class, 'store'])
        ->name('railway.request.store');

    Route::post('/requests/track', [MaintenanceRequestController::class, 'track'])
        ->name('railway.request.track');

    Route::get('/admin/login', [AuthController::class, 'create'])
        ->name('railway.admin.login');

    Route::post('/admin/login', [AuthController::class, 'store'])
        ->name('railway.admin.login.store');

    Route::post('/admin/logout', [AuthController::class, 'destroy'])
        ->middleware(['auth', 'admin'])
        ->name('railway.admin.logout');

    Route::middleware(['auth', 'admin'])
        ->prefix('admin')
        ->name('railway.admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminRequestController::class, 'index'])
                ->name('dashboard');

            Route::get('/personnel', [PersonnelController::class, 'index'])
                ->name('personnel.index');

            Route::get('/personnel/create', [PersonnelController::class, 'create'])
                ->name('personnel.create');

            Route::post('/personnel', [PersonnelController::class, 'store'])
                ->name('personnel.store');

            Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])
                ->name('personnel.edit');

            Route::patch('/personnel/{personnel}', [PersonnelController::class, 'update'])
                ->name('personnel.update');

            Route::patch('/personnel/{personnel}/duty', [PersonnelController::class, 'updateDuty'])
                ->name('personnel.duty.update');

            Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])
                ->name('personnel.destroy');

            Route::get('/requests/{maintenanceRequest}', [AdminRequestController::class, 'show'])
                ->name('requests.show');

            Route::patch('/requests/{maintenanceRequest}', [AdminRequestController::class, 'update'])
                ->name('requests.update');

            Route::delete('/requests/{maintenanceRequest}', [AdminRequestController::class, 'destroy'])
                ->name('requests.destroy');
        });
});
