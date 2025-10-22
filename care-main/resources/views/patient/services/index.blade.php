@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">Services</h2>
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full max-w-lg" method="get">
            <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500" placeholder="Search services...">
            <input name="min_price" value="{{ request('min_price') }}" type="number" step="0.01" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500" placeholder="Min">
            <input name="max_price" value="{{ request('max_price') }}" type="number" step="0.01" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500" placeholder="Max">
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($services as $s)
            <div class="bg-white rounded-lg shadow p-5 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <h5 class="text-lg font-bold text-blue-700">{{ $s->name }}</h5>
                        @if($s->description)
                            <p class="text-sm text-gray-500">{{ Str::limit($s->description, 120) }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-blue-700">{{ number_format($s->price,2) }}</div>
                        @if(!$s->active)
                            <div class="text-sm text-red-600">Inactive</div>
                        @else
                            <div class="text-sm text-green-600">Active</div>
                        @endif
                    </div>
                </div>

                <div class="mt-auto flex gap-2">
                    <a href="{{ route('appointments.book', ['service_id' => $s->id]) }}" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs">Book Appointment</a>
                    <a href="#" class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs">Details</a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-400">No services found.</div>
        @endforelse
    </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No services found.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $services->links() }}</div>
</div>
@endsection
