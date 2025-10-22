@extends('layouts.app')

@section('content')
<main class="container py-4 px-3">
    <div class="max-w-md mx-auto">

        <!-- عنوان المريض -->
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">{{ $patient->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">الرمز: {{ $patient->code }}</p>
            <p class="text-xs text-gray-500 mt-1">
                {{ ucfirst($patient->gender ?? '-') }} • {{ $patient->phone ?? '-' }} • العمر: {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age : '-' }}
            </p>
            @if($patient->address)
                <p class="text-xs text-gray-500 mt-1">{{ $patient->address }}</p>
            @endif
        </div>

        <!-- زر العودة وبدء الزيارة -->
        <div class="flex gap-2 mb-6">
            <a href="{{ route('admin.patients.index') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                رجوع
            </a>
            <button id="startVisitBtn" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all flex-1">
                بدء زيارة
            </button>
        </div>

        <!-- نافذة اختيار الطبيب والموعد -->
        <div id="visitModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">اختيار الطبيب والموعد</h2>
                <form id="visitForm">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">اختر الطبيب:</label>
                        <select id="doctorSelect" name="doctor_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- اختر الطبيب --</option>
                            @foreach(App\Models\Doctor::all() as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">اختر موعد فارغ:</label>
                        <select id="slotSelect" name="starts_at" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- اختر موعد --</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium">بدء الزيارة</button>
                        <button type="button" id="closeVisitModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- التبويبات العمودية (مثالية للجوال) -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex flex-col space-y-1">
                <button class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 font-medium text-sm rounded-lg border border-blue-200 text-center" data-tab="overview">نظرة عامة</button>
                <button class="w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200 text-center hover:bg-gray-50 transition-colors" data-tab="appointments">المواعيد</button>
                <button class="w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200 text-center hover:bg-gray-50 transition-colors" data-tab="records">السجلات الطبية</button>
                <button class="w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200 text-center hover:bg-gray-50 transition-colors" data-tab="prescriptions">الوصفات</button>
                <button class="w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200 text-center hover:bg-gray-50 transition-colors" data-tab="billing">الفوترة</button>
            </nav>
        </div>

        <!-- محتوى التبويبات (عمودي - واحد فقط ظاهر في وقت واحد) -->
        <div class="space-y-4">

            <!-- نظرة عامة -->
            <div class="tab-pane active" id="overview">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-3">الموعد القادم</h3>
                    @php
                        $next = $patient->appointments()->where('starts_at', '>', now())->orderBy('starts_at')->first();
                    @endphp
                    @if($next)
                        <div class="mb-2">
                            <span class="text-sm">{{ $next->starts_at->format('d/m/Y H:i') }}</span>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full ml-2">{{ $next->status }}</span>
                        </div>
                        <p class="text-sm text-gray-600">مع: {{ $next->doctor->name ?? 'غير محدد' }}</p>
                    @else
                        <p class="text-sm text-gray-500 italic">لا يوجد مواعيد قادمة</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">الفواتير المستحقة</h3>
                    @php
                        $invoices = $patient->invoices()->where('status', '!=', 'paid')->get();
                    @endphp
                    @if($invoices->isEmpty())
                        <p class="text-sm text-gray-500 italic">لا توجد فواتير مستحقة</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($invoices as $inv)
                                <li class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium">#{{ $inv->id }} • {{ number_format($inv->net_total, 2) }} ر.س</span>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">{{ $inv->status }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- المواعيد -->
            <div class="tab-pane hidden" id="appointments">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">المواعيد</h3>
                    @forelse($patient->appointments()->with('doctor.user')->orderByDesc('starts_at')->get() as $a)
                        <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm">{{ $a->starts_at->format('d/m/Y H:i') }} - {{ $a->ends_at->format('H:i') }}</p>
                                    <p class="text-xs text-gray-600">الطبيب: {{ $a->doctor->name ?? 'غير محدد' }}</p>
                                </div>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $a->status }}</span>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('admin.appointments.edit', $a->id) }}" class="flex-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-lg text-center hover:bg-gray-200 transition-colors">تعديل</a>
                                <form method="POST" action="{{ route('admin.appointments.destroy', $a->id) }}" class="flex-1" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1 bg-red-100 text-red-800 text-xs rounded-lg text-center hover:bg-red-200 transition-colors">إلغاء</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد مواعيد</p>
                    @endforelse
                </div>
            </div>

            <!-- السجلات الطبية -->
            <div class="tab-pane hidden" id="records">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">السجلات الطبية</h3>
                    @forelse($patient->medicalRecords as $r)
                        <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm">{{ optional($r->visit_date)->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-gray-600">الطبيب: {{ $r->doctor->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-700 mt-1">{{ Str::limit($r->diagnosis, 80) }}</p>
                                </div>
                                <button class="toggle-attachments text-xs text-blue-600 hover:text-blue-800" data-target="#attach-{{ $r->id }}">
                                    مرفقات ({{ count($r->attachments ?? []) }})
                                </button>
                            </div>

                            <!-- مرفقات السجل -->
                            <div id="attach-{{ $r->id }}" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                                <form action="{{ route('admin.medical_records.attachments', $r->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                                    @csrf
                                    <input type="file" name="files[]" multiple class="w-full text-xs border border-gray-300 rounded-lg mb-2">
                                    <button type="submit" class="w-full px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">رفع</button>
                                </form>

                                @if($r->attachments && count($r->attachments) > 0)
                                    <div class="space-y-1">
                                        @foreach($r->attachments as $file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="block text-xs text-blue-600 hover:text-blue-800 underline truncate">
                                                {{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-gray-500">لا توجد مرفقات</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد سجلات طبية</p>
                    @endforelse
                </div>
            </div>

            <!-- الوصفات -->
            <div class="tab-pane hidden" id="prescriptions">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">الوصفات</h3>
                    @forelse($patient->medicalRecords as $r)
                        <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0">
                            <h4 class="font-medium text-sm mb-2">{{ optional($r->visit_date)->format('d/m/Y') }}</h4>
                            @forelse($r->prescriptions as $pres)
                                <div class="p-3 bg-gray-50 rounded-lg mb-2">
                                    <p class="font-medium text-sm">{{ $pres->drug_name }} — {{ $pres->dosage }}</p>
                                    <p class="text-xs text-gray-600">التكرار: {{ $pres->frequency }} • المدة: {{ $pres->duration }}</p>
                                    @if($pres->notes)
                                        <p class="text-xs text-gray-500 mt-1">{{ $pres->notes }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic">لا توجد وصفات</p>
                            @endforelse
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد وصفات</p>
                    @endforelse
                </div>
            </div>

            <!-- الفوترة -->
            <div class="tab-pane hidden" id="billing">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">الفوترة</h3>
                    @forelse($patient->invoices as $inv)
                        <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-sm">فاتورة #{{ $inv->id }}</p>
                                <p class="text-xs text-gray-600">{{ number_format($inv->net_total, 2) }} ر.س • {{ $inv->status }}</p>
                                <p class="text-xs text-gray-500">استحقاق: {{ optional($inv->due_date)->format('d/m/Y') }}</p>
                            </div>
                            <a href="#" class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition-colors">عرض</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد فواتير</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('[data-tab]');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Hide all tabs
            tabPanes.forEach(pane => pane.classList.add('hidden'));
            tabs.forEach(t => t.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-200'));

            // Show selected tab
            document.getElementById(tab.dataset.tab).classList.remove('hidden');
            tab.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200');
        });
    });

    // Toggle attachments
    document.querySelectorAll('.toggle-attachments').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.target);
            target.classList.toggle('hidden');
        });
    });

    // Start Visit Button
    // نافذة بدء زيارة
    const visitModal = document.getElementById('visitModal');
    const doctorSelect = document.getElementById('doctorSelect');
    const slotSelect = document.getElementById('slotSelect');
    const visitForm = document.getElementById('visitForm');
    document.getElementById('startVisitBtn').addEventListener('click', function() {
        visitModal.classList.remove('hidden');
    });
    document.getElementById('closeVisitModal').addEventListener('click', function() {
        visitModal.classList.add('hidden');
        doctorSelect.value = '';
        slotSelect.innerHTML = '<option value="">-- اختر موعد --</option>';
    });

    doctorSelect.addEventListener('change', function() {
        slotSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        fetch('/admin/appointments/api?doctor_id=' + doctorSelect.value)
            .then(res => res.json())
            .then(data => {
                slotSelect.innerHTML = '<option value="">-- اختر موعد --</option>';
                if (Array.isArray(data)) {
                    data.filter(a => a.status === 'free').forEach(a => {
                        slotSelect.innerHTML += `<option value="${a.starts_at}">${a.starts_at} - ${a.ends_at}</option>`;
                    });
                }
            });
    });

    visitForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const doctor_id = doctorSelect.value;
        const starts_at = slotSelect.value;
        if (!doctor_id || !starts_at) {
            alert('يرجى اختيار الطبيب والموعد.');
            return;
        }
        fetch('{{ route('admin.medical_records.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                patient_id: {{ $patient->id }},
                doctor_id: doctor_id,
                visit_date: starts_at
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.id) {
                window.location.href = '/admin/medical-records/' + data.id + '/edit';
            } else {
                alert('حدث خطأ أثناء بدء الزيارة.');
            }
        })
        .catch(() => {
            alert('تعذر الاتصال بالخادم. حاول مرة أخرى.');
        });
    });

    // Initialize default tab
    document.getElementById('overview').classList.remove('hidden');
    document.querySelector('[data-tab="overview"]').classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200');
});
</script>

<style>
    .tab-pane {
        display: none;
    }
</style>

@endsection