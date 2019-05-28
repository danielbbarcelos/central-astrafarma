@extends('layouts.template')

@section('page-title', 'Usuários')

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
                        <span class="card-title">Usuários</span><br>
                        <div class="">
                            <table class="display responsive-table datatable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Acesso</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th>Funções</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td class="text-center">
                                            @if($item->mobile == '1' and $item->web == '1')
                                                <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Mobile e Web">phonelink</i>
                                            @elseif($item->mobile == '1' and $item->web == '0')
                                                <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Apenas mobile">phone_android</i>
                                            @elseif($item->mobile == '0' and $item->web == '1')
                                                <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Apenas web">desktop_mac</i>
                                            @else
                                                <i class="material-icons icon-default tooltipped cursor-pointer" data-position="top" data-delay="20" data-tooltip="Nenhum acesso configurado">phonelink_off</i>
                                            @endif
                                        </td>
                                        <td>
                                            {{$item->name}}

                                            @if($item->type == 'A')
                                                <i class="material-icons icon-warning font-size-14">grade</i>
                                            @endif
                                        </td>
                                        <td>{{$item->email}}</td>
                                        <td>
                                            @if($item->status == '1')
                                                <span class="label bg-success">Ativo</span>
                                            @else
                                                <span class="label bg-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(Permission::check('configuracao','User','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Configurações gerais" href="{{url('/usuarios/'.$item->id.'/configuracoes')}}">
                                                    <i class="material-icons">settings</i>
                                                </a>
                                            @endif
                                            @if(Permission::check('edita','User','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Cadastro" href="{{url('/usuarios/'.$item->id.'/edit')}}">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                            @elseif(Permission::check('visualiza','User','Central'))
                                                <a class="waves-effect margin-5 white tooltipped waves-light btn m-b-xs" data-position="top" data-delay="10" data-tooltip="Visualizar" href="{{url('/usuarios/'.$item->id.'/show')}}">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            @endif
                                            @if(Permission::check('excluiPost','User','Central'))
                                                <a class="waves-effect margin-5 white @if($item->type !== 'A') tooltipped @endif waves-light btn m-b-xs" @if($item->type == 'A') disabled="" @else data-position="top" data-delay="10" data-tooltip="Excluir" onclick="excluiItem('{!! url('/usuarios/'.$item->id.'/del') !!}')" @endif>
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
    @if(Permission::check('adiciona','User','Central'))
        <div class="fixed-action-btn tooltipped" data-position="top" data-delay="10" data-tooltip="Adicionar" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red" href="{{url('/usuarios/add')}}">
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
                    zeroRecords: "Nenhum usuário encontrado",
                    info: "Exibindo página _PAGE_ de _PAGES_",
                    infoEmpty: "asd",
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