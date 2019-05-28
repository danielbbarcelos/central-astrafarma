@extends('layouts.template')

@section('page-title', 'Configurações gerais')

@section('page-css')

    <style>


    </style>

@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Configurações gerais do sistema
                    </span><br>
                    <div class="row">
                        <form class="s12" method="post" enctype="multipart/form-data">

                            {{csrf_field()}}



                            <div class="col s12">
                                <h6 class="card-title font-weight-500 padding-top-20">
                                    Logo da empresa
                                </h6>
                            </div>
                            <div class="row">
                                <div class="input-field col s3">
                                    <div id="logo-empresa-div">
                                        <img src="{{isset($configuracao->logo_empresa) ? $configuracao->logo_empresa : url('/assets/img/icons/no-photo.png')}}" id="logo-empresa-img">
                                        <input type="file" id="logo-empresa-input" name="logo_empresa" style="display:none">
                                    </div>
                                </div>
                            </div>


                            <hr style="border: 1px solid #e3e3e3">

                            <div class="col s12">
                                <h6 class="card-title font-weight-500 padding-top-20">
                                    Template utilizado para impressão em PDF
                                </h6>
                                <small>Aplica-se para impressão de pedidos de vendas e relatórios em geral</small>
                            </div>
                            <br><br><br><br><br>

                            <div style="cursor: pointer">
                                <div class="col s6 m3 l2" onclick="selecionaTemplate('aqua')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-aqua ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'aqua') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-aqua.png" width="100%">
                                    </div>
                                </div>

                                <div class="col s6 m3 l2" onclick="selecionaTemplate('green')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-green ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'green') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-green.png" width="100%">
                                    </div>
                                </div>

                                <div class="col s6 m3 l2" onclick="selecionaTemplate('grey')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-grey ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'grey') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-grey.png" width="100%">
                                    </div>
                                </div>

                                <div class="col s6 m3 l2" onclick="selecionaTemplate('red')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-red ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'red') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-red.png" width="100%">
                                    </div>
                                </div>

                                <div class="col s6 m3 l2" onclick="selecionaTemplate('transparent')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-transparent ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'transparent') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-transparent.png" width="100%">
                                    </div>
                                </div>

                                <div class="col s6 m3 l2" onclick="selecionaTemplate('yellow')" style="cursor: pointer">
                                    <div class="card-panel">
                                        <div class="template-selected template-ribbon-yellow ribbon ribbon-top-right" @if(strpos($configuracao->pdf_template, 'yellow') == false) hidden @endif>
                                            <span>Selecionado</span>
                                        </div>
                                        <img src="/assets/img/pdf/border-yellow.png" width="100%">
                                    </div>
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

    <script src="{{url('/assets/js/pages/configuracoes.0cc175b9c0f1b6a831c399e269772661.js')}}"></script>


@endsection

