@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">Welcome, {{ $patient->name }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 flex flex-col justify-between">
            <h6 class="text-lg font-semibold text-gray-700 mb-2">Next Appointment</h6>
            @if($next)
                <div class="text-blue-700 font-bold">{{ $next->starts_at->toDayDateTimeString() }}</div>
                <div class="text-gray-500">with Dr. {{ $next->doctor->name }}</div>
            @else
                <div class="text-gray-400">No upcoming appointments</div>
                <a href="/doctor/calendar" class="mt-2 inline-block px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Book Appointment</a>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-5 flex flex-col justify-between">
            <h6 class="text-lg font-semibold text-gray-700 mb-2">Outstanding Balance</h6>
            <div class="text-2xl font-bold text-blue-700">{{ number_format($outstanding,2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 flex flex-col justify-between">
            <h6 class="text-lg font-semibold text-gray-700 mb-2">Last Prescription</h6>
            @if($lastPrescription)
                <div class="text-blue-700 font-bold">{{ $lastPrescription->created_at->toDateString() }}</div>
                <div class="text-gray-500">{{ implode(', ', $lastPrescription->medicines ?? []) }}</div>
            @else
                <div class="text-gray-400">No prescriptions yet</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="font-semibold text-gray-700">Upcoming Appointments</div>
                <a href="/patient/appointments" class="text-blue-600 hover:underline text-sm">View all</a>
            </div>
            <div class="p-5">
                @if($upcoming->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-gray-400">You have no upcoming appointments.</p>
                        <a href="/doctor/services" class="mt-2 inline-block px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">View Services</a>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach($upcoming as $a)
                            <li class="flex items-center justify-between bg-gray-50 rounded px-4 py-3">
                                <div>
                                    <div class="font-bold text-blue-700">{{ $a->starts_at->toDayDateTimeString() }}</div>
                                    <div class="text-gray-500">Dr. {{ $a->doctor->name }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/patient/appointments/{{ $a->id }}" class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs">View</a>
                                    <a href="/patient/appointments/{{ $a->id }}/reschedule" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">Reschedule</a>
                                    <form method="post" action="/patient/appointments/{{ $a->id }}/cancel" onsubmit="return confirm('Cancel appointment?')">
                                        @csrf
                                        <button class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs">Cancel</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="font-semibold text-gray-700">Recent Medical Records</div>
                <a href="/patient/medical-records" class="text-blue-600 hover:underline text-sm">View all</a>
            </div>
            <div class="p-5">
                @if($records->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-gray-400">No medical records yet.</p>
                        <a href="/doctor/services" class="mt-2 inline-block px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">View Services</a>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach($records as $r)
                            <li class="flex items-center justify-between bg-gray-50 rounded px-4 py-3">
                                <div>
                                    <div class="font-bold text-blue-700">{{ optional($r->visit_date)->toDateString() }}</div>
                                    <div class="text-gray-500">{{ \Illuminate\Support\Str::limit($r->diagnosis, 80) }}</div>
                                </div>
                                <a href="/patient/medical-records/{{ $r->id }}" class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs">View</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection
