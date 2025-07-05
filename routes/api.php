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
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\ReviewController;

// --------- AUTH ---------
Route::post('/register', [AuthController::class, 'register']); // đăng ký người dùng mới
Route::post('/otp/send', [OtpController::class, 'sendOtp']); // gửi mã OTP
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']); // xác minh mã OTP
Route::post('/login', [AuthController::class, 'login']); // đăng nhập người dùng

Route::middleware('auth:sanctum')->group(function () {
    // Lấy user hiện tại (cho FE kiểm tra đăng nhập)
    Route::get('/me', fn(Request $request) => response()->json(['user' => $request->user()]));

    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // -------- USERS --------
    Route::get('/users', [UserController::class, 'index']); // liệt kê tất cả người dùng
    Route::post('/user', [UserController::class, 'store']); // tạo mới người dùng
    Route::get('/user/{id}', [UserController::class, 'show']); // lấy chi tiết người dùng theo ID
    Route::put('/user/{id}', [UserController::class, 'update']); // cập nhật thông tin người dùng
    Route::delete('/user/{id}', [UserController::class, 'destroy']); // xóa vĩnh viễn người dùng
    Route::put('/user/{id}/soft-delete', [UserController::class, 'softDelete']); // xóa mềm người dùng
    Route::get('/users/trashed', [UserController::class, 'trashed']); // liệt kê người dùng đã xóa
    Route::put('/users/{id}/restore', [UserController::class, 'restore']); // khôi phục người dùng đã xóa
    Route::get('/profile', [UserController::class, 'profile']); // lấy thông tin hồ sơ cá nhân
    Route::put('/profile/update', [UserController::class, 'updateProfile']); // cập nhật hồ sơ cá nhân

    // -------- TOURS --------
    Route::apiResource('tours', TourController::class)->except(['create', 'edit']); // quản lý tài nguyên tour

    // -------- TOURS-CATEGORY --------
    Route::prefix('tour-categories')->group(function () {
        Route::get('/', [TourCategoryController::class, 'index']); // liệt kê tất cả danh mục tour
        Route::get('/{id}', [TourCategoryController::class, 'show']); // lấy chi tiết danh mục tour theo ID
        Route::post('/', [TourCategoryController::class, 'store']); // tạo mới danh mục tour
        Route::put('/{id}', [TourCategoryController::class, 'update']); // cập nhật danh mục tour
        Route::post('/{id}/soft-delete', [TourCategoryController::class, 'softDelete']); // xóa mềm danh mục tour
        Route::delete('/{id}', [TourCategoryController::class, 'destroy']); // xóa vĩnh viễn danh mục tour
    });

    // -------- ALBUMS MANAGEMENT --------
    Route::prefix('albums')->group(function () {
        Route::get('/', [AlbumController::class, 'index']); // liệt kê tất cả album
        Route::post('/', [AlbumController::class, 'store']); // tạo mới album
        Route::get('/{id}', [AlbumController::class, 'show']); // lấy chi tiết album theo ID
        Route::put('/{id}', [AlbumController::class, 'update']); // cập nhật album
        Route::patch('/{id}/soft-delete', [AlbumController::class, 'softDelete']); // xóa mềm album
        Route::delete('/{id}', [AlbumController::class, 'destroy']); // xóa vĩnh viễn album
        Route::get('/trashed', [AlbumController::class, 'trashed']); // liệt kê album đã xóa
        Route::get('/statistics', [AlbumController::class, 'statistics']); // thống kê album
    });

    // -------- ALBUMS IMAGE --------
    Route::prefix('albums/{albumId}/images')->group(function () {
        Route::get('/', [AlbumImageController::class, 'index']); // liệt kê tất cả hình ảnh trong album
        Route::post('/', [AlbumImageController::class, 'store']); // thêm mới hình ảnh vào album
        Route::get('/{imageId}', [AlbumImageController::class, 'show']); // lấy chi tiết hình ảnh theo ID
        Route::post('/{imageId}', [AlbumImageController::class, 'update']); // cập nhật caption hình ảnh
        Route::patch('/{imageId}/soft-delete', [AlbumImageController::class, 'softDelete']); // xóa mềm hình ảnh
        Route::delete('/{imageId}', [AlbumImageController::class, 'destroy']); // xóa vĩnh viễn hình ảnh
        Route::get('/trashed', [AlbumImageController::class, 'trashed']); // liệt kê hình ảnh đã xóa
        Route::get('/statistics', [AlbumImageController::class, 'statistics']); // thống kê hình ảnh
    });

    // -------- DESTINATION-CATEGORIES --------
    Route::prefix('destination-categories')->group(function () {
        Route::get('/', [DestinationCategoryController::class, 'index']); // liệt kê tất cả danh mục điểm đến
        Route::get('/{id}', [DestinationCategoryController::class, 'show']); // lấy chi tiết danh mục điểm đến theo ID
        Route::post('/', [DestinationCategoryController::class, 'store']); // tạo mới danh mục điểm đến
        Route::put('/{id}', [DestinationCategoryController::class, 'update']); // cập nhật danh mục điểm đến
        Route::put('/{id}/soft-delete', [DestinationCategoryController::class, 'softDelete']); // xóa mềm danh mục điểm đến
        Route::delete('/{id}', [DestinationCategoryController::class, 'destroy']); // xóa vĩnh viễn danh mục điểm đến
    });

    // -------- DESTINATION --------
    Route::prefix('destinations')->group(function () {
        Route::get('/', [DestinationController::class, 'index']); // liệt kê tất cả điểm đến
        Route::get('/trashed', [DestinationController::class, 'trashed']); // liệt kê điểm đến đã xóa
        Route::get('/{id}', [DestinationController::class, 'show']); // lấy chi tiết điểm đến theo ID
        Route::post('/', [DestinationController::class, 'store']); // tạo mới điểm đến
        Route::put('/{id}', [DestinationController::class, 'update']); // cập nhật điểm đến
        Route::patch('/{id}/soft-delete', [DestinationController::class, 'softDelete']); // xóa mềm điểm đến
        Route::delete('/{id}', [DestinationController::class, 'destroy']); // xóa vĩnh viễn điểm đến
    });

    // -------- BOOKING --------
    Route::get('/bookings', [BookingController::class, 'index']); // liệt kê tất cả booking
    Route::get('/bookings/trashed', [BookingController::class, 'trashed']); // liệt kê booking đã xóa
    Route::post('/bookings', [BookingController::class, 'store']); // tạo mới booking
    Route::get('/bookings/{id}', [BookingController::class, 'show']); // lấy chi tiết booking theo ID
    Route::put('/bookings/{id}', [BookingController::class, 'update']); // cập nhật booking
    Route::patch('/bookings/{id}/soft-delete', [BookingController::class, 'softDelete']); // xóa mềm booking
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']); // xóa vĩnh viễn booking

    // -------- PROMOTIONS --------
    Route::prefix('promotions')->group(function () {
        Route::get('/', [PromotionController::class, 'index']); // liệt kê tất cả promotion
        Route::get('/trashed', [PromotionController::class, 'trashed']); // liệt kê promotion đã xóa
        Route::get('/statistics', [PromotionController::class, 'statistics']); // thống kê promotion
        Route::get('/{id}', [PromotionController::class, 'show']); // lấy chi tiết promotion theo ID
        Route::post('/', [PromotionController::class, 'store']); // tạo mới promotion
        Route::put('/{id}', [PromotionController::class, 'update']); // cập nhật promotion
        Route::patch('/{id}/soft-delete', [PromotionController::class, 'softDelete']); // xóa mềm promotion
        Route::delete('/{id}', [PromotionController::class, 'destroy']); // xóa vĩnh viễn promotion
    });

    // -------- SITE SETTINGS --------
    Route::prefix('settings')->group(function () {
        Route::get('/', [SiteSettingController::class, 'index']); // liệt kê tất cả các thiết lập
        Route::get('/trashed', [SiteSettingController::class, 'trashed']); // liệt kê các thiết lập đã xóa
        Route::get('/statistics', [SiteSettingController::class, 'statistics']); // thống kê các thiết lập
        Route::get('/key/{keyName}', [SiteSettingController::class, 'getByKey']); // lấy thiết lập theo key
        Route::get('/{id}', [SiteSettingController::class, 'show']); // lấy thiết lập theo ID
        Route::post('/', [SiteSettingController::class, 'store']); // tạo mới thiết lập
        Route::put('/{id}', [SiteSettingController::class, 'update']); // cập nhật thiết lập
        Route::patch('/{id}/soft-delete', [SiteSettingController::class, 'softDelete']); // xóa mềm thiết lập
        Route::delete('/{id}', [SiteSettingController::class, 'destroy']); // xóa thiết lập
        Route::post('/bulk-update', [SiteSettingController::class, 'bulkUpdate']); // cập nhật hàng loạt thiết lập
    });

    // -------- REVIEWS --------
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']); // liệt kê tất cả reviews
        Route::get('/trashed', [ReviewController::class, 'trashed']); // liệt kê reviews đã ẩn
        Route::get('/statistics', [ReviewController::class, 'statistics']); // thống kê reviews
        Route::get('/tour/{tourId}', [ReviewController::class, 'getByTour']); // reviews theo tour
        Route::get('/{id}', [ReviewController::class, 'show']); // lấy chi tiết review theo ID
        Route::post('/', [ReviewController::class, 'store']); // tạo mới review
        Route::put('/{id}', [ReviewController::class, 'update']); // cập nhật review
        Route::patch('/{id}/soft-delete', [ReviewController::class, 'softDelete']); // xóa mềm review
        Route::patch('/{id}/restore', [ReviewController::class, 'restore']); // khôi phục review
        Route::delete('/{id}', [ReviewController::class, 'destroy']); // xóa vĩnh viễn review
    });
});