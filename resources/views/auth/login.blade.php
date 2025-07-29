@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 400px; margin-top: 50px;">
    <h2 class="mb-4 text-center">تسجيل الدخول</h2>


    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">تذكرني</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">دخول</button>
        <div class="mt-3 text-center">
            <a href="{{ route('register') }}">ليس لديك حساب؟ سجل الآن</a>
        </div>
    </form>
</div>
@endsection