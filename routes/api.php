<?php

use App\Http\Controllers\OrderController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::post('/update', [OrderController::class, 'api_update']);
    });
});

// dms token ZMZnsDCHrNTMJN91e8VUvsUISHxkIVU666izbPy9b72d5e76
