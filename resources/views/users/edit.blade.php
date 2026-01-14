@extends('layouts.app')
@section('page-script')
<script src="{{ asset('assets/js/user-role.js') }}"></script>
@endsection

@section('content')
<main id="main" class="main">

    <section class="section">
        <form
            method="POST"
            action="{{ route('users.update', $user->id) }}">
            @method('PUT')
            <div class="row">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Օգտատերերի ցանկ</a></li>

                                        <li class="breadcrumb-item active">Խմբագրել</li>
                                    </ol>
                                </nav>
                            </h5>



                            <div class="row mb-3">

                                <label for="inputText" class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Աշխատակցի անունը"
                                        value="{{ $user->name }}">
                                    @error('name')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Էլ.հասցե</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email"
                                        placeholder="example@gmail.com"
                                        value="{{ $user->email }}">
                                    @error('email')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Գաղտնաբառ</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" placeholder="Password" value="">
                                    @error('password')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror

                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Հաստատել գաղտնաբառը</label>
                                <div class="col-sm-9">
                                    <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password" value="">
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Դերեր</label>
                                <div class="col-sm-9">


                                    <select name="roles[]" class="form-control" id="selectedRole" multiple="multiple">
                                        @foreach ($roles as $value => $label)

                                        <option
                                            @if($isEditMode) disabled @endif
                                            value="{{ $value }}" {{ isset($userRole[$value]) ? 'selected' : ''}}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            {{-- TRAINER SCHEDULE --}}
                            {{-- TRAINER SCHEDULE --}}
                            <div class="row mb-3 d-none" id="scheduleSelectWrapper">
                                <label class="col-sm-3 col-form-label">Գրաֆիկ</label>
                                <div class="col-sm-9">

                                    <select name="schedule_name_ids[]" class="form-control" id="scheduleSelect" multiple>
                                        @foreach ($scheduleNames as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $user->scheduleNames->contains($id) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('schedule_name_ids')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                    @error('schedule_name_ids.*')
                                    <div class="text-danger fts-14">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- TRAINER SESSION DURATIONS --}}
                            <div class="mb-3 d-none" id="session_duration">
                                <label class="form-label fw-bold">Ժամային պարապմունքներ</label>

                                <div id="sessionDurationList">
                                    @php
                                    // edit-ում եթե validation error լինի՝ old()-ը առաջնային է
                                    $oldDurations = old('session_durations');

                                    if (is_array($oldDurations)) {
                                    $durationsForView = $oldDurations;
                                    } else {
                                    // DB-ից եկած durations
                                    $durationsForView = ($trainerSessionDurations ?? collect())->map(function ($d) {
                                    return [
                                    'session_duration_id' => $d->id,
                                    'minutes' => $d->minutes,
                                    'title' => $d->title,
                                    'price_amd' => $d->price_amd, // եթե քո table-ում ուրիշ անուն է՝ հարմարեցրու
                                    ];
                                    })->toArray();

                                    // եթե ոչինչ չկա, գոնե 1 դատարկ row ցույց տանք
                                    if (empty($durationsForView)) $durationsForView = [[]];
                                    }
                                    @endphp

                                    @foreach ($durationsForView as $index => $duration)
                                    <div class="session-duration-item border rounded p-3 mb-2">
                                        <div class="row g-2 align-items-center">

                                            {{-- եթե սա DB-ից եկած duration է, պահենք id-ն --}}
                                            <input type="hidden"
                                                name="session_durations[{{ $index }}][session_duration_id]"
                                                value="{{ $duration['session_duration_id'] ?? '' }}">

                                            <div class="col-md-3">
                                                <input type="number" min="1" class="form-control"
                                                    name="session_durations[{{ $index }}][minutes]"
                                                    placeholder="Տևողություն (րոպե)"
                                                    value="{{ $duration['minutes'] ?? '' }}">
                                            </div>

                                            <div class="col-md-4">
                                                <input type="text" class="form-control"
                                                    name="session_durations[{{ $index }}][title]"
                                                    placeholder="Անվանում"
                                                    value="{{ $duration['title'] ?? '' }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input type="number" min="0" class="form-control"
                                                    name="session_durations[{{ $index }}][price_amd]"
                                                    placeholder="Գին (AMD)"
                                                    value="{{ $duration['price_amd'] ?? '' }}">
                                            </div>

                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-session">✕</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addSessionDuration">
                                    + Ավելացնել պարապմունք
                                </button>

                                @error('session_durations')
                                <div class="text-danger fts-14">{{ $message }}</div>
                                @enderror
                            </div>




                        </div>
                    </div>

                </div>
                <div class="col-lg-6" id="componentContainer">


                    @if (in_array('client_admin', $userRole) || in_array('client_admin_rfID', $userRole))

                    <x-client-edit :user="$user" />
                    @endif

                </div>
                <div class="row mb-3 {{ in_array('client_admin', $userRole) || in_array('client_admin_rfID', $userRole) ? 'd-none' : null  }}" id="loginBtn">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Պահպանել</button>
                    </div>
                </div>




            </div>
        </form>

    </section>


</main>
@endsection