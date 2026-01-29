@extends('layouts.app')
@php

$latestPayment = $data['latest_payment'] ?? null;
$isPaidNow = old('is_paid');
if ($isPaidNow === null) {
$isPaidNow = ($latestPayment && $latestPayment->status === 'paid') ? 1 : 0;
}

$currentPaymentMethod = old('payment_method') ?? ($latestPayment->payment_method ?? '');
$currentPaymentBank = old('payment_bank') ?? ($latestPayment->payment_bank ?? '');

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

$latestBooking = $person->latestBooking;
$isExpired = $latestBooking && \Carbon\Carbon::parse($latestBooking->session_end_time)->lt(\Carbon\Carbon::today());
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
        const changePackageChk = document.getElementById("changePackageChk");
        const packageSelect = document.getElementById("packageSelect");

        // ✅ payment elements
        const paymentMethod = document.getElementById("paymentMethod");
        const bankRow = document.getElementById("bankRow");
        const paymentBank = document.getElementById("paymentBank");

        function toggleBank() {
            const v = paymentMethod?.value || "";
            const needsBank = (v === "cashless" || v === "credit");

            if (needsBank) {
                bankRow?.classList.remove("d-none");
                paymentBank?.setAttribute("required", "required");
            } else {
                bankRow?.classList.add("d-none");
                if (paymentBank) {
                    paymentBank.removeAttribute("required");
                    paymentBank.value = "";
                }
            }
        }

        // init + change
        toggleBank();
        paymentMethod?.addEventListener("change", toggleBank);


        function resetScheduleUI() {
            scheduleRow?.classList.add("d-none");
            durationRow?.classList.add("d-none");
            multiRow?.classList.add("d-none");

            if (scheduleSelect) {
                scheduleSelect.innerHTML = `<option value="" disabled selected>Ընտրել Ժամային գրաֆիկ</option>`;
                scheduleSelect.value = "";
            }
            if (durationSelect) {
                durationSelect.innerHTML = `<option value="" disabled selected>Ընտրել պարապմունքը</option>`;
                durationSelect.value = "";
            }

            if (multiWrap) multiWrap.innerHTML = "";
            if (weeklyJsonInput) weeklyJsonInput.value = "[]";
            saved = [];
        }

        function togglePackage() {
            if (!changePackageChk || !packageSelect) return;

            if (changePackageChk.checked) {
                packageSelect.removeAttribute("disabled");
            } else {
                packageSelect.setAttribute("disabled", "disabled");

                // ցանկության դեպքում՝ վերադարձնել նախնական selected-ը (person-ի package)
                // packageSelect.value = String(@json($person->package_id ?? ''));
            }
        }

        changePackageChk?.addEventListener("change", togglePackage);
        togglePackage();

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

                // ✅ հաշվենք առաջարկվող slot-երը տվյալ օրվա համար
                const allSlots = [];
                arr.forEach(detail => {
                    buildSlotsForDetail(detail, minutes).forEach(s => allSlots.push(s));
                });

                // ✅ եթե slot չկա՝ օրը չենք ցույց տալիս
                if (!allSlots.length) return;

                // ✅ dedupe
                const seen = new Set();
                const uniqueSlots = [];
                allSlots.forEach(s => {
                    const key = `${s.start}-${s.end}`;
                    if (seen.has(key)) return;
                    seen.add(key);
                    uniqueSlots.push(s);
                });

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

                uniqueSlots.forEach(s => {
                    const o = document.createElement("option");
                    o.value = s.start;
                    o.textContent = s.label;
                    o.setAttribute("data-end", s.end);
                    sel.appendChild(o);
                });

                // ✅ restore saved (եթե տվյալ day-ը saved JSON-ում կա)
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


            if (multiWrap && multiWrap.children.length > 0) {
                multiRow.classList.remove("d-none");
            } else {
                multiRow.classList.add("d-none");
                weeklyJsonInput.value = "[]";
            }
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
            if (trainerSelect.value === "__remove__") {
                // ✅ backend-ին ճիշտ արժեք ուղարկելու համար
                // select-ը submit-ի պահին դարձնենք empty
                resetScheduleUI();
                return;
            }

            // trainer փոխվել է
            saved = [];
            weeklyJsonInput.value = "[]";
            fillSchedulesAndDurations(false);
        });


        scheduleSelect.addEventListener("change", renderMultiDays);
        durationSelect.addEventListener("change", renderMultiDays);

        form?.addEventListener("submit", () => {
            updateWeeklyJson();

            if (trainerSelect.value === "__remove__") {
                // ✅ request-ում trainer_id դատարկ կգնա
                trainerSelect.value = "";
            }
        });

        // init
        function setInvalid(el, msg) {
            if (!el) return;
            el.classList.add("is-invalid");

            // remove old feedback
            const old = el.parentElement?.querySelector(".invalid-feedback.js");
            if (old) old.remove();

            const div = document.createElement("div");
            div.className = "invalid-feedback js";
            div.textContent = msg || "Սխալ արժեք";
            el.parentElement?.appendChild(div);
        }

        function clearInvalid(el) {
            if (!el) return;
            el.classList.remove("is-invalid");
            const old = el.parentElement?.querySelector(".invalid-feedback.js");
            if (old) old.remove();
        }

        function validateForm() {
            let ok = true;

            // ✅ clear previous errors
            [trainerSelect, scheduleSelect, durationSelect, packageSelect].forEach(clearInvalid);

            // -------- Trainer logic --------
            const trainerVal = trainerSelect?.value || "";

            // placeholder "" (disabled) չի submit անում, բայց if current empty -> ignore
            const isRemoveTrainer = trainerVal === "__remove__";
            const hasTrainer = !!trainerVal && !isRemoveTrainer;

            // եթե user-ը ոչինչ չի ընտրել (placeholder) և currentTrainerId չունես, կարող ես պարտադրել՝
            // բայց քո edit-ում գուցե trainer չունենա, դրա համար չենք պարտադրում trainer-ը

            if (hasTrainer) {
                // schedule required
                if (!scheduleSelect?.value) {
                    setInvalid(scheduleSelect, "Ընտրիր ժամային գրաֆիկը");
                    ok = false;
                }

                // duration required
                if (!durationSelect?.value) {
                    setInvalid(durationSelect, "Ընտրիր պարապմունքի տևողությունը");
                    ok = false;
                }

                // weekly slots required (եթե schedule + duration ընտրված են)
                if (scheduleSelect?.value && durationSelect?.value) {
                    // ensure latest json
                    updateWeeklyJson();

                    let arr = [];
                    try {
                        arr = JSON.parse(weeklyJsonInput?.value || "[]");
                    } catch (e) {
                        arr = [];
                    }

                    if (!Array.isArray(arr) || arr.length === 0) {
                        // multiRow-ի տակ error ցույց տանք
                        setInvalid(durationSelect, "Ընտրիր գոնե 1 օր և ժամ");
                        ok = false;
                    }
                }
            }

            // եթե trainer remove է → schedule/duration/slots պետք չի ստուգել
            // եթե trainer empty է → նույնպես skip

            // -------- Package logic --------
            if (changePackageChk?.checked) {
                if (!packageSelect?.value) {
                    setInvalid(packageSelect, "Ընտրիր փաթեթը կամ անջատի checkbox-ը");
                    ok = false;
                }
            } else {
                clearInvalid(packageSelect);
            }

            return ok;
        }

        // ✅ live validation clears
        trainerSelect?.addEventListener("change", () => {
            clearInvalid(trainerSelect);
            clearInvalid(scheduleSelect);
            clearInvalid(durationSelect);
        });

        scheduleSelect?.addEventListener("change", () => clearInvalid(scheduleSelect));
        durationSelect?.addEventListener("change", () => clearInvalid(durationSelect));
        packageSelect?.addEventListener("change", () => clearInvalid(packageSelect));
        changePackageChk?.addEventListener("change", () => clearInvalid(packageSelect));

        // ✅ submit validation
        form?.addEventListener("submit", (e) => {
            // եթե remove trainer ընտրված է՝ submit-ից առաջ trainer_id դատարկ ուղարկենք (քեզ մոտ արդեն կա)
            // updateWeeklyJson() նույնպես կանչվում է

            const ok = validateForm();
            if (!ok) {
                e.preventDefault();
                e.stopPropagation();

                // optional: scroll to first invalid
                const first = form.querySelector(".is-invalid");
                first?.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }
        });

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
                            {{-- TRAINER SELECT --}}
                            <div class="row mb-3" id="trainerRow">
                                <label class="col-sm-3 col-form-label">Մարզիչ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="trainer_id" id="trainerSelect">
                                        {{-- placeholder --}}
                                        <option value="" disabled {{ $currentTrainerId ? '' : 'selected' }}>
                                            Ընտրել մարզիչ
                                        </option>

                                        {{-- ✅ remove trainer --}}
                                        <option value="__remove__">— Հանել մարզիչը —</option>

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
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="changePackageChk"
                                            name="change_package"
                                            value="1"
                                            {{ old('change_package') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="changePackageChk">
                                            Փոխել փաթեթը
                                        </label>
                                    </div>

                                    <select
                                        class="form-select"
                                        name="package_id"
                                        id="packageSelect"
                                        {{ old('change_package') ? '' : 'disabled' }}>
                                        <option value="" disabled {{ old('package_id', $person->package_id ?? null) ? '' : 'selected' }}>
                                            Ընտրել փաթեթը
                                        </option>

                                        @foreach($packages as $p)
                                        @php
                                        $hasDiscount = (bool)($p->is_discounted ?? false);
                                        $finalPrice = $hasDiscount ? (int) round($p->discounted_price_amd) : (int) $p->price_amd;
                                        @endphp

                                        <option
                                            value="{{ $p->id }}"
                                            {{ (string)old('package_id', $person->package_id ?? '') === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->months }} ամիս — {{ number_format($finalPrice) }} դրամ
                                            @if($hasDiscount)
                                            (զեղչված, {{ number_format((int)$p->price_amd) }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @if($isExpired)
                                    <div class="small text-danger fw-semibold mt-1">
                                        ⚠ Փաթեթի ժամկետը ավարտվել է ({{ \Carbon\Carbon::parse($latestBooking->session_end_time)->format('d.m.Y') }})
                                    </div>
                                    @endif
                                    <small class="text-muted d-block mt-1">
                                        Եթե “Փոխել փաթեթը”-ը չնշես, փաթեթը չի փոխվի update-ի ժամանակ։
                                    </small>

                                    @error('package_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ Payment method --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Վճարման եղանակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="payment_method" id="paymentMethod" required>
                                        <option value="" disabled {{ $currentPaymentMethod ? '' : 'selected' }}>Ընտրել</option>

                                        <option value="cash" {{ $currentPaymentMethod==='cash' ? 'selected' : '' }}>Կանխիկ</option>
                                        <option value="cashless" {{ $currentPaymentMethod==='cashless' ? 'selected' : '' }}>Անկանխիկ</option>
                                        <option value="credit" {{ $currentPaymentMethod==='credit' ? 'selected' : '' }}>Կրեդիտ</option>
                                    </select>

                                    @error('payment_method')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ Bank (only for cashless/credit) --}}
                            <div class="row mb-3 d-none" id="bankRow">
                                <label class="col-sm-3 col-form-label">Բանկ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="payment_bank" id="paymentBank">
                                        <option value="" disabled {{ $currentPaymentBank ? '' : 'selected' }}>Ընտրել բանկ</option>

                                        <option value="Ameria" {{ $currentPaymentBank==='Ameria' ? 'selected' : '' }}>Ameria</option>
                                        <option value="Ardshin" {{ $currentPaymentBank==='Ardshin' ? 'selected' : '' }}>Ardshin</option>
                                        <option value="ACBA" {{ $currentPaymentBank==='ACBA' ? 'selected' : '' }}>ACBA</option>
                                        <option value="IDBank" {{ $currentPaymentBank==='IDBank' ? 'selected' : '' }}>IDBank</option>
                                        <option value="Inecobank" {{ $currentPaymentBank==='Inecobank' ? 'selected' : '' }}>Inecobank</option>
                                        <option value="Evoca" {{ $currentPaymentBank==='Evoca' ? 'selected' : '' }}>Evoca</option>
                                        <option value="Araratbank" {{ $currentPaymentBank==='Araratbank' ? 'selected' : '' }}>Araratbank</option>
                                        <option value="VTB" {{ $currentPaymentBank==='VTB' ? 'selected' : '' }}>VTB</option>
                                        <option value="Unibank" {{ $currentPaymentBank==='Unibank' ? 'selected' : '' }}>Unibank</option>
                                        <option value="Other" {{ $currentPaymentBank==='Other' ? 'selected' : '' }}>Այլ</option>
                                    </select>

                                    @error('payment_bank')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ Paid status checkbox --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Վճարված է</label>
                                <div class="col-sm-9">
                                    <div class="form-check mt-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="isPaidChk"
                                            name="is_paid"
                                            value="1"
                                            {{ (string)$isPaidNow === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isPaidChk">
                                            Նշել որպես վճարված
                                        </label>
                                    </div>
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