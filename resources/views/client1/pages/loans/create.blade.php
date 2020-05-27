@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.Apply for loan')
@stop

@section('contentHeader')

@stop
@section('body_class')
    fixed-content
@stop

@section('content')
    <div class="froggy-listing">
        <div class="froggy-listing__header"
             style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
            <div class="container">
                <h3>@lang('keywords.Apply for loan')</h3>
            </div>
        </div>
        <div class="froggy-listing__content">
            <div class="container">
                @include('client1.partials.loans_form')
            </div>
        </div>
    </div>
@stop

@section('contentFooter')
    <script>
        var has_active_loan = '{!! $has_active_loan !!}';
        var has_active_loan_error = '{!! $has_active_loan_error !!}';
    </script>
    <script src="{!! asset(mix('resources/js/client/loansCreate.js')) !!}"></script>
    <script>
        loansCreate.init();
    </script>
@stop