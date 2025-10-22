@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white text-center">
                    لوحة تحكم الأدمن
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center">مرحباً بك أيها الأدمن!</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">عدد المستخدمين</li>
                        <li class="list-group-item">عدد الأطباء</li>
                        <li class="list-group-item">عدد الموظفين</li>
                        <li class="list-group-item">إدارة الصلاحيات</li>
                    </ul>
                    <a href="{{ route('admin.users') }}" class="btn btn-primary w-100 mb-2">إدارة المستخدمين</a>
                    <a href="{{ route('admin.permissions') }}" class="btn btn-info w-100 mb-2">إدارة الصلاحيات</a>
                    <a href="#" class="btn btn-secondary w-100">إعدادات النظام</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
