@extends('layouts.template')

@section('page-title', 'Logs de sincronização')

@section('page-css')

    <link href="{{url('/assets/plugins/datatables/css/jquery.dataTables.css')}}" rel="stylesheet">

@endsection

@section('page-content')


    <div class="middle padding-top-20 padding-right-20">
        <div class="row">
            <div class="col s12">
                <div class="page-title"></div>
            </div>
            <div class="col s12 ">
                <div class="card-panel">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s8">
                                <h6 class="card-title font-weight-600 font-size-16">
                                    Logs de sincronização

                                    @if(strpos(Request::getRequestUri(), 'com-sucesso') == true)
                                        com sucesso
                                    @elseif(strpos(Request::getRequestUri(), 'pendentes') == true)
                                        pendentes
                                    @elseif(strpos(Request::getRequestUri(), 'sem-sucesso') == true)
                                        com erros
                                    @endif
                                </h6>
                            </div>
                            <div class="col s4">
                                <div class="" style="float: right !important;">
                                    <i onclick="javascript:window.location='/vex-sync/logs/com-sucesso'" class="tooltipped material-icons font-size" style="z-index: 9999; color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Com sucesso">fiber_manual_record</i>
                                    <i onclick="javascript:window.location='/vex-sync/logs/pendentes'" class="tooltipped material-icons" style="z-index: 9999; color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Pendentes">fiber_manual_record</i>
                                    <i onclick="javascript:window.location='/vex-sync/logs/sem-sucesso'" class="tooltipped material-icons" style="z-index: 9999; color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Com erros">fiber_manual_record</i>
                                </div>
                            </div>
                        </div>


                        <br>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="display datatable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Método</th>
                                        <th>Entidade</th>
                                        <th>WebService</th>
                                        @if(strpos(Request::getRequestUri(), 'sem-sucesso') == true)
                                            <th>Tentativas</th>
                                            <th>Status</th>
                                        @endif
                                        <th>Últ. atualização</th>
                                        @if((strpos(Request::getRequestUri(), 'sem-sucesso') == true) and Permission::check('adicionaPost','Chamado','Central'))
                                            <th>Funções</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($syncs as $item)
                                        <tr>
                                            <td>
                                                <span hidden>{{$item->id}}</span>
                                                @if(strpos(Request::getRequestUri(), 'com-sucesso') == true)
                                                    <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Com sucesso">fiber_manual_record</i>
                                                @elseif(strpos(Request::getRequestUri(), 'pendentes') == true)
                                                    <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Pendentes">fiber_manual_record</i>
                                                @elseif(strpos(Request::getRequestUri(), 'sem-sucesso') == true)
                                                    <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Com erros">fiber_manual_record</i>
                                                @endif

                                                {{$item->id}}
                                            </td>
                                            <td>{{strtoupper($item->action)}}</td>
                                            <td>{{Aliases::entityByTable($item->tabela).' #'.$item->tabela_id}}</td>
                                            <td>{{$item->webservice}}</td>
                                            @if(strpos(Request::getRequestUri(), 'sem-sucesso') == true)
                                                <td>{{$item->tentativa}}</td>
                                                <td>
                                                    @if($item->bloqueado == '0')
                                                        <span style="color: #709dad" class="font-weight-700">Desbloqueado</span>
                                                    @else
                                                        <span style="color: #b60b23" class="font-weight-700">Bloqueado</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <span hidden>{{$item->updated_at}}</span>
                                                {{Carbon::createFromFormat('Y-m-d H:i:s',$item->updated_at)->format('d/m/Y - H:i:s')}}
                                            </td>
                                            @if((strpos(Request::getRequestUri(), 'sem-sucesso') == true) and Permission::check('adicionaPost','Chamado','Central'))
                                                <td class="uk-text-center">
                                                    @if((int)$item->bloqueado == 0)
                                                        <a href="{{url('/vex-sync/logs/'.$item->id.'/status')}}"
                                                           class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Bloquear sincronização">
                                                            <i class="material-icons" style="color: #b60b23">block</i>
                                                        </a>
                                                    @else
                                                        <a href="{{url('/vex-sync/logs/'.$item->id.'/status')}}"
                                                           class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Desbloquear sincronização">
                                                            <i class="material-icons" style="color: #46ab7f">check</i>
                                                        </a>
                                                    @endif
                                                    <a onclick="novoChamado('{!! $item->id !!}','{!! strtoupper($item->action) !!}','{!! Aliases::entityByTable($item->tabela) !!}','{!! $item->tabela_id !!}','{!! $item->webservice !!}','{!! Carbon::createFromFormat('Y-m-d H:i:s',$item->updated_at)->format('d/m/Y - H:i:s') !!}','{!! $item->log !!}')"
                                                       class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Abrir chamado">
                                                        <i class="material-icons">help</i>
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="fixed-action-btn tooltipped" data-position="top" data-delay="10" data-tooltip="Sincronizar" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red" id="btn-sync">
            <i id="icon-sync1" class="fa fa-redo-alt" style="display: block"></i>
            <i id="icon-sync2" class="fa fa-sync-alt fa-spin" style="display: none"></i>
        </a>
    </div>


    <!-- Botão para adicionar -->
    @if(Permission::check('adicionaPost','Chamado','Central'))
        @include('pages.chamados.modal-adiciona')
    @endif


@endsection


@section('page-scripts')

    <script src="{{url('/assets/plugins/datatables/js/jquery.dataTables.js')}}"></script>
    <script src="{{url('/assets/js/pages/chamado.b28ee5703ea4d40ddd04cccd6a5f99f9f.js')}}"></script>
    <script>

        $(document).ready(function(){

            $('.datatable').DataTable({
                order: [[ 0, "desc" ]],
                language: {
                    searchPlaceholder: 'Procurar',
                    sSearch: '',
                    sLengthMenu: 'Exibir _MENU_',
                    sLength: 'dataTables_length',
                    zeroRecords: "Nenhum registro encontrado",
                    info: "Exibindo página _PAGE_ de _PAGES_",
                    infoEmpty: "",
                    infoFiltered: "(filtrado de _MAX_ itens)",
                    oPaginate: {
                        sFirst: '<i class="material-icons">chevron_left</i>',
                        sPrevious: '<i class="material-icons">chevron_left</i>',
                        sNext: '<i class="material-icons">chevron_right</i>',
                        sLast: '<i class="material-icons">chevron_right</i>' 
                }
                }
            });
            $('.dataTables_length select').addClass('browser-default');

        });


        $("#btn-sync").on("click",function(){
            pageBlockUI('Por favor aguarde. VEX Sync em execução...');

            window.location = '/api/v1/vex-sync/sincroniza?web=true'
        });

    </script>

@endsection