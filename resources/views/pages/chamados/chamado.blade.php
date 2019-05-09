@extends('layouts.template')

@section('page-title', 'Chamado #'.$chamado->id)

@section('page-css')

    <style>
        .timeline {
            list-style: none;
            padding: 10px 0 10px;
            position: relative;
        }
        .timeline:before {
            top: 0;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 5px;
            background-color: #ffffff;
            left: 50%;
            margin-left: -1.5px;
        }
        .timeline > li {
            margin-bottom: 20px;
            position: relative;
        }
        .timeline > li:before,
        .timeline > li:after {
            content: " ";
            display: table;
        }
        .timeline > li:after {
            clear: both;
        }
        .timeline > li:before,
        .timeline > li:after {
            content: " ";
            display: table;
        }
        .timeline > li:after {
            clear: both;
        }
        .timeline > li > .timeline-panel {
            width: 45%;
            float: left;
            border: 1px solid #ffffff;
            border-radius: 2px;
            background-color: #fff;
            padding: 20px;
            position: relative;
            -webkit-box-shadow: 0 1px 6px rgb(196, 196, 196);
            box-shadow: 0 1px 6px rgb(196, 196, 196);
        }
        .timeline > li > .timeline-panel:before {
            position: absolute;
            top: 26px;
            right: -8px;
            display: inline-block;
            border-top: 8px solid transparent;
            border-left: 8px solid #fff;
            border-right: 0 solid #fff;
            border-bottom: 8px solid transparent;
            content: " ";
        }
        .timeline > li > .timeline-panel:after {
            position: absolute;
            top: 27px;
            right: -10px;
            display: inline-block;
            border-top: 8px solid transparent;
            border-left: 8px solid #fff;
            border-right: 0 solid #fff;
            border-bottom: 8px solid transparent;
            content: " ";
        }
        .timeline > li > .timeline-badge {
            color: #35aab2;
            width: 50px;
            height: 50px;
            line-height: 50px;
            font-size: 1.4em;
            text-align: center;
            position: absolute;
            top: 16px;
            left: 50%;
            margin-left: -25px;
            background-color: #ffffff;
            z-index: 100;
            border-radius: 50%;
            border: 2px solid #ededed
        }
        .timeline > li.timeline-inverted > .timeline-panel {
            float: right;
        }
        .timeline > li.timeline-inverted > .timeline-panel:before {
            border-left-width: 0;
            border-right-width: 15px;
            left: -15px;
            right: auto;
        }
        .timeline > li.timeline-inverted > .timeline-panel:after {
            border-left-width: 0;
            border-right-width: 14px;
            left: -14px;
            right: auto;
        }
        .timeline-badge.primary {
            background-color: #2e6da4 !important;
        }
        .timeline-badge.success {
            background-color: #3f903f !important;
        }
        .timeline-badge.warning {
            background-color: #f0ad4e !important;
        }
        .timeline-badge.danger {
            background-color: #d9534f !important;
        }
        .timeline-badge.info {
            background-color: #5bc0de !important;
        }
        .timeline-title {
            margin-top: 0;
            color: inherit;
        }
        .timeline-body > p,
        .timeline-body > ul {
            margin-bottom: 0;
        }
        .timeline-body > p + p {
            margin-top: 5px;
        }
    </style>

@endsection

@section('page-content')

    <div class="card padding-right-30 padding-top-30 card-transparent no-m">
        <div class="card-content no-s">
            <div class="z-depth-1 search-tabs">
                <div class="search-tabs-container">
                    <div class="col s12 m12 l12">

                        <div class="row ticket-row padding-bottom-20">
                            <div class="ticket-header">
                                <h5>Chamado #{{$chamado->id}}</h5>
                            </div>
                            <h6>Responsável: {{json_decode($chamado->responsavel)->name}}</h6>
                            <h6>E-mail: {{json_decode($chamado->responsavel)->email}}</h6>
                            <h6>Assunto: {{$chamado->assunto}}</h6>
                            <h6>Mensagem: {{$chamado->mensagem}}</h6>
                        </div>

                        <div class="row ticket-row ticket-container grey lighten-4">
                            <div class="col s12 m6 l6 left-align search-stats">
                                <span class="m-r-sm">
                                    Status
                                    @if($chamado->status == 'A')
                                        <span class="label bg-warning">Ag. atendimento</span>
                                    @elseif($chamado->status == 'C')
                                        <span class="label bg-danger">Cancelado</span>
                                    @elseif($chamado->status == 'E')
                                        <span class="label bg-info">Em atendimento</span>
                                    @elseif($chamado->status == 'F')
                                        <span class="label bg-success">Finalizado</span>
                                    @endif
                                </span>
                            </div>
                            <div class="col s12 m6 l6 right-align search-stats">
                                <span class="m-r-sm">Aberto em {{Carbon::createFromFormat('Y-m-d H:i:s',$chamado->created_at)->format('d/m/Y à\s H:i:s')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <div class="container">
        <ul class="timeline">
            @foreach($chamadoInteracoes as $item)
                @if($item->oculto !== 1)
                    <li @if($item->empresa_user_id == null) class="timeline-inverted" @endif>
                        <div class="timeline-badge">
                            <img src="{{url('/assets/img/icons/tickets/'.$item->acao.'.png')}}" width="30" alt="Picture" style="padding-top: 8px">
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <label class="timeline-title">Por: {{json_decode($item->responsavel)->name}}</label>
                                <p><small class="text-muted">{{Carbon::createFromFormat('Y-m-d H:i:s',$item->created_at)->format('d/m/Y à\s H:i:s')}}</small></p>
                            </div>
                            <div class="timeline-body">
                                <div class="col s12">
                                    <p>{!! str_replace("\n","<br>",$item->mensagem) !!}</p>
                                </div>

                                @if($item->upload !== null)
                                    <a href="{{$item->upload}}" class="waves-effect waves-blue padding-top-30"><small>Baixar anexo</small></a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>






    @if(Permission::check('interagePost','Chamado','Central'))
        @if($chamado->status !== 'C' and $chamado->status !== 'F')
            <div class="middle padding-top-20 padding-right-20">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title"></div>
                    </div>
                    <div class="col l12 m12 s12">
                        <div class="card">
                            <div class="card-content">
                                <span class="padding-left-10 card-title">Interagir no chamado</span><br>
                                <div class="row">
                                    <form class="s12" method="post" enctype="multipart/form-data" action="{{url('/suporte/chamados/'.$chamado->id.'/edit')}}">

                                        {{csrf_field()}}

                                        <div class="row row-input">
                                            <div class="file-field input-field col s12">
                                                <div class="btn teal lighten-1">
                                                    <input type="file" name="upload">
                                                    <span>Anexo</span>
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text" placeholder="Envie o arquivo em formato ZIP">
                                                </div>
                                            </div>

                                            <div class="input-field col s12">
                                                <textarea class="materialize-textarea" required name="mensagem" style="height: 6rem"
                                                        maxlength="10000" length="10000">{{old('mensagem')}}</textarea>
                                                <label>Mensagem</label>
                                            </div>

                                            <div class="col s12">
                                                <p class="">
                                                    <input class="with-gap" required value="I" name="acao" type="radio" id="tipo1" />
                                                    <label for="tipo1" class="new badge warning">Apenas interagir</label>

                                                    <input class="with-gap" value="C" name="acao" type="radio" id="tipo2" />
                                                    <label for="tipo2">Cancelar</label>

                                                    <input class="with-gap" value="F" name="acao" type="radio" id="tipo3" />
                                                    <label for="tipo3">Solucionar</label>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col s12 right-align">
                                            <button type="submit" class="waves-effect waves-light btn blue">Confirmar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    

@endsection


@section('page-scripts')

@endsection