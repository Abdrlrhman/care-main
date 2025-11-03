<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>لوحة التحكم - Care System</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite: css + main js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- x-cloak helper --}}
    <style>
      [x-cloak] { display: none !important; }

      /* لو داير تغيّر العرض بتاع السايدبار للكمبيوتر عدّل w-72 */
      @media (min-width: 768px) {
        /* لو عايز السايدبار ثابت تحت الهيدر ممكن تضيف top حسب ارتفاع الهيدر */
      }
    </style>
    <style>
  /* منع السكّول الأفقي العام (آمن لو عملت overflow-x-auto للمحتوى اللي محتاج سكّول) */
  html, body {
    max-width: 100%;
    overflow-x: hidden;
  }

  /* تحسين التمرير الأفقي على الموبايل */
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }

  /* لو في تيبل كبيرة خليها قابلة للتمرير بدل ما تكسر اللايـوت */
  .table-responsive { overflow-x: auto; }

  /* تجنّب ارتفاع/عرض إضافي من عناصر داخل الفليكـس */
  .min-w-0 { min-width: 0; }
</style>

</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="{ open: false, loading: true }"
     x-init="window.addEventListener('load', ()=> loading = false); /*fallback*/ setTimeout(()=> loading = false, 2500)"
     x-bind:class="(open || loading) ? 'overflow-hidden' : ''"
     class="min-h-screen flex flex-col">

    {{-- ---------- LOADER (full-screen) ---------- --}}
    <div x-show="loading" x-cloak
         x-transition.opacity
         class="fixed inset-0 z-60 bg-white/80 flex items-center justify-center">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.15" stroke-width="4"></circle>
                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
            </svg>
            <p class="mt-3 text-gray-700 font-medium">جاري تحميل الصفحة ...</p>
        </div>
    </div>

    <!-- الترويسة -->
    <header class="flex items-center justify-between bg-blue-600 text-white px-4 py-3 shadow">
        <div class="flex items-center gap-2">
            <svg viewBox="0 0 40 40" class="h-8 w-8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="heartGradient" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#3B82F6" />
                        <stop offset="1" stop-color="#93C5FD" />
                    </linearGradient>
                </defs>
                <path d="M20 36s-13-8.79-13-18C7 10.5 12 6 18 6c2.5 0 4.75 1.35 6 3.5C25.25 7.35 27.5 6 30 6 36 6 41 10.5 41 18c0 9.21-21 18-21 18z" fill="url(#heartGradient)" />
            </svg>
            <span class="font-bold">Care System</span>
        </div>

        <!-- زر القائمة للموبايل -->
        <div class="flex items-center gap-3">
            <button @click="open = true" class="md:hidden text-2xl focus:outline-none" aria-label="فتح القائمة">☰</button>

            {{-- ممكن تضيف اسم المستخدم / avatar هنا --}}
        </div>
    </header>

    {{-- ====== المحتوى: سايدبار ديسكتوب ثابت + main ====== --}}
    <div class="flex-1 flex">

        {{-- Desktop sidebar (ظاهر في md وما فوق) --}}
        <aside class="hidden md:block w-72 bg-white shadow-xl z-40">
            <div class="p-5 border-b border-gray-200 bg-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-blue-800 text-lg">Care System</span>
                </div>
            </div>

            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-64px)]" aria-label="القائمة الرئيسية">
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">لوحة التحكم</a>
                    <a href="{{ route('admin.doctors.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الأطباء</a>
                    <a href="{{ route('admin.patients.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المرضى</a>
                    <a href="{{ route('admin.services.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الخدمات</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المواعيد</a>
                    <a href="{{ route('admin.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقارير</a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الإعدادات</a>
                @endrole

                @role('doctor')
                    <a href="{{ route('doctor.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('doctor.calendar') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقويم</a>
                    <a href="{{ route('doctor.medical_records.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">السجلات الطبية</a>
                    <a href="{{ route('doctor.prescriptions.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الوصفات</a>
                    <a href="{{ route('doctor.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('doctor.profile.edit') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الملف الشخصي</a>
                @endrole

                @role('staff')
                    <a href="{{ route('staff.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المواعيد</a>
                    <a href="{{ route('staff.patients.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المرضى</a>
                    <a href="{{ route('staff.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('staff.services.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الخدمات</a>
                    <a href="{{ route('staff.reports.daily') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقارير</a>
                @endrole

                @role('user')
                    <a href="{{ route('patient.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">مواعيدي</a>
                    <a href="{{ route('patient.medical_records.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">سجلي الطبي</a>
                    <a href="{{ route('patient.prescriptions.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">وصفاتي</a>
                    <a href="{{ route('patient.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">فواتيري</a>
                    <a href="{{ route('patient.profile.edit') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">ملفي الشخصي</a>
                @endrole

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-gray-200">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-medium text-sm rounded-lg hover:bg-red-50">تسجيل الخروج</button>
                    </form>
                @endauth
            </nav>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 p-4 md:p-6 min-w-0">
            {{-- رسائل التنبيه --}}
            @if (session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{-- المحتوى --}}
            @yield('content')
        </main>
    </div>

    {{-- ====== Mobile overlay sidebar (only on small screens) ====== --}}
    <div class="fixed inset-0 z-50 md:hidden" x-show="open" x-cloak
         x-transition.opacity
         aria-hidden="false">
        <!-- خلفية ديم -->
        <div class="absolute inset-0 bg-black/50" @click="open = false" aria-hidden="true"></div>

        <!-- اللوحة الجانبية للموبايل -->
        <aside
            class="absolute right-0 top-0 h-full w-72 bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
            x-show="open"
            x-transition:enter="transform transition"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.away="open = false"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-white">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-blue-800 text-lg">Care System</span>
                </div>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 text-2xl transition-colors" aria-label="اغلاق">&times;</button>
            </div>

            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100%-100px)]" aria-label="القائمة الرئيسية">
                {{-- نفس الروابط زي الديسكتوب --}}
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">لوحة التحكم</a>
                    <a href="{{ route('admin.doctors.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الأطباء</a>
                    <a href="{{ route('admin.patients.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المرضى</a>
                    <a href="{{ route('admin.services.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الخدمات</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المواعيد</a>
                    <a href="{{ route('admin.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقارير</a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الإعدادات</a>
                    <a href="{{ route('admin.staff.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700"> الموظفين</a>
                @endrole

                @role('doctor')
                    <a href="{{ route('doctor.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('doctor.calendar') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقويم</a>
                    <a href="{{ route('doctor.medical_records.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">السجلات الطبية</a>
                    <a href="{{ route('doctor.prescriptions.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الوصفات</a>
                    <a href="{{ route('doctor.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('doctor.profile.edit') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الملف الشخصي</a>
                @endrole

                @role('staff')
                    <a href="{{ route('staff.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المواعيد</a>
                    <a href="{{ route('staff.patients.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">المرضى</a>
                    <a href="{{ route('staff.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الفواتير</a>
                    <a href="{{ route('staff.services.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">الخدمات</a>
                    <a href="{{ route('staff.reports.daily') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">التقارير</a>
                @endrole

                @role('user')
                    <a href="{{ route('patient.dashboard') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">لوحة التحكم</a>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">مواعيدي</a>
                    <a href="{{ route('patient.medical_records.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">سجلي الطبي</a>
                    <a href="{{ route('patient.prescriptions.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">وصفاتي</a>
                    <a href="{{ route('patient.invoices.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">فواتيري</a>
                    <a href="{{ route('patient.profile.edit') }}" class="block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm hover:bg-blue-50 hover:text-blue-700">ملفي الشخصي</a>
                @endrole

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-gray-200">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-medium text-sm rounded-lg hover:bg-red-50">تسجيل الخروج</button>
                    </form>
                @endauth
            </nav>
        </aside>
    </div>

</div>

</body>
</html>
