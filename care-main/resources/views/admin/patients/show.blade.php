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
            <a href="{{ route('admin.patients.index') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg text-center hover:bg-gray-50">
                رجوع
            </a>

            <!-- زر التوجيه لرابط بداء زياره مع ارسال ايدي المريض في الرابط   -->
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" id="startVisitBtn" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg text-center hover:bg-blue-700">
                بدء زيارة جديدة </a>
        </div>

        <!-- مودال بدء الزيارة -->
        <div id="visitModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden" aria-hidden="true">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">اختيار الطبيب والتاريخ</h2>

                <!-- FORM لتحميل المواعيد (GET) -->
                <form id="loadSlotsForm" method="GET" action="{{ url()->current() }}" class="mb-4">
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div class="mb-3">
                        <label class="block mb-2 text-sm font-medium">اختر الطبيب</label>
                        <select name="doctor_id" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="">-- اختر الطبيب --</option>
                            @foreach(App\Models\Doctor::orderBy('name')->get() as $doc)
                                <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block mb-2 text-sm font-medium">اختر اليوم</label>
                        <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}" class="w-full border rounded-lg px-3 py-2" required>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">عرض المواعيد</button>
                        <button type="button" id="closeVisitModalTop" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">إغلاق</button>
                    </div>
                </form>

                <!-- لو السيرفر رجع availableSlots (شكل: مصفوفة من عناصر فيها appointment_id, starts_at, ends_at) -->
                @if(isset($availableSlots) && is_array($availableSlots) && count($availableSlots) > 0)
                    <div class="mb-4">
                        <h3 class="mb-2 font-medium">المواعيد المتاحة</h3>

                        <form method="POST" action="{{ route('admin.medical_records.store') }}" id="visitFormServer">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                            <div class="mb-3">
                                <label class="block mb-2 text-sm font-medium">اختر موعد فارغ</label>
                                <select name="appointment_id" class="w-full border rounded-lg px-3 py-2" required>
                                    <option value="">-- اختر موعد --</option>
                                    @foreach($availableSlots as $slot)
                                        {{-- slot: ['id'=>..., 'starts_at'=>..., 'ends_at'=>...] أو ['appointment_id', 'starts_at'...] --}}
                                        @php
                                            $aid = $slot['id'] ?? $slot['appointment_id'] ?? null;
                                            $starts = \Carbon\Carbon::parse($slot['starts_at'])->format('Y-m-d H:i');
                                            $label = \Carbon\Carbon::parse($slot['starts_at'])->format('H:i') . ' - ' . \Carbon\Carbon::parse($slot['ends_at'])->format('H:i') . ' • ' . \Carbon\Carbon::parse($slot['starts_at'])->format('d/m/Y');
                                        @endphp
                                        <option value="{{ $aid }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">بدء الزيارة</button>
                                <button type="button" id="closeVisitModalBottom" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">إلغاء</button>
                            </div>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-gray-500">لا توجد مواعيد محملة بعد. استخدم 'عرض المواعيد' لتحميل المواعيد المتاحة من السيرفر.</p>
                @endif

            </div>
        </div>

        <!-- التبويبات العمودية -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex flex-col space-y-1">
                <button class="tab-btn w-full text-left px-4 py-3 bg-blue-50 text-blue-700 font-medium text-sm rounded-lg border border-blue-200" data-tab="overview">نظرة عامة</button>
                <button class="tab-btn w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200" data-tab="appointments">المواعيد</button>
                <button class="tab-btn w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200" data-tab="records">السجلات الطبية</button>
                <button class="tab-btn w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200" data-tab="prescriptions">الوصفات</button>
                <button class="tab-btn w-full text-left px-4 py-3 text-gray-700 font-medium text-sm rounded-lg border border-gray-200" data-tab="billing">الفوترة</button>
            </nav>
        </div>

        <!-- محتوى التبويبات -->
        <div>
            <!-- نظرة عامة -->
            <div class="tab-pane" id="overview">
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
                    @forelse($patient->appointments()->with('doctor')->orderByDesc('starts_at')->get() as $a)
                        <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm">{{ $a->starts_at->format('d/m/Y H:i') }} - {{ $a->ends_at->format('H:i') }}</p>
                                    <p class="text-xs text-gray-600">الطبيب: {{ $a->doctor->name ?? 'غير محدد' }}</p>
                                </div>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $a->status }}</span>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('appointments.edit', $a->id) }}" class="flex-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-lg text-center">تعديل</a>
                                <form method="POST" action="{{ route('appointments.destroy', $a->id) }}" class="flex-1" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1 bg-red-100 text-red-800 text-xs rounded-lg text-center">إلغاء</button>
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

                            <div id="attach-{{ $r->id }}" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                                <form action="{{ route('admin.medical_records.attachments', $r->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                                    @csrf
                                    <input type="file" name="files[]" multiple class="w-full text-xs border border-gray-300 rounded-lg mb-2">
                                    <button type="submit" class="w-full px-3 py-1 bg-blue-600 text-white text-xs rounded-lg">رفع</button>
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
                            <a href="#" class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-lg">عرض</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد فواتير</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</main>

<!-- أقل قدر من JS: فتح/قفل المودال وتبديل التابز وتوغل المرفقات -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  

    
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const panes = document.querySelectorAll('.tab-pane');
    function showTab(id) {
        panes.forEach(p => p.classList.add('hidden'));
        tabBtns.forEach(b => b.classList.remove('bg-blue-50','text-blue-700','border-blue-200'));
        document.getElementById(id).classList.remove('hidden');
        document.querySelector(`[data-tab="${id}"]`)?.classList.add('bg-blue-50','text-blue-700','border-blue-200');
    }
    // default
    showTab('overview');
    tabBtns.forEach(btn => btn.addEventListener('click', () => showTab(btn.dataset.tab)));

    // Toggle attachments
    document.querySelectorAll('.toggle-attachments').forEach(btn => {
        btn.addEventListener('click', () => {
            const tgt = document.querySelector(btn.dataset.target);
            tgt && tgt.classList.toggle('hidden');
        });
    });
});
</script>

<style>
    .tab-pane.hidden { display: none; }
    .tab-pane { display: block; }
</style>

@endsection
