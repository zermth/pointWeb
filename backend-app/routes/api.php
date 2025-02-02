<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/redeem', [ProductController::class, 'redeem']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/user', [AuthController::class, 'getUserData']);
