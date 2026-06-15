@props([
    'filterId' => 'dateFilter',
    'selectClass' => 'filter-select',
    'wrapperStyle' => null,
    'selectStyle' => null,
    'showWeek' => true,
    'showMonth' => false,
    'showYear' => false,
    'showLast7' => false,
    'showLast30' => false,
])

<div class="date-filter-group" @if($wrapperStyle) style="{{ $wrapperStyle }}" @endif>
    <select
        class="{{ $selectClass }}"
        id="{{ $filterId }}"
        @if($selectStyle) style="{{ $selectStyle }}" @endif
        onchange="toggleCustomDateFilter('{{ $filterId }}')"
    >
        <option value="">كل التواريخ</option>
        <option value="today">اليوم</option>
        @if($showWeek)
            <option value="week">الأسبوع</option>
        @endif
        @if($showMonth)
            <option value="month">هذا الشهر</option>
        @endif
        @if($showYear)
            <option value="year">هذا العام</option>
        @endif
        @if($showLast7)
            <option value="last7">آخر 7 أيام</option>
        @endif
        @if($showLast30)
            <option value="last30">آخر 30 يوم</option>
        @endif
        <option value="custom">تاريخ محدد</option>
    </select>
    <div class="date-filter-picker-wrap" id="{{ $filterId }}PickerWrap">
        <input
            type="date"
            class="{{ $selectClass }}"
            id="{{ $filterId }}CustomDate"
            @if($selectStyle) style="{{ $selectStyle }}" @endif
        >
    </div>
</div>
