$(function () {
    $('.change_status').on("change", function () {

      let id = $(this).parents('.action').attr('data-id')

      let tb_name = $(this).parents('.action').attr('data-tb-name')
      let status = $(this).prop('checked')
      let field_name = $(this).attr('data-field-name')
      let changeElemen = $(this)
      console.log(changeElemen)


console.log(id, tb_name, status, field_name)
      $.ajax({
        type: "post",
        url: "/change-status",
        data: { id, tb_name, status, field_name },
        cache: false,
        success: function (data) {
          console.log(data.result)

          let status_word = ''
          let status_class = ''



            if (data.result == 1) {
                if(field_name=="activation"){
                    window.location.reload

                    status ? (status_word = 'Ակտիվ', status_class = 'success') : (status_word = 'Պասիվ', status_class = 'danger')

                    changeElemen.closest('tr').find('td.activation .activationSection').html(`<div><span class="badge bg-${status_class} me-1">${status_word}</span></div>`)
                    changeElemen.closest('tr').find( 'td.personName, .personSurname, .personPhone').empty()
                }
                if(field_name=="status"){

                    status ? (status_word = 'Գործող', status_class = 'success') : (status_word = 'Կասեցված', status_class = 'danger')
                    changeElemen.closest('tr').find('td.status .statusSection').html(`<div><span class="badge bg-${status_class} me-1">${status_word}</span></div>`)

                }

            }


        }

      });

    });

  });
