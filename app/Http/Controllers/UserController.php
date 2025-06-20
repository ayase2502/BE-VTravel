<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    // Danh sách user (chỉ admin mới xem được)
    public function index()
    {
        return response()->json(User::all());
    }

    // Chi tiết user
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy user'], 404);
        $this->authorize('view', $user);

        $user->avatar_url = $user->avatar ? asset('storage/' . $user->avatar) : null;
        return response()->json($user);
    }

    // Tạo user mới (admin)
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'in:customer,staff,admin',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath,
            'role' => $request->role ?? 'customer'
        ]);

        $user->avatar_url = $avatarPath ? asset('storage/' . $avatarPath) : null;
        return response()->json(['message' => 'Thêm tài khoản thành công', 'user' => $user], 201);
    }

    // Cập nhật user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy user'], 404);
        $this->authorize('update', $user);

        $request->validate([
            'full_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $id . ',id',
            'phone' => 'sometimes|string|unique:users,phone,' . $id . ',id',
            'password' => 'nullable|string|min:6',
            'role' => 'in:customer,staff,admin',
            'avatar' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->fill($request->except(['password', 'avatar']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->avatar_url = $user->avatar ? asset('storage/' . $user->avatar) : null;
        return response()->json(['message' => 'Cập nhật thành công', 'user' => $user]);
    }

    // Xoá user (admin)
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy user'], 404);
        $this->authorize('delete', $user);

        if ($user->avatar) Storage::disk('public')->delete($user->avatar);
        $user->delete();

        return response()->json(['message' => 'Xoá user thành công']);
    }

    // Xem thông tin profile của user hiện tại
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'full_name' => 'nullable|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu avatar mới
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật thông tin khác
        if ($request->filled('full_name')) $user->full_name = $request->full_name;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('password')) $user->password = bcrypt($request->password);

        $user->save();

        return response()->json([
            'message' => 'Cập nhật thông tin cá nhân thành công!',
            'user' => $user
        ]);
    }
}
