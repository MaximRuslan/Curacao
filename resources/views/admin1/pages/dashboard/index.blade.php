@extends('admin1.layouts.master')
@section('page_name')
    Dashboard
@stop
@section('contentHeader')
    <style>
        .col-xl-2-5 {
            max-width: 20%;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="page-title">Dashboard</h4>
                <div class="mb-2 mt-4">
                    <span style="font-weight:600;">Total clients:</span> <span id="total_client"></span>
                </div>
                <div class="mb-4">
                    <span style="font-weight:600;">Total credit in wallet:</span> <span id="total_credit"></span>
                </div>

                {{-- <div class="mt-2">
                     @if(auth()->user()->hasRole('super admin|admin'))
                         <div class="row">
                             <div class="col-md-3">
                                 <div class="form-group">
                                     {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                                     {!!Form::text('start_date', \App\Library\Helper::databaseToFrontEditDate(date('Y-m-d',strtotime('first day of this month'))), ['class'=>'form-control old-date-picker','id'=>'start_date','placeholder'=>'Start Date'])!!}
                                 </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                     {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                                     {!!Form::text('end_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','id'=>'end_date','placeholder'=>'End Date'])!!}
                                 </div>
                             </div>
                         </div>
                     @endif
                 </div>--}}
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="font-weight: 600;">Status</th>
                        <th style="font-weight: 600;">Number of loans</th>
                        @if(auth()->user()->hasRole('super admin|admin'))
                            <th style="font-weight: 600;">Outstanding Principal</th>
                            <th style="font-weight: 600;">Payment Received</th>
                        @elseif(auth()->user()->hasRole('debt collector'))
                            <th style="font-weight: 600;">Outstanding Principal</th>
                            <th style="font-weight: 600;">Payments received by debt coll (excl tax)</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody id="table_tbody">

                    </tbody>
                    <tfoot id="table_tfoot">

                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @if(env('POST_COUNTER',false) && auth()->user()->hasRole('super admin|admin'))
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="mt-2">
                        @if(auth()->user()->hasRole('super admin|admin'))
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                                        {!!Form::text('start_date', \App\Library\Helper::databaseToFrontEditDate(date('Y-m-d',strtotime('first day of this month'))), ['class'=>'form-control old-date-picker','id'=>'start_date_below','placeholder'=>'Start Date'])!!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                                        {!!Form::text('end_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','id'=>'end_date_below','placeholder'=>'End Date'])!!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button class="btn btn-default js--search" style="margin-top: 1.7rem;">Filter</button>
                                        <button class="btn btn-default js--excel" style="margin-top: 1.7rem;">Export</button>
                                        <a href="#nogo" id="exportPDFLink"></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-12 col-xl-4">
                            <div class="widget-bg-color-icon card-box">
                                <div class="bg-icon bg-info pull-left">
                                    <i class="md md-add-shopping-cart text-white"></i>
                                </div>
                                <div class="text-right">
                                    <h3 class="text-dark"><span class="counter" id="js--total-loans-count"></span></h3>
                                    <p class="text-muted mb-0">Total Loans Count</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-12 col-xl-4">
                            <div class="widget-bg-color-icon card-box">
                                <div class="bg-icon bg-info pull-left">
                                    <i class="md md-attach-money text-white"></i>
                                </div>
                                <div class="text-right">
                                    <h3 class="text-dark"><span class="counter" id="js--total-loans-amount"></span></h3>
                                    <p class="text-muted mb-0">Total Principal Amount</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-12 col-xl-4">
                            <div class="widget-bg-color-icon card-box">
                                <div class="bg-icon bg-info pull-left">
                                    <i class="md md-attach-money text-white"></i>
                                </div>
                                <div class="text-right">
                                    <h3 class="text-dark"><span class="counter" id="js--total-loans-amount-collected"></span></h3>
                                    <p class="text-muted mb-0">Total Principal Collected</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Renewal Fees Posted</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--renewal-fees">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Debt Collection Fees Posted</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--debt-collection-fees">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Admin Fees Posted</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--admin-fees">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Interest Posted</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--interest-posted">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Origination fee</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--origination-fees">0</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: -25px;">
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Renewal Fees Collected</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--renewal-fees-collected">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Debt Collection Fees Collected</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--debt-collection-fees-collected">0</span>
                                </h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Admin Fees Collected</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--admin-fees-collected">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Interest Collected</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--interest-collected">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Collected</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--total-collected">0</span></h4>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Principal Outstanding</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--principal-outstanding">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Renewal Fees Outstanding</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--renewal-fees-outstanding">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Debt Collector Fees Outstanding</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--debt-collection-fees-outstanding">0</span>
                                </h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Interest Outstanding</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--interest-outstanding">0</span></h4>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-2-5">
                            <div class="card-box widget-box-1 bg-white" style="min-height: 160px;">
                                <h5 class="text-dark font-18" style="height: 60px;">Total Admin Fees Outstanding</h5>
                                <h4 style="font-size: 25px;" class="text-primary text-center"><span style="word-break: break-all" id="js--admin-fees-outstanding">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(auth()->user()->hasRole('super admin|admin|auditor'))
        <div class="card-box">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-title">Loan History Report</h4>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                        {!!Form::text('start_date', \App\Library\Helper::databaseToFrontEditDate(date('Y-m-d',strtotime('first day of this month'))), ['class'=>'form-control old-date-picker','id'=>'start_date_loan','placeholder'=>'Start Date'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                        {!!Form::text('end_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','id'=>'end_date_loan','placeholder'=>'End Date'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('client_id','Client',['style'=>"font-weight:600"])!!}
                        {!!Form::select('client_id',$clients, old('client_id'), ['class'=>'form-control','id'=>'client_id_loan'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('branch','Branch',['style'=>"font-weight:600"])!!}
                        {!!Form::select('branch',$branches_history, old('branch'), ['class'=>'form-control','id'=>'branch_id_loan'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('users','Users',['style'=>"font-weight:600"])!!}
                        {!!Form::select('users',$users, old('users'), ['class'=>'form-control','id'=>'user_id_loan'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <button class="btn btn-default js--history-excel" style="margin-top: 1.7rem;">Export</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(env('CASH_TRACKING',false) && auth()->user()->hasRole('super admin|admin|auditor'))
        <div class="card-box">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-title">Cash Tracking Report</h4>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                        {!!Form::text('start_date', \App\Library\Helper::databaseToFrontEditDate(date('Y-m-d',strtotime('first day of this month'))), ['class'=>'form-control old-date-picker','id'=>'start_date_cash','placeholder'=>'Start Date'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                        {!!Form::text('end_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','id'=>'end_date_cash','placeholder'=>'End Date'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('branch','Branch',['style'=>"font-weight:600"])!!}
                        {!!Form::select('branch',$branches, old('branch'), ['class'=>'form-control','id'=>'branch_id'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('transactions','Transactions',['style'=>"font-weight:600"])!!}
                        {!!Form::select('transactions',$transactions, old('transactions'), ['class'=>'form-control','id'=>'transaction_id'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('credits','Credits',['style'=>"font-weight:600"])!!}
                        {!!Form::select('credits',$credits, old('credits'), ['class'=>'form-control','id'=>'credit_id'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        {!!Form::label('users','Users',['style'=>"font-weight:600"])!!}
                        {!!Form::select('users',$users, old('users'), ['class'=>'form-control','id'=>'user_id'])!!}
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <button class="btn btn-default js--cash-excel" style="margin-top: 1.7rem;">Export</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/dashboard.js')) !!}"></script>
    <script>
        dashboard.init();
    </script>
@stop