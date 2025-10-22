@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="bg-yellow-500 text-white rounded-t-lg px-6 py-4 text-lg font-bold">بيانات الطبيب</div>
                <div class="px-6 py-6">
                    <div class="mb-6">
                        <label class="block font-bold mb-1">الاسم الكامل</label>
                        <p class="text-gray-700 bg-gray-100 rounded px-4 py-2">{{ $doctor->user->name ?? 'غير محدد' }}</p>
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold mb-1">البريد الإلكتروني</label>
                        <p class="text-gray-700 bg-gray-100 rounded px-4 py-2">{{ $doctor->user->email ?? 'غير محدد' }}</p>
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold mb-1">التخصص</label>
                        <p class="text-gray-700 bg-gray-100 rounded px-4 py-2">{{ $doctor->specialty ?? 'غير محدد' }}</p>
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold mb-1">الهاتف</label>
                        <p class="text-gray-700 bg-gray-100 rounded px-4 py-2">{{ $doctor->phone ?? 'غير محدد' }}</p>
                    </div>
                    <div class="flex">
                        <a href="{{ route('admin.doctors.edit', $doctor) }}" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded transition">تعديل</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection