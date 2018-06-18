<header id="header" class="fixed-top" role="banner">
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="/"><img src="/img/logo.svg" height="70px"></a>

        <button class="navbar-toggler my-2 my-sm-0" type="button" data-toggle="collapse" data-target="#menuTopo"
                aria-controls="menuTopo" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="menuTopo">
            <div class="navbar-nav float-right text-right pr-4">
                <a class="nav-item nav-link active" href="{{route('home')}}">@lang('includes.menu.home')<span class="sr-only">@lang('includes.menu.active')
                </span></a>
                <a class="nav-item nav-link" href="{{route('list')}}">@lang('includes.menu.list')</a>
                <a class="nav-item nav-link" href="{{route('warnings')}}">@lang('includes.menu.warnings')</a>
                <a class="nav-item nav-link" href="{{route('information')}}">@lang('includes.menu.information')</a>
                <a class="nav-item nav-link" href="{{route('stats')}}">@lang('includes.menu.stats')</a>
                <a class="nav-item nav-link" href="{{route('notifications')}}">@lang('includes.menu.notifications')</a>
                <a class="nav-item nav-link" href="{{route('partnerships')}}">@lang('includes.menu.partnerships')</a>
                <a class="nav-item nav-link" href="{{route('about')}}">@lang('includes.menu.about')</a>


                <a class="nav-item nav-link" href="https://twitter.com/fogospt" target="_blank">Twitter <i class="fab fa-twitter"></i></a>
                <a class="nav-item nav-link" href="https://facebook.com/fogospt" target="_blank">Facebook <i class="fab fa-facebook-square"></i></a>
                <a class="nav-item nav-link" href="https://play.google.com/store/apps/details?id=com.tomahock.fogos" target="_blank">Android <i class="fab fa-google-play"></i></a>
                <a class="nav-item nav-link" href="https://itunes.apple.com/us/app/fogos.pt/id1126944255" target="_blank">iPhone <i class="fab fa-app-store-ios"></i></a>

                {{--<a class="nav-item nav-link" href="{{route('manifest')}}">@lang('includes.menu.manifest')</a>--}}
                <a class="nav-item nav-link" href="https://aldeiasegura.pt" target="_blank">Aldeia Segura</a>
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    {{\App::getLocale()}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    @foreach(config('custom.availableLocales') as $item)
                        <a class="dropdown-item" href="{{route('changeLanguage', [$item])}}">{{$item}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>
</header>
