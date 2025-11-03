@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="bg-green-500 text-white rounded-t-lg px-6 py-4 text-lg font-bold">إنشاء مستخدم جديد كطبيب</div>
                <div class="px-6 py-6">
                    {{-- عرض الأخطاء --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.doctors.store') }}">
                        @csrf

                        {{-- بيانات المستخدم --}}
                        <div class="mb-4">
                            <label for="name" class="block font-bold mb-1">الاسم الكامل</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block font-bold mb-1">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                required>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block font-bold mb-1">كلمة المرور</label>
                                <input type="password" name="password" id="password"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                    required>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block font-bold mb-1">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                    required>
                            </div>
                        </div>
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="appointment_fee" class="block font-bold mb-1">رسوم الموعد ($)</label>
                                <input type="number" step="0.01" name="appointment_fee" id="appointment_fee" value="{{ old('appointment_fee') }}"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                    required></div>
                            <div>
                                <label for="profit_share" class="block font-bold mb-1">نسبة الأرباح (%)</label>
                                <input type="number" step="0.01" name="profit_share" id="profit_share" value="{{ old('profit_share') }}"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                    required></div>
                        </div>
                        {{-- بيانات الطبيب --}}
                        <div class="mb-4">
                            <label for="specialty" class="block font-bold mb-1">التخصص</label>
                            <input type="text" name="specialty" id="specialty" value="{{ old('specialty') }}"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>

                        <div class="mb-6">
                            <label for="phone" class="block font-bold mb-1">الهاتف</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>

                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded transition">إنشاء كطبيب</button>

                        <a href="{{ route('admin.doctors.index') }}" class="block mt-4 text-center text-sm text-gray-600 hover:underline">عودة لقائمة الأطباء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
