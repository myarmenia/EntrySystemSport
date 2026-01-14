@extends('layouts.app')

@section("page-script")
    <script src="{{ asset('assets/js/change-status.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        th{
            font-size:12px
        }
        th{
            font-size:12px
        }
        /* table th {
            height: 100px;
            text-align: center
        } */

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
            overflow: auto;
            position: relative;
        }
        .table thead {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 100;
        }

    </style>
@endsection


@section('content')
@php
    use Carbon\Carbon;

    use App\Models\AttendanceSheet;
    use Illuminate\Support\Facades\DB;



    // Assuming $request->month contains "2024-10"
    $monthYear = $data['month'];

    // Parse the month-year string to get the start and end of the month
    $startOfMonth = Carbon::parse($monthYear)->startOfMonth();
    $endOfMonth = Carbon::parse($monthYear)->endOfMonth();
    // dd($startOfMonth);

@endphp



   <main id="main" class="main">


    <section class="section">
      <div class="row">

        <div class="col-lg-12">

                <div class="card">

                        <div class="card-body">
                            @if (session('create_client'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('create_client') }}
                                </div>
                            @endif

                            <div class = "d-flex justify-content-between">
                                <h5 class="card-title">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active">Հաշվետվություն</li>
                                        </ol>
                                    </nav>
                                </h5>
                                @php
                                    $day = \Carbon\Carbon::now()->format('l'); // 'l' gives the full name of the day (e.g., Monday, Tuesday)

                                @endphp


                            </div>


                            <form  action="{{ route('reportFilter.list') }}" method="get" class="mb-3 justify-content-end" style="display: flex; gap: 8px">

                                        <div class="col-sm-2">

                                                 <select class="form-select" name="department_id">
                                                    <option value="" disabled >
                                                        Ընտրել ստորաբաժանումը
                                                    </option>

                                                    @foreach($data['client_department'] as $department)
                                                        <option value="{{ $department->id }}"

                                                            {{ request()->department_id == $department->id ? 'selected' : '' }}
                                                            >
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach

                                                    <option value="" {{ request()->department_id === null ? 'selected' : '' }}>
                                                        Ընտրել բոլորը
                                                    </option>
                                                </select>
                                        </div>
                                <div class="col-2">
                                     <input
                                        class = "form-select"
                                        type = "text"
                                        id ="monthPicker"
                                        placeholder = "Ընտրել ամիսը տարեթվով"
                                        name = "month"
                                        value = {{ session('selected_month') ?? null }}
                                       />

                                </div>
                                <button type="submit" class="btn btn-primary col-2 search">Հաշվետվություն</button>
                                {{-- <a href="{{ route('export-xlsx',['mounth'=>$data['month']]) }}" type="submit" class="btn btn-primary col-2 search">Արտահանել XLSX</a> --}}
                                <a href="{{ route('export-xlsx',['department_id'=>request()->department_id,'month'=>$data['month']]) }}" type="submit" class="btn btn-primary col-2 search">Արտահանել XLSX</a>
                            </form>
                            <!-- Bordered Table -->

                            @if(($data['attendance_sheet'])>0)
  {{-- {{dd($data['attendance_sheet'])}} --}}
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="fix_column">Հ/Հ</th>
                                            <th scope="col" class="fix_column">ID</th>
                                            <th scope="col" class="fix_column">
                                                    <span>Անուն Ազգանուն</span>

                                                </th>


                                                @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                                                    <th>{{ $date->format('d') }}</th> <!-- Displays each day in "YYYY-MM-DD" format -->
                                                @endfor
                                                <th>Օրերի քանակ</th>
                                                <th>ժամերի քանակ</th>
                                                <th>Ուշացման ժամանակի գումար</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        {{-- {{ dd$$data['attendance_sheet']) }} --}}
                                        @foreach ($data['attendance_sheet'] as $peopleId=>$item)

                                        {{-- @dump($item) --}}

                                        <tr class="parent">
                                            <td  class="fix_column"> {{ ++$data['i']}}</td>
                                            <td scope="row"  class="fix_column">{{ $peopleId }}</td>
                                            <td  class="fix_column">
                                                {{ getPeople($peopleId)->name ?? null }}  {{ getPeople($peopleId)->surname ?? null }}
                                            </td>

                                            @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())

                                                <td class="p-0 text-center">
                                                    {{-- {{ dd($item) }} --}}



                                                    @if(isset($item[$date->format('d')]))


                                                        @if (isset($item[$date->format('d')]['absence']))

                                                            <div class="{{ isset($item[$date->format('d')]['delay_display'])?'bg-danger':null}}"> {{ mb_substr($item[$date->format('d')]['absence'], 0, 1, "UTF-8")}}</div>

                                                        @endif
                                                        @if (isset($item[$date->format('d')]['daily_working_times']))

                                                            <div style="width:60px"> {{ $item[$date->format('d')]['daily_working_times'] }}</div>

                                                        @endif
                                                    @endif
                                                </td>
                                            @endfor
                                            <td>
                                                {{$item['totalMonthDayCount'] }}

                                            </td>
                                            <td>

                                                <span class="{{ isset($item['personWorkingTimeLessThenClientWorkingTime']) ? 'text-danger' : null  }}">
                                                    {{$item['totalWorkingTimePerPerson'] }}
                                                <span>

                                            </td>
                                            <td>
                                                {{$item['totaldelayPerPerson'] }}
                                            </td>
                                        </tr>
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


