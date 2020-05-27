@extends('merchant.layouts.master')

@section('page_name')
    @lang('keywords.dashboard')
@stop

@section('header')
@stop

@section('content')
    @if(\App\Library\Helper::authMerchantUser()->type==1)
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-3">
                <div class="widget-bg-color-icon card-box">
                    <div class="bg-icon bg-info pull-left">
                        <i class="md md-attach-money text-white"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><span class="counter" id="js--total-loans-count">{!! $total_balance !!}</span></h3>
                        <p class="text-muted mb-0">@lang('keywords.total_balance')</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-12 col-xl-3">
                <div class="widget-bg-color-icon card-box">
                    <div class="bg-icon bg-info pull-left">
                        <i class="fa fa-handshake-o text-white"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><span class="counter" id="js--total-loans-amount">{!! $total_commission !!}</span></h3>
                        <p class="text-muted mb-0">@lang('keywords.total_commission')</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-12 col-xl-3">
                <div class="widget-bg-color-icon card-box">
                    <div class="bg-icon bg-info pull-left">
                        <i class="fa fa-handshake-o text-white"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><span class="counter" id="js--total-loans-amount">{!! $reconciled !!}</span></h3>
                        <p class="text-muted mb-0">@lang('keywords.reconciled')</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-12 col-xl-3">
                <div class="widget-bg-color-icon card-box">
                    <div class="bg-icon bg-info pull-left">
                        <i class="fa fa-money text-white"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><span class="counter" id="js--total-loans-amount-collected">{!! $account_payable !!}</span></h3>
                        <p class="text-muted mb-0">@lang('keywords.account_payable')</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('footer')
@stop