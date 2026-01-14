@extends('layouts.app')

@section("page-script")
    <script src="{{ asset('assets/js/change-status.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        th{
            font-size:12px
        }
        table th {
            height: 100px;
            text-align: center
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
    </style>
@endsection


@section('content')
@php
    use Carbon\Carbon;

    use App\Models\AttendanceSheet;
    use Illuminate\Support\Facades\DB;



    // Assuming $request->month contains "2024-10"
    $monthYear = $mounth ?? null;

    // Parse the month-year string to get the start and end of the month
    $startOfMonth = Carbon::parse($monthYear)->startOfMonth();
    $endOfMonth = Carbon::parse($monthYear)->endOfMonth();

    $groupedEntries = $groupedEntries ?? null

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
                                            <li class="breadcrumb-item active">Հաշվետվություն ըստ մուտքի և ելքի</li>
                                        </ol>
                                    </nav>
                                </h5>
                                @php
                                    $day = \Carbon\Carbon::now()->format('l'); // 'l' gives the full name of the day (e.g., Monday, Tuesday)

                                @endphp


                            </div>


                            <form  action="{{ route('reportListArmobile') }}" method="get" class="mb-3 justify-content-end" style="display: flex; gap: 8px">

                                <div class="col-2">
                                    <input type="text"  class="form-select"  id="monthPicker" placeholder="Ընտրել ամիսը տարեթվով" name="mounth"/>
                                </div>

                                <button type="submit" class="btn btn-primary col-2 search">Հաշվետվություն</button>
                                <a href="{{ route('export-xlsx-armobil',$mounth) }}" type="submit" class="btn btn-primary col-2 search">Արտահանել XLSX</a>
                            </form>
                            <!-- Bordered Table -->
                            @if(($groupedEntries)>0)

<<<<<<< HEAD
                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
=======
                                <div class="table-responsive">
>>>>>>> b03274f5aea0f8449c18d35f74054bdba089ba42
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="fix_column">Հ/Հ</th>
                                                <th rowspan="2" class="fix_column">ID</th>
                                                <th rowspan="2" class="fix_column">Անուն</th>
                                                <th rowspan="2" class="fix_column">Ազգանուն</th>

                                                @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                                                    <th colspan="2">{{ $date->format('d') }}</th>
                                                @endfor

                                                <th rowspan="2">Օրերի քանակ</th>
                                                <th rowspan="2">ժամերի քանակ</th>
                                                <th rowspan="2">Ուշացման ժամանակի գումար</th>
                                            </tr>
                                            <tr>
                                                @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                                                    <th>Մուտք</th>
                                                    <th>Ելք</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- {{ dd($groupedEntries) }} --}}
                                            @foreach ($groupedEntries as $peopleId => $item)

                                            {{-- @if($peopleId==78) --}}
                                            {{-- {{ dd($item) }} --}}
                                                <tr>
                                                    <td class="fix_column">{{ ++$i }}</td>
                                                    <td class="fix_column">{{ $peopleId }}</td>
                                                    <td class="fix_column">{{ getPeople($peopleId)->name ?? null }}</td>
                                                    <td class="fix_column">{{ getPeople($peopleId)->surname ?? null }}</td>

                                                    @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                                                        <td class="p-0 text-center">
                                                            @if(isset($item[$date->format('d')]['enter']))
                                                                @if (is_array($item[$date->format('d')]['enter']))
                                                                         {{ $item[$date->format('d')]['enter'][0] }}

                                                                @else
                                                                   <div class="editable" style="width:20px;height:20px;background-color:grey"></div>

                                                                @endif
                                                            @endif
                                                        </td>

                                                        <td class="p-0 text-center">
                                                            @if(isset($item[$date->format('d')]['exit']))
<<<<<<< HEAD

                                                                {{-- {{ $item[$date->format('d')]['exit']->last() }} --}}
=======
                                                               @if (is_array($item[$date->format('d')]['exit']))
                                                                 <span>
                                                                     {{  last(array_slice($item[$date->format('d')]['exit'], -1))  }}
                                                                 </span>
                                                               @else
>>>>>>> b03274f5aea0f8449c18d35f74054bdba089ba42

                                                                 <div class="editable" style="width:20px;height:20px;background-color:grey"></div>

<<<<<<< HEAD
                                                                        {{-- {{  last(array_slice($item[$date->format('d')]['exit'], -1))  }} --}}
                                                                        {{ end($item[$date->format('d')]['exit']) }}
=======
>>>>>>> b03274f5aea0f8449c18d35f74054bdba089ba42

                                                               @endif

                                                            @endif
                                                        </td>
                                                    @endfor

                                                    <td>{{ $item['totalMonthDayCount'] }}</td>
                                                    <td class="{{ isset($item['personWorkingTimeLessThenClientWorkingTime']) ? 'text-danger' : '' }}">
                                                        {{ $item['totalWorkingTimePerPerson'] }}
                                                    </td>
                                                    <td>{{ $item['totaldelayPerPerson'] }}</td>
                                                </tr>
                                                {{-- @endif --}}
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @endif

                            <!-- End Bordered Table -->
                            <div class="demo-inline-spacing">
                                {{-- {{ $data->links() }} --}}
                            </div>
                        </div>



                </div>



        </div>

      </div>

    </section>

  </main><!-- End #main -->

@endsection


