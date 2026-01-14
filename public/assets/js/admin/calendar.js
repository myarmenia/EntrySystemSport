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
    const person_id = segments[segments.length - 1];

    var reserved_date = $(this).attr('data-date')
    $.ajax({
      url: `/get-day-reservations/${person_id}/` + reserved_date,
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


  // =========== store or update educational program reservetion ==================================
  $('body').on('submit', '.reserve', function (e) {

    e.preventDefault()
    var $that = $(this)
    var formData = new FormData($(this)[0]);
    var educational_program_type = 'educational_program'
    var educational_program_id = $(this).find(".educational_program_id").val()
    var method = $(this).attr('method')
    var url = ''

    if (educational_program_id == 'null_id') {
      educational_program_type = 'excursion'

    }

    if (method == 'post') {
      url = '/educational-programs/reserve-store'
    }
    else {
      var reserve_id = $(this).attr('data-id')
      url = '/educational-programs/reserve-update/' + reserve_id
    }

    formData.append("type", educational_program_type)
    console.log(educational_program_id)
    $('.error').html('')

    $.ajax({
      url: url,
      data: formData,
      processData: false,
      contentType: false,
      type: 'post',
      beforeSend: function (x) {
        console.log('befor sebd')
      },
      success: function (data) {
        if (method == 'post') {
          $that.find('.item').val('')

        }
        console.log(555555)
        $that.find('.result_message').html(`<span class=" text-success">Գործողությունը կատարված է</span>`)
        calendar()
        setTimeout(function () {
          $('.result_message').html('');
        }, 2000);
      },
      error: function (data) {
        var errors = data.responseJSON.errors;

        $.each(errors, function (field_name, error) {
          $that.find('[name=' + field_name + ']').after('<span class="error text-strong text-danger">' + error + '</span>')
        })

      }
    });
  })
  // ==================== E N D =======================================================





});

