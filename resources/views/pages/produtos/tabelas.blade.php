<div class="row padding-top-30">
    @if(count($tabelas) == 0)
        Nenhum registro encontrado
    @else
        <table class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Cód. ERP</th>
                <th>UF</th>
                <th>Data de vigência</th>
                <th>Descrição</th>
                <th>Preço de venda</th>
                <th>Preço máximo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tabelas as $item)
                <tr>
                    <td>{{$item->erp_id !== null ? $item->erp_id : '-'}}</td>
                    <td>{{$item->uf}}</td>
                    <td>{{$item->data_vigencia !== null ? Carbon::createFromFormat('Y-m-d',$item->data_vigencia)->format('d/m/Y') : '-'}}</td>
                    <td>{{$item->descricao}}</td>
                    <td>R$ {{number_format($item->preco_venda,2,',','.')}}</td>
                    <td>R$ {{number_format($item->preco_maximo,2,',','.')}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>