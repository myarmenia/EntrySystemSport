$(document).ready(function () {
    let sourceDayRow = null; // day from where we copy
    let selectedDays = [];

    let lastChangedDayRow = null;

    const $copyBtn = $("#copyToOthersBtn");
    const $modal = $("#smallModal");

    // ================== FUNCTION =====================
    function isDayFilled($row) {
        const start = $row.find(".start-time").val();
        const end = $row.find(".end-time").val();
        return start !== "" || end !== "";
    }


    function collectDayData($row) {

        const workStart = $row.find(".start-time").val();
        const workEnd = $row.find(".end-time").val();

        const $breakBlock = $row.find(".break-time-block");
        const breakData = $breakBlock.length
            ? {
                  start: $breakBlock.find(".break-start").val(),
                  end: $breakBlock.find(".break-end").val(),
              }
            : null;

        const smokingData = [];
        $row.find(".smoking-time-block").each(function () {
            smokingData.push({
                start: $(this).find(".smoke-start").val(),
                end: $(this).find(".smoke-end").val(),
            });
        });

        return {
            work: { start: workStart, end: workEnd },
            break: breakData,
            smoking: smokingData,
        };
    }

    function applyDayData($row, data) {
        console.log($row, "row", data, "data", "applyDayData");
        $row.find(".start-time").val(data.work.start);
        $row.find(".end-time").val(data.work.end);

        // Break
        const $breakContainer = $row.find(".break-time-container");
        $breakContainer.empty();
        if (data.break) {
            const html = `
                <div class="row g-2 p-2 break-time-block justify-content-center align-items-center"
                     style="background: rgba(240, 253, 244, 1); border:2px solid rgba(185, 248, 207, 1);border-radius:8px;">
                    <div class="col-4">
                        <button type="button" class="btn btn-sm mb-2" style="background: rgba(220, 252, 231, 1);">
                            <i class="fa-solid fa-utensils"></i> Ընդմիջման ժամ
                        </button>
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Սկիզբ</label>
                        <input type="time" name="week_days[${$row.data("day")}][break_start_time]" class="form-control break-start" value="${data.break.start}">
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Ավարտ</label>
                        <input type="time" name="week_days[${$row.data("day")}][break_end_time]" class="form-control break-end" value="${data.break.end}">
                    </div>
                    <div class="col-1 mt-4">
                        <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
                    </div>
                </div>`;
            $breakContainer.html(html);
        }

        // Smoking
        const $smokeContainer = $row.find(".smoking-time-container");
        $smokeContainer.empty();
        $.each(data.smoking, function (i, sm) {

            const html = `
            <div class="row g-2 p-2 d-flex smoking-time-block align-items-center mt-2"
                 style="background: rgba(255, 251, 235, 1); border:2px solid rgba(254, 230, 133, 1); border-radius:8px">
                <div class="col-3 d-flex align-items-center py-1">
                    <button type="button" class="btn btn-sm " >
                        <i class="fa-solid fa-smoking ms-2"></i> <span class="ms-2 fw-bold">Ծխելու ժամ</span>
                    </button>
                </div>
                <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Սկիզբ</label>
                    <input type="time" name="week_days[${$row.data("day")}][smoke_break][${i}][smoke_start_time]" class="form-control smoke-start" value="${sm.start}">
                </div>
                <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Ավարտ</label>
                    <input type="time" name="week_days[${$row.data("day")}][smoke_break][${i}][smoke_end_time]" class="form-control smoke-end" value="${sm.end}">
                </div>
                <div class="col-1 d-flex align-items-center justify-content-center py-1">
                    <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
                </div>
            </div>`;
            $smokeContainer.append(html);
        });
    }

    function checkFilledDays() {
        let filledCount = 0;

        $(".day-row").each(function () {
            if (isDayFilled($(this))) {
                filledCount++;
            }
        });

        if (filledCount >= 1) {
            $copyBtn.removeClass("d-none");
        } else {
            $copyBtn.addClass("d-none");
            sourceDayRow = null;
            lastChangedDayRow = null;
        }
    }

    function highlightSourceDay() {

        $("#smallModal .week-day-item").removeClass(
            "bg-primary text-white border-primary",
        );
        if (sourceDayRow) {
            console.log(sourceDayRow)
            $(
                `#smallModal .week-day-item[data-day="${sourceDayRow.data("day")}"]`,
            ).addClass("bg-primary text-white border-primary");
        }
    }

    // ================== EVENTS ======================

    // Input change
    $(document).on("change", ".day-row .start-time, .day-row .end-time", function () {
        const $row = $(this).closest(".day-row");

        // եթե տվյալ օրը դեռ ամբողջությամբ լրացված չէ → source չդարձնել
        if (!isDayFilled($row)) {
            // ստուգում ենք՝ գոնե մեկ ամբողջությամբ լրացված օր կա՞
            checkFilledDays();
            return;
        }
        // if fill it will become source
        console.log($row)
        lastChangedDayRow = $row;
        sourceDayRow = $row;
        checkFilledDays();
    });

    // Break button
    $(document).on("click", ".break-time", function () {
        const $row = $(this).closest(".day-row");
        const key = $row.data("day");
        const $container = $row.find(".break-time-container");
        if ($container.find(".break-time-block").length) return;
        const html = `
        <div class="row g-2 p-2 d-flex break-time-block align-items-center"
             style="background: rgba(240, 253, 244, 1); border:2px solid rgba(185, 248, 207, 1);border-radius:8px;">
            <div class="col-3 d-flex align-items-center py-1">
                <button type="button" class="btn btn-sm">
                    <i class="fa-solid fa-utensils"></i>
                    <span class="ms-2 fw-bold">Ընդմիջման ժամ</span>
                </button>
            </div>
            <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Սկիզբ</label>
                <input type="time" name="week_days[${key}][break_start_time]" class="form-control break-start">
            </div>
            <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Ավարտ</label>
                <input type="time" name="week_days[${key}][break_end_time]" class="form-control break-end">
            </div>
            <div class="col-1 d-flex align-items-center justify-content-center py-1">
                <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
            </div>
        </div>`;
        $container.html(html);
    });

    // Smoke button
    $(document).on("click", ".smoke-time", function () {

        const $row = $(this).closest(".day-row");
        const key = $row.data("day");
        const $container = $row.find(".smoking-time-container");
        let smokeCount = $container.find(".smoking-time-block").length;

        const html = `
        <div class="row g-2 p-2 d-flex smoking-time-block align-items-center mt-2"
             style="background: rgba(255, 251, 235, 1); border:2px solid rgba(254, 230, 133, 1); border-radius:8px">
             <div class="col-3 d-flex align-items-center py-1">
                <button type="button" class="btn btn-sm ">
                    <i class="fa-solid fa-smoking ms-2 "></i> <span class="ms-2 fw-bold">Ծխելու ժամ</span>
                </button>
            </div>

            <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Սկիզբ</label>
                <input type="time" name="week_days[${key}][smoke_break][${smokeCount}][smoke_start_time]" class="form-control smoke-start">
            </div>
            <div class="col-4 d-flex flex-column justify-content-center" style="margin: 0 0 18px 0;font-size: 14px">
                <label class="form-label small">Ավարտ</label>
                <input type="time" name="week_days[${key}][smoke_break][${smokeCount}][smoke_end_time]" class="form-control smoke-end">
            </div>
            <div class="col-1 d-flex align-items-center justify-content-center py-1">
                <a class="text-danger delete fw-bold ms-3" style="cursor:pointer;">x</a>
            </div>
        </div>`;
        $container.append(html);
    });

    // Delete
    $(document).on("click", ".delete", function () {
        const $block = $(this).closest(
                ".break-time-block, .smoking-time-block"
        );

        $block.next(".break-error, .smoke-error").remove();
        $(this).closest('.row').remove();
    });

    // Copy button click
    $copyBtn.on("click", function () {
        highlightSourceDay();
        $modal.modal("show");
    });

    // Select weekday in modal
    $(document).on("click", "#smallModal .week-day-item", function () {
        const day = $(this).data("day");
        if ($(this).hasClass("bg-primary")) {
            $(this).removeClass("bg-primary text-white border-primary");
            selectedDays = selectedDays.filter((d) => d !== day);
        } else {
            $(this).addClass("bg-primary text-white border-primary");
            selectedDays.push(day);
        }
        console.log(selectedDays);
    });

    // Apply button inside modal (required <button id="applyDays">Spread</button>  in modal)
    $(document).on("click", "#applyDays", function () {

        if (!sourceDayRow) return;
        const data = collectDayData(sourceDayRow);
        console.log(data);
        console.log(selectedDays, "selectedDays");

        selectedDays.forEach((dayKey) => {
            const $targetRow = $(`.day-row[data-day="${dayKey}"]`);
            console.log($targetRow, "targetRow");
            if ($targetRow.length && !$targetRow.is(sourceDayRow)) {
                applyDayData($targetRow, data);
            }
        });
        $modal.modal("hide");
    });

});
