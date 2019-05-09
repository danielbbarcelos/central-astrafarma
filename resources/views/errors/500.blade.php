@extends('errors.template')


@section('title')

    500 | Central Vex

@endsection


@section('content')
    <tr>
        <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td  align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <img src="{{url('/assets/img/logo/vex_large_splash.png')}}" width="200"><br><br>
                        <p style="font-family: 'Verdana'; font-size: 30px; font-weight: normal; margin: 0; Margin-bottom: 30px;">
                            Algo de errado aconteceu :(<br>

                        </p>
                        <small style="Margin-bottom: 60px;">Tente novamente mais tarde ou entre em contato com a equipe de suporte.</small>
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="padding-top: 30px; gborder-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                            <tbody>
                            <tr>
                                <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                        <tbody>
                                        <tr>
                                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;">
                                                <a href="javascript:window.history.back();" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">Voltar</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"></p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>


@endsection