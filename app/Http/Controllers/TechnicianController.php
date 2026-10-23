<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Breakdown;
use App\Models\Building;
use App\Models\MonthlyService;

class TechnicianController extends Controller
{
    // نمایش صفحه اصلی و داشبورد تکنسین
    public function index(Request $request)
    {
        $user = Auth::user();

        // استخراج بلوک‌های مجاز تکنسین
        if ($user->role === 'super_admin') {
            $locations = \App\Models\Location::with('buildings')->get();
            $buildings = Building::with('location')->get();
        } else {
            $locations = $user->locations()->with('buildings')->get();
            $locationIds = $locations->pluck('id');
            $excludedBuildingIds = $user->excludedBuildings()->pluck('buildings.id');

            $buildings = Building::with('location')
                ->whereIn('location_id', $locationIds)
                ->whereNotIn('id', $excludedBuildingIds)
                ->get();
        }

        // ماه جاری شمسی
        $currentMonth = function_exists('jdate') ? jdate()->format('Y/m') : now()->format('Y/m');
        $selectedMonth = $request->get('month', $currentMonth);

        // سوابق سرویس‌های ماه جاری ورودی‌های مجاز
        $monthlyServices = MonthlyService::where('service_month', $selectedMonth)
            ->whereIn('building_id', $buildings->pluck('id'))
            ->get()
            ->keyBy('building_id');

        // خرابی‌ها و کارهای ارجاعی
        $buildingIds = $buildings->pluck('id');
        $tasks = Breakdown::whereIn('building_id', $buildingIds)
            ->where('status', '!=', 'completed')
            ->with('building.location')
            ->latest()
            ->get();

        return view('technician.index', compact('buildings', 'locations', 'tasks', 'monthlyServices', 'selectedMonth'));
    }

    // ثبت کار یا خرابی جدید (فوری یا برای آینده)
    public function storeBreakdown(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'building_unit' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'urgency' => 'required|in:immediate,postponed',
            'is_billable' => 'nullable',
            'amount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'part_name' => 'nullable|string|max:150',
            'part_price' => 'nullable|numeric|min:0',
        ]);

        $isBillable = $request->has('is_billable');
        $amount = $isBillable ? ($request->amount ?? 0) : 0;

        Breakdown::create([
            'building_id' => $request->building_id,
            'building_unit' => $request->building_unit,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'part_name' => $request->part_name,
            'part_price' => $request->part_price ?? 0,
            'technician_note' => $request->description,
            'urgency' => $request->urgency,
            'status' => 'pending_approval',
            'is_billable' => $isBillable,
            'amount' => $amount,
            'approval_status' => 'pending',
        ]);

        return back()->with('success', 'گزارش کار با موفقیت ثبت و ارسال شد.');
    }

    // تیک زدن و ثبت اتمام کار
    public function completeTask(Request $request, Breakdown $breakdown)
    {
        $request->validate([
            'is_billable' => 'nullable',
            'amount' => 'nullable|numeric',
            'technician_note' => 'nullable|string',
        ]);

        $isBillable = $request->has('is_billable');
        $amount = $isBillable ? ($request->amount ?? 0) : 0;

        $breakdown->update([
            'status' => 'completed',
            'is_billable' => $isBillable,
            'amount' => $amount,
            'technician_note' => $request->technician_note ?? $breakdown->technician_note,
            'approval_status' => $amount > 0 ? 'pending' : 'approved',
        ]);

        return back()->with('success', 'وضعیت کار به انجام‌شده تغییر یافت.');
    }

    // ذخیره یا به‌روزرسانی سرویس ماهانه یک ورودی
    public function updateMonthlyService(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'service_month' => 'required|string|max:7',
            'serviced_at' => 'nullable|string|max:20',
            'is_completed' => 'nullable',
            'technician_note' => 'nullable|string',
        ]);

        $isCompleted = $request->has('is_completed');
        $defaultDate = function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d');

        MonthlyService::updateOrCreate(
            [
                'building_id' => $request->building_id,
                'service_month' => $request->service_month,
            ],
            [
                'user_id' => auth()->id(),
                'serviced_at' => $isCompleted ? ($request->serviced_at ?: $defaultDate) : null,
                'is_completed' => $isCompleted,
                'technician_note' => $request->technician_note,
            ]
        );

        return back()->with('success', 'وضعیت سرویس ورودی با موفقیت ثبت شد.');
    }
}