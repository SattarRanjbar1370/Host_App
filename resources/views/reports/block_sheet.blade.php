<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>کاربرگ رسمی سرویس و نگهداری آسانسور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-clean { border: none !important; box-shadow: none !important; padding: 0 !important; }
            input, textarea {
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
                outline: none !important;
                width: 100% !important;
                resize: none !important;
            }
            @page { size: A4 portrait; margin: 8mm 10mm; }
        }
    </style>
</head>

@php
    $companiesArray = $companies->map(function($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'brand' => $c->brand ?: $c->name,
            'reg_number' => $c->reg_number ?: '-',
            'national_id' => $c->national_id ?: '-',
            'phone' => $c->phone ?: '-',
            'logo' => $c->logo_path ? asset('uploads/' . $c->logo_path) : null,
            'logo_icon' => $c->logo_icon ?: '🛗',
        ];
    });

    $initialBreakdowns = $breakdowns->map(function($b) {
        return [
            'building' => $b->building->title . ($b->building_unit ? " ($b->building_unit)" : ''),
            'title' => $b->title,
            'part' => $b->part_name ?: '-',
            'cost' => number_format($b->amount),
        ];
    })->values();
@endphp

<body class="bg-slate-100 text-slate-900 p-4 sm:p-6 min-h-screen"
      x-data="{
          companies: {{ json_encode($companiesArray, JSON_UNESCAPED_UNICODE) }},
          selectedCompanyId: {{ $defaultCompany->id ?? ($companies->first()->id ?? 1) }},
          blockTitle: '{{ $location->title }}',
          serviceMonth: '{{ $month }}',
          reportDate: '{{ function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d') }}',
          
          // آرایه پویا برای مدیریت سطرهای خرابی و قطعات
          breakdownList: {{ json_encode($initialBreakdowns, JSON_UNESCAPED_UNICODE) }},
          
          addRow() {
              this.breakdownList.push({
                  building: '',
                  title: '',
                  part: '-',
                  cost: '0'
              });
          },
          
          removeRow(index) {
              this.breakdownList.splice(index, 1);
          },

          get currentCompany() {
              return this.companies.find(c => c.id == this.selectedCompanyId) || this.companies[0] || {};
          }
      }">

    <div class="max-w-4xl mx-auto bg-white border border-slate-300 p-8 rounded-2xl shadow-sm print-clean space-y-6">

        <!-- نوار ابزار بالا -->
        <div class="no-print bg-slate-900 text-slate-100 p-4 rounded-xl flex flex-wrap items-center justify-between gap-3 shadow-md">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-3 py-1.5 rounded-lg transition">
                    ⬅ بازگشت به داشبورد
                </a>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-300 font-bold">انتخاب سربرگ شرکت:</label>
                    <select x-model="selectedCompanyId" class="bg-slate-950 border border-slate-700 text-amber-400 text-xs rounded-lg px-3 py-1.5 focus:outline-none cursor-pointer">
                        <template x-for="comp in companies" :key="comp.id">
                            <option :value="comp.id" x-text="comp.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.report.block.excel', ['location' => $location->id, 'month' => $month]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                    📥 خروجی اکسل
                </a>
                <button onclick="window.print()" class="bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black px-4 py-1.5 rounded-lg transition cursor-pointer">
                    🖨 چاپ برگه / ذخیره PDF
                </button>
            </div>
        </div>

        <!-- سربرگ رسمی با متون قابل ویرایش -->
        <div class="border-b-2 border-slate-800 pb-4">
            <div class="flex justify-between items-start gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <template x-if="currentCompany.logo">
                        <img :src="currentCompany.logo" alt="Logo" class="w-16 h-16 object-contain">
                    </template>
                    <template x-if="!currentCompany.logo">
                        <div class="w-14 h-14 bg-slate-100 border border-slate-300 rounded-xl flex items-center justify-center text-2xl" x-text="currentCompany.logo_icon || '🛗'"></div>
                    </template>

                    <div class="flex-1 space-y-0.5">
                        <input type="text" x-model="currentCompany.name" class="text-lg font-black text-slate-900 tracking-tight w-full bg-transparent border-b border-transparent hover:border-slate-300 focus:border-amber-500 focus:outline-none">
                        <p class="text-xs text-slate-600 font-medium">کاربرگ سرویس و نگهداری دوره‌ای و گزارش فنی آسانسور</p>
                        <p class="text-[11px] text-slate-500">
                            شماره ثبت: <span class="font-mono font-bold" x-text="currentCompany.reg_number"></span>
                            <span> | شناسه ملی: <span class="font-mono" x-text="currentCompany.national_id"></span></span>
                            <span> | تلفن تماس: <span class="font-mono" x-text="currentCompany.phone"></span></span>
                        </p>
                    </div>
                </div>

                <!-- مشخصات برگه (دوره، بلوک، تاریخ تنظیم) کاملاً قابل ادیت -->
                <div class="text-left text-xs space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200 min-w-[220px]">
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-slate-500 text-[11px]">محدوده / بلوک:</span>
                        <input type="text" x-model="blockTitle" class="font-bold text-slate-800 text-left bg-transparent border-b border-transparent hover:border-slate-300 focus:border-amber-500 focus:outline-none w-28">
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-slate-500 text-[11px]">دوره سرویس:</span>
                        <input type="text" x-model="serviceMonth" class="font-mono font-bold text-slate-800 text-left bg-transparent border-b border-transparent hover:border-slate-300 focus:border-amber-500 focus:outline-none w-28">
                    </div>
                    <div class="flex justify-between items-center gap-2 border-t border-slate-200 pt-1">
                        <span class="text-slate-500 text-[11px]">تاریخ تنظیم لیست:</span>
                        <input type="text" x-model="reportDate" class="font-mono font-bold text-slate-800 text-left bg-transparent border-b border-transparent hover:border-slate-300 focus:border-amber-500 focus:outline-none w-28">
                    </div>
                </div>
            </div>
        </div>

        <!-- بخش اول: چک‌لیست سرویس ماهانه با ردیف‌های قابل ویرایش -->
        <div class="space-y-2">
            <div class="flex justify-between items-center bg-slate-100 px-3 py-1.5 rounded">
                <h2 class="text-xs font-black text-slate-800">۱. وضعیت سرویس دوره‌ای ورودی‌های بلوک</h2>
                <span class="no-print text-[10px] text-slate-500">💡 تمام ردیف‌ها و تاریخ‌ها برای چاپ قابل ویرایش هستند.</span>
            </div>

            <table class="w-full text-xs text-right border border-slate-300 border-collapse">
                <thead>
                    <tr class="bg-slate-200/80 text-slate-800 text-[11px]">
                        <th class="p-2 border border-slate-300 w-10 text-center">ردیف</th>
                        <th class="p-2 border border-slate-300 w-44">ورودی / آسانسور</th>
                        <th class="p-2 border border-slate-300 text-center w-28">وضعیت سرویس</th>
                        <th class="p-2 border border-slate-300 text-center w-28">تاریخ سرویس</th>
                        <th class="p-2 border border-slate-300">شرح عملیات و اقدامات انجام‌شده</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($buildings as $idx => $b)
                        @php
                            $srv = $b->monthlyServices->first();
                            $defaultDate = $srv?->serviced_at ?: ($srv?->is_completed ? (function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d')) : '-');
                            $defaultStatus = ($srv && $srv->is_completed) ? '✔ انجام شد' : 'انجام نشده';
                            $defaultNote = $srv?->technician_note ?: (($srv && $srv->is_completed) ? 'سرویس ماهانه، آچارکشی، نظافت و روغن‌کاری کامل انجام شد.' : 'در انتظار سرویس دوره‌ای');
                        @endphp
                        <tr x-data="{ title: '{{ $b->title }} {{ $b->code ? "($b->code)" : '' }}', status: '{{ $defaultStatus }}', date: '{{ $defaultDate }}', note: '{{ $defaultNote }}' }">
                            <td class="p-2 border border-slate-300 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="p-2 border border-slate-300">
                                <input type="text" x-model="title" class="w-full font-bold text-slate-800 bg-transparent focus:outline-none">
                            </td>
                            <td class="p-2 border border-slate-300 text-center">
                                <input type="text" x-model="status" class="w-full text-center font-bold text-slate-800 bg-transparent focus:outline-none">
                            </td>
                            <td class="p-2 border border-slate-300 text-center font-mono">
                                <input type="text" x-model="date" class="w-full text-center bg-transparent border border-slate-200 focus:border-amber-500 rounded px-1 py-0.5 text-xs font-mono">
                            </td>
                            <td class="p-2 border border-slate-300">
                                <input type="text" x-model="note" class="w-full bg-transparent border border-slate-200 focus:border-amber-500 rounded px-1.5 py-0.5 text-xs text-slate-800">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- بخش دوم: خرابی‌ها و قطعات مصرفی با قابلیت افزودن/حذف ردیف -->
        <div class="space-y-2">
            <div class="flex justify-between items-center bg-slate-100 px-3 py-1.5 rounded">
                <h2 class="text-xs font-black text-slate-800">۲. خرابی‌های رفع‌شده و قطعات مصرفی ماه</h2>
                <button type="button" @click="addRow()" class="no-print bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] px-2.5 py-1 rounded-md transition shadow cursor-pointer">
                    ➕ افزودن سطر جدید
                </button>
            </div>
            
            <table class="w-full text-xs text-right border border-slate-300 border-collapse">
                <thead>
                    <tr class="bg-slate-200/80 text-slate-800 text-[11px]">
                        <th class="p-2 border border-slate-300 w-10 text-center">ردیف</th>
                        <th class="p-2 border border-slate-300 w-44">ورودی / واحد</th>
                        <th class="p-2 border border-slate-300">شرح خرابی و عملیات رفع نقص</th>
                        <th class="p-2 border border-slate-300 w-40">قطعه تعویضی</th>
                        <th class="p-2 border border-slate-300 text-left w-28">هزینه (تومان)</th>
                        <th class="no-print p-2 border border-slate-300 w-10 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in breakdownList" :key="index">
                        <tr>
                            <td class="p-2 border border-slate-300 text-center font-mono text-slate-500" x-text="index + 1"></td>
                            <td class="p-2 border border-slate-300 font-bold text-slate-800">
                                <input type="text" x-model="item.building" placeholder="مثال: ورودی ۳" class="w-full font-bold text-slate-800 bg-transparent border border-transparent hover:border-slate-200 focus:border-amber-500 rounded px-1 py-0.5 focus:outline-none">
                            </td>
                            <td class="p-2 border border-slate-300">
                                <input type="text" x-model="item.title" placeholder="شرح نقص..." class="w-full bg-transparent border border-slate-200 focus:border-amber-500 rounded px-1.5 py-0.5 text-xs">
                            </td>
                            <td class="p-2 border border-slate-300 font-mono">
                                <input type="text" x-model="item.part" placeholder="قطعه..." class="w-full bg-transparent border border-slate-200 focus:border-amber-500 rounded px-1.5 py-0.5 text-xs font-mono">
                            </td>
                            <td class="p-2 border border-slate-300 text-left font-mono font-bold text-slate-900">
                                <input type="text" x-model="item.cost" placeholder="0" class="w-full text-left font-mono font-bold bg-transparent border border-slate-200 focus:border-amber-500 rounded px-1 py-0.5 text-xs">
                            </td>
                            <td class="no-print p-2 border border-slate-300 text-center">
                                <button type="button" @click="removeRow(index)" class="text-rose-500 hover:text-rose-700 font-bold px-1.5 py-0.5 rounded text-xs transition cursor-pointer" title="حذف سطر">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="breakdownList.length === 0">
                        <tr>
                            <td colspan="6" class="p-3 text-center text-slate-500 border border-slate-300">
                                خرابی هزینه‌داری برای این بلوک ثبت نشده است. برای درج دستی روی دکمه «افزودن سطر جدید» کلیک کنید.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- بخش سوم: امضاها با نام تجاری شرکت انتخاب‌شده -->
        <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
            <div class="border border-slate-400 p-4 rounded-xl text-center space-y-10">
                <p class="font-bold text-slate-800">تایید و امضای نماینده / مدیریت محترم بلوک:</p>
                <div class="h-6"></div>
            </div>
            <div class="border border-slate-400 p-4 rounded-xl text-center space-y-10">
                <p class="font-bold text-slate-800">
                    مهر و امضای مسئول سرویس و نگهداری (<span x-text="currentCompany.brand || currentCompany.name"></span>):
                </p>
                <div class="h-6"></div>
            </div>
        </div>

    </div>

</body>
</html>