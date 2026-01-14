@extends('layouts.app')

@section('page-script')


    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="{{ asset('assets/js/admin/calendar.js') }}"></script>
    {{-- <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'> --}}
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales-all.global.min.js'></script>
    <link href="{{ asset('assets/css/admin/calendar.css') }}" rel='stylesheet' />
@endsection

@section('content')


   <main id="main" class="main">



    <section class="section">
        <h4 class="py-3 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        {{-- <a href="{{ route('educational_programs_list') }}">Այցելությունների օրացույց և ամրագրում</a> --}}
                    </li>
                </ol>
            </nav>
        </h4>
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="card mb-4">
                        <div class="card-body py-0">

                            <p class="mt-2">{{ $data->full_name }}</p>

                            <div id='calendar'></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="your-component">
        {{-- $reservetions   գալիս է js-ից --}}

        <x-offcanvas :reservetions=" isset($reservetions) ? $reservetions : [] " person_full_name="{{ $data->full_name }}" url={{ $url }}></x-offcanvas>
    </div>

  </main><!-- End #main -->


@endsection
