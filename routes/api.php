<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['custom.guest'])->group(function () {
  Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
  });
});

Route::middleware(['cookie.auth'])->group(function () {
  Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('mi', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
  });
});

Route::middleware(['cookie.auth', 'role:teacher,admin'])->group(function () {
  Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('me', [AuthController::class, 'me']);
  });
});
