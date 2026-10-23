<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Breakdown;
use App\Models\Building;
use App\Models\User;
use App\Models\Payment;
use App\Models\Contact;
use App\Models\Location;
use App\Models\TechnicianPayout;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // داشبورد اصلی و یکپارچه مدیریت
    public function index()
    {
        $pendingApprovals = Breakdown::where('approval_status', 'pending')
            ->with(['building.location', 'user'])
            ->latest()
            ->get();

        $pendingBreakdowns = $pendingApprovals;

        // دیتای بلوک‌ها و ساختمان‌ها برای تب درون‌صفحه‌ای
        $locations = Location::with(['buildings.contacts', 'users'])->latest()->get();

        // دیتای تکنسین‌ها و پرونده‌ها برای تب درون‌صفحه‌ای
        $technicians = User::where('role', 'technician')
            ->with(['locations', 'breakdowns.building', 'payouts'])
            ->withCount(['breakdowns as completed_tasks_count' => function ($q) {
                $q->where('approval_status', 'approved');
            }])
            ->latest()
            ->get();

        $stats = [
            'pending_count' => $pendingApprovals->count(),
            'total_buildings' => Building::count(),
            'total_technicians' => $technicians->count(),
        ];

        $companies = Company::latest()->get();

        return view('admin.dashboard', compact('pendingApprovals', 'pendingBreakdowns', 'stats', 'locations', 'technicians', 'companies'));
    }

    // تایید تسک و انتقال به معین حسابداری (سازگار با AJAX بدون رفرش)
    public function approve(Request $request, Breakdown $breakdown)
    {
       $breakdown->update([
            'approval_status' => 'approved',
            'is_billable' => $breakdown->amount > 0,
            'admin_note' => $request->admin_note ?? $breakdown->admin_note,
        ]);

        // اگر درخواست ایجکس بود یا هدر JSON داشت، فقط موفقیت بفرست و ریدایرکت نکن
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تسک تایید شد.');
    }
        
    public function approveBreakdown(Request $request, Breakdown $breakdown)
    {
        return $this->approve($request, $breakdown);
    }

    // ویرایش مبلغ، قطعه مصرفی، تعیین پورسانت پرسنل و تایید نهایی
    public function updateAndApprove(Request $request, Breakdown $breakdown)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'part_name' => 'nullable|string|max:150',
            'part_price' => 'nullable|numeric|min:0',
            'technician_share_type' => 'nullable|in:none,percentage,fixed',
            'technician_share_value' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string',
        ]);

        $amount = (float) $request->amount;
        $shareType = $request->technician_share_type ?? 'none';
        $shareValue = (float) ($request->technician_share_value ?? 0);
        $shareAmount = 0;

        if ($shareType === 'percentage' && $shareValue > 0) {
            $shareAmount = round(($amount * $shareValue) / 100);
        } elseif ($shareType === 'fixed') {
            $shareAmount = $shareValue;
        }

        $breakdown->update([
            'amount' => $amount,
            'part_name' => $request->part_name ?? $breakdown->part_name,
            'part_price' => $request->part_price ?? ($breakdown->part_price ?? 0),
            'technician_share_type' => $shareType,
            'technician_share_value' => $shareValue,
            'technician_share_amount' => $shareAmount,
            'admin_note' => $request->admin_note,
            'is_billable' => $amount > 0,
            'approval_status' => 'approved',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'مبلغ و سهم تکنسین با موفقیت اعمال شد.',
                'id' => $breakdown->id
            ]);
        }

        return back()->with('success', 'مبلغ و سهم تکنسین اعمال و رکورد با موفقیت تایید شد.');
    }

    public function updateAndApproveBreakdown(Request $request, Breakdown $breakdown)
    {
        return $this->updateAndApprove($request, $breakdown);
    }

    // تبدیل به سرویس رایگان (سازگار با AJAX بدون رفرش)
    public function rejectCost(Request $request, Breakdown $breakdown)
    {
        $breakdown->update([
            'is_billable' => false,
            'amount' => 0,
            'technician_share_amount' => 0,
            'approval_status' => 'approved',
            'admin_note' => 'هزینه توسط مدیر ارشد لغو و جزو سرویس عادی محاسبه شد.',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'هزینه لغو و به سرویس رایگان تبدیل شد.',
                'id' => $breakdown->id
            ]);
        }

        return back()->with('success', 'هزینه لغو و کار به صورت سرویس رایگان ثبت گردید.');
    }

    public function rejectToFree(Request $request, Breakdown $breakdown)
    {
        return $this->rejectCost($request, $breakdown);
    }

    // نمایش صفحه مدیریت با ساختار درختی بلوک‌ها و ساختمان‌ها
    public function buildings()
    {
        $locations = Location::with(['buildings.contacts', 'users'])->latest()->get();
        $technicians = User::where('role', 'technician')->where('is_active', true)->get();

        return view('admin.buildings', compact('locations', 'technicians'));
    }

    // ۱. ثبت محدوده / بلوک جدید (پدر)
    public function storeLocation(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'technicians' => 'nullable|array',
            'technicians.*' => 'exists:users,id',
        ]);

        $location = Location::create($request->only('title', 'code', 'address'));

        if ($request->has('technicians')) {
            $location->users()->sync($request->technicians);
        }

        return back()->with('success', 'محدوده/بلوک جدید با موفقیت ایجاد شد.');
    }

    // ویرایش محدوده / بلوک
    public function updateLocation(Request $request, Location $location)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $location->update($request->only('title', 'code', 'address'));

        return back()->with('success', 'اطلاعات بلوک ویرایش شد.');
    }

    // حذف محدوده / بلوک
    public function destroyLocation(Location $location)
    {
        $location->delete();
        return back()->with('success', 'بلوک و ساختمان‌های زیرمجموعه آن به بایگانی منتقل شدند.');
    }

    // ویرایش دسترسی پرسنل به کل بلوک
    public function updateLocationAccess(Request $request, Location $location)
    {
        $location->users()->sync($request->technicians ?? []);

        return back()->with('success', 'دسترسی تکنسین‌ها به این بلوک ذخیره شد.');
    }

    // ۲. ثبت ساختمان یا ورودی جدید داخل یک بلوک (فرزند)
    public function storeBuilding(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'title' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'has_monthly_fee' => 'nullable',
            'monthly_fee' => 'nullable|numeric|min:0',
        ]);

        $hasMonthlyFee = $request->has('has_monthly_fee');
        $monthlyFee = $hasMonthlyFee ? ($request->monthly_fee ?? 0) : 0;

        Building::create([
            'location_id' => $request->location_id,
            'title' => $request->title,
            'code' => $request->code,
            'address' => $request->address,
            'has_monthly_fee' => $hasMonthlyFee,
            'monthly_fee' => $monthlyFee,
        ]);

        return back()->with('success', 'ساختمان/ورودی با موفقیت به این بلوک اضافه شد.');
    }

    // ویرایش مشخصات ساختمان / ورودی
    public function updateBuilding(Request $request, Building $building)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'has_monthly_fee' => 'nullable',
            'monthly_fee' => 'nullable|numeric|min:0',
        ]);

        $hasMonthlyFee = $request->has('has_monthly_fee');
        $monthlyFee = $hasMonthlyFee ? ($request->monthly_fee ?? 0) : 0;

        $building->update([
            'title' => $request->title,
            'code' => $request->code,
            'address' => $request->address,
            'has_monthly_fee' => $hasMonthlyFee,
            'monthly_fee' => $monthlyFee,
        ]);

        return back()->with('success', 'اطلاعات ساختمان به‌روزرسانی شد.');
    }

    // حذف ساختمان / ورودی
    public function destroyBuilding(Building $building)
    {
        $building->delete();
        return back()->with('success', 'ساختمان حذف شد.');
    }

    // ۳. ثبت مخاطب (مدیر / سرایدار) برای یک ساختمان یا ورودی
    public function storeContact(Request $request, Building $building)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role_title' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
        ]);

        $building->contacts()->create($request->only('name', 'role_title', 'phone'));

        return back()->with('success', 'اطلاعات مدیر/مخاطب برای این ساختمان ثبت شد.');
    }

    // حذف مخاطب
    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'مخاطب حذف گردید.');
    }

    // ذخیره دسترسی‌های پرسنل
    public function updateAccess(Request $request, Building $building)
    {
        $building->users()->sync($request->technicians ?? []);

        return back()->with('success', 'دسترسی تکنسین‌ها با موفقیت ذخیره شد.');
    }

    // ثبت استثنائات دسترسی ورودی خاص برای تکنسین‌ها
    public function updateBuildingExclusions(Request $request, Building $building)
    {
        $excludedTechIds = $request->input('excluded_technicians', []);

        $syncData = [];
        foreach ($excludedTechIds as $techId) {
            $syncData[$techId] = ['is_excluded' => true];
        }

        $building->users()->sync($syncData);

        return back()->with('success', 'محدودیت دسترسی این ورودی ثبت شد.');
    }

    // ثبت واریز نقدی / بانکی به تکنسین
    public function storeTechnicianPayout(Request $request, User $technician)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'paid_at' => 'nullable|string|max:20',
            'tracking_code' => 'nullable|string|max:100',
            'payment_type' => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        $technician->payouts()->create([
            'amount' => $request->amount,
            'paid_at' => $request->paid_at ?? (function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d')),
            'tracking_code' => $request->tracking_code,
            'payment_type' => $request->payment_type,
            'description' => $request->description,
        ]);

        return back()->with('success', 'واریزی به حساب تکنسین با موفقیت ثبت شد.');
    }

    // حذف یک واریزی در صورت ثبت اشتباه
    public function destroyTechnicianPayout(TechnicianPayout $payout)
    {
        $payout->delete();
        return back()->with('success', 'سند واریزی حذف شد.');
    }

    // صفحه حسابداری و معین
    public function accounting()
    {
        $buildings = Building::with([
            'location',
            'breakdowns' => function ($q) {
                $q->where('approval_status', 'approved')->latest();
            },
            'payments' => function ($q) {
                $q->latest();
            }
        ])->get();

        $recentPayments = Payment::with(['building.location', 'user'])->latest()->take(10)->get();

        return view('admin.accounting', compact('buildings', 'recentPayments'));
    }

    // ثبت فیش واریزی
    public function storePayment(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'amount' => 'required|numeric|min:1000',
            'paid_at' => 'required|date',
            'tracking_code' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        Payment::create([
            'building_id' => $request->building_id,
            'recorded_by' => auth()->id(),
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'tracking_code' => $request->tracking_code,
            'description' => $request->description,
        ]);

        return back()->with('success', 'واریزی در حساب معین بلوک ثبت شد.');
    }

    // خروجی اکسل بدون وابستگی (CSV با فرمت UTF-8)
    public function exportExcel(Request $request)
    {
        $fileName = 'service123_report_' . date('Y_m_d') . '.csv';
        $breakdowns = Breakdown::with(['building', 'user'])->where('approval_status', 'approved')->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['شناسه', 'ساختمان', 'واحد/ورودی', 'شرح کار', 'تکنسین', 'مبلغ (تومان)', 'تاریخ'];

        $callback = function () use ($breakdowns, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);

            foreach ($breakdowns as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->building->title ?? '-',
                    $item->building_unit ?? '-',
                    $item->title,
                    $item->user->name ?? 'سیستم',
                    $item->is_billable ? $item->amount : 'سرویس عادی',
                    function_exists('jdate') ? jdate($item->created_at)->format('Y/m/d') : $item->created_at->format('Y/m/d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // پیش‌نمایش و چاپ PDF
    public function exportPdf(Building $building)
    {
        $breakdowns = $building->breakdowns()->where('approval_status', 'approved')->with('user')->get();
        $totalCost = $breakdowns->sum('amount');
        $totalPaid = $building->payments()->sum('amount');
        $balance = $totalPaid - $totalCost;

        return view('reports.print', compact('building', 'breakdowns', 'totalCost', 'totalPaid', 'balance'));
    }

    // نمایش صفحه لیست و پرونده پرسنل
    public function technicians()
    {
        $technicians = User::where('role', 'technician')
            ->with(['locations', 'breakdowns.building', 'payouts'])
            ->withCount(['breakdowns as completed_tasks_count' => function ($q) {
                $q->where('approval_status', 'approved');
            }])
            ->latest()
            ->get();

        $locations = Location::with('buildings')->get();

        return view('admin.technicians', compact('technicians', 'locations'));
    }

    // ثبت تکنسین جدید
    public function storeTechnician(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone',
            'national_code' => 'nullable|string|max:10',
            'secondary_phone' => 'nullable|string|max:15',
            'birth_date' => 'nullable|string|max:15',
            'base_salary' => 'nullable|numeric|min:0',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'national_code' => $request->national_code,
            'secondary_phone' => $request->secondary_phone,
            'birth_date' => $request->birth_date,
            'base_salary' => $request->base_salary ?? 0,
            'password' => bcrypt($request->password),
            'role' => 'technician',
            'is_active' => true,
        ]);

        return back()->with('success', 'تکنسین جدید با موفقیت ثبت شد.');
    }

    // تغییر وضعیت فعال / معلق
    public function toggleTechnicianStatus(User $technician)
    {
        $technician->update(['is_active' => !$technician->is_active]);
        $status = $technician->is_active ? 'فعال' : 'معلق';
        return back()->with('success', "وضعیت تکنسین به $status تغییر یافت.");
    }

    // سپردن کار جدید به پرسنل همراه با ثبت سهم و پورسانت
    public function assignTask(Request $request, User $technician)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'title' => 'required|string|max:200',
            'building_unit' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'technician_share_type' => 'nullable|in:none,percentage,fixed',
            'technician_share_value' => 'nullable|numeric|min:0',
        ]);

        $amount = (float) ($request->amount ?? 0);
        $isBillable = $amount > 0;
        $shareType = $request->technician_share_type ?? 'none';
        $shareValue = (float) ($request->technician_share_value ?? 0);
        $shareAmount = 0;

        if ($shareType === 'percentage' && $shareValue > 0) {
            $shareAmount = round(($amount * $shareValue) / 100);
        } elseif ($shareType === 'fixed') {
            $shareAmount = $shareValue;
        }

        Breakdown::create([
            'building_id' => $request->building_id,
            'user_id' => $technician->id,
            'title' => $request->title,
            'building_unit' => $request->building_unit,
            'is_billable' => $isBillable,
            'amount' => $amount,
            'technician_share_type' => $shareType,
            'technician_share_value' => $shareValue,
            'technician_share_amount' => $shareAmount,
            'approval_status' => 'pending',
            'admin_note' => 'ارجاع مستقیم از طرف مدیر مجموعه',
        ]);

        return back()->with('success', 'کار با موفقیت به تکنسین محول شد.');
    }

    // ویرایش پرونده مشخصات پرسنل
    public function updateTechnician(Request $request, User $technician)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone,' . $technician->id,
            'national_code' => 'nullable|string|max:10',
            'secondary_phone' => 'nullable|string|max:15',
            'birth_date' => 'nullable|string|max:15',
            'base_salary' => 'nullable|numeric|min:0',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'national_code' => $request->national_code,
            'secondary_phone' => $request->secondary_phone,
            'birth_date' => $request->birth_date,
            'base_salary' => $request->base_salary ?? 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $technician->update($data);

        return back()->with('success', 'مشخصات پرونده پرسنل با موفقیت به‌روزرسانی شد.');
    }

    // خروجی اکسل UTF-8 با ساپورت فارسی
    public function exportBlockExcel(Request $request, Location $location)
    {
        $month = $request->get('month', function_exists('jdate') ? jdate()->format('Y/m') : now()->format('Y/m'));
        $fileName = 'Service_Report_' . $location->title . '_' . str_replace('/', '-', $month) . '.csv';

        $buildings = $location->buildings()->with(['monthlyServices' => function ($q) use ($month) {
            $q->where('service_month', $month);
        }])->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($buildings, $location, $month) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ["گزارش عملیات سرویس و نگهداری آسانسور - $location->title (دوره: $month)"]);
            fputcsv($file, []);
            fputcsv($file, ['ردیف', 'ورودی / ساختمان', 'وضعیت سرویس', 'تاریخ سرویس', 'توضیحات و گزارش فنی سرویس‌کار']);

            foreach ($buildings as $index => $b) {
                $srv = $b->monthlyServices->first();
                fputcsv($file, [
                    $index + 1,
                    $b->title . ($b->code ? " ($b->code)" : ''),
                    ($srv && $srv->is_completed) ? 'انجام شد' : 'انجام نشده',
                    $srv?->serviced_at ?: '-',
                    $srv?->technician_note ?: '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // نمایش برگه ماهانه بلوک با سربرگ داینامیک
    public function blockMonthlyReport(Request $request, Location $location)
    {
        $month = $request->get('month', function_exists('jdate') ? jdate()->format('Y/m') : now()->format('Y/m'));

        $buildings = $location->buildings()->with(['monthlyServices' => function ($q) use ($month) {
            $q->where('service_month', $month);
        }])->get();

        $breakdowns = Breakdown::whereIn('building_id', $buildings->pluck('id'))
            ->where('approval_status', 'approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->with('building', 'user')
            ->get();

        $companies = Company::all();
        if ($companies->isEmpty()) {
            $configList = config('companies.list', []);
            foreach ($configList as $item) {
                Company::create([
                    'name' => $item['name'],
                    'brand' => $item['brand'] ?? $item['name'],
                    'reg_number' => $item['reg_number'] ?? null,
                    'national_id' => $item['national_id'] ?? null,
                    'phone' => $item['phone'] ?? null,
                ]);
            }
            $companies = Company::all();
        }

        $defaultCompany = $companies->where('is_default', true)->first() ?? $companies->first();

        return view('reports.block_sheet', compact('location', 'month', 'buildings', 'breakdowns', 'companies', 'defaultCompany'));
    }

    // ثبت شرکت و سربرگ جدید با قابلیت آپلود لوگو
    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'reg_number' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies', 'public');
        }

        Company::create([
            'name' => $request->name,
            'brand' => $request->brand ?: $request->name,
            'reg_number' => $request->reg_number,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'logo_path' => $logoPath,
            'is_default' => Company::count() === 0,
        ]);

        return back()->with('success', 'شرکت و سربرگ جدید با موفقیت ایجاد شد.');
    }

    // ویرایش مشخصات شرکت و لوگو
    public function updateCompany(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'reg_number' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $data = $request->only('name', 'brand', 'reg_number', 'national_id', 'phone');

        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update($data);

        return back()->with('success', 'مشخصات سربرگ شرکت به‌روزرسانی شد.');
    }

    // حذف شرکت
    public function destroyCompany(Company $company)
    {
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }
        $company->delete();

        return back()->with('success', 'شرکت مورد نظر حذف گردید.');
    }

    // پیش‌نمایش و چاپ فاکتور اختصاصی ورودی در قطع A5
    public function buildingInvoiceA5(Request $request, Building $building)
    {
        $month = $request->get('month', function_exists('jdate') ? jdate()->format('Y/m') : now()->format('Y/m'));

        $breakdowns = $building->breakdowns()
            ->where('approval_status', 'approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->with('user')
            ->get();

        $monthlyService = $building->monthlyServices()
            ->where('service_month', $month)
            ->first();

        $primaryContact = $building->contacts()->first();

        $companies = Company::all();
        if ($companies->isEmpty()) {
            $configList = config('companies.list', []);
            foreach ($configList as $item) {
                Company::create([
                    'name' => $item['name'],
                    'brand' => $item['brand'] ?? $item['name'],
                    'reg_number' => $item['reg_number'] ?? null,
                    'national_id' => $item['national_id'] ?? null,
                    'phone' => $item['phone'] ?? null,
                ]);
            }
            $companies = Company::all();
        }

        $defaultCompany = $companies->where('is_default', true)->first() ?? $companies->first();

        return view('reports.invoice_a5', compact(
            'building',
            'month',
            'breakdowns',
            'monthlyService',
            'primaryContact',
            'companies',
            'defaultCompany'
        ));
    }
    // ویرایش تسک محول‌شده به تکنسین
    public function updateAssignedTask(Request $request, Breakdown $breakdown)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'building_id' => 'required|exists:buildings,id',
            'building_unit' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric|min:0',
            'technician_share_type' => 'nullable|in:none,percentage,fixed',
            'technician_share_value' => 'nullable|numeric|min:0',
        ]);

        $amount = (float) ($request->amount ?? 0);
        $shareType = $request->technician_share_type ?? 'none';
        $shareValue = (float) ($request->technician_share_value ?? 0);
        $shareAmount = 0;

        if ($shareType === 'percentage' && $shareValue > 0) {
            $shareAmount = round(($amount * $shareValue) / 100);
        } elseif ($shareType === 'fixed') {
            $shareAmount = $shareValue;
        }

        $breakdown->update([
            'building_id' => $request->building_id,
            'title' => $request->title,
            'building_unit' => $request->building_unit,
            'is_billable' => $amount > 0,
            'amount' => $amount,
            'technician_share_type' => $shareType,
            'technician_share_value' => $shareValue,
            'technician_share_amount' => $shareAmount,
        ]);

        return back()->with('success', 'تسک با موفقیت ویرایش شد.');
    }

    // حذف تسک ارجاع‌شده
    public function destroyTask(Breakdown $breakdown)
    {
        $breakdown->delete();
        return back()->with('success', 'تسک ارجاعی با موفقیت حذف شد.');
    }
}