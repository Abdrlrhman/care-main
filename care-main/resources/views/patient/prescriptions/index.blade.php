@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h3 class="text-2xl font-bold text-blue-700 mb-6">My Prescriptions</h3>

    @if($grouped->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-400 mb-4">No prescriptions found.</p>
            <a href="/doctor/services" class="inline-block px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">View Services</a>
        </div>
    @else
        @foreach($grouped as $key => $items)
            @php list($date, $doctor) = explode('|', $key); @endphp
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <div class="font-semibold text-gray-700"><strong>{{ $date }}</strong> — Dr. {{ $doctor }}</div>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" onclick="window.print()">Print</button>
                        <a href="#" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">Download PDF</a>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($items as $pres)
                        <div class="bg-gray-50 rounded p-4 flex flex-col justify-between">
                            <div class="font-bold text-blue-700 mb-2">{{ $pres->drug_name }}</div>
                            <div class="text-sm text-gray-700">Dosage: {{ $pres->dosage }}</div>
                            <div class="text-sm text-gray-700">Frequency: {{ $pres->frequency }}</div>
                            <div class="text-sm text-gray-700">Duration: {{ $pres->duration }}</div>
                            <div class="mt-2 text-gray-500">{{ $pres->notes }}</div>
                            <div class="mt-4 flex gap-2">
                                <button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs" onclick="window.print()">Print</button>
                                <a href="#" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">Download PDF</a>
                                <button class="px-3 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs" onclick="setReminder('{{ addslashes($pres->drug_name) }}','{{ addslashes($pres->notes) }}')">Set Reminder</button>
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
    if (!('Notification' in window)) return alert('Notifications not supported');
    Notification.requestPermission().then(function(permission){
        if (permission === 'granted'){
            new Notification('Prescription reminder: ' + title, { body: body });
            alert('Reminder set (local notification shown now).');
        }
    });
}
</script>
@endsection

@endsection
