@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">

        <!-- العنوان -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-right">حجز موعد</h1>

        <!-- عامل التقدم (Wizard) -->
        <div id="wizard" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-6">

            <!-- الخطوة 1: اختيار التخصص -->
            <div class="step relative">
                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">اختر التخصص</label>
                <select id="specialty" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right">
                    <option value="">-- اختر تخصصًا --</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <!-- الخطوة 2: اختيار الطبيب (مخفية افتراضيًا) -->
            <div id="step-doctors" class="step d-none">
                <label class="block text-sm font-medium text-gray-700 mb-3 text-right">اختر الطبيب</label>
                <div id="doctors-list" class="space-y-2"></div>
            </div>

            <!-- الخطوة 3: اختيار الموعد -->
            <div id="step-slots" class="step d-none">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 text-right">الطبيب</label>
                    <div id="doctor-profile" class="p-4 bg-gray-50 rounded-xl text-right">
                        <h5 class="font-semibold text-gray-800" id="doctor-name">—</h5>
                        <p class="text-sm text-gray-600 mt-1" id="doctor-bio">—</p>
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">اختر التاريخ</label>
                <input type="date" id="slot-date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right">

                <label class="block text-sm font-medium text-gray-700 mb-3 mt-4 text-right">اختيار الوقت</label>
                <div id="slots-container" class="flex flex-wrap gap-2 min-h-[80px]"></div>
            </div>

            <!-- الخطوة 4: التأكيد -->
            <div id="step-confirm" class="step d-none">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-right">تأكيد الحجز</h3>
                <div id="summary" class="bg-gray-50 rounded-xl p-4 mb-5 text-right">
                    <p><strong>الطبيب:</strong> <span id="summary-doctor">—</span></p>
                    <p><strong>التاريخ:</strong> <span id="summary-date">—</span></p>
                    <p><strong>الوقت:</strong> <span id="summary-time">—</span></p>
                </div>

                <form id="confirmForm" method="POST" action="{{ route('appointments.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="doctor_id" id="form_doctor_id">
                    <input type="hidden" name="starts_at" id="form_starts_at">
                    <input type="hidden" name="ends_at" id="form_ends_at">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-right">ملاحظات إضافية</label>
                        <textarea name="notes" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-20" placeholder="اكتب أي ملاحظات ترغب في إضافتها..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                        حجز الموعد
                    </button>
                </form>
            </div>

            <!-- مؤشر التقدم (خطوات) -->
            <div class="flex justify-center items-center gap-2 my-6">
                <div class="w-3 h-3 bg-gray-300 rounded-full step-indicator active"></div>
                <div class="w-3 h-3 bg-gray-300 rounded-full step-indicator"></div>
                <div class="w-3 h-3 bg-gray-300 rounded-full step-indicator"></div>
                <div class="w-3 h-3 bg-gray-300 rounded-full step-indicator"></div>
            </div>

        </div>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    let selectedDoctor = null;
    let selectedSlot = null;

    const specialtySelect = document.getElementById('specialty');
    const doctorsList = document.getElementById('doctors-list');
    const slotDateInput = document.getElementById('slot-date');
    const slotsContainer = document.getElementById('slots-container');
    const doctorProfile = document.getElementById('doctor-profile');
    const doctorName = document.getElementById('doctor-name');
    const doctorBio = document.getElementById('doctor-bio');
    const summaryDoctor = document.getElementById('summary-doctor');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');

    // خطوات التقدم
    const steps = document.querySelectorAll('.step');
    const indicators = document.querySelectorAll('.step-indicator');

    // تحديث مؤشر التقدم
    function updateIndicator(stepIndex) {
        indicators.forEach((el, i) => {
            el.classList.toggle('bg-blue-500', i <= stepIndex);
            el.classList.toggle('bg-gray-300', i > stepIndex);
        });
    }

    // عرض خطوة معينة
    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('d-none', i !== index);
        });
        updateIndicator(index);
    }

    // التحقق من اختيار التخصص
    specialtySelect.addEventListener('change', function() {
        const spec = this.value;
        if (!spec) return;

        fetch(`/appointments/api/doctors?specialty=${encodeURIComponent(spec)}`)
            .then(r => r.json())
            .then(list => {
                doctorsList.innerHTML = '';
                if (list.length === 0) {
                    doctorsList.innerHTML = '<div class="text-center text-gray-500 py-4">لا يوجد أطباء بهذا التخصص.</div>';
                    return;
                }

                list.forEach(d => {
                    const button = document.createElement('button');
                    button.className = 'w-full px-4 py-3 text-left bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-right';
                    button.innerHTML = `
                        <div class="font-medium text-gray-800">${d.name}</div>
                        <div class="text-sm text-gray-600 mt-1">${d.specialty}</div>
                    `;
                    button.addEventListener('click', () => {
                        selectedDoctor = d;
                        showDoctorStep(d);
                    });
                    doctorsList.appendChild(button);
                });

                showStep(1); // الانتقال إلى خطوة الأطباء
            });
    });

    // عرض تفاصيل الطبيب
    function showDoctorStep(doctor) {
        doctorName.textContent = doctor.name;
        doctorBio.textContent = doctor.bio || 'لا توجد معلومات إضافية.';
        showStep(2); // الانتقال إلى خطوة المواعيد
    }

    // تحميل المواعيد المتاحة
    slotDateInput.addEventListener('change', function() {
        const date = this.value;
        if (!selectedDoctor) {
            alert('يرجى اختيار طبيب أولاً.');
            return;
        }

        slotsContainer.innerHTML = '<div class="col-span-2 text-center text-gray-500 py-4">جاري التحميل...</div>';

        fetch(`/appointments/api/slots?doctor_id=${selectedDoctor.id}&date=${date}`)
            .then(r => r.json())
            .then(slots => {
                slotsContainer.innerHTML = '';
                if (slots.length === 0) {
                    slotsContainer.innerHTML = '<div class="col-span-2 text-center text-gray-500 py-4">لا توجد مواعيد متاحة في هذا التاريخ.</div>';
                    return;
                }

                slots.forEach(s => {
                    const timeStart = new Date(s.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const timeEnd = new Date(s.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const btn = document.createElement('button');
                    btn.className = 'px-4 py-3 bg-gray-100 text-gray-800 rounded-xl hover:bg-blue-100 hover:text-blue-700 transition-colors text-right';
                    btn.innerText = `${timeStart} - ${timeEnd}`;
                    btn.addEventListener('click', () => {
                        selectedSlot = s;
                        showConfirm();
                    });
                    slotsContainer.appendChild(btn);
                });
            })
            .catch(() => {
                slotsContainer.innerHTML = '<div class="col-span-2 text-center text-red-500 py-4">خطأ في تحميل المواعيد.</div>';
            });
    });

    // عرض صفحة التأكيد
    function showConfirm() {
        const start = new Date(selectedSlot.start);
        const dateStr = start.toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const timeStr = `${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(selectedSlot.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;

        summaryDoctor.textContent = selectedDoctor.name;
        summaryDate.textContent = dateStr;
        summaryTime.textContent = timeStr;

        document.getElementById('form_doctor_id').value = selectedDoctor.id;
        document.getElementById('form_starts_at').value = selectedSlot.start;
        document.getElementById('form_ends_at').value = selectedSlot.end;

        showStep(3); // الانتقال إلى خطوة التأكيد
    }

    // تفعيل الخطوة الأولى عند التحميل
    showStep(0);
});
</script>

<style>
    /* تحسين ترتيب الخطوات */
    .step-indicator {
        transition: background-color 0.3s ease;
    }
    .step {
        transition: opacity 0.3s ease;
    }
    .step.d-none {
        opacity: 0;
        display: none;
    }
    .step:not(.d-none) {
        display: block;
        opacity: 1;
    }
</style>
@endpush

@endsection