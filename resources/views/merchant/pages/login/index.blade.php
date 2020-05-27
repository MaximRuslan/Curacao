@extends('merchant.layouts.login')
@section('page_name')
    @lang('keywords.login')
@stop

@section('header')
@stop

@section('content')
    <section class="section" id="login">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @include('common.success')
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title text-center">@lang('keywords.merchant_login')</h3>
                    @if(session()->has('message') && session()->has('class'))
                        <div class="alert alert-{!! session('class') !!} alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {!! session('message') !!}
                        </div>
                    @endif
                    {!! Form::open(['route'=>'merchant.login.store','class'=>'form-horizontal','id'=>'js--login-form']) !!}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="control-label">@lang('keywords.EmailAddress')</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        <span class="help-block error" for="email">
                            @if ($errors->has('email'))
                                {!!  $errors->first('email')  !!}
                            @endif
                        </span>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="control-label">@lang('keywords.Password')</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                        <span class="help-block error" for="password">
                            @if ($errors->has('password'))
                                {!!  $errors->first('password')  !!}
                            @endif
                        </span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            @lang('keywords.Login')
                        </button>
                        <a class="btn btn-link forgot" href="#nogo">
                            @lang('keywords.ForgotPassword')
                        </a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
    @include('merchant.popups.emails')
@stop

@section('footer')
    <script src="{!! asset(mix('resources/js/merchant/login.js')) !!}"></script>
@stop