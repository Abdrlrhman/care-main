@extends('layouts.app')

@section('content')
<main class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5">تقويم الدكتور - {{ $doctor->name }}</h2>
        <div>
            <div class="btn-group" role="group" aria-label="view switcher">
                <button class="btn btn-outline-secondary" id="prev">سابق</button>
                <button class="btn btn-outline-secondary" id="today">اليوم</button>
                <button class="btn btn-outline-secondary" id="next">التالي</button>
            </div>
            <div class="btn-group ms-2" role="group" aria-label="views">
                <button class="btn btn-outline-primary active" data-view="timeGridWeek">أسبوع</button>
                <button class="btn btn-outline-primary" data-view="dayGridMonth">شهر</button>
                <button class="btn btn-outline-primary" data-view="timeGridDay">يوم</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="apptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="apptForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">إنشاء موعد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="appt_id">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label">المريض</label>
                                <input type="text" id="patient_lookup" class="form-control" placeholder="ابحث بالاسم أو الكود">
                                <input type="hidden" name="patient_id" id="patient_id">
                                <div id="patient_suggestions" class="list-group mt-1" style="max-height:200px;overflow:auto;display:none"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">بداية</label>
                                <input type="datetime-local" class="form-control" name="starts_at" id="starts_at">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">نهاية</label>
                                <input type="datetime-local" class="form-control" name="ends_at" id="ends_at">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="confirmed">مؤكد</option>
                                    <option value="in_progress">قيد الحضور</option>
                                    <option value="cancelled">ملغى</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" id="deleteBtn" style="display:none">إلغاء الموعد</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">حفظ</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

@push('scripts')
<!-- FullCalendar CSS & JS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const calendarEl = document.getElementById('calendar');
    const modalEl = new bootstrap.Modal(document.getElementById('apptModal'));
    let currentEvent = null;

    let workingHours = [];
    fetch('/doctor/calendar/working-hours').then(r=>r.json()).then(data=>{ workingHours = data; });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: false,
        selectable: true,
        editable: true,
        nowIndicator: true,
        businessHours: true,
        eventDisplay: 'block',
        events: {
            url: '/doctor/calendar/appointments',
            method: 'GET'
        },
        select: function(info){
            // check against workingHours (weekday: 0=Sun .. 6=Sat)
            const start = info.start;
            const end = info.end || info.start;
            const w = start.getDay();
            const hrs = workingHours.filter(h=>h.weekday == w);
            let inside = false;
            if(hrs.length){
                for(const h of hrs){
                    const sParts = h.start_time.split(':');
                    const eParts = h.end_time.split(':');
                    const s = new Date(start);
                    s.setHours(parseInt(sParts[0]), parseInt(sParts[1]),0,0);
                    const e = new Date(start);
                    e.setHours(parseInt(eParts[0]), parseInt(eParts[1]),0,0);
                    if(start >= s && end <= e){ inside = true; break; }
                }
            }
            if(!inside){
                if(!confirm('الموعد خارج ساعات العمل المحددة. هل ترغب بإنشاء الموعد على أي حال؟')) return;
            }
            // open create modal with prefilled times
            currentEvent = null;
            document.getElementById('modalTitle').textContent = 'إنشاء موعد';
            document.getElementById('apptForm').reset();
            document.getElementById('appt_id').value = '';
            document.getElementById('starts_at').value = info.startStr.slice(0,16);
            document.getElementById('ends_at').value = info.endStr ? info.endStr.slice(0,16) : info.startStr.slice(0,16);
            document.getElementById('deleteBtn').style.display = 'none';
            modalEl.show();
        },
        eventClick: function(info){
            currentEvent = info.event;
            document.getElementById('modalTitle').textContent = 'تعديل الموعد';
            document.getElementById('apptForm').reset();
            document.getElementById('appt_id').value = info.event.id;
            document.getElementById('starts_at').value = info.event.start.toISOString().slice(0,16);
            document.getElementById('ends_at').value = (info.event.end || info.event.start).toISOString().slice(0,16);
            document.getElementById('status').value = info.event.extendedProps.status || 'confirmed';
            document.getElementById('notes').value = info.event.extendedProps.notes || '';
            document.getElementById('patient_id').value = info.event.extendedProps.patient_id || '';
            document.getElementById('patient_lookup').value = info.event.title || '';
            document.getElementById('deleteBtn').style.display = '';
            modalEl.show();
        },
        eventDrop: function(info){
            // update start/end
            fetch(`/doctor/calendar/appointments/${info.event.id}`,{
                method:'PUT',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({
                    patient_id: info.event.extendedProps.patient_id,
                    starts_at: info.event.start.toISOString(),
                    ends_at: info.event.end ? info.event.end.toISOString() : info.event.start.toISOString(),
                    status: info.event.extendedProps.status || 'confirmed',
                    notes: info.event.extendedProps.notes || ''
                })
            }).then(async res=>{
                if(!res.ok){
                    alert('التحديث فشل: ' + (await res.json()).message);
                    info.revert();
                }
            });
        }
    });

    calendar.render();

    // navigation buttons
    document.getElementById('prev').addEventListener('click', ()=> calendar.prev());
    document.getElementById('today').addEventListener('click', ()=> calendar.today());
    document.getElementById('next').addEventListener('click', ()=> calendar.next());
    document.querySelectorAll('[data-view]').forEach(btn=>{
        btn.addEventListener('click', function(){
            document.querySelectorAll('[data-view]').forEach(b=>b.classList.remove('active'));
            this.classList.add('active');
            calendar.changeView(this.dataset.view);
        });
    });

    // patient lookup
    const lookup = document.getElementById('patient_lookup');
    const suggestions = document.getElementById('patient_suggestions');
    let lookupTimer = null;
    lookup.addEventListener('input', function(){
        const q = this.value.trim();
        if(lookupTimer) clearTimeout(lookupTimer);
        if(!q){ suggestions.style.display='none'; return; }
        lookupTimer = setTimeout(()=>{
            fetch('/doctor/calendar/patients?query='+encodeURIComponent(q))
            .then(r=>r.json()).then(data=>{
                suggestions.innerHTML = '';
                data.forEach(p=>{
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'list-group-item list-group-item-action';
                    el.textContent = p.name + ' — ' + (p.code||p.phone||'');
                    el.dataset.id = p.id;
                    el.addEventListener('click', ()=>{
                        document.getElementById('patient_id').value = p.id;
                        lookup.value = p.name + ' — ' + (p.code||p.phone||'');
                        suggestions.style.display = 'none';
                    });
                    suggestions.appendChild(el);
                });
                suggestions.style.display = data.length ? '' : 'none';
            });
        }, 250);
    });

    // save form
    document.getElementById('apptForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = document.getElementById('appt_id').value;
        const payload = {
            patient_id: document.getElementById('patient_id').value,
            starts_at: document.getElementById('starts_at').value,
            ends_at: document.getElementById('ends_at').value,
            status: document.getElementById('status').value,
            notes: document.getElementById('notes').value,
        };
        const url = id ? `/doctor/calendar/appointments/${id}` : '/doctor/calendar/appointments';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify(payload)
        }).then(async res=>{
            if(res.ok){
                modalEl.hide();
                calendar.refetchEvents();
            } else {
                const json = await res.json();
                alert(json.message || 'خطأ في المدخلات');
            }
        });
    });

    document.getElementById('deleteBtn').addEventListener('click', function(){
        if(!confirm('هل تريد فعلاً إلغاء الموعد؟')) return;
        const id = document.getElementById('appt_id').value;
        fetch(`/doctor/calendar/appointments/${id}`,{
            method:'DELETE',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
        }).then(()=>{ modalEl.hide(); calendar.refetchEvents(); });
    });
});
</script>
@endpush
@endsection
