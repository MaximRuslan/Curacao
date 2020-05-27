@extends('merchant.layouts.master')

@section('page_name')
    @lang('keywords.payments')
@stop

@section('header')
@stop

@section('action_buttons')
    <button type="button" class="btn btn-default waves-effect waves-light js--add-payment">@lang('keywords.add')</button>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="indexDatatable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('keywords.Client')</th>
                        <th>@lang('keywords.Client')</th>
                        <th>@lang('keywords.branch')</th>
                        <th>@lang('keywords.amount')</th>
                        <th>@lang('keywords.Date')</th>
                        <th>@lang('keywords.received_by')</th>
                        <th>@lang('keywords.received_by')</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> <!-- end row -->
@stop

@section('popups')
    @include('merchant.popups.payment-add')
@stop

@section('footer')
    <script>
        var merchant = '{!! \App\Library\Helper::authMerchantUser()->type==1 !!}';
    </script>
    <script src="{!! asset(mix('resources/js/merchant/payments.js')) !!}"></script>
    <script>
        payments.init();
    </script>
@stop