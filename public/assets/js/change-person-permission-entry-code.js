(function () {
  $('#entryCodeNumber').on("change", function () {

    let entryCodeId = $(this).val()
    let personId = $(this).attr('data-person-id')
    $.ajax({
      type: "post",
      url: "/change-person-permission-entry-code",
      data: { entryCodeId, personId },
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
document.addEventListener("DOMContentLoaded", () => {
    const trainerSelect = document.getElementById("trainerSelect");
    const durationRow = document.getElementById("trainerDurationRow");
    const durationSelect = document.getElementById("trainerDurationSelect");

    if (!trainerSelect || !durationRow || !durationSelect) return;

    const currentDurationId = @json(
        old('session_duration_id')
            ?? ($person['schedule_department_people'][0]->session_duration_id ?? null)
    );

    function fillDurations() {
        const opt = trainerSelect.selectedOptions?.[0];
        const durationsJson = opt?.getAttribute("data-durations");

        durationSelect.innerHTML = `<option value="" disabled selected>Ընտրել պարապմունքը</option>`;

        if (!trainerSelect.value || !durationsJson) {
            durationRow.classList.add("d-none");
            return;
        }

        let durations = [];
        try { durations = JSON.parse(durationsJson); } catch (e) { durations = []; }

        if (!Array.isArray(durations) || durations.length === 0) {
            durationRow.classList.add("d-none");
            return;
        }

        durations.forEach(d => {
            const o = document.createElement("option");
            o.value = d.id;
            const title = d.title ? ` — ${d.title}` : "";
            o.textContent = `${d.minutes} րոպե${title} — ${d.price_amd} դրամ`;
            durationSelect.appendChild(o);
        });

        if (currentDurationId) {
            const exists = durations.some(d => String(d.id) === String(currentDurationId));
            if (exists) durationSelect.value = String(currentDurationId);
        }

        durationRow.classList.remove("d-none");
    }

    trainerSelect.addEventListener("change", () => {
        durationSelect.value = "";
        fillDurations();
    });

    fillDurations();
});

