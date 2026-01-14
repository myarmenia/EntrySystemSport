@extends('layouts.app')

@section('content')
@php
$client_id = $entry_codes['client_id'];
$schedule_name = isset($entry_codes['client_schedule']) ? $entry_codes['client_schedule'] : null;
$departments = isset($entry_codes['department']) ? $entry_codes['department'] : null;

unset($entry_codes['client_schedule']);
unset($entry_codes['department']);
unset($entry_codes['client_id']);

$fixedType = 'worker';
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
                                        <li class="breadcrumb-item"><a href="{{ route('workers.list') }}">Աշխատողների ցանկ</a></li>
                                        <li class="breadcrumb-item active">Ստեղծել</li>
                                    </ol>
                                </nav>
                            </h5>

                            <form action="{{ route('workers.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" value="{{ $client_id }}" name="client_id">

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Նույնականացման կոդ</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="entry_code_id">
                                            <option value="" disabled selected>Ընտրել նույնականացման կոդը</option>
                                            @foreach ($entry_codes as $code)
                                                <option value="{{ $code->id }}">{{ $code->token }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Անուն</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" placeholder="Աշխատակցի անունը" value="{{ old('name') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Ազգանուն</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="surname" placeholder="Աշխատակցի ազգանունը" value="{{ old('surname') }}">
                                    </div>
                                </div>

                                @if($schedule_name!=null)
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Հերթափոխեր</label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="schedule_name_id">
                                                <option value="" disabled selected>Ընտրել հերթափոխը</option>
                                                @foreach ($schedule_name as $schedule)
                                                    <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                @if ($departments!=null)
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Ստորաբաժանումներ</label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="department_id">
                                                <option value="" disabled selected>Ընտրել ստորաբաժանումը</option>
                                                @foreach ($departments as $department )
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" placeholder="example@gmail.com" value="{{ old('email') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Հեռախոսահամար</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="phone" placeholder="+374980000" value="{{ old('phone') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">ներբեռնել նկար</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="file" id="formFile" name="image">
                                    </div>
                                </div>

                                {{-- ✅ FIXED TYPE: WORKER --}}
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Աշխատակցի կարգավիճակ</label>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="type" value="{{ $fixedType }}">

                                        <select class="form-select" disabled>
                                            <option value="worker" selected>Աշխատող</option>
                                            <option value="visitor">Այցելու</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- {{-- ✅ Always open package (no JS) --}}
                                <div class="row mb-3" id="packageRow">
                                    <label class="col-sm-3 col-form-label">Փաթեթ</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="package_id" id="packageSelect">
                                            <option value="" disabled selected>Ընտրել փաթեթը</option>
                                            @foreach($packages as $p)
                                                <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>
                                                    {{ $p->months }} ամիս — {{ number_format($p->price_amd) }} դրամ
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> -->

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
