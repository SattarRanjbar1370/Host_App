<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Building;
use App\Models\Breakdown;
use App\Models\Location;
use App\Models\User;

class ApiTechnicianController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            // ۱. شناسایی تکنسین و دریافت دسترسی‌های بلوکی
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? User::with('locations.buildings')->find($userId) : null;

            $selectedMonth = function_exists('jdate') ? jdate()->format('Y/m') : date('Y-m');
            $displayMonth = function_exists('jdate') ? jdate()->format('%B %Y') : date('F Y');

            // ۲. فیلتر کردن بلوک‌ها و ورودی‌ها بر اساس محدوده مجاز تکنسین
            if ($user && $user->role !== 'super_admin') {
                $allowedLocationIds = $user->locations ? $user->locations->pluck('id')->toArray() : [];

                $locations = Location::whereIn('id', $allowedLocationIds)->get();
                $buildings = Building::whereIn('location_id', $allowedLocationIds)->with('location')->get();
            } else {
                $locations = Location::all();
                $buildings = Building::with('location')->get();
            }

            $allowedBuildingIds = $buildings->pluck('id')->toArray();

            // ۳. فیلتر خرابی‌ها: محدود به ورودی‌های مجاز و کارهای منتسب به این تکنسین
            $tasksQuery = Breakdown::where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '!=', 'completed');
            })
                ->where('approval_status', 'pending')
                ->whereIn('building_id', $allowedBuildingIds)
                ->with('building.location', 'parts');

            if ($user && $user->role === 'technician') {
                $tasksQuery->where(function ($q) use ($user) {
                    $q->whereNull('user_id')
                        ->orWhere('user_id', 0)
                        ->orWhere('user_id', $user->id);
                });
            }

            $tasks = $tasksQuery->latest()->get();

            // ۴. دریافت سرویس‌های ثبت‌شده برای ورودی‌های مجاز
            $servicesMap = collect();
            if (Schema::hasTable('monthly_services')) {
                $servicesMap = DB::table('monthly_services')
                    ->whereIn('building_id', $allowedBuildingIds)
                    ->orderBy('id', 'desc')
                    ->get()
                    ->keyBy('building_id');
            }

            // ۵. متصل کردن وضعیت سرویس مستقیماً به آبجکت ساختمان‌ها (جهت حفظ تیک و یادداشت در فرانت‌اند)
            $buildings->transform(function ($bld) use ($servicesMap) {
                $svc = $servicesMap->get($bld->id);
                $bld->service = $svc ? [
                    'id' => $svc->id,
                    'is_completed' => (bool) $svc->is_completed,
                    'is_approved' => (bool) ($svc->is_approved ?? 0),
                    'approved_at' => $svc->approved_at ?? null,
                    'service_note' => $svc->technician_note ?? $svc->service_note ?? '',
                    'technician_note' => $svc->technician_note ?? $svc->service_note ?? '',
                    'service_month' => $svc->service_month ?? ''
                ] : [
                    'id' => null,
                    'is_completed' => false,
                    'is_approved' => false,
                    'approved_at' => null,
                    'service_note' => '',
                    'technician_note' => '',
                    'service_month' => ''
                ];
                $bld->service_note = $svc ? ($svc->technician_note ?? $svc->service_note ?? '') : '';
                return $bld;
            });

            return response()->json([
                'user' => $user,
                'selected_month' => $selectedMonth,
                'display_month' => $displayMonth,
                'locations' => $locations,
                'buildings' => $buildings,
                'all_buildings' => $buildings,
                'monthly_services' => $servicesMap,
                'tasks' => $tasks,
                'breakdowns' => $tasks,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

   public function updateBreakdown(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $breakdown = Breakdown::findOrFail($id);

            $rawUrgency = $request->input('urgency') ?: ($request->input('priority') ?: ($breakdown->urgency ?: 'postponed'));
            $safeUrgency = in_array($rawUrgency, ['urgent', 'immediate']) ? 'immediate' : 'postponed';

            $partsInput = $request->input('parts', []);
            $totalCalculatedPartsPrice = 0;
            $primaryPartName = $request->input('part_name', $breakdown->part_name);
            $primaryPartCount = (int) $request->input('part_count', $breakdown->part_count ?? 1);

            if (!empty($partsInput) && is_array($partsInput)) {
                $breakdown->parts()->delete();
                $names = [];

                foreach ($partsInput as $item) {
                    $name = trim($item['part_name'] ?? '');
                    if (empty($name)) continue;

                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $unitPrice = (float) ($item['unit_price'] ?? 0);
                    $lineTotal = (float) ($item['total_price'] ?? ($qty * $unitPrice));

                    $breakdown->parts()->create([
                        'part_name'   => $name,
                        'quantity'    => $qty,
                        'unit_price'  => $unitPrice,
                        'total_price' => $lineTotal,
                        'part_code'   => $item['part_code'] ?? null,
                    ]);

                    $totalCalculatedPartsPrice += $lineTotal;
                    $names[] = $name . ($qty > 1 ? " ({$qty} عدد)" : "");
                }

                if (!empty($names)) {
                    $primaryPartName = implode(' ، ', $names);
                    $primaryPartCount = count($names);
                }
            }

            $finalAmount = $totalCalculatedPartsPrice > 0 ? $totalCalculatedPartsPrice : (float) $request->input('amount', $breakdown->amount ?? 0);

            $breakdown->update([
                'building_id'     => $request->input('building_id', $breakdown->building_id),
                'user_id'         => $request->input('user_id', $breakdown->user_id),
                'building_unit'   => $request->input('building_unit', $breakdown->building_unit),
                'title'           => $request->input('title', $breakdown->title),
                'description'     => $request->input('description', $breakdown->description),
                'technician_note' => $request->input('technician_note', $breakdown->technician_note),
                'urgency'         => $safeUrgency,
                'part_name'       => $primaryPartName,
                'part_count'      => $primaryPartCount,
                'part_price'      => $totalCalculatedPartsPrice,
                'is_billable'     => $request->boolean('is_billable'),
                'amount'          => $finalAmount,
            ]);

            DB::commit();

            return response()->json([
                'status'    => 'success',
                'message'   => 'خرابی با موفقیت ویرایش شد.',
                'breakdown' => $breakdown->load('parts')
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function destroyBreakdown($id)
    {
        try {
            $breakdown = Breakdown::findOrFail($id);
            $breakdown->delete();

            return response()->json(['status' => 'success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

   public function storeBreakdown(Request $request)
{
    try {
        DB::beginTransaction();

        $rawUrgency = $request->input('urgency') ?: ($request->input('priority') ?: 'postponed');
        $safeUrgency = in_array($rawUrgency, ['urgent', 'immediate']) ? 'immediate' : 'postponed';

        $createdAt = $request->input('performed_at') ? date('Y-m-d H:i:s', strtotime($request->input('performed_at'))) : now();

        $partsInput = $request->input('parts', []);
        $totalCalculatedPartsPrice = 0;
        $primaryPartName = $request->input('part_name');
        $primaryPartCount = (int) $request->input('part_count', 1);

        $breakdown = Breakdown::create([
            'building_id'     => $request->input('building_id'),
            'user_id'         => $request->input('user_id') ?: (auth()->id() ?: null),
            'title'           => $request->input('title'),
            'building_unit'   => $request->input('building_unit'),
            'description'     => $request->input('description'),
            'technician_note' => $request->input('technician_note'),
            'is_billable'     => filter_var($request->input('is_billable'), FILTER_VALIDATE_BOOLEAN),
            'amount'          => (float) $request->input('amount', 0),
            'part_name'       => $primaryPartName,
            'part_count'      => $primaryPartCount,
            'urgency'         => $safeUrgency,
            'approval_status' => 'pending',
            'status'          => 'in_progress',
            'created_at'      => $createdAt,
        ]);

        if (!empty($partsInput) && is_array($partsInput)) {
            $names = [];
            foreach ($partsInput as $item) {
                $name = trim($item['part_name'] ?? '');
                if (empty($name)) continue;

                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = (float) ($item['total_price'] ?? ($qty * $unitPrice));

                $breakdown->parts()->create([
                    'part_name'   => $name,
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'total_price' => $lineTotal,
                    'part_code'   => $item['part_code'] ?? null,
                ]);

                $totalCalculatedPartsPrice += $lineTotal;
                $names[] = $name . ($qty > 1 ? " ({$qty} عدد)" : "");
            }

            if (!empty($names)) {
                $breakdown->update([
                    'part_name'  => implode(' ، ', $names),
                    'part_count' => count($names),
                    'part_price' => $totalCalculatedPartsPrice,
                    'amount'     => $totalCalculatedPartsPrice > 0 ? $totalCalculatedPartsPrice : $breakdown->amount,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'status'  => 'success',
            'message' => 'خرابی با موفقیت ثبت شد.',
            'data'    => $breakdown->load('parts')
        ], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}


  // نمونه متدی که تکنسین با آن کار را تمام می‌کند (در کنترلر مربوطه)
 public function completeTask(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $breakdown = ($id instanceof Breakdown) ? $id : Breakdown::findOrFail($id);

        $partsInput = $request->input('parts', []);
        $totalCalculatedPartsPrice = 0;
        $primaryPartName = '';
        $primaryPartCount = 1;

        if (!empty($partsInput) && is_array($partsInput)) {
            // حذف قطعات قبلی همین تسک و ثبت لیست جدید
            $breakdown->parts()->delete();

            $partNamesList = [];

            foreach ($partsInput as $item) {
                $name = trim($item['part_name'] ?? '');
                if (empty($name)) continue;

                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = (float) ($item['total_price'] ?? ($qty * $unitPrice));

                $breakdown->parts()->create([
                    'part_name'   => $name,
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'total_price' => $lineTotal,
                    'part_code'   => $item['part_code'] ?? null,
                ]);

                $totalCalculatedPartsPrice += $lineTotal;
                $partNamesList[] = $name . ($qty > 1 ? " ({$qty} عدد)" : "");
            }

            $primaryPartName = implode(' ، ', $partNamesList);
            $primaryPartCount = count($partNamesList);
        } else {
            // حالت سازگاری با سیستم قبل
            $primaryPartName = $request->input('part_name', $breakdown->part_name);
            $primaryPartCount = (int) $request->input('part_count', $breakdown->part_count ?? 1);
            $totalCalculatedPartsPrice = (float) $request->input('amount', $breakdown->amount ?? 0);
        }

        $technicianNote = $request->input('technician_note', $breakdown->technician_note);

        $breakdown->update([
            'technician_note' => $technicianNote,
            'part_name'       => $primaryPartName,
            'part_count'      => $primaryPartCount,
            'part_price'      => $totalCalculatedPartsPrice,
            'amount'          => $totalCalculatedPartsPrice > 0 ? $totalCalculatedPartsPrice : $breakdown->amount,
            'status'          => 'in_progress',
            'approval_status' => 'pending', // تا قبل از تایید مدیر در انتظار می‌ماند
        ]);

        DB::commit();

        return response()->json([
            'status'    => 'success',
            'message'   => 'گزارش رفع عیب و لیست قطعات با موفقیت ذخیره شد.',
            'breakdown' => $breakdown->load('parts')
        ], 200);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function toggleService(Request $request)
    {
        try {
            $buildingId = $request->input('building_id');
            $userId = $request->input('user_id') ?: (auth()->id() ?: 2);
            $serviceMonth = $request->input('service_month') ?: (function_exists('jdate') ? jdate()->format('Y/m') : date('Y-m'));
            $isCompleted = $request->boolean('is_completed');
            $technicianNote = $request->input('technician_note', '');

            $service = DB::table('monthly_services')
                ->where('building_id', $buildingId)
                ->where('service_month', $serviceMonth)
                ->first();

            // اگر توسط ادمین تایید و قفل شده باشد، نباید تغییر کند
            if ($service && !empty($service->is_approved)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'این سرویس توسط مدیریت تایید و قفل شده است و امکان تغییر آن وجود ندارد.'
                ], 403);
            }

            $nowJalali = function_exists('jdate') ? jdate()->format('Y/m/d') : date('Y/m/d');

            if ($service) {
                DB::table('monthly_services')
                    ->where('id', $service->id)
                    ->update([
                        'user_id' => $userId,
                        'is_completed' => $isCompleted ? 1 : 0,
                        'serviced_at' => $isCompleted ? ($service->serviced_at ?: $nowJalali) : null,
                        'technician_note' => $technicianNote,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('monthly_services')->insert([
                    'building_id' => $buildingId,
                    'user_id' => $userId,
                    'service_month' => $serviceMonth,
                    'serviced_at' => $isCompleted ? $nowJalali : null,
                    'is_completed' => $isCompleted ? 1 : 0,
                    'technician_note' => $technicianNote,
                    'is_approved' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'وضعیت سرویس با موفقیت ثبت شد.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

   public function updateMonthlyService(Request $request)
    {
        try {
            $buildingId = $request->input('building_id');
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $serviceMonth = $request->input('service_month') ?: (function_exists('jdate') ? jdate()->format('Y/m') : date('Y-m'));
            $isCompleted = $request->boolean('is_completed');
            $newNote = trim($request->input('technician_note', $request->input('service_note', '')));
            $performedAt = $request->input('performed_at'); // زمان ثبت واقعی در گوشی

            $service = DB::table('monthly_services')
                ->where('building_id', $buildingId)
                ->where('service_month', $serviceMonth)
                ->first();

            // اگر سرویس توسط مدیریت تایید و قفل شده باشد
            if ($service && !empty($service->is_approved)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'این سرویس توسط مدیریت تایید و قفل شده است و امکان تغییر آن وجود ندارد.'
                ], 403);
            }

            // تعیین تاریخ سرویس بر اساس زمان واقعی انجام کار یا تاریخ جاری
            $serviceDate = $performedAt ? substr($performedAt, 0, 10) : (function_exists('jdate') ? jdate()->format('Y/m/d') : date('Y/m/d'));

            if ($service) {
                // مدیریت تداخل یادداشت‌ها: اگر یادداشت قبلی وجود دارد و متن جدیدی آمده که متفاوت است
                $finalNote = $service->technician_note ?? '';
                if (!empty($newNote)) {
                    if (empty($finalNote)) {
                        $finalNote = $newNote;
                    } elseif ($finalNote !== $newNote && !str_contains($finalNote, $newNote)) {
                        // نگه داشتن هر دو یادداشت جهت تصمیم‌گیری مدیر ارشد
                        $userObj = User::find($userId);
                        $techName = $userObj ? $userObj->name : "تکنسین $userId";
                        $finalNote = $finalNote . " | [ثبت همزمان توسط {$techName}]: " . $newNote;
                    }
                }

                DB::table('monthly_services')
                    ->where('id', $service->id)
                    ->update([
                        'user_id' => $userId ?: $service->user_id,
                        'is_completed' => $isCompleted ? 1 : 0,
                        'serviced_at' => $isCompleted ? ($service->serviced_at ?: $serviceDate) : null,
                        'technician_note' => $finalNote,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('monthly_services')->insert([
                    'building_id' => $buildingId,
                    'user_id' => $userId,
                    'service_month' => $serviceMonth,
                    'serviced_at' => $isCompleted ? $serviceDate : null,
                    'is_completed' => $isCompleted ? 1 : 0,
                    'technician_note' => $newNote,
                    'is_approved' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'وضعیت سرویس ماهانه با موفقیت ذخیره شد.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}