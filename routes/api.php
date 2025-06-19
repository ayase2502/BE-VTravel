<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourCategoryController;
use App\Http\Controllers\AlbumController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',[UserController::class,'profile']);
    Route::put('/user',[UserController::class,'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('tours', [TourController::class,'store']);
    Route::get('/tours', [TourController::class,'index']);
    Route::get('/tours/{id}', [TourController::class,'show']);
    Route::put('/tours/{id}', [TourController::class,'update']);
    Route::delete('/tours/{id}', [TourController::class,'destroy']);

    Route::apiResource('categories', TourCategoryController::class)->only(['index', 'show']);

    Route::apiResource('albums', AlbumController::class)->only(['index', 'show']);
});

