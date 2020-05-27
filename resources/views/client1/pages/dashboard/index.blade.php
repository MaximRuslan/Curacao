@extends('client1.layouts.master')

@section('pageTitle')
    Dashboard
@stop

@section('contentHeader')

@stop

@section('body_class')
    fixed-content
@stop


@section('content')
    <div class="froggy-cover" style="background-image:url({!! asset('resources/img/client/froggy-cover.jpg') !!})">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 col-sm-7">
                    <div class="froggy-cover__wrap">
                        <h2 class="froggy-cover__title">@lang('keywords.client_dashboard_header')</h2>
                        <p class="froggy-cover__text">@lang('keywords.client_dashboard_info')</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-5">
                    <div class="froggy-cover__image"><img src="{!! asset('resources/img/client/credit-cover.png') !!}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="froggy-functionality">
        <div class="container">
            <ul class="ul-reset functionality">
                <li>
                    <a href="#nogo" class="functionality-box js--has-active-loan">
                        <div class="functionality-box__img">
                            <img src="{!! asset('resources/img/client/icon-loan.png') !!}" style="width:40px;"/>
                        </div>
                        <label>@lang('keywords.Buy Miles')</label>
                    </a>
                </li>
                <li>
                    <a href="{!! route('client1.loans.index') !!}" class="functionality-box">
                        <div class="functionality-box__img">
                            <img src="{!! asset('resources/img/client/icon-security.png') !!}"/>
                        </div>
                        <label>@lang('keywords.My Loans')</label>
                    </a>
                </li>
                <li>
                    <a href="{!! route('client1.credits.index') !!}" class="functionality-box">
                        <div class="functionality-box__img">
                            <img src="{!! asset('resources/img/client/icon-coins.png') !!}"/>
                        </div>
                        <label>@lang('keywords.My wallet')</label>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="functionality-box">
                        <div class="functionality-box__img">
                            <img src="{!! asset('resources/img/client/icon-statement.png') !!}"/>
                        </div>
                        <label>@lang('keywords.My statements')</label>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="functionality-box profileOpen">
                        <div class="functionality-box__img">
                            <img src="{!! asset('resources/img/client/profile.png') !!}" style="width:30px;"/>
                        </div>
                        <label>@lang('keywords.My profile')</label>
                    </a>
                </li>
                @if($country->referral==1)
                    <li>
                        <a href="{!! route('client1.referrals.index') !!}" class="functionality-box">
                            <div class="functionality-box__img" style="width: auto;">
                                <img src="{!! asset('resources/img/client/referrals.png') !!}" style="height:35px; width: auto;"/>
                            </div>
                            <label>@lang('keywords.my_referrals')</label>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@stop

@section('contentFooter')

@stop