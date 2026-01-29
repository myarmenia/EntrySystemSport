$(function () {
    $(".click_attach_person").on("click", function () {
        let recommendationId = $(this).parents(".action").attr("data-id");

        $("#recommendation_id").val(recommendationId);
        // remove old errors
        $("#trainerPersonsFormErrors").addClass("d-none").empty();

        $(".user-checkbox").prop("checked", false);
        $("#select_all").prop("checked", false);

        $("#select_all").on("change", function () {
            const checked = $(this).is(":checked");

            $(".user-checkbox").prop("checked", checked);
        });

        $(document).on("change", ".user-checkbox", function () {
            const total = $(".user-checkbox").length;
            const checked = $(".user-checkbox:checked").length;

            if (checked === total) {
                $("#select_all").prop("checked", true);
            } else {
                $("#select_all").prop("checked", false);
            }
        });
    });

    // submit
    $("#trainerPersonsForm").on("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(
            document.querySelector("#trainerPersonsForm"),
        );

        // скрываем прошлые ошибки
        const $errorBox = $("#trainerPersonsFormErrors");
        $errorBox.addClass("d-none").empty();

        // debug
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        $.ajax({
            url: "/recommendation/person-recommendation",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",

            success: function (response) {
                console.log(response, 888);
                $("#trainerPerson").modal("hide");

                if (response.success) {
                    $("#successAlert").html(`
                        <div class="alert alert-success" role="alert">
                            ${response.message}
                        </div>
                    `);
                }
            },

            error(xhr) {
                const error = xhr.responseJSON;
                // 1️⃣ if isset validation errors
                if (error?.errors) {
                    let messages = [];
                    // объединяем все сообщения из всех полей
                    for (let key in error.errors) {
                        if (error.errors.hasOwnProperty(key)) {
                            messages.push(...error.errors[key]);
                        }
                    }

                    $errorBox.html(messages.join("<br>")).removeClass("d-none");
                    return;
                }
                // if isset error_code
                if (error.error_code) {
                    $errorBox.html(error.message).removeClass("d-none");
                    return;
                }
                 $errorBox.html("Անսպասելի սխալ է տեղի ունեցել").removeClass("d-none");
            },
        });
    });
});
