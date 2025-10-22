@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <!-- العنوان وزر العودة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">تعديل ساعات العمل — د. {{ $doctor->name }}</h1>
            <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all">
                ← العودة للملف الشخصي
            </a>
        </div>

        <!-- عرض رسائل الخطأ -->
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 text-right">
                <ul class="text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>
                            @if(str_contains($error, 'weekday'))
                                <strong>خطأ في اليوم:</strong> يرجى اختيار يوم صحيح من القائمة.
                            @elseif(str_contains($error, 'start_time') || str_contains($error, 'end_time'))
                                <strong>خطأ في الوقت:</strong> يرجى إدخال الوقت بالتنسيق الصحيح (مثال: 08:00)، ويجب أن يكون وقت النهاية بعد وقت البداية.
                            @else
                                {{ $error }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 text-right">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif
        <!-- تعليمات المستخدم -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-6 text-right">
            <p class="text-sm text-blue-800">
                <strong>اختر أيام وأوقات العمل</strong> من خلال واجهة بسيطة أدناه — لا حاجة لكتابة أي رمز.
            </p>
        </div>

        <!-- نموذج التعديل -->
    <form method="POST" action="{{ route('admin.doctors.working-hours.store', $doctor->id) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6" id="working-hours-form" onsubmit="return validateWorkingHoursForm();">
            @csrf

            <!-- عرض الأيام -->
            <div class="space-y-6">
                @php
                    $days = [
                        0 => 'الأحد',
                        1 => 'الاثنين',
                        2 => 'الثلاثاء',
                        3 => 'الأربعاء',
                        4 => 'الخميس',
                        5 => 'الجمعة',
                        6 => 'السبت'
                    ];
                    
                    $existingIntervals = $doctor->workingHours->map(function($item) {
                        return [
                            'weekday' => $item->weekday,
                            'start' => $item->start_time,
                            'end' => $item->end_time,
                        ];
                    })->toArray();
                    $byDay = [];
                    foreach ($existingIntervals as $item) {
                        $day = $item['weekday'] ?? 0;
                        if (!isset($byDay[$day])) $byDay[$day] = [];
                        $byDay[$day][] = ['start' => $item['start'], 'end' => $item['end']];
                    }
                @endphp

                @foreach($days as $weekday => $dayName)
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-gray-800">{{ $dayName }}</h3>
                            <button type="button" class="btn-add-period text-blue-600 text-sm hover:text-blue-800 font-medium flex items-center gap-1" onclick="addPeriod({{ $weekday }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                إضافة فترة
                            </button>
                        </div>

                        <div class="space-y-3" id="day-{{ $weekday }}">
                            @if(isset($byDay[$weekday]) && is_array($byDay[$weekday]))
                                @foreach($byDay[$weekday] as $period)
                                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center p-3 bg-gray-50 rounded-lg period-row">
                                        <input type="hidden" name="periods[{{ $weekday }}][weekday][]" value="{{ $weekday }}">
                                        <input type="time" name="periods[{{ $weekday }}][start_time][]" value="{{ $period['start'] }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <span class="text-gray-500">إلى</span>
                                        <input type="time" name="periods[{{ $weekday }}][end_time][]" value="{{ $period['end'] }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <small class="text-xs text-gray-400 block w-full">التنسيق المطلوب: HH:MM مثال: 08:00</small>
                                        <button type="button" class="btn-remove-period text-red-600 hover:text-red-800 font-medium text-sm flex-shrink-0" onclick="removePeriod(this)">
                                            حذف
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 italic">لا توجد فترات مضافة لهذا اليوم.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- أزرار الحفظ والعودة -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-medium text-sm rounded-xl shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    حفظ التغييرات
                </button>
                <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="w-full sm:w-auto px-6 py-3 bg-gray-100 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all text-center">
                    إلغاء
                </a>
            </div>
        </form>

       

    </div>
</main>

<script>
function addPeriod(day) {
    const container = document.getElementById('day-' + day);
    const div = document.createElement('div');
    div.className = 'flex flex-col sm:flex-row gap-3 items-start sm:items-center p-3 bg-gray-50 rounded-lg period-row';
    div.innerHTML = `
        <input type="hidden" name="periods[${day}][weekday][]" value="${day}">
        <input type="time" name="periods[${day}][start_time][]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
        <span class="text-gray-500">إلى</span>
        <input type="time" name="periods[${day}][end_time][]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
        <small class="text-xs text-gray-400 block w-full">التنسيق المطلوب: HH:MM مثال: 08:00</small>
        <button type="button" class="btn-remove-period text-red-600 hover:text-red-800 font-medium text-sm flex-shrink-0" onclick="removePeriod(this)">
            حذف
        </button>
    `;
    container.appendChild(div);
}

function removePeriod(btn) {
    btn.closest('.period-row').remove();
}

function validateWorkingHoursForm() {
    let valid = true;
    let errorMessages = [];
    // تحقق من كل فترة
    document.querySelectorAll('.period-row').forEach(function(row) {
        const weekdayInput = row.querySelector('input[type="hidden"][name*="weekday"]');
        const startInput = row.querySelector('input[type="time"][name*="start_time"]');
        const endInput = row.querySelector('input[type="time"][name*="end_time"]');
        let weekday = weekdayInput ? weekdayInput.value : null;
        let start = startInput ? startInput.value : null;
        let end = endInput ? endInput.value : null;
        // تحقق من اليوم
        if (weekday === null || isNaN(parseInt(weekday)) || parseInt(weekday) < 0 || parseInt(weekday) > 6) {
            valid = false;
            errorMessages.push('يرجى اختيار يوم صحيح من القائمة.');
        }
        // تحقق من الوقت
        if (!/^\d{2}:\d{2}$/.test(start) || !/^\d{2}:\d{2}$/.test(end)) {
            valid = false;
            errorMessages.push('يرجى إدخال الوقت بالتنسيق الصحيح (مثال: 08:00).');
        } else if (start >= end) {
            valid = false;
            errorMessages.push('يجب أن يكون وقت النهاية بعد وقت البداية.');
        }
    });
    if (!valid) {
        alert(errorMessages.join('\n'));
    }
    return valid;
}
</script>

<style>
    /* تحسين التنسيق واللمس */
    input[type="time"] {
        min-width: 160px;
        height: 48px;
        font-size: 1.25rem;
        padding: 0 12px;
        border-radius: 12px;
        border: 2px solid #a0aec0;
        background: #f7fafc;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        touch-action: manipulation;
    }
    .period-row {
        gap: 1.5rem;
    }
    button, .btn-add-period, .btn-remove-period {
        min-height: 48px;
        font-size: 1.1rem;
        border-radius: 12px;
        padding: 0 16px;
        touch-action: manipulation;
    }
</style>
@endsection