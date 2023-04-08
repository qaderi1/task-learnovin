<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use JWTAuth;
use App\Models\User;

//ایجاد توکن برای درخواست کاربر
class UserController extends Controller
{
    public function generateToken($userId)
    {

        $user = User::find($userId);
        //اگر کاربر باشد
        if ($user) {
            $user = User::findOrFail($userId);
            $token = JWTAuth::fromUser($user);
            $user->update(['remember_token' => $token]);
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['token' => 'user not find']);
        }
    }

    public function getUser(Request $request)
    {


        // گرفتن توکن از هدر
        $token = JWTAuth::parseToken()->getToken();


        try {
            // بررسی اعتبار توکن
            $user = JWTAuth::toUser($token);
        } catch (JWTException $e) {
            // اگر توکن نامعتبر بود
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        // بررسی وجود کاربر
        $user = User::where('remember_token', $token)->first();


        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // اگر توکن و کاربر معتبر بودند، اطلاعات کاربر را بازگردانی می‌کنیم
        return response()->json(['user' => $user]);
    }
}