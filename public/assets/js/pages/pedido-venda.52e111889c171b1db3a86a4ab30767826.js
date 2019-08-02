//---------------------------------------------------------------------------
//
// Inicializa os plugins
//
//---------------------------------------------------------------------------
$(document).ready(function() {
    $('.stepper').activateStepper();

    $('#data_entrega').bootstrapMaterialDatePicker({
        format : 'DD/MM/YYYY',
        minDate : new Date(),
        time: false,
        nowButton: 'Hoje',
        nowText: 'Hoje',
        okText: 'Confirmar',
        cancelText: 'Voltar',
        clearButton: false,
    });

    calculaTotalPedido()

});


//---------------------------------------------------------------------------
//
// Validação do passo 1 (seleção de cliente)
//
//---------------------------------------------------------------------------
function validateStepOne() {


    if($("#vxglocli_id").val() === '')
    {
        Materialize.toast('Selecione o cliente para continuar', 5000, 'red');

        return false;
    }


    var credito = $("#vxglocli_id option:selected").attr("data-credito-disponivel");

    if(parseFloat(credito) <= 0.00)
    {
        Materialize.toast('O cliente não possui crédito disponível', 5000, 'red');

        return false;
    }

    return true;
}


//---------------------------------------------------------------------------
//
// Validação do passo 2 (itens do pedido)
//
//---------------------------------------------------------------------------
function validateStepTwo() {

    var valor = $('.pedido-valor-total').html().replace('R$ ','').replace('.','').replace(',','.');

    if(parseFloat(valor) <= 0.00)
    {
        Materialize.toast('O valor total dos produtos deve ser maior que R$ 0,00', 5000, 'red');

        return false;
    }


    //calcula o valor restante de crédito do cliente, após adicionar o item no pedido
    var valorTotal = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
        valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });

    var credito  = $("#vxglocli_id option:selected").attr("data-credito-disponivel");

    if(parseFloat(valorTotal) > parseFloat(credito))
    {
        Materialize.toast('O crédito disponível para o cliente é inferior ao valor do pedido', 5000, 'red');

        return false;
    }


    //verifica se o desconto aplicado é maior que o desconto permitido
    var percentualDesconto = calculaDescontoPedido();

    if(parseFloat(percentualDesconto) > parseFloat( $("#vxfatrisco option:selected").text().replace('.','').replace(',','.') ))
    {
        Materialize.toast('O desconto máximo permitido para o cliente é de '+$("#vxfatrisco option:selected").text()+' %', 5000, 'red');

        return false;
    }

    return true;
}


//---------------------------------------------------------------------------
//
// Validação do passo 3 (condição de pagamento, data de entrega e conclusão)
//
//---------------------------------------------------------------------------
function validateStepThree() {

    var success = true;

    if($("#vxglocpgto_id").val() === '')
    {
        Materialize.toast('Selecione a condição de pagamento para continuar', 5000, 'red');

        success = false;
    }

    if($("#data_entrega").val() === '')
    {
        Materialize.toast('Selecione a data de entrega para continuar', 5000, 'red');

        success = false;
    }

    if($("#observacao").val() === '')
    {
        Materialize.toast('Insira as observações da nota fiscal para continuar', 5000, 'red');

        success = false;
    }

    if(!success)
    {
        return false;
    }
    else
    {
        //valida se ainda tem lotes disponíveis para os produtos do pedido
        success = validaLotes();

        if(success)
        {
            $("#btn-submit-step").text("Processando...").attr("disabled",true);
            $("#form-pedido").submit();
        }
    }


}



//---------------------------------------------------------------------------
//
// Exibe o resumo do cliente e dados financeiros no primeiro passo
//
//---------------------------------------------------------------------------
$("#vxglocli_id").on("change",function(){

    if(this.value === '')
    {
        $("#data-cliente").attr("hidden",true);
        $("#vxfatrisco").val("").trigger("change");
    }
    else
    {
        $("#data-cliente").attr("hidden",false);
        $("#vxfatrisco").val($("#vxglocli_id option:selected").attr("data-risco")).trigger("change");

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

        if(parseFloat(credito) <= 0.00)
        {
            html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>- R$ "+number_format(Math.abs(parseFloat(credito)),2,',','.')+"</span>";
        }
        else
        {
            html = "<span style='font-weight: 800; color: rgba(19,157,0,0.91)'>= R$ "+number_format(Math.abs(parseFloat(credito)),2,',','.')+"</span>";
        }

        $("#cliente-credito-disponivel").html(html);



        //calcula o valor restante de crédito do cliente, caso a tabela de itens do pedido contenha algum item
        var valorTotal = 0.00;
        $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
            valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
        });

        var restante = parseFloat(credito) - parseFloat(valorTotal);

        if(parseFloat(restante) <= 0.00)
        {
            html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>- R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
        }
        else
        {
            html = "<span style='font-weight: 800; color: rgba(19,157,0,0.91)'>+ R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
        }

        $("#credito-restante").html(html);

    }
});



//---------------------------------------------------------------------------------------------------------
//
// Atualiza/Esconde os valores do produto, verificando qual produto e tabela de preço estão selecionados
//
//---------------------------------------------------------------------------------------------------------
$("#produto_id").on("change", function(){

    if(this.value === '')
    {
        alteraValoresPorItem(true);
    }
    else
    {


        if($("#tabela_preco_id").val() === '')
        {
            alteraValoresPorItem(true);
        }
        else
        {
            alteraValoresPorItem(false);
        }

    }
});

$("#tabela_preco_id").on("change", function(){

    if(this.value === '')
    {
        alteraValoresPorItem(true);
    }
    else
    {
        if($("#produto_id").val() === '')
        {
            alteraValoresPorItem(true);
        }
        else
        {
            alteraValoresPorItem(false);
        }

    }
});


function alteraValoresPorItem(hidden)
{

    if(hidden === true)
    {
        $("#produto_quantidade").val('0');
        $("#produto_preco_venda").val('0,00');
        $("#produto_valor_desconto").val('0,00');
        $("#produto_percentual_desconto").val('0,00');
        $("#produto_preco_total").val('0,00');


        //não exibe select para seleção de lote
        $("#div-lote").attr("hidden",true);
        $("#lote_id").val("").trigger("change");


        $("#div-valores-item").attr('hidden',hidden);

        //não exibe saldo total
        $("#div-saldo-total").attr("hidden",true);
    }
    else
    {

        $("#produto_erp_id").val($('#produto_id option:selected').attr('erp_id'));
        $("#produto_descricao").val($('#produto_id option:selected').attr('descricao'));


        var produto_id = $("#produto_id").val();
        var tabela_id  = $("#tabela_preco_id").val();
        var uf         = $('#vxglocli_id option:selected').attr('data-uf');
        var call       = '/api/v1/tabelas-precos/'+tabela_id+'/'+uf+'/'+produto_id+'/precos';

        var settings = {
            "url": call,
            "method": "GET",
            "headers": {
                "request-ajax": "Token "+ ajaxToken()
            }
        };

        $.ajax(settings).done(function (response) {

            if(response.success === false)
            {
                $("#produto_quantidade").val(1);
                $("#produto_preco_unitario").val(number_format(0.00,2,',','.'));
                $("#produto_preco_venda").val(number_format(0.00,2,',','.'));
                $("#produto_preco_total").val(number_format(0.00,2,',','.'));
                $("#produto_valor_desconto").val('0,00');
                $("#produto_percentual_desconto").val('0,00');
                $("#produto_fator").val(number_format(0.00,2,',','.'));

                //não exibe select para seleção de lote
                $("#div-lote").attr("hidden",true);
                $("#lote_id").val("").trigger("change");

                //exibe mensagem de erro
                $("#erro-produto").attr("hidden",false);

                if (response.log.error)
                {
                    $("#erro-produto span").html(response.log.error);
                }
                else
                {
                    $("#erro-produto span").html(response.log[0]);
                }


                //não exibe saldo total
                $("#div-saldo-total").attr("hidden",true);

            }
            else
            {
                $("#produto_quantidade").val(1);
                $("#produto_preco_unitario").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_preco_venda").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_preco_total").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_valor_desconto").val('0,00');
                $("#produto_percentual_desconto").val('0,00');
                $("#produto_fator").val(number_format(response.preco.fator,2,',','.'));

                //esconde mensagem de erro
                $("#erro-produto").attr("hidden",true);


                //carrega os lotes disponíveis para o produto
                buscaLotes(produto_id, tabela_id);

                $("#div-valores-item").attr('hidden',hidden);
            }
        });

        calculaPrecoTotalProduto();
    }
}



//-----------------------------------------------------------------------------------
//
// Retorna os lotes com saldo, referente ao produto e tabela de preço selecionados
//
//-----------------------------------------------------------------------------------
function buscaLotes(produto_id, tabela_id)
{
    $("#div-saldo-total").attr("hidden",false);

    var call = '/api/v1/lotes/'+produto_id+'/'+tabela_id;

    var settings = {
        "url": call,
        "method": "GET",
        "headers": {
            "request-ajax": "Token "+ ajaxToken()
        }
    };

    $.ajax(settings).done(function (response) {

        if(response.lotes.length === 0)
        {
            $("#erro-produto").attr("hidden",false);
            $("#erro-produto span").html("Não há lotes com saldo em estoque referente a este produto");


            $("#div-lote").attr("hidden",true);
            $("#lote_id").html("").val("").trigger("change");


            //exibe o saldo total em estoque
            $("#saldo-total").val('0');

        }
        else
        {
            $("#erro-produto").attr("hidden",true);
            $("#erro-produto span").html("");

            var validade = '';

            var options = "<option value=''>Selecione...</option>";

            var itens      = 0;
            var saldoTotal = 0;

            $(response.lotes).each(function(){

                validade = this.dt_valid;
                validade = validade.split("-").reverse().join("/");

                //verifica se o lote foi adicionado anteriormente, para poder recalcular seu saldo
                var saldo = this.saldo;
                $("input[name='produto_lote_erp_id[]'][value='"+this.erp_id+"']").each(function () {
                    var hash = this.id.split('-')[4];
                    var qtde = $("#produto-quantidade-"+hash).val();
                    saldo = parseInt(saldo) - parseInt(qtde);
                });

                if(saldo > 0)
                {
                    options += "<option value='"+this.id+"' dt_valid='"+this.dt_valid+"' saldo='"+parseInt(saldo)+"' erp_id='"+this.erp_id+"'>"+this.erp_id+" - Validade: "+validade+" - Saldo em estoque: "+parseInt(saldo)+"</option>";

                    itens++;
                }

                saldoTotal = parseInt(saldoTotal) + parseInt(saldo);

            });

            //exibe o saldo total em estoque
            $("#saldo-total").val(saldoTotal);

            if(itens > 0)
            {
                $("#div-lote").attr("hidden",false);
                $("#lote_id").html(options).val("").trigger("change");
            }
            else
            {
                $("#erro-produto").attr("hidden",false);
                $("#erro-produto span").html("Não há lotes com saldo em estoque referente a este produto");


                $("#div-lote").attr("hidden",true);
                $("#lote_id").html("").val("").trigger("change");
            }

        }
    });
}



//---------------------------------------------------------------------------
//
// Calcula o preço total do produto a ser adicionado
//
//---------------------------------------------------------------------------
function calculaPrecoTotalProduto()
{
    var quantidade     = $("#produto_quantidade").val().replace(".","").replace(",",".");
    var preco_unitario = $("#produto_preco_unitario").val().replace(".","").replace(",",".");
    var preco_venda    = $("#produto_preco_venda").val().replace(".","").replace(",",".");

    var preco_total    = quantidade * preco_venda;

    $("#produto_preco_total").val(number_format(preco_total,2,',','.'));

    //calculamos o valor de desconto aplicado, que será gravado no banco de dados
    var valor_desconto = preco_total - (quantidade * preco_unitario);
    $("#produto_valor_desconto").val(number_format(valor_desconto * -1,2,',','.'));

    //calculamos o percentual de desconto aplicado, apenas para exibição da informação
    var percentual_desconto = 0.00;
    if(parseFloat(preco_venda) < parseFloat(preco_unitario))
    {
        percentual_desconto = 100 - (parseFloat(preco_venda) * 100 / parseFloat(preco_unitario));
    }
    $("#produto_percentual_desconto").val(number_format(percentual_desconto,2,',','.'));
}




//---------------------------------------------------------------------------
//
// Adiciona produto ao pedido de acordo com lote sugerido no webservice
//
//---------------------------------------------------------------------------
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
    else if($("#tabela_preco_id").val() === '')
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("Tabela de preços não selecionada");
    }
    else if($("#div-lote").attr("hidden") == "true" || $("#div-lote").attr("hidden") == "hidden")
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("Não há lotes com saldo disponível referente ao produto selecionado");
    }
    else if($("#lote_id").val() === '')
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("Lote não selecionado");
    }
    else if($("#produto_quantidade").val() < 1)
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("A quantidade do produto não pode ser menor que 1");
    }
    else if($("#produto_quantidade").val() > parseInt($("#lote_id option:selected").attr("saldo")))
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("A quantidade do produto não pode ser maior que o saldo disponível em estoque");
    }
    else if(parseFloat($("#produto_preco_unitario").val().replace(".","").replace(",",".")) > parseFloat($("#produto_preco_maximo").val()))
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("O preço máximo tabelado por unidade é R$ "+$("#produto_preco_maximo").val(),2,',','.');
    }
    else if(parseFloat($("#produto_percentual_desconto").val().replace(".","").replace(",",".")) >= 100.00)
    {
        success = false;
        $("#erro-produto").attr("hidden",false);
        $("#erro-produto span").html("O desconto deve ser inferior a 100%");
    }

    if(success)
    {
        var itemHash = hashGenerator(30,'');

        var validade = $("#lote_id option:selected").attr('dt_valid');
        validade     = validade.split("-").reverse().join("/");

        var row = "<tr>";
        //inputs e descrição
        row    += "<td style='width: 35%'>";
        row    += "<input type='hidden' name='vxfatipvend_id[]' value=''>";
        row    += "<input type='hidden' id='produto-id-"+itemHash+"' name='produto_id[]' class='item-pedido' value='"+$("#produto_id").val()+"'>";
        row    += "<input type='hidden' id='produto-tabela-id-"+itemHash+"' name='produto_tabela_id[]' value='"+$("#tabela_preco_id").val()+"'>";
        row    += "<input type='hidden' id='produto-quantidade-"+itemHash+"' name='produto_quantidade[]' value='"+$("#produto_quantidade").val()+"'>";
        row    += "<input type='hidden' name='produto_lote_id[]' value='"+$("#lote_id option:selected").val()+"'>";
        row    += "<input type='hidden' id='produto-lote-erp-id-"+itemHash+"' name='produto_lote_erp_id[]' value='"+$("#lote_id option:selected").attr('erp_id')+"'>";
        row    += "<input type='hidden' name='produto_preco_unitario[]' value='"+$("#produto_preco_unitario").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_venda[]' value='"+$("#produto_preco_venda").val()+"'>";
        row    += "<input type='hidden' name='produto_valor_desconto[]' value='"+$("#produto_valor_desconto").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_total[]' value='"+$("#produto_preco_total").val()+"'>";
        row    += "<a class='tooltipped' data-position='top' data-delay='10' data-tooltip='Código: "+$("#produto_id option:selected").attr('erp_id')+"'  href='/produtos/"+$("#produto_id").val()+"/show' target='_blank'>"+$("#produto_id option:selected").attr('descricao')+"</a>";
        row    += "</td>";
        //quantidade
        row    += "<td style='width: 10%; text-align: center !important;'>"+$("#produto_quantidade").val()+"</td>";
        //informações do lote
        row    += "<td style='width: 15%; text-align: center !important;'>";
        row    += $("#lote_id option:selected").attr('erp_id');
        row    += "</td>";
        //informações da validade
        row    += "<td style='width: 15%; text-align: center !important;'>";
        row    += validade;
        row    += "</td>";
        //informações de valores
        if(parseFloat($("#produto_preco_venda").val().replace('.',',').replace(',','.')) > parseFloat($("#produto_preco_unitario").val().replace('.',',').replace(',','.')))
        {
            var acrescimo = (parseFloat($("#produto_preco_venda").val().replace('.',',').replace(',','.')) * 100 / parseFloat($("#produto_preco_unitario").val().replace('.',',').replace(',','.'))) - 100;
            acrescimo     = number_format(acrescimo,2,',','.');
            row    += "<td style='width: 15%; text-align: center !important;'>";
            row    += "<a class='tooltipped cursor-pointer' data-position='top' data-delay='10' data-html='true' data-tooltip='Preço de venda: R$ "+$("#produto_preco_venda").val()+"<br>Acréscimo: "+acrescimo+" %' >R$ "+$("#produto_preco_total").val()+"</a>";
            row    += "</td>";
        }
        else
        {
            row    += "<td style='width: 15%; text-align: center !important;'>";
            row    += "<a class='tooltipped cursor-pointer' data-position='top' data-delay='10' data-html='true' data-tooltip='Preço de venda: R$ "+$("#produto_preco_venda").val()+"<br>Desconto: "+$("#produto_percentual_desconto").val()+" %' >R$ "+$("#produto_preco_total").val()+"</a>";
            row    += "</td>";
        }
        //funções
        row    += "<td style='width: 12%; text-align: center !important;'><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>";
        row    += "<tr>";


        $("#ipvenda-tbody").append(row);

        $(".tooltipped").tooltip();

        calculaTotalPedido();




        //calcula o valor restante de crédito do cliente, após adicionar o item no pedido
        var valorTotal = 0.00;
        $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
            valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
        });

        var credito  = $("#vxglocli_id option:selected").attr("data-credito-disponivel");
        var restante = parseFloat(credito) - parseFloat(valorTotal);
        var html     = "";

        if(parseFloat(restante) <= 0.00)
        {
            html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>- R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
        }
        else
        {
            html = "<span style='font-weight: 800; color: rgba(19,157,0,0.91)'>+ R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
        }

        $("#credito-restante").html(html);




        //reseta valores da modal
        $('#produto_id').val("").trigger("change");
        $("#tabela_preco_id").val("").trigger("change");

        $("#produto_quantidade").val("");
        $("#produto_preco_unitario").val("");
        $("#produto_preco_venda").val("");
        $("#produto_valor_desconto").val("");
        $("#produto_percentual_desconto").val("");
        $("#produto_preco_total").val("");

        $('#modal-produto').closeModal();

    }
}


//---------------------------------------------------------------------------
//
// Exclui o produto da lista de itens
//
//---------------------------------------------------------------------------
function excluiProduto(row)
{
    $(row).closest("tr").remove();

    calculaTotalPedido();



    //calcula o valor restante de crédito do cliente, após excluir o item no pedido
    var valorTotal = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
        valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });

    var credito  = $("#vxglocli_id option:selected").attr("data-credito-disponivel");
    var restante = parseFloat(credito) - parseFloat(valorTotal);
    var html     = "";

    if(parseFloat(restante) <= 0.00)
    {
        html = "<span style='font-weight: 800; color: rgba(182,11,35,0.8)'>- R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
    }
    else
    {
        html = "<span style='font-weight: 800; color: rgba(19,157,0,0.91)'>+ R$ "+number_format(Math.abs(parseFloat(restante)),2,',','.')+"</span>";
    }

    $("#credito-restante").html(html);

}



//---------------------------------------------------------------------------
//
// Calcula valor total do item
//
//---------------------------------------------------------------------------
function calculaTotalPedido()
{
    //quantidade
    var quantidade = 0;
    $("#ipvenda-tbody input[name='produto_quantidade[]']").each(function(){
        quantidade = parseInt(quantidade) + parseInt($(this).val());
    });
    $(".pedido-quantidade-produto").html(quantidade);

    //valor de venda
    var valorUnitario = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_venda[]']").each(function(){
        valorUnitario = parseFloat(valorUnitario) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });
    $(".pedido-valor-unitario").html('R$ ' + number_format(valorUnitario,2,',','.'));

    //valor total
    var valorTotal = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_total[]']").each(function(){
        valorTotal = parseFloat(valorTotal) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });

    $(".pedido-valor-total").html('R$ ' + number_format(valorTotal,2,',','.'));


    //percentual de desconto
    var percentualDesconto = calculaDescontoPedido();

    if(parseFloat(percentualDesconto) > parseFloat( $("#vxfatrisco option:selected").text().replace('.','').replace(',','.') ))
    {
        $(".pedido-percentual-desconto").css("color","#b60b23").html(number_format(percentualDesconto ,2,',','.')+' %  <small>(acima dos '+$("#vxfatrisco option:selected").text()+'% permitido)</small>');
    }
    else
    {
        $(".pedido-percentual-desconto").css("color","#666666").html(number_format(percentualDesconto ,2,',','.')+' %');
    }


}



//-------------------------------------------------------------------------------
//
// Calcula desconto total do pedido
//
//-------------------------------------------------------------------------------
function calculaDescontoPedido()
{
    var precoUnitario = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_unitario[]']").each(function(){
        precoUnitario = parseFloat(precoUnitario) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });

    var precoVenda = 0.00;
    $("#ipvenda-tbody input[name='produto_preco_venda[]']").each(function(){
        precoVenda = parseFloat(precoVenda) + parseFloat($(this).val().replace('.','').replace(',','.'));
    });

    var percentualDesconto = 0.00;

    if(parseFloat(precoVenda) < parseFloat(precoUnitario))
    {
        percentualDesconto = 100 - (parseFloat(precoVenda) * 100 / parseFloat(precoUnitario));
    }

    return percentualDesconto;
}



//-------------------------------------------------------------------------------
//
// Valida se há lotes/estoque disponível dos itens, antes de concluir o pedido
//
//-------------------------------------------------------------------------------
function validaLotes()
{
    var success = true;

    var total = $(".item-pedido").length;
    var index = 0;

    $(".item-pedido").each(function(){
        var id    = this.id;
        var split = id.split('-');
        var hash  = split[2];

        var produto    = $("#produto-id-"+hash).val();
        var tabela     = $("#produto-tabela-id-"+hash).val();
        var quantidade = $("#produto-quantidade-"+hash).val();

        //verifica se existem lotes disponíveis para o produto
        var form = new FormData();
        form.append("produto_id", produto);
        form.append("tabela_id", tabela);
        form.append("quantidade", quantidade);

        var settings = {
            "url": "/api/v1/lotes/pedidos-itens",
            "method": "POST",
            "headers": {
                "request-ajax": "Token "+ ajaxToken()
            },
            "processData": false,
            "contentType": false,
            "mimeType": "multipart/form-data",
            "data": form
        };

        $.ajax(settings).done(function (response) {
            if(response.success === false)
            {
                success = false;
                Materialize.toast(response.log.error, 5000, 'red');
            }

            index++;
        });
    });


    return success;


}

//---------------------------------------------------------------------------
//
// Submete o formulário
//
//---------------------------------------------------------------------------
$("#btn-submit").on("click",function(){
    $("#form-pedido").submit();
});
