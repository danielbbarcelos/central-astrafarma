@extends('layouts.template')

@section('page-title', 'Principal')

@section('page-css')

    <link href="/assets/plugins/bower-components/weather-icons/css/weather-icons.min.css" media="all">
    <link href="/assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
    <link href="/assets/plugins/switcher/switcher.css" rel="stylesheet">

@endsection

@section('page-content')

    @if($dashboard->assinatura_status == '1')
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
                            <span class="stats-counter"><span class="counter font-weight-400">{{Carbon::createFromFormat('Y-m-d',$assinatura->data_final)->format('d/m/Y')}}</span></span>
                        </div>
                        <div class="progress stats-card-progress">
                            <div class="determinate" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if($dashboard->bi_status == '1')
        <div class="middle padding-top-20 padding-right-20 padding-left-15">
            <iframe id="bi-dashboard" width="100%" height="600" src="{{$dashboard->bi_url}}" frameborder="0" allowFullScreen="true"></iframe>
        </div>
    @endif

    {{--<div class="middle padding-top-20 padding-right-20">
        <div class="row no-m-t no-m-b">
            @include('pages.dashboard.pedido-venda')
            @include('pages.dashboard.conexao')
        </div>
    </div>--}}


    <form id="form-delete" method="post" action="{{url('/teste-plugin')}}">
        {{csrf_field()}}
    </form>

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

    @if($dashboard->bi_status == '1')
        <script>
            $('#page-main').position();
        </script>
    @endif

@endsection

