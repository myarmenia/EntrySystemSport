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
                            <div class="col-12">

                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <label for="inputCity" class="form-label ">Անվանում</label>
                                                <input type="text"
                                                        name="name"
                                                        class="form-control"
                                                        id="name"
                                                        >
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pagetitle d-flex justify-content-end align-items-center">
                                <a c href="javascript:void(0)" id="applyToAll">Տարածել շաբաթվա բոլոր օրերի վրա</a>
                            </div>
                            @foreach ($weekdays  as $key => $week )
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row mt-3 day-row" data-day="{{ $week }}">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="fw-bold">Երեքշաբթի{{ $week }}</h6>
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
                                                    <label for="breaks" class="form-label">Ընդմիջում</label>
                                                    <div class="d-flex gap-2 mt-2">
                                                        <button type="button"
                                                                class="btn btn-sm mb-2 break-time"
                                                                data-key="{{ $key }}"
                                                                style="background: rgba(220, 252, 231, 1); color: #28a745; font-weight: 600;">
                                                            <i class="fa-solid fa-utensils"></i> 14:00
                                                        </button>
                                                        <!-- Smoking break / ծխելու ընդմիջում -->
                                                        <button type="button"
                                                                class="btn btn-sm mb-2 smoke-time"
                                                                data-key="{{ $key }}"
                                                                style="background: rgba(254, 243, 198, 1); color: #ffc107; font-weight: 600;">
                                                            <i class="fa-solid fa-smoking"></i> 13:00
                                                        </button>
                                                        </div>
                                                </div>
                                                <div class="col-md-12 break-time-container mt-2">

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
