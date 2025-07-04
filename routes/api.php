<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourCategoryController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationCategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;

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
    
    // Danh sách user bị xóa mềm và khôi phục
    Route::get('/users/trashed', [UserController::class, 'trashed']);
    Route::put('/users/{id}/restore', [UserController::class, 'restore']);

    // Profile cá nhân
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update', [UserController::class, 'updateProfile']);

    // -------- TOURS --------
    Route::apiResource('tours', TourController::class)->except(['create', 'edit']);
    Route::apiResource('albums', AlbumController::class)->only(['index', 'show']);

    // -------- TOURS-CAT --------
    Route::prefix('tour-categories')->group(function () {
    Route::get('/', [TourCategoryController::class, 'index']);              // Danh sách (chỉ active)
    Route::get('/{id}', [TourCategoryController::class, 'show']);           // Chi tiết
    Route::post('/', [TourCategoryController::class, 'store']);             // Thêm mới
    Route::put('/{id}', [TourCategoryController::class, 'update']);         // Cập nhật
    Route::post('/{id}/soft-delete', [TourCategoryController::class, 'softDelete']); // Xóa mềm / khôi phục
    Route::delete('/{id}', [TourCategoryController::class, 'destroy']);     // Xóa vĩnh viễn
    });

    //-------- DESTINATION-CAT --------
    Route::prefix('destination-categories')->group(function () {
    Route::get('/', [DestinationCategoryController::class, 'index']);
    Route::get('/{id}', [DestinationCategoryController::class, 'show']);
    Route::post('/', [DestinationCategoryController::class, 'store']);
    Route::put('/{id}', [DestinationCategoryController::class, 'update']);
    Route::put('/{id}/soft-delete', [DestinationCategoryController::class, 'softDelete']);
    Route::delete('/{id}', [DestinationCategoryController::class, 'destroy']);

    //-------- DESTINATION --------
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/destinations', [DestinationController::class, 'index']); // danh sách active
    Route::get('/destinations/trashed', [DestinationController::class, 'trashed']); // danh sách đã xóa mềm
    Route::get('/destinations/{id}', [DestinationController::class, 'show']);
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{id}', [DestinationController::class, 'update']);
    Route::patch('/destinations/{id}/soft', [DestinationController::class, 'softDelete']); // xóa mềm/khôi phục
    Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']); // xóa vĩnh viễn
    });

    //-------- BOOKING --------
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/trashed', [BookingController::class, 'trashed']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::patch('/bookings/{id}/soft-delete', [BookingController::class, 'softDelete']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    });

});
});