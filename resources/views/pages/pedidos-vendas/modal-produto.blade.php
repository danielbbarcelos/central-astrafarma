<div id="modal-produto" class="modal modal-fixed-footer" style="background-color: #fff; z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 250.516304347826px;">
    <div class="modal-content">
        <h4 class="">Adicionar item</h4>

        <div class="flash-message" id="erro-produto" hidden style="margin-bottom: 20px">
            <div class="chip chip-message-danger padding-bottom-20">
                <span></span>
                <i class="close material-icons">close</i>
            </div>
        </div>

        <!-- Itens padrões da tabela de preço selecionada e do produto selecionado -->
        <input type="hidden" value="" id="produto_preco_unitario">
        <input type="hidden" value="" id="produto_preco_maximo">
        <input type="hidden" value="" id="produto_desconto_maximo">
        <input type="hidden" value="" id="produto_fator">
        <input type="hidden" value="" id="produto_erp_id">
        <input type="hidden" value="" id="produto_descricao">


        <div class="row row-input padding-bottom-10">
            <div class="input-field col s12">
                <select id="produto_id" class="select2" style="width: 100%">
                    <option value="">Selecione...</option>
                    @if($pedido->vxfattabprc_erp_id !== null and $pedido->vxglocli_erp_id !== null)
                        @foreach($tabelas as $tabela)
                            @if($tabela->erp_id == $pedido->vxfattabprc_erp_id)
                                @foreach($tabela->produtos as $item)
                                    @if($item->uf == json_decode($pedido->cliente_data)->uf)
                                        <option value="{{$item->id}}"
                                                erp_id="{{$item->erp_id}}"
                                                descricao="{{$item->descricao}}"
                                                preco_unitario="{{$item->preco_venda}}"
                                                preco_maximo="{{$item->preco_maximo}}"
                                                valor_desconto="{{$item->valor_desconto}}"
                                                fator="{{$item->fator}}"
                                                >{{$item->erp_id.': '.$item->descricao}}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="active">Produto</label>
            </div>
        </div>

        <div class="row row-input">
            <div class="input-field col s3">
                <input type="text" value="" class="mask-inteiro-nm" placeholder="" id="produto_quantidade" onkeyup="calculaPrecoTotalProduto()" onfocusout="validaDesconto()">
                <label>Quantidade</label>
            </div>

            <div class="input-field col s3">
                <input type="text" value="" class="mask-decimal" placeholder="" id="produto_preco_venda" readonly>
                <label>Preço de venda (R$)</label>
            </div>

            <div class="input-field col s3">
                <input type="text" value="" class="mask-decimal-zero" placeholder="" id="produto_valor_desconto" onkeyup="calculaPrecoTotalProduto()" onfocusout="validaDesconto()">
                <label>Valor desconto (R$)</label>
            </div>

            <div class="input-field col s3 right-align">
                <input type="text" value="" readonly placeholder="" id="produto_preco_total">
                <label>Preço final (R$)</label>
            </div>
        </div>

        <div class="row row-input padding-top-30" id="div-produto-data" hidden>
            <div class="input-field col s12">
                <label>Preço padrão do produto: <span class="font-weight-600" id="produto_preco_padrao">R$ 0,00</span></label>
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