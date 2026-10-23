<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiTechnicianController;
use App\Http\Controllers\Api\ApiAdminController;

// ==================== ۱. احراز هویت و خروج وب سنتی ====================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ۲. پنل وب سنتی تکنسین ====================
Route::middleware('auth')->prefix('technician')->name('technician.')->group(function () {
    Route::get('/', [TechnicianController::class, 'index'])->name('dashboard');
    Route::post('/breakdown', [TechnicianController::class, 'storeBreakdown'])->name('breakdown.store');
    Route::post('/task/{breakdown}/complete', [TechnicianController::class, 'completeTask'])->name('task.complete');
    Route::post('/monthly-service', [TechnicianController::class, 'updateMonthlyService'])->name('monthlyService.update');
});

// ==================== ۳. پنل وب سنتی مدیریت ارشد ====================
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // کارتابل تاییدات تسک‌ها و خرابی‌ها
    Route::post('/breakdown/{breakdown}/approve', [AdminController::class, 'approve'])->name('breakdown.approve');
    Route::post('/breakdown/{breakdown}/update-approve', [AdminController::class, 'updateAndApprove'])->name('breakdown.update_approve');
    Route::post('/breakdown/{breakdown}/reject-cost', [AdminController::class, 'rejectCost'])->name('breakdown.reject_cost');
    Route::put('/technicians/tasks/{breakdown}', [AdminController::class, 'updateAssignedTask'])->name('technician.task.update');
    Route::delete('/technicians/tasks/{breakdown}', [AdminController::class, 'destroyTask'])->name('technician.task.destroy');

    // مدیریت محدوده‌ها / بلوک‌ها
    Route::post('/locations', [AdminController::class, 'storeLocation'])->name('location.store');
    Route::put('/locations/{location}', [AdminController::class, 'updateLocation'])->name('location.update');
    Route::delete('/locations/{location}', [AdminController::class, 'destroyLocation'])->name('location.destroy');
    Route::post('/locations/{location}/access', [AdminController::class, 'updateLocationAccess'])->name('location.access');
    Route::get('/buildings/{building}/invoice-a5', [AdminController::class, 'buildingInvoiceA5'])->name('building.invoice.a5');
    Route::get('/reports/block/{location}', [AdminController::class, 'blockMonthlyReport'])->name('report.block');
    Route::get('/reports/block/{location}/excel', [AdminController::class, 'exportBlockExcel'])->name('report.block.excel');
    Route::get('/export/pdf/{building}', [AdminController::class, 'exportPdf'])->name('export.pdf');

    // مدیریت ورودی‌ها / ساختمان‌ها
    Route::get('/buildings', [AdminController::class, 'buildings'])->name('buildings');
    Route::post('/buildings', [AdminController::class, 'storeBuilding'])->name('building.store');
    Route::put('/buildings/{building}', [AdminController::class, 'updateBuilding'])->name('building.update');
    Route::delete('/buildings/{building}', [AdminController::class, 'destroyBuilding'])->name('building.destroy');
    Route::post('/buildings/{building}/exclusions', [AdminController::class, 'updateBuildingExclusions'])->name('building.exclusions');

    // دفترچه مخاطبان
    Route::post('/buildings/{building}/contacts', [AdminController::class, 'storeContact'])->name('building.contact.store');
    Route::delete('/contacts/{contact}', [AdminController::class, 'destroyContact'])->name('contact.destroy');

    // مدیریت پرسنل و پرونده تکنسین‌ها
    Route::get('/technicians', [AdminController::class, 'technicians'])->name('technicians');
    Route::post('/technicians', [AdminController::class, 'storeTechnician'])->name('technician.store');
    Route::post('/technicians/{technician}/toggle', [AdminController::class, 'toggleTechnicianStatus'])->name('technician.toggle');
    Route::post('/technicians/{technician}/assign', [AdminController::class, 'assignTask'])->name('technician.assign');
    Route::put('/technicians/{technician}', [AdminController::class, 'updateTechnician'])->name('technician.update');
    
    // اسناد مالی پرسنل
    Route::post('/technicians/{technician}/payouts', [AdminController::class, 'storeTechnicianPayout'])->name('technician.payout.store');
    Route::delete('/technician-payouts/{payout}', [AdminController::class, 'destroyTechnicianPayout'])->name('technician.payout.destroy');

    // حسابداری معین
    Route::get('/accounting', [AdminController::class, 'accounting'])->name('accounting');
    Route::post('/payments', [AdminController::class, 'storePayment'])->name('payment.store');
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');

    // سربرگ شرکت 
    Route::post('/companies', [AdminController::class, 'storeCompany'])->name('company.store');
    Route::put('/companies/{company}', [AdminController::class, 'updateCompany'])->name('company.update');
    Route::delete('/companies/{company}', [AdminController::class, 'destroyCompany'])->name('company.destroy');
});

// ==================== ۴. روت‌های ورود API ====================
Route::post('/api/v1/login', [ApiAuthController::class, 'login']);

// ==================== ۵. روت‌های API پنل ادمین (یکپارچه و مرتب‌شده) ====================
Route::prefix('api/v1/admin')->group(function () {
    Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
    
    // کارتابل تاییدات
    Route::post('/breakdown/{id}/approve', [ApiAdminController::class, 'approve']);
    Route::post('/breakdown/{id}/reject-cost', [ApiAdminController::class, 'rejectCost']);
    Route::post('/breakdowns/{id}/update-approve', [ApiAdminController::class, 'updateAndApprove']);
    
    // پرسنل و تکنسین‌ها
    Route::post('/technicians', [ApiAdminController::class, 'storeTechnician']);
    Route::match(['put', 'post'], '/technicians/{id}', [ApiAdminController::class, 'updateTechnician']);
    Route::post('/technicians/{id}/toggle-status', [ApiAdminController::class, 'toggleTechnicianStatus']);
    Route::post('/technicians/{id}/pay', [ApiAdminController::class, 'storeTechnicianPayout']);
    Route::post('/tasks/assign', [ApiAdminController::class, 'assignTask']);
    Route::match(['delete', 'post'], '/breakdowns/{id}/delete', [ApiAdminController::class, 'destroyTask']);

    // مدیریت بلوک‌ها (Locations)
    Route::post('/locations', [ApiAdminController::class, 'storeLocation']);
    Route::match(['put', 'post'], '/locations/{id}', [ApiAdminController::class, 'updateLocation']);
    Route::match(['delete', 'post'], '/locations/{id}/delete', [ApiAdminController::class, 'destroyLocation']);

    // مدیریت ورودی‌ها (Buildings)
    Route::post('/buildings', [ApiAdminController::class, 'storeBuilding']);
    Route::match(['put', 'post'], '/buildings/{id}', [ApiAdminController::class, 'updateBuilding']);
    Route::match(['delete', 'post'], '/buildings/{id}/delete', [ApiAdminController::class, 'destroyBuilding']);

    // مخاطبان و مدیران ساختمان (Contacts)
    Route::post('/buildings/{id}/contacts', [ApiAdminController::class, 'storeContact']);
    Route::match(['delete', 'post'], '/contacts/{id}/delete', [ApiAdminController::class, 'destroyContact']);
});

// ==================== ۶. روت‌های API تکنسین ====================
Route::prefix('api/v1/technician')->group(function () {
    Route::get('/dashboard', [ApiTechnicianController::class, 'dashboard']);
    Route::post('/breakdown', [ApiTechnicianController::class, 'storeBreakdown']);
    Route::match(['put', 'post'], '/breakdown/{id}', [ApiTechnicianController::class, 'updateBreakdown']);
    Route::match(['delete', 'post'], '/breakdown/{id}/delete', [ApiTechnicianController::class, 'destroyBreakdown']);
    Route::post('/task/{id}/complete', [ApiTechnicianController::class, 'completeTask']);
    Route::post('/monthly-service', [ApiTechnicianController::class, 'updateMonthlyService']);
    Route::get('/buildings/{id}/invoice-data', [ApiAdminController::class, 'getBuildingInvoiceData']);
});