@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg mx-auto" x-data="appointmentForm()">
    <h2 class="text-xl font-semibold mb-4">حجز مقابلة</h2>

    <template x-if="message">
        <div class="mb-3 p-2 rounded text-sm" :class="messageType == 'error' ? 'bg-red-100' : 'bg-green-100'" x-text="message"></div>
    </template>

    <!-- Step indicators -->
    <div class="flex items-center mb-4 text-xs">
        <div class="flex-1 text-center" :class="step==1 ? 'font-bold' : ''">1. اختر الطبيب</div>
        <div class="flex-1 text-center" :class="step==2 ? 'font-bold' : ''">2. اختار زمن</div>
        <div class="flex-1 text-center" :class="step==3 ? 'font-bold' : ''">3. تأكيد</div>
    </div>

    <!-- Step 1 -->
    <div x-show="step==1" class="space-y-3">
        <div>
            <label class="block text-sm">الطبيب</label>
            <select x-model="doctor_id" class="w-full p-2 border rounded">
                <option value="">اختر الطبيب</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}">{{ $doc->name ?? 'دكتور #'.$doc->id }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm">التاريخ</label>
                <input type="date" x-model="date" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block text-sm">المدة (دق)</label>
                <select x-model="duration" class="w-full p-2 border rounded">
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="45">45</option>
                    <option value="60">60</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button class="flex-1 p-2 rounded bg-blue-600 text-white" @click="fetchSlots()">التالي</button>
        </div>
    </div>

    <!-- Step 2: available slots -->
    <div x-show="step==2" class="space-y-3">
        <div>
            <label class="block text-sm mb-2">الفواصل المتاحة في <span x-text="date"></span></label>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="slot in slots" :key="slot.starts_at">
                    <button type="button" class="p-2 text-sm border rounded text-center" @click="selectSlot(slot)" x-text="slot.label"></button>
                </template>
            </div>
            <div x-show="slots.length == 0" class="text-sm mt-2">مافي فواصل متاحة، جرّب تاريخ تاني.</div>
        </div>

        <div class="flex gap-2">
            <button class="flex-1 p-2 rounded border" @click="step=1">رجوع</button>
            <button class="flex-1 p-2 rounded bg-blue-600 text-white" :class="selectedSlot ? '' : 'opacity-50 cursor-not-allowed'" @click="step=3" :disabled="!selectedSlot">تأكيد الوقت</button>
        </div>
    </div>

    <!-- Step 3: confirmation and submit -->
    <div x-show="step==3" class="space-y-3">
        <div>
            <label class="block text-sm">الوقت المختار</label>
            <div class="p-2 border rounded" x-text="selectedSlot ? selectedSlot.label : ''"></div>
        </div>

        <div>
            <label class="block text-sm">المريض</label>
            <select x-model="patient_id" class="w-full p-2 border rounded">
                <option value="">اختر المريض</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}">{{ $p->name ?? 'مريض #'.$p->id }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm">ملاحظات</label>
            <textarea x-model="notes" class="w-full p-2 border rounded"></textarea>
        </div>

        <form method="POST" action="{{ route('admin.appointments.store_multistep') }}" x-ref="form">
            @csrf
            <input type="hidden" name="doctor_id" :value="doctor_id">
            <input type="hidden" name="patient_id" :value="patient_id">
            <input type="hidden" name="starts_at" :value="selectedSlot ? selectedSlot.starts_at : ''">
            <input type="hidden" name="duration" :value="duration">
            <input type="hidden" name="notes" :value="notes">

            <div class="flex gap-2">
                <button type="button" class="flex-1 p-2 rounded border" @click="step=2">رجوع</button>
                <button type="button" class="flex-1 p-2 rounded bg-green-600 text-white" @click="submit()">احجز</button>
            </div>
        </form>
    </div>
</div>

<script>
function appointmentForm(){
    return {
        step: 1,
        doctor_id: '',
        date: '',
        duration: 30,
        slots: [],
        selectedSlot: null,
        patient_id: '',
        notes: '',
        message: '',
        messageType: '',

        async fetchSlots(){
            if(!this.doctor_id || !this.date || !this.duration) {
                this.message = 'أكمل اختيار الطبيب والتاريخ والمدة.';
                this.messageType = 'error';
                return;
            }
            this.message = '';
            this.slots = [];
            // fetch slots
            try{
                const params = new URLSearchParams({doctor_id: this.doctor_id, date: this.date, duration: this.duration});
                const res = await fetch('/admin/appointments/available-slots?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.slots = json.slots;
                this.step = 2;
            }catch(e){
                this.message = 'حصل خطأ في جلب الفواصل.';
                this.messageType = 'error';
            }
        },

        selectSlot(slot){
            this.selectedSlot = slot;
        },

        submit(){
            if(!this.selectedSlot){
                this.message = 'حدد وقت أول.';
                this.messageType = 'error';
                return;
            }
            if(!this.patient_id){
                this.message = 'حدد المريض.';
                this.messageType = 'error';
                return;
            }
            // fill hidden inputs then submit
            const form = this.$refs.form;
            form.querySelector('input[name="doctor_id"]').value = this.doctor_id;
            form.querySelector('input[name="patient_id"]').value = this.patient_id;
            form.querySelector('input[name="starts_at"]').value = this.selectedSlot.starts_at;
            form.querySelector('input[name="duration"]').value = this.duration;
            form.querySelector('input[name="notes"]').value = this.notes;
            form.submit();
        }
    }
}
</script>
@endsection
