<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        $isAdmin = Auth::check(); // true nếu admin tạo, false nếu khách

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^(0|\+84)[0-9]{9,10}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['nullable', 'in:customer,staff,admin'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được đăng ký',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'phone.unique' => 'Số điện thoại đã được đăng ký',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'role.in' => 'Quyền không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
            'is_verified' => $isAdmin, // admin tạo thì tự động xác thực
        ]);

        if (!$isAdmin) {
            $otpRequest = new Request([
                'user_id' => $user->id,
                'method' => filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone'
            ]);
        }

        return response()->json([
            'message' => $isAdmin ? 'Tạo tài khoản thành công!' : 'Đăng ký thành công! Vui lòng xác thực tài khoản',
            'user' => $user,
            'need_verification' => !$isAdmin, // frontend dùng để hiển thị bước xác thực
        ], 201);
    }

    // Đăng nhập bằng email hoặc phone
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'login.required' => 'Vui lòng nhập email hoặc số điện thoại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $login = $request->input('login');

        if (!filter_var($login, FILTER_VALIDATE_EMAIL) && !preg_match('/^(0|\+84)[0-9]{9,10}$/', $login)) {
            return response()->json([
                'message' => 'Định dạng không hợp lệ (email hoặc số điện thoại)',
            ], 422);
        }

        $user = User::where('email', $login)
            ->orWhere('phone', $login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác',
            ], 401);
        }

        if ($user->is_deleted === 'inactive') {
            return response()->json([
                'message' => 'Tài khoản đã bị vô hiệu hóa',
            ], 403);
        }

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
        ])->cookie(
                'auth_token',
                $token,
                60 * 24 * 7,
                null,
                null,
                false, // Secure = false trong local, cần bật khi deploy
                true,
                false,
                'Strict'
            );
    }


    // Đăng xuất
    public function logout(Request $request)
    {
        Log::info('Logout route hit');

        try {
            $user = $request->user();
            Log::info('User:', [$user]);

            // Lấy access token
            $accessToken = $user?->currentAccessToken();

            // Chỉ xóa token nếu đúng loại
            if ($accessToken instanceof PersonalAccessToken) {
                $accessToken->delete();
                Log::info('Token deleted');
            } else {
                Log::info('Không phải PersonalAccessToken, không xóa');
            }

            // Nếu dùng session (guard web)
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
                Log::info('Đã logout khỏi session web');
            }

            // Invalidate session & regenerate CSRF
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Đăng xuất thành công'])
                ->cookie('XSRF-TOKEN', '', -1)
                ->cookie('laravel_session', '', -1);
        } catch (\Throwable $e) {
            Log::error('Logout failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Đăng xuất thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}