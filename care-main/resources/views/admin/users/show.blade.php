@extends('layouts.app')

@section('content')
<div class="container">
    <h2>User Profile</h2>

    <form method="post" action="{{ route('admin.users.update', $user) }}">
        @csrf
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" value="{{ old('name',$user->name) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone</label>
                            <input name="phone" class="form-control" value="{{ $user->doctor->phone ?? $user->patient->phone ?? '' }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Bio / Address</label>
                            <textarea name="bio" class="form-control">{{ old('bio', $user->doctor->bio ?? $user->patient->address ?? '') }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Specialization (for doctors)</label>
                            <input name="specialty" class="form-control" value="{{ old('specialty', $user->doctor->specialty ?? '') }}">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                @foreach($roles as $r)
                                    <option value="{{ $r }}" {{ $user->roles->contains('name',$r)? 'selected':'' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Save</button>
                            <form method="post" action="{{ route('admin.users.reset_password', $user) }}">@csrf
                                <input type="password" name="password" placeholder="new password" class="form-control d-inline-block" style="width:auto;display:inline-block">
                                <input type="password" name="password_confirmation" placeholder="confirm" class="form-control d-inline-block" style="width:auto;display:inline-block">
                                <button class="btn btn-outline-secondary">Reset Password</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Account</h5>
                        <div class="mb-2">Email: {{ $user->email }}</div>
                        <div class="mb-2">Status: {{ $user->active? 'Active':'Inactive' }}</div>
                        <div class="mb-2">Joined: {{ $user->created_at->toDateString() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
