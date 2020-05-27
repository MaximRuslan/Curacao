@extends('admin1.layouts.login')
@section('page_name')
    Login
@stop
@section('contentHeader')
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
                    <h3 class="title text-center">@lang('keywords.Login')</h3>
                    @if(session()->has('message') && session()->has('class'))
                        <div class="alert alert-{!! session('class') !!} alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {!! session('message') !!}
                        </div>
                    @endif
                    <form class="form-horizontal" method="POST" action="{{ route('login.store') }}">
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
                            <button type="submit" class="btn btn-primary">
                                @lang('keywords.Login')
                            </button>
                            <a class="btn btn-link forgot" href="#nogo">
                                @lang('keywords.ForgotPassword')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('admin1.popups.emails')
@endsection
@section('contentFooter')
    <script type="text/javascript">
        $(".forgot").click(function () {
            $(".help-block").html('<strong</strong>');
            $("#reset-password-modal").modal("show");
        });
    </script>
@endsection
