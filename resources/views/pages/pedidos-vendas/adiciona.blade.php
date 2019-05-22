@extends('layouts.template')

@section('page-title', 'Novo pedido de venda')

@section('page-css')

    <link href="/assets/plugins/materialize-stepper/stepper.css" rel="stylesheet">
    <link href="/assets/plugins/bm-datepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">

@endsection

@section('page-breadcrumbs')

    <div class="breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/pedidos-vendas')}}" class="breadcrumbs__element">Lista de pedidos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Novo pedido de venda</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row">
        <div class="col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <form id="form-pedido" method="post">

                        {{csrf_field()}}

                        <ul class="stepper parallel horizontal">

                            <!-- Cliente-->
                            <li class="step active">
                                <div class="step-title waves-effect waves-dark" onclick="event.stopPropagation();">Cliente</div>
                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row padding-top-30">
                                        <div class="row row-input">
                                            <div class="input-field col s12 m12">
                                                <select name="vxglocli_id" id="vxglocli_id" class="select2">
                                                    <option value="">Selecione...</option>
                                                    @foreach($clientes as $item)
                                                        <option value="{{$item->id}}"
                                                            data-razao-social="{{$item->razao_social}}"
                                                            data-nome-fantasia="{{$item->nome_fantasia}}"
                                                            data-cnpj-cpf="{{ Helper::insereMascara($item->cnpj_cpf, $item->tipo_pessoa == 'J' ? '##.###.###/####-##' : '###.###.###-##') }}"
                                                            data-cidade-uf="{{$item->cidade.'/'.$item->uf}}"
                                                            data-uf="{{$item->uf}}"
                                                        >{{$item->razao_social}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label>Cliente</label>

                                                <div id="data-cliente" class="padding-top-20" hidden>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-800">Razão social:</div>
                                                        <div class="col s10" id="cliente-razao-social"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-800">Nome fantasia:</div>
                                                        <div class="col s10" id="cliente-nome-fantasia"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-800">CNPJ/CPF:</div>
                                                        <div class="col s10" id="cliente-cnpj-cpf"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-600">Cidade:</div>
                                                        <div class="col s10" id="cliente-cidade-uf"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-800">Limite de crédito:</div>
                                                        <div class="col s10" id="cliente-email">R$ 25.000,00</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col s2 font-weight-800">Saldo devedor:</div>
                                                        <div class="col s10" id="cliente-email">R$ 5.350,00</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-actions">
                                        <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepOne">Próximo</button>
                                    </div>
                                </div>
                            </li>



                            <!-- Tabeça de preços -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark" onclick="event.stopPropagation();">Tabela de preços</div>
                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row padding-top-30">
                                        <div class="row row-input">

                                            <input type="hidden" id="uf-tabela-preco" value="">

                                            <div class="input-field col s12 m12">
                                                <select name="vxfattabprc_id" id="vxfattabprc_id" class="select2">
                                                    <option value="">Selecione...</option>
                                                    @foreach($tabelas as $item)
                                                        <option value="{{$item->id}}">{{$item->descricao}}</option>
                                                    @endforeach
                                                </select>
                                                <label>Tabela de preços</label>
                                            </div>
                                        </div>


                                        <!-- div para resgatar os dados da tabela selecionada -->
                                        <div hidden>
                                            @foreach($tabelas as $item)
                                                <input id="vxfattabprc_{{$item->id}}_descricao" value="{{$item->descricao}}">
                                                <input id="vxfattabprc_{{$item->id}}_produtos" value="{{json_encode($item->produtos,JSON_UNESCAPED_UNICODE)}}">
                                            @endforeach
                                        </div>


                                    </div>
                                    <div class="step-actions">
                                        <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepTwo">Próximo</button>
                                        <button class="waves-effect btn btn-default btn-submit previous-step font-weight-800">Voltar</button>
                                    </div>
                                </div>
                            </li>




                            <!-- Produtos -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark" onclick="event.stopPropagation();">Produtos</div>
                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row ">

                                        <div class="row row-input">
                                            <div id="ipvenda" class="col s12">
                                                <div class="row">
                                                    <table class="display" style="padding-right: 20px; display: inline-block; overflow-y: auto; width: 100%;margin: 0 auto; max-height:270px;" cellspacing="0">
                                                        <thead style="display: inline-table; width: 100%">
                                                        <tr>
                                                            <th style="width: 10%">Cód. produto</th>
                                                            <th style="width: 30%">Descrição</th>
                                                            <th style="width: 15%">Quantidade</th>
                                                            <th style="width: 15%">Preço de venda</th>
                                                            <th style="width: 15%">Desconto</th>
                                                            <th style="width: 15%">Valor total</th>
                                                            <th style="width: 15%">
                                                                <a id="btn-produto" class="waves-effect waves-light btn blue btn-submit modal-trigger" href="#modal-produto">+ ITEM</a>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="ipvenda-tbody" style="display: inline-table; width: 100%">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="step-actions" style="position: absolute; bottom: 0; ">
                                            <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepThree">Próximo</button>
                                            <button class="waves-effect btn btn-default btn-submit previous-step font-weight-800">Voltar</button>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Conclusão -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark" onclick="event.stopPropagation();">Conclusão</div>
                                <div class="step-content">
                                    <div class="row">

                                        <div class="row row-input">
                                            <div class="col s12">
                                                <div class="col s12 m12 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Qtde de produtos</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-quantidade-produto">1</span>
                                                </div>
                                                <div class="col s12 m12 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Valor unitário</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-valor-unitario">R$ 0,00</span>
                                                </div>
                                                <div class="col s12 m12 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Desconto</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-valor-desconto">R$ 0,00</span>
                                                </div>
                                                <div class="col s12 m12 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Valor total</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-valor-total">R$ 0,00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row padding-top-30">
                                        <div class="row row-input">

                                            <div class="input-field col s6">
                                                <select name="vxglocpgto_id" id="vxglocpgto_id" class="select2">
                                                    <option disabled selected>Selecione...</option>
                                                    @foreach($condicoes as $item)
                                                        <option value="{{$item->id}}" @if($item->erp_id == $pedido->vxglocpgto_erp_id) selected @endif>{{$item->descricao}}</option>
                                                    @endforeach
                                                </select>
                                                <label>Condição de pagamento</label>
                                            </div>

                                            <div class="input-field col s6">
                                                <input type="text"  value="{{isset($pedido->data_entrega) ? Carbon::createFromFormat('Y-m-d',$pedido->data_entrega)->format('d/m/Y') : ''}}" class="datepicker" placeholder="" id="data_entrega" name="data_entrega">
                                                <label>Data prevista da entrega</label>
                                            </div>


                                            <div class="input-field col s12">
                                                <textarea class="materialize-textarea" name="observacao" style="height: 6rem"
                                                          maxlength="10000" length="10000">{{$pedido->observacao or old('observacao')}}</textarea>
                                                <label>Observação</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-actions">
                                        <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepFour">Concluir</button>
                                        <button class="waves-effect btn btn-default btn-submit previous-step font-weight-800">Voltar</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @include('pages.pedidos-vendas.modal-produto')

@endsection

@section('page-scripts')

    <script src="/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/assets/plugins/materialize-stepper/stepper.js"></script>
    <script src="/assets/plugins/bm-datepicker/js/bootstrap-material-datetimepicker.js"></script>
    <script src="/assets/js/pages/pedido-venda.dbe70c23a45c222e40ce3c469080ffee.js"></script>


@endsection

