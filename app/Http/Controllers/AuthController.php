<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'fullname' => 'required|string|max:50',
            'email' => 'required|string|email|unique:users',
            'sdt' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'fullname' => $request->fullname,
            'email'=> $request->email,
            'sdt'=> $request->sdt,
            'password'=> bcrypt($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => "Đăng ký thành công!",
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function login(Request $request){
        // $user = User::where('email', $request->email)->first();

        // if (! $user || !Hash::check($request->password, $user->password)) {
        //     return response()->json(['message' => "Sai email hoặc mật khẩu"], 401);
        // }

        $request -> validate([
            'login'=> 'required|string',
            'password'=>'required'
        ]);

        $user = User::where('email',$request->login)->orwhere('sdt',$request->login)->first();

        if(!$user||!Hash::check($request->password, $user->password)){
            return response()->json(['message'=>"Sai thông tin đăng nhập!"],401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => "Đăng nhập thành công!",
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request){
        // $request->user()->currentAccessToken()->delete();
        // return response()->json(['message' => "Đăng xuất thành công!"]);
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        return response()->json(['message' => "Đăng xuất thành công!"]);
    }
}