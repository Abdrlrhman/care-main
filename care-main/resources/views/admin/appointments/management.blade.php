@extends('layouts.app')

@section('content')
<main class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- العنوان وزر التبديل -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">إدارة المواعيد</h1>
            <button id="toggleView" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span id="toggleText">عرض القائمة</span>
            </button>
        </div>

        <!-- فلاتر البحث -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <!-- الطبيب -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 text-right">الطبيب</label>
                <select id="filterDoctor" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right">
                    <option value="">جميع الأطباء</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->specialty }})</option>
                    @endforeach
                </select>
            </div>

            <!-- المريض -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 text-right">المريض</label>
                <select id="filterPatient" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right">
                    <option value="">جميع المرضى</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 text-right">الحالة</label>
                <select id="filterStatus" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right">
                    <option value="">أي حالة</option>
                    <option value="scheduled">مجدول</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="completed">منتهٍ</option>
                    <option value="cancelled">ملغى</option>
                </select>
            </div>

            <!-- التواريخ -->
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 text-right">من</label>
                    <input id="fromDate" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 text-right">إلى</label>
                    <input id="toDate" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <button id="applyFilters" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all self-end">
                    تطبيق
                </button>
            </div>
        </div>

        <!-- مؤشر الفلترة -->
        <div id="filterSummary" class="text-sm text-gray-600 mb-4 text-right hidden"></div>

        <!-- منطقة التقويم والقائمة -->
        <div id="calendarWrap" class="mb-6">
            <div id="calendar" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 min-h-[600px]"></div>
        </div>

        <!-- منطقة القائمة (مخفية افتراضيًا) -->
        <div id="listView" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">قائمة المواعيد</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المريض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطبيب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">من</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إلى</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ملاحظات</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody id="appointmentsList" class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <!-- قائمة يومية للهواتف فقط -->
        <div id="mobileDayList" class="md:hidden space-y-3 mt-6 px-4 pb-6"></div>

    </div>
</main>

@push('scripts')
<script>
let listVisible = false;

// تبديل بين التقويم والقائمة
document.getElementById('toggleView').addEventListener('click', () => {
    listVisible = !listVisible;
    document.getElementById('calendarWrap').style.display = listVisible ? 'none' : 'block';
    document.getElementById('listView').classList.toggle('hidden', !listVisible);
    document.getElementById('toggleText').textContent = listVisible ? 'عرض التقويم' : 'عرض القائمة';
});

// تحميل المواعيد
async function loadAppointments() {
    const params = new URLSearchParams();
    const doctorId = document.getElementById('filterDoctor').value;
    const patientId = document.getElementById('filterPatient').value;
    const status = document.getElementById('filterStatus').value;
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (doctorId) params.set('doctor_id', doctorId);
    if (patientId) params.set('patient_id', patientId);
    if (status) params.set('status', status);
    if (fromDate) params.set('from', fromDate);
    if (toDate) params.set('to', toDate);

    const res = await fetch('/admin/appointments/api?' + params.toString());
    const items = await res.json();

    // تحديث ملخص الفلتر
    const summaryEl = document.getElementById('filterSummary');
    if (params.toString()) {
        summaryEl.textContent = `تم تطبيق ${params.size} فلتر`;
        summaryEl.classList.remove('hidden');
    } else {
        summaryEl.classList.add('hidden');
    }

    // تحديث القائمة
    const tbody = document.getElementById('appointmentsList');
    tbody.innerHTML = '';
    items.forEach(a => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors";
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${a.patient?.name || '—'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${a.doctor?.name || '—'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${a.starts_at}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${a.ends_at}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    ${a.status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                      a.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                      a.status === 'completed' ? 'bg-green-100 text-green-800' :
                      a.status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}">
                    ${a.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">${a.notes || '—'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <a href="/admin/appointments/${a.id}/edit" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition-colors mr-1">
                    تعديل
                </a>
                <button class="btn btn-sm btn-danger text-xs px-3 py-1 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors"
                        onclick="cancel(${a.id})">
                    إلغاء
                </button>
                <button class="btn btn-sm btn-warning text-xs px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors ml-1"
                        onclick="showReassign(${a.id})">
                    إعادة تعيين
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // تحديث قائمة الأيام للهواتف
    const mobileList = document.getElementById('mobileDayList');
    mobileList.innerHTML = '';

    const byDay = {};
    items.forEach(a => {
        const dateStr = new Date(a.starts_at).toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        if (!byDay[dateStr]) byDay[dateStr] = [];
        byDay[dateStr].push(a);
    });

    for (const [day, appointments] of Object.entries(byDay)) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-xl shadow-sm border border-gray-100 p-4';

        card.innerHTML = `
            <h4 class="font-semibold text-gray-800 mb-3 text-right">${day}</h4>
            ${appointments.map(a => `
                <div class="border-b border-gray-100 pb-3 last:border-b-0">
                    <div class="flex justify-between items-center mb-1">
                        <strong class="text-sm">${new Date(a.starts_at).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })}</strong>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            ${a.status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                              a.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                              a.status === 'completed' ? 'bg-green-100 text-green-800' :
                              a.status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}">
                            ${a.status}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 text-right mb-1">${a.patient?.name || '—'}</p>
                    <div class="flex justify-end gap-1 mt-2">
                        <a href="/admin/appointments/${a.id}/edit" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">تعديل</a>
                        <button class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200" onclick="cancel(${a.id})">إلغاء</button>
                    </div>
                </div>
            `).join('')}
        `;
        mobileList.appendChild(card);
    }
}

// تطبيق الفلاتر
document.getElementById('applyFilters').addEventListener('click', loadAppointments);
document.getElementById('filterDoctor').addEventListener('change', loadAppointments);
document.getElementById('filterPatient').addEventListener('change', loadAppointments);
document.getElementById('filterStatus').addEventListener('change', loadAppointments);
document.getElementById('fromDate').addEventListener('change', loadAppointments);
document.getElementById('toDate').addEventListener('change', loadAppointments);

// إلغاء موعد
async function cancel(id) {
    if (!confirm('هل أنت متأكد من إلغاء هذا الموعد؟')) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    await fetch(`/admin/appointments/${id}/cancel`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token }
    });
    loadAppointments();
}

// إعادة تعيين الموعد
async function showReassign(id) {
    const doctorId = prompt('أدخل معرف الطبيب الجديد:');
    if (!doctorId || !doctorId.trim()) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const res = await fetch(`/admin/appointments/${id}/reassign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ doctor_id: doctorId })
        });
        if (res.ok) {
            alert('تم إعادة تعيين الموعد بنجاح.');
            loadAppointments();
        } else {
            throw new Error('فشل إعادة التعيين');
        }
    } catch (e) {
        alert('خطأ في إعادة التعيين: ' + e.message);
    }
}

// تحميل أولي
loadAppointments();

// تفعيل التبديل عند تغيير العرض
window.addEventListener('resize', () => {
    if (window.innerWidth >= 768 && listVisible) {
        // على سطح المكتب: أظهر التقويم تلقائيًا إذا كان مغلقًا
        document.getElementById('toggleView').click();
    }
});
</script>

<!-- تحسين دعم التقويم الافتراضي على iOS -->
<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
</style>
@endpush

@endsection