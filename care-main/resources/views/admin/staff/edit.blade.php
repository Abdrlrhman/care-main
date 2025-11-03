@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <div class="max-w-lg mx-auto mt-8 bg-white rounded shadow p-6">
    <h3 class="text-lg font-bold mb-4">تعديل بيانات الموظف</h3>

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="block mb-1">الاسم</label>
        <input name="name" value="{{ old('name', $staff->name) }}" required class="w-full rounded border px-3 py-2"/>
      </div>

      <div class="mb-3">
        <label class="block mb-1">البريد الإلكتروني</label>
        <input name="email" type="email" value="{{ old('email', $staff->email) }}" required class="w-full rounded border px-3 py-2"/>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="block mb-1">كلمة المرور (فارغ إن ما عايز تغيّر)</label>
          <input name="password" type="password" class="w-full rounded border px-3 py-2"/>
        </div>
        <div>
          <label class="block mb-1">تأكيد كلمة المرور</label>
          <input name="password_confirmation" type="password" class="w-full rounded border px-3 py-2"/>
        </div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">الهاتف</label>
        <input name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full rounded border px-3 py-2"/>
      </div>

      <button class="w-full bg-yellow-500 text-white py-2 rounded">حفظ التغييرات</button>
    </form>
  </div>
</div>
@endsection
