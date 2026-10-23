<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Breakdown;
use App\Models\Building;
use App\Models\User;
use App\Models\Location;
use App\Models\Company;
use App\Models\Contact;
use App\Models\TechnicianPayout;
use App\Models\Project;
use App\Models\Message;
use App\Models\ShiftReport;
use Illuminate\Support\Facades\DB;

class ApiAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            // ۱. شناسایی کاربر جاری
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? \App\Models\User::with('locations.buildings')->find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));

            $isSuperAdmin = ($role === 'super_admin' || (!$user && !$request->filled('user_id')));

            $accountsCount = \App\Models\User::count();

            // ۲. تعیین بلوک‌ها و ساختمان‌های مجاز بر اساس نقش
            if ($isSuperAdmin) {
                $locations = Location::with([
                    'buildings.contacts',
                    'buildings.location',
                    'buildings.breakdowns',
                    'users'
                ])->latest()->get();

                $allBuildings = Building::with('location')->get();
            } else {
                $allowedLocationIds = $user ? $user->locations->pluck('id')->toArray() : [];

                $locations = Location::whereIn('id', $allowedLocationIds)
                    ->with([
                        'buildings.contacts',
                        'buildings.location',
                        'buildings.breakdowns',
                        'users'
                    ])->latest()->get();

                $allBuildings = Building::whereIn('location_id', $allowedLocationIds)
                    ->with('location')
                    ->get();
            }

            $allowedBuildingIds = $allBuildings->pluck('id')->toArray();

            // ۳. کارتابل تاییدات مدیریت (فیلتر بر اساس ساختمان‌های مجاز)
            $pendingApprovalsQuery = Breakdown::where('approval_status', 'pending')
                ->with(['building.location', 'user', 'parts'])
                ->oldest();

            if (!$isSuperAdmin) {
                $pendingApprovalsQuery->whereIn('building_id', $allowedBuildingIds);
            }
            $pendingApprovals = $pendingApprovalsQuery->get();

            // استخراج وضعیت سرویس‌های ماهانه ثبت‌شده (اصلاح‌شده برای تایید و قفل شدن)
            $servicesMap = \Illuminate\Support\Facades\DB::table('monthly_services')
                ->whereIn('building_id', $allowedBuildingIds)
                ->orderBy('id', 'desc')
                ->get()
                ->keyBy('building_id');

            $allBuildings->transform(function ($bld) use ($servicesMap) {
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

            // ۴. پرونده تکنسین‌ها
            $techniciansQuery = User::where('role', 'technician')
                ->with(['locations.buildings', 'breakdowns.building.location', 'payouts'])
                ->withCount([
                    'breakdowns as completed_tasks_count' => function ($q) {
                        $q->where('approval_status', 'approved');
                    }
                ])
                ->latest();

            if ($role === 'technician' && $user) {
                $techniciansQuery->where('id', $user->id);
            }

            $technicians = $techniciansQuery->get()->map(function ($tech) use ($accountsCount) {
                $totalClaims = (float) $tech->breakdowns
                    ->where('approval_status', 'approved')
                    ->sum('technician_share_amount');

                $totalPaid = (float) ($tech->payouts ? $tech->payouts->sum('amount') : 0);

                $allowedBuildings = collect();
                if ($tech->locations) {
                    foreach ($tech->locations as $loc) {
                        if ($loc->buildings) {
                            $allowedBuildings = $allowedBuildings->merge($loc->buildings);
                        }
                    }
                }

                return [
                    'id' => $tech->id,
                    'name' => $tech->name,
                    'phone' => $tech->phone,
                    'national_code' => $tech->national_code ?: '-',
                    'secondary_phone' => $tech->secondary_phone ?: '',
                    'birth_date' => $tech->birth_date ?: '',
                    'base_salary' => $tech->base_salary ?: 0,
                    'is_active' => (bool) $tech->is_active,
                    'created_at_jalali' => function_exists('jdate') ? jdate($tech->created_at)->format('Y/m/d') : substr($tech->created_at, 0, 10),
                    'locations' => $tech->locations ?? [],
                    'allowed_buildings' => $allowedBuildings->unique('id')->values(),
                    'buildings' => $allowedBuildings->unique('id')->values(),
                    'completed_tasks_count' => $tech->completed_tasks_count ?? 0,
                    'accounts_count' => $accountsCount,
                    'technicians_count' => $accountsCount,
                    'tasks' => ($tech->breakdowns ?? collect())->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'title' => $t->title,
                            'building_unit' => $t->building_unit,
                            'amount' => (float) $t->amount,
                            'approval_status' => $t->approval_status,
                            'technician_note' => $t->technician_note,
                            'building' => $t->building,
                            'created_at_jalali' => function_exists('jdate') ? jdate($t->created_at)->format('Y/m/d H:i') : substr($t->created_at, 0, 16),
                        ];
                    })->values(),
                    'total_claims' => $totalClaims,
                    'total_paid' => $totalPaid,
                    'payouts' => $tech->payouts ?? [],
                ];
            });

            // ۵. سربرگ شرکت‌ها
            $companies = Company::latest()->get()->map(function ($comp) {
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'brand' => $comp->brand ?: $comp->name,
                    'reg_number' => $comp->reg_number ?: '-',
                    'national_id' => $comp->national_id ?: '-',
                    'phone' => $comp->phone ?: '-',
                    'logo_path' => $comp->logo_path,
                    'logo_url' => $comp->logo_path ? 'https://service123.ir/uploads/' . $comp->logo_path : null,
                    'logo_icon' => $comp->logo_icon ?: '🛗',
                ];
            });

            // ۶. دریافت کلیه پرسنل فعال
            $allStaff = User::select('id', 'name', 'phone', 'role')->where('is_active', true)->get();

            return response()->json([
                'stats' => [
                    'pending_count' => $pendingApprovals->count(),
                    'total_buildings' => $allBuildings->count(),
                    'total_technicians' => $technicians->count(),
                    'total_users' => $accountsCount,
                    'total_companies' => $companies->count(),
                ],
                'pending_approvals' => $pendingApprovals,
                'locations' => $locations,
                'buildings' => $allBuildings,
                'all_buildings' => $allBuildings,
                'technicians' => $technicians,
                'all_staff' => $allStaff,
                'companies' => $companies,
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

    public function storeLocation(Request $request)
    {
        $loc = Location::create($request->only('title', 'code', 'address'));
        return response()->json(['status' => 'success', 'location' => $loc]);
    }

    public function updateLocation(Request $request, $id)
    {
        $loc = Location::findOrFail($id);
        $loc->update($request->only('title', 'code', 'address'));
        return response()->json(['status' => 'success', 'location' => $loc]);
    }

    public function destroyLocation($id)
    {
        Location::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }

    public function storeBuilding(Request $request)
    {
        $data = $request->only('location_id', 'title', 'code', 'address', 'monthly_fee');
        $data['has_monthly_fee'] = $request->boolean('has_monthly_fee');
        $building = Building::create($data);
        return response()->json(['status' => 'success', 'building' => $building]);
    }

    public function updateBuilding(Request $request, $id)
    {
        $building = Building::findOrFail($id);
        $building->update([
            'title' => $request->title,
            'code' => $request->code,
            'address' => $request->address,
            'has_monthly_fee' => (bool) $request->has_monthly_fee,
            'monthly_fee' => $request->monthly_fee ?: 0,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'ورودی با موفقیت ویرایش شد.',
            'building' => $building
        ], 200);
    }

    public function destroyBuilding($id)
    {
        Building::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }

    public function storeContact(Request $request, $buildingId)
    {
        $request->validate([
            'name' => 'required|string',
            'role_title' => 'required|string',
            'phone' => 'required|string',
        ]);

        $contact = Contact::create([
            'building_id' => $buildingId,
            'name' => $request->name,
            'role_title' => $request->role_title,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'مشخصات مدیر با موفقیت ثبت شد.',
            'contact' => $contact
        ], 200);
    }

    public function destroyContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return response()->json(['status' => 'success', 'message' => 'مخاطب با موفقیت حذف شد.'], 200);
    }

    public function storeTechnician(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:technician,admin,internal_admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'national_code' => $request->national_code,
            'secondary_phone' => $request->secondary_phone,
            'birth_date' => $request->birth_date,
            'base_salary' => $request->base_salary ?: 0,
            'password' => bcrypt($request->password),
            'role' => $request->input('role', 'technician'),
            'is_active' => true,
        ]);

        if ($request->has('location_ids') && is_array($request->location_ids)) {
            $user->locations()->sync($request->location_ids);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'پرسنل جدید با موفقیت ثبت شد.',
            'user' => $user->load('locations')
        ], 201);
    }

    public function updateTechnician(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'role' => 'nullable|string|in:technician,admin,internal_admin',
        ]);

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'national_code' => $request->national_code,
            'birth_date' => $request->birth_date,
            'base_salary' => $request->input('base_salary', $user->base_salary),
        ];

        if ($request->filled('role') && $user->role !== 'super_admin') {
            $updateData['role'] = $request->role;
        }

        $user->update($updateData);

        if ($request->has('location_ids')) {
            $user->locations()->sync($request->location_ids);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'پرونده پرسنل و دسترسی بلوک‌ها با موفقیت ویرایش شد.',
            'user' => $user->load('locations')
        ], 200);
    }

    public function toggleTechnicianStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['status' => 'success'], 200);
    }

    public function assignTask(Request $request)
    {
        $amount = (float) ($request->amount ?? 0);
        $shareType = $request->technician_share_type ?? 'none';
        $shareValue = (float) ($request->technician_share_value ?? 0);
        $shareAmount = 0;

        if ($shareType === 'percentage' && $shareValue > 0) {
            $shareAmount = round(($amount * $shareValue) / 100);
        } elseif ($shareType === 'fixed') {
            $shareAmount = $shareValue;
        }

        $breakdown = Breakdown::create([
            'building_id' => $request->building_id,
            'user_id' => $request->user_id,
            'title' => $request->title,
            'building_unit' => $request->building_unit,
            'is_billable' => $amount > 0,
            'amount' => $amount,
            'technician_share_type' => $shareType,
            'technician_share_value' => $shareValue,
            'technician_share_amount' => $shareAmount,
            'approval_status' => 'pending',
            'admin_note' => 'ارجاع مستقیم از داشبورد ادمین',
        ]);

        return response()->json(['status' => 'success', 'breakdown' => $breakdown], 200);
    }

    public function storeTechnicianPayout(Request $request, $id)
    {
        $tech = User::findOrFail($id);
        $payout = $tech->payouts()->create([
            'amount' => $request->amount,
            'paid_at' => function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d'),
            'tracking_code' => $request->tracking_code,
            'payment_type' => 'bank_transfer',
            'description' => $request->description,
        ]);
        return response()->json(['status' => 'success', 'payout' => $payout], 200);
    }

public function updateAndApprove(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $breakdown = Breakdown::findOrFail($id);

        // ۱. بررسی قطعات ارسالی جدید از فرانت
        $partsInput = $request->input('parts', []);
        $totalCalculatedPartsPrice = 0;
        $primaryPartName = '';
        $primaryPartCount = 1;

        if (!empty($partsInput) && is_array($partsInput)) {
            // حذف رکوردهای قبلی همین خرابی در صورت وجود
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

            // برای سازگاری کامل با فیلدهای قدیمی و گزارش‌های قبلی
            $primaryPartName = implode(' ، ', $partNamesList);
            $primaryPartCount = count($partNamesList);
        } else {
            // حالت سازگاری با متد قدیمی تک‌قطعه‌ای
            $primaryPartName = $request->filled('part_name') ? $request->input('part_name') : $breakdown->part_name;
            $primaryPartCount = $request->filled('part_count') ? (int) $request->input('part_count') : ($breakdown->part_count ?? 1);
            $totalCalculatedPartsPrice = (float) $request->input('part_price', $breakdown->part_price ?? 0);
        }

        // ۲. مبلغ نهایی کار (اگر مبلغ دستی ارسال نشده باشد، جمع قطعات قرار می‌گیرد)
        $amount = $request->filled('amount') 
            ? (float) $request->input('amount') 
            : $totalCalculatedPartsPrice;

        // ۳. محاسبه سهم تکنسین
        $shareType = $request->input('technician_share_type', $breakdown->technician_share_type ?? 'none');
        $shareValue = (float) ($request->input('technician_share_value', $breakdown->technician_share_value ?? 0));
        $shareAmount = 0;

        if ($shareType === 'percentage' && $shareValue > 0) {
            $shareAmount = round(($amount * $shareValue) / 100);
        } elseif ($shareType === 'fixed') {
            $shareAmount = $shareValue;
        }

        // ۴. ذخیره در جدول breakdowns
        $breakdown->update([
            'title'                  => $request->input('title', $breakdown->title),
            'building_unit'          => $request->input('building_unit', $breakdown->building_unit),
            'technician_note'        => $request->input('technician_note', $breakdown->technician_note),
            'amount'                 => $amount,
            'part_name'              => $primaryPartName,
            'part_count'             => $primaryPartCount,
            'part_price'             => $totalCalculatedPartsPrice,
            'technician_share_type'  => $shareType,
            'technician_share_value' => $shareValue,
            'technician_share_amount'=> $shareAmount,
            'admin_note'             => $request->input('admin_note', $breakdown->admin_note),
            'is_billable'            => $amount > 0 || $request->boolean('is_billable'),
            'status'                 => 'completed',
            'approval_status'        => 'approved',
        ]);

        DB::commit();

        return response()->json([
            'status'    => 'success',
            'message'   => 'تسک با لیست قطعات تایید و به بایگانی منتقل شد.',
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

    public function destroyTask($id)
    {
        Breakdown::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'کار با موفقیت حذف شد.'], 200);
    }

    public function approve(Request $request, Breakdown $breakdown)
    {
        $breakdown->update([
            'approval_status' => 'approved',
            'admin_note' => $request->input('admin_note', 'تایید شده توسط مدیریت')
        ]);
        return response()->json(['status' => 'success', 'message' => 'تسک با موفقیت تایید شد.', 'breakdown' => $breakdown], 200);
    }

    public function rejectCost(Request $request, Breakdown $breakdown)
    {
        $breakdown->update([
            'is_billable' => false,
            'amount' => 0,
            'technician_share_amount' => 0,
            'approval_status' => 'approved',
            'admin_note' => 'هزینه رد شد - سرویس عادی بدون هزینه',
        ]);
        return response()->json(['status' => 'success', 'message' => 'هزینه تسک رد شد و به عنوان کار بدون هزینه تایید گردید.', 'breakdown' => $breakdown], 200);
    }

    public function storeCompany(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $data = $request->only(['name', 'brand', 'reg_number', 'national_id', 'phone', 'logo_icon']);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Company::where('is_default', true)->update(['is_default' => false]);
        }

        $company = Company::create($data);
        return response()->json(['status' => 'success', 'message' => 'سربرگ شرکت با موفقیت ثبت شد.', 'company' => $company], 201);
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255']);
        $data = $request->only(['name', 'brand', 'reg_number', 'national_id', 'phone', 'logo_icon']);

        if ($request->has('is_default')) {
            $data['is_default'] = $request->boolean('is_default');
            if ($data['is_default']) {
                Company::where('id', '!=', $company->id)->where('is_default', true)->update(['is_default' => false]);
            }
        }

        $company->update($data);
        return response()->json(['status' => 'success', 'message' => 'سربرگ شرکت با موفقیت ویرایش شد.', 'company' => $company], 200);
    }

    public function destroyCompany($id)
    {
        Company::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'سربرگ شرکت حذف شد.'], 200);
    }

    public function markAsRead($id)
    {
        Message::findOrFail($id)->update(['is_read' => true]);
        return response()->json(['status' => 'success']);
    }

    public function getAccountingLedger()
    {
        $buildings = Building::with([
            'location',
            'breakdowns' => function ($q) {
                $q->where('amount', '>', 0);
            },
            'payments'
        ])->get()->map(function ($b) {
            $totalCost = $b->breakdowns->sum('amount');
            $totalPaid = $b->payments ? $b->payments->sum('amount') : 0;
            $balance = $totalPaid - $totalCost;

            return [
                'id' => $b->id,
                'title' => $b->title,
                'code' => $b->code,
                'location_title' => $b->location ? $b->location->title : 'بلوک',
                'total_cost' => (int) $totalCost,
                'total_paid' => (int) $totalPaid,
                'balance' => (int) $balance,
                'payments' => $b->payments ?? [],
                'breakdowns' => $b->breakdowns->map(function ($brk) {
                    return [
                        'id' => $brk->id,
                        'title' => $brk->title,
                        'part_name' => $brk->part_name ?: '-',
                        'amount' => (int) $brk->amount,
                        'created_at' => $brk->created_at ? $brk->created_at->format('Y/m/d') : '---'
                    ];
                })
            ];
        });

        return response()->json(['buildings' => $buildings]);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $payment = \App\Models\Payment::create([
            'building_id' => $request->building_id,
            'amount' => $request->amount,
            'paid_at' => $request->paid_at ?? now(),
            'tracking_code' => $request->tracking_code,
            'description' => $request->description,
            'created_by' => auth()->id() ?? 1
        ]);

        return response()->json(['status' => 'success', 'payment' => $payment]);
    }

    // ==================== متدهای استاندارد پروژه‌ها ====================

    public function getProjects(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user('sanctum')?->id ?: auth()->id());
            $user = $userId ? User::with('locations.buildings')->find($userId) : null;

            $role = $user ? strtolower(trim($user->role ?? '')) : 'technician';
            $isSuperAdmin = ($role === 'super_admin' || (!$user && !$request->filled('user_id')));

            $allowedLocationIds = ($user && $user->locations) ? $user->locations->pluck('id')->toArray() : [];
            $allowedBuildingIds = !empty($allowedLocationIds)
                ? Building::whereIn('location_id', $allowedLocationIds)->pluck('id')->toArray()
                : [];

            $uid = (int) ($user?->id ?? 0);
            $userName = trim($user?->name ?? '');

            // دریافت تمام پروژه‌ها و فیلتر مطمئن در سطح کالکشن لاراول جهت عبور از دابل‌انکودینگ
            $projects = Project::orderBy('id', 'desc')->get()->filter(function ($proj) use ($isSuperAdmin, $allowedBuildingIds, $uid, $userName) {
                if ($isSuperAdmin)
                    return true;

                // ۱. بررسی اتصال به ساختمان‌های مجاز
                if (!empty($proj->building_id) && in_array($proj->building_id, $allowedBuildingIds)) {
                    return true;
                }

                // ۲. استخراج آرایه اعضای تیم با حل کامل چندلایه بودن جیسون
                $team = $proj->team_members;
                for ($i = 0; $i < 3; $i++) {
                    if (is_string($team)) {
                        $decoded = json_decode($team, true);
                        if ($decoded !== null)
                            $team = $decoded;
                    }
                }

                if (is_array($team)) {
                    foreach ($team as $member) {
                        if (is_array($member)) {
                            if (isset($member['id']) && (int) $member['id'] === $uid)
                                return true;
                            if (isset($member['name']) && $userName && mb_strpos($member['name'], $userName) !== false)
                                return true;
                        } elseif (is_string($member) && $userName && mb_strpos($member, $userName) !== false) {
                            return true;
                        }
                    }
                }

                // ۳. بررسی متنی خام ستون تیم برای پروژه‌های ذخیره شده با متد قدیمی
                $rawTeam = is_string($proj->team_members) ? $proj->team_members : json_encode($proj->team_members, JSON_UNESCAPED_UNICODE);
                if (strpos($rawTeam, '"id":' . $uid) !== false || strpos($rawTeam, '"id":"' . $uid . '"') !== false) {
                    return true;
                }

                return false;
            })->values()->map(function ($proj) use ($role) {
                $steps = $proj->steps;
                for ($i = 0; $i < 3; $i++) {
                    if (is_string($steps)) {
                        $decoded = json_decode($steps, true);
                        if ($decoded !== null)
                            $steps = $decoded;
                    }
                }

                $team = $proj->team_members;
                for ($i = 0; $i < 3; $i++) {
                    if (is_string($team)) {
                        $decoded = json_decode($team, true);
                        if ($decoded !== null)
                            $team = $decoded;
                    }
                }

                $data = $proj->toArray();
                $data['steps'] = is_array($steps) ? $steps : [];
                $data['team_members'] = is_array($team) ? $team : [];

                if ($role !== 'super_admin') {
                    unset($data['contract_amount']);
                }
                return $data;
            });

            return response()->json([
                'status' => 'success',
                'projects' => $projects
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeProject(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user('sanctum')?->id ?: auth()->id());
            $user = $userId ? User::find($userId) : null;
            $role = $user ? strtolower(trim($user->role ?? '')) : '';

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'building_id' => 'nullable',
                'location_text' => 'nullable|string|max:255',
                'steps' => 'required|array',
                'team_members' => 'nullable|array',
                'contract_amount' => 'nullable|numeric',
                'start_date' => 'nullable|string',
                'end_date' => 'nullable|string'
            ]);

            $amount = ($role === 'super_admin') ? ($validated['contract_amount'] ?? 0) : 0;
            $bldId = !empty($validated['building_id']) ? (int) $validated['building_id'] : null;

            // مدل خودش casts دارد، مستقیم آرایه پاس داده می‌شود بدون json_encode دستی
            $project = Project::create([
                'title' => $validated['title'],
                'building_id' => $bldId,
                'location_text' => $validated['location_text'] ?? '',
                'steps' => $validated['steps'],
                'team_members' => $validated['team_members'] ?? [],
                'contract_amount' => $amount,
                'status' => 'in_progress',
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'پروژه با موفقیت ایجاد شد',
                'project' => $project
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProjectProgress(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $userId = $request->input('user_id') ?: (auth()->id() ?: null);
            $user = $userId ? User::find($userId) : null;
            $role = $user ? strtolower(trim($user->role ?? '')) : 'technician';

            if ($request->has('steps')) {
                $project->steps = $request->steps;
            }

            if ($request->has('final_report') && in_array($role, ['super_admin', 'admin', 'internal_admin'])) {
                $project->final_report = $request->final_report;
            }

            if ($request->has('contract_amount') && $role === 'super_admin') {
                $project->contract_amount = $request->contract_amount;
            }

            if ($request->has('status')) {
                $project->status = $request->status;
            }

            $project->save();

            return response()->json(['status' => 'success', 'message' => 'پروژه به‌روزرسانی شد', 'project' => $project], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== مرکز پیام و گزارش شیفت ====================

    public function getMessages(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? User::find($userId) : null;

            $query = Message::with(['sender', 'recipient', 'parent.sender'])->latest();

            if ($user) {
                $role = strtolower(trim($user->role ?? ''));
                if ($role !== 'super_admin') {
                    $query->where(function ($q) use ($user) {
                        $q->whereNull('recipient_id')
                            ->orWhere('recipient_id', $user->id)
                            ->orWhere('sender_id', $user->id);
                    });
                }
            }

            return response()->json(['status' => 'success', 'messages' => $query->get()], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $senderId = $request->input('sender_id') ?: ($userId ?: 1);

            $recipientId = $request->input('recipient_id');
            if ($recipientId === 'all' || empty($recipientId)) {
                $recipientId = null;
            }

            $msg = Message::create([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'parent_id' => $request->input('parent_id') ?: null,
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'is_urgent' => $request->boolean('is_urgent'),
                'is_read' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'پیام با موفقیت ارسال شد.',
                'data' => $msg->load(['sender', 'recipient'])
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function getShiftReports(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? User::find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));

            $query = ShiftReport::with('user')->latest();

            if ($role === 'technician') {
                $query->where('user_id', $user->id);
            } elseif ($role === 'super_admin') {
                if ($request->filled('filter_technician_id')) {
                    $query->where('user_id', $request->filter_technician_id);
                }
                if ($request->filled('filter_date')) {
                    $query->where('shift_date', 'like', '%' . $request->filter_date . '%');
                }
            }

            return response()->json(['status' => 'success', 'reports' => $query->get()], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeShiftReport(Request $request)
    {
        $request->validate([
            'summary' => 'required|string',
            'shift_date' => 'nullable|string',
            'covered_buildings' => 'nullable|string',
        ]);

        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());

            $shiftDate = $request->input('shift_date');
            if (empty($shiftDate)) {
                $shiftDate = function_exists('jdate') ? jdate()->format('Y/m/d') : date('Y/m/d');
            }

            $report = ShiftReport::create([
                'user_id' => $userId,
                'shift_date' => $shiftDate,
                'shift_type' => $request->input('shift_type', 'شیفت صبح'),
                'workshop_status' => $request->input('workshop_status', 'all_normal'),
                'covered_buildings' => $request->input('covered_buildings'),
                'summary' => $request->input('summary'),
                'pending_issues' => $request->input('pending_issues'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'گزارش شیفت با موفقیت ثبت شد.',
                'report' => $report->load('user')
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateShiftReportFeedback(Request $request, $id)
    {
        try {
            $report = ShiftReport::findOrFail($id);
            $report->update(['manager_feedback' => $request->input('manager_feedback')]);
            return response()->json(['status' => 'success', 'message' => 'بازخورد مدیریت با موفقیت ثبت شد.', 'report' => $report], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    // تغییر وضعیت پیام به خوانده‌شده
    public function markMessageAsRead($id)
    {
        try {
            $msg = \App\Models\Message::findOrFail($id);
            $msg->update(['is_read' => true]);
            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    // واکشی خرابی‌های بایگانی‌شده و مختومه
    // واکشی خرابی‌های بایگانی‌شده و مختومه
    public function getArchiveBreakdowns(Request $request)
    {
        try {
            $breakdowns = Breakdown::where('approval_status', 'approved')
                ->with(['building.location', 'user', 'parts'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $locations = Location::all();

            return response()->json([
                'status'     => 'success',
                'breakdowns' => $breakdowns,
                'locations'  => $locations,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function archive(Request $request)
    {
        try {
            // دریافت ایمن شناسه کاربر بدون متد سانکتوم
            $userId = $request->input('user_id') ?: (auth()->id() ?: null);
            $user = $userId ? User::with('locations')->find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));
            $isSuperAdmin = ($role === 'super_admin' || (!$user && !$request->filled('user_id')));

            $query = Breakdown::where('approval_status', 'approved')
                ->with(['building.location', 'user', 'parts'])
                ->orderBy('created_at', 'desc');

            // دسترسی‌های کاربر به بلوک‌ها
            if (!$isSuperAdmin && $user) {
                $allowedLocationIds = $user->locations ? $user->locations->pluck('id')->toArray() : [];
                $allowedBuildingIds = Building::whereIn('location_id', $allowedLocationIds)->pluck('id')->toArray();
                $query->whereIn('building_id', $allowedBuildingIds);
                $locations = Location::whereIn('id', $allowedLocationIds)->get();
            } else {
                $locations = Location::all();
            }

            // فیلتر بلوک
            if ($request->filled('location_id')) {
                $bldIds = Building::where('location_id', $request->location_id)->pluck('id')->toArray();
                $query->whereIn('building_id', $bldIds);
            }

            // فیلتر بازه تاریخ
            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
            }
            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
            }

            // جستجوی متنی
            if ($request->filled('search')) {
                $s = trim($request->search);
                $query->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                      ->orWhere('technician_note', 'like', "%{$s}%");
                });
            }

            // فیلتر مالی
            if ($request->filled('billable')) {
                if ($request->billable === 'billable') {
                    $query->where('is_billable', true)->where('amount', '>', 0);
                } elseif ($request->billable === 'free') {
                    $query->where(function ($q) {
                        $q->where('is_billable', false)->orWhere('amount', 0);
                    });
                }
            }

            // فیلتر قطعه تعویضی
            if ($request->filled('has_part')) {
    if ($request->has_part === 'yes') {
        $query->where(function ($q) {
            $q->has('parts')
              ->orWhere(function ($sub) {
                  $sub->whereNotNull('part_name')->where('part_name', '!=', '');
              });
        });
    } elseif ($request->has_part === 'no') {
        $query->doesntHave('parts')->where(function ($q) {
            $q->whereNull('part_name')->orWhere('part_name', '');
        });
    }
}

            // صفحه‌بندی یا خروجی کل جهت پرینت
            $perPage = (int) $request->input('per_page', 25);
            if ($request->boolean('all')) {
                $breakdowns = $query->get();
            } else {
                $breakdowns = $query->paginate($perPage);
            }

            return response()->json([
                'status'     => 'success',
                'breakdowns' => $breakdowns,
                'locations'  => $locations,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
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
    // تایید گروهی یا تکی سرویس‌های ماهانه توسط مدیریت (ارشد / میانی)
    public function approveMonthlyService(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? User::find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));

            if (!in_array($role, ['super_admin', 'admin'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'شما دسترسی تایید سرویس را ندارید.'
                ], 403);
            }

            $buildingIds = $request->input('building_ids');
            if (empty($buildingIds)) {
                $singleId = $request->input('building_id');
                $buildingIds = $singleId ? [$singleId] : [];
            }

            if (empty($buildingIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'شناسه ورودی ارسال نشده است.'
                ], 422);
            }

            $serviceMonth = $request->input('service_month') ?: (function_exists('jdate') ? jdate()->format('Y/m') : date('Y-m'));
            $nowJalali = function_exists('jdate') ? jdate()->format('Y/m/d H:i') : date('Y/m/d H:i');

            \Illuminate\Support\Facades\DB::table('monthly_services')
                ->whereIn('building_id', $buildingIds)
                ->where('service_month', $serviceMonth)
                ->where('is_completed', 1)
                ->update([
                    'is_approved' => 1,
                    'approved_by' => $userId,
                    'approved_at' => $nowJalali,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'سرویس‌های انجام‌شده با موفقیت تایید و قفل شدند.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

  // واکشی سرویس‌های ماهانه جهت نمایش در صفحه بایگانی
    public function getArchiveServices(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? User::with('locations')->find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));
            $isSuperAdmin = ($role === 'super_admin' || !$user);

            $query = \Illuminate\Support\Facades\DB::table('monthly_services')
                ->join('buildings', 'monthly_services.building_id', '=', 'buildings.id')
                ->leftJoin('locations', 'buildings.location_id', '=', 'locations.id')
                ->leftJoin('users as tech', 'monthly_services.user_id', '=', 'tech.id')
                ->leftJoin('users as approver', 'monthly_services.approved_by', '=', 'approver.id')
                ->select([
                    'monthly_services.*',
                    'buildings.title as building_title',
                    'buildings.code as building_code',
                    'locations.id as location_id',
                    'locations.title as location_title',
                    'tech.name as technician_name',
                    'approver.name as approver_name'
                ])
                ->where('monthly_services.is_completed', 1)
                ->orderBy('monthly_services.service_month', 'desc')
                ->orderBy('monthly_services.updated_at', 'desc');

            if (!$isSuperAdmin && $user) {
                $allowedLocationIds = $user->locations ? $user->locations->pluck('id')->toArray() : [];
                $query->whereIn('locations.id', $allowedLocationIds);
                $locations = Location::whereIn('id', $allowedLocationIds)->get();
            } else {
                $locations = Location::all();
            }

            if ($request->filled('service_month')) {
                $query->where('monthly_services.service_month', $request->service_month);
            }

            $services = $query->get();

            $availableMonths = \Illuminate\Support\Facades\DB::table('monthly_services')
                ->distinct()
                ->pluck('service_month')
                ->filter()
                ->values();

            return response()->json([
                'status' => 'success',
                'services' => $services,
                'locations' => $locations,
                'available_months' => $availableMonths,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
/**
     * دریافت اطلاعات ماه فعال کاری و وضعیت پیشرفت سرویس‌ها
     */
    public function getServicePeriodStatus(Request $request)
    {
        try {
            // پیدا کردن آخرین ماهی که در دیتابیس تعریف شده است
            $latestService = DB::table('monthly_services')
                ->orderBy('id', 'desc')
                ->first();

            $activeMonth = $latestService ? $latestService->service_month : (function_exists('jdate') ? jdate()->format('Y/m') : date('Y/m'));

            if ($request->filled('month')) {
                $activeMonth = $request->input('month');
            }

            $totalBuildings = Building::count();
            $completedCount = DB::table('monthly_services')
                ->where('service_month', $activeMonth)
                ->where('is_completed', 1)
                ->count();

            $approvedCount = DB::table('monthly_services')
                ->where('service_month', $activeMonth)
                ->where('is_approved', 1)
                ->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'active_month'      => $activeMonth,
                    'total_buildings'   => $totalBuildings,
                    'completed_count'   => $completedCount,
                    'pending_count'     => max(0, $totalBuildings - $completedCount),
                    'approved_count'    => $approvedCount,
                    'can_close_period'  => true
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * بستن دستی دوره ماه قبل و باز کردن ماه جدید (بدون بستن اتوماتیک)
     */
    public function closeServicePeriod(Request $request)
    {
        try {
            $userId = $request->input('user_id') ?: ($request->user()?->id ?: auth()->id());
            $user = $userId ? \App\Models\User::find($userId) : null;
            $role = strtolower(trim($user->role ?? ''));

            if (!in_array($role, ['super_admin', 'admin']) && $user) {
                return response()->json(['status' => 'error', 'message' => 'شما دسترسی بستن دوره را ندارید.'], 403);
            }

            $currentMonth = $request->input('current_month');
            // محاسبه خودکار ماه بعد در صورت عدم ارسال دستی
            $nextMonth    = $request->input('next_month') ?: $this->calculateNextJalaliMonth($currentMonth);

            if (empty($currentMonth) || empty($nextMonth)) {
                return response()->json(['status' => 'error', 'message' => 'مشخص کردن ماه جاری الزامی است.'], 422);
            }

            $nowFormatted = function_exists('jdate') ? jdate()->format('Y/m/d H:i') : date('Y/m/d H:i');

            DB::beginTransaction();

            // ۱. قفل و تایید تمامی سرویس‌های تکمیل‌شده ماه قبل
            DB::table('monthly_services')
                ->where('service_month', $currentMonth)
                ->where('is_completed', 1)
                ->update([
                    'is_approved' => 1,
                    'approved_by' => $userId,
                    'approved_at' => $nowFormatted,
                    'updated_at'  => now()
                ]);

            // ۲. ایجاد رکوردهای خام ماه جدید برای تمامی ورودی‌های فعال
            $allBuildings = Building::pluck('id');
            $existingNextMonth = DB::table('monthly_services')
                ->where('service_month', $nextMonth)
                ->pluck('building_id')
                ->toArray();

            $inserts = [];
            foreach ($allBuildings as $bldId) {
                if (!in_array($bldId, $existingNextMonth)) {
                    $inserts[] = [
                        'building_id'     => $bldId,
                        'user_id'         => null,
                        'service_month'   => $nextMonth,
                        'serviced_at'     => null,
                        'is_completed'    => 0,
                        'is_approved'     => 0,
                        'technician_note' => '',
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if (!empty($inserts)) {
                DB::table('monthly_services')->insert($inserts);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => "دوره {$currentMonth} با موفقیت بسته شد و سرویس‌های دوره {$nextMonth} ایجاد گردید."
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * محاسبه خودکار ماه بعد بر اساس تقویم شمسی
     */
    private function calculateNextJalaliMonth($currentMonth)
    {
        $parts = explode('/', str_replace('-', '/', trim($currentMonth)));
        if (count($parts) < 2) {
            return $currentMonth;
        }

        $year = (int)$parts[0];
        $month = (int)$parts[1];

        if ($month >= 12) {
            $year += 1;
            $month = 1;
        } else {
            $month += 1;
        }

        return sprintf("%04d/%02d", $year, $month);
    }

public function restoreBreakdown($id)
    {
        try {
            $breakdown = \App\Models\Breakdown::findOrFail($id);

            // بازگردانی وضعیت‌ها به حالت فعال کارتابل
            $breakdown->update([
                'status' => 'in_progress', 
                'approval_status' => 'pending' 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'پرونده با موفقیت از بایگانی خارج و به کارتابل برگشت.',
                'data' => $breakdown
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}