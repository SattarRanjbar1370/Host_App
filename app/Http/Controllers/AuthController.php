<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // نمایش صفحه لاگین اصلی service123.ir
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    // پردازش فرم لاگین
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'شماره موبایل الزامی است.',
            'password.required' => 'رمز عبور را وارد کنید.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password'], 'is_active' => true], $remember)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'phone' => 'اطلاعات ورود نادرست است یا حساب کاربری غیرفعال می‌باشد.',
        ])->onlyInput('phone');
    }

    // هدایت هوشمند بر اساس نقش کاربر
    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'super_admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/technician');
    }

    // خروج از حساب
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}