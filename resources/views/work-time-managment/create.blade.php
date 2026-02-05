@extends('layouts.app')
@section('page-script')
    <script src="{{ asset('assets/js/work-time-managment.js') }}"></script>
@endsection
{{-- @section('style')
<style>
.time-input::-webkit-calendar-picker-indicator {
    opacity: 0;
    cursor: pointer;
}

/* Firefox */
.time-input {
    appearance: none;
    -moz-appearance: textfield;
}
</style>


@endsection --}}


@section('content')

    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>Աշխատանքային ժամանակի ստեղծում</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>

            </div>
        </div><!-- End Page Title -->

        <form action="{{ route('schedule.work-time-store') }}" method="post">
            @csrf
            <section class="section dashboard">
                <div class="row">
                    <!-- Left side columns -->
                    <div class="col-lg-12">
                        <div class="row">
                            <!-- Reports -->
                            <div class="col-8">

                                <div class="card">
                                    <div class="card-body">
                                        <div class="row  mt-3" >
                                            <div>
                                                <label for="inputCity" class="form-label ">Անվանում</label>
                                                <input type="text"
                                                        name="name"
                                                        class="form-control"
                                                        id="name"
                                                        value={{ old('name') }}
                                                        >
                                                @error("name")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                         @if(auth()->user()->hasAnyRole(['client_admin',"client_admin_rfID","client_sport"]))
                                            <div class="row mb-3 col-6 d-flex align-items-center gap-2">
                                                <label class="col-4 col-form-label">Ակտիվացում </label>
                                                <div class="col-1">
                                                    <div class="form-check form-switch" >
                                                        <input class="form-check-input" type="checkbox"
                                                            name="status">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class=" col-8 pagetitle d-flex justify-content-end  ">
                                <a  id="copyToOthersBtn" class="d-none"  href="javascript:void(0)"
                                 {{-- id="applyToAll" --}}
                                 >Տարածել շաբաթվա բոլոր օրերի վրա</a>
                            </div>
                            @error('week_days')
                                <div class="col-8 alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                            @foreach ($weekdays  as $key => $week )
                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row mt-3 day-row" data-day="{{ $week }}">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="fw-bold">{{ $week }}</h6>
                                                    <input type="hidden" name="week_days[{{ $key }}][week_day]" value="{{ $week }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="inputCity" class="form-label">Աշխատանքային ժամի սկիզբ</label>
                                                    <div class="input-group">

                                                        <input type="time"
                                                            name="week_days[{{ $key }}][day_start_time]"
                                                            class="form-control border-start-0 start-time"
                                                            placeholder="09:00">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputCity" class="form-label">Աշխատանքային ժամի ավարտ</label>
                                                    <div class="input-group">

                                                        <input type="time"
                                                            name="week_days[{{ $key }}][day_end_time]"
                                                            class="form-control border-start-0 end-time"
                                                            placeholder="09:00">
                                                    </div>
                                                </div>
                                               <div class="col-md-4">
                                                    <label class="form-label">Ընդմիջում</label>

                                                    <div class="d-flex gap-2">
                                                        <button type="button"
                                                                class="btn btn-sm mb-2 break-time d-flex align-items-center justify-content-center gap-2 flex-fill"
                                                                data-key="{{ $key }}"
                                                                style="
                                                                    border-radius:8px;
                                                                    padding:8px 14px;
                                                                    background: rgba(220, 252, 231, 1);
                                                                    color: #28a745;
                                                                    font-weight: 600;
                                                                ">
                                                            <i class="fa-solid fa-utensils"></i>Ընդմիջման ժամ
                                                        </button>

                                                        <button type="button"
                                                                class="btn btn-sm mb-2 smoke-time d-flex align-items-center justify-content-center gap-2 flex-fill"
                                                                data-key="{{ $key }}"
                                                                style="
                                                                    border-radius:8px;
                                                                    padding:8px;
                                                                    background: rgba(254, 243, 198, 1);
                                                                    color: #ffc107;
                                                                    font-weight: 600;
                                                                ">
                                                            <i class="fa-solid fa-smoking"></i> Ծխելու ժամ
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 break-time-container mt-3">

                                                </div>
                                                <div class="col-md-12 smoking-time-container mt-2">

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach

                            <div class="col-12">
                               <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
                                </div>

                            </div>

                        </div><!-- End Left side columns -->
                    </div>
            </section>
        </form>
    </main>
@endsection
<x-work-time-alert-modal/>
