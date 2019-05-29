@extends('layouts.template')

@section('page-title', 'Logs de syncronização')

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
                                    Logs de syncronização

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





                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Método</th>
                                    <th>Entidade</th>
                                    <th>ID da entidade</th>
                                    <th>WebService</th>
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
                                        <td>{{Aliases::entityByTable($item->tabela)}}</td>
                                        <td>{{$item->tabela_id}}</td>
                                        <td>{{$item->webservice}}</td>
                                        <td>
                                            <span hidden>{{$item->updated_at}}</span>
                                            {{Carbon::createFromFormat('Y-m-d H:i:s',$item->updated_at)->format('d/m/Y - H:i:s')}}
                                        </td>
                                        @if((strpos(Request::getRequestUri(), 'sem-sucesso') == true) and Permission::check('adicionaPost','Chamado','Central'))
                                            <td class="uk-text-center">
                                                <a onclick="novoChamado('{!! $item->id !!}','{!! strtoupper($item->action) !!}','{!! Aliases::entityByTable($item->tabela) !!}','{!! $item->tabela_id !!}','{!! $item->webservice !!}','{!! Carbon::createFromFormat('Y-m-d H:i:s',$item->updated_at)->format('d/m/Y - H:i:s') !!}','{!! str_replace(["\n",'"'],["",""],json_decode($item->log)->mensagem) !!}')"
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




    <!-- Botão para adicionar -->
    @if(Permission::check('adicionaPost','Chamado','Central'))
        @include('pages.chamados.modal-adiciona')
    @endif


@endsection


@section('page-scripts')

    <script src="{{url('/assets/plugins/datatables/js/jquery.dataTables.js')}}"></script>
    <script src="{{url('/assets/js/pages/chamado.a28ee5703ea4d40ddd04cccd6a5f99f9f.js')}}"></script>
    <script>

        $(document).ready(function(){

            $('.datatable').DataTable({
                order: [[ 0, "desc" ]],
                language: {
                    searchPlaceholder: 'Procurar',
                    sSearch: '',
                    sLengthMenu: 'Exibir _MENU_',
                    sLength: 'dataTables_length',
                    zeroRecords: "Nenhum produto encontrado",
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

    </script>

@endsection