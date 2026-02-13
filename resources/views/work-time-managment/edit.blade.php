@extends('layouts.app')
@section('page-script')
    <script src="{{ asset('assets/js/work-time-managment_edit.js') }}"></script>
@endsection
@section('style')
background: rgba(69, 85, 108, 1);

@endsection
@section('content')

    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>Աշխատանքային ժամանակի խմբագրում </h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('schedule.work-time-list')}}">Ժամանակացույց</a></li>
                        <li class="breadcrumb-item active">Խմբագրել</li>
                    </ol>
                </nav>

            </div>
        </div><!-- End Page Title -->

        <form action="{{ route('schedule.work-time-update',$data->id) }}" method="post">
            @csrf
             @method('put')
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
                                            {{-- {{ dd($data->name) }} --}}
                                            <div>
                                                <label for="inputCity" class="form-label ">Անվանում</label>
                                                <input type="text"
                                                        name="name"
                                                        class="form-control"
                                                        id="name"
                                                        value="{{ $data->name ?? old('name')}}"
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
                                                    <div class="form-check form-switch" style="margin: 5px 0 0 -60px">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="status"
                                                             value="1"
                                                                {{ old('status', $data->status) ? 'checked' : '' }}

                                                            >
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class=" col-8 pagetitle d-flex justify-content-end  mb-3 ">
                                <a  id="copyToOthersBtn"
                                    class="d-none ml-5"
                                    href="javascript:void(0)"
                                    data-bs-toggle="modal"
                                    data-bs-target="#smallModal"
                                    style="color: rgba(21, 93, 252, 1); margin-right:60px";
"
                                 >Տարածել շաբաթվա օրերի վրա</a>
                            </div>
                            @if (
                                !$errors->has('week_days.0.day_time') &&
                                !$errors->has('week_days.1.day_time') &&
                                !$errors->has('week_days.2.day_time') &&
                                !$errors->has('week_days.3.day_time') &&
                                !$errors->has('week_days.4.day_time') &&
                                !$errors->has('week_days.5.day_time') &&
                                !$errors->has('week_days.6.day_time')
                                 )

                                  @error('week_days')
                                <div class="col-8 alert alert-danger mt-2">{{ $message }}</div>
                            @enderror

                            @endif

                            @php
                                $items_old = old("week_days",[]);
                                // dump($items_old);
                                $scheduleDetailsByDay = $data->schedule_details->keyBy('week_day');
                                $smokingDetailsByDay = $data->client_schedule_smokes->groupBy('week_day');

                            @endphp
  {{-- {{dump($items_old,'444') }} ; --}}
                            @foreach ($weekdays  as $key => $week )
                            {{-- {{ dump($key) }} --}}

                                @php
                                    $detail = $scheduleDetailsByDay[$week] ?? null;
                                    $smokingDetail = $smokingDetailsByDay [$week] ?? null;
                                    // dump($smokingDetail);
                                    // dump($detail);
                                    // dump($detail ? $detail->break_start_time : '' );
                                    // dump($detail->day_break_start_time)
                                @endphp

                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row mt-3 day-row" data-day="{{ $key }}">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="fw-bold">{{ $week }}</h6>
                                                    <input type="hidden" name="week_days[{{ $key }}][week_day]" value="{{ $week }}">
                                                </div>


                                                    {{-- @if ((array_key_exists('day_start_time', $items_old[$key]))) --}}

                                                        <div class="col-md-4">
                                                            <label for="inputCity" class="form-label">Աշխատանքային ժամի սկիզբ</label>
                                                            <div class="input-group">

                                                                <input type="time"
                                                                    name="week_days[{{ $key }}][day_start_time]"
                                                                    class="form-control border-start-0 start-time"
                                                                    placeholder="09:00"
                                                                    value="{{ old('week_days.' . $key . '.day_start_time', $detail?->day_start_time) }}"
                                                                    >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="inputCity" class="form-label">Աշխատանքային ժամի ավարտ</label>
                                                            <div class="input-group">

                                                                <input type="time"
                                                                    name="week_days[{{ $key }}][day_end_time]"
                                                                    class="form-control border-start-0 end-time"
                                                                    placeholder="09:00"
                                                                    {{-- $detail ? $detail->day_end_time : ''  --}}
                                                                    value="{{old('week_days.'. $key .'day_end_time', $detail?->day_end_time) }}">
                                                            </div>
                                                        </div>
                                                    {{-- @endif --}}

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
                                                            <i class="fa-solid fa-smoking"
                                                                style="color: rgba(187, 77, 0, 1)"
                                                            ></i>
                                                            <span style="color: rgba(187, 77, 0, 1)"> Ծխելու ժամ</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                 @error('week_days.'.$key.'.day_time')
                                                    <div class="text-danger small mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                <div class="col-md-12 break-time-container mt-3">

                                                    @if ($detail?->break_start_time)
                                                        <x-breake-section
                                                        :key="$key"
                                                        :break_start_time="$detail->break_start_time"
                                                        :break_end_time="$detail->break_end_time"
                                                    />

                                                    @else
                                                     @if(array_key_exists($key, $items_old))

                                                        @if ((array_key_exists('break_start_time', $items_old[$key])) ||
                                                            (array_key_exists('break_end_time', $items_old[$key])
                                                        ))
                                                            <x-breake-section
                                                                :key="$key"
                                                            />
                                                        @endif
                                                    @endif
                                                    @endif


                                                </div>
                                                <div class="col-md-12 smoking-time-container">

                                                    <x-smoke-section
                                                                :key="$key"
                                                                :smokingDetail="$smokingDetail ?? collect()"
                                                    />
                                                       @if ( isset($items_old[$key]['smoke_break']))
                                                            @foreach ($items_old[$key]['smoke_break'] as $i=>$s )
                                                                    @if(!($smokingDetail?->has($i)))

                                                                    <div class="row g-2 p-2 d-flex smoking-time-block align-items-center mt-2"
                                                                         style="background: rgba(255, 251, 235, 1); border:2px solid rgba(254, 230, 133, 1); border-radius:8px">
                                                                        <div class="col-3 d-flex align-items-center py-1">
                                                                            <button type="button" class="btn btn-sm " >
                                                                                <i class="fa-solid fa-smoking ms-2"></i> <span class="ms-2 fw-bold">Ծխելու ժամ</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                                                                            <label class="form-label small">Սկիզբ</label>
                                                                            <input type="time"
                                                                                class="form-control smoke-start"
                                                                                name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_start_time]"
                                                                                value="{{ old('week_days.'.$key.'.smoke_break.'. $i .'.smoke_start_time') }}"
                                                                                >
                                                                        </div>

                                                                         <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                                                                            <label class="form-label small">Ավարտ</label>
                                                                            <input type="time"
                                                                                    class="form-control smoke-end"
                                                                                    name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_end_time]"
                                                                                    value="{{ old('week_days.'.$key.'.smoke_break.'. $i .'.smoke_end_time') }}"
                                                                                >
                                                                        </div>
                                                                        <div class="col-1 d-flex align-items-center justify-content-center py-1">
                                                                            <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
                                                                        </div>
                                                                    </div>
                                                                    @error('week_days.' . $key . '.smoke_break.' . $i)
                                                                        <div class="text-danger small mt-1 smoke-error">
                                                                        {{ $message }}
                                                                    </div>
                                                                    @enderror
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            @endforeach


                            <div class="col-12">
                               <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">Պահպանել</button>
                                </div>

                            </div>

                        </div><!-- End Left side columns -->
                    </div>
            </section>
        </form>
    </main>

@endsection
<x-work-time-alert-modal :weekdays="$weekdays" />

