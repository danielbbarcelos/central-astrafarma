@extends('layouts.template')

@section('page-title', 'Configuração de pedidos de venda')

@section('page-css')


@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Configuração de pedidos de venda
                    </span><br>
                    <div class="row">
                        <form class="s12" method="post" enctype="multipart/form-data">

                            {{csrf_field()}}

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('aqua')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-aqua ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'aqua') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-aqua.png" width="100%">
                                </div>
                            </div>

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('green')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-green ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'green') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-green.png" width="100%">
                                </div>
                            </div>

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('grey')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-grey ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'grey') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-grey.png" width="100%">
                                </div>
                            </div>

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('red')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-red ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'red') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-red.png" width="100%">
                                </div>
                            </div>

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('transparent')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-transparent ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'transparent') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-transparent.png" width="100%">
                                </div>
                            </div>

                            <div class="col s6 m3 l2" onclick="selecionaTemplate('yellow')" style="cursor: pointer">
                                <div class="card-panel">
                                    <div class="template-selected template-ribbon-yellow ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'yellow') == false) hidden @endif>
                                        <span>Selecionado</span>
                                    </div>
                                    <img src="/assets/img/pdf/pvconf/border-yellow.png" width="100%">
                                </div>
                            </div>

                            <div class="col s12 m12 l12">
                                <input hidden id="pdf_template" name="pdf_template" value="{{$configuracao->pdf_template}}">
                                <a onclick="exibeTemplate()" style="cursor: pointer">Clique aqui</a> para exibir o template selecionado
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

        function selecionaTemplate(color)
        {
            $(".template-selected").attr("hidden",true);
            $(".template-ribbon-"+color).attr("hidden",false);
            $("#pdf_template").val("/assets/img/pdf/pvconf/border-"+color+".png");
        }

        function exibeTemplate()
        {
            window.open($("#pdf_template").val());
        }

    </script>

@endsection

