<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صورت وضعیت مالی و معین | service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</head>

<body class="bg-slate-950 text-slate-100 min-h-screen pb-12" x-data="{ 
    showPayModal: false, 
    selectedBuildingForPay: '',
    detailModal: false,
    currentBuilding: null
}">

    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-3 py-1.5 rounded-lg">⬅ بازگشت به داشبورد</a>
            <span class="text-sm font-bold text-slate-200">دفتر معین و حسابداری فنی</span>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6">

        @if (session('success'))
        <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 text-xs rounded-xl p-3 text-center">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-sm font-bold text-slate-200">وضعیت حسابداری بلوک‌ها و ورودی‌ها</h1>
                <p class="text-xs text-slate-500 mt-0.5">تفکیک مطالبات، اجرت‌ها و فیش‌های ثبت‌شده</p>
            </div>
            <button @click="showPayModal = true; selectedBuildingForPay = ''" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-lg transition">
                💳 ثبت واریزی جدید
            </button>
        </div>

        <!-- فرم بازشونده ثبت واریزی -->
        <div x-show="showPayModal" x-cloak class="bg-slate-900 border border-emerald-500/40 rounded-2xl p-5 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                <h2 class="text-xs font-bold text-emerald-400">ثبت فیش دریافتی و واریز وجه کارفرما</h2>
                <button @click="showPayModal = false" class="text-slate-400 hover:text-white text-xs">✕ بستن</button>
            </div>
            <form action="{{ route('admin.payment.store') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">طرف‌حساب (بلوک و ورودی):</label>
                        <select name="building_id" x-model="selectedBuildingForPay" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200">
                            <option value="">-- انتخاب ورودی --</option>
                            @foreach ($buildings as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->location->title ?? 'بلوک نامشخص' }} - {{ $b->title }} {{ $b->code ? "($b->code)" : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">مبلغ دریافتی (تومان):</label>
                        <input type="number" name="amount" required placeholder="مثال: 1500000" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold font-mono">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">تاریخ واریز:</label>
                        <input type="date" name="paid_at" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="tracking_code" placeholder="شماره پیگیری فیش بانکی (اختیاری)" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs">
                    <input type="text" name="description" placeholder="توضیحات (مثلاً: شارژ سرویس تیرماه یا تسویه فاکتور قطعه)" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs">
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 rounded-xl text-xs transition">ثبت در دفتر معین</button>
            </form>
        </div>

        <!-- جدول مانده حساب‌ها -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="p-3.5">موقعیت (بلوک / ورودی)</th>
                        <th class="p-3.5">مجموع هزینه‌ها (قطعه و اجرت)</th>
                        <th class="p-3.5">مجموع واریزی‌ها</th>
                        <th class="p-3.5">مانده‌حساب نهایی</th>
                        <th class="p-3.5 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($buildings as $b)
                    @php
                    $approvedBreakdowns = $b->breakdowns->where('approval_status', 'approved');
                    $cost = $approvedBreakdowns->sum('amount');
                    $paid = $b->payments->sum('amount');
                    $diff = $paid - $cost;

                    // آماده‌سازی داده برای مودال ریز گردش
                    $modalData = [
                    'id' => $b->id,
                    'full_title' => ($b->location->title ?? 'بلوک نامشخص') . ' - ' . $b->title,
                    'cost' => number_format($cost),
                    'paid' => number_format($paid),
                    'diff' => number_format(abs($diff)),
                    'diff_raw' => $diff,
                    'breakdowns' => $approvedBreakdowns->map(function($br) {
                    return [
                    'title' => $br->title,
                    'part_name' => $br->part_name ?: '-',
                    'amount' => number_format($br->amount),
                    'date' => function_exists('jdate') ? jdate($br->created_at)->format('Y/m/d') : $br->created_at->format('Y/m/d'),
                    ];
                    }),
                    'payments' => $b->payments->map(function($p) {
                    return [
                    'amount' => number_format($p->amount),
                    'tracking_code' => $p->tracking_code ?: '-',
                    'description' => $p->description ?: '-',
                    'date' => $p->paid_at,
                    ];
                    })
                    ];
                    @endphp
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="p-3.5">
                            <span class="font-bold text-amber-300">{{ $b->location->title ?? 'محدوده عمومی' }}</span>
                            <span class="text-slate-400 mx-1">/</span>
                            <span class="text-slate-200">{{ $b->title }}</span>
                            @if($b->code)
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded mr-1 font-mono">{{ $b->code }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-rose-400 font-mono font-bold">{{ number_format($cost) }} تومان</td>
                        <td class="p-3.5 text-emerald-400 font-mono font-bold">{{ number_format($paid) }} تومان</td>
                        <td class="p-3.5">
                            @if($diff < 0)
                                <span class="bg-rose-500/20 text-rose-300 border border-rose-500/30 px-2.5 py-1 rounded-lg font-bold">
                                {{ number_format(abs($diff)) }} تومان بدهکار
                                </span>
                                @elseif($diff > 0)
                                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2.5 py-1 rounded-lg font-bold">
                                    {{ number_format($diff) }} تومان بستانکار
                                </span>
                                @else
                                <span class="bg-slate-800 text-slate-400 px-2.5 py-1 rounded-lg font-medium">تسویه کامل</span>
                                @endif
                        </td>
                        <td class="p-3.5 text-center flex items-center justify-center gap-2">
                            <button
                                @click="currentBuilding = {{ json_encode($modalData) }}; detailModal = true"
                                class="bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white px-2.5 py-1 rounded-lg transition text-[11px]">
                                🔍 ریز گردش
                            </button>
                            <!-- کد جدید متصل به فاکتور A5 با سربرگ داینامیک: -->
                            <a href="{{ route('admin.building.invoice.a5', $b->id) }}" target="_blank" class="bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white border border-amber-500/30 px-2.5 py-1 rounded-lg transition text-[11px] font-medium" title="صدور فاکتور رسمی A5 با سربرگ شرکت">
                                📄 فاکتور A5
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-500">هیچ ساختمانی برای محاسبه مالی وجود ندارد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- مودال نمایش ریز گردش و دفتر معین اختصاصی ورودی -->
        <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="detailModal = false" class="bg-slate-900 border border-slate-700 w-full max-w-4xl rounded-2xl overflow-hidden max-h-[90vh] flex flex-col shadow-2xl">

                <!-- هدر مودال -->
                <div class="bg-slate-950 p-4 border-b border-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-bold text-amber-300" x-text="currentBuilding?.full_title"></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">صورت ریز تراکنش‌ها و تاریخچه تسویه</p>
                    </div>
                    <button @click="detailModal = false" class="text-slate-400 hover:text-white text-sm bg-slate-800 px-2.5 py-1 rounded-lg">✕</button>
                </div>

                <!-- بدنه اسکرول‌خور -->
                <div class="p-4 overflow-y-auto space-y-5 text-xs">

                    <!-- کارت خلاصه وضعیت بالا -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-rose-500/20">
                            <span class="text-slate-400 text-[11px] block">کل هزینه‌ها / مطالبات:</span>
                            <span class="text-rose-400 font-bold font-mono text-sm" x-text="currentBuilding?.cost + ' تومان'"></span>
                        </div>
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-emerald-500/20">
                            <span class="text-slate-400 text-[11px] block">کل دریافتی / واریزی:</span>
                            <span class="text-emerald-400 font-bold font-mono text-sm" x-text="currentBuilding?.paid + ' تومان'"></span>
                        </div>
                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[11px] block">مانده‌حساب فعلی:</span>
                            <span class="font-bold font-mono text-sm"
                                :class="currentBuilding?.diff_raw < 0 ? 'text-rose-400' : (currentBuilding?.diff_raw > 0 ? 'text-emerald-400' : 'text-slate-400')"
                                x-text="currentBuilding?.diff_raw < 0 ? currentBuilding?.diff + ' تومان (بدهکار)' : (currentBuilding?.diff_raw > 0 ? currentBuilding?.diff + ' تومان (بستانکار)' : 'تسویه کامل')">
                            </span>
                        </div>
                    </div>

                    <!-- بخش ۱: هزینه‌ها و کارهای ثبت‌شده -->
                    <div>
                        <h4 class="font-bold text-slate-300 mb-2 border-r-2 border-rose-500 pr-2">فاکتورها، خدمات و قطعات مصرفی</h4>
                        <div class="bg-slate-950 rounded-xl border border-slate-800 overflow-hidden">
                            <table class="w-full text-right">
                                <thead class="bg-slate-900/60 text-slate-400 text-[11px]">
                                    <tr>
                                        <th class="p-2.5">تاریخ</th>
                                        <th class="p-2.5">شرح عملیات / سرویس</th>
                                        <th class="p-2.5">قطعه تأمینی</th>
                                        <th class="p-2.5">مبلغ بدهی</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900">
                                    <template x-for="item in currentBuilding?.breakdowns" :key="item.title + item.date">
                                        <tr>
                                            <td class="p-2.5 text-slate-400 font-mono text-[11px]" x-text="item.date"></td>
                                            <td class="p-2.5 font-medium text-slate-200" x-text="item.title"></td>
                                            <td class="p-2.5 text-slate-400" x-text="item.part_name"></td>
                                            <td class="p-2.5 text-rose-400 font-mono font-bold" x-text="item.amount + ' تومان'"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!currentBuilding?.breakdowns || currentBuilding?.breakdowns.length === 0">
                                        <tr>
                                            <td colspan="4" class="p-3 text-center text-slate-500">هیچ رکوردی ثبت نشده است.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- بخش ۲: پرداختی‌ها و فیش‌ها -->
                    <div>
                        <h4 class="font-bold text-slate-300 mb-2 border-r-2 border-emerald-500 pr-2">فیش‌های واریزی کارفرما</h4>
                        <div class="bg-slate-950 rounded-xl border border-slate-800 overflow-hidden">
                            <table class="w-full text-right">
                                <thead class="bg-slate-900/60 text-slate-400 text-[11px]">
                                    <tr>
                                        <th class="p-2.5">تاریخ واریز</th>
                                        <th class="p-2.5">مبلغ واریزی</th>
                                        <th class="p-2.5">شماره پیگیری</th>
                                        <th class="p-2.5">شرح / بابت</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900">
                                    <template x-for="pay in currentBuilding?.payments" :key="pay.date + pay.tracking_code">
                                        <tr>
                                            <td class="p-2.5 text-slate-400 font-mono text-[11px]" x-text="pay.date"></td>
                                            <td class="p-2.5 text-emerald-400 font-mono font-bold" x-text="pay.amount + ' تومان'"></td>
                                            <td class="p-2.5 text-slate-400 font-mono text-[11px]" x-text="pay.tracking_code"></td>
                                            <td class="p-2.5 text-slate-300" x-text="pay.description"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!currentBuilding?.payments || currentBuilding?.payments.length === 0">
                                        <tr>
                                            <td colspan="4" class="p-3 text-center text-slate-500">هیچ فیش واریزی ثبت نشده است.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- فوتر مودال -->
                <div class="bg-slate-950 p-3 border-t border-slate-800 flex justify-between items-center">
                    <button
                        @click="showPayModal = true; selectedBuildingForPay = currentBuilding.id; detailModal = false"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3 py-1.5 rounded-lg text-xs">
                        + ثبت فیش واریزی برای این ورودی
                    </button>
                    <button @click="detailModal = false" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-1.5 rounded-lg text-xs">بستن</button>
                </div>

            </div>
        </div>

    </main>
</body>

</html>