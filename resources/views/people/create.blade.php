@extends('layouts.app')

@section('content')
@php
$client_id = $entry_codes['client_id'];
$departments = isset($entry_codes['department']) ? $entry_codes['department'] : null;

unset($entry_codes['client_schedule']);
unset($entry_codes['department']);
unset($entry_codes['client_id']);

$fixedType = 'visitor';
@endphp

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">

                    @if (count($entry_codes) == 0)
                    <div class="d-flex justify-content-center vh-100 fw-bold">
                        <h2 class="mt-5">Նույնականացման կոդերը բացակայում են</h2>
                    </div>
                    @else
                    <div class="card-body">

                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('visitors.list') }}">Այցելուների ցանկ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Ստեղծել</li>
                                </ol>
                            </nav>
                        </h5>

                        <form action="{{ route('visitors.store') }}" method="post" enctype="multipart/form-data" id="visitorCreateForm">
                            @csrf
                            <input type="hidden" value="{{ $client_id }}" name="client_id">

                            {{-- ✅ NEW: all chosen days/times will be saved here --}}
                            <input type="hidden" name="weekly_slots_json" id="weeklySlotsJson" value="{{ old('weekly_slots_json','[]') }}">

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Նույնականացման կոդ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="entry_code_id" required>
                                        <option value="" disabled selected>Ընտրել նույնականացման կոդը</option>
                                        @foreach ($entry_codes as $code)
                                        <option value="{{ $code->id }}" {{ old('entry_code_id') == $code->id ? 'selected' : '' }}>
                                            {{ $code->token }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('entry_code_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="mb-3 row">
                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ազգանուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="surname" value="{{ old('surname') }}" required>
                                    @error('surname')
                                    <div class="mb-3 row">
                                        <p class="col-sm-9 mb-3 text-danger fs-6">{{ $message }}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- TRAINER SELECT --}}
                            @if (Auth::user()->hasRole([ 'client_sport']))
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Մարզիչ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="trainer_id" id="trainerSelect">
                                        <option value="" disabled {{ old('trainer_id') ? '' : 'selected' }}>Ընտրել մարզիչ</option>

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
                                            {{ old('trainer_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('trainer_id')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- schedule --}}
                            <div class="row mb-3 d-none" id="trainerScheduleRow">
                                <label class="col-sm-3 col-form-label">Մարզչի Ժամային գրաֆիկ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="schedule_name_id" id="trainerScheduleSelect">
                                        <option value="" disabled selected>Ընտրել Ժամային գրաֆիկ</option>
                                    </select>
                                    @error('schedule_name_id')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- duration --}}
                            <div class="row mb-3 d-none" id="trainerDurationRow">
                                <label class="col-sm-3 col-form-label">Պարապմունք (տևողություն)</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="session_duration_id" id="trainerDurationSelect">
                                        <option value="" disabled selected>Ընտրել պարապմունքը</option>
                                    </select>
                                    @error('session_duration_id')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- MULTI DAY + TIME UI --}}
                            <div class="row mb-3 d-none" id="multiDaysRow">
                                <label class="col-sm-3 col-form-label">Օրեր / Ժամեր</label>
                                <div class="col-sm-9" id="multiDaysWrap"></div>
                            </div>
                            @endif

                            @if($departments != null)
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ստորաբաժանումներ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="department_id">
                                        <option value="" disabled selected>Ընտրել ստորաբաժանումը</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Հեռախոսահամար</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ներբեռնել նկար</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="file" name="image">
                                </div>
                            </div>

                            {{-- fixed type --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անձի կարգավիճակ</label>
                                <div class="col-sm-9">
                                    <input type="hidden" name="type" value="{{ $fixedType }}">
                                    <select class="form-select" disabled>
                                        <option value="worker" {{ $fixedType === 'worker' ? 'selected' : '' }}>Աշխատող</option>
                                        <option value="visitor" {{ $fixedType === 'visitor' ? 'selected' : '' }}>Այցելու</option>
                                    </select>
                                </div>
                            </div>

                            {{-- package --}}
                            <div class="row mb-3" id="packageRow">
                                <label class="col-sm-3 col-form-label">Փաթեթ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="package_id" id="packageSelect">
                                        <option value="" disabled {{ old('package_id') ? '' : 'selected' }}>Ընտրել փաթեթը</option>

                                        @foreach($packages as $p)
                                        @php
                                        $basePrice = (int) ($p->price_amd ?? 0);
                                        $hasDiscount = (bool) ($p->is_discounted ?? false);
                                        $discountedPrice = (int) round($p->discounted_price_amd ?? $basePrice);
                                        @endphp

                                        <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} —
                                            @if($hasDiscount)
                                                {{ number_format($discountedPrice) }} AMD <span class="text-success">(զեղչված)</span>
                                            @else
                                                {{ number_format($basePrice) }} AMD
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('package_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Payment method (fixed list) --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Վճարման եղանակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="payment_method" id="paymentMethod" required>
                                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Ընտրել</option>
                                        <option value="cash" {{ old('payment_method')=='cash' ? 'selected' : '' }}>Կանխիկ</option>
                                        <option value="cashless" {{ old('payment_method')=='cashless' ? 'selected' : '' }}>Անկանխիկ</option>
                                        <option value="credit" {{ old('payment_method')=='credit' ? 'selected' : '' }}>Կրեդիտ</option>
                                    </select>
                                    @error('payment_method')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Bank (only for cashless/credit) --}}
                            <div class="row mb-3 d-none" id="bankRow">
                                <label class="col-sm-3 col-form-label">Բանկ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="payment_bank" id="paymentBank">
                                        <option value="" disabled {{ old('payment_bank') ? '' : 'selected' }}>Ընտրել բանկ</option>
                                        <option value="Ameria" {{ old('payment_bank')=='Ameria' ? 'selected' : '' }}>Ameria</option>
                                        <option value="Ardshin" {{ old('payment_bank')=='Ardshin' ? 'selected' : '' }}>Ardshin</option>
                                        <option value="ACBA" {{ old('payment_bank')=='ACBA' ? 'selected' : '' }}>ACBA</option>
                                        <option value="IDBank" {{ old('payment_bank')=='IDBank' ? 'selected' : '' }}>IDBank</option>
                                        <option value="Inecobank" {{ old('payment_bank')=='Inecobank' ? 'selected' : '' }}>Inecobank</option>
                                        <option value="Evoca" {{ old('payment_bank')=='Evoca' ? 'selected' : '' }}>Evoca</option>
                                        <option value="Araratbank" {{ old('payment_bank')=='Araratbank' ? 'selected' : '' }}>Araratbank</option>
                                        <option value="VTB" {{ old('payment_bank')=='VTB' ? 'selected' : '' }}>VTB</option>
                                        <option value="Unibank" {{ old('payment_bank')=='Unibank' ? 'selected' : '' }}>Unibank</option>
                                        <option value="Other" {{ old('payment_bank')=='Other' ? 'selected' : '' }}>Այլ</option>
                                    </select>
                                    @error('payment_bank')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
                                </div>
                            </div>

                        </form>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Validation errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("visitorCreateForm");
    const weeklyJsonInput = document.getElementById("weeklySlotsJson");

    // ✅ payment elements (always exist)
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

    // initial + on change (old() support)
    toggleBank();
    paymentMethod?.addEventListener("change", toggleBank);

    // ✅ trainer elements (might not exist)
    const trainerSelect = document.getElementById("trainerSelect");
    const scheduleRow = document.getElementById("trainerScheduleRow");
    const scheduleSelect = document.getElementById("trainerScheduleSelect");
    const durationRow = document.getElementById("trainerDurationRow");
    const durationSelect = document.getElementById("trainerDurationSelect");
    const multiRow = document.getElementById("multiDaysRow");
    const multiWrap = document.getElementById("multiDaysWrap");

    const DAY_ORDER = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

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
        if (!trainerSelect) return [];
        const opt = trainerSelect.selectedOptions?.[0];
        const schedulesJson = opt?.getAttribute("data-schedules");
        try { return schedulesJson ? JSON.parse(schedulesJson) : []; } catch(e) { return []; }
    }

    function getSelectedScheduleDetails() {
        if (!scheduleSelect) return [];
        const scheduleId = scheduleSelect.value;
        if (!scheduleId) return [];
        const schedules = getTrainerSchedules();
        const s = schedules.find(x => String(x.id) === String(scheduleId));
        return s?.details || [];
    }

    function getDurationMinutes() {
        if (!durationSelect || !trainerSelect) return 0;
        const durationId = durationSelect.value;
        if (!durationId) return 0;

        const opt = trainerSelect.selectedOptions?.[0];
        const durationsJson = opt?.getAttribute("data-durations");

        let durations = [];
        try { durations = durationsJson ? JSON.parse(durationsJson) : []; } catch(e) { durations = []; }

        const d = durations.find(x => String(x.id) === String(durationId));
        return Number(d?.minutes || 0);
    }

    function buildSlotsForDetail(detail, durationMinutes) {
        const slots = [];
        const DAY = 24 * 60;

        let start = timeToMinutes(detail.day_start_time);
        let end = timeToMinutes(detail.day_end_time);

        if (end <= start) end += DAY;

        let breakStart = null;
        let breakEnd = null;

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
            const slotStart = t;
            const slotEnd = t + durationMinutes;

            const overlapsBreak = (breakStart != null && breakEnd != null)
                ? !(slotEnd <= breakStart || slotStart >= breakEnd)
                : false;

            if (!overlapsBreak) {
                const s = minutesToTime(slotStart % DAY);
                const e = minutesToTime(slotEnd % DAY);
                slots.push({ start: s, end: e, label: `${s} - ${e}` });
            }
        }

        return slots;
    }

    function clearMulti() {
        if (multiWrap) multiWrap.innerHTML = "";
        if (multiRow) multiRow.classList.add("d-none");
        if (weeklyJsonInput) weeklyJsonInput.value = "[]";
    }

    function updateWeeklyJson() {
        if (!weeklyJsonInput || !multiWrap) return;

        const result = [];
        const blocks = multiWrap.querySelectorAll("[data-day-block='1']");

        blocks.forEach(block => {
            const day = block.getAttribute("data-day") || "";
            const check = block.querySelector(".dayCheck");
            const sel = block.querySelector(".dayTimeSelect");

            if (!day || !check?.checked) return;

            const start = sel?.value || "";
            const end = sel?.selectedOptions?.[0]?.getAttribute("data-end") || "";

            if (start && end) result.push({ week_day: day, start_time: start, end_time: end });
        });

        weeklyJsonInput.value = JSON.stringify(result);
    }

    function renderMultiDays() {
        if (!trainerSelect || !scheduleSelect || !durationSelect || !multiWrap || !multiRow) return;

        clearMulti();

        const scheduleId = scheduleSelect.value;
        const minutes = getDurationMinutes();
        const details = getSelectedScheduleDetails();

        if (!trainerSelect.value || !scheduleId || !minutes || !details.length) return;

        const byDay = {};
        details.forEach(d => {
            if (!byDay[d.week_day]) byDay[d.week_day] = [];
            byDay[d.week_day].push(d);
        });

        DAY_ORDER.forEach(day => {
            const arr = byDay[day];
            if (!arr || !arr.length) return;

            const allSlots = [];
            arr.forEach(detail => buildSlotsForDetail(detail, minutes).forEach(s => allSlots.push(s)));
            if (!allSlots.length) return;

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
                    <div class="small text-muted mt-1">Ընտրիր ժամ</div>
                </div>
            `;

            multiWrap.appendChild(block);

            const sel = block.querySelector(".dayTimeSelect");
            const slotsBox = block.querySelector(".daySlots");
            const check = block.querySelector(".dayCheck");

            uniqueSlots.forEach(s => {
                const o = document.createElement("option");
                o.value = s.start;
                o.textContent = s.label;
                o.setAttribute("data-end", s.end);
                sel.appendChild(o);
            });

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

        if (multiWrap.children.length > 0) multiRow.classList.remove("d-none");
        else multiRow.classList.add("d-none");

        updateWeeklyJson();
    }

    function fillTrainerData() {
        if (!trainerSelect || !scheduleRow || !scheduleSelect || !durationRow || !durationSelect) return;

        const opt = trainerSelect.selectedOptions?.[0];

        // schedules
        const schedules = getTrainerSchedules();
        scheduleSelect.innerHTML = `<option value="" disabled selected>Ընտրել Ժամային գրաֆիկ</option>`;

        if (trainerSelect.value && schedules.length) {
            schedules.forEach(s => {
                const o = document.createElement("option");
                o.value = s.id;
                o.textContent = s.name;
                scheduleSelect.appendChild(o);
            });
            scheduleRow.classList.remove("d-none");
        } else {
            scheduleRow.classList.add("d-none");
        }

        // durations
        const durationsJson = opt?.getAttribute("data-durations");
        let durations = [];
        try { durations = durationsJson ? JSON.parse(durationsJson) : []; } catch(e) { durations = []; }

        durationSelect.innerHTML = `<option value="" disabled selected>Ընտրել պարապմունքը</option>`;

        if (trainerSelect.value && durations.length) {
            durations.forEach(d => {
                const o = document.createElement("option");
                o.value = d.id;
                const title = (d.title && String(d.title).trim().length) ? d.title : "";
                const labelTitle = title ? ` — ${title}` : "";
                o.textContent = `${d.minutes} րոպե${labelTitle} — ${d.price_amd} դրամ`;
                durationSelect.appendChild(o);
            });
            durationRow.classList.remove("d-none");
        } else {
            durationRow.classList.add("d-none");
        }

        clearMulti();
    }

    // ----------------------------
    // ✅ validation helpers
    // ----------------------------
    function setInvalid(el, msg) {
        if (!el) return;
        el.classList.add("is-invalid");
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

    function validateCreateForm() {
        let ok = true;

        // package
        const packageSelect = document.getElementById("packageSelect");
        clearInvalid(packageSelect);
        if (!packageSelect?.value) {
            setInvalid(packageSelect, "Ընտրիր փաթեթը");
            ok = false;
        }

        // trainer block only if exists + selected
        if (trainerSelect) {
            [trainerSelect, scheduleSelect, durationSelect].forEach(clearInvalid);

            const hasTrainer = !!trainerSelect.value;
            if (hasTrainer) {
                if (!scheduleSelect?.value) { setInvalid(scheduleSelect, "Ընտրիր ժամային գրաֆիկը"); ok = false; }
                if (!durationSelect?.value) { setInvalid(durationSelect, "Ընտրիր պարապմունքի տևողությունը"); ok = false; }

                if (scheduleSelect?.value && durationSelect?.value) {
                    updateWeeklyJson();
                    let arr = [];
                    try { arr = JSON.parse(weeklyJsonInput?.value || "[]"); } catch(e) { arr = []; }
                    if (!Array.isArray(arr) || arr.length === 0) {
                        setInvalid(durationSelect, "Ընտրիր գոնե 1 օր և ժամ");
                        ok = false;
                    }
                }
            }
        }

        // payment
        clearInvalid(paymentMethod);
        clearInvalid(paymentBank);

        if (!paymentMethod?.value) {
            setInvalid(paymentMethod, "Ընտրիր վճարման եղանակը");
            ok = false;
        } else {
            const needsBank = (paymentMethod.value === "cashless" || paymentMethod.value === "credit");
            if (needsBank && !paymentBank?.value) {
                setInvalid(paymentBank, "Ընտրիր բանկը");
                ok = false;
            }
        }

        return ok;
    }

    // submit validation
    form?.addEventListener("submit", (e) => {
        updateWeeklyJson();
        const ok = validateCreateForm();
        if (!ok) {
            e.preventDefault();
            e.stopPropagation();
            const first = form.querySelector(".is-invalid");
            first?.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    });

    // live clear
    paymentMethod?.addEventListener("change", () => clearInvalid(paymentMethod));
    paymentBank?.addEventListener("change", () => clearInvalid(paymentBank));
    document.getElementById("packageSelect")?.addEventListener("change", () => clearInvalid(document.getElementById("packageSelect")));

    if (trainerSelect) {
        trainerSelect.addEventListener("change", () => {
            if (scheduleSelect) scheduleSelect.value = "";
            if (durationSelect) durationSelect.value = "";
            clearInvalid(trainerSelect);
            clearInvalid(scheduleSelect);
            clearInvalid(durationSelect);
            fillTrainerData();
        });

        scheduleSelect?.addEventListener("change", () => { clearInvalid(scheduleSelect); renderMultiDays(); });
        durationSelect?.addEventListener("change", () => { clearInvalid(durationSelect); renderMultiDays(); });

        // init
        fillTrainerData();
    }
});
</script>
@endsection
