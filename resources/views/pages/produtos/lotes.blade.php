<div class="row padding-top-30">
    @if(count($lotes) == 0)
        Nenhum lote com saldo em estoque encontrado
    @else
        <table class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Lote</th>
                <th>Data de fabricação</th>
                <th>Data de validade</th>
                <th>Qtde de origem</th>
                <th>Saldo em estoque</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lotes as $item)
                <tr>
                    <td>
                        @if($item->dt_valid < Carbon::now()->format('Y-m-d'))
                            <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #e6493e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Lote vencido">fiber_manual_record</i>
                        @elseif($item->dt_valid == Carbon::now()->format('Y-m-d'))
                            <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #fbe053; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Lote a vencer">fiber_manual_record</i>
                        @else
                            <i class="tooltipped material-icons" style="font-size: 14px; z-index: 9999; color: #72bc6e; cursor: pointer" data-position="top" data-delay="10" data-tooltip="Sem ressalvas">fiber_manual_record</i>
                        @endif
                    </td>
                    <td>{{$item->erp_id !== null ? $item->erp_id : '-'}}</td>
                    <td>{{$item->dt_fabric !== null ? Carbon::createFromFormat('Y-m-d',$item->dt_fabric)->format('d/m/Y') : '-'}}</td>
                    <td>{{$item->dt_valid !== null ? Carbon::createFromFormat('Y-m-d',$item->dt_valid)->format('d/m/Y') : '-'}}</td>
                    <td>{{$item->quant_ori}}</td>
                    <td>{{$item->saldo}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>