@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Services Catalog</h3>
        <form class="d-flex" method="get">
            <input type="text" name="q" class="form-control me-2" placeholder="Search service" value="{{ request('q') }}">
            <div class="form-check form-check-inline me-2">
                <input class="form-check-input" type="checkbox" id="active_only" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }}>
                <label class="form-check-label" for="active_only">Active only</label>
            </div>
            <button class="btn btn-outline-primary">Search</button>
        </form>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Price</th><th>Active</th></tr></thead>
            <tbody>
                @foreach($services as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>{{ number_format($s->price,2) }}</td>
                        <td>{!! $s->active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-block d-md-none">
        <div class="row">
            @foreach($services as $s)
                <div class="col-12 mb-2">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $s->name }}</div>
                                <div class="text-muted">{{ number_format($s->price,2) }}</div>
                            </div>
                            <div>{!! $s->active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{ $services->links() }}
</div>

@endsection
