
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
        // $day = \Carbon\Carbon::now()->format('l');

    @endphp

    @if(($data['attendance_sheet'])>0)
                            {{-- {{dd($data['attendance_sheet'])}} --}}

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" class="fix_column">Հ/Հ</th>
                    <th scope="col" class="fix_column">ID</th>
                    <th  colspan="3" scope="col" class="fix_column">
                        <span>Անուն Ազգանուն</span>
                    </th>
                    @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                        <th>{{ $date->format('d') }}</th>
                    @endfor
                    <th>Օրերի քանակ</th>
                    <th>ժամերի քանակ</th>
                    <th>Ուշացման ժամանակի գումար</th>
                </tr>

            </thead>
            <tbody>

                @foreach ($data['attendance_sheet'] as $peopleId=>$item)
                    <tr class="parent">
                        <td>{{ ++$data['i']}}</td>
                        <td scope="row">{{ $peopleId }}</td>
                        <td colspan="3" >
                            {{ getPeople($peopleId)->name ?? null }}  {{ getPeople($peopleId)->surname ?? null }}
                        </td>
                        @for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay())
                            <td class="p-0 text-center">
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
@endif







