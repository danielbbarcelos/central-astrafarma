<div id="style_switcher">
    <div id="style_switcher_toggle" class="">
        <i class="material-icons">store</i>
    </div>
    <div class="uk-margin-medium-bottom" id="support_ambiente">
        <div style="padding-bottom: 25px;">
            <label class="text-bold" >Filiais disponíveis:</label>
        </div>

        @foreach($filiais as $item)

            <div class="text-bold"
                 @if(Auth::user()->vxwebuseref_id !== $item->id)
                    onclick="selecionaFilial('{!! $item->id !!}')" style="padding-bottom: 10px; cursor: pointer; font-size: 12px;"
                 @else
                    style="padding-bottom: 10px; font-size: 12px;"
                 @endif>

                {{$item->empfil->filial_erp_id.' - '.$item->empfil->nome}}

                @if(Auth::user()->vxwebuseref_id == $item->id)
                    <i class="material-icons" style="font-size: 12px; color: #99ce80;">check_circle</i>
                @endif
            </div>

        @endforeach
    </div>
</div>
