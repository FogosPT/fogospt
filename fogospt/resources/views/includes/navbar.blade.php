<header id="header" class="fixed-top" role="banner">
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="/"><img src="/img/logo.svg" height="32" alt="Fogos.pt"></a>

        <button class="navbar-toggler my-2 my-sm-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuTopo"
                aria-controls="menuTopo" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="menuTopo">
            <div class="navbar-nav menu-panel">
                <div class="menu-links">
                    <a class="nav-item nav-link @if(request()->routeIs('home')) active @endif" href="{{route('home', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('home')) aria-current="page" @endif><i class="fa-solid fa-house"></i> @lang('includes.menu.home')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('list')) active @endif" href="{{route('list', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('list')) aria-current="page" @endif><i class="fa-solid fa-list"></i> @lang('includes.menu.list')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('table')) active @endif" href="{{route('table', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('table')) aria-current="page" @endif><i class="fa-solid fa-table"></i> @lang('includes.menu.table')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('warnings')) active @endif" href="{{route('warnings', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('warnings')) aria-current="page" @endif><i class="fa-solid fa-triangle-exclamation"></i> @lang('includes.menu.warnings')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('warningsMadeira')) active @endif" href="{{route('warningsMadeira', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('warningsMadeira')) aria-current="page" @endif><i class="fa-solid fa-mountain"></i> @lang('includes.menu.warnings-madeira')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('mapConfigurator')) active @endif" href="{{route('mapConfigurator', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('mapConfigurator')) aria-current="page" @endif><i class="fa-solid fa-map-location-dot"></i> @lang('includes.menu.mapConfigurator')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('information')) active @endif" href="{{route('information', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('information')) aria-current="page" @endif><i class="fa-solid fa-circle-info"></i> @lang('includes.menu.information')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('stats')) active @endif" href="{{route('stats', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('stats')) aria-current="page" @endif><i class="fa-solid fa-chart-column"></i> @lang('includes.menu.stats')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('notifications')) active @endif" href="{{route('notifications', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('notifications')) aria-current="page" @endif><i class="fa-solid fa-bell"></i> @lang('includes.menu.notifications')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('partnerships')) active @endif" href="{{route('partnerships', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('partnerships')) aria-current="page" @endif><i class="fa-solid fa-handshake"></i> @lang('includes.menu.partnerships')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('api')) active @endif" href="{{route('api', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('api')) aria-current="page" @endif><i class="fa-solid fa-code"></i> @lang('includes.menu.api')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('api-termos')) active @endif" href="{{route('api-termos', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('api-termos')) aria-current="page" @endif><i class="fa-solid fa-file-contract"></i> @lang('includes.menu.api-termos')</a>
                    <a class="nav-item nav-link @if(request()->routeIs('about')) active @endif" href="{{route('about', ['locale' => \App::getLocale()])}}" @if(request()->routeIs('about')) aria-current="page" @endif><i class="fa-solid fa-circle-question"></i> @lang('includes.menu.about')</a>
                    <a class="nav-item nav-link js-open-consent" href="#"><i class="fa-solid fa-cookie-bite"></i> @lang('includes.menu.cookies')</a>
                    <a class="nav-item nav-link" href="https://vost.pt" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Vost.pt</a>
                </div>

                <hr class="menu-divider">

                <div class="menu-social">
                    <a class="menu-social-link" href="https://x.com/fogospt" target="_blank" rel="noopener" title="X (Twitter)" aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
                    <a class="menu-social-link" href="https://facebook.com/fogospt" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><i class="fa-brands fa-square-facebook"></i></a>
                    <a class="menu-social-link" href="https://play.google.com/store/apps/details?id=com.tomahock.fogos" target="_blank" rel="noopener" title="Android" aria-label="Android"><i class="fa-brands fa-google-play"></i></a>
                    <a class="menu-social-link" href="https://itunes.apple.com/us/app/fogos.pt/id1126944255" target="_blank" rel="noopener" title="iPhone" aria-label="iPhone"><i class="fa-brands fa-app-store-ios"></i></a>
                    <a class="menu-social-link" href="https://github.com/fogospt" target="_blank" rel="noopener" title="Github" aria-label="Github"><i class="fa-brands fa-square-github"></i></a>
                </div>

                <hr class="menu-divider">

                <div class="menu-lang">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        {{\App::getLocale()}}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach(config('custom.availableLocales') as $item)
                            <a class="dropdown-item" href="{{route('changeLanguage', ['locale' => \App::getLocale(), 'lang' => $item])}}">{{$item}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
