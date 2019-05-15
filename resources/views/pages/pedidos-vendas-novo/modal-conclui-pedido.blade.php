<div id="modal-conclui-pedido" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 250.516304347826px;">
    <div class="modal-content">
        <h4 class="padding-bottom-20">Concluir pedido</h4>

        <div class="flash-message" id="erro-produto" hidden>
            <div class="chip chip-message-danger padding-bottom-20">
                <span></span>
                <i class="close material-icons">close</i>
            </div>
        </div>

        <div class="row row-input">

            <div class="input-field col s6">
                <select name="vxglocpgto_id" id="vxglocpgto_id">
                    <option disabled selected>Selecione...</option>
                    @foreach($condicoes as $item)
                        <option value="{{$item->id}}" @if($item->erp_id == $pedido->vxglocpgto_erp_id) selected @endif>{{$item->descricao}}</option>
                    @endforeach
                </select>
                <label>Condição de pagamento</label>
            </div>

            <div class="input-field col s6">
                <input type="text"  value="{{isset($pedido->data_entrega) ? Carbon::createFromFormat('Y-m-d',$pedido->data_entrega)->format('d/m/Y') : ''}}" class="datepicker" placeholder="" name="data_entrega">
                <label>Data prevista da entrega</label>
            </div>


            <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="observacao" style="height: 6rem"
                              maxlength="10000" length="10000">{{$pedido->observacao or old('observacao')}}</textarea>
                <label>Observação</label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="padding-bottom-10 padding-right-10">
            <a class="modal-action waves-effect waves-blue btn-flat" id="btn-submit">Confirmar</a>
            <a class="modal-action modal-close waves-effect waves-red btn-flat ">Voltar</a>
        </div>
    </div>
</div>