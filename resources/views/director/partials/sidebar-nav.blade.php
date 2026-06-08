@php
    $dp = '/director';
@endphp

<a href="{{ $dp }}/dashboard" class="nav-item {{ request()->is('director/dashboard') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    <span class="nav-item-text">نظرة عامة</span>
</a>

<div class="nav-label">الإدارة والزوار</div>
<div class="nav-dropdown {{ request()->is('director/admin*') ? 'open' : '' }}">
    <button class="nav-item dropdown-toggle" onclick="toggleDropdown(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span class="nav-item-text">الإدارة والزوار</span>
        <svg class="arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div class="dropdown-menu" style="{{ request()->is('director/admin*') ? 'display: flex;' : 'display: none;' }}">
        <a href="{{ $dp }}/admin/employees" class="nav-item {{ request()->is('director/admin/employees') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">حسابات الموظفين</span></a>
        <a href="{{ $dp }}/admin/animals" class="nav-item {{ request()->is('director/admin/animals*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">المحتوى التعريفي</span></a>
        <a href="{{ $dp }}/admin/map-locations" class="nav-item {{ request()->is('director/admin/map-locations*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">خريطة الحديقة</span></a>
        <a href="{{ $dp }}/admin/tickets" class="nav-item {{ request()->is('director/admin/tickets*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">إدارة التذاكر</span></a>
        <a href="{{ $dp }}/admin/visit-info" class="nav-item {{ request()->is('director/admin/visit-info*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">معلومات الزيارة</span></a>
    </div>
</div>

<div class="nav-label">المستشفى البيطري</div>
<div class="nav-dropdown {{ request()->is('director/vet*') ? 'open' : '' }}">
    <button class="nav-item dropdown-toggle" onclick="toggleDropdown(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        <span class="nav-item-text">المستشفى البيطري</span>
        <svg class="arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div class="dropdown-menu" style="{{ request()->is('director/vet*') ? 'display: flex;' : 'display: none;' }}">
        <span class="nav-item" style="cursor:default; opacity:0.85; padding-right:28px;">
            <span style="font-size:0.72rem;color:#94a3b8;font-weight:800;">الحالات الطبية</span>
        </span>
        <a href="{{ $dp }}/vet/cases/field" class="nav-item {{ request()->is('director/vet/cases/field*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">الميدانية الطبية</span></a>
        <a href="{{ $dp }}/vet/cases/hospital" class="nav-item {{ request()->is('director/vet/cases/hospital*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">داخل المستشفى</span></a>
        <a href="{{ $dp }}/vet/quarantine" class="nav-item {{ request()->is('director/vet/quarantine*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">الحجر الصحي</span></a>
        <a href="{{ $dp }}/vet/referrals/treatment" class="nav-item {{ request()->is('director/vet/referrals/treatment*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">إحالات العلاج</span></a>
        <a href="{{ $dp }}/vet/referrals/autopsy" class="nav-item {{ request()->is('director/vet/referrals/autopsy*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">إحالات التشريح</span></a>
        <a href="{{ $dp }}/vet/decisions" class="nav-item {{ request()->is('director/vet/decisions*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">القرارات الطبية</span></a>
    </div>
</div>

<div class="nav-label">الرعاية والتغذية</div>
<div class="nav-dropdown {{ request()->is('director/care*') ? 'open' : '' }}">
    <button class="nav-item dropdown-toggle" onclick="toggleDropdown(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span class="nav-item-text">الرعاية والتغذية</span>
        <svg class="arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div class="dropdown-menu" style="{{ request()->is('director/care*') ? 'display: flex;' : 'display: none;' }}">
        <a href="{{ $dp }}/care/health" class="nav-item {{ request()->is('director/care/health*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">الحالات الصحية</span></a>
        <a href="{{ $dp }}/care/births" class="nav-item {{ request()->is('director/care/births*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">الولادات الجديدة</span></a>
        <a href="{{ $dp }}/care/mortality" class="nav-item {{ request()->is('director/care/mortality*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">حالات النفوق</span></a>
        <a href="{{ $dp }}/care/notes" class="nav-item {{ request()->is('director/care/notes*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">الملاحظات التشغيلية</span></a>
        <a href="{{ $dp }}/care/referrals/treatment" class="nav-item {{ request()->is('director/care/referrals/treatment*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">إحالات العلاج</span></a>
        <a href="{{ $dp }}/care/referrals/autopsy" class="nav-item {{ request()->is('director/care/referrals/autopsy*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">إحالات التشريح</span></a>
        <a href="{{ $dp }}/care/decisions" class="nav-item {{ request()->is('director/care/decisions*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">القرارات الطبية</span></a>
    </div>
</div>

<div class="nav-label">السجلات والتوثيق</div>
<div class="nav-dropdown {{ request()->is('director/records*') ? 'open' : '' }}">
    <button class="nav-item dropdown-toggle" onclick="toggleDropdown(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span class="nav-item-text">السجلات والتوثيق</span>
        <svg class="arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div class="dropdown-menu" style="{{ request()->is('director/records*') ? 'display: flex;' : 'display: none;' }}">
        <a href="{{ $dp }}/records/animals" class="nav-item {{ request()->is('director/records/animals*') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">قائمة الحيوانات</span></a>
        <a href="{{ $dp }}/records/logs/births" class="nav-item {{ request()->is('director/records/logs/births') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل الولادات</span></a>
        <a href="{{ $dp }}/records/logs/stillbirths" class="nav-item {{ request()->is('director/records/logs/stillbirths') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل الولادات النافقة</span></a>
        <a href="{{ $dp }}/records/logs/entries" class="nav-item {{ request()->is('director/records/logs/entries') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل الحيوانات الداخلة</span></a>
        <a href="{{ $dp }}/records/logs/exits" class="nav-item {{ request()->is('director/records/logs/exits') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل الحيوانات الخارجة</span></a>
        <a href="{{ $dp }}/records/logs/mortality" class="nav-item {{ request()->is('director/records/logs/mortality') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل النفوق</span></a>
        <a href="{{ $dp }}/records/logs/slaughter" class="nav-item {{ request()->is('director/records/logs/slaughter') ? 'active' : '' }}"><span style="font-size:0.8rem;color:#64748b;">•</span><span class="nav-item-text">سجل الذبح الاضطراري</span></a>
    </div>
</div>
