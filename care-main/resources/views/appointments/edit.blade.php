@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 shadow rounded">
    <h1 class="text-xl font-semibold mb-4">تعديل الموعد</h1>

    <form method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block text-sm text-gray-600">الطبيب</label>
            <select name="doctor_id" class="w-full border rounded p-2">
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm text-gray-600">المريض</label>
            <select name="patient_id" class="w-full border rounded p-2">
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm text-gray-600">تاريخ ووقت البدء</label>
            <input type="datetime-local" name="starts_at" value="{{ $appointment->starts_at->format('Y-m-d\TH:i') }}" class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label class="block text-sm text-gray-600">ملاحظات</label>
            <textarea name="notes" class="w-full border rounded p-2" rows="3">{{ $appointment->notes }}</textarea>
        </div>

        <div class="flex justify-end">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">تحديث</button>
        </div>
    </form>
</div>
@endsection
