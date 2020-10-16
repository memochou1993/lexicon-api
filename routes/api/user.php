<?php

use App\Http\Controllers\Api\User\ProjectController;
use App\Http\Controllers\Api\User\TeamController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::get('/', [UserController::class, 'show']);
    Route::patch('/', [UserController::class, 'update']);
    Route::apiResource('teams', TeamController::class)
        ->only('index', 'store');
    Route::apiResource('projects', ProjectController::class)
        ->only('index');
});
