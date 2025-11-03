@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 min-w-0">
  <h2 class="text-2xl font-bold mb-4">حجز موعد جديد</h2>

  @if(session('success'))
    <div class="mb-3 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif

  <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 min-w-0">
      <div class="min-w-0">
        <label class="block text-sm font-medium mb-1">الطبيب</label>
        <select id="doctorSelect" name="doctor_id" class="w-full border rounded px-3 py-2 min-w-0" required>
          <option value="">اختر الطبيب</option>
          @foreach($doctors as $d)
            <option value="{{ $d->id }}">{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="min-w-0">
        <label class="block text-sm font-medium mb-1">التاريخ (مطلوب)</label>
        <input type="date" id="dateInput" name="date" class="w-full border rounded px-3 py-2" value="{{ date('Y-m-d') }}" required>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
      <div>
        <label class="block text-sm font-medium mb-1">عدد الأيام للعرض</label>
        <select id="days" class="w-full border rounded px-3 py-2">
          <option value="1" selected>يوم واحد</option>
          <option value="7">أسبوع (7 أيام)</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">المريض</label>

       @php
    // حاول نستخدم $currentPatient لو جاي من الكنترولر
    $autoPatient = $currentPatient ?? null;

    // لو المستخدم مسجل وعندو دور "user" ونموذج patient مربوط بيهو، نستخدمو
    if (! $autoPatient && auth()->check() && auth()->user()->hasRole('user') && method_exists(auth()->user(), 'patient')) {
        $autoPatient = auth()->user()->patient ?? null;
    }

    // لو في patient_id في الريكويست وحصلنا عليه من الداتا، نستخدمو
    if (! $autoPatient && request()->filled('patient_id')) {
        $autoPatient = \App\Models\Patient::find(request('patient_id'));
    }
@endphp

@if($autoPatient)
    <input type="hidden" name="patient_id" value="{{ $autoPatient->id }}">
    <div class="p-2 rounded border bg-gray-50 truncate">{{ $autoPatient->name }} — {{ $autoPatient->phone }}</div>

        @elseif(isset($patients) && $patients)
          <select name="patient_id" id="patientSelect" class="w-full border rounded px-3 py-2">
            <option value="">اختر مريضاً</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->phone ?? ($p->user->email ?? '') }})</option>
            @endforeach
          </select>
        @else
          <div class="text-sm text-gray-500">إذا كنت مريض سجّل دخولك أو أنشئ مريض جديد.</div>
          <input type="hidden" id="maybePatientId" name="patient_id" value="">
        @endif
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">ملاحظات</label>
        <input type="text" name="notes" class="w-full border rounded px-3 py-2" placeholder="اختياري">
      </div>
    </div>

    <div class="mb-3 flex gap-2">
      <button type="button" id="loadBtn" class="px-4 py-2 bg-blue-600 text-white rounded">عرض الفتحات</button>
      <button type="button" id="clearSelection" class="px-4 py-2 bg-gray-100 rounded">مسح الاختيار</button>
    </div>

    <div id="slotsArea" class="mb-4 min-w-0">
      <div id="slotsContainer" class="space-y-3 min-w-0"></div>
    </div>

    <input type="hidden" name="starts_at" id="starts_at">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">تأكيد الحجز</button>
  </form>
</div>

<style>
  /* حماية نهائية لمنع overflow عرضي على الموبايل */
  html, body { overflow-x: hidden; }
  /* اجعل الأزرار قابلة للقص بدل كسر الـ layout */
  .slot-btn { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
</style>

<script>
(function(){
  const doctorSelect = document.getElementById('doctorSelect');
  const dateInput = document.getElementById('dateInput');
  const daysSelect = document.getElementById('days');
  const slotsContainer = document.getElementById('slotsContainer');
  const startsAtInput = document.getElementById('starts_at');
  const loadBtn = document.getElementById('loadBtn');
  const clearBtn = document.getElementById('clearSelection');
  const patientSelect = document.getElementById('patientSelect');

  function qs(params){
    return Object.keys(params).filter(k => params[k] !== '' && params[k] !== null && params[k] !== undefined)
      .map(k => encodeURIComponent(k)+'='+encodeURIComponent(params[k])).join('&');
  }

  async function fetchSlots(url) {
    try {
      const res = await fetch(url, { headers:{ 'Accept':'application/json' }, credentials:'same-origin' });
      const text = await res.text();
      // لو رجع JSON متوقع parsable, حاول parse وإلا اطبع النص
      try {
        const json = JSON.parse(text);
        if (!res.ok) {
          console.error('Server returned error:', res.status, json);
          throw new Error('Server error ' + res.status);
        }
        return json;
      } catch (e) {
        // لو محتوى النص HTML (stacktrace) او خطأ، ورّيه في الكونسول
        console.error('Non-JSON response from server:', text);
        throw new Error('فشل استرجاع البيانات من السيرفر — راجع storage/logs/laravel.log');
      }
    } catch (err) {
      console.error('Fetch error', err);
      throw err;
    }
  }

  async function loadSlots() {
    slotsContainer.innerHTML = '<div class="p-3 bg-gray-50 rounded">جارٍ التحميل...</div>';
    const docId = doctorSelect.value;
    const date = dateInput.value;
    const days = daysSelect.value || '1';

    if (!docId) {
      slotsContainer.innerHTML = '<div class="p-3 text-red-600">اختر الطبيب أولاً</div>';
      return;
    }
    if (!date) {
      slotsContainer.innerHTML = '<div class="p-3 text-red-600">حدد التاريخ أولاً</div>';
      return;
    }

    const url = `/appointments/${docId}/slots?${qs({date: date, days: days})}`;
    try {
      const json = await fetchSlots(url);
      renderSlots(json.slots || {});
    } catch (err) {
      slotsContainer.innerHTML = '<div class="p-3 text-red-600">فشل في جلب الفتحات — شوف اللوغ على الخادم</div>';
    }
  }

  function renderSlots(slotsMap) {
    slotsContainer.innerHTML = '';
    const dates = Object.keys(slotsMap).sort();
    if (dates.length === 0) {
      slotsContainer.innerHTML = '<div class="p-3 text-gray-600">لا توجد فتحات متاحة.</div>';
      return;
    }

    for (const date of dates) {
      const arr = slotsMap[date] || [];
      const dayHeader = document.createElement('div');
      dayHeader.className = 'p-2 bg-gray-100 rounded flex items-center justify-between';
      const d = new Date(date + 'T00:00:00');
      dayHeader.innerHTML = `<div class="truncate"><strong>${date}</strong> — ${d.toLocaleDateString()}</div><div class="text-sm text-gray-500">${arr.length} فتحة</div>`;
      slotsContainer.appendChild(dayHeader);

      if (!arr.length) {
        const empty = document.createElement('div');
        empty.className = 'p-2 text-gray-500';
        empty.textContent = 'لا توجد فتحات لهذا اليوم';
        slotsContainer.appendChild(empty);
        continue;
      }

      const grid = document.createElement('div');
      grid.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 p-2';

      arr.forEach(s => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'px-2 py-2 bg-white border rounded text-sm hover:bg-blue-50 slot-btn';
        btn.textContent = s.label;
        btn.dataset.starts = s.starts_at;
        btn.dataset.ends = s.ends_at;

        btn.addEventListener('click', () => {
          const patientElem = document.querySelector('input[name="patient_id"]') || patientSelect;
          const patientVal = patientElem ? patientElem.value : '';
          if (document.getElementById('patientSelect') && !patientVal) {
            alert('اختر مريض أولاً');
            return;
          }

          startsAtInput.value = s.starts_at;
          document.querySelectorAll('#slotsContainer button').forEach(b => b.classList.remove('ring-2','ring-blue-300'));
          btn.classList.add('ring-2','ring-blue-300');
          btn.scrollIntoView({behavior: 'smooth', block: 'center'});
        });

        grid.appendChild(btn);
      });

      slotsContainer.appendChild(grid);
    }
  }

  loadBtn.addEventListener('click', loadSlots);
  clearBtn.addEventListener('click', function(){
    startsAtInput.value = '';
    document.querySelectorAll('#slotsContainer button').forEach(b => b.classList.remove('ring-2','ring-blue-300'));
  });

  doctorSelect.addEventListener('change', () => { if (doctorSelect.value) loadSlots(); });
  dateInput.addEventListener('change', () => loadSlots());
  daysSelect.addEventListener('change', () => loadSlots());
})();
</script>
@endsection
