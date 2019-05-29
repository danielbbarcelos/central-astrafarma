@extends('layouts.template')

@section('page-title', $title)

@section('page-css')

    <link href="{{url('/assets/plugins/datatables/css/jquery.dataTables.css')}}" rel="stylesheet">
    
@endsection

@section('page-content')


    <div class="middle padding-top-20 padding-right-20">
        <div class="row">
            <div class="col s12">
                <div class="page-title"></div>
            </div>
            <div class="col s12">
                <div class="card-panel">
                    <div class="card-content">
                        <span class="card-title">{{$title}}</span><br>
                        <div class="row">
                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Assunto</th>
                                    <th>Reclamante</th>
                                    <th>Status</th>
                                    <th>Aberto em</th>
                                    <th>Funções</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($chamados as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>
                                            @if($item->tipo == 'D')
                                                <i class="material-icons tooltipped" data-position="top" data-delay="20" data-tooltip="Dúvida">contact_support</i>
                                            @elseif($item->tipo == 'E')
                                                <i class="material-icons tooltipped" data-position="top" data-delay="20" data-tooltip="Erro ou problema">report</i>
                                            @elseif($item->tipo == 'F')
                                                <i class="material-icons tooltipped" data-position="top" data-delay="20" data-tooltip="Solicitação de serviço">new_releases</i>
                                            @elseif($item->tipo == 'S')
                                                <i class="material-icons tooltipped" data-position="top" data-delay="20" data-tooltip="Sugestão">check_circle</i>
                                            @endif
                                        </td>
                                        <td>{{$item->assunto}}</td>
                                        <td>{{json_decode($item->responsavel)->name}}</td>
                                        <td>
                                            @if($item->status == 'A')
                                                <span class="label bg-warning">Ag. atendimento</span>
                                            @elseif($item->status == 'C')
                                                <span class="label bg-danger">Cancelado</span>
                                            @elseif($item->status == 'E')
                                                <span class="label bg-info">Em atendimento</span>
                                            @elseif($item->status == 'F')
                                                <span class="label bg-success">Finalizado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span hidden>{{$item->created_at}}</span>
                                            {{Carbon::createFromFormat('Y-m-d H:i:s',$item->created_at)->format('d/m/Y - H:i')}}
                                        </td>
                                        <td>
                                            @if(Permission::check('visualiza','Chamado','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar e interagir" href="{{url('/suporte/chamados/'.$item->id.'/show')}}">
                                                    <i class="material-icons">chat</i>
                                                </a>
                                            @endif
                                        </td>
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
        <div class="fixed-action-btn tooltipped" data-position="top" data-delay="20" data-tooltip="Adicionar" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red modal-trigger" href="#modal-adiciona">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

    <!-- modal para adicionar chamado -->
    @include('pages.chamados.modal-adiciona')

@endsection


@section('page-scripts')

    <script src="{{url('/assets/plugins/datatables/js/jquery.dataTables.js')}}"></script>
    <script src="{{url('/assets/js/pages/chamado.a28ee5703ea4d40ddd04cccd6a5f99f9f.js')}}"></script>
    <script>

        $(document).ready(function(){

            $('.datatable').DataTable({
                language: {
                    searchPlaceholder: 'Procurar',
                    sSearch: '',
                    sLengthMenu: 'Exibir _MENU_',
                    sLength: 'dataTables_length',
                    zeroRecords: "Nenhum chamado encontrado",
                    info: "Exibindo página _PAGE_ de _PAGES_",
                    infoEmpty: "",
                    infoFiltered: "filtrado de _MAX_ itens",
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