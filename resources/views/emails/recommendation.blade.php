<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Բարի գալուստ Մուտքի համակարգեր</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f9f9f9;">



    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600px" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; background-color: white;">
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <img src="{{ $message->embed(public_path('/assets/img/logo.png')) }}" alt="">
                        </td>
                    </tr>

                    <tr style="text-align:center; font-weight: bold;font-size:14px">
                        <td style="padding: 30px; text-align: center;">
                            <h2 style="margin: 0; color: #2E86C1;">Բարև, {{ $person->name }}!</h2>
                            <p style="font-size: 16px; margin: 10px 0 20px;">Դուք ստացել եք  մարզչի խորհուրդները:
                            </p>

                            <!-- Название рекомендации -->
                            <h3 style="margin: 0 0 10px; color: #1F618D;">{{ $recommendation->name }}</h3>

                            <!-- Описание рекомендации -->
                            <p style="font-size: 15px; line-height: 1.5; color: #555;">
                                {!! $recommendation->description !!}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 20px; text-align: center; font-size: 12px; color: #888; background-color: #f0f0f0;">
                            Այս նամակը ավտոմատ է ստեղծվել մուտքի վերահսկման համակարգի կողմից:<br>
                            Եթե դուք չեք ակնկալել այս նամակը, կարող եք անտեսել այն:<br>
                            &copy; {{ date('Y') }} {{ config('app.name') }}. Բոլոր իրավունքները պաշտպանված են:
                        </td>
                    </tr>

                </table>
        </tr>
    </table>
</body>

</html>
