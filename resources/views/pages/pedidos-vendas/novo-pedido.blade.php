@extends('layouts.template')

@section('page-title', 'Novo pedido de venda')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/pedidos-vendas')}}" class="breadcrumbs__element">Lista de pedidos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Novo pedido de venda</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div id="app">
        <add-pedido-venda-component></add-pedido-venda-component>
    </div>


@endsection

@section('page-scripts')

    <script type="text/javascript" src="{{ url('/assets/vue/js/app.js')}}"></script>

@endsection
