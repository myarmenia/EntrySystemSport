@extends('layouts.app')

@section('page-script')
<script src="{{ asset('assets/js/user-role.js') }}"></script>
@endsection
@section('content')
<main id="main" class="main">
    <section class="section">
        <form action="{{ route('users.store') }}" method="post" {{-- enctype="multipart/form-data" --}}>
            <div class="row">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Օգտատերերի
                                                ցանկ</a></li>

                                            <li class="breadcrumb-item active">Ստեղծել Օգտատեր</li>
                                        </ol>
                                    </nav>
                                </h5>

                            <!-- General Form Elements -->


                            <div class="row mb-3">

                                <label for="inputText" class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" placeholder="Աշխատակցի անունը"
                                        value="{{ old('name') }}">
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
                                        placeholder="example@gmail.com" value="{{ old('email') }}">
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
                                    <input type="password" name="password" class="form-control" placeholder="Password"
                                        value="">
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
                                    <input type="password" name="confirm-password" class="form-control"
                                        placeholder="Confirm Password" value="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Դերեր</label>
                                <div class="col-sm-9">
                                    <select name="roles" class="form-control" id="selectedRole">
                                        <option disabled> Ընտրել դերեր</option>
                                        @foreach ($roles as $value => $label)
                                        @continue(
                                        in_array($value, ['trainer']) &&
                                        auth()->user()->hasAnyRole(['client_admin', 'client_admin_rfID'])
                                        )
                                        <option value="{{ $value }}" {{  old('roles') == $value ? 'selected' : '' }}>
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
                            <div class="row mb-3 d-none" id="scheduleSelectWrapper">
                                <label class="col-sm-3 col-form-label">Հերթափոխեր</label>
                                <div class="col-sm-9">
                                    <select name="schedule_name_ids[]" class="form-control" id="scheduleSelect" multiple>
                                        @foreach ($scheduleNames as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ collect(old('schedule_name_ids', []))->contains((string)$id) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>


                                    @error('schedule_name_ids')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                    @error('schedule_name_ids.*')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 d-none" id="session_duration">
                                <label class="form-label fw-bold">Ժամային պարապմունքներ</label>

                                <div id="sessionDurationList">
                                    {{-- initial one (old() support) --}}
                                    @php
                                    $oldDurations = old('session_durations', [[]]);
                                    @endphp

                                    @foreach ($oldDurations as $index => $duration)
                                    <div class="session-duration-item border rounded p-3 mb-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-3">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    class="form-control"
                                                    name="session_durations[{{ $index }}][minutes]"
                                                    placeholder="Տևողություն (րոպե)"
                                                    value="{{ $duration['minutes'] ?? '' }}">
                                            </div>

                                            <div class="col-md-4">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="session_durations[{{ $index }}][title]"
                                                    placeholder="Անվանում"
                                                    value="{{ $duration['title'] ?? '' }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    class="form-control"
                                                    name="session_durations[{{ $index }}][price_amd]"
                                                    placeholder="Գին (AMD)"
                                                    value="{{ $duration['price_amd'] ?? '' }}">
                                            </div>

                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-session">
                                                    ✕
                                                </button>
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

            </div>


            <div class="col-lg-6" id="componentContainer">
                @if ($errors->has('client.name'))
                <x-client />
                @endif

                @if ($errors->any(['name', 'email', 'password', 'confirm-password']))

                @if (old('roles') === 'client_admin' || old('roles') === 'client_admin_rfID')
                @if (!$errors->has('client.name'))
                <x-client />
                @endif
                @endif
                @endif

            </div>
            @if (old('roles') != 'client_admin' && old('roles') != 'client_admin_rfID')
            <div class="row mb-3 {{$errors->has('client.name') ? 'd-none' : null  }}" id="loginBtn">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
                </div>
            </div>
            @endif

            </div>
        </form>

    </section>

</main>


@endsection
