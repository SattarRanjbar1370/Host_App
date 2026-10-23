<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'شماره موبایل الزامی است.',
            'password.required' => 'رمز عبور را وارد کنید.',
        ]);

        // ۱. تبدیل ارقام فارسی و عربی به انگلیسی و پاک‌سازی فاصله‌ها
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $phone = str_replace($persian, $english, (string)$request->phone);
        $phone = str_replace($arabic, $english, $phone);
        $phone = trim($phone);

        // ۲. جستجوی کاربر با شماره ورودی (با صفر یا بدون صفر)
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            $altPhone = str_starts_with($phone, '0') ? substr($phone, 1) : ('0' . $phone);
            $user = User::where('phone', $altPhone)->first();
        }

        // ۳. بررسی صحت رمز عبور
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'شماره موبایل یا رمز عبور اشتباه است.'
            ], 401);
        }

        // ۴. بررسی وضعیت فعال بودن اکانت
        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'message' => 'حساب کاربری شما غیرفعال است.'
            ], 403);
        }

        // ۵. تولید توکن ورود
        $token = Str::random(64);
        $user->forceFill(['remember_token' => hash('sha256', $token)])->save();

        return response()->json([
            'token' => $token,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
                'role'  => strtolower(trim($user->role ?? 'technician')),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'با موفقیت خارج شدید.']);
    }
}