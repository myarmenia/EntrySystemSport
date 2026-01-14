<!DOCTYPE html>


<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Armobil</title>

</head>
@php
    use Carbon\Carbon;

    use App\Models\AttendanceSheet;
    use Illuminate\Support\Facades\DB;
    $YearMonthDate = $yearMonthDate ?? null;

@endphp

<body>
    <div>

        <table>
            <thead>
                <tr>
                </tr>
                <tr>
                    <td colspan="20" >{{ $YearMonthDate}}-ի դրությամբ {{ $personFullName }}  մուտքի և ելքի ժամերը</td>
                </tr>
                <tr>
                </tr>
                <tr>
                    <th>Հ/Հ</th>
                    <th>ժամ</th>
                    <th> Գործողություն</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance_sheet as $key=>$data)
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{ \Carbon\Carbon::parse($data->date)->format('H:i') }}</td>
                        <td>
                             @if ($data->direction !== null)
                                 {{ $data->direction === 'enter' ? 'Մուտք' : 'Ելք' }}
                             @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
{{-- {{ dd(777) }} --}}
</html>

