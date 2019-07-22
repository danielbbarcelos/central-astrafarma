<div id="modal-produto" class="modal modal-fixed-footer" style="min-height: 530px !important; background-color: #fff; z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 250.516304347826px;">
    <div class="modal-content" style="">
        <h4 class="">Adicionar item</h4>

        <div class="flash-message" id="erro-produto" hidden style="margin-bottom: 20px">
            <div class="chip chip-message-danger padding-bottom-20">
                <span></span>
                <i class="close material-icons">close</i>
            </div>
        </div>

        <!-- Itens padrões da tabela de preço selecionada e do produto selecionado -->
        <input type="hidden" value="" id="produto_preco_unitario">
        <input type="hidden" value="" id="produto_fator">
        <input type="hidden" value="" id="produto_erp_id">
        <input type="hidden" value="" id="produto_descricao">


        <div class="row row-input padding-bottom-10">
            <div class="input-field col s12">
                <select id="produto_id" class="select2" style="width: 100%">
                    <option value="">Selecione...</option>
                    @foreach($produtos as $item)
                        <option value="{{$item->id}}" erp_id="{{$item->erp_id}}" descricao="{{$item->descricao}}">{{$item->erp_id.': '.$item->descricao}}</option>
                    @endforeach
                </select>
                <label class="active">Produto</label>
            </div>
        </div>

        <div class="row row-input padding-bottom-10">
            <div class="input-field col s12">
                <select id="tabela_preco_id" class="select2" style="width: 100%">
                    <option value="">Selecione...</option>
                    @foreach($tabelas as $item)
                        <option value="{{$item->id}}">
                            {{$item->erp_id.': '.$item->descricao}}
                        </option>
                    @endforeach
                </select>
                <label class="active">Tabela de preço</label>
            </div>
        </div>


        <div class="row row-input" id="div-saldo-total" hidden>
            <div class="input-field col s12">
                <input type="text" value="" placeholder="" readonly id="saldo-total">
                <label>Saldo total em estoque</label>
            </div>
        </div>


        <div hidden id="div-lote">
            <div class="row row-input padding-bottom-10">
                <div class="input-field col s12">
                    <select id="lote_id" class="select2" style="width: 100%">
                        <option value="">Selecione...</option>
                    </select>
                    <label class="active">Lote</label>
                </div>
            </div>

            <div class="row row-input" id="div-valores-item" hidden>
                <div class="input-field col s3">
                    <input type="text" value="" class="mask-inteiro-nm" placeholder="" id="produto_quantidade" onkeyup="calculaPrecoTotalProduto()">
                    <label>Quantidade</label>
                </div>

                <div class="input-field col s3">
                    <input type="text" value="" class="mask-decimal" placeholder="" id="produto_preco_venda" onkeyup="calculaPrecoTotalProduto()">
                    <label>Preço de venda (R$)</label>
                </div>

                <div class="input-field col s3">
                    <input type="text" value="" class="mask-decimal-zero" placeholder="" id="produto_valor_desconto" readonly>
                    <label>Valor desconto (R$)</label>
                </div>

                <div class="input-field col s3 right-align">
                    <input type="text" value="" readonly placeholder="" id="produto_preco_total">
                    <label>Preço final (R$)</label>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <div class="padding-bottom-10 padding-right-10">
            <a class="modal-action waves-effect waves-blue btn-flat btn-submit" id="btn-adiciona-item" onclick="adicionaProduto()">Confirmar</a>
            <a class="modal-action modal-close waves-effect waves-red btn-flat ">Voltar</a>
        </div>
    </div>
</div>