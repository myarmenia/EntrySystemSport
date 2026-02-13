@props(['key', 'break_start_time', 'break_end_time'])
{{-- <div class="row g-2 p-2 break-time-block justify-content-center align-items-center"
    style="background: rgba(240, 253, 244, 1); border:2px solid rgba(185, 248, 207, 1);border-radius:8px;">
    <div class="col-4" style="border:1px solid red">
        <button type="button" class="btn btn-sm mb-2" >
            <i class="fa-solid fa-utensils"></i> <span class="me-3">Ընդմիջման ժամ</span>
        </button>
    </div>
    <div class="col-3" style="border:1px solid red">
        <label class="form-label small">Սկիզբ</label>
        <input type="time" class="form-control break-start" name="week_days[{{ $key }}][break_start_time]"
            value="{{ old('week_days.' . $key . '.break_start_time', $break_start_time ?? null) }}">
    </div>
    <div class="col-3" style="border:1px solid red">
        <label class="form-label small">Ավարտ</label>
        <input type="time" class="form-control break-end" name="week_days[{{ $key }}][break_end_time]"
            value="{{ old('week_days.' . $key . '.break_end_time', $break_end_time ?? null) }}">
    </div>
    <div class="col-1 mt-4" style="border:1px solid red">
        <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
    </div>
</div> --}}
<div class="row g-2 p-2 d-flex break-time-block align-items-center"
     style="background: rgba(240, 253, 244, 1);
            border: 2px solid rgba(185, 248, 207, 1);
            border-radius: 8px;">

    <!-- Break label -->
    <div class="col-3 d-flex align-items-center py-1">
        <button type="button" class="btn btn-sm">
            <i class="fa-solid fa-utensils"></i>
            <span class="ms-2 fw-bold">Ընդմիջման ժամ</span>
        </button>
    </div>

    <!-- Start time -->
    <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
        <label class="form-label small">Սկիզբ</label>
        <input type="time"
               class="form-control break-start"
               name="week_days[{{ $key }}][break_start_time]"
               value="{{ old('week_days.' . $key . '.break_start_time', $break_start_time ?? null) }}">
    </div>

    <!-- End time -->
    <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
        <label class="form-label small">Ավարտ</label>
        <input type="time"
               class="form-control break-end"
               name="week_days[{{ $key }}][break_end_time]"
               value="{{ old('week_days.' . $key . '.break_end_time', $break_end_time ?? null) }}">
    </div>

    <!-- Delete -->
    <div class="col-1 d-flex align-items-center justify-content-center py-1">
        <a class="text-danger delete fw-bold " style="cursor:pointer;">x</a>
    </div>
</div>


@error('week_days.' . $key . '.break_time')
    <div class="text-danger  small mt-1 break-error">
        {{ $message }}
    </div>
@enderror

