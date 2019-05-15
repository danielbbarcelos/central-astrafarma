<header class="mn-header navbar-fixed">
    <nav class="cyan darken-1">
        <div class="nav-wrapper row">
            <section class="material-design-hamburger navigation-toggle">
                <a href="#" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                    <span class="material-design-hamburger__layer"></span>
                </a>
            </section>
            <div class="header-title col s3">
                <img src="{{url('/assets/img/logo/logo_vex_horizontal_cinza.png')}}" width="100">
            </div>

            <ul class="right col s9 m3 nav-right-menu">
                <li>
                    <a href="javascript:void(0)" data-activates="dropdown1" class="dropdown-button dropdown-right show-on-large">
                        <img src="{{url('/assets/img/icons/user.png')}}">
                        <span class="">Olá, {{Auth::user()->name}}</span>
                    </a>
                </li>
            </ul>

            <ul id="dropdown1" class="dropdown-content notifications-dropdown">
                <li class="notificatoins-dropdown-container">
                    <a href="{{url('/senha')}}">
                        <div class="notification">
                            <div class="notification-icon circle cyan">
                                <i class="material-icons">lock_outline</i>
                            </div>
                            <div class="notification-text"><p><b>Alteração de senha</b></p><span>Altere sua senha de acesso</span></div>
                        </div>
                    </a>
                </li>
                <li class="notificatoins-dropdown-container">
                    <a href="{{url('/logout')}}">
                        <div class="notification">
                            <div class="notification-icon circle cyan">
                                <i class="material-icons">exit_to_app</i>
                            </div>
                            <div class="notification-text"><p><b>Sair</b></p><span>Encerrar sessão</span></div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>