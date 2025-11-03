@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 min-w-0">
  <div class="flex items-center justify-between gap-4 mt-6">
    <h2 class="text-2xl font-extrabold">الموظفين</h2>
    <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      إنشاء موظف
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 p-3 bg-green-100 text-green-800 rounded-lg shadow-sm">{{ session('success') }}</div>
  @endif

  {{-- search --}}
  <div class="mt-4">
    <form class="flex gap-2" method="GET" action="{{ route('admin.staff.index') }}">
      <input name="q" value="{{ request('q') }}" type="search" placeholder="ابحث بالاسم أو الإيميل" class="flex-1 rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
      <button type="submit" class="bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200">بحث</button>
    </form>
  </div>

  {{-- responsive table (shows on mobile too) --}}
  <div class="mt-6 bg-white rounded-lg shadow overflow-x-auto min-w-0">
    <table class="w-full text-right table-auto">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-3 text-sm font-medium">الاسم</th>
          <th class="p-3 text-sm font-medium hidden sm:table-cell">الإيميل</th>
          <th class="p-3 text-sm font-medium hidden md:table-cell">الهاتف</th>
          <th class="p-3 text-sm font-medium hidden lg:table-cell">تاريخ الإنشاء</th>
          <th class="p-3 text-sm font-medium">الإجراءات</th>
        </tr>
      </thead>

      <tbody>
        @forelse($staff as $s)
          <tr class="border-t hover:bg-gray-50">
            {{-- name + avatar --}}
            <td class="p-3 align-top">
              <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold flex-shrink-0">
                  {{ strtoupper(substr($s->name,0,1) ?: '-') }}
                </div>
                <div class="min-w-0">
                  <div class="font-medium truncate">{{ $s->name }}</div>
                  {{-- show email under name on very small screens --}}
                  <div class="text-xs text-gray-500 sm:hidden truncate">{{ $s->email }}</div>
                </div>
              </div>
            </td>

            {{-- email (hidden on xs) --}}
            <td class="p-3 text-sm text-gray-600 hidden sm:table-cell break-words whitespace-normal">{{ $s->email }}</td>

            {{-- phone (hidden on md and below) --}}
            <td class="p-3 text-sm text-gray-600 hidden md:table-cell">{{ $s->phone ?? '-' }}</td>

            {{-- created at (hidden on lg and below) --}}
            <td class="p-3 text-sm text-gray-500 hidden lg:table-cell">{{ $s->created_at->format('Y-m-d') }}</td>

            {{-- actions: small icons --}}
            <td class="p-3">
              <div class="flex items-center gap-2 justify-start sm:justify-end">
                {{-- view --}}
                <a href="{{ route('admin.staff.show', $s->id) }}" class="p-1 rounded hover:bg-gray-100" title="عرض">
                  <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </a>

                {{-- edit --}}
                <a href="{{ route('admin.staff.edit', $s->id) }}" class="p-1 rounded hover:bg-yellow-50" title="تعديل">
                  <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536M4 13.5V18h4.5L17.81 8.69a2 2 0 000-2.828L15.536 3.07a2 2 0 00-2.828 0L4 11.778z"/>
                  </svg>
                </a>

                {{-- delete (form) --}}
                <form action="{{ route('admin.staff.destroy', $s->id) }}" method="POST" onsubmit="return confirm('متأكد من حذف الموظف؟');">
                  @csrf @method('DELETE')
                  <button type="submit" class="p-1 rounded hover:bg-red-50" title="حذف">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                    </svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="p-6 text-center text-gray-500">ما في موظفين</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="p-4">
      {{ $staff->links() }}
    </div>
  </div>
</div>
@endsection
