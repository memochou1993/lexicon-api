<?php

use App\Http\Controllers\Api\Client\DispatchController;
use App\Http\Controllers\Api\Client\CacheController;
use App\Http\Controllers\Api\Client\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'client',
])->group(function () {
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/', [ProjectController::class, 'show']);
        Route::delete('cache', [CacheController::class, 'destroy']);
        Route::post('dispatch', DispatchController::class);
    });
});
