$("#upload").on("change",function(){

    var fileTypes = ['zip','rar'];

    if (this.files && this.files[0]) {
        var reader = new FileReader();
        var extension = this.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
        if (!isSuccess)
        {
            $("#upload").val("");

            Materialize.toast('Arquivo inválido. Extensões permitidas: zip ou rar', 5000, 'red');
        }
    }
});

//chamada realizada através da página de logs de sincronizaçao

function novoChamado(id, metodo, entidade, entidade_id, webservice, updated_at, log)
{

    var mensagem = "VEX Sync ID "+id+" apresentando erros.\n\n";
    mensagem    += "Chamada: "+metodo+" - "+webservice+"\n";
    mensagem    += "Entidade: "+entidade+"\n";
    mensagem    += "ID da entidade: "+entidade_id+"\n";
    mensagem    += "Última atualização: "+updated_at+"\n\n";
    mensagem    += "Mensagem de erro: "+log+"\n\n";

    $("#assunto").val("VEX Sync com erros - ID "+id).attr('readonly',true);
    $("#mensagem").val(mensagem);
    $("#tipo2").attr("checked",true);
    $("#modal-adiciona").openModal();
    $("#mensagem").focus();
}