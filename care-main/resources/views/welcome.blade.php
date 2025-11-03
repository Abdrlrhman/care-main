<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ \Illuminate\Support\Str::limit(\App\Models\Setting::get('clinic_name','Care'), 60) }} | نظام إدارة العيادات</title>
  @vite('resources/css/app.css')
  <style>
    /* صغير لتفادي overflow عرضي على بعض الشاشات */
    html,body{overflow-x:hidden;}
  </style>
</head>
<body class="antialiased bg-gradient-to-b from-white to-gray-50 text-gray-800">

  @php
    $clinicName = \App\Models\Setting::get('clinic_name', 'Care');
    $clinicLogo = \App\Models\Setting::get('clinic_logo', '');
    $clinicAddress = \App\Models\Setting::get('clinic_address', '');
    $clinicPhone = \App\Models\Setting::get('clinic_phone', '');
    $clinicEmail = \App\Models\Setting::get('clinic_email', '');
  @endphp

  <div class="min-h-screen flex flex-col">

    <!-- Hero -->
    <header class="w-full bg-white/50 backdrop-blur-sm shadow-sm">
      <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          @if($clinicLogo)
            <img src="{{ $clinicLogo }}" alt="logo" class="h-12 w-12 rounded-lg object-cover shadow-sm">
          @else
            <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 font-bold shadow-sm">C</div>
          @endif
          <div>
            <div class="text-lg font-bold leading-none">{{ $clinicName }}</div>
            <div class="text-xs text-gray-500">نظام إدارة العيادات</div>
          </div>
        </div>

        <nav class="flex items-center gap-2">
          @guest
            <a href="{{ route('login') }}" class="hidden sm:inline-block px-3 py-2 rounded text-sm font-medium text-blue-600 border border-blue-600 hover:bg-blue-50 transition">تسجيل الدخول</a>
            <a href="{{ route('register.patient') }}" class="hidden sm:inline-block px-3 py-2 rounded text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition">سجِّل كمريض</a>
          @else
            <a href="{{ url('/') }}" class="px-3 py-2 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 transition">لوحة التحكم</a>
          @endguest
          <button id="menuBtn" class="sm:hidden px-2 py-2 rounded bg-gray-100" aria-label="قائمة">☰</button>
        </nav>
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4">
      <div class="max-w-xl w-full">

        <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 sm:p-8">
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0">
                @if($clinicLogo)
                  <img src="{{ $clinicLogo }}" alt="logo" class="h-20 w-20 rounded-xl object-cover shadow">
                @else
                  <div class="h-20 w-20 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-extrabold text-2xl shadow">C</div>
                @endif
              </div>
              <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight text-gray-800">{{ $clinicName }}</h1>
                <p class="mt-1 text-sm text-gray-500">نظام متكامل لحجز المواعيد، إدارة المرضى، والفواتير — سهل وسريع على الهاتف.</p>
              </div>
            </div>

            <!-- Quick stats / features -->
            <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
              <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                <div class="text-xs text-gray-500">سهل الاستخدام</div>
                <div class="font-semibold text-gray-700">واجهة مخصّصة للهاتف</div>
              </div>
              <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                <div class="text-xs text-gray-500">آمن ومرن</div>
                <div class="font-semibold text-gray-700">تحكم بالصلاحيات</div>
              </div>
              <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                <div class="text-xs text-gray-500">تقارير</div>
                <div class="font-semibold text-gray-700">مخططات و CSV</div>
              </div>
              <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                <div class="text-xs text-gray-500">دعم</div>
                <div class="font-semibold text-gray-700">إخطارات وإيميل</div>
              </div>
            </div>

            <!-- CTA Buttons (mobile first big buttons) -->
            <div class="mt-6 grid grid-cols-1 gap-3">
              @guest
                <a href="{{ route('register.patient') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-blue-600 text-white font-bold text-base shadow hover:bg-blue-700 transition">
                  إنشاء حساب كمريض
                </a>
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-white text-blue-600 font-semibold border border-blue-600 hover:bg-blue-50 transition">
                  تسجيل الدخول
                </a>
              @else
                <a href="{{ url('/') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-green-600 text-white font-bold text-base shadow hover:bg-green-700 transition">
                  انتقل إلى لوحة التحكم
                </a>
              @endguest
            </div>

            <!-- Contact / info -->
            <div class="mt-6 text-sm text-gray-600 space-y-2">
              @if($clinicAddress)
                <div class="flex items-start gap-2">
                  <svg class="h-4 w-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2C8 2 2 7 2 12c0 5 5 9 10 12 5-3 10-7 10-12 0-5-6-10-10-10z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <div>{{ $clinicAddress }}</div>
                </div>
              @endif

              @if($clinicPhone)
                <div class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5h2l2 5-2 2a11 11 0 005 5l2-2 5 2v2a2 2 0 01-2 2A17 17 0 013 5z" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <a href="tel:{{ $clinicPhone }}" class="text-blue-600 font-medium">{{ $clinicPhone }}</a>
                </div>
              @endif

              @if($clinicEmail)
                <div class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l9 6 9-6" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 8v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <a href="mailto:{{ $clinicEmail }}" class="text-blue-600 font-medium">{{ $clinicEmail }}</a>
                </div>
              @endif
            </div>

          </div>

          <!-- decorative footer -->
          <footer class="bg-gradient-to-r from-blue-50 to-white/50 p-4 sm:p-5 text-center text-xs text-gray-500">
            © {{ date('Y') }} {{ $clinicName }} — جميع الحقوق محفوظة
          </footer>
        </section>

      </div>
    </main>

    <!-- bottom mobile bar CTA -->
    @guest
      <div class="fixed bottom-3 left-0 right-0 px-4 sm:hidden">
        <div class="max-w-3xl mx-auto flex gap-3">
          <a href="{{ route('register.patient') }}" class="flex-1 text-center px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow">سجِّل كمريض</a>
          <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-3 rounded-xl bg-white text-blue-600 border border-blue-600">دخول</a>
        </div>
      </div>
    @endguest

  </div>

</body>
</html>
