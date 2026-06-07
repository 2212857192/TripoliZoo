@extends('vet.layout')
@section('title', 'إضافة حيوان للحجر الصحي | المستشفى البيطري')
@section('page_title', 'إضافة للحجر الصحي')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">إضافة حيوان جديد للحجر الصحي</h3>
    </div>
    <div class="card-body">
        <p style="color: #64748b;">هذه صفحة إضافة حيوان للحجر الصحي. سيتم تصميمها وإضافة الحقول (مثل اختيار الحيوان، تاريخ الدخول، الملاحظات) لاحقاً بناءً على توجيهاتك.</p>
        <br>
        <button class="btn-cancel" onclick="window.history.back()" style="padding:8px 16px; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer;">العودة للخلف</button>
    </div>
</div>
@endsection
