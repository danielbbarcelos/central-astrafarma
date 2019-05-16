@extends('layouts.template')

@section('page-title', 'Dispositivo')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/dispositivos')}}" class="breadcrumbs__element">Lista de dispositivos</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Cadastro de dispositivo</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Cadastro de dispositivo
                    </span><br>
                    <div class="row">
                        <form class="s12" method="post">

                            {{csrf_field()}}

                            <div class="row row-input">
                                <div class="input-field col s6">
                                    <input type="text" placeholder="" name="descricao" id="descricao" class="validate" maxlength="100" required value="{{$dispositivo->descricao or old('descricao')}}">
                                    <label>Descrição</label>
                                </div>

                                <div class="input-field col s6">
                                    <input type="text" placeholder="" class="masked" name="device_id" id="device_id" value="{{$dispositivo->device_id or old('device_id')}}" maxlength="50" required>
                                    <label>Device ID</label>
                                </div>
                            </div>


                            <div class="row row-input">
                                <div class="input-field col s12">
                                    <textarea class="materialize-textarea" length="500" name="observacao" cols="10" rows="5"
                                            maxlength="500">{{$dispositivo->observacao or old('observacao')}}</textarea>
                                    <label>Observação</label>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col s12 padding-top-10">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="status" name="status" value="1" @if((int)$dispositivo->status == 1) checked @endif />
                                        <label for="status">Dispositivo ativo</label>
                                    </p>
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



@endsection

