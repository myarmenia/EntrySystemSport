@extends('layouts.app')

@section('content')

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
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
              <h5 class="card-title">
                <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="{{ route('department.list') }}">Ստորաբաժանումների ցանկ</a></li>

                      <li class="breadcrumb-item active">Ստեղծել</li>
                    </ol>
                  </nav>
              </h5>

              <!-- General Form Elements -->
              <form action="{{ route('department.store')}}" method="post" enctype="multipart/form-data">

                    @if (Auth::user()->hasAnyRole(["client_admin","client_admin_rfID","client_sport","manager"]))

                        <div class="row mb-3">
                            <label for="inputEmail" class="col-sm-3 col-form-label">Ստորաբաժանման անուն </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" value={{ old('name') }}>
                                @error("name")
                                    <div class="mb-3 row ">
                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                        </p>
                                    </div>
                                @enderror
                            </div>
                        </div>




                        <div class="row mt-3">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary">Ստեղծել</button>
                            </div>
                        </div>

                    @endif

              </form>

            </div>
          </div>

        </div>
    </section>

  </main><!-- End #main -->


@endsection
