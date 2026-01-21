<div class="col-lg-4 col-md-6">
    <div class="mt-3">
          <button class="btn btn-primary" id="show_reservetion" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBoth" aria-controls="offcanvasBoth">Enable both scrolling & backdrop</button>
          <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasBoth" aria-labelledby="offcanvasBothLabel">

                <div class="offcanvas-header">

                      <h5 id="offcanvasBothLabel" class="offcanvas-title fw-bold">{{count($reservetions) > 0 ?  date('d-m-Y', strtotime($reservetions[0]->date))  : ''}} </h5>

                      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                </div>
                <div class="reservetion-result text-center"><span class="text-danger">{{count($reservetions) == 0 ? 'Գրանցումներ չկան' : ''}}</span></div>
                <div class="offcanvas-body  mx-0 flex-grow-0">
                    <div class="reservetions">
                        @if (count($reservetions) > 0)

                                <ul class="list-group">
                                    @foreach ($reservetions as $key => $item)
                                        <li class="list-group-item list-group-item-{{$item->direction=="enter" ? "primary" :"secondary"}}">{{date('H:i', strtotime($item->date))}}-{{$item->direction=="enter" ? "Մուտք" :"Ելք"}}</li>
                                    @endforeach
                                </ul>

                                <div class="d-flex justify-content-between mt-2">
                                    <a></a>
                                    <a href="{{ $url }}" type="submit" class="btn btn-primary">Արտահանել XLSX</a>
                                </div>



                        @endif

                    </div>

                </div>
          </div>
    </div>
</div>
