<?php


// dashboard routes

use App\Http\Controllers\Dashboard\AccreditationProjectController;
use App\Http\Controllers\Dashboard\AllocationController;
use App\Http\Controllers\Dashboard\BrokerController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\ConstantController;
use App\Http\Controllers\Dashboard\CurrencyController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ExecutiveController;
use App\Http\Controllers\Dashboard\ItemController;
use App\Http\Controllers\Dashboard\ReportController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '',
    'middleware' => ['check.cookie'],
    'as' => 'dashboard.'
], function () {
    /* ********************************************************** */ 

    // Dashboard ************************
    Route::get('/', [HomeController::class,'index'])->name('home');

    // Logs ************************
    Route::get('logs',[ActivityLogController::class,'index'])->name('logs.index');
    Route::get('getLogs',[ActivityLogController::class,'getLogs'])->name('logs.getLogs');

    // users ************************
    Route::get('profile/settings',[UserController::class,'settings'])->name('profile.settings');

    // Reports ************************
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');
    /* ********************************************************** */ 

    // Accreditations ************************
    Route::get('accreditations/createExecutive', [AccreditationProjectController::class, 'createExecutive'])->name('accreditations.createExecutive');
    Route::get('accreditations/{accreditation}/editExecutive', [AccreditationProjectController::class, 'editExecutive'])->name('accreditations.editExecutive');
    Route::post('accreditations/checkNew', [AccreditationProjectController::class, 'checkNew'])->name('accreditations.checkNew');
    Route::post('accreditations/{accreditation}/adoption', [AccreditationProjectController::class, 'adoption'])->name('accreditations.adoption');

    // Allocations ************************    
    Route::post('allocations/{allocation}/print', [AllocationController::class, 'print'])->name('allocations.print');
    Route::post('allocations/import', [AllocationController::class, 'import'])->name('allocations.import');

    // Executives ************************
    Route::post('executives/import', [ExecutiveController::class, 'import'])->name('executives.import');


    /* ********************************************************** */

    // Resources

    Route::resource('constants', ConstantController::class)->only(['index','store','destroy']);
    Route::resource('currencies', CurrencyController::class)->except(['show','edit','create']);


    Route::resources([
        'users' => UserController::class,
        'brokers' => BrokerController::class,
        'items' => ItemController::class,
        'allocations' => AllocationController::class,
        'executives' => ExecutiveController::class,
        'accreditations' => AccreditationProjectController::class
    ]);
    /* ********************************************************** */ 
});