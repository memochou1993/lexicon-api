<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('demo', DemoController::class)->name('demo');
Route::get('{any}', fn() => view('app'))->where('any', '.*');
