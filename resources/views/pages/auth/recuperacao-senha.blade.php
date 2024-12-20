<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="Central VEX"/>
    <meta name="author" content="2Mind"/>
    <title>Recuperação de senha | Central VEX</title>

    <!-- FAVICON -->
    <link rel="shortcut icon" href="{{ url('/assets/img/logo/vex_icon.png') }}" type="image/x-icon"/>

        <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.v1.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{url('/assets/plugins/material-preloader/css/materialPreloader.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Didact+Gothic|Rubik" rel="stylesheet">

    <!-- Theme Styles -->
    <link href="{{url('/assets/css/alpha.c4ca4238a0b923820dcc509a6f75849b.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/custom.2ceccbc87e4b5ce2fe28308fd9f2a7baf3.css')}}" rel="stylesheet" type="text/css"/>


</head>

<body class="signin-page">

    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                <div class="row">
                    <div class="col s12 m6 l6 offset-l3 offset-m3">
                        <div class="card-panel white darken-1">
                            <div class="card-content">
                                <span class="card-title center-align">
                                    <img src="{{url('/assets/img/logo/vex_large_splash.png')}}" width="140">
                                </span>
                                <div class="row">
                                    <form class="col s12" method="post">

                                        {{csrf_field()}}

                                        <div class="input-field col s12">
                                            <input id="email" type="email" name="email" class="validate" placeholder="" required>
                                            <label for="email">E-mail</label>
                                        </div>
                                        
                                        <div class="col s12 center-align">
                                            <button type="submit" class="waves-effect waves-light col s12 btn blue btn-submit">Recuperar senha</button>
                                        </div>
                                        <div class="col s12 center-align m-t-sm">
                                            <a href="{{url('/')}}" class="waves-effect waves-grey col s12 btn-flat">Voltar para login</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>



    @if(env('APP_TEST') == true)
        @include("layouts.footer-homologacao")
    @endif



    <!-- Javascripts -->
    <script src="{{url('/assets/plugins/jquery/jquery-2.2.0.min.js')}}"></script>
    <script src="{{url('/assets/plugins/materialize/js/materialize.min.js')}}"></script>
    <script src="{{url('/assets/plugins/material-preloader/js/materialPreloader.min.js')}}"></script>
    <script src="{{url('/assets/plugins/jquery-blockui/jquery.blockui.js')}}"></script>
    <script src="{{url('/assets/js/alpha.a36c299926dedd08c3f48d5f546a683e6.js')}}"></script>


    @include("layouts.notifications")


</body>

</html>