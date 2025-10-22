@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">

        <!-- العنوان وزر العودة -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">تعديل الموعد</h1>
            <a href="{{ route('admin.appointments.index') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800 flex items-center gap-1">
                ← العودة للقائمة
            </a>
        </div>

        <!-- نموذج التعديل -->
        <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- من (تاريخ ووقت) -->
            <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2 text-right">من <span class="text-red-600">*</span></label>
                <input type="datetime-local"
                       name="starts_at"
                       id="starts_at"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
                       value="{{ $appointment->starts_at->format('Y-m-d\TH:i') }}"
                       required
                       min="{{ now()->addMinutes(1)->format('Y-m-d\TH:i') }}">
            </div>

            <!-- إلى (تاريخ ووقت) -->
            <div>
                <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2 text-right">إلى <span class="text-red-600">*</span></label>
                <input type="datetime-local"
                       name="ends_at"
                       id="ends_at"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
                       value="{{ $appointment->ends_at->format('Y-m-d\TH:i') }}"
                       required
                       min="{{ $appointment->starts_at->addMinutes(15)->format('Y-m-d\TH:i') }}">
            </div>

            <!-- الملاحظات -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2 text-right">ملاحظات</label>
                <textarea name="notes"
                          id="notes"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
                          rows="4"
                          placeholder="اكتب أي تعديلات أو ملاحظات إضافية...">{{ $appointment->notes }}</textarea>
            </div>

            <!-- زر الحفظ -->
            <button type="submit" class="w-full bg-yellow-600 text-white py-4 rounded-xl font-medium text-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all shadow-md">
                حفظ التعديلات
            </button>
        </form>

        <!-- معلومات إضافية للمستخدم (اختياري) -->
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-right">
            <p class="text-sm text-yellow-800">
                <strong>تم الحجز في:</strong> {{ $appointment->created_at->diffForHumans() }}<br>
                <strong>المريض:</strong> {{ $appointment->patient->name }}<br>
                <strong>الطبيب:</strong> {{ $appointment->doctor->name ?? '-' }}
            </p>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startsAt = document.getElementById('starts_at');
    const endsAt = document.getElementById('ends_at');

    // تحديث "إلى" تلقائيًا عند تغيير "من"
    startsAt.addEventListener('change', function() {
        if (this.value) {
            const start = new Date(this.value);
            start.setMinutes(start.getMinutes() + 15); // الأقل 15 دقيقة بعد البداية
            const minEnd = start.toISOString().slice(0, 16);
            endsAt.min = minEnd;
            if (endsAt.value < minEnd) {
                endsAt.value = minEnd;
            }
        }
    });

    // تقييد "من" ليكون بعد الآن (بعد دقيقة واحدة على الأقل)
    const now = new Date();
    now.setMinutes(now.getMinutes() + 1);
    const nowStr = now.toISOString().slice(0, 16);
    startsAt.min = nowStr;
});
</script>

<style>
    /* تحسين مؤشر التقويم على iOS */
    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
</style>
@endsection