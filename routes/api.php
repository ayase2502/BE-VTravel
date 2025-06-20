<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourCategoryController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\UserController;
use Symfony\Component\HttpKernel\Profiler\Profile;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/profile',[ProfileController::class,'profile']);
//     Route::put('/frofile',[ProfileController::class,'update']);
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

Route::get('/users', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
   
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/update/{id}', [UserController::class, 'update']);
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('tours', [TourController::class,'store']);
    Route::get('/tours', [TourController::class,'index']);
    Route::get('/tours/{id}', [TourController::class,'show']);
    Route::put('/tours/{id}', [TourController::class,'update']);
    Route::delete('/tours/{id}', [TourController::class,'destroy']);

    Route::apiResource('categories', TourCategoryController::class);


    Route::apiResource('albums', AlbumController::class)->only(['index', 'show']);
});

