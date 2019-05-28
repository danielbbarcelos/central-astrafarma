@extends('layouts.template')

@section('page-title', 'Pedidos de venda')

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
                        <span class="card-title">Pedidos de venda</span><br>
                        <div class="row">
                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cód. ERP</th>
                                    <th>Cliente</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Valor total (R$)</th>
                                    <th>Status</th>
                                    <th>Funções</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pedidos as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->erp_id !== null ? $item->erp_id : '-'}}</td>
                                        <td>{{$item->cliente->razao_social}}</td>
                                        <td>{{Helper::insereMascara($item->cliente->cnpj_cpf, $item->cliente->tipo_pessoa == 'J' ? '##.###.###/####-##' : '###.###.###-##')}}</td>
                                        <td>R$ {{number_format($item->valorTotal(),2,',','.')}}</td>
                                        <td>
                                            @if($item->situacao_pedido == 'A')
                                                <i class="tooltipped material-icons" style="color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Aberto">fiber_manual_record</i>
                                            @elseif($item->situacao_pedido == 'B')
                                                <i class="tooltipped material-icons" style="color: #fe8c2d; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de crédito">fiber_manual_record</i>
                                            @elseif($item->situacao_pedido == 'C')
                                                <i class="tooltipped material-icons" style="color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio comercial">fiber_manual_record</i>
                                            @elseif($item->situacao_pedido == 'E')
                                                <i class="tooltipped material-icons" style="color: #1f9dc2; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de estoque">fiber_manual_record</i>
                                            @elseif($item->situacao_pedido == 'F')
                                                <i class="tooltipped material-icons" style="color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Faturado">fiber_manual_record</i>
                                            @endif
                                        </td>
                                        <td class="uk-text-center">
                                            @if(Permission::check('visualiza','PedidoVenda','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar" href="{{url('/pedidos-vendas/'.$item->id.'/show')}}">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            @endif
                                            @if(Permission::check('imprimePDF','PedidoVenda','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Imprimir" href="{{url('/pedidos-vendas/'.$item->id.'/pdf')}}" target="_blank">
                                                    <i class="material-icons">print</i>
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
    @if(Permission::check('adiciona','Cliente','Central'))
        <div class="fixed-action-btn tooltipped" data-position="top" data-delay="20" data-tooltip="Adicionar" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red" href="{{url('/pedidos-vendas/add')}}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

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
                    zeroRecords: "Nenhum pedido encontrado",
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