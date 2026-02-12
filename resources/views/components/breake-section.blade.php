@props(['key', 'break_start_time', 'break_end_time'])
<div class="row g-2 p-2 break-time-block justify-content-center align-items-center"
    style="background: rgba(220, 252, 231, 1); border:1px solid #28a745;border-radius:8px;">
    <div class="col-4">
        <button type="button" class="btn btn-sm mb-2" style="background: rgba(220, 252, 231, 1);">
            <i class="fa-solid fa-utensils"></i> Ընդմիջման ժամ
        </button>
    </div>
    <div class="col-3">
        <label class="form-label small">Սկիզբ</label>
        <input type="time" class="form-control break-start" name="week_days[{{ $key }}][break_start_time]"
            value="{{ old('week_days.' . $key . '.break_start_time', $break_start_time) }}">
    </div>
    <div class="col-3">
        <label class="form-label small">Ավարտ</label>
        <input type="time" class="form-control break-end" name="week_days[{{ $key }}][break_end_time]"
            value="{{ old('week_days.' . $key . '.break_end_time', $break_end_time) }}">
    </div>
    <div class="col-1 mt-4">
        <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
    </div>
</div>
@error('week_days.' . $key . '.break_time')
    <div class="text-danger  small mt-1 break-error">
        {{ $message }}
    </div>
@enderror
