@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">Edit Patient #{{ $patient->code }}</h2>
    @if($errors->any())<div class="mb-4 px-4 py-2 rounded bg-red-100 text-red-700">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('staff.patients.update', $patient->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
            <input name="code" class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $patient->code }}" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input name="name" class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $patient->name }}" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border rounded px-3 py-2 text-gray-700">
                <option value="">Select</option>
                <option value="male" {{ $patient->gender=='male'?'selected':'' }}>Male</option>
                <option value="female" {{ $patient->gender=='female'?'selected':'' }}>Female</option>
                <option value="other" {{ $patient->gender=='other'?'selected':'' }}>Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input name="phone" class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $patient->phone }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
            <input type="date" name="birth_date" class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $patient->birth_date?->format('Y-m-d') }}">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full border rounded px-3 py-2 text-gray-700">{{ $patient->address }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" class="w-full border rounded px-3 py-2 text-gray-700">{{ $patient->notes }}</textarea>
        </div>
        <div class="md:col-span-2 flex gap-2 mt-4">
            <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Save</button>
            <a href="{{ route('staff.patients.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Back</a>
        </div>
    </form>
</div>
@endsection
