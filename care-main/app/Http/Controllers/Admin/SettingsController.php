<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $data = [
            'clinic_name' => Setting::get('clinic_name', 'My Clinic'),
            'clinic_logo' => Setting::get('clinic_logo', ''),
            'clinic_address' => Setting::get('clinic_address', ''),
            'clinic_phone' => Setting::get('clinic_phone', ''),
            'clinic_email' => Setting::get('clinic_email', ''),
            'notification_email_provider' => Setting::get('notification_email_provider', ''),
            'notification_sms_provider' => Setting::get('notification_sms_provider', ''),
            'billing_tax_rate' => Setting::get('billing_tax_rate', '0'),
            'billing_currency' => Setting::get('billing_currency', 'USD'),
            'roles_permissions' => Setting::get('roles_permissions', '{}'),
        ];

        return view('admin.settings.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinic_name' => 'nullable|string|max:255',
            'clinic_logo' => 'nullable|image|max:2048',
            'clinic_address' => 'nullable|string',
            'clinic_phone' => 'nullable|string|max:50',
            'clinic_email' => 'nullable|email',
            'notification_email_provider' => 'nullable|string',
            'notification_sms_provider' => 'nullable|string',
            'billing_tax_rate' => 'nullable|numeric',
            'billing_currency' => 'nullable|string|max:10',
            'roles_permissions' => 'nullable|string',
        ]);

        Setting::set('clinic_name', $request->input('clinic_name'));
        Setting::set('clinic_address', $request->input('clinic_address'));
        Setting::set('clinic_phone', $request->input('clinic_phone'));
        Setting::set('clinic_email', $request->input('clinic_email'));
        Setting::set('notification_email_provider', $request->input('notification_email_provider'));
        Setting::set('notification_sms_provider', $request->input('notification_sms_provider'));
        Setting::set('billing_tax_rate', $request->input('billing_tax_rate'));
        Setting::set('billing_currency', $request->input('billing_currency'));
        Setting::set('roles_permissions', $request->input('roles_permissions'));

        if ($request->hasFile('clinic_logo')) {
            $file = $request->file('clinic_logo');
            $path = $file->store('public/settings');
            // store path without public/
            $publicPath = Storage::url($path);
            Setting::set('clinic_logo', $publicPath);
        }

        return redirect()->route('admin.settings.index')->with('status', 'Settings saved.');
    }

    public function resetDefaults()
    {
        // simple defaults; expand as needed
        Setting::set('clinic_name', 'My Clinic');
        Setting::set('clinic_logo', '');
        Setting::set('clinic_address', '');
        Setting::set('clinic_phone', '');
        Setting::set('clinic_email', '');
        Setting::set('notification_email_provider', '');
        Setting::set('notification_sms_provider', '');
        Setting::set('billing_tax_rate', '0');
        Setting::set('billing_currency', 'USD');
        Setting::set('roles_permissions', '{}');

        return redirect()->route('admin.settings.index')->with('status', 'Defaults restored.');
    }
}
