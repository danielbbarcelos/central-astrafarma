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
    <link href="{{url('/assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css"/>           

    <!-- CSS especifico das paginas -->
    @yield('page-css')

    <!-- Theme Styles -->
    <link href="{{url('/assets/css/alpha.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/assets/css/ribbon.css')}}" rel="stylesheet" type="text/css"/>

    <!-- Jquery -->
    <script src="{{url('/assets/plugins/jquery/jquery-2.2.0.min.js')}}"></script>

</head>
<body>
    @include('layouts.gif-loader')

    <div class="mn-content fixed-sidebar">

        <header class="mn-header navbar-fixed">
            <nav class="cyan darken-1">
                <div class="nav-wrapper row">
                    <section class="material-design-hamburger navigation-toggle">
                        <a href="#" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                            <span class="material-design-hamburger__layer"></span>
                        </a>
                    </section>
                    <div class="header-title col s3">      
                        <span class="chapter-title">CENTRALVEX</span>
                    </div>

                    <form class="left search col s6 hide-on-small-and-down">
                        <div class="input-field">
                            <input id="search" type="search" placeholder="Procurar..." autocomplete="off">
                            <label for="search"><i class="material-icons search-icon">search</i></label>
                        </div>
                        <a href="javascript: void(0)" class="close-search"><i class="material-icons">close</i></a>
                    </form>
                    
                    <ul class="right col s9 m3 nav-right-menu">
                        <li><a href="javascript:void(0)" data-activates="chat-sidebar" class="chat-button show-on-large"><i class="material-icons">more_vert</i></a></li>
                        <li class="hide-on-small-and-down"><a href="javascript:void(0)" data-activates="dropdown1" class="dropdown-button dropdown-right show-on-large"><i class="material-icons">notifications_none</i><span class="badge">4</span></a></li>
                        <li class="hide-on-med-and-up"><a href="javascript:void(0)" class="search-toggle"><i class="material-icons">search</i></a></li>
                    </ul>
                </div>
            </nav>
        </header>


        <!-- Left Sidebar -->
        @include("layouts.menu")


        <main class="mn-inner inner-active-sidebar">

            @yield('breadcrumbs')


            @yield('page-content')
               
        </main>
        
    </div>
    <div class="left-sidebar-hover"></div>
        
    @include("layouts.scripts")
    @include("layouts.notifications")

</body>
</html>