@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8 px-4">
  <div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">تسجيل حساب كمريض</h2>

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register.patient.store') }}">
      @csrf

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">الاسم الكامل</label>
        <input name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2" />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">البريد الإلكتروني</label>
        <input name="email" value="{{ old('email') }}" type="email" required class="w-full border rounded px-3 py-2" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium mb-1">كلمة المرور</label>
          <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">تأكيد كلمة المرور</label>
          <input name="password_confirmation" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium mb-1">الهاتف</label>
          <input name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">الجنس</label>
          <select name="gender" class="w-full border rounded px-3 py-2">
            <option value="">اختَر</option>
            <option value="male" {{ old('gender')=='male' ? 'selected':'' }}>ذكر</option>
            <option value="female" {{ old('gender')=='female' ? 'selected':'' }}>أنثى</option>
            <option value="other" {{ old('gender')=='other' ? 'selected':'' }}>آخر</option>
          </select>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">تاريخ الميلاد</label>
        <input name="birth_date" value="{{ old('birth_date') }}" type="date" class="w-full border rounded px-3 py-2" />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">العنوان</label>
        <input name="address" value="{{ old('address') }}" class="w-full border rounded px-3 py-2" />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">ملاحظات</label>
        <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3">{{ old('notes') }}</textarea>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">تسجيل</button>
        <a href="{{ route('login') }}" class="text-sm text-gray-600">هل لديك حساب؟ تسجيل الدخول</a>
      </div>
    </form>
  </div>
</div>
@endsection
