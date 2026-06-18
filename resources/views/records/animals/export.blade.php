<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملف الحيوان الرسمي — {{ $animal->code }} | حديقة حيوان طرابلس</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 1.5rem;
            line-height: 1.7;
            background: #e5e5e5;
        }
        .no-print {
            margin-bottom: 1rem;
            display: flex;
            gap: 10px;
        }
        .no-print button {
            padding: 10px 20px;
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        .no-print button:hover { background: #333; }
        .no-print button.secondary {
            background: #fff;
            color: #475569;
            border: 1.5px solid #e2e8f0;
        }
        .zoo-export-photo {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }
        .zoo-export-attachments {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .zoo-export-attachment {
            padding: 0.85rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fafafa;
            page-break-inside: avoid;
        }
        .zoo-export-attachment__meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.65rem;
            font-size: 0.85rem;
        }
        .zoo-export-attachment__type {
            font-weight: 800;
            color: #111;
        }
        .zoo-export-attachment__date {
            color: #6b7280;
            font-weight: 700;
        }
        .zoo-export-attachment__preview-link {
            display: block;
            text-decoration: none;
        }
        .zoo-export-attachment__img {
            display: block;
            width: 100%;
            max-width: 520px;
            max-height: 420px;
            object-fit: contain;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #fff;
            margin: 0 auto;
        }
        .zoo-export-attachment__caption {
            margin-top: 0.45rem;
            font-size: 0.78rem;
            color: #6b7280;
            font-weight: 700;
            text-align: center;
        }
        .zoo-export-attachment__file {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 800;
            color: #111;
            text-decoration: underline;
            word-break: break-all;
        }
        .zoo-official-empty {
            text-align: center;
            color: #6b7280;
            font-weight: 700;
            padding: 1rem;
        }
        .zoo-official-section-title { page-break-after: avoid; }
        .zoo-official-table { page-break-inside: auto; }
        .zoo-official-table tr { page-break-inside: avoid; }
    </style>
    @include('partials.zoo-official.styles')
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">طباعة / حفظ PDF</button>
        <button type="button" class="secondary" onclick="window.close()">إغلاق</button>
    </div>

    @php
        $p = $profile ?? [];
        $name = $p['name'] ?? ($animal->name ?: '—');
        $type = $p['type'] ?? $animal->species;
        $subtitle = ($name !== '—' ? $name . ' — ' : '') . $type;
    @endphp

    <div class="zoo-official-doc">
        @include('partials.zoo-official.header', [
            'officialTitle'      => 'ملف الحيوان الرسمي',
            'officialSubtitle'   => $subtitle,
            'officialDepartment' => 'إدارة السجلات والتوثيق',
            'officialRef'        => $animal->code,
            'officialRefLabel'   => 'رقم الحيوان',
            'officialIssuedAt'   => now()->format('Y-m-d'),
            'officialDocType'    => 'ملف حيوان',
        ])

        <div class="zoo-official-doc__body">
            @include('records.animals.partials.export-content')
        </div>

        @include('partials.zoo-official.footer', [
            'officialFooterNote' => 'نسخة مطبوعة من ملف الحيوان الرسمي — للاطلاع والتوثيق فقط.',
        ])
    </div>

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
