$(document).ready(function () {

    $('#applyToAll').on('click', function () {
        // Monday block
        let $monday = $('.day-row[data-day="Monday"]');
        console.log($monday,4444)

        let startTime = $monday.find('.start-time').val();
        let endTime   = $monday.find('.end-time').val();
        console.log(startTime,"startTime",endTime)
        if(startTime=='' && endTime==''){
            $(this).attr('data-bs-toggle', 'modal');
            $(this).attr('data-bs-target', '#smallModal');
        }

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
                                                     <div class="col-4">
                                                         <button type="button" class="btn btn-sm mb-2"
                                                        style="background: rgba(220, 252, 231, 1);">
                                                        <i class="fa-solid fa-utensils"></i> Ընդմիջման ժամ
                                                    </button>
                                                     </div>
                                                    <div class="col-3">
                                                        <label class="form-label small">Սկիզբ</label>
                                                        <input type="time"
                                                            name="week_days[${key }][break_start_time]"
                                                            class="form-control break-start">
                                                    </div>
                                                    <div class="col-3" >
                                                        <label class="form-label small">Ավարտ</label>
                                                        <input type="time"
                                                                name="week_days[${key }][break_end_time]"
                                                                class="form-control break-end"
                                                         >
                                                    </div>
                                                    <div class="col-1 mt-4 " >
                                                        <a class="text-danger delate fw-bold ms-3" style="cursor: pointer;">x</a>
                                                    </div>
                                                </div>`;

        container.html(html)
    });
// ============ smoking-time ===================
let count = 0
    $(document).on('click', '.smoke-time', function () {
        count++;
        console.log(count)
        let key = $(this).attr('data-key')
        let container_smoke = $(this)
            .closest('.day-row')
            .find('.smoking-time-container');
            console.log(container_smoke,111)

        let html =
            `<div class="row g-2 p-2 mt-2 smoking-time-block justify-content-center align-items-center"  style="background: rgba(254, 243, 198, 1); border:1px solid #ffc107;border-radius:8px;">
                                                     <div class="col-4">
                                                         <button type="button" class="btn btn-sm mb-2"
                                                        style="background: rgba(254, 243, 198, 1);">
                                                        <i class="fa-solid fa-smoking"></i> Ծխելու ժամ
                                                    </button>
                                                     </div>
                                                    <div class="col-3">
                                                        <label class="form-label small">Սկիզբ</label>
                                                        <input type="time"
                                                               class="form-control smoke-start"
                                                                name="week_days[${key }][smoke_break][${count}][smoke_start_time]"
                                                               >
                                                    </div>
                                                    <div class="col-3" >
                                                        <label class="form-label small">Ավարտ</label>
                                                        <input type="time"
                                                               class="form-control smoke-end"
                                                               name="week_days[${key }][smoke_break][${count}][smoke_end_time]"
                                                               >
                                                    </div>
                                                    <div class="col-1 mt-4 " >
                                                        <a class="text-danger delate fw-bold ms-3" style="cursor: pointer;">x</a>
                                                    </div>
                                                </div>`;


        container_smoke.append(html)
    });

     $(document).on('click', '.delate', function () {
        $(this).closest('.row').remove();

     })
// ================================
    document.addEventListener('input', function (e) {

            // լսում ենք միայն start / end input-ները
            if (!e.target.matches('.start-time, .end-time')) {
                return;
            }

            const dayRow = e.target.closest('.day-row');
            console.log(dayRow)

            // եթե տվյալ օրը լրացված է
            if (isDayFilled(dayRow)) {
                console.log('Օրը լրացված է ✅');
                // console.log(dayRow,444)
            }

            checkFilledDays(); // ստուգում ենք՝ քանի օր է լրացված
        });

        // ստուգում է՝ մեկ օրը լրացված է, թե ոչ
        function isDayFilled(dayRow) {
            const start = dayRow.querySelector('.start-time')?.value;
            const end   = dayRow.querySelector('.end-time')?.value;

            return !!(start && end);
        }

        // հաշվում է՝ քանի օր է լրացված
        function checkFilledDays() {
            let filledCount = 0;
            console.log('checkFilledDays')

            document.querySelectorAll('.day-row').forEach(row => {
                if (isDayFilled(row)) {
                    filledCount++;
                    console.log(filledCount)
                }
            });

            const btn = document.getElementById('copyToOthersBtn');
            console.log(btn)

            if (filledCount >= 1) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }

             const modalEl = document.getElementById('smallModal');
             const modal = new bootstrap.Modal(modalEl);

                btn.addEventListener('click', function () {
                    modal.show();
                });
        }

        let selectedDays = [];

document.addEventListener('click', function (e) {
    const item = e.target.closest('.week-day-item');
    if (!item) return;

    const day = item.dataset.day;

    // toggle bootstrap classes
    item.classList.toggle('bg-primary');
    item.classList.toggle('text-white');
    item.classList.toggle('border-primary');

    if (item.classList.contains('bg-primary')) {
        if (!selectedDays.includes(day)) {
            selectedDays.push(day);
        }
    } else {
        selectedDays = selectedDays.filter(d => d !== day);
    }

    console.log(selectedDays);
});


});
