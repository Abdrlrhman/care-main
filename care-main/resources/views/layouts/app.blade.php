<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - Care System</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="{ open: false }" class="min-h-screen flex flex-col">

    <!-- ✅ الترويسة -->
    <header class="flex items-center justify-between bg-blue-600 text-white px-4 py-3 shadow">
        <div class="flex items-center gap-2">
            <img src="{{ asset('logo.png') }}" alt="Care Logo" class="h-8 w-8">
            <span class="font-bold">Care System</span>
        </div>
        <!-- زر القائمة للموبايل -->
        <button @click="open = true" class="md:hidden text-2xl focus:outline-none">☰</button>
    </header>

    <!-- ✅ القائمة الجانبية للموبايل (overlay) -->
   <!-- ✅ القائمة الجانبية للموبايل (overlay) -->
<div
    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex md:hidden"
    x-show="open"
    x-transition
>
    <aside
        class="bg-gradient-to-b from-blue-50 to-white w-64 h-full shadow-xl transform transition-transform duration-300 ease-in-out"
        :class="open ? 'translate-x-0' : 'translate-x-full'"
    >
        <!-- هيدر القائمة -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-white">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="Care Logo" class="h-7 w-7 rounded-md">
                <span class="font-bold text-blue-800 text-lg">Care System</span>
            </div>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 text-2xl transition-colors">&times;</button>
        </div>

        <!-- روابط القائمة -->
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100%-100px)]">

            @role('admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    لوحة التحكم
                </a>
                <a href="{{ route('admin.doctors.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الأطباء
                </a>
                <a href="{{ route('admin.patients.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    المرضى
                </a>
                <a href="{{ route('admin.services.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الخدمات
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الفواتير
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    التقارير
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الإعدادات
                </a>
            @endrole

            @role('doctor')
                <a href="{{ route('doctor.dashboard') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    لوحة التحكم
                </a>
                <a href="{{ route('doctor.calendar') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    التقويم
                </a>
                <a href="{{ route('doctor.medical_records.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    السجلات الطبية
                </a>
                <a href="{{ route('doctor.prescriptions.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الوصفات
                </a>
                <a href="{{ route('doctor.invoices.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الفواتير
                </a>
                <a href="{{ route('doctor.profile.edit') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الملف الشخصي
                </a>
            @endrole

            @role('staff')
                <a href="{{ route('staff.dashboard') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    لوحة التحكم
                </a>
                <a href="{{ route('staff.appointments.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    المواعيد
                </a>
                <a href="{{ route('staff.patients.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    المرضى
                </a>
                <a href="{{ route('staff.invoices.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الفواتير
                </a>
                <a href="{{ route('staff.services.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    الخدمات
                </a>
                <a href="{{ route('staff.reports.daily') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    التقارير
                </a>
            @endrole

            @role('user')
                <a href="{{ route('patient.dashboard') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    لوحة التحكم
                </a>
                <a href="{{ route('appointments.book') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    حجز موعد
                </a>
                <a href="{{ route('patient.appointments.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    مواعيدي
                </a>
                <a href="{{ route('patient.medical_records.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    سجلي الطبي
                </a>
                <a href="{{ route('patient.prescriptions.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    وصفاتي
                </a>
                <a href="{{ route('patient.invoices.index') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    فواتيري
                </a>
                <a href="{{ route('patient.profile.edit') }}" class="nav-link block px-4 py-3 rounded-lg text-gray-700 font-medium text-sm transition-all duration-200 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    ملفي الشخصي
                </a>
            @endrole

            @auth
                <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-gray-200">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-medium text-sm rounded-lg hover:bg-red-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300">
                        تسجيل الخروج
                    </button>
                </form>
            @endauth

        </nav>
    </aside>
</div>

    <!-- ✅ المحتوى -->
    <main class="flex-1 p-4 md:p-6">
        @yield('content')
    </main>
</div>

<style>
    .nav-link {
        @apply block px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-600 text-sm font-medium;
    }
</style>

</body>
</html>
