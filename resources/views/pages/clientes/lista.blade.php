@extends('layouts.template')

@section('page-title', 'Clientes')

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
                        <span class="card-title">Clientes</span><br>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="display datatable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cód. ERP</th>
                                        <th>Razão Social/Nome</th>
                                        <th>CNPJ/CPF</th>
                                        <th>Status</th>
                                        <th>Funções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clientes as $item)
                                        <tr>
                                            <td>{{$item->id}}</td>
                                            <td>{{$item->erp_id !== null ? $item->erp_id : '-'}}</td>
                                            <td>{{$item->razao_social}}</td>
                                            <td>{{Helper::insereMascara($item->cnpj_cpf, $item->tipo_pessoa == 'F' ? '###.###.###-##' : '##.###.###/####-##')}}</td>
                                            <td>
                                                @if($item->status == '1')
                                                    <span class="label bg-success">Ativo</span>
                                                @else
                                                    <span class="label bg-danger">Inativo</span>
                                                @endif

                                            </td>
                                            <td class="uk-text-center">
                                                @if(Permission::check('edita','Cliente','Central'))
                                                    <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Editar" href="{{url('/clientes/'.$item->id.'/edit')}}">
                                                        <i class="material-icons">edit</i>
                                                    </a>
                                                @elseif(Permission::check('visualiza','Cliente','Central'))
                                                    <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar" href="{{url('/clientes/'.$item->id.'/show')}}">
                                                        <i class="material-icons">visibility</i>
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
    </div>


    <!-- Botão para adicionar -->
    @if(Permission::check('adiciona','PedidoVenda','Central'))
        <div class="fixed-action-btn tooltipped" data-position="top" data-delay="20" data-tooltip="Adicionar" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red" href="{{url('/clientes/add')}}">
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
                    zeroRecords: "Nenhum cliente encontrado",
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