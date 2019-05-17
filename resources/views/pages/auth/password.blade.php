@extends('layouts.template')

@section('page-title', 'Alteração de senha')

@section('page-css')

@endsection

@section('page-content')

    <div class="middle padding-top-20 padding-right-20">
        <div class="row">
            <div class="col s12">
                <div class="page-title"></div>
            </div>
            <div class="col l12 m12 s12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title padding-left-10">Alteração de senha</span><br>
                        <div class="row">
                            <form class="col s12" method="post">

                                {{csrf_field()}}
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input type="password" name="password" class="validate" placeholder="" required>
                                        <label>Nova senha</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input type="password" name="confirm_password" class="validate" placeholder="" required>
                                        <label>Confirmação de senha</label>
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
    </div>


@endsection


@section('page-scripts')


@endsection