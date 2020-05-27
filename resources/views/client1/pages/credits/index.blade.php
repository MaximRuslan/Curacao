@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.My wallet')
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
                <h3>@lang('keywords.My wallet')</h3>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="my-wallet">
                            <label for="">@lang('keywords.Ledger') @lang('keywords.Balance')</label>: <span class="total_amount">{!! $wallet !!}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="my-wallet">
                            <label for="">@lang('keywords.Available') @lang('keywords.Balance')</label>
                            : <span class="available_amount">{!! number_format($available_balance,2) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="froggy-listing__content">
            <div class="container">
                <div style="display:flex;">
                    @if($wallet > 0)
                        <div class="dropdown" style="margin-left:auto;">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                @lang('keywords.Use Credit')
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item creditAddModalOpen" href="#" data-payment-type="1"
                                       data-title="@lang('keywords.Cash') @lang('keywords.Payout') @lang('keywords.Request')">
                                        @lang('keywords.Cash') @lang('keywords.Payout')
                                    </a>
                                </li>
                                {{--<li>
                                    <a class="dropdown-item creditAddModalOpen" href="#" data-toggle="tooltip"
                                       data-title="@lang('keywords.BankTransfer')" data-payment-type="2">
                                        @lang('keywords.Deposit on bank account')
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="modal" class="dropdown-item" href="#merchantModal"
                                       data-toggle="tooltip"
                                       data-title="@lang('keywords.Cash') @lang('keywords.Payout') @lang('keywords.Request')">
                                        @lang('keywords.Payment') @lang('keywords.Merchant')
                                    </a>
                                </li>--}}
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="mt-5">
                    <div class="froggy-table table-box mt-3">
                        <table id="datatable">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th style="width: 10%;">@lang('keywords.PaymentType')</th>
                                <th style="width: 10%;">@lang('keywords.Amount')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.TransactionCharge')</th>
                                <th style="width: 10%;">@lang('keywords.Info')</th>
                                <th style="width: 10%;">@lang('keywords.Notes')</th>
                                <th style="width: 10%;">@lang('keywords.Status')</th>
                                <th style="width: 10%;">@lang('keywords.Date')</th>
                                <th style="width: 10%;">@lang('keywords.Action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('client1.popups.deleteConfirm',['modal_name'=>'Credit'])
    @include('client1.popups.credit_merchant_payment')
    @include('client1.popups.credit_create')
@stop

@section('contentFooter')
    <script src="{!! asset(mix('resources/js/client/credit.js')) !!}"></script>
    <script>
        credit.init();
    </script>
@stop
