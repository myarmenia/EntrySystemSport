$(document).ready(function () {

    $('#applyToAll').on('click', function () {
        // Monday block
        let $monday = $('.day-row[data-day="Monday"]');
        console.log($monday,4444)

        let startTime = $monday.find('.start-time').val();
        let endTime   = $monday.find('.end-time').val();
        console.log(startTime,"startTime",endTime)

        // loop all days
        $('.day-row').each(function () {
            let $day = $(this);
            // skip Monday
            if ($day.data('day') === 'Monday') {
                return;
            }
            // set times
            $day.find('.start-time').val(startTime);
            $day.find('.end-time').val(endTime);

        });

    });

// ============ break-time ===================
    $(document).on('click', '.break-time', function () {
        let key = $(this).attr('data-key')
        console.log(key,111)
        let container = $(this)
            .closest('.day-row')
            .find('.break-time-container');
            console.log(container)
        // чтобы не добавлялся второй раз
        if (container.find('.break-time-block').length) {
            return;
        }

        let html =
            `   <div class="row g-2 p-2  break-time-block justify-content-center align-items-center"  style="background: rgba(220, 252, 231, 1); border:1px solid #28a745;border-radius:8px;">
                                                     <div class="col-3">
                                                         <button type="button" class="btn btn-sm mb-2"
                                                        style="background: rgba(220, 252, 231, 1);">
                                                        <i class="fa-solid fa-utensils"></i> 14:00
                                                    </button>
                                                     </div>
                                                    <div class="col-3">
                                                        <label class="form-label small">Սկիզբ</label>
                                                        <input type="time"
                                                            name="week_days[${$key }][break_start_time]"
                                                            class="form-control break-start">
                                                    </div>
                                                    <div class="col-3" >
                                                        <label class="form-label small">Ավարտ</label>
                                                        <input type="time"
                                                                name="week_days[${$key }][break_end_time]"
                                                                class="form-control break-end"
                                                         >
                                                    </div>
                                                    <div class="col-3 text-center " >
                                                        <a class="text-danger delate fw-bold">x</a>
                                                    </div>
                                                </div>`;

        container.html(html)
    });
// ============ smoking-time ===================
    $(document).on('click', '.smoke-time', function () {
        let container_smoke = $(this)
            .closest('.day-row')
            .find('.smoking-time-container');
            console.log(container_smoke,111)

        let html =
            `<div class="row g-2 p-2 mt-2 smoking-time-block justify-content-center align-items-center"  style="background: rgba(254, 243, 198, 1); border:1px solid #ffc107;border-radius:8px;">
                                                     <div class="col-3">
                                                         <button type="button" class="btn btn-sm mb-2"
                                                        style="background: rgba(254, 243, 198, 1);">
                                                        <i class="fa-solid fa-smoking"></i> 14:00
                                                    </button>
                                                     </div>
                                                    <div class="col-3">
                                                        <label class="form-label small">Սկիզբ</label>
                                                        <input type="time" class="form-control break-start">
                                                    </div>
                                                    <div class="col-3" >
                                                        <label class="form-label small">Ավարտ</label>
                                                        <input type="time" class="form-control break-end">
                                                    </div>
                                                    <div class="col-3 text-center " >
                                                        <a class="text-danger fw-bold delate">x</a>
                                                    </div>
                                                </div>`;


        container_smoke.append(html)
    });

     $(document).on('click', '.delate', function () {
        $(this).closest('.row').remove();

     })

});
