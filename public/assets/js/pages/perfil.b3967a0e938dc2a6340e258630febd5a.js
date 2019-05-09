$(".permissao").on("change",function(){

    var codigo      = $(this).attr('data-codigo');
    var prioridade  = $(this).attr('data-prioridade');
    var superior    = $(this).attr('data-superior');

    if($(this).prop('checked') === true)
    {
        if(prioridade === '1')
        {
            $(".permissao[data-superior='"+codigo+"']").attr("disabled",false);
        }
    }
    else
    {
        if(prioridade === '1')
        {
            $(".permissao[data-superior='"+codigo+"']").prop('checked',false).attr("disabled",true);
        }
    }

});
