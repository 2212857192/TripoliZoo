@extends('layouts.auth')

@section('title', 'كلمة مرور جديدة | Tripoli Zoo')

@section('back_link')
    <a href="{{ route('login') }}" class="back-nav">← العودة لتسجيل الدخول</a>
@endsection

@section('hero_title')
    كلمة مرور<br>جديدة
@endsection
@section('hero_text', 'اختر كلمة مرور قوية لا تقل عن 8 أحرف.')

@section('content')
    <div class="form-title">
        <h3>تعيين كلمة مرور جديدة</h3>
        <p>أدخل كلمة المرور الجديدة وتأكيدها.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="input-group">
            <label for="password">كلمة المرور الجديدة</label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" minlength="8" required dir="ltr" style="text-align:left;">
            </div>
        </div>
        <div class="input-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <div class="input-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required dir="ltr" style="text-align:left;">
            </div>
        </div>
        <button type="submit" class="btn-primary">حفظ كلمة المرور</button>
    </form>
@endsection
