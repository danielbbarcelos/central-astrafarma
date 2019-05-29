
$("input[name='tipo_pessoa']").on("change", function(){

    var tipo = this.value;

    if(tipo === 'F')
    {
        $("#nome_fantasia").val("").focus().attr("disabled",true).attr("required",false);
        $("#label_razao_social").html("Nome completo");
    }
    else
    {
        $("#nome_fantasia").val("").attr("disabled",false).attr("required",true);
        $("#label_razao_social").html("Razão social");
    }
});


$("#uf").on("change", function (){

    if(this.value === '')
    {
        var options = "<option disabled selected>Selecione o estado</option>";

        $("#cidade").html(options).trigger("change");
    }
    else
    {

        $.ajax({
            type: "GET",
            url: "/api/v1/estados/"+this.value+"/cidades",
            dataType: 'json',
            success: function(data) {

                var options = "";

                $.each(data.result.cidades, function(i, cidade) {

                    options += "<option value='"+cidade.nome+"'>"+cidade.nome+"</option>";

                });

                $("#cidade").html(options).trigger("change");

            },
        });
    }
});
