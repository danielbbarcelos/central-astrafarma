@extends('errors.template')


@section('title')

    503 | Central Vex

@endsection


@section('content')
    <tr>
        <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td  align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <img src="{{url('/assets/img/logo/vex_large_splash.png')}}" width="200"><br><br>
                        <p style="font-family: 'Verdana'; font-size: 30px; font-weight: normal; margin: 0; Margin-bottom: 60px;">
                            Serviço em manutenção
                        </p>

                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"></p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>


@endsection