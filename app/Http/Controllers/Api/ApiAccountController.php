<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ApiAccountController extends Controller
{
    // دریافت لیست کاربران بر اساس سطح دسترسی نقش کاربر جاری همراه با بلوک‌های مجاز
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        $query = User::with('locations')->latest();

        // اگر ادمین معمولی بود، فقط تکنسین‌ها را ببیند
        if ($currentUser && $currentUser->role === 'admin') {
            $query->where('role', 'technician');
        }

        $users = $query->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'phone' => $u->phone,
                'role' => $u->role,
                'is_active' => (bool) $u->is_active,
                'national_code' => $u->national_code,
                'secondary_phone' => $u->secondary_phone,
                'locations' => $u->locations ? $u->locations->map(function ($loc) {
                    return [
                        'id' => $loc->id,
                        'title' => $loc->title,
                    ];
                }) : [],
                'created_at_jalali' => function_exists('jdate') ? jdate($u->created_at)->format('Y/m/d') : $u->created_at->format('Y/m/d'),
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    // تعریف اکانت جدید به همراه تخصیص اولیه‌ی بلوک‌ها
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,admin,technician',
            'national_code' => 'nullable|string|max:10',
            'secondary_phone' => 'nullable|string|max:15',
            'base_salary' => 'nullable|numeric|min:0',
            'location_ids' => 'nullable|array',
        ], [
            'phone.unique' => 'این شماره موبایل قبلاً در سیستم ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
            'role.in' => 'نقش انتخاب‌شده معتبر نیست.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'national_code' => $validated['national_code'] ?? null,
            'secondary_phone' => $validated['secondary_phone'] ?? null,
            'base_salary' => $validated['base_salary'] ?? 0,
            'is_active' => true,
        ]);

        // همگام‌سازی بلوک‌ها برای تکنسین و مدیر میانی
        if ($user->role !== 'super_admin' && $request->has('location_ids') && is_array($request->location_ids)) {
            $user->locations()->sync($request->location_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'اکانت با موفقیت ایجاد شد.',
            'user' => $user->load('locations')
        ], 201);
    }

    // تغییر وضعیت فعال / معلق
    public function toggleStatus(Request $request, $id)
    {
        $currentUser = auth()->user();
        $targetUser = User::findOrFail($id);

        if ($targetUser->role === 'super_admin' && ($currentUser && $currentUser->id !== $targetUser->id)) {
            return response()->json([
                'success' => false,
                'message' => 'امکان تغییر وضعیت مدیر ارشد وجود ندارد.'
            ], 403);
        }

        if ($currentUser && $currentUser->role === 'admin' && $targetUser->role !== 'technician') {
            return response()->json([
                'success' => false,
                'message' => 'عدم دسترسی به تغییر وضعیت این کاربر.'
            ], 403);
        }

        $targetUser->is_active = !$targetUser->is_active;
        $targetUser->save();

        return response()->json([
            'success' => true,
            'message' => 'وضعیت اکانت با موفقیت تغییر کرد.',
            'is_active' => $targetUser->is_active
        ]);
    }

    // ریست سریع رمز عبور
    public function resetPassword(Request $request, $id)
    {
        $currentUser = auth()->user();
        $targetUser = User::findOrFail($id);

        if ($currentUser && $currentUser->role === 'admin' && $targetUser->role !== 'technician') {
            return response()->json([
                'success' => false,
                'message' => 'عدم دسترسی به تغییر رمز این اکانت.'
            ], 403);
        }

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $targetUser->password = Hash::make($request->password);
        $targetUser->save();

        return response()->json([
            'success' => true,
            'message' => 'رمز عبور با موفقیت به‌روزرسانی شد.'
        ]);
    }

    // ویرایش کامل مشخصات اکانت و بلوک‌های مجاز
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone,' . $user->id,
            'role' => 'required|in:super_admin,admin,technician',
            'national_code' => 'nullable|string|max:10',
            'secondary_phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:6',
            'location_ids' => 'nullable|array',
        ], [
            'phone.unique' => 'این شماره موبایل قبلاً برای اکانت دیگری ثبت شده است.',
            'password.min' => 'رمز عبور در صورت تغییر باید حداقل ۶ کاراکتر باشد.',
            'role.in' => 'نقش انتخاب‌شده نامعتبر است.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'national_code' => $validated['national_code'] ?? null,
            'secondary_phone' => $validated['secondary_phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // همگام‌سازی بلوک‌های منتسب به کاربر
        if ($user->role !== 'super_admin' && $request->has('location_ids')) {
            $user->locations()->sync($request->location_ids ?: []);
        } elseif ($user->role === 'super_admin') {
            // مدیر ارشد نیازی به وابستگی در جدول واسط ندارد
            $user->locations()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'مشخصات اکانت و بلوک‌های مجاز با موفقیت ویرایش شد.',
            'user' => $user->load('locations')
        ]);
    }

    // حذف نرم اکانت (انتقال به بایگانی)
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'کاربر مورد نظر یافت نشد.'
            ], 404);
        }

        if ($user->id == 1) {
            return response()->json([
                'success' => false,
                'message' => 'اکانت اصلی سیستم قابل حذف نیست.'
            ], 403);
        }

        $user->is_active = false;
        $user->save();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'اکانت با موفقیت به بایگانی منتقل شد.'
        ]);
    }
}