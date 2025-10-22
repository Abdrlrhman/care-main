@extends('layouts.app')

@section('content')
<div class="container py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        <!-- العنوان -->
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-right">إعدادات النظام</h1>

        @if(session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 text-right" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- نموذج الإعدادات -->
        <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6">

                <!-- القسم العام -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 text-right">
                        <h2 class="font-bold text-lg">الإعدادات العامة</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم العيادة</label>
                            <input type="text" name="clinic_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('clinic_name', $clinic_name) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الشعار</label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="flex-shrink-0">
                                    @if($clinic_logo)
                                        <img src="{{ $clinic_logo }}" alt="شعار العيادة" class="w-24 h-16 object-contain bg-white border border-gray-200 rounded p-1">
                                    @else
                                        <div class="w-24 h-16 border-2 border-dashed border-gray-300 rounded flex items-center justify-center text-xs text-gray-500">
                                            لا يوجد شعار
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file" name="clinic_logo" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <p class="mt-2 text-xs text-gray-500">أقصى حجم 2MB - صيغ PNG/JPG/GIF</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                            <textarea name="clinic_address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" rows="3">{{ old('clinic_address', $clinic_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الهاتف</label>
                                <input type="text" name="clinic_phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('clinic_phone', $clinic_phone) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                <input type="email" name="clinic_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('clinic_email', $clinic_email) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإشعارات -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-4 text-right">
                        <h2 class="font-bold text-lg">الإشعارات</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مزود البريد الإلكتروني (driver)</label>
                            <input type="text" name="notification_email_provider" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('notification_email_provider', $notification_email_provider) }}">
                            <p class="mt-1 text-xs text-gray-500">مثال: smtp, mailgun, ses</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مزود الرسائل النصية</label>
                            <input type="text" name="notification_sms_provider" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('notification_sms_provider', $notification_sms_provider) }}">
                            <p class="mt-1 text-xs text-gray-500">مثال: twilio, nexmo</p>
                        </div>
                    </div>
                </div>

                <!-- الفوترة -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4 text-right">
                        <h2 class="font-bold text-lg">الفوترة</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">معدل الضريبة (%)</label>
                            <input type="number" step="0.01" name="billing_tax_rate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('billing_tax_rate', $billing_tax_rate) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العملة الافتراضية</label>
                            <input type="text" name="billing_currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('billing_currency', $billing_currency) }}">
                            <p class="mt-1 text-xs text-gray-500">رمز ISO مثل: USD, SAR, EGP</p>
                        </div>
                    </div>
                </div>

                <!-- الأدوار والأذونات -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-6 py-4 text-right">
                        <h2 class="font-bold text-lg">الأدوار والأذونات (JSON)</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <textarea name="roles_permissions" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono text-sm" rows="6">{{ old('roles_permissions', $roles_permissions) }}</textarea>
                        <p class="text-xs text-gray-500">هيكل JSON اختياري يربط الوحدات بالأدوار. لتحكم متقدم، استخدم صفحة الأذونات.</p>
                    </div>
                </div>

            </div>

            <!-- أزرار الحفظ وإعادة التعيين -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-all transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    حفظ التغييرات
                </button>
                <a href="{{ route('admin.settings.reset') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg shadow-md transition-all border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                   onclick="event.preventDefault(); if(confirm('هل أنت متأكد أنك تريد إعادة التعيين إلى القيم الافتراضية؟')){ document.getElementById('reset-form').submit(); }">
                    إعادة التعيين
                </a>
            </div>

        </form>

        <!-- نموذج إعادة التعيين الخفي -->
        <form id="reset-form" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">@csrf</form>

    </div>
</div>
@endsection