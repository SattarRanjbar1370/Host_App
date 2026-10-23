<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت پرسنل و تکنسین‌ها | service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 min-h-screen pb-16">

    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-3 py-1.5 rounded-lg transition">⬅ بازگشت به داشبورد</a>
            <span class="text-sm font-bold text-slate-200">پرونده پرسنل، کارتابل ارجاع و مالی</span>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white px-3 py-1.5 rounded-lg transition">خروج</button>
        </form>
    </header>

    <main class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6" x-data="{ showNewTech: false }">

        @if (session('success'))
        <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 text-xs rounded-xl p-3 text-center">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-rose-500/20 border border-rose-500 text-rose-300 text-xs rounded-xl p-3 text-right">
            <ul class="list-disc pr-4 space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-sm font-bold text-slate-200">لیست پرسنل و پرونده‌های فعال</h1>
                <p class="text-[11px] text-slate-400 mt-0.5">مشاهده سوابق، تماس مستقیم، ارجاع کارهای جدید و حسابرسی مبالغ دریافتی</p>
            </div>
            <button @click="showNewTech = !showNewTech" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2 rounded-xl text-xs transition shadow">
                ➕ تعریف پرسنل جدید
            </button>
        </div>

        <!-- فرم ثبت تکنسین جدید -->
        <div x-show="showNewTech" x-cloak class="bg-slate-900 border border-amber-500/40 rounded-2xl p-5 shadow-xl space-y-3">
            <h2 class="text-xs font-bold text-amber-400 border-b border-slate-800 pb-2">ثبت مشخصات تکنسین جدید</h2>
            <form action="{{ route('admin.technician.store') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">نام و نام خانوادگی:</label>
                        <input type="text" name="name" required placeholder="مثال: حسن رنجبر" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">شماره همراه اصلی (نام کاربری):</label>
                        <input type="text" name="phone" required placeholder="09xxxxxxxxx" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-left font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">رمز عبور ورود:</label>
                        <input type="text" name="password" required value="12345678" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-left font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">کد ملی:</label>
                        <input type="text" name="national_code" placeholder="۱۰ رقم" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-left font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">شماره همراه دوم:</label>
                        <input type="text" name="secondary_phone" placeholder="09xxxxxxxxx" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-left font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">تاریخ تولد:</label>
                        <input type="text" name="birth_date" placeholder="مثال: 1374/02/15" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-center focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">حقوق ثابت ماهانه (تومان):</label>
                        <input type="number" name="base_salary" placeholder="0" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2 rounded-xl text-xs transition">
                    ثبت نهایی پرونده پرسنل
                </button>
            </form>
        </div>

        <!-- لیست کارت‌های پرسنل -->
        <div class="space-y-6">
            @forelse ($technicians as $tech)
            @php
            // محاسبه دقیق مطالبات بر اساس پورسانت کارهای تاییدشده
            $commissionEarned = $tech->breakdowns->where('approval_status', 'approved')->sum('technician_share_amount');
            $totalPaid = $tech->payouts->sum('amount');
            
            // دسته‌بندی مبالغ واریزی
            $salaryPaid = $tech->payouts->where('payment_type', 'salary')->sum('amount');
            $commPaid = $tech->payouts->where('payment_type', 'commission')->sum('amount');
            $advancePaid = $tech->payouts->where('payment_type', 'advance')->sum('amount');

            // مانده حساب بر مبنای پورسانت‌ها (و حقوق در صورت نیاز)
            $balance = $commissionEarned - ($commPaid + $advancePaid);
            @endphp

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-4" x-data="{ activeTab: 'profile' }">

                <!-- هدر کارت -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-lg">
                            👷
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black text-slate-100">{{ $tech->name }}</h3>
                                @if($tech->is_active)
                                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-bold">● فعال</span>
                                @else
                                <span class="text-[10px] bg-rose-500/20 text-rose-400 px-2 py-0.5 rounded-full font-bold">● معلق</span>
                                @endif
                                @if($tech->base_salary > 0)
                                <span class="text-[10px] bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded font-bold">حقوق ثابت: {{ number_format($tech->base_salary) }} ت</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">ثبت‌شده از تاریخ: {{ jdate($tech->created_at)->format('Y/m/d') }}</p>
                        </div>
                    </div>

                    <!-- ناوبری تب‌ها -->
                    <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 text-xs">
                        <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition">👤 پرونده</button>
                        <button @click="activeTab = 'assign'" :class="activeTab === 'assign' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition">➕ سپردن کار</button>
                        <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition">📋 تاریخچه کارهـا</button>
                        <button @click="activeTab = 'finance'" :class="activeTab === 'finance' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition">💰 وضعیت مالی و تسویه</button>
                    </div>
                </div>

                <!-- تب ۱: پرونده و مشخصات -->
                <div x-show="activeTab === 'profile'" class="space-y-4" x-data="{ openEditModal: false }">
                    
                    <div class="flex justify-between items-center bg-slate-950/80 p-3 rounded-xl border border-slate-800">
                        <span class="text-xs text-slate-300 font-bold">اطلاعات هویتی و شغلی:</span>
                        <button type="button" @click="openEditModal = true" class="text-xs bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-3.5 py-1.5 rounded-lg transition shadow flex items-center gap-1.5 cursor-pointer">
                            <span>✏ ویرایش پرونده</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 block">شماره تماس اصلی:</span>
                                <span class="text-xs font-mono font-bold text-slate-200">{{ $tech->phone }}</span>
                            </div>
                            <a href="tel:{{ $tech->phone }}" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                📞 تماس
                            </a>
                        </div>

                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 block">شماره همراه دوم:</span>
                                <span class="text-xs font-mono text-slate-300">{{ $tech->secondary_phone ?? 'ثبت نشده' }}</span>
                            </div>
                            @if($tech->secondary_phone)
                            <a href="tel:{{ $tech->secondary_phone }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] px-2 py-1 rounded-lg transition">
                                📞 تماس
                            </a>
                            @endif
                        </div>

                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 text-[10px]">کد ملی:</span>
                                <span class="font-mono text-slate-300">{{ $tech->national_code ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 text-[10px]">تاریخ تولد:</span>
                                <span class="text-slate-300">{{ $tech->birth_date ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-2">
                        <span class="text-[11px] font-bold text-amber-400 block">محدوده‌ها و بلوک‌های مجاز:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($tech->locations as $loc)
                            <span class="bg-slate-900 border border-slate-700 text-slate-300 text-[11px] px-2 py-0.5 rounded-md">
                                🏢 {{ $loc->title }}
                            </span>
                            @empty
                            <span class="text-[11px] text-slate-500 italic">هنوز به هیچ بلوکی اختصاص داده نشده است.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <form action="{{ route('admin.technician.toggle', $tech->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs {{ $tech->is_active ? 'bg-rose-900/40 text-rose-300 hover:bg-rose-900/60' : 'bg-emerald-900/40 text-emerald-300 hover:bg-emerald-900/60' }} px-3 py-1.5 rounded-lg border border-slate-700 transition">
                                {{ $tech->is_active ? '🚫 معلق کردن حساب کاربری' : '✔ فعال‌سازی مجدد حساب' }}
                            </button>
                        </form>
                    </div>

                    <!-- مودال ویرایش پرونده -->
                    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                        <div @click.away="openEditModal = false" class="bg-slate-900 border border-amber-500/50 rounded-2xl w-full max-w-2xl p-5 space-y-4 shadow-2xl">
                            <div class="flex justify-between items-center border-b border-slate-800 pb-2.5">
                                <h3 class="text-xs font-black text-amber-400">ویرایش مشخصات پرونده {{ $tech->name }}</h3>
                                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-white text-base">✕</button>
                            </div>

                            <form action="{{ route('admin.technician.update', $tech->id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">نام و نام خانوادگی:</label>
                                        <input type="text" name="name" value="{{ $tech->name }}" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">شماره همراه اصلی:</label>
                                        <input type="text" name="phone" value="{{ $tech->phone }}" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">تغییر رمز عبور:</label>
                                        <input type="text" name="password" placeholder="در صورت عدم تغییر خالی بگذارید" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">کد ملی:</label>
                                        <input type="text" name="national_code" value="{{ $tech->national_code }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">شماره همراه دوم:</label>
                                        <input type="text" name="secondary_phone" value="{{ $tech->secondary_phone }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">تاریخ تولد:</label>
                                        <input type="text" name="birth_date" value="{{ $tech->birth_date }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-center text-slate-200 focus:border-amber-500 focus:outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">حقوق ثابت ماهانه (تومان):</label>
                                    <input type="number" name="base_salary" value="{{ $tech->base_salary }}" class="w-full sm:w-1/2 bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold focus:border-amber-500 focus:outline-none">
                                </div>

                                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                                    <button type="button" @click="openEditModal = false" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-3.5 py-1.5 rounded-lg text-xs cursor-pointer">انصراف</button>
                                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-1.5 rounded-lg text-xs cursor-pointer">ذخیره تغییرات</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- تب ۲: سپردن کار با کادر اختصاص سهم -->
                <div x-show="activeTab === 'assign'" class="space-y-3" x-data="{ hasShare: false, shareType: 'percentage', invoiceAmount: 0, shareVal: 0 }">
                    <form action="{{ route('admin.technician.assign', $tech->id) }}" method="POST" class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 space-y-3">
                        @csrf
                        <p class="text-[11px] font-bold text-amber-400">ارجاع مستقیم کار به {{ $tech->name }}:</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">انتخاب ساختمان / ورودی هدف:</label>
                                <select name="building_id" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                                    <option value="">-- انتخاب کنید --</option>
                                    @foreach ($locations as $loc)
                                    <optgroup label="محدوده {{ $loc->title }}">
                                        @foreach ($loc->buildings as $b)
                                        <option value="{{ $b->id }}">{{ $b->title }} ({{ $loc->title }})</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">شماره واحد / طبقه / آسانسور:</label>
                                <input type="text" name="building_unit" placeholder="مثال: آسانسور غربی، واحد ۱۲" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] text-slate-400 mb-1">شرح عملیات ارجاعی:</label>
                                <input type="text" name="title" required placeholder="مثال: تعویض لنت ترمز یا سرویس ماهانه" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">مبلغ فاکتور مشتری (تومان):</label>
                                <input type="number" name="amount" x-model.number="invoiceAmount" placeholder="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold">
                            </div>
                        </div>

                        <!-- کادر پورسانت و سهم تکنسین -->
                        <div class="bg-slate-900/70 border border-slate-800 p-3 rounded-xl space-y-2.5">
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" x-model="hasShare" class="rounded text-amber-500 focus:ring-0">
                                <span class="font-bold text-amber-400">تکنسین از این کار حق‌الزحمه / پورسانت مستقیم دریافت می‌کند</span>
                            </label>

                            <div x-show="hasShare" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-slate-800">
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">روش محاسبه سهم:</label>
                                    <select name="technician_share_type" x-model="shareType" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-slate-200">
                                        <option value="percentage">درصدی از فاکتور (%)</option>
                                        <option value="fixed">مبلغ مقطوع / اجرت ثابت (تومان)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1" x-text="shareType === 'percentage' ? 'درصد سهم تکنسین:' : 'مبلغ اجرت تکنسین (تومان):'"></label>
                                    <input type="number" name="technician_share_value" x-model.number="shareVal" placeholder="0" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-bold">
                                </div>
                                <div class="flex flex-col justify-end">
                                    <span class="text-[10px] text-slate-500">سهم محاسبه‌شده تکنسین:</span>
                                    <span class="text-xs font-bold text-emerald-400 mt-1">
                                        <span x-text="shareType === 'percentage' ? Math.round((invoiceAmount * shareVal) / 100).toLocaleString() : Number(shareVal).toLocaleString()"></span> تومان
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition">
                            ثبت و ارجاع به پنل تکنسین
                        </button>
                    </form>
                </div>

                <!-- تب ۳: تاریخچه کارها -->
                <div x-show="activeTab === 'history'" class="space-y-2">
                    <div class="max-h-80 overflow-y-auto space-y-2.5 pr-1">
                        @forelse ($tech->breakdowns()->latest()->get() as $task)
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-100">{{ $task->title }}</span>
                                        <span class="text-[10px] bg-slate-900 text-amber-400 px-1.5 py-0.5 rounded border border-slate-800">{{ $task->building->title ?? '-' }}</span>
                                        @if($task->building_unit)
                                        <span class="text-[10px] text-slate-400">({{ $task->building_unit }})</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ jdate($task->created_at)->format('Y/m/d - H:i') }}</span>
                                </div>

                                <div class="text-left">
                                    <span class="text-xs font-bold {{ $task->approval_status === 'approved' ? 'text-emerald-400' : 'text-amber-400' }}">
                                        {{ $task->is_billable ? number_format($task->amount).' تومان' : 'سرویس عادی' }}
                                    </span>
                                    @if($task->technician_share_amount > 0)
                                    <span class="block text-[10px] text-amber-400 font-bold">سهم تکنسین: {{ number_format($task->technician_share_amount) }} ت</span>
                                    @endif
                                    <span class="block text-[10px] text-slate-500 mt-0.5">{{ $task->approval_status === 'approved' ? '✔ تایید شده' : '⏳ در انتظار تایید' }}</span>
                                </div>
                            </div>

                            @if($task->technician_note)
                            <div class="bg-slate-900/90 border-r-2 border-amber-500 p-2 rounded-lg text-[11px] text-slate-300">
                                <span class="text-amber-400 font-bold block text-[10px] mb-0.5">🛠 گزارش و توضیحات تکنسین:</span>
                                <p class="leading-relaxed">{{ $task->technician_note }}</p>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-600 italic block">بدون توضیحات تکمیلی تکنسین</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-xs text-slate-500 p-3 text-center">هنوز هیچ فعالیتی ثبت نشده است.</p>
                        @endforelse
                    </div>
                </div>

                <!-- تب ۴: وضعیت مالی و معین پرسنل -->
                <div x-show="activeTab === 'finance'" class="space-y-4" x-data="{ showPayoutForm: false }">

                    <!-- باکس‌های جمع‌بندی مالی تفکیک‌شده -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl">
                            <span class="text-[10px] text-slate-400 block">کل پورسانت‌های تاییدشده:</span>
                            <span class="text-sm font-black text-amber-400 font-mono mt-1 block">{{ number_format($commissionEarned) }} تومان</span>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl">
                            <span class="text-[10px] text-slate-400 block">حقوق ثابت ماهانه:</span>
                            <span class="text-sm font-black text-blue-400 font-mono mt-1 block">{{ number_format($tech->base_salary ?? 0) }} تومان</span>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl">
                            <span class="text-[10px] text-slate-400 block">کل پرداختی‌ها و مساعده‌ها:</span>
                            <span class="text-sm font-black text-emerald-400 font-mono mt-1 block">{{ number_format($totalPaid) }} تومان</span>
                        </div>
                        <div class="bg-slate-950 border {{ $balance > 0 ? 'border-amber-500/50 bg-amber-500/5' : ($balance < 0 ? 'border-purple-500/50 bg-purple-500/5' : 'border-emerald-500/50 bg-emerald-500/5') }} p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">مانده‌طلب پورسانت:</span>
                                <span class="text-sm font-black font-mono {{ $balance > 0 ? 'text-amber-400' : ($balance < 0 ? 'text-purple-400' : 'text-emerald-400') }} mt-1 block">
                                    {{ number_format(abs($balance)) }} تومان
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $balance > 0 ? '(طلبکار)' : ($balance < 0 ? '(مساعده اضافی)' : '(تسویه کامل)') }}
                                </span>
                            </div>
                            <button type="button" @click="showPayoutForm = !showPayoutForm" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                                💳 ثبت واریز
                            </button>
                        </div>
                    </div>

                    <!-- فرم ثبت واریز بانکی جدید به تکنسین -->
                    <div x-show="showPayoutForm" x-cloak class="bg-slate-950 border border-amber-500/30 p-3.5 rounded-xl space-y-3">
                        <p class="text-xs font-bold text-amber-400">ثبت فیش واریزی جدید به حساب {{ $tech->name }}:</p>
                        <form action="{{ route('admin.technician.payout.store', $tech->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">مبلغ واریزی (تومان):</label>
                                    <input type="number" name="amount" required placeholder="مثال: 3000000" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">تاریخ واریز:</label>
                                    <input type="text" name="paid_at" value="{{ jdate()->format('Y/m/d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-center text-slate-200 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">شماره پیگیری / فیش:</label>
                                    <input type="text" name="tracking_code" placeholder="کد ارجاع بانکی" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">بابت:</label>
                                    <select name="payment_type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                                        <option value="commission">پورسانت و اجرت کارها</option>
                                        <option value="advance">مساعده / علی‌الحساب</option>
                                        <option value="salary">حقوق ثابت ماهانه</option>
                                        <option value="settlement">تسویه‌حساب نهایی</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">توضیحات اختیاری:</label>
                                <input type="text" name="description" placeholder="مثال: واریز بابت پورسانت تعویض لنت یا تسویه نیمه اول ماه" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                            </div>
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition">
                                ثبت سند و اعمال در معین پرسنل
                            </button>
                        </form>
                    </div>

                    <!-- جدول تاریخچه واریزی‌های بانکی -->
                    <div class="space-y-1.5">
                        <span class="text-[11px] font-bold text-slate-400 block">ریز فیش‌ها و اسناد پرداختی به پرسنل:</span>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-right border-collapse">
                                <thead>
                                    <tr class="bg-slate-950 text-slate-400 border-b border-slate-800 text-[10px]">
                                        <th class="p-2">تاریخ</th>
                                        <th class="p-2">مبلغ واریزی</th>
                                        <th class="p-2">نوع پرداخت</th>
                                        <th class="p-2">کد پیگیری</th>
                                        <th class="p-2">توضیحات</th>
                                        <th class="p-2 text-center">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    @forelse ($tech->payouts()->latest()->get() as $payout)
                                    <tr class="hover:bg-slate-950/40">
                                        <td class="p-2 font-mono text-[11px] text-slate-300">{{ $payout->paid_at }}</td>
                                        <td class="p-2 font-bold text-emerald-400 font-mono">{{ number_format($payout->amount) }} تومان</td>
                                        <td class="p-2">
                                            @switch($payout->payment_type)
                                            @case('commission') <span class="text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded text-[10px] font-bold">پورسانت کارها</span> @break
                                            @case('advance') <span class="text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded text-[10px] font-bold">مساعده / علی‌الحساب</span> @break
                                            @case('salary') <span class="text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded text-[10px] font-bold">حقوق ثابت</span> @break
                                            @case('settlement') <span class="text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded text-[10px] font-bold">تسویه نهایی</span> @break
                                            @default <span class="text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded text-[10px]">سایر</span>
                                            @endswitch
                                        </td>
                                        <td class="p-2 font-mono text-[11px] text-slate-400">{{ $payout->tracking_code ?? '-' }}</td>
                                        <td class="p-2 text-slate-400 text-[11px]">{{ $payout->description ?? '-' }}</td>
                                        <td class="p-2 text-center">
                                            <form action="{{ route('admin.technician.payout.destroy', $payout->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این سند واریزی اطمینان دارید؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-500 hover:text-rose-400 transition" title="حذف فیش">✕</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-slate-500 text-[11px]">هنوز هیچ پرداختی برای این پرسنل ثبت نشده است.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
            @empty
            <div class="text-center py-10 text-xs text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                هیچ پرسنلی در سیستم تعریف نشده است. با دکمه «تعریف پرسنل جدید» در بالای صفحه اولین نیرو را اضافه کنید.
            </div>
            @endforelse
        </div>

    </main>

</body>

</html>