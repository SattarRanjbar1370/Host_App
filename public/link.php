<?php
// ساخت میانبر اختصاصی پوشه استوریج به ریشه وبسایت
$target = __DIR__ . '/../laravel_core/storage/app/public';
$shortcut = __DIR__ . '/storage';

if (file_exists($shortcut)) {
    // اگر فایل یا میانبر ناقص از قبل بود حذف شود
    @unlink($shortcut);
}

if (symlink($target, $shortcut)) {
    echo "<h2 style='color:green;'>✔ پیوند پوشه تصاویر (storage:link) با موفقیت ساخته شد!</h2>";
} else {
    echo "<h2 style='color:red;'>خطا در ایجاد پیوند. دسترسی‌های هاست را بررسی کنید.</h2>";
}