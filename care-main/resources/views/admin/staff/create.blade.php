@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <div class="max-w-lg mx-auto mt-8 bg-white rounded shadow p-6">
    <h3 class="text-lg font-bold mb-4">إنشاء موظف جديد</h3>

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.staff.store') }}">
      @csrf
      <div class="mb-3">
        <label class="block mb-1">الاسم</label>
        <input name="name" value="{{ old('name') }}" required class="w-full rounded border px-3 py-2"/>
      </div>

      <div class="mb-3">
        <label class="block mb-1">البريد الإلكتروني</label>
        <input name="email" type="email" value="{{ old('email') }}" required class="w-full rounded border px-3 py-2"/>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="block mb-1">كلمة المرور</label>
          <input name="password" type="password" required class="w-full rounded border px-3 py-2"/>
        </div>
        <div>
          <label class="block mb-1">تأكيد كلمة المرور</label>
          <input name="password_confirmation" type="password" required class="w-full rounded border px-3 py-2"/>
        </div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">الهاتف (اختياري)</label>
        <input name="phone" value="{{ old('phone') }}" class="w-full rounded border px-3 py-2"/>
      </div>

      <button class="w-full bg-blue-600 text-white py-2 rounded">إنشاء موظف</button>
    </form>
  </div>
</div>
@endsection
