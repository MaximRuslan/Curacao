@extends('admin1.layouts.master')
@section('page_name')
    Payments
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12 m-b-20">
            <h4 class="page-title">@yield('page_name')</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 m-b-20">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('start_month','Start Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('start_month', \App\Library\Helper::time_to_current_timezone(date('Y-m-d H:i:s'),null,'d/m/Y'), ['class'=>'form-control all-date-picker','id'=>'start_month','placeholder'=>'Start Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('end_month','End Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('end_month', \App\Library\Helper::time_to_current_timezone(date('Y-m-d H:i:s'),null,'d/m/Y'), ['class'=>'form-control all-date-picker','id'=>'end_month','placeholder'=>'End Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('search','Search',['style'=>"font-weight:600"])!!}
                            {!!Form::text('search', old('search'), ['class'=>'form-control','id'=>'search','placeholder'=>'Search'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-default" id="js--export-excel" style="margin-top: 29px;"><i class="fa fa-file-pdf-o"></i> Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Country</th>
                        <th>Merchant</th>
                        <th>Merchant</th>
                        <th>Merchant</th>
                        <th>Merchant</th>
                        <th>Branch</th>
                        <th>Month</th>
                        <th>Collected Amount</th>
                        <th>Commission</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.merchant-transactions')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/merchantsPayment.js')) !!}"></script>
    <script>
        merchantsPayment.init();
    </script>
@stop