<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\PMScheduleController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PMDetailController;
use App\Http\Controllers\MachineProblemController;
use App\Http\Controllers\MachineMeasurementController;
use App\Http\Controllers\MachineChecklistController;
use App\Http\Controllers\MachineProblemFindingController;

Route::view('/', 'welcome')->name('home');


Route::middleware(['auth'])->group(function () {
    Route::resource('machines', MachineController::class);

    Route::view('/dashboard', 'dashboard') ->name('dashboard');

    Route::resource('pm-schedules', PMScheduleController::class)
        ->middleware('auth');

    Route::resource('spareparts', SparepartController::class);

    Route::get('/reports', [ReportController::class, 'index'])
           ->name('reports.index');

    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store']);

    Route::post('/pm-schedules/{id}/details', [PMDetailController::class, 'store'])
    ->name('pm-details.store');

    Route::delete('/pm-details/{id}', [PMDetailController::class, 'destroy'])
        ->name('pm-details.destroy');

    Route::post('/pm-schedules/import', [PMScheduleController::class, 'import'])
    ->name('pm-schedules.import');

    Route::get('/machines/import', [MachineController::class, 'importForm'])
    ->name('machines.import.form');

    Route::post('/machines/import', [MachineController::class, 'import'])
        ->name('machines.import');

    Route::resource('machine-problems', MachineProblemController::class);

    Route::get('/machine-problems/by-type/{type}', function ($type) {

        return \App\Models\MachineProblem::where('machine_type', $type)
            ->orderBy('problem')
            ->get();

    });

    Route::resource('machine-problems', MachineProblemController::class)
    ->except(['show']);
    Route::get('/machine-problems/by-type/{type}', [MachineProblemController::class, 'getByType']);

    Route::get('/machine-problems/check-duplicate', [MachineProblemController::class, 'checkDuplicate'])
        ->name('machine-problems.checkDuplicate');

    Route::resource('machine-measurements', MachineMeasurementController::class);

    Route::post(
        '/spareparts/import',
        [SparepartController::class, 'import']
    )
    ->name('spareparts.import');


    Route::resource(
        'machine-checklists',
        MachineChecklistController::class
    );

    Route::post(
        '/machine-checklists/import',
        [MachineChecklistController::class, 'import']
    )->name('machine-checklists.import');


    Route::resource(
        'machine-problem-findings',
        MachineProblemFindingController::class
    );
    Route::post(
        '/machine-problem-findings/import',
        [MachineProblemFindingController::class, 'import']
    )->name('machine-problem-findings.import');



});

require __DIR__.'/settings.php';
// require __DIR__.'/auth.php'; // pastikan ada
