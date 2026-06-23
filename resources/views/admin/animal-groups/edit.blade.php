@extends($__layout ?? 'admin.layout')
@section('title', 'تعديل مجموعة حيوانية | Tripoli Zoo')
@section('page_title', 'تعديل مجموعة حيوانية')

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
        <h2>تعديل المجموعة #{{ $group->id }}</h2>
        <p>تعديل الاسم أو البادئة يُحدّث السجلات والحسابات المرتبطة تلقائياً.</p>
    </div>

    <div class="premium-card">
        <div class="card-accent-header">
            <div class="icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <h3>بيانات المجموعة</h3>
        </div>
        <div class="premium-card-body">
            <form method="POST" action="{{ route('admin.animal-groups.update', $group) }}">
                @csrf
                @method('PUT')
                @include('admin.animal-groups.partials.form-fields', ['group' => $group, 'nextSortOrder' => $group->sort_order])
                <div class="actions-row">
                    <a href="{{ route('admin.animal-groups.index') }}" class="btn-cancel-premium">إلغاء وتراجع</a>
                    <button type="submit" class="btn-submit-premium">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
