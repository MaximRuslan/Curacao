<nav class="navbar navbar-custom navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand logo" href="{{ route('client.dashboard') }}">
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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.dashboard') }}">@lang('keywords.home')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{!! route('client.loans.create') !!}">@lang('keywords.Apply for loan')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('client.loans') !!}">@lang('keywords.My Loans')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.credits.index') }}">@lang('keywords.My wallet')</a>
                    </li>
                    <li class="nav-item dropdown notification-list" style="margin-top: -13px;">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:;" role="button"
                           aria-haspopup="false" aria-expanded="false">
                            @if(auth()->user()->profile_pic)
                                <img width="50px;" height="50px;"
                                     src="{{asset('uploads/'.auth()->user()->profile_pic)}}" alt="user"
                                     class="rounded-circle">
                            @else
                                <i style="margin-top: -10px;" class="user-profile-icon fa fa-user-circle fa-3x "></i>
                            @endif
                            {{auth()->user()->firstname.' '.auth()->user()->lastname}}
                        </a>
                        <div class="dropdown-menu dropdown-content dropdown-menu-right profile-dropdown "
                             style="width: 230px;"
                             aria-labelledby="Preview">
                            {{--<div class="dropdown-item noti-title" style="margin-left: -12px;">--}}
                            {{--<h5 class="text-overflow">--}}
                            <a href="#nogo" class="nav-link">
                                {{--<small style="font-size: 65% !important;">--}}
                                <span style="font-size: 90%;">@lang('keywords.welcome') {{auth()->user()->firstname.' '.auth()->user()->lastname}}</span>
                                {{--</small>--}}
                            </a>
                            {{--</h5>--}}
                            {{--</div>--}}
                            <a href="#profileModal" class="nav-link" data-toggle="modal"
                               class="dropdown-item notify-item">
                                @lang('keywords.profile')</a>
                            <a href="#changePasswordModal" class="nav-link" data-toggle="modal"
                               class="dropdown-item notify-item">
                                @lang('keywords.change password')
                            </a>

                            <a href="{!! url('terms-conditions') !!}" class="nav-link"
                               class="dropdown-item notify-item" target="_blank">
                                @lang('keywords.Legal Information')
                            </a>
                            <a href="{!! url('logout') !!}" class="nav-link" class="dropdown-item notify-item">
                                {{--onclick="event.preventDefault(); document.getElementById('logout-form').submit();"--}}

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
