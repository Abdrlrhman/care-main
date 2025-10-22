@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">Patient: {{ $patient->name }} ({{ $patient->code }})</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="mb-2"><span class="font-semibold text-gray-700">Phone:</span> {{ $patient->phone }}</p>
            <p class="mb-2"><span class="font-semibold text-gray-700">Gender:</span> {{ ucfirst($patient->gender) }}</p>
            <p class="mb-2"><span class="font-semibold text-gray-700">Birth Date:</span> {{ $patient->birth_date?->format('Y-m-d') }}</p>
            <p class="mb-2"><span class="font-semibold text-gray-700">Address:</span><br>{{ $patient->address }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="mb-2"><span class="font-semibold text-gray-700">Notes:</span><br>{{ $patient->notes }}</p>
        </div>
    </div>
    <a href="{{ route('staff.patients.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Back</a>
</div>
@endsection
