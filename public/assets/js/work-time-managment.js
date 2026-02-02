$(document).ready(function () {

    $('#applyToAll').on('click', function () {



        // Monday block
        let $monday = $('.day-row[data-day="Monday"]');
        console.log($monday)

        let startTime = $monday.find('.start-time').val();
        let endTime   = $monday.find('.end-time').val();
        console.log(startTime,"startTime",endTime)

        // // break buttons (ուտել, ծխել և այլն)
        // let $mondayBreaks = $monday.find('button');

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

            // set breaks
            // $day.find('button').each(function (index) {
            //     if ($mondayBreaks.eq(index).length) {
            //         $(this).html($mondayBreaks.eq(index).html());
            //     }
            // });

        });

    });

});
