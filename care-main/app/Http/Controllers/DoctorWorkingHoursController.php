<?php

namespace App\Http\Controllers;

use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorWorkingHoursController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;
        $hours = WorkingHour::where('doctor_id', $doctor->id)->orderBy('weekday')->orderBy('start_time')->get();
        return view('doctor.working_hours.index', compact('hours'));
    }

    /**
     * Show edit UI. If doctor id is provided and current user is admin, allow editing that doctor's hours.
     */
    public function edit($doctorId = null)
    {
        if ($doctorId) {
            // allow only admins to edit someone else's hours
            if (!auth()->user()->hasRole('admin')) {
                abort(403);
            }
            $hours = WorkingHour::where('doctor_id', $doctorId)->orderBy('weekday')->orderBy('start_time')->get();
            $doctor = \App\Models\Doctor::findOrFail($doctorId);
            return view('doctor.working_hours.edit_for_admin', compact('hours','doctor'));
        }

        $doctor = Auth::user()->doctor;
        $hours = WorkingHour::where('doctor_id', $doctor->id)->orderBy('weekday')->orderBy('start_time')->get();
        return view('doctor.working_hours.index', compact('hours'));
    }

    public function store(Request $request, $doctorId = null)
    {
        // determine target doctor: admin may pass doctor id
        if ($doctorId) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403);
            }
            $doctor = \App\Models\Doctor::findOrFail($doctorId);
        } else {
            $doctor = Auth::user()->doctor;
        }
        $periods = $request->input('periods');
        // تحويل periods إلى مصفوفة flat إذا كانت بصيغة periods[day][]
        $flatPeriods = [];
        if (is_array($periods)) {
            foreach ($periods as $day => $items) {
                if (is_array($items['weekday'] ?? null)) {
                    // إذا كانت periods[day][weekday][]
                    for ($i = 0; $i < count($items['weekday']); $i++) {
                        $flatPeriods[] = [
                            'weekday' => $items['weekday'][$i] ?? $day,
                            'start_time' => $items['start_time'][$i] ?? null,
                            'end_time' => $items['end_time'][$i] ?? null,
                        ];
                    }
                } else {
                    // إذا كانت periods[day][weekday]
                    $flatPeriods[] = [
                        'weekday' => $items['weekday'] ?? $day,
                        'start_time' => $items['start_time'] ?? null,
                        'end_time' => $items['end_time'] ?? null,
                    ];
                }
            }
        }

        // تحقق من أن periods ليست فارغة
        if (empty($flatPeriods)) {
            return back()->withErrors(['periods' => 'يجب إضافة فترة واحدة على الأقل.'])->withInput();
        }

        // تحقق من صحة البيانات
        foreach ($flatPeriods as $idx => $int) {
            if (!isset($int['weekday']) || !is_numeric($int['weekday']) || $int['weekday'] < 0 || $int['weekday'] > 6) {
                return back()->withErrors(["periods.$idx.weekday" => "يرجى اختيار يوم صحيح للفترة رقم " . ($idx+1)])->withInput();
            }
            if (!preg_match('/^\d{2}:\d{2}$/', $int['start_time'] ?? '')) {
                return back()->withErrors(["periods.$idx.start_time" => "يرجى إدخال وقت البداية بالتنسيق الصحيح للفترة رقم " . ($idx+1)])->withInput();
            }
            if (!preg_match('/^\d{2}:\d{2}$/', $int['end_time'] ?? '')) {
                return back()->withErrors(["periods.$idx.end_time" => "يرجى إدخال وقت النهاية بالتنسيق الصحيح للفترة رقم " . ($idx+1)])->withInput();
            }
            if (($int['start_time'] ?? '') >= ($int['end_time'] ?? '')) {
                return back()->withErrors(["periods.$idx.end_time" => "يجب أن يكون وقت النهاية بعد وقت البداية للفترة رقم " . ($idx+1)])->withInput();
            }
        }

        // تحقق من التداخل
        $byDay = [];
        foreach($flatPeriods as $int){
            $w = $int['weekday'];
            $s = $int['start_time'];
            $e = $int['end_time'];
            if(!isset($byDay[$w])) $byDay[$w]=[];
            foreach($byDay[$w] as $existing){
                if(!($e <= $existing[0] || $s >= $existing[1])){
                    return back()->with('error', 'فترات متداخلة في اليوم: '.$w);
                }
            }
            $byDay[$w][] = [$s,$e];
        }

        DB::transaction(function() use ($doctor, $flatPeriods){
            WorkingHour::where('doctor_id', $doctor->id)->delete();
            foreach($flatPeriods as $int){
                WorkingHour::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $int['weekday'],
                    'start_time' => $int['start_time'],
                    'end_time' => $int['end_time'],
                ]);
            }
        });

        if (auth()->user()->hasRole('admin') && $doctorId) {
            return redirect()->route('admin.doctors.show', $doctor->id)->with('success','تم حفظ ساعات العمل');
        }

        return redirect()->route('doctor.working_hours.index')->with('success','تم حفظ ساعات العمل');
    }

    public function reset()
    {
        $doctor = Auth::user()->doctor;
        // default: Mon-Fri 09:00-17:00
        WorkingHour::where('doctor_id', $doctor->id)->delete();
        for($d=1;$d<=5;$d++){
            WorkingHour::create(['doctor_id'=>$doctor->id,'weekday'=>$d,'start_time'=>'09:00','end_time'=>'17:00']);
        }
        return redirect()->route('doctor.working_hours.index')->with('success','تم إعادة الضبط');
    }
}
