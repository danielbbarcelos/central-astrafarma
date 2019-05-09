@extends('layouts.template')

@section('page-title', 'Produtos')

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
                        <span class="card-title">Produtos</span><br>
                        <div class="row">
                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Unidade principal</th>
                                    <th>Preço de venda</th>
                                    <th>Status</th>
                                    <th>Funções</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($produtos as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->descricao}}</td>
                                        <td>{{$item->unidade_principal}}</td>
                                        <td>R$ {{number_format($item->preco_venda,2,',','.')}}</td>
                                        <td>
                                            @if($item->status == '1')
                                                <span class="label bg-success">Ativo</span>
                                            @else
                                                <span class="label bg-danger">Inativo</span>
                                            @endif

                                        </td>
                                        <td class="uk-text-center">
                                            @if(Permission::check('visualiza','Produto','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar" href="{{url('/produtos/'.$item->id.'/show')}}">
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


@endsection


@section('page-scripts')

    <script src="{{url('/assets/plugins/datatables/js/jquery.dataTables.js')}}"></script>
    <script>

        $(document).ready(function(){

            $('.datatable').DataTable({
                order: [[ 1, "asc" ]],
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