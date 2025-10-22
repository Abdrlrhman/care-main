@extends('layouts.app')

@section('content')
<div class="container py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- العنوان وزر الإضافة (مُحسّن للهاتف) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center">
                <span class="mr-2">👥</span>
                دليل المرضى
            </h1>
            <a href="{{ route('admin.patients.create') ?? '#' }}" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm sm:text-base font-medium rounded-xl shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all w-full sm:w-auto justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إضافة مريض
            </a>
        </div>

        <!-- نموذج الفلترة — مُعاد تصميمه كـ "مجموعة مدمجة" مناسبة للهاتف -->
        <form method="GET" class="space-y-3 mb-6 p-4 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="grid grid-cols-2 gap-3">
                <!-- الجنس -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">الجنس</label>
                    <select name="gender" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">أي جنس</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

            </div>
            <div class="grid grid-cols-2 gap-3">
                <!-- العمر (من - إلى) -->
                <!-- العمر (من - إلى) -->
<div>
    <label class="block text-xs font-medium text-gray-700 mb-1">العمر</label>
    <div class="flex gap-2">
        <!-- حقل "من" أصغر بكثير -->
        <input name="min_age" type="number" class="w-16 px-2 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-center" placeholder="من" value="{{ request('min_age') }}" min="0" max="150">

        <!-- فاصل جميل -->
        <span class="flex items-center text-gray-500 text-sm px-1">إلى</span>

        <!-- حقل "إلى" أوسع قليلاً -->
        <input name="max_age" type="number" class="flex-1 px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="إلى" value="{{ request('max_age') }}" min="0" max="150">
    </div>
</div>
                </div>
            <div class="grid grid-cols-2 gap-3">
                <!-- تاريخ التسجيل -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">تسجيل من</label>
                    <input name="registered_from" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" value="{{ request('registered_from') }}">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">إلى</label>
                    <input name="registered_to" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" value="{{ request('registered_to') }}">
                </div>
            </div>

            <!-- زر الفلترة — كبير ومميز -->
            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-xl shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                تصفية
            </button>
        </form>

        <!-- رسالة النتائج -->
        @if(request()->hasAny(['gender', 'min_age', 'max_age', 'registered_from', 'registered_to']))
            <p class="text-sm text-gray-600 mb-4 text-right">
                تم تطبيق {{ count(array_filter(request()->all())) }} فلتر
            </p>
        @endif

        <!-- جدول سطح المكتب — مُحسّن ليكون أنيقًا وسهل القراءة -->
        <div class="hidden md:block">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرمز</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الجنس</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العمر</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر زيارة</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($patients as $p)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->code ?? $p->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.patients.show', $p->id) ?? '#' }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $p->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($p->gender ?? '-') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $p->phone ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if(optional($visits[$p->id] ?? null)->last_visit)
                                        <span class="text-green-600 font-medium">
                                            {{ \Carbon\Carbon::parse($visits[$p->id]->last_visit)->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">لم يزر</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $p->created_at ? $p->created_at->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin.patients.show', $p->id) ?? '#' }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition-colors mr-2">
                                        عرض
                                    </a>
                                    <form method="POST" action="{{ route('admin.patients.deactivate', $p->id) }}" class="inline-block" onsubmit="return confirm('هل أنت متأكد من تعطيل هذا المريض؟')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 text-xs rounded-lg hover:bg-red-200 transition-colors">
                                            تعطيل
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- بطاقات الهاتف — مُعاد تصميمها بالكامل لتجربة استخدام ممتازة -->
        <div class="md:hidden space-y-3 px-4 pb-6">
            @foreach($patients as $p)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                    <!-- رأس البطاقة -->
                    <div class="p-5 pb-3 border-b border-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 leading-tight">
                                <a href="{{ route('admin.patients.show', $p->id) ?? '#' }}" class="text-blue-600 hover:text-blue-700">
                                    {{ $p->name }}
                                </a>
                            </h3>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                                {{ $p->code ?? '#' . $p->id }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ ucfirst($p->gender ?? '-') }}</p>
                    </div>

                    <!-- التفاصيل -->
                    <div class="px-5 pb-4 space-y-2">
                        <!-- الهاتف -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948 1.372l2.68 8.05A1 1 0 0110.63 17H17a2 2 0 012 2v3.28a1 1 0 01-1.372.948l-8.05-2.68A1 1 0 015.63 17H4a2 2 0 01-2-2V5z" />
                            </svg>
                            <span class="text-gray-700">{{ $p->phone ?? '-' }}</span>
                        </div>

                        <!-- العمر -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-700">
                                العمر: <strong>{{ $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age : '-' }}</strong>
                            </span>
                        </div>

                        <!-- آخر زيارة -->
                        <div class="flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700">
                                آخر زيارة: 
                                <strong class="{{ optional($visits[$p->id] ?? null)->last_visit ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ optional($visits[$p->id] ?? null)->last_visit ? \Carbon\Carbon::parse($visits[$p->id]->last_visit)->diffForHumans() : 'لم يزر' }}
                                </strong>
                            </span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات (مثبتة في الأسفل) -->
                    <div class="px-5 pb-5 pt-2 flex gap-2 border-t border-gray-100">
                        <a href="{{ route('admin.patients.show', $p->id) ?? '#' }}" class="flex-1 text-center py-3 bg-gray-50 text-gray-700 text-sm font-medium rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors">
                            عرض التفاصيل
                        </a>
                        <form method="POST" action="{{ route('admin.patients.deactivate', $p->id) }}" onsubmit="return confirm('هل أنت متأكد من تعطيل هذا المريض؟')">
                            @csrf
                            <button type="submit" class="px-4 py-3 bg-red-50 text-red-600 text-sm font-medium rounded-xl border border-red-200 hover:bg-red-100 transition-colors flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                تعطيل
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- حالة فارغة — مُحسّنة بشكل كامل -->
            @if($patients->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-1">لا يوجد مرضى مسجلين</h3>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto mb-6">
                        ابدأ بإضافة أول مريض لتتبع السجلات الطبية وتنظيم المواعيد.
                    </p>
                    <a href="{{ route('admin.patients.create') }}" class="inline-block px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                        إضافة مريض الآن
                    </a>
                </div>
            @endif
        </div>

        <!-- الترقيم — مُحسّن للهاتف -->
        @if($patients->hasPages())
            <div class="mt-8 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                    {{ $patients->onEachSide(1)->links('pagination::tailwind') }}
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