<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
  Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
  });
});

Route::middleware(['auth:api'])->group(function () {
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::post('mi', [AuthController::class, 'me']);
  Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api', 'role:teacher,admin'])->group(function () {
  Route::post('me', [AuthController::class, 'me']);
});
