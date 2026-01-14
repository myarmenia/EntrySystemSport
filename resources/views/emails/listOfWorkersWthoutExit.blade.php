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
                <table width="600px" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: white;">
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <img src="{{ $message->embed(public_path('/assets/img/logo.png')) }}" alt="">
                        </td>
                    </tr>

                    <tr style="text-align:center; font-weight: bold;font-size:14px" >{{ $data['date'] }} -ին  {{ $data['userFullName'] }} ելքը չի գրանցվել:</tr>

                </table>
          </tr>
    </table>
  </body>
</html>
