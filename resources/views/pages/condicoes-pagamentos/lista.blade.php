@extends('layouts.template')

@section('page-title', 'Condições de pagamento')

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
                        <span class="card-title">Condições de pagamento</span><br>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="display datatable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cód. ERP</th>
                                        <th>Habilitado</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($condicoes as $item)
                                        <tr>
                                            <td>{{$item->id}}</td>
                                            <td>{{$item->erp_id !== null ? $item->erp_id : '-'}}</td>
                                            <td>
                                                @if($item->mobile == '1')
                                                    <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Para mobile e Web">phonelink</i>
                                                @else
                                                    <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Apenas para web">desktop_mac</i>
                                                @endif
                                            </td>
                                            <td>{{$item->descricao}}</td>
                                            <td>
                                                @if($item->status == '1')
                                                    <span class="label bg-success">Ativo</span>
                                                @else
                                                    <span class="label bg-danger">Inativo</span>
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
                    zeroRecords: "Nenhuma condição de pagamento encontrada",
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