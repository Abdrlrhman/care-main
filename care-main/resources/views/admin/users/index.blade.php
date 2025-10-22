@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Users</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
    </div>

    <form class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ request('role')==$r? 'selected':'' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select name="specialization" class="form-select" onchange="this.form.submit()">
                <option value="">All specializations</option>
                @foreach($specializations as $s)
                    <option value="{{ $s }}" {{ request('specialization')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                        <td>{{ $u->doctor->phone ?? $u->patient->phone ?? '' }}</td>
                        <td>{{ $u->active? 'Active':'Inactive' }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="post" action="{{ route('admin.users.deactivate', $u) }}" style="display:inline">@csrf<button class="btn btn-sm btn-outline-danger">Deactivate</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-block d-md-none">
        <div class="row">
            @foreach($users as $u)
                <div class="col-12 mb-2">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->roles->pluck('name')->join(', ') }}</div>
                                <div class="small">{{ $u->doctor->phone ?? $u->patient->phone ?? '' }}</div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.users.show', $u) }}">View</a>
                                    <a class="dropdown-item" href="#">Edit</a>
                                    <form method="post" action="{{ route('admin.users.deactivate', $u) }}">@csrf<button class="dropdown-item">Deactivate</button></form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
