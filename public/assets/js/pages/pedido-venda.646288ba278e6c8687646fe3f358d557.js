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
        Materialize.toast('Insira as observações do pedido para continuar', 5000, 'red');

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
            //$("#form-pedido").submit();

        }
    }


}



//---------------------------------------------------------------------------
//
// Exibe o resumo do cliente no primeiro passo
//
//---------------------------------------------------------------------------
$("#vxglocli_id").on("change",function(){

    if(this.value === '')
    {
        $("#data-cliente").attr("hidden",true);
    }
    else
    {
        $("#data-cliente").attr("hidden",false);
        $("#cliente-erp-id").html($("#vxglocli_id option:selected").attr("data-erp-id"));
        $("#cliente-razao-social").html($("#vxglocli_id option:selected").attr("data-razao-social"));
        $("#cliente-nome-fantasia").html($("#vxglocli_id option:selected").attr("data-nome-fantasia"));
        $("#cliente-cnpj-cpf").html($("#vxglocli_id option:selected").attr("data-cnpj-cpf"));
        $("#cliente-cidade-uf").html($("#vxglocli_id option:selected").attr("data-cidade-uf"));
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
    $("#div-valores-item").attr('hidden',hidden);

    if(hidden === true)
    {
        $("#produto_quantidade").val('0');
        $("#produto_preco_venda").val('0,00');
        $("#produto_valor_desconto").val('0,00');
        $("#produto_preco_total").val('0,00');
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
                $("#produto_fator").val(number_format(0.00,2,',','.'));
            }
            else
            {
                $("#produto_quantidade").val(1);
                $("#produto_preco_unitario").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_preco_venda").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_preco_total").val(number_format(response.preco.preco_venda,2,',','.'));
                $("#produto_valor_desconto").val('0,00');
                $("#produto_fator").val(number_format(response.preco.fator,2,',','.'));
            }
        });

        calculaPrecoTotalProduto();
    }
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
    var valor_desconto = $("#produto_valor_desconto").val().replace(".","").replace(",",".");

    var preco_total    = quantidade * preco_unitario - valor_desconto;

    $("#produto_preco_total").val(number_format(preco_total,2,',','.'));


    //recalcula preço unitário com base no preço total encontrado
    if(parseInt(quantidade) > 0)
    {
        preco_unitario = parseFloat(preco_total) / parseInt(quantidade);
        $("#produto_preco_venda").val(number_format(preco_unitario.toFixed(2),2,',','.'));
    }
}


//---------------------------------------------------------------------------
//
// Verifica se o desconto do produto inserido é válido
//
//---------------------------------------------------------------------------
function validaDesconto()
{
    var quantidade  = $("#produto_quantidade").val().replace(".","").replace(",",".");
    var preco_venda = $("#produto_preco_venda").val().replace(".","").replace(",",".");
    var preco_total = $("#produto_preco_total").val().replace(".","").replace(",",".");

    if(parseInt(quantidade) > 0)
    {
        if(parseFloat(parseFloat(preco_venda) * parseInt(quantidade)) !== parseFloat(preco_total))
        {
            var ajuste = parseFloat(preco_total) - (parseFloat(preco_venda) * parseInt(quantidade));

            var valor_desconto = $("#produto_valor_desconto").val().replace(".","").replace(",",".");

            $("#produto_valor_desconto").val(number_format(parseFloat(parseFloat(valor_desconto) + parseFloat(ajuste.toFixed(2))),2,',','.'))

            calculaPrecoTotalProduto();

        }
    }

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
        validaDesconto();

        var itemHash = hashGenerator(30,'');

        var row = "<tr>";
        row    += "<td style='width: 40%'>";
        row    += "<input type='hidden' name='vxfatipvend_id[]' value=''>";
        row    += "<input type='hidden' id='produto-id-"+itemHash+"' name='produto_id[]' class='item-pedido' value='"+$("#produto_id").val()+"'>";
        row    += "<input type='hidden' id='produto-tabela-id-"+itemHash+"' name='produto_tabela_id[]' value='"+$("#tabela_preco_id").val()+"'>";
        row    += "<input type='hidden' id='produto-quantidade-"+itemHash+"' name='produto_quantidade[]' value='"+$("#produto_quantidade").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_unitario[]' value='"+$("#produto_preco_unitario").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_venda[]' value='"+$("#produto_preco_venda").val()+"'>";
        row    += "<input type='hidden' name='produto_valor_desconto[]' value='"+$("#produto_valor_desconto").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_total[]' value='"+$("#produto_preco_total").val()+"'>";
        row    += "<a title='"+$("#produto_id option:selected").attr('erp_id')+"' href='/produtos/"+$("#produto_id").val()+"/show' target='_blank'>"+$("#produto_id option:selected").attr('descricao')+"</a>";
        row    += "</td>";
        row    += "<td style='width: 10%'>"+$("#produto_quantidade").val()+"</td>";
        row    += "<td style='width: 15%'>R$ "+$("#produto_preco_venda").val()+"</td>";
        row    += "<td style='width: 15%'>R$ "+$("#produto_valor_desconto").val()+"</td>";
        row    += "<td style='width: 15%'>R$ "+$("#produto_preco_total").val()+"</td>";
        row    += "<td style='width: 12%'><a style='cursor: pointer' onclick='excluiProduto(this)'>Excluir</a></td>";
        row    += "<tr>";

        $("#ipvenda-tbody").append(row);

        calculaTotalPedido();

        //reseta valores da modal
        $('#produto_id').val("").trigger("change");
        $("#tabela_preco_id").val("").trigger("change");

        $("#produto_quantidade").val("");
        $("#produto_preco_unitario").val("");
        $("#produto_preco_venda").val("");
        $("#produto_valor_desconto").val("");
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



//-------------------------------------------------------------------------------
//
// Valida se há lotes/estoque disponível dos itens, antes de concluir o pedido
//
//-------------------------------------------------------------------------------
function validaLotes()
{
    $(".item-pedido").each(function(){
        console.log(this.id)
    })
    //verifica quais lotes serão utilizados pelo produto
    var form = new FormData();
    form.append("produto_id", $("#produto_id").val());
    form.append("tabela_id", $("#tabela_preco_id").val());
    form.append("quantidade", $("#produto_quantidade").val());

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

    });
}

//---------------------------------------------------------------------------
//
// Submete o formulário
//
//---------------------------------------------------------------------------
$("#btn-submit").on("click",function(){
    $("#form-pedido").submit();
});