@extends('client1.layouts.master')

@section('pageTitle')
    Login
@stop

@section('contentHeader')

@stop

@section('body_class')
    fixed-login
@stop

@section('content')
    <div class="froggy-wrap">
        <div class="container-fluid">
            <div class="row froggy-box">
                <div class="col-md-7 col-sm-6 froggy-wallet"
                     style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
                    <div class="froggy-wallet__header">
                        <h2>{!! config('app.name') !!}</h2>
                        <p>
                            Bienvenido - Welcome - Bon bini
                        </p>
                    </div>
                    <div class="froggy-wallet__cover">
                        <img src="{!! asset('resources/img/client/credit-cover.png') !!}"/>
                    </div>
                </div>
                <div class="col-md-5 col-sm-6 froggy-signup">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="froggy-signup__form">
                        <h2>Sign In</h2>
                        <p>Please enter your email address and password</p>
                        {!!Form::open(['route'=>'client1.login.store'])!!}
                        <ul class="ul-reset form-box">
                            <li>
                                {!!Form::email('email', old('email'), ['placeholder'=>'Email Address'])!!}
                                @if($errors->has('email'))
                                    <p class="help-block">{!!$errors->first('email')!!}</p>
                                @endif
                            </li>
                            <li>
                                {!!Form::password('password', ['placeholder'=>'Password'])!!}
                                @if($errors->has('password'))
                                    <p class="help-block">{!!$errors->first('password')!!}</p>
                                @endif
                            </li>
                        </ul>
                        <div class="form-action">
                            <button class="btn button" type="submit">Sign-in</button>
                            <a href="javascript:;" class="forgot">Forgot Password? </a>
                        </div>
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('client1.popups.forgot_password')
@stop

@section('contentFooter')
    <script type="text/javascript">
        $(".forgot").click(function () {
            $(".help-block").html('<strong</strong>');
            $("#reset-password-modal").modal("show");
        });
    </script>
@stop