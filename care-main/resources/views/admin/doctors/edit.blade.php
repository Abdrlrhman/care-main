@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="bg-yellow-500 text-white rounded-t-lg px-6 py-4 text-lg font-bold">تعديل بيانات الطبيب</div>
                <div class="px-6 py-6">
                    <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-6">
                            <label for="specialty" class="block font-bold mb-1">التخصص</label>
                            <input type="text" name="specialty" id="specialty"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                value="{{ $doctor->specialty }}">
                        </div>
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label for="appointment_fee" class="block font-bold mb-1">رسوم الموعد ($)</label>
                                <input type="number" step="0.01" name="appointment_fee" id="appointment_fee"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    value="{{ $doctor->appointment_fee }}">
                            </div>
                            <div>
                                <label for="profit_share" class="block font-bold mb-1">نسبة الأرباح (%)</label>
                                <input type="number" step="0.01" name="profit_share" id="profit_share"
                                    class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    value="{{ $doctor->profit_share }}">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="phone" class="block font-bold mb-1">الهاتف</label>
                            <input type="text" name="phone" id="phone"
                                class="block w-full rounded bg-gray-100 border border-gray-300 px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                value="{{ $doctor->phone }}">
                        </div>
                        <button type="submit"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded transition">تعديل</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
