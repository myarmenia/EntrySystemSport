<div class="col-lg-4 col-md-6">
    <div class="mt-3">
        {{-- {{ dd($reservetions) }} --}}
        <button class="btn btn-primary" id="show_reservetion" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasBoth" aria-controls="offcanvasBoth">Enable both scrolling & backdrop</button>
        <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasBoth"
            aria-labelledby="offcanvasBothLabel">
            <div>
                <h5 id="offcanvasBothLabel" class="offcanvas-title fw-bold m-3">
                    {{ count($reservetions) > 0 ? $date->translatedFormat('d F Y թ.') : '' }}
                </h5>
                <small class="text-muted p-3">
                    {{ count($reservetions) }} գրանցում
                </small>
            </div>
            <div class="offcanvas-body  mx-0 flex-grow-0">
                <div class="reservetions">
                    @forelse ($reservetions as $item)
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="fw-semibold">
                                        👤 {{ $item->person->name }} {{ $item->person->surname }}
                                    </div>
                                    <div class="text-muted small">
                                        🕒 {{ $item->start_time }} – {{ $item->end_time }}
                                    </div>
                                </div>



                            </div>
                        </div>
                    @empty
                        <div class="text-center mt-5">
                            <div class="fs-1">📭</div>
                            <h6 class="fw-bold mt-3">
                                Գրանցումներ չկան
                            </h6>
                            <p class="text-muted small">
                                Այս օրը այցելուներ չեն գրանցվել
                            </p>
                        </div>
                    @endforelse


                </div>

            </div>
        </div>
    </div>
</div>
