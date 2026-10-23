<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سامانه service123</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0f172a">
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
  }
</script>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between items-center p-4">

    <!-- بخش هدر -->
    <header class="w-full max-w-md pt-8 text-center">
        <h1 class="text-3xl font-black tracking-wider text-amber-500">service123</h1>
        <p class="text-xs text-slate-400 mt-1">سامانه مدیریت عملیات و نگهداری فنی</p>
    </header>

    <!-- کادر فرم لاگین -->
    <main class="w-full max-w-sm bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl my-auto">
        <h2 class="text-lg font-bold text-center mb-6 text-slate-200">ورود به حساب کاربری</h2>

        @if ($errors->any())
        <div class="bg-rose-500/10 border border-rose-500 text-rose-300 text-xs rounded-lg p-3 mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-slate-400 mb-1">شماره موبایل</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required autofocus
                    placeholder="مثال: 09120000000"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm text-center tracking-widest focus:outline-none focus:border-amber-500 transition">
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">رمز عبور</label>
                <input type="password" name="password" required
                    placeholder="••••••••"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm text-center tracking-widest focus:outline-none focus:border-amber-500 transition">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-0">
                    مرا به خاطر بسپار
                </label>
            </div>

            <button type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-3 rounded-xl text-sm transition duration-200 shadow-lg shadow-amber-500/20 active:scale-95">
                ورود به سامانه
            </button>
        </form>
    </main>

    <!-- بخش تماس اضطراری و پشتیبانی -->
    <footer class="w-full max-w-sm pb-6 text-center">
        <div class="border-t border-slate-800 pt-4 text-xs text-slate-400">
            <p>نیاز به راهنمایی یا بازیابی دسترسی دارید؟</p>
            <a href="tel:09120000000" class="inline-flex items-center gap-1 text-amber-500 hover:underline mt-1.5 font-medium">
                تماس با پشتیبانی / مدیر فنی
            </a>
        </div>
    </footer>

</body>

</html>