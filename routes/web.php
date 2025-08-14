<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [LogController::class, 'dashboard']);
Route::get('/logs/parse', [LogController::class, 'index']);