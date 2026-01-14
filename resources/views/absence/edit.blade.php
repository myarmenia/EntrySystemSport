@extends('layouts.app')

@section('content')

   <main id="main" class="main">

    <section class="section">

      <div class="row">
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
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
                      <li class="breadcrumb-item">
                        <a href="{{ route('absence.list',$data->person->id) }}">Հարգելի բացակայությունների ցանկ</a></li>

                      <li class="breadcrumb-item active">Խմբագրել</li>
                    </ol>
                  </nav>
              </h5>

              <!-- General Form Elements -->
              <form action="{{ route('absence.update',$id)}}" method="post" enctype="multipart/form-data">

               @method('put')
                <input type="hidden" value="{{ $data->person->id }}" name="person_id">

                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Աշխատակցի Անուն Ազգանուն </label>
                    <div class="col-sm-9">
                        <p>{{ $data->person->full_name }}</p>
                    </div>

                </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Բացակայության տեսակը </label>
                        <div class="col-sm-9">
                        <select class="form-select" aria-label="Default select example" name ="type">
                            <option value='' disabled >Բացակայության տեսակը </option>
                            @foreach ($absence_type as  $type)
                                <option value="{{ $type }}" {{ $data->type==$type ?"selected" : null }}>{{ $type}}</option>
                            @endforeach

                        </select>

                        </div>

                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label"> Սկիզբ  </label>
                        <div class="col-sm-9">
                            <input type = "date" class="form-control" name="start_date" value={{ $data->start_date }}>
                            @error("start_date")
                                <div class="mb-3 row ">
                                    <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                    </p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="inputEmail" class="col-sm-3 col-form-label">Ավարտ  </label>
                        <div class="col-sm-9">
                           <input type = "date" class="form-control" name="end_date" value={{ $data->end_date }}>
                            @error("end_date")
                                <div class="mb-3 row ">
                                    <p class="col-sm-10 text-danger fs-6">{{ $message }}
                                    </p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-9">
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
