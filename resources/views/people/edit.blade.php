@extends('layouts.app')
@php
$client_schedules = $data['person_connected_schedule_department']['client_schedules'] ?? null;
$departments = $data['person_connected_schedule_department']['department'] ?? null;
$person = $data['person_connected_schedule_department']['person'] ?? null;

$currentTrainerId = old('trainer_id') ?? ($person->trainer_id ?? null);
$currentScheduleId = old('schedule_name_id') ?? optional($person->schedule_department_people->first())->schedule_name_id;
$currentDurationId = old('session_duration_id') ?? optional($person->schedule_department_people->first())->session_duration_id;

// ✅ bookings (multi)
$bookings = $data['bookings'] ?? collect();

// ✅ initial weekly json (edit defaults)
$initialWeeklyJson = old('weekly_slots_json');
if (!$initialWeeklyJson) {
$initialWeeklyJson = $bookings->map(fn($b) => [
'week_day' => $b->day,
'start_time' => \Illuminate\Support\Str::of($b->start_time)->substr(0,5),
'end_time' => \Illuminate\Support\Str::of($b->end_time)->substr(0,5),
])->values()->toJson();
}
@endphp
@section("page-script")
<script src="{{ asset('assets/js/change-person-permission-entry-code.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("visitorEditForm");
        const weeklyJsonInput = document.getElementById("weeklySlotsJson");

        const trainerSelect = document.getElementById("trainerSelect");
        const scheduleRow = document.getElementById("trainerScheduleRow");
        const scheduleSelect = document.getElementById("trainerScheduleSelect");
        const durationRow = document.getElementById("trainerDurationRow");
        const durationSelect = document.getElementById("trainerDurationSelect");

        const multiRow = document.getElementById("multiDaysRow");
        const multiWrap = document.getElementById("multiDaysWrap");

        if (!trainerSelect) return;

        const DAY_ORDER = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        let saved = [];
        try {
            saved = JSON.parse(weeklyJsonInput?.value || "[]");
        } catch (e) {
            saved = [];
        }

        const currentScheduleId = @json($currentScheduleId);
        const currentDurationId = @json($currentDurationId);

        function timeToMinutes(t) {
            const [h, m] = String(t).split(":").map(Number);
            return h * 60 + m;
        }

        function minutesToTime(min) {
            const h = String(Math.floor(min / 60)).padStart(2, "0");
            const m = String(min % 60).padStart(2, "0");
            return `${h}:${m}`;
        }

        function getTrainerSchedules() {
            const opt = trainerSelect.selectedOptions?.[0];
            const json = opt?.getAttribute("data-schedules");
            try {
                return json ? JSON.parse(json) : [];
            } catch (e) {
                return [];
            }
        }

        function getSelectedScheduleDetails() {
            const scheduleId = scheduleSelect?.value;
            if (!scheduleId) return [];
            const schedules = getTrainerSchedules();
            const s = schedules.find(x => String(x.id) === String(scheduleId));
            return s?.details || [];
        }

        function getDurationMinutes() {
            const durationId = durationSelect?.value;
            if (!durationId) return 0;

            const opt = trainerSelect.selectedOptions?.[0];
            const json = opt?.getAttribute("data-durations");
            let durations = [];
            try {
                durations = json ? JSON.parse(json) : [];
            } catch (e) {
                durations = [];
            }
            const d = durations.find(x => String(x.id) === String(durationId));
            return Number(d?.minutes || 0);
        }

        function buildSlotsForDetail(detail, durationMinutes) {
            const slots = [];
            const DAY = 24 * 60;

            let start = timeToMinutes(detail.day_start_time);
            let end = timeToMinutes(detail.day_end_time);
            if (end <= start) end += DAY;

            let breakStart = null,
                breakEnd = null;
            if (detail.break_start_time && detail.break_end_time) {
                breakStart = timeToMinutes(detail.break_start_time);
                breakEnd = timeToMinutes(detail.break_end_time);
                if (breakEnd <= breakStart) breakEnd += DAY;
                if (end > DAY && breakStart < start) {
                    breakStart += DAY;
                    breakEnd += DAY;
                }
            }

            for (let t = start; t + durationMinutes <= end; t += durationMinutes) {
                const slotStart = t,
                    slotEnd = t + durationMinutes;
                const overlaps = (breakStart != null && breakEnd != null) ? !(slotEnd <= breakStart || slotStart >= breakEnd) : false;
                if (!overlaps) {
                    const s = minutesToTime(slotStart % DAY),
                        e = minutesToTime(slotEnd % DAY);
                    slots.push({
                        start: s,
                        end: e,
                        label: `${s} - ${e}`
                    });
                }
            }
            return slots;
        }

        function updateWeeklyJson() {
            const result = [];
            const blocks = multiWrap?.querySelectorAll("[data-day-block='1']") || [];
            blocks.forEach(block => {
                const day = block.getAttribute("data-day");
                const check = block.querySelector(".dayCheck");
                const sel = block.querySelector(".dayTimeSelect");
                if (!day || !check?.checked) return;

                const start = sel?.value || "";
                const end = sel?.selectedOptions?.[0]?.getAttribute("data-end") || "";
                if (start && end) result.push({
                    week_day: day,
                    start_time: start,
                    end_time: end
                });
            });
            weeklyJsonInput.value = JSON.stringify(result);
        }

        function clearMulti() {
            if (multiWrap) multiWrap.innerHTML = "";
            if (multiRow) multiRow.classList.add("d-none");
        }

        function renderMultiDays() {
            clearMulti();

            const minutes = getDurationMinutes();
            const details = getSelectedScheduleDetails();
            if (!trainerSelect.value || !scheduleSelect.value || !minutes || !details.length) return;

            const byDay = {};
            details.forEach(d => {
                (byDay[d.week_day] ||= []).push(d);
            });

            DAY_ORDER.forEach(day => {
                const arr = byDay[day];
                if (!arr?.length) return;

                const block = document.createElement("div");
                block.className = "border rounded p-2 mb-2";
                block.setAttribute("data-day-block", "1");
                block.setAttribute("data-day", day);

                block.innerHTML = `
        <div class="form-check">
          <input class="form-check-input dayCheck" type="checkbox" id="chk_${day}">
          <label class="form-check-label fw-semibold" for="chk_${day}">${day}</label>
        </div>
        <div class="mt-2 d-none daySlots">
          <select class="form-select dayTimeSelect">
            <option value="" disabled selected>Ընտրել ժամ</option>
          </select>
        </div>
      `;
                multiWrap.appendChild(block);

                const check = block.querySelector(".dayCheck");
                const slotsBox = block.querySelector(".daySlots");
                const sel = block.querySelector(".dayTimeSelect");

                // slots
                const seen = new Set();
                arr.forEach(detail => {
                    buildSlotsForDetail(detail, minutes).forEach(s => {
                        const key = `${s.start}-${s.end}`;
                        if (seen.has(key)) return;
                        seen.add(key);

                        const o = document.createElement("option");
                        o.value = s.start;
                        o.textContent = s.label;
                        o.setAttribute("data-end", s.end);
                        sel.appendChild(o);
                    });
                });

                // restore saved
                const savedForDay = saved.find(x => String(x.week_day) === String(day));
                if (savedForDay) {
                    check.checked = true;
                    slotsBox.classList.remove("d-none");
                    const start = String(savedForDay.start_time || "").slice(0, 5);
                    if (start) sel.value = start;
                }

                check.addEventListener("change", () => {
                    if (check.checked) slotsBox.classList.remove("d-none");
                    else {
                        slotsBox.classList.add("d-none");
                        sel.value = "";
                    }
                    updateWeeklyJson();
                });

                sel.addEventListener("change", updateWeeklyJson);
            });

            multiRow.classList.remove("d-none");
            updateWeeklyJson();
        }

        function fillSchedulesAndDurations(keepSelected = true) {
            const opt = trainerSelect.selectedOptions?.[0];
            const schedulesJson = opt?.getAttribute("data-schedules");
            const durationsJson = opt?.getAttribute("data-durations");

            // schedules
            scheduleSelect.innerHTML = `<option value="" disabled selected>Ընտրել հերթափոխը</option>`;
            let schedules = [];
            try {
                schedules = schedulesJson ? JSON.parse(schedulesJson) : [];
            } catch (e) {
                schedules = [];
            }

            if (!trainerSelect.value || schedules.length === 0) {
                scheduleRow.classList.add("d-none");
            } else {
                schedules.forEach(s => {
                    const o = document.createElement("option");
                    o.value = s.id;
                    o.textContent = s.name;
                    scheduleSelect.appendChild(o);
                });
                scheduleRow.classList.remove("d-none");
                if (keepSelected && currentScheduleId) scheduleSelect.value = String(currentScheduleId);
            }

            // durations
            durationSelect.innerHTML = `<option value="" disabled selected>Ընտրել պարապմունքը</option>`;
            let durations = [];
            try {
                durations = durationsJson ? JSON.parse(durationsJson) : [];
            } catch (e) {
                durations = [];
            }

            if (!trainerSelect.value || durations.length === 0) {
                durationRow.classList.add("d-none");
            } else {
                durations.forEach(d => {
                    const o = document.createElement("option");
                    o.value = d.id;
                    const title = d.title ? ` — ${d.title}` : "";
                    o.textContent = `${d.minutes} րոպե${title} — ${d.price_amd} դրամ`;
                    durationSelect.appendChild(o);
                });
                durationRow.classList.remove("d-none");
                if (keepSelected && currentDurationId) durationSelect.value = String(currentDurationId);
            }

            renderMultiDays();
        }

        trainerSelect.addEventListener("change", () => {
            // reset saved when trainer changes
            saved = [];
            weeklyJsonInput.value = "[]";
            fillSchedulesAndDurations(false);
        });

        scheduleSelect.addEventListener("change", renderMultiDays);
        durationSelect.addEventListener("change", renderMultiDays);

        form?.addEventListener("submit", () => updateWeeklyJson());

        // init
        fillSchedulesAndDurations(true);
    });
</script>
@endsection





@section('content')
<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">

                    <div class="card-body">
                        @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('visitors.list') }}">Այցելուների ցանկ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Խմբագրել</li>
                                </ol>
                            </nav>
                        </h5>

                        <form action="{{ route('visitors.update', $person->id) }}" method="post" enctype="multipart/form-data" id="visitorEditForm">
                            @csrf
                            @method('put')

                            {{-- ✅ MULTI booking json --}}
                            <input type="hidden" name="weekly_slots_json" id="weeklySlotsJson" value="{{ $initialWeeklyJson }}">

                            {{-- Entry code --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Նույնականացման կոդ</label>
                                <div class="col-sm-9">
                                    @if ($data['non_active_entry_code']==false && $person->activated_code_connected_person!=null)
                                    <input type="text" class="form-control" disabled
                                        value="{{ $person->activated_code_connected_person->entry_code->token }}">
                                    @endif

                                    @if ($data['non_active_entry_code']!=false)
                                    <select class="form-select" name="entry_code_id" id="entryCodeNumber" data-person-id="{{ $person->id }}">
                                        <option value="" disabled>Ընտրել նույնականացման կոդը</option>

                                        @foreach ($data['non_active_entry_code'] as $code)
                                        <option value="{{ $code->id }}">{{ $code->token }}</option>
                                        @endforeach

                                        @if ($person->activated_code_connected_person != null)
                                        <option class="active"
                                            value="{{ $person->activated_code_connected_person->entry_code_id }}"
                                            selected>
                                            {{ $person->activated_code_connected_person->entry_code->token }}
                                        </option>
                                        @endif
                                    </select>
                                    @endif
                                </div>
                            </div>

                            {{-- Name --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Աշխատակցի անունը"
                                        value="{{ old('name', $person->name ?? '') }}">
                                </div>
                            </div>

                            {{-- Surname --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ազգանուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="surname"
                                        placeholder="Աշխատակցի ազգանունը"
                                        value="{{ old('surname', $person->surname ?? '') }}">
                                </div>
                            </div>

                            {{-- TRAINER SELECT --}}
                            <div class="row mb-3" id="trainerRow">
                                <label class="col-sm-3 col-form-label">Մարզիչ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="trainer_id" id="trainerSelect">
                                        <option value="" disabled {{ $currentTrainerId ? '' : 'selected' }}>Ընտրել մարզիչ</option>

                                        @if(isset($trainers) && count($trainers) > 0)
                                        @foreach($trainers as $t)
                                        @php
                                        $schedArr = $t->scheduleNames->map(function($s) {
                                        return [
                                        'id' => $s->id,
                                        'name' => $s->name,
                                        'details' => $s->schedule_details
                                        ? $s->schedule_details->map(fn($d) => [
                                        'id' => $d->id,
                                        'week_day' => $d->week_day,
                                        'day_start_time' => substr($d->day_start_time, 0, 5),
                                        'day_end_time' => substr($d->day_end_time, 0, 5),
                                        'break_start_time' => $d->break_start_time ? substr($d->break_start_time, 0, 5) : null,
                                        'break_end_time' => $d->break_end_time ? substr($d->break_end_time, 0, 5) : null,
                                        ])->values()
                                        : [],
                                        ];
                                        })->values();

                                        $durArr = $t->sessionDurations->map(fn($d) => [
                                        'id' => $d->id,
                                        'minutes' => $d->minutes,
                                        'title' => $d->title,
                                        'price_amd' => $d->price_amd,
                                        ])->values();
                                        @endphp


                                        <option
                                            value="{{ $t->id }}"
                                            data-schedules='@json($schedArr)'
                                            data-durations='@json($durArr)'
                                            {{ (string)$currentTrainerId === (string)$t->id ? 'selected' : '' }}>
                                            {{ $t->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>

                                    @error('trainer_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- SCHEDULE --}}
                            <div class="row mb-3 d-none" id="trainerScheduleRow">
                                <label class="col-sm-3 col-form-label">Ժամային գրաֆիկ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="schedule_name_id" id="trainerScheduleSelect">
                                        <option value="" disabled selected>Ընտրել Ժամային գրաֆիկ</option>
                                    </select>

                                    @error("schedule_name_id")
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- DURATION --}}
                            <div class="row mb-3 d-none" id="trainerDurationRow">
                                <label class="col-sm-3 col-form-label">Պարապմունք</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="session_duration_id" id="trainerDurationSelect">
                                        <option value="" disabled selected>Ընտրել պարապմունքը</option>
                                    </select>

                                    @error('session_duration_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ MULTI DAYS UI --}}
                            <div class="row mb-3 d-none" id="multiDaysRow">
                                <label class="col-sm-3 col-form-label">Օրեր / Ժամեր</label>
                                <div class="col-sm-9" id="multiDaysWrap"></div>
                            </div>

                            {{-- Department --}}
                            @if($departments != null && count($person['schedule_department_people']) > 0 && count($departments) > 0)
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ստորաբաժանումներ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="department_id">
                                        <option value="" disabled>Ընտրել ստորաբաժանումը</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ (string)($person['schedule_department_people'][0]->department_id ?? '') === (string)$department->id ? "selected" : "" }}>
                                            {{ $department->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error("department_id")
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            {{-- Email --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email"
                                        placeholder="example@gmail.com"
                                        value="{{ old('email', $person->email ?? '') }}">
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Հեռախոսահամար</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="+374980000"
                                        value="{{ old('phone', $person->phone ?? '') }}">

                                    @error('phone')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Image --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">ներբեռնել նկար</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="file" id="formFile" name="image">
                                </div>
                            </div>

                            @if ($person->image !== null)
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <div class="uploaded-image-div mx-2">
                                        <img src="{{ route('get-file', ['path' => $person->image]) }}"
                                            class="d-block rounded uploaded-image uploaded-photo-project"
                                            style="width:150px">
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Type fixed --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անձի կարգավիճակ</label>
                                <div class="col-sm-9">
                                    <input type="hidden" name="type" value="visitor">
                                    <select class="form-select" disabled>
                                        <option value="worker">Աշխատող</option>
                                        <option value="visitor" selected>Այցելու</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Package --}}
                            <div class="row mb-3" id="packageRow">
                                <label class="col-sm-3 col-form-label">Փաթեթ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="package_id" id="packageSelect">
                                        <option value="" disabled {{ old('package_id', $person->package_id ?? null) ? '' : 'selected' }}>Ընտրել փաթեթը</option>
                                        @if(isset($packages) && count($packages) > 0)
                                        @foreach($packages as $p)
                                        @php
                                        $hasDiscount = (bool)($p->is_discounted ?? false);
                                        $finalPrice = $hasDiscount
                                        ? (int) round($p->discounted_price_amd)
                                        : (int) $p->price_amd;
                                        @endphp

                                        <option value="{{ $p->id }}"
                                            {{ (string)old('package_id', $person->package_id ?? '') === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->months }} ամիս —
                                            {{ number_format($finalPrice) }} դրամ
                                            @if($hasDiscount)
                                            (զեղչված, {{ number_format((int)$p->price_amd) }})
                                            @endif
                                        </option>
                                        @endforeach

                                        @endif
                                    </select>

                                    @error('package_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Պահպանել</button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>
@endsection