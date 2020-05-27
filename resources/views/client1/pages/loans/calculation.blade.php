@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.LoanHistory')
@stop

@section('contentHeader')

@stop
@section('body_class')
    fixed-content
@stop

@section('content')
    <div class="froggy-listing">
        <div class="froggy-listing__header" style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h3>@lang('keywords.LoanHistory')</h3>
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
                            <th>@lang('keywords.Week')</th>
                            <th>@lang('keywords.Date')</th>
                            <th>@lang('keywords.Transaction')</th>
                            <th>@lang('keywords.Amount For Trans')</th>
                            <th>@lang('keywords.Principal')</th>
                            <th>@lang('keywords.Origination Fee')</th>
                            <th>@lang('keywords.Interest')</th>
                            <th>@lang('keywords.Renewal fee')</th>
                            <th>@lang('keywords.Taxes')</th>
                            <th>@lang('keywords.Debt collection fee')</th>
                            <th>@lang('keywords.Debt collection tax')</th>
                            <th>@lang('keywords.Administration fee')</th>
                            <th>@lang('keywords.Total Balance')</th>
                            <th>@lang('keywords.Action')</th>
                        </tr>
                        </thead>
                        <tbody class="loan_history_table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('contentFooter')
    <script src="{!! asset(mix('resources/js/client/loanCalculation.js')) !!}"></script>
    @if(env('RECEIPT_ON')==true)
        <script>
            window.receipt_on = true;
        </script>
    @else
        <script>
            window.receipt_on = false;
        </script>
    @endif
    <script>
        window.loan_id = '{!! $loan->id !!}';
        loanCalculation.init();
    </script>
@stop
