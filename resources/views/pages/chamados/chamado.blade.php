@extends('layouts.template')

@section('page-title', 'Chamado #'.$chamado->id)

@section('page-css')

@endsection


@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/suporte/chamados')}}" class="breadcrumbs__element">Lista de chamado</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Chamado #{{$chamado->id}}</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="card padding-right-15 padding-left-15 card-transparent no-m">
        <div class="card-content no-s">
            <div class="z-depth-1 search-tabs">
                <div class="search-tabs-container">
                    <div class="col s12 m12 l12">
                        <div class="row ticket-row padding-bottom-20">
                            <div class="ticket-header">
                                <h6><b>Chamado #{{$chamado->id.': '.$chamado->assunto}}</b></h6>
                            </div>
                            <h6 class=" padding-bottom-20">Aberto por {{json_decode($chamado->responsavel)->name}} (<b>{{json_decode($chamado->responsavel)->email}}</b>)</h6>
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
            <!-- mensagem de abertura do chamado -->
            <li>
                <div class="timeline-badge">
                    <img src="{{url('/assets/img/icons/tickets/I.png')}}" width="30" alt="Picture" style="padding-top: 8px">
                </div>
                <div class="timeline-panel">

                    <div class="row padding-bottom-20">
                        <div class="col s6 m6 l6 left-align">
                            <label class="timeline-title tooltipped cursor-pointer" data-position="top" data-delay="10" data-tooltip="{{'Nome completo: '.json_decode($chamado->responsavel)->name}}">
                                Por: {{explode(' ',json_decode($chamado->responsavel)->name)[0]}}
                            </label>
                        </div>
                        <div class="col s6 m6 l6 right-align">
                            <small class="text-muted right-align">{{Carbon::createFromFormat('Y-m-d H:i:s',$chamado->created_at)->format('d/m/Y à\s H:i:s')}}</small>
                        </div>
                    </div>

                    <div class="timeline-body">
                        <div class="col s12">
                            <p>{!! str_replace("\n","<br>",$chamado->mensagem) !!}</p>
                        </div>

                        @if($chamado->upload !== null)
                            <a href="{{$chamado->upload}}" class="waves-effect text-muted padding-top-30">
                                <i class="material-icons text-primary">attachment</i>
                            </a>
                        @endif
                    </div>
                </div>
            </li>

            <!-- interações -->
            @foreach($chamadoInteracoes as $item)
                @if($item->oculto !== 1)
                    <li @if($item->empresa_user_id == null) class="timeline-inverted" @endif>
                        <div class="timeline-badge">
                            <img src="{{url('/assets/img/icons/tickets/'.$item->acao.'.png')}}" width="30" alt="Picture" style="padding-top: 8px">
                        </div>
                        <div class="timeline-panel">

                            <div class="row padding-bottom-20">
                                <div class="col s6 m6 l6 left-align">
                                    <label class="timeline-title tooltipped cursor-pointer" data-position="top" data-delay="10" data-tooltip="{{'Nome completo: '.json_decode($item->responsavel)->name}}">
                                        Por: {{explode(' ',json_decode($item->responsavel)->name)[0]}}
                                    </label>
                                </div>
                                <div class="col s6 m6 l6 right-align">
                                    <small class="text-muted right-align">{{Carbon::createFromFormat('Y-m-d H:i:s',$item->created_at)->format('d/m/Y à\s H:i:s')}}</small>
                                </div>
                            </div>


                            <div class="timeline-body">
                                <div class="col s12">
                                    <p>{!! str_replace("\n","<br>",$item->mensagem) !!}</p>
                                </div>

                                @if($item->upload !== null)
                                    <a href="{{$item->upload}}" class="waves-effect text-muted padding-top-30">
                                        <i class="material-icons text-primary">attachment</i>
                                    </a>
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
            <div class="middle padding-top-20 padding-right-15">
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
                                                    <input type="file" name="upload" id="upload">
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
                                                    <label for="tipo1">
                                                        <span class="label bg-warning">Apenas interagir</span>
                                                    </label>

                                                    <input class="with-gap" value="C" name="acao" type="radio" id="tipo2" />
                                                    <label for="tipo2">
                                                        <span class="label bg-danger">Cancelar</span>
                                                    </label>

                                                    <input class="with-gap" value="F" name="acao" type="radio" id="tipo3" />
                                                    <label for="tipo3">
                                                        <span class="label bg-primary">Solucionar</span>
                                                    </label>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col s12 right-align">
                                            <button type="submit" class="waves-effect waves-light btn blue btn-submit">Confirmar</button>
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

    <script src="{{url('/assets/js/pages/chamado.b28ee5703ea4d40ddd04cccd6a5f99f9f.js')}}"></script>


@endsection