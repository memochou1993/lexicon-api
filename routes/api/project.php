<?php

use App\Http\Controllers\Api\Project\CacheController;
use App\Http\Controllers\Api\Project\DispatchController;
use App\Http\Controllers\Api\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'token:project',
])->group(function () {
    Route::get('/', [ProjectController::class, 'show']);
    Route::delete('cache', [CacheController::class, 'destroy']);
    Route::post('dispatch', DispatchController::class);
});
