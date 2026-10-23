<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت بلوک‌ها و ساختمان‌ها | service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style> body { font-family: 'Vazirmatn', sans-serif; } </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen pb-16">

    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-3 py-1.5 rounded-lg transition">⬅ بازگشت به داشبورد</a>
            <span class="text-sm font-bold text-slate-200">دفترچه بلوک‌ها، ورودی‌ها و پرسنل</span>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white px-3 py-1.5 rounded-lg transition">خروج</button>
        </form>
    </header>

    <main class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6" x-data="{ showNewLocation: false }">

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
                <h1 class="text-sm font-bold text-slate-200">محدوده‌ها و بلوک‌های فعال</h1>
                <p class="text-[11px] text-slate-400 mt-0.5">تعیین دسترسی کلی به بلوک و استثنا کردن ورودی‌های مشخص برای هر پرسنل</p>
            </div>
            <button @click="showNewLocation = !showNewLocation" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2 rounded-xl text-xs transition shadow">
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
                        <input type="text" name="title" required placeholder="مثال: بلوک A4 یا شهرک بیمه" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">کد شناسایی (اختیاری):</label>
                        <input type="text" name="code" placeholder="مثال: A4" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">آدرس پایه محدوده:</label>
                    <input type="text" name="address" placeholder="مثال: شهرک اکباتان، فاز ۱ یا میدان سالاری" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2 rounded-xl text-xs transition">
                    ثبت بلوک
                </button>
            </form>
        </div>

        <!-- لیست درختی بلوک‌ها -->
        <div class="space-y-5">
            @forelse ($locations as $loc)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-4" x-data="{ newBuilding: false, editAccess: false, editLoc: false }">
                    
                    <!-- هدر بلوک -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-amber-500/20 text-amber-400 font-bold px-2 py-0.5 rounded">محدوده</span>
                            <h3 class="text-sm font-black text-slate-100">{{ $loc->title }}</h3>
                            @if($loc->code)
                                <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">{{ $loc->code }}</span>
                            @endif
                            <span class="text-[11px] text-slate-500">({{ $loc->buildings->count() }} ورودی)</span>
                        </div>

                        <div class="flex items-center gap-1.5 text-xs">
                            <button @click="newBuilding = !newBuilding" class="bg-emerald-600/80 hover:bg-emerald-600 text-white px-2.5 py-1 rounded-lg transition">➕ افزودن ورودی/ساختمان</button>
                            <button @click="editAccess = !editAccess" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-2.5 py-1 rounded-lg transition">👷 دسترسی پرسنل ({{ $loc->users->count() }})</button>
                            <button @click="editLoc = !editLoc" class="bg-slate-800 hover:bg-slate-700 text-amber-400 px-2 py-1 rounded-lg transition">✏</button>
                            <form action="{{ route('admin.location.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('حذف بلوک؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-slate-800 hover:bg-rose-900/60 text-rose-400 px-2 py-1 rounded-lg transition">🗑</button>
                            </form>
                        </div>
                    </div>

                    <!-- ویرایش مشخصات بلوک -->
                    <form x-show="editLoc" x-cloak action="{{ route('admin.location.update', $loc->id) }}" method="POST" class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-2">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="title" value="{{ $loc->title }}" required class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            <input type="text" name="code" value="{{ $loc->code }}" placeholder="کد" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                        </div>
                        <input type="text" name="address" value="{{ $loc->address }}" placeholder="آدرس پایه محدوده" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                        <button type="submit" class="bg-amber-500 text-slate-950 font-bold py-1 px-3 rounded-lg text-xs">ذخیره ویرایش</button>
                    </form>

                    <!-- تعیین تکنسین‌های مجاز بلوک -->
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
                                <p class="text-[11px] text-rose-400">هنوز حسابی با نقش تکنسین ثبت نشده است.</p>
                            @endforelse
                        </div>
                        <button type="submit" class="bg-amber-500 text-slate-950 font-bold py-1 px-4 rounded-lg text-xs mt-1">ثبت دسترسی‌ها</button>
                    </form>

                    <!-- فرم افزودن ورودی جدید با تیک ادامه آدرس -->
                    <form x-show="newBuilding" x-cloak action="{{ route('admin.building.store') }}" method="POST" class="bg-slate-950 p-3.5 rounded-xl border border-emerald-500/30 space-y-2.5" x-data="{ hasFee: false, hasCustomAddress: false }">
                        @csrf
                        <input type="hidden" name="location_id" value="{{ $loc->id }}">
                        <p class="text-[11px] font-bold text-emerald-400">افزودن ورودی / ساختمان جدید:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="title" required placeholder="عنوان ورودی (مثال: ورودی ۲ یا ساختمان الماس)" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                            <input type="text" name="code" placeholder="پلاک یا کد اختصاصی" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200">
                        </div>

                        <!-- بخش تیک ادامه آدرس -->
                        <div class="space-y-1.5 pt-1 border-t border-slate-800/60">
                            <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" x-model="hasCustomAddress" class="rounded text-amber-500 focus:ring-0">
                                <span>نیاز به آدرس اختصاصی / ادامه آدرس دارد</span>
                            </label>
                            
                            <div x-show="hasCustomAddress" x-cloak class="space-y-1">
                                @if($loc->address)
                                    <p class="text-[10px] text-slate-400">آدرس پایه محدوده: <span class="text-amber-400">{{ $loc->address }}</span></p>
                                @endif
                                <input type="text" name="address" placeholder="ادامه آدرس (مثال: خیابان بیمه ۴، کوچه سوم، پلاک ۱۲)" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:border-amber-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- شارژ ماهانه -->
                        <div class="flex items-center gap-3 pt-1 border-t border-slate-800/60">
                            <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="has_monthly_fee" x-model="hasFee" class="rounded text-amber-500 focus:ring-0">
                                <span>شارژ ماهانه دارد</span>
                            </label>
                            <div x-show="hasFee">
                                <input type="number" name="monthly_fee" placeholder="مبلغ شارژ (تومان)" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-emerald-400 font-bold">
                            </div>
                        </div>

                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1 px-4 rounded-lg text-xs">ثبت ورودی</button>
                    </form>

                    <!-- لیست ساختمان‌ها / ورودی‌های زیرمجموعه -->
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
                                        <button @click="editExclusions = !editExclusions" class="text-rose-400 hover:text-rose-300 bg-slate-900 px-2 py-0.5 rounded border border-rose-500/20">🚫 محدودیت تکنسین</button>
                                        <button @click="addContact = !addContact" class="text-slate-300 hover:text-white bg-slate-900 px-2 py-0.5 rounded">📞 ثبت مدیر</button>
                                        <button @click="editBuilding = !editBuilding" class="text-amber-400 hover:text-amber-300 bg-slate-900 px-2 py-0.5 rounded">✏</button>
                                        <form action="{{ route('admin.building.destroy', $b->id) }}" method="POST" onsubmit="return confirm('حذف ورودی؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 bg-slate-900 px-2 py-0.5 rounded">🗑</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- فرم استثنا کردن تکنسین‌های خاص از این ورودی -->
                                <form x-show="editExclusions" x-cloak action="{{ route('admin.building.exclusions', $b->id) }}" method="POST" class="bg-slate-900 p-3 rounded-lg border border-rose-500/30 space-y-2">
                                    @csrf
                                    <p class="text-[11px] text-rose-300 font-bold">تکنسین‌هایی که نباید این ورودی را ببینند تیک بزنید:</p>
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
                                    <button type="submit" class="bg-rose-600 hover:bg-rose-500 text-white font-bold px-3 py-1 rounded text-xs">ذخیره استثنائات</button>
                                </form>

                                <!-- ویرایش ورودی همراه با آدرس -->
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
                                            <span>آدرس اختصاصی / تکمیلی</span>
                                        </label>
                                        <div x-show="hasCustomAddress" x-cloak>
                                            <input type="text" name="address" value="{{ $b->address }}" placeholder="ادامه آدرس دقیق ساختمان" class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-1 text-xs text-slate-200">
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
                                    <button type="submit" class="bg-amber-500 text-slate-950 font-bold px-3 py-1 rounded text-xs">ذخیره</button>
                                </form>

                                <!-- مخاطبان و مدیران ورودی -->
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
                                                        <button type="submit" class="text-slate-500 hover:text-rose-400">✕</button>
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
                                        <button type="submit" class="bg-emerald-600 text-white font-bold px-3 py-1 rounded text-xs">ثبت مدیر</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 p-2 bg-slate-950 rounded-lg">هنوز ورودی یا ساختمانی داخل این بلوک تعریف نشده است.</p>
                        @endforelse
                    </div>

                </div>
            @empty
                <div class="text-center py-10 text-xs text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                    هیچ بلوکی تعریف نشده است.
                </div>
            @endforelse
        </div>

    </main>

</body>
</html>