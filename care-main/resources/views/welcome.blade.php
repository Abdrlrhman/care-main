<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كير | نظام إدارة العيادات</title>

    {{-- Tailwind --}}
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-50 text-gray-800">

    <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-blue-600 selection:text-white">
        <!-- العنوان الرئيسي -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-extrabold text-blue-700 mb-4 tracking-tight">Care</h1>
            <p class="text-xl text-gray-600">تطبيق متكامل لإدارة العيادات الطبية بسهولة واحترافية</p>
        </div>

        <!-- روابط حسب حالة تسجيل الدخول -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            @guest
                <a href="{{ url('/login') }}"
                   class="px-8 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition font-semibold shadow">
                   تسجيل الدخول
                </a>
                <a href="{{ url('/register') }}"
                   class="px-8 py-3 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 transition font-semibold shadow">
                   إنشاء حساب
                </a>
            @endguest
            @auth
                @if(auth()->user()->hasRole('doctor'))
                    <a href="{{ url('/doctor/dashboard') }}"
                       class="px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-semibold shadow">
                       لوحة تحكم الطبيب
                    </a>
                @elseif(auth()->user()->hasRole('staff'))
                    <a href="{{ url('/staff/dashboard') }}"
                       class="px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-semibold shadow">
                       لوحة تحكم موظف العيادة
                    </a>
                @elseif(auth()->user()->hasRole('patient'))
                    <a href="{{ url('/patient/dashboard') }}"
                       class="px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-semibold shadow">
                       لوحة تحكم المريض
                    </a>
                @elseif(auth()->user()->hasRole('admin'))
                    <a href="{{ url('/admin/dashboard') }}"
                       class="px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-semibold shadow">
                       لوحة تحكم المدير
                    </a>
                @endif
            @endauth
        </div>

        <!-- الفوتر -->
        <div class="mt-16 text-gray-500 text-sm text-center">
            <p>© {{ date('Y') }} جميع الحقوق محفوظة لتطبيق <span class="text-blue-600 font-semibold">كير</span></p>
        </div>
    </div>
</body>
</html>
