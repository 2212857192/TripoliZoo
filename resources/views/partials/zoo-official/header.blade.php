@php
    $officialTitle      = $officialTitle ?? 'وثيقة رسمية';
    $officialSubtitle   = $officialSubtitle ?? null;
    $officialDepartment = $officialDepartment ?? 'إدارة السجلات والتوثيق';
    $officialRef        = $officialRef ?? null;
    $officialRefLabel   = $officialRefLabel ?? 'رقم المرجع';
    $officialIssuedAt   = $officialIssuedAt ?? now()->format('Y-m-d');
    $officialDocType    = $officialDocType ?? null;
    $officialStatusSlot = $officialStatusSlot ?? false;
@endphp

<header class="zoo-official-letterhead">
    <div class="zoo-official-brand">
        <img src="/logo.jpg" alt="شعار حديقة حيوان طرابلس" class="zoo-official-logo">
    </div>

    <div class="zoo-official-org">
        <h2 class="zoo-official-org__name-ar">حديقة حيوان طرابلس</h2>
        <p class="zoo-official-org__name-en">Tripoli Zoological Garden</p>
        <span class="zoo-official-org__dept">{{ $officialDepartment }}</span>
    </div>

    <div class="zoo-official-meta-col">
        @if($officialRef)
        <div class="zoo-official-meta-row">
            {{ $officialRefLabel }}
            <strong><span class="ref-code" @if(!empty($officialRefId)) id="{{ $officialRefId }}" @endif>#{{ $officialRef }}</span></strong>
        </div>
        @endif
        <div class="zoo-official-meta-row">
            تاريخ الإصدار
            <strong>{{ $officialIssuedAt }}</strong>
        </div>
        @if($officialDocType)
        <div class="zoo-official-meta-row">
            نوع الوثيقة
            <strong>{{ $officialDocType }}</strong>
        </div>
        @endif
    </div>
</header>

<div class="zoo-official-titlebar">
    <div class="zoo-official-titlebar__main">
        <h1>{{ $officialTitle }}</h1>
        @if($officialSubtitle)
        <p @if(!empty($officialSubtitleId)) id="{{ $officialSubtitleId }}" @endif>{{ $officialSubtitle }}</p>
        @elseif(!empty($officialSubtitleId))
        <p id="{{ $officialSubtitleId }}">—</p>
        @endif
    </div>
    <div class="zoo-official-titlebar__extras">
        @if($officialStatusSlot)
        <span id="headerBadge"></span>
        @endif
        @if($officialDocType && !$officialStatusSlot)
        <span class="zoo-official-doc-type">{{ $officialDocType }}</span>
        @endif
    </div>
</div>
