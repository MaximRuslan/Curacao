@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.My Loans')
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
                <div class="row">
                    <div class="col-md-8">
                        <h3>@lang('keywords.My Loans')</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="#nogo" class="btn btn-primary js--has-active-loan">
                            @lang('keywords.Apply for loan')
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="froggy-listing__content">
            <div class="container">
                <div class="froggy-table table-box mt-3">
                    <table id="datatable" width="100%">
                        <thead>
                        <tr>
                            <th>@lang('keywords.Loan ID')</th>
                            <th style="width: 10%;">@lang('keywords.Loan reason')</th>
                            <th>@lang('keywords.Loan reason')</th>
                            <th>@lang('keywords.Loan reason')</th>
                            <th style="width: 10%;">@lang('keywords.Amount')</th>
                            <th style="width: 10%;">@lang('keywords.Loan type')</th>
                            <th>@lang('keywords.Loan type')</th>
                            <th>@lang('keywords.Loan type')</th>
                            <th style="width: 10%;">@lang('keywords.Status')</th>
                            <th>@lang('keywords.Status')</th>
                            <th>@lang('keywords.Status')</th>
                            <th style="width: 10%;">@lang('keywords.Decline reason')</th>
                            <th>@lang('keywords.Decline reason')</th>
                            <th>@lang('keywords.Decline reason')</th>
                            <th>@lang('keywords.Decline reason')</th>
                            <th>@lang('keywords.Decline reason')</th>
                            <th>@lang('keywords.Decline reason')</th>
                            <th style="width: 10%;">@lang('keywords.Requested Date')</th>
                            <th style="width: 10%;">@lang('keywords.Start Date')</th>
                            <th style="width: 10%;">@lang('keywords.Completed Date')</th>
                            <th style="width: 20%;">@lang('keywords.Action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('client1.popups.loan_application_modal')
    @include('client1.popups.deleteConfirm',['modal_name'=>'Loan'])
@stop

@section('contentFooter')
    <script>
        var has_active_loan = false;
    </script>
    <script src="{!! asset(mix('resources/js/client/loansIndex.js')) !!}"></script>
    <script src="{!! asset(mix('resources/js/client/loansCreate.js')) !!}"></script>
    <script>
        var keywords = {!! json_encode(Lang::get('keywords')) !!};
        loansIndex.init();
        loansCreate.init();
    </script>
@stop
