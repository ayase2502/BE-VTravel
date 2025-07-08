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
use App\Http\Controllers\AlbumImageController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\MotorbikeController;

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
    
    // -------- TOURS-CAT --------
    Route::prefix('tour-categories')->group(function () {
    Route::get('/', [TourCategoryController::class, 'index']);              // Danh sách (chỉ active)
    Route::get('/{id}', [TourCategoryController::class, 'show']);           // Chi tiết
    Route::post('/', [TourCategoryController::class, 'store']);             // Thêm mới
    Route::put('/{id}', [TourCategoryController::class, 'update']);         // Cập nhật
    Route::post('/{id}/soft-delete', [TourCategoryController::class, 'softDelete']); // Xóa mềm / khôi phục
    Route::delete('/{id}', [TourCategoryController::class, 'destroy']);     // Xóa vĩnh viễn
    });



     // -------- ALBUMS MANAGEMENT --------
    Route::get('/albums', [AlbumController::class, 'index']);
    Route::get('/albums/trashed', [AlbumController::class, 'trashed']);
    Route::get('/albums/{id}', [AlbumController::class, 'show']);
    Route::post('/albums', [AlbumController::class, 'store']);
    Route::put('/albums/{id}', [AlbumController::class, 'update']);
    Route::post('/albums/{id}/soft-delete', [AlbumController::class, 'softDelete']);
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);

    // Album Images
    Route::get('/albums/{albumId}/images', [AlbumImageController::class, 'index']);
    Route::get('/albums/{albumId}/images/trashed', [AlbumImageController::class, 'trashed']);
    Route::post('/albums/{albumId}/images', [AlbumImageController::class, 'store']);
    Route::post('/albums/{albumId}/images/{imageId}/soft-delete', [AlbumImageController::class, 'softDelete']);
    Route::delete('/images/{imageId}', [AlbumImageController::class, 'destroy']);
    Route::get('/albums/images/all', [AlbumImageController::class, 'allImages']);

    //-------- DESTINATION-CATEGORIES --------
    Route::prefix('destination-categories')->group(function () {
        Route::get('/', [DestinationCategoryController::class, 'index']);
        Route::get('/{id}', [DestinationCategoryController::class, 'show']);
        Route::post('/', [DestinationCategoryController::class, 'store']);
        Route::put('/{id}', [DestinationCategoryController::class, 'update']);
        Route::post('/{id}/soft-delete', [DestinationCategoryController::class, 'softDelete']);
        Route::delete('/{id}', [DestinationCategoryController::class, 'destroy']);
    });

    //-------- DESTINATION --------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/destinations', [DestinationController::class, 'index']); // danh sách active
        Route::get('/destinations/trashed', [DestinationController::class, 'trashed']); // danh sách đã xóa mềm
        Route::get('/destinations/{id}', [DestinationController::class, 'show']);
        Route::post('/destinations', [DestinationController::class, 'store']);
        Route::put('/destinations/{id}', [DestinationController::class, 'update']);
        Route::post('/destinations/{id}/soft-delete', [DestinationController::class, 'softDelete']); // xóa mềm/khôi phục
        Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']); // xóa vĩnh viễn
    });

    //-------- BOOKING --------
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/trashed', [BookingController::class, 'trashed']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::post('/bookings/{id}/soft-delete', [BookingController::class, 'softDelete']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    });

    //-------- GUIDES ------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/guides', [GuideController::class, 'index']);
        Route::get('/guides/trashed', [GuideController::class, 'trashed']);
        Route::get('/guides/{id}', [GuideController::class, 'show']);
        Route::post('/guides', [GuideController::class, 'store']);
        Route::put('/guides/{id}', [GuideController::class, 'update']);
        Route::post('/guides/{id}/soft-delete', [GuideController::class, 'softDelete']);
        Route::delete('/guides/{id}', [GuideController::class, 'destroy']);
    });

    //-------- HOTELS -------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/hotels', [HotelController::class, 'index']);
        Route::get('/hotels/trashed', [HotelController::class, 'trashed']);
        Route::get('/hotels/{id}', [HotelController::class, 'show']);
        Route::post('/hotels', [HotelController::class, 'store']);
        Route::put('/hotels/{id}', [HotelController::class, 'update']);
        Route::post('/hotels/{id}/soft-delete', [HotelController::class, 'softDelete']);
        Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);
    }); 

    //-------- BUS ---------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/bus-routes', [BusRouteController::class, 'index']);
        Route::get('/bus-routes/trashed', [BusRouteController::class, 'trashed']);
        Route::get('/bus-routes/{id}', [BusRouteController::class, 'show']);
        Route::post('/bus-routes', [BusRouteController::class, 'store']);
        Route::put('/bus-routes/{id}', [BusRouteController::class, 'update']);
        Route::post('/bus-routes/{id}/soft-delete', [BusRouteController::class, 'softDelete']);
        Route::delete('/bus-routes/{id}', [BusRouteController::class, 'destroy']);
    });

    //-------- MOTOBIKE ------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/motorbikes', [MotorbikeController::class, 'index']);
        Route::get('/motorbikes/trashed', [MotorbikeController::class, 'trashed']);
        Route::get('/motorbikes/{id}', [MotorbikeController::class, 'show']);
        Route::post('/motorbikes', [MotorbikeController::class, 'store']);
        Route::put('/motorbikes/{id}', [MotorbikeController::class, 'update']);
        Route::post ('/motorbikes/{id}/soft-delete', [MotorbikeController::class, 'softDelete']);
        Route::delete('/motorbikes/{id}', [MotorbikeController::class, 'destroy']);
    });
});