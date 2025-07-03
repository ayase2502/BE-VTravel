<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourCategoryController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\UserController;

// --------- AUTH ---------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/otp/send', [OtpController::class, 'sendOtp']);
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Lấy user hiện tại (cho FE kiểm tra đăng nhập)
    Route::get('/me', fn(Request $request) => response()->json(['user' => $request->user()]));

    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // -------- USERS --------
    // Danh sách, tạo, xem chi tiết user
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']); // Xóa vĩnh viễn (chỉ role = admin xóa mới được)
    Route::put('/user/{id}/soft-delete', [UserController::class, 'softDelete']); // Xóa mềm (disable/enable)

    // Profile cá nhân
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update', [UserController::class, 'updateProfile']);

    // -------- TOURS --------
    Route::apiResource('tours', TourController::class)->except(['create', 'edit']);
    Route::apiResource('categories', TourCategoryController::class)->except(['create', 'edit']);
    Route::apiResource('albums', AlbumController::class)->only(['index', 'show']);
});