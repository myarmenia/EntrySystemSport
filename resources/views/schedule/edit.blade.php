@extends('layouts.app')

@section('content')
<main id="main" class="main">
  <section class="section">
    <div class="row">
      <div class="col-lg-6">

        {{-- ScheduleName edit --}}
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
                  <li class="breadcrumb-item">
                    <a href="{{ route('schedule.list') }}">Հերթափոխերի ցանկ</a>
                  </li>
                  <li class="breadcrumb-item active">Խմբագրել</li>
                </ol>
              </nav>
            </h5>

            <form action="{{ route('schedule.update', $data->id) }}" method="post" enctype="multipart/form-data">
              @csrf
              @method('put')

              @if (Auth::user()->hasRole(["client_admin","client_admin_rfID",'manager','client_sport']))
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Հերթափոխի անուն</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ old('name', $data->name) }}">
                    @error("name")
                      <div class="mt-2">
                        <p class="text-danger fs-6 mb-0">{{ $message }}</p>
                      </div>
                    @enderror
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Ակտիվացում</label>
                  <div class="col-sm-9">
                    <div class="form-check form-switch">
                      <input class="form-check-input"
                             type="checkbox"
                             name="status"
                             value="1"
                             {{ old('status', $data->status) ? 'checked' : '' }}>
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

        {{-- ScheduleDetails edit --}}
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-3">Օրերի ժամեր</h5>

            <form action="{{ route('schedule_details.update', $data->id) }}" method="post" enctype="multipart/form-data">
              @csrf
              @method('put')

              @foreach ($weekdays as $key => $week)
                @php
                  // DB-ից enabled (եթե row չկա՝ 0)
                  $dbEnabled = (int)($data->schedule_details[$key]->enabled ?? 0);

                  // validation error-ից հետո old enabled-ը պահի, հակառակ դեպքում՝ dbEnabled
                  $enabled = (int) old("week_days.$key.enabled", $dbEnabled);

                  // value-ները
                  $dayStart = old("week_days.$key.day_start_time", $data->schedule_details[$key]->day_start_time ?? '');
                  $dayEnd   = old("week_days.$key.day_end_time", $data->schedule_details[$key]->day_end_time ?? '');
                  $brStart  = old("week_days.$key.break_start_time", $data->schedule_details[$key]->break_start_time ?? '');
                  $brEnd    = old("week_days.$key.break_end_time", $data->schedule_details[$key]->break_end_time ?? '');
                @endphp

                <div class="mb-2">

                  {{-- hidden fields --}}
                  <input type="hidden" name="week_days[{{ $key }}][schedule_name_id]" value="{{ $data->id }}">
                  <input type="hidden" name="week_days[{{ $key }}][week_day]" value="{{ $week }}">

                  {{-- Week day + checkbox --}}
                  <div class="row mb-3 mt-3 align-items-center">
                    <label class="col-sm-3 col-form-label">Շաբաթվա օր</label>
                    <div class="col-sm-9 d-flex align-items-center gap-3">
                      <input type="text" class="form-control" style="max-width: 220px;" value="{{ $week }}" readonly>

                      <div class="form-check">
                        {{-- unchecked դեպքում էլ ուղարկի 0 --}}
                        <input type="hidden" name="week_days[{{ $key }}][enabled]" value="0">

                        <input class="form-check-input js-day-toggle"
                               type="checkbox"
                               id="day_toggle_{{ $key }}"
                               name="week_days[{{ $key }}][enabled]"
                               value="1"
                               {{ $enabled ? 'checked' : '' }}>

                        <label class="form-check-label" for="day_toggle_{{ $key }}">
                          Փոխել այս օրը
                        </label>
                      </div>
                    </div>
                  </div>

                  {{-- Day start --}}
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Աշխատանքային օրվա սկիզբ</label>
                    <div class="col-sm-9">
                      <input type="time"
                             class="form-control js-time-{{ $key }}"
                             name="week_days[{{ $key }}][day_start_time]"
                             value="{{ $dayStart }}"
                             {{ $enabled ? '' : 'disabled' }}>
                      @error("week_days.$key.day_start_time")
                        <div class="mt-2">
                          <p class="text-danger fs-6 mb-0">{{ $message }}</p>
                        </div>
                      @enderror
                    </div>
                  </div>

                  {{-- Day end --}}
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Աշխատանքային օրվա ավարտ</label>
                    <div class="col-sm-9">
                      <input type="time"
                             class="form-control js-time-{{ $key }}"
                             name="week_days[{{ $key }}][day_end_time]"
                             value="{{ $dayEnd }}"
                             {{ $enabled ? '' : 'disabled' }}>
                      @error("week_days.$key.day_end_time")
                        <div class="mt-2">
                          <p class="text-danger fs-6 mb-0">{{ $message }}</p>
                        </div>
                      @enderror
                    </div>
                  </div>

                  {{-- Break start --}}
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Ընդմիջման սկիզբ</label>
                    <div class="col-sm-9">
                      <input type="time"
                             class="form-control js-time-{{ $key }}"
                             name="week_days[{{ $key }}][break_start_time]"
                             value="{{ $brStart }}"
                             {{ $enabled ? '' : 'disabled' }}>
                      @error("week_days.$key.break_start_time")
                        <div class="mt-2">
                          <p class="text-danger fs-6 mb-0">{{ $message }}</p>
                        </div>
                      @enderror
                    </div>
                  </div>

                  {{-- Break end --}}
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Ընդմիջման ավարտ</label>
                    <div class="col-sm-9">
                      <input type="time"
                             class="form-control js-time-{{ $key }}"
                             name="week_days[{{ $key }}][break_end_time]"
                             value="{{ $brEnd }}"
                             {{ $enabled ? '' : 'disabled' }}>
                      @error("week_days.$key.break_end_time")
                        <div class="mt-2">
                          <p class="text-danger fs-6 mb-0">{{ $message }}</p>
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

      </div>
    </div>
  </section>
</main>
@endsection

{{-- JS: enable/disable fields per day --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.js-day-toggle').forEach((cb) => {
    const key = cb.id.replace('day_toggle_', '');

    const toggle = () => {
      document.querySelectorAll('.js-time-' + key).forEach((inp) => {
        inp.disabled = !cb.checked;
      });
    };

    cb.addEventListener('change', toggle);
    toggle(); // initial
  });
});
</script>
