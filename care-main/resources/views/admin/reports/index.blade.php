@extends('layouts.app')

@section('content')
<div class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- العنوان وأزرار التصدير -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">التقارير والتحليلات</h1>
            <div class="flex flex-wrap gap-2">
                <button id="exportRevenue" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    تصدير إيرادات CSV
                </button>
                <button id="exportServices" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    تصدير الخدمات CSV
                </button>
            </div>
        </div>

        <!-- فلاتر التقرير -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                <input id="from" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                <input id="to" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الطبيب</label>
                <select id="filterDoctor" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">جميع الأطباء</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الخدمة</label>
                <select id="filterService" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">جميع الخدمات</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- الرسوم البيانية -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- الإيرادات الشهرية -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الإيرادات حسب الشهر</h3>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- المواعيد حسب التخصص -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">المواعيد حسب التخصص</h3>
                <div class="h-64">
                    <canvas id="specialtyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- استخدام الخدمات -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">تردد استخدام الخدمات</h3>
            <div class="h-80">
                <canvas id="servicesChart"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function fetchJson(url, params = {}) {
        const ps = new URLSearchParams(params);
        const r = await fetch(url + '?' + ps.toString());
        return r.json();
    }

    async function loadReports() {
        const params = {
            from: document.getElementById('from').value,
            to: document.getElementById('to').value,
            doctor_id: document.getElementById('filterDoctor').value,
            service_id: document.getElementById('filterService').value
        };

        const rev = await fetchJson('/admin/reports/revenue', params);
        const labels = rev.map(r => r.month);
        const data = rev.map(r => parseFloat(r.revenue));
        renderBar('revenueChart', labels, data, 'الإيرادات');

        const spec = await fetchJson('/admin/reports/appointments-specialty', params);
        renderPie('specialtyChart', spec.map(s => s.specialization), spec.map(s => s.count));

        const svc = await fetchJson('/admin/reports/services-usage', params);
        renderBar('servicesChart', svc.map(s => s.service), svc.map(s => s.total), 'الاستخدام');
    }

    function renderBar(id, labels, data, label) {
        const ctx = document.getElementById(id).getContext('2d');
        if (window[id]) window[id].destroy();
        window[id] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('ar-SA', { style: 'currency', currency: 'SAR' });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('ar-SA', { style: 'currency', currency: 'SAR' });
                            }
                        }
                    }
                }
            }
        });
    }

    function renderPie(id, labels, data) {
        const ctx = document.getElementById(id).getContext('2d');
        if (window[id]) window[id].destroy();
        window[id] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed} موعد`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ربط الأحداث
    document.getElementById('from').addEventListener('change', loadReports);
    document.getElementById('to').addEventListener('change', loadReports);
    document.getElementById('filterDoctor').addEventListener('change', loadReports);
    document.getElementById('filterService').addEventListener('change', loadReports);

    // تصدير CSV
    document.getElementById('exportRevenue').addEventListener('click', () => {
        const params = new URLSearchParams({
            from: document.getElementById('from').value,
            to: document.getElementById('to').value
        });
        location.href = '/admin/reports/export/revenue?' + params.toString();
    });

    document.getElementById('exportServices').addEventListener('click', () => {
        const params = new URLSearchParams({
            from: document.getElementById('from').value,
            to: document.getElementById('to').value
        });
        location.href = '/admin/reports/export/services?' + params.toString();
    });

    // التحميل الأولي
    loadReports();
</script>
@endsection