@extends('layouts.app')

@section('content')
<div class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        <!-- العنوان ونموذج الفلترة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">دليل الأطباء</h1>
                <!-- زر إضافة طبيب جديد -->
                <a href="{{ route('admin.doctors.create') }}"
                   class="inline-block px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                    + إضافة طبيب
                </a>
            </div>

            <!-- نموذج الفلترة - مخصص للهاتف -->
            <form method="GET" action="{{ route('admin.doctors.index') }}" class="w-full sm:w-auto">
                <div class="flex gap-2">
                    <input name="specialty" type="text"
                           class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm w-full sm:w-48"
                           placeholder="فلترة بالتخصص"
                           value="{{ request('specialty') }}">
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        تصفية
                    </button>
                </div>
            </form>
        </div>

        <!-- رسالة الفلترة (اختيارية) -->
        @if(request('specialty'))
            <p class="text-sm text-gray-600 mb-6 text-right">
                تم تطبيق فلتر: <strong>"{{ request('specialty') }}"</strong>
            </p>
        @endif

        <!-- جدول سطح المكتب (مخفى على الهواتف) -->
        <div class="hidden md:block">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التخصص</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">عدد المرضى</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">أيام العمل</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($doctors as $doctor)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.doctors.show', optional($doctor)->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ optional($doctor)->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ optional($doctor)->specialty ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ optional($doctor)->phone ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                    {{ data_get($patientsCounts, optional($doctor)->id, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                    {{ data_get($wh, optional($doctor)->id . '.days', 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin.doctors.show', optional($doctor)->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition-colors mr-2">
                                        الملف الشخصي
                                    </a>
                                    <a href="{{ route('admin.doctors.working-hours.edit', optional($doctor)->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                        مواعيد العمل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- بطاقات الهاتف (مظهر رئيسي) -->
        <div class="md:hidden space-y-4 px-4 pb-6">
            @foreach($doctors as $doctor)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                    <!-- رأس البطاقة -->
                    <div class="p-5 pb-3 border-b border-gray-50">
                        <h3 class="font-bold text-gray-800 leading-tight">
                            <a href="{{ route('admin.doctors.show', optional($doctor)->id) }}" class="text-blue-600 hover:text-blue-700">
                                {{ optional($doctor)->name ?? '-' }}
                            </a>
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">{{ optional($doctor)->specialty ?? '—' }}</p>
                    </div>

                    <!-- التفاصيل -->
                    <div class="px-5 pb-4 space-y-2">
                        <!-- الهاتف -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948 1.372l2.68 8.05A1 1 0 0110.63 17H17a2 2 0 012 2v3.28a1 1 0 01-1.372.948l-8.05-2.68A1 1 0 015.63 17H4a2 2 0 01-2-2V5z" />
                            </svg>
                            <span class="text-gray-700">{{ optional($doctor)->phone ?? '—' }}</span>
                        </div>

                        <!-- عدد المرضى -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.738 4h-4.996m0 0v4.996M12 12v4" />
                            </svg>
                            <span class="text-gray-700">
                                المرضى: <strong>{{ data_get($patientsCounts, optional($doctor)->id, 0) }}</strong>
                            </span>
                        </div>

                        <!-- أيام العمل -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700">
                                أيام العمل: <strong>{{ data_get($wh, optional($doctor)->id . '.days', 0) }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="px-5 pb-5 pt-2 flex gap-2 border-t border-gray-100">
                        <a href="{{ route('admin.doctors.show', optional($doctor)->id) }}" class="flex-1 text-center py-3 bg-gray-50 text-gray-700 text-sm font-medium rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors">
                            الملف الشخصي
                        </a>
                        <a href="{{ route('admin.doctors.working-hours.edit', optional($doctor)->id) }}" class="px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            مواعيد العمل
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- حالة فارغة -->
            @if($doctors->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-1">لا يوجد أطباء مسجلين</h3>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto">
                        ابدأ بإضافة أول طبيب لتنظيم المواعيد والسجلات الطبية.
                    </p>
                    <a href="{{ route('admin.doctors.create') }}" class="inline-block mt-4 px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                        إضافة طبيب جديد
                    </a>
                </div>
            @endif
        </div>

        <!-- الترقيم -->
        @if($doctors->hasPages())
            <div class="mt-8 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                    {{ $doctors->onEachSide(1)->links('pagination::tailwind') }}
                </nav>
            </div>
        @endif

    </div>
</div>

<!-- تأثير الحركة الخفيف للبطاقات -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endsection