@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 min-w-0">
  <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
    <h1 class="text-2xl font-bold text-gray-800">التقارير والتحليلات</h1>

    <div class="flex gap-2">
      <a id="exportRevenueBtn" href="#" class="inline-flex items-center gap-2 px-3 py-2 border rounded text-sm bg-white hover:bg-gray-50 shadow-sm">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 11l4 4 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        تصدير إيرادات CSV
      </a>
      <a id="exportServicesBtn" href="#" class="inline-flex items-center gap-2 px-3 py-2 border rounded text-sm bg-white hover:bg-gray-50 shadow-sm">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 11l4 4 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        تصدير خدمات CSV
      </a>
    </div>
  </div>

  {{-- الفلاتر --}}
  <form id="filtersForm" method="GET" action="{{ url('/admin/reports') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div>
      <label class="block text-sm font-medium mb-1">من تاريخ</label>
      <input id="from" name="from" type="date" value="{{ request('from') }}" class="w-full border rounded px-3 py-2"/>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">إلى تاريخ</label>
      <input id="to" name="to" type="date" value="{{ request('to') }}" class="w-full border rounded px-3 py-2"/>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">الطبيب</label>
      <select id="filterDoctor" name="doctor_id" class="w-full border rounded px-3 py-2">
        <option value="">كل الأطباء</option>
        @foreach($doctors as $d)
          <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">الخدمة</label>
      <select id="filterService" name="service_id" class="w-full border rounded px-3 py-2">
        <option value="">كل الخدمات</option>
        @foreach($services as $s)
          <option value="{{ $s->id }}" {{ request('service_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="sm:col-span-2 lg:col-span-4 flex gap-2 mt-2">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">تطبيق الفلاتر</button>
      <a href="{{ url('/admin/reports') }}" class="px-4 py-2 bg-gray-100 rounded">إعادة ضبط</a>
    </div>
  </form>

  <!-- الرسوم البيانية -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold">الإيرادات حسب الشهر</h3>
        <div id="revenueLoader" class="hidden"><div class="animate-spin h-4 w-4 border-2 border-gray-300 border-t-blue-600 rounded-full"></div></div>
      </div>
      <div class="h-56 relative">
        <canvas id="revenueChart" class="w-full h-full"></canvas>
        <div id="revenueEmpty" class="absolute inset-0 flex items-center justify-center text-gray-500 hidden">لا توجد بيانات لعرضها.</div>
      </div>
    </div>

    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold">المواعيد حسب التخصص</h3>
        <div id="specialtyLoader" class="hidden"><div class="animate-spin h-4 w-4 border-2 border-gray-300 border-t-blue-600 rounded-full"></div></div>
      </div>
      <div class="h-56 relative">
        <canvas id="specialtyChart" class="w-full h-full"></canvas>
        <div id="specialtyEmpty" class="absolute inset-0 flex items-center justify-center text-gray-500 hidden">لا توجد بيانات لعرضها.</div>
      </div>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold">تردد استخدام الخدمات</h3>
      <div id="servicesLoader" class="hidden"><div class="animate-spin h-4 w-4 border-2 border-gray-300 border-t-blue-600 rounded-full"></div></div>
    </div>
    <div class="h-64 relative">
      <canvas id="servicesChart" class="w-full h-full"></canvas>
      <div id="servicesEmpty" class="absolute inset-0 flex items-center justify-center text-gray-500 hidden">لا توجد بيانات لعرضها.</div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

  const charts = {};

  function qs(params){
    // build query only with non-empty values
    const p = new URLSearchParams();
    for (const k in params) {
      if (params[k] !== null && params[k] !== undefined && params[k] !== '') p.append(k, params[k]);
    }
    return p.toString();
  }

  function show(id){ const e=document.getElementById(id); if(e) e.classList.remove('hidden'); }
  function hide(id){ const e=document.getElementById(id); if(e) e.classList.add('hidden'); }

  function destroyChart(key){
    if (charts[key] && typeof charts[key].destroy === 'function') {
      try { charts[key].destroy(); } catch(e){ console.warn('destroy error', e); }
      delete charts[key];
    } else {
      // try Chart.getChart
      const canvas = document.getElementById(key);
      if (window.Chart && canvas) {
        const inst = Chart.getChart(canvas);
        if (inst && typeof inst.destroy === 'function') {
          try { inst.destroy(); } catch(e){ console.warn('destroy error', e); }
        }
      }
    }
  }

  function renderBar(id, labels, data, title){
    destroyChart(id);
    const canvas = document.getElementById(id);
    if (!canvas) return;
    charts[id] = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{ label: title, data, backgroundColor: '#3b82f6' }]
      },
      options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    });
  }

  function renderPie(id, labels, data){
    destroyChart(id);
    const canvas = document.getElementById(id);
    if (!canvas) return;
    charts[id] = new Chart(canvas.getContext('2d'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{ data, backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] }]
      },
      options: { responsive:true, maintainAspectRatio:false }
    });
  }

  async function fetchJson(url, params = {}){
    const q = qs(params);
    const full = url + (q ? '?' + q : '');
    try {
      const res = await fetch(full, { headers:{ 'Accept':'application/json' }, credentials:'same-origin' });
      if (!res.ok) { console.error('Fetch failed', full, res.status); return []; }
      const j = await res.json();
      console.log('📊', full, j);
      return j;
    } catch(err){
      console.error('Fetch error', err);
      return [];
    }
  }

  async function loadAll(){
    const params = {
      from: document.getElementById('from')?.value || '',
      to: document.getElementById('to')?.value || '',
      doctor_id: document.getElementById('filterDoctor')?.value || '',
      service_id: document.getElementById('filterService')?.value || ''
    };

    // show loaders
    show('revenueLoader'); show('specialtyLoader'); show('servicesLoader');
    hide('revenueEmpty'); hide('specialtyEmpty'); hide('servicesEmpty');

    // fetch parallel
    const [rev, spec, svc] = await Promise.all([
      fetchJson('/admin/reports/revenue', params),
      fetchJson('/admin/reports/appointments-specialty', params),
      fetchJson('/admin/reports/services-usage', params),
    ]);

    // update export links
    const q = qs(params);
    const er = document.getElementById('exportRevenueBtn');
    const es = document.getElementById('exportServicesBtn');
    if (er) er.href = '/admin/reports/export/revenue' + (q ? '?' + q : '');
    if (es) es.href = '/admin/reports/export/services' + (q ? '?' + q : '');

    // revenue chart
    destroyChart('revenueChart');
    if (!rev || rev.length === 0 || rev.every(r => Number(r.revenue) === 0)) {
      hide('revenueChart'); show('revenueEmpty');
    } else {
      show('revenueChart'); hide('revenueEmpty');
      renderBar('revenueChart', rev.map(r => r.month), rev.map(r => Number(r.revenue)), 'الإيرادات الشهرية');
    }

    // specialty chart
    destroyChart('specialtyChart');
    if (!spec || spec.length === 0 || spec.every(s => Number(s.count) === 0)) {
      hide('specialtyChart'); show('specialtyEmpty');
    } else {
      show('specialtyChart'); hide('specialtyEmpty');
      renderPie('specialtyChart', spec.map(s => s.specialization || s.specialty || 'غير محدد'), spec.map(s => Number(s.count)));
    }

    // services chart
    destroyChart('servicesChart');
    if (!svc || svc.length === 0 || svc.every(s => Number(s.total || s.usage) === 0)) {
      hide('servicesChart'); show('servicesEmpty');
    } else {
      show('servicesChart'); hide('servicesEmpty');
      // some endpoints return `total` others `usage` — normalize
      const labels = svc.map(s => s.service || s.name || 'غير معروف');
      const data = svc.map(s => Number(s.total ?? s.usage ?? 0));
      renderBar('servicesChart', labels, data, 'الاستخدام');
    }

    // hide loaders
    hide('revenueLoader'); hide('specialtyLoader'); hide('servicesLoader');
  }

  // events
  ['from','to','filterDoctor','filterService'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', loadAll);
  });

  // initial load
  loadAll();

});
</script>
@endsection
