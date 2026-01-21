document.addEventListener('DOMContentLoaded', function () {

  // ==================  C A L E N D A R =================================
function calendar() {
    var calendarEl = document.getElementById('calendar');
    const path = window.location.pathname;

        // Разбиваем путь на сегменты
        const segments = path.split('/');

        // Получаем нужный сегмент, в данном случае "2"
        const id = segments[segments.length - 1];


    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'hy-am',
      timeZone: 'UTC',
      themeSystem: 'bootstrap5',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      weekNumbers: true,
      dayMaxEvents: true, // allow "more" link when too many events
      events: `/calendar-data/${id}`,
      eventTimeFormat: {
        hour: '2-digit', //2-digit, numeric
        minute: '2-digit', //2-digit, numeric
        hour12: false //true, false
      },
      slotDuration: '01:00:00'
    });
    calendar.render();

  }

  // ====================== E N D ==================================

  calendar()

  // ==================  Click calendar td and get reservations ============================

  $('body').on('click', '.fc-daygrid-day', function () {
    const path = window.location.pathname;

    // Разбиваем путь на сегменты
    const segments = path.split('/');

    // Получаем нужный сегмент, в данном случае "2"
    const schedule_id = segments[segments.length - 1];

    var reserved_date = $(this).attr('data-date')
    console.log(reserved_date,444)
    $.ajax({
      url: `/get-trainer-schedule-visitors/${schedule_id}/` + reserved_date,
      processData: false,
      contentType: false,
      type: 'get',
      beforeSend: function (x) {
        console.log('befor sebd')
      },
      success: function (response) {

        $('.your-component').html(response);
        $('#show_reservetion').click()
      }

    });

  })
  // ========================== E N D ===========================================================


 




});

