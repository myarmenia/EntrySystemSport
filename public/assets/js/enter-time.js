$(function () {
    $('.enter_time_item').on('click', function () {
        $('#time').val('')
        $('.text-danger').text('');
      let person_id = $(this).parents('.action').attr('data-person-id')

      let tb_name = $(this).parents('.action').attr('data-tb-name')
      let day = $(this).attr('data-day')
      let date = $(this).attr('data-date')
      let direction = $(this).attr('data-direction')
      let clientId = $(this).attr('data-clientId')
      let enterExitTime =$(this).attr('data-enterExitTime')

    //   console.log(person_id,tb_name,day,date, 5252)

      let url = `/enter-time/${tb_name}/${person_id}/${clientId}/${direction}/${date}/${day}/${enterExitTime}`
      console.log( url)

      $('.send_enter_time').attr('data-url', url)
    //   $('.message_cont').html('')


    })

})


  $(function () {
    $('.send_enter_time').on('click', function () {

        let time = $('#time').val()
        let url = $(this).attr('data-url')
        url = url+"/"+time
        let id = url.split("/").pop();

      console.log(time, url+"/"+time)

      if(url){

        $.ajax({
          type: "GET",
          url: url,
          cache: false,
          success: function (data) {
            console.log(data)
            let message = ''
            let type = ''
            if (data.result) {
                $('.text-danger').text('');
                    console.log(data.result,5555)
                     message = data.result
                     console.log(message)
                     if(message!="Գործողությունը հաստատված է:"){
                        type = 'danger'
                     }
                     else{
                        type = 'success'
                     }


                $('.message_cont').html(`<span class="text-${type}">${message}</span>`)

          
            }
             },

        });
      }
    })
    $('.btn-close').on('click', function () {
        window.location.reload();
    });


  })
