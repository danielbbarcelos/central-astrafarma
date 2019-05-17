<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no"/>

    <link rel="shortcut icon" href="{{ url('/assets/img/logo/vex_icon.png') }}" type="image/x-icon"/>

    <title>@yield('page-title') | Central Vex</title>

    <!-- Styles -->
    <link href="{{url('/assets/plugins/materialize/css/materialize.v1.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/material-icons.css')}}" rel="stylesheet">
    <link href="{{url('/assets/plugins/material-preloader/css/materialPreloader.min.css')}}" rel="stylesheet">        
    <link href="https://fonts.googleapis.com/css?family=Didact+Gothic|Rubik" rel="stylesheet">

    <!-- CSS especifico das paginas -->
    @yield('page-css')

    <!-- Theme Styles -->
    <link href="{{url('/assets/css/alpha.c4ca4238a0b923820dcc509a6f75849b.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/custom.eccbc87e4b5ce2fe28308fd9f2a7baf3.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/ribbon.css')}}" rel="stylesheet" type="text/css"/>

    <!-- Jquery -->
    <script src="{{url('/assets/plugins/jquery/jquery-2.2.0.min.js')}}"></script>

</head>
<body>
    @include('layouts.gif-loader')

    <div class="mn-content fixed-sidebar">

        @include("layouts.header")


        <!-- Left Sidebar -->
        @include("layouts.menu")


        <main class="mn-inner inner-active-sidebar">

            <div class="row">
                <div class="middle col s12">
                    @yield('page-breadcrumbs')
                </div>
            </div>

            @yield('page-content')
               
        </main>
        
    </div>
    <div class="left-sidebar-hover"></div>

    @include("layouts.modal-dialog")
    @include("layouts.scripts")
    @include("layouts.notifications")

</body>
</html>