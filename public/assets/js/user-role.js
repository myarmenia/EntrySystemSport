$(function () {

    $('#selectedRole').on("change", function () {
        console.log($('#selectedRole'))
        var selectedValues = $(this).val();
        console.log(selectedValues, '+++++++++')
        // if ($.inArray('client_admin', selectedValues) !== -1 || $.inArray('client_admin_rfID', selectedValues) !== -1) {
        if ($(this).val() == "client_admin" || $(this).val() == "client_admin_rfID" || $(this).val() == "client_sport") {
            console.log("inner")
            console.log($(this).val())
            $.ajax({
                url: '/client-component',
                type: 'POST',
                data: {
                    // _token: '{{ csrf_token() }}',
                    // name:userName,
                    // email:userEmail,
                    // password:userPassword,
                    // confirmPassword:userConfirmPassword,
                    // userRole:userRole
                    // Любые данные, которые нужно передать
                },
                success: function (response) {
                    console.log(response.html)

                    $('#componentContainer').html(response.html);
                    $('#loginBtn').css({
                        'display': 'none'
                    });
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });


        } else {
            $('#componentContainer').html('');
        }


    })

})


// document.addEventListener("DOMContentLoaded", () => {
//     const roleSelect = document.getElementById("selectedRole");
//     const sessionWrapper = document.getElementById("session_duration");
//     const wrapper = document.getElementById("scheduleSelectWrapper");

//     const list = document.getElementById("sessionDurationList");
//     const addBtn = document.getElementById("addSessionDuration");

//     function toggleSessionBlock() {
//         if (roleSelect?.value === "trainer") {
//             sessionWrapper.classList.remove("d-none");
//             wrapper?.classList.remove("d-none");
//             // session_duration?.classList.remove("d-none");
//         } else {
//             wrapper?.classList.add("d-none");

//             sessionWrapper.classList.add("d-none");
//         }
//     }

//     roleSelect?.addEventListener("change", toggleSessionBlock);
//     toggleSessionBlock();

//     let index = list.children.length;

//     addBtn?.addEventListener("click", () => {
//         const item = document.createElement("div");
//         item.className = "session-duration-item border rounded p-3 mb-2";

//         item.innerHTML = `
//             <div class="row g-2 align-items-center">
//                 <div class="col-md-3">
//                     <input type="number" min="1" class="form-control"
//                         name="session_durations[${index}][minutes]"
//                         placeholder="Տևողություն (րոպե)">
//                 </div>

//                 <div class="col-md-4">
//                     <input type="text" class="form-control"
//                         name="session_durations[${index}][title]"
//                         placeholder="Անվանում">
//                 </div>

//                 <div class="col-md-3">
//                     <input type="number" min="0" class="form-control"
//                         name="session_durations[${index}][price_amd]"
//                         placeholder="Գին (AMD)">
//                 </div>

//                 <div class="col-md-2 text-end">
//                     <button type="button" class="btn btn-outline-danger btn-sm remove-session">✕</button>
//                 </div>
//             </div>
//         `;

//         list.appendChild(item);
//         index++;
//     });

//     list?.addEventListener("click", (e) => {
//         if (e.target.classList.contains("remove-session")) {
//             e.target.closest(".session-duration-item").remove();
//         }
//     });
// });
document.addEventListener("DOMContentLoaded", () => {
    const roleSelect = document.getElementById("selectedRole");

    const scheduleWrapper = document.getElementById("scheduleSelectWrapper");
    const scheduleSelect = document.getElementById("scheduleSelect");

    const sessionWrapper = document.getElementById("session_duration");
    const list = document.getElementById("sessionDurationList");
    const addBtn = document.getElementById("addSessionDuration");

    function hasTrainerRoleSelected() {
        if (!roleSelect) return false;

        // edit-ում multiple է, create-ում single է -> սա աշխատում է երկուսի համար
        const selected = Array.from(roleSelect.selectedOptions || []).map(o => o.value);
        return selected.includes("trainer") || roleSelect.value === "trainer";
    }

    function toggleTrainerBlocks() {
        const isTrainer = hasTrainerRoleSelected();

        // schedule
        if (scheduleWrapper) {
            if (isTrainer) scheduleWrapper.classList.remove("d-none");
            else scheduleWrapper.classList.add("d-none");
        }

        // session durations
        if (sessionWrapper) {
            if (isTrainer) sessionWrapper.classList.remove("d-none");
            else sessionWrapper.classList.add("d-none");
        }
    }

    roleSelect?.addEventListener("change", toggleTrainerBlocks);
    toggleTrainerBlocks();

    // ========= dynamic add/remove session duration rows =========
    if (!list || !addBtn) return;

    let index = list.children.length;

    addBtn.addEventListener("click", () => {
        const item = document.createElement("div");
        item.className = "session-duration-item border rounded p-3 mb-2";

        item.innerHTML = `
          <div class="row g-2 align-items-center">
            <input type="hidden" name="session_durations[${index}][session_duration_id]" value="">
            <div class="col-md-3">
              <input type="number" min="1" class="form-control"
                name="session_durations[${index}][minutes]" placeholder="Տևողություն (րոպե)">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control"
                name="session_durations[${index}][title]" placeholder="Անվանում">
            </div>
            <div class="col-md-3">
              <input type="number" min="0" class="form-control"
                name="session_durations[${index}][price_amd]" placeholder="Գին (AMD)">
            </div>
            <div class="col-md-2 text-end">
              <button type="button" class="btn btn-outline-danger btn-sm remove-session">✕</button>
            </div>
          </div>
        `;

        list.appendChild(item);
        index++;
    });

    list.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-session")) {
            e.target.closest(".session-duration-item")?.remove();
        }
    });
});


