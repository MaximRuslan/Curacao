<header>
    <div class="container">
        <div class="row header-wrap">
            <div class="col-md-1 col-xs-4">
                <a class="logo" href="javascript:;"><img src="{!! asset(config('site.logo')) !!}"/></a>
            </div>
            <div class="col-md-11 col-xs-8">
                @if(auth()->check())
                    <a class="nav-toggle" href="javascript:;"><i class="material-icons">menu</i></a>
                    <div class="nav justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client1.home') }}">@lang('keywords.home')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js--has-active-loan" href="#nogo">
                                @lang('keywords.Buy Miles')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('client1.loans.index') !!}">
                                @lang('keywords.My Bundles')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client1.credits.index') }}">
                                @lang('keywords.My wallet')
                            </a>
                        </li>
                        @if($country->referral==1)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client1.referrals.index') }}">
                                    @lang('keywords.my_referrals')
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <div class="form-group mt-3" style="width:100%;">
                                {!!Form::select('lang', config('site.language'),auth()->user()->lang, ['class'=>'form-control','required','id'=>'lang_select'])!!}
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link  dropdown-toggle" href="javascript:;" data-toggle="dropdown"
                               role="button" aria-haspopup="true" aria-expanded="false">
                                {{auth()->user()->firstname.' '.auth()->user()->lastname}}
                                <div class="profile-icon">
                                    @if(auth()->user()->profile_pic)
                                        <img src="{{asset('uploads/'.auth()->user()->profile_pic)}}">
                                    @else
                                        {{--<i style="margin-top: -10px;"
                                           class="user-profile-icon fa fa-user-circle fa-3x "></i>--}}
                                        <img src="{!! asset('resources/img/client/user-avtar.png') !!}"/>
                                    @endif
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item profileOpen" href="#">@lang('keywords.profile')</a>
                                <a class="dropdown-item changePasswordOpen" href="#">
                                    @lang('keywords.change password')
                                </a>
                                <a class="dropdown-item" href="{!! url('terms-conditions') !!}" target="_blank">
                                    @lang('keywords.Legal Information')
                                </a>
                                <a class="dropdown-item" href="{!! url('logout') !!}">@lang('keywords.logout')</a>
                            </div>
                        </li>
                    </div>
                @else
                    {{--<li class="nav-item">
                        <a class="nav-link" href="{!! url()->route('client1.login') !!}">Sign Up</a>
                    </li>--}}
                @endif
            </div>
        </div>
    </div>
</header>