
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
        $("#cidade").empty().html('');

        $("#cidade").append(
            $("<option disabled selected></option>").attr("value","").text("Selecione o estado")
        );

        $("#cidade").material_select('update');
    }
    else
    {

        $.ajax({
            type: "GET",
            url: "/api/v1/estados/"+this.value+"/cidades",
            dataType: 'json',
            success: function(data) {

                $("#cidade").empty().html('');

                $.each(data.result.cidades, function(i, cidade) {
                    $("#cidade").append(
                        $("<option></option>").attr("value",cidade.nome).text(cidade.nome)
                    );
                });

                $("#cidade").material_select('update');

            },
        });
    }
});
