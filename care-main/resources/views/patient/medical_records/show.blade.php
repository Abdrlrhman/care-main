@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h3 class="text-2xl font-bold text-blue-700 mb-6">Medical Record</h3>
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="mb-2"><span class="font-semibold text-gray-700">Date:</span> {{ optional($medicalRecord->visit_date)->toDayDateTimeString() }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Doctor:</span> Dr. {{ $medicalRecord->doctor->name }}</div>
        <hr class="my-4">
        <h5 class="text-lg font-semibold text-gray-700 mb-2">Diagnosis</h5>
        <div class="mb-4 text-gray-700">{{ $medicalRecord->diagnosis }}</div>
        <h5 class="text-lg font-semibold text-gray-700 mb-2">Notes</h5>
        <div class="mb-4 text-gray-700">{{ $medicalRecord->notes }}</div>
        <h5 class="text-lg font-semibold text-gray-700 mb-2">Attachments</h5>
        @if(!empty($medicalRecord->attachments))
            <ul class="list-disc pl-6">
            @foreach($medicalRecord->attachments as $att)
                <li><a href="{{ asset('storage/'.$att) }}" download class="text-blue-600 hover:underline">{{ basename($att) }}</a></li>
            @endforeach
            </ul>
        @else
            <div class="text-gray-400">No attachments</div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h5 class="text-lg font-semibold text-gray-700 mb-4">Prescriptions</h5>
        @if($medicalRecord->prescriptions->isEmpty())
            <div class="text-gray-400">No prescriptions</div>
        @else
            <ul class="space-y-3">
                @foreach($medicalRecord->prescriptions as $p)
                    <li class="bg-gray-50 rounded px-4 py-3">
                        <div class="font-bold text-blue-700">{{ optional($p->created_at)->toDateString() }}</div>
                        <div class="text-gray-700">{{ $p->notes ?? '' }}</div>
                        <div class="text-gray-500">{{ implode(', ', $p->medicines ?? []) }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@endsection
