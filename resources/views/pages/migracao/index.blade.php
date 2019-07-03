@extends('layouts.template')

@section('page-title', 'Migração de dados')

@section('page-css')

@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Migração de dados
                    </span><br>
                    <div class="row">
                        <form method="post" id="form-migracao">

                            {{csrf_field()}}
                            <div class="row row-input">

                                <div class="input-field col s12 m12">
                                    <select name="tabela" id="tabela" required class="select2">
                                        <option value="" selected disabled>Selecione...</option>
                                        <option value="vx_est_armz">Armazéns</option>
                                        <option value="vx_glo_cli">Clientes</option>
                                        <option value="vx_glo_cpgto">Condições de pagamento</option>
                                        <option value="vx_est_lote">Lotes de produtos</option>
                                        <option value="vx_glo_prod">Produtos</option>
                                        <option value="vx_fat_tabprc">Tabelas de preços</option>
                                        <option value="vx_fat_vend">Vendedores</option>
                                    </select>
                                    <label class="active">Tabela a ser migrada</label>
                                </div>

                                <div class="input-field col s12 m12">
                                    <input type="text" placeholder="Ex: all, 001..." name="uri" class="validate" maxlength="30"  value="">
                                    <label>Complemento da URL (opcional) </label>
                                </div>
                                <div class="input-field col s12 m12">
                                    <input type="password" placeholder="" name="password" class="validate" maxlength="30" required value="">
                                    <label>Senha </label>
                                </div>
                            </div>



                            <div class="col s12 right-align">
                                <button type="submit" class="waves-effect waves-light btn blue btn-submit">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



@section('page-scripts')

    <script>
        $("#form-migracao").on("submit",function(){
            $("button[type='submit']").attr("disabled",true).text("Processando...");
        })
    </script>

@endsection

