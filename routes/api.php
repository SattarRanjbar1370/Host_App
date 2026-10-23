<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiTechnicianController;
use App\Http\Controllers\Api\ApiAdminController;
use App\Http\Controllers\Api\ApiAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// تست سلامت API
Route::get('/test-ping', function () {
    return response()->json(['status' => 'ok', 'message' => 'API is working!']);
});

// تابع بازگشتی برای تعریف یکسان مسیرها (پشتیبانی همزمان از /api و /api/v1)
$registerAppRoutes = function () {
    // ۱. احراز هویت و لاگین
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // ۲. پنل و عملیات تکنسین
    Route::prefix('technician')->group(function () {
        Route::get('/dashboard', [ApiTechnicianController::class, 'dashboard']);
        
        // ثبت خرابی جدید (پشتیبانی از هر دو حالت مفرد و جمع)
        Route::post('/breakdown', [ApiTechnicianController::class, 'storeBreakdown']);
        Route::post('/breakdowns', [ApiTechnicianController::class, 'storeBreakdown']);

        // ویرایش خرابی (پشتیبانی از تمام حالات مفرد، جمع و پسوند /update)
        Route::post('/breakdown/{id}', [ApiTechnicianController::class, 'updateBreakdown']);
        Route::put('/breakdown/{id}', [ApiTechnicianController::class, 'updateBreakdown']);
        Route::post('/breakdowns/{id}', [ApiTechnicianController::class, 'updateBreakdown']);
        Route::put('/breakdowns/{id}', [ApiTechnicianController::class, 'updateBreakdown']);
        Route::post('/breakdowns/{id}/update', [ApiTechnicianController::class, 'updateBreakdown']);
        Route::post('/breakdown/{id}/update', [ApiTechnicianController::class, 'updateBreakdown']);

        // حذف خرابی
        Route::delete('/breakdown/{id}', [ApiTechnicianController::class, 'destroyBreakdown']);
        Route::delete('/breakdowns/{id}', [ApiTechnicianController::class, 'destroyBreakdown']);

        // تکمیل کار و ثبت قطعات توسط تکنسین
        Route::post('/task/{breakdown}/complete', [ApiTechnicianController::class, 'completeTask']);
        Route::post('/task/{id}/complete', [ApiTechnicianController::class, 'completeTask']);
        Route::post('/breakdown/{id}/complete', [ApiTechnicianController::class, 'completeTask']);
        Route::post('/breakdowns/{id}/complete', [ApiTechnicianController::class, 'completeTask']);

        // سرویس‌های ماهانه
        Route::post('/monthly-service', [ApiTechnicianController::class, 'updateMonthlyService']);
        Route::post('/service/toggle', [ApiTechnicianController::class, 'toggleService']);
    });

    // ۳. پنل مدیریت، تاییدات و عملیات اداری
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);

        // تاییدات و کارتابل پرسنل
        Route::post('/breakdown/{breakdown}/approve', [ApiAdminController::class, 'approve']);
        Route::post('/breakdown/{breakdown}/reject-cost', [ApiAdminController::class, 'rejectCost']);
        Route::post('/breakdown/{breakdown}/update-approve', [ApiAdminController::class, 'updateAndApprove']);
        Route::post('/tasks/{id}/update-and-approve', [ApiAdminController::class, 'updateAndApprove']);
        Route::post('/assign-task', [ApiAdminController::class, 'assignTask']);
        Route::delete('/task/{id}', [ApiAdminController::class, 'destroyTask']);
        Route::delete('/tasks/{id}', [ApiAdminController::class, 'destroyTask']);

        // تایید سرویس‌های ماهانه توسط مدیریت (ارشد / میانی)
        Route::post('/services/approve', [ApiAdminController::class, 'approveMonthlyService']);

        // تسویه‌حساب تکنسین
        Route::post('/technician/{id}/payout', [ApiAdminController::class, 'storeTechnicianPayout']);

        // مدیریت اکانت‌ها و پرسنل
        Route::get('/users', [ApiAccountController::class, 'index']);
        Route::post('/users', [ApiAccountController::class, 'store']);
        Route::patch('/users/{id}/toggle-status', [ApiAccountController::class, 'toggleStatus']);
        Route::patch('/users/{id}/reset-password', [ApiAccountController::class, 'resetPassword']);
        Route::put('/users/{id}', [ApiAccountController::class, 'update']);
        Route::delete('/users/{id}', [ApiAccountController::class, 'destroy']);
        Route::post('/users/{id}/delete', [ApiAccountController::class, 'destroy']);

        // مدیریت سربرگ شرکت‌ها
        Route::post('/companies', [ApiAdminController::class, 'storeCompany']);
        Route::post('/companies/{id}', [ApiAdminController::class, 'updateCompany']);
        Route::delete('/companies/{id}', [ApiAdminController::class, 'destroyCompany']);
        Route::post('/companies/{id}/delete', [ApiAdminController::class, 'destroyCompany']);

        // دفتر معین مالی و واریزی‌ها
        Route::get('/accounting', [ApiAdminController::class, 'getAccountingLedger']);
        Route::post('/payments', [ApiAdminController::class, 'storePayment']);

        // بایگانی‌ها
        Route::get('/breakdowns/archive', [ApiAdminController::class, 'archive']);
        Route::get('/services/archive', [ApiAdminController::class, 'getArchiveServices']);
        
        // دریافت وضعیت ماه کاری فعال و آمار سرویس‌ها
        Route::get('/monthly-services/period-status', [App\Http\Controllers\Api\ApiAdminController::class, 'getServicePeriodStatus']);

        // بستن دستی دوره ماه جاری و شروع دوره ماه بعد
        Route::post('/monthly-services/close-period', [App\Http\Controllers\Api\ApiAdminController::class, 'closeServicePeriod']);
        
        // بازگردانی خرابی از بایگانی به کارتابل
        Route::post('/breakdown/{id}/restore', [App\Http\Controllers\Api\ApiAdminController::class, 'restoreBreakdown']);
    });

    // ۴. مدیریت پروژه‌ها
    Route::get('/projects', [ApiAdminController::class, 'getProjects']);
    Route::post('/projects', [ApiAdminController::class, 'storeProject']);
    Route::patch('/projects/{id}/progress', [ApiAdminController::class, 'updateProjectProgress']);

    // ۵. مرکز پیام دوطرفه (Message Center)
    Route::get('/messages', [ApiAdminController::class, 'getMessages']);
    Route::post('/messages', [ApiAdminController::class, 'storeMessage']);
    Route::post('/messages/{id}/read', [ApiAdminController::class, 'markMessageAsRead']);

    // ۶. دفتر گزارش‌های شیفت
    Route::get('/shift-reports', [ApiAdminController::class, 'getShiftReports']);
    Route::post('/shift-reports', [ApiAdminController::class, 'storeShiftReport']);
    Route::patch('/shift-reports/{id}/feedback', [ApiAdminController::class, 'updateShiftReportFeedback']);

    // ۷. استعلام آخرین نسخه برنامه
    Route::get('/app/version', function () {
        $latest = DB::table('app_versions')
            ->orderBy('version_code', 'desc')
            ->first();

        if (!$latest) {
            return response()->json([
                'latest_version_code' => 35,
                'latest_version_name' => '1.0.35',
                'download_url' => 'https://service123.ir/downloads/app-release.apk',
                'release_notes' => 'نسخه رسمی برنامه',
                'force_update' => false
            ]);
        }

        return response()->json([
            'latest_version_code' => (int) $latest->version_code,
            'latest_version_name' => $latest->version_name,
            'download_url' => $latest->download_url,
            'release_notes' => $latest->release_notes,
            'force_update' => (bool) $latest->force_update,
            'released_at' => $latest->created_at
        ]);
    });

    // ۸. ثبت خودکار نسخه جدید توسط گیت‌هاب اکشنز (امن با توکن)
    Route::post('/app/internal-register-version', function (Request $request) {
        if ($request->header('X-DEPLOY-KEY') !== 'service123_deploy_token_9988') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $buildNum = (int) $request->input('version_code');
        $versionName = $request->input('version_name', '1.0.' . $buildNum);
        $downloadUrl = $request->input('download_url', 'https://service123.ir/downloads/app-release.apk');
        $notes = $request->input('release_notes', 'به‌روزرسانی خودکار و بهبود کارایی سیستم');

        DB::table('app_versions')->updateOrInsert(
            ['version_code' => $buildNum],
            [
                'version_name' => $versionName,
                'download_url' => $downloadUrl,
                'release_notes' => $notes,
                'force_update' => 0,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json(['status' => 'success', 'registered_version' => $buildNum]);
    });
};

// ثبت مسیرها در هر دو حالت: /api/... و /api/v1/...
$registerAppRoutes();
Route::prefix('v1')->group($registerAppRoutes);