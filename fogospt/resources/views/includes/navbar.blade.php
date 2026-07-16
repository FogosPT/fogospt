<header id="header" class="fixed-top" role="banner">
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="/"><img src="/img/logo.svg" height="32px"></a>

        <button class="navbar-toggler my-2 my-sm-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuTopo"
                aria-controls="menuTopo" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="menuTopo">
            <div class="navbar-nav menu-panel">
                <div class="menu-links">
                    <a class="nav-item nav-link active" href="{{route('home', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-house"></i> @lang('includes.menu.home')<span class="visually-hidden">@lang('includes.menu.active')
                    </span></a>
                    <a class="nav-item nav-link" href="{{route('list', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-list"></i> @lang('includes.menu.list')</a>
                    <a class="nav-item nav-link" href="{{route('table', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-table"></i> @lang('includes.menu.table')</a>
                    <a class="nav-item nav-link" href="{{route('warnings', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-triangle-exclamation"></i> @lang('includes.menu.warnings')</a>
                    <a class="nav-item nav-link" href="{{route('warningsMadeira', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-mountain"></i> @lang('includes.menu.warnings-madeira')</a>
                    <a class="nav-item nav-link" href="{{route('information', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-circle-info"></i> @lang('includes.menu.information')</a>
                    <a class="nav-item nav-link" href="{{route('stats', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-chart-column"></i> @lang('includes.menu.stats')</a>
                    <a class="nav-item nav-link" href="{{route('notifications', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-bell"></i> @lang('includes.menu.notifications')</a>
                    <a class="nav-item nav-link" href="{{route('partnerships', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-handshake"></i> @lang('includes.menu.partnerships')</a>
                    <a class="nav-item nav-link" href="{{route('api', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-code"></i> @lang('includes.menu.api')</a>
                    <a class="nav-item nav-link" href="{{route('api-termos', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-file-contract"></i> @lang('includes.menu.api-termos')</a>
                    <a class="nav-item nav-link" href="{{route('about', ['locale' => \App::getLocale()])}}"><i class="fa-solid fa-circle-question"></i> @lang('includes.menu.about')</a>
                    <a class="nav-item nav-link" href="https://vost.pt" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Vost.pt</a>
                </div>

                <hr class="menu-divider">

                <div class="menu-social">
                    <a class="menu-social-link" href="https://x.com/fogospt" target="_blank" title="X (Twitter)" aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
                    <a class="menu-social-link" href="https://facebook.com/fogospt" target="_blank" title="Facebook" aria-label="Facebook"><i class="fa-brands fa-square-facebook"></i></a>
                    <a class="menu-social-link" href="https://play.google.com/store/apps/details?id=com.tomahock.fogos" target="_blank" title="Android" aria-label="Android"><i class="fa-brands fa-google-play"></i></a>
                    <a class="menu-social-link" href="https://itunes.apple.com/us/app/fogos.pt/id1126944255" target="_blank" title="iPhone" aria-label="iPhone"><i class="fa-brands fa-app-store-ios"></i></a>
                    <a class="menu-social-link" href="https://github.com/fogospt" target="_blank" title="Github" aria-label="Github"><i class="fa-brands fa-square-github"></i></a>
                </div>

                <hr class="menu-divider">

                <div class="menu-lang">
                    {{--<a class="nav-item nav-link" href="{{route('manifest')}}">@lang('includes.menu.manifest')</a>--}}
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
