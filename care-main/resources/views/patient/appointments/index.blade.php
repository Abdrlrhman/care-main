@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h3 class="text-xl font-bold text-blue-700">My Appointments</h3>
        <form class="flex gap-2" method="get">
            <select name="status" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500">
                <option value="">All</option>
                <option value="upcoming" {{ request('status')=='upcoming'?'selected':'' }}>Upcoming</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
            <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Filter</button>
        </form>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Time</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Doctor</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Specialization</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $a)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $a->starts_at->toDateString() }}</td>
                        <td class="px-4 py-2">{{ $a->starts_at->format('H:i') }} - {{ $a->ends_at->format('H:i') }}</td>
                        <td class="px-4 py-2">{{ $a->doctor->name }}</td>
                        <td class="px-4 py-2">{{ $a->doctor->specialty }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                @if($a->status=='cancelled') bg-gray-300 text-gray-700
                                @elseif($a->status=='pending') bg-yellow-100 text-yellow-800
                                @elseif($a->status=='completed') bg-green-100 text-green-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" href="{{ url('/patient/appointments/'.$a->id) }}">View Details</a>
                            <a class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs" href="{{ url('/appointments/book?appointment_id='.$a->id) }}">Reschedule</a>
                            @if($a->status!='cancelled')
                                <button class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs" data-bs-toggle="modal" data-bs-target="#cancelModal" data-appt-id="{{ $a->id }}">Cancel</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="block md:hidden">
        <div class="grid grid-cols-1 gap-3">
            @foreach($appointments as $a)
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-bold text-blue-700">{{ $a->starts_at->toDayDateTimeString() }}</div>
                            <div class="text-gray-500">Dr. {{ $a->doctor->name }} — {{ $a->doctor->specialty }}</div>
                        </div>
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                            @if($a->status=='cancelled') bg-gray-300 text-gray-700
                            @elseif($a->status=='pending') bg-yellow-100 text-yellow-800
                            @elseif($a->status=='completed') bg-green-100 text-green-700
                            @else bg-blue-100 text-blue-700 @endif">
                            {{ ucfirst($a->status) }}
                        </span>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <a class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" href="{{ url('/patient/appointments/'.$a->id) }}">View</a>
                        <a class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs" href="{{ url('/appointments/book?appointment_id='.$a->id) }}">Reschedule</a>
                        @if($a->status!='cancelled')
                            <button class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs" data-bs-toggle="modal" data-bs-target="#cancelModal" data-appt-id="{{ $a->id }}">Cancel</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6">{{ $appointments->links() }}</div>

    <!-- Cancel modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden" id="cancelModal">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <form method="post" id="cancelForm">
            @csrf
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h5 class="font-bold text-lg">Cancel Appointment</h5>
                <button type="button" class="text-gray-400 hover:text-gray-700 text-2xl" onclick="document.getElementById('cancelModal').classList.add('hidden')">&times;</button>
            </div>
            <div class="px-6 py-4">Are you sure you want to cancel this appointment?</div>
            <div class="px-6 py-4 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Yes, cancel</button>
                <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" onclick="document.getElementById('cancelModal').classList.add('hidden')">No</button>
            </div>
        </form>
      </div>
    </div>
</div>

document.addEventListener('DOMContentLoaded', function(){
    var cancelModal = document.getElementById('cancelModal');
    cancelModal.addEventListener('show.bs.modal', function(e){
        var btn = e.relatedTarget;
        var id = btn.getAttribute('data-appt-id');
        var form = document.getElementById('cancelForm');
        form.action = '/patient/appointments/' + id + '/cancel';
    });
});

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = btn.getAttribute('data-appt-id');
            var form = document.getElementById('cancelForm');
            form.action = '/patient/appointments/' + id + '/cancel';
            document.getElementById('cancelModal').classList.remove('hidden');
        });
    });
});
</script>
@endsection

@endsection
