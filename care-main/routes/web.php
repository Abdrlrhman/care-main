<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
    
// مسار API للمواعيد متاح للواجهة فقط بدون تحقق صلاحيات
Route::get('/admin/appointments/api', [App\Http\Controllers\Admin\AppointmentsController::class, 'apiList']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin_dashboard');
    })->name('admin.dashboard');

    Route::get('/admin', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.index');
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // 1. تسجيل موارد الطبيب مع استثناء المسار 'index' لأنه سيُستخدم من كونترولر آخر
Route::resource('/admin/doctors', App\Http\Controllers\DoctorController::class)
    ->names([
        'create' => 'admin.doctors.create',
        'store' => 'admin.doctors.store',
        'edit' => 'admin.doctors.edit',
        'update' => 'admin.doctors.update',
        'destroy' => 'admin.doctors.destroy',
        'show' => 'admin.doctors.show',
    ])
    ->parameters(['doctors' => 'doctor'])
    ->except(['index']); // 👈 استثناء المسار index حتى لا يتعارض

// 2. تسجيل مسار index بشكل منفصل مع الكونترولر الجديد
Route::get('/admin/doctors', [App\Http\Controllers\Admin\DoctorsDirectoryController::class, 'index'])
    ->name('admin.doctors.index');

    
    Route::resource('/admin/appointments', App\Http\Controllers\AppointmentController::class)->names([
        'index' => 'admin.appointments.index',
        'create' => 'admin.appointments.create',
        'store' => 'admin.appointments.store',
        'edit' => 'admin.appointments.edit',
        'update' => 'admin.appointments.update',
        'destroy' => 'admin.appointments.destroy',
    ])->parameters(['appointments' => 'appointment']);
    // Appointments management UI
    Route::get('/admin/appointments/management', [App\Http\Controllers\Admin\AppointmentsController::class, 'index'])->name('admin.appointments.management');
    Route::get('/admin/appointments/api', [App\Http\Controllers\Admin\AppointmentsController::class, 'apiList']);
    Route::post('/admin/appointments/{appointment}/cancel', [App\Http\Controllers\Admin\AppointmentsController::class, 'cancel']);
    Route::post('/admin/appointments/{appointment}/reassign', [App\Http\Controllers\Admin\AppointmentsController::class, 'reassign']);
    // start visit from an appointment
    Route::get('/admin/appointments/{appointment}/start', [App\Http\Controllers\AppointmentController::class, 'start'])->name('admin.appointments.start');
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');
    Route::get('/admin/permissions', function () {
        return view('admin.permissions');
    })->name('admin.permissions');
    // patients
    Route::get('/admin/patients/create', [App\Http\Controllers\PatientsController::class, 'create'])->name('admin.patients.create');
    Route::post('/admin/patients', [App\Http\Controllers\PatientsController::class, 'store'])->name('admin.patients.store');
    // Admin Patients Directory
    Route::get('/admin/patients', [App\Http\Controllers\Admin\PatientsDirectoryController::class, 'index'])->name('admin.patients.index');
    Route::post('/admin/patients/{patient}/deactivate', [App\Http\Controllers\Admin\PatientsDirectoryController::class, 'deactivate'])->name('admin.patients.deactivate');
    Route::get('/admin/patients/{patient}', function($id){
        // minimal show page placeholder; can be expanded to a full profile
        $patient = App\Models\Patient::with('medicalRecords')->findOrFail($id);
        return view('admin.patients.show', compact('patient'));
    })->name('admin.patients.show');
    Route::post('/admin/medical-records', [App\Http\Controllers\MedicalRecordsController::class, 'store'])->name('admin.medical_records.store');
    Route::post('/admin/medical-records/{medicalRecord}/attachments', [App\Http\Controllers\MedicalRecordsController::class, 'uploadAttachment'])->name('admin.medical_records.attachments');
    // Medical record editor
    Route::get('/admin/medical-records/create', [App\Http\Controllers\MedicalRecordEditorController::class, 'create'])->name('admin.medical_records.create');
    Route::get('/admin/medical-records/{medicalRecord}/edit', [App\Http\Controllers\MedicalRecordEditorController::class, 'edit'])->name('admin.medical_records.edit');
    Route::post('/admin/medical-records/save', [App\Http\Controllers\MedicalRecordEditorController::class, 'store'])->name('admin.medical_records.save');
    Route::put('/admin/medical-records/{medicalRecord}', [App\Http\Controllers\MedicalRecordEditorController::class, 'update'])->name('admin.medical_records.update');
    // user management
    Route::get('/admin/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\Admin\UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/{user}/deactivate', [App\Http\Controllers\Admin\UsersController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::post('/admin/users/{user}/reset-password', [App\Http\Controllers\Admin\UsersController::class, 'resetPassword'])->name('admin.users.reset_password');
    Route::put('/admin/users/{user}', [App\Http\Controllers\Admin\UsersController::class, 'update'])->name('admin.users.update');
    // Doctors Directory (admin)
    Route::get('/admin/doctors/directory', [App\Http\Controllers\Admin\DoctorsDirectoryController::class, 'index'])->name('admin.doctors.directory');

    // Allow admins to edit a doctor's working hours via the doctor's working hours controller
    Route::get('/admin/doctors/{doctor}/working-hours/edit', [App\Http\Controllers\DoctorWorkingHoursController::class, 'edit'])->name('admin.doctors.working-hours.edit');
    Route::post('/admin/doctors/{doctor}/working-hours', [App\Http\Controllers\DoctorWorkingHoursController::class, 'store'])->name('admin.doctors.working-hours.store');
    // Services management
    Route::resource('/admin/services', App\Http\Controllers\Admin\ServicesController::class)->names([
        'index' => 'admin.services.index',
        'store' => 'admin.services.store',
        'update' => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ])->parameters(['services' => 'service']);
    Route::post('/admin/services/{service}/toggle', [App\Http\Controllers\Admin\ServicesController::class, 'toggleActive'])->name('admin.services.toggle');
    // Admin invoices & payments
    Route::get('/admin/invoices', [App\Http\Controllers\Admin\InvoicesController::class, 'index'])->name('admin.invoices.index');
    Route::get('/admin/invoices/{invoice}', [App\Http\Controllers\Admin\InvoicesController::class, 'show'])->name('admin.invoices.show');
    Route::post('/admin/invoices/{invoice}/payments', [App\Http\Controllers\Admin\InvoicesController::class, 'addPayment'])->name('admin.invoices.add_payment');
    Route::post('/admin/invoices/{invoice}/cancel', [App\Http\Controllers\Admin\InvoicesController::class, 'cancel'])->name('admin.invoices.cancel');

    // Reports & Analytics
    Route::get('/admin/reports', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/revenue', [App\Http\Controllers\Admin\ReportsController::class, 'revenueByMonth']);
    Route::get('/admin/reports/appointments-specialty', [App\Http\Controllers\Admin\ReportsController::class, 'appointmentsBySpecialization']);
    Route::get('/admin/reports/services-usage', [App\Http\Controllers\Admin\ReportsController::class, 'servicesUsage']);
    Route::get('/admin/reports/export/{report}', [App\Http\Controllers\Admin\ReportsController::class, 'exportCsv']);

    // System Settings
    Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('admin.settings.store');
    Route::post('/admin/settings/reset', [App\Http\Controllers\Admin\SettingsController::class, 'resetDefaults'])->name('admin.settings.reset');
});






// doctor routes (role:doctor)
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [App\Http\Controllers\DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    // doctor calendar
    Route::get('/doctor/calendar', [App\Http\Controllers\DoctorCalendarController::class, 'index'])->name('doctor.calendar');
    Route::get('/doctor/calendar/appointments', [App\Http\Controllers\DoctorCalendarController::class, 'apiAppointments']);
    Route::get('/doctor/calendar/patients', [App\Http\Controllers\DoctorCalendarController::class, 'apiPatients']);
    Route::post('/doctor/calendar/appointments', [App\Http\Controllers\DoctorCalendarController::class, 'apiStore']);
    Route::put('/doctor/calendar/appointments/{appointment}', [App\Http\Controllers\DoctorCalendarController::class, 'apiUpdate']);
    Route::delete('/doctor/calendar/appointments/{appointment}', [App\Http\Controllers\DoctorCalendarController::class, 'apiDestroy']);
    Route::get('/doctor/calendar/working-hours', [App\Http\Controllers\DoctorCalendarController::class, 'apiWorkingHours']);
    // doctor medical records
    Route::get('/doctor/medical-records', [App\Http\Controllers\MedicalRecordsController::class, 'index'])->name('doctor.medical_records.index');
    Route::get('/doctor/medical-records/export', [App\Http\Controllers\MedicalRecordsController::class, 'exportCsv'])->name('doctor.medical_records.export');
    Route::get('/doctor/prescriptions', [App\Http\Controllers\PrescriptionsController::class, 'index'])->name('doctor.prescriptions.index');
    Route::get('/doctor/services', [App\Http\Controllers\DoctorServicesController::class, 'index'])->name('doctor.services.index');
    // doctor profile & preferences
    Route::get('/doctor/profile', [App\Http\Controllers\DoctorProfileController::class, 'edit'])->name('doctor.profile.edit');
    Route::put('/doctor/profile', [App\Http\Controllers\DoctorProfileController::class, 'update'])->name('doctor.profile.update');
    // working hours
    Route::get('/doctor/working-hours', [App\Http\Controllers\DoctorWorkingHoursController::class, 'index'])->name('doctor.working_hours.index');
    Route::post('/doctor/working-hours', [App\Http\Controllers\DoctorWorkingHoursController::class, 'store'])->name('doctor.working_hours.store');
    Route::post('/doctor/working-hours/reset', [App\Http\Controllers\DoctorWorkingHoursController::class, 'reset'])->name('doctor.working_hours.reset');

    // invoices
    Route::get('/doctor/invoices', [App\Http\Controllers\InvoicesController::class, 'index'])->name('doctor.invoices.index');
    Route::get('/doctor/invoices/create', [App\Http\Controllers\InvoicesController::class, 'create'])->name('doctor.invoices.create');
    Route::post('/doctor/invoices', [App\Http\Controllers\InvoicesController::class, 'store'])->name('doctor.invoices.store');
    Route::get('/doctor/invoices/{invoice}/edit', [App\Http\Controllers\InvoicesController::class, 'edit'])->name('doctor.invoices.edit');
    Route::put('/doctor/invoices/{invoice}', [App\Http\Controllers\InvoicesController::class, 'update'])->name('doctor.invoices.update');
    Route::get('/doctor/invoices/{invoice}', [App\Http\Controllers\InvoicesController::class, 'show'])->name('doctor.invoices.show');
    Route::post('/doctor/invoices/{invoice}/payments', [App\Http\Controllers\InvoicesController::class, 'recordPayment'])->name('doctor.invoices.payments');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', function () {
        return view('staff_dashboard');
    })->name('staff.dashboard');

    // Staff dashboard
    Route::get('/staff/dashboard', [App\Http\Controllers\Staff\StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::post('/staff/appointments/{appointment}/checkin', [App\Http\Controllers\Staff\StaffDashboardController::class, 'checkIn'])->name('staff.appointments.checkin');
    Route::post('/staff/appointments/{appointment}/cancel', [App\Http\Controllers\Staff\StaffDashboardController::class, 'cancel'])->name('staff.appointments.cancel');
});

// Staff appointment management routes (role:staff)
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/appointments', [App\Http\Controllers\Staff\AppointmentController::class, 'index'])->name('staff.appointments.index');
    Route::post('/staff/appointments', [App\Http\Controllers\Staff\AppointmentController::class, 'store'])->name('staff.appointments.store');
    Route::get('/staff/appointments/{appointment}/edit', [App\Http\Controllers\Staff\AppointmentController::class, 'edit'])->name('staff.appointments.edit');
    Route::put('/staff/appointments/{appointment}', [App\Http\Controllers\Staff\AppointmentController::class, 'update'])->name('staff.appointments.update');
    Route::delete('/staff/appointments/{appointment}', [App\Http\Controllers\Staff\AppointmentController::class, 'destroy'])->name('staff.appointments.destroy');

    // Staff patient management
    Route::get('/staff/patients', [App\Http\Controllers\Staff\PatientController::class, 'index'])->name('staff.patients.index');
    Route::post('/staff/patients', [App\Http\Controllers\Staff\PatientController::class, 'store'])->name('staff.patients.store');
    Route::get('/staff/patients/{patient}/edit', [App\Http\Controllers\Staff\PatientController::class, 'edit'])->name('staff.patients.edit');
    Route::put('/staff/patients/{patient}', [App\Http\Controllers\Staff\PatientController::class, 'update'])->name('staff.patients.update');
    Route::get('/staff/patients/{patient}', [App\Http\Controllers\Staff\PatientController::class, 'show'])->name('staff.patients.show');

    // Staff invoices
    Route::get('/staff/invoices', [App\Http\Controllers\Staff\InvoiceController::class, 'index'])->name('staff.invoices.index');
    Route::get('/staff/invoices/create', [App\Http\Controllers\Staff\InvoiceController::class, 'create'])->name('staff.invoices.create');
    Route::post('/staff/invoices', [App\Http\Controllers\Staff\InvoiceController::class, 'store'])->name('staff.invoices.store');
    Route::get('/staff/invoices/{invoice}/edit', [App\Http\Controllers\Staff\InvoiceController::class, 'edit'])->name('staff.invoices.edit');
    Route::put('/staff/invoices/{invoice}', [App\Http\Controllers\Staff\InvoiceController::class, 'update'])->name('staff.invoices.update');
    Route::get('/staff/invoices/{invoice}', [App\Http\Controllers\Staff\InvoiceController::class, 'show'])->name('staff.invoices.show');
    Route::post('/staff/invoices/{invoice}/mark-paid', [App\Http\Controllers\Staff\InvoiceController::class, 'markPaid'])->name('staff.invoices.markPaid');
    Route::post('/staff/invoices/{invoice}/payments', [App\Http\Controllers\Staff\PaymentController::class, 'store'])->name('staff.invoices.payments');
    // Services lookup (read-only) for staff
    Route::get('/staff/services', [App\Http\Controllers\Staff\ServicesController::class, 'index'])->name('staff.services.index');
    // Staff reports
    Route::get('/staff/reports/daily', [App\Http\Controllers\Staff\ReportsController::class, 'daily'])->name('staff.reports.daily');
    Route::get('/staff/reports/export', [App\Http\Controllers\Staff\ReportsController::class, 'exportCsv'])->name('staff.reports.export');
});

// patient routes (authenticated users with patient profile)
Route::middleware(['auth'])->group(function () {
    Route::get('/patient/dashboard', [App\Http\Controllers\PatientDashboardController::class, 'index'])->name('patient.dashboard');

    Route::get('/appointments/book', [App\Http\Controllers\BookAppointmentController::class, 'index'])->name('appointments.book');
    Route::get('/appointments/api/doctors', [App\Http\Controllers\BookAppointmentController::class, 'apiDoctors']);
    Route::get('/appointments/api/slots', [App\Http\Controllers\BookAppointmentController::class, 'apiSlots']);
    Route::post('/appointments/book', [App\Http\Controllers\BookAppointmentController::class, 'store'])->name('appointments.store');
    // patient appointments
    Route::get('/patient/appointments', [App\Http\Controllers\PatientAppointmentsController::class, 'index'])->name('patient.appointments.index');
    Route::post('/patient/appointments/{appointment}/cancel', [App\Http\Controllers\PatientAppointmentsController::class, 'cancel'])->name('patient.appointments.cancel');
    // patient medical records
    Route::get('/patient/medical-records', [App\Http\Controllers\PatientMedicalRecordsController::class, 'index'])->name('patient.medical_records.index');
    Route::get('/patient/medical-records/{medicalRecord}', [App\Http\Controllers\PatientMedicalRecordsController::class, 'show'])->name('patient.medical_records.show');
    // patient prescriptions
    Route::get('/patient/prescriptions', [App\Http\Controllers\PatientPrescriptionsController::class, 'index'])->name('patient.prescriptions.index');
    // patient invoices
    Route::get('/patient/invoices', [App\Http\Controllers\PatientInvoicesController::class, 'index'])->name('patient.invoices.index');
    Route::get('/patient/invoices/{invoice}', [App\Http\Controllers\PatientInvoicesController::class, 'show'])->name('patient.invoices.show');
    Route::post('/patient/invoices/{invoice}/payments', [App\Http\Controllers\PatientInvoicesController::class, 'recordPayment'])->name('patient.invoices.payments');
    // patient services
    Route::get('/patient/services', [App\Http\Controllers\PatientServicesController::class, 'index'])->name('patient.services.index');
    // patient profile & settings
    Route::get('/patient/profile', [App\Http\Controllers\PatientProfileController::class, 'edit'])->name('patient.profile.edit');
    Route::put('/patient/profile', [App\Http\Controllers\PatientProfileController::class, 'update'])->name('patient.profile.update');
});



require __DIR__.'/auth.php';
  