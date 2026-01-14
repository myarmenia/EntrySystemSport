@extends('layouts.app')

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>


</div> --}}


{{-- commentel em heto karox e petq gal  --}}
   {{-- @include("navbar") --}}




   <main id="main" class="main">



    <section class="section">

      <div class="row">
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
                @if (session('repeating_token'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('repeating_token') }}
                    </div>
                @endif
              <h5 class="card-title">
                <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="{{ route('entry-codes-list') }}">Նույնականացման կոդերի ցանկ</a></li>

                      <li class="breadcrumb-item active">Խմբագրել</li>
                    </ol>
                  </nav>
              </h5>

              <!-- General Form Elements -->
              <form action="{{ route('entry_codes-update',$data->id)}}" method="post" enctype="multipart/form-data">
                   @method('put')
                @if (Auth::user()->hasRole("super_admin"))
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Գործատուի անվանում </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="client_id" value = {{$data->client->name}} disabled>
                        </div>

                    </div>
                    @endif

                    <div class="row mb-3">
                        <label for="inputEmail" class="col-sm-3 col-form-label">Թոքեն </label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control" name="token" value={{ $data->token }}>
                        @error("token")
                            <div class="mb-3 row ">
                                <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                </p>
                            </div>
                        @enderror
                    </div>
                  </div>

               @if (Auth::user()->hasRole("super_admin"))
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Տեսակ</label>
                  <div class="col-sm-9">
                    <select class="form-select" aria-label="Default select example" name ="type">
                      <option value='' disabled >Ընտրել տեսակը</option>
                      <option value="rfID" {{$data->type=="rfID" ? "selected": null}}>rfId</option>
                      <option value="FaceId" {{$data->type=="FaceId" ? "selected": null}}>FaceID</option>
                    </select>
                    @error("type")
                        <div class="mb-3 row justify-content-end">
                            <div class="col-sm-10 text-danger fts-14">{{ $message }}
                            </div>
                        </div>
                    @enderror
                  </div>

                </div>
                @endif
                @if(auth()->user()->hasRole('super_admin'))
                    @if ($data->activation==0)
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Կարգավիճակ</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                        name="status"value="{{$data->status}}" {{ $data->status == 1 ? 'checked' : '' }}>

                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if(auth()->user()->hasRole('client_admin'))
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Ակտիվացում </label>
                        <div class="col-sm-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                    name="activation"  {{ $data->activation == 1 ? 'checked' : '' }}>

                            </div>
                        </div>
                    </div>
                @endif




                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"></label>
                  <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Պահպանել</button>
                  </div>
                </div>

              </form>

            </div>
          </div>

        </div>
    </section>

  </main><!-- End #main -->


@endsection
