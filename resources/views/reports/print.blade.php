<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>صورت‌وضعیت فنی {{ $building->title }}</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; direction: rtl; padding: 25px; color: #0f172a; }
        .no-print { margin-bottom: 20px; display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; }
        .btn-print { background: #0284c7; color: white; }
        .header { text-align: center; border-bottom: 2px solid #cbd5e1; padding-bottom: 12px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: right; }
        th { background-color: #f1f5f9; }
        .summary { margin-top: 25px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 15px; border-radius: 10px; font-size: 13px; }
        .signatures { margin-top: 60px; display: flex; justify-content: space-between; text-align: center; }
        .sig-box { width: 40%; border-top: 1px dashed #64748b; padding-top: 8px; font-size: 12px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">🖨 چاپ یا ذخیره به صورت PDF</button>
    </div>

    <div class="header">
        <h2 style="margin:0; color:#d97706;">سامانه فنی service123</h2>
        <p style="margin:4px 0 0 0; font-size:12px; color:#64748b;">صورت‌وضعیت عملیات و تسویه‌حساب ساختمانی</p>
    </div>

    <div style="display:flex; justify-content:space-between; font-size:12px;">
        <span><strong>ساختمان / بلوک:</strong> {{ $building->title }}</span>
        <span><strong>تاریخ:</strong> {{ date('Y/m/d') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>شرح خدمات / خرابی</th>
                <th>واحد / ورودی</th>
                <th>تکنسین</th>
                <th>مبلغ (تومان)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($breakdowns as $idx => $b)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $b->title }}</td>
                    <td>{{ $b->building_unit ?? '-' }}</td>
                    <td>{{ $b->user->name ?? '-' }}</td>
                    <td>{{ $b->is_billable ? number_format($b->amount) : 'رایگان' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">موردی ثبت نشده است.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div><strong>کل هزینه‌ها:</strong> {{ number_format($totalCost) }} تومان</div>
        <div style="margin-top:5px;"><strong>کل دریافتی‌ها:</strong> {{ number_format($totalPaid) }} تومان</div>
        <div style="margin-top:5px; font-weight:bold;">
            وضعیت مانده‌حساب: 
            {{ $balance < 0 ? number_format(abs($balance)).' تومان بدهکار' : ($balance > 0 ? number_format($balance).' تومان بستانکار' : 'تسویه کامل') }}
        </div>
    </div>

    <div class="signatures">
        <div class="sig-box">مهر و امضای مدیر فنی مجموعه</div>
        <div class="sig-box">امضای نماینده / مدیر ساختمان</div>
    </div>

</body>
</html>