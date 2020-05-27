@extends('admin1.layouts.login')
@section('page_name')
    Reset
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
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <h3 class="title">@lang('keywords.ResetPassword')</h3>
                <form class="form-horizontal" method="POST" action="{{ route('password.request') }}" id="resetPassword">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">@lang('keywords.EmailAddress')</label>

                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">@lang('keywords.Password')</label>

                        <div class="col-md-12">
                            <input id="password" type="password" class="form-control" name="password" required>
                            <span class="help-block pwd_error">
                            </span>
                            <br>
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label for="password-confirm" class="col-md-4 control-label">@lang('keywords.ConfirmPassword')</label>
                        <div class="col-md-12">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                            @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button  class="btn btn-primary resetPassword">
                                @lang('keywords.ResetPassword')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
</section>
@endsection

@section('custom-js')
<script type="text/javascript">
  $(".resetPassword").click(function(e){
    e.preventDefault();
    $(".pwd_error").html("");
    var reg = new RegExp(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/);
    var password = $('#password').val();
    var error = 1;
    if (!reg.test(password)) {
      error = 0;
      $(".pwd_error").html("<strong>Passwords should not be less than 8 characters including uppercase, lowercase, at least one number and special character.</strong>");
    }
    console.log(error);
    if(error == 1){
      $("#resetPassword").submit();
    }
  })
</script>
@endsection
