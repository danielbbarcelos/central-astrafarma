
<!-- Modal Structure -->
<form method="post" enctype="multipart/form-data" action="{{url('/suporte/chamados/add')}}">
    {{csrf_field()}}

    <div id="modal-adiciona" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 250.516304347826px;">
        <div class="modal-content">
            <h4 class="padding-bottom-20">Novo chamado</h4>

            <div class="row row-input">

                <div class="input-field col s6">
                    <input type="text" name="assunto" class="validate" maxlength="100" required value="{{old('assunto')}}">
                    <label class="f-bold">Assunto</label>
                </div>

                <div class="file-field input-field col s6">
                    <div class="btn teal lighten-1">
                        <input type="file" name="upload">
                        <span>Anexo</span>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Envie o arquivo em formato ZIP">
                    </div>
                </div>

                <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="mensagem" style="height: 6rem" required
                              maxlength="10000" length="10000">{{old('mensagem')}}</textarea>
                    <label>Mensagem</label>
                </div>

                <div class="row row-input padding-bottom-30">
                    <div class="">
                        <p class="">
                            <input class="with-gap" required value="D" name="tipo" type="radio" id="tipo1" />
                            <label for="tipo1" class="new badge warning">Dúvida</label>

                            <input class="with-gap" value="E" name="tipo" type="radio" id="tipo2" />
                            <label for="tipo2">Erro ou problema</label>

                            <input class="with-gap" value="F" name="tipo" type="radio" id="tipo3" />
                            <label for="tipo3">Solicitação de serviço</label>

                            <input class="with-gap" value="S" name="tipo" type="radio" id="tipo4" />
                            <label for="tipo4">Sugestão</label>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="waves-effect waves-blue btn-flat">Confirmar</button>
            <a class="modal-action modal-close waves-effect waves-red btn-flat ">Voltar</a>
        </div>
    </div>

</form>
