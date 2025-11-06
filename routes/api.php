<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::apiResource('transactions', TransactionController::class);
Route::post('/login', [UserController::class, 'login']);
Route::apiResource('categories', CategoryController::class);
Route::resource('plans', PlanController::class);
Route::patch('plans/{plan}/complete', [PlanController::class, 'complete'])->name('plans.complete');

