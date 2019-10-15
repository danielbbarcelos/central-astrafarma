@extends('layouts.template')

@section('page-title', 'Configurações gerais do usuário')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/usuarios'.($user->type == 'S' ? '?suporte=1' : ''))}}" class="breadcrumbs__element">Lista de usuários</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Configurações gerais do usuário {{'#'.str_pad($user->id,3,'0',STR_PAD_LEFT)}}</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row padding-right-20">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Configurações gerais do usuário {{'#'.str_pad($user->id,3,'0',STR_PAD_LEFT)}}
                    </span><br>
                    <div class="row">
                        <form class="s12" method="post" enctype="multipart/form-data">

                            {{csrf_field()}}

                            <div class="padding-top-20">
                                <div class="row">
                                    <div class="col s2 font-weight-800">Nome:</div>
                                    <div class="col s10">{{$user->name}}</div>
                                </div>
                                <div class="row">
                                    <div class="col s2 font-weight-800">E-mail de acesso:</div>
                                    <div class="col s10">{{$user->email}}</div>
                                </div>
                            </div>

                            <hr style="border: 1px solid #e3e3e3">

                            <h6 class="card-title font-weight-800 padding-top-20">
                                Dashboard do usuário
                            </h6><br>

                            <div class="row row-input">
                                <div class="col s12">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="assinatura_status" name="assinatura_status" value="1" @if($dashboard->assinatura_status == '1') checked @endif />
                                        <label for="assinatura_status" class="font-weight-500 text-dark">Exibição do resumo da assinatura</label>
                                    </p>
                                </div>
                            </div>

                            <div class="row row-input">
                                <div class="col s12">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in input-bi" id="bi_status" name="bi_status" value="1" @if($dashboard->bi_status == '1') checked @endif />
                                        <label for="bi_status" class="font-weight-500 text-dark">Exibição de BI do usuário</label>
                                    </p>
                                </div>
                            </div>


                            <div class="row row-input padding-top-40" @if($dashboard->bi_url == null) hidden @endif id="div-bi">
                                <div class="input-field col s12">
                                    <input type="text" placeholder="" name="bi_url" id="bi_url" class="validate input-bi" @if($dashboard->bi_url == null) disabled @else required @endif
                                           value="{{$dashboard->bi_url or old('bi_url')}}">
                                    <label>URL do BI</label>
                                </div>
                            </div>


                            <div class="col s12 right-align padding-top-40">
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


    @if($user->type == 'A')

        <script>
            $("input,select").not(".input-bi").attr('disabled',true);
        </script>

    @endif

    <script>
        $("#bi_status").on("change",function(){

            if($(this).prop('checked') === true)
            {
                $("#div-bi").attr("hidden",false);
                $("#bi_url").attr("disabled",false).attr("required",true).val("");
            }
            else
            {
                $("#div-bi").attr("hidden",true);
                $("#bi_url").attr("disabled",true).attr("required",false).val("");
            }
        });
    </script>

@endsection

