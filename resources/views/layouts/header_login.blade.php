<nav class="navbar navbar-custom navbar-expand-lg navbar-light">
    <div class="container" style="text-align:center;">
        <a class="navbar-brand logo col-md-6" href="{{ route('client1.home') }}">
            <img src="{{asset(config('site.logo'))}}" height="60"/>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsMenu"
                aria-controls="navbarsMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsMenu">
            <ul class="navbar-nav ml-auto">
                {{-- <li class="nav-item active">
                    <a class="nav-link" href="{{ route('client.dashboard') }}">@lang('keywords.home')</a>
                </li> --}}
                @if (Auth::guest())
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">@lang('keywords.login_register')</a>
                    </li> --}}
                @else
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ route('client1.home') }}">@lang('keywords.home')</a>
                    </li>
                    <li class="nav-item dropdown notification-list">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:;" role="button"
                           aria-haspopup="false" aria-expanded="false">
                            @lang('keywords.welcome') {{auth()->user()->firstname.' '.auth()->user()->lastname}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                            <a href="#profileModal" class="nav-link" data-toggle="modal"
                               class="dropdown-item notify-item">
                                @lang('keywords.profile')</a>
                            <a href="#changePasswordModal" class="nav-link" data-toggle="modal"
                               class="dropdown-item notify-item">
                                @lang('keywords.change password')</a>

                            <a href="#" class="nav-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="dropdown-item notify-item">
                                @lang('keywords.logout')</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                @endif
                {{-- @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <li>
                        <a rel="alternate" class="nav-link" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>
                    </li>
                    @endforeach --}}
            </ul>

        </div>
    </div>
</nav>
