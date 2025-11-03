@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 mt-6">
  <div class="bg-white rounded shadow p-6 max-w-md mx-auto">
    <h3 class="text-xl font-bold mb-4">معلومات الموظف</h3>
    <p><strong>الاسم:</strong> {{ $staff->name }}</p>
    <p><strong>الإيميل:</strong> {{ $staff->email }}</p>
    <p><strong>الهاتف:</strong> {{ $staff->phone ?? '-' }}</p>

    <div class="mt-4 flex gap-2">
      <a href="{{ route('admin.staff.edit', $staff->id) }}" class="px-3 py-2 bg-yellow-400 rounded">تعديل</a>
      <a href="{{ route('admin.staff.index') }}" class="px-3 py-2 bg-gray-200 rounded">رجوع</a>
    </div>
  </div>
</div>
@endsection
