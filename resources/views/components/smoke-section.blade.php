@props(['key', 'smokingDetail'])
@foreach ($smokingDetail as $i=>$s )
{{-- {{ dd($s) }} --}}
    <div class="row g-2 p-2 mt-2 smoking-time-block justify-content-center align-items-center"
        style="background: rgba(254, 243, 198, 1); border:1px solid #ffc107;border-radius:8px;">
        <div class="col-4">
            <button type="button" class="btn btn-sm mb-2" style="background: rgba(254, 243, 198, 1);">
                <i class="fa-solid fa-smoking"></i> Ծխելու ժամ
            </button>
        </div>
        <div class="col-3">
            <label class="form-label small">Սկիզբ</label>
            <input type="time" class="form-control smoke-start"
                name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_start_time]"
                value="{{ old('week_days.' . $key . '.smoke_break.' . $i . '.smoke_start_time',$s->smoke_start_time) }}">
        </div>

        <div class="col-3">
            <label class="form-label small">Ավարտ</label>
            <input type="time" class="form-control smoke-end"
                name="week_days[{{ $key }}][smoke_break][{{ $i }}][smoke_end_time]"
                value="{{ old('week_days.' . $key . '.smoke_break.' . $i . '.smoke_end_time',$s->smoke_end_time) }}">
        </div>
        <div class="col-1 mt-4">
            <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
        </div>
    </div>
    @error('week_days.' . $key . '.smoke_break.' . $i)
        <div class="text-danger small mt-1 smoke-error">
            {{ $message }}
        </div>
    @enderror
 @endforeach
