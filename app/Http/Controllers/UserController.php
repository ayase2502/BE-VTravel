<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request){
        $user = $request->user();

        $request->validate([
            'fullname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'sdt' => 'required|string|unique:users,sdt,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if($request->hasFile('avatar')){
            $avatarPath = $request->file('avatar')->store('avatars','public');
            $user->avatar = $avatarPath;
        }

        $user->update([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'sdt' => $request->sdt,
        ]);

        return response()->json([
            'message' => 'Cập nhật thành công!',
            'user' => $user,
        ]);
    }

    public function profile(Request $request){
        return response()->json($request->user());
    }
}
