@extends('layouts.app')

@section('content')
<div class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        <!-- العنوان وزر العودة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <span class="mr-2">👤</span>
                إضافة مريض جديد
            </h1>
            <a href="{{ route('admin.patients.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                العودة للقائمة
            </a>
        </div>

        <!-- نموذج الإضافة -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <form id="createPatientForm" class="space-y-6">

                    @csrf

                    <!-- الحقول الأساسية -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- الكود -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                كود المريض <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                        </div>

                        <!-- الاسم الكامل -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                الاسم الكامل <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                        </div>

                        <!-- الجنس -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الجنس</label>
                            <select name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">غير محدد</option>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                                <option value="other">آخر</option>
                            </select>
                        </div>

                        <!-- تاريخ الميلاد -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الميلاد</label>
                            <input type="date" name="birth_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>

                        <!-- رقم الهاتف -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="+966...">
                        </div>

                        <!-- العنوان -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                            <textarea name="address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" rows="2"></textarea>
                        </div>

                        <!-- الملاحظات -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" rows="2"></textarea>
                        </div>

                    </div>

                    <!-- قسم إنشاء حساب مستخدم -->
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">👤⚙️</span>
                            إنشاء حساب مستخدم (اختياري)
                        </h2>

                        <div class="mb-5">
                            <div class="flex items-start">
                                <input type="checkbox" id="createUser" class="mt-1 h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                                <label for="createUser" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                    إنشاء حساب مستخدم لهذا المريض
                                </label>
                            </div>
                        </div>

                        <!-- حقول الحساب (مخفية افتراضيًا) -->
                        <div id="userFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 d-none">

                            <!-- البريد الإلكتروني -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    البريد الإلكتروني <span class="text-red-600">*</span>
                                </label>
                                <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <!-- كلمة المرور -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    كلمة المرور <span class="text-red-600">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="password" name="password" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <button type="button" id="generatePassword" class="px-4 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                        توليد
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- زر الحفظ -->
                    <div class="pt-6 text-right">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium text-sm rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            حفظ المريض
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('createUser');
    const userFields = document.getElementById('userFields');
    const passwordInput = document.querySelector('input[name="password"]');
    const generateBtn = document.getElementById('generatePassword');

    // Toggle User Fields
    toggle.addEventListener('change', function() {
        if (this.checked) {
            userFields.classList.remove('d-none');
            document.querySelector('input[name="email"]').setAttribute('required', true);
            passwordInput.setAttribute('required', true);
        } else {
            userFields.classList.add('d-none');
            document.querySelector('input[name="email"]').removeAttribute('required');
            passwordInput.removeAttribute('required');
        }
    });

    // Generate Random Password
    generateBtn.addEventListener('click', function() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordInput.value = password;
    });

    // Handle Form Submission
    document.getElementById('createPatientForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        if (!toggle.checked) {
            formData.delete('email');
            formData.delete('password');
        }

        try {
            const response = await fetch('{{ route('admin.patients.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (response.ok) {
                window.location.href = '{{ url('/admin/patients') }}/' + result.id;
            } else {
                alert('حدث خطأ: ' + (result.message || 'يرجى المحاولة لاحقًا'));
            }
        } catch (error) {
            alert('خطأ في الاتصال بالخادم');
        }
    });
});
</script>
@endsection