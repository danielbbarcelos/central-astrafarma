@extends('layouts.template')

@section('page-title', 'Produto')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/produtos')}}" class="breadcrumbs__element">Lista de produtos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Cadastro de produto</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row padding-right-20">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Cadastro de produto
                    </span><br>
                    <div class="row">
                        <form class="s12" method="post">

                            {{csrf_field()}}

                            <div class="row row-input">
                                <div class="input-field col s3">
                                    <input type="text" maxlength="100" readonly value="{{$produto->erp_id}}">
                                    <label>Código e Descrição</label>
                                </div>
                                <div class="input-field col s9">
                                    <input type="text" maxlength="100" readonly value="{{$produto->descricao}}">
                                    <label>Código e Descrição</label>
                                </div>
                            </div>


                            <div class="row row-input">
                                <div class="input-field col s3">
                                    <input type="text" maxlength="100" readonly
                                           @if($produto->tipo == 'MC')
                                           value="Material de consumo"
                                           @elseif($produto->tipo == 'ME')
                                           value="Mercadoria"
                                           @elseif($produto->tipo == 'MP')
                                           value="Matéria-prima"
                                           @elseif($produto->tipo == 'PA')
                                           value="Produto acabado"
                                           @elseif($produto->tipo == 'PI')
                                           value="Produto intermediário"
                                           @elseif($produto->tipo == 'SV')
                                           value="Serviço"
                                           @else
                                           value="Não informado"
                                            @endif>
                                    <label>Tipo</label>
                                </div>

                                <div class="input-field col s3">
                                    <input type="text" maxlength="100" readonly value="{{$produto->unidade_principal or 'Não informado'}}">
                                    <label>Unidade principal</label>
                                </div>

                                <div class="input-field col s3">
                                    <input type="text" maxlength="100" readonly value="{{$produto->unidade_principal or 'Não informado'}}">
                                    <label>Unidade secundária</label>
                                </div>


                                <div class="input-field col s3">
                                    <input type="text" maxlength="100" readonly value="{{$produto->fabricante or 'Não informado'}}">
                                    <label>Fabricante</label>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col s12 padding-top-10">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="status" name="status" value="1" @if((int)$produto->status == 1) checked @endif disabled />
                                        <label for="status">Produto ativo</label>
                                    </p>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="row">
                        <div class="col s12">
                            <ul class="tabs tab-demo " style="width: 100%;">
                                <li class="tab col s6"><a href="#lotes">Lotes do produto</a></li>
                                <li class="tab col s6"><a href="#tabelas">Tabelas de preço</a></li>
                            </ul>
                        </div>
                        <div id="lotes" class="col s12">
                            @include('pages.produtos.lotes')
                        </div>
                        <div id="tabelas" class="col s12">
                            @include('pages.produtos.tabelas')
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    

@endsection



@section('page-scripts')



@endsection

