<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()->with('doctor','patient');

        if ($request->filled('role')) {
            $q->role($request->role);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') $q->where('active',1);
            elseif ($request->status === 'inactive') $q->where('active',0);
        }
        if ($request->filled('specialization')) {
            $q->whereHas('doctor', function($qq) use ($request){ $qq->where('specialty', $request->specialization); });
        }

        $users = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        $roles = Role::pluck('name');
        $specializations = Doctor::distinct()->pluck('specialty');

        return view('admin.users.index', compact('users','roles','specializations'));
    }

    public function create()
    {
        $roles = Role::pluck('name');
        $specializations = Doctor::distinct()->pluck('specialty');
        return view('admin.users.create', compact('roles','specializations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => 1,
        ]);

        $user->assignRole($data['role']);

        // if doctor role, create doctor stub
        if ($data['role'] === 'doctor') {
            Doctor::firstOrCreate(['user_id' => $user->id], ['name' => $user->name]);
        }
        if ($data['role'] === 'patient') {
            Patient::firstOrCreate(['user_id' => $user->id], ['name' => $user->name]);
        }

        return redirect()->route('admin.users.index')->with('success','User created.');
    }

    public function show(User $user)
    {
        $roles = Role::pluck('name');
        $specializations = Doctor::distinct()->pluck('specialty');
        $user->load('doctor','patient');
        return view('admin.users.show', compact('user','roles','specializations'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
            'specialty' => 'nullable|string|max:255',
            'role' => 'required|string',
        ]);

        $user->name = $data['name'];
        $user->save();

        // sync role
        $user->syncRoles([$data['role']]);

        // update doctor/patient specifics
        if ($data['role'] === 'doctor') {
            $doctor = $user->doctor ?? new Doctor(['user_id' => $user->id]);
            $doctor->name = $user->name;
            $doctor->phone = $data['phone'] ?? $doctor->phone;
            $doctor->bio = $data['bio'] ?? $doctor->bio;
            $doctor->specialty = $data['specialty'] ?? $doctor->specialty;
            $doctor->save();
        }

        if ($data['role'] === 'patient') {
            $patient = $user->patient ?? new Patient(['user_id' => $user->id]);
            $patient->name = $user->name;
            $patient->phone = $data['phone'] ?? $patient->phone;
            $patient->address = $data['bio'] ?? $patient->address;
            $patient->save();
        }

        return back()->with('success','User updated.');
    }

    public function deactivate(User $user)
    {
        $user->active = 0;
        $user->save();
        return back()->with('success','User deactivated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user->password = Hash::make($data['password']);
        $user->save();
        return back()->with('success','Password reset.');
    }
}
