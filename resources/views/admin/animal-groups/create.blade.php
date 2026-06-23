@extends($__layout ?? 'admin.layout')
@section('title', 'إضافة مجموعة حيوانية | Tripoli Zoo')
@section('page_title', 'إضافة مجموعة حيوانية')

@section('styles')
@include('admin.animal-groups.partials.form-styles')
@endsection

@section('content')
<div class="group-form-layout">
    <a href="{{ route('admin.animal-groups.index') }}" class="page-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        العودة إلى قائمة المجموعات
    </a>

    <div class="page-hero">
        <h2>إضافة مجموعة حيوانية جديدة</h2>
        <p>المجموعة الجديدة ستظهر فوراً في السجلات، الموظفين، الحجر الصحي، والتطبيق.</p>
    </div>

    <div class="premium-card">
        <div class="card-accent-header">
            <div class="icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 7h18"/><path d="M6 3h12v18H6z"/></svg>
            </div>
            <h3>بيانات المجموعة</h3>
        </div>
        <div class="premium-card-body">
            <form method="POST" action="{{ route('admin.animal-groups.store') }}">
                @csrf
                @include('admin.animal-groups.partials.form-fields', ['group' => null, 'nextSortOrder' => $nextSortOrder])
                <div class="actions-row">
                    <a href="{{ route('admin.animal-groups.index') }}" class="btn-cancel-premium">إلغاء وتراجع</a>
                    <button type="submit" class="btn-submit-premium">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        حفظ المجموعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
