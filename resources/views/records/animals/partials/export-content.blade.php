@php
    $p = $profile ?? [];
    $name = $p['name'] ?? ($animal->name ?: '—');
    $type = $p['type'] ?? $animal->species;
    $state = $p['state'] ?? 'active';
    $stateLabel = match($state) {
        'active' => 'داخل الحديقة',
        'quarantine' => 'تحت الحجر الصحي',
        'pending_receipt' => 'بانتظار الاستلام',
        'dead' => 'نافق',
        'stillborn' => 'مولود نافق',
        'slaughter' => 'ذبح اضطراري',
        'exited' => 'خارج من الحديقة',
        default => '—',
    };
    $medical = $p['medical'] ?? ['diagnoses' => [], 'treatments' => [], 'vaccinations' => [], 'nutrition' => []];
    $origin = $p['originInfo'] ?? [];
    $attachments = [];
    if (!empty($p['historyAttachment'])) {
        $attachments[] = [
            'date' => $p['historyAttachment']['date'] ?? ($p['regDate'] ?? '—'),
            'type' => 'مرفق تاريخ سابق',
            'fileName' => $p['historyAttachment']['fileName'] ?? '—',
            'url' => $p['historyAttachment']['url'] ?? null,
        ];
    }
    if ($state === 'dead' && !empty($p['mortality']['attachmentFile'])) {
        $attachments[] = [
            'date' => $p['mortality']['deathDate'] ?? '—',
            'type' => 'مرفق حالة نفوق',
            'fileName' => $p['mortality']['attachmentFile'],
            'url' => $p['mortality']['attachmentUrl'] ?? null,
        ];
    }
    if ($state === 'dead' && !empty($p['mortality']['autopsyReferral']) && $p['mortality']['autopsyReferral'] === 'نعم' && !empty($p['mortality']['reportFile'])) {
        $attachments[] = [
            'date' => $p['mortality']['docDate'] ?? '—',
            'type' => 'تقرير صفة تشريحية',
            'fileName' => $p['mortality']['reportFile'],
            'url' => $p['mortality']['reportUrl'] ?? null,
        ];
    }
    if ($state === 'exited' && !empty($p['exit']['exitFile'])) {
        $attachments[] = [
            'date' => $p['exit']['exitFile']['date'] ?? ($p['exit']['exitDate'] ?? '—'),
            'type' => 'مرفق خروج',
            'fileName' => $p['exit']['exitFile']['fileName'] ?? '—',
            'url' => $p['exit']['exitFile']['url'] ?? null,
        ];
    }
    usort($attachments, fn ($a, $b) => strcmp($a['date'], $b['date']));

    $isImageAttachment = function (?string $url, ?string $fileName): bool {
        $path = strtolower($url ?? $fileName ?? '');

        return (bool) preg_match('/\.(jpe?g|png|gif|webp|bmp)(\?|$)/i', $path);
    };
@endphp

<h3 class="zoo-official-section-title">البيانات الأساسية</h3>
@if(!empty($p['photoUrl']))
<div style="text-align:center;margin-bottom:1rem;">
    <img src="{{ $p['photoUrl'] }}" alt="صورة الحيوان" class="zoo-export-photo">
</div>
@endif
<table class="zoo-official-table">
    <tr><th>رقم الحيوان</th><td>#{{ $animal->code }}</td></tr>
    <tr><th>الاسم</th><td>{{ $name }}</td></tr>
    <tr><th>النوع</th><td>{{ $type }}</td></tr>
    <tr><th>المجموعة</th><td>{{ $p['group'] ?? $animal->group }}</td></tr>
    <tr><th>الجنس</th><td>{{ $p['gender'] ?? $animal->gender }}</td></tr>
    <tr><th>العمر</th><td>{{ $p['age'] ?? $animal->formattedAge() }}</td></tr>
    <tr><th>تاريخ التسجيل</th><td>{{ $p['regDate'] ?? ($animal->registered_at?->format('Y-m-d') ?? '—') }}</td></tr>
    <tr><th>العلامة المميزة</th><td>{{ $p['marks'] ?? ($animal->distinguishing_marks ?: '—') }}</td></tr>
    <tr><th>الحالة</th><td>{{ $stateLabel }}</td></tr>
</table>

@if($state === 'dead' && !empty($p['mortality']))
@php $m = $p['mortality']; @endphp
<h3 class="zoo-official-section-title">بيانات النفوق</h3>
<table class="zoo-official-table">
    <tr><th>رقم الحالة</th><td>{{ $m['caseNumber'] ?? '—' }}</td></tr>
    <tr><th>تاريخ النفوق</th><td>{{ $m['deathDate'] ?? '—' }}</td></tr>
    <tr><th>سبب النفوق</th><td>{{ $m['cause'] ?? '—' }}</td></tr>
    <tr><th>المشرف المسجّل</th><td>{{ $m['supervisor'] ?? '—' }}</td></tr>
    <tr><th>حالة الملف</th><td>{{ $m['caseStatus'] ?? '—' }}</td></tr>
    <tr><th>هل تمت الإحالة للتشريح؟</th><td>{{ $m['autopsyReferral'] ?? '—' }}</td></tr>
    <tr><th>سبب التشريح</th><td>{{ $m['autopsyReason'] ?? '—' }}</td></tr>
    <tr><th>تاريخ التوثيق</th><td>{{ $m['docDate'] ?? '—' }}</td></tr>
    <tr><th>المعتمد</th><td>{{ $m['reviewer'] ?? '—' }}</td></tr>
    @if(($m['autopsyReferral'] ?? '') === 'نعم' && !empty($m['reportFile']))
    <tr><th>تقرير الصفة التشريحية</th><td>{{ $m['reportFile'] }}</td></tr>
    @endif
    @if(!empty($m['notes']) && $m['notes'] !== '—')
    <tr><th>ملاحظات</th><td>{{ $m['notes'] }}</td></tr>
    @endif
</table>
@endif

@if($state === 'stillborn' && !empty($p['stillborn']))
@php $s = $p['stillborn']; @endphp
<h3 class="zoo-official-section-title">بيانات نفوق المولود</h3>
<table class="zoo-official-table">
    <tr><th>رقم الحالة</th><td>{{ $s['caseNumber'] ?? '—' }}</td></tr>
    <tr><th>تاريخ الولادة</th><td>{{ $s['birthDate'] ?? '—' }}</td></tr>
    <tr><th>تاريخ النفوق</th><td>{{ $s['deathDate'] ?? '—' }}</td></tr>
    <tr><th>سبب النفوق</th><td>{{ $s['cause'] ?? '—' }}</td></tr>
    <tr><th>المشرف المسجّل</th><td>{{ $s['supervisor'] ?? '—' }}</td></tr>
    <tr><th>هل تم التشريح؟</th><td>{{ $s['autopsy'] ?? '—' }}</td></tr>
    <tr><th>تاريخ التوثيق</th><td>{{ $s['docDate'] ?? '—' }}</td></tr>
    @if(!empty($s['notes']) && $s['notes'] !== '—')
    <tr><th>ملاحظات</th><td>{{ $s['notes'] }}</td></tr>
    @endif
</table>
@endif

@if($state === 'slaughter' && !empty($p['slaughter']))
@php $sl = $p['slaughter']; @endphp
<h3 class="zoo-official-section-title">بيانات الذبح الاضطراري</h3>
<table class="zoo-official-table">
    <tr><th>رقم الحالة</th><td>{{ $sl['caseNumber'] ?? '—' }}</td></tr>
    <tr><th>تاريخ الدخول للمستشفى</th><td>{{ $sl['admittedAt'] ?? '—' }}</td></tr>
    <tr><th>تاريخ القرار</th><td>{{ $sl['decisionDate'] ?? '—' }}</td></tr>
    <tr><th>الشكوى الرئيسية</th><td>{{ $sl['chiefComplaint'] ?? '—' }}</td></tr>
    <tr><th>نتيجة القرار</th><td>{{ $sl['closingOutcome'] ?? '—' }}</td></tr>
    <tr><th>الطبيب المعالج</th><td>{{ $sl['vet'] ?? '—' }}</td></tr>
    <tr><th>رئيس القسم المعتمد</th><td>{{ $sl['headVet'] ?? '—' }}</td></tr>
</table>
@if(!empty($sl['decisions']))
<h3 class="zoo-official-section-title">سجل القرارات والإجراءات الطبية</h3>
<table class="zoo-official-table">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>التشخيص</th>
            <th>العلاج / الإجراء</th>
            <th>الطبيب</th>
            <th>النتيجة</th>
            <th>ملاحظة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sl['decisions'] as $row)
        <tr>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['diagnosis'] ?? '—' }}</td>
            <td>{{ $row['treatment'] ?? '—' }}</td>
            <td>{{ $row['vet'] ?? '—' }}</td>
            <td>{{ $row['result'] ?? '—' }}</td>
            <td>{{ $row['note'] ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif

@if($state === 'exited' && !empty($p['exit']))
@php $e = $p['exit']; @endphp
<h3 class="zoo-official-section-title">بيانات الخروج</h3>
<table class="zoo-official-table">
    <tr><th>تاريخ الخروج</th><td>{{ $e['exitDate'] ?? '—' }}</td></tr>
    <tr><th>نوع الخروج</th><td>{{ $e['exitType'] ?? '—' }}</td></tr>
    <tr><th>الجهة المستلمة</th><td>{{ $e['recipient'] ?? '—' }}</td></tr>
    <tr><th>سبب الخروج</th><td>{{ $e['reason'] ?? '—' }}</td></tr>
    <tr><th>ملاحظات</th><td>{{ ($e['notes'] ?? '—') !== '—' ? $e['notes'] : 'إن وجدت' }}</td></tr>
</table>
@endif

@if($state === 'active' && !empty($p['repro']))
<h3 class="zoo-official-section-title">التاريخ التناسلي</h3>
<table class="zoo-official-table">
    <thead>
        <tr>
            <th>رقم المولود</th>
            <th>تاريخ الولادة</th>
            <th>النوع</th>
            <th>الجنس</th>
            <th>علامة التمييز</th>
            <th>حالة المولود</th>
            <th>السجل المرتبط</th>
        </tr>
    </thead>
    <tbody>
        @foreach($p['repro'] as $row)
        <tr>
            <td>{{ $row['id'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['type'] ?? '—' }}</td>
            <td>{{ $row['gender'] ?? '—' }}</td>
            <td>{{ $row['mark'] ?? '—' }}</td>
            <td>{{ $row['status'] ?? '—' }}</td>
            <td>{{ $row['ref'] ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h3 class="zoo-official-section-title">الأصل والتسجيل</h3>
<table class="zoo-official-table">
    <tr><th>أصل الحيوان</th><td>{{ $origin['animalOrigin'] ?? '—' }}</td></tr>
    <tr><th>مصدر الحيوان</th><td>{{ $origin['source'] ?? '—' }}</td></tr>
    <tr><th>طريقة الإدخال</th><td>{{ $origin['entryMethod'] ?? '—' }}</td></tr>
    <tr><th>تاريخ التسجيل في النظام</th><td>{{ $origin['regDate'] ?? '—' }}</td></tr>
</table>

<h3 class="zoo-official-section-title">جدول التشخيصات</h3>
<table class="zoo-official-table">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>نوع الحالة</th>
            <th>التشخيص</th>
            <th>الطبيب</th>
            <th>المرجع</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medical['diagnoses'] ?? [] as $row)
        <tr>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['caseType'] ?? '—' }}</td>
            <td>{{ $row['diagnosis'] ?? '—' }}</td>
            <td>{{ $row['vet'] ?? '—' }}</td>
            <td>{{ $row['ref'] ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="zoo-official-empty">لا توجد تشخيصات مسجّلة لهذا الحيوان</td></tr>
        @endforelse
    </tbody>
</table>

<h3 class="zoo-official-section-title">جدول العلاجات والإجراءات الطبية</h3>
<table class="zoo-official-table">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>العلاج / الإجراء</th>
            <th>الطبيب</th>
            <th>مرتبط بتشخيص</th>
            <th>المرجع</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medical['treatments'] ?? [] as $row)
        <tr>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['treatment'] ?? '—' }}</td>
            <td>{{ $row['vet'] ?? '—' }}</td>
            <td>{{ $row['linkedDiagnosis'] ?? '—' }}</td>
            <td>{{ $row['ref'] ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="zoo-official-empty">لا توجد علاجات أو إجراءات مسجّلة</td></tr>
        @endforelse
    </tbody>
</table>

<h3 class="zoo-official-section-title">جدول التوصيات الغذائية العلاجية</h3>
<table class="zoo-official-table">
    <thead>
        <tr>
            <th>تاريخ البداية</th>
            <th>التوصية</th>
            <th>المدة</th>
            <th>الحالة</th>
            <th>المرجع</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medical['nutrition'] ?? [] as $row)
        <tr>
            <td>{{ $row['startDate'] ?? '—' }}</td>
            <td>{{ $row['recommendation'] ?? '—' }}</td>
            <td>{{ $row['duration'] ?? '—' }}</td>
            <td>{{ $row['status'] ?? '—' }}</td>
            <td>{{ $row['ref'] ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="zoo-official-empty">لا توجد توصيات غذائية علاجية</td></tr>
        @endforelse
    </tbody>
</table>

<h3 class="zoo-official-section-title">المرفقات والتقارير</h3>
@if(count($attachments))
<div class="zoo-export-attachments">
    @foreach($attachments as $item)
    <div class="zoo-export-attachment">
        <div class="zoo-export-attachment__meta">
            <span class="zoo-export-attachment__type">{{ $item['type'] }}</span>
            <span class="zoo-export-attachment__date">{{ $item['date'] }}</span>
        </div>
        @if(!empty($item['url']))
            @if($isImageAttachment($item['url'], $item['fileName']))
            <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="zoo-export-attachment__preview-link">
                <img src="{{ $item['url'] }}" alt="{{ $item['fileName'] }}" class="zoo-export-attachment__img">
            </a>
            <div class="zoo-export-attachment__caption">{{ $item['fileName'] }}</div>
            @else
            <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="zoo-export-attachment__file">
                {{ $item['fileName'] }}
            </a>
            @endif
        @else
        <div class="zoo-export-attachment__caption">{{ $item['fileName'] }}</div>
        @endif
    </div>
    @endforeach
</div>
@else
<p class="zoo-official-empty">لا توجد مرفقات أو تقارير مرتبطة بهذا الحيوان</p>
@endif
