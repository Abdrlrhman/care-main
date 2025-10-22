@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white text-center">
                    إدارة المستخدمين
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center">قائمة المستخدمين</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الدور</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- هنا تضاف بيانات المستخدمين من الكنترولر --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
