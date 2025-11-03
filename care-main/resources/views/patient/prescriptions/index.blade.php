@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-2 py-4">
    <h3 class="text-2xl font-bold text-blue-700 mb-4">وصفاتي الطبية</h3>

    @if($grouped->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-400 mb-4">لا توجد وصفات.</p>
            <a href="/doctor/services" class="inline-block px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">عرض الخدمات</a>
        </div>
    @else
        @foreach($grouped as $key => $items)
            @php list($date, $doctor) = explode('|', $key); @endphp
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <div class="font-semibold text-gray-700"><strong>{{ $date }}</strong> — الدكتور {{ $doctor }}</div>
                    <div class="flex gap-2 flex-wrap">
                        <button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" onclick="window.print()">طباعة</button>
                        <a href="#" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">تحميل PDF</a>
                    </div>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($items as $pres)
                        <div class="bg-gray-50 rounded p-3 flex flex-col justify-between">
                            <div class="font-bold text-blue-700 mb-2">{{ $pres->drug_name }}</div>
                            <div class="text-sm text-gray-700">الجرعة: {{ $pres->dosage }}</div>
                            <div class="text-sm text-gray-700">التكرار: {{ $pres->frequency }}</div>
                            <div class="text-sm text-gray-700">المدة: {{ $pres->duration }}</div>
                            <div class="mt-2 text-gray-500">{{ $pres->notes }}</div>
                            <div class="mt-4 flex gap-2 flex-wrap">
                                <button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" onclick="window.print()">طباعة</button>
                                <a href="#" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">تحميل PDF</a>
                                <button class="px-3 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs" onclick="setReminder('{{ addslashes($pres->drug_name) }}','{{ addslashes($pres->notes) }}')">تذكير</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

@section('scripts')
<script>
function setReminder(title, body){
    if (!('Notification' in window)) return alert('المتصفح لا يدعم التنبيهات');
    Notification.requestPermission().then(function(permission){
        if (permission === 'granted'){
            new Notification('تذكير وصفة: ' + title, { body: body });
            alert('تم ضبط التذكير (تم عرض التنبيه الآن).');
        }
    });
}
</script>
@endsection

@endsection
