@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-blue-700">My Medical Records</h3>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Doctor</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Diagnosis</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ optional($r->visit_date)->toDateString() }}</td>
                        <td class="px-4 py-2">{{ $r->doctor->name }}</td>
                        <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($r->diagnosis, 80) }}</td>
                        <td class="px-4 py-2">
                            <a class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" href="{{ route('patient.medical_records.show', $r) }}">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="block md:hidden">
        <div class="grid grid-cols-1 gap-3">
            @foreach($records as $r)
                <div class="bg-white rounded-lg shadow p-4 mb-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-bold text-blue-700">{{ optional($r->visit_date)->toDayDateString() }}</div>
                            <div class="text-gray-500">Dr. {{ $r->doctor->name }}</div>
                        </div>
                        <a class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" href="{{ route('patient.medical_records.show', $r) }}">View</a>
                    </div>
                    <div class="mt-2 text-gray-500">{{ $r->diagnosis }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6">{{ $records->links() }}</div>
</div>

@endsection
