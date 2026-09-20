<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::get('/accounts/{account}', [AccountController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/journals', [JournalController::class, 'index']);
    Route::get('/journals/{journal}', [JournalController::class, 'show']);
    Route::post('/journals', [JournalController::class, 'store']);
});

Route::get('/health', [HealthController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
