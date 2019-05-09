@extends('layouts.template')

@section('page-title', 'Principal')

@section('page-css')

    <link href="/assets/plugins/bower-components/weather-icons/css/weather-icons.min.css" media="all">
    <link href="/assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
    <link href="/assets/plugins/switcher/switcher.css" rel="stylesheet">

@endsection

@section('page-content')

    <div class="middle padding-top-20 padding-right-20">
        <div class="row no-m-t no-m-b">
            <div class="col s12 m12 l4">
                <div class="card stats-card">
                    <div class="card-content">
                        <div class="card-options">
                            <ul>
                                <li><a href="{{url('/dispositivos')}}"><i class="material-icons">phone_android</i></a></li>
                            </ul>
                        </div>
                        <span class="card-title">Dispositivos permitidos</span>
                        <span class="stats-counter"><span class="counter">{{$assinatura->quantidade_dispositivo}}</span></span>
                    </div>
                    <div class="progress stats-card-progress">
                        <div class="determinate" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col s12 m12 l4">
                <div class="card stats-card">
                    <div class="card-content">
                        <div class="card-options">
                            <ul>
                                <li><a href="{{url('/usuarios/web')}}"><i class="large material-icons">desktop_windows</i></a></li>
                            </ul>
                        </div>
                        <span class="card-title">Usuários web</span>
                        <span class="stats-counter"><span class="counter">{{$assinatura->quantidade_web_user}}</span></span>
                    </div>
                    <div class="progress stats-card-progress">
                        <div class="determinate" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col s12 m12 l4">
                <div class="card stats-card">
                    <div class="card-content">
                        <div class="card-options">
                            <ul>
                                <li><a href="{{url('/assinatura')}}"><i class="material-icons">date_range</i></a></li>
                            </ul>
                        </div>
                        <span class="card-title">Assinatura válida até</span>
                        <span class="stats-counter"><span class="counter">{{Carbon::createFromFormat('Y-m-d',$assinatura->data_final)->format('d/m/Y')}}</span></span>
                    </div>
                    <div class="progress stats-card-progress">
                        <div class="indeterminate" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="middle padding-top-20 padding-right-20">
        <div class="row no-m-t no-m-b">
            <div class="col s12 m12 l8">
                <div class="card visitors-card">
                    <div class="card-content">
                        <div class="card-options">
                            <ul>
                                <li><a href="javascript:void(0)" class="card-refresh"><i class="material-icons">refresh</i></a></li>
                            </ul>
                        </div>
                        <span class="card-title">Visitors<span class="secondary-title">Showing stats from the last week</span></span>
                        <div id="flotchart1"></div>
                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4">
                <div class="card server-card">
                    <div class="card-content">
                        <div class="card-options">
                            <ul>
                                <li class="red-text"><span class="badge blue-grey lighten-3">optimal</span></li>
                            </ul>
                        </div>
                        <span class="card-title">Server Load</span>
                        <div class="server-load row">
                            <div class="server-stat col s4">
                                <p>167GB</p>
                                <span>Usage</span>
                            </div>
                            <div class="server-stat col s4">
                                <p>320GB</p>
                                <span>Space</span>
                            </div>
                            <div class="server-stat col s4">
                                <p>57.4%</p>
                                <span>CPU</span>
                            </div>
                        </div>
                        <div class="stats-info">
                            <ul>
                                <li>Google Chrome<div class="percent-info green-text right">32% <i class="material-icons">trending_up</i></div></li>
                                <li>Safari<div class="percent-info red-text right">20% <i class="material-icons">trending_down</i></div></li>
                                <li>Mozilla Firefox<div class="percent-info green-text right">18% <i class="material-icons">trending_up</i></div></li>
                            </ul>
                        </div>
                        <div id="flotchart2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection



@section('page-scripts')

    <script src="/assets/plugins/waypoints/jquery.waypoints.min.js"></script>
    <script src="/assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
    <script src="/assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="/assets/plugins/chart.js/chart.min.js"></script>
    <script src="/assets/plugins/flot/jquery.flot.min.js"></script>
    <script src="/assets/plugins/flot/jquery.flot.time.min.js"></script>
    <script src="/assets/plugins/flot/jquery.flot.symbol.min.js"></script>
    <script src="/assets/plugins/flot/jquery.flot.resize.min.js"></script>
    <script src="/assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="/assets/plugins/curvedlines/curvedLines.js"></script>
    <script src="/assets/plugins/peity/jquery.peity.min.js"></script>
    <script src="/assets/plugins/switcher/switcher.js"></script>
    <script src="/assets/js/pages/dashboard.js"></script>

    @if(count($filiais) > 1)

        @include('layouts.filial')

    @endif

@endsection

