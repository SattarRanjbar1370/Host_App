<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتور خدمات آسانسور - {{ $building->title }}</title>
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
            @page { size: A5 portrait; margin: 6mm 8mm; }
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

    // محاسبه پیش‌فرض اقلام
    $monthlyFee = $building->has_monthly_fee ? (float)$building->monthly_fee : 0;
    $breakdownsTotal = (float)$breakdowns->sum('amount');
    $initialTotal = $monthlyFee + $breakdownsTotal;
@endphp

<body class="bg-slate-100 text-slate-900 p-3 sm:p-5 min-h-screen text-xs"
      x-data="{
          companies: {{ json_encode($companiesArray, JSON_UNESCAPED_UNICODE) }},
          selectedCompanyId: {{ $defaultCompany->id ?? ($companies->first()->id ?? 1) }},
          invoiceNo: '{{ $building->id . '-' . date('ymd') }}',
          invoiceDate: '{{ function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d') }}',
          periodMonth: '{{ $month }}',
          customerName: '{{ $building->location->title ?? '' }} - {{ $building->title }}',
          contactPerson: '{{ $primaryContact->name ?? 'مدیریت محترم ساختمان' }}',
          contactPhone: '{{ $primaryContact->phone ?? '-' }}',
          hasServiceFee: {{ $building->has_monthly_fee ? 'true' : 'false' }},
          serviceFeeAmount: {{ $monthlyFee }},
          items: [
              @foreach($breakdowns as $b)
              {
                  title: '{{ $b->title }}',
                  part: '{{ $b->part_name ?: '-' }}',
                  amount: {{ (float)$b->amount }}
              },
              @endforeach
          ],
          newItemTitle: '',
          newItemPart: '',
          newItemAmount: 0,
          addItem() {
              if(this.newItemTitle) {
                  this.items.push({ title: this.newItemTitle, part: this.newItemPart || '-', amount: Number(this.newItemAmount) || 0 });
                  this.newItemTitle = '';
                  this.newItemPart = '';
                  this.newItemAmount = 0;
              }
          },
          removeItem(index) {
              this.items.splice(index, 1);
          },
          get totalAmount() {
              let sum = this.hasServiceFee ? Number(this.serviceFeeAmount) : 0;
              this.items.forEach(i => sum += Number(i.amount));
              return sum;
          },
          get currentCompany() {
              return this.companies.find(c => c.id == this.selectedCompanyId) || this.companies[0] || {};
          }
      }">

    <div class="max-w-xl mx-auto bg-white border border-slate-300 p-5 rounded-2xl shadow-sm print-clean space-y-4">

        <!-- نوار کنترل چاپ و شرکت -->
        <div class="no-print bg-slate-900 text-slate-100 p-3 rounded-xl flex flex-wrap items-center justify-between gap-2 shadow-md">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-[11px] bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1 rounded-lg transition">
                    ⬅ داشبورد
                </a>
                <select x-model="selectedCompanyId" class="bg-slate-950 border border-slate-700 text-amber-400 text-xs rounded-lg px-2.5 py-1 focus:outline-none cursor-pointer">
                    <template x-for="comp in companies" :key="comp.id">
                        <option :value="comp.id" x-text="comp.name"></option>
                    </template>
                </select>
            </div>

            <button onclick="window.print()" class="bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black px-3.5 py-1 rounded-lg transition cursor-pointer">
                🖨 چاپ فاکتور A5
            </button>
        </div>

        <!-- سربرگ رسمی A5 -->
        <div class="border-b-2 border-slate-800 pb-3">
            <div class="flex justify-between items-start gap-2">
                <div class="flex items-center gap-2.5 flex-1">
                    <template x-if="currentCompany.logo">
                        <img :src="currentCompany.logo" alt="Logo" class="w-12 h-12 object-contain">
                    </template>
                    <template x-if="!currentCompany.logo">
                        <div class="w-11 h-11 bg-slate-100 border border-slate-300 rounded-lg flex items-center justify-center text-xl" x-text="currentCompany.logo_icon || '🛗'"></div>
                    </template>

                    <div>
                        <h1 class="text-sm font-black text-slate-900 leading-tight" x-text="currentCompany.name"></h1>
                        <p class="text-[10px] text-slate-500 mt-0.5">
                            شماره ثبت: <span class="font-mono font-bold" x-text="currentCompany.reg_number"></span>
                            | تلفن: <span class="font-mono" x-text="currentCompany.phone"></span>
                        </p>
                    </div>
                </div>

                <div class="text-left text-[11px] space-y-0.5 bg-slate-50 p-2 rounded-lg border border-slate-200 min-w-[150px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500">شماره فاکتور:</span>
                        <input type="text" x-model="invoiceNo" class="font-mono font-bold text-slate-800 text-left w-20 bg-transparent focus:outline-none">
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">تاریخ صدور:</span>
                        <input type="text" x-model="invoiceDate" class="font-mono font-bold text-slate-800 text-left w-20 bg-transparent focus:outline-none">
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-0.5">
                        <span class="text-slate-500">دوره خدمات:</span>
                        <input type="text" x-model="periodMonth" class="font-mono font-bold text-slate-800 text-left w-20 bg-transparent focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- کادر مشخصات خریدار / کارفرما -->
        <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xl grid grid-cols-2 gap-2 text-[11px]">
            <div>
                <span class="text-slate-500">ساختمان / ورودی:</span>
                <input type="text" x-model="customerName" class="font-bold text-slate-900 w-full bg-transparent focus:outline-none">
            </div>
            <div>
                <span class="text-slate-500">تحویل‌گیرنده / مدیر:</span>
                <input type="text" x-model="contactPerson" class="font-bold text-slate-900 w-full bg-transparent focus:outline-none">
            </div>
        </div>

        <!-- جدول اقلام صورت‌حساب -->
        <div class="space-y-1">
            <table class="w-full text-right border border-slate-300 border-collapse text-[11px]">
                <thead>
                    <tr class="bg-slate-200 text-slate-800">
                        <th class="p-1.5 border border-slate-300 w-8 text-center">#</th>
                        <th class="p-1.5 border border-slate-300">شرح خدمات / قطعه</th>
                        <th class="p-1.5 border border-slate-300 w-28 text-center">قطعه مصرفی</th>
                        <th class="p-1.5 border border-slate-300 w-24 text-left">مبلغ (تومان)</th>
                        <th class="no-print p-1.5 border border-slate-300 w-8 text-center">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ردیف شارژ سرویس ماهانه -->
                    <template x-if="hasServiceFee">
                        <tr class="bg-slate-50">
                            <td class="p-1.5 border border-slate-300 text-center font-mono text-slate-500">۱</td>
                            <td class="p-1.5 border border-slate-300 font-bold text-slate-800">
                                سرویس و آچارکشی دوره‌ای آسانسور
                            </td>
                            <td class="p-1.5 border border-slate-300 text-center text-slate-400 font-mono">-</td>
                            <td class="p-1.5 border border-slate-300 text-left font-mono font-bold text-slate-900">
                                <input type="number" x-model.number="serviceFeeAmount" class="w-full text-left font-mono font-bold bg-transparent focus:outline-none">
                            </td>
                            <td class="no-print p-1.5 border border-slate-300 text-center">
                                <button type="button" @click="hasServiceFee = false" class="text-rose-500 hover:text-rose-700">✕</button>
                            </td>
                        </tr>
                    </template>

                    <!-- سایر ردیف‌ها و خرابی‌ها -->
                    <template x-for="(item, idx) in items" :key="idx">
                        <tr>
                            <td class="p-1.5 border border-slate-300 text-center font-mono text-slate-500" x-text="hasServiceFee ? idx + 2 : idx + 1"></td>
                            <td class="p-1.5 border border-slate-300">
                                <input type="text" x-model="item.title" class="w-full bg-transparent focus:outline-none">
                            </td>
                            <td class="p-1.5 border border-slate-300 text-center font-mono">
                                <input type="text" x-model="item.part" class="w-full text-center bg-transparent focus:outline-none">
                            </td>
                            <td class="p-1.5 border border-slate-300 text-left font-mono font-bold text-slate-900">
                                <input type="number" x-model.number="item.amount" class="w-full text-left font-mono font-bold bg-transparent focus:outline-none">
                            </td>
                            <td class="no-print p-1.5 border border-slate-300 text-center">
                                <button type="button" @click="removeItem(idx)" class="text-rose-500 hover:text-rose-700">✕</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- فرم افزودن ردیف جدید (فقط در حالت نمایش) -->
            <div class="no-print bg-slate-50 border border-slate-200 p-2 rounded-lg flex items-center gap-2 text-[11px]">
                <input type="text" x-model="newItemTitle" placeholder="شرح خدمات جدید..." class="flex-1 bg-white border border-slate-300 rounded px-2 py-1">
                <input type="text" x-model="newItemPart" placeholder="قطعه..." class="w-24 bg-white border border-slate-300 rounded px-2 py-1 text-center">
                <input type="number" x-model.number="newItemAmount" placeholder="مبلغ (تومان)" class="w-28 bg-white border border-slate-300 rounded px-2 py-1 text-left font-mono">
                <button type="button" @click="addItem()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1 rounded">➕ افزودن</button>
            </div>
        </div>

        <!-- جمع کل صورت‌حساب -->
        <div class="flex justify-between items-center bg-slate-100 border border-slate-300 p-2.5 rounded-xl font-bold">
            <span class="text-slate-700">جمع کل قابل پرداخت:</span>
            <span class="text-sm font-black text-slate-900 font-mono">
                <span x-text="Number(totalAmount).toLocaleString()"></span> تومان
            </span>
        </div>

        <!-- پاورقی و امضاها -->
        <div class="grid grid-cols-2 gap-4 pt-3 text-[11px]">
            <div class="border border-dashed border-slate-400 p-2.5 rounded-xl text-center space-y-6">
                <p class="font-bold text-slate-700">مهر و امضای شرکت مجری:</p>
                <div class="h-4"></div>
            </div>
            <div class="border border-dashed border-slate-400 p-2.5 rounded-xl text-center space-y-6">
                <p class="font-bold text-slate-700">امضای نماینده / مدیریت ساختمان:</p>
                <div class="h-4"></div>
            </div>
        </div>

    </div>

</body>
</html>