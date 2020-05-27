@extends('layouts.login')

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
                    <h3 class="title text-center">@lang('keywords.Login')</h3>
                    @if(session()->has('message') && session()->has('class'))
                        <div class="alert alert-{!! session('class') !!} alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {!! session('message') !!}
                        </div>
                    @endif
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">@lang('keywords.EmailAddress')</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                   required autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block">
                            <strong>{!!  $errors->first('email')  !!}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">@lang('keywords.Password')</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('keywords.RememberMe')
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                @lang('keywords.Login')
                            </button>
                            <a class="btn btn-link forgot" href="#nogo">
                                @lang('keywords.ForgotPassword')
                            </a>
                        </div>
                    </form>
                </div>
                <div class="col-md-6" style="display: none">
                    <h3 class="title text-center">@lang('keywords.register')</h3>
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname" class="control-label">@lang('keywords.firstname')</label>
                            <input id="firstname" type="text" class="form-control" name="firstname"
                                   value="{{ old('firstname') }}" required autofocus>
                            @if ($errors->has('firstname'))
                                <span class="help-block">
                            <strong>{{ $errors->first('firstname') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                            <label for="lastname" class="control-label">@lang('keywords.lastname')</label>
                            <input id="lastname" type="text" class="form-control" name="lastname"
                                   value="{{ old('lastname') }}" required autofocus>
                            @if ($errors->has('lastname'))
                                <span class="help-block">
                            <strong>{{ $errors->first('lastname') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="login_email" class="control-label">@lang('keywords.email')</label>
                            <input id="login_email" type="email" class="form-control" name="email"
                                   value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                                <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="login_password" class="control-label">@lang('keywords.password')</label>
                            <input id="login_password" type="password" class="form-control" name="password" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password-confirm"
                                   class="control-label">@lang('keywords.confirm_password')</label>
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>
                        {{--<div class="form-group">
                            <label class="control-label">@lang('keywords.territory')</label>
                            <select name="territory" class="form-control">
                                @foreach($territory as $item)
                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                @endforeach
                            </select>
                        </div>--}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                @lang('keywords.register')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('auth.passwords.email')
@endsection
@section('custom-js')
    <script type="text/javascript">
        $(".forgot").click(function () {
            $(".help-block").html('<strong</strong>');
            $("#reset-password-modal").modal("show");
        });
    </script>
@endsection
