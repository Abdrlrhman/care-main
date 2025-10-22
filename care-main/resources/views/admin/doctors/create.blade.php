@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="bg-green-500 text-white rounded-t-lg px-6 py-4 text-lg font-bold">إضافة طبيب جديد</div>
                <div class="px-6 py-6">
                    <form method="POST" action="{{ route('admin.doctors.store') }}">
                        @csrf
                        <div class="mb-6">
                            <label for="user_id" class="block font-bold mb-1">اختر المستخدم</label>
                            <select name="user_id" id="user_id"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
                                required>
                                <option value="">-- اختر --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-6">
                            <label for="specialty" class="block font-bold mb-1">التخصص</label>
                            <input type="text" name="specialty" id="specialty"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <div class="mb-6">
                            <label for="phone" class="block font-bold mb-1">الهاتف</label>
                            <input type="text" name="phone" id="phone"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded transition">إضافة</button>
                        <a href="{{ route('admin.users.create') }}" class="block mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded text-center transition">إنشاء مستخدم جديد كطبيب</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
