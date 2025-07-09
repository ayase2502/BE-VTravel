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
use App\Http\Controllers\FavoriteController;
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
    Route::prefix('albums')->group(function () {
        Route::get('/', [AlbumController::class, 'index']); // liệt kê tất cả album
        Route::post('/', [AlbumController::class, 'store']); // tạo mới album
        Route::get('/{id}', [AlbumController::class, 'show']); // lấy chi tiết album theo ID
        Route::put('/{id}', [AlbumController::class, 'update']); // cập nhật album
        Route::post('/{id}/soft-delete', [AlbumController::class, 'softDelete']); // xóa mềm album
        Route::delete('/{id}', [AlbumController::class, 'destroy']); // xóa vĩnh viễn album
        Route::get('/trashed', [AlbumController::class, 'trashed']); // liệt kê album đã xóa
    });

    // -------- ALBUMS IMAGE --------
    Route::prefix('albums/{albumId}/images')->group(function () {
        Route::get('/', [AlbumImageController::class, 'index']); // liệt kê tất cả hình ảnh trong album
        Route::post('/', [AlbumImageController::class, 'store']); // thêm mới hình ảnh vào album
        Route::get('/{imageId}', [AlbumImageController::class, 'show']); // lấy chi tiết hình ảnh theo ID
        Route::post('/{imageId}', [AlbumImageController::class, 'update']); // cập nhật caption hình ảnh
        Route::post('/{imageId}/soft-delete', [AlbumImageController::class, 'softDelete']); // xóa mềm hình ảnh
        Route::delete('/{imageId}', [AlbumImageController::class, 'destroy']); // xóa vĩnh viễn hình ảnh
        Route::get('/trashed', [AlbumImageController::class, 'trashed']); // liệt kê hình ảnh đã xóa
        Route::get('/statistics', [AlbumImageController::class, 'statistics']); // thống kê hình ảnh
    });

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
        Route::get('/destinations', [DestinationController::class, 'index']);
        Route::get('/destinations/{id}', [DestinationController::class, 'show']);
        Route::post('/destinations', [DestinationController::class, 'store']);
        Route::put('/destinations/{id}', [DestinationController::class, 'update']);
        Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']);
        Route::post('/destinations/{id}/toggle', [DestinationController::class, 'softDelete']);
        Route::get('/destinations/trashed', [DestinationController::class, 'trashed']);
        Route::post('/destinations/{id}/highlight', [DestinationController::class, 'toggleHighlight']);
        Route::get('/destinations/highlights', [DestinationController::class, 'highlights']);
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

    // -------- FAVORITES --------
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']); // liệt kê tất cả favorites
        Route::get('/my-favorites', [FavoriteController::class, 'myFavorites']); // danh sách yêu thích của user hiện tại
        Route::get('/trashed', [FavoriteController::class, 'trashed']); // liệt kê favorites đã xóa
        Route::get('/statistics', [FavoriteController::class, 'statistics']); // thống kê favorites
        Route::get('/{id}', [FavoriteController::class, 'show']); // lấy chi tiết favorite theo ID
        Route::post('/', [FavoriteController::class, 'store']); // tạo mới favorite
        Route::put('/{id}', [FavoriteController::class, 'update']); // cập nhật favorite
        Route::patch('/{id}/soft-delete', [FavoriteController::class, 'softDelete']); // xóa mềm favorite
        Route::patch('/{id}/restore', [FavoriteController::class, 'restore']); // khôi phục favorite
        Route::delete('/{id}', [FavoriteController::class, 'destroy']); // xóa vĩnh viễn favorite
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