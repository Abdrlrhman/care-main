@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-2 py-4">
    <h3 class="text-2xl font-bold text-blue-700 mb-4">سجل طبي</h3>
    <div class="bg-white rounded-lg shadow mb-4 p-4">
        <div class="mb-2"><span class="font-semibold text-gray-700">التاريخ:</span> {{ optional($medicalRecord->visit_date)->toDayDateTimeString() }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">الدكتور:</span> {{ $medicalRecord->doctor->name }}</div>
        <hr class="my-4">
        <h5 class="text-lg font-semibold text-gray-700 mb-2">التشخيص</h5>
        <div class="mb-4 text-gray-700">{{ $medicalRecord->diagnosis }}</div>
        <h5 class="text-lg font-semibold text-gray-700 mb-2">ملاحظات</h5>
        <div class="mb-4 text-gray-700">{{ $medicalRecord->notes }}</div>
        <h5 class="text-lg font-semibold text-gray-700 mb-2">المرفقات</h5>
        @if(!empty($medicalRecord->attachments))
            <ul class="list-disc pl-6">
            @foreach($medicalRecord->attachments as $att)
                <li><a href="{{ asset('storage/'.$att) }}" download class="text-blue-600 hover:underline">{{ basename($att) }}</a></li>
            @endforeach
            </ul>
        @else
            <div class="text-gray-400">لا توجد مرفقات</div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h5 class="text-lg font-semibold text-gray-700 mb-4">الوصفات الطبية</h5>
        @if($medicalRecord->prescriptions->isEmpty())
            <div class="text-gray-400">لا توجد وصفات</div>
        @else
            <ul class="space-y-3">
                @foreach($medicalRecord->prescriptions as $p)
                    <li class="bg-gray-50 rounded px-4 py-3">
                        <div class="font-bold text-blue-700">{{ optional($p->created_at)->toDateString() }}</div>
                        <div class="text-gray-700">{{ $p->notes ?? '' }}</div>
                        <div class="text-gray-500">{{ implode('، ', $p->medicines ?? []) }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@endsection
