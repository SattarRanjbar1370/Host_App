<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>پنل پرسنل فنی | service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            -webkit-tap-highlight-color: transparent;
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

<body class="bg-slate-900 text-slate-100 min-h-screen pb-16">

    <!-- نوار بالای صفحه -->
    <header class="bg-slate-800 border-b border-slate-700 px-4 py-3 flex justify-between items-center sticky top-0 z-30 shadow-md">
        <div>
            <h1 class="text-base font-bold text-amber-500">service123</h1>
            <p class="text-[11px] text-slate-400">تکنسین: {{ auth()->user()->name }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs bg-slate-700 hover:bg-rose-600/80 text-slate-300 hover:text-white px-3 py-1.5 rounded-lg transition cursor-pointer">
                خروج
            </button>
        </form>
    </header>

    <main class="p-4 max-w-lg mx-auto space-y-5" x-data="{ mainTab: 'tasks', showNewForm: false }">

        <!-- پیام موفقیت عملیات -->
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

        <!-- نوار انتخاب تب کاری تکنسین -->
        <div class="flex items-center justify-center gap-2 bg-slate-850 p-1.5 rounded-2xl border border-slate-700 bg-slate-800/80 text-xs shadow-md">
            <button type="button" @click="mainTab = 'tasks'"
                :class="mainTab === 'tasks' ? 'bg-amber-500 text-slate-950 font-black shadow' : 'text-slate-300 hover:text-white'"
                class="flex-1 py-2.5 rounded-xl transition cursor-pointer text-center">
                ⚡ خرابی‌ها و کارهای روز
            </button>
            <button type="button" @click="mainTab = 'monthly'"
                :class="mainTab === 'monthly' ? 'bg-amber-500 text-slate-950 font-black shadow' : 'text-slate-300 hover:text-white'"
                class="flex-1 py-2.5 rounded-xl transition cursor-pointer text-center">
                📋 کاربرگ سرویس ماهانه
            </button>
        </div>

        <!-- ==================== تب ۱: خرابی‌ها و کارهای روزانه ==================== -->
        <div x-show="mainTab === 'tasks'" class="space-y-4">

            <!-- دکمه باز و بسته کردن فرم ثبت خرابی -->
            <button @click="showNewForm = !showNewForm"
                class="w-full bg-amber-500 hover:bg-amber-600 active:scale-98 text-slate-950 font-black py-3 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 transition cursor-pointer">
                <span class="text-sm leading-none" x-text="showNewForm ? '✖ بستن فرم ثبت' : '➕ ثبت خرابی یا کار جدید'"></span>
            </button>

            <!-- فرم بازشونده ثبت کار و خرابی -->
            <div x-show="showNewForm" x-cloak class="bg-slate-800 border border-amber-500/40 rounded-2xl p-4 shadow-xl space-y-3" x-data="{ hasPart: false, hasCost: false }">
                <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                    <h2 class="text-xs font-bold text-amber-400">ثبت تسک یا گزارش خرابی</h2>
                    <button type="button" @click="showNewForm = false" class="text-xs text-slate-400 hover:text-white cursor-pointer">✕ بستن</button>
                </div>

                <form action="{{ route('technician.breakdown.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-[11px] text-slate-300 mb-1 font-medium">انتخاب ساختمان / ورودی هدف:</label>
                        <select name="building_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach ($buildings->groupBy('location.title') as $locTitle => $bGroup)
                            <optgroup label="محدوده {{ $locTitle ?: 'سایر' }}">
                                @foreach ($bGroup as $building)
                                <option value="{{ $building->id }}">{{ $building->title }} {{ $building->code ? "($building->code)" : '' }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] text-slate-300 mb-1 font-medium">شماره آسانسور / واحد (اختیاری):</label>
                            <input type="text" name="building_unit" placeholder="مثال: آسانسور غربی، واحد ۳"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-amber-500 focus:outline-none text-slate-200">
                        </div>

                        <div>
                            <label class="block text-[11px] text-slate-300 mb-1 font-medium">زمان اجرا:</label>
                            <div class="grid grid-cols-2 gap-1.5 text-xs">
                                <label class="flex items-center gap-1.5 bg-slate-900 border border-slate-700 p-2 rounded-xl cursor-pointer">
                                    <input type="radio" name="urgency" value="immediate" checked class="text-amber-500 focus:ring-0">
                                    <span class="text-[11px]">🟢 فوری</span>
                                </label>
                                <label class="flex items-center gap-1.5 bg-slate-900 border border-slate-700 p-2 rounded-xl cursor-pointer">
                                    <input type="radio" name="urgency" value="postponed" class="text-amber-500 focus:ring-0">
                                    <span class="text-[11px]">🟡 معوق</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-300 mb-1 font-medium">عنوان یا شرح خلاصه خرابی:</label>
                        <input type="text" name="title" required placeholder="مثال: تعویض لنت ترمز یا رفع گیرپاژ درب"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-amber-500 focus:outline-none text-slate-200">
                    </div>

                    <!-- بخش قطعه تعویضی -->
                    <div class="bg-slate-900/80 border border-slate-700/80 p-2.5 rounded-xl space-y-2">
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" x-model="hasPart" class="rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-0">
                            <span class="font-bold text-amber-400">قطعه تعویضی دارد / قطعه مصرف شد</span>
                        </label>

                        <div x-show="hasPart" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 border-t border-slate-800">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-0.5">نام قطعه:</label>
                                <input type="text" name="part_name" placeholder="مثال: کنتاکتور ۲۵ آمپر"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-0.5">مبلغ قطعه (تومان):</label>
                                <input type="number" name="part_price" placeholder="0"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- بخش هزینه فاکتور -->
                    <div class="bg-slate-900/80 border border-slate-700/80 p-2.5 rounded-xl space-y-2">
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_billable" x-model="hasCost" class="rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-0">
                            <span class="font-bold">این کار نیاز به دریافت هزینه (فاکتور) دارد</span>
                        </label>

                        <div x-show="hasCost" x-cloak class="pt-1 border-t border-slate-800">
                            <label class="block text-[10px] text-amber-400 mb-1">کل مبلغ اعلامی به مشتری (اجرت + قطعه):</label>
                            <input type="number" name="amount" placeholder="0"
                                class="w-full bg-slate-950 border border-amber-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-300 mb-1 font-medium">گزارش و توضیحات تکنسین:</label>
                        <textarea name="description" rows="2" placeholder="توضیح اختیاری درباره علائم خرابی، نحوه رفع عیب یا نیازهای بعدی..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-2.5 rounded-xl shadow transition cursor-pointer">
                        ثبت نهایی و ارسال به سیستم
                    </button>
                </form>
            </div>

            <!-- لیست چک‌لیست کارهای روزانه -->
            <section class="space-y-3">
                <h2 class="text-xs font-bold text-slate-400 flex items-center justify-between">
                    <span>کارهای امروز و چک‌لیست فعال</span>
                    <span class="bg-slate-800 px-2 py-0.5 rounded text-[10px] text-amber-500">{{ $tasks->count() }} تسک</span>
                </h2>

                @forelse ($tasks as $task)
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-3.5 space-y-3" x-data="{ completeBox: false, hasCost: false }">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-700 text-amber-400">
                                    {{ $task->building->location->title ?? '' }} - {{ $task->building->title }}
                                </span>
                                @if($task->building_unit)
                                <span class="text-[10px] text-slate-400">({{ $task->building_unit }})</span>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-slate-100">{{ $task->title }}</h3>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded {{ $task->urgency === 'immediate' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' }}">
                            {{ $task->urgency === 'immediate' ? 'فوری' : 'معوق' }}
                        </span>
                    </div>

                    <div class="border-t border-slate-700/60 pt-2 flex items-center justify-between">
                        <button @click="completeBox = !completeBox" type="button"
                            class="text-xs font-bold bg-slate-700 hover:bg-emerald-600 text-slate-200 hover:text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1 cursor-pointer">
                            <span>✔ تیک انجام کار</span>
                        </button>
                        <span class="text-[10px] text-slate-500 font-mono">{{ function_exists('jdate') ? jdate($task->created_at)->format('Y/m/d - H:i') : $task->created_at->format('Y/m/d') }}</span>
                    </div>

                    <!-- فرم تایید اتمام کار -->
                    <form x-show="completeBox" x-cloak action="{{ route('technician.task.complete', $task->id) }}" method="POST" class="space-y-2.5 pt-2 border-t border-slate-700">
                        @csrf
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_billable" x-model="hasCost" class="rounded text-amber-500 focus:ring-0">
                            <span>هزینه‌دار بود؟</span>
                        </label>

                        <div x-show="hasCost" x-cloak class="pt-1">
                            <input type="number" name="amount" placeholder="مبلغ کل فاکتور مشتری (تومان)" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold focus:outline-none">
                        </div>

                        <input type="text" name="technician_note" placeholder="توضیح اختیاری درباره نحوه انجام کار..." class="w-full bg-slate-900 border border-slate-600 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 rounded-lg text-xs cursor-pointer">
                            تایید نهایی و ثبت پایان کار
                        </button>
                    </form>
                </div>
                @empty
                <div class="bg-slate-800/40 border border-dashed border-slate-700 rounded-xl p-6 text-center text-xs text-slate-500">
                    هیچ کار یا خرابی در نوبت فعالی وجود ندارد.
                </div>
                @endforelse
            </section>
        </div>

        <!-- ==================== تب ۲: کاربرگ سرویس ماهانه بلوک‌ها ==================== -->
        <div x-show="mainTab === 'monthly'" x-cloak class="space-y-4" x-data="{ activeLoc: '{{ $locations->first()->id ?? '' }}' }">

            <!-- نوار دوره ماهانه و انتخاب بلوک -->
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px]">دوره سرویس ماهانه:</span>
                    <span class="font-bold text-amber-400 font-mono text-sm">{{ $selectedMonth }}</span>
                </div>

                <!-- دکمه‌های بلوک‌ها -->
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($locations as $loc)
                    <button type="button" @click="activeLoc = '{{ $loc->id }}'"
                        :class="activeLoc === '{{ $loc->id }}' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-900 text-slate-300 border border-slate-700'"
                        class="px-3 py-1 rounded-lg transition cursor-pointer text-xs">
                        🏢 {{ $loc->title }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- جدول ورودی‌های بلوک انتخاب‌شده -->
            @foreach ($locations as $loc)
            <div x-show="activeLoc === '{{ $loc->id }}'" class="space-y-3">
                @php
                $locBuildings = $buildings->where('location_id', $loc->id);
                @endphp

                <div class="overflow-x-auto rounded-xl border border-slate-700 bg-slate-800/90 shadow">
                    <table class="w-full text-right text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-slate-400 border-b border-slate-700 text-[11px]">
                                <th class="p-3">ورودی / ساختمان</th>
                                <th class="p-3 text-center">وضعیت</th>
                                <th class="p-3">تاریخ انجام</th>
                                <th class="p-3">توضیحات فنی</th>
                                <th class="p-3 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/60">
                            @forelse ($locBuildings as $b)
                            @php
                            $service = $monthlyServices->get($b->id);
                            $isDone = $service && $service->is_completed;
                            @endphp
                            <tr class="hover:bg-slate-900/40 {{ $isDone ? 'bg-emerald-500/5' : '' }}">
                                <td class="p-3 font-bold text-slate-200">
                                    <!-- فرم استاندارد با ویژگی form در HTML5 -->
                                    <form id="srv-form-{{ $b->id }}" action="{{ url('/technician/monthly-service') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="building_id" value="{{ $b->id }}">
                                        <input type="hidden" name="service_month" value="{{ $selectedMonth }}">
                                    </form>

                                    {{ $b->title }}
                                    @if($b->code) <span class="text-[10px] text-slate-400 font-mono">({{ $b->code }})</span> @endif
                                </td>

                                <td class="p-3 text-center">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" form="srv-form-{{ $b->id }}" name="is_completed" value="1" {{ $isDone ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-0">
                                        <span class="text-[11px] {{ $isDone ? 'text-emerald-400 font-bold' : 'text-slate-400' }}">
                                            {{ $isDone ? '✔ انجام شد' : 'در انتظار' }}
                                        </span>
                                    </label>
                                </td>

                                <td class="p-3">
                                    <input type="text" form="srv-form-{{ $b->id }}" name="serviced_at" value="{{ $service?->serviced_at ?: (function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d')) }}" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-[11px] font-mono text-center text-slate-200 w-28 focus:border-amber-500 focus:outline-none">
                                </td>

                                <td class="p-3">
                                    <input type="text" form="srv-form-{{ $b->id }}" name="technician_note" value="{{ $service?->technician_note }}" placeholder="روغن‌کاری، تنظیم قفل..." class="w-full min-w-[150px] bg-slate-950 border border-slate-700 rounded px-2.5 py-1 text-[11px] text-slate-200 focus:border-amber-500 focus:outline-none">
                                </td>

                                <td class="p-3 text-center">
                                    <button type="submit" form="srv-form-{{ $b->id }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-3 py-1 rounded-lg text-[11px] transition shadow cursor-pointer">
                                        ثبت ردیف
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">هیچ ورودی مجازی در این بلوک وجود ندارد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </div>

    </main>

</body>

</html>