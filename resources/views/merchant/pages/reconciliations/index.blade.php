@extends('merchant.layouts.master')

@section('page_name')
    @lang('keywords.reconciliations')
@stop

@section('header')
@stop

@section('action_buttons')
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="indexDatatable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('keywords.transaction_id')</th>
                        <th>@lang('keywords.branch')</th>
                        <th>@lang('keywords.amount')</th>
                        <th>@lang('keywords.Status')</th>
                        <th>@lang('keywords.Date')</th>
                        <th>@lang('keywords.Action')</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> <!-- end row -->
@stop

@section('popups')
    @include('merchant.popups.reconciliation-approve')
    @include('merchant.popups.reconciliation-history')
@stop

@section('footer')
    <script>
        var merchant = '{!! \App\Library\Helper::authMerchantUser()->type==1 !!}';
    </script>
    <script src="{!! asset(mix('resources/js/merchant/reconciliations.js')) !!}"></script>
    <script>
        reconciliations.init();
    </script>
@stop