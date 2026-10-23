<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ==========================================
// مشخصات ادمین ارشد دلخواهت را اینجا وارد کن:
$phone    = '09012477761'; // شماره موبایل دلخواه
$name     = 'ستار رنجبر';    // نام مدیر
$password = 'Sattar0132831';    // رمز عبور دلخواه
// ==========================================

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// ایجاد یا به‌روزرسانی مشخصات ادمین ارشد
$user = User::updateOrCreate(
    ['phone' => $phone],
    [
        'name'     => $name,
        'password' => Hash::make($password),
        'role'     => 'super_admin',
    ]
);

echo "<div style='font-family:tahoma; padding:20px; background:#e8f5e9; color:#2e7d32; border-radius:8px;'>";
echo "<h2>✔ کاربر ادمین ارشد با موفقیت ثبت/بروزرسانی شد!</h2>";
echo "<b>نام:</b> " . $user->name . "<br>";
echo "<b>شماره موبایل:</b> " . $user->phone . "<br>";
echo "<b>نقش کاربری:</b> " . $user->role . "<br>";
echo "</div>";