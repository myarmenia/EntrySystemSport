@extends('layouts.app')

@section("page-script")
<script src="{{ asset('assets/js/change-person-permission-entry-code.js') }}"></script>
@endsection

@php
$client_schedules = $data['person_connected_schedule_department']['client_schedules'] ?? null;
$departments = $data['person_connected_schedule_department']['department'] ?? null;
$person = $data['person_connected_schedule_department']['person'] ?? null;

// ✅ visitor হলে type-ը ֆիքսված կլինի
$isVisitorFixed = ($person && $person->type === 'worker');
@endphp

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
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('visitors.list') }}">Այցելուների ցանկ</a></li>
                                    <li class="breadcrumb-item active">Խմբագրել</li>
                                </ol>
                            </nav>
                        </h5>

                        <form action="{{ route('visitors.update', $person->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">
                                    {{ $data['non_active_entry_code']==false && $person->activated_code_connected_person!=null ? "Նույնականացման կոդ" : "Նույնականացման կոդ" }}
                                </label>
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

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Աշխատակցի անունը"
                                        value="{{ $person->name ?? null }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ազգանուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="surname"
                                        placeholder="Աշխատակցի ազգանունը"
                                        value="{{ $person->surname ?? null }}">
                                </div>
                            </div>
                            {{-- ✅ TRAINER SELECT --}}
                            <div class="row mb-3" id="trainerRow">
                                <label class="col-sm-3 col-form-label">Trainer</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="trainer_id" id="trainerSelect">
                                        <option value="" disabled {{ old('trainer_id') || (isset($person->trainer_id) && $person->trainer_id) ? '' : 'selected' }}>
                                            Ընտրել մարզիչ
                                        </option>

                                        @if(isset($trainers) && count($trainers) > 0)
                                        @foreach($trainers as $t)
                                        <option value="{{ $t->id }}"
                                            {{ (old('trainer_id') == $t->id || (isset($person->trainer_id) && $person->trainer_id == $t->id)) ? 'selected' : '' }}>
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


                            @if($client_schedules != null && count($person['schedule_department_people']) > 0 && count($client_schedules) > 0)
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Հերթափոխեր</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="schedule_name_id">
                                        <option value="" disabled>Ընտրել հերթափոխը</option>
                                        @foreach ($client_schedules as $schedule)
                                        <option value="{{ $schedule->id }}" {{ $person['schedule_department_people'][0]->schedule_name_id == $schedule->id ? "selected" : "" }}>
                                            {{ $schedule->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error("schedule_name_id")
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            @if($departments != null && count($person['schedule_department_people']) > 0 && count($departments) > 0)
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ստորաբաժանումներ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="department_id">
                                        <option value="" disabled>Ընտրել ստորաբաժանումը</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ $person['schedule_department_people'][0]->department_id == $department->id ? "selected" : "" }}>
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

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email"
                                        placeholder="example@gmail.com"
                                        value="{{ $person->email ?? null }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Հեռախոսահամար</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="+374980000"
                                        value="{{ $person->phone ?? null }}">

                                    @error('phone')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

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

                            {{-- ✅ TYPE: people.edit-ում միշտ ֆիքսված visitor --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անձի կարգավիճակ</label>
                                <div class="col-sm-9">

                                    {{-- submit value --}}
                                    <input type="hidden" name="type" value="visitor">

                                    {{-- visible but disabled --}}
                                    <select class="form-select" disabled>
                                        <option value="worker">Աշխատող</option>
                                        <option value="visitor" selected>Այցելու</option>
                                    </select>

                                    @error("type")
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror

                                </div>
                            </div>


                            {{-- ✅ Փաթեթը միշտ բաց (JS պետք չէ) --}}
                            <div class="row mb-3" id="packageRow">
                                <label class="col-sm-3 col-form-label">Փաթեթ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="package_id" id="packageSelect">
                                        <option value="" disabled selected>Ընտրել փաթեթը</option>
                                        @if(isset($packages) && count($packages) > 0)
                                        @foreach($packages as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (old('package_id') == $p->id || (isset($person->package_id) && $person->package_id == $p->id)) ? 'selected' : '' }}>
                                            {{ $p->months }} ամիս — {{ number_format($p->price_amd) }} դրամ
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