<div class="topbar-right">
    <div class="page-title">
        <h1>@yield('page_title', $defaultTitle ?? 'لوحة التحكم')</h1>
        <div class="breadcrumb">
            {{ $sectionLabel ?? '' }} <span>/</span> @yield('page_title', $defaultTitle ?? 'لوحة التحكم')
        </div>
    </div>
</div>
