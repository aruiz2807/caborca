<?php

use App\Http\Controllers\DependencyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::prefix('orders')->group(function () {
        Route::get('/active', [OrderController::class, 'active'])->name('orders.active');
        Route::get('/vehicle_data/{economic_number}', [OrderController::class, 'vehicle_data'])->name('orders.vehicle_data');
        Route::post('/store', [OrderController::class, 'store'])->name("orders.store");
        Route::post('/schedule/{order_id}', [OrderController::class, 'schedule'])->name("orders.schedule");

        Route::get('/archive', [OrderController::class, 'archive'])->name('orders.archive');
        Route::get('/archive/{status}', [OrderController::class, 'archive_orders'])->name('orders.archive_orders');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name("users.store");
        Route::put('/users/{user}', [UserController::class, 'update'])->name("users.update");
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name("users.destroy");

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name("roles.store");
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name("roles.update");
        Route::delete('/roles/{user}', [RoleController::class, 'destroy'])->name("roles.destroy");

        Route::get('/dependencies', [DependencyController::class, 'index'])->name('dependencies.index');
        Route::post('/dependencies', [DependencyController::class, 'store'])->name("dependencies.store");
        Route::put('/dependencies/{dependency}', [DependencyController::class, 'update'])->name("dependencies.update");
        Route::delete('/dependencies/{dependency}', [DependencyController::class, 'destroy'])->name("dependencies.destroy");

        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])->name("locations.store");
        Route::put('/locations/{location}', [LocationController::class, 'update'])->name("locations.update");
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name("locations.destroy");

        Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
        Route::post('/workshops', [WorkshopController::class, 'store'])->name("workshops.store");
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name("workshops.update");
        Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name("workshops.destroy");

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name("services.store");
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name("services.update");
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name("services.destroy");
    });
});
