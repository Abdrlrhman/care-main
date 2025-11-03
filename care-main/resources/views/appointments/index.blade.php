@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    // اعتبار إنو المريض عندو relation patient()
    if ($user && method_exists($user, 'patient') && $user->patient) {
        $pageTitle = 'مقابلاتي';
    } elseif ($user && (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('staff')))) {
        $pageTitle = 'المواعيد';
    } else {
        // الافتراضي
        $pageTitle = 'المواعيد';
    }
@endphp

<div class="max-w-6xl mx-auto p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">{{ $pageTitle }}</h1>
    <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">حجز جديد</a>
  </div>

  <div class="bg-white shadow rounded-lg p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead>
          <tr class="text-left text-sm text-gray-600">
            <th class="px-3 py-2">الوقت</th>
            <th class="px-3 py-2">الطبيب</th>
            <th class="px-3 py-2">المريض</th>
            <th class="px-3 py-2">الحالة</th>
            <th class="px-3 py-2">ملاحظات</th>
            <th class="px-3 py-2">اجراء</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          @foreach($appointments as $a)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $a->starts_at->format('Y-m-d H:i') }} - {{ $a->ends_at->format('H:i') }}</td>
            <td class="px-3 py-2">{{ $a->doctor->name ?? '—' }}</td>
            <td class="px-3 py-2">{{ $a->patient->name ?? '—' }}</td>
            <td class="px-3 py-2">{{ ucfirst($a->status) }}</td>
            <td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($a->notes, 80) }}</td>
            <td class="px-3 py-2">
              <form class="inline cancelForm" data-id="{{ $a->id }}">
                <button type="button" class="cancelBtn px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">الغاء</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $appointments->links() }}</div>
  </div>
</div>
@endsection
