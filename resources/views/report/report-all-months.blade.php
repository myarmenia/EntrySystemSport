@extends('layouts.app')

@section("page-script")
    <script src="{{ asset('assets/js/change-status.js') }}"></script>
    <script src="{{ asset('assets/js/enter-time.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        th{
            font-size:12px
        }
        table th {

            text-align: center;


        }

        table td {
            text-align: center;
        }

        .fix_column {
            position: sticky;
            left: 0;
            background-color: #343a40;
            color: #fff
        }
        .table-responsive {
            max-height: 700px;
            overflow-y: auto;
            position: relative;
        }
        .table thead {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 100;
        }
        .table thead th {
    position: sticky;
    top: 0;
    background: #fff; /* фон, чтобы текст не сливался */
    z-index: 2; /* чтобы шапка была поверх */
}


    </style>
@endsection


@section('content')
@php
    use Carbon\Carbon;

    use App\Models\AttendanceSheet;
    use Illuminate\Support\Facades\DB;



    // Assuming $request->month contains "2024-10"
    // $monthYear = $data['month'];

    // // Parse the month-year string to get the start and end of the month
    // $startOfMonth = Carbon::parse($monthYear)->startOfMonth();
    // $endOfMonth = Carbon::parse($monthYear)->endOfMonth();

    // $groupedEntries = $data['attendance_sheet'] ?? null

@endphp




   <main id="main" class="main">


    <section class="section">
      <div class="row">



        <div class="col-lg-12">

                <div class="card ">

                        <div class="card-body">
                            @if (session('create_client'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('create_client') }}
                                </div>
                            @endif


                            <div class = "d-flex justify-content-between">
                                @if (isset($error))
                                    <div class="alert alert-danger">
                                        {{ $error }}
                                    </div>
                                @endif
                                <h5 class="card-title">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active">Հաշվետվություն ըստ  ամիսների</li>
                                        </ol>
                                    </nav>
                                </h5>
                            </div>
                             <form  action="{{ route('reportAllMonth.list') }}" method="get" class="mb-3 justify-content-end" style="display: flex; gap: 8px">

                                <div class="col-2">
                                    {{-- <select name="year" class="form-select">
                                         @for ($year = now()->year; $year >= 2024; $year--)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}> {{ $year }} </option>
                                         @endfor
                                    </select> --}}

                                    <input type="text"
                                           class="form-select"
                                           id="monthPicker"
                                           placeholder="Ընտրել ամիսը տարեթվով"
                                           name="month"
                                           value="{{ session('selected_month') }}"
                                           />
                                </div>

                                <button type="submit" class="btn btn-primary col-2 search">Հաշվետվություն</button>
                                {{-- <a href="{{ route('export-xlsx-armobil',$mounth) }}" type="submit" class="btn btn-primary col-2 search">Արտահանել XLSX</a> --}}
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="fix_column">Հ/Հ</th>
                                            <th rowspan="2" class="fix_column">ID</th>
                                            <th rowspan="2" class="fix_column">Անուն Ազգանուն</th>
                                            @foreach ($months as $month)
                                                    <th colspan="3">{{ $month }}</th>
                                            @endforeach
                                            <th colspan="3">Ընդամենը</th>

                                        </tr>
                                        <tr>
                                            @foreach ($months as $mon)
                                                <th>Հաժախումներ /օր/</th>
                                                <th>Հաժախումներ /ժամ/</th>
                                                    <th>Ուշացման ժամանակի գումար /րոպե/</th>
                                            @endforeach
                                            <th>Հաժախումներ /օր/</th>
                                            <th>Հաժախումներ /ժամ/</th>
                                            <th>Ուշացման ժամանակի գումար /րոպե/</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($usersReport as $peopleId => $item)
                                        {{-- {{ dd( $peopleId, $item) }} --}}
                                            <tr  class="action" data-person-id="{{ $peopleId }}"  data-tb-name="attendance_sheets">
                                                <td class="fix_column">1</td>
                                                <td class="fix_column">{{ $peopleId }}</td>
                                                <td class="fix_column">{{ getPeople($peopleId)->name ?? null }} {{ getPeople($peopleId)->surname ?? null }}</td>

                                                @foreach ($months as $month)

                                                    @php
                                                        $monthData = $item['months'][$month] ?? null;
                                                    @endphp

                                                    @if ($monthData)
                                                        <td class="p-0 text-center">{{ $monthData['days'] ?? 0 }}</td>
                                                        <td class="p-0 text-center">{{ $monthData['hours'] ?? 0 }}</td>
                                                        <td class="p-0 text-center">{{ $monthData['delay'] ?? 0 }}</td>
                                                    @else
                                                        <td class="p-0 text-center">-</td>
                                                        <td class="p-0 text-center">-</td>
                                                        <td class="p-0 text-center">-</td>
                                                    @endif

                                                @endforeach

                                                <td>{{ $item['totalDays'] }}</td>
                                                <td>
                                                    {{ $item['totalHours'] }}
                                                </td>
                                                <td>{{ $item['totalDelay'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>


                        </div>



                </div>



        </div>

      </div>

    </section>



  </main><!-- End #main -->

@endsection
<x-modal-edit-time></x-modal-edit-time>



