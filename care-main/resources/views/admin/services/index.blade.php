@extends('layouts.app')

@section('content')
<div class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        <!-- العنوان وزر الإضافة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <span class="mr-2">📋</span>
                إدارة الخدمات
            </h2>
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all"
                data-bs-toggle="collapse"
                data-bs-target="#addService"
            >
                <i class="bi bi-plus-circle me-1"></i>
                إضافة خدمة
            </button>
        </div>

        <!-- نموذج إضافة الخدمة -->
        <div id="addService" class="collapse show mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="POST" action="{{ route('admin.services.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الخدمة</label>
                        <input name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="مثال: كشف طبيب أسنان" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">السعر (ر.س)</label>
                        <input name="price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="0.00" required>
                    </div>
                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input name="active" class="form-check-input h-5 w-5 text-blue-600 rounded focus:ring-blue-500" type="checkbox" id="activeDefault" checked>
                            <label class="ml-2 block text-sm text-gray-700" for="activeDefault">مفعل</label>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium text-sm rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- رسالة النجاح -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-lg mb-6 text-right" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- جدول سطح المكتب (مخفى على الهواتف) -->
        <div class="hidden md:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($services as $s)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $s->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">{{ number_format($s->price, 2) }} ر.س</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end">
                                        <input
                                            type="checkbox"
                                            class="service-toggle form-check-input h-5 w-5 text-blue-600 rounded focus:ring-blue-500 cursor-pointer"
                                            data-id="{{ $s->id }}"
                                            {{ $s->active ? 'checked' : '' }}
                                        >
                                        <span class="ml-2 text-sm text-gray-500">{{ $s->active ? 'مفعل' : 'معطل' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button
                                        class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-md hover:bg-yellow-200 mr-2 transition-colors"
                                        onclick="openDesktopEdit({{ $s->id }}, '{{ addslashes($s->name) }}', {{ $s->price }}, {{ $s->active ? 'true' : 'false' }})"
                                    >
                                        <i class="bi bi-pencil me-1"></i> تعديل
                                    </button>
                                    <form method="POST" action="{{ route('admin.services.destroy', $s->id) }}" class="inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-xs rounded-md hover:bg-red-200 transition-colors">
                                            <i class="bi bi-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">
                                    لا توجد خدمات مسجلة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- نموذج التعديل على سطح المكتب (مخفي) -->
        <div id="desktopEditForm" class="hidden mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="POST" action="" id="editServiceForm" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الخدمة</label>
                        <input name="name" id="edit_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="اسم الخدمة" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">السعر (ر.س)</label>
                        <input name="price" id="edit_price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="السعر" required>
                    </div>
                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input name="active" id="edit_active" class="form-check-input h-5 w-5 text-blue-600 rounded focus:ring-blue-500 cursor-pointer" type="checkbox">
                            <label class="ml-2 block text-sm text-gray-700">مفعل</label>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقات الهاتف (مظهر رئيسي على الجوال) -->
        <div class="md:hidden space-y-4">
            @forelse($services as $s)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $s->name }}</h3>
                                <p class="text-green-600 font-bold mt-1">{{ number_format($s->price, 2) }} ر.س</p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center mb-2">
                                    <input
                                        type="checkbox"
                                        class="mobile-toggle form-check-input h-5 w-5 text-blue-600 rounded focus:ring-blue-500 cursor-pointer"
                                        data-id="{{ $s->id }}"
                                        {{ $s->active ? 'checked' : '' }}
                                    >
                                    <span class="ml-2 text-xs text-gray-500">{{ $s->active ? 'مفعل' : 'معطل' }}</span>
                                </div>
                                <button
                                    class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-md hover:bg-gray-200 transition-colors mb-2"
                                    onclick="openMobileEdit({{ $s->id }}, '{{ addslashes($s->name) }}', {{ $s->price }}, {{ $s->active ? 'true' : 'false' }})"
                                >
                                    <i class="bi bi-pencil me-1"></i> تعديل
                                </button>
                                <form method="POST" action="{{ route('admin.services.destroy', $s->id) }}" class="inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-xs rounded-md hover:bg-red-200 transition-colors">
                                        <i class="bi bi-trash me-1"></i> حذف
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- نموذج التعديل على الهاتف -->
                        <div id="mobile-edit-{{ $s->id }}" class="mt-3 p-4 bg-gray-50 rounded-lg hidden">
                            <form method="POST" action="{{ route('admin.services.update', $s->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <input name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $s->name }}" placeholder="اسم الخدمة" required>
                                    </div>
                                    <div>
                                        <input name="price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $s->price }}" placeholder="السعر" required>
                                    </div>
                                </div>
                                <div class="flex items-center mb-3">
                                    <input name="active" class="form-check-input h-5 w-5 text-blue-600 rounded focus:ring-blue-500" type="checkbox" {{ $s->active ? 'checked' : '' }}>
                                    <label class="ml-2 text-sm text-gray-700">مفعل</label>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                    حفظ التغييرات
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <i class="bi bi-list-task text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-lg">لا توجد خدمات</p>
                </div>
            @endforelse
        </div>

        <!-- الترقيم -->
        @if($services->hasPages())
            <div class="mt-8">
                {{ $services->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle Active Status (Desktop & Mobile)
    document.querySelectorAll('.service-toggle, .mobile-toggle').forEach(cb => {
        cb.addEventListener('change', async function() {
            const id = this.dataset.id;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/admin/services/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    this.checked = !this.checked; // Revert if failed
                    alert('فشل في تحديث الحالة');
                }
            } catch (error) {
                this.checked = !this.checked;
                alert('خطأ في الاتصال');
            }
        });
    });

    // Open Desktop Edit (injects form above table)
    function openDesktopEdit(id, name, price, active) {
        const formContainer = document.getElementById('desktopEditForm');
        const form = document.getElementById('editServiceForm');

        // Set form action
        form.action = `/admin/services/${id}`;

        // Fill data
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_active').checked = active;

        // Inject form after table or before pagination
        const table = document.querySelector('.hidden.md\\:block');
        if (table) {
            table.parentNode.insertBefore(formContainer, table.nextSibling);
            formContainer.classList.remove('hidden');
        }
    }

    // Open Mobile Edit
    function openMobileEdit(id, name, price, active) {
        const el = document.getElementById(`mobile-edit-${id}`);
        el.classList.toggle('hidden');
    }
</script>
@endsection