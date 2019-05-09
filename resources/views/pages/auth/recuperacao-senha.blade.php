<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="Central Vex"/>
    <meta name="author" content="2Mind"/>
    <title>Recuperação de senha | Central Vex</title>

    <!-- FAVICON -->
    <link rel="shortcut icon" href="{{ url('/assets/img/logo/vex_icon.png') }}" type="image/x-icon"/>

        <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.v1.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{url('/assets/plugins/material-preloader/css/materialPreloader.min.css')}}" rel="stylesheet">        
        
    <!-- Theme Styles -->
    <link href="{{url('/assets/css/alpha.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    

</head>

<body class="signin-page">

    @include('layouts.gif-loader')

    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                <div class="row">
                    <div class="col s12 m6 l4 offset-l4 offset-m3">
                        <div class="card white darken-1">
                            <div class="card-content">
                                <span class="card-title center-align">
                                    @if(env('LOGO_EMPRESA') !== '' and env('LOGO_EMPRESA') !== null)
                                        <img src="{{env('ADMIN_URL') . env('LOGO_EMPRESA')}}" width="140">
                                    @else 
                                        <img src="{{url('/assets/img/logo/vex_large_splash.png')}}" width="140">
                                    @endif
                                </span>
                                <div class="row">
                                    <form class="col s12" method="post">

                                        {{csrf_field()}}

                                        <div class="input-field col s12">
                                            <input id="email" type="email" name="email" class="validate" required>
                                            <label for="email">E-mail</label>
                                        </div>
                                        
                                        <div class="col s12 center-align">
                                            <button type="submit" class="waves-effect waves-light col s12 btn blue">Recuperar senha</button>
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

    <!-- Javascripts -->
    <script src="{{url('/assets/plugins/jquery/jquery-2.2.0.min.js')}}"></script>
    <script src="{{url('/assets/plugins/materialize/js/materialize.min.js')}}"></script>
    <script src="{{url('/assets/plugins/material-preloader/js/materialPreloader.min.js')}}"></script>
    <script src="{{url('/assets/plugins/jquery-blockui/jquery.blockui.js')}}"></script>
    <script src="{{url('/assets/js/alpha.js')}}"></script>

    @include("layouts.notifications")


</body>

</html>