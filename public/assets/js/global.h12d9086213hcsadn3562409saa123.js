$(document).ready(function(){

    // ========================================
    //
    // Inicializa mÃ¡scaras dos campos
    //
    // ========================================
    try
    {
        $('.mask-cpf').inputmask('999.999.999-99');
        $('.mask-cnpj').inputmask('99.999.999/9999-99');
        $('.mask-cpf-cnpj').inputmask(['999.999.999-99','99.999.999/9999-99']);
        $('.mask-data').inputmask('99/99/9999');
        $('.mask-cep').inputmask('99999-999');
        $('.mask-ddd').inputmask('(99)');
        $(".mask-ddd-fone").inputmask( "(99) 9999-9999[9]" ).keyup();
        $(".mask-fone").inputmask( "9999-9999[9]" );
        $('.mask-uf').inputmask('AA');
        $('.mask-placa-veiculo').inputmask('AAA-9999');
        $('.mask-horario').inputmask('99:99');
        $('.mask-mac-address').inputmask('**:**:**:**:**:**');
        $('.mask-agencia').inputmask(['9','99','999','9999']);
        $('.mask-conta').inputmask(['9999','99999','999999','9999999','99999999','999999999','9999999999']);
        $('.mask-digito-verificador').inputmask('9');

        $(".mask-decimal").maskMoney({thousands:'.', decimal:',', symbolStay: true});
        $(".mask-inteiro").maskMoney({thousands:'.', decimal:',', symbolStay: true,  precision: 0});
        $(".mask-inteiro-nm").maskMoney({thousands: '', decimal: '', symbolStay: false,  precision: 0}); //sem mÃ¡scara para unidades de milhar
        $(".mask-decimal-zero").maskMoney({thousands:'.', decimal:',', symbolStay: true, allowZero: true});
        $(".mask-inteiro-zero").maskMoney({thousands: '.', decimal: ',', symbolStay: true, allowZero: true, precision: 0});
    }
    catch(err)
    {
        //
    }

    $('.mask-ddd-fone').on("keyup",function(event) {

        var number = $(this).val().replace(/[^0-9]/g, '');

        if(number.length > 10) // Celular com 9 dÃ­gitos + 2 dÃ­gitos DDD e 4 da mÃ¡scara
        { 
            $(this).inputmask('(99) 99999-999[9]');
        } else {
            $(this).inputmask('(99) 9999-9999[9]');
        }
    });


    $('.mask-fone').on("keyup",function(event) {

        var number = $(this).val().replace(/[^0-9]/g, '');

        if(number.length > 8) 
        { 
            $(this).inputmask('99999-999[9]');
        } else {
            $(this).inputmask('9999-9999[9]');
        }
    });

    $(".mask-ddd-fone").keyup();
    $(".mask-fone").keyup();

});


// ==========================================================
//
// Função global para excluir um registro
//
// ==========================================================
function excluiItem(action)
{
    swal({
        title: "",
        text: "<br><strong style='font-weight: 700; font-family: Rubik, sans-serif; padding-bottom: 60px'>Deseja realmente excluir?</strong><br><br><strong  style='font-weight: 500; font-family: Rubik, sans-serif;'>Não será possível recuperar o item selecionado</strong><br><br>",
        type: "error",
        html: true,
        showCancelButton: true,
        confirmButtonColor: "rgba(249,27,40,0.78)",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Voltar",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if (isConfirm) {
            $("#form-delete").attr('action', action).submit();
        } else {
            //
        }
    });
}

// ==========================================================
//
// FunÃ§Ã£o para formatar decimais semelhante a funÃ§Ã£o em PHP
//
// ==========================================================
function number_format (number, decimals, decPoint, thousandsSep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
    var n = !isFinite(+number) ? 0 : +number
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
    var s = ''

    var toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec)
        return '' + (Math.round(n * k) / k)
        .toFixed(prec)
    }

    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
    }

    return s.join(dec)

}