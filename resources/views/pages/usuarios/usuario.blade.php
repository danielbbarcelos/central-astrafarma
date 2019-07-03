@extends('layouts.template')

@section('page-title', 'Usuário')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/usuarios')}}" class="breadcrumbs__element">Lista de usuários</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Cadastro de usuário</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Cadastro de usuário
                    </span><br>
                    <div class="row">
                        <form method="post">

                            {{csrf_field()}}

                            <div class="row row-input">
                                <div class="input-field col s12">
                                    <input type="text" placeholder="" name="name" class="validate" maxlength="100" required value="{{$user->name or old('name')}}">
                                    <label>Nome</label>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="input-field col s12 @if($action == 'create') m6 @endif">
                                    <input type="email" placeholder="" name="email" class="validate" maxlength="100" required value="{{$user->email or old('email')}}">
                                    <label>E-mail de acesso</label>
                                </div>

                                @if($action == 'create')
                                    <div class="input-field col s12 m6">
                                        <input type="password" placeholder="" name="password" class="validate" maxlength="30" required value="">
                                        <label>Senha de acesso</label>
                                    </div>
                                @endif
                            </div>


                            <div class="row row-input">
                                <div class="input-field col s12 m12">
                                    <select name="vxwebperfil_id" id="vxwebperfil_id" @if($user->type == 'A') disabled @endif required class="select2">
                                        @if($user->vxwebperfil_id == null)
                                            <option value="" selected disabled>Selecione...</option>
                                        @endif
                                        @foreach($perfis as $item)
                                            <option value="{{$item->id}}" @if($item->id == $user->vxwebperfil_id) selected @endif>{{$item->nome}}</option>
                                        @endforeach
                                    </select>
                                    <label class="active">Perfil de acesso</label>
                                </div>
                            </div>


                            <div class="row row-input">
                                <div class="input-field col s12 m12">
                                    <select name="empfil[]" id="empfil" multiple required class="select-material">
                                        @if(count($userFiliais) == 0)
                                            <option value="" disabled selected>Selecione...</option>
                                        @endif
                                        @foreach($filiais as $item)
                                            <option value="{{$item->id}}" @if(in_array($item->id, $userFiliais)) selected @endif>{{$item->filial_erp_id.' - '.$item->nome}}</option>
                                        @endforeach
                                    </select>
                                    <label>Filiais habilitadas para o usuário</label>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="input-field col s12 m12">
                                    <select name="vxfatvend_id" id="vxfatvend_id" class="select2">
                                        <option value="" selected>N/A</option>
                                        @foreach($vendedores as $item)
                                            <option value="{{$item->id}}" @if($item->id == $user->vxfatvend_id) selected @endif
                                                >{{$item->erp_id .': '. $item->nome.(($item->cpf !== '' and $item->cpf !== null) ? ' - CPF: '.Helper::insereMascara($item->cpf,'###.###.###-##') : ' - CPF não informado')}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="active">Vendedor vinculado ao usuário</label>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="col s12">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="web" name="web" value="1" @if($user->web == '1') checked @endif />
                                        <label for="web" class="font-weight-500 text-dark">Usuário com acesso a web</label>
                                    </p>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="col s12">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="mobile" name="mobile" value="1" @if($user->mobile == '1') checked @endif />
                                        <label for="mobile" class="font-weight-500 text-dark">Usuário com acesso ao aplicativo</label>
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col s12 padding-top-10">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="status" name="status" value="1" @if($user->status == '1') checked @endif />
                                        <label for="status" class="font-weight-500 text-dark">Usuário ativo</label>
                                    </p>
                                </div>
                            </div>

                            <div class="col s12 right-align">
                                <button type="submit" class="waves-effect waves-light btn blue btn-submit">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



@section('page-scripts')


    @if($user->type == 'A')

        <script>
            $("input:not([name='email']):not([name='_token']),select:not([name='vxwebperfil_id']):not([name='vxfatvend_id'])").attr('disabled',true);
        </script>

    @endif

    @if($action == 'read')
        <script>
            $("input,textarea,select").attr('disabled',true);
            $("button[type='submit']").css('display','none');
        </script>
    @endif

@endsection

