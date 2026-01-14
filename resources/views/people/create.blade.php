@extends('layouts.app')

@section('content')
@php
$client_id = $entry_codes['client_id'];
$schedule_name = isset($entry_codes['client_schedule']) ? $entry_codes['client_schedule'] : null;
$departments = isset($entry_codes['department']) ? $entry_codes['department'] : null;

unset($entry_codes['client_schedule']);
unset($entry_codes['department']);
unset($entry_codes['client_id']);

// ✅ FIXED TYPE HERE:
$fixedType = 'visitor'; // change to 'worker' if you want
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

                        <form action="{{ route('visitors.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" value="{{ $client_id }}" name="client_id">

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
                                    <input type="text" class="form-control" name="name" placeholder="Աշխատակցի անունը" value="{{ old('name') }}" required>
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
                                    <input type="text" class="form-control" name="surname" placeholder="Աշխատակցի ազգանունը" value="{{ old('surname') }}" required>
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
                                        $schedArr = $t->scheduleNames->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
                                        @endphp

                                        <option
                                            value="{{ $t->id }}"
                                            data-schedules='@json($schedArr)'
                                            {{ old('trainer_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }}
                                            @if($t->scheduleNames->count())
                                            — ({{ $t->scheduleNames->pluck('name')->join(', ') }})
                                            @endif
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

                            {{-- TRAINER SCHEDULE PICKER (shown after trainer selection) --}}
                            <div class="row mb-3 d-none" id="trainerScheduleRow">
                                <label class="col-sm-3 col-form-label">Մարզչի հերթափոխ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="schedule_name_id" id="trainerScheduleSelect">
                                        <option value="" disabled selected>Ընտրել հերթափոխը</option>
                                    </select>

                                    @error('schedule_name_id')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
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
                                    @error('department_id')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" placeholder="example@gmail.com" value="{{ old('email') }}">
                                    @error('email')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Հեռախոսահամար</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone" placeholder="+374980000" value="{{ old('phone') }}">
                                    @error('phone')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ներբեռնել նկար</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="file" id="formFile" name="image">
                                    @error('image')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ FIXED TYPE: no choice --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անձի կարգավիճակ</label>
                                <div class="col-sm-9">
                                    {{-- value that will be submitted --}}
                                    <input type="hidden" name="type" value="{{ $fixedType }}">

                                    {{-- visible but not changeable --}}
                                    <select class="form-select" disabled>
                                        <option value="worker" {{ $fixedType === 'worker' ? 'selected' : '' }}>Աշխատող</option>
                                        <option value="visitor" {{ $fixedType === 'visitor' ? 'selected' : '' }}>Այցելու</option>
                                    </select>

                                    @error('type')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ ALWAYS OPEN PACKAGE (no JS needed) --}}
                            <div class="row mb-3" id="packageRow">
                                <label class="col-sm-3 col-form-label">Փաթեթ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="package_id" id="packageSelect">
                                        <option value="" disabled {{ old('package_id') ? '' : 'selected' }}>Ընտրել փաթեթը</option>
                                        @foreach($packages as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->name }} —
                                            @if($p->is_discounted)
                                            {{ round($p->discounted_price_amd) }} AMD (զեղչված)
                                            @else
                                            {{ $p->price_amd }} AMD
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

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
                                </div>
                            </div>

                        </form>

                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const trainerSelect = document.getElementById("trainerSelect");
        const row = document.getElementById("trainerScheduleRow");
        const scheduleSelect = document.getElementById("trainerScheduleSelect");

        if (!trainerSelect || !row || !scheduleSelect) return;

        function fillSchedules() {
            const opt = trainerSelect.selectedOptions?.[0];
            const schedulesJson = opt?.getAttribute("data-schedules");

            // reset
            scheduleSelect.innerHTML = `<option value="" disabled selected>Ընտրել հերթափոխը</option>`;

            if (!trainerSelect.value || !schedulesJson) {
                row.classList.add("d-none");
                return;
            }

            let schedules = [];
            try {
                schedules = JSON.parse(schedulesJson);
            } catch (e) {
                schedules = [];
            }

            if (!Array.isArray(schedules) || schedules.length === 0) {
                row.classList.add("d-none");
                return;
            }

            schedules.forEach(s => {
                const o = document.createElement("option");
                o.value = s.id;
                o.textContent = s.name;
                scheduleSelect.appendChild(o);
            });

            // old() restore (validation error case)
            const oldVal = @json(old('schedule_name_id'));
            if (oldVal) scheduleSelect.value = String(oldVal);

            row.classList.remove("d-none");
        }

        trainerSelect.addEventListener("change", fillSchedules);
        fillSchedules(); // page load
    });
</script>