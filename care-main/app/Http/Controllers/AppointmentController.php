<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (method_exists($user, 'patient') && $user->patient) {
            $q = Appointment::where('patient_id', $user->patient->id);
        } else {
            $q = Appointment::with(['patient', 'doctor'])->orderBy('starts_at', 'desc');
            if ($request->filled('doctor_id')) {
                $q->where('doctor_id', $request->doctor_id);
            }
            if ($request->filled('from')) {
                $q->whereDate('starts_at', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $q->whereDate('starts_at', '<=', $request->to);
            }
        }

        $appointments = $q->paginate(20);

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request)
    {
        $doctors = Doctor::orderBy('name')->get();
        $user = auth()->user();

        // لو المستخدم مريض عندو سجل patient => نستخدمو ومانعرض لستة المرضى
        $currentPatient = null;
        if ($user && method_exists($user, 'patient') && $user->patient) {
            $currentPatient = $user->patient;
            $patients = null;
        } else {
            // لو staff أو admin عايزين لستة مرضى لاختيار واحد
            $patients = ($user && ($user->hasRole('admin') || $user->hasRole('staff')))
                ? Patient::orderBy('name')->get()
                : null;
        }

        return view('appointments.create', compact('doctors', 'patients', 'currentPatient'));
    }

    public function slots(Doctor $doctor, Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',        // دلوقتي التاريخ لازم يتبعت
            'days' => 'sometimes|integer|min:1|max:14', // optional: كم يوم تبدأ من التاريخ المرسل
        ]);

        $days = $data['days'] ?? 1;
        $startDate = Carbon::parse($data['date'])->startOfDay();

        $result = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $startDate->copy()->addDays($i);
            $dateString = $day->toDateString();

            // استدعي دالة الموديل مباشرة — interval ثابت 30 دقيقة
            $timeSlots = $doctor->availableHours($dateString, 30); // بترد array of ['start'=>'H:i','end'=>'H:i']

            // حولها لstarts_at / ends_at بصيغة datetime و label
            $mapped = $timeSlots->map(function ($s) use ($dateString) {
                $startsAt = Carbon::parse($dateString.' '.$s['start'])->toDateTimeString();
                $endsAt = Carbon::parse($dateString.' '.$s['end'])->toDateTimeString();

                return [
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'label' => $s['start'].' - '.$s['end'],
                ];
            })->values()->all();

            $result[$dateString] = $mapped;
        }

        return response()->json([
            'doctor' => ['id' => $doctor->id, 'name' => $doctor->name],
            'slots' => $result,
        ]);
    }

    // حفظ الموعد — نفس المنطق لكن سمّيت المتغيرات بوضوح
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'starts_at' => 'required|date',
            'patient_id' => 'nullable|exists:patients,id',
            'notes' => 'nullable|string',
        ]);

        $duration = 30; // نص ساعة ثابت
        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();

        $user = auth()->user();

        // patient determination:
        $patientId = null;
        if ($user && method_exists($user, 'patient') && $user->patient) {
            $patientId = $user->patient->id;
        } elseif (! empty($data['patient_id'])) {
            $patientId = $data['patient_id'];
        } else {
            // staff/admin must provide patient_id
            if ($user && ($user->hasRole('admin') || $user->hasRole('staff'))) {
                $msg = 'يجب تحديد مريض لإتمام الحجز.';
                if ($request->wantsJson()) {
                    return response()->json(['ok' => false, 'error' => $msg], 422);
                }

                return back()->withInput()->withErrors(['patient_id' => $msg]);
            }
            // otherwise error
            $msg = 'المريض غير محدد.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }

            return back()->withInput()->withErrors(['patient_id' => $msg]);
        }

        try {
            $startsAt = Carbon::parse($data['starts_at']);
        } catch (\Exception $e) {
            $msg = 'صيغة التاريخ غير صحيحة.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }

            return back()->withInput()->withErrors(['starts_at' => $msg]);
        }
        $endsAt = $startsAt->copy()->addMinutes($duration);

        // working hours check
        $weekday = $startsAt->dayOfWeek;
        $working = WorkingHour::where('doctor_id', $data['doctor_id'])->where('weekday', $weekday)->get();
        if ($working->isEmpty()) {
            $msg = 'الطبيب غير متاح في اليوم دا.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }

            return back()->withInput()->withErrors(['starts_at' => $msg]);
        }

        // check that appointment inside at least one working shift
        $insideShift = false;
        foreach ($working as $wh) {
            $format = strlen($wh->start_time) === 5 ? 'H:i' : 'H:i:s';
            $ws = Carbon::createFromFormat($format, $wh->start_time)->setDate($startsAt->year, $startsAt->month, $startsAt->day);
            $we = Carbon::createFromFormat($format, $wh->end_time)->setDate($startsAt->year, $startsAt->month, $startsAt->day);
            if ($we->lessThanOrEqualTo($ws)) {
                $we->addDay();
            }

            if ($startsAt->greaterThanOrEqualTo($ws) && $endsAt->lessThanOrEqualTo($we)) {
                $insideShift = true;
                break;
            }
        }
        if (! $insideShift) {
            $msg = 'الوقت خارج ساعات العمل للطبيب.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }

            return back()->withInput()->withErrors(['starts_at' => $msg]);
        }

        // create appointment with DB lock to avoid race
        try {
            $appointment = DB::transaction(function () use ($data, $startsAt, $endsAt, $patientId) {
                $conflict = Appointment::where('doctor_id', $data['doctor_id'])
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($startsAt, $endsAt) {
                        $q->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt);
                    })->lockForUpdate()->exists();

                if ($conflict) {
                    throw new \RuntimeException('conflict');
                }

                return Appointment::create([
                    'doctor_id' => $data['doctor_id'],
                    'patient_id' => $patientId,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => 'scheduled',
                    'notes' => $data['notes'] ?? null,
                ]);
            }, 5);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'conflict') {
                $msg = 'الوقت دا اتاخد. اختار وقت تاني.';
                if ($request->wantsJson()) {
                    return response()->json(['ok' => false, 'error' => $msg], 409);
                }

                return back()->withInput()->withErrors(['starts_at' => $msg]);
            }
            Log::error('Appointment creation failed (runtime)', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            $msg = 'فشل إنشاء الموعد.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 500);
            }

            return back()->withInput()->withErrors(['error' => $msg]);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating appointment', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            $msg = 'حصل خطأ غير متوقع.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => $msg], 500);
            }

            return back()->withInput()->withErrors(['error' => $msg]);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'appointment' => $appointment], 201);
        }
        // إنشاء فاتورة تلقائياً للمقابلة الجديدة
        Invoice::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'total' => $appointment->doctor->appointment_fee,
            'net_total' => $appointment->doctor->appointment_fee - ($appointment->doctor->appointment_fee * $appointment->doctor->profit_share / 100),
            'status' => 'unpaid',
        ]);

        return redirect()->route('appointments.create')->with('success', 'تم حجز الموعد.');
    }

    public function edit(Appointment $appointment)
    {
        $user = auth()->user();

        // السماح فقط للـ admin و staff
        if (! $user->hasRole('admin') && ! $user->hasRole('staff')) {
            abort(403, 'غير مسموح');
        }

        $doctors = Doctor::orderBy('name')->get();
        $patients = Patient::orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        if (! $user->hasRole('admin') && ! $user->hasRole('staff')) {
            abort(403, 'غير مسموح');
        }

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'starts_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $duration = 30;
        $startsAt = \Carbon\Carbon::parse($data['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($duration);

        $appointment->update([
            'doctor_id' => $data['doctor_id'],
            'patient_id' => $data['patient_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')->with('success', 'تم تعديل الموعد بنجاح.');
    }

    // باقي الدوال (index, cancel) زي ما كانت لكن اتأكد انها موجودة

    public function cancel(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        if (method_exists($user, 'patient') && $user->patient) {
            if ($appointment->patient_id !== $user->patient->id) {
                return response()->json(['ok' => false, 'error' => 'غير مخول'], 403);
            }
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json(['ok' => true, 'message' => 'تم إلغاء الموعد']);
    }

    public function destroy(Appointment $appointment)
    {
        $user = auth()->user();
        if (! $user->hasRole('admin') && ! $user->hasRole('staff')) {
            abort(403, 'غير مسموح');
        }

        // نحذف الفاتورة المرتبطة لو موجودة
        if ($appointment->invoice) {
            $appointment->invoice->delete();
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'تم حذف الموعد والفاتورة المرتبطة به.');
    }
}
