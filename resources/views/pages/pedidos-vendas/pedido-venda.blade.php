@extends('layouts.template')

@section('page-title', 'Pedido de venda')

@section('page-css')

@endsection

@section('page-content')

    <form class="s12" method="post" id="form-pedido" action="{{url('/pedidos-vendas/'.$pedido->id.'/edit')}}">

        {{csrf_field()}}

        <div class="middle padding-top-20 padding-right-20">
            <div class="row">
                <div class="col s12">
                    <div class="page-title"></div>
                </div>
                <div class="col l12 m12 s12">
                    <div class="card-panel">
                        <div class="card-content">
                            <span class="card-title">
                                <a href="{{url('/pedidos-vendas')}}" class="card-breadcrumb-link">Lista de pedidos</a>
                                <i class="material-icons card-breadcrumb-separator">chevron_right</i>
                                Pedido de venda
                            </span><br>
                            <div class="row">
                                <div class="row row-input">
                                    <div class="input-field col s12 m12">
                                        <select name="vxglocli_id" id="vxglocli_id" @if($pedido->situacao_pedido !== 'A') disabled @endif>
                                            <option value="">Selecione...</option>
                                            @foreach($clientes as $item)
                                                <option value="{{$item->id}}" @if($item->erp_id == $pedido->vxglocli_erp_id) selected @endif>{{$item->razao_social}}</option>
                                            @endforeach
                                        </select>
                                        <label>Cliente</label>
                                    </div>
                                </div>


                                <div class="row row-input">
                                    <div class="input-field col s12 m12">
                                        <select name="vxfattabprc_id" id="vxfattabprc_id" @if($pedido->situacao_pedido !== 'A') disabled @endif>
                                            <option value="">Selecione...</option>
                                            @foreach($tabelas as $item)
                                                <option value="{{$item->id}}" @if($item->erp_id == $pedido->vxfattabprc_erp_id) selected @endif>{{$item->descricao}}</option>
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


                                <div id="ipvenda" class="col s12">
                                    <div class="col s12 row" style="margin-top: 20px">
                                        <label class="card-title" style="font-weight: 800; font-size: 14px;">
                                            Itens do pedido <h5 class="padding-left-20">Valor total: R$ <span id="valor_total">{{number_format($pedido->valorTotal(),2,',','.')}}</span></h5>
                                        </label>
                                        @if($pedido->situacao_pedido == 'A')
                                            <div class="right-align">
                                                <a id="btn-produto" class="waves-effect waves-light btn blue modal-trigger" href="#modal-produto">+ ITEM</a>
                                            </div>
                                        @endif
                                    </div><br>
                                    <div class="row">
                                        <table class="display" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Cód. produto</th>
                                                <th>Descrição</th>
                                                <th>Quantidade</th>
                                                <th>Valor unitário</th>
                                                <th>Desconto</th>
                                                <th>Valor total</th>
                                                @if($pedido->situacao_pedido == 'A')
                                                    <th>Funções</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody id="ipvenda-tbody">
                                                @foreach($itens as $item)
                                                    <tr>
                                                        <td>
                                                            <input type='hidden' name='vxfatipvend_id[]' value='{{$item->id}}'>
                                                            <input type='hidden' name='produto_id[]' value='{{isset($item->produto) ? $item->produto->id : json_decode($item->produto_data)->id}}'>
                                                            <input type='hidden' name='produto_quantidade[]' value='{{$item->quantidade}}'>
                                                            <input type='hidden' name='produto_preco_unitario[]' value='{{number_format($item->preco_unitario,2,',','.')}}'>
                                                            <input type='hidden' name='produto_valor_desconto[]' value='{{number_format($item->valor_desconto,2,',','.')}}'>
                                                            <input type='hidden' name='produto_preco_total[]' value='{{number_format($item->valor_total,2,',','.')}}'>
                                                            {{isset($item->produto) ? $item->produto->id : json_decode($item->produto_data)->id}}
                                                        </td>
                                                        <td>{{isset($item->produto) ? $item->produto->descricao : json_decode($item->produto_data)->descricao}}</td>
                                                        <td>{{$item->quantidade}}</td>
                                                        <td>{{number_format($item->preco_unitario,2,',','.')}}</td>
                                                        <td>{{number_format($item->valor_desconto,2,',','.')}}</td>
                                                        <td>{{number_format($item->valor_total,2,',','.')}}</td>
                                                        @if($pedido->situacao_pedido == 'A')
                                                            <td><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @if($pedido->situacao_pedido == 'A')
                                    <div class="col s12 right-align" style="margin-top: 30px">
                                        <button type="button" id="btn-concluir" class="waves-effect waves-light btn blue">Concluir pedido</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.pedidos-vendas.modal-produto')
        @include('pages.pedidos-vendas.modal-conclui-pedido')
    </form>

@endsection



@section('page-scripts')

    <script>

        $(document).ready(function(){
            $('.datepicker').pickadate({
                format: 'dd/mm/yyyy',
                monthsFull: [ 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro' ],
                monthsShort: [ 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez' ],
                weekdaysFull: [ 'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado' ],
                weekdaysShort: [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb' ],
                weekdaysLetter: [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb' ],
                today: 'Hoje',
                clear: 'Limpar',
                close: 'Fechar',
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 15 // Creates a dropdown of 15 years to control year
            });

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
                if(confirm("Você adicionou itens ao pedido. Ao confirmar a alteração da tabela, todos os itens serão perdidos. Deseja continuar?")) {
                    alteraTabelaPreco(this)
                }
            }
        });


        function alteraTabelaPreco(tabela)
        {
            $("#ipvenda-tbody").html('');

            calculaValorTotalPedido();

            if(tabela.value === '')
            {
                $("#btn-concluir").attr("disabled",true);
                $("#ipvenda").attr("hidden",true);
            }
            else
            {
                $("#btn-concluir").attr("disabled",false);
                $("#ipvenda").attr("hidden",false);


                var prefix   = "vxfattabprc_"+this.value;

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
                row    += "<td>";
                row    += "<input type='hidden' name='vxfatipvend_id[]' value=''>";
                row    += "<input type='hidden' name='produto_id[]' value='"+$("#produto_id").val()+"'>";
                row    += "<input type='hidden' name='produto_quantidade[]' value='"+$("#produto_quantidade").val()+"'>";
                row    += "<input type='hidden' name='produto_preco_unitario[]' value='"+$("#produto_preco_unitario").val()+"'>";
                row    += "<input type='hidden' name='produto_valor_desconto[]' value='"+$("#produto_valor_desconto").val()+"'>";
                row    += "<input type='hidden' name='produto_preco_total[]' value='"+$("#produto_preco_total").val()+"'>";
                row    += $("#produto_id").val();
                row    += "</td>";
                row    += "<td>"+$("#produto_id option:selected").text()+"</td>";
                row    += "<td>"+$("#produto_quantidade").val()+"</td>";
                row    += "<td>"+$("#produto_preco_unitario").val()+"</td>";
                row    += "<td>"+$("#produto_valor_desconto").val()+"</td>";
                row    += "<td>"+$("#produto_preco_total").val()+"</td>";
                row    += "<td><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>";
                row    += "<tr>";

                $("#ipvenda-tbody").append(row);

                calculaValorTotalPedido();

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

            calculaValorTotalPedido();
        }


        function calculaValorTotalPedido()
        {
            var total = 0.00;

            $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
                total = parseFloat(total) + parseFloat($(this).val().replace('.','').replace(',','.'));
            });

            $("#valor_total").html(number_format(total,2,',','.'));
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

