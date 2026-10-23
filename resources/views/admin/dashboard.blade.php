<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد مدیریت ارشد | service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 min-h-screen pb-16" 
      x-data="{ 
          activeSection: localStorage.getItem('admin_active_section') || 'approvals', 
          showNewLocation: false, 
          showNewTech: false, 
          showNewCompany: false,
          pendingBadgeCount: {{ $stats['pending_count'] }}
      }"
      x-init="$watch('activeSection', val => localStorage.setItem('admin_active_section', val))">

    <!-- نوار مدیریت بالا -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-3">
            <span class="text-xl font-black text-amber-500 tracking-wider">service123</span>
            <span class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2.5 py-0.5 rounded-full font-bold">پنل مدیریت ارشد</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.accounting') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg transition">📊 دفتر معین حسابداری</a>

            <button type="button" @click="activeSection = 'companies'"
                :class="activeSection === 'companies' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 hover:bg-slate-700 text-amber-400 border border-amber-500/30'"
                class="text-xs px-3 py-1.5 rounded-lg transition cursor-pointer">
                🏢 مدیریت سربرگ شرکت‌ها
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white px-3 py-1.5 rounded-lg transition cursor-pointer">خروج</button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6">

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

        <!-- کارت‌های تب‌ساز ۴ گانه -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- تب ۱: کارتابل تاییدات -->
            <div @click="activeSection = 'approvals'"
                :class="activeSection === 'approvals' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-slate-900/90' : 'border-slate-800 bg-slate-900 hover:border-slate-700'"
                class="border p-4 rounded-2xl flex items-center justify-between transition cursor-pointer">
                <div>
                    <p class="text-xs text-slate-400">در انتظار بررسی</p>
                    <p class="text-2xl font-black text-amber-500 mt-1" x-text="pendingBadgeCount"></p>
                </div>
                <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl text-lg">⏳</div>
            </div>

            <!-- تب ۲: ساختمان‌ها و بلوک‌ها -->
            <div @click="activeSection = 'buildings'"
                :class="activeSection === 'buildings' ? 'border-blue-500 ring-2 ring-blue-500/20 bg-slate-900/90' : 'border-slate-800 bg-slate-900 hover:border-slate-700'"
                class="border p-4 rounded-2xl flex items-center justify-between transition cursor-pointer">
                <div>
                    <p class="text-xs text-slate-400">ساختمان‌ها و بلوک‌ها</p>
                    <p class="text-2xl font-black text-slate-200 mt-1">{{ $stats['total_buildings'] }}</p>
                </div>
                <div class="p-3 bg-blue-500/10 text-blue-400 rounded-xl text-lg">🏢</div>
            </div>

            <!-- تب ۳: پرسنل و تکنسین‌ها -->
            <div @click="activeSection = 'technicians'"
                :class="activeSection === 'technicians' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-slate-900/90' : 'border-slate-800 bg-slate-900 hover:border-slate-700'"
                class="border p-4 rounded-2xl flex items-center justify-between transition cursor-pointer">
                <div>
                    <p class="text-xs text-slate-400">پرسنل و تکنسین‌ها</p>
                    <p class="text-2xl font-black text-slate-200 mt-1">{{ $stats['total_technicians'] }}</p>
                </div>
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl text-lg">👷</div>
            </div>

            <!-- تب ۴: سربرگ شرکت‌ها -->
            <div @click="activeSection = 'companies'"
                :class="activeSection === 'companies' ? 'border-purple-500 ring-2 ring-purple-500/20 bg-slate-900/90' : 'border-slate-800 bg-slate-900 hover:border-slate-700'"
                class="border p-4 rounded-2xl flex items-center justify-between transition cursor-pointer">
                <div>
                    <p class="text-xs text-slate-400">سربرگ شرکت‌ها</p>
                    <p class="text-2xl font-black text-purple-400 mt-1">{{ $companies->count() }}</p>
                </div>
                <div class="p-3 bg-purple-500/10 text-purple-400 rounded-xl text-lg">📜</div>
            </div>
        </div>

        <!-- ==================== بخش ۱: کارتابل تاییدات پرسنل ==================== -->
        <section x-show="activeSection === 'approvals'" class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-sm font-bold text-amber-400 flex items-center gap-2">
                    <span>📋 کارتابل تاییدات پرسنل</span>
                    <span class="bg-amber-500/20 text-amber-300 text-[11px] px-2.5 py-0.5 rounded-full font-bold">
                        <span x-text="pendingBadgeCount"></span> مورد نیازمند تایید
                    </span>
                </h2>
            </div>

            <div class="space-y-3">
                @forelse ($pendingApprovals as $item)
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-3 transition-all duration-300" 
                     x-data="{ 
                         showEdit: false, 
         shareType: 'none', 
         totalAmount: {{ $item->amount ?? 0 }}, 
         shareVal: 0,
         isProcessing: false,
         isRemoved: false,
         sendApproval(form) {
             if(this.isProcessing) return;
             this.isProcessing = true;
             
             const formData = new FormData(form);
             
             fetch(form.action, {
                 method: 'POST',
                 body: formData,
                 headers: {
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json'
                 }
             })
             .then(response => {
                 // حتی اگر لاراول ریدایرکت بفرستد، چون عملیات با موفقیت در دیتابیس ثبت شده کارت را حذف می‌کنیم
                 this.isRemoved = true;
                 if (typeof pendingBadgeCount !== 'undefined' && pendingBadgeCount > 0) {
                     pendingBadgeCount--;
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('خطا در ثبت اطلاعات.');
             })
             .finally(() => {
                 this.isProcessing = false;
             });
         }
     }"
                     x-show="!isRemoved"
                     x-transition:leave="transition ease-in duration-200 transform opacity-0 -translate-y-2">
                    
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs bg-slate-900 text-amber-400 font-bold px-2 py-0.5 rounded border border-slate-800">
                                    {{ $item->building->location->title ?? '' }} - {{ $item->building->title ?? '-' }}
                                    @if($item->building_unit) ({{ $item->building_unit }}) @endif
                                </span>
                                <span class="text-[11px] text-slate-400">ثبت‌شده توسط: <strong class="text-slate-200">{{ $item->user->name ?? 'سیستم' }}</strong></span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ function_exists('jdate') ? jdate($item->created_at)->format('Y/m/d - H:i') : $item->created_at->format('Y/m/d') }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-100">{{ $item->title }}</h3>

                            @if($item->part_name)
                            <p class="text-xs text-amber-300/90 bg-amber-500/10 border border-amber-500/20 px-2 py-1 rounded inline-block mt-1">
                                ⚙ قطعه مصرفی: <strong>{{ $item->part_name }}</strong> (مبلغ: {{ number_format($item->part_price) }} تومان)
                            </p>
                            @endif

                            @if($item->technician_note)
                            <div class="text-[11px] text-slate-400 bg-slate-900/90 p-2 rounded border-r-2 border-amber-500 mt-1">
                                <span class="text-amber-400 font-bold">گزارش تکنسین:</span> {{ $item->technician_note }}
                            </div>
                            @endif
                        </div>

                        <div class="text-left">
                            <span class="text-[10px] text-slate-400 block">مبلغ اعلامی:</span>
                            <span class="text-base font-black text-emerald-400 font-mono">{{ number_format($item->amount) }} تومان</span>
                        </div>
                    </div>

                   <!-- دکمه‌های عملیات سریع بدون فرم و بدون امکان رفرش صفحه -->
<div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-800/80">
    <button type="button" 
            onclick="handleQuickApprove(this, '{{ route('admin.breakdown.approve', $item->id) }}')" 
            class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition flex items-center gap-1 cursor-pointer">
        ✔ تایید همین مبلغ
    </button>

    <button type="button" @click="showEdit = !showEdit" class="bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1 cursor-pointer">
        ✏ <span x-text="showEdit ? 'بستن فرم ویرایش' : 'ویرایش مبلغ و پورسانت'"></span>
    </button>

    <button type="button" 
            onclick="if(confirm('تبدیل به سرویس رایگان؟')) handleQuickApprove(this, '{{ route('admin.breakdown.reject_cost', $item->id) }}')" 
            class="bg-slate-800 hover:bg-rose-900/60 text-rose-400 text-xs px-3 py-1.5 rounded-lg transition cursor-pointer">
        ✖ تبدیل به رایگان
    </button>
</div>

                    <!-- فرم کشویی ویرایش مبلغ -->
                    <form x-show="showEdit" x-cloak action="{{ route('admin.breakdown.update_approve', $item->id) }}" method="POST" class="bg-slate-900 border border-amber-500/30 p-3.5 rounded-xl space-y-3 mt-2">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">مبلغ کارشناسی کل (تومان):</label>
                                <input type="number" name="amount" x-model.number="totalAmount" value="{{ $item->amount }}" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">نام قطعه مصرفی:</label>
                                <input type="text" name="part_name" value="{{ $item->part_name }}" placeholder="اختیاری" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">قیمت قطعه (تومان):</label>
                                <input type="number" name="part_price" value="{{ $item->part_price }}" placeholder="0" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                            </div>
                        </div>

                        <!-- سهم تکنسین -->
                        <div class="bg-slate-950 p-2.5 rounded-lg border border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1">پورسانت تکنسین:</label>
                                <select name="technician_share_type" x-model="shareType" class="w-full bg-slate-900 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                    <option value="none">بدون سهم مستقیم</option>
                                    <option value="percentage">درصدی از کل مبلغ (%)</option>
                                    <option value="fixed">مبلغ ثابت (تومان)</option>
                                </select>
                            </div>
                            <div x-show="shareType !== 'none'">
                                <label class="block text-[10px] text-slate-400 mb-1" x-text="shareType === 'percentage' ? 'درصد سهم:' : 'مبلغ سهم (تومان):'"></label>
                                <input type="number" name="technician_share_value" x-model.number="shareVal" placeholder="0" class="w-full bg-slate-900 border border-slate-700 rounded px-2 py-1 text-xs text-amber-400 font-bold">
                            </div>
                            <div x-show="shareType !== 'none'" class="flex flex-col justify-end">
                                <span class="text-[10px] text-slate-500">سهم دریافتی پرسنل:</span>
                                <span class="text-xs font-bold text-emerald-400 mt-0.5">
                                    <span x-text="shareType === 'percentage' ? Math.round((totalAmount * shareVal) / 100).toLocaleString() : Number(shareVal).toLocaleString()"></span> تومان
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-400 mb-1">یادداشت مدیر (اختیاری):</label>
                            <input type="text" name="admin_note" value="{{ $item->admin_note }}" placeholder="علت تغییر مبلغ..." class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                        </div>

                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-1.5 rounded-lg text-xs transition cursor-pointer">
                            ثبت تغییرات و تایید نهایی
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-8 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                    هیچ تسک در انتظار تاییدی وجود ندارد؛ همه‌چیز به‌روز است.
                </div>
                @endforelse
            </div>
        </section>

        <!-- ==================== بخش ۲: ساختمان‌ها و بلوک‌ها ==================== -->
        <section x-show="activeSection === 'buildings'" x-cloak class="space-y-4">
            
            <!-- نوار ابزار جستجو و ایجاد بلوک جدید -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900 border border-slate-800 p-4 rounded-2xl"
                 x-data="{ searchBlock: '' }">
                <div class="flex items-center gap-3 flex-1 max-w-md">
                    <div class="relative w-full">
                        <input type="text" 
                               x-model="searchBlock" 
                               @input="$dispatch('filter-blocks', searchBlock.toLowerCase().trim())"
                               placeholder="🔍 جستجوی سریع نام بلوک، ورودی یا کد..." 
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-amber-500 focus:outline-none">
                        <button type="button" 
                                x-show="searchBlock" 
                                @click="searchBlock = ''; $dispatch('filter-blocks', '')" 
                                class="absolute left-2.5 top-2 text-slate-400 hover:text-white text-xs">✕</button>
                    </div>
                    <button type="button" 
                            @click="$dispatch('toggle-all-blocks')" 
                            class="whitespace-nowrap text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 px-3 py-2 rounded-xl transition cursor-pointer">
                        ↕ باز / بستن همه
                    </button>
                </div>

                <button @click="showNewLocation = !showNewLocation" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2 rounded-xl text-xs transition shadow cursor-pointer">
                    ➕ تعریف بلوک / محدوده جدید
                </button>
            </div>

            <!-- فرم ثبت بلوک جدید -->
            <div x-show="showNewLocation" x-cloak class="bg-slate-900 border border-amber-500/40 rounded-2xl p-5 shadow-xl space-y-3">
                <h2 class="text-xs font-bold text-amber-400 border-b border-slate-800 pb-2">ثبت مشخصات بلوک یا محدوده کلی</h2>
                <form action="{{ route('admin.location.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">نام بلوک یا محدوده:</label>
                            <input type="text" name="title" required placeholder="مثال: بلوک A4 یا اکباتان فاز ۱" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">کد شناسایی (اختیاری):</label>
                            <input type="text" name="code" placeholder="مثال: A4" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">آدرس پایه محدوده:</label>
                        <input type="text" name="address" placeholder="مثال: شهرک اکباتان، فاز ۱" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2 rounded-xl text-xs transition cursor-pointer">
                        ثبت بلوک
                    </button>
                </form>
            </div>

            <!-- لیست درختی بلوک‌ها و ساختمان‌ها به شکل کشویی و قابل جستجو -->
            <div class="space-y-3"
                 x-data="{ 
                     filterQuery: '', 
                     allOpen: false 
                 }" 
                 @filter-blocks.window="filterQuery = $event.detail" 
                 @toggle-all-blocks.window="allOpen = !allOpen">

                @forelse ($locations as $loc)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition shadow-sm"
                     x-data="{ 
                         isOpen: localStorage.getItem('block_open_{{ $loc->id }}') === 'true', 
         newBuilding: false, 
         editAccess: false, 
         editLoc: false,
         searchTarget: '{{ strtolower($loc->title) }} {{ strtolower($loc->code) }} {{ strtolower($loc->buildings->pluck('title')->implode(' ')) }}'
     }"
                     x-show="filterQuery === '' || searchTarget.includes(filterQuery)"
                     x-effect="isOpen = allOpen">

                    <!-- هدر کشو -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-900/90 hover:bg-slate-800/40 cursor-pointer select-none border-b border-slate-800/80 transition"
                         @click="isOpen = !isOpen">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 transition-transform duration-200" :class="isOpen ? 'rotate-90 text-amber-400 font-bold' : ''">◀</span>
                            <span class="text-xs bg-amber-500/20 text-amber-400 font-bold px-2 py-0.5 rounded">محدوده</span>
                            <h3 class="text-sm font-black text-slate-100">{{ $loc->title }}</h3>
                            @if($loc->code)
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">{{ $loc->code }}</span>
                            @endif
                            <span class="text-[11px] text-slate-400">({{ $loc->buildings->count() }} ورودی)</span>
                        </div>

                        <!-- دکمه‌های عملیاتی -->
                        <div class="flex items-center gap-1.5 text-xs" @click.stop>
                            <button type="button" @click="newBuilding = !newBuilding; isOpen = true" class="bg-emerald-600/80 hover:bg-emerald-600 text-white px-2.5 py-1 rounded-lg transition cursor-pointer">➕ افزودن ورودی</button>
                            <a href="{{ route('admin.report.block', $loc->id) }}" target="_blank" class="bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 font-bold px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                🖨 کاربرگ و سربرگ A4
                            </a>
                            <button type="button" @click="editAccess = !editAccess; isOpen = true" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-2.5 py-1 rounded-lg transition cursor-pointer">👷 دسترسی پرسنل ({{ $loc->users->count() }})</button>
                            <button type="button" @click="editLoc = !editLoc; isOpen = true" class="bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1 rounded-lg transition cursor-pointer">✏</button>
                            <form action="{{ route('admin.location.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('حذف بلوک؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-slate-800 hover:bg-rose-900/60 text-rose-400 px-2 py-1 rounded-lg transition cursor-pointer">🗑</button>
                            </form>
                        </div>
                    </div>

                    <!-- محتوای داخل کشوی هر بلوک -->
                    <div x-show="isOpen" x-cloak x-transition.opacity class="p-4 space-y-4 bg-slate-950/40">

                        <!-- ویرایش مشخصات بلوک -->
                        <form x-show="editLoc" x-cloak action="{{ route('admin.location.update', $loc->id) }}" method="POST" class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-2">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" name="title" value="{{ $loc->title }}" required class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                                <input type="text" name="code" value="{{ $loc->code }}" placeholder="کد" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            </div>
                            <input type="text" name="address" value="{{ $loc->address }}" placeholder="آدرس پایه محدوده" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            <button type="submit" class="bg-amber-500 text-slate-950 font-bold py-1 px-3 rounded-lg text-xs cursor-pointer">ذخیره ویرایش</button>
                        </form>

                        <!-- تعیین دسترسی پرسنل -->
                        <form x-show="editAccess" x-cloak action="{{ route('admin.location.access', $loc->id) }}" method="POST" class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 space-y-2.5">
                            @csrf
                            <p class="text-[11px] text-slate-400 font-bold">انتخاب تکنسین‌های مجاز برای این بلوک:</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($technicians as $tech)
                                <label class="flex items-center gap-1.5 bg-slate-900 border border-slate-700 px-2.5 py-1.5 rounded-lg text-xs cursor-pointer">
                                    <input type="checkbox" name="technicians[]" value="{{ $tech->id }}" {{ $loc->users->contains($tech->id) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-0">
                                    <span class="text-slate-200">{{ $tech->name }} ({{ $tech->phone }})</span>
                                </label>
                                @empty
                                <p class="text-[11px] text-rose-400">هنوز پرسنلی در سیستم تعریف نشده است.</p>
                                @endforelse
                            </div>
                            <button type="submit" class="bg-amber-500 text-slate-950 font-bold py-1 px-4 rounded-lg text-xs mt-1 cursor-pointer">ثبت دسترسی‌ها</button>
                        </form>

                        <!-- فرم افزودن ورودی جدید -->
                        <form x-show="newBuilding" x-cloak action="{{ route('admin.building.store') }}" method="POST" class="bg-slate-950 p-3.5 rounded-xl border border-emerald-500/30 space-y-2.5" x-data="{ hasFee: false, hasCustomAddress: false }">
                            @csrf
                            <input type="hidden" name="location_id" value="{{ $loc->id }}">
                            <p class="text-[11px] font-bold text-emerald-400">افزودن ورودی / ساختمان جدید:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" name="title" required placeholder="عنوان ورودی (مثال: ورودی ۲ یا ساختمان الماس)" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                                <input type="text" name="code" placeholder="پلاک یا کد اختصاصی" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            </div>

                            <div class="space-y-1.5 pt-1 border-t border-slate-800/60">
                                <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                                    <input type="checkbox" x-model="hasCustomAddress" class="rounded text-amber-500 focus:ring-0">
                                    <span>نیاز به آدرس اختصاصی / تکمیلی دارد</span>
                                </label>
                                <div x-show="hasCustomAddress" x-cloak>
                                    <input type="text" name="address" placeholder="ادامه آدرس دقیق ساختمان..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-1 border-t border-slate-800/60">
                                <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="has_monthly_fee" x-model="hasFee" class="rounded text-amber-500 focus:ring-0">
                                    <span>شارژ ماهانه دارد</span>
                                </label>
                                <div x-show="hasFee">
                                    <input type="number" name="monthly_fee" placeholder="مبلغ شارژ (تومان)" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-emerald-400 font-bold">
                                </div>
                            </div>

                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1 px-4 rounded-lg text-xs cursor-pointer">ثبت ورودی</button>
                        </form>

                        <!-- لیست ورودی‌های زیرمجموعه -->
                        <div class="space-y-3 pt-1">
                            @forelse ($loc->buildings as $b)
                            <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-3.5 space-y-3" x-data="{ addContact: false, editBuilding: false, editExclusions: false }">
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800/60 pb-2">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-amber-300">🏢 {{ $b->title }}</span>
                                            @if($b->code)
                                            <span class="text-[10px] bg-slate-900 text-slate-400 px-1.5 py-0.5 rounded font-mono">{{ $b->code }}</span>
                                            @endif
                                            @if($b->has_monthly_fee)
                                            <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded font-bold">
                                                شارژ: {{ number_format($b->monthly_fee) }} تومان
                                            </span>
                                            @endif
                                        </div>
                                        @if($b->address)
                                        <p class="text-[10px] text-slate-400">📍 {{ $b->location?->address ? $b->location->address.'، ' : '' }}{{ $b->address }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 text-[11px]">
                                        <a href="{{ route('admin.building.invoice.a5', $b->id) }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 bg-slate-900 px-2 py-0.5 rounded border border-emerald-500/20 flex items-center gap-1 cursor-pointer">
                                            🧾 فاکتور A5
                                        </a>
                                        <button @click="editExclusions = !editExclusions" class="text-rose-400 hover:text-rose-300 bg-slate-900 px-2 py-0.5 rounded border border-rose-500/20 cursor-pointer">🚫 محدودیت پرسنل</button>
                                        <button @click="addContact = !addContact" class="text-slate-300 hover:text-white bg-slate-900 px-2 py-0.5 rounded cursor-pointer">📞 ثبت مدیر</button>
                                        <button @click="editBuilding = !editBuilding" class="text-amber-400 hover:text-amber-300 bg-slate-900 px-2 py-0.5 rounded cursor-pointer">✏</button>
                                        <form action="{{ route('admin.building.destroy', $b->id) }}" method="POST" onsubmit="return confirm('حذف ورودی؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 bg-slate-900 px-2 py-0.5 rounded cursor-pointer">🗑</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- فرم محدودیت پرسنل -->
                                <form x-show="editExclusions" x-cloak action="{{ route('admin.building.exclusions', $b->id) }}" method="POST" class="bg-slate-900 p-3 rounded-lg border border-rose-500/30 space-y-2">
                                    @csrf
                                    <p class="text-[11px] text-rose-300 font-bold">پرسنلی که نباید این ورودی را ببینند تیک بزنید:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($loc->users as $locTech)
                                        @php
                                        $isExcluded = $b->users()->where('users.id', $locTech->id)->wherePivot('is_excluded', true)->exists();
                                        @endphp
                                        <label class="flex items-center gap-1.5 bg-slate-950 border border-slate-700 px-2.5 py-1 rounded text-xs cursor-pointer">
                                            <input type="checkbox" name="excluded_technicians[]" value="{{ $locTech->id }}" {{ $isExcluded ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-0">
                                            <span class="text-slate-300">{{ $locTech->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="bg-rose-600 hover:bg-rose-500 text-white font-bold px-3 py-1 rounded text-xs cursor-pointer">ذخیره استثنائات</button>
                                </form>

                                <!-- ویرایش ورودی -->
                                <form x-show="editBuilding" x-cloak action="{{ route('admin.building.update', $b->id) }}" method="POST" class="bg-slate-900 p-2.5 rounded-lg border border-slate-700 space-y-2" x-data="{ fee: {{ $b->has_monthly_fee ? 'true' : 'false' }}, hasCustomAddress: {{ $b->address ? 'true' : 'false' }} }">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <input type="text" name="title" value="{{ $b->title }}" required class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                        <input type="text" name="code" value="{{ $b->code }}" placeholder="پلاک/کد" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                    </div>
                                    <div class="space-y-1 pt-1 border-t border-slate-800/60">
                                        <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                                            <input type="checkbox" x-model="hasCustomAddress" class="rounded text-amber-500">
                                            <span>آدرس تکمیلی اختصاصی</span>
                                        </label>
                                        <div x-show="hasCustomAddress" x-cloak>
                                            <input type="text" name="address" value="{{ $b->address }}" placeholder="آدرس دقیق ورودی" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 pt-1 border-t border-slate-800/60">
                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" name="has_monthly_fee" x-model="fee" class="rounded text-amber-500">
                                            <span>شارژ ماهانه</span>
                                        </label>
                                        <div x-show="fee">
                                            <input type="number" name="monthly_fee" value="{{ $b->monthly_fee }}" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-emerald-400 font-bold">
                                        </div>
                                    </div>
                                    <button type="submit" class="bg-amber-500 text-slate-950 font-bold px-3 py-1 rounded text-xs cursor-pointer">ذخیره</button>
                                </form>

                                <!-- مخاطبان ورودی -->
                                <div class="space-y-1">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        @forelse ($b->contacts as $contact)
                                        <div class="bg-slate-900 border border-slate-800 p-2 rounded-lg flex items-center justify-between text-xs">
                                            <div>
                                                <p class="font-bold text-slate-200">{{ $contact->name }}</p>
                                                <span class="text-[10px] text-amber-400/80">{{ $contact->role_title }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="tel:{{ $contact->phone }}" class="text-emerald-400 font-mono text-[11px]">{{ $contact->phone }}</a>
                                                <form action="{{ route('admin.contact.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-500 hover:text-rose-400 cursor-pointer">✕</button>
                                                </form>
                                            </div>
                                        </div>
                                        @empty
                                        <p class="text-[11px] text-slate-500 col-span-3">مشخصات مدیری برای این ورودی ثبت نشده است.</p>
                                        @endforelse
                                    </div>

                                    <form x-show="addContact" x-cloak action="{{ route('admin.building.contact.store', $b->id) }}" method="POST" class="bg-slate-900 p-2.5 rounded-lg border border-slate-700 space-y-2 mt-2">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <input type="text" name="name" required placeholder="نام مدیر" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                            <input type="text" name="role_title" required placeholder="سمت (مدیر، سرایدار)" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                                            <input type="text" name="phone" required placeholder="تلفن تماس" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200 text-left">
                                        </div>
                                        <button type="submit" class="bg-emerald-600 text-white font-bold px-3 py-1 rounded text-xs cursor-pointer">ثبت مدیر</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <p class="text-xs text-slate-500 p-2 bg-slate-950 rounded-lg">هنوز ورودی تعریف نشده است.</p>
                            @endforelse
                        </div>

                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-xs text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                    هیچ بلوکی تعریف نشده است.
                </div>
                @endforelse
            </div>
        </section>

        <!-- ==================== بخش ۳: پرسنل و تکنسین‌ها ==================== -->
        <section x-show="activeSection === 'technicians'" x-cloak class="space-y-5">
            <div class="flex justify-between items-center bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                <div>
                    <h2 class="text-sm font-bold text-slate-200">لیست پرسنل و تکنسین‌ها</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">مشاهده پرونده‌ها، سپردن کار، تاریخچه و وضعیت تسویه‌حساب مالی</p>
                </div>
                <button @click="showNewTech = !showNewTech" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2 rounded-xl text-xs transition shadow cursor-pointer">
                    ➕ تعریف پرسنل جدید
                </button>
            </div>

            <!-- فرم ثبت پرسنل جدید -->
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
                            <label class="block text-[11px] text-slate-400 mb-1">شماره همراه اصلی:</label>
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

                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2 rounded-xl text-xs transition cursor-pointer">
                        ثبت نهایی پرونده پرسنل
                    </button>
                </form>
            </div>

            <!-- کارت‌های پرسنل -->
            <div class="space-y-6">
                @forelse ($technicians as $tech)
                @php
                $totalEarned = $tech->breakdowns->where('approval_status', 'approved')->sum('technician_share_amount') + ($tech->base_salary ?? 0);
                $totalPaid = $tech->payouts->sum('amount');
                $balance = $totalEarned - $totalPaid;
                @endphp

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-4" x-data="{ activeTab: 'profile' }">
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
                                <p class="text-[11px] text-slate-400 mt-0.5">ثبت‌شده از تاریخ: {{ function_exists('jdate') ? jdate($tech->created_at)->format('Y/m/d') : $tech->created_at->format('Y/m/d') }}</p>
                            </div>
                        </div>

                        <!-- تب‌های داخلی تکنسین -->
                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 text-xs">
                            <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition cursor-pointer">👤 پرونده</button>
                            <button @click="activeTab = 'assign'" :class="activeTab === 'assign' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition cursor-pointer">➕ سپردن کار</button>
                            <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition cursor-pointer">📋 تاریخچه کارهـا</button>
                            <button @click="activeTab = 'finance'" :class="activeTab === 'finance' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2.5 py-1 rounded-lg transition cursor-pointer">💰 وضعیت مالی</button>
                        </div>
                    </div>

                    <!-- تب ۱ پرسنل: پرونده -->
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
                                <a href="tel:{{ $tech->phone }}" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] px-2.5 py-1 rounded-lg transition flex items-center gap-1">📞 تماس</a>
                            </div>

                            <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-500 block">شماره همراه دوم:</span>
                                    <span class="text-xs font-mono text-slate-300">{{ $tech->secondary_phone ?? 'ثبت نشده' }}</span>
                                </div>
                                @if($tech->secondary_phone)
                                <a href="tel:{{ $tech->secondary_phone }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] px-2 py-1 rounded-lg transition">📞 تماس</a>
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
                                <span class="bg-slate-900 border border-slate-700 text-slate-300 text-[11px] px-2 py-0.5 rounded-md">🏢 {{ $loc->title }}</span>
                                @empty
                                <span class="text-[11px] text-slate-500 italic">هنوز به هیچ بلوکی اختصاص داده نشده است.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex justify-end pt-1">
                            <form action="{{ route('admin.technician.toggle', $tech->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs {{ $tech->is_active ? 'bg-rose-900/40 text-rose-300 hover:bg-rose-900/60' : 'bg-emerald-900/40 text-emerald-300 hover:bg-emerald-900/60' }} px-3 py-1.5 rounded-lg border border-slate-700 transition cursor-pointer">
                                    {{ $tech->is_active ? '🚫 معلق کردن حساب کاربری' : '✔ فعال‌سازی مجدد حساب' }}
                                </button>
                            </form>
                        </div>

                        <!-- مودال ویرایش پرونده -->
                        <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                            <div @click.away="openEditModal = false" class="bg-slate-900 border border-amber-500/50 rounded-2xl w-full max-w-2xl p-5 space-y-4 shadow-2xl">
                                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5">
                                    <h3 class="text-xs font-black text-amber-400">ویرایش پرونده {{ $tech->name }}</h3>
                                    <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-white text-base cursor-pointer">✕</button>
                                </div>
                                <form action="{{ route('admin.technician.update', $tech->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">نام و نام خانوادگی:</label>
                                            <input type="text" name="name" value="{{ $tech->name }}" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">شماره همراه اصلی:</label>
                                            <input type="text" name="phone" value="{{ $tech->phone }}" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">تغییر رمز عبور:</label>
                                            <input type="text" name="password" placeholder="اختیاری" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">کد ملی:</label>
                                            <input type="text" name="national_code" value="{{ $tech->national_code }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">شماره همراه دوم:</label>
                                            <input type="text" name="secondary_phone" value="{{ $tech->secondary_phone }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-400 mb-1">تاریخ تولد:</label>
                                            <input type="text" name="birth_date" value="{{ $tech->birth_date }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-center text-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">حقوق ثابت ماهانه (تومان):</label>
                                        <input type="number" name="base_salary" value="{{ $tech->base_salary }}" class="w-full sm:w-1/2 bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-400 font-bold">
                                    </div>

                                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                                        <button type="button" @click="openEditModal = false" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-3.5 py-1.5 rounded-lg text-xs cursor-pointer">انصراف</button>
                                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-1.5 rounded-lg text-xs cursor-pointer">ذخیره تغییرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- تب ۲ پرسنل: سپردن کار -->
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

                            <!-- کادر پورسانت -->
                            <div class="bg-slate-900/70 border border-slate-800 p-3 rounded-xl space-y-2.5">
                                <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                    <input type="checkbox" x-model="hasShare" class="rounded text-amber-500 focus:ring-0">
                                    <span class="font-bold text-amber-400">تکنسین از این کار پورسانت / اجرت مستقیم دریافت می‌کند</span>
                                </label>
                                <div x-show="hasShare" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-slate-800">
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">روش محاسبه سهم:</label>
                                        <select name="technician_share_type" x-model="shareType" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-slate-200">
                                            <option value="percentage">درصدی از فاکتور (%)</option>
                                            <option value="fixed">مبلغ مقطوع (تومان)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1" x-text="shareType === 'percentage' ? 'درصد سهم تکنسین:' : 'مبلغ اجرت تکنسین (تومان):'"></label>
                                        <input type="number" name="technician_share_value" x-model.number="shareVal" placeholder="0" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-400 font-bold">
                                    </div>
                                    <div class="flex flex-col justify-end">
                                        <span class="text-[10px] text-slate-500">سهم محاسبه‌شده پرسنل:</span>
                                        <span class="text-xs font-bold text-emerald-400 mt-1">
                                            <span x-text="shareType === 'percentage' ? Math.round((invoiceAmount * shareVal) / 100).toLocaleString() : Number(shareVal).toLocaleString()"></span> تومان
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition cursor-pointer">
                                ارجاع به پنل تکنسین
                            </button>
                        </form>
                    </div>

                    <!-- تب ۳ پرسنل: تاریخچه -->
                    <div x-show="activeTab === 'history'" class="space-y-2">
                        <div class="max-h-80 overflow-y-auto space-y-2.5 pr-1">
                            @forelse ($tech->breakdowns()->latest()->get() as $task)
        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-2 text-xs" 
             x-data="{ 
                 openEditTask: false,
                 editAmount: {{ $task->amount ?? 0 }},
                 shareType: '{{ $task->technician_share_type ?? 'none' }}',
                 shareVal: {{ $task->technician_share_value ?? 0 }}
             }">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-100">{{ $task->title }}</span>
                        <span class="text-[10px] bg-slate-900 text-amber-400 px-1.5 py-0.5 rounded border border-slate-800">{{ $task->building->title ?? '-' }}</span>
                        @if($task->building_unit)
                        <span class="text-[10px] text-slate-400">({{ $task->building_unit }})</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">{{ function_exists('jdate') ? jdate($task->created_at)->format('Y/m/d - H:i') : $task->created_at->format('Y/m/d') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-left">
                        <span class="text-xs font-bold {{ $task->approval_status === 'approved' ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $task->is_billable ? number_format($task->amount).' تومان' : 'سرویس عادی' }}
                        </span>
                        @if($task->technician_share_amount > 0)
                        <span class="block text-[10px] text-amber-400 font-bold">سهم تکنسین: {{ number_format($task->technician_share_amount) }} ت</span>
                        @endif
                        <span class="block text-[10px] text-slate-500 mt-0.5">{{ $task->approval_status === 'approved' ? '✔ تایید شده' : '⏳ در انتظار تایید' }}</span>
                    </div>

                    <!-- دکمه‌های ویرایش و حذف تسک ارجاعی -->
                    <div class="flex items-center gap-1 border-r border-slate-800 pr-2">
                        <button type="button" @click="openEditTask = !openEditTask" class="text-amber-400 hover:text-amber-300 bg-slate-900 px-2 py-1 rounded text-xs cursor-pointer" title="ویرایش تسک">
                            ✏
                        </button>
                        <form action="{{ route('admin.technician.task.destroy', $task->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این کار ارجاعی اطمینان دارید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:text-rose-300 bg-slate-900 px-2 py-1 rounded text-xs cursor-pointer" title="حذف تسک">
                                🗑
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($task->technician_note)
            <div class="bg-slate-900/90 border-r-2 border-amber-500 p-2 rounded-lg text-[11px] text-slate-300">
                <span class="text-amber-400 font-bold block text-[10px] mb-0.5">🛠 گزارش تکنسین:</span>
                <p class="leading-relaxed">{{ $task->technician_note }}</p>
            </div>
            @endif

            <!-- فرم بازشونده ویرایش تسک -->
            <form x-show="openEditTask" x-cloak action="{{ route('admin.technician.task.update', $task->id) }}" method="POST" class="bg-slate-900 border border-amber-500/30 p-3 rounded-xl space-y-2.5 mt-2">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">ورودی / ساختمان هدف:</label>
                        <select name="building_id" required class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1.5 text-xs text-slate-200">
                            @foreach ($locations as $loc)
                            <optgroup label="محدوده {{ $loc->title }}">
                                @foreach ($loc->buildings as $b)
                                <option value="{{ $b->id }}" {{ $task->building_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->title }} ({{ $loc->title }})
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">واحد / آسانسور:</label>
                        <input type="text" name="building_unit" value="{{ $task->building_unit }}" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] text-slate-400 mb-1">شرح عملیات ارجاعی:</label>
                        <input type="text" name="title" value="{{ $task->title }}" required class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">مبلغ فاکتور (تومان):</label>
                        <input type="number" name="amount" x-model.number="editAmount" value="{{ $task->amount }}" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-emerald-400 font-bold">
                    </div>
                </div>

                <!-- تنظیمات سهم و پورسانت تکنسین -->
                <div class="bg-slate-950 p-2.5 rounded-lg border border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-0.5">نوع سهم تکنسین:</label>
                        <select name="technician_share_type" x-model="shareType" class="w-full bg-slate-900 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
                            <option value="none">بدون سهم</option>
                            <option value="percentage">درصدی (%)</option>
                            <option value="fixed">مبلغ مقطوع (تومان)</option>
                        </select>
                    </div>
                    <div x-show="shareType !== 'none'">
                        <label class="block text-[10px] text-slate-400 mb-0.5" x-text="shareType === 'percentage' ? 'درصد سهم:' : 'مبلغ سهم (تومان):'"></label>
                        <input type="number" name="technician_share_value" x-model.number="shareVal" value="{{ $task->technician_share_value }}" class="w-full bg-slate-900 border border-slate-700 rounded px-2 py-1 text-xs text-amber-400 font-bold">
                    </div>
                    <div x-show="shareType !== 'none'" class="flex flex-col justify-end">
                        <span class="text-[10px] text-slate-500">سهم پرسنل:</span>
                        <span class="text-xs font-bold text-emerald-400">
                            <span x-text="shareType === 'percentage' ? Math.round((editAmount * shareVal) / 100).toLocaleString() : Number(shareVal).toLocaleString()"></span> ت
                        </span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="openEditTask = false" class="bg-slate-800 text-slate-300 px-3 py-1 rounded text-xs cursor-pointer">انصراف</button>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-3 py-1 rounded text-xs cursor-pointer">ذخیره ویرایش کار</button>
                </div>
            </form>
        </div>
        @empty
        <p class="text-xs text-slate-500 p-3 text-center">هنوز هیچ فعالیتی ثبت نشده است.</p>
        @endforelse
                        </div>
                    </div>

                    <!-- تب ۴ پرسنل: مالی و تسویه -->
                    <div x-show="activeTab === 'finance'" class="space-y-4" x-data="{ showPayoutForm: false }">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl">
                                <span class="text-[10px] text-slate-400 block">کل مطالبات پرسنل:</span>
                                <span class="text-sm font-black text-slate-200 mt-1 block">{{ number_format($totalEarned) }} تومان</span>
                            </div>
                            <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl">
                                <span class="text-[10px] text-slate-400 block">مجموع واریزی‌ها:</span>
                                <span class="text-sm font-black text-emerald-400 mt-1 block">{{ number_format($totalPaid) }} تومان</span>
                            </div>
                            <div class="bg-slate-950 border {{ $balance > 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-emerald-500/50 bg-emerald-500/5' }} p-3 rounded-xl flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">مانده‌طلب تکنسین:</span>
                                    <span class="text-sm font-black {{ $balance > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1 block">
                                        {{ number_format(abs($balance)) }} تومان
                                        <span class="text-[10px] font-normal">({{ $balance > 0 ? 'طلبکار' : 'تسویه کامل' }})</span>
                                    </span>
                                </div>
                                <button type="button" @click="showPayoutForm = !showPayoutForm" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                                    💳 ثبت واریز
                                </button>
                            </div>
                        </div>

                        <!-- فرم ثبت فیش واریز -->
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
                                        <input type="text" name="paid_at" value="{{ function_exists('jdate') ? jdate()->format('Y/m/d') : now()->format('Y/m/d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-center text-slate-200 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">شماره پیگیری / فیش:</label>
                                        <input type="text" name="tracking_code" placeholder="کد ارجاع بانکی" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-left font-mono text-slate-200 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-1">بابت:</label>
                                        <select name="payment_type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                                            <option value="salary">حقوق ماهانه</option>
                                            <option value="commission">پورسانت و اجرت کارها</option>
                                            <option value="advance">مساعده / علی‌الحساب</option>
                                            <option value="settlement">تسویه‌حساب نهایی</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-1">توضیحات اختیاری:</label>
                                    <input type="text" name="description" placeholder="مثال: واریز از حساب سپه، بابت تسویه نیمه اول ماه" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none">
                                </div>
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition cursor-pointer">
                                    ثبت سند و کسر از مانده‌طلب
                                </button>
                            </form>
                        </div>

                        <!-- جدول واریزی‌ها -->
                        <div class="space-y-1.5">
                            <span class="text-[11px] font-bold text-slate-400 block">ریز اسناد پرداختی به پرسنل:</span>
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
                                            <td class="p-2 font-bold text-emerald-400">{{ number_format($payout->amount) }} تومان</td>
                                            <td class="p-2">
                                                @switch($payout->payment_type)
                                                @case('salary') <span class="text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded text-[10px]">حقوق ماهانه</span> @break
                                                @case('commission') <span class="text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded text-[10px]">پورسانت</span> @break
                                                @case('advance') <span class="text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded text-[10px]">مساعده</span> @break
                                                @default <span class="text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded text-[10px]">سایر</span>
                                                @endswitch
                                            </td>
                                            <td class="p-2 font-mono text-[11px] text-slate-400">{{ $payout->tracking_code ?? '-' }}</td>
                                            <td class="p-2 text-slate-400 text-[11px]">{{ $payout->description ?? '-' }}</td>
                                            <td class="p-2 text-center">
                                                <form action="{{ route('admin.technician.payout.destroy', $payout->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این سند واریزی اطمینان دارید؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-500 hover:text-rose-400 transition cursor-pointer">✕</button>
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
                    هیچ پرسنلی در سیستم تعریف نشده است.
                </div>
                @endforelse
            </div>
        </section>

        <!-- ==================== بخش ۴: مدیریت سربرگ‌ها و شرکت‌ها ==================== -->
        <section x-show="activeSection === 'companies'" x-cloak class="space-y-5">
            <div class="flex justify-between items-center bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                <div>
                    <h2 class="text-sm font-bold text-slate-200">سربرگ‌ها و مشخصات رسمی شرکت‌ها</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">تعریف شرکت جدید، شماره ثبت، شناسه ملی، شماره تماس و لوگوی رسمی</p>
                </div>
                <button @click="showNewCompany = !showNewCompany" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2 rounded-xl text-xs transition shadow cursor-pointer">
                    ➕ تعریف شرکت جدید
                </button>
            </div>

            <!-- فرم تعریف شرکت جدید -->
            <div x-show="showNewCompany" x-cloak class="bg-slate-900 border border-amber-500/40 rounded-2xl p-5 shadow-xl space-y-3">
                <h2 class="text-xs font-bold text-amber-400 border-b border-slate-800 pb-2">مشخصات ثبتی و لوگوی شرکت</h2>
                <form action="{{ route('admin.company.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">نام کامل ثبتی شرکت:</label>
                            <input type="text" name="name" required placeholder="مثال: شرکت فنی مهندسی آسان بالابر خاطره" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">نام مختصر / برند تجاری:</label>
                            <input type="text" name="brand" placeholder="مثال: آسان بالابر خاطره" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">شماره ثبت:</label>
                            <input type="text" name="reg_number" placeholder="مثال: ۴۵۸۹۲۰" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-left text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">شناسه ملی شرکت:</label>
                            <input type="text" name="national_id" placeholder="مثال: ۱۰۱۰۳۴۹۸۲۱" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-left text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">تلفن تماس دفتر:</label>
                            <input type="text" name="phone" placeholder="مثال: ۰۲۱-۴۴۲۲۰۰۱۱" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-left text-slate-200 focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">فایل لوگوی رسمی (PNG یا SVG با کیفیت):</label>
                        <input type="file" name="logo" accept="image/*" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-400 file:bg-slate-800 file:border-none file:text-slate-200 file:rounded-lg file:px-2.5 file:py-1 file:text-xs">
                    </div>

                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2 rounded-xl text-xs transition cursor-pointer">
                        ذخیره شرکت
                    </button>
                </form>
            </div>

            <!-- لیست شرکت‌ها -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($companies as $comp)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 space-y-3" x-data="{ editMode: false }">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            @if($comp->logo_path)
                            <img src="{{ asset('uploads/' . $comp->logo_path) }}" alt="Logo" class="w-12 h-12 rounded-xl object-contain bg-slate-950 p-1 border border-slate-800">
                            @else
                            <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-2xl">
                                {{ $comp->logo_icon }}
                            </div>
                            @endif
                            <div>
                                <h3 class="text-xs font-black text-slate-100">{{ $comp->name }}</h3>
                                <p class="text-[11px] text-amber-400 mt-0.5">برند: {{ $comp->brand }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="editMode = !editMode" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1 rounded-lg transition cursor-pointer">✏</button>
                            <form action="{{ route('admin.company.destroy', $comp->id) }}" method="POST" onsubmit="return confirm('حذف شرکت؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs bg-slate-800 hover:bg-rose-900/60 text-rose-400 px-2 py-1 rounded-lg transition cursor-pointer">🗑</button>
                            </form>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400 space-y-1">
                        <p>شماره ثبت: <strong class="text-slate-200 font-mono">{{ $comp->reg_number ?: '-' }}</strong></p>
                        <p>شناسه ملی: <strong class="text-slate-200 font-mono">{{ $comp->national_id ?: '-' }}</strong></p>
                        <p>تلفن: <strong class="text-slate-200 font-mono">{{ $comp->phone ?: '-' }}</strong></p>
                    </div>

                    <!-- فرم ویرایش بازشونده شرکت -->
                    <form x-show="editMode" x-cloak action="{{ route('admin.company.update', $comp->id) }}" method="POST" enctype="multipart/form-data" class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-2 mt-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $comp->name }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-slate-200">
                        <input type="text" name="brand" value="{{ $comp->brand }}" placeholder="نام مختصر" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-slate-200">
                        <div class="grid grid-cols-3 gap-2">
                            <input type="text" name="reg_number" value="{{ $comp->reg_number }}" placeholder="شماره ثبت" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs font-mono text-slate-200">
                            <input type="text" name="national_id" value="{{ $comp->national_id }}" placeholder="شناسه ملی" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs font-mono text-slate-200">
                            <input type="text" name="phone" value="{{ $comp->phone }}" placeholder="تلفن" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs font-mono text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-0.5">تغییر فایل لوگو:</label>
                            <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-400 file:bg-slate-800 file:border-none file:text-slate-200 file:rounded file:px-2 file:py-0.5">
                        </div>
                        <button type="submit" class="bg-amber-500 text-slate-950 font-bold px-3 py-1 rounded-lg text-xs cursor-pointer">ذخیره تغییرات</button>
                    </form>
                </div>
                @empty
                <p class="text-xs text-slate-500 p-4 col-span-2 text-center">هیچ شرکتی ثبت نشده است.</p>
                @endforelse
            </div>
        </section>

    </main>

</body>

<script>
function handleQuickApprove(btnElement, targetUrl) {
    btnElement.disabled = true;
    const oldText = btnElement.innerText;
    btnElement.innerText = 'در حال ثبت...';

    // پیدا کردن کارت تسک
    const card = btnElement.closest('.bg-slate-950');

    fetch(targetUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.json();
    })
    .then(data => {
        // حذف نرم و بدون رفرش کارت
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-10px)';
            setTimeout(() => card.remove(), 300);
        }
    })
    .catch(err => {
        console.error(err);
        btnElement.disabled = false;
        btnElement.innerText = oldText;
        alert('خطا در ثبت تایید، لطفاً کنسول را بررسی کنید.');
    });
}
</script>

</html>