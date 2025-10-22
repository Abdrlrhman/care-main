@extends('layouts.app')

@section('content')
<main class="container py-3 px-4">
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-bold text-gray-800">محرر السجل الطبي</h1>
        <a href="{{ route('admin.patients.show', $record->patient_id) }}" class="text-blue-600 text-sm font-medium hover:text-blue-800 flex items-center gap-1">
            ← رجوع
        </a>
    </div>

    <form id="mrForm" method="POST" action="{{ $record->exists ? route('admin.medical_records.update', $record->id) : route('admin.medical_records.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($record->exists) @method('PUT') @endif
        <input type="hidden" name="patient_id" value="{{ $record->patient_id }}">
        <input type="hidden" name="doctor_id" value="{{ $record->doctor_id }}">

        <!-- معلومات المريض والطبيب -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">معلومات الزيارة</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">المريض</label>
                <div class="text-sm">
                    <strong class="text-gray-800">{{ $record->patient->name ?? '-' }}</strong>
                    <span class="text-gray-500 ms-1">{{ $record->patient->code ?? '' }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">الطبيب</label>
                <div class="text-sm text-gray-800">{{ $record->doctor->name ?? '—' }}</div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الزيارة</label>
                <input type="datetime-local" name="visit_date" 
                       value="{{ old('visit_date', $record->visit_date?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">التشخيص</label>
                <textarea name="diagnosis" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" rows="4" placeholder="اكتب التشخيص هنا...">{{ old('diagnosis', $record->diagnosis) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات عامة</label>
                <textarea name="notes" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" rows="4" placeholder="أي ملاحظات إضافية...">{{ old('notes', $record->notes) }}</textarea>
            </div>
        </div>

        <!-- الوصفات الطبية -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-800">الوصفات الطبية</h2>
                <button type="button" id="addPrescription" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة وصفة
                </button>
            </div>

            <div id="prescriptionsContainer">
                @php
                    $prescriptions = old('prescriptions', $record->prescriptions->toArray() ?? []);
                @endphp
                @foreach($prescriptions as $i => $pres)
                    <div class="prescription-row bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">الدواء</label>
                                <input name="prescriptions[{{ $i }}][drug_name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $pres['drug_name'] ?? '' }}" placeholder="اسم الدواء">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label>
                                <input name="prescriptions[{{ $i }}][dosage]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $pres['dosage'] ?? '' }}" placeholder="مثال: 1 كبسولة">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label>
                                <input name="prescriptions[{{ $i }}][frequency]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $pres['frequency'] ?? '' }}" placeholder="مثال: 3 مرات يومياً">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">المدة</label>
                                <input name="prescriptions[{{ $i }}][duration]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $pres['duration'] ?? '' }}" placeholder="مثال: 7 أيام">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                            <textarea name="prescriptions[{{ $i }}][notes]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="2" placeholder="تعليمات خاصة...">{{ $pres['notes'] ?? '' }}</textarea>
                        </div>

                        <button type="button" class="remove-prescription inline-flex items-center text-red-600 text-sm font-medium hover:text-red-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            حذف الوصفة
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- قالب إضافة وصفة جديدة -->
            <template id="presTemplate">
                <div class="prescription-row bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">الدواء</label>
                            <input name="prescriptions[__IDX__][drug_name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="اسم الدواء">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label>
                            <input name="prescriptions[__IDX__][dosage]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: 1 كبسولة">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label>
                            <input name="prescriptions[__IDX__][frequency]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: 3 مرات يومياً">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">المدة</label>
                            <input name="prescriptions[__IDX__][duration]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: 7 أيام">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                        <textarea name="prescriptions[__IDX__][notes]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="2" placeholder="تعليمات خاصة..."></textarea>
                    </div>

                    <button type="button" class="remove-prescription inline-flex items-center text-red-600 text-sm font-medium hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        حذف الوصفة
                    </button>
                </div>
            </template>
        </div>

        <!-- المرفقات -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-4">المرفقات</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">رفع صور أو فواتير</label>
                <input type="file" id="attachments" name="files[]" multiple class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">يمكنك رفع أكثر من ملف (صور، فواتير، تقارير)</p>
            </div>

            <div id="attachmentsPreview">
                @foreach($record->attachments ?? [] as $file)
                    <div class="mb-2">
                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-600 text-sm hover:underline">
                            {{ basename($file) }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <div id="autosaveIndicator" class="text-sm text-gray-500 text-right flex-1">حالة: جاهز</div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="saveDraft" class="px-6 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all flex-1 sm:flex-none">
                    حفظ مسودة
                </button>
                <button type="submit" id="finalizeBtn" class="px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all flex-1 sm:flex-none">
                    تأكيد وحفظ
                </button>
                <button type="button" id="printBtn" class="px-6 py-3 bg-green-100 text-green-700 text-sm font-medium rounded-xl hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all flex-1 sm:flex-none">
                    طباعة الملخص
                </button>
            </div>
        </div>
    </form>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const presContainer = document.getElementById('prescriptionsContainer');
    const presTemplate = document.getElementById('presTemplate').innerHTML;
    let idx = presContainer.querySelectorAll('.prescription-row').length;

    // إضافة وصفة جديدة
    document.getElementById('addPrescription').addEventListener('click', function(){
        const html = presTemplate.replace(/__IDX__/g, idx);
        presContainer.insertAdjacentHTML('beforeend', html);
        idx++;
    });

    // حذف وصفة
    presContainer.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-prescription')){
            e.target.closest('.prescription-row').remove();
        }
    });

    // معاينة الملفات المرفوعة
    document.getElementById('attachments').addEventListener('change', function(e){
        const preview = document.getElementById('attachmentsPreview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(f=>{
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <span class="text-sm text-gray-700">${f.name} (${Math.round(f.size/1024)} KB)</span>
                <button type="button" class="ml-2 text-red-500 text-xs hover:text-red-700 remove-file">×</button>
            `;
            preview.appendChild(div);

            // إمكانية حذف الملف من المعاينة قبل الرفع
            div.querySelector('.remove-file').addEventListener('click', () => {
                div.remove();
            });
        });
    });

    // حفظ مسودة (AJAX)
    document.getElementById('saveDraft').addEventListener('click', function(){
        const form = document.getElementById('mrForm');
        const data = new FormData(form);
        fetch(form.action, {
            method: form.method || 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: data
        })
        .then(() => {
            document.getElementById('autosaveIndicator').textContent = 'تم حفظ المسودة!';
            setTimeout(() => {
                document.getElementById('autosaveIndicator').textContent = 'حالة: جاهز';
            }, 2000);
        })
        .catch(() => {
            alert('خطأ في الحفظ. يرجى المحاولة لاحقًا.');
        });
    });

    // طباعة الملخص
    document.getElementById('printBtn').addEventListener('click', function(){
        window.print();
    });
});
</script>
@endpush

<style>
    /* تحسين الطباعة */
    @media print {
        body * {
            visibility: hidden;
        }
        #mrForm, #mrForm * {
            visibility: visible;
        }
        #mrForm {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .btn, .remove-file {
            display: none !important;
        }
    }
</style>
@endsection