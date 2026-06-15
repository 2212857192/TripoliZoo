@extends('layouts.auth')

@section('title', 'رمز التحقق | Tripoli Zoo')

@section('back_link')
    <a href="{{ route('password.request') }}" class="back-nav">← العودة</a>
@endsection

@section('hero_title')
    تحقق<br>من الرمز
@endsection
@section('hero_text', 'أدخل الرمز المكوّن من 6 أرقام الذي وصل إلى بريدك الإلكتروني.')

@section('content')
    <div class="form-title">
        <h3>رمز التحقق</h3>
        <p>أدخل الرمز المرسل إلى</p>
        <p><strong dir="ltr" style="display:inline-block;color:var(--text-main);">{{ $email }}</strong></p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.verify.submit') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="input-group">
            <label for="code">رمز التحقق</label>
            <div class="input-wrapper">
                <input type="text" id="code" name="code" class="code-input" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="000000" required autofocus>
            </div>
        </div>
        <button type="submit" class="btn-primary">تأكيد الرمز</button>
    </form>

    <form method="POST" action="{{ route('password.resend') }}" style="text-align:center;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="btn-link" style="background:none;border:none;cursor:pointer;">إعادة إرسال الرمز</button>
    </form>
@endsection
