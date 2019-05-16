@extends('layouts.template')

@section('page-title', 'Novo pedido de venda')

@section('page-css')

    <link href="/assets/plugins/materialize-stepper/stepper.css" rel="stylesheet">

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
                    <form>
                        <ul class="stepper parallel horizontal">

                            <!-- Cliente-->
                            <li class="step active">
                                <div class="step-title waves-effect waves-dark">Cliente</div>
                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row padding-top-30">
                                        <div class="row row-input">
                                            <div class="input-field col s12 m12">
                                                <select name="vxglocli_id" id="vxglocli_id">
                                                    <option value="">Selecione...</option>
                                                    @foreach($clientes as $item)
                                                        <option value="{{$item->id}}"
                                                            data-razao-social="{{$item->razao_social}}"
                                                            data-nome-fantasia="{{$item->nome_fantasia}}"
                                                            data-cnpj-cpf="{{ Helper::insereMascara($item->cnpj_cpf, $item->tipo_pessoa == 'J' ? '##.###.###/####-##' : '###.###.###-##') }}"
                                                            data-cidade-uf="{{$item->cidade.'/'.$item->uf}}"
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
                                        <button class="waves-effect btn btn-blue btn-submit next-step" data-validator="validateStepOne">Próximo</button>
                                    </div>
                                </div>
                            </li>



                            <!-- Tabeça de preços -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark">Tabela de preços</div>
                                <div class="step-content" style="overflow-y: hidden">
                                    <div class="row padding-top-30">
                                        <div class="row row-input">
                                            <div class="input-field col s12 m12">
                                                <select name="vxfattabprc_id" id="vxfattabprc_id">
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
                                        <button class="waves-effect waves-dark btn next-step" data-validator="validateStepTwo">Próximo</button>
                                        <button class="waves-effect waves-dark btn-flat previous-step">Voltar</button>
                                    </div>
                                </div>
                            </li>




                            <!-- Produtos -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark">Produtos</div>
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
                                                            <th style="width: 15%">Valor unitário</th>
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
                                            <button class="waves-effect waves-dark btn next-step" data-validator="validateStepThree">Próximo</button>
                                            <button class="waves-effect waves-dark btn-flat previous-step">Voltar</button>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Conclusão -->
                            <li class="step">
                                <div class="step-title waves-effect waves-dark">Conclusão</div>
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
                                    <div class="step-actions">
                                        <input type="submit" class="waves-effect waves-dark btn next-step" value="SUBMIT"/>
                                        <button class="waves-effect waves-dark btn-flat previous-step">BACK</button>
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

    <script>
        $(document).ready(function() {
            $('.stepper').activateStepper();
        });

        function validateStepOne() {

            if($("#vxglocli_id").val() === '')
            {
                Materialize.toast('Selecione o cliente para continuar', 5000, 'red');

                return false;
            }

            return true;
        }

        function validateStepTwo() {

            if($("#vxfattabprc_id").val() === '')
            {
                Materialize.toast('Selecione a tabela de preços para continuar', 5000, 'red');

                return false;
            }

            return true;
        }

        function validateStepThree() {

            return true;

            var validation = true;
            if($('.step:nth-child(3) input[type="text"]').val().indexOf('materialize') === -1)
                validation = false;
            if($('.step:nth-child(3) input[type="checkbox"]:checked').length === 0)
                validation = false;
            return validation;

        }

        function nextStepThreeHandler() {
            if(validateStepThree())
                $('.stepper').nextStep();
            else {
                $('.stepper ').destroyFeedback();
                $('.stepper').getStep($('.stepper').getActiveStep()).addClass('wrong');
            }
        }


        $("#vxglocli_id").on("change",function(){

            if(this.value === '')
            {
                $("#data-cliente").attr("hidden",true);
            }
            else
            {
                $("#data-cliente").attr("hidden",false);
                $("#cliente-razao-social").html($("#vxglocli_id option:selected").attr("data-razao-social"));
                $("#cliente-nome-fantasia").html($("#vxglocli_id option:selected").attr("data-nome-fantasia"));
                $("#cliente-cnpj-cpf").html($("#vxglocli_id option:selected").attr("data-cnpj-cpf"));
                $("#cliente-cidade-uf").html($("#vxglocli_id option:selected").attr("data-cidade-uf"));
            }
        });



        $("#vxfattabprc_id").on("change",function(){

            var itens = 0;

            $("#ipvenda-tbody input[name='produto_id[]']").each(function(){
                itens++;
            });

            if(parseInt(itens) === 0)
            {
                alteraTabelaPreco(this)
            }
            else
            {
                var tabela = this;

                swal({
                    title: "",
                    text: "<br><strong style='font-weight: 500; font-family: Rubik, sans-serif; padding-bottom: 60px'>Você adicionou itens ao pedido. <br><br>Ao confirmar a alteração da tabela, todos os itens serão perdidos. Deseja continuar?</strong><br><br>",
                    type: "info",
                    html: true,
                    showCancelButton: true,
                    confirmButtonColor: "rgba(0,172,194,0.78)",
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Voltar",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {
                        alteraTabelaPreco(tabela)
                    } else {
                        //
                    }
                });

            }
        });


        function alteraTabelaPreco(tabela)
        {
            $("#ipvenda-tbody").html('');

            calculaTotalPedido();

            if(tabela.value === '')
            {
                $("#btn-concluir").attr("disabled",true);
            }
            else
            {
                $("#btn-concluir").attr("disabled",false);

                var prefix   = "vxfattabprc_"+tabela.value;


                //exibe apenas os produtos cadastrados na tabela de preço
                var produtos = JSON.parse($("#"+prefix+"_produtos").val());

                $("#produto_id").empty().html('');

                $("#produto_id").append($('<option selected></option>').attr("value","").text("Selecione..."));

                $.each(produtos, function(index){

                    $("#produto_id").append($('<option></option>')
                        .attr("value",produtos[index].id)
                        .attr("preco_venda",produtos[index].preco_venda)
                        .attr("preco_maximo",produtos[index].preco_maximo)
                        .attr("valor_desconto",produtos[index].valor_desconto)
                        .attr("fator",produtos[index].fator)
                        .text(produtos[index].descricao));
                });

                $("#produto_id").material_select('update');

            }
        }



        $("#produto_id").on("change", function(){
            if(this.value === '')
            {
                $("#produto_quantidade").val("");
                $("#produto_preco_unitario").val("");
                $("#produto_valor_desconto").val("");
                $("#produto_preco_total").val("");
            }
            else
            {
                $("#produto_quantidade").val(1);
                $("#produto_preco_venda").val(number_format($('option:selected', this).attr('preco_venda'),2,',','.'));
                $("#produto_preco_unitario").val(number_format($('option:selected', this).attr('preco_venda'),2,',','.'));
                $("#produto_preco_maximo").val(number_format($('option:selected', this).attr('preco_maximo'),2,',','.'));
                $("#produto_desconto_maximo").val(number_format($('option:selected', this).attr('valor_desconto'),2,',','.'));
                $("#produto_valor_desconto").val('0,00');
                $("#produto_fator").val(number_format($('option:selected', this).attr('fator'),2,',','.'));

                calculaPrecoTotalProduto();
            }
        });

        function calculaPrecoTotalProduto()
        {
            var quantidade     = $("#produto_quantidade").val().replace(".","").replace(",",".");
            var preco_unitario = $("#produto_preco_unitario").val().replace(".","").replace(",",".");
            var valor_desconto = $("#produto_valor_desconto").val().replace(".","").replace(",",".");

            var preco_total    = quantidade * preco_unitario - valor_desconto;
            $("#produto_preco_total").val(number_format(preco_total,2,',','.'));
        }

        function adicionaProduto()
        {
            var success = true;

            $("#erro-produto").attr("hidden",true);

            if($("#produto_id").val() === '')
            {
                success = false;
                $("#erro-produto").attr("hidden",false);
                $("#erro-produto span").html("Produto não selecionado");
            }
            else if($("#produto_quantidade").val() < 1)
            {
                success = false;
                $("#erro-produto").attr("hidden",false);
                $("#erro-produto span").html("A quantidade do produto não pode ser menor que 1");
            }
            else if(parseFloat($("#produto_preco_unitario").val().replace(".","").replace(",",".")) > parseFloat($("#produto_preco_maximo").val()))
            {
                success = false;
                $("#erro-produto").attr("hidden",false);
                $("#erro-produto span").html("O preço máximo tabelado por unidade é R$ "+$("#produto_preco_maximo").val(),2,',','.');
            }
            else if(parseFloat($("#produto_valor_desconto").val().replace(".","").replace(",",".")) > parseFloat($("#produto_preco_total").val().replace(".","").replace(",",".")))
            {
                success = false;
                $("#erro-produto").attr("hidden",false);
                $("#erro-produto span").html("O valor de desconto não pode ser maior que o preço total");
            }

            if(success)
            {

                var row = "<tr>";
                row    += "<td style='width: 10%'>";
                row    += "<input type='hidden' name='vxfatipvend_id[]' value=''>";
                row    += "<input type='hidden' name='produto_id[]' value='"+$("#produto_id").val()+"'>";
                row    += "<input type='hidden' name='produto_quantidade[]' value='"+$("#produto_quantidade").val()+"'>";
                row    += "<input type='hidden' name='produto_preco_unitario[]' value='"+$("#produto_preco_unitario").val()+"'>";
                row    += "<input type='hidden' name='produto_valor_desconto[]' value='"+$("#produto_valor_desconto").val()+"'>";
                row    += "<input type='hidden' name='produto_preco_total[]' value='"+$("#produto_preco_total").val()+"'>";
                row    += $("#produto_id").val();
                row    += "</td>";
                row    += "<td style='width: 30%'>"+$("#produto_id option:selected").text()+"</td>";
                row    += "<td style='width: 15%'>"+$("#produto_quantidade").val()+"</td>";
                row    += "<td style='width: 15%'>R$ "+$("#produto_preco_unitario").val()+"</td>";
                row    += "<td style='width: 15%'>R$ "+$("#produto_valor_desconto").val()+"</td>";
                row    += "<td style='width: 15%'>R$ "+$("#produto_preco_total").val()+"</td>";
                row    += "<td style='width: 12%'><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>";
                row    += "<tr>";

                $("#ipvenda-tbody").append(row);

                calculaTotalPedido();

                //reseta valores da modal
                $("#produto_id").val("").trigger("change");
                $("#produto_quantidade").val("");
                $("#produto_preco_unitario").val("");
                $("#produto_valor_desconto").val("");
                $("#produto_preco_total").val("");

                $('#modal-produto').closeModal();
            }
        }

        function excluiProduto(row)
        {
            $(row).closest("tr").remove();

            calculaTotalPedido();
        }


        function calculaTotalPedido()
        {
            //quantidade
            var quantidade = 0;
            $("#ipvenda-tbody input[name='produto_quantidade[]']").each(function(){
                quantidade = parseInt(quantidade) + parseInt($(this).val());
            });
            $(".pedido-quantidade-produto").html(quantidade);

            //valor unitario
            var valorUnitario = 0.00;
            $("#ipvenda-tbody input[name='produto_preco_unitario[]']").each(function(){
                valorUnitario = parseFloat(valorUnitario) + parseFloat($(this).val().replace('.','').replace(',','.'));
            });
            $(".pedido-valor-unitario").html('R$ ' + number_format(valorUnitario,2,',','.'));

            //valor desconto
            var valorDesconto = 0.00;
            $("#ipvenda-tbody input[name='produto_valor_desconto[]']").each(function(){
                valorDesconto = parseFloat(valorDesconto) + parseFloat($(this).val().replace('.','').replace(',','.'));
            });
            $(".pedido-valor-desconto").html('R$ ' + number_format(valorDesconto,2,',','.'));

            //valor total
            var valorTotal = 0.00;
            $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
                valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
            });
            $(".pedido-valor-total").html('R$ ' + number_format(valorTotal,2,',','.'));
        }


        $("#btn-concluir").on("click", function(){

            if($("#vxglocli_id").val() === '')
            {
                Materialize.toast('Selecione o cliente para concluir o pedido', 5000, 'red');
            }
            else
            {
                var itens = 0;

                $("#ipvenda-tbody input[name='produto_id[]']").each(function(){
                    itens++;
                });

                if(parseInt(itens) === 0)
                {
                    Materialize.toast('Nenhum produto foi adicionado ao pedido', 5000, 'red');

                    return false;
                }

                $('#modal-conclui-pedido').openModal();
            }
        });


        $("#btn-submit").on("click",function(){
            $("#form-pedido").submit();
        })



    </script>


@endsection

