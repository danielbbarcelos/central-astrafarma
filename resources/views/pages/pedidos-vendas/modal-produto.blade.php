<div id="modal-produto" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 250.516304347826px;">
    <div class="modal-content">
        <h4 class="padding-bottom-20">Adicionar item</h4>

        <div class="flash-message" id="erro-produto" hidden>
            <div class="chip chip-message-danger padding-bottom-20">
                <span></span>
                <i class="close material-icons">close</i>
            </div>
        </div>

        <!-- Itens padrões da tabela de preço selecionada -->
        <input type="hidden" value="" id="produto_preco_venda">
        <input type="hidden" value="" id="produto_preco_maximo">
        <input type="hidden" value="" id="produto_desconto_maximo">
        <input type="hidden" value="" id="produto_fator">


        <div class="row row-input padding-bottom-10">
            <div class="input-field col s12">
                <select id="produto_id">
                    <option value="">Selecione...</option>
                    @if($pedido->vxfattabprc_erp_id !== null)
                        @foreach($tabelas as $tabela)
                            @if($tabela->erp_id == $pedido->vxfattabprc_erp_id)
                                @foreach($tabela->produtos as $item)
                                    <option value="{{$item->id}}" preco_venda="{{$item->preco_venda}}" preco_maximo="{{$item->preco_maximo}}" valor_desconto="{{$item->valor_desconto}}" fator="{{$item->fator}}">
                                        {{$item->descricao}}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </select>
                <label>Produto</label>
            </div>
        </div>

        <div class="row row-input">
        <div class="input-field col s3">
            <input type="text" value="" class="mask-inteiro-nm" placeholder="" id="produto_quantidade" onkeyup="calculaPrecoTotalProduto()">
            <label>Quantidade</label>
        </div>

        <div class="input-field col s3">
            <input type="text" value="" class="mask-decimal" placeholder="" id="produto_preco_unitario" onkeyup="calculaPrecoTotalProduto()">
            <label>Preço unitário (R$)</label>
        </div>

        <div class="input-field col s3">
            <input type="text" value="" class="mask-decimal-zero" placeholder="" id="produto_valor_desconto" onkeyup="calculaPrecoTotalProduto()">
            <label>Valor desconto (R$)</label>
        </div>

        <div class="input-field col s3">
            <input type="text" value="" readonly placeholder="" id="produto_preco_total">
            <label>Preço final (R$)</label>
        </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="padding-bottom-10 padding-right-10">
            <a class="modal-action waves-effect waves-blue btn-flat btn-submit" onclick="adicionaProduto()">Confirmar</a>
            <a class="modal-action modal-close waves-effect waves-red btn-flat ">Voltar</a>
        </div>
    </div>
</div>