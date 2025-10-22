@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">

        <!-- العنوان -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">إضافة موعد جديد</h1>
            <a href="{{ route('admin.appointments.index') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800 flex items-center gap-1">
                ← العودة للقائمة
            </a>
        </div>

        <!-- نموذج الإضافة -->
        <form method="POST" action="{{ route('admin.appointments.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf

            <!-- المريض -->
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2 text-right">المريض <span class="text-red-600">*</span></label>
                <select name="patient_id" id="patient_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right" required>
                    <option value="">-- اختر مريضًا --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->name }} ({{ $p->code ?? '#' . $p->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- الطبيب -->
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2 text-right">الطبيب <span class="text-red-600">*</span></label>
                <select name="doctor_id" id="doctor_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right" required>
                    <option value="">-- اختر طبيبًا --</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">
                            {{ $d->name }}
                            @if($d->specialty)
                                <span class="text-xs text-gray-500">({{ $d->specialty }})</span>
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- من (تاريخ ووقت) -->
            <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2 text-right">من <span class="text-red-600">*</span></label>
                <input type="datetime-local"
                       name="starts_at"
                       id="starts_at"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
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
                       required
                       min="{{ now()->addMinutes(2)->format('Y-m-d\TH:i') }}">
            </div>

            <!-- الملاحظات -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2 text-right">ملاحظات</label>
                <textarea name="notes"
                          id="notes"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
                          rows="4"
                          placeholder="اكتب أي ملاحظات إضافية..."></textarea>
            </div>

            <!-- زر الحفظ -->
            <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-medium text-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-md">
                حفظ الموعد
            </button>
        </form>

    </div>
</main>

<script>
// تقييد وقت "إلى" ليكون دائمًا بعد "من"
document.addEventListener('DOMContentLoaded', function() {
    const startsAt = document.getElementById('starts_at');
    const endsAt = document.getElementById('ends_at');

    startsAt.addEventListener('change', function() {
        if (this.value) {
            const start = new Date(this.value);
            // تعيين "إلى" على الأقل 15 دقيقة بعد "من"
            start.setMinutes(start.getMinutes() + 15);
            const minEnd = start.toISOString().slice(0, 16);
            endsAt.min = minEnd;
            endsAt.value = minEnd;
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
    /* تحسين التواريخ على iOS */
    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
</style>
@endsection