<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'phone',
        'appointment_fee' => 'float',
        'profit_share' => 'float',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    /**
     * ارجع الفترات الفارغة ليوم معين
     *
     * @param  string|\Carbon\Carbon  $date  (مثال: '2025-10-05' أو Carbon)
     * @param  int  $interval  (بالدقايق) - الافتراضي 30
     * @return \Illuminate\Support\Collection [ ['start'=>'09:00','end'=>'09:30'], ... ]
     */
    public function availableHours($date, $interval = 30)
    {
        $date = \Carbon\Carbon::parse($date);
        $weekday = $date->dayOfWeek; // 0 = Sunday

        // جلب كل شفتات العمل لليوم دا (ممكن يكون في أكثر من شفت)
        $workingHours = $this->workingHours()->where('weekday', $weekday)->get();
        if ($workingHours->isEmpty()) {
            return collect();
        }

        // جلب المواعيد في اليوم ده وتحويلهم لكائنات Carbon
        $appointments = $this->appointments()
            ->whereDate('starts_at', $date->toDateString())
            ->get()
            ->map(function ($a) {
                return [
                    'start' => \Carbon\Carbon::parse($a->starts_at),
                    'end' => \Carbon\Carbon::parse($a->ends_at),
                ];
            });

        $slots = collect();

        foreach ($workingHours as $wh) {
            $shiftStart = \Carbon\Carbon::parse($date->format('Y-m-d').' '.$wh->start_time);
            $shiftEnd = \Carbon\Carbon::parse($date->format('Y-m-d').' '.$wh->end_time);

            $current = $shiftStart->copy();
            while ($current->lt($shiftEnd)) {
                $slotStart = $current->copy();
                $slotEnd = $slotStart->copy()->addMinutes($interval);

                // اذا نهاية السلوط بعد نهاية الشفت نكسر
                if ($slotEnd->gt($shiftEnd)) {
                    break;
                }

                // شرط التداخل الصحيح: slotStart < apptEnd && apptStart < slotEnd
                $overlap = $appointments->first(function ($app) use ($slotStart, $slotEnd) {
                    return $slotStart->lt($app['end']) && $app['start']->lt($slotEnd);
                });

                if (! $overlap) {
                    $slots->push([
                        'start' => $slotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                    ]);
                }

                $current->addMinutes($interval);
            }
        }

        // ازالة التكرارات وترتيب النتيجة
        return $slots
            ->unique(function ($s) {
                return $s['start'].'-'.$s['end'];
            })
            ->sortBy(function ($s) {
                return $s['start'];
            })
            ->values();
    }
}
