@extends('layouts.app')

@section('content')
<div class="p-4">

    <!-- 📊 الإحصائيات -->
    <h2 class="text-xl font-bold mb-4 text-gray-700">لوحة تحكم المدير</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="text-gray-500 text-sm">إجمالي الأطباء</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalDoctors }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="text-gray-500 text-sm">إجمالي المرضى</div>
            <div class="text-2xl font-bold text-green-600">{{ $totalPatients }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="text-gray-500 text-sm">مواعيد اليوم</div>
            <div class="text-2xl font-bold text-purple-600">{{ $todaysAppointments }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="text-gray-500 text-sm">إيرادات الشهر</div>
            <div class="text-2xl font-bold text-orange-600">{{ number_format($monthlyRevenue,2) }}</div>
        </div>
    </div>

    <!-- 📈 الرسوم البيانية -->
    <div class="bg-white rounded-xl shadow p-4 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="font-semibold text-gray-700">الرسوم البيانية</h5>
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-1 min-h-[250px]">
                <canvas id="appointmentsChart"></canvas>
            </div>
            <div class="flex-1 min-h-[250px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 🧾 الفواتير و المرضى -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

        <!-- Latest Invoices -->
        <div class="bg-white rounded-xl shadow p-4">
            <h5 class="font-semibold text-gray-700 mb-3">أحدث الفواتير</h5>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-2">#</th>
                            <th class="p-2">المريض</th>
                            <th class="p-2">الطبيب</th>
                            <th class="p-2 text-left">الإجمالي</th>
                            <th class="p-2">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($latestInvoices as $inv)
                            <tr>
                                <td class="p-2">{{ $inv->id }}</td>
                                <td class="p-2">{{ $inv->patient->name ?? $inv->patient->first_name.' '.$inv->patient->last_name }}</td>
                                <td class="p-2">{{ $inv->doctor->user->name ?? $inv->doctor->name }}</td>
                                <td class="p-2 text-left font-semibold">{{ number_format($inv->net_total,2) }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Latest Patients -->
        <div class="bg-white rounded-xl shadow p-4">
            <h5 class="font-semibold text-gray-700 mb-3">أحدث المرضى</h5>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-2">الاسم</th>
                            <th class="p-2">الكود</th>
                            <th class="p-2">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($latestPatients as $p)
                            <tr>
                                <td class="p-2">{{ $p->name }}</td>
                                <td class="p-2">{{ $p->code }}</td>
                                <td class="p-2">{{ $p->created_at->toDateString() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dates = {!! json_encode($dates) !!};
    const counts = {!! json_encode($counts) !!};

    new Chart(document.getElementById('appointmentsChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'المواعيد',
                data: counts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                tension: 0.3
            }]
        },
        options: {responsive:true, maintainAspectRatio:false}
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'doughnut',
        data: {
            labels: ['مدفوع','غير مدفوع'],
            datasets: [{
                data: [{{ (float)$paid }}, {{ (float)$unpaid }}],
                backgroundColor: ['#22c55e','#ef4444']
            }]
        },
        options: {responsive:true, maintainAspectRatio:false}
    });
</script>
@endsection
