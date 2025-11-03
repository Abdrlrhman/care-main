<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // عرض قائمة الموظفين (paginate)
    public function index()
    {
        try {
            $staff = User::role('staff')->paginate(15);
        } catch (\BadMethodCallException $e) {
            // لو ما في spatie أو الميثود مش موجودة
            $staff = User::where('role', 'staff')->paginate(15);
        }

        return view('admin.staff.index', compact('staff'));
    }

    // نموذج إنشاء
    public function create()
    {
        return view('admin.staff.create');
    }

    // خزن موظف جديد واديه دور staff
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($data, &$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('staff');
            }
        });

        return redirect()->route('admin.staff.index')->with('success', 'تم إنشاء الموظف بنجاح');
    }

    // عرض تفاصيل موظف
    public function show(User $staff)
    {
        if (method_exists($staff, 'hasRole') && ! $staff->hasRole('staff')) {
            abort(404);
        }

        return view('admin.staff.show', ['staff' => $staff]);
    }

    // نموذج تعديل
    public function edit(User $staff)
    {
        if (method_exists($staff, 'hasRole') && ! $staff->hasRole('staff')) {
            abort(404);
        }

        return view('admin.staff.edit', compact('staff'));
    }

    // تحديث بيانات الموظف
    public function update(Request $request, User $staff)
    {
        if (method_exists($staff, 'hasRole') && ! $staff->hasRole('staff')) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$staff->id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50',
        ]);

        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->phone = $data['phone'] ?? null;

        if (! empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        $staff->save();

        // تأكّد إنو عندو دور staff
        if (method_exists($staff, 'assignRole') && ! $staff->hasRole('staff')) {
            $staff->assignRole('staff');
        }

        return redirect()->route('admin.staff.index')->with('success', 'تم تحديث بيانات الموظف');
    }

    // حذف الموظف
    public function destroy(User $staff)
    {
        if (method_exists($staff, 'hasRole') && ! $staff->hasRole('staff')) {
            abort(404);
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'تم حذف الموظف');
    }
}
