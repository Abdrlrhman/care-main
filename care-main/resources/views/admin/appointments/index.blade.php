@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

        <!-- العنوان وزر الإضافة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">قائمة المواعيد</h1>
            <a href="{{ route('admin.appointments.create') }}" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-xl shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إضافة موعد
            </a>
        </div>

        <!-- جدول سطح المكتب (مخفى على الهواتف) -->
        <div class="hidden md:block">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المريض</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطبيب</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">من</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إلى</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($appointments as $appointment)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $appointment->patient->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment->doctor->name ?? ($appointment->doctor->user->name ?? '-') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment->starts_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment->ends_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($appointment->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($appointment->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ __($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-800 text-xs rounded-lg hover:bg-yellow-200 transition-colors mr-2">
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 text-xs rounded-lg hover:bg-red-200 transition-colors">
                                            حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- بطاقات الهاتف (مظهر رئيسي) -->
        <div class="md:hidden space-y-4 px-4 pb-6">
            @foreach($appointments as $appointment)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                    <!-- رأس البطاقة -->
                    <div class="p-5 pb-3 border-b border-gray-50">
                        <h3 class="font-semibold text-gray-800 text-right">
                            {{ $appointment->patient->name }}
                        </h3>
                        <p class="text-sm text-gray-600 text-right mt-1">
                            الطبيب: {{ $appointment->doctor->name ?? ($appointment->doctor->user->name ?? '-') }}
                        </p>
                    </div>

                    <!-- التفاصيل -->
                    <div class="px-5 pb-4 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">من:</span>
                            <span class="font-medium">{{ $appointment->starts_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">إلى:</span>
                            <span class="font-medium">{{ $appointment->ends_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">الحالة:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($appointment->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($appointment->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ __($appointment->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="px-5 pb-5 pt-2 flex gap-2 border-t border-gray-100">
                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="flex-1 text-center py-3 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-xl border border-yellow-200 hover:bg-yellow-100 transition-colors">
                            تعديل
                        </a>
                        <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-3 bg-red-50 text-red-600 text-sm font-medium rounded-xl border border-red-200 hover:bg-red-100 transition-colors flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- حالة فارغة -->
            @if($appointments->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-1">لا يوجد مواعيد</h3>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto">
                        ابدأ بإضافة أول موعد لتتبع جدولة المرضى والأطباء.
                    </p>
                    <a href="{{ route('admin.appointments.create') }}" class="inline-block mt-4 px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                        إضافة موعد جديد
                    </a>
                </div>
            @endif
        </div>

        <!-- الترقيم -->
        @if($appointments->hasPages())
            <div class="mt-8 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                    {{ $appointments->onEachSide(1)->links('pagination::tailwind') }}
                </nav>
            </div>
        @endif

    </div>
</main>

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