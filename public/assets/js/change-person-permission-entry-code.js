(function () {
    $('#entryCodeNumber').on("change", function () {

      let entryCodeId = $(this).val()
      let personId = $(this).attr('data-person-id')
      $.ajax({
        type: "post",
        url: "/change-person-permission-entry-code",
        data: { entryCodeId , personId },
        cache: false,
        success: function (data) {
          console.log(data.result)
          console.log(status)
          let status_word = ''
          let status_class = ''
          console.log(field_name)


            if (data.result == 1) {
                

            }


        }

      });

    });

  });
