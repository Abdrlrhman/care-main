@extends('layouts.app')

@section('content')
<main class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5">إعدادات ساعات العمل</h2>
        <div>
            <form method="POST" action="{{ route('doctor.working_hours.reset') }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary">Reset</button></form>
        </div>
    </div>

    <form method="POST" action="{{ route('doctor.working_hours.store') }}" id="whForm">
        @csrf
        <div class="d-none d-md-block">
            <table class="table">
                <thead><tr><th>اليوم</th><th>فترات</th><th></th></tr></thead>
                <tbody>
                    @php $days=['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت']; @endphp
                    @foreach(range(0,6) as $w)
                        <tr data-weekday="{{ $w }}">
                            <td>{{ $days[$w] }}</td>
                            <td>
                                <div class="intervals" data-weekday="{{ $w }}">
                                    @foreach($hours->where('weekday',$w) as $h)
                                        <div class="d-flex g-2 mb-2 interval-row">
                                            <input type="hidden" name="intervals[][weekday]" value="{{ $w }}">
                                            <input type="time" name="intervals[][start_time]" value="{{ $h->start_time }}" class="form-control me-1" style="width:140px">
                                            <input type="time" name="intervals[][end_time]" value="{{ $h->end_time }}" class="form-control me-1" style="width:140px">
                                            <button type="button" class="btn btn-sm btn-danger remove-interval">حذف</button>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td><button type="button" class="btn btn-sm btn-outline-primary add-interval" data-weekday="{{ $w }}">إضافة فترة</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile accordions -->
        <div class="d-md-none">
            @foreach(range(0,6) as $w)
                <div class="accordion mb-2" id="day-{{ $w }}">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $w }}">{{ $days[$w] }}</button>
                        </h2>
                        <div id="collapse-{{ $w }}" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div class="intervals" data-weekday="{{ $w }}">
                                    @foreach($hours->where('weekday',$w) as $h)
                                        <div class="d-flex g-2 mb-2 interval-row">
                                            <input type="hidden" name="intervals[][weekday]" value="{{ $w }}">
                                            <input type="time" name="intervals[][start_time]" value="{{ $h->start_time }}" class="form-control me-1" style="width:140px">
                                            <input type="time" name="intervals[][end_time]" value="{{ $h->end_time }}" class="form-control me-1" style="width:140px">
                                            <button type="button" class="btn btn-sm btn-danger remove-interval">حذف</button>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary add-interval" data-weekday="{{ $w }}">إضافة فترة</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary">حفظ</button>
        </div>
    </form>

    <template id="intervalTpl">
        <div class="d-flex g-2 mb-2 interval-row">
            <input type="hidden" name="intervals[][weekday]" value="__W__">
            <input type="time" name="intervals[][start_time]" class="form-control me-1" style="width:140px">
            <input type="time" name="intervals[][end_time]" class="form-control me-1" style="width:140px">
            <button type="button" class="btn btn-sm btn-danger remove-interval">حذف</button>
        </div>
    </template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.add-interval').forEach(btn=>{
        btn.addEventListener('click', function(){
            const w = this.dataset.weekday;
            const tpl = document.getElementById('intervalTpl').innerHTML.replace(/__W__/g, w);
            const container = document.querySelector('.intervals[data-weekday="'+w+'"]');
            container.insertAdjacentHTML('beforeend', tpl);
        });
    });

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-interval')){
            e.target.closest('.interval-row').remove();
        }
    });
});
</script>
@endpush

@endsection
