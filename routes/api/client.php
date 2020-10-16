<?php

use App\Http\Controllers\Api\Client\EventController;
use App\Http\Controllers\Api\Client\ProjectCacheController;
use App\Http\Controllers\Api\Client\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'client',
])->group(function () {
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/', [ProjectController::class, 'show']);
        Route::delete('cache', [ProjectCacheController::class, 'destroy']);
        Route::post('events/dispatch', [EventController::class, 'index']);
    });
});
