@extends('layouts.app')

@section('content')


   <main id="main" class="main">

    <section class="section">

      <div class="row">
        <div class="col-lg-6">

          <div class="card">

            <div class="card-body">
                @if (session('repeating_token'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('repeating_token') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
              <h5 class="card-title">
                <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="{{ route('schedule.list') }}">Հերթափոխերի ցանկ</a></li>

                      <li class="breadcrumb-item active">Խմբագրել</li>
                    </ol>
                  </nav>
              </h5>

              <!-- General Form Elements -->
              <form action="{{ route('schedule.update',$data->id)}}" method="post" enctype="multipart/form-data">
                @method('put')
                    @if (Auth::user()->hasRole(["client_admin","client_admin_rfID",'manager','client_sport']))

                        <div class="row mb-3">
                            <label for="inputEmail" class="col-sm-3 col-form-label">Հերթափոխի անուն </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" value="{{ $data->name }}">
                                @error("name")
                                    <div class="mb-3 row ">
                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                        </p>
                                    </div>
                                @enderror


                            </div>
                        </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ակտիվացում </label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            name="status" value="{{ $data->status }}" {{ $data->status == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                        <div class="row mt-3">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary">Պահպանել</button>
                            </div>
                        </div>
                    @endif

              </form>

            </div>

          </div>
          <div class="card">
            <form action="{{ route('schedule_details.update',$data->id)}}" method="post" enctype="multipart/form-data">
                @method('put')
                @foreach ($weekdays as $key => $week)
                        <div class="card-body">
                        <!-- General Form Elements -->



                        <input type="hidden" class="form-control" name="week_days[{{ $key }}][schedule_name_id]{{ $data->id }}" value={{$data->id }}>
                                        <div class="row mb-3 mt-3">
                                            <label for="inputEmail" class="col-sm-3 col-form-label ">Շաբաթվա օր </label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="week_days[{{ $key }}][week_day]{{ $week }}" value="{{$week  }}">
                                                @error("week_days.$key.week_day")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror


                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputEmail" class="col-sm-3 col-form-label">Աշխատանքային օրվա սկիզբ </label>
                                            <div class="col-sm-9">

                                                <input type="time" class="form-control"
                                                                    name="week_days[{{ $key }}][day_start_time]"

                                                                    {{-- value="{{ isset($data->schedule_details[$key]->day_start_time) ? $data->schedule_details[$key]->day_start_time : old("week_days. $key.day_start_time") }}" --}}
                                                                    value="{{ isset($data->schedule_details[$key]->day_start_time)
                                                                    ? $data->schedule_details[$key]->day_start_time
                                                                    : old("week_days.$key.day_start_time") }}"
                                                                    >

                                                @error("week_days.$key.day_start_time")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror


                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputEmail" class="col-sm-3 col-form-label">Աշխատանքային օրվա ավարտ </label>
                                            <div class="col-sm-9">
                                                <input type="time" class="form-control"
                                                       name="week_days[{{ $key }}][day_end_time]"
                                                       value="{{ isset($data->schedule_details[$key]->day_end_time) ?
                                                                 $data->schedule_details[$key]->day_end_time :
                                                                 old("week_days.$key.day_end_time")  }}"
                                                         >
                                                @error("week_days.$key.day_end_time")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror


                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputEmail" class="col-sm-3 col-form-label">Ընդմիջման սկիզբ </label>
                                            <div class="col-sm-9">

                                                <input type="time" class="form-control"
                                                                   name="week_days[{{ $key }}][break_start_time]"
                                                                   value="{{ isset($data->schedule_details[$key]->break_start_time) ?
                                                                             $data->schedule_details[$key]->break_start_time :
                                                                             old("week_days.$key.break_start_time")  }}"
                                                                    >

                                                @error("week_days.$key.break_start_time")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror


                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputEmail" class="col-sm-3 col-form-label">Ընդմիջման ավարտ</label>
                                            <div class="col-sm-9">
                                                <input type="time" class="form-control"
                                                                   name="week_days[{{ $key }}][break_end_time]"
                                                                    value = {{ isset($data->schedule_details[$key]->break_end_time) ?
                                                                                $data->schedule_details[$key]->break_end_time :
                                                                                old("week_days.$key.break_end_time") }}

                                                                        >

                                                @error("week_days.$key.break_end_time")
                                                    <div class="mb-3 row ">
                                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                                        </p>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                        </div>
                        <hr/>

                @endforeach

                <div class="row mt-3">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Պահպանել</button>
                    </div>
                </div>
            </form>
          </div>

        </div>
    </section>

  </main><!-- End #main -->


@endsection
