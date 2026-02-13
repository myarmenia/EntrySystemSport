@props(['key', 'smokingDetail'])
@foreach ($smokingDetail as $i=>$s )

    <div class="row g-2 p-2 d-flex smoking-time-block align-items-center mt-2"
        style="background: rgba(255, 251, 235, 1); border:2px solid rgba(254, 230, 133, 1); border-radius:8px">

        <div class="col-3 d-flex align-items-center py-1">
            <button type="button" class="btn btn-sm " >
                <i class="fa-solid fa-smoking ms-2"></i> <span class="ms-2 fw-bold">Ծխելու ժամ</span>
            </button>
        </div>
        <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Սկիզբ</label>
            <input type="time" class="form-control smoke-start"
                name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_start_time]"
                value="{{ old('week_days.' . $key . '.smoke_break.' . $i . '.smoke_start_time',$s->smoke_start_time ?? null) }}">
        </div>

        <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Ավարտ</label>
            <input type="time" class="form-control smoke-end"
                name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_end_time]"
                value="{{ old('week_days.' . $key . '.smoke_break.' . $i . '.smoke_end_time',$s->smoke_end_time ?? null) }}">
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
 @endforeach


