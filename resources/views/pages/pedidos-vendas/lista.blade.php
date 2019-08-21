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
                            <div class="table-responsive">
                                <table class="display datatable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th style="width: 10%">Cód. ERP</th>
                                        <th style="width: 30%">Cliente</th>
                                        <th style="width: 20%">CPF/CNPJ</th>
                                        <th style="width: 15%">Valor total (R$)</th>
                                        <th style="width: 5%">Status</th>
                                        <th style="width: 15%">Funções</th>
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
                                                    <i class="tooltipped material-icons" style="color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Aberto">fiber_manual_record</i>
                                                @elseif($item->situacao_pedido == 'B')
                                                    <i class="tooltipped material-icons" style="color: #1f9dc2; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de crédito">fiber_manual_record</i>
                                                @elseif($item->situacao_pedido == 'E')
                                                    <i class="tooltipped material-icons" style="color: #a3a3a3; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de estoque">fiber_manual_record</i>
                                                @elseif($item->situacao_pedido == 'F')
                                                    <i class="tooltipped material-icons" style="color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Faturado">fiber_manual_record</i>
                                                @elseif($item->situacao_pedido == 'L')
                                                    <i class="tooltipped material-icons" style="color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Liberado para faturamento">fiber_manual_record</i>
                                                @elseif($item->situacao_pedido == 'S')
                                                    <i class="tooltipped material-icons" style="z-index: 9999; color: #fe8c2d; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Pedido em análise de estoque">fiber_manual_record</i>
                                                @endif
                                            </td>
                                            <td class="uk-text-center">
                                                @if(Permission::check('visualiza','PedidoVenda','Central'))
                                                    <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar e editar" href="{{url('/pedidos-vendas/'.$item->id.'/show')}}">
                                                        <i class="material-icons">edit</i>
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
                order: [[ 0, "desc" ]],
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