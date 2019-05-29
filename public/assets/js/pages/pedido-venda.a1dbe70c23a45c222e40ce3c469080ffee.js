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

    var valor = $('.pedido-valor-total').html().replace('R$ ','').replace('.','').replace(',','.');
    if(parseFloat(valor) <= 0.00)
    {
        Materialize.toast('O valor total dos produtos deve ser maior que R$ 0,00', 5000, 'red');

        return false;
    }

    return true;

}

function validateStepFour() {

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

    $("#form-pedido").submit();

}


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

        //armazena valor para listar produtos da tabela de preço (produtos do mesmo estado do cliente)
        $("#uf-tabela-preco").val($("#vxglocli_id option:selected").attr("data-uf"));
    }
});



var tabelaValue = $("#vxfattabprc_id").val();

$("#vxfattabprc_id").on("change",function(){

    var itens = 0;

    $("#ipvenda-tbody input[name='produto_id[]']").each(function(){
        itens++;
    });

    if(parseInt(itens) === 0)
    {
        alteraTabelaPreco()
    }
    else if(tabelaValue !== this.value)
    {
        var modalType    = 'warning';
        var modalAction  = '';
        var modalTitle   = 'Você adicionou itens ao pedido.';
        var modalContent = 'Ao confirmar a alteração da tabela, todos os itens serão perdidos. Deseja continuar?';
        modalDialogCustom(modalType, modalAction, modalTitle, modalContent);

    }
});

$("#modal_dialog_warning_btn_confirm").on("click", function(){
    $("#modal_dialog_warning").closeModal();
    alteraTabelaPreco();
});

$("#modal_dialog_warning_btn_close").on("click", function(){

    $('#vxfattabprc_id').find('option[value="'+tabelaValue+'"]').prop('selected', true).trigger("change");

    $("#modal_dialog_warning").closeModal();
});


function alteraTabelaPreco()
{
    tabelaValue = $("#vxfattabprc_id").val();

    $("#ipvenda-tbody").html('');

    calculaTotalPedido();

    $("#btn-concluir").attr("disabled",false);

    var prefix   = "vxfattabprc_"+$("#vxfattabprc_id").val();

    //exibe apenas os produtos cadastrados na tabela de preço
    var produtos = JSON.parse($("#"+prefix+"_produtos").val());


    var options = "<option value='' disabled selected>Selecione...</option>";


    $.each(produtos, function(index){

        if(produtos[index].uf === $("#uf-tabela-preco").val())
        {
            options += '<option value="'+produtos[index].id+'" erp_id="'+produtos[index].erp_id+'" descricao="'+produtos[index].descricao+'" preco_unitario="'+produtos[index].preco_venda+'" preco_maximo="'+produtos[index].preco_maximo+'" valor_desconto="'+produtos[index].valor_desconto+'" fator="'+produtos[index].fator+'">'+produtos[index].erp_id+': '+produtos[index].descricao+'</option>';
        }
    });

    $("#produto_id").html(options).trigger("change");
}



$("#produto_id").on("change", function(){
    if(this.value === '')
    {
        $("#div-produto-data").attr("hidden",true);

        $("#produto_erp_id").val("");
        $("#produto_descricao").val("");
        $("#produto_quantidade").val("");
        $("#produto_preco_unitario").val("");
        $("#produto_preco_venda").val("");
        $("#produto_valor_desconto").val("");
        $("#produto_preco_total").val("");
    }
    else
    {
        $("#div-produto-data").attr("hidden",false);
        $("#produto_preco_padrao").html("R$ "+number_format($('option:selected', this).attr('preco_unitario'),2,',','.'));


        $("#produto_erp_id").val($('option:selected', this).attr('erp_id'));
        $("#produto_descricao").val($('option:selected', this).attr('descricao'));
        $("#produto_quantidade").val(1);
        $("#produto_preco_unitario").val(number_format($('option:selected', this).attr('preco_unitario'),2,',','.'));
        $("#produto_preco_venda").val(number_format($('option:selected', this).attr('preco_unitario'),2,',','.'));
        $("#produto_preco_total").val(number_format($('option:selected', this).attr('preco_unitario'),2,',','.'));
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


    //recalcula preço unitário com base no preço total encontrado
    if(parseInt(quantidade) > 0)
    {
        preco_unitario = parseFloat(preco_total) / parseInt(quantidade);
        $("#produto_preco_venda").val(number_format(preco_unitario.toFixed(2),2,',','.'));
    }
}


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

        var row = "<tr>";
        row    += "<td style='width: 40%'>";
        row    += "<input type='hidden' name='vxfatipvend_id[]' value=''>";
        row    += "<input type='hidden' name='produto_id[]' value='"+$("#produto_id").val()+"'>";
        row    += "<input type='hidden' name='produto_quantidade[]' value='"+$("#produto_quantidade").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_unitario[]' value='"+$("#produto_preco_unitario").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_venda[]' value='"+$("#produto_preco_venda").val()+"'>";
        row    += "<input type='hidden' name='produto_valor_desconto[]' value='"+$("#produto_valor_desconto").val()+"'>";
        row    += "<input type='hidden' name='produto_preco_total[]' value='"+$("#produto_preco_total").val()+"'>";
        row    += "<a title='"+$("#produto_erp_id").val()+"' href='/produtos/"+$("#produto_id").val()+"/show' target='_blank'>"+$("#produto_descricao").val()+"</a>";
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
        $('#produto_id').find('option[value=""]').prop('selected', true);
        $("#produto_id").material_select();

        $("#produto_erp_id").val("");
        $("#produto_descricao").val("");
        $("#produto_quantidade").val("");
        $("#produto_preco_unitario").val("");
        $("#produto_preco_venda").val("");
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


$("#btn-submit").on("click",function(){
    $("#form-pedido").submit();
});