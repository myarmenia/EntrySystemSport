$(function () {
    // բացվում է մոդալը
    $(".click_attach_person").on("click", function () {
        let recommendationId = $(this).data("data-id");
        $("#recommendation_id").val(recommendationId);

        $('#select_all').on('change', function () {
            const checked = $(this).is(':checked');

            $('.user-checkbox').prop('checked', checked);
        });

        $(document).on('change', '.user-checkbox', function () {

            const total = $('.user-checkbox').length;
            const checked = $('.user-checkbox:checked').length;

            if (checked === total) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });


    });

    // submit
    $("#trainerPersonsForm").on("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        // debug
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        $.ajax({
            url: '/recommendation/trainer-people',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success() {
                $('#trainerPerson').modal('hide');
                alert('Բարեհաջող կցվեց');
            },

            error() {
                alert('Սխալ է տեղի ունեցել');
            }
        });
    });
});
