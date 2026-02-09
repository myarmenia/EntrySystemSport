<!-- Small Modal -->
<div class="modal fade" id="smallModal" tabindex="-1" aria-labelledby="smallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content border-0 shadow-lg rounded-3">

      <!-- Modal Header -->
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title" id="smallModalLabel">
          Երկուշաբթի օրը պետք է լինի լրացված
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body text-center py-4">
        <div class="d-flex flex-wrap gap-2" id="weekDaysSelector">
            @foreach($weekdays as $key => $day)
                <div
                    class="week-day-item px-3 py-2 rounded border text-center user-select-none"
                    data-day="{{ $key }}"
                    style="cursor:pointer"
                >
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Apply button -->
        <div class="mt-3">
            <button type="button" class="btn btn-primary" id="applyDays">Տարածել</button>
        </div>
      </div>

    </div>
  </div>
</div>
