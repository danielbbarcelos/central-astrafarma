@extends('layouts.template')

@section('page-title', 'Pedido de venda '.(isset($pedido->erp_id) ? '#'.$pedido->erp_id : 'em sincronização'))

@section('page-css')

    <link href="/assets/plugins/materialize-stepper/stepper.5ce1026a32c86fdeda9632be1a4244c0.css" rel="stylesheet">
    <link href="/assets/plugins/bm-datepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">

@endsection

@section('page-breadcrumbs')

    <div class="breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/pedidos-vendas')}}" class="breadcrumbs__element">Lista de pedidos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active">
                <span class="breadcrumbs__element">
                    {{'Pedido de venda '.(isset($pedido->erp_id) ? '#'.$pedido->erp_id : 'em sincronização')}}
                </span>
            </li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row padding-right-20">
        <div class="col l12 m12 s12">
            <div class="card-panel" style="padding: 0 !important;" >
                <div class="card-content">
                    <div class="row padding-top-10">
                        <div class="col s8">
                            <h6 class="font-weight-500 padding-bottom-10">

                                <span style="position: absolute; padding-top: 5px" class="padding-left-30">
                                    @if(isset($pedido->erp_id))
                                        Pedido de venda {{'#'.$pedido->erp_id}}
                                    @elseif($pedido->situacao_pedido == 'S')
                                        Pedido em análise de estoque
                                    @else
                                        Sincronizando
                                    @endif
                                </span>

                                @if($pedido->situacao_pedido == 'A')
                                    <i class="tooltipped material-icons" style="color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Aberto">fiber_manual_record</i>
                                @elseif($pedido->situacao_pedido == 'B')
                                    <i class="tooltipped material-icons" style="color: #1f9dc2; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de crédito">fiber_manual_record</i>
                                @elseif($pedido->situacao_pedido == 'E')
                                    <i class="tooltipped material-icons" style="color: #a3a3a3; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Bloqueio de estoque">fiber_manual_record</i>
                                @elseif($pedido->situacao_pedido == 'F')
                                    <i class="tooltipped material-icons" style="color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Faturado">fiber_manual_record</i>
                                @elseif($pedido->situacao_pedido == 'L')
                                    <i class="tooltipped material-icons" style="color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Liberado para faturamento">fiber_manual_record</i>
                                @elseif($pedido->situacao_pedido == 'S')
                                    <i class="tooltipped material-icons" style="z-index: 9999; color: #fe8c2d; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Pedido em análise de estoque">fiber_manual_record</i>
                                @endif
                            </h6>
                        </div>
                        <div class="col s4 right-align">
                            @if(Permission::check('imprimePDF','PedidoVenda','Central'))
                                <a class="waves-effect btn btn-default btn-submit"href="{{url('/pedidos-vendas/'.$pedido->id.'/pdf')}}" target="_blank">
                                    <label class="cursor-pointer text-dark font-weight-800">
                                        <i class="material-icons" style="font-size: 12px">print</i>
                                        <span class="hidden-xs">IMPRIMIR</span>
                                    </label>
                                </a>
                            @endif
                            @if(Permission::check('excluiPost','PedidoVenda','Central') and $pedido->situacao_pedido == 'A')
                                <a class="waves-effect btn btn-default red btn-submit" onclick="excluiItem('{!! url('/pedidos-vendas/'.$pedido->id.'/del') !!}')">
                                    <label class="cursor-pointer font-weight-800" style="color: white">
                                        <i class="material-icons" style="font-size: 12px">print</i>
                                        <span class="hidden-xs">EXCLUIR</span>
                                    </label>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row padding-right-20">
        <div class="col l12 m12 s12">
            <div class="" style="  transition: box-shadow .25s;
                    box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.15);
                    position: relative;
                    padding: 0 5px 10px 5px;
                    margin: 0.5rem 0 1rem 0;
                    border-radius: 2px;
                    background-color: #fff;">
                <div class="card-content">
                    <form id="form-pedido" method="post" action="{{url('/pedidos-vendas/'.$pedido->id.'/edit')}}">

                        {{csrf_field()}}

                        <input type="hidden" id="pedido_id" value="{{$pedido->id}}">

                        <ul class="stepper parallel horizontal">

                            <!-- Cliente-->
                            <li class="step active">
                                <div class="step-title waves-effect waves-dark" style="cursor: default" onclick="event.stopPropagation();">
                                    <span class="hidden-xs">Cliente</span>
                                </div>

                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row padding-top-30">
                                        <div class="row row-input">
                                            <div class="input-field col s12 m12">
                                                <div class="input-field col s6 m8 padding-bottom-20">
                                                    <select name="vxglocli_id" id="vxglocli_id" class="select2">
                                                        <option value="">Selecione...</option>
                                                        @foreach($clientes as $item)
                                                            <option value="{{$item->id}}"
                                                                data-erp-id="{{$item->erp_id}}"
                                                                data-razao-social="{{$item->razao_social}}"
                                                                data-nome-fantasia="{{$item->nome_fantasia}}"
                                                                data-cnpj-cpf="{{ Helper::insereMascara($item->cnpj_cpf, $item->tipo_pessoa == 'J' ? '##.###.###/####-##' : '###.###.###-##') }}"
                                                                data-cidade-uf="{{$item->cidade.'/'.$item->uf}}"
                                                                data-uf="{{$item->uf}}"
                                                                data-limite-credito="{{$item->limite_credito}}"
                                                                data-saldo-devedor="{{$item->saldo_devedor}}"
                                                                data-credito-disponivel="{{$item->limite_credito - $item->saldo_devedor}}"
                                                                data-risco="{{$item->risco}}"
                                                                @if($pedido->cliente->id == $item->id) selected @endif
                                                            >{{$item->erp_id.' - '.($item->razao_social !== '' ? $item->razao_social : 'Razão social não identificada')}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label class="active">Cliente</label>
                                                </div>

                                                <div class="input-field col s6 m4 padding-bottom-20">
                                                    <select name="vxglocpgto_id" id="vxglocpgto_id" class="select2">
                                                        <option value="">Selecione...</option>
                                                        @foreach($condicoes as $item)
                                                            <option value="{{$item->id}}" data-desconto="{{$item->libera_desconto}}" @if($item->erp_id == $pedido->vxglocpgto_erp_id) selected @endif>{{$item->descricao}}</option>
                                                        @endforeach
                                                    </select>
                                                    <label class="active">Condição de pagamento</label>
                                                </div>



                                                <!-- classificação de risco utilizada para tratativa de desconto -->
                                                <div hidden>
                                                    <select id="vxfatrisco" class="select2">
                                                        <option value=""></option>
                                                        @foreach($riscos as $item)
                                                            <option value="{{$item->codigo}}">{{number_format($item->percentual_desconto,2,',','.')}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div id="data-cliente" class="padding-top-20" hidden>
                                                    <div class="row">
                                                        <div class="col s12 m6 l6 hidden-xs">
                                                            <div class="row padding-bottom-20">
                                                                <div class="col s12 font-weight-800 font-size-16">Dados principais do cliente</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l4 font-weight-800">Cód. ERP:</div>
                                                                <div class="col s12 m12 l8" id="cliente-erp-id"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l4 font-weight-800">Razão social:</div>
                                                                <div class="col s12 m12 l8" id="cliente-razao-social"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l4 font-weight-800">Nome fantasia:</div>
                                                                <div class="col s12 m12 l8" id="cliente-nome-fantasia"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l4 font-weight-800">CNPJ/CPF:</div>
                                                                <div class="col s12 m12 l8" id="cliente-cnpj-cpf"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l4 font-weight-600">Cidade:</div>
                                                                <div class="col s12 m12 l8" id="cliente-cidade-uf"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col s12 m6 l6">

                                                            <div class="row padding-bottom-20">
                                                                <div class="col s12 font-weight-800 font-size-16">Análise financeira</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l5 font-weight-800">Limite de crédito:</div>
                                                                <div class="col s12 m12 l7 font-weight-600" id="cliente-limite-credito"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l5 font-weight-800">Saldo devedor:</div>
                                                                <div class="col s12 m12 l7 font-weight-600" id="cliente-saldo-devedor" style="color: rgba(182,11,35,0.8)">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col s12 m12 l5 font-weight-800">Crédito disponível:</div>
                                                                <div class="col s12 m12 l7 font-weight-600" id="cliente-credito-disponivel"></div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col s12 m12 l5 font-weight-800">Desconto máximo:</div>
                                                                <div class="col s12 m12 l7 font-weight-600" id="cliente-desconto-maximo"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-actions" >
                                        <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepOne">Próximo</button>
                                    </div>
                                </div>
                            </li>



                            <!-- Produtos -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark" style="cursor: default" onclick="event.stopPropagation();">
                                    <span class="hidden-xs">Produtos</span>
                                </div>
                                <div class="step-content" style="overflow-y: hidden">

                                    <div class="row ">


                                        <div class="row row-input">
                                            <div id="ipvenda" class="col s12">
                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table class="display" style="padding-right: 20px; display: inline-block; overflow-y: auto; width: 100%;margin: 0 auto; max-height:300px;" cellspacing="0">
                                                            <thead style="display: inline-table; width: 100%">
                                                            <tr>
                                                                <th style="width: 35%">Produto</th>
                                                                <th style="width: 10%; text-align: center !important;">Quantidade</th>
                                                                <th style="width: 15%; text-align: center !important;">Lote</th>
                                                                <th style="width: 15%; text-align: center !important;">Validade</th>
                                                                <th style="width: 15%; text-align: center !important;">Valor total</th>
                                                                @if($pedido->situacao_pedido == 'A' or $pedido->situacao_pedido == 'S')
                                                                    <th style="width: 15%; text-align: center !important;">
                                                                        <a id="btn-produto" class="waves-effect waves-light btn blue btn-submit modal-trigger" href="#modal-produto">+ ITEM</a>
                                                                    </th>
                                                                @endif
                                                            </tr>
                                                            </thead>
                                                            <tbody id="ipvenda-tbody" style="display: inline-table; width: 100%">
                                                            <div hidden>{{$i = 0}}</div>
                                                            @foreach($itens as $item)
                                                                <div hidden>{{++$i}}</div>
                                                                <tr>
                                                                    <td style='width: 35%'>
                                                                        <input type='hidden' name='vxfatipvend_id[]' value='{{$item->id}}'>
                                                                        <input type='hidden' id="produto-id-{{$i}}" name='produto_id[]' value='{{isset($item->produto) ? $item->produto->id : json_decode($item->produto_data)->id}}'>
                                                                        <input type='hidden' id="produto-tabela-id-{{$i}}" name='produto_tabela_id[]' value='{{$item->tabela->id}}'>
                                                                        <input type='hidden' id="produto-quantidade-{{$i}}" name='produto_quantidade[]' value='{{$item->quantidade}}'>
                                                                        <input type='hidden' name='produto_lote_id[]' value='{{$item->lote->id}}'>
                                                                        <input type='hidden' id="produto-lote-erp-id-{{$i}}" name='produto_lote_erp_id[]' value='{{$item->lote->erp_id}}'>
                                                                        <input type='hidden' name='produto_preco_unitario[]' value='{{number_format($item->preco_unitario,2,',','.')}}'>
                                                                        <input type='hidden' name='produto_preco_venda[]' value='{{number_format($item->preco_venda,2,',','.')}}'>
                                                                        @if((float)$item->valor_acrescimo == 0.00)
                                                                            <input type='hidden' name='produto_valor_desconto[]' value='{{number_format($item->valor_desconto,2,',','.')}}'>
                                                                        @else
                                                                            <input type='hidden' name='produto_valor_desconto[]' value='{{number_format($item->valor_acrescimo * -1,2,',','.')}}'>
                                                                        @endif
                                                                        <input type='hidden' name='produto_preco_total[]' value='{{number_format($item->valor_total,2,',','.')}}'>
                                                                        <a href="/produtos/{{isset($item->produto) ? $item->produto->id : json_decode($item->produto_data)->id}}/show" target="_blank" class='tooltipped cursor-pointer' data-position='top' data-delay='10' data-tooltip="Código: {{isset($item->produto) ? $item->produto->erp_id : json_decode($item->produto_data)->erp_id}}">
                                                                            {{isset($item->produto) ? $item->produto->descricao : json_decode($item->produto_data)->descricao}}
                                                                        </a>
                                                                    </td>
                                                                    <td style='width: 10%; text-align: center !important;'>
                                                                        {{$item->quantidade}}
                                                                        @if($item->alerta_estoque !== null and $item->alerta_estoque !== '')
                                                                            <a class="white tooltipped cursor-pointer" data-position="top" data-delay="10" data-tooltip="{{$item->alerta_estoque}}">
                                                                                <i class="material-icons" style="color: #ff1c1c">error</i>
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                    <td style='width: 15%; text-align: center !important;'>{{$item->lote->erp_id}}</td>
                                                                    <td style='width: 15%; text-align: center !important;'>{{Carbon::createFromFormat('Y-m-d',$item->lote->dt_valid)->format('d/m/Y')}}</td>
                                                                    <td style='width: 15%; text-align: center !important;'>
                                                                        @if((float)$item->valor_acrescimo == 0.00)
                                                                            <a class='tooltipped cursor-pointer' data-position='top' data-delay='10' data-html='true' data-tooltip='Preço de venda: R$ {{number_format($item->preco_venda,2,',','.')}}<br>Desconto: {{number_format(100 - ($item->preco_venda * 100 / $item->preco_unitario),2,',','.')}} %' >
                                                                                R$ {{number_format($item->valor_total,2,',','.')}}
                                                                            </a>
                                                                        @else
                                                                            <a class='tooltipped cursor-pointer' data-position='top' data-delay='10' data-html='true' data-tooltip='Preço de venda: R$ {{number_format($item->preco_venda,2,',','.')}}<br>Acréscimo: {{number_format(($item->preco_venda * 100 / $item->preco_unitario) - 100,2,',','.')}} %' >
                                                                                R$ {{number_format($item->valor_total,2,',','.')}}
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                    @if($pedido->situacao_pedido == 'A' or $pedido->situacao_pedido == 'S')
                                                                        <td style="width: 12%; text-align: center !important;"><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>


                                                        <br>
                                                        <hr style="border: 0.5px solid #e3e3e3; ">
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row hidden-xs" style="margin-top: 20px; margin-bottom: 10px">
                                                <div class="col s12 font-size-14 font-weight-800 hidden-xs" style="margin-top: 20px;">
                                                    <div class="row">
                                                        <div class="col s12 m6 l3">
                                                            Valor total do pedido:
                                                        </div>
                                                        <div class="col s12 m6 l9">
                                                            <span class="pedido-valor-total font-size-14 font-weight-800">R$ 0,00</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col s12 font-size-14 font-weight-800">
                                                    <div class="row">
                                                        <div class="col s12 m6 l3">
                                                            Desconto aplicado:
                                                        </div>
                                                        <div class="col s12 m6 l9">
                                                            <span class="pedido-percentual-desconto font-size-14 font-weight-800">0,00%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col s12 font-size-14 font-weight-800">
                                                    <div class="row">
                                                        <div class="col s12 m6 l3">
                                                            Crédito disponível:
                                                        </div>
                                                        <div class="col s12 m6 l9">
                                                            <span id="credito-restante" class="font-size-14 font-weight-800">R$ 0,00</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="step-actions" >
                                            <button class="waves-effect btn btn-info btn-submit next-step font-weight-800" @if($pedido->situacao_pedido == 'A' or $pedido->situacao_pedido == 'S') data-validator="validateStepTwo" @endif>Próximo</button>
                                            <button class="waves-effect btn btn-default btn-submit previous-step font-weight-800">Voltar</button>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Conclusão -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark" style="cursor: default" onclick="event.stopPropagation();">
                                    <span class="hidden-xs">Conclusão</span>
                                </div>
                                <div class="step-content">
                                    <div class="row">

                                        <div class="row row-input">
                                            <div class="col s12">
                                                <div class="hidden-xs col s6 m3 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Qtde de produtos</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-quantidade-produto">1</span>
                                                </div>
                                                <div class="hidden-xs col s6 m3 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Valor unitário</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-valor-unitario">R$ 0,00</span>
                                                </div>
                                                <div class="col s6 m3 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Desconto</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-percentual-desconto">0,00 %</span>
                                                </div>
                                                <div class="col s6 m3 l3 card-simple-widget">
                                                    <span class="font-weight-400 font-size-12">Valor total</span><br>
                                                    <span class="font-weight-600 font-size-16 pedido-valor-total">R$ 0,00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row padding-top-30 padding-bottom-20">
                                        <div class="row row-input">


                                            <div class="input-field col s6 m6">
                                                <input type="text"  value="{{isset($pedido->data_entrega) ? Carbon::createFromFormat('Y-m-d',$pedido->data_entrega)->format('d/m/Y') : ''}}" class="datepicker" placeholder="" id="data_entrega" name="data_entrega">
                                                <label>Data prevista da entrega</label>
                                            </div>

                                            <div class="input-field col s12 m6">
                                                <select name="status_entrega" id="status_entrega" class="select2">
                                                    <option value="">Selecione...</option>
                                                    <option @if($pedido->status_entrega == "1") selected @endif value="1">1 - Sem programação</option>
                                                    <option @if($pedido->status_entrega == "2") selected @endif value="2">2 - Programado</option>
                                                    <option @if($pedido->status_entrega == "3") selected @endif value="3">3 - PGTO</option>
                                                </select>
                                                <label class="active">Status da entrega</label>
                                            </div>

                                            <div class="input-field col s12 m6">
                                                <textarea class="materialize-textarea" name="observacao" style="height: 6rem" required id="observacao"
                                                          maxlength="10000" length="10000">{{$pedido->observacao or old('observacao')}}</textarea>
                                                <label>Observação na nota fiscal</label>
                                            </div>

                                            <div class="hidden-xs input-field col s12 m6">
                                                <textarea class="materialize-textarea" name="obs_interna" style="height: 6rem" required id="obs_interna"
                                                          maxlength="10000">{{$pedido->obs_interna or old('obs_interna')}}</textarea>
                                                <label>Observação interna</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-actions">
                                        @if($pedido->situacao_pedido == 'A' or $pedido->situacao_pedido == 'S')
                                            <button id="btn-submit-step" class="waves-effect btn btn-info btn-submit next-step font-weight-800" data-validator="validateStepThree">Concluir</button>
                                        @endif
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



    <!-- form é submetido na confirmação de "onclick" presente na tag "a" de cada item. A action é gerada durante a confirmação da exclusão -->
    <form id="form-delete" method="post" action="">
        {{csrf_field()}}
    </form>

@endsection

@section('page-scripts')

    <script src="/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/assets/plugins/materialize-stepper/stepper.js"></script>
    <script src="/assets/plugins/bm-datepicker/js/bootstrap-material-datetimepicker.js"></script>
    <script src="/assets/js/pages/pedido-venda.72e111889c171b1db3a86a4ab30767826.js"></script>

    @if($pedido->situacao_pedido !== 'A' and $pedido->situacao_pedido !== 'S')
        <script>
            $("input,textarea,select").attr('disabled',true);
        </script>
    @endif

    <script>
        $(document).ready(function(){

            $("#data-cliente").attr("hidden",false);
            $("#cliente-erp-id").html($("#vxglocli_id option:selected").attr("data-erp-id"));
            $("#cliente-razao-social").html($("#vxglocli_id option:selected").attr("data-razao-social"));
            $("#cliente-nome-fantasia").html($("#vxglocli_id option:selected").attr("data-nome-fantasia"));
            $("#cliente-cnpj-cpf").html($("#vxglocli_id option:selected").attr("data-cnpj-cpf"));
            $("#cliente-cidade-uf").html($("#vxglocli_id option:selected").attr("data-cidade-uf"));
            $("#cliente-limite-credito").html('+ R$ '+number_format($("#vxglocli_id option:selected").attr("data-limite-credito"),2,',','.'));
            $("#cliente-saldo-devedor").html('- R$ '+number_format($("#vxglocli_id option:selected").attr("data-saldo-devedor"),2,',','.'));
            $("#cliente-desconto-maximo").html( $("#vxfatrisco option:selected").text()+'% (risco '+$("#vxglocli_id option:selected").attr("data-risco")+')' );

            var credito = $("#vxglocli_id option:selected").attr("data-credito-disponivel");
            var html    = "";


            if(parseFloat(credito) == 0.00)
            {
                html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>= R$ "+number_format(Math.abs(parseFloat(credito)),2,',','.')+"</span>";
            }
            else if(parseFloat(credito) <= 0.00)
            {
                html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>- R$ "+number_format(Math.abs(parseFloat(credito)),2,',','.')+"</span>";
            }
            else
            {
                html = "<span style='font-weight: 800; color: rgba(19,157,0,0.91)'>+ R$ "+number_format(Math.abs(parseFloat(credito)),2,',','.')+"</span>";
            }

            $("#cliente-credito-disponivel").html(html);

            //armazena valor para listar produtos da tabela de preço (produtos do mesmo estado do cliente)
            $("#uf-tabela-preco").val($("#vxglocli_id option:selected").attr("data-uf"));


            //calcula novamente o total do pedido para gerar os alertas e informações adicionais na blade
            calculaTotalPedido();


            //exibe a informação de desconto máximo por cliente
            exibeDescontoMaximo('cliente');


            //caso o crédito do cliente seja R$ 0,00, permitimos apenas a seleção da condição de pagamento com libera-desconto = 1
            if(parseFloat(credito) <= 0.00)
            {
                $("#vxglocpgto_id option[data-desconto='0']").attr("disabled",true);
            }
            else
            {
                $("#vxglocpgto_id option[data-desconto='0']").attr("disabled",false);
            }

        })
    </script>


@endsection

