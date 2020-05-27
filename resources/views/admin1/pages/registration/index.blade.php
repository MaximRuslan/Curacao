@extends('admin1.layouts.login')
@section('page_name')
    Register
@stop
@section('container_css')
    display: table-cell !important;
@stop
@section('extra_image')
    <img src="{{asset('uploads/'.$country->logo)}}" height="100"/>
@stop
@section('container_content')
    @if(request('langSelector')!=null && (request('langSelector')==false || request('langSelector')=='false'))

    @else
        <div class="row-block">
            <div class="form-group mt-2 margin-none">
                {!!Form::select('languageCode', config('site.language'),request('languageCode'), ['class'=>'form-control select2single','required','id'=>'lang_select'])!!}
            </div>
        </div>
    @endif
@stop
@section('contentHeader')
    <style>
        .row-block {
            display: flex;
            justify-content: flex-end;
            padding: 0 8px;
        }
    </style>
@stop
@section('content')
    <section class="section" id="login">
        {!! Form::open(['route'=>'registration.store','files'=>true,'autocomplete'=>'off','class'=>'form-horizontal']) !!}
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @include('common.success')
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6 col-md-offset-3">
                    <h3 class="title text-center">@lang('keywords.registration')</h3>
                    @if(session()->has('message') && session()->has('class'))
                        <div class="alert alert-{!! session('class') !!} alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {!! session('message') !!}
                        </div>
                    @endif

                    {!! Form::hidden('languageCode',old('languageCode')) !!}
                    {!! Form::hidden('countryCode',old('countryCode')) !!}
                    <div class="mt-4 form-group{{ $errors->has('how_much_loan') ? ' has-danger' : '' }}">
                        <label for="how_much_loan" class="control-label">@lang('keywords.how_much_loan')</label>
                        <input id="how_much_loan" autocomplete="off" type="number" class="form-control" min="0" name="how_much_loan" value="{{ old('how_much_loan') }}" autofocus>
                        @if ($errors->has('how_much_loan'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('how_much_loan')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('repay_loan_2_weeks') ? ' has-danger' : '' }}">
                        <label for="repay_loan_2_weeks" class="control-label">@lang('keywords.repay_loan_2_weeks')</label>
                        {!! Form::select('repay_loan_2_weeks',$options,old('repay_loan_2_weeks'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                        @if ($errors->has('repay_loan_2_weeks'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('repay_loan_2_weeks')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                        <label for="firstname" class="control-label">@lang('keywords.Firstname')</label>
                        <input id="firstname" autocomplete="off" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}">
                        @if ($errors->has('firstname'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('firstname')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('lastname') ? ' has-danger' : '' }}">
                        <label for="lastname" class="control-label">@lang('keywords.Lastname')</label>
                        <input id="lastname" autocomplete="off" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                        @if ($errors->has('lastname'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('lastname')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('id_number') ? ' has-danger' : '' }}">
                        <label for="id_number" class="control-label">@lang('keywords.id_number')</label>
                        <input id="id_number" autocomplete="off" type="text" class="form-control" name="id_number" value="{{ old('id_number') }}">
                        @if ($errors->has('id_number'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('id_number')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email" class="control-label">@lang('keywords.EmailAddress')</label>
                        <input id="email" autocomplete="off" type="email" class="form-control" name="email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('email')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('telephone') ? ' has-danger' : '' }}">
                        <label for="telephone" class="control-label">@lang('keywords.telephone')</label>
                        <input id="telephone" autocomplete="off" type="text" class="form-control" name="telephone" value="{{ old('telephone') }}">
                        @if ($errors->has('telephone'))
                            <span class="help-block error">
                                <strong>{!!  $errors->first('telephone')  !!}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('have_bank_loan') ? ' has-danger' : '' }}">
                        <label for="have_bank_loan" class="control-label">@lang('keywords.have_bank_loan')</label>
                        {!! Form::select('have_bank_loan',$options,old('have_bank_loan'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                        @if ($errors->has('have_bank_loan'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('have_bank_loan')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('have_bank_account') ? ' has-danger' : '' }}">
                        <label for="have_bank_account" class="control-label">@lang('keywords.have_bank_account')</label>
                        {!! Form::select('have_bank_account',$options,old('have_bank_account'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                        @if ($errors->has('have_bank_account'))
                            <span class="help-block error">
                                    <strong>{!!  $errors->first('have_bank_account')  !!}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('payslip1') ? ' has-danger' : '' }}">
                        <label for="how_much_loan" class="control-label" data-toggle='tooltip' title='png,gif,jpg,jpeg,doc,docx,pdf'>@lang('keywords.upload_recent_payslip')</label>
                        {!! Form::file('payslip1',['class'=>'form-control','data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf','accept'=>'image/x-png,image/gif,image/jpeg,.doc,.docx,.pdf']) !!}
                        @if ($errors->has('payslip1'))
                            <span class="help-block error">
                                    <strong>{{ $errors->first('payslip1') }}</strong>
                                </span>
                        @endif
                    </div>
                    @if($country->referral==1)
                        <div class="form-group{{ $errors->has('referred_by') ? ' has-danger' : '' }}">
                            <label for="referral_code" class="control-label">@lang('keywords.referral_code')</label>
                            <input id="referral_code" autocomplete="off" type="text" class="form-control" name="referred_by" value="{{ old('referred_by') }}">
                            @if ($errors->has('referred_by'))
                                <span class="help-block error">
                                <strong>{!!  $errors->first('referred_by')  !!}</strong>
                            </span>
                            @endif
                        </div>
                    @endif
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            @lang('keywords.apply_now')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
@endsection
@section('contentFooter')
    <script>
        $('[data-toggle="tooltip"]').tooltip();
        $('.select2single').select2();
        $(document).on('change', '#lang_select', function (e) {
            e.preventDefault();
            location.href = "{!!route('registration')!!}?countryCode={!!request('countryCode')!!}&languageCode=" + $(this).val();
            // $.ajax({
            //     dataType: 'json',
            //     method: 'get',
            //     url: '{!! url('/') !!}/' + 'lang-change/' + $(this).val(),
            //     data: $(this).serialize(),
            //     success: function (data) {

            //     }
            // });
        });
        $('[name="payslip1"]').inputFileText({
            text: keywords.select_file
        });
    </script>
@endsection
