@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-blue-700">Services</h2>
    </div>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <input type="search" name="q" value="{{ request('q') }}" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500" placeholder="Search services by name or description">
        <select name="active" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500">
            <option value="">All</option>
            <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active only</option>
        </select>
        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Filter</button>
    </form>

    <div class="bg-white rounded-lg shadow">
        <div class="p-0">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Description</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Price</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $s)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $s->name }}</td>
                                <td class="px-4 py-2">{{ Str::limit($s->description, 120) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($s->price,2) }}</td>
                                <td class="px-4 py-2 text-right">
                                    @if($s->active)
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-gray-300 text-gray-700">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-gray-400">No services found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="block md:hidden p-3">
                <div class="grid grid-cols-1 gap-3">
                    @foreach($services as $s)
                        <div class="bg-gray-50 rounded p-4 flex flex-col justify-between">
                            <div class="font-bold text-blue-700 mb-2">{{ $s->name }}</div>
                            <div class="text-sm text-gray-500 mb-2">{{ Str::limit($s->description, 120) }}</div>
                            <div class="text-xl font-bold text-blue-700 mb-2">{{ number_format($s->price,2) }}</div>
                            <div>
                                @if($s->active)
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-gray-300 text-gray-700">Inactive</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
                                    <div class="d-flex justify-content-between"><div><strong>{{ $s->name }}</strong><div class="text-muted small">{{ Str::limit($s->description, 100) }}</div></div><div class="text-end">{{ number_format($s->price,2) }}<div>@if($s->active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</div></div></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $services->withQueryString()->links() }}</div>
</div>
@endsection
