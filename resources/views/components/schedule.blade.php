<div class="col-lg-4 col-md-6">
    <div class="mt-3">
        {{-- {{ dd($reservetions) }} --}}
          <button class="btn btn-primary" id="show_reservetion" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBoth" aria-controls="offcanvasBoth">Enable both scrolling & backdrop</button>
          <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasBoth" aria-labelledby="offcanvasBothLabel">

                <div class="offcanvas-header">

                      <h5 id="offcanvasBothLabel" class="offcanvas-title fw-bold">{{count($reservetions) > 0 ?  $date->translatedFormat('d, F Y թ.')  : ''}} </h5>

                      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                </div>
                <div class="reservetion-result text-center"><span class="text-{{count($reservetions) == 0 ? 'danger' : 'primary'}}">{{count($reservetions) == 0 ? 'Գրանցումներ չկան' : 'Գրանցված այցելուներ'}}</span></div>
                <div class="offcanvas-body  mx-0 flex-grow-0">
                    <div class="reservetions">
                        @if (count($reservetions) > 0)


                                <ul class="list-group">
                                    @foreach ($reservetions as $key => $item)
                                     <li class="list-group-item list-group-item-primary">{{$item->person->name . ' ' . $item->person->surname . " սկիզբ " . $item->start_time . " ավարտ " . $item->end_time }}</li>

                                    @endforeach
                                </ul>





                        @endif

                    </div>

                </div>
          </div>
    </div>
</div>
