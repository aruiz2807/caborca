<?php

use App\Http\Controllers\DependencyController;
use App\Http\Controllers\BiSettingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SmtpSettingController;
use App\Http\Controllers\TsplusController;
use App\Http\Controllers\TsplusSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }

    return Inertia::render('Auth/Login/Index');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/home', [OrderController::class, 'home'])
        ->middleware('permission:view-home-dashboard')
        ->name('home');
    Route::get('/tsplus', [TsplusController::class, 'index'])
        ->middleware('permission:view-tsplus')
        ->name('tsplus.index');

    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index'])
            ->middleware('permission:view-messages')
            ->name('messages.index');
        Route::put('/{message}', [MessageController::class, 'update'])
            ->middleware('permission:update-message')
            ->name('messages.update');
        Route::delete('/{message}', [MessageController::class, 'destroy'])
            ->middleware('permission:delete-message')
            ->name('messages.destroy');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/active', [OrderController::class, 'active'])
            ->middleware('permission:view-orders-active')
            ->name('orders.active');
        Route::get('/vehicle_data/{economic_number}', [OrderController::class, 'vehicle_data'])
            ->middleware('permission:view-orders-active')
            ->name('orders.vehicle_data');
        Route::get('/available_slots', [OrderController::class, 'available_slots'])
            ->middleware('permission:view-orders-active')
            ->name('orders.available_slots');
        Route::post('/store', [OrderController::class, 'store'])
            ->middleware('permission:create-order')
            ->name("orders.store");
        Route::post('/schedule/{order_id}', [OrderController::class, 'schedule'])
            ->middleware('permission:create-appointment')
            ->name("orders.schedule");
        Route::post('/cancel_appointment/{order_id}', [OrderController::class, 'cancel_appointment'])
            ->middleware('permission:cancel-appointment')
            ->name("orders.cancel_appointment");
        Route::post('/parts/{order_id}', [OrderController::class, 'update_parts'])
            ->middleware('permission:update-order-parts')
            ->name("orders.update_parts");

        Route::get('/archive', [OrderController::class, 'archive'])
            ->middleware('permission:view-orders-archive')
            ->name('orders.archive');
        Route::get('/archive/{status}', [OrderController::class, 'archive_orders'])
            ->middleware('permission:view-orders-archive')
            ->name('orders.archive_orders');
    });

    Route::prefix('settings')->middleware('can:access-settings')->group(function () {
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:view-users')
            ->name('users.index');
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:create-user')
            ->name("users.store");
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:update-user')
            ->name("users.update");
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:delete-user')
            ->name("users.destroy");

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:view-roles')
            ->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:create-role')
            ->name("roles.store");
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:update-role')
            ->name("roles.update");
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->middleware('permission:manage-role-permissions')
            ->name("roles.update_permissions");
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:delete-role')
            ->name("roles.destroy");

        Route::get('/smtp', [SmtpSettingController::class, 'index'])
            ->middleware('permission:view-smtp')
            ->name('smtp.index');
        Route::put('/smtp', [SmtpSettingController::class, 'update'])
            ->middleware('permission:manage-smtp')
            ->name('smtp.update');
        Route::post('/smtp/test', [SmtpSettingController::class, 'test'])
            ->middleware('permission:manage-smtp')
            ->name('smtp.test');

        Route::get('/tsplus', [TsplusSettingController::class, 'index'])
            ->middleware('permission:view-tsplus-settings')
            ->name('tsplus-settings.index');
        Route::put('/tsplus', [TsplusSettingController::class, 'update'])
            ->middleware('permission:manage-tsplus-settings')
            ->name('tsplus-settings.update');

        Route::get('/bi', [BiSettingController::class, 'index'])
            ->middleware('permission:view-bi-settings')
            ->name('bi-settings.index');
        Route::post('/bi/sections', [BiSettingController::class, 'storeSection'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.sections.store');
        Route::put('/bi/sections/{biSection}', [BiSettingController::class, 'updateSection'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.sections.update');
        Route::delete('/bi/sections/{biSection}', [BiSettingController::class, 'destroySection'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.sections.destroy');
        Route::post('/bi/reports', [BiSettingController::class, 'storeReport'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.reports.store');
        Route::put('/bi/reports/{biReport}', [BiSettingController::class, 'updateReport'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.reports.update');
        Route::delete('/bi/reports/{biReport}', [BiSettingController::class, 'destroyReport'])
            ->middleware('permission:manage-bi-settings')
            ->name('bi-settings.reports.destroy');

        Route::get('/dependencies', [DependencyController::class, 'index'])
            ->middleware('permission:view-dependencies')
            ->name('dependencies.index');
        Route::post('/dependencies', [DependencyController::class, 'store'])
            ->middleware('permission:create-dependency')
            ->name("dependencies.store");
        Route::put('/dependencies/{dependency}', [DependencyController::class, 'update'])
            ->middleware('permission:update-dependency')
            ->name("dependencies.update");
        Route::delete('/dependencies/{dependency}', [DependencyController::class, 'destroy'])
            ->middleware('permission:delete-dependency')
            ->name("dependencies.destroy");

        Route::get('/locations', [LocationController::class, 'index'])
            ->middleware('permission:view-locations')
            ->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])
            ->middleware('permission:create-location')
            ->name("locations.store");
        Route::put('/locations/{location}', [LocationController::class, 'update'])
            ->middleware('permission:update-location')
            ->name("locations.update");
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])
            ->middleware('permission:delete-location')
            ->name("locations.destroy");

        Route::get('/workshops', [WorkshopController::class, 'index'])
            ->middleware('permission:view-workshops')
            ->name('workshops.index');
        Route::post('/workshops', [WorkshopController::class, 'store'])
            ->middleware('permission:create-workshop')
            ->name("workshops.store");
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])
            ->middleware('permission:update-workshop')
            ->name("workshops.update");
        Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])
            ->middleware('permission:delete-workshop')
            ->name("workshops.destroy");

        Route::get('/services', [ServiceController::class, 'index'])
            ->middleware('permission:view-services')
            ->name('services.index');
        Route::post('/services', [ServiceController::class, 'store'])
            ->middleware('permission:create-service-type')
            ->name("services.store");
        Route::put('/services/{service}', [ServiceController::class, 'update'])
            ->middleware('permission:update-service-type')
            ->name("services.update");
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])
            ->middleware('permission:delete-service-type')
            ->name("services.destroy");
    });
});
