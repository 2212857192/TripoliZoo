@php
    $officialFooterNote = $officialFooterNote ?? 'نسخة مطبوعة من ملف الحيوان الرسمي — للاطلاع والتوثيق فقط.';
    $officialPrintDate  = $officialPrintDate ?? now()->format('Y-m-d H:i');
@endphp

<footer class="zoo-official-footer">
    <p class="zoo-official-footer__note">{{ $officialFooterNote }}</p>
    <p class="zoo-official-footer__meta">
        <span>تاريخ الطباعة: <strong>{{ $officialPrintDate }}</strong></span>
        <span class="zoo-official-footer__sep">|</span>
        <span>الجهة المصدرة: <strong>قسم السجلات والتوثيق</strong></span>
    </p>
</footer>
