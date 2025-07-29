<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تطبيق الأعمال</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Tajawal', 'Segoe UI', sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .welcome-card {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
        }
        .welcome-title {
            color: #F53003;
            font-weight: bold;
            font-size: 2rem;
        }
        .welcome-msg {
            font-size: 1.2rem;
            color: #198754;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="/">تطبيق الأعمال</a>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('chat.index') }}" 
                       class="btn d-flex align-items-center px-3 py-2"
                       style="background: linear-gradient(90deg,#F53003 0,#ff7b54 100%); color: #fff; font-weight:500; box-shadow: 0 2px 8px rgba(245,48,3,0.10); border:none; transition:0.2s;">
                        {{-- أيقونة رسائل حديثة من Bootstrap Icons --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="me-2" viewBox="0 0 16 16">
                            <path d="M8 2a6 6 0 1 0 4.472 10.472l2.528.632a.5.5 0 0 0 .632-.632l-.632-2.528A6 6 0 0 0 8 2zm0 1a5 5 0 1 1-4.472 7.472.5.5 0 0 0-.632.632l.632 2.528a.5.5 0 0 0 .632.632l2.528-.632A5 5 0 0 1 8 3z"/>
                        </svg>
                        الشات
                    </a>
                    <span class="fw-bold text-dark me-3">
                        مرحباً، {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">تسجيل الخروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">دخول</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-success">تسجيل</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="welcome-card text-center">
        <div class="welcome-title mb-3">أهلاً بك في تطبيق الأعمال</div>
        @auth
            <div class="welcome-msg mb-4">
                يسعدنا وجودك معنا، {{ Auth::user()->name }}!
            </div>
        @else
            <div class="welcome-msg mb-4 text-secondary">
                سجل دخولك أو أنشئ حساب جديد للبدء في استخدام التطبيق.
            </div>
        @endauth

        <hr class="my-4">

        <div class="mb-3">
            <h5 class="fw-bold mb-2">ابدأ الآن:</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="https://laravel.com/docs" target="_blank" class="text-danger fw-bold text-decoration-underline">توثيق Laravel</a>
                </li>
                <li class="mb-2">
                    <a href="https://laracasts.com" target="_blank" class="text-success fw-bold text-decoration-underline">دروس فيديو Laracasts</a>
                </li>
                <li>
                    <a href="https://cloud.laravel.com" target="_blank" class="btn btn-dark">انشر مشروعك الآن</a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>