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
                <a class="nav-item nav-link" href="{{route('about')}}">@lang('includes.menu.about')</a>
                <a class="nav-item nav-link" href="{{route('information')}}">@lang('includes.menu.information')</a>
                <a class="nav-item nav-link" href="{{route('partnerships')}}">@lang('includes.menu.partnerships')</a>
                <a class="nav-item nav-link" href="{{route('manifest')}}">@lang('includes.menu.manifest')</a>
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
