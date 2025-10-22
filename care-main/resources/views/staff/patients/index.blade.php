@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">Patients</h2>
        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition" data-bs-toggle="modal" data-bs-target="#addPatientModal">Add Patient</button>
    </div>

    @if(session('status'))<div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-700">{{ session('status') }}</div>@endif

    <form method="GET" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="q" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500 w-full" placeholder="Search by name, code or phone" value="{{ request('q') }}">
            <button class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Search</button>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow">
        <div class="p-0">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Code</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Gender</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Phone</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Age</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Last Visit</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $p)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $p->code }}</td>
                                <td class="px-4 py-2">{{ $p->name }}</td>
                                <td class="px-4 py-2">{{ ucfirst($p->gender) }}</td>
                                <td class="px-4 py-2">{{ $p->phone }}</td>
                                <td class="px-4 py-2">{{ $p->birth_date ? $p->birth_date->age : '—' }}</td>
                                <td class="px-4 py-2">{{ optional($p->appointments()->latest('starts_at')->first())->starts_at ? optional($p->appointments()->latest('starts_at')->first())->starts_at->format('Y-m-d') : '—' }}</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <a href="{{ route('staff.patients.show', $p->id) }}" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs">View</a>
                                    <a href="{{ route('staff.patients.edit', $p->id) }}" class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-400">No patients found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none p-3">
                @forelse($patients as $p)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><strong>{{ $p->name }}</strong></div>
                                <div class="text-muted">{{ $p->code }}</div>
                            </div>
                            <div>{{ $p->phone }} • {{ ucfirst($p->gender) }}</div>
                            <div class="mt-2">
                                <a href="{{ route('staff.patients.show', $p->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('staff.patients.edit', $p->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">No patients found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $patients->withQueryString()->links() }}</div>

    {{-- Add Patient Modal --}}
    <div class="modal fade" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('staff.patients.store') }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Add Patient</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-2"><label class="form-label">Code</label><input name="code" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                        <div class="mb-2"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
                        <div class="mb-2"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control"></div>
                        <div class="mb-2"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        <div class="mb-2"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Add</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
