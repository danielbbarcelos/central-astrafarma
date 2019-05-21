@extends('layouts.template')

@section('page-title', 'Cliente')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/clientes')}}" class="breadcrumbs__element">Lista de clientes</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Cadastro de cliente</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Cadastro de cliente
                    </span><br>
                    <div >
                        <form class="s12" method="post">

                            {{csrf_field()}}

                            <div class="row row-input padding-bottom-30">
                                <div class="">
                                    <p class="">
                                        <input class="with-gap" required value="J" name="tipo_pessoa" type="radio" id="tipoJ" @if($cliente->tipo_pessoa !== 'F') checked @endif />
                                        <label for="tipoJ" class="margin-5">Jurídica</label>

                                        <input class="with-gap" value="F" name="tipo_pessoa" type="radio" id="tipoF" @if($cliente->tipo_pessoa == 'F') checked @endif />
                                        <label for="tipoF" class="margin-5">Física</label>
                                    </p>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="input-field col s12 m8">
                                    <input type="text" placeholder="" name="razao_social" id="razao_social" class="validate" maxlength="100" required value="{{$cliente->razao_social or old('razao_social')}}">
                                    <label id="label_razao_social">@if($cliente->tipo_pessoa == 'F') Nome completo @else Razão social @endif</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <input type="text" name="nome_fantasia" id="nome_fantasia" class="validate" maxlength="100" placeholder=""
                                           @if($cliente->tipo_pessoa == 'F') disabled @else required @endif
                                           value="{{$cliente->nome_fantasia or old('nome_fantasia')}}">
                                    <label>Nome fantasia</label>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="input-field col s12 m6">
                                    <input type="text" placeholder="" name="cnpj_cpf" id="cnpj_cpf" value="{{$cliente->cnpj_cpf or old('cnpj_cpf')}}"
                                           class="mask-cpf-cnpj" required>
                                    <label>CNPJ/CPF</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <select name="tipo_cliente" class="select2" required style="width: 50%">
                                        <option value="" disabled>Selecione o tipo de cliente</option>
                                        <option value="R" @if($cliente->tipo_cliente == 'R') selected @endif>Revendedor</option>
                                        <option value="F" @if($cliente->tipo_cliente == 'F') selected @endif>Final</option>
                                        <option value="L" @if($cliente->tipo_cliente == 'L') selected @endif>Produtor rural</option>
                                        <option value="S" @if($cliente->tipo_cliente == 'S') selected @endif>Solidário</option>
                                        <option value="X" @if($cliente->tipo_cliente == 'X') selected @endif>Exportação</option>
                                    </select>
                                    <label>Tipo de cliente</label>
                                </div>
                            </div>

                            <!-- Dados de contato -->
                            <div class="row row-input padding-top-20">
                                <div class="input-field col s12 m4">
                                    <input type="text" placeholder="" required name="nome_contato" id="nome_contato" class="masked" maxlength="100" value="{{$cliente->nome_contato or old('nome_contato')}}">
                                    <label>Nome do contato</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <input type="email" placeholder="" required name="email" id="email" class="validate" maxlength="100" value="{{$cliente->email or old('email')}}">
                                    <label>E-mail de contato</label>
                                </div>
                                <div class="input-field col s12 m1">
                                    <input type="text" placeholder="" name="ddd" id="ddd" class="mask-ddd" maxlength="4" value="{{$cliente->ddd or old('ddd')}}">
                                    <label>DDD</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <input type="text" placeholder="" name="fone" id="fone" class="mask-fone" maxlength="10" value="{{$cliente->fone or old('fone')}}">
                                    <label>Fone</label>
                                </div>
                            </div>


                            <!-- Endereço -->
                            <div class="row row-input">
                                <div class="input-field col s12 m6">
                                    <input type="text" placeholder="" required name="endereco" id="endereco" class="validate" maxlength="200" value="{{$cliente->endereco or old('endereco')}}">
                                    <label>Endereço</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <input type="text" placeholder="" required name="endereco_numero" id="endereco_numero" class="validate" maxlength="10" value="{{$cliente->endereco_numero or old('endereco_numero')}}">
                                    <label>Número</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <input type="text" placeholder="" name="complemento" id="complemento" class="validate" maxlength="100" value="{{$cliente->complemento or old('complemento')}}">
                                    <label>Complemento</label>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="input-field col s12 m4">
                                    <input type="text" placeholder="" required name="bairro" id="bairro" class="validate" maxlength="100" value="{{$cliente->bairro or old('bairro')}}">
                                    <label>Bairro</label>
                                </div>
                                <div class="input-field col s12 m2">
                                    <input type="text" placeholder="" required name="cep" id="cep" class="mask-cep" maxlength="9" value="{{$cliente->cep or old('cep')}}">
                                    <label>CEP</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <select class="select2" name="uf" id="uf" style="width: 100%" required>
                                        <option value="" disabled selected>Selecione o estado</option>
                                        @foreach($estados as $item)
                                            <option value="{{$item->uf}}" @if($cliente->uf == $item->uf) selected @endif>{{$item->nome}}</option>
                                        @endforeach
                                    </select>
                                    <label>UF</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <select class="select2" name="cidade" id="cidade" style="width: 100%" required>
                                        @if($cliente->uf == null)
                                            <option value="" disabled selected>Selecione o estado</option>
                                        @elseif($cliente->cidade == null)
                                            <option value="" disabled selected>Selecione a cidade</option>
                                        @endif
                                        @foreach($cidades as $item)
                                            <option value="{{$item->nome}}" @if($cliente->cod_mun == $item->cod_mun) selected @endif>{{$item->nome}}</option>
                                        @endforeach
                                    </select>
                                    <label>Cidade</label>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col s12 padding-top-10">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="status" name="status" value="1" @if((int)$cliente->status == 1) checked @endif />
                                        <label for="status">Cliente ativo</label>
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

    <script src="/assets/js/pages/cliente.d30189274fjsdf7824631.js"></script>


    @if($action == 'read')
        <script>
            $("input,textarea,select").attr('disabled',true);
            $("button[type='submit']").css('display','none');
        </script>
    @endif

@endsection

