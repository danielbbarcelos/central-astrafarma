<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="text-center">

                @if(env('LOGO_EMPRESA') !== '' and env('LOGO_EMPRESA') !== null)
                    @if(env('LOGO_LOCAL') == 'true')
                        <img src="{{env('LOGO_EMPRESA')}}" style="max-height: 70px; height: auto; max-width: 100%;">
                    @else
                        <img src="{{env('ADMIN_URL') . env('LOGO_EMPRESA')}}" style="max-height: 70px; height: 100%; width: auto">
                    @endif
                @else
                    <img src="{{url('/assets/img/logo/vex_large_splash.png')}}" style="max-height: 70px; height: 100%; width: auto">
                @endif
            </div>
            <div class="sidebar-profile-info text-center">
                <a href="javascript:void(0);" class="account-settings-link text-dark font-weight-600">
                    {{Auth::user()->userEmpresaFilial->empfil->filial_erp_id .' - '}} {{strlen(Auth::user()->userEmpresaFilial->empfil->nome) > 11 ? substr(Helper::formataString(Auth::user()->userEmpresaFilial->empfil->nome),0,11).'...' : Auth::user()->userEmpresaFilial->empfil->nome}}
                    <span>
                        {{Helper::insereMascara(Auth::user()->userEmpresaFilial->empfil->cnpj,'##.###.###/####-##')}}
                    </span>
                </a>
            </div>
        </div>

        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">

            <!-- dashboard -->
            <li class="no-padding">
                <a class="waves-effect waves-grey" href="{{url('/dashboard')}}">
                    <i class="material-icons">assessment</i>
                    Dashboard
                </a>
            </li>


            <!-- Cadastros -->
            @if(Permission::check('lista','Cliente','Central') or Permission::check('lista','Produto','Central'))
                <li class="no-padding">
                    <a class="collapsible-header waves-effect waves-grey">
                        <i class="material-icons">folder_open</i>
                        Cadastros
                        <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                    </a>
                    <div class="collapsible-body">
                        <ul>
                            @if(Permission::check('lista','Cliente','Central'))
                                <li>
                                    <a href="{{url('/clientes')}}">Clientes</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','CondicaoPagamento','Central'))
                                <li>
                                    <a href="{{url('/condicoes-pagamentos')}}">Condições de pagamento</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','Produto','Central'))
                                <li>
                                    <a href="{{url('/produtos')}}">Produtos</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            <!-- Pedidos -->
            @if(Permission::check('configuracao','PedidoVenda','Central') or Permission::check('lista','PedidoVenda','Central') or Permission::check('adiciona','PedidoVenda','Central'))
                <li class="no-padding">
                    <a class="collapsible-header waves-effect waves-grey">
                        <i class="material-icons">shopping_cart</i>
                        Pedidos de venda
                        <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                    </a>
                    <div class="collapsible-body">
                        <ul>
                            @if(Permission::check('lista','PedidoVenda','Central'))
                                <li>
                                    <a href="{{url('/pedidos-vendas')}}">Lista de pedidos</a>
                                </li>
                            @endif
                            @if(Permission::check('adiciona','PedidoVenda','Central'))
                                <li>
                                    <a href="{{url('/pedidos-vendas/add')}}">Novo pedido</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            <!-- Sistema -->
            @if(Permission::check('lista','Dispositivo','Central') or Permission::check('lista','Perfil','Central') or Permission::check('lista','User','Central'))
                <li class="no-padding">
                    <a class="collapsible-header waves-effect waves-grey">
                        <i class="material-icons">phonelink_setup</i>
                        Sistema
                        <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                    </a>
                    <div class="collapsible-body">
                        <ul>
                            @if(Permission::check('visualiza','Configuracao','Central'))
                                <li>
                                    <a href="{{url('/configuracoes')}}">Configurações</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','Dispositivo','Central'))
                                <li>
                                    <a href="{{url('/dispositivos')}}">Dispositivos</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','VexSync','Central'))
                                <li>
                                    <a href="{{url('/vex-sync/logs/sem-sucesso')}}">Logs de sincronização</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','Perfil','Central') or Permission::check('lista','User','Central'))
                                <li>
                                    <a href="{{url('/perfis')}}">Perfis de acesso</a>
                                </li>
                            @endif
                            @if(Permission::check('lista','User','Central'))
                                <li>
                                    <a href="{{url('/usuarios')}}">Usuários</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            <!-- chamados -->
            @if(Permission::check('lista','Chamado','Central'))
                <li class="no-padding">
                    <a class="waves-effect waves-grey" href="{{url('/suporte/chamados')}}">
                        <i class="material-icons">settings</i>
                        Suporte
                    </a>
                </li>
            @endif

        </ul>

        <div class="footer row">
            <p class="copyright">VEX Mobile ©</p>
            <a href="https://2mind.com.br" target="_blank">2Mind</a> &amp; <a href="http://sigaagis.com" target="_blank">Siga Agis</a>
        </div>
    </div>
</aside>