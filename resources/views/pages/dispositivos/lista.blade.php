@extends('layouts.template')

@section('page-title', 'Dispositivos')

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
                        <span class="card-title">Dispositivos</span><br>
                        <div class="row">
                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Device ID</th>
                                    <th>Status</th>
                                    <th>Funções</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dispositivos as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->descricao}}</td>
                                        <td>{{$item->device_id}}</td>
                                        <td>
                                            @if($item->status == '1')
                                                <span class="label bg-success">Ativo</span>
                                            @else
                                                <span class="label bg-danger">Inativo</span>
                                            @endif

                                        </td>
                                        <td class="uk-text-center">
                                            @if(Permission::check('visualiza','Dispositivo','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar" href="{{url('/dispositivos/'.$item->id.'/show')}}">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            @endif
                                            @if(Permission::check('edita','Dispositivo','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Editar" href="{{url('/dispositivos/'.$item->id.'/edit')}}">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                            @endif
                                            @if(Permission::check('excluiPost','Dispositivo','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Excluir" onclick="excluiItem('{!! url('/dispositivos/'.$item->id.'/del') !!}')">
                                                    <i class="material-icons icon-danger">delete_forever</i>
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
    @if(Permission::check('adiciona','Dispositivo','Central'))
        <div class="fixed-action-btn tooltipped" data-position="top" data-delay="20" data-tooltip="Adicionar" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red" href="{{url('/dispositivos/add')}}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif


    <!-- form é submetido na confirmação de "onclick" presente na tag "a" de cada item. A action é gerada durante a confirmação da exclusão -->
    <form id="form-delete" method="post" action="">
        {{csrf_field()}}
    </form>


@endsection


@section('page-scripts')

    <script src="{{url('/assets/plugins/datatables/js/jquery.dataTables.js')}}"></script>
    <script>

        $(document).ready(function(){

            $('.datatable').DataTable({
                language: {
                    searchPlaceholder: 'Procurar',
                    sSearch: '',
                    sLengthMenu: 'Exibir _MENU_',
                    sLength: 'dataTables_length',
                    zeroRecords: "Nenhum dispositivo encontrado",
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