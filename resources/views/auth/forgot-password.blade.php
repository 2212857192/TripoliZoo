@extends('layouts.auth')

@section('title', 'نسيت كلمة المرور | Tripoli Zoo')

@section('back_link')
    <a href="{{ route('login') }}" class="back-nav">← العودة لتسجيل الدخول</a>
@endsection

@section('content')
    <div class="form-title">
        <h3>نسيت كلمة المرور؟</h3>
        <p>أدخل بريدك الإلكتروني وسنرسل لك رمز تحقق لإعادة التعيين.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="input-group">
            <label for="email">البريد الإلكتروني</label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@tripolizoo.ly" required dir="ltr" style="text-align:left;">
            </div>
        </div>
        <button type="submit" class="btn-primary">إرسال الرمز</button>
    </form>
@endsection
