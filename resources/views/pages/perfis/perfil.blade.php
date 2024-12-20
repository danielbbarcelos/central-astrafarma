@extends('layouts.template')

@section('page-title', 'Perfil de acesso')

@section('page-css')

@endsection

@section('page-breadcrumbs')

    <div class="middle breadcrumbs">
        <ul class="breadcrumbs-itens breadcrumbs_chevron">
            <li class="breadcrumbs__item"><a href="{{url('/perfis')}}" class="breadcrumbs__element">Lista de perfis</a></li>
            <li class="breadcrumbs__item breadcrumbs__item_active"><span class="breadcrumbs__element">Cadastro de perfil de acesso</span></li>
        </ul>
    </div>

@endsection

@section('page-content')

    <div class="row padding-right-20">
        <div class="middle col l12 m12 s12">
            <div class="card-panel">
                <div class="card-content">
                    <span class="card-title">
                        Cadastro de perfil de acesso
                    </span><br>
                    <div class="row">
                        <form method="post">

                            {{csrf_field()}}

                            <div class="row row-input">
                                <div class="input-field col s12 m12 l6">
                                    <input type="text" placeholder="" name="nome" class="validate" maxlength="100" required value="{{$perfil->nome or old('nome')}}">
                                    <label>Nome</label>
                                </div>
                                <div class="input-field col s12 m12 l6">
                                    <input type="text" placeholder="" name="descricao" class="validate" maxlength="100" required value="{{$perfil->descricao or old('descricao')}}">
                                    <label>Descrição</label>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col s12 padding-top-10">
                                    <p class="p-v-xs">
                                        <input type="checkbox" class="filled-in" id="status" name="status" value="1" @if($perfil->status == '1') checked @endif />
                                        <label for="status">Perfil de acesso ativo</label>
                                    </p>
                                </div>
                            </div>


                            <hr style="border: 1px solid #e3e3e3">
                            <br>

                            <div class="col s12">
                                <label class="text-semibold">Permissões de acesso</label><br><br>

                                <div class="row">
                                    @foreach($permissoes as $permissao => $locator)
                                        <div class="col s12 m4 card-permissao" style="margin-top: 30px; min-height: 250px">
                                            <strong class="text-black-50">{!! str_replace(">",'<i class="material-icons card-breadcrumb-separator">chevron_right</i>',$permissao) !!}</strong><br><br>
                                            @foreach($locator as $titulo)
                                                @foreach($titulo as $function)

                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="perfil_permissoes[]"
                                                               value="{{$function['id']}}" class="cursor-pointer permissao"
                                                               id="permissao_{{$function['id']}}"
                                                               data-superior="{{$function['superior']}}"
                                                               data-prioridade="{{$function['prioridade']}}"
                                                               data-codigo="{{$function['codigo']}}"
                                                               @if(in_array($function['id'], $perfilPermissoes)) checked @endif>
                                                        <label for="permissao_{{$function['id']}}" class="cursor-pointer" @if($function['prioridade'] == 1) style="font-weight: 800" @endif>
                                                            {{$function['descricao']}}
                                                        </label>
                                                    </div>

                                                    <script>
                                                        if('{!! $function['prioridade'] !!}' === '0')
                                                        {
                                                            var superior = '{!! $function['superior'] !!}';

                                                            if($(".permissao[data-codigo='"+ superior +"']").prop('checked') === false)
                                                            {
                                                                var codigo = '{!! $function['codigo'] !!}';

                                                                $(".permissao[data-codigo='"+ codigo +"']").prop('checked',false).attr("disabled",true)
                                                            }
                                                        }
                                                    </script>
                                                @endforeach
                                            @endforeach
                                            <br>
                                        </div>
                                    @endforeach
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

    <script src="{{url('/assets/js/pages/perfil.b3967a0e938dc2a6340e258630febd5a.js')}}"></script>

    @if($perfil->nome == 'Administrador')

        <script>
            $("input,button[type='submit']").attr('disabled',true);
        </script>

    @endif


    @if($action == 'read')
        <script>
            $("input,textarea,select").attr('disabled',true);
            $("button[type='submit']").css('display','none');
        </script>
    @endif

@endsection


